@php
    $planCaption = getContent('plan.content', true);
    $plans = App\Models\Plan::with('timeSetting')
        ->whereHas('timeSetting', function ($time) {
            $time->where('status', 1);
        })
        ->where('status', 1)
        ->where('featured', 1)
        ->get();

    $gatewayCurrency = null;
    if (auth()->check()) {
        $gatewayCurrency = App\Models\GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', 1);
        })
            ->with('method')
            ->orderby('method_code')
            ->get();
    }
@endphp

<section class="plan-section my-120">
    <div class="container">
        <div class="section-heading">
            <h1 class="section-heading__title wow fadeInUp" data-wow-delay="0.1s">
                {{ __(@$planCaption->data_values->heading) }}
            </h1>
            <p class="section-heading__desc wow fadeInUp" data-wow-delay="0.2s">
                {{ __(@$planCaption->data_values->subheading) }}
            </p>
        </div>

        <div class="row gy-4 justify-content-center">
            @include('Template::partials.plan', ['plans' => $plans])
        </div>
    </div>
</section>
