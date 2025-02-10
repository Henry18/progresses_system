@extends($activeTemplate . 'layouts.master')

@section('content')
    <section class="pb-60 pt-60">
        <div class="row gy-3">
            <div class="col-xl-4">
                <div class="card custom--card">
                    <div class="card-header">
                        <h5>@lang('Plan Information')</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            @php
                                $plan = $invest->plan;
                            @endphp
                            <li class="list-group-item d-flex justify-content-between">
                                @lang('Plan Name')
                                <span>{{ __($plan->name) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                @lang('Investable Amount')
                                <span>
                                    @if ($plan->fixed_amount > 0)
                                        {{ showAmount($plan->fixed_amount) }}
                                    @else
                                        {{ showAmount($plan->minimum) }} - {{ showAmount($plan->maximum) }}
                                    @endif
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                @lang('Interest')
                                <span>{{ showAmount($plan->interest, currencyFormat: false) }}{{ $plan->interest_type == 1 ? '%' : gs('cur_text') }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                @lang('Hold Capital')
                                <span>
                                    @if ($plan->hold_capital)
                                        @lang('Yes')
                                    @else
                                        @lang('No')
                                    @endif
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                @lang('Repeat Time')
                                <span>
                                    @if ($plan->repeat_time)
                                        {{ $plan->repeat_time }} @lang('Months')
                                    @else
                                        @lang('Lifetime')
                                    @endif
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                @lang('Status')
                                <span>
                                    @if ($plan->status)
                                        <span class="badge badge--success">@lang('Enable')</span>
                                    @else
                                        <span class="badge badge--warning">@lang('Disable')</span>
                                    @endif
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-6">
                <div class="card custom--card">
                    <div class="card-header">
                        <h5>@lang('Basic Information')</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between">
                                @lang('Initial Invest')
                                <span>{{ showAmount($invest->initial_amount) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                @lang('Current Invest')
                                <span>{{ showAmount($invest->amount) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                @lang('Invested')
                                <span>{{ showDateTime($invest->created_at) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                @lang('Initial Interest Pay')
                                <span>{{ showAmount($invest->initial_interest) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                @lang('Current Interest Pay')
                                <span>{{ showAmount($invest->amount * ($invest->interest_rate/100)) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                @lang('Pay Interest Interval')
                                <span>@lang('Every ') @lang('days')</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                @lang('Status')
                                <span>
                                    @if ($invest->status)
                                        <span class="badge badge--success">@lang('Running')</span>
                                    @else
                                        <span class="badge badge--info">@lang('Completed')</span>
                                    @endif
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-6">
                <div class="card custom--card">
                    <div class="card-header">
                        <h5>@lang('Other Information')</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between">
                                @lang('Total time')
                                <span>
                                    @if ($invest->period != -1)
                                        {{ $invest->period }} @lang('Months') ({{ $invest->period * 21 }} @lang('days'))
                                    @else
                                        @lang('Lifetime')
                                    @endif
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                @lang('Elapsed Time')
                                <span>{{ ($invest->return_rec_time * 21) + (21 - $invest->rec_total_days) }} @lang('days')</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                @lang('Total Paid Amount')
                                <span>{{ showAmount($invest->paid) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                @lang('Should Pay')
                                <span>
                                    @if ($invest->should_pay != -1)
                                        {{ showAmount($invest->should_pay) }}
                                    @else
                                        **
                                    @endif
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                @lang('Last Paid Time')
                                <span>{{ showDateTime($invest->last_time) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                @lang('Next Pay Time')
                                <span>{{ showDateTime($invest->next_time) }}</span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between">
                                @lang('Net Interest')
                                <span>{{ showAmount($invest->net_interest) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                @lang('Fractional Capital Pay')
                                <span>
                                    @if ($invest->fractional_capital)
                                        @lang('Yes')
                                    @else
                                        @lang('No')
                                    @endif
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-lg-12">
                @if ($invest->compound_times)
                    <h5 class="my-2">@lang('All Interests & Compound Investment')</h5>
                @else
                    <h5 class="my-2">@lang('All Interests')</h5>
                @endif

                <table class="table style-two table-responsive--lg">
                    <thead>
                        <tr>
                            <th>@lang('TRX')</th>
                            <th>@lang('Transacted')</th>
                            <th>@lang('Amount')</th>
                            <th>@lang('Post Balance')</th>
                            <th>@lang('Details')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $trx)
                            <tr>
                                <td><strong>{{ $trx->trx }}</strong></td>
                                <td>{{ showDateTime($trx->created_at) }}<br>{{ diffForHumans($trx->created_at) }}</td>

                                <td class="budget">
                                    <span class="fw-bold @if ($trx->trx_type == '+') text--success @else text--danger @endif">
                                        {{ $trx->trx_type }} {{ showAmount($trx->amount) }}
                                    </span>
                                </td>

                                <td class="budget">
                                    {{ showAmount($trx->post_balance) }}
                                </td>

                                <td>{{ __($trx->details) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table><!-- table end -->

                @if ($transactions->hasPages())
                    <div class="custom--pagination">
                        {{ paginateLinks($transactions) }}
                    </div>
                @endif

            </div>
        </div>
    </section>
@endsection
