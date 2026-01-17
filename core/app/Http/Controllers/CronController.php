<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Lib\CurlRequest;
use App\Lib\HyipLab;
use App\Models\CronJob;
use App\Models\CronJobLog;
use App\Models\Invest;
use App\Models\Plan;
use App\Models\ScheduleInvest;
use App\Models\StakingInvest;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserRanking;
use Carbon\Carbon;

class CronController extends Controller
{
    public function cron()
    {
        $general            = gs();
        $general->last_cron = now();
        $general->save();

        $crons = CronJob::with('schedule');

        if (request()->alias) {
            $crons->where('alias', request()->alias);
        } else {
            $crons->where('next_run', '<', now())->where('is_running', Status::YES);
        }
        $crons = $crons->get();
        foreach ($crons as $cron) {
            $cronLog              = new CronJobLog();
            $cronLog->cron_job_id = $cron->id;
            $cronLog->start_at    = now();
            if ($cron->is_default) {
                $controller = new $cron->action[0];
                try {
                    $method = $cron->action[1];
                    $controller->$method();
                } catch (\Exception $e) {
                    $cronLog->error = $e->getMessage();
                }
            } else {
                try {
                    CurlRequest::curlContent($cron->url);

                } catch (\Exception $e) {
                    $cronLog->error = $e->getMessage();
                }
            }
            $cron->last_run = now();
            $cron->next_run = now()->addSeconds((int) $cron->schedule->interval);
            $cron->save();

            $cronLog->end_at = $cron->last_run;

            $startTime         = Carbon::parse($cronLog->start_at);
            $endTime           = Carbon::parse($cronLog->end_at);
            $diffInSeconds     = $startTime->diffInSeconds($endTime);
            $cronLog->duration = $diffInSeconds;
            $cronLog->save();
        }
        if (request()->target == 'all') {
            $notify[] = ['success', 'Cron executed successfully'];
            return back()->withNotify($notify);
        }
        if (request()->alias) {
            $notify[] = ['success', keyToTitle(request()->alias) . ' executed successfully'];
            return back()->withNotify($notify);
        }
    }

    public function interest()
    {
        try {
            $now     = Carbon::now();
            $general = gs();

            $day    = strtolower(date('D'));
            $offDay = (array) $general->off_day;
            if (array_key_exists($day, $offDay)) {
                echo "Holiday";
                exit;
            }

            // Log total pending investments for monitoring
            $totalPending = Invest::where('status', Status::INVEST_RUNNING)
                ->where('next_time', '<=', $now)
                ->count();
            \Log::info("Cron Interest: $totalPending pending investments");

            $invests = Invest::with('plan.timeSetting', 'user')->where('status', Status::INVEST_RUNNING)->where('next_time', '<=', $now)->orderBy('last_time')->take(100)->get();

            \Log::info("Cron Interest: Processing " . $invests->count() . " investments");

            foreach ($invests as $invest) {
                $user = $invest->user;

                // Determine next payment time and calculation mode
                // TEST MODE: For quick testing with 15-minute cycles
                // PRODUCTION MODE: Real business days calculation
                $useTestMode = env('CRON_TEST_MODE', false);

                if ($useTestMode) {
                    // TEST MODE: Next payment in 15 minutes
                    $next = HyipLab::nextWorkingMinute(15);

                    // Calculate payment cycles (15-minute intervals = 1 "month" in test mode)
                    $lastPayment = $invest->last_time ?: $invest->created_at;
                    $minutesPassed = Carbon::parse($lastPayment)->diffInMinutes($now);
                    $monthsCompleted = floor($minutesPassed / 15);
                    $newRecTotalDays = 21; // Reset for next cycle

                    \Log::info("TEST MODE - Invest #{$invest->id}: {$minutesPassed} minutes passed, {$monthsCompleted} cycles completed");
                } else {
                    // PRODUCTION MODE: Next payment based on plan configuration
                    $next = HyipLab::nextWorkingDay($invest->plan?->timeSetting->time);

                    // Calculate real business days passed
                    $lastPayment = $invest->last_time ?: $invest->created_at;
                    $businessDaysPassed = $this->calculateBusinessDays($lastPayment, $now);

                    // Determine complete months (21 business days = 1 month)
                    $monthsCompleted = floor($businessDaysPassed / 21);

                    // Calculate remaining days for next month
                    $remainingDays = $businessDaysPassed % 21;
                    $newRecTotalDays = 21 - $remainingDays;

                    \Log::info("PRODUCTION MODE - Invest #{$invest->id}: {$businessDaysPassed} business days passed, {$monthsCompleted} months completed");
                }

                // Skip if no complete months/cycles have passed
                if ($monthsCompleted == 0) {
                    // Update next_time and continue
                    $invest->next_time = $next;
                    $invest->save();
                    continue;
                }

                // Calculate interest (using distribution if configured)
                $interest = $this->calculateInterest($invest);

                // Update investment counters
                $invest->return_rec_time += $monthsCompleted;
                $invest->rec_total_days = $newRecTotalDays;
                $invest->paid += $interest * $monthsCompleted;
                $invest->should_pay -= $invest->period > 0 ? $invest->interest * $monthsCompleted : 0;
                $invest->next_time = $next;
                $invest->last_time = $now;
                $invest->net_interest += $invest->rem_compound_times ? 0 : ($interest * $monthsCompleted);

                // Add Return Amount to user's wallet (for all completed months)
                // If plan has restricted_withdrawal, credit to special_wallet, otherwise to interest_wallet
                $totalInterestPayment = $interest * $monthsCompleted;
                $plan = $invest->plan;
                $useSpecialWallet = $plan && $plan->restricted_withdrawal;

                if ($useSpecialWallet) {
                    $user->special_wallet += $totalInterestPayment;
                    $walletType = 'special_wallet';
                    $postBalance = $user->special_wallet;
                } else {
                    $user->interest_wallet += $totalInterestPayment;
                    $walletType = 'interest_wallet';
                    $postBalance = $user->interest_wallet;
                }
                $user->save();

                $trx = getTrx();

                // Create The Transaction for Interest Back
                $monthsText = $monthsCompleted > 1 ? " ({$monthsCompleted} months)" : "";
                $transaction               = new Transaction();
                $transaction->user_id      = $user->id;
                $transaction->invest_id    = $invest->id;
                $transaction->amount       = $totalInterestPayment;
                $transaction->charge       = 0;
                $transaction->post_balance = $postBalance;
                $transaction->trx_type     = '+';
                $transaction->trx          = $trx;
                $transaction->remark       = $useSpecialWallet ? 'interest_special' : 'interest';
                $transaction->wallet_type  = $walletType;
                $transaction->details      = showAmount($totalInterestPayment) . $monthsText . ' - ' . @$invest->plan->name;
                $transaction->save();

                // Give Referral Commission if Enabled (for all completed months)
                if ($general->invest_return_commission == 1) {
                    $commissionType = 'invest_return_commission';
                    HyipLab::levelCommission($user, $totalInterestPayment, $commissionType, $trx, $general);
                }

                // Complete the investment if user get full amount as plan
                if ($invest->return_rec_time >= $invest->period && $invest->period != -1) {
                    $invest->status = 0; // Change Status so he do not get any more return

                    // Give the capital back if plan says the same and hold capital option is disabled
                    if ($invest->capital_status == 1 && !$invest->hold_capital && !$invest->fractional_capital) {
                        HyipLab::capitalReturn($invest);
                    }
                }

                if ($invest->rem_compound_times) {
                    $interest        = $invest->interest;
                    $newInvestAmount = $invest->amount + $interest;
                    $newInterest     = $invest->interest * $newInvestAmount / $invest->amount;
                    $newShouldPay    = $invest->should_pay == -1 ? -1 : ($invest->period - $invest->return_rec_time) * $newInterest;

                    $user->interest_wallet -= $invest->interest;
                    $user->save();

                    $invest->amount     = $newInvestAmount;
                    $invest->interest   = $newInterest;
                    $invest->should_pay = $newShouldPay;
                    $invest->rem_compound_times -= 1;

                    $transaction               = new Transaction();
                    $transaction->user_id      = $user->id;
                    $transaction->invest_id    = $invest->id;
                    $transaction->amount       = $interest;
                    $transaction->post_balance = $user->interest_wallet;
                    $transaction->charge       = 0;
                    $transaction->trx_type     = '+';
                    $transaction->details      = '' . $invest->plan->name;
                    $transaction->trx          = $trx;
                    $transaction->wallet_type  = 'interest_wallet';
                    $transaction->remark       = 'invest_compound';
                    $transaction->save();
                }

                // Handle fractional capital return (for all completed months)
                // Capital return uses the same wallet as interest (special_wallet if restricted)
                if ($invest->fractional_capital && (($invest->period - $invest->return_rec_time) <= $invest->period_return_capital)) {
                    // Calculate total capital to return for all completed months
                    $totalCapitalReturn = $invest->mon_return_amount * $monthsCompleted;
                    $newInvestAmount = $invest->amount - $totalCapitalReturn;
                    $invest->amount  = max(0, $newInvestAmount); // Ensure it doesn't go below 0

                    if ($useSpecialWallet) {
                        $user->special_wallet += $totalCapitalReturn;
                        $capitalPostBalance = $user->special_wallet;
                    } else {
                        $user->interest_wallet += $totalCapitalReturn;
                        $capitalPostBalance = $user->interest_wallet;
                    }
                    $user->save();

                    $monthsCapitalText = $monthsCompleted > 1 ? " ({$monthsCompleted} months)" : "";
                    $transaction               = new Transaction();
                    $transaction->user_id      = $user->id;
                    $transaction->invest_id    = $invest->id;
                    $transaction->amount       = $totalCapitalReturn;
                    $transaction->post_balance = $capitalPostBalance;
                    $transaction->charge       = 0;
                    $transaction->trx_type     = '+';
                    $transaction->details      = __('tagretufraccapital') . $monthsCapitalText . ' - ' . $invest->plan->name;
                    $transaction->trx          = $trx;
                    $transaction->wallet_type  = $walletType;
                    $transaction->remark       = 'return_fractional_capital';
                    $transaction->save();
                }

                $invest->save();

                notify($user, 'INTEREST', [
                    'trx'          => $invest->trx,
                    'amount'       => showAmount($totalInterestPayment, currencyFormat: false),
                    'plan_name'    => @$invest->plan->name,
                    'post_balance' => showAmount($useSpecialWallet ? $user->special_wallet : $user->interest_wallet, currencyFormat: false),
                ]);
            }
        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage());
        }
    }

    public function rank()
    {
        try {
            $general = gs();
            if (!$general->user_ranking) {
                return 'MODULE DISABLED';
            }

            $users = User::with('referrals', 'activeReferrals')->orderBy('last_rank_update', 'asc')->limit(100)->get();
            foreach ($users as $user) {
                $user->last_rank_update = now();
                $user->save();

                $userInvests     = $user->total_invests;
                $referralInvests = $user->team_invests;
                $referralCount   = $user->activeReferrals->count();

                $rankings = UserRanking::active()->where('id', '>', $user->user_ranking_id)->where('minimum_invest', '<=', $userInvests)->where('min_referral_invest', '<=', $referralInvests)->where('min_referral', '<=', $referralCount)->get();

                foreach ($rankings as $ranking) {
                    $user->bonus_wallet += $ranking->bonus;
                    $user->user_ranking_id = $ranking->id;
                    $user->save();

                    $transaction               = new Transaction();
                    $transaction->user_id      = $user->id;
                    $transaction->amount       = $ranking->bonus;
                    $transaction->charge       = 0;
                    $transaction->post_balance = $user->bonus_wallet;
                    $transaction->trx_type     = '+';
                    $transaction->trx          = getTrx();
                    $transaction->remark       = 'ranking_bonus';
                    $transaction->wallet_type  = 'bonus_wallet';
                    $transaction->details      = showAmount($ranking->bonus) . __('tagrankingbonusfor') . @$ranking->name;
                    $transaction->save();
                }
            }
        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage());
        }
    }

    public function investSchedule()
    {
        try {
            if (!gs('schedule_invest')) {
                return 'MODULE DISABLED';
            }

            $scheduleInvests = ScheduleInvest::with('user.deviceTokens', 'plan.timeSetting')->where('next_invest', '<=', now())->where('rem_schedule_times', '>', 0)->where('status', Status::ENABLE)->get();
            $planIds         = array_unique($scheduleInvests->pluck('plan_id')->toArray());
            $activePlanIds   = Plan::whereIn('id', $planIds)->where('status', Status::ENABLE)->whereHas('timeSetting', function ($timeSetting) {
                $timeSetting->where('status', Status::ENABLE);
            })->pluck('id')->toArray();

            foreach ($scheduleInvests as $scheduleInvest) {
                $user   = $scheduleInvest->user;
                $wallet = $scheduleInvest->wallet;

                if ($scheduleInvest->amount > $user->$wallet) {
                    $scheduleInvest->next_invest = now()->addHours($scheduleInvest->interval_hours);
                    $scheduleInvest->save();

                    notify($user, 'INSUFFICIENT_BALANCE', [
                        'invest_amount' => showAmount($scheduleInvest->amount, currencyFormat: false),
                        'wallet'        => keyToTitle($wallet),
                        'plan_name'     => $scheduleInvest->plan->name,
                        'balance'       => showAmount($user->$wallet, currencyFormat: false),
                        'next_schedule' => $scheduleInvest->next_invest,
                    ]);
                    continue;
                }

                if (!in_array($scheduleInvest->plan_id, $activePlanIds)) {
                    continue;
                }

                $hyip = new HyipLab($user, $scheduleInvest->plan);
                $hyip->invest($scheduleInvest->amount, $wallet, $scheduleInvest->compound_times);

                $scheduleInvest->rem_schedule_times -= 1;
                $scheduleInvest->next_invest = $scheduleInvest->rem_schedule_times ? now()->addHours($scheduleInvest->interval_hours) : null;
                $scheduleInvest->status      = $scheduleInvest->rem_schedule_times ? 1 : 0;
                $scheduleInvest->save();
            }
        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage());
        }
    }

    public function staking()
    {
        try {
            $stakingInvests = StakingInvest::with('user')->where('status', Status::STAKING_RUNNING)->where('end_at', '<=', now())->get();

            foreach ($stakingInvests as $stakingInvest) {
                $user = $stakingInvest->user;
                $user->interest_wallet += $stakingInvest->invest_amount + $stakingInvest->interest;
                $user->save();

                $stakingInvest->status = Status::STAKING_COMPLETED;
                $stakingInvest->save();

                $transaction               = new Transaction();
                $transaction->user_id      = $user->id;
                $transaction->staking_invest_id = $stakingInvest->id;
                $transaction->amount       = $stakingInvest->invest_amount + $stakingInvest->interest;
                $transaction->post_balance = $user->interest_wallet;
                $transaction->charge       = 0;
                $transaction->trx_type     = '+';
                $transaction->details      = __('tagstakinginvestedreturn');
                $transaction->trx          = getTrx();
                $transaction->wallet_type  = 'interest_wallet';
                $transaction->remark       = 'staking_invest_return';
                $transaction->save();
            }

        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage());
        }
    }

    /**
     * Calculate interest based on distribution configuration
     *
     * @param Invest $invest
     * @return float
     */
    protected function calculateInterest($invest)
    {
        $plan = $invest->plan;

        // Check if plan has interest distribution configured
        if (!$plan->interest_distribution || !isset($plan->interest_distribution['enabled']) || !$plan->interest_distribution['enabled']) {
            // Use traditional calculation
            return $invest->amount * ($invest->mon_interest_rate / 100);
        }

        // Get distribution configuration
        $distribution = $plan->interest_distribution;
        $segments = $distribution['segments'] ?? [];

        if (empty($segments)) {
            // Fallback to traditional calculation if no segments
            return $invest->amount * ($invest->mon_interest_rate / 100);
        }

        // Determine current month (1-based index)
        $currentMonth = $invest->return_rec_time + 1;

        // Find which segment the current month belongs to
        $currentSegment = $this->getCurrentSegment($currentMonth, $segments);

        if (!$currentSegment) {
            // Fallback if segment not found
            return $invest->amount * ($invest->mon_interest_rate / 100);
        }

        // Calculate monthly interest rate for current segment
        $segmentMonthlyRate = $currentSegment['percentage'] / $currentSegment['months'];

        // Apply interest based on type (percentage or fixed)
        if ($plan->interest_type == 1) {
            // Percentage-based interest
            $monthlyInterest = $invest->amount * ($segmentMonthlyRate / 100);
        } else {
            // Fixed interest
            $monthlyInterest = $segmentMonthlyRate;
        }

        return $monthlyInterest;
    }

    /**
     * Get the segment that corresponds to the current month
     *
     * @param int $currentMonth
     * @param array $segments
     * @return array|null
     */
    protected function getCurrentSegment($currentMonth, $segments)
    {
        $accumulatedMonths = 0;

        foreach ($segments as $segment) {
            $accumulatedMonths += $segment['months'];

            if ($currentMonth <= $accumulatedMonths) {
                return $segment;
            }
        }

        return null;
    }

    /**
     * Calculate business days between two dates (excluding weekends and holidays)
     *
     * @param string|Carbon $startDate
     * @param string|Carbon $endDate
     * @return int
     */
    protected function calculateBusinessDays($startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $days = 0;
        $general = gs();
        $offDays = (array) $general->off_day;

        while ($start->lt($end)) {
            $dayName = strtolower($start->format('D'));

            // Skip if it's an off day
            if (!array_key_exists($dayName, $offDays)) {
                // Check if it's not a holiday
                $isHoliday = Holiday::where('date', $start->format('Y-m-d'))->exists();
                if (!$isHoliday) {
                    $days++;
                }
            }

            $start->addDay();
        }

        return $days;
    }

}
