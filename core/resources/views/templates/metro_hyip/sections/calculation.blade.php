@php
    $calculationContent = getContent('calculation.content', true);
    $planList = \App\Models\Plan::with('timeSetting')
        ->whereHas('timeSetting', function ($time) {
            $time->where('status', 1);
        })
        ->where('status', 1)
        ->where('testing', 0)
        ->orderBy('id', 'desc')
        ->with('timeSetting')
        ->get();

    $gatewayCurrency = \App\Models\GatewayCurrency::whereHas('method', function ($gate) {
        $gate->where('status', 1);
    })
        ->with('method')
        ->orderby('name')
        ->get();

@endphp

<section class="investment-section bg-img pt-120" style="background-image: url({{ asset($activeTemplateTrue . 'images/shapes/invest.png') }});">
    <div class="shape-one">
        <img src="{{ asset($activeTemplateTrue . 'images/shapes/invest01.png') }}" alt="">
    </div>
    <div class="shape-two">
        <img src="{{ asset($activeTemplateTrue . 'images/shapes/invest02.png') }}" alt="">
    </div>
    <div class="container">
        <div class="row justify-content-center align-items-center flex-wrap">
            <div class="col-lg-10">
                <div class="investment-inner">
                    <div class="plan-card">
                        <div class="plan-card__shape"></div>
                        <div class="plan-card__shape-one"></div>

                        <h4 class="calculator__title">{{ __($calculationContent->data_values->heading) }}</h4>
                        <div class="plan-card-select">
                            <select class="select" id="changePlan">
                                <option value="" selected disabled>@lang('Select plan')</option>
                                @foreach ($planList as $k => $data)
                                    <option value="{{ $data->id }}" data-interest-yes="{{ $data->interest_type }}" data-interest="{{ $data->interest }}" data-lifetime="{{ $data->lifetime }}" data-repeat="{{ $data->lifetime }}" data-name="{{ $data->name }}" data-time-name="{{ $data->timeSetting->name }}" data-fixed_amount="{{ $data->fixed_amount }}" data-minimum_amount="{{ $data->minimum }}" data-maximum_amount="{{ $data->maximum }}" data-plan="{{ $data }}">
                                        {{ $data->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <h3 class="title">
                            <span class="interest">00.00% @lang('Hourly')</span>
                        </h3>
                        <h5 class="subtitle"> @lang('For Lifetime') </h5>
                        <ul class="plan-list">
                            <input type="hidden" step="any" class="form-control form--control invest-input" required>

                            <li class="plan-list__item range"> @lang('Invest'): <span class="invest-range">
                                    {{ gs('cur_sym') }}00
                                </span></li>
                            <li class="plan-list__item"> @lang('Profit Info'): <br> <span class="profit-input">{{ gs('cur_sym') }}00</span>
                            </li>
                            <li class="plan-list__item net-pro"> @lang('Net profit'): <br> <span class="net-profit">{{ gs('cur_sym') }}00</span>
                            </li>
                        </ul>
                    </div>

                    <div class="calculator">
                        <div class="calculator__shape"></div>
                        <h4 class="calculator__title">@lang('Invest a Best Plan')</h4>

                        @csrf
                        <div class="cal-area">
                            <div class="input-wrap">
                                <div class="position-relative">
                                    <label for="number" id="number" class="form-label"></label>
                                    <input type="number" id="number" class="form--control invest-amount" required>
                                    <span class="cal-area__icon">
                                        {{ __(gs('cur_text')) }}
                                    </span>
                                </div>
                            </div>
                            <div class="profit-result">
                                <span class="profit">@lang('Net Profit')</span>
                                <h4 class="plan-net-profit"> 00 {{ gs('cur_text') }}</h4>
                            </div>
                        </div>
                        <div class="cal-bottom-area">
                            <div class="profit-cal">
                                <div class="profit-cal__button">
                                    @auth
                                        <button data-bs-toggle="modal" data-bs-target="#investModal" class="btn btn--base pill investModal calInvest" disabled> @lang('Invest Now')
                                        </button>
                                    @else
                                        <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#investModalCheck" class="btn btn--base pill outline outlineinvestModal">@lang('Invest Now')</a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal custom--modal fade" id="investModalCheck">
        <div class="modal-dialog modal-dialog-centered modal-content-bg">
            <div class="modal-content">
                <div class="modal-header text-end">
                    <h5 class="text--danger"> @lang('Required')</h5>
                    <button type="button" data-bs-dismiss="modal">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('user.login') }}" class="btn btn--base pill w-100 text-center">@lang('At first sign in your account')</a>
                </div>
                </form>
            </div>
        </div>
    </div>
</section>

@push('script')
    <script>
        (function($) {
            "use strict";
            var curSym = `{{ gs('cur_sym') }}`;
            $(document).ready(function() {
                $('.net-pro').hide();
                $('#changePlan').on('change', function() {
                    var selectedPlan = $('#changePlan').find(':selected');
                    var planId = selectedPlan.val();
                    var data = selectedPlan.data();
                    $('.calInvest').data('plan', data.plan);
                    $('.calInvest').attr('disabled', false);

                    var fixedAmount = parseFloat(data.fixed_amount).toFixed(2);
                    var minimumAmount = parseFloat(data.minimum_amount).toFixed(2);
                    var maximumAmount = parseFloat(data.maximum_amount).toFixed(2);

                    var interest = parseFloat(data.interest).toFixed(2);
                    if (data.interestYes == 1) {
                        var interestAmount = interest + '%';
                    } else {
                        var interestAmount = interest + ' ' + `{{ gs('cur_text') }}`;
                    }
                    $('.interest').text(interestAmount);

                    var timeName = data.timeName;
                    if (data.lifetime == 0) {
                        $('.subtitle').text(`@lang('For ')` + timeName);
                    } else {
                        $('.subtitle').text(timeName + ' ' + data.repeat + ' ' + `@lang('for Lifetime')`);
                        $('.plan-net-profit').text(`@lang('Unlimited')`);

                    }
                    if (fixedAmount > 0) {
                        $('.net-pro').hide();
                        $('.invest-input').val(fixedAmount);
                        $('.invest-range').text(curSym + fixedAmount);
                        $('.invest-amount').val(fixedAmount);
                        $('.invest-amount').attr('readonly', true);
                    } else {
                        $('.net-pro').show();
                        $('.invest-input').val(minimumAmount);
                        $('.invest-range').text('(' + curSym + minimumAmount + ' - ' + curSym +
                            maximumAmount + ')');
                        $('.invest-amount').val(minimumAmount);
                        $('.invest-amount').attr('readonly', false);
                        if (data.lifetime == 1) {
                            $('.net-pro').hide();
                        }
                    }
                    var investAmount = $('.invest-input').val();
                    var profitInput = $('.profit-input').text('');

                    $('.period').text('');

                    if (investAmount != '' && planId != null) {
                        ajaxPlanCalc(planId, investAmount)
                    }
                    $('.plan-name').text(data.name);
                    $('.method-charge').addClass('d-none');
                });
            });

            $(".invest-amount").on('change', function() {
                var planId = $("#changePlan option:selected").val();
                var investAmount = $(this).val();
                var profitInput = $('.profit-input').text('');
                $('.period').text('');
                if (investAmount != '' && planId != null) {
                    ajaxPlanCalc(planId, investAmount)
                }
            });

            function ajaxPlanCalc(planId, investAmount) {
                $.ajax({
                    url: "{{ route('planCalculator') }}",
                    type: "post",
                    data: {
                        planId,
                        _token: '{{ csrf_token() }}',
                        investAmount
                    },
                    success: function(response) {
                        if (response.errors) {
                            iziToast.error({
                                message: response.errors,
                                position: "topRight"
                            });
                            $('.investNow').prop('disabled', true);
                        } else {
                            $('.investNow').prop('disabled', false);
                            var msg = `${response.description}`
                            var curText = `{{ gs('cur_text') }}`
                            $('.profit-input').text(msg);
                            if (response.netProfit) {
                                $('.net-profit').text(response.netProfit);
                                $('.plan-net-profit').text(response.netProfit);
                            }
                        }
                    }
                });
            }

        })(jQuery);
    </script>
@endpush

@include($activeTemplate . 'partials.invest_modal')
