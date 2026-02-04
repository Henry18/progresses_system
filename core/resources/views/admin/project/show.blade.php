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
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.project.index') }}" />
    <a href="{{ route('admin.project.edit', $project->id) }}" class="btn btn-sm btn--primary">
        <i class="las la-pen"></i> @lang('Edit Project')
    </a>
@endpush
