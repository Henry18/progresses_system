@extends('Template::layouts.master')

@php
    $kyc = getContent('kyc.content', true);
@endphp

@section('content')
    <div class="notice"></div>

    @if ($user->kv == Status::KYC_UNVERIFIED && $user->kyc_rejection_reason)
        <div class="alert border--base-two alert--base-two mb-4" role="alert">
            <div class="alert__icon d-flex align-items-center">
                <i class="fas fa-times"></i>
            </div>
            <p class="alert__content">
                <span class="fw-bold">@lang('KYC Documents Rejected')</span><br>
                <small>
                    {{ __(@$kyc->data_values->reject) }}
                    <a href="javascript::void(0)" class="link-color" data-bs-toggle="modal"
                        data-bs-target="#kycRejectionReason">@lang('Click here')</a> @lang('to show the reason').

                    <a href="{{ route('user.kyc.form') }}" class="link-color">@lang('Click Here')</a> @lang('to Re-submit Documents').
                    <a href="{{ route('user.kyc.data') }}" class="link-color">@lang('See KYC Data')</a>
                </small>
            </p>
        </div>
    @elseif($user->kv == Status::KYC_UNVERIFIED)
        <div class="alert border border--info alert--info mb-4" role="alert">
            <div class="alert__icon d-flex align-items-center"><i class="fas fa-exclamation-circle"></i>
            </div>
            <p class="alert__content">
                <span class="fw-bold">@lang('KYC Verification Required')</span><br>
                <small>{{ __(@$kyc->data_values->required) }} <a href="{{ route('user.kyc.form') }}">@lang('Click Here to Submit Documents')</a>
                </small>
            </p>
        </div>
    @elseif($user->kv == Status::KYC_PENDING)
        <div class="alert border border--warning alert--warning mb-4" role="alert">
            <div class="alert__icon d-flex align-items-center"><i class="las la-hourglass-half"></i>
            </div>
            <p class="alert__content">
                <span class="fw-bold">@lang('KYC Verification Pending')</span><br>
                <small>{{ __(@$kyc->data_values->pending) }} <a href="{{ route('user.kyc.data') }}">@lang('See KYC Data')</a>
                </small>
            </p>
        </div>
    @endif

    <div class="dashboard-widget-wrapper mb-60">
        <div class="row gy-4 justify-content-center">
            <div class="col-xxl-3 col-xl-4 col-sm-6">
                <div class="dashboard-widget">
                    <div class="dashboard-widget-content">
                        <div class="dashboard-widget-icon">
                            <i class="fa-solid fas fa-dollar-sign"></i>
                        </div>
                        <p class="dashboard-widget-title">@lang('Deposit Wallet Balance')</p>
                        <h4 class="dashboard-widget-amount">
                            {{ gs('cur_sym') }}{{ showAmount($user->deposit_wallet, currencyFormat: false) }}</h4>
                    </div>
                    <a href="{{ route('user.transactions', ['wallet_type' => 'deposit_wallet']) }}"
                        class="dashboard-widget-btn">
                        @lang('View All')
                    </a>
                </div>
            </div>
            <div class="col-xxl-3 col-xl-4 col-sm-6">
                <div class="dashboard-widget">
                    <div class="dashboard-widget-content">
                        <div class="dashboard-widget-icon">
                            <i class="fa-solid fas fa-wallet"></i>
                        </div>
                        <p class="dashboard-widget-title">@lang('Interest Wallet Balance')</p>
                        <h4 class="dashboard-widget-amount">
                            {{ gs('cur_sym') }}{{ showAmount($user->interest_wallet, currencyFormat: false) }}</h4>
                    </div>
                    <a href="{{ route('user.transactions', ['wallet_type' => 'interest_wallet']) }}"
                        class="dashboard-widget-btn">
                        @lang('View All')
                    </a>
                </div>
            </div>
            <div class="col-xxl-3 col-xl-4 col-sm-6">
                <div class="dashboard-widget">
                    <div class="dashboard-widget-content">
                        <div class="dashboard-widget-icon">
                            <i class="fa-solid fa-cubes"></i>
                        </div>
                        <p class="dashboard-widget-title">@lang('Total Invest')</p>
                        <h4 class="dashboard-widget-amount">
                            {{ gs('cur_sym') }}{{ showAmount($totalInvest, currencyFormat: false) }}</h4>
                    </div>
                    <a href="{{ route('user.transactions', ['remark' => 'invest']) }}" class="dashboard-widget-btn">
                        @lang('View All')
                    </a>
                </div>
            </div>
            <div class="col-xxl-3 col-xl-4 col-sm-6">
                <div class="dashboard-widget">
                    <div class="dashboard-widget-content">
                        <div class="dashboard-widget-icon">
                            <i class="fa-solid fas fa-credit-card"></i>
                        </div>
                        <p class="dashboard-widget-title">@lang('Total Deposit')</p>
                        <h4 class="dashboard-widget-amount">
                            {{ gs('cur_sym') }}{{ showAmount($totalDeposit, currencyFormat: false) }}</h4>
                    </div>
                    <a href="{{ route('user.deposit.history') }}" class="dashboard-widget-btn">
                        @lang('View All')
                    </a>
                </div>
            </div>
            <div class="col-xxl-3 col-xl-4 col-sm-6">
                <div class="dashboard-widget">
                    <div class="dashboard-widget-content">
                        <div class="dashboard-widget-icon">
                            <i class="fa-solid fas fa-cloud-download-alt"></i>
                        </div>
                        <p class="dashboard-widget-title">@lang('Total Withdraw')</p>
                        <h4 class="dashboard-widget-amount">
                            {{ gs('cur_sym') }}{{ showAmount($totalWithdraw, currencyFormat: false) }}</h4>
                    </div>
                    <a href="{{ route('user.withdraw.history') }}" class="dashboard-widget-btn">
                        @lang('View All')
                    </a>
                </div>
            </div>
            <div class="col-xxl-3 col-xl-4 col-sm-6">
                <div class="dashboard-widget">
                    <div class="dashboard-widget-content">
                        <div class="dashboard-widget-icon">
                            <i class="fa-solid fas fa-user-friends"></i>
                        </div>
                        <p class="dashboard-widget-title">@lang('Referral Earnings')</p>
                        <h4 class="dashboard-widget-amount">
                            {{ gs('cur_sym') }}{{ showAmount($referralEarnings, currencyFormat: false) }}</h4>
                    </div>
                    <a href="{{ route('user.transactions', ['remark' => 'referral_commission']) }}"
                        class="dashboard-widget-btn">
                        @lang('View All')
                    </a>
                </div>
            </div>
            <div class="col-xxl-3 col-xl-4 col-sm-6">
                <div class="dashboard-widget">
                    <div class="dashboard-widget-content">
                        <div class="dashboard-widget-icon">
                            <i class="fa-solid fas fa-exchange"></i>
                        </div>
                        <p class="dashboard-widget-title">@lang('Transaction')</p>
                        <h4 class="dashboard-widget-amount">{{ getAmount($user?->transactions?->count() ?? 0) }}</h4>
                    </div>
                    <a href="{{ route('user.transactions') }}" class="dashboard-widget-btn">
                        @lang('View All')
                    </a>
                </div>
            </div>
            <div class="col-xxl-3 col-xl-4 col-sm-6">
                <div class="dashboard-widget">
                    <div class="dashboard-widget-content">
                        <div class="dashboard-widget-icon">
                            <i class="fa-solid fas fa-headphones"></i>
                        </div>
                        <p class="dashboard-widget-title">@lang('Support Ticket')</p>
                        <h4 class="dashboard-widget-amount">{{ getAmount($totalTicket) }}</h4>
                    </div>
                    <a href="{{ route('ticket.index') }}" class="dashboard-widget-btn">
                        @lang('View All')
                    </a>
                </div>
            </div>
        </div>
    </div>

    <h4 class="mb-4"> @lang('Latest Transaction') </h4>
    <div class="dashboard-table-wrapper">
        <table class="table table--responsive--xl">
            <thead>
                <tr>
                    <th>@lang('Date')</th>
                    <th>@lang('Transaction ID')</th>
                    <th>@lang('Amount')</th>
                    <th>@lang('Wallet')</th>
                    <th>@lang('Details')</th>
                    <th>@lang('Post Balance')</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $trx)
                    <tr>
                        <td>
                            {{ showDatetime($trx->created_at, 'd/m/Y') }}
                        </td>
                        <td><span class="text--primary">{{ $trx->trx }}</span></td>

                        <td>
                            @if ($trx->trx_type == '+')
                                <span class="text--success">+
                                    {{ __(gs('cur_sym')) }}{{ getAmount($trx->amount) }}</span>
                            @else
                                <span class="text--danger">-
                                    {{ __(gs('cur_sym')) }}{{ getAmount($trx->amount) }}</span>
                            @endif
                        </td>
                        <td>
                            @if ($trx->wallet_type == 'deposit_wallet')
                                <span class="badge badge--info">@lang('Deposit Wallet')</span>
                            @else
                                <span class="badge badge--primary">@lang('Interest Wallet')</span>
                            @endif
                        </td>
                        <td>{{ $trx->details }}</td>
                        <td><span>
                                {{ __(gs('cur_sym')) }}{{ getAmount($trx->post_balance) }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="100%" class="text-center">@lang('No Transaction Found')</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($user->kv == Status::KYC_UNVERIFIED && $user->kyc_rejection_reason)
        <div class="modal fade" id="kycRejectionReason">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title">@lang('KYC Document Rejection Reason')</h6>
                        <button type="button" class="close" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>{{ $user->kyc_rejection_reason }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('style')
    <style>
        .notice .alert {
            margin-bottom: 1.5rem;
        }
    </style>
@endpush
@push('script')
    <script>
        'use strict';
        (function($) {
            @if ($isHoliday)
                function createCountDown(elementId, sec) {
                    var tms = sec;
                    var x = setInterval(function() {
                        var distance = tms * 1000;
                        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                        var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                        var days = `<span>${days}d</span>`;
                        var hours = `<span>${hours}h</span>`;
                        var minutes = `<span>${minutes}m</span>`;
                        var seconds = `<span>${seconds}s</span>`;
                        document.getElementById(elementId).innerHTML = days + ' ' + hours + " " + minutes +
                            " " + seconds;
                        if (distance < 0) {
                            clearInterval(x);
                            document.getElementById(elementId).innerHTML = "COMPLETE";
                        }
                        tms--;
                    }, 1000);
                }
                createCountDown('counter', {{ abs(\Carbon\Carbon::parse($nextWorkingDay)->diffInSeconds()) }});
            @endif
        })(jQuery);
    </script>
@endpush
