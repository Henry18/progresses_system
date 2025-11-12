@php
    $cta = getContent('cta.content', true);
@endphp

<section class="cta-section">
    <div class="container">
        <div class="row gy-4 justify-content-between">
            <div class="col-lg-6">
                <div class="cta-content wow fadeInUp" data-wow-delay="0.1s">
                    <h1 class="cta-content-title" data-break="-1">{{ __(@$cta->data_values->heading) }}</h1>
                    <p class="cta-content-text wow fadeInUp" data-wow-delay="0.2s">
                        {{ __(@$cta->data_values->subheading) }}
                    </p>
                    <h1 class="cta-content-count wow fadeInUp" data-wow-delay="0.3s">0.1%</h1>

                    <a href="{{ $cta->data_values->button_url }}" class="btn--gr btn flex-inline wow fadeInUp" data-wow-delay="0.3s">
                        {{ __($cta->data_values->button_text) }}
                        <span class="icon d-flex ms-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                                <path d="M18.8371 9.07696L13.1374 2.74397C13.0158 2.60971 12.8461 2.53371 12.6662 2.53371H8.86643C8.61691 2.53371 8.39019 2.68064 8.2876 2.90863C8.18627 3.13788 8.22807 3.40513 8.39526 3.59006L13.7137 9.5L8.39526 15.4087C8.22807 15.5949 8.185 15.8621 8.2876 16.0901C8.39019 16.3194 8.61691 16.4663 8.86643 16.4663H12.6662C12.8461 16.4663 13.0158 16.389 13.1374 16.2573L18.8371 9.92431C19.0537 9.68366 19.0537 9.31635 18.8371 9.07696Z" fill="currentColor" />
                                <path d="M10.6047 9.07696L4.90498 2.74397C4.78339 2.60971 4.61366 2.53371 4.43381 2.53371H0.634011C0.384491 2.53371 0.15777 2.68064 0.0551759 2.90863C-0.046152 3.13788 -0.00435422 3.40513 0.162837 3.59006L5.48128 9.5L0.162837 15.4087C-0.00435422 15.5949 -0.0474186 15.8621 0.0551759 16.0901C0.15777 16.3194 0.384491 16.4663 0.634011 16.4663H4.43381C4.61366 16.4663 4.78339 16.389 4.90498 16.2573L10.6047 9.92431C10.8213 9.68366 10.8213 9.31635 10.6047 9.07696Z" fill="currentColor" />
                            </svg>
                        </span>
                    </a>
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-block">
                <div class="cta-thumb wow fadeInUp" data-wow-delay="0.5s">
                    <div class="cta-thumb-shape">
                        <img src="{{ frontendImage('cta', @$cta->data_values->shape, '240x195') }}" alt="image">
                    </div>
                    <img src="{{ frontendImage('cta', @$cta->data_values->image, '480x540') }}" class="fit-image" alt="image">
                </div>
            </div>
        </div>
    </div>
</section>
