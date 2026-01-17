<?php

namespace App\Lib;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Invest;
use App\Models\Holiday;
use App\Models\Referral;
use App\Models\UserReferralsRanking;
use App\Models\UserRanking;
use App\Constants\Status;
use App\Models\Transaction;
use App\Models\ScheduleInvest;
use App\Models\AdminNotification;

class HyipLab
{
    /**
     * Instance of investor user
     *
     * @var object
     */
    private $user;

    /**
     * Plan which is purchasing
     *
     * @var object
     */
    private $plan;

    /**
     * General setting
     *
     * @var object
     */
    private $setting;

    /**
     * Set some properties
     *
     * @param object $user
     * @param object $plan
     * @return void
     */
    public function __construct($user, $plan)
    {
        $this->user    = $user;
        $this->plan    = $plan;
        $this->setting = gs();
    }

    /**
     * Invest process
     *
     * @param float $amount
     * @param string $wallet
     * @return void
     */
    public function invest($amount, $wallet, $compoundTimes = 0, $fractional_capital = 0)
    {
        $plan = $this->plan;
        $user = $this->user;

        $user->$wallet -= $amount;
        $user->total_invests += $amount;
        $user->save();

        $trx                       = getTrx();
        $transaction               = new Transaction();
        $transaction->user_id      = $user->id;
        $transaction->amount       = $amount;
        $transaction->post_balance = $user->$wallet;
        $transaction->charge       = 0;
        $transaction->trx_type     = '-';
        $transaction->details      = 'Invested on ' . $plan->name;
        $transaction->trx          = $trx;
        $transaction->wallet_type  = $wallet;
        $transaction->remark       = 'invest';
        $transaction->save();

        //start
        if ($plan->interest_type == 1) {
            $interestAmount = ($amount * $plan->interest) / 100;
        } else {
            $interestAmount = $plan->interest;
        }

        $period = ($plan->lifetime == 1) ? -1 : $plan->repeat_time;

        //$next = self::nextWorkingDay($plan->timeSetting->time);
        //multiplica los dias para el inicio * 24 horas, y el sistema entonces basado en esas horas le coloca la proxima fecha de pago
        $next = self::nextWorkingDay($plan->days_to_init * 24);
        $shouldPay = -1;
        if ($period > 0) {
            $shouldPay = $interestAmount * $period;
        }

        $invest                     = new Invest();
        $invest->user_id            = $user->id;
        $invest->plan_id            = $plan->id;
        $invest->amount             = $amount;
        $invest->initial_amount     = $amount;
        $invest->interest           = $interestAmount;
        $invest->initial_interest   = $interestAmount;
        $invest->interest_rate      = $plan->interest;
        $invest->mon_interest_rate  = $plan->interest/21;
        $invest->period_return_capital = $period - $plan->capital_months_return;
        $invest->mon_return_amount  = $amount / ($period - $plan->capital_months_return);
        $invest->period             = $period;
        $invest->rec_total_days     = 21;
        $invest->time_name          = $plan->timeSetting->name;
        $invest->hours              = $plan->timeSetting->time;
        $invest->next_time          = $next;
        $invest->should_pay         = $shouldPay;
        $invest->status             = 1;
        $invest->wallet_type        = $wallet;
        $invest->capital_status     = $plan->capital_back;
        $invest->trx                = $trx;
        $invest->compound_times     = $compoundTimes ?? 0;
        $invest->rem_compound_times = $compoundTimes ?? 0;
        $invest->hold_capital       = $plan->hold_capital;
        $invest->fractional_capital = $fractional_capital;
        $invest->terms_accepted     = 1;
        $invest->terms_accepted_at  = now();

        // Save project terms acceptance if project has PDF
        if ($plan->project && $plan->project->pdf) {
            $invest->project_terms_accepted = 1;
            $invest->project_terms_accepted_at = now();
        }

        $invest->save();

        if ($this->setting->invest_commission == 1) {
            $referrer = User::find($user->ref_by);
            $commissionType = 'invest_commission';
            if($referrer !== null && $referrer->total_invests > 0)
            {
                self::levelCommission($user, $amount, $commissionType, $trx, $this->setting, $referrer);
            }
        }

        notify($user, 'INVESTMENT', [
            'trx'             => $invest->trx,
            'amount'          => showAmount($amount, currencyFormat:false),
            'plan_name'       => $plan->name,
            'interest_amount' => showAmount($interestAmount, currencyFormat:false),
            'time'            => $plan->lifetime == Status::YES ? 'lifetime' : $plan->repeat_time . ' times',
            'time_name'       => $plan->timeSetting->name,
            'wallet_type'     => keyToTitle($wallet),
            'post_balance'    => showAmount($user->$wallet, currencyFormat:false),
        ]);

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $user->id;
        $adminNotification->title     = showAmount($amount, currencyFormat:false) . ' invested to ' . $plan->name;
        $adminNotification->click_url = '#';
        $adminNotification->save();

        while ($user->ref_by) {
            $user = User::find($user->ref_by);
            $user->team_invests += $amount;
            $user->save();
        }
    }

    public static function saveScheduleInvest($request)
    {
        $scheduleInvest                     = new ScheduleInvest();
        $scheduleInvest->user_id            = auth()->id();
        $scheduleInvest->plan_id            = $request->plan_id;
        $scheduleInvest->wallet             = $request->wallet_type;
        $scheduleInvest->amount             = $request->amount;
        $scheduleInvest->schedule_times     = $request->schedule_times;
        $scheduleInvest->rem_schedule_times = $request->schedule_times;
        $scheduleInvest->interval_hours     = $request->hours;
        $scheduleInvest->compound_times     = $request->compound_interest ?? 0;
        $scheduleInvest->next_invest        = now()->addHours((int) $request->hours);
        $scheduleInvest->save();
    }

    /**
     * Get the next working day of the system
     *
     * @param integer $hours
     * @return string
     */
    public static function nextWorkingDay($hours)
    {
        $now     = now();
        $setting = gs();
        $hours = (int) $hours;
        while (0 == 0) {
            $nextPossible = Carbon::parse($now)->addHours($hours)->toDateTimeString();

            if (!self::isHoliDay($nextPossible, $setting)) {
                $next = $nextPossible;
                break;
            }
            $now = $now->addDay();
        }
        return $next;
    }

    public static function nextWorkingMinute($minutes) {
        $now     = now();
        $setting = gs();
        $minutes = (int) $minutes;
        while (0 == 0) {
            $nextPossible = Carbon::parse($now)->addMinutes($minutes)->toDateTimeString();

            if (!self::isHoliDay($nextPossible, $setting)) {
                $next = $nextPossible;
                break;
            }
            $now = $now->addDay();
        }
        return $next;
    }

    /**
     * Check the date is holiday or not
     *
     * @param string $date
     * @param object $setting
     * @return string
     */
    public static function isHoliDay($date, $setting)
    {
        $isHoliday = true;
        $dayName   = strtolower(date('D', strtotime($date)));
        $holiday   = Holiday::where('date', date('Y-m-d', strtotime($date)))->count();
        $offDay    = (array) $setting->off_day;

        if (!array_key_exists($dayName, $offDay)) {
            if ($holiday == 0) {
                $isHoliday = false;
            }
        }

        return $isHoliday;

    }

    /**
     * Give referral commission
     *
     * @param object $user
     * @param float $amount
     * @param string $commissionType
     * @param string $trx
     * @param object $setting
     * @return void
     */
    public static function levelCommission($user, $amount, $commissionType, $trx, $setting, $referrer)
    {
        $level = Referral::where('commission_type', $commissionType)->first();
        $referral_bonus_times_max = 0;
        $refer_bonus_level = 0;
        if ($user->ref_by > 0) {
            if($referrer->user_ranking_id !== 0)
            {
                $levelRanking = UserRanking::find($referrer->user_ranking_id);
                $referral_bonus_times_max = $levelRanking->level;
                $refer_bonus_level = $levelRanking->refer_bonus_level;
            }

            $refer_times = UserReferralsRanking::updateOrCreate(
                [
                    'codigo_referred' => $user->id,
                    'codigo_referrer' => $user->ref_by,
                ],
                [
                    'referral_bonus_times_max' => $referral_bonus_times_max,
                    'referral_ranking' => $referrer->user_ranking_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
            $recentCreate = $refer_times->wasRecentlyCreated;

            $refer_bonus_level = ($recentCreate && $refer_times->referral_bonus_times  == 0) ? $refer_bonus_level : $refer_times->referral_bonus_times + 1;

            $bonusPercent = $recentCreate ? $level->percent : $refer_bonus_level;

            if ($recentCreate || $refer_times->referral_bonus_times < $refer_times->referral_bonus_times_max) {
                $com = ($amount * $bonusPercent) / 100;
                $referrer->bonus_wallet += $com;
                $referrer->save();

                $transactions[] = [
                    'user_id' => $referrer->id,
                    'amount' => $com,
                    'post_balance' => $referrer->bonus_wallet,
                    'charge' => 0,
                    'trx_type' => '+',
                    'details' => 'Nivel ' . $refer_bonus_level . ' comisión de referido de ' . $user->username .
                               ' (Bono #' . ($refer_times->referral_bonus_times  == 0 ? $refer_times->referral_bonus_times : $refer_times->referral_bonus_times + 1) . ' de ' .
                               $refer_times->referral_bonus_times_max . ')',
                    'trx' => $trx,
                    'wallet_type' => 'bonus_wallet',
                    'remark' => 'referral_commission',
                    'created_at' => now(),
                ];
                Transaction::insert($transactions);

                // Notificación al referidor
                notify($referrer, 'REFERRAL_COMMISSION', [
                    'amount' => showAmount($com, currencyFormat:false),
                    'post_balance' => showAmount($referrer->bonus_wallet, currencyFormat:false),
                    'trx' => $trx,
                    'level' => $level,
                    'type' => 'Invest',
                ]);


                if(!$recentCreate)
                {
                    $refer_times->referral_ranking = $referrer->user_ranking_id;
                    $refer_times->referral_bonus_times += 1;
                    $refer_times->save();
                }
            }


        }

       /* $transactions = [];

        $transactions[] = [
            'user_id' => $refer->id,
            'amount' => $com,
            'post_balance' => $refer->bonus_wallet,
            'charge' => 0,
            'trx_type' => '+',
            'details' => 'Nivel ' . $i . ' comisión de referido de ' . $user->username .
                       ' (Bono #' . $referralRanking->referral_bonus_times . ' de ' .
                       $referralRanking->referral_bonus_times_max . ')',
            'trx' => $trx,
            'wallet_type' => 'bonus_wallet',
            'remark' => 'referral_commission',
            'created_at' => now(),
        ];

        // Notificación al referidor
        notify($refer, 'REFERRAL_COMMISSION', [
            'amount' => showAmount($com, currencyFormat:false),
            'post_balance' => showAmount($refer->bonus_wallet, currencyFormat:false),
            'trx' => $trx,
            'level' => ordinal($i),
            'type' => $commissionType == 'deposit_commission' ? 'Deposit' :
                     ($commissionType == 'interest_commission' ? 'Interest' : 'Invest'),
        ]);
        $meUser = $user;
        $i = 1;
        $level = Referral::where('commission_type', $commissionType)->count();
        $transactions = [];

        while ($i <= $level) {
            $me = $meUser;
            $refer = $me->referrer;
            if ($refer == "") {
                break;
            }

            $commission = Referral::where('commission_type', $commissionType)->where('level', $i)->first();
            if (!$commission) {
                break;
            }

            $referralRanking = UserReferralsRanking::firstOrCreate(
                [
                    'codigo_referred' => $user->id,
                    'codigo_referrer' => $refer->id,
                ],
                [
                    'referral_bonus_times' => 0,
                    'referral_bonus_times_max' => $refer->level,
                    'referral_ranking' => $refer->user_ranking_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            // Calcular el porcentaje del bono basado en el ranking
            $bonusPercent = $commission->percent;
            if ($referralRanking->referral_bonus_times > 0) {
                // Si no es la primera vez, usar el porcentaje según el ranking
                $bonusPercent = $bonusPercent * ($referralRanking->referral_ranking / 100);
            }

            // Verificar si aún puede recibir bonos
            if ($referralRanking->referral_bonus_times < $referralRanking->referral_bonus_times_max) {
                $com = ($amount * $bonusPercent) / 100;
                $refer->bonus_wallet += $com;
                $refer->save();

                // Incrementar el contador de bonos
                $referralRanking->referral_bonus_times++;
                $referralRanking->save();

                $transactions[] = [
                    'user_id' => $refer->id,
                    'amount' => $com,
                    'post_balance' => $refer->bonus_wallet,
                    'charge' => 0,
                    'trx_type' => '+',
                    'details' => 'Nivel ' . $i . ' comisión de referido de ' . $user->username .
                               ' (Bono #' . $referralRanking->referral_bonus_times . ' de ' .
                               $referralRanking->referral_bonus_times_max . ')',
                    'trx' => $trx,
                    'wallet_type' => 'bonus_wallet',
                    'remark' => 'referral_commission',
                    'created_at' => now(),
                ];

                // Notificación al referidor
                notify($refer, 'REFERRAL_COMMISSION', [
                    'amount' => showAmount($com, currencyFormat:false),
                    'post_balance' => showAmount($refer->bonus_wallet, currencyFormat:false),
                    'trx' => $trx,
                    'level' => ordinal($i),
                    'type' => $commissionType == 'deposit_commission' ? 'Deposit' :
                             ($commissionType == 'interest_commission' ? 'Interest' : 'Invest'),
                ]);
            }

            $meUser = $refer;
            $i++;
        }

        if (!empty($transactions)) {
            Transaction::insert($transactions);
        }*/
    }

    /**
     * Capital return
     *
     * @param object $invest
     * @param object $user
     * @return void
     */

    public static function capitalReturn($invest, $wallet = 'interest_wallet')
    {
        $user = $invest->user;
        $user->$wallet += $invest->amount;
        $user->save();

        $invest->capital_back = 1;
        $invest->save();

        $transaction               = new Transaction();
        $transaction->user_id      = $user->id;
        $transaction->amount       = $invest->amount;
        $transaction->charge       = 0;
        $transaction->post_balance = $user->$wallet;
        $transaction->trx_type     = '+';
        $transaction->trx          = getTrx();
        $transaction->wallet_type  = $wallet;
        $transaction->remark       = 'capital_return';
        $transaction->details      = showAmount($invest->amount) . ' ' . gs()->cur_text . '' . @$invest->plan->name;
        $transaction->save();
    }
}
