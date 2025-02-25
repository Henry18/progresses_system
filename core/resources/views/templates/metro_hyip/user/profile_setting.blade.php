@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="mt-60">
            <div class="row">
                <div class="col-lg-4 mb-30">
                    <div class="card custom--card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h4 class="">{{ $user->fullname }}</h4>
                                @if ($user->userRanking && gs('user_ranking'))
                                    <img class="ranking-user" src="{{ getImage(getFilePath('userRanking') . '/' . $user->userRanking->icon, getFileSize('userRanking')) }}" alt="">
                                @endif
                            </div>
                            <ul class="list-group">

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="las la-user base--color"></i> @lang('DNI')</span> <span class="fw-bold">{{ $user->dni }}</span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="las la-user base--color"></i> @lang('Username')</span> <span class="fw-bold">{{ $user->username }}</span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="las la-envelope base--color"></i> @lang('Email')</span> <span class="fw-bold">{{ $user->email }}</span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="las la-phone base--color"></i> @lang('Mobile')</span> <span class="fw-bold">{{ $user->mobile }}</span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="las la-globe base--color"></i> @lang('Country')</span> <span class="fw-bold">{{ $user->country_name }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="card custom--card">
                        <div class="card-body">
                            <form class="register" method="post">
                                @csrf
                                <div class="row">
                                    @if (!$user->firstname)
                                    <div class="form-group col-sm-6">
                                        <label class="form--label">@lang('First Name')</label>
                                        <input type="text" class="form--control" name="firstname" value="{{ $user->firstname }}" required>
                                    </div>
                                    @else
                                        <span><i class="las la-user base--color"></i> @lang('First Name')</span> <span class="fw-bold">{{ $user->firstname }}</span>
                                    @endif
                                    @if (!$user->lastname)
                                    <div class="form-group col-sm-6">
                                        <label class="form--label">@lang('Last Name')</label>
                                        <input type="text" class="form--control" name="lastname" value="{{ $user->lastname }}" required>
                                    </div>
                                    @else
                                        <span><i class="las la-user base--color"></i> @lang('Last Name')</span> <span class="fw-bold">{{ $user->lastname }}</span>
                                    @endif
                                    @if (!$user->dni)
                                    <div class="form-group col-sm-6">
                                        <label class="form--label">@lang('DNI')</label>
                                        <input type="text" class="form--control" name="dni" value="{{ $user->dni }}" required>
                                    </div>
                                    @else
                                        <span><i class="las la-user base--color"></i> @lang('DNI')</span> <span class="fw-bold">{{ $user->dni }}</span>
                                    @endif
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="country" class="form--label">@lang('Country')</label>
                                            <select name="country" class="form--control" id="country" required>
                                                @foreach ($countries as $key => $country)
                                                    <option @selected($user->country_code == $key) data-mobile_code="{{ $country->dial_code }}" value="{{ $country->country }}" data-code="{{ $key }}">
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
                                                <input type="number" name="mobile" value="{{ $user->mobile }}" class="form-control form--control checkUser" id="mobile" required>
                                                <small class="text--danger mobileExist"></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-sm-6">
                                        <label class="form--label">@lang('Address')</label>
                                        <input type="text" class="form--control" name="address" value="{{ @$user->address }}" required>
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label class="form--label">@lang('State')</label>
                                        <input type="text" class="form--control" name="state" value="{{ @$user->state }}">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-sm-6">
                                        <label class="form--label">@lang('Zip Code')</label>
                                        <input type="text" class="form--control" name="zip" value="{{ @$user->zip }}">
                                    </div>

                                    <div class="form-group col-sm-6">
                                        <label class="form--label">@lang('City')</label>
                                        <input type="text" class="form--control" name="city" value="{{ @$user->city }}">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
    </div>
@endsection


@push('script')
    <script>
        "use strict";
        (function($) {

            @if ($user->dial_code)
                $(`option[data-code={{ $user->dial_code }}]`).attr('selected', '');
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
