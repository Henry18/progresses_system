@extends($activeTemplate . 'layouts.master')

@section('content')
    <section class="pt-4 pb-60">
        <div class="row justify-content-center gy-4">
            @auth
                <div class="col-md-12">
                    <div class="text-end">
                        <a href="{{ route('user.pool.invests') }}" class="btn btn--base">
                            @lang('My Pool')
                        </a>
                    </div>
                </div>
            @endauth

            @foreach ($pools as $pool)
                <div class="col-lg-3 col-sm-6 col-xsm-6">
                    <div class="plan-item">
                        <div class="plan-item__header">
                            <p class="return-title">
                                @lang('Interest Range')</p>
                            <span class="plan-item__title">{{ $pool->interest_range }}</span>
                            <h4 class="plan-item__name"> {{ __($pool->name) }} </h4>
                        </div>
                        <div class="plan-item__body">
                            <div class="plan-item__info">
                                <h5 class="plan-item__time">
                                    @lang('Invested') {{ showAmount($pool->invested_amount) }}
                                </h5>
                                <div>
                                    @lang('Total') {{ showAmount($pool->amount) }}
                                </div>
                            </div>
                            <ul class="plan-item__list">
                                <li class="plan-item__list-inner">@lang('Invest till') {{ showDateTime($pool->start_date) }}</li>
                                <li class="plan-item__list-inner">@lang('Return Date') {{ showDateTime($pool->end_date) }}</li>
                                <li class="plan-item__list-inner">
                                    <span class="remaining-amount">{{ showAmount($pool->invested_amount) }}/{{ showAmount($pool->amount) }}</span>
                                            <div class="progress mt-1">
                                                <div class="progress-bar customWidth" data-invested="{{ getAmount(($pool->invested_amount / $pool->amount) * 100) }}" role="progressbar" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                </li>
                            </ul>
                            <a href="javascript:void(0)" data-bs-toggle="modal" data-pool_id="{{ $pool->id }}" data-pool_name="{{ __($pool->name) }}"  data-bs-target="#poolInvestModal" class="btn btn--base pill outline poolInvestNow" >@lang('Invest Now')</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <div class="modal custom--modal fade" id="poolInvestModal">
        <div class="modal-dialog modal-dialog-centered modal-content-bg">
            <div class="modal-content">
                <div class="modal-header">
                    <strong class="modal-title text-white" id="ModalLabel">
                        @lang('Confirm to invest on') <span class="planName"></span>
                    </strong>

                    <button type="button" data-bs-dismiss="modal">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('user.pool.invest') }}" method="post">
                    @csrf
                    <input type="hidden" name="pool_id">
                        <div class="modal-body">
                            <div class="form-group mb-3">
                                <label>@lang('Pay Via')</label>
                                <select class="form--control" name="wallet_type" required>
                                    <option value="">@lang('Select One')</option>
                                    @if (auth()->user()->deposit_wallet > 0)
                                        <option value="deposit_wallet">@lang('Deposit Wallet - ' . showAmount(auth()->user()->deposit_wallet))</option>
                                    @endif
                                    @if (auth()->user()->interest_wallet > 0)
                                        <option value="interest_wallet">@lang('Interest Wallet -' . showAmount(auth()->user()->interest_wallet))</option>
                                    @endif
                                </select>
                            </div>

                            <div class="form-group">
                                <label>@lang('Invest Amount')</label>
                                <div class="input-group">
                                    <input type="number" step="any" min="0" class="form-control form--control" name="amount" required>
                                    <div class="input-group-text bg--base">{{ gs('cur_text') }}</div>
                                </div>
                            </div>
                        </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--base btn-sm h-45 w-100 text-center">@lang('Invest Now')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@push('script')
    <script>
        (function($) {
            "use strict"

            $('.customWidth').each(function(index, element) {
                let width = $(this).data('invested');
                $(this).css('width', `${width}%`);
            });

            $('.poolInvestNow').on('click', function() {
                $('[name=pool_id]').val($(this).data('pool_id'));
                $('.planName').text($(this).data('pool_name'));
            });


        })(jQuery);
    </script>
@endpush
