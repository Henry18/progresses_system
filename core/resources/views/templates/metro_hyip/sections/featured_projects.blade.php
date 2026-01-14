@if(isset($featuredProjects) && $featuredProjects->count() > 0)
@php
    $content = getContent('featured_projects.content', true);
@endphp
<section class="pt-120 pb-120 bg--section position-relative overflow-hidden">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="section-header text-center">
                    <h2 class="section-title">{{ __(@$content->data_values->heading) }}</h2>
                    <p>{{ __(@$content->data_values->subheading) }}</p>
                </div>
            </div>
        </div>

        <div class="row gy-4 justify-content-center mt-4">
            @foreach($featuredProjects as $project)
            <div class="col-lg-4 col-md-6">
                <div class="plan-card h-100 hover-effect">
                    <div class="plan-card__header position-relative">
                        <div class="plan-card__img">
                            <img src="{{ getImage(getFilePath('projectImage') . '/' . $project->image, getFileSize('projectImage')) }}"
                                 alt="{{ __($project->name) }}"
                                 class="w-100">
                        </div>
                        <div class="featured-badge">
                            <span class="badge badge--success">
                                <i class="las la-star"></i> @lang('Featured')
                            </span>
                        </div>
                    </div>

                    <div class="plan-card__body">
                        <h3 class="plan-card__name mb-3">{{ __($project->name) }}</h3>
                        <p class="plan-card__desc text-muted mb-4">
                            {{ __(strLimit($project->description, 100)) }}
                        </p>

                        <div class="project-stats mb-4">
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="stat-item text-center p-2 bg--base-light rounded">
                                        <small class="d-block text-muted">@lang('Min Investment')</small>
                                        <strong class="text--base">{{ showAmount($project->minimum_investment) }}</strong>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-item text-center p-2 bg--base-light rounded">
                                        <small class="d-block text-muted">@lang('Max Investment')</small>
                                        <strong class="text--base">{{ showAmount($project->maximum_investment) }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge badge--primary">
                                <i class="las la-layer-group"></i> {{ $project->active_plans_count }} @lang('Plans')
                            </span>
                            <span class="text-muted small">
                                <i class="las la-calendar"></i> {{ $project->days_to_init }} @lang('days to start')
                            </span>
                        </div>

                        <a href="{{ route('projects.show', $project->id) }}" class="btn btn--base w-100">
                            @lang('Explore Project') <i class="las la-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="row mt-5">
            <div class="col-12 text-center">
                <a href="{{ route('projects.index') }}" class="btn btn--outline-base">
                    @lang('View All Projects') <i class="las la-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<style>
    .plan-card {
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 10px;
        overflow: hidden;
        transition: all 0.3s ease;
        background: rgba(255,255,255,0.05);
    }

    .plan-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }

    .plan-card__img img {
        height: 200px;
        object-fit: cover;
    }

    .featured-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 1;
    }

    .bg--base-light {
        background-color: rgba(var(--base-rgb), 0.1);
    }
</style>
@endif
