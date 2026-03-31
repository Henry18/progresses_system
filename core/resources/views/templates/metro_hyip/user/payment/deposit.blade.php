@extends($activeTemplate . 'layouts.master')
@section('content')

<div class="prgr-deposit">

    {{-- Page header --}}
    <div class="prgr-page-header">
        <div class="prgr-page-header__left">
            <h1 class="prgr-page-header__title">@lang('Nuevo Depósito')</h1>
            <p class="prgr-page-header__subtitle">@lang('Selecciona el monto y el método de pago para continuar.')</p>
        </div>
        <div class="prgr-page-header__actions">
            <a href="{{ route('user.deposit.history') }}" class="prgr-btn prgr-btn--outline">
                <i class="las la-history"></i> @lang('Historial')
            </a>
        </div>
    </div>

    <form action="{{ route('user.deposit.insert') }}" method="post" class="deposit-form">
        @csrf
        <input type="hidden" name="currency">

        <div class="prgr-deposit-layout">

            {{-- LEFT: Amount + Summary --}}
            <div class="prgr-deposit-main">
                <div class="prgr-card">
                    <div class="prgr-card__header">
                        <h5 class="prgr-card__title">@lang('Monto del Depósito')</h5>
                    </div>
                    <div class="prgr-card__body">

                        <div class="prgr-field">
                            <label class="prgr-field__label">@lang('Ingresa el monto')</label>
                            <div class="prgr-field__input-group">
                                <span class="prgr-field__prefix">{{ gs('cur_sym') }}</span>
                                <input type="text" class="prgr-field__input amount" name="amount"
                                    placeholder="0.00" value="{{ old('amount') }}" autocomplete="off">
                                <span class="prgr-field__suffix">{{ __(gs('cur_text')) }}</span>
                            </div>
                        </div>

                        <div class="prgr-summary mt-4">
                            <div class="prgr-summary__row">
                                <span>@lang('Límite')</span>
                                <span class="gateway-limit fw-600">—</span>
                            </div>
                            <div class="prgr-summary__row">
                                <span>@lang('Cargo de Procesamiento')</span>
                                <span class="processing-fee fw-600">0.00</span>
                            </div>
                            <div class="prgr-summary__row prgr-summary__row--total">
                                <span>@lang('Total a Pagar')</span>
                                <span class="final-amount fw-700">0.00 {{ __(gs('cur_text')) }}</span>
                            </div>
                            <div class="deposit-info gateway-conversion d-none prgr-summary__row">
                                <span>@lang('Conversión')</span>
                                <span></span>
                            </div>
                            <div class="deposit-info conversion-currency d-none prgr-summary__row">
                                <span>@lang('En') <span class="gateway-currency"></span></span>
                                <span class="in-currency"></span>
                            </div>
                            <div class="d-none crypto-message prgr-alert prgr-alert--info mt-3">
                                <i class="las la-info-circle prgr-alert__icon"></i>
                                <div class="prgr-alert__body">
                                    @lang('Conversión con') <span class="gateway-currency"></span>. @lang('El valor final se mostrará en el siguiente paso.')
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="prgr-btn prgr-btn--primary w-100 mt-4" disabled>
                            <i class="las la-lock"></i> @lang('Confirmar Depósito')
                        </button>

                        <p class="prgr-deposit-note mt-3">
                            <i class="las la-shield-alt"></i>
                            @lang('Proceso seguro con verificación en tiempo real.')
                        </p>
                    </div>
                </div>
            </div>

            {{-- RIGHT: Payment methods --}}
            <div class="prgr-deposit-methods">
                <div class="prgr-card">
                    <div class="prgr-card__header">
                        <h5 class="prgr-card__title">@lang('Método de Pago')</h5>
                    </div>
                    <div class="prgr-card__body prgr-card__body--flush">
                        <div class="prgr-methods-list gateway-option-list is-scrollable">
                            @foreach ($gatewayCurrency as $data)
                                <label for="{{ titleToKey($data->name) }}"
                                    class="prgr-method-item @if ($loop->index > 4) d-none @endif gateway-option">
                                    <input class="prgr-method-item__radio gateway-input"
                                        id="{{ titleToKey($data->name) }}" hidden
                                        data-gateway='@json($data)' type="radio" name="gateway"
                                        value="{{ $data->method_code }}"
                                        @if (old('gateway')) @checked(old('gateway') == $data->method_code) @else @checked($loop->first) @endif
                                        data-min-amount="{{ showAmount($data->min_amount) }}"
                                        data-max-amount="{{ showAmount($data->max_amount) }}">
                                    <img class="prgr-method-item__img"
                                        src="{{ getImage(getFilePath('gateway') . '/' . $data->method->image) }}"
                                        alt="{{ __($data->name) }}">
                                    <span class="prgr-method-item__name">{{ __($data->name) }}</span>
                                    <span class="prgr-method-item__check"><i class="las la-check"></i></span>
                                </label>
                            @endforeach
                            @if ($gatewayCurrency->count() > 4)
                                <button type="button" class="prgr-methods-more more-gateway-option">
                                    <i class="las la-chevron-down"></i> @lang('Ver todos los métodos')
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>

</div>

@endsection

@push('style')
<style>
.prgr-deposit-layout {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    align-items: start;
}
@media (max-width: 768px) {
    .prgr-deposit-layout { grid-template-columns: 1fr; }
    .prgr-deposit-methods { order: -1; }
}

/* Field */
.prgr-field__label {
    display: block;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .07em;
    text-transform: uppercase;
    color: #64748b;
    margin-bottom: 8px;
}
.prgr-field__input-group {
    display: flex;
    align-items: center;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    overflow: hidden;
    transition: border-color .15s;
}
.prgr-field__input-group:focus-within {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37,99,235,.12);
}
.prgr-field__prefix,
.prgr-field__suffix {
    padding: 0 14px;
    font-size: 13px;
    font-weight: 600;
    color: #64748b;
    background: #f8fafc;
    border-right: 1.5px solid #e2e8f0;
    height: 48px;
    display: flex;
    align-items: center;
}
.prgr-field__suffix {
    border-right: none;
    border-left: 1.5px solid #e2e8f0;
}
.prgr-field__input {
    flex: 1;
    border: none;
    outline: none;
    padding: 0 14px;
    font-size: 18px;
    font-weight: 600;
    color: #0f172a;
    height: 48px;
    background: transparent;
}

/* Summary */
.prgr-summary { border-top: 1px solid #f1f5f9; padding-top: 16px; }
.prgr-summary__row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 7px 0;
    font-size: 13px;
    color: #64748b;
    border-bottom: 1px solid #f8fafc;
}
.prgr-summary__row--total {
    font-weight: 700;
    font-size: 15px;
    color: #0f172a;
    border-bottom: none;
    padding-top: 12px;
    margin-top: 4px;
    border-top: 2px solid #e2e8f0;
}
.fw-600 { font-weight: 600; color: #334155; }
.fw-700 { font-weight: 700; }

/* Note */
.prgr-deposit-note {
    font-size: 12px;
    color: #94a3b8;
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
}

/* Methods */
.prgr-methods-list {
    display: flex;
    flex-direction: column;
    max-height: 400px;
    overflow-y: auto;
    padding: 4px 0;
}
.prgr-method-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 20px;
    cursor: pointer;
    border-bottom: 1px solid #f1f5f9;
    transition: background .12s;
    position: relative;
}
.prgr-method-item:last-of-type { border-bottom: none; }
.prgr-method-item:hover { background: #f8fafc; }
.prgr-method-item:has(.gateway-input:checked) {
    background: rgba(37,99,235,.05);
    border-left: 3px solid #2563eb;
}
.prgr-method-item__img {
    width: 40px;
    height: 28px;
    object-fit: contain;
    border-radius: 4px;
    border: 1px solid #e2e8f0;
    padding: 2px;
    background: #fff;
}
.prgr-method-item__name {
    flex: 1;
    font-size: 13.5px;
    font-weight: 500;
    color: #334155;
}
.prgr-method-item__check {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    border: 2px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    color: transparent;
    transition: all .15s;
}
.prgr-method-item:has(.gateway-input:checked) .prgr-method-item__check {
    background: #2563eb;
    border-color: #2563eb;
    color: #fff;
}
.prgr-methods-more {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 12px;
    width: 100%;
    border: none;
    background: #f8fafc;
    font-size: 13px;
    font-weight: 600;
    color: #2563eb;
    cursor: pointer;
    transition: background .12s;
}
.prgr-methods-more:hover { background: #eff6ff; }
</style>
@endpush

@push('script')
<script>
"use strict";
(function($) {
    var amount = parseFloat($('.amount').val() || 0);
    var gateway, minAmount, maxAmount;

    $('.amount').on('input', function(e) {
        amount = parseFloat($(this).val());
        if (!amount) { amount = 0; }
        calculation();
    });

    $('.gateway-input').on('change', function(e) { gatewayChange(); });

    function gatewayChange() {
        let gatewayElement = $('.gateway-input:checked');
        gateway = gatewayElement.data('gateway');
        minAmount = gatewayElement.data('min-amount');
        maxAmount = gatewayElement.data('max-amount');
        let processingFeeInfo = `${parseFloat(gateway.percent_charge).toFixed(2)}% + {{ __(gs('cur_text')) }}${parseFloat(gateway.fixed_charge).toFixed(2)} @lang('cargo de procesamiento')`;
        $(".proccessing-fee-info").attr("data-bs-original-title", processingFeeInfo);
        calculation();
    }

    gatewayChange();

    $(".more-gateway-option").on("click", function(e) {
        let paymentList = $(".gateway-option-list");
        paymentList.find(".gateway-option").removeClass("d-none");
        $(this).addClass('d-none');
    });

    function calculation() {
        if (!gateway) return;
        $(".gateway-limit").text(minAmount + " - " + maxAmount);
        let percentCharge = 0, fixedCharge = 0, totalPercentCharge = 0;
        if (amount) {
            percentCharge = parseFloat(gateway.percent_charge);
            fixedCharge = parseFloat(gateway.fixed_charge);
            totalPercentCharge = parseFloat(amount / 100 * percentCharge);
        }
        let totalCharge = parseFloat(totalPercentCharge + fixedCharge);
        let totalAmount = parseFloat((amount || 0) + totalPercentCharge + fixedCharge);

        $(".final-amount").text(totalAmount.toFixed(2) + ' {{ __(gs("cur_text")) }}');
        $(".processing-fee").text(totalCharge.toFixed(2));
        $("input[name=currency]").val(gateway.currency);
        $(".gateway-currency").text(gateway.currency);

        if (amount < Number(gateway.min_amount) || amount > Number(gateway.max_amount)) {
            $(".deposit-form button[type=submit]").attr('disabled', true);
        } else {
            $(".deposit-form button[type=submit]").removeAttr('disabled');
        }

        if (gateway.currency != "{{ gs('cur_text') }}" && gateway.method.crypto != 1) {
            $(".gateway-conversion, .conversion-currency").removeClass('d-none');
            $(".gateway-conversion").find('.prgr-summary__row span:last').html(
                `1 {{ __(gs('cur_text')) }} = ${parseFloat(gateway.rate).toFixed(2)} ${gateway.currency}`
            );
            $('.in-currency').text(parseFloat(totalAmount * gateway.rate).toFixed(2));
        } else {
            $(".gateway-conversion, .conversion-currency").addClass('d-none');
        }

        if (gateway.method.crypto == 1) {
            $('.crypto-message').removeClass('d-none');
        } else {
            $('.crypto-message').addClass('d-none');
        }
    }

    $('.gateway-input').change();
})(jQuery);
</script>
@endpush
