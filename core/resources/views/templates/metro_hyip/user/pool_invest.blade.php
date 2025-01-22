@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="mt-3">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="text-end">
                    <a href="{{ route('user.pool.index') }}" class="btn btn--base stakingNow">
                        @lang('Pool')
                    </a>
                </div>
            </div>
            <div class="col-md-12">
                <div class="table-section">
                    <table class="table style-two table--responsive--lg">
                        <thead>
                            <tr>
                                <th>@lang('Pool')</th>
                                <th>@lang('Invest Amount')</th>
                                <th>@lang('Invest Till')</th>
                                <th>@lang('Return Date')</th>
                                <th>@lang('Total Return')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($poolInvests as $poolInvest)
                                <tr>
                                    <td>{{ __($poolInvest->pool->name) }}</td>
                                    <td>{{ showAmount($poolInvest->invest_amount) }}</td>
                                    <td>{{ showDateTime($poolInvest->pool->start_date, 'M d, Y h:i A') }}</td>
                                    <td>{{ showDateTime($poolInvest->pool->end_date, 'M d, Y h:i A') }}</td>

                                    <td class="text-end">
                                        @if ($poolInvest->pool->share_interest)
                                            {{ showAmount($poolInvest->invest_amount * (1 + $poolInvest->pool->interest / 100)) }}
                                        @else
                                            @lang('Not return yet!')
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="100%" class="text-center">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>


            @if ($poolInvests->hasPages())
                <div class="custom--pagination">
                    {{ paginateLinks($poolInvests) }}
                </div>
            @endif

        </div>
    </div>
@endsection
