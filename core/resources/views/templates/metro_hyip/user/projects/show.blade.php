@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8">
                <!-- Project Header -->
                <div class="card custom--card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-5">
                                <img src="{{ getImage(getFilePath('projectImage') . '/' . $project->image, getFileSize('projectImage')) }}"
                                     alt="{{ __($project->name) }}"
                                     class="w-100 rounded">
                            </div>
                            <div class="col-md-7">
                                <h3 class="mb-3">{{ __($project->name) }}</h3>

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
                                            <span class="fw-bold">@lang('Investment Range')</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <small class="d-block ">@lang('Minimum')</small>
                                                <h3 class="mb-0">{{ showAmount($project->minimum_investment) }}</h3>
                                            </div>
                                            <div class="text-center">
                                                <i class="las la-arrows-alt-h fs-3"></i>
                                            </div>
                                            <div class="text-end">
                                                <small class="d-block ">@lang('Maximum')</small>
                                                <h3 class="mb-0">{{ showAmount($project->maximum_investment) }}</h3>
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
                <div class="card custom--card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">@lang('Project Description')</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-justify">{{ __($project->description) }}</p>
                    </div>
                </div>

                <!-- Investment Plans -->
                <div class="card custom--card">
                    <div class="card-header">
                        <h5 class="mb-0">@lang('Available Investment Plans')</h5>
                        <p class=" mb-0 small">@lang('Choose the plan that best fits your investment strategy')</p>
                    </div>
                    <div class="card-body">
                        @if($project->activePlans->count() > 0)
                        <div class="row gy-4">
                            @foreach($project->activePlans as $plan)
                            <div class="col-lg-6">
                                <div class="card custom--card h-100 border">
                                    <div class="card-header bg--base">
                                        <h5 class="mb-0 text-white">{{ __($plan->name) }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="text-center mb-3">
                                            <h2 class=" mb-0">
                                                @if($plan->interest_type == 1)
                                                    {{ getAmount($plan->interest) }}%
                                                @else
                                                    {{ showAmount($plan->interest) }}
                                                @endif
                                            </h2>
                                            <p class=" mb-0">
                                                @if($plan->interest_type == 1) @lang('Percent') @else {{ gs('cur_text') }} @endif
                                                @if($plan->lifetime)
                                                    @lang('For Lifetime')
                                                @else
                                                    @lang('For') {{ $plan->repeat_time }} @lang('Months')
                                                @endif
                                            </p>
                                        </div>

                                        <ul class="list-group list-group-flush mb-3">
                                            <li class="list-group-item px-0">
                                                <i class="las la-check-circle "></i>
                                                @lang('Every') {{ __($plan->timeSetting->name) }}
                                            </li>
                                            @if($plan->fixed_amount > 0)
                                            <li class="list-group-item px-0">
                                                <i class="las la-check-circle "></i>
                                                @lang('Fixed Amount'): {{ showAmount($plan->fixed_amount) }}
                                            </li>
                                            @else
                                            <li class="list-group-item px-0">
                                                <i class="las la-check-circle "></i>
                                                @lang('Min'): {{ showAmount($plan->minimum) }} - @lang('Max'): {{ showAmount($plan->maximum) }}
                                            </li>
                                            @endif
                                            @if($plan->capital_back)
                                            <li class="list-group-item px-0">
                                                <i class="las la-check-circle "></i>
                                                @lang('Capital Back')
                                                @if($plan->capital_months_return > 0)
                                                    @lang('after') {{ $plan->capital_months_return }} @lang('months')
                                                @endif
                                            </li>
                                            @endif
                                            @if($plan->interest_distribution && isset($plan->interest_distribution['enabled']) && $plan->interest_distribution['enabled'])
                                            <li class="list-group-item px-0">
                                                <i class="las la-check-circle "></i>
                                                @lang('Segmented Interest Distribution')
                                            </li>
                                            @endif
                                        </ul>

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
                            <i class="las la-info-circle " style="font-size: 64px;"></i>
                            <p class=" mt-3">@lang('No active plans available for this project at the moment.')</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Project Info -->
                <div class="card custom--card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">@lang('Project Information')</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="">@lang('Days to Start')</span>
                                <span class="fw-bold">{{ $project->days_to_init }} @lang('days')</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="">@lang('Available Plans')</span>
                                <span class="fw-bold">{{ $project->activePlans->count() }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="">@lang('Status')</span>
                                <span class="badge badge--success">@lang('Active')</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Other Projects -->
                @if($otherProjects->count() > 0)
                <div class="card custom--card">
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
                                    <p class=" small mb-2">{{ $otherProject->active_plans_count }} @lang('Plans')</p>
                                    <a href="{{ route('user.projects.show', $otherProject->id) }}" class="btn btn--sm btn--base">
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
@endsection
