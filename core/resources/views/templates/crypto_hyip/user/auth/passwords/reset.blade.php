@extends('Template::layouts.frontend')

@section('content')
    <section class="my-120">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-7 col-xl-5">
                    <div class="card custom--card">
                        <div class="card-body">
                            <div class="mb-4">
                                <p>@lang('Your account is verified successfully. Now you can change your password. Please enter a strong password and don\'t share it with anyone.')</p>
                            </div>
                            <form method="POST" action="{{ route('user.password.update') }}">
                                @csrf
                                <input type="hidden" name="email" value="{{ $email }}">
                                <input type="hidden" name="token" value="{{ $token }}">
                                <div class="form-group">
                                    <label for="password" class="form--label">@lang('Password')</label>
                                    <div class="position-relative">
                                        <input type="password"
                                            class="form--control form-two @if (gs('secure_password')) secure-password @endif"
                                            name="password" required id="password" placeholder="@lang('Password')">
                                        <span class="password-show-hide fa-solid fa-eye-slash toggle-password"
                                            id="#password"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="password_confirmation" class="form--label">@lang('Confirm Password')</label>
                                    <div class="position-relative">
                                        <input type="password" class="form--control form-two" name="password_confirmation"
                                            required id="password_confirmation" placeholder="@lang('Confirm Password')">
                                        <div class="password-show-hide fa-solid fa-eye-slash toggle-password"
                                            id="#password_confirmation"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn--base w-100 "> @lang('Submit')</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
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
    </style>
@endpush
