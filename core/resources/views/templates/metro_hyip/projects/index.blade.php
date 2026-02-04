@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <style>
        /* Override plan-card width only for projects view */
        .projects-grid .plan-card {
            width: 100% !important;
            max-width: 100% !important;
        }
    </style>
    <section class="pt-120 pb-120">
        <div class="container projects-grid">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="section-header text-center">
                        <h2 class="section-title">@lang('Investment Projects')</h2>
                        <p>@lang('Browse our carefully selected investment opportunities. Each project offers different plans tailored to your investment goals.')</p>
                    </div>
                </div>
            </div>

            @if($featuredProjects->count() > 0)
            <div class="row justify-content-center mb-5">
                <div class="col-lg-12">
                    <h3 class="mb-4">@lang('Featured Projects')</h3>
                </div>
                @foreach($featuredProjects as $project)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="plan-card h-100">
                        <div class="plan-card__header">
                            <div class="plan-card__img">
                                <img src="{{ getImage(getFilePath('projectImage') . '/' . $project->image, getFileSize('projectImage')) }}" alt="{{ __($project->name) }}">
                            </div>
                            <span class="badge badge--success">@lang('Featured')</span>
                        </div>
                        <div class="plan-card__body">
                            <h3 class="plan-card__name">{{ __($project->name) }}</h3>
                            <p class="plan-card__desc">{!! strLimit($project->description, 120) !!}</p>

                            <div class="plan-card__range mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text--base">@lang('Investment Range')</span>
                                </div>
                                <div class="mt-2">
                                    <span class="fw-bold">{{ showAmount($project->minimum_investment) }}</span>
                                    <span class="text-muted mx-2">-</span>
                                    <span class="fw-bold">{{ showAmount($project->maximum_investment) }}</span>
                                </div>
                            </div>

                            <div class="plan-card__footer">
                                <div class="mb-3">
                                    <span class="badge badge--primary">{{ $project->active_plans_count }} @lang('Plans Available')</span>
                                </div>
                                <a href="{{ route('projects.show', $project->id) }}" class="btn btn--base w-100">
                                    @lang('View Details') <i class="las la-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            <div class="row justify-content-center gy-4">
                <div class="col-lg-12">
                    <h3 class="mb-4">@lang('All Projects')</h3>
                </div>
                @forelse($projects as $project)
                <div class="col-lg-4 col-md-6">
                    <div class="plan-card h-100">
                        <div class="plan-card__header">
                            <div class="plan-card__img">
                                <img src="{{ getImage(getFilePath('projectImage') . '/' . $project->image, getFileSize('projectImage')) }}" alt="{{ __($project->name) }}">
                            </div>
                        </div>
                        <div class="plan-card__body">
                            <h3 class="plan-card__name">{{ __($project->name) }}</h3>
                            <p class="plan-card__desc">{{ __(strLimit($project->description, 120)) }}</p>

                            <div class="plan-card__range mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text--base">@lang('Investment Range')</span>
                                </div>
                                <div class="mt-2">
                                    <span class="fw-bold">{{ showAmount($project->minimum_investment) }}</span>
                                    <span class="text-muted mx-2">-</span>
                                    <span class="fw-bold">{{ showAmount($project->maximum_investment) }}</span>
                                </div>
                            </div>

                            <div class="plan-card__footer">
                                <div class="mb-3">
                                    <span class="badge badge--primary">{{ $project->active_plans_count }} @lang('Plans Available')</span>
                                </div>
                                <a href="{{ route('projects.show', $project->id) }}" class="btn btn--base w-100">
                                    @lang('View Details') <i class="las la-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-lg-12">
                    <div class="text-center">
                        <p class="text-muted">@lang('No projects available at this moment. Please check back later.')</p>
                    </div>
                </div>
                @endforelse
            </div>

            @if($projects->hasPages())
            <div class="row mt-5">
                <div class="col-lg-12">
                    {{ paginateLinks($projects) }}
                </div>
            </div>
            @endif
        </div>
    </section>
@endsection
