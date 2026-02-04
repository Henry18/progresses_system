{{-- Include global investment calculator --}}
@include($activeTemplate . 'partials.investment_calculator')

@once
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
                <form action="{{ route('user.invest.submit') }}" method="post">
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

                                {{-- Fractional Capital Return Checkbox --}}
                                <div class="col-12 fractionalCapitalContainer" style="display: none;">
                                    <div class="form-group">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="fractionalCapitalReturn" name="fractional_capital_return" value="1">
                                            <label class="form-check-label" for="fractionalCapitalReturn">
                                                <i class="las la-calculator text--base"></i> @lang('Calculate with Fractional Capital Return')
                                            </label>
                                        </div>
                                        <small class="fst-italic text--info d-block mt-1"><i class="las la-info-circle"></i> @lang('Interest will be calculated based on remaining capital after each monthly capital return.')</small>
                                    </div>
                                </div>

                                {{-- Calculated Return Preview --}}
                                <div class="col-12 calculatedReturnPreview" style="display: none;">
                                    <div class="alert alert--primary py-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span><i class="las la-chart-line"></i> @lang('Estimated Total Profit'):</span>
                                            <strong class="estimatedProfit">{{ gs('cur_text') }} 0.00</strong>
                                        </div>
                                    </div>
                                </div>

                                @if (auth()->check())
                                    <div class="col-12">
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="termsAccepted" name="terms_accepted" value="1" required>
                                                <label class="form-check-label" for="termsAccepted">
                                                    @lang('I have read and accept the') <a href="{{ route('policy.pages', 'terms-and-service') }}" target="_blank" class="text--base">@lang('Terms and Conditions')</a>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 projectTermsContainer" style="display: none;">
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="projectTermsAccepted" name="project_terms_accepted" value="1">
                                                <label class="form-check-label" for="projectTermsAccepted">
                                                    @lang('I have read and accept the project conditions') <a href="#" id="projectPdfLink" target="_blank" class="text--base">@lang('Project Conditions')</a>
                                                </label>
                                            </div>
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
                            <button type="submit" class="btn btn--base btn--sm pill" id="investSubmitBtn">@lang('Yes')</button>
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

                    $('.projectTermsContainer').show();
                    $('#projectTermsAccepted').prop('required', true);

                    if (plan.project && plan.project.pdf) {
                        var pdfUrl = '{{ asset('') }}' + 'assets/projects/files/' + plan.project.pdf;
                        $('#projectPdfLink').attr('href', pdfUrl);
                    } else {
                        $('#projectPdfLink').hide();
                    }

                    let fixedAmount = parseFloat(plan.fixed_amount).toFixed(2);
                    let minimumAmount = parseFloat(plan.minimum).toFixed(2);
                    let maximumAmount = parseFloat(plan.maximum).toFixed(2);
                    let interestAmount = parseFloat(plan.interest);

                    if (fixedAmount > 0) {
                        modal.find('.investAmountRange').text(`Invest: ${symbol}${fixedAmount}`);
                        modal.find('[name=amount]').val(fixedAmount);
                        modal.find('[name=amount]').attr('readonly', true);
                    } else {
                        modal.find('.investAmountRange').text(
                            `Invest: ${symbol}${minimumAmount} - ${symbol}${maximumAmount}`);
                        modal.find('[name=amount]').val('');
                        modal.find('[name=amount]').removeAttr('readonly');
                    }

                    if (plan.interest_type == '1') {
                        modal.find('.interestDetails').html(
                            `<strong> Interest: ${interestAmount}% </strong>`);
                    } else {
                        modal.find('.interestDetails').html(
                            `<strong> Interest: ${interestAmount} ${currency}  </strong>`);
                    }

                    if (plan.lifetime == '0') {
                        modal.find('.interestValidity').html(
                            `<strong>  Per ${plan.time_setting.time} hours ,  ${plan.repeat_time} times</strong>`
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

                    // Show fractional capital checkbox only if plan has capital back with months
                    if (plan.capital_back == '1' && plan.capital_months_return > 0) {
                        $('.fractionalCapitalContainer').show();
                        $('.calculatedReturnPreview').show();
                        $('#fractionalCapitalReturn').prop('checked', false);
                    } else {
                        $('.fractionalCapitalContainer').hide();
                        $('.calculatedReturnPreview').hide();
                    }

                    calculateInterest();
                });

                $('.calInvest').on('click', function() {
                    $('[name=amount]').val($('.invest-amount').val());
                });

                $(document).on('input', '.investAmount', function() {
                    $('.methodWallet').trigger('change');
                    calculateInterest();
                })

                $('.methodWallet').change(function() {
                    var amount = $('.investAmount').val();
                    if ($(this).val() && $(this).val() != 'deposit_wallet' && $(this).val() != 'interest_wallet' &&
                        amount) {
                        var resource = $('.methodWallet option:selected').data('gateway');
                        var fixed_charge = parseFloat(resource.fixed_charge);
                        var percent_charge = parseFloat(resource.percent_charge);
                        var charge = parseFloat(fixed_charge + (amount * percent_charge / 100)).toFixed(2);
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
                });

                // Fractional capital checkbox handler
                $('#fractionalCapitalReturn').on('change', function() {
                    calculateInterest();
                });


                function calculateInterest() {
                    let interest = parseFloat(plan.interest);
                    let interestType = plan.interest_type; //1: percent, 0: fixed
                    let repeatTime = plan.repeat_time;
                    let capitalBack = plan.capital_back;
                    let capitalMonthsReturn = plan.capital_months_return || 0;
                    let investAmount = $('[name=amount]').val() * 1;
                    let compoundInterest = $('[name=compound_interest]').val() ?? 0;
                    let fractionalCapital = $('#fractionalCapitalReturn').is(':checked');

                    if (repeatTime == 0 || investAmount == 0) {
                        $('.calculatedInterest').hide();
                        $('.calculatedReturnPreview').hide();
                        return false;
                    } else {
                        $('.calculatedInterest').show();
                        if (capitalBack == '1' && capitalMonthsReturn > 0) {
                            $('.calculatedReturnPreview').show();
                        }
                    }

                    // Use global calculator if available
                    if (typeof InvestmentCalculator !== 'undefined') {
                        let interestDistribution = null;
                        if (plan.interest_distribution && plan.interest_distribution.enabled && plan.interest_distribution.segments) {
                            interestDistribution = plan.interest_distribution.segments;
                        }

                        const result = InvestmentCalculator.calculate({
                            investmentAmount: investAmount,
                            interestType: parseInt(interestType),
                            interest: interest,
                            repeatTime: repeatTime,
                            lifetime: plan.lifetime || 0,
                            capitalBack: parseInt(capitalBack) || 0,
                            capitalMonthsReturn: capitalMonthsReturn,
                            fractionalCapital: fractionalCapital,
                            compoundInterest: parseInt(compoundInterest) || 0,
                            interestDistribution: interestDistribution
                        });

                        const totalProfit = result.totalInterest;
                        $('.calculatedInterest').text(`@lang('Total Profit') ` + symbol + totalProfit.toFixed(2));
                        $('.estimatedProfit').text(symbol + ' ' + totalProfit.toFixed(2));

                        // Show different text based on fractional option
                        if (fractionalCapital) {
                            $('.calculatedInterest').html(`@lang('Total Profit') <span class="badge badge--info ms-1">@lang('Fractional')</span> ` + symbol + totalProfit.toFixed(2));
                        }
                    } else {
                        // Fallback to original calculation
                        let totalInterest = interest * repeatTime;

                        if (interestType == '1') {
                            if (compoundInterest > 0) {
                                let remainingRepeatTime = repeatTime - compoundInterest;
                                let interestRatio = 1 + interest / 100;
                                let compoundCapital = investAmount * Math.pow(interestRatio, compoundInterest);
                                totalInterest = (compoundCapital * interest / 100) * remainingRepeatTime;
                            } else {
                                totalInterest = (1 + interest / 100) * investAmount;
                            }
                        }

                        totalInterest = capitalBack ? totalInterest : totalInterest - investAmount;
                        $('.calculatedInterest').text(`@lang('Total Profit') ` + symbol + totalInterest.toFixed(2));
                        $('.estimatedProfit').text(symbol + ' ' + totalInterest.toFixed(2));
                    }
                }

                @if (!gs('schedule_invest'))
                    $('.modal-dialog').removeClass('modal-lg');
                    $('.modal-dialog').find('.col-md-6').addClass('col-md-12');
                @endif

                // Function to validate all required checkboxes
                function validateTermsCheckboxes() {
                    var termsChecked = $('#termsAccepted').is(':checked');
                    var projectTermsRequired = $('#projectTermsAccepted').prop('required');
                    var projectTermsChecked = $('#projectTermsAccepted').is(':checked');

                    // Enable submit button only if:
                    // 1. General terms are checked AND
                    // 2. Project terms are checked (if required)
                    if (termsChecked && (!projectTermsRequired || projectTermsChecked)) {
                        $('#investSubmitBtn').prop('disabled', false);
                    } else {
                        $('#investSubmitBtn').prop('disabled', true);
                    }
                }

                // Terms and conditions checkbox handlers
                $('#termsAccepted, #projectTermsAccepted').on('change', function() {
                    validateTermsCheckboxes();
                });

                // Reset checkboxes when modal opens
                $('#investModal').on('show.bs.modal', function() {
                    $('#termsAccepted').prop('checked', false);
                    $('#projectTermsAccepted').prop('checked', false);
                    $('#fractionalCapitalReturn').prop('checked', false);
                    $('#investSubmitBtn').prop('disabled', true);
                });

            })(jQuery);
        </script>
    @endpush
@endonce
