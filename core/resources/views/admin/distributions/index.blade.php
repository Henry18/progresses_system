@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">@lang('Historial de Distribuciones de Ganancias')</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive--md table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('#')</th>
                                <th>@lang('Fecha')</th>
                                <th>@lang('Plan')</th>
                                <th>@lang('Tipo')</th>
                                <th>@lang('Monto Total')</th>
                                <th>@lang('Cuentas Afectadas')</th>
                                <th>@lang('Ejecutado por')</th>
                                <th>@lang('Detalle')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($distributions as $dist)
                            <tr>
                                <td>{{ $dist->id }}</td>
                                <td>{{ showDateTime($dist->created_at) }}</td>
                                <td>
                                    <span class="fw-bold">{{ __($dist->plan->name ?? '—') }}</span>
                                    @if($dist->plan->project)
                                        <br><small class="text-muted">{{ __($dist->plan->project->name) }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($dist->type === 'equitativo')
                                        <span class="badge badge--info">@lang('Equitativo')</span>
                                    @else
                                        <span class="badge badge--primary">@lang('Por Porcentaje')</span>
                                    @endif
                                </td>
                                <td>{{ showAmount($dist->total_amount) }}</td>
                                <td>
                                    <span class="badge badge--success">{{ $dist->accounts_affected }}</span>
                                </td>
                                <td>{{ $dist->admin->username ?? '—' }}</td>
                                <td>
                                    <a href="{{ route('admin.distribution.show', $dist->id) }}" class="btn btn-sm btn-outline--primary">
                                        <i class="las la-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">
                                    @lang('No hay distribuciones registradas aún.')
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($distributions->hasPages())
                <div class="card-footer">
                    {{ paginateLinks($distributions) }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.project.index') }}" />
@endpush