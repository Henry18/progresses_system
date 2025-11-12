@extends('Template::layouts.frontend')

@php
    $contact = getContent('contact.content', true);
    $contactElements = getContent('contact.element', orderById: true);
@endphp

@section('content')
    <section class="contact-page my-120">
        <div class="container">
            <div class="contact-top mb-120">
                <div class="row justify-content-center gy-4">
                    @foreach ($contactElements as $contactElement)
                        <div class="col-lg-4 col-sm-6">
                            <div class="contact-card wow fadeInUp" data-wow-delay="0.1s">
                                <div class="contact-card__icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="80" height="84" viewBox="0 0 80 84"
                                        fill="none">
                                        <path
                                            d="M70.6113 5.23897C78.611 11.6996 80.982 24.9776 79.652 36.9409C78.3026 48.923 73.2522 59.5905 65.2524 68.6053C57.2527 77.62 46.3036 84.9821 36.4726 83.8928C26.6223 82.8223 17.9093 73.2817 11.2204 64.2669C4.55073 55.2521 -0.0949104 46.7632 0.00147211 38.3495C0.0978546 29.9545 4.95553 21.6534 11.6252 15.2116C18.3141 8.75097 26.8344 4.16847 37.9183 1.67063C48.9831 -0.808435 62.6115 -1.20283 70.6113 5.23897Z"
                                            fill="url(#paint0_linear_17765_594)" />

                                        <defs>
                                            <linearGradient id="paint0_linear_17765_594" x1="40.004" y1="83.9919"
                                                x2="40.004" y2="-0.00196252" gradientUnits="userSpaceOnUse">
                                                <stop stop-color="hsl(var(--base-d-300))" />
                                                <stop offset="1" stop-color="hsl(var(--base-l-100))" />
                                            </linearGradient>
                                        </defs>
                                    </svg>
                                    @php echo @$contactElement->data_values->icon; @endphp
                                </div>
                                <div class="contact-card__content">
                                    <h4 class="contact-card__title">{{ __(@$contactElement->data_values->title) }}</h4>
                                    <p class="contact-card__desc">{{ __(@$contactElement->data_values->content) }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="contact-wrapper">
                <div class="row align-items-center">
                    <div class="col-xl-5 col-lg-6 d-none d-lg-block">
                        <div class="contact-thumb wow fadeInLeft" data-wow-delay="0.1s">
                            <img src="{{ frontendImage('contact', @$contact->data_values->image, '495x550') }}"
                                alt="@lang('contact-thumb')">
                        </div>
                    </div>
                    <div class="col-xl-7 col-lg-6">
                        <div class="contact-form wow fadeInRight" data-wow-delay="0.1s">
                            <p class="contact-form__subtitle">{{ __(@$contact->data_values->heading) }}</p>
                            <h4 class="contact-form__title">{{ __(@$contact->data_values->subheading) }}</h4>

                            <form action="{{ route('contact') }}" method="post" class="verify-gcaptcha">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label class="form--label">@lang('Name') </label>
                                        <input type="text" name="name" class="form--control"
                                            value="{{ old('name', @$user->fullname) }}"
                                            @if ($user && $user->profile_complete) readonly @endif required
                                            placeholder="@lang('Full Name')">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="form--label">@lang('Email') </label>
                                        <input type="email" name="email" class="form--control"
                                            value="{{ old('email', @$user->email) }}"
                                            @if ($user) readonly @endif required
                                            placeholder="@lang('Email Address')">
                                    </div>
                                    <div class="col-md-12 form-group">
                                        <label class="form--label">@lang('Subject') </label>
                                        <input name="subject" class="form--control" required
                                            placeholder="@lang('Subject')">
                                    </div>
                                    <div class="col-md-12 form-group">
                                        <label class="form--label">@lang('Subject') </label>
                                        <textarea cols="30" rows="10" class="form--control" name="message" required placeholder="@lang('Write message...')"></textarea>
                                    </div>
                                    <div class="col-md-12">
                                        <x-captcha />
                                    </div>
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn--gr w-100">@lang('Submit')</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if (@$sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @include('Template::sections.' . $sec)
        @endforeach
    @endif
@endsection
