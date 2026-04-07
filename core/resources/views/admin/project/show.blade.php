@extends('admin.layouts.app')

@section('panel')
<div class="row mb-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <img src="{{ getImage(getFilePath('projectImage') . '/' . $project->image, getFileSize('projectImage')) }}" alt="{{ __($project->name) }}" class="w-100 rounded">
                    </div>
                    <div class="col-md-9">
                        <h3 class="mb-3">{{ __($project->name) }}</h3>
                        <p class="text-muted">{!! __($project->description) !!}</p>

                        <div class="row mt-3">
                            <div class="col-md-3">
                                <div class="widget-two style--two box--shadow2 b-radius--5 bg--info mb-3">
                                    <div class="widget-two__content">
                                        <h6 class="text-white">@lang('Min Investment')</h6>
                                        <h4 class="text-white">{{ showAmount($project->minimum_investment) }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="widget-two style--two box--shadow2 b-radius--5 bg--success mb-3">
                                    <div class="widget-two__content">
                                        <h6 class="text-white">@lang('Max Investment')</h6>
                                        <h4 class="text-white">{{ showAmount($project->maximum_investment) }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="widget-two style--two box--shadow2 b-radius--5 bg--primary mb-3">
                                    <div class="widget-two__content">
                                        <h6 class="text-white">@lang('Days to Init')</h6>
                                        <h4 class="text-white">{{ $project->days_to_init }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="widget-two style--two box--shadow2 b-radius--5 bg--warning mb-3">
                                    <div class="widget-two__content">
                                        <h6 class="text-white">@lang('Active Plans')</h6>
                                        <h4 class="text-white">{{ $project->plans->count() }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-2">
                            @if($project->featured)
                                <span class="badge badge--success">@lang('Featured')</span>
                            @endif
                            @if($project->testing)
                                <span class="badge badge--warning">@lang('Testing')</span>
                            @endif
                            @php echo $project->statusBadge @endphp
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">@lang('Investment Plans')</h5>
                <a href="{{ route('admin.plan.create') }}?project_id={{ $project->id }}" class="btn btn-sm btn--primary">
                    <i class="las la-plus"></i> @lang('Add New Plan')
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive--md table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('Plan Name')</th>
                                <th>@lang('Interest')</th>
                                <th>@lang('Duration')</th>
                                <th>@lang('Frequency')</th>
                                <th>@lang('Distribution')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($project->plans as $plan)
                            <tr>
                                <td>{{ __($plan->name) }}</td>
                                <td>
                                    {{ showAmount($plan->interest, currencyFormat: false) }}
                                    @if ($plan->interest_type == 1)
                                        %
                                    @else
                                        {{ gs('cur_text') }}
                                    @endif
                                </td>
                                <td>
                                    @if ($plan->lifetime == 0)
                                        {{ $plan->repeat_time }} @lang('Months')
                                    @else
                                        @lang('Lifetime')
                                    @endif
                                </td>
                                <td>{{ @$plan->timeSetting->time }} @lang('Hours')</td>
                                <td>
                                    @if($plan->interest_distribution)
                                        <span class="badge badge--warning">
                                            <i class="las la-chart-area"></i> @lang('Enabled')
                                        </span>
                                    @else
                                        <span class="badge badge--dark">@lang('Standard')</span>
                                    @endif
                                </td>
                                <td>
                                    @php echo $plan->statusBadge @endphp
                                </td>
                                <td>
                                    <div class="button-group">
                                        <a href="{{ route('admin.plan.edit', $plan->id) }}" class="btn btn-sm btn-outline--primary">
                                            <i class="la la-pen"></i>
                                        </a>

                                        @if($plan->status == 1)
                                        <button type="button" class="btn btn-sm btn-outline--danger confirmationBtn"
                                            data-action="{{ route('admin.plan.status', $plan->id) }}"
                                            data-question="@lang('Are you sure to disable this plan?')">
                                            <i class="la la-eye-slash"></i>
                                        </button>
                                        @else
                                        <button type="button" class="btn btn-sm btn-outline--success confirmationBtn"
                                            data-action="{{ route('admin.plan.status', $plan->id) }}"
                                            data-question="@lang('Are you sure to enable this plan?')">
                                            <i class="la la-eye"></i>
                                        </button>
                                        @endif

                                        <button type="button"
                                            class="btn btn-sm btn-outline--warning distributeBtn"
                                            data-plan-id="{{ $plan->id }}"
                                            data-plan-name="{{ __($plan->name) }}"
                                            data-action="{{ route('admin.distribution.store', $plan->id) }}"
                                            data-active-invests="{{ $plan->invests()->where('status', 1)->count() }}">
                                            <i class="las la-coins"></i> @lang('Dist. Ganancias')
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">
                                    @lang('No investment plans configured yet.')
                                    <a href="{{ route('admin.plan.create') }}?project_id={{ $project->id }}" class="text--primary">@lang('Add one now')</a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<x-confirmation-modal />

{{-- Modal: Distribución de Ganancias --}}
<div class="modal fade" id="distributeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="las la-coins text--warning"></i>
                    @lang('Distribución Equitativa de Ganancias')
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="distributeForm" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info d-flex align-items-center gap-2 mb-3">
                        <i class="las la-info-circle fs-5"></i>
                        <div>
                            @lang('Plan'): <strong id="modalPlanName">—</strong><br>
                            @lang('Inversiones activas'): <strong id="modalActiveInvests">—</strong>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label fw-bold">@lang('Tipo de Distribución') <span class="text-danger">*</span></label>
                        <select name="type" id="distType" class="form-control form--control">
                            <option value="equitativo">@lang('Equitativo') — @lang('mismo monto a cada cuenta')</option>
                            <option value="porcentaje">@lang('Por Porcentaje') — @lang('proporcional al monto invertido')</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label fw-bold">@lang('Monto Total a Distribuir') <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="amount" id="distAmount"
                                class="form-control form--control"
                                min="0.00000001" step="any"
                                placeholder="0.00" required>
                            <span class="input-group-text">{{ gs('cur_text') }}</span>
                        </div>
                    </div>

                    <div id="distPreview" class="alert alert-success d-none mt-2 mb-0 py-2 px-3 small">
                        <i class="las la-calculator"></i>
                        <span id="distPreviewText"></span>
                    </div>

                    <div class="form-group mt-3">
                        <label class="form-label">@lang('Notas') <small class="text-muted">(@lang('opcional'))</small></label>
                        <textarea name="notes" class="form-control form--control" rows="2" maxlength="500"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--secondary" data-bs-dismiss="modal">@lang('Cancelar')</button>
                    <button type="submit" class="btn btn--warning" id="distSubmitBtn">
                        <i class="las la-paper-plane"></i> @lang('Ejecutar Distribución')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.project.index') }}" />
    <a href="{{ route('admin.project.edit', $project->id) }}" class="btn btn-sm btn--primary">
        <i class="las la-pen"></i> @lang('Edit Project')
    </a>
    <a href="{{ route('admin.distribution.index') }}" class="btn btn-sm btn--info">
        <i class="las la-history"></i> @lang('Historial Distribuciones')
    </a>
@endpush

@push('script')
<script>
(function ($) {
    var activeInvests = 0;

    $(document).on('click', '.distributeBtn', function () {
        var btn = $(this);
        activeInvests = parseInt(btn.data('active-invests')) || 0;

        $('#modalPlanName').text(btn.data('plan-name'));
        $('#modalActiveInvests').text(activeInvests);
        $('#distributeForm').attr('action', btn.data('action'));
        $('#distAmount').val('');
        $('#distPreview').addClass('d-none');
        $('#distModal_notes').val('');

        $('#distributeModal').modal('show');
    });

    function updatePreview() {
        var amount = parseFloat($('#distAmount').val());
        var type   = $('#distType').val();

        if (!amount || amount <= 0 || activeInvests <= 0) {
            $('#distPreview').addClass('d-none');
            return;
        }

        var text = '';
        if (type === 'equitativo') {
            var perAccount = (amount / activeInvests).toFixed(8);
            text = 'Cada cuenta recibirá: {{ gs("cur_sym") }}' + perAccount + ' (entre ' + activeInvests + ' cuentas)';
        } else {
            text = 'Se distribuirán {{ gs("cur_sym") }}' + amount.toFixed(2) + ' proporcionalmente según el % invertido en el plan (' + activeInvests + ' cuentas).';
        }

        $('#distPreviewText').text(text);
        $('#distPreview').removeClass('d-none');
    }

    $('#distAmount, #distType').on('input change', updatePreview);

    $('#distributeForm').on('submit', function () {
        $('#distSubmitBtn').prop('disabled', true).html('<i class="las la-spinner la-spin"></i> Procesando...');
    });
})(jQuery);
</script>
@endpush
