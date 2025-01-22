@extends($activeTemplate . 'layouts.frontend')

@section('content')
    <section class="account">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="my-4">
                        <h3>@lang('Complete Your Profile')</h3>
                        <p>@lang('You must complete your profile by providing the required information').</p>
                    </div>
                    <div class="account-wrapper">
                        <div class="account-wrapper-inner">
                            <div class="account-form">
                                <form method="POST" action="{{ route('user.data.submit') }}">
                                    @csrf
                                    <div class="row justify-content-center align-items-center">

                                        @if (!$user->email)
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label class="form--label">@lang('First Name')</label>
                                                    <input type="text" class="form--control" name="firstname" value="{{ old('firstname') }}" required>
                                                </div>
                                            </div>

                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label class="form--label">@lang('Last Name')</label>
                                                    <input type="text" class="form--control" name="lastname" value="{{ old('lastname') }}" required>
                                                </div>
                                            </div>

                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label for="email" class="form--label">@lang('Email Address')</label>
                                                    <input type="text" name="email" class="form--control checkUser" id="email" value="{{ old('email') }}" required>
                                                    <small class="text--danger emailExist"></small>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="username" class="form--label">@lang('Username')</label>
                                                <input type="text" name="username" class="form--control checkUser" id="username" value="{{ old('username') }}" required>
                                                <small class="text--danger usernameExist"></small>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="country" class="form--label">@lang('Country')</label>
                                                <select name="country" class="form--control" id="country" required>
                                                    @foreach ($countries as $key => $country)
                                                        <option data-mobile_code="{{ $country->dial_code }}" value="{{ $country->country }}" data-code="{{ $key }}">
                                                            {{ __($country->country) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="mobile" class="form--label">@lang('Mobile')</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg--base mobile-code">

                                                    </span>
                                                    <input type="hidden" name="mobile_code">
                                                    <input type="hidden" name="country_code">
                                                    <input type="number" name="mobile" value="{{ old('mobile') }}" class="form-control form--control checkUser" id="mobile" required>
                                                    <small class="text--danger mobileExist"></small>
                                                </div>
                                            </div>
                                        </div>



                                        <div class="form-group">
                                            <label class="form--label">@lang('Address')</label>
                                            <input type="text" class="form--control" name="address" value="{{ old('address') }}">
                                        </div>

                                        <div class="form-group">
                                            <label class="form--label">@lang('State')</label>
                                            <input type="text" class="form--control" name="state" value="{{ old('state') }}">
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="form--label">@lang('Zip Code')</label>
                                                <input type="text" class="form--control" name="zip" value="{{ old('zip') }}">
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group ">
                                                <label class="form--label">@lang('City')</label>
                                                <input type="text" class="form--control" name="city" value="{{ old('city') }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn--base w-100">
                                            @lang('Submit')
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="custom--modal modal fade" id="existModalCenter" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="custom--modal modal-header">
                    <h5 class="modal-title" id="existModalLongTitle">@lang('You are with us')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <h6 class="text-center">@lang('You already have an account please Login ')</h6>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--danger btn--sm pill" data-bs-dismiss="modal">@lang('Close')</button>
                    <a href="{{ route('user.logout') }}" class="btn btn--base btn--sm pill">@lang('Login')</a>
                </div>
            </div>
        </div>
    </div>
@endsection



@push('script')
    <script>
        "use strict";
        (function($) {

            @if ($mobileCode)
                $(`option[data-code={{ $mobileCode }}]`).attr('selected', '');
            @endif

            $('select[name=country]').on('change', function() {
                $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
                $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
                $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));
                var value = $('[name=mobile]').val();
                var name = 'mobile';
                checkUser(value, name);
            });

            $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
            $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
            $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));


            $('.checkUser').on('focusout', function(e) {
                var value = $(this).val();
                var name = $(this).attr('name')
                checkUser(value, name);
            });

            function checkUser(value, name) {
                var url = '{{ route('user.checkUser') }}';
                var token = '{{ csrf_token() }}';

                if (name == 'mobile') {
                    var mobile = `${value}`;
                    var data = {
                        mobile: mobile,
                        mobile_code: $('.mobile-code').text().substr(1),
                        _token: token
                    }
                }
                if (name == 'email') {
                    var data = {
                        email: value,
                        _token: token
                    }
                }
                if (name == 'username') {
                    var data = {
                        username: value,
                        _token: token
                    }
                }
                $.post(url, data, function(response) {
                    if (response.data != false) {
                        $(`.${response.type}Exist`).text(`${response.field} already exist`);
                    } else {
                        $(`.${response.type}Exist`).text('');
                    }
                });
            }
        })(jQuery);
    </script>
@endpush
