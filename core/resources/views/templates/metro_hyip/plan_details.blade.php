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

            {{-- Plan Image and Title --}}
            <div class="col-lg-12">
                <div class="plan-details-header">
                    <div class="plan-details-image">
                        @if($plan->image)
                            <img src="{{ getImage(getFilePath('planImage') . '/' . $plan->image, getFileSize('planImage')) }}"
                                 alt="{{ __($plan->name) }}" class="w-100">
                        @else
                            <img src="{{ getImage(getFilePath('planImage') . '/default.png', getFileSize('planImage')) }}"
                                 alt="{{ __($plan->name) }}" class="w-100">
                        @endif
                    </div>
                    <div class="plan-details-title-overlay">
                        <h2 class="plan-details-title">{{ __($plan->name) }}</h2>
                    </div>
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
                        <li class="nav-item" role="presentation">
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
                                                    <td class="text-end" id="calcFooterInterest">{{ showAmount(0) }}</td>
                                                    <td class="text-end" id="calcFooterCapital">{{ showAmount(0) }}</td>
                                                    <td class="text-end" id="calcFooterTotal">{{ showAmount(0) }}</td>
                                                    <td class="text-end" id="calcFooterBalance">{{ showAmount(0) }}</td>
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
                                                    {{ $plan->interest * $plan->repeat_time }}{{ $plan->interest_type == 1 ? '%' : ' ' . __(gs('cur_text')) }}
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
        interestType: {{ $plan->interest_type }}, // 1 = percentage, 2 = fixed
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

        // Initial calculation
        calculateReturns();
    });

    function calculateReturns() {
        const investmentAmount = parseFloat($('#calcInvestmentAmount').val()) || 0;

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

        // Calculate based on plan type
        if (planData.hasInterestDistribution && planData.interestDistribution) {
            calculateWithInterestDistribution(investmentAmount);
        } else {
            calculateStandardPlan(investmentAmount);
        }
    }

    function calculateStandardPlan(investmentAmount) {
        const monthlyBreakdown = [];
        let totalInterest = 0;
        let totalCapitalReturn = 0;
        let cumulativeBalance = 0; // Solo acumula los retornos (intereses)
        let remainingCapital = investmentAmount; // Capital que queda en la plataforma

        const totalMonths = planData.lifetime === 1 ? 12 : planData.repeatTime; // If lifetime, show 12 months as example

        // Calcular el interés mensual (dividir el porcentaje total entre los meses)
        let baseMonthlyInterestRate;
        if (planData.interestType === 1) {
            // Percentage-based: dividir el porcentaje total entre los meses
            baseMonthlyInterestRate = planData.interest / totalMonths;
        }

        for (let month = 1; month <= totalMonths; month++) {
            // Calculate interest for this month PRIMERO con el capital actual
            let monthlyInterest = 0;
            if (planData.interestType === 1) {
                // Percentage-based interest sobre el capital que queda en la plataforma
                monthlyInterest = remainingCapital * (baseMonthlyInterestRate / 100);
            } else {
                // Fixed interest (dividir entre los meses)
                monthlyInterest = planData.interest / totalMonths;
            }

            // Calculate capital return for this month DESPUÉS de calcular el interés
            let monthlyCapitalReturn = 0;
            if (planData.capitalBack === 1 && planData.capitalMonthsReturn > 0 && month >= planData.capitalMonthsReturn) {
                // Capital fraccionado: se devuelve desde el mes especificado
                const remainingMonths = totalMonths - planData.capitalMonthsReturn + 1;
                monthlyCapitalReturn = investmentAmount / remainingMonths;
                totalCapitalReturn += monthlyCapitalReturn;

                // Reducir el capital restante DESPUÉS de calcular el interés (para el próximo mes)
                remainingCapital -= monthlyCapitalReturn;
            }

            totalInterest += monthlyInterest;
            const monthlyTotal = monthlyInterest + monthlyCapitalReturn;
            cumulativeBalance += monthlyInterest; // Solo acumula los intereses

            monthlyBreakdown.push({
                month: month,
                interest: monthlyInterest,
                capitalReturn: monthlyCapitalReturn,
                total: monthlyTotal,
                balance: cumulativeBalance
            });
        }

        // Si hay capital back pero NO es fraccionado (capital_months_return = 0),
        // agregar una fila final con el retorno total del capital
        if (planData.capitalBack !== 1 && planData.capitalMonthsReturn === 0) {
            monthlyBreakdown.push({
                month: 'Final',
                interest: 0,
                capitalReturn: investmentAmount,
                total: investmentAmount,
                balance: cumulativeBalance + investmentAmount,
                isFinalCapitalReturn: true
            });
            totalCapitalReturn = investmentAmount;
        }

        // Update UI
        updateCalculatorUI(investmentAmount, totalInterest, totalCapitalReturn, monthlyBreakdown);
    }

    function calculateWithInterestDistribution(investmentAmount) {
        const monthlyBreakdown = [];
        let totalInterest = 0;
        let totalCapitalReturn = 0;
        let cumulativeBalance = 0; // Solo acumula los retornos (intereses)
        let remainingCapital = investmentAmount; // Capital que queda en la plataforma
        let currentMonth = 1;

        // Process each segment
        planData.interestDistribution.forEach(segment => {
            const segmentMonths = segment.months;
            const segmentPercentage = segment.percentage;
            const monthlyRate = segmentPercentage / segmentMonths;

            for (let i = 0; i < segmentMonths; i++) {
                // Calculate interest for this month PRIMERO con el capital actual
                const monthlyInterest = investmentAmount * (monthlyRate / 100);

                // Calculate capital return for this month DESPUÉS de calcular el interés
                let monthlyCapitalReturn = 0;
                if (planData.capitalBack === 1 && planData.capitalMonthsReturn > 0 && currentMonth >= planData.capitalMonthsReturn) {
                    // Capital fraccionado: se devuelve desde el mes especificado
                    const remainingMonths = planData.repeatTime - planData.capitalMonthsReturn + 1;
                    monthlyCapitalReturn = investmentAmount / remainingMonths;
                    totalCapitalReturn += monthlyCapitalReturn;

                    // Reducir el capital restante DESPUÉS de calcular el interés (para el próximo mes)
                    remainingCapital -= monthlyCapitalReturn;
                }

                totalInterest += monthlyInterest;
                const monthlyTotal = monthlyInterest + monthlyCapitalReturn;
                cumulativeBalance += monthlyInterest; // Solo acumula los intereses

                monthlyBreakdown.push({
                    month: currentMonth,
                    interest: monthlyInterest,
                    capitalReturn: monthlyCapitalReturn,
                    total: monthlyTotal,
                    balance: cumulativeBalance,
                    segment: segment.description
                });

                currentMonth++;
            }
        });

        // NO agregar fila final de capital para planes con interest distribution
        // El capital se maneja igual que en planes estándar (fraccionado o al final según config)
            monthlyBreakdown.push({
                month: 'Final',
                interest: 0,
                capitalReturn: investmentAmount,
                total: investmentAmount,
                balance: cumulativeBalance + investmentAmount,
                isFinalCapitalReturn: true
            });
            totalCapitalReturn = investmentAmount;
        // Update UI
        updateCalculatorUI(investmentAmount, totalInterest, totalCapitalReturn, monthlyBreakdown);
    }

    function updateCalculatorUI(investmentAmount, totalInterest, totalCapitalReturn, monthlyBreakdown) {
        // Update summary cards
        $('#calcTotalInterest').text(formatAmount(totalInterest));
        const totalReturn = totalInterest + totalCapitalReturn;
        $('#calcTotalReturn').text(formatAmount(totalReturn));

        // Calculate ROI
        const roi = (totalInterest / investmentAmount) * 100;
        $('#calcROI').text(roi.toFixed(2) + '%');

        // Update breakdown table
        let tableHTML = '';
        monthlyBreakdown.forEach(row => {
            // Estilo especial para la fila de retorno final de capital
            const rowClass = row.isFinalCapitalReturn ? 'fw-bold' : '';
            const monthLabel = row.isFinalCapitalReturn ? '@lang("Capital Return")' : row.month;

            tableHTML += `
                <tr class="${rowClass}">
                    <td>
                        ${monthLabel}
                        ${row.segment ? `<br><small class="">${row.segment}</small>` : ''}
                    </td>
                    <td class="text-end">${formatAmount(row.interest)}</td>
                    <td class="text-end">${formatAmount(row.capitalReturn)}</td>
                    <td class="text-end fw-bold">${formatAmount(row.total)}</td>
                    <td class="text-end text-success">${formatAmount(row.balance)}</td>
                </tr>
            `;
        });
        $('#calcBreakdownBody').html(tableHTML);

        // Update footer - Balance es solo los intereses acumulados
        $('#calcFooterInterest').text(formatAmount(totalInterest));
        $('#calcFooterCapital').text(formatAmount(totalCapitalReturn));
        $('#calcFooterTotal').text(formatAmount(totalReturn));
        $('#calcFooterBalance').text(formatAmount(totalInterest + totalCapitalReturn)); // Solo los intereses acumulados
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
        // Format number with 2 decimals and add currency symbol
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

    .plan-details-header {
        position: relative;
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 30px;
    }

    .plan-details-image img {
        width: 100%;
        height: 400px;
        object-fit: cover;
    }

    .plan-details-title-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
        padding: 30px;
    }

    .plan-details-title {
        color: white;
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
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
        .plan-details-title {
            font-size: 1.8rem;
        }

        .plan-details-image img {
            height: 250px;
        }

        .custom-tabs .nav-link {
            padding: 10px 15px;
            font-size: 14px;
        }

        .custom-tabs .nav-link i {
            display: none;
        }
    }
</style>
@endpush
