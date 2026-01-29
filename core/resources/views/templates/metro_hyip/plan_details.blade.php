@extends($activeTemplate . 'layouts.' . $layout)

@section('content')
<section class="pt-120 pb-120">
    <div class="container">
        <div class="row gy-4">
            {{-- Project Information (if plan belongs to a project) --}}
            @if($plan->project)
            <div class="col-lg-12">
                <div class="alert alert--primary" role="alert">
                    <div class="d-flex align-items-center gap-3">
                        <div class="flex-shrink-0">
                            <i class="las la-project-diagram fs-1"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="alert-heading mb-2">
                                <i class="las la-info-circle"></i> @lang('This plan is part of:') <strong>{{ __($plan->project->name) }}</strong>
                            </h5>
                            <p class="mb-2">{{ __(strLimit($plan->project->description, 150)) }}</p>
                            <div class="d-flex gap-3 flex-wrap">
                                <a href="{{ route('user.projects.show', $plan->project->id) }}" class="btn btn--sm btn--base">
                                    <i class="las la-eye"></i> @lang('View Project Details')
                                </a>
                                @if($plan->project->pdf)
                                <a href="{{ getImage(getFilePath('projectFile') . '/' . $plan->project->pdf) }}"
                                   target="_blank" class="btn btn--sm btn--outline-base">
                                    <i class="las la-file-pdf"></i> @lang('Download Project PDF')
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Plan Title --}}
            <div class="col-lg-12">
                <div class="plan-details-header-simple">
                    <h2 class="plan-details-title-simple">{{ __($plan->name) }}</h2>
                </div>
            </div>

            {{-- Tabs Navigation --}}
            <div class="col-lg-8">
                <div class="custom-tabs">
                    <ul class="nav nav-tabs" id="planDetailsTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="calculator-tab" data-bs-toggle="tab"
                                    data-bs-target="#calculator" type="button" role="tab">
                                <i class="las la-calculator"></i> @lang('Investment Calculator')
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="overview-tab" data-bs-toggle="tab"
                                    data-bs-target="#overview" type="button" role="tab">
                                <i class="las la-info-circle"></i> @lang('Overview')
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="investment-plan-tab" data-bs-toggle="tab"
                                    data-bs-target="#investment-plan" type="button" role="tab">
                                <i class="las la-chart-line"></i> @lang('Investment Plan')
                            </button>
                        </li>
                        <li class="nav-item" role="presentation" hidden>
                            <button class="nav-link" id="comments-tab" data-bs-toggle="tab"
                                    data-bs-target="#comments" type="button" role="tab">
                                <i class="las la-comments"></i> @lang('Comments')
                            </button>
                        </li>
                        @if($plan->pdf)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pdf-tab" data-bs-toggle="tab"
                                    data-bs-target="#pdf" type="button" role="tab">
                                <i class="las la-file-pdf"></i> @lang('PDF Document')
                            </button>
                        </li>
                        @endif
                    </ul>

                    <div class="tab-content mt-4" id="planDetailsTabsContent">
                        {{-- Calculator Tab --}}
                        <div class="tab-pane fade show active" id="calculator" role="tabpanel">
                            <div class="card custom--card">
                                <div class="card-header bg--base">
                                    <h5 class="card-title text-white mb-0">
                                        <i class="las la-calculator"></i> @lang('Calculate Your Returns')
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p class=" mb-4">
                                        @lang('Use this calculator to estimate your potential returns based on different investment amounts.')
                                    </p>

                                    {{-- Investment Amount Input --}}
                                    <div class="mb-4">
                                        <label class="form-label fw-bold">@lang('Investment Amount')</label>
                                        <div class="input-group input-group-lg">
                                            <span class="input-group-text">{{ __(gs('cur_text')) }}</span>
                                            <input type="number"
                                                   id="calcInvestmentAmount"
                                                   class="form-control"
                                                   @if($plan->fixed_amount > 0)
                                                       value="{{ $plan->fixed_amount }}"
                                                       readonly
                                                   @else
                                                       min="{{ $plan->minimum }}"
                                                       max="{{ $plan->maximum }}"
                                                       value="{{ $plan->minimum }}"
                                                       step="0.01"
                                                   @endif
                                                   placeholder="@lang('Enter amount')">
                                        </div>
                                        @if($plan->fixed_amount == 0)
                                        <div class="d-flex justify-content-between mt-2">
                                            <small class="">@lang('Min'): {{ showAmount($plan->minimum) }}</small>
                                            <small class="">@lang('Max'): {{ showAmount($plan->maximum) }}</small>
                                        </div>
                                        {{-- Range Slider --}}
                                        <input type="range"
                                               id="calcAmountSlider"
                                               class="form-range mt-2"
                                               min="{{ $plan->minimum }}"
                                               max="{{ $plan->maximum }}"
                                               value="{{ $plan->minimum }}"
                                               step="{{ $plan->minimum >= 100 ? 10 : 1 }}">
                                        @endif
                                    </div>

                                    {{-- Fractional Capital Return Checkbox --}}
                                    @if($plan->capital_back == 1 && $plan->capital_months_return > 0)
                                    <div class="mb-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="calcFractionalCapital" style="width: 50px; height: 25px;">
                                            <label class="form-check-label ms-2 fw-bold" for="calcFractionalCapital">
                                                <i class="las la-calculator text--base"></i> @lang('Calculate with Fractional Capital Return')
                                            </label>
                                        </div>
                                        <small class="text--info mt-1 d-block">
                                            <i class="las la-info-circle"></i> @lang('When enabled, interest will be calculated based on the remaining capital after each monthly return, resulting in lower total interest.')
                                        </small>
                                    </div>
                                    @endif

                                    {{-- Summary Cards --}}
                                    <div class="row gy-3 mb-4">
                                        <div class="col-md-4">
                                            <div class="calc-summary-card">
                                                <div class="calc-summary-icon">
                                                    <i class="las la-hand-holding-usd"></i>
                                                </div>
                                                <div class="calc-summary-content">
                                                    <small class="">@lang('Total Interest')</small>
                                                    <h4 class="mb-0" id="calcTotalInterest">{{ showAmount(0) }}</h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="calc-summary-card">
                                                <div class="calc-summary-icon">
                                                    <i class="las la-wallet"></i>
                                                </div>
                                                <div class="calc-summary-content">
                                                    <small class="">@lang('Total Return')</small>
                                                    <h4 class="mb-0" id="calcTotalReturn">{{ showAmount(0) }}</h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="calc-summary-card">
                                                <div class="calc-summary-icon">
                                                    <i class="las la-percentage"></i>
                                                </div>
                                                <div class="calc-summary-content">
                                                    <small class="">@lang('ROI')</small>
                                                    <h4 class="mb-0" id="calcROI">0%</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Monthly Breakdown Table --}}
                                    <div class="table-responsive">
                                        <table class="table" id="calcBreakdownTable">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>@lang('Month')</th>
                                                    <th class="text-end">@lang('Interest')</th>
                                                    <th class="text-end">@lang('Capital Return')</th>
                                                    <th class="text-end">@lang('Total Received')</th>
                                                    <th class="text-end">@lang('Balance')</th>
                                                </tr>
                                            </thead>
                                            <tbody id="calcBreakdownBody">
                                                {{-- Will be populated by JavaScript --}}
                                            </tbody>
                                            <tfoot class="table-dark fw-bold">
                                                <tr>
                                                    <td>@lang('TOTAL')</td>
                                                    <td class="text-end" id="calcFooterInterest" data-label="@lang('Total Interest')">{{ showAmount(0) }}</td>
                                                    <td class="text-end" id="calcFooterCapital" data-label="@lang('Total Capital')">{{ showAmount(0) }}</td>
                                                    <td class="text-end" id="calcFooterTotal" data-label="@lang('Total Received')">{{ showAmount(0) }}</td>
                                                    <td class="text-end" id="calcFooterBalance" data-label="@lang('Final Balance')">{{ showAmount(0) }}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    {{-- Invest Now Button --}}
                                    <div class="mt-4 text-center">
                                        <button type="button"
                                                class="btn btn--base btn-lg px-5 investModal"
                                                data-bs-toggle="modal"
                                                data-bs-target="#investModal"
                                                data-plan="{{ $plan }}">
                                            <i class="las la-rocket"></i> @lang('Invest Now')
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Overview Tab --}}
                        <div class="tab-pane fade" id="overview" role="tabpanel">
                            <div class="plan-overview-content">
                                <div class="row gy-4">
                                    <div class="col-lg-12">
                                        <div class="card custom--card">
                                            <div class="card-header">
                                                <h5 class="card-title">@lang('Description')</h5>
                                            </div>
                                            <div class="card-body">
                                                <p class="plan-description">{{ __($plan->description) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Investment Plan Tab --}}
                        <div class="tab-pane fade" id="investment-plan" role="tabpanel">
                            <div class="card custom--card">
                                <div class="card-header">
                                    <h5 class="card-title">@lang('Investment Details')</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row gy-3">
                                        <div class="col-md-6">
                                            <div class="plan-detail-item">
                                                <span class="plan-detail-label">@lang('Interest Type'):</span>
                                                <span class="plan-detail-value">
                                                    {{ $plan->interest_type == 1 ? __('Percentage') : __('Fixed') }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="plan-detail-item">
                                                <span class="plan-detail-label">@lang('Interest Amount'):</span>
                                                <span class="plan-detail-value">
                                                    {{ showAmount($plan->interest, currencyFormat:false) }}{{ $plan->interest_type == 1 ? '%' : ' ' . __(gs('cur_text')) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="plan-detail-item">
                                                <span class="plan-detail-label">@lang('Frequency of Pay'):</span>
                                                <span class="plan-detail-value">
                                                    @lang('Every') {{ $plan->timeSetting->time }} @lang('Hours')
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="plan-detail-item">
                                                <span class="plan-detail-label">@lang('Return Type'):</span>
                                                <span class="plan-detail-value">
                                                    {{ $plan->lifetime == 1 ? __('Lifetime') : __('Repeat') }}
                                                </span>
                                            </div>
                                        </div>
                                        @if ($plan->lifetime == 0)
                                        <div class="col-md-6">
                                            <div class="plan-detail-item">
                                                <span class="plan-detail-label">@lang('Total Return'):</span>
                                                <span class="plan-detail-value">
                                                    {{ $plan->interest }}{{ $plan->interest_type == 1 ? '%' : ' ' . __(gs('cur_text')) }}
                                                    @if ($plan->capital_back == 1)
                                                        + <span class="badge badge--success">@lang('Capital')</span>
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                        @endif
                                        @if ($plan->capital_back == 1)
                                        <div class="col-md-6">
                                            <div class="plan-detail-item">
                                                <span class="plan-detail-label">@lang('Capital Back'):</span>
                                                <span class="plan-detail-value">
                                                    <span class="badge badge--success">@lang('Yes')</span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="plan-detail-item">
                                                <span class="plan-detail-label">@lang('Months to Start Capital Return'):</span>
                                                <span class="plan-detail-value">
                                                    {{ $plan->capital_months_return }} @lang('Months')
                                                </span>
                                            </div>
                                        </div>
                                        @endif
                                        @if ($plan->compound_interest)
                                        <div class="col-md-6">
                                            <div class="plan-detail-item">
                                                <span class="plan-detail-label">@lang('Compound Interest'):</span>
                                                <span class="plan-detail-value">
                                                    <span class="badge badge--success">@lang('Available')</span>
                                                </span>
                                            </div>
                                        </div>
                                        @endif
                                        @if ($plan->hold_capital)
                                        <div class="col-md-6">
                                            <div class="plan-detail-item">
                                                <span class="plan-detail-label">@lang('Hold Capital'):</span>
                                                <span class="plan-detail-value">
                                                    <span class="badge badge--info">@lang('Yes')</span>
                                                </span>
                                            </div>
                                        </div>
                                        @endif
                                    </div>

                                    {{-- Interest Distribution Section --}}
                                    @if($plan->interest_distribution && isset($plan->interest_distribution['enabled']) && $plan->interest_distribution['enabled'] && isset($plan->interest_distribution['segments']))
                                    <hr class="my-4">
                                    <div class="distribution-visualization">
                                        <h5 class="mb-3">
                                            <i class="las la-chart-area"></i> @lang('Interest Distribution Timeline')
                                        </h5>
                                        <p class=" mb-4">
                                            @lang('This plan distributes returns across different periods based on the project\'s expected performance.')
                                        </p>

                                        {{-- Visual Progress Bar --}}
                                        <div class="distribution-progress mb-4">
                                            <div class="progress" style="height: 40px;">
                                                @php
                                                    $totalMonths = $plan->repeat_time;
                                                    $accumulatedMonths = 0;
                                                    $colors = ['#28a745', '#17a2b8', '#ffc107', '#dc3545', '#6f42c1'];
                                                @endphp
                                                @foreach($plan->interest_distribution['segments'] as $index => $segment)
                                                    @php
                                                        $widthPercentage = ($segment['months'] / $totalMonths) * 100;
                                                        $color = $colors[$index % count($colors)];
                                                    @endphp
                                                    <div class="progress-bar" role="progressbar"
                                                         style="width: {{ $widthPercentage }}%; background-color: {{ $color }};"
                                                         data-bs-toggle="tooltip"
                                                         title="{{ $segment['description'] }}: {{ $segment['percentage'] }}%">
                                                        <strong>{{ $segment['percentage'] }}%</strong>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="d-flex justify-content-between mt-2 small ">
                                                <span>@lang('Month') 1</span>
                                                <span>@lang('Month') {{ $totalMonths }}</span>
                                            </div>
                                        </div>

                                        {{-- Segments Detail Cards --}}
                                        <div class="row gy-3">
                                            @foreach($plan->interest_distribution['segments'] as $index => $segment)
                                            @php
                                                $monthlyRate = $segment['months'] > 0 ? ($segment['percentage'] / $segment['months']) : 0;
                                                $startMonth = array_sum(array_column(array_slice($plan->interest_distribution['segments'], 0, $index), 'months')) + 1;
                                                $endMonth = $startMonth + $segment['months'] - 1;
                                            @endphp
                                            <div class="col-md-6 col-lg-4">
                                                <div class="card border-0 shadow-sm h-100">
                                                    <div class="card-body">
                                                        <div class="d-flex align-items-center mb-3">
                                                            <div class="segment-icon me-3">
                                                                <i class="las la-calendar-check" style="font-size: 32px; color: {{ $colors[$index % count($colors)] }};"></i>
                                                            </div>
                                                            <div>
                                                                <h6 class="mb-0">@lang('Segment') {{ $segment['segment'] }}</h6>
                                                                <small class="">{{ $segment['description'] }}</small>
                                                            </div>
                                                        </div>
                                                        <ul class="list-unstyled mb-0">
                                                            <li class="mb-2">
                                                                <i class="las la-clock text-primary"></i>
                                                                <strong>@lang('Period'):</strong> @lang('Months') {{ $startMonth }}-{{ $endMonth }}
                                                            </li>
                                                            <li class="mb-2">
                                                                <i class="las la-hourglass-half text-info"></i>
                                                                <strong>@lang('Duration'):</strong> {{ $segment['months'] }} @lang('months')
                                                            </li>
                                                            <li class="mb-2">
                                                                <i class="las la-percentage text-success"></i>
                                                                <strong>@lang('Total Interest'):</strong> {{ $segment['percentage'] }}%
                                                            </li>
                                                            <li class="mb-0">
                                                                <i class="las la-calculator text-warning"></i>
                                                                <strong>@lang('Monthly Rate'):</strong> {{ number_format($monthlyRate, 4) }}%
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>

                                        {{-- Investment Example Calculator --}}
                                        <div class="card bg-light border-0 mt-4">
                                            <div class="card-body">
                                                <h6 class="mb-3">
                                                    <i class="las la-lightbulb"></i> @lang('Example Calculation')
                                                </h6>
                                                <p class="mb-2">
                                                    @lang('For an investment of') <strong>{{ showAmount(100) }}</strong>:
                                                </p>
                                                <ul class="mb-0">
                                                    @foreach($plan->interest_distribution['segments'] as $segment)
                                                    @php
                                                        $earnings = 100 * ($segment['percentage'] / 100);
                                                    @endphp
                                                    <li>
                                                        {{ $segment['description'] }}: <strong>{{ showAmount($earnings) }}</strong>
                                                        <small class="">({{ $segment['percentage'] }}%)</small>
                                                    </li>
                                                    @endforeach
                                                </ul>
                                                @php
                                                    $totalEarnings = 100 * ($plan->interest / 100);
                                                @endphp
                                                <hr class="my-2">
                                                <p class="mb-0">
                                                    <strong>@lang('Total Expected Profit'):</strong> {{ showAmount($totalEarnings) }}
                                                    @if($plan->capital_back == 1)
                                                        + <span class="badge badge--success">@lang('Capital Return')</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Comments Tab --}}
                        <div class="tab-pane fade" id="comments" role="tabpanel">
                            <div class="card custom--card">
                                <div class="card-header">
                                    <h5 class="card-title">@lang('User Comments')</h5>
                                </div>
                                <div class="card-body">
                                    <div class="text-center py-5">
                                        <i class="las la-comments" style="font-size: 48px; color: #ccc;"></i>
                                        <p class=" mt-3">@lang('Comments section coming soon')</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- PDF Viewer Tab --}}
                        @if($plan->pdf)
                        <div class="tab-pane fade" id="pdf" role="tabpanel">
                            <div class="card custom--card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">@lang('Plan Documentation')</h5>
                                    <a href="{{ getImage(getFilePath('planFile') . '/' . $plan->pdf) }}"
                                       target="_blank" class="btn btn--sm btn--base">
                                        <i class="las la-download"></i> @lang('Download PDF')
                                    </a>
                                </div>
                                <div class="card-body">
                                    <div class="pdf-viewer-container">
                                        <iframe src="{{ getImage(getFilePath('planFile') . '/' . $plan->pdf) }}"
                                                width="100%" height="800px" style="border: none;">
                                        </iframe>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card custom--card">
                    <div class="card-header bg--base">
                        <h5 class="card-title text-white">@lang('Quick Stats')</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>@lang('ROI')</span>
                                <span class="fw-bold">
                                    {{ showAmount($plan->interest, currencyFormat:false) }}{{ $plan->interest_type == 1 ? '%' : ' ' . __(gs('cur_text')) }}
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>@lang('Duration')</span>
                                <span class="fw-bold">
                                    @if ($plan->lifetime == 0)
                                        {{ $plan->repeat_time }} @lang('Months')
                                    @else
                                        @lang('Lifetime')
                                    @endif
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>@lang('Investment Range')</span>
                                <span class="fw-bold">
                                    @if ($plan->fixed_amount == 0)
                                        {{ showAmount($plan->minimum) }} - {{ showAmount($plan->maximum) }}
                                    @else
                                        {{ showAmount($plan->fixed_amount) }}
                                    @endif
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>@lang('Days to Init')</span>
                                <span class="fw-bold">{{ $plan->days_to_init }}</span>
                            </li>
                        </ul>

                        <div class="mt-4">
                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#investModal"
                                data-plan="{{ $plan }}" class="btn btn--base w-100 investModal">
                                @lang('Invest Now')
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Include Global Investment Calculator --}}
@include($activeTemplate . 'partials.investment_calculator')

{{-- Include Investment Modal --}}
@include($activeTemplate . 'partials.invest_modal', ['plans' => [$plan], 'gatewayCurrency' => $gatewayCurrency])

@endsection

@push('script')
<script>
    'use strict';

    // Plan data from backend
    const planData = {
        fixedAmount: {{ $plan->fixed_amount }},
        minimum: {{ $plan->minimum }},
        maximum: {{ $plan->maximum }},
        interestType: {{ $plan->interest_type }}, // 1 = percentage, 0 = fixed
        interest: {{ $plan->interest }},
        lifetime: {{ $plan->lifetime }},
        repeatTime: {{ $plan->repeat_time ?? 12 }}, // Default to 12 months if not set
        capitalBack: {{ $plan->capital_back }},
        capitalMonthsReturn: {{ $plan->capital_months_return ?? 0 }},
        compoundInterest: {{ $plan->compound_interest ?? 0 }},
        hasInterestDistribution: {{ isset($plan->interest_distribution['enabled']) && $plan->interest_distribution['enabled'] ? 'true' : 'false' }},
        @if(isset($plan->interest_distribution['enabled']) && $plan->interest_distribution['enabled'] && isset($plan->interest_distribution['segments']))
        interestDistribution: {!! json_encode($plan->interest_distribution['segments']) !!}
        @else
        interestDistribution: null
        @endif
    };

    const currencySymbol = '{{ gs('cur_text') }}';

    // Initialize calculator on page load
    $(document).ready(function() {
        // Sync slider and input
        $('#calcAmountSlider').on('input', function() {
            $('#calcInvestmentAmount').val(this.value);
            calculateReturns();
        });

        $('#calcInvestmentAmount').on('input', function() {
            const value = parseFloat(this.value) || 0;
            $('#calcAmountSlider').val(value);
            calculateReturns();
        });

        // Fractional capital checkbox handler
        $('#calcFractionalCapital').on('change', function() {
            calculateReturns();
        });

        // Initial calculation
        calculateReturns();
    });

    function calculateReturns() {
        const investmentAmount = parseFloat($('#calcInvestmentAmount').val()) || 0;
        const fractionalCapital = $('#calcFractionalCapital').is(':checked');

        if (investmentAmount <= 0) {
            resetCalculator();
            return;
        }

        // Validate amount
        if (planData.fixedAmount > 0) {
            // Fixed amount plan
            if (investmentAmount !== planData.fixedAmount) {
                $('#calcInvestmentAmount').val(planData.fixedAmount);
                return;
            }
        } else {
            // Range plan - validate min/max
            if (investmentAmount < planData.minimum || investmentAmount > planData.maximum) {
                return;
            }
        }

        // Use global InvestmentCalculator
        if (typeof InvestmentCalculator !== 'undefined') {
            const result = InvestmentCalculator.calculate({
                investmentAmount: investmentAmount,
                interestType: planData.interestType,
                interest: planData.interest,
                repeatTime: planData.repeatTime,
                lifetime: planData.lifetime,
                capitalBack: planData.capitalBack,
                capitalMonthsReturn: planData.capitalMonthsReturn,
                fractionalCapital: fractionalCapital,
                compoundInterest: planData.compoundInterest,
                interestDistribution: planData.interestDistribution
            });

            updateCalculatorUI(result, fractionalCapital);
        } else {
            // Fallback to previous calculation method
            if (planData.hasInterestDistribution && planData.interestDistribution) {
                calculateWithInterestDistributionFallback(investmentAmount, fractionalCapital);
            } else {
                calculateStandardPlanFallback(investmentAmount, fractionalCapital);
            }
        }
    }

    // Fallback calculation for standard plan (without global calculator)
    function calculateStandardPlanFallback(investmentAmount, fractionalCapital) {
        const monthlyBreakdown = [];
        let totalInterest = 0;
        let totalCapitalReturn = 0;
        let cumulativeBalance = 0;
        let remainingCapital = investmentAmount;

        const totalMonths = planData.lifetime === 1 ? 12 : planData.repeatTime;

        let baseMonthlyInterestRate = 0;
        if (planData.interestType === 1) {
            baseMonthlyInterestRate = planData.interest / totalMonths;
        }

        let capitalPerMonth = 0;
        if (planData.capitalBack === 1 && planData.capitalMonthsReturn > 0) {
            const remainingMonths = totalMonths - planData.capitalMonthsReturn + 1;
            capitalPerMonth = investmentAmount / remainingMonths;
        }

        for (let month = 1; month <= totalMonths; month++) {
            let monthlyInterest = 0;
            if (planData.interestType === 1) {
                if (fractionalCapital && planData.capitalBack === 1 && planData.capitalMonthsReturn > 0) {
                    monthlyInterest = remainingCapital * (baseMonthlyInterestRate / 100);
                } else {
                    monthlyInterest = investmentAmount * (baseMonthlyInterestRate / 100);
                }
            } else {
                monthlyInterest = planData.interest / totalMonths;
            }

            let monthlyCapitalReturn = 0;
            if (planData.capitalBack === 1 && planData.capitalMonthsReturn > 0 && month >= planData.capitalMonthsReturn) {
                monthlyCapitalReturn = capitalPerMonth;
                totalCapitalReturn += monthlyCapitalReturn;
                remainingCapital -= monthlyCapitalReturn;
                if (remainingCapital < 0) remainingCapital = 0;
            }

            totalInterest += monthlyInterest;
            const monthlyTotal = monthlyInterest + monthlyCapitalReturn;
            cumulativeBalance += monthlyInterest;

            monthlyBreakdown.push({
                month: month,
                interest: monthlyInterest,
                capitalReturn: monthlyCapitalReturn,
                total: monthlyTotal,
                balance: cumulativeBalance,
                remainingCapital: remainingCapital
            });
        }

        if (planData.capitalBack === 1 && planData.capitalMonthsReturn === 0) {
            monthlyBreakdown.push({
                month: 'Final',
                interest: 0,
                capitalReturn: investmentAmount,
                total: investmentAmount,
                balance: cumulativeBalance + investmentAmount,
                remainingCapital: 0,
                isFinalCapitalReturn: true
            });
            totalCapitalReturn = investmentAmount;
        }

        const result = {
            totalInterest,
            totalCapitalReturn,
            totalReturn: totalInterest + totalCapitalReturn,
            roi: (totalInterest / investmentAmount) * 100,
            monthlyBreakdown,
            investmentAmount
        };

        updateCalculatorUI(result, fractionalCapital);
    }

    // Fallback calculation for interest distribution
    function calculateWithInterestDistributionFallback(investmentAmount, fractionalCapital) {
        const monthlyBreakdown = [];
        let totalInterest = 0;
        let totalCapitalReturn = 0;
        let cumulativeBalance = 0;
        let remainingCapital = investmentAmount;
        let currentMonth = 1;

        let capitalPerMonth = 0;
        if (planData.capitalBack === 1 && planData.capitalMonthsReturn > 0) {
            const remainingMonths = planData.repeatTime - planData.capitalMonthsReturn + 1;
            capitalPerMonth = investmentAmount / remainingMonths;
        }

        planData.interestDistribution.forEach(segment => {
            const segmentMonths = segment.months;
            const segmentPercentage = segment.percentage;
            const monthlyRate = segmentPercentage / segmentMonths;

            for (let i = 0; i < segmentMonths; i++) {
                let monthlyInterest = 0;
                if (fractionalCapital && planData.capitalBack === 1 && planData.capitalMonthsReturn > 0) {
                    monthlyInterest = remainingCapital * (monthlyRate / 100);
                } else {
                    monthlyInterest = investmentAmount * (monthlyRate / 100);
                }

                let monthlyCapitalReturn = 0;
                if (planData.capitalBack === 1 && planData.capitalMonthsReturn > 0 && currentMonth >= planData.capitalMonthsReturn) {
                    monthlyCapitalReturn = capitalPerMonth;
                    totalCapitalReturn += monthlyCapitalReturn;
                    remainingCapital -= monthlyCapitalReturn;
                    if (remainingCapital < 0) remainingCapital = 0;
                }

                totalInterest += monthlyInterest;
                const monthlyTotal = monthlyInterest + monthlyCapitalReturn;
                cumulativeBalance += monthlyInterest;

                monthlyBreakdown.push({
                    month: currentMonth,
                    interest: monthlyInterest,
                    capitalReturn: monthlyCapitalReturn,
                    total: monthlyTotal,
                    balance: cumulativeBalance,
                    remainingCapital: remainingCapital,
                    segment: segment.description
                });

                currentMonth++;
            }
        });

        if (planData.capitalBack === 1 && planData.capitalMonthsReturn === 0) {
            monthlyBreakdown.push({
                month: 'Final',
                interest: 0,
                capitalReturn: investmentAmount,
                total: investmentAmount,
                balance: cumulativeBalance + investmentAmount,
                remainingCapital: 0,
                isFinalCapitalReturn: true
            });
            totalCapitalReturn = investmentAmount;
        }

        const result = {
            totalInterest,
            totalCapitalReturn,
            totalReturn: totalInterest + totalCapitalReturn,
            roi: (totalInterest / investmentAmount) * 100,
            monthlyBreakdown,
            investmentAmount
        };

        updateCalculatorUI(result, fractionalCapital);
    }

    function updateCalculatorUI(result, fractionalCapital) {
        const { totalInterest, totalCapitalReturn, totalReturn, roi, monthlyBreakdown, investmentAmount } = result;

        // Update summary cards
        $('#calcTotalInterest').text(formatAmount(totalInterest));
        $('#calcTotalReturn').text(formatAmount(totalReturn));
        $('#calcROI').text(roi.toFixed(2) + '%');

        // Update breakdown table
        let tableHTML = '';
        monthlyBreakdown.forEach(row => {
            const rowClass = row.isFinalCapitalReturn ? 'fw-bold' : '';
            const monthLabel = row.isFinalCapitalReturn ? '@lang("Capital Return")' : row.month;

            tableHTML += `
                <tr class="${rowClass}">
                    <td data-label="@lang('Month')">
                        ${monthLabel}
                        ${row.segment ? `<br><small class="">${row.segment}</small>` : ''}
                    </td>
                    <td class="text-end" data-label="@lang('Interest')">${formatAmount(row.interest)}</td>
                    <td class="text-end" data-label="@lang('Capital Return')">${formatAmount(row.capitalReturn)}</td>
                    <td class="text-end fw-bold" data-label="@lang('Total Received')">${formatAmount(row.total)}</td>
                    <td class="text-end text-success" data-label="@lang('Balance')">${formatAmount(row.balance)}</td>
                </tr>
            `;
        });
        $('#calcBreakdownBody').html(tableHTML);

        // Update footer
        $('#calcFooterInterest').text(formatAmount(totalInterest));
        $('#calcFooterCapital').text(formatAmount(totalCapitalReturn));
        $('#calcFooterTotal').text(formatAmount(totalReturn));
        $('#calcFooterBalance').text(formatAmount(totalReturn));

        // Show indicator if fractional calculation is active
        if (fractionalCapital) {
            $('#calcTotalInterest').closest('.calc-summary-card').find('small').html('@lang("Total Interest") <span class=\"badge bg-info ms-1\">@lang("Fractional")</span>');
        } else {
            $('#calcTotalInterest').closest('.calc-summary-card').find('small').html('@lang("Total Interest")');
        }
    }

    function resetCalculator() {
        $('#calcTotalInterest').text(formatAmount(0));
        $('#calcTotalReturn').text(formatAmount(0));
        $('#calcROI').text('0%');
        $('#calcBreakdownBody').html('<tr><td colspan="5" class="text-center ">@lang("Enter an investment amount to see projections")</td></tr>');
        $('#calcFooterInterest').text(formatAmount(0));
        $('#calcFooterCapital').text(formatAmount(0));
        $('#calcFooterTotal').text(formatAmount(0));
        $('#calcFooterBalance').text(formatAmount(0));
    }

    function formatAmount(amount) {
        return currencySymbol + ' ' + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }
</script>
@endpush

@push('style')
<style>
    /* Calculator Styles */
    .calc-summary-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
        padding: 20px;
        color: white;
        display: flex;
        align-items: center;
        gap: 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .calc-summary-card:hover {
        transform: translateY(-5px);
    }

    .calc-summary-card:nth-child(2) .calc-summary-card {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .calc-summary-card:nth-child(3) .calc-summary-card {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .calc-summary-icon {
        font-size: 36px;
        opacity: 0.9;
    }

    .calc-summary-content h4 {
        color: white;
        margin: 0;
        font-weight: 700;
    }

    .calc-summary-content small {
        color: rgba(255, 255, 255, 0.9);
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    #calcBreakdownTable {
        font-size: 14px;
    }

    #calcBreakdownTable thead th {
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    #calcBreakdownTable tbody tr:hover {
        background-color: rgba(102, 126, 234, 0.1);
    }

    .form-range {
        height: 6px;
    }

    .form-range::-webkit-slider-thumb {
        width: 20px;
        height: 20px;
        background: hsl(var(--base));
    }

    .form-range::-moz-range-thumb {
        width: 20px;
        height: 20px;
        background: hsl(var(--base));
    }

    .plan-details-header-simple {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
        padding: 25px 30px;
        margin-bottom: 30px;
    }

    .plan-details-title-simple {
        color: white;
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
    }

    .custom-tabs .nav-tabs {
        border-bottom: 2px solid #e0e0e0;
        margin-bottom: 0;
    }

    .custom-tabs .nav-link {
        border: none;
        color: #666;
        padding: 15px 25px;
        font-weight: 500;
        transition: all 0.3s;
        border-bottom: 3px solid transparent;
    }

    .custom-tabs .nav-link:hover {
        color: hsl(var(--base));
        border-bottom-color: hsl(var(--base) / 0.3);
    }

    .custom-tabs .nav-link.active {
        color: hsl(var(--base));
        background: transparent;
        border-bottom-color: hsl(var(--base));
    }

    .custom-tabs .nav-link i {
        font-size: 18px;
        margin-right: 5px;
    }

    .plan-description {
        font-size: 16px;
        line-height: 1.8;
        color: #f8f9fa;
    }

    .plan-detail-item {
        display: flex;
        justify-content: space-between;
        padding: 12px;
        background: #f8f9fa;
        border-radius: 5px;
    }

    .plan-detail-label {
        font-weight: 500;
        color: #666;
    }

    .plan-detail-value {
        font-weight: 600;
        color: #333;
    }

    .pdf-viewer-container {
        background: #f5f5f5;
        border-radius: 5px;
        overflow: hidden;
    }

    @media (max-width: 768px) {
        .plan-details-header-simple {
            padding: 20px;
        }

        .plan-details-title-simple {
            font-size: 1.5rem;
        }

        .custom-tabs .nav-link {
            padding: 10px 15px;
            font-size: 14px;
        }

        .custom-tabs .nav-link i {
            display: none;
        }

        /* Responsive Calculator Table */
        .calc-summary-card {
            padding: 15px;
            gap: 10px;
        }

        .calc-summary-icon {
            font-size: 28px;
        }

        .calc-summary-content h4 {
            font-size: 1.1rem;
        }

        #calcBreakdownTable {
            font-size: 12px;
        }

        #calcBreakdownTable thead th {
            font-size: 10px;
            padding: 8px 4px;
            white-space: nowrap;
        }

        #calcBreakdownTable tbody td,
        #calcBreakdownTable tfoot td {
            padding: 8px 4px;
        }

        /* Stack cards on mobile */
        .calc-summary-card {
            flex-direction: column;
            text-align: center;
            gap: 8px;
        }
    }

    /* Extra small devices - Transform table to card layout */
    @media (max-width: 576px) {
        /* Remove table-responsive scroll on mobile */
        .table-responsive {
            overflow-x: visible;
        }

        #calcBreakdownTable {
            display: block;
            border: none;
        }

        #calcBreakdownTable thead {
            display: none !important;
            visibility: hidden;
            height: 0;
            position: absolute;
        }

        #calcBreakdownTable tbody {
            display: block;
        }

        #calcBreakdownTable tbody tr {
            display: block;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            margin-bottom: 12px;
            padding: 12px;
        }

        #calcBreakdownTable tbody tr:hover {
            background: rgba(102, 126, 234, 0.15);
        }

        #calcBreakdownTable tbody td {
            display: flex !important;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0 !important;
            border: none;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            text-align: right;
        }

        #calcBreakdownTable tbody td:last-child {
            border-bottom: none;
        }

        #calcBreakdownTable tbody td::before {
            content: attr(data-label);
            font-weight: 600;
            color: rgba(255, 255, 255, 0.7);
            text-align: left;
            flex-shrink: 0;
            margin-right: 10px;
            font-size: 11px;
            text-transform: uppercase;
        }

        /* Footer table - Totals card */
        #calcBreakdownTable tfoot {
            display: block !important;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 8px;
            padding: 12px;
            margin-top: 8px;
        }

        #calcBreakdownTable tfoot tr {
            display: block !important;
        }

        #calcBreakdownTable tfoot td {
            display: flex !important;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0 !important;
            border: none;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            background: transparent !important;
        }

        #calcBreakdownTable tfoot td:first-child {
            display: none !important;
        }

        #calcBreakdownTable tfoot td:last-child {
            border-bottom: none;
        }

        #calcBreakdownTable tfoot td::before {
            content: attr(data-label);
            font-weight: 600;
            color: rgba(255, 255, 255, 0.95);
            font-size: 12px;
            flex-shrink: 0;
            margin-right: 10px;
        }

        #calcBreakdownTable tfoot td .text-end {
            text-align: right;
        }
    }
</style>
@endpush
