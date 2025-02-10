@foreach ($plans as $data)
    <div class="col-lg-3 col-sm-6 col-xsm-6">
        <div class="plan-item">
            <div class="plan-item__header">
                <p class="return-title" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('Return On Invest')">
                    @lang('ROI')</p>
                <span class="plan-item__title">
                    {{ showAmount($data->interest, currencyFormat:false) }}{{ $data->interest_type == 1 ? '% ' : ' ' . __(gs('cur_text')) }}
                </span>
                <h4 class="plan-item__name"> {{ __($data->name) }} </h4>
            </div>
            <div class="plan-item__body">
                <div class="plan-item__info">
                    <h5 class="plan-item__time">
                        @if ($data->lifetime == 0)
                            {{ __($data->repeat_time) }} Meses
                        @else
                            @lang('Lifetime')
                        @endif
                    </h5>
                    <div>
                        @if ($data->fixed_amount == 0)
                            <p class="plan-item__amount">@lang('Min'): {{ showAmount($data->minimum) }} </p>
                            <p class="plan-item__amount">@lang('Max'): {{ showAmount($data->maximum) }} </p>
                        @else
                            <p class="plan-item__amount">@lang('Fixed'): {{ showAmount($data->fixed_amount) }} </p>
                        @endif
                    </div>
                </div>
                <ul class="plan-item__list">
                    <li class="plan-item__list-inner">@lang('Days to init')
                        {{ $data->days_to_init }}
                    </li>
                    <li class="plan-item__list-inner">@lang('Return')
                        {{ showAmount($data->interest, currencyFormat:false) }}{{ $data->interest_type == 1 ? '% ' : ' ' . __(gs('cur_text')) }} @lang('monthly')
                    </li>
                    <li class="plan-item__list-inner">
                        @if ($data->lifetime == 0)
                            {{ __($data->repeat_time) }} Meses
                        @else
                            @lang('Lifetime')
                        @endif
                    </li>
                    <li class="plan-item__list-inner">
                        @if ($data->lifetime == 0)
                            @lang('Total')
                            {{ $data->interest * $data->repeat_time }}{{ $data->interest_type == 1 ? '%' : ' ' . __(gs('cur_text')) }}
                            @if ($data->capital_back == 1)
                                +
                                <span class="badge badge--success">@lang('Capital')</span>
                            @endif
                        @else
                            @lang('Lifetime Earning')
                        @endif
                    </li>
                    @if ($data->compound_interest)
                        <li class="plan-item__list-inner">
                            @lang('Compound interest available')
                        </li>
                    @endif
                    @if ($data->hold_capital)
                        <li class="plan-item__list-inner">
                            @lang('Hold capital & reinvest')
                        </li>
                    @endif
                </ul>
                <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#investModal" data-plan="{{ $data }}" class="btn btn--base pill outline investModal">@lang('Invest Now')</a>
            </div>
        </div>
    </div>
@endforeach

<div class="modal custom--modal fade" id="investModal">
    <div class="modal-dialog modal-lg modal-content-bg">
        <div class="modal-content">
            <div class="modal-header">
                @if (auth()->check())
                    <strong class="modal-title text-white" id="ModalLabel">
                        @lang('Confirm to invest on') <span class="planName"></span>
                    </strong>
                @else
                    <strong class="modal-title text-white" id="ModalLabel">
                        @lang('At first sign in your account')
                    </strong>
                @endif
                <button type="button" data-bs-dismiss="modal">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="{{ route('user.invest.submit') }}" class="disableSubmission" method="post">
                @csrf
                <input type="hidden" name="plan_id">
                @if (auth()->check())
                    <div class="modal-body">
                        <div>
                            <h6 class="text-center investAmountRange"></h6>
                            <p class="text-center mt-1 interestDetails"></p>
                            <p class="text-center interestValidity"></p>
                            <p class="text-center calculatedInterest"></p>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Pay Via')</label>
                                    <select class="form--control methodWallet" name="wallet_type" required>
                                        <option value="">@lang('Select One')</option>
                                        @if (auth()->user()->deposit_wallet > 0)
                                            <option value="deposit_wallet">@lang('Deposit Wallet - ' . showAmount(auth()->user()->deposit_wallet))</option>
                                        @endif
                                        @foreach ($gatewayCurrency as $data)
                                            <option value="{{ $data->id }}" @selected(old('wallet_type') == $data->method_code) data-gateway="{{ $data }}">{{ $data->name }}</option>
                                        @endforeach
                                    </select>
                                    <code class="gateway-info rate-info d-none">@lang('Rate'): 1 {{ gs('cur_text') }}
                                        =
                                        <span class="rate"></span> <span class="method_currency"></span></code>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Invest Amount')</label>
                                    <div class="input-group">
                                        <input type="number" step="any" class="form-control form--control investAmount" name="amount" required>
                                        <div class="input-group-text bg--base">{{ gs('cur_text') }}</div>
                                    </div>
                                    <code class="gateway-info d-none">@lang('Charge'): <span class="charge"></span>
                                        {{ gs('cur_text') }}. @lang('Total amount'): <span class="total"></span>
                                        {{ gs('cur_text') }}</code>
                                </div>
                            </div>

                            <div class="col-md-6 return_capital">
                                <div class="form-group">
                                    <div class="form-group">
                                        <label for="">@lang('Capital back') </label>
                                        <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-on="@lang('Yes')" data-off="@lang('No')" name="hold_capital">
                                        <i class="las la-info-circle" title="@lang('By activating this option you indicate that you want to receive your invested balance in equal fractions in the last') {{$data->repeat_time - $data->capital_months_return}} @lang('months of the plan')"></i>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-6 compoundInterest">
                                <div class="form-group">
                                    <label>@lang('Compound Interest') (@lang('optional'))</label>
                                    <div class="input-group">
                                        <input type="number" min="0" class="form--control form-control" name="compound_interest">

                                        <div class="input-group-text bg--base">@lang('Times')</div>
                                    </div>
                                    <small class="fst-italic text--info"><i class="las la-info-circle"></i> @lang('Your interest will add to the investment capital amount for a specific time that you\'re entering.')</small>
                                </div>
                            </div>
                            @if (gs('schedule_invest'))
                                <div class="col-md-6 investTime">
                                    <div class="form-group">
                                        <label>@lang('Auto Schedule Invest')</label>
                                        <select class="form--control" name="invest_time" required>
                                            <option value="invest_now">@lang('Invest Now')</option>
                                            <option value="schedule">@lang('Schedule')</option>
                                        </select>
                                        <small class="fst-italic text--info"><i class="las la-info-circle"></i> @lang('You can set your investment as a scheduler or invest instant.')</small>
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if (gs('schedule_invest'))
                            <div class="row schedule">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="required">@lang('Schedule For')</label>
                                        <div class="input-group">
                                            <input type="number" min="0" class="form-control form--control" name="schedule_times">
                                            <span class="input-group-text bg--base">@lang('Times')</span>
                                        </div>
                                        <small class="fst-italic text--info"><i class="las la-info-circle"></i> @lang('Set how many times you want to invest.')</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="required">@lang('After')</label>
                                        <div class="input-group">
                                            <input type="number" class="form--control form-control" name="hours" min="0">
                                            <span class="input-group-text bg--base">@lang('Hours')</span>
                                        </div>
                                        <small class="fst-italic text--info"><i class="las la-info-circle"></i> @lang('Set a frequency at which you prefer to make investments.')</small>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>
                @endif
                <div class="modal-footer">
                    @if (auth()->check())
                        <button type="button" class="btn btn--danger btn--sm pill" data-bs-dismiss="modal">@lang('No')</button>
                        <button type="submit" class="btn btn--base btn--sm pill">@lang('Yes')</button>
                    @else
                        <a href="{{ route('user.login') }}" class="btn btn--base pill w-100 text-center">@lang('At first sign in your account')</a>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

@push('script')
    <script>
        (function($) {
            "use strict"
            var symbol = '{{ gs('cur_sym') }}';
            var currency = '{{ gs('cur_text') }}';
            var plan;

            $('.investModal').click(function() {
                $('.gateway-info').addClass('d-none');
                var modal = $('#investModal');
                plan = $(this).data('plan');

                modal.find('.planName').text(plan.name)
                modal.find('[name=plan_id]').val(plan.id);
                let fixedAmount = parseFloat(plan.fixed_amount).toFixed(2);
                let minimumAmount = parseFloat(plan.minimum).toFixed(2);
                let maximumAmount = parseFloat(plan.maximum).toFixed(2);
                let interestAmount = parseFloat(plan.interest);

                if (plan.fixed_amount > 0) {
                    modal.find('.investAmountRange').text(`Invest: ${symbol}${fixedAmount}`);
                    modal.find('[name=amount]').val(fixedAmount);
                    modal.find('[name=amount]').attr('readonly', true);
                } else {
                    modal.find('.investAmountRange').text(
                        `Invest: ${symbol}${minimumAmount} - ${symbol}${maximumAmount}`);
                    modal.find('[name=amount]').val('');
                    modal.find('[name=amount]').removeAttr('readonly');
                }

                if(plan.capital_back == 1){
                    $('.return_capital').show();
                }else{
                    $('.return_capital').hide();
                }

                if (plan.interest_type == '1') {
                    modal.find('.interestDetails').html(
                        `<strong> Interest: ${interestAmount}% / @lang('monthly') </strong>`);
                } else {
                    modal.find('.interestDetails').html(
                        `<strong> Interest: ${interestAmount} ${currency}  </strong>`);
                }

                if (plan.lifetime == '0') {
                    modal.find('.interestValidity').html(
                        `<strong>  Paga cada ${plan.time_setting.time} horas ,  ${plan.repeat_time} meses</strong>`
                    );
                } else {
                    modal.find('.interestValidity').html(
                        `<strong>  Per ${plan.time_setting.time} hours,  life time </strong>`);
                }

                if (plan.compound_interest == '1') {
                    $('.compoundInterest').show();
                    $('.investTime').removeClass('col-md-12');
                } else {
                    $('.compoundInterest').hide();
                    $('.investTime').addClass('col-md-12');
                }
                calculateInterest();
            });

            $('.investAmount').on('input', function() {
                $('.methodWallet').trigger('change');
                calculateInterest();
            })

            $('.methodWallet').change(function() {
                var amount = $('.investAmount').val();
                if ($(this).val() != 'deposit_wallet' && $(this).val() != 'interest_wallet' &&
                    amount) {
                    var resource = $('.methodWallet option:selected').data('gateway');
                    var fixed_charge = parseFloat(resource.fixed_charge);
                    var percent_charge = parseFloat(resource.percent_charge);
                    var charge = parseFloat(fixed_charge + (amount * percent_charge / 100)).toFixed(
                        2);
                    $('.charge').text(charge);
                    $('.rate').text(parseFloat(resource.rate));
                    $('.gateway-info').removeClass('d-none');
                    if (resource.currency == '{{ gs('cur_text') }}') {
                        $('.rate-info').addClass('d-none');
                    } else {
                        $('.rate-info').removeClass('d-none');
                    }
                    $('.method_currency').text(resource.currency);
                    $('.total').text(parseFloat(charge) + parseFloat(amount));
                } else {
                    $('.gateway-info').addClass('d-none');
                }
            });

            $('[name=invest_time]').on('change', function() {
                let investTime = $(this).find(':selected').val();
                if (investTime == 'invest_now') {
                    $('.schedule').hide();
                } else {
                    $('.schedule').show();
                }
            }).change();

            $('[name=schedule_times]').on('input', function() {
                let text = $(this).val() == 1 ? `@lang('After')` : `@lang('Every')`;
                $('[name=hours]').closest('.form-group').find('label').text(text);
            });

            $('[name=compound_interest]').on('input', function() {
                calculateInterest();
            })

            $('[name=hold_capital]').on('change', function() {
                calculateInterest();
            })


            function calculateInterest() {
                let interest = parseFloat(plan.interest);
                let interestType = plan.interest_type; //1: percent, 0: fixed
                let repeatTime = plan.repeat_time;
                let capitalBack = plan.capital_back;
                let investAmount = $('[name=amount]').val() * 1;
                let compoundInterest = $('[name=compound_interest]').val() ?? 0;
                let calculatedInterest = 0;
                let baseInterest = 0;
                let selectBackCapital = $('[name=hold_capital]').is(':checked');
                let monthDays = 21;
                let dailyPercent = interest / monthDays;
                let monthsReturnCapital = plan.capital_months_return;

                if (repeatTime == 0 || investAmount == 0) {
                    $('.calculatedInterest').hide();
                    return false;
                } else {
                    $('.calculatedInterest').show();
                }

                let totalInterest = interest * repeatTime;

                if (interestType == '1') {
                    if (compoundInterest > 0) {
                        let remainingRepeatTime = repeatTime - compoundInterest;
                        let interestRatio = 1 + interest / 100;
                        let compoundCapital = investAmount * Math.pow(interestRatio, compoundInterest);
                        totalInterest = (compoundCapital * interest / 100) * remainingRepeatTime;
                    } else {
                        totalInterest = interest * investAmount / 100 * repeatTime;
                    }
                }

                if (selectBackCapital) {
                    let tempTotalTime = repeatTime - monthsReturnCapital;
                    let tempCapital = investAmount * interest / 100 * monthsReturnCapital;
                    let tempReturn = investAmount / tempTotalTime;
                    let acum = investAmount;
                    for (let i = 0; i < tempTotalTime; i++) {
                        tempCapital += acum * interest / 100;
                        acum = acum - tempReturn;
                    }
                    totalInterest = tempCapital;
                }

                totalInterest = capitalBack ? totalInterest : totalInterest - investAmount;
                $('.calculatedInterest').text(`@lang('Total Profit') ` + symbol + totalInterest.toFixed(2));
            }

            @if (!gs('schedule_invest'))
                $('.modal-dialog').removeClass('modal-lg');
                $('.modal-dialog').find('.col-md-6').addClass('col-md-12');
            @endif

        })(jQuery);
        tooltips()
        function tooltips() {
            setTimeout(function() {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title], [data-title], [data-bs-title]'));
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }, 100);
            }
    </script>
@endpush
