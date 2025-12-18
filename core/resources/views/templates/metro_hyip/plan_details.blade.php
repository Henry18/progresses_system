@extends($activeTemplate . 'layouts.' . $layout)

@section('content')
<section class="pt-120 pb-120">
    <div class="container">
        <div class="row gy-4">
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
            <div class="col-lg-12">
                <div class="custom-tabs">
                    <ul class="nav nav-tabs" id="planDetailsTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="overview-tab" data-bs-toggle="tab"
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
                        {{-- Overview Tab --}}
                        <div class="tab-pane fade show active" id="overview" role="tabpanel">
                            <div class="plan-overview-content">
                                <div class="row gy-4">
                                    <div class="col-lg-8">
                                        <div class="card custom--card">
                                            <div class="card-header">
                                                <h5 class="card-title">@lang('Description')</h5>
                                            </div>
                                            <div class="card-body">
                                                <p class="plan-description">{{ __($plan->description) }}</p>
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
                                        <p class="text-muted mt-3">@lang('Comments section coming soon')</p>
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
        </div>
    </div>
</section>

{{-- Include Investment Modal --}}
@include($activeTemplate . 'partials.invest_modal', ['plans' => [$plan], 'gatewayCurrency' => $gatewayCurrency])

@endsection

@push('style')
<style>
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
