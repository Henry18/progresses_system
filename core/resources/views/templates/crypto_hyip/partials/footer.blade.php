@php
    $policies = getContent('policy_pages.element', orderById:true);
    $footer = getContent('footer.content', true);
    $socialElements = getContent('social_icon.element', orderById:false);
    $contactElements = getContent('contact.element', orderById:true);
@endphp

<!-- ==================== Footer Start Here ==================== -->
<footer class="footer-area">
    <div class="footer-area__gradient"></div>
    <div class="footer-area__thumb">
        <img src="{{ frontendImage('footer', @$footer->data_values->right_image, '890x735') }}" alt="@lang('footer-thumb')">
    </div>
    <div class="footer-area__coin">
        <img src="{{ frontendImage('footer', @$footer->data_values->left_image, '190x350') }}" alt="@lang('footer-thumb')">
    </div>
    <div class="footer-top mb-120">
        <div class="container">
            <a href="{{ route('home') }}" class="footer-logo">
                <img src="{{ siteLogo() }}" alt="logo">
            </a>
            <div class="row gy-4 justify-content-between">
                <div class="col-xsm-6 col-sm-6 col-lg-4">
                    <div class="footer-item">
                        <h5 class="footer-item__title"> @lang('About') {{ gs('site_name') }} </h5>
                        <p class="footer-item__desc mb-4">
                            {{ __(@$footer->data_values->description) }}
                        </p>
                        <ul class="social-list">
                            @foreach ($socialElements as $socialElement)
                                <li class="social-list__item">
                                    <a class="social-list__link flex-center" href="{{ @$socialElement->data_values->url }}" target="_blank">
                                        @php
                                            echo @$socialElement->data_values->icon;
                                        @endphp
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-xsm-6 col-sm-6 col-lg-2">
                    <div class="footer-item">
                        <h5 class="footer-item__title">@lang('Useful Link')</h5>
                        <ul class="footer-menu">
                            <li class="footer-menu__item">
                                <a href="{{ route('home') }}" class="footer-menu__link">@lang('Home')</a>
                            </li>
                            <li class="footer-menu__item">
                                <a href="{{ route('blogs') }}" class="footer-menu__link">@lang('Blog')</a>
                            </li>
                            <li class="footer-menu__item">
                                <a href="{{ route('contact') }}" class="footer-menu__link">@lang('Contact Us')</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-xsm-6 col-sm-6 col-lg-2">
                    <div class="footer-item">
                        <h5 class="footer-item__title">@lang('Policy Pages')</h5>
                        <ul class="footer-menu">
                            <li class="footer-menu__item">
                                <a href="{{ route('cookie.policy') }}" class="footer-menu__link">@lang('Cookie Policy')</a>
                            </li>
                            @foreach ($policies as $policy)
                                <li class="footer-menu__item">
                                    <a href="{{ route('policy.pages', $policy->slug) }}" class="footer-menu__link">{{ __($policy->data_values->title) }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-xsm-6 col-sm-6 col-lg-3">
                    <div class="footer-item">
                        <h5 class="footer-item__title">@lang('Contact With Us')</h5>
                        <div class="footer-contact">
                             @foreach($contactElements as $contactElement)
                                <div class="footer-contact__info">
                                    <span class="icon">@php echo @$contactElement->data_values->icon; @endphp</span>
                                    <span class="text">{{ __(@$contactElement->data_values->content) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ==================== Footer Bottom Start Here ==================== -->
    <div class="bottom-footer">
        <div class="container">
            <div class="bottom-footer-text py-3">&copy; @lang('Copyright') {{ date('Y') }}. @lang('All Rights Reserved').</div>
        </div>
    </div>
</footer>
<!-- ==================== Footer End Here ==================== -->