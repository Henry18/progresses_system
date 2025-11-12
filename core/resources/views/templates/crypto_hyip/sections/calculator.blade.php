@php
    $planList = \App\Models\Plan::whereHas('timeSetting', function ($time) {
        $time->where('status', 1);
    })
        ->where('status', 1)
        ->orderBy('id', 'desc')
        ->get();
    $calculation = getContent('calculator.content', true);
@endphp
<section class="calculator-section my-120">
    <div class="container">
        <div class="section-heading">
            <h1 class="section-heading__title wow fadeInUp" data-wow-delay="0.1s">
                {{ __(@$calculation->data_values->heading) }}
            </h1>
            <p class="section-heading__desc wow fadeInUp" data-wow-delay="0.2s">
                {{ __(@$calculation->data_values->subheading) }}
            </p>
        </div>

        <div class="calculator-wrapper">
            <div class="calculator-form wow fadeInLeft" data-wow-delay="0.3s">
                <div class="form-group">
                    <label for="investment-plan" class="form--label">
                        @lang('Choose Plan')
                    </label>
                    <select id="changePlan" class="form--control select2" required>
                        @foreach ($planList as $k => $data)
                            <option value="{{ $data->id }}" data-fixed_amount="{{ $data->fixed_amount }}" data-minimum_amount="{{ $data->minimum }}" data-maximum_amount="{{ $data->maximum }}">
                                {{ $data->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="investment-amount" class="form--label">
                        @lang('Invest Amount') <span class="invest-range"></span>
                    </label>
                    <input type="text" placeholder="0.00" class="invest-input form--control" onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')" required>
                </div>

                <button class="btn btn--gr flex-inline gap-2" id="calculate_profit_btn">
                    @lang('CALCULATE PROFIT')
                    <span class="d-flex">
                        <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                            <path d="M18.8371 9.07696L13.1374 2.74397C13.0158 2.60971 12.8461 2.53371 12.6662 2.53371H8.86643C8.61691 2.53371 8.39019 2.68064 8.2876 2.90863C8.18627 3.13788 8.22807 3.40513 8.39526 3.59006L13.7137 9.5L8.39526 15.4087C8.22807 15.5949 8.185 15.8621 8.2876 16.0901C8.39019 16.3194 8.61691 16.4663 8.86643 16.4663H12.6662C12.8461 16.4663 13.0158 16.389 13.1374 16.2573L18.8371 9.92431C19.0537 9.68366 19.0537 9.31635 18.8371 9.07696Z" fill="currentColor"></path>
                            <path d="M10.6047 9.07696L4.90498 2.74397C4.78339 2.60971 4.61366 2.53371 4.43381 2.53371H0.634011C0.384491 2.53371 0.15777 2.68064 0.0551759 2.90863C-0.046152 3.13788 -0.00435422 3.40513 0.162837 3.59006L5.48128 9.5L0.162837 15.4087C-0.00435422 15.5949 -0.0474186 15.8621 0.0551759 16.0901C0.15777 16.3194 0.384491 16.4663 0.634011 16.4663H4.43381C4.61366 16.4663 4.78339 16.389 4.90498 16.2573L10.6047 9.92431C10.8213 9.68366 10.8213 9.31635 10.6047 9.07696Z" fill="currentColor"></path>
                        </svg>
                    </span>
                </button>
            </div>

            <div class="calculator-result wow fadeInRight" data-wow-delay="0.3s">
                <h4 class="calculator-result-title">
                    @lang('Calculation Result')
                </h4>
                <ul class="calculator-result-list">
                    <li class="calculator-result-item">
                        <span class="title">@lang('Plan')</span>
                        <span class="value" id = "plan"></span>
                    </li>
                    <li class="calculator-result-item">
                        <span class="title">@lang('Amount')</span>
                        <span class="value" id = "amount"></span>
                    </li>
                    <li class="calculator-result-item">
                        <span class="title">@lang('Payment Interval')</span>
                        <span class="value" id="interval"></span>
                    </li>
                    <li class="calculator-result-item">
                        <span class="title">@lang('Profit')</span>
                        <span class="value" id="profit"></span>
                    </li>
                    <li class="calculator-result-item">
                        <span class="title">@lang('Capital Back')</span>
                        <span class="value" id="capital_back"></span>
                    </li>
                    <li class="calculator-result-item">
                        <span class="title">@lang('Total')</span>
                        <span class="value" id="total"></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

@push('style')
    <style>
        .form--control:disabled,
        .form--control[readonly] {
            border: 1px solid hsl(var(--white)/0.2);
            opacity: 0.75;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $(document).ready(function() {
                var curSym = '{{ gs('cur_sym') }}';

                function setPlanData() {
                    var selectedPlan = $('#changePlan').find(':selected');
                    var data = selectedPlan.data();
                    var fixedAmount = parseFloat(data.fixed_amount).toFixed(2);
                    var minimumAmount = parseFloat(data.minimum_amount).toFixed(2);
                    var maximumAmount = parseFloat(data.maximum_amount).toFixed(2);

                    if (fixedAmount > 0) {
                        $('.invest-input').val(fixedAmount);
                        $('.invest-input').attr('readonly', true);
                        $('.invest-range').text('');
                    } else {
                        $('.invest-input').val(minimumAmount);
                        $('.invest-input').attr('readonly', false);
                        $('.invest-range').text('(' + curSym + minimumAmount + ' - ' + curSym +
                            maximumAmount + ')');
                    }
                };

                $('#changePlan').on('change', function() {
                    setPlanData();
                });


                $('#calculate_profit_btn').on('click', function(e) {
                    initializeCalculator();
                });

                function initializeCalculator() {
                    var selectedPlan = $('#changePlan').find(':selected');
                    var planId = selectedPlan.val();
                    var data = selectedPlan.data();

                    var fixedAmount = parseFloat(data.fixed_amount).toFixed(2);
                    var minimumAmount = parseFloat(data.minimum_amount).toFixed(2);
                    var maximumAmount = parseFloat(data.maximum_amount).toFixed(2);

                    var investAmount = $('.invest-input').val();
                    var profitInput = $('.profit-input').text('');

                    $('.period').text('');

                    if (investAmount != '' && planId != null) {
                        ajaxPlanCalc(planId, investAmount)
                    }
                }

                setPlanData();
                initializeCalculator();

               
            });

            function ajaxPlanCalc(planId, investAmount) {
                console.log(investAmount);
                
                $.ajax({
                    url: "{{ route('planCalculator') }}",
                    type: "post",
                    data: {
                        planId,
                        _token: '{{ csrf_token() }}',
                        investAmount
                    },
                    success: function(response) {
                        console.log(response);
                        
                        if (response.errors) {
                            iziToast.error({
                                message: response.errors,
                                position: "topRight"
                            });
                        } else {
                            $('#plan').empty().text(response.planName ?? '-');
                            $('#amount').empty().text(response.amount ?? '-');
                            $('#interval').empty().text(response.interval ?? '-');
                            $('#profit').empty().text(response.netProfit ?? '-');
                            $('#capital_back').empty().text(response.capitalBack ?? '-');
                            $('#total').empty().text(response.total ?? '-');
                        }
                    }
                });
            }
        })(jQuery);
    </script>
@endpush