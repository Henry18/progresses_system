<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Lib\HyipLab;
use App\Models\AdminNotification;
use App\Models\Transaction;
use App\Models\Withdrawal;
use App\Models\WithdrawMethod;
use App\Models\WithdrawalPeriod;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WithdrawController extends Controller
{

    public function withdrawMoney()
    {
        $pageTitle      = 'Withdraw Money';
        $withdrawMethod = WithdrawMethod::with('activeWithdrawalPeriods')->active()->get();
        $isHoliday      = HyipLab::isHoliDay(now()->toDateTimeString(), gs());
        $nextWorkingDay = now()->toDateString();

        if ($isHoliday && !gs()->holiday_withdraw) {
            $nextWorkingDay = HyipLab::nextWorkingDay(24);
            $nextWorkingDay = Carbon::parse($nextWorkingDay)->toDateString();
        }

        return view('Template::user.withdraw.methods', compact('pageTitle', 'withdrawMethod', 'isHoliday', 'nextWorkingDay'));
    }

    public function withdrawStore(Request $request)
    {
        $isHoliday = HyipLab::isHoliDay(now()->toDateTimeString(), gs());
        if ($isHoliday && !gs()->holiday_withdraw) {
            $notify[] = ['error', 'Today is holiday. You\'re unable to withdraw today'];
            return back()->withNotify($notify);
        }

        $request->validate([
            'method_code' => 'required',
            'amount'      => 'required|numeric',
            'terms_accepted' => 'required|accepted',
        ], [
            'terms_accepted.required' => 'You must accept the terms and conditions to proceed',
            'terms_accepted.accepted' => 'You must accept the terms and conditions to proceed',
        ]);

        $method = WithdrawMethod::where('id', $request->method_code)->active()->firstOrFail();
        $user   = auth()->user();
        $wallet = $request->type;

        if ($request->amount < $method->min_limit) {
            $notify[] = ['error', 'Your requested amount is smaller than minimum amount'];
            return back()->withNotify($notify)->withInput($request->all());
        }
        if ($request->amount > $method->max_limit) {
            $notify[] = ['error', 'Your requested amount is larger than maximum amount'];
            return back()->withNotify($notify)->withInput($request->all());
        }

        if ($request->amount > $user->$wallet) {
            $notify[] = ['error', 'Insufficient balance for withdrawal'];
            return back()->withNotify($notify)->withInput($request->all());
        }

        // Determine charge based on wallet type and withdrawal period
        $isWithinPeriod = $method->isWithinWithdrawalPeriod();

        if ($wallet == 'special_wallet') {
            // Special wallet uses special charges based on period
            if ($isWithinPeriod) {
                $charge = $method->fixed_charge_special + ($request->amount * $method->percent_charge_special / 100);
            } else {
                // Out of period - apply penalty charges
                $charge = $method->fixed_charge_special_out + ($request->amount * $method->percent_charge_special_out / 100);
            }
        } elseif ($wallet == 'bonus_wallet') {
            $charge = $method->fixed_charge_bonus + ($request->amount * $method->percent_charge_bonus / 100);
        } else {
            // interest_wallet - standard charges
            $charge = $method->fixed_charge + ($request->amount * $method->percent_charge / 100);
        }

        $afterCharge = $request->amount - $charge;

        if ($afterCharge <= 0) {
            $notify[] = ['error', 'Withdraw amount must be sufficient for charges'];
            return back()->withNotify($notify)->withInput($request->all());
        }

        $finalAmount = $afterCharge * $method->rate;

        $withdraw               = new Withdrawal();
        $withdraw->method_id    = $method->id;
        $withdraw->user_id      = $user->id;
        $withdraw->amount       = $request->amount;
        $withdraw->currency     = $method->currency;
        $withdraw->rate         = $method->rate;
        $withdraw->charge       = $charge;
        $withdraw->final_amount = $finalAmount;
        $withdraw->withdraw_wallet = $wallet;
        $withdraw->after_charge = $afterCharge;
        $withdraw->trx          = getTrx();
        $withdraw->is_within_period = $isWithinPeriod ? 1 : 0;
        $withdraw->save();

        session()->put('wtrx', $withdraw->trx);
        return to_route('user.withdraw.preview');
    }

    public function withdrawPreview()
    {
        $withdraw  = Withdrawal::with('method', 'user')->where('trx', session()->get('wtrx'))->where('status', Status::PAYMENT_INITIATE)->orderBy('id', 'desc')->firstOrFail();
        $pageTitle = 'Withdraw Preview';
        return view('Template::user.withdraw.preview', compact('pageTitle', 'withdraw'));
    }

    public function withdrawSubmit(Request $request)
    {
        $withdraw = Withdrawal::with('method', 'user')->where('trx', session()->get('wtrx'))->where('status', Status::PAYMENT_INITIATE)->orderBy('id', 'desc')->firstOrFail();

        $method = $withdraw->method;
        $wallet = $withdraw->withdraw_wallet;
        if ($method->status == Status::DISABLE) {
            abort(404);
        }

        $formData = @$method->form->form_data ?? [];

        $formProcessor  = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $userData = $formProcessor->processFormData($request, $formData);

        $user = auth()->user();
        if ($user->ts) {
            $response = verifyG2fa($user, $request->authenticator_code);
            if (!$response) {
                $notify[] = ['error', 'Wrong verification code'];
                return back()->withNotify($notify)->withInput($request->all());
            }
        }

        if ($withdraw->amount > $user->$wallet) {
            $notify[] = ['error', 'Your request amount is larger then your current balance.'];
            return back()->withNotify($notify)->withInput($request->all());
        }

        $withdraw->status               = Status::PAYMENT_PENDING;
        $withdraw->withdraw_information = $userData;
        $withdraw->save();
        $user->$wallet -= $withdraw->amount;
        $user->save();

        $transaction               = new Transaction();
        $transaction->user_id      = $withdraw->user_id;
        $transaction->amount       = $withdraw->amount;
        $transaction->post_balance = $user->$wallet;
        $transaction->charge       = $withdraw->charge;
        $transaction->trx_type     = '-';
        $transaction->details      = 'Withdraw request via ' . $withdraw->method->name;
        $transaction->trx          = $withdraw->trx;
        $transaction->remark       = 'withdraw';
        $transaction->save();

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $user->id;
        $adminNotification->title     = 'New withdraw request from ' . $user->username;
        $adminNotification->click_url = urlPath('admin.withdraw.data.details', $withdraw->id);
        $adminNotification->save();

        notify($user, 'WITHDRAW_REQUEST', [
            'method_name'     => $withdraw->method->name,
            'method_currency' => $withdraw->currency,
            'method_amount'   => showAmount($withdraw->final_amount, currencyFormat: false),
            'amount'          => showAmount($withdraw->amount, currencyFormat: false),
            'charge'          => showAmount($withdraw->charge, currencyFormat: false),
            'rate'            => showAmount($withdraw->rate, currencyFormat: false),
            'trx'             => $withdraw->trx,
            'post_balance'    => showAmount($user->$wallet, currencyFormat: false),
        ]);

        $notify[] = ['success', 'Withdraw request sent successfully'];
        return to_route('user.withdraw.history')->withNotify($notify);
    }

    public function withdrawLog(Request $request)
    {
        $pageTitle = "Withdrawal Log";
        $withdraws = Withdrawal::where('user_id', auth()->id())->where('status', '!=', Status::PAYMENT_INITIATE);
        if ($request->search) {
            $withdraws = $withdraws->where('trx', $request->search);
        }
        $withdraws = $withdraws->with('method')->orderBy('id', 'desc')->paginate(getPaginate());
        return view('Template::user.withdraw.log', compact('pageTitle', 'withdraws'));
    }
}
