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
                var days = `<span>${days}d</span>`;
                var hours = `<span>${hours}h</span>`;
                var minutes = `<span>${minutes}m</span>`;
                var seconds = `<span>${seconds}s</span>`;
                document.getElementById(elementId).innerHTML = days + ' ' + hours + " " + minutes + " " + seconds;
                if (distance < 0) {
                    clearInterval(x);
                    document.getElementById(elementId).innerHTML = "COMPLETE";
                }
                tms--;
            }, 1000);
        }
    </script>
    <section class="pt-120">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-10">
                @if ($isHoliday && !gs('holiday_withdraw'))
                    <div class="card custom--card @if ($isHoliday && !gs('holiday_withdraw')) card countdown-card @endif">
                        <div class="card-body">
                            <div class="text-center">
                                <h4 class="mb-3">@lang('Withdrawal request is disable for today. Please wait for next working day.')</h4>
                                <h2 class="text--base mb-3">@lang('Next Working Day')</h2>
                                <div id="counter" class="countdown-wrapper"></div>
                                <script>
                                    createCountDown('counter', {{ abs(\Carbon\Carbon::parse($nextWorkingDay)->diffInSeconds()) }});
                                </script>
                            </div>
                        </div>
                    </div>
                @else
                    <form action="{{ route('user.withdraw.money') }}" method="post" class="withdraw-form">
                        @csrf
                        <div class="gateway-card">
                        <select name="type" id="type" class="form-control wallet_type">
                            <option value="interest_wallet" selected>Billetera de Intereses</option>
                            <option value="bonus_wallet">Billetera de Bonos</option>
                            <option value="special_wallet">Billetera Especial</option>
                        </select>
                            <div class="row justify-content-center gy-sm-4 gy-3">
                                <div class="col-lg-6">
                                    <div class="payment-system-list is-scrollable gateway-option-list">
                                        @foreach ($withdrawMethod as $data)
                                            <label for="{{ titleToKey($data->name) }}" class="payment-item @if ($loop->index > 4) d-none @endif gateway-option">
                                                <div class="payment-item__info">
                                                    <span class="payment-item__check"></span>
                                                    <span class="payment-item__name">{{ __($data->name) }}</span>
                                                </div>
                                                <div class="payment-item__thumb">
                                                    <img class="payment-item__thumb-img" src="{{ getImage(getFilePath('withdrawMethod') . '/' . $data->image) }}" alt="@lang('payment-thumb')">
                                                </div>
                                                <input class="payment-item__radio gateway-input" id="{{ titleToKey($data->name) }}" hidden data-gateway='@json($data)' type="radio" name="method_code" value="{{ $data->id }}" @if (old('method_code')) @checked(old('method_code') == $data->id) @else @checked($loop->first) @endif data-min-amount="{{ showAmount($data->min_limit) }}" data-max-amount="{{ showAmount($data->max_limit) }}">
                                            </label>
                                        @endforeach
                                        @if ($withdrawMethod->count() > 4)
                                            <button type="button" class="payment-item__btn more-gateway-option">
                                                <p class="payment-item__btn-text">@lang('Show All Payment Options')</p>
                                                <span class="payment-item__btn__icon"><i class="fas fa-chevron-down"></i></i></span>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="payment-system-list p-3">
                                        <div class="deposit-info">
                                            <div class="deposit-info__title">
                                                <p class="text mb-0">@lang('Amount')</p>
                                            </div>
                                            <div class="deposit-info__input">
                                                <div class="deposit-info__input-group input-group">
                                                    <span class="deposit-info__input-group-text px-2">{{ gs('cur_sym') }}</span>
                                                    <input type="text" class="form-control form--control amount" name="amount" placeholder="@lang('00.00')" value="{{ old('amount') }}" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="deposit-info">
                                            <div class="deposit-info__title">
                                                <p class="text has-icon"> @lang('Limit')</p>
                                            </div>
                                            <div class="deposit-info__input">
                                                <p class="text"><span class="gateway-limit">@lang('0.00')</span> </p>
                                            </div>
                                        </div>
                                        <div class="deposit-info">
                                            <div class="deposit-info__title">
                                                <p class="text has-icon">@lang('Processing Charge')
                                                    <span data-bs-toggle="tooltip" title="@lang('Processing charge for withdraw method')" class="proccessing-fee-info"><i class="las la-info-circle"></i> </span>
                                                </p>
                                            </div>
                                            <div class="deposit-info__input">
                                                <p class="text">{{ gs('cur_sym') }}<span class="processing-fee">@lang('0.00')</span>
                                                    {{ __(gs('cur_text')) }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="deposit-info total-amount pt-3">
                                            <div class="deposit-info__title">
                                                <p class="text">@lang('Receivable')</p>
                                            </div>
                                            <div class="deposit-info__input">
                                                <p class="text">{{ gs('cur_sym') }}<span class="final-amount">@lang('0.00')</span>
                                                    {{ __(gs('cur_text')) }}</p>
                                            </div>
                                        </div>

                                        <div class="deposit-info gateway-conversion d-none total-amount pt-2">
                                            <div class="deposit-info__title">
                                                <p class="text">@lang('Conversion')
                                                </p>
                                            </div>
                                            <div class="deposit-info__input">
                                                <p class="text"></p>
                                            </div>
                                        </div>
                                        <div class="deposit-info conversion-currency d-none total-amount pt-2">
                                            <div class="deposit-info__title">
                                                <p class="text">
                                                    @lang('In') <span class="gateway-currency"></span>
                                                </p>
                                            </div>
                                            <div class="deposit-info__input">
                                                <p class="text">
                                                    <span class="in-currency"></span>
                                                </p>
                                            </div>
                                        </div>
                                        <!-- Period Warning for Special Wallet -->
                                        <div class="deposit-info period-warning d-none pt-2">
                                            <div class="alert alert-warning mb-2 p-2">
                                                <small>
                                                    <i class="las la-exclamation-triangle"></i>
                                                    <span class="period-warning-text"></span>
                                                </small>
                                            </div>
                                        </div>

                                        <!-- Terms and Conditions Hidden Input -->
                                        <input type="hidden" name="terms_accepted" id="terms_accepted_input" value="">

                                        <button type="button" class="btn btn--base w-100 open-terms-modal" disabled>
                                            @lang('Confirm Withdraw')
                                        </button>
                                        <div class="info-text pt-3">
                                            <p class="text">@lang('Safely withdraw your funds using our highly secure process and various withdrawal method')</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Terms and Conditions Modal -->
                    <div class="modal fade" id="withdrawTermsModal" tabindex="-1" aria-labelledby="withdrawTermsModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content" style="background-color: #1a1754; border: 1px solid #5a48e0;">
                                <div class="modal-header" style="border-bottom: 1px solid #5a48e0;">
                                    <h5 class="modal-title text-white" id="withdrawTermsModalLabel">
                                        <i class="las la-file-contract"></i> @lang('Withdrawal Terms & Conditions')
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-white">
                                    <!-- Standard Disclaimer -->
                                    <div class="terms-section mb-4">
                                        <h6 class="text-warning"><i class="las la-exclamation-circle"></i> @lang('Important Notice')</h6>
                                        <div class="terms-content p-3" style="background: rgba(90, 72, 224, 0.1); border-radius: 8px; max-height: 200px; overflow-y: auto;">
                                            <p>@lang('By proceeding with this withdrawal, you acknowledge and agree to the following terms:')</p>
                                            <ul>
                                                <li>@lang('Withdrawal requests are processed within 24-48 business hours.')</li>
                                                <li>@lang('Processing fees are non-refundable once the withdrawal is initiated.')</li>
                                                <li>@lang('You are responsible for providing accurate payment details.')</li>
                                                <li>@lang('The platform is not responsible for delays caused by third-party payment processors.')</li>
                                                <li>@lang('Withdrawals may be subject to verification for security purposes.')</li>
                                            </ul>
                                        </div>
                                    </div>

                                    <!-- Special Wallet Warning (shown only for special_wallet) -->
                                    <div class="special-wallet-warning d-none mb-4">
                                        <h6 class="text-danger"><i class="las la-calendar-times"></i> @lang('Special Wallet Notice')</h6>
                                        <div class="p-3" style="background: rgba(220, 53, 69, 0.1); border-radius: 8px; border: 1px solid rgba(220, 53, 69, 0.3);">
                                            <p class="mb-2 special-period-status"></p>

                                            <!-- Allowed Periods List -->
                                            <div class="allowed-periods-section d-none mt-3">
                                                <p class="mb-2 text-info"><i class="las la-calendar-check"></i> @lang('Allowed withdrawal periods for this method:')</p>
                                                <ul class="allowed-periods-list mb-2" style="list-style: none; padding-left: 0;"></ul>
                                            </div>

                                            <p class="mb-0 text-warning special-charge-info"></p>
                                        </div>
                                    </div>

                                    <!-- Terms Checkbox -->
                                    <div class="form-check mt-3">
                                        <input class="form-check-input" type="checkbox" id="termsCheckbox" style="width: 20px; height: 20px;">
                                        <label class="form-check-label ms-2" for="termsCheckbox" style="cursor: pointer;">
                                            @lang('I have read and accept the terms and conditions for this withdrawal')
                                        </label>
                                    </div>
                                </div>
                                <div class="modal-footer" style="border-top: 1px solid #5a48e0;">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Cancel')</button>
                                    <button type="button" class="btn btn--base confirm-withdraw-btn" disabled>
                                        <i class="las la-check-circle"></i> @lang('Accept & Withdraw')
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

@endsection



@push('script')
    <script>
        "use strict";
        (function($) {

            var amount = parseFloat($('.amount').val() || 0);
            var walletType = '';
            var gateway, minAmount, maxAmount;
            let percentCharge = 0;
            let fixedCharge = 0;
            let totalPercentCharge = 0;

            // Withdrawal periods data from server
            var withdrawalPeriods = @json($withdrawMethod->mapWithKeys(function($method) {
                return [$method->id => [
                    'isWithinPeriod' => $method->isWithinWithdrawalPeriod(),
                    'periods' => $method->activeWithdrawalPeriods->map(function($p) {
                        return [
                            'start' => $p->start_date->format('M d'),
                            'end' => $p->end_date->format('M d')
                        ];
                    })
                ]];
            }));

            var isWithinPeriod = false;

            $('.amount').on('input', function(e) {
                walletType = $('.wallet_type').val();
                amount = parseFloat($(this).val());
                if (!amount) {
                    amount = 0;
                }
                calculation();
            });

            $('.wallet_type').on('change', function(e) {
                walletType = $(this).val();
                amount = parseFloat($('.amount').val() || 0);
                if (!amount) {
                    amount = 0;
                }
                calculation();
            });

            $('.gateway-input').on('change', function(e) {
                gatewayChange();
            });

            function gatewayChange() {
                let gatewayElement = $('.gateway-input:checked');
                let methodCode = gatewayElement.val();

                gateway = gatewayElement.data('gateway');
                minAmount = gatewayElement.data('min-amount');
                maxAmount = gatewayElement.data('max-amount');

                // Get period status for this method
                if (withdrawalPeriods[methodCode]) {
                    isWithinPeriod = withdrawalPeriods[methodCode].isWithinPeriod;
                } else {
                    isWithinPeriod = false;
                }

                calculation();
            }

            gatewayChange();

            $(".more-gateway-option").on("click", function(e) {
                let paymentList = $(".gateway-option-list");
                paymentList.find(".gateway-option").removeClass("d-none");
                $(this).addClass('d-none');
                paymentList.animate({
                    scrollTop: (paymentList.height() - 60)
                }, 'slow');
            });

            function calculation() {
                if (!gateway) return;
                walletType = $('.wallet_type').val();
                $(".gateway-limit").text(minAmount + " - " + maxAmount);

                // Determine charges based on wallet type and period
                if (walletType == 'special_wallet') {
                    if (isWithinPeriod) {
                        percentCharge = parseFloat(gateway.percent_charge_special || 0);
                        fixedCharge = parseFloat(gateway.fixed_charge_special || 0);
                        $(".period-warning").addClass('d-none');
                    } else {
                        percentCharge = parseFloat(gateway.percent_charge_special_out || 0);
                        fixedCharge = parseFloat(gateway.fixed_charge_special_out || 0);
                        $(".period-warning").removeClass('d-none');
                        $(".period-warning-text").text("@lang('You are withdrawing outside the allowed period. Higher charges apply.')");
                    }
                } else if (walletType == 'bonus_wallet') {
                    percentCharge = parseFloat(gateway.percent_charge_bonus || 0);
                    fixedCharge = parseFloat(gateway.fixed_charge_bonus || 0);
                    $(".period-warning").addClass('d-none');
                } else {
                    percentCharge = parseFloat(gateway.percent_charge || 0);
                    fixedCharge = parseFloat(gateway.fixed_charge || 0);
                    $(".period-warning").addClass('d-none');
                }

                if (amount) {
                    totalPercentCharge = parseFloat(amount / 100 * percentCharge);
                    let processingFeeInfo = `${parseFloat(percentCharge).toFixed(2)}% + {{ __(gs('cur_text')) }}${parseFloat(fixedCharge).toFixed(2)} @lang('charge for processing fees')`;
                    $(".proccessing-fee-info").attr("data-bs-original-title", processingFeeInfo);
                } else {
                    totalPercentCharge = 0;
                }

                let totalCharge = parseFloat(totalPercentCharge + fixedCharge);
                let totalAmount = parseFloat((amount || 0) - totalPercentCharge - fixedCharge);

                $(".final-amount").text(totalAmount.toFixed(2));
                $(".processing-fee").text(totalCharge.toFixed(2));
                $("input[name=currency]").val(gateway.currency);
                $(".gateway-currency").text(gateway.currency);

                if (amount < Number(gateway.min_limit) || amount > Number(gateway.max_limit)) {
                    $(".open-terms-modal").attr('disabled', true);
                } else {
                    $(".open-terms-modal").removeAttr('disabled');
                }

                if (gateway.currency != "{{ gs('cur_text') }}") {
                    $('.withdraw-form').addClass('adjust-height')
                    $(".gateway-conversion, .conversion-currency").removeClass('d-none');
                    $(".gateway-conversion").find('.deposit-info__input .text').html(
                        `1 {{ __(gs('cur_text')) }} = <span class="rate">${parseFloat(gateway.rate).toFixed(2)}</span>  <span class="method_currency">${gateway.currency}</span>`
                    );
                    $('.in-currency').text(parseFloat(totalAmount * gateway.rate).toFixed(2))
                } else {
                    $(".gateway-conversion, .conversion-currency").addClass('d-none');
                    $('.withdraw-form').removeClass('adjust-height')
                }
            }

            // Open terms modal
            $('.open-terms-modal').on('click', function() {
                walletType = $('.wallet_type').val();
                let methodCode = $('.gateway-input:checked').val();

                // Update modal content based on wallet type
                if (walletType == 'special_wallet') {
                    $('.special-wallet-warning').removeClass('d-none');

                    // Get periods for this method
                    let methodPeriods = withdrawalPeriods[methodCode] ? withdrawalPeriods[methodCode].periods : [];

                    if (isWithinPeriod) {
                        $('.special-period-status').html('<i class="las la-check-circle text-success"></i> @lang("You are within an allowed withdrawal period.")');
                        $('.special-charge-info').text('@lang("Standard special wallet charges will apply.")');
                        $('.allowed-periods-section').addClass('d-none');
                    } else {
                        $('.special-period-status').html('<i class="las la-times-circle text-danger"></i> @lang("You are outside the allowed withdrawal periods.")');
                        $('.special-charge-info').text('@lang("Penalty charges will be applied to this withdrawal: ") ' + percentCharge.toFixed(2) + '% + {{ __(gs("cur_text")) }}' + fixedCharge.toFixed(2));

                        // Show allowed periods
                        if (methodPeriods.length > 0) {
                            $('.allowed-periods-section').removeClass('d-none');
                            let periodsHtml = '';
                            methodPeriods.forEach(function(period) {
                                periodsHtml += `<li class="mb-1"><i class="las la-calendar text-success"></i> ${period.start} - ${period.end}</li>`;
                            });
                            $('.allowed-periods-list').html(periodsHtml);
                        } else {
                            $('.allowed-periods-section').addClass('d-none');
                        }
                    }
                } else {
                    $('.special-wallet-warning').addClass('d-none');
                }

                // Reset checkbox
                $('#termsCheckbox').prop('checked', false);
                $('.confirm-withdraw-btn').attr('disabled', true);

                // Show modal
                var modal = new bootstrap.Modal(document.getElementById('withdrawTermsModal'));
                modal.show();
            });

            // Enable/disable confirm button based on checkbox
            $('#termsCheckbox').on('change', function() {
                if ($(this).is(':checked')) {
                    $('.confirm-withdraw-btn').removeAttr('disabled');
                } else {
                    $('.confirm-withdraw-btn').attr('disabled', true);
                }
            });

            // Confirm withdrawal
            $('.confirm-withdraw-btn').on('click', function() {
                $('#terms_accepted_input').val('1');
                $('#withdrawTermsModal').modal('hide');
                $('.withdraw-form').submit();
            });

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            $('.gateway-input').change();
        })(jQuery);
    </script>
@endpush
<style>
    #type {
  appearance: none;
  background-color: #1a1754;
  color: #ffffff;
  border: 2px solid #5a48e0;
  padding: 10px;
  border-radius: 5px;
  font-size: 16px;
  outline: none;
  cursor: pointer;
  width: 100%;
  background-image: url("data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='white'%3E%3Cpath fill-rule='evenodd' d='M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' clip-rule='evenodd'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 10px center;
  background-size: 15px;
  margin-bottom: 12px;
}

#type:hover {
  background-color: #2b267a;
  border-color: #7a65ff;
}

#type:focus {
  border-color: #a28bff;
  box-shadow: 0 0 5px rgba(162, 139, 255, 0.5);
}

#type option {
  background-color: #1a1754;
  color: white;
  padding: 5px;
}

</style>
