@extends($activeTemplate . 'layouts.master')
@section('content')
@php $kyc = getContent('kyc.content', true); @endphp

<div class="prgr-dashboard">

    {{-- Page header --}}
    <div class="prgr-page-header">
        <div class="prgr-page-header__left">
            <h1 class="prgr-page-header__title">@lang('Panel de Control')</h1>
            <p class="prgr-page-header__subtitle">
                @lang('Bienvenido de nuevo'),
                <a href="{{ route('user.profile.setting') }}">{{ auth()->user()->fullname }}</a>.
                @lang('Aquí tienes el resumen de tus participaciones.')
            </p>
        </div>
        <div class="prgr-page-header__actions">
            <a href="{{ route('user.invest.log') }}" class="prgr-btn prgr-btn--outline">
                <i class="las la-download"></i> @lang('Exportar Reporte')
            </a>
            <a href="{{ route('plan') }}" class="prgr-btn prgr-btn--primary">
                <i class="las la-plus"></i> @lang('Nueva Inversión')
            </a>
        </div>
    </div>

    {{-- Alert banners --}}
    <div class="prgr-alerts">

        @if($user->profile_complete == 0)
        <div class="prgr-alert prgr-alert--warning">
            <div class="prgr-alert__icon"><i class="las la-user-edit"></i></div>
            <div class="prgr-alert__body">
                <strong>@lang('Perfil incompleto')</strong>
                <span>@lang('Completa tu perfil para acceder a todas las funciones.')</span>
            </div>
            <a href="{{ route('user.profile.setting') }}" class="prgr-alert__action">@lang('COMPLETAR')</a>
        </div>
        @endif

        @if($user->kv == Status::KYC_UNVERIFIED && $user->kyc_rejection_reason)
        <div class="prgr-alert prgr-alert--danger">
            <div class="prgr-alert__icon"><i class="las la-times-circle"></i></div>
            <div class="prgr-alert__body">
                <strong>@lang('KYC Rechazado')</strong>
                <span>{{ __(@$kyc->data_values->reject) }}</span>
            </div>
            <a href="{{ route('user.kyc.form') }}" class="prgr-alert__action">@lang('REENVIAR')</a>
        </div>
        @elseif($user->kv == Status::KYC_UNVERIFIED)
        <div class="prgr-alert prgr-alert--info">
            <div class="prgr-alert__icon"><i class="las la-id-card"></i></div>
            <div class="prgr-alert__body">
                <strong>@lang('Verificación KYC requerida')</strong>
                <span>{{ __(@$kyc->data_values->required) }}</span>
            </div>
            <a href="{{ route('user.kyc.form') }}" class="prgr-alert__action">@lang('VERIFICAR')</a>
        </div>
        @elseif($user->kv == Status::KYC_PENDING)
        <div class="prgr-alert prgr-alert--warning">
            <div class="prgr-alert__icon"><i class="las la-hourglass-half"></i></div>
            <div class="prgr-alert__body">
                <strong>@lang('KYC en revisión')</strong>
                <span>{{ __(@$kyc->data_values->pending) }}</span>
            </div>
            <a href="{{ route('user.kyc.data') }}" class="prgr-alert__action">@lang('VER DATOS')</a>
        </div>
        @endif

        @if(!$user->ts)
        <div class="prgr-alert prgr-alert--warning">
            <div class="prgr-alert__icon"><i class="las la-bell"></i></div>
            <div class="prgr-alert__body">
                <strong>@lang('Notificaciones del Navegador')</strong>
                <span>@lang('Por favor, permite las notificaciones para recibir actualizaciones en tiempo real sobre tus depósitos.')</span>
            </div>
            <a href="{{ route('user.twofactor') }}" class="prgr-alert__action">@lang('CONFIGURAR')</a>
        </div>
        @endif

        @if($pendingDeposits)
        <div class="prgr-alert prgr-alert--info">
            <div class="prgr-alert__icon"><i class="las la-clock"></i></div>
            <div class="prgr-alert__body">
                <strong>@lang('Depósito Pendiente')</strong>
                <span>@lang('Tu solicitud de') {{ showAmount($pendingDeposits) }} @lang('está siendo procesada por el administrador.')</span>
            </div>
            <a href="{{ route('user.deposit.history') }}" class="prgr-alert__action">@lang('VER HISTORIAL')</a>
        </div>
        @endif

        @if($pendingWithdrawals)
        <div class="prgr-alert prgr-alert--info">
            <div class="prgr-alert__icon"><i class="las la-hourglass-half"></i></div>
            <div class="prgr-alert__body">
                <strong>@lang('Retiro Pendiente')</strong>
                <span>@lang('Total') {{ showAmount($pendingWithdrawals) }} @lang('en proceso de aprobación.')</span>
            </div>
            <a href="{{ route('user.withdraw.history') }}" class="prgr-alert__action">@lang('VER HISTORIAL')</a>
        </div>
        @endif

        @if($isHoliday)
        <div class="prgr-alert prgr-alert--warning">
            <div class="prgr-alert__icon"><i class="las la-calendar-times"></i></div>
            <div class="prgr-alert__body">
                <strong>@lang('Día Festivo')</strong>
                <span>@lang('Hoy no se generan intereses. Próximo día hábil en:') <span id="counter" class="fw-bold ms-1"></span></span>
            </div>
        </div>
        @endif

    </div>

    {{-- Stats grid --}}
    <div class="prgr-stats-grid">

        <div class="prgr-stat-card">
            <div class="prgr-stat-card__icon prgr-stat-card__icon--blue">
                <i class="las la-chart-line"></i>
            </div>
            <span class="prgr-stat-card__label">@lang('Inversión Activa')</span>
            <h2 class="prgr-stat-card__amount">{{ showAmount($runningInvests) }}</h2>
            <span class="prgr-stat-card__trend prgr-stat-card__trend--up">
                <i class="las la-arrow-up"></i> @lang('vs mes anterior')
            </span>
        </div>

        <div class="prgr-stat-card">
            <div class="prgr-stat-card__icon prgr-stat-card__icon--green">
                <i class="las la-wallet"></i>
            </div>
            <span class="prgr-stat-card__label">@lang('Ganancia Disponible')</span>
            <h2 class="prgr-stat-card__amount">{{ showAmount(auth()->user()->interest_wallet) }}</h2>
            <span class="prgr-stat-card__trend prgr-stat-card__trend--up">
                <i class="las la-arrow-up"></i> @lang('vs mes anterior')
            </span>
        </div>

        <div class="prgr-stat-card">
            <div class="prgr-stat-card__icon prgr-stat-card__icon--purple">
                <i class="las la-gift"></i>
            </div>
            <span class="prgr-stat-card__label">@lang('Bonos Disponibles')</span>
            <h2 class="prgr-stat-card__amount">{{ showAmount($bonusWalletInvests) }}</h2>
            <span class="prgr-stat-card__trend prgr-stat-card__trend--neutral">
                <i class="las la-minus"></i> @lang('vs mes anterior')
            </span>
        </div>

        <div class="prgr-stat-card">
            <div class="prgr-stat-card__icon prgr-stat-card__icon--amber">
                <i class="las la-history"></i>
            </div>
            <span class="prgr-stat-card__label">@lang('Devolución de Capital')</span>
            <h2 class="prgr-stat-card__amount">{{ showAmount($fractionalCapital) }}</h2>
            <span class="prgr-stat-card__trend prgr-stat-card__trend--up">
                <i class="las la-arrow-up"></i> @lang('vs mes anterior')
            </span>
        </div>

        <div class="prgr-stat-card">
            <div class="prgr-stat-card__icon prgr-stat-card__icon--teal">
                <i class="las la-university"></i>
            </div>
            <span class="prgr-stat-card__label">@lang('Retiro Total Ganancias')</span>
            <h2 class="prgr-stat-card__amount">{{ showAmount($successfulWithdrawals) }}</h2>
            <span class="prgr-stat-card__trend prgr-stat-card__trend--up">
                <i class="las la-arrow-up"></i> @lang('vs mes anterior')
            </span>
        </div>

        <div class="prgr-stat-card">
            <div class="prgr-stat-card__icon prgr-stat-card__icon--rose">
                <i class="las la-star"></i>
            </div>
            <span class="prgr-stat-card__label">@lang('Retiro Total Bonos')</span>
            <h2 class="prgr-stat-card__amount">{{ showAmount($successfulWithdrawalsBonus) }}</h2>
            <span class="prgr-stat-card__trend prgr-stat-card__trend--neutral">
                <i class="las la-minus"></i> @lang('vs mes anterior')
            </span>
        </div>

    </div>

    {{-- Investment progress & transactions --}}
    <div class="prgr-two-col">

        <div class="prgr-card">
            <div class="prgr-card__header">
                <h5 class="prgr-card__title">@lang('Progreso de Inversión')</h5>
            </div>
            <div class="prgr-card__body">
                @php
                    $completedPercent = $totalInvest ? ($completedInvests / $totalInvest) * 100 : 0;
                    $runningPercent   = $totalInvest ? ($runningInvests / $totalInvest) * 100 : 0;
                @endphp
                <div class="prgr-progress-item">
                    <div class="prgr-progress-item__header">
                        <span>@lang('Inversión Total')</span>
                        <span>{{ showAmount($totalInvest) }}</span>
                    </div>
                    <div class="prgr-progress-bar"><div class="prgr-progress-bar__fill" style="width:100%"></div></div>
                </div>
                <div class="prgr-progress-item">
                    <div class="prgr-progress-item__header">
                        <span>@lang('Completada')</span>
                        <span>{{ showAmount($completedPercent, currencyFormat:false) }}% · {{ showAmount($completedInvests) }}</span>
                    </div>
                    <div class="prgr-progress-bar"><div class="prgr-progress-bar__fill prgr-progress-bar__fill--green" style="width:{{ $completedPercent }}%"></div></div>
                </div>
                <div class="prgr-progress-item">
                    <div class="prgr-progress-item__header">
                        <span>@lang('En curso')</span>
                        <span>{{ showAmount($runningPercent, currencyFormat:false) }}% · {{ showAmount($runningInvests) }}</span>
                    </div>
                    <div class="prgr-progress-bar"><div class="prgr-progress-bar__fill prgr-progress-bar__fill--blue" style="width:{{ $runningPercent }}%"></div></div>
                </div>
                @php
                    $investPaidHistory = $user?->invests->where('status', 1);
                    $shouldPay = 0; $paid = 0;
                    foreach ($investPaidHistory as $v) { $shouldPay += $v->should_pay; $paid += $v->paid; }
                    $total2 = $shouldPay + $paid;
                    $paidPercent = $total2 > 0 ? ($paid / $total2) * 100 : 0;
                @endphp
                <div class="prgr-progress-item" style="margin-top:1rem;padding-top:1rem;border-top:1px solid #f1f5f9">
                    <div class="prgr-progress-item__header">
                        <span>@lang('Ganancias Pagadas')</span>
                        <span>{{ showAmount($paidPercent, currencyFormat:false) }}% · {{ showAmount($paid) }}</span>
                    </div>
                    <div class="prgr-progress-bar"><div class="prgr-progress-bar__fill prgr-progress-bar__fill--amber" style="width:{{ $paidPercent }}%"></div></div>
                    <div class="d-flex justify-content-between mt-1">
                        <small class="text-muted">@lang('Pendiente') {{ showAmount($shouldPay) }}</small>
                        <small class="text-muted">@lang('Total') {{ showAmount($total2) }}</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="prgr-card">
            <div class="prgr-card__header">
                <h5 class="prgr-card__title">@lang('Últimas Transacciones')</h5>
                <a href="{{ route('user.transactions') }}" class="prgr-card__header-link">@lang('Ver todas') <i class="las la-arrow-right"></i></a>
            </div>
            <div class="prgr-card__body prgr-card__body--flush">
                @forelse($transactions as $trx)
                <div class="prgr-trx-row">
                    <div class="prgr-trx-row__icon {{ $trx->trx_type == '+' ? 'prgr-trx-row__icon--in' : 'prgr-trx-row__icon--out' }}">
                        <i class="las {{ $trx->trx_type == '+' ? 'la-arrow-down' : 'la-arrow-up' }}"></i>
                    </div>
                    <div class="prgr-trx-row__info">
                        <span class="prgr-trx-row__detail">{{ Str::limit($trx->details, 35) }}</span>
                        <span class="prgr-trx-row__date">{{ showDatetime($trx->created_at, 'd/m/Y') }}</span>
                    </div>
                    <div class="prgr-trx-row__amount {{ $trx->trx_type == '+' ? 'prgr-trx-row__amount--in' : 'prgr-trx-row__amount--out' }}">
                        {{ $trx->trx_type }}{{ showAmount($trx->amount) }}
                    </div>
                </div>
                @empty
                <div class="prgr-empty">
                    <i class="las la-receipt"></i>
                    <span>@lang('Sin transacciones recientes')</span>
                </div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- Featured projects --}}
    @if(isset($featuredProjects) && $featuredProjects->count() > 0)
    <div class="prgr-card mt-4">
        <div class="prgr-card__header">
            <h5 class="prgr-card__title">@lang('Proyectos Destacados')</h5>
            <a href="{{ route('projects.index') }}" class="prgr-card__header-link">@lang('Ver todos') <i class="las la-arrow-right"></i></a>
        </div>
        <div class="prgr-card__body">
            <div class="row g-3">
                @foreach($featuredProjects as $project)
                <div class="col-lg-4 col-md-6">
                    <div class="prgr-project-card">
                        <img src="{{ getImage(getFilePath('projectImage') . '/' . $project->image, getFileSize('projectImage')) }}"
                             alt="{{ __($project->name) }}" class="prgr-project-card__img">
                        <div class="prgr-project-card__body">
                            <h6 class="prgr-project-card__name">{{ __($project->name) }}</h6>
                            <div class="prgr-project-card__meta">
                                <span>{{ showAmount($project->minimum_investment) }} – {{ showAmount($project->maximum_investment) }}</span>
                                <span class="prgr-badge prgr-badge--blue">{{ $project->active_plans_count }} @lang('planes')</span>
                            </div>
                            <a href="{{ route('projects.show', $project->id) }}" class="prgr-btn prgr-btn--primary prgr-btn--sm w-100 mt-2">
                                @lang('Ver Proyecto') <i class="las la-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

</div>

@if($user->kv == Status::KYC_UNVERIFIED && $user->kyc_rejection_reason)
<div class="modal fade" id="kycRejectionReason">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('KYC Document Rejection Reason')</h5>
                <button type="button" class="close" data-bs-dismiss="modal"><i class="las la-times"></i></button>
            </div>
            <div class="modal-body"><p>{{ $user->kyc_rejection_reason }}</p></div>
        </div>
    </div>
</div>
@endif

@endsection

@push('script')
<script>
'use strict';
(function($) {
    @if($isHoliday)
    function createCountDown(elementId, sec) {
        var tms = sec;
        var x = setInterval(function() {
            var d = tms * 1000;
            document.getElementById(elementId).innerHTML =
                `<span>${Math.floor(d/(1000*60*60*24))}d</span> ` +
                `<span>${Math.floor((d%(1000*60*60*24))/(1000*60*60))}h</span> ` +
                `<span>${Math.floor((d%(1000*60*60))/(1000*60))}m</span> ` +
                `<span>${Math.floor((d%(1000*60))/1000)}s</span>`;
            if (d < 0) { clearInterval(x); document.getElementById(elementId).innerHTML = "COMPLETO"; }
            tms--;
        }, 1000);
    }
    createCountDown('counter', {{ abs(\Carbon\Carbon::parse($nextWorkingDay)->diffInSeconds()) }});
    @endif
})(jQuery);
</script>
@endpush