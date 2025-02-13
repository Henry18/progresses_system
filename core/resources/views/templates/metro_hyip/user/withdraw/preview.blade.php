@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="mt-60">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card custom--card">
                    <div class="card-body">
                        <div class="alert alert-box">
                            <div class="alert-icon">
                                <i class="las la-info-circle"></i>
                            </div>
                            <p class="mb-0 alert-box__content"> @lang('You are requesting') <b class="text--success">{{ showAmount($withdraw->amount)  }}</b> @lang('for withdraw.') @lang('The admin will send you')
                                <b class="text--success">{{showAmount($withdraw->final_amount,currencyFormat:false) .' '.$withdraw->currency }} </b> @lang('to your account.')</p>
                        </div>
                        <form action="{{ route('user.withdraw.submit') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-2">
                                @php
                                    echo $withdraw->method->description;
                                @endphp
                            </div>
                            <x-viser-form identifier="id" identifierValue="{{ $withdraw->method->form_id }}" />
                            @if (auth()->user()->ts)
                                <div class="form-group">
                                    <label class="form--label">@lang('Google Authenticator Code')</label>
                                    <input type="text" name="authenticator_code" class="form--control" required>
                                </div>
                            @endif
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
