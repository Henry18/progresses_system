@extends('admin.layouts.app')

@section('panel')

{{-- Summary card --}}
<div class="row mb-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="widget-two style--two box--shadow2 b-radius--5 bg--primary mb-3">
                            <div class="widget-two__content">
                                <h6 class="text-white">@lang('Monto Distribuido')</h6>
                                <h4 class="text-white">{{ showAmount($distribution->total_amount) }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="widget-two style--two box--shadow2 b-radius--5 bg--success mb-3">
                            <div class="widget-two__content">
                                <h6 class="text-white">@lang('Cuentas Afectadas')</h6>
                                <h4 class="text-white">{{ $distribution->accounts_affected }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="widget-two style--two box--shadow2 b-radius--5 bg--info mb-3">
                            <div class="widget-two__content">
                                <h6 class="text-white">@lang('Tipo')</h6>
                                <h4 class="text-white">{{ $distribution->getTypeLabel() }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="widget-two style--two box--shadow2 b-radius--5 bg--warning mb-3">
                            <div class="widget-two__content">
                                <h6 class="text-white">@lang('Plan')</h6>
                                <h4 class="text-white">{{ __($distribution->plan->name ?? '—') }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>@lang('Proyecto'):</strong> {{ __($distribution->plan->project->name ?? '—') }}</p>
                        <p class="mb-1"><strong>@lang('Ejecutado por'):</strong> {{ $distribution->admin->username ?? '—' }}</p>
                        <p class="mb-1"><strong>@lang('Fecha'):</strong> {{ showDateTime($distribution->created_at) }}</p>
                    </div>
                    @if($distribution->notes)
                    <div class="col-md-6">
                        <p class="mb-1"><strong>@lang('Notas'):</strong></p>
                        <p class="text-muted">{{ $distribution->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Transactions detail --}}
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">@lang('Detalle por Cuenta')</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive--md table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('Usuario')</th>
                                <th>@lang('Monto Acreditado')</th>
                                <th>@lang('Wallet')</th>
                                <th>@lang('Saldo Post-Crédito')</th>
                                <th>@lang('TRX')</th>
                                <th>@lang('Detalle')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $trx)
                            <tr>
                                <td>
                                    <span class="fw-bold">{{ $trx->user->fullname ?? '—' }}</span>
                                    <br><small class="text-muted">{{ $trx->user->username ?? '' }}</small>
                                </td>
                                <td class="text--success fw-bold">+{{ showAmount($trx->amount) }}</td>
                                <td>
                                    <span class="badge badge--dark">{{ $trx->wallet_type }}</span>
                                </td>
                                <td>{{ showAmount($trx->post_balance) }}</td>
                                <td><small class="text-muted">{{ $trx->trx }}</small></td>
                                <td><small>{{ $trx->details }}</small></td>
                            </tr>
                            @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">
                                    @lang('No se encontraron transacciones para esta distribución.')
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($transactions->hasPages())
                <div class="card-footer">
                    {{ paginateLinks($transactions) }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.distribution.index') }}" />
@endpush