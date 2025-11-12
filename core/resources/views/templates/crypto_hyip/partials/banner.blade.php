@php
    $banner = getContent('banner.content', true);
@endphp

<!--========================== Banner Section Start ==========================-->
<section class="banner-section">
    <div class="banner-group">
        <div class="banner-group-one"></div>
        <div class="banner-group-two"></div>
        <div class="banner-group-three"></div>
    </div>
    <div class="banner-group style-bottom">
        <div class="banner-group-one"></div>
        <div class="banner-group-two"></div>
        <div class="banner-group-three"></div>
    </div>
    <div class="banner-group style-right">
        <div class="banner-group-one"></div>
        <div class="banner-group-two"></div>
        <div class="banner-group-three"></div>
    </div>

    <div class="banner-shape-one">
        <img src="{{ frontendImage('banner', @$banner->data_values->shape_one, '255x135') }}" alt="@lang('image')">
    </div>
    <div class="banner-shape-two">
        <img src="{{ frontendImage('banner', @$banner->data_values->shape_two, '240x185') }}" alt="@lang('image')">
    </div>

    <div class="banner-coin-one banner-coin">
        <img src="{{ frontendImage('banner', @$banner->data_values->coin_one, '70x70') }}" alt="@lang('image')">
    </div>
    <div class="banner-coin-two banner-coin">
        <img src="{{ frontendImage('banner', @$banner->data_values->coin_two, '70x70') }}" alt="@lang('image')">
    </div>
    <div class="banner-coin-three banner-coin">
        <img src="{{ frontendImage('banner', @$banner->data_values->coin_three, '65x65') }}" alt="@lang('image')">
    </div>
    <div class="banner-coin-four banner-coin">
        <img src="{{ frontendImage('banner', @$banner->data_values->coin_four, '70x70') }}" alt="@lang('image')">
    </div>

    <div class="container-fluid p-0">
        <div class="banner-content">
            <h1 class="banner-content__title wow fadeInUp" data-wow-delay="0.1s">
                {{ __(@$banner->data_values->heading) }}
            </h1>
            <p class="banner-content__desc wow fadeInUp" data-wow-delay="0.2s">
                {{ __(@$banner->data_values->subheading) }}
            </p>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="{{ url(@$banner->data_values->button_url_one) }}" class="btn--gr btn flex-inline wow fadeInUp" data-wow-delay="0.3s">
                    {{ __(@$banner->data_values->button_text_one) }}
                    <span class="icon d-flex ms-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                            viewBox="0 0 19 19" fill="none">
                            <path
                                d="M18.8371 9.07696L13.1374 2.74397C13.0158 2.60971 12.8461 2.53371 12.6662 2.53371H8.86643C8.61691 2.53371 8.39019 2.68064 8.2876 2.90863C8.18627 3.13788 8.22807 3.40513 8.39526 3.59006L13.7137 9.5L8.39526 15.4087C8.22807 15.5949 8.185 15.8621 8.2876 16.0901C8.39019 16.3194 8.61691 16.4663 8.86643 16.4663H12.6662C12.8461 16.4663 13.0158 16.389 13.1374 16.2573L18.8371 9.92431C19.0537 9.68366 19.0537 9.31635 18.8371 9.07696Z"
                                fill="currentColor" />
                            <path
                                d="M10.6047 9.07696L4.90498 2.74397C4.78339 2.60971 4.61366 2.53371 4.43381 2.53371H0.634011C0.384491 2.53371 0.15777 2.68064 0.0551759 2.90863C-0.046152 3.13788 -0.00435422 3.40513 0.162837 3.59006L5.48128 9.5L0.162837 15.4087C-0.00435422 15.5949 -0.0474186 15.8621 0.0551759 16.0901C0.15777 16.3194 0.384491 16.4663 0.634011 16.4663H4.43381C4.61366 16.4663 4.78339 16.389 4.90498 16.2573L10.6047 9.92431C10.8213 9.68366 10.8213 9.31635 10.6047 9.07696Z"
                                fill="currentColor" />
                        </svg>
                    </span>
                </a>
                <a href="{{ url(@$banner->data_values->button_url_two) }}" class="btn--gr btn flex-inline wow fadeInUp" data-wow-delay="0.3s">
                    {{ __(@$banner->data_values->button_text_two) }}
                    <span class="icon d-flex ms-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                            viewBox="0 0 19 19" fill="none">
                            <path
                                d="M18.8371 9.07696L13.1374 2.74397C13.0158 2.60971 12.8461 2.53371 12.6662 2.53371H8.86643C8.61691 2.53371 8.39019 2.68064 8.2876 2.90863C8.18627 3.13788 8.22807 3.40513 8.39526 3.59006L13.7137 9.5L8.39526 15.4087C8.22807 15.5949 8.185 15.8621 8.2876 16.0901C8.39019 16.3194 8.61691 16.4663 8.86643 16.4663H12.6662C12.8461 16.4663 13.0158 16.389 13.1374 16.2573L18.8371 9.92431C19.0537 9.68366 19.0537 9.31635 18.8371 9.07696Z"
                                fill="currentColor" />
                            <path
                                d="M10.6047 9.07696L4.90498 2.74397C4.78339 2.60971 4.61366 2.53371 4.43381 2.53371H0.634011C0.384491 2.53371 0.15777 2.68064 0.0551759 2.90863C-0.046152 3.13788 -0.00435422 3.40513 0.162837 3.59006L5.48128 9.5L0.162837 15.4087C-0.00435422 15.5949 -0.0474186 15.8621 0.0551759 16.0901C0.15777 16.3194 0.384491 16.4663 0.634011 16.4663H4.43381C4.61366 16.4663 4.78339 16.389 4.90498 16.2573L10.6047 9.92431C10.8213 9.68366 10.8213 9.31635 10.6047 9.07696Z"
                                fill="currentColor" />
                        </svg>
                    </span>
                </a>      
            </div>
        </div>
        <div class="banner-image">
            <img class="fit-image wow fadeInUp" data-wow-delay="0.4s" src="{{ frontendImage('banner', @$banner->data_values->background_image, '1905x470') }}" alt="@lang('image')">
        </div>
    </div>
</section>
<!--========================== Banner Section End ==========================-->