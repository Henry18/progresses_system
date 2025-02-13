@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="mt-3">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="text-end">
                    <a href="{{ route('plan') }}" class="btn btn--base">
                        @lang('Investment Plan')
                    </a>
                </div>
            </div>
            <div class="col-md-12">
                <div class="table-section">
                    <table class="table style-two table--responsive--lg">
                        <thead>
                            <tr>
                                <th>@lang('Plan')</th>
                                <th>@lang('Return')</th>
                                <th>@lang('Wallet')</th>
                                <th class="text-center">@lang('Remaining Times')</th>
                                <th>@lang('Next Invest')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($scheduleInvests as $scheduleInvest)
                                @php
                                    $plan = $scheduleInvest->plan;
                                    $interest = $plan->interest_type == 1 ? ($scheduleInvest->amount * $plan->interest) / 100 : $plan->interest;
                                @endphp
                                <tr>
                                    <td>{{ __($scheduleInvest->plan->name) }} <br> {{ showAmount($scheduleInvest->amount) }} </td>
                                    <td>
                                        {{ showAmount($interest) }} @lang('every') {{ $plan->timeSetting->name }}
                                        <br>
                                        @lang('for')
                                        @if ($plan->lifetime)
                                            @lang('Lifetime')
                                        @else
                                            {{ $plan->repeat_time }}
                                            {{ $plan->timeSetting->name }}
                                        @endif
                                        @if ($plan->capital_back)
                                            + @lang('Capital')
                                        @endif
                                    </td>
                                    <td>{{ __(keyToTitle($scheduleInvest->wallet)) }}</td>
                                    <td class="text-center">{{ $scheduleInvest->rem_schedule_times }}</td>
                                    <td>{{ $scheduleInvest->next_invest ? showDateTime($scheduleInvest->next_invest) : '----' }}</td>
                                    <td>
                                        <button class="btn btn--base btn--sm detailsBtn" data-schedule_invest="{{ $scheduleInvest }}" data-interest="{{ getAmount($interest) }}" data-next_invest="{{ $scheduleInvest->next_invest ? showDateTime($scheduleInvest->next_invest) : '-----' }}">
                                            <i class="fa fa-desktop"></i>
                                        </button>
                                        @if ($scheduleInvest->rem_schedule_times)
                                            @if ($scheduleInvest->status)
                                                <button class="btn btn--success btn--sm confirmationBtn" data-question="@lang('Are you sure to pause this schedule invest?')" data-action="{{ route('user.invest.schedule.status', $scheduleInvest->id) }}">
                                                    <i class="fas fa-pause"></i>
                                                </button>
                                            @else
                                                <button class="btn btn--danger btn--sm confirmationBtn" data-question="@lang('Are you sure to continue this schedule invest?')" data-action="{{ route('user.invest.schedule.status', $scheduleInvest->id) }}">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="100%" class="text-center">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($scheduleInvests->hasPages())
                <div class="custom--pagination">
                    {{ paginateLinks($scheduleInvests) }}
                </div>
            @endif

        </div>
    </div>

    <div class="modal custom--modal fade" id="detailsModal">
        <div class="modal-dialog modal-dialog-centered modal-content-bg">
            <div class="modal-content">
                <div class="modal-header">
                    <strong class="modal-title text-white" id="ModalLabel">
                        @lang('Schedule Invest Details')
                    </strong>
                    <button type="button" class="close" data-bs-dismiss="modal">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between">
                            @lang('Plan Name')
                            <span class="planName"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            @lang('Invest Amount')
                            <span class="investAmount"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            @lang('Interest')
                            <span class="interestAmount"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between compoundInterestBlock">
                            @lang('Compound Interest')
                            <span class="compoundInterest"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            @lang('Schedule Times')
                            <span class="scheduleTimes"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            @lang('Remaining Schedule Times')
                            <span class="remScheduleTimes"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            @lang('Interval')
                            <span class="intervalHours"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            @lang('Next Invest')
                            <span class="nextInvest"></span>
                        </li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-md" data-bs-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>

    <x-confirmation-modal closeBtn="btn btn--danger btn--sm pill" submitBtn="btn btn--base pill btn--sm"/>
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";

            let curSym = `{{ gs('cur_sym') }}`;

            $('.detailsBtn').on('click', function() {
                let modal = $('#detailsModal');
                let data = $(this).data();
                let scheduleInvest = data.schedule_invest;

                modal.find('.planName').text(scheduleInvest.plan.name);
                modal.find('.investAmount').text(curSym + parseFloat(scheduleInvest.amount).toFixed(2));
                modal.find('.interestAmount').text(curSym + parseFloat(data.interest).toFixed(2));
                modal.find('.scheduleTimes').text(scheduleInvest.schedule_times);
                modal.find('.remScheduleTimes').text(scheduleInvest.rem_schedule_times);
                modal.find('.intervalHours').text(`${scheduleInvest.interval_hours} @lang('Hours')`);
                modal.find('.nextInvest').text(data.next_invest);

                if (scheduleInvest.compound_times) {
                    modal.find('.compoundInterest').text(`${scheduleInvest.compound_times} @lang('times')`);
                    $('.compoundInterestBlock').removeClass('d-none');
                } else {
                    $('.compoundInterestBlock').addClass('d-none');
                }

                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
