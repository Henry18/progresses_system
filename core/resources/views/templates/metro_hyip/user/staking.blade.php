@extends($activeTemplate . 'layouts.master')
@section('content')
    <script>
        "use strict"

        function createCountDown(elementId, sec) {
            var tms = sec;
            var x = setInterval(function() {
                var distance = tms * 1000;
                var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                document.getElementById(elementId).innerHTML = days + "d: " + hours + "h " + minutes + "m " +
                    seconds + "s ";
                if (distance < 0) {
                    clearInterval(x);
                    document.getElementById(elementId).innerHTML = "COMPLETE";
                }
                tms--;
            }, 1000);
        }
    </script>

    <div class="mt-3">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="text-end">
                    <button class="btn btn--base stakingNow">
                        @lang('Staking Now')
                    </button>
                </div>
            </div>
            <div class="col-md-12">
                <div class="table-section">
                    <table class="table style-two table--responsive--lg">
                        <thead>
                            <tr>
                                <th>@lang('Invest Date')</th>
                                <th>@lang('Invest Amount')</th>
                                <th>@lang('Total Return')</th>
                                <th>@lang('Interest')</th>
                                <th>@lang('Remaining')</th>
                                <th>@lang('End At')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($myStakings as $staking)
                                <tr>
                                    <td>{{ showDateTime($staking->created_at) }}</td>
                                    <td>{{ showAmount($staking->invest_amount) }}</td>
                                    <td>{{ showAmount($staking->invest_amount + $staking->interest) }}</td>
                                    <td>{{ showAmount($staking->interest) }}</td>

                                    <td scope="row" class="font-weight-bold text-center">
                                        @if ($staking->end_at > now())
                                            <p id="counter{{ $staking->id }}" class="demo countdown timess2 "></p>
                                            <div class="progress">
                                                <div class="progress-bar customWidth progress-bar-striped" role="progressbar" style="width: {{ diffDatePercent($staking->created_at, $staking->end_at) }}%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100">
                                                </div>
                                            </div>
                                        @else
                                            <span class="badge badge--info">@lang('Completed')</span>
                                        @endif
                                    </td>
                                    <td>{{ showDateTime($staking->end_at) }}</td>
                                </tr>
                                @if (\Carbon\Carbon::parse($staking->end_at) > now())
                                    <script>
                                        createCountDown('counter<?php echo $staking->id; ?>', {{ abs(\Carbon\Carbon::parse($staking->end_at)->diffInSeconds()) }});
                                    </script>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="100%" class="text-center">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($myStakings->hasPages())
                <div class="custom--pagination">
                    {{ paginateLinks($myStakings) }}
                </div>
            @endif
            
        </div>
    </div>

    <div class="modal custom--modal fade" id="stakingModal">
        <div class="modal-dialog modal-dialog-centered modal-content-bg">
            <div class="modal-content">
                <div class="modal-header">
                    <strong class="modal-title text-white" id="ModalLabel">
                        @lang('Staking Now')
                    </strong>
                    <button type="button" data-bs-dismiss="modal">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('user.staking.save') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label>@lang('Duration')</label>
                            <select name="duration" class="form--control" required>
                                <option hidden>@lang('Select One')</option>
                                @foreach ($stakings as $staking)
                                    <option value="{{ $staking->id }}" data-interest="{{ $staking->interest_percent }}">{{ $staking->days }} @lang('Days - Interest') {{ $staking->interest_percent }}%</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label>@lang('Wallet')</label>
                            <select name="wallet" class="form--control" required>
                                <option hidden>@lang('Select One')</option>
                                <option value="deposit_wallet">@lang('Deposit Wallet - ') {{ showAmount(auth()->user()->deposit_wallet) }}</option>
                                <option value="interest_wallet">@lang('Interest Wallet - ') {{ showAmount(auth()->user()->interest_wallet) }}</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label>@lang('Amount') ({{ showAmount(gs('staking_min_amount')) . ' - ' . showAmount(gs('staking_max_amount')) }})</label>
                            <div class="input-group">
                                <input type="number" name="amount" class="form-control form--control" min="0" step="any" autocomplete="off" required>
                                <span class="input-group-text bg--base">{{ gs('cur_text') }}</span>
                            </div>
                        </div>
                        <span class="text--danger totalReturn">@lang('Total Return: ')<span class="returnAmount"></span></span>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--base btn-sm h-45 w-100 text-center">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@push('script')
    <script>
        (function($) {
            "use strict";
            $('.stakingNow').on('click', function() {
                let modal = $('#stakingModal');
                modal.find('[name=invest_id]').val($(this).data('id'));
                modal.modal('show');
            });

            let interest = 0,
                amount = 0,
                totalReturn = 0;

            $('[name=duration]').on('change', function() {
                interest = $(this).find(':selected').data('interest');
                calculateInterest();
            }).change();

            $('[name=amount]').on('input', function() {
                amount = $(this).val() * 1;
                calculateInterest();
            });

            function calculateInterest() {
                totalReturn = amount * interest / 100 + amount;
                if (totalReturn) {
                    $('.totalReturn').show();
                    $('.returnAmount').text(totalReturn.toFixed(2) + ` {{ __(gs('cur_text')) }}`);
                } else {
                    $('.totalReturn').hide();
                }
            }

        })(jQuery);
    </script>
@endpush
