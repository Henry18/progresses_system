@php
    $testimonial = getContent('testimonial.content', true);
    $testimonialElements = getContent('testimonial.element', orderById:true);
@endphp

<!--========================== Testimonial Section Start ==========================-->
<section class="testimonial my-120">
    <div class=" container">
        <div class="section-heading">
            <h1 class="section-heading__title wow fadeInUp" data-wow-delay="0.1s">{{ __(@$testimonial->data_values->heading) }}</h1>
            <p class="section-heading__desc wow fadeInUp" data-wow-delay="0.2s">{{ __(@$testimonial->data_values->subheading) }}</p>
        </div>
        <div class="testimonial-slider wow fadeInUp" data-wow-delay="0.3s">
            @foreach($testimonialElements as $testimonialElement)
                <div class="testimonial-item">
                    <div class="testimonial-item-thumb">
                        <img class="fit-image" src="{{ frontendImage('testimonial', @$testimonialElement->data_values->image, '100x120') }}" alt="@lang('testimonial')">
                    </div>
                    <div class="testimonial-item-content">
                        <span class="testimonial-item-quote">
                            <svg xmlns="http://www.w3.org/2000/svg" width="46" height="34"
                                viewBox="0 0 46 34" fill="none">
                                <path
                                    d="M8.87072 15.9931L2.80639 15.9794L2.84107 0.58576L21.4218 0.627621L21.398 11.1696C21.3739 21.8713 13.8862 31.1257 3.4137 33.3296C2.47577 33.527 1.91224 33.5837 1.91224 33.5837L0.038533 28.9893C8.37149 24.8579 8.87072 15.9931 8.87072 15.9931Z"
                                    fill="hsl(var(--white))" fill-opacity="0.2" />
                                <path
                                    d="M33.0306 16.0472L26.9662 16.0336L27.0009 0.639954L45.5839 0.68182L45.5601 11.2238C45.536 21.9255 38.0477 31.1799 27.5752 33.3838C26.6373 33.5812 26.0744 33.6379 26.0744 33.6379L24.2006 29.0435C32.5308 24.9121 33.0306 16.0472 33.0306 16.0472Z"
                                    fill="hsl(var(--white))" fill-opacity="0.2" />
                            </svg>
                        </span>
                        <p class="testimonial-item-desc">
                            {{ __(@$testimonialElement->data_values->quote) }}
                        </p>
                        <div class="flex-between gap-3">
                            <div class="testimonial-item-auth">
                                <h5 class="name">{{ __(@$testimonialElement->data_values->name) }}</h5>
                                <p class="address">{{ __(@$testimonialElement->data_values->designation) }}</p>
                            </div>

                            <div class="testimonial-item-rating">
                                <span class="icon"><i class="fa-solid fa-star"></i></span>
                                <span class="value">{{ @$testimonialElement->data_values->rating }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
<!--========================== Testimonials Section End ==========================-->