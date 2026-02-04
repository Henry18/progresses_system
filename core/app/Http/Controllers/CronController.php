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
                $plan = $invest->plan;

                // Determine mode and calculate next payment time
                // TEST MODE: 15-minute cycles simulate 1 business day each
                // PRODUCTION MODE: Daily payments (cron runs every business day)
                $useTestMode = env('CRON_TEST_MODE', false);

                if ($useTestMode) {
                    // TEST MODE: Next payment in 15 minutes (simulates next business day)
                    $next = HyipLab::nextWorkingMinute(15);
                    \Log::info("TEST MODE - Invest #{$invest->id}: Processing daily payment (day {$invest->return_rec_time})");
                } else {
                    // PRODUCTION MODE: Next payment in 24 hours (next business day)
                    $next = HyipLab::nextWorkingDay(24);
                    \Log::info("PRODUCTION MODE - Invest #{$invest->id}: Processing daily payment (day {$invest->return_rec_time})");
                }

                // Calculate DAILY interest payment
                // mon_interest_rate = interest_rate / 21 (already stored)
                // So daily interest = amount * (mon_interest_rate / 100)
                $dailyInterest = $this->calculateDailyInterest($invest);

                // Determine wallet type based on plan settings
                $useSpecialWallet = $plan && $plan->restricted_withdrawal;
                $walletType = $useSpecialWallet ? 'special_wallet' : 'interest_wallet';

                // Add daily interest to user's wallet
                if ($useSpecialWallet) {
                    $user->special_wallet += $dailyInterest;
                    $postBalance = $user->special_wallet;
                } else {
                    $user->interest_wallet += $dailyInterest;
                    $postBalance = $user->interest_wallet;
                }
                $user->save();

                // Update investment tracking
                $invest->return_rec_time += 1; // Increment days counter
                $invest->paid += $dailyInterest;
                $invest->net_interest += $invest->rem_compound_times ? 0 : $dailyInterest;
                $invest->next_time = $next;
                $invest->last_time = $now;

                // Update rec_total_days (days remaining in current month)
                $daysInCurrentMonth = $invest->return_rec_time % 21;
                $invest->rec_total_days = $daysInCurrentMonth == 0 ? 21 : (21 - $daysInCurrentMonth);

                $trx = getTrx();

                // Calculate current month and day for logging
                $currentMonth = floor(($invest->return_rec_time - 1) / 21) + 1;
                $dayInMonth = (($invest->return_rec_time - 1) % 21) + 1;

                // Create transaction for daily interest
                $transaction               = new Transaction();
                $transaction->user_id      = $user->id;
                $transaction->invest_id    = $invest->id;
                $transaction->amount       = $dailyInterest;
                $transaction->charge       = 0;
                $transaction->post_balance = $postBalance;
                $transaction->trx_type     = '+';
                $transaction->trx          = $trx;
                $transaction->remark       = $useSpecialWallet ? 'interest_special' : 'interest';
                $transaction->wallet_type  = $walletType;
                $transaction->details      = showAmount($dailyInterest) . ' - M' . $currentMonth . ' D' . $dayInMonth . ' - ' . @$invest->plan->name;
                $transaction->save();

                \Log::info("Invest #{$invest->id}: Month {$currentMonth}, Day {$dayInMonth}/21, Daily Interest: {$dailyInterest}");

                // Give Referral Commission if Enabled
                if ($general->invest_return_commission == 1) {
                    $commissionType = 'invest_return_commission';
                    HyipLab::levelCommission($user, $dailyInterest, $commissionType, $trx, $general);
                }

                // Calculate total days for complete plan (period months * 21 days)
                $totalPlanDays = $invest->period * 21;

                // Check if investment is complete
                if ($invest->return_rec_time >= $totalPlanDays && $invest->period != -1) {
                    $invest->status = 0; // Complete the investment

                    \Log::info("Invest #{$invest->id}: COMPLETED after {$invest->return_rec_time} days");

                    // Return capital if configured (and not fractional)
                    if ($invest->capital_status == 1 && !$invest->hold_capital && !$invest->fractional_capital) {
                        // Return the INITIAL invested amount (not current amount which may have changed)
                        $capitalToReturn = $invest->initial_amount;

                        if ($useSpecialWallet) {
                            $user->special_wallet += $capitalToReturn;
                            $capitalPostBalance = $user->special_wallet;
                        } else {
                            $user->interest_wallet += $capitalToReturn;
                            $capitalPostBalance = $user->interest_wallet;
                        }
                        $user->save();

                        $invest->capital_back = 1;

                        $capitalTrx               = new Transaction();
                        $capitalTrx->user_id      = $user->id;
                        $capitalTrx->invest_id    = $invest->id;
                        $capitalTrx->amount       = $capitalToReturn;
                        $capitalTrx->charge       = 0;
                        $capitalTrx->post_balance = $capitalPostBalance;
                        $capitalTrx->trx_type     = '+';
                        $capitalTrx->trx          = getTrx();
                        $capitalTrx->wallet_type  = $walletType;
                        $capitalTrx->remark       = 'capital_return';
                        $capitalTrx->details      = 'Capital Return - ' . showAmount($capitalToReturn) . ' - ' . @$invest->plan->name;
                        $capitalTrx->save();

                        \Log::info("Invest #{$invest->id}: Capital returned: {$capitalToReturn}");
                    }
                }

                // Handle compound interest (at end of each month = every 21 days)
                if ($invest->rem_compound_times && ($invest->return_rec_time % 21 == 0)) {
                    $monthlyInterest = $invest->interest; // Monthly interest amount
                    $newInvestAmount = $invest->amount + $monthlyInterest;
                    $newInterest     = $invest->interest * $newInvestAmount / $invest->amount;

                    $user->interest_wallet -= $monthlyInterest;
                    $user->save();

                    $invest->amount     = $newInvestAmount;
                    $invest->interest   = $newInterest;
                    $invest->rem_compound_times -= 1;

                    $compoundTrx               = new Transaction();
                    $compoundTrx->user_id      = $user->id;
                    $compoundTrx->invest_id    = $invest->id;
                    $compoundTrx->amount       = $monthlyInterest;
                    $compoundTrx->post_balance = $user->interest_wallet;
                    $compoundTrx->charge       = 0;
                    $compoundTrx->trx_type     = '-';
                    $compoundTrx->details      = 'Compound Interest - Month ' . $currentMonth . ' - ' . $invest->plan->name;
                    $compoundTrx->trx          = $trx;
                    $compoundTrx->wallet_type  = 'interest_wallet';
                    $compoundTrx->remark       = 'invest_compound';
                    $compoundTrx->save();
                }

                // Handle fractional capital return
                // Capital return starts after (period - period_return_capital) months
                // period_return_capital = number of months for capital return
                $capitalStartDay = ($invest->period - $invest->period_return_capital) * 21;

                if ($invest->fractional_capital && $invest->period_return_capital > 0 && $invest->return_rec_time > $capitalStartDay) {
                    // Daily capital return = initial_amount / (period_return_capital * 21 days)
                    $totalCapitalDays = $invest->period_return_capital * 21;
                    $dailyCapitalReturn = $invest->initial_amount / $totalCapitalDays;

                    // Don't return more than remaining amount
                    $dailyCapitalReturn = min($dailyCapitalReturn, $invest->amount);

                    if ($dailyCapitalReturn > 0) {
                        $invest->amount = max(0, $invest->amount - $dailyCapitalReturn);

                        if ($useSpecialWallet) {
                            $user->special_wallet += $dailyCapitalReturn;
                            $capitalPostBalance = $user->special_wallet;
                        } else {
                            $user->interest_wallet += $dailyCapitalReturn;
                            $capitalPostBalance = $user->interest_wallet;
                        }
                        $user->save();

                        $fracCapitalTrx               = new Transaction();
                        $fracCapitalTrx->user_id      = $user->id;
                        $fracCapitalTrx->invest_id    = $invest->id;
                        $fracCapitalTrx->amount       = $dailyCapitalReturn;
                        $fracCapitalTrx->post_balance = $capitalPostBalance;
                        $fracCapitalTrx->charge       = 0;
                        $fracCapitalTrx->trx_type     = '+';
                        $fracCapitalTrx->details      = __('Fractional Capital') . ' - M' . $currentMonth . ' D' . $dayInMonth . ' - ' . $invest->plan->name;
                        $fracCapitalTrx->trx          = $trx;
                        $fracCapitalTrx->wallet_type  = $walletType;
                        $fracCapitalTrx->remark       = 'return_fractional_capital';
                        $fracCapitalTrx->save();
                    }
                }

                $invest->save();

                notify($user, 'INTEREST', [
                    'trx'          => $invest->trx,
                    'amount'       => showAmount($dailyInterest, currencyFormat: false),
                    'plan_name'    => @$invest->plan->name,
                    'post_balance' => showAmount($postBalance, currencyFormat: false),
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
     * Calculate DAILY interest payment
     * The system pays daily (21 business days = 1 month)
     * mon_interest_rate is already interest_rate/21
     *
     * @param Invest $invest
     * @return float
     */
    protected function calculateDailyInterest($invest)
    {
        $plan = $invest->plan;

        // Check if plan has interest distribution configured
        if ($plan->interest_distribution && isset($plan->interest_distribution['enabled']) && $plan->interest_distribution['enabled']) {
            return $this->calculateDailyInterestWithDistribution($invest);
        }

        // Standard calculation: daily interest = amount * (mon_interest_rate / 100)
        // mon_interest_rate = interest_rate / 21, so this gives us the daily rate
        return ($invest->amount * ($invest->mon_interest_rate / 100) / 21);
    }

    /**
     * Calculate daily interest when plan has interest distribution segments
     *
     * @param Invest $invest
     * @return float
     */
    protected function calculateDailyInterestWithDistribution($invest)
    {
        $plan = $invest->plan;
        $distribution = $plan->interest_distribution;
        $segments = $distribution['segments'] ?? [];

        if (empty($segments)) {
            // Fallback to standard calculation
            return ($invest->amount * ($invest->mon_interest_rate / 100) / 21);
        }

        // Current day (0-based for calculation, return_rec_time is the day we're about to pay)
        $currentDay = $invest->return_rec_time;

        // Determine which month we're in (0-based)
        $currentMonth = floor($currentDay / 21) + 1;

        // Find which segment the current month belongs to
        $currentSegment = $this->getCurrentSegment($currentMonth, $segments);

        if (!$currentSegment) {
            // Fallback if segment not found
            return ($invest->amount * ($invest->mon_interest_rate / 100) / 21);
        }

        // Calculate daily interest rate for current segment
        // Segment percentage is for all months in segment, so:
        // Daily rate = (segment_percentage / segment_months) / 21
        $segmentMonthlyRate = $currentSegment['percentage'] / $currentSegment['months'];
        $segmentDailyRate = ($segmentMonthlyRate / 100) / 21;

        $dailyInterest = ($invest->amount * $segmentDailyRate);

        return $dailyInterest;
    }

    /**
     * Calculate MONTHLY interest (kept for backward compatibility)
     *
     * @param Invest $invest
     * @return float
     */
    protected function calculateInterest($invest)
    {
        // Monthly interest = daily interest * 21
        return $this->calculateDailyInterest($invest) * 21;
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
