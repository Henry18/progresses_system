@php
    $latestDeposit = \App\Models\Deposit::with('user', 'gateway')->where('status', 1)->latest()->limit(10)->get();
    $fakeDeposit = \App\Models\Frontend::where('data_keys', 'transaction.element')->whereJsonContains('data_values->trx_type', 'deposit')->limit(10)->get();
    $deposits = $latestDeposit->merge($fakeDeposit);
    $deposits = $deposits->sortByDesc('created_at')->take(10);

    $latestWithdraw = \App\Models\Withdrawal::with('user', 'method')->where('status', 1)->latest()->limit(10)->get();
    $fakeWithdraw = \App\Models\Frontend::where('data_keys', 'transaction.element')->whereJsonContains('data_values->trx_type', 'withdraw')->limit(10)->get();
    $withdrawals = $latestWithdraw->merge($fakeWithdraw);
    $withdrawals = $withdrawals->sortByDesc('created_at')->take(10);

    $transactionContent = getContent('transaction.content', true);

@endphp
<!-- latest-transaction-section start -->
<section class="latest-transaction-section pt-150 pb-150">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="section-header text-center">
                    <h2 class="section__title">{{ __(@$transactionContent->data_values->heading) }}</h2>
                    <div class="header__divider">
                        <span class="left-dot"></span>
                        <span class="right-dot"></span>
                    </div>
                    <p>{{ __(@$transactionContent->data_values->sub_heading) }}</p>
                </div><!-- section-header end -->
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <ul class="nav nav-tabs justify-content-center mb-40" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active color-one" id="home-tab" data-bs-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">@lang('Latest Deposit')</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link color-two" id="profile-tab" data-bs-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">@lang('Latest Withdraw')</a>
                    </li>
                </ul>

                <div class="tab-content mt-3" id="myTabContent">
                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <div class="table-responsive--sm neu--table">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>@lang('Name')</th>
                                        <th>@lang('Date')</th>
                                        <th>@lang('Amount')</th>
                                        <th>@lang('Gateway')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($deposits as $data)
                                        <tr>
                                            @if (@$data->data_values)
                                                <td>
                                                    {{ __(@$data->data_values->name) }}
                                                </td>
                                                <td>{{ @$data->data_values->date }}</td>
                                                <td>{{ @$data->data_values->amount }} {{ gs('cur_text') }}</td>
                                                <td>{{ __(@$data->data_values->gateway) }}</td>
                                            @else
                                                <td>{{ __(@$data->user->fullname) }}</td>
                                                <td>{{ showDateTime($data->created_at, 'Y-m-d') }}</td>
                                                <td>{{ getAmount($data->amount) }} {{ gs('cur_text') }}</td>
                                                <td>{{ __($data->gateway->name) }}</td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        <div class="table-responsive--sm neu--table">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>@lang('Name')</th>
                                        <th>@lang('Date')</th>
                                        <th>@lang('Amount')</th>
                                        <th>@lang('Method')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($withdrawals as $data)
                                        <tr>
                                            @if (@$data->data_values)
                                                <td>{{ __(@$data->data_values->name) }}</td>
                                                <td>{{ @$data->data_values->date }}</td>
                                                <td>{{ @$data->data_values->amount }} {{ gs('cur_text') }}</td>
                                                <td>{{ __(@$data->data_values->gateway) }}</td>
                                            @else
                                                <td>{{ __($data->user->fullname) }}</td>
                                                <td>{{ showDateTime($data->created_at, 'Y-m-d') }}</td>
                                                <td>{{ getAmount($data->amount) }} {{ gs('cur_text') }}</td>
                                                <td>{{ __($data->method->name) }}</td>
                                            @endif
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</section>
