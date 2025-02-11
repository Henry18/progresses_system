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
                                        <button type="submit" class="btn btn--base w-100" disabled>
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
            var walletType = ''
            var gateway, minAmount, maxAmount;


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
                console.log(walletType);

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

                let processingFeeInfo =
                    `${parseFloat(gateway.percent_charge).toFixed(2)}% with ${parseFloat(gateway.fixed_charge).toFixed(2)} {{ __(gs('cur_text')) }} charge for processing fees`
                $(".proccessing-fee-info").attr("data-bs-original-title", processingFeeInfo);

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
                $(".gateway-limit").text(minAmount + " - " + maxAmount);
                let percentCharge = 0;
                let fixedCharge = 0;
                let totalPercentCharge = 0;

                if (amount) {
                    percentCharge = walletType == 'interest_wallet' ? parseFloat(gateway.percent_charge) : parseFloat(gateway.percent_charge_bonus);
                    fixedCharge = walletType == 'interest_wallet' ? parseFloat(gateway.fixed_charge) : parseFloat(gateway.fixed_charge_bonus);
                    totalPercentCharge = parseFloat(amount / 100 * percentCharge);
                }

                let totalCharge = parseFloat(totalPercentCharge + fixedCharge);
                let totalAmount = parseFloat((amount || 0) - totalPercentCharge - fixedCharge);

                $(".final-amount").text(totalAmount.toFixed(2));
                $(".processing-fee").text(totalCharge.toFixed(2));
                $("input[name=currency]").val(gateway.currency);
                $(".gateway-currency").text(gateway.currency);

                if (amount < Number(gateway.min_limit) || amount > Number(gateway.max_limit)) {
                    $(".withdraw-form button[type=submit]").attr('disabled', true);
                } else {
                    $(".withdraw-form button[type=submit]").removeAttr('disabled');
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