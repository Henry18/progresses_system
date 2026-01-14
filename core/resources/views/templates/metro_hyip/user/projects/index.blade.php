@extends($activeTemplate . 'layouts.master')
@section('content')
    <style>
        /* Override plan-card width only for projects view */
        .projects-grid .plan-card {
            width: 100% !important;
            max-width: 100% !important;
        }
    </style>
    <div class="container-fluid projects-grid">
        <div class="row justify-content-center mb-4">
            <div class="col-lg-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">@lang('Investment Projects')</h4>
                </div>
                <p class="text-muted mt-2">@lang('Browse our carefully selected investment opportunities. Each project offers different plans tailored to your investment goals.')</p>
            </div>
        </div>

        <div class="row gy-4">
            @forelse($projects as $project)
            <div class="col-lg-4 col-md-6">
                <div class="card custom--card h-100">
                    <div class="card-header p-0">
                        <img src="{{ getImage(getFilePath('projectImage') . '/' . $project->image, getFileSize('projectImage')) }}"
                             alt="{{ __($project->name) }}"
                             class="w-100"
                             style="height: 200px; object-fit: cover;">
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ __($project->name) }}</h5>
                        <p class="card-text text-muted">{{ __(strLimit($project->description, 120)) }}</p>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text--base fw-bold">@lang('Investment Range')</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge badge--dark">@lang('Min:')</span>
                                <span class="fw-bold">{{ showAmount($project->minimum_investment) }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-1">
                                <span class="badge badge--dark">@lang('Max:')</span>
                                <span class="fw-bold">{{ showAmount($project->maximum_investment) }}</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <span class="badge badge--primary">
                                <i class="las la-layer-group"></i> {{ $project->active_plans_count }} @lang('Plans Available')
                            </span>
                        </div>

                        <a href="{{ route('user.projects.show', $project->id) }}" class="btn btn--base w-100">
                            @lang('View Details') <i class="las la-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-lg-12">
                <div class="card custom--card">
                    <div class="card-body text-center py-5">
                        <i class="las la-folder-open text-muted" style="font-size: 64px;"></i>
                        <p class="text-muted mt-3">@lang('No projects available at this moment. Please check back later.')</p>
                    </div>
                </div>
            </div>
            @endforelse
        </div>

        @if($projects->hasPages())
        <div class="row mt-4">
            <div class="col-lg-12">
                {{ paginateLinks($projects) }}
            </div>
        </div>
        @endif
    </div>
@endsection
