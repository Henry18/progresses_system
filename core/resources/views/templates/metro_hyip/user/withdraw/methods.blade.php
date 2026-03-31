@extends($activeTemplate . 'layouts.master')
@section('content')
<script>
"use strict"
function createCountDown(elementId, sec) {
    var tms = sec;
    var x = setInterval(function() {
        var distance = tms * 1000;
        document.getElementById(elementId).innerHTML =
            `<span>${Math.floor(distance/(1000*60*60*24))}d</span> ` +
            `<span>${Math.floor((distance%(1000*60*60*24))/(1000*60*60))}h</span> ` +
            `<span>${Math.floor((distance%(1000*60*60))/(1000*60))}m</span> ` +
            `<span>${Math.floor((distance%(1000*60))/1000)}s</span>`;
        if (distance < 0) { clearInterval(x); document.getElementById(elementId).innerHTML = "COMPLETE"; }
        tms--;
    }, 1000);
}
</script>

<div class="prgr-deposit">

    <div class="prgr-page-header">
        <div class="prgr-page-header__left">
            <h1 class="prgr-page-header__title">@lang('Solicitar Retiro')</h1>
            <p class="prgr-page-header__subtitle">@lang('Elige tu billetera, el método y el monto a retirar.')</p>
        </div>
        <div class="prgr-page-header__actions">
            <a href="{{ route('user.withdraw.history') }}" class="prgr-btn prgr-btn--outline">
                <i class="las la-history"></i> @lang('Historial')
            </a>
        </div>
    </div>

    @if ($isHoliday && !gs('holiday_withdraw'))
        <div class="prgr-card">
            <div class="prgr-card__body text-center py-5">
                <i class="las la-calendar-times" style="font-size:48px;color:#d97706;display:block;margin-bottom:12px;"></i>
                <h4 class="mb-2">@lang('Retiros deshabilitados hoy')</h4>
                <p class="text-muted mb-4">@lang('Por favor espera al siguiente día hábil')</p>
                <div id="counter" class="prgr-stat-card__amount"></div>
                <script>createCountDown('counter', {{ abs(\Carbon\Carbon::parse($nextWorkingDay)->diffInSeconds()) }});</script>
            </div>
        </div>
    @else

    <form action="{{ route('user.withdraw.money') }}" method="post" class="withdraw-form">
        @csrf

        <div class="prgr-deposit-layout">

            {{-- LEFT: Wallet + Amount + Summary --}}
            <div class="prgr-deposit-main">

                <div class="prgr-card mb-3">
                    <div class="prgr-card__header">
                        <h5 class="prgr-card__title">@lang('Billetera de Origen')</h5>
                    </div>
                    <div class="prgr-card__body">
                        <div class="prgr-wallet-selector">
                            <label class="prgr-wallet-option">
                                <input type="radio" name="type" value="interest_wallet" class="wallet_type" checked>
                                <span class="prgr-wallet-option__icon" style="background:#eff6ff;color:#2563eb;"><i class="las la-chart-line"></i></span>
                                <div>
                                    <strong>@lang('Ganancias')</strong>
                                    <small>{{ gs('cur_sym') }}{{ showAmount(auth()->user()->interest_wallet, currencyFormat:false) }}</small>
                                </div>
                            </label>
                            <label class="prgr-wallet-option">
                                <input type="radio" name="type" value="bonus_wallet" class="wallet_type">
                                <span class="prgr-wallet-option__icon" style="background:#eff6ff;color:#2563eb;"><i class="las la-gift"></i></span>
                                <div>
                                    <strong>@lang('Bonos')</strong>
                                    <small>{{ gs('cur_sym') }}{{ showAmount(auth()->user()->bonus_wallet, currencyFormat:false) }}</small>
                                </div>
                            </label>
                            <label class="prgr-wallet-option">
                                <input type="radio" name="type" value="special_wallet" class="wallet_type">
                                <span class="prgr-wallet-option__icon" style="background:#fff7ed;color:#d97706;"><i class="las la-star"></i></span>
                                <div>
                                    <strong>@lang('Especial')</strong>
                                    <small>{{ gs('cur_sym') }}{{ showAmount(auth()->user()->special_wallet ?? 0, currencyFormat:false) }}</small>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="prgr-card">
                    <div class="prgr-card__header">
                        <h5 class="prgr-card__title">@lang('Monto del Retiro')</h5>
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

                        <div class="period-warning d-none mt-3">
                            <div class="prgr-alert prgr-alert--warning">
                                <i class="las la-exclamation-triangle prgr-alert__icon"></i>
                                <div class="prgr-alert__body">
                                    <span class="period-warning-text"></span>
                                </div>
                            </div>
                        </div>

                        <div class="prgr-summary mt-4">
                            <div class="prgr-summary__row">
                                <span>@lang('Límite del método')</span>
                                <span class="gateway-limit fw-600">—</span>
                            </div>
                            <div class="prgr-summary__row">
                                <span>
                                    @lang('Cargo')
                                    <span data-bs-toggle="tooltip" title="" class="proccessing-fee-info ms-1" style="cursor:help;">
                                        <i class="las la-info-circle"></i>
                                    </span>
                                </span>
                                <span>{{ gs('cur_sym') }}<span class="processing-fee fw-600">0.00</span></span>
                            </div>
                            <div class="deposit-info gateway-conversion d-none prgr-summary__row">
                                <span>@lang('Conversión')</span>
                                <span></span>
                            </div>
                            <div class="deposit-info conversion-currency d-none prgr-summary__row">
                                <span>@lang('En') <span class="gateway-currency"></span></span>
                                <span class="in-currency"></span>
                            </div>
                            <div class="prgr-summary__row prgr-summary__row--total">
                                <span>@lang('Recibirás')</span>
                                <span>{{ gs('cur_sym') }}<span class="final-amount fw-700">0.00</span> {{ __(gs('cur_text')) }}</span>
                            </div>
                        </div>

                        <input type="hidden" name="terms_accepted" id="terms_accepted_input" value="">
                        <button type="button" class="prgr-btn prgr-btn--primary w-100 mt-4 open-terms-modal" disabled>
                            <i class="las la-lock"></i> @lang('Confirmar Retiro')
                        </button>
                        <p class="prgr-deposit-note mt-3">
                            <i class="las la-shield-alt"></i>
                            @lang('Se requiere confirmación de términos antes de procesar.')
                        </p>
                    </div>
                </div>
            </div>

            {{-- RIGHT: Methods --}}
            <div class="prgr-deposit-methods">
                <div class="prgr-card">
                    <div class="prgr-card__header">
                        <h5 class="prgr-card__title">@lang('Método de Retiro')</h5>
                    </div>
                    <div class="prgr-card__body prgr-card__body--flush">
                        <div class="prgr-methods-list gateway-option-list is-scrollable">
                            @foreach ($withdrawMethod as $data)
                                <label for="{{ titleToKey($data->name) }}"
                                    class="prgr-method-item @if ($loop->index > 4) d-none @endif gateway-option">
                                    <input class="prgr-method-item__radio gateway-input"
                                        id="{{ titleToKey($data->name) }}" hidden
                                        data-gateway='@json($data)' type="radio" name="method_code"
                                        value="{{ $data->id }}"
                                        @if (old('method_code')) @checked(old('method_code') == $data->id) @else @checked($loop->first) @endif
                                        data-min-amount="{{ showAmount($data->min_limit) }}"
                                        data-max-amount="{{ showAmount($data->max_limit) }}">
                                    <img class="prgr-method-item__img"
                                        src="{{ getImage(getFilePath('withdrawMethod') . '/' . $data->image) }}"
                                        alt="{{ __($data->name) }}">
                                    <span class="prgr-method-item__name">{{ __($data->name) }}</span>
                                    <span class="prgr-method-item__check"><i class="las la-check"></i></span>
                                </label>
                            @endforeach
                            @if ($withdrawMethod->count() > 4)
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

    {{-- Terms Modal --}}
    <div class="modal fade" id="withdrawTermsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="las la-file-contract"></i> @lang('Términos y Condiciones de Retiro')
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="terms-section mb-4">
                        <h6 class="text-warning"><i class="las la-exclamation-circle"></i> @lang('Aviso Importante')</h6>
                        <div class="p-3 rounded" style="background:#f8fafc;border:1px solid #e2e8f0;max-height:200px;overflow-y:auto;">
                            <p>@lang('Al continuar, aceptas los siguientes términos:')</p>
                            <ul>
                                <li>@lang('Las solicitudes se procesan en 24-48 horas hábiles.')</li>
                                <li>@lang('Los cargos de procesamiento no son reembolsables.')</li>
                                <li>@lang('Eres responsable de los datos de pago proporcionados.')</li>
                                <li>@lang('La plataforma no es responsable por demoras de terceros.')</li>
                                <li>@lang('Los retiros pueden estar sujetos a verificación.')</li>
                            </ul>
                        </div>
                    </div>

                    <div class="special-wallet-warning d-none mb-4">
                        <h6 class="text-danger"><i class="las la-calendar-times"></i> @lang('Aviso Billetera Especial')</h6>
                        <div class="p-3 rounded" style="background:#fef2f2;border:1px solid #fecaca;">
                            <p class="mb-2 special-period-status"></p>
                            <div class="allowed-periods-section d-none mt-3">
                                <p class="mb-2 text-primary"><i class="las la-calendar-check"></i> @lang('Períodos permitidos:')</p>
                                <ul class="allowed-periods-list mb-2" style="list-style:none;padding-left:0;"></ul>
                            </div>
                            <p class="mb-0 text-warning special-charge-info"></p>
                        </div>
                    </div>

                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" id="termsCheckbox">
                        <label class="form-check-label ms-2" for="termsCheckbox" style="cursor:pointer;">
                            @lang('He leído y acepto los términos y condiciones de este retiro')
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="prgr-btn prgr-btn--outline" data-bs-dismiss="modal">@lang('Cancelar')</button>
                    <button type="button" class="prgr-btn prgr-btn--primary confirm-withdraw-btn" disabled>
                        <i class="las la-check-circle"></i> @lang('Aceptar y Retirar')
                    </button>
                </div>
            </div>
        </div>
    </div>

    @endif

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
.prgr-field__prefix, .prgr-field__suffix {
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
.prgr-field__suffix { border-right: none; border-left: 1.5px solid #e2e8f0; }
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
.prgr-deposit-note {
    font-size: 12px;
    color: #94a3b8;
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
}
/* Wallet selector */
.prgr-wallet-selector {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.prgr-wallet-option {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 14px;
    border-radius: 10px;
    border: 1.5px solid #e2e8f0;
    cursor: pointer;
    transition: border-color .15s, background .15s;
}
.prgr-wallet-option:has(input:checked) {
    border-color: #2563eb;
    background: rgba(37,99,235,.04);
}
.prgr-wallet-option input { display: none; }
.prgr-wallet-option__icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
.prgr-wallet-option strong { display: block; font-size: 13.5px; font-weight: 600; color: #0f172a; }
.prgr-wallet-option small { font-size: 12px; color: #64748b; }
/* Methods */
.prgr-methods-list {
    display: flex;
    flex-direction: column;
    max-height: 480px;
    overflow-y: auto;
}
.prgr-method-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 20px;
    cursor: pointer;
    border-bottom: 1px solid #f1f5f9;
    transition: background .12s;
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
.prgr-method-item__name { flex: 1; font-size: 13.5px; font-weight: 500; color: #334155; }
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
}
.prgr-methods-more:hover { background: #eff6ff; }
</style>
@endpush

@push('script')
<script>
"use strict";
(function($) {
    var amount = parseFloat($('.amount').val() || 0);
    var walletType = 'interest_wallet';
    var gateway, minAmount, maxAmount;
    let percentCharge = 0, fixedCharge = 0, totalPercentCharge = 0;

    var withdrawalPeriods = @json($withdrawMethod->mapWithKeys(function($method) {
        return [$method->id => [
            'isWithinPeriod' => $method->isWithinWithdrawalPeriod(),
            'periods' => $method->activeWithdrawalPeriods->map(function($p) {
                return ['start' => $p->start_date->format('M d'), 'end' => $p->end_date->format('M d')];
            })
        ]];
    }));

    var isWithinPeriod = false;

    $('.amount').on('input', function() {
        amount = parseFloat($(this).val()) || 0;
        calculation();
    });

    $('input[name="type"]').on('change', function() {
        walletType = $(this).val();
        amount = parseFloat($('.amount').val()) || 0;
        calculation();
    });

    $('.gateway-input').on('change', function() { gatewayChange(); });

    function gatewayChange() {
        let el = $('.gateway-input:checked');
        let methodCode = el.val();
        gateway = el.data('gateway');
        minAmount = el.data('min-amount');
        maxAmount = el.data('max-amount');
        isWithinPeriod = withdrawalPeriods[methodCode] ? withdrawalPeriods[methodCode].isWithinPeriod : false;
        calculation();
    }

    gatewayChange();

    $(".more-gateway-option").on("click", function() {
        $(".gateway-option-list .gateway-option").removeClass("d-none");
        $(this).addClass('d-none');
    });

    function calculation() {
        if (!gateway) return;
        walletType = $('input[name="type"]:checked').val();
        $(".gateway-limit").text(minAmount + " - " + maxAmount);

        if (walletType == 'special_wallet') {
            if (isWithinPeriod) {
                percentCharge = parseFloat(gateway.percent_charge_special || 0);
                fixedCharge = parseFloat(gateway.fixed_charge_special || 0);
                $(".period-warning").addClass('d-none');
            } else {
                percentCharge = parseFloat(gateway.percent_charge_special_out || 0);
                fixedCharge = parseFloat(gateway.fixed_charge_special_out || 0);
                $(".period-warning").removeClass('d-none');
                $(".period-warning-text").text("@lang('Estás retirando fuera del período permitido. Aplican cargos adicionales.')");
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
            let feeInfo = `${percentCharge.toFixed(2)}% + {{ __(gs('cur_text')) }}${fixedCharge.toFixed(2)}`;
            $(".proccessing-fee-info").attr("data-bs-original-title", feeInfo);
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
            $(".gateway-conversion, .conversion-currency").removeClass('d-none');
            $(".gateway-conversion span:last").html(
                `1 {{ __(gs('cur_text')) }} = ${parseFloat(gateway.rate).toFixed(2)} ${gateway.currency}`
            );
            $('.in-currency').text(parseFloat(totalAmount * gateway.rate).toFixed(2));
        } else {
            $(".gateway-conversion, .conversion-currency").addClass('d-none');
        }
    }

    // Terms modal
    $('.open-terms-modal').on('click', function() {
        walletType = $('input[name="type"]:checked').val();
        let methodCode = $('.gateway-input:checked').val();

        if (walletType == 'special_wallet') {
            $('.special-wallet-warning').removeClass('d-none');
            let methodPeriods = withdrawalPeriods[methodCode] ? withdrawalPeriods[methodCode].periods : [];
            if (isWithinPeriod) {
                $('.special-period-status').html('<i class="las la-check-circle text-success"></i> @lang("Estás dentro de un período permitido de retiro.")');
                $('.special-charge-info').text('@lang("Se aplicarán cargos estándar de billetera especial.")');
                $('.allowed-periods-section').addClass('d-none');
            } else {
                $('.special-period-status').html('<i class="las la-times-circle text-danger"></i> @lang("Estás fuera de los períodos permitidos.")');
                $('.special-charge-info').text('@lang("Se aplicarán cargos penalizados: ") ' + percentCharge.toFixed(2) + '% + {{ __(gs("cur_text")) }}' + fixedCharge.toFixed(2));
                if (methodPeriods.length > 0) {
                    $('.allowed-periods-section').removeClass('d-none');
                    let html = '';
                    methodPeriods.forEach(p => { html += `<li class="mb-1"><i class="las la-calendar text-success"></i> ${p.start} - ${p.end}</li>`; });
                    $('.allowed-periods-list').html(html);
                }
            }
        } else {
            $('.special-wallet-warning').addClass('d-none');
        }

        $('#termsCheckbox').prop('checked', false);
        $('.confirm-withdraw-btn').attr('disabled', true);
        new bootstrap.Modal(document.getElementById('withdrawTermsModal')).show();
    });

    $('#termsCheckbox').on('change', function() {
        $(this).is(':checked') ? $('.confirm-withdraw-btn').removeAttr('disabled') : $('.confirm-withdraw-btn').attr('disabled', true);
    });

    $('.confirm-withdraw-btn').on('click', function() {
        $('#terms_accepted_input').val('1');
        $('#withdrawTermsModal').modal('hide');
        $('.withdraw-form').submit();
    });

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(el => new bootstrap.Tooltip(el));

    $('.gateway-input').change();
})(jQuery);
</script>
@endpush
