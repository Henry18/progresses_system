<link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/frontend-lite.min.css') }}">
<link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/wfeature.min.css') }}">
<link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/post-1717.css') }}">

@php
    $custom_codeContent = getContent('custom_code.content', true);
    $planList = \App\Models\Plan::with('timeSetting')
        ->whereHas('timeSetting', function ($time) {
            $time->where('status', 1);
        })
        ->where('status', 1)
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
<section class="elementor-section elementor-top-section elementor-element elementor-element-27de702 elementor-section-boxed elementor-section-height-default elementor-section-height-default">
    @php
    echo $custom_codeContent->data_values->heading;
    @endphp
</section>