@extends('Template::layouts.frontend')

@php
    $login = getContent('login.content', true);
@endphp

@section('content')
    <section class="account-page my-120">
        <div class="container">
            <div class="account">
                <div class="account-thumb wow fadeInLeft" data-wow-delay="0.1s">
                    <img class="fit-image" src="{{ frontendImage('login', @$login->data_values->image, '600x715') }}"
                        alt="@lang('account-thumb')">
                </div>
                <div class="account-form wow fadeInRight" data-wow-delay="0.1s">
                    <h3 class="account-form__title">{{ __(@$login->data_values->heading) }}</h3>
                    <p class="account-form__desc">
                        @lang('Don\'t have an account?') <a href="{{ route('user.register') }}">@lang('Create Account')</a>
                    </p>

                    <form method="POST" action="{{ route('user.login') }}" class="verify-gcaptcha">
                        @csrf
                        <div class="form-group">
                            <label for="username" class="form--label"> @lang('Username') </label>
                            <input type="text" name="username" value="{{ old('username') }}" class="form--control"
                                required placeholder="@lang('Username')">
                        </div>
                        <div class="form-group">
                            <label for="password" class="form--label">@lang('Password')</label>
                            <div class="position-relative">
                                <input id="password" name="password" type="password" class="form--control"
                                    placeholder="@lang('Password')" required>
                                <span class="password-show-hide fa-solid fa-eye-slash toggle-password"
                                    id="#password"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="flex-between mb-4 gap-2">
                                <div class="form--check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                        {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label fw-normal" for="remember">@lang('Remember Me')</label>
                                </div>
                                <p class="fs-14 fw-bold">
                                    <a href="{{ route('user.password.request') }}" class="text--base">@lang('Forgot password?')</a>
                                </p>
                            </div>
                        </div>

                        <x-captcha />

                        <button type="submit" class="btn btn--gr w-100">@lang('Login')</button>

                        @include('Template::partials.social_login')
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
