@extends($activeTemplate . 'layouts.app')
@section('panel')
    @php
        $authContent = getContent('authentication.content', true);
    @endphp
    <!-- Account Section -->
    <section class="account-section position-relative">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-6 col-lg-7 col-md-8">
                    <a href="{{ route('home') }}" class="text-center d-block mb-3 mb-sm-4 auth-page-logo"><img src="{{ siteLogo('dark') }}" alt="logo"></a>
                    <form action="{{ route('user.login') }}" method="POST" class="verify-gcaptcha account-form">
                        @csrf
                        <div class="mb-4">
                            <h4 class="mb-2">{{ __(@$authContent->data_values->login_title) }}</h4>
                            <p>{{ __(@$authContent->data_values->login_subtitle) }}</p>

                            @include($activeTemplate.'partials.social_login')
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>@lang('Username or Email')</label>
                                    <input type="text" name="username" class="form-control form--control h-45">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>@lang('Password')</label>
                                    <input type="password" name="password" class="form-control form--control h-45">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex flex-wrap gap-2 justify-content-between">
                                    <div class="form-group custom--checkbox">
                                        <input type="checkbox" id="remember" name="remember" class="form-check-input">
                                        <label for="remember">@lang('Keep me Logged in')</label>
                                    </div>
                                    <a href="{{ route('user.password.request') }}" class="text--base fw-bold">@lang('Forgot Password?')</a>
                                </div>
                            </div>
                            <div class="col-12">
                                <x-captcha />
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn--base w-100">@lang('Login Account')</button>
                            </div>
                            <div class="col-12 mt-4">
                                <p class="text-center">@lang('Don\'t have any account?') <a href="{{ route('user.register') }}" class="fw-bold text--base">@lang('Create Account')</a></p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <!-- Account Section -->
@endsection
