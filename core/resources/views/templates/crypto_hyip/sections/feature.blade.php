@php
    $featureElements = getContent('feature.element', orderById:true);
@endphp

<section class="feature-section py-120">
    <div class="container">
        <div class="feature-card-wrapper">
            @foreach($featureElements as $featureElement)
                <div class="feature-card wow fadeInUp" data-wow-delay="0.3s">
                    <div class="feature-card-content">
                        <span class="feature-card-icon">
                            <img src="{{ frontendImage('feature', @$featureElement->data_values->image, '35x35') }}" alt="@lang('image')">
                        </span>
                        <h5 class="feature-card-title">{{ __(@$featureElement->data_values->title) }}</h5>
                        <p class="feature-card-desc">
                            {{ __(@$featureElement->data_values->description) }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>