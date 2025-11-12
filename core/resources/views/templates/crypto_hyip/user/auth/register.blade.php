@extends('Template::layouts.frontend')

@php
    $register = getContent('registration.content', true);
    $policyPages = getContent('policy_pages.element', orderById: true);
@endphp

@section('content')
    <section class="account-page my-120">
        <div class="container">
            <div class="account">
                <div class="account-thumb wow fadeInLeft" data-wow-delay="0.1s">
                    <img class="fit-image"
                        src="{{ frontendImage('registration', @$register->data_values->image, '600x715') }}"
                        alt="account-thumb">
                </div>

                <div class="account-form wow fadeInRight" data-wow-delay="0.1s">
                    <h3 class="account-form__title">{{ __(@$register->data_values->heading) }}</h3>
                    <p class="account-form__desc">
                        @lang('Already have an account?') <a href="{{ route('user.login') }}" class="text--base">@lang('Login')</a>
                    </p>

                    <form action="{{ route('user.register') }}" method="POST" class="verify-gcaptcha">
                        @csrf
                        <div class="row">

                            @if (session()->get('reference') != null)
                                <div class="col-md-12 form-group">
                                    <p>@lang('You\'re referred by') <i class="fw-bold text--base">{{ session()->get('reference') }}</i>
                                    </p>
                                </div>
                            @endif

                            <div class="col-xl-6 form-group">
                                <label for="firstname" class="form--label"> @lang('First Name') </label>
                                <input type="text" class="form--control" name="firstname" value="{{ old('firstname') }}"
                                    required placeholder="@lang('First Name')" id="firstname">
                            </div>
                            <div class="col-xl-6 form-group">
                                <label for="lastname" class="form--label"> @lang('Last Name') </label>
                                <input type="text" class="form--control" name="lastname" value="{{ old('lastname') }}"
                                    required placeholder="@lang('Last Name')" id="lastname">
                            </div>
                            <div class="col-12 form-group">
                                <label for="email" class="form--label"> @lang('E-Mail Address') </label>
                                <input type="email" class="form--control checkUser" name="email"
                                    value="{{ old('email') }}" required placeholder="@lang('Email')" id="email">
                            </div>
                            <div class="col-xl-6 form-group">
                                <label for="password" class="form--label">@lang('Password')</label>
                                <div class="position-relative">
                                    <input type="password"
                                        class="form--control @if (gs('secure_password')) secure-password @endif"
                                        name="password" required id="password" placeholder="@lang('Password')">
                                    <span class="password-show-hide fa-solid fa-eye-slash toggle-password"
                                        id="#password"></span>
                                </div>
                            </div>
                            <div class="col-xl-6 form-group">
                                <label for="password_confirmation" class="form--label">@lang('Confirm Password')</label>
                                <div class="position-relative">
                                    <input type="password" class="form--control" name="password_confirmation" required
                                        id="password_confirmation" placeholder="@lang('Confirm Password')">
                                    <div class="password-show-hide fa-solid fa-eye-slash toggle-password"
                                        id="#password_confirmation"></div>
                                </div>
                            </div>

                            <x-captcha />

                            @if (gs('agree'))
                                <div class="flex-between mb-4 gap-2">
                                    <div class="form--check">
                                        <input class="form-check-input" type="checkbox" @checked(old('agree'))
                                            name="agree" id="agree">
                                        <label class="form-check-label fw-normal" for="agree">
                                            @lang('I agree with')
                                            @foreach ($policyPages as $policy)
                                                <a href="{{ route('policy.pages', $policy->slug) }}"
                                                    target="_blank">{{ __($policy->data_values->title) }}</a>
                                                @if (!$loop->last)
                                                    ,
                                                @endif
                                            @endforeach
                                        </label>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <button type="submit" class="btn btn--gr w-100">@lang('Register')</button>

                        @include('Template::partials.social_login')
                    </form>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="existModalCenter" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="existModalLongTitle">@lang('You are with us')</h6>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <h6 class="text-center">@lang('You already have an account please Login ')</h6>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Close')</button>
                    <a href="{{ route('user.login') }}" class="btn btn--base">@lang('Login')</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@if (gs('secure_password'))
    @push('script-lib')
        <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
    @endpush
@endif

@push('style')
    <style>
        .hover-input-popup .input-popup {
            bottom: calc(100% + 6px);
        }

        @media screen and (max-width: 575px) {
            #existModalCenter .modal-body h6 {
                font-size: 0.75rem;
            }
        }
    </style>
@endpush

@push('script')
    <script>
        "use strict";
        (function($) {

            $('.checkUser').on('focusout', function(e) {
                var url = '{{ route('user.checkUser') }}';
                var value = $(this).val();
                var token = '{{ csrf_token() }}';

                var data = {
                    email: value,
                    _token: token
                }

                $.post(url, data, function(response) {
                    if (response.data != false) {
                        $('#existModalCenter').modal('show');
                    }
                });
            });
        })(jQuery);
    </script>
@endpush
