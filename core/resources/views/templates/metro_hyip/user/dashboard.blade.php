@extends($activeTemplate . 'layouts.master') @section('content') @php $kyc = getContent('kyc.content', true); @endphp
<section class="mt-3 mb-60">
	<div class="notice"></div>

	@if ($user->kv == Status::KYC_UNVERIFIED && $user->kyc_rejection_reason)
	<div class="alert border border--danger" role="alert">
		<div class="alert__icon d-flex align-items-center text--danger"><i class="fas fa-times-circle"></i>
		</div>
		<p class="alert__message">
			<span class="fw-bold">@lang('KYC Documents Rejected')</span><br>
			<small>
                        {{ __(@$kyc->data_values->reject) }}
                        <a href="javascript::void(0)" class="text--base-two" data-bs-toggle="modal" data-bs-target="#kycRejectionReason">@lang('Click here')</a> @lang('to show the reason').

                        <a href="{{ route('user.kyc.form') }}" class="text--base-two">@lang('Click Here')</a> @lang('to Re-submit Documents').
                        <a href="{{ route('user.kyc.data') }}" class="text--base-two">@lang('See KYC Data')</a>
                    </small>
		</p>
	</div>
	@elseif($user->kv == Status::KYC_UNVERIFIED)
	<div class="alert border border--info" role="alert">
		<div class="alert__icon d-flex align-items-center text--info"><i class="fas fa-exclamation-circle"></i>
		</div>
		<p class="alert__message">
			<span class="fw-bold">@lang('KYC Verification Required')</span><br>
			<small>{{ __(@$kyc->data_values->required) }} <a href="{{ route('user.kyc.form') }}" class="text--base-two">@lang('Click Here to Submit Documents')</a>
                    </small>
		</p>
	</div>
	@elseif($user->kv == Status::KYC_PENDING)
	<div class="alert border border--warning" role="alert">
		<div class="alert__icon d-flex align-items-center text--warning"><i class="las la-hourglass-half"></i>
		</div>
		<p class="alert__message">
			<span class="fw-bold">@lang('KYC Verification Pending')</span><br>
			<small>{{ __(@$kyc->data_values->pending) }} <a href="{{ route('user.kyc.data') }}" class="text--base-two">@lang('See KYC Data')</a>
                    </small>
		</p>
	</div>
	@endif

	<div class="row justify-content-center mb-3">
		<div class="col-md-12">

			@if ($user->deposit_wallet
			<=0 && $user->interest_wallet
				<=0 ) <div class="alert border border--danger" role="alert">
					<div class="alert__icon d-flex align-items-center text--danger"><i class="fas fa-exclamation-triangle"></i>
					</div>
					<p class="alert__message">
						<span class="fw-bold">@lang('Empty Balance')</span><br>
						<small>
                                @lang('Your balance is empty. Please make')
                                <a class="text--base-two" href="{{ route('user.deposit.index') }}" class="link-color">@lang('deposit')</a> @lang('for your next investment.')
                            </small>
					</p>
		</div>
		@endif @if ($user->deposits->where('status', 1)->count() == 1 && !$user->invests->count())
		<div class="alert border border--success" role="alert">
			<div class="alert__icon d-flex align-items-center text--success"><i class="fas fa-check"></i>
			</div>
			<p class="alert__message">
				<span class="fw-bold">@lang('First Deposit')</span><br>
				<small>
                                <span class="fw-bold">@lang('Congratulations!')</span> @lang('You\'ve made your first deposit successfully. Go to')
                                <a href="{{ route('plan') }}" class="text--base-two">@lang('investment plan')</a>
                                @lang('page and invest now')
                            </small>
			</p>
		</div>
		@endif @if ($pendingWithdrawals)
		<div class="alert border border--primary" role="alert">
			<div class="alert__icon d-flex align-items-center text--primary"><i class="fas fa-spinner"></i>
			</div>
			<p class="alert__message">
				<span class="fw-bold">@lang('Withdrawal Pending')</span><br>
				<small>
                                @lang('Total') {{ showAmount($pendingWithdrawals) }} @lang('withdrawal request is pending. Please wait for admin approval. The amount will send to the account which you\'ve provided. See') <a class="text--base-two" href="{{ route('user.withdraw.history') }}" class="link-color">@lang('withdrawal history')</a>
                            </small>
			</p>
		</div>
		@endif @if ($pendingDeposits)
		<div class="alert border border--primary" role="alert">
			<div class="alert__icon d-flex align-items-center text--primary"><i class="fas fa-spinner"></i>
			</div>
			<p class="alert__message">
				<span class="fw-bold">@lang('Deposit Pending')</span><br>
				<small>
                                @lang('Total') {{ showAmount($pendingDeposits) }}
                                @lang('deposit request is pending. Please wait for admin approval. See') <a class="text--base-two" href="{{ route('user.deposit.history') }}">@lang('deposit history')</a>
                            </small>
			</p>
		</div>
		@endif @if (!$user->ts)
		<div class="alert border border--warning" role="alert">
			<div class="alert__icon d-flex align-items-center text--warning"><i class="fas fa-user-lock"></i>
			</div>
			<p class="alert__message">
				<span class="fw-bold">@lang('2FA Authentication')</span><br>
				<small>
                            @lang('To keep safe your account, Please enable') <a href="{{ route('user.twofactor') }}" class="text--base-two">@lang('2FA')</a> @lang('security').
                                @lang('It will make secure your account and balance.')
                        </small>
			</p>
		</div>
		@endif @if ($isHoliday)

		<div class="alert border border--info" role="alert">
			<div class="alert__icon d-flex align-items-center text--info"><i class="fas fa-toggle-off"></i>
			</div>
			<p class="alert__message">
				<span class="fw-bold">@lang('Holiday')</span><br>
				<small>@lang('Today is holiday on this system. You\'ll not get any interest today from this system. Also you\'re unable to make withdrawal request today.') <br> @lang('The next working day is coming after') <span id="counter" class="fw-bold text--violet fs--15px"></span></small>
			</p>
		</div>
		@endif

	</div>
	</div>
	<div class="row gy-4 justify-content-center">
		
        <div class="col-xxl-6 col-xl-6 col-sm-6">
			<div class="dashboard-card">
				<div class="dashboard-card__shape"></div>
				<div class="dashboard-card__header">
					<span class="dashboard-card__header-icon">
                            <i class="fas fa-funnel-dollar"></i>
                        </span>
					<div class="dashboard-card__header-content">
						<h6 class="dashboard-card__header-title"> @lang('Total Investments') </h6>
						<span class="dashboard-card__header-currency"> {{ showAmount($invests) }}
                            </span>
					</div>
				</div>
				<div class="dashboard-card__item">
					<div class="dashboard-card__content">
						<span class="dashboard-card__text"> @lang('Completed') </span>
						<h4 class="dashboard-card__amount">
                                {{ showAmount($completedInvests) }} </h4>
					</div>
					<div class="dashboard-card__content">
						<span class="dashboard-card__text"> @lang('Running') </span>
						<h4 class="dashboard-card__amount"> {{ showAmount($runningInvests) }}
                            </h4>
					</div>
					<div class="dashboard-card__content">
						<span class="dashboard-card__text"> @lang('Interests') </span>
						<h4 class="dashboard-card__amount"> {{ showAmount($interests) }} </h4>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xxl-6 col-xl-6 col-sm-6">
			<div class="dashboard-card">
				<div class="dashboard-card__shape"></div>
				<div class="dashboard-card__header">
					<span class="dashboard-card__header-icon style-two">
                            <i class="fas fa-coins"></i>
                        </span>
					<div class="dashboard-card__header-content">
						<h6 class="dashboard-card__header-title"> @lang('Total Withdraw') </h6>
						<span class="dashboard-card__header-currency"> {{ showAmount($successfulWithdrawals) }}
                            </span>
					</div>
				</div>
				<div class="dashboard-card__item">
					<div class="dashboard-card__content">
						<span class="dashboard-card__text"> @lang('Submitted') </span>
						<h4 class="dashboard-card__amount">
                                {{ showAmount($submittedWithdrawals) }} </h4>
					</div>
					<div class="dashboard-card__content">
						<span class="dashboard-card__text"> @lang('Pending') </span>
						<h4 class="dashboard-card__amount">
                                {{ showAmount($pendingWithdrawals) }} </h4>
					</div>
					<div class="dashboard-card__content">
						<span class="dashboard-card__text"> @lang('Rejected') </span>
						<h4 class="dashboard-card__amount">
                                {{ showAmount($rejectedWithdrawals) }} </h4>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xxl-6 col-xl-6 col-sm-6">
			<div class="dashboard-card">
				<div class="dashboard-card__shape"></div>
				<div class="dashboard-card__header">
					<span class="dashboard-card__header-icon style-four">
                            <i class="fas fa-wallet"></i>
                        </span>
					<div class="dashboard-card__header-content">
						<h6 class="dashboard-card__header-title"> @lang('Total Deposit') </h6>
						<span class="dashboard-card__header-currency"> {{ showAmount($successfulDeposits) }}
                            </span>
					</div>
				</div>
				<div class="dashboard-card__item">
					<div class="dashboard-card__content">
						<span class="dashboard-card__text"> @lang('Submitted') </span>
						<h4 class="dashboard-card__amount">
                                {{ showAmount($submittedDeposits) }} </h4>
					</div>
					<div class="dashboard-card__content">
						<span class="dashboard-card__text"> @lang('Pending') </span>
						<h4 class="dashboard-card__amount"> {{ showAmount($pendingDeposits) }}
                            </h4>
					</div>
					<div class="dashboard-card__content">
						<span class="dashboard-card__text"> @lang('Rejected') </span>
						<h4 class="dashboard-card__amount">
                                {{ showAmount($rejectedDeposits) }} </h4>
					</div>
				</div>
			</div>
		</div>

        <div class="col-xxl-6 col-xl-6 col-sm-6">
			<div class="dashboard-card">
				<div class="dashboard-card__shape"></div>
				<div class="dashboard-card__header">
					<span class="dashboard-card__header-icon  style-five">
                            <i class="fa-solid fa-sack-dollar"></i>
                        </span>
					<div class="dashboard-card__header-content">
						<h6 class="dashboard-card__header-title"> @lang('Bonus')</h6>
						<span class="dashboard-card__header-currency"> {{ showAmount($referralEarnings) }}
                            </span>
					</div>
				</div>
				<div class="dashboard-card__item">
					<div class="dashboard-card__content">
						<span class="dashboard-card__text"> @lang('Referral') </span>
						<h4 class="dashboard-card__amount">
                        {{ showAmount($referralEarnings) }}</h4>
					</div>
                    <div class="dashboard-card__content">
						<span class="dashboard-card__text"> @lang('Ranking') </span>
						<h4 class="dashboard-card__amount">
                        {{ showAmount($referralEarnings) }}</h4>
					</div>
					<div class="dashboard-card__content">
						<span class="dashboard-card__text"> @lang('Sellers') </span>
						<h4 class="dashboard-card__amount">
                        {{ showAmount($referralEarnings) }}</h4>
					</div>
				</div>
			</div>
		</div>

	</div>
	<div class="mt-4">
		<div class="accordion" id="miAcordeon">
			<div class="accordion-item">
				<h2 class="accordion-header" id="headingOne">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                @lang('My Investment Progress') 
                </button>
            </h2>
				<div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#miAcordeon">
					<div class="accordion-body">
						<div class="row gy-4 pt-4 justify-content-center">
							<div class="col-xxl-6 col-xl-6 col-md-6">
								<div class="dashboard-item">
									<h5 class="dashboard-item__title">
                        @lang('My Investment Progress')
                    </h5> @php $completedPercent = $totalInvest ? ($completedInvests / $totalInvest) * 100 : 0; $runningPercent = $totalInvest ? ($runningInvests / $totalInvest) * 100 : 0; @endphp
									<div class="progress-wrapper mb-70">
										<div class="progress-basic">
											<div class="mb-3">
												<div class="investment-wrapper">
													<div class="d-flex align-items-center">
														<span class="investment-wrapper__icon">
                                            <i class="fas fa-funnel-dollar"></i>
                                        </span>
														<div class="investment-wrapper__rate">
															<h6 class="investment-wrapper__title">
                                                @lang('Total Investment')
                                            </h6>
															<span class="investment-wrapper__interest"> 100% @lang('investment is')
                                                {{ showAmount($totalInvest) }} </span>
														</div>
													</div>

												</div>
												<div class="progress mb-2">
													<div class="progress-bar progress-basic-1" data-wow-duration="1s" style="width: {{ $totalInvest }}%;" data-wow-delay="0.5s" role="progressbar" aria-valuenow="{{ $totalInvest }}" aria-valuemin="0" aria-valuemax="100">
													</div>
												</div>
											</div>
											<div class="mb-3">
												<div class="investment-wrapper">
													<div class="d-flex align-items-center">
														<span class="investment-wrapper__icon style-two">
                                            <i class="fas fa-funnel-dollar"></i>
                                        </span>
														<div class="investment-wrapper__rate">
															<h6 class="investment-wrapper__title">
                                                @lang('Complete Investment')
                                            </h6>
															<span class="investment-wrapper__interest">
                                                {{ showAmount($completedPercent, currencyFormat: false) }}% @lang('of')
                                                {{ showAmount($completedInvests) }}</span>
														</div>
													</div>

												</div>
												<div class="progress mb-2">
													<div class="progress-bar progress-basic-2" data-wow-duration="1s" style="width: {{ $completedPercent }}%;" data-wow-delay="0.5s" role="progressbar" aria-valuenow="1" aria-valuemin="0" aria-valuemax="100">
													</div>
												</div>
											</div>
											<div>
												<div class="investment-wrapper">
													<div class="d-flex align-items-center">
														<span class="investment-wrapper__icon style-three">
                                            <i class="fas fa-funnel-dollar"></i>
                                        </span>
														<div class="investment-wrapper__rate">
															<h6 class="investment-wrapper__title">
                                                @lang('Running Investment')
                                            </h6>
															<span class="investment-wrapper__interest"> {{ showAmount($runningPercent, currencyFormat: false) }}%
                                                @lang('of') {{ showAmount($runningInvests) }}
                                            </span>
														</div>
													</div>

												</div>

												<div class="progress mb-2">
													<div class="progress-bar progress-basic-3" data-wow-duration="1s" style="width: {{ $runningPercent }}%;" data-wow-delay="0.5s" role="progressbar" aria-valuenow="{{ $runningPercent }}" aria-valuemin="0" aria-valuemax="100">
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-xxl-6 col-xl-6 col-md-6">
								<div class="dashboard-item">
									<h4 class="dashboard-item__title"> @lang('Profit Paying History')</h4> @php $investPaidHistory = $user?->invests->where('status', 1); $shouldPay = 0; $paid = 0; foreach ($investPaidHistory as $value) { $shouldPay += $value->should_pay; $paid += $value->paid; } $total = $shouldPay + $paid; $paidPercent = $total > 0 ? ($paid / $total) * 100 : 0; @endphp
									<div class="dashboard-item__pay">
										<span class="dashboard-item__investment-title">@lang('Should Pay')</span>
										<h6 class="dashboard-item__pay-number">{{ showAmount($shouldPay) }}</h6>
									</div>
									<div class="d-flex justify-content-between mb-2">
										<span class="price"> @lang('Paid') ({{ showAmount($paidPercent, currencyFormat: false) }}%) / {{ showAmount($paid) }}</span>
										<span class="price"> {{ showAmount($total) }} </span>
									</div>
									<div class="progress-basic">
										<div class="progress mb-3">
											<div class="progress-bar progress-basic-1" data-wow-duration="1s" style="width: {{ $paidPercent }}%" data-wow-delay="0.5s" role="progressbar" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100">
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
    <div class="mt-4">
		<div class="accordion" id="miAcordeon">
			<div class="accordion-item">
				<h2 class="accordion-header" id="headingTwo">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                @lang('Transactions') 
                </button>
            </h2>
				<div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#miAcordeon">
					<div class="accordion-body">
                    <div class="pt-4 table-section">
                            <h4>@lang('My Latest Transactions')</h4>
                            <div class="row gy-4">
                                <div class="col-lg-12">
                                    <table class="table style-two table--responsive--lg">
                                        <thead>
                                            <tr>
                                                <th>@lang('Date')</th>
                                                <th>@lang('Transaction ID')</th>
                                                <th>@lang('Amount')</th>
                                                <th>@lang('Wallet')</th>
                                                <th>@lang('Details')</th>
                                                <th>@lang('Post Balance')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($transactions as $trx)
                                            <tr>
                                                <td>
                                                    {{ showDatetime($trx->created_at, 'd/m/Y') }}
                                                </td>
                                                <td><span class="text-primary">{{ $trx->trx }}</span></td>

                                                <td>
                                                    @if ($trx->trx_type == '+')
                                                    <span class="text--success">+
                                                                    {{ showAmount($trx->amount) }}</span> @else
                                                    <span class="text--danger">-
                                                                    {{ showAmount($trx->amount) }}</span> @endif
                                                </td>
                                                <td>
                                                    @if ($trx->wallet_type == 'deposit_wallet')
                                                    <span class="badge badge--info">@lang('Deposit Wallet')</span> @else
                                                    <span class="badge badge--warning">@lang('Interest Wallet')</span> @endif
                                                </td>
                                                <td>{{ $trx->details }}</td>
                                                <td><span>{{ showAmount($trx->post_balance) }}</span>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="100%" class="text-center">
                                                    {{ __('No Transaction Found') }}</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
					</div>
				</div>
			</div>
		</div>
	</div>

	
</section>

@if ($user->kv == Status::KYC_UNVERIFIED && $user->kyc_rejection_reason)
<div class="modal fade" id="kycRejectionReason">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">@lang('KYC Document Rejection Reason')</h5>
				<button type="button" class="close" data-bs-dismiss="modal">
                            <i class="las la-times"></i>
                        </button>
			</div>
			<div class="modal-body">
				<p>{{ $user->kyc_rejection_reason }}</p>
			</div>
		</div>
	</div>
</div>
@endif @endsection @push('script')
<script>
	'use strict';
	        (function($) {
	            @if ($isHoliday)
	                function createCountDown(elementId, sec) {
	                    var tms = sec;
	                    var x = setInterval(function() {
	                        var distance = tms * 1000;
	                        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
	                        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
	                        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
	                        var seconds = Math.floor((distance % (1000 * 60)) / 1000);
	                        var days = `<span>${days}d</span>`;
	                        var hours = `<span>${hours}h</span>`;
	                        var minutes = `<span>${minutes}m</span>`;
	                        var seconds = `<span>${seconds}s</span>`;
	                        document.getElementById(elementId).innerHTML = days + ' ' + hours + " " + minutes +
	                            " " + seconds;
	                        if (distance < 0) {
	                            clearInterval(x);
	                            document.getElementById(elementId).innerHTML = "COMPLETE";
	                        }
	                        tms--;
	                    }, 1000);
	                }
	
	                createCountDown('counter', {{ abs(\Carbon\Carbon::parse($nextWorkingDay)->diffInSeconds()) }});
	            @endif
	        })(jQuery);
	
</script>
@endpush