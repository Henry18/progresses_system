@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="pt-120 pb-120">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Project Header -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-5">
                                    <img src="{{ getImage(getFilePath('projectImage') . '/' . $project->image, getFileSize('projectImage')) }}"
                                         alt="{{ __($project->name) }}"
                                         class="w-100 rounded">
                                </div>
                                <div class="col-md-7">
                                    <h2 class="mb-3">{{ __($project->name) }}</h2>

                                    <div class="project-meta mb-4">
                                        <div class="d-flex flex-wrap gap-2 mb-3">
                                            @if($project->featured)
                                            <span class="badge badge--success">
                                                <i class="las la-star"></i> @lang('Featured')
                                            </span>
                                            @endif
                                            <span class="badge badge--primary">
                                                <i class="las la-layer-group"></i> {{ $project->activePlans->count() }} @lang('Plans')
                                            </span>
                                        </div>

                                        <div class="project-investment-range p-3 bg--section rounded">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="text--base fw-bold">@lang('Investment Range')</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="d-block text-muted">@lang('Minimum')</small>
                                                    <h4 class="text--base mb-0">{{ showAmount($project->minimum_investment) }}</h4>
                                                </div>
                                                <div class="text-center">
                                                    <i class="las la-arrows-alt-h fs-3"></i>
                                                </div>
                                                <div class="text-end">
                                                    <small class="d-block text-muted">@lang('Maximum')</small>
                                                    <h4 class="text--base mb-0">{{ showAmount($project->maximum_investment) }}</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @if($project->pdf)
                                    <div class="mb-3">
                                        <a href="{{ getImage(getFilePath('projectFile') . '/' . $project->pdf) }}"
                                           target="_blank"
                                           class="btn btn--outline-base btn-sm">
                                            <i class="las la-file-pdf"></i> @lang('Download Project Documentation')
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Project Description -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4 class="mb-0">@lang('Project Description')</h4>
                        </div>
                        <div class="card-body">
                            <div class="project-description">
                                @php echo $project->description @endphp
                            </div>
                        </div>
                    </div>

                    <!-- Investment Plans -->
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0">@lang('Available Investment Plans')</h4>
                            <p class="text-muted mb-0 small">@lang('Choose the plan that best fits your investment strategy')</p>
                        </div>
                        <div class="card-body">
                            @if($project->activePlans->count() > 0)
                            <div class="row gy-4">
                                @foreach($project->activePlans as $plan)
                                <div class="col-lg-6">
                                    <div class="plan-item h-100">
                                        <div class="plan-item__header">
                                            <h4 class="plan-item__name">{{ __($plan->name) }}</h4>
                                        </div>
                                        <div class="plan-item__body">
                                            <div class="plan-item__interest">
                                                <h3 class="plan-item__amount">
                                                    @if($plan->interest_type == 1)
                                                        {{ getAmount($plan->interest) }}%
                                                    @else
                                                        {{ showAmount($plan->interest) }}
                                                    @endif
                                                </h3>
                                                <p class="plan-item__type">
                                                    @if($plan->interest_type == 1) @lang('Percent') @else {{ gs('cur_text') }} @endif
                                                    @if($plan->lifetime)
                                                        @lang('For Lifetime')
                                                    @else
                                                        @lang('For') {{ $plan->repeat_time }} {{ __($plan->timeSetting->name) }}
                                                    @endif
                                                </p>
                                            </div>

                                            <ul class="plan-item__features">
                                                <li>
                                                    <i class="las la-check-circle text--base"></i>
                                                    @lang('Every') {{ __($plan->timeSetting->name) }}
                                                </li>
                                                @if($plan->fixed_amount > 0)
                                                <li>
                                                    <i class="las la-check-circle text--base"></i>
                                                    @lang('Fixed Amount'): {{ showAmount($plan->fixed_amount) }}
                                                </li>
                                                @else
                                                <li>
                                                    <i class="las la-check-circle text--base"></i>
                                                    @lang('Min'): {{ showAmount($plan->minimum) }} - @lang('Max'): {{ showAmount($plan->maximum) }}
                                                </li>
                                                @endif
                                                @if($plan->capital_back)
                                                <li>
                                                    <i class="las la-check-circle text--base"></i>
                                                    @lang('Capital Back')
                                                    @if($plan->capital_months_return > 0)
                                                        @lang('after') {{ $plan->capital_months_return }} @lang('months')
                                                    @endif
                                                </li>
                                                @endif
                                                @if($plan->interest_distribution && isset($plan->interest_distribution['enabled']) && $plan->interest_distribution['enabled'])
                                                <li>
                                                    <i class="las la-check-circle text--base"></i>
                                                    @lang('Segmented Interest Distribution')
                                                </li>
                                                @endif
                                            </ul>
                                        </div>
                                        <div class="plan-item__footer">
                                            <a href="{{ route('plan.details', [$plan->id, slug($plan->name)]) }}" class="btn btn--base w-100">
                                                @lang('View Plan Details') <i class="las la-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="text-center py-5">
                                <p class="text-muted">@lang('No active plans available for this project at the moment.')</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Project Info -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">@lang('Project Information')</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="text-muted">@lang('Days to Start')</span>
                                    <span class="fw-bold">{{ $project->days_to_init }} @lang('days')</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="text-muted">@lang('Available Plans')</span>
                                    <span class="fw-bold">{{ $project->activePlans->count() }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="text-muted">@lang('Status')</span>
                                    <span class="badge badge--success">@lang('Active')</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Other Projects -->
                    @if($otherProjects->count() > 0)
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">@lang('Other Projects')</h5>
                        </div>
                        <div class="card-body">
                            @foreach($otherProjects as $otherProject)
                            <div class="other-project-item mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="d-flex gap-3">
                                    <div class="flex-shrink-0">
                                        <img src="{{ getImage(getFilePath('projectImage') . '/' . $otherProject->image, getFileSize('projectImage')) }}"
                                             alt="{{ __($otherProject->name) }}"
                                             style="width: 60px; height: 60px; object-fit: cover;"
                                             class="rounded">
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ __(strLimit($otherProject->name, 30)) }}</h6>
                                        <p class="text-muted small mb-2">{{ $otherProject->active_plans_count }} @lang('Plans')</p>
                                        <a href="{{ route('projects.show', $otherProject->id) }}" class="btn btn--sm btn--base">
                                            @lang('View')
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
