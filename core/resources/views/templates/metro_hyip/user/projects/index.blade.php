@extends($activeTemplate . 'layouts.master')
@section('content')
    <style>
        /* Override plan-card width only for projects view */
        .projects-grid .plan-card {
            width: 100% !important;
            max-width: 100% !important;
        }

        /* Disclaimer Modal Styles */
        .disclaimer-modal .modal-content {
            background: linear-gradient(135deg, #1a1754 0%, #0d0b2e 100%);
            border: 1px solid rgba(90, 72, 224, 0.3);
            border-radius: 16px;
        }

        .disclaimer-modal .modal-header {
            border-bottom: 1px solid rgba(90, 72, 224, 0.3);
            padding: 1.5rem;
        }

        .disclaimer-modal .modal-title {
            color: #ffc107;
            font-weight: 600;
        }

        .disclaimer-modal .modal-body {
            padding: 1.5rem;
        }

        .disclaimer-modal .disclaimer-section {
            background: rgba(90, 72, 224, 0.1);
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 1rem;
            border-left: 4px solid #5a48e0;
        }

        .disclaimer-modal .disclaimer-section:last-child {
            margin-bottom: 0;
        }

        .disclaimer-modal .disclaimer-section h6 {
            color: #5a48e0;
            font-weight: 600;
            margin-bottom: 0.75rem;
        }

        .disclaimer-modal .disclaimer-section p {
            color: rgba(255, 255, 255, 0.85);
            margin-bottom: 0;
            line-height: 1.6;
        }

        .disclaimer-modal .modal-footer {
            border-top: 1px solid rgba(90, 72, 224, 0.3);
            padding: 1rem 1.5rem;
        }

        .disclaimer-modal .form-check-label {
            color: rgba(255, 255, 255, 0.8);
            cursor: pointer;
        }

        .disclaimer-modal .btn-close-white {
            filter: invert(1) grayscale(100%) brightness(200%);
        }
    </style>

    <!-- Projects Disclaimer Modal -->
    <div class="modal fade disclaimer-modal" id="projectsDisclaimerModal" tabindex="-1" aria-labelledby="projectsDisclaimerLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="projectsDisclaimerLabel">
                        <i class="las la-exclamation-triangle me-2"></i>@lang('Important Notice')
                    </h5>
                </div>
                <div class="modal-body text-white">
                    <div class="disclaimer-section">
                        <h6><i class="las la-clock me-1"></i> @lang('Project Duration & Participation')</h6>
                        <p>@lang('Each available project has a defined duration and participation goal. Once the participation goal is reached, the project will be closed to new participants. Management and fulfillment of commitments to active participants will continue as planned.')</p>
                    </div>

                    <div class="disclaimer-section">
                        <h6><i class="las la-project-diagram me-1"></i> @lang('Independent Management')</h6>
                        <p>@lang('Each project is managed independently. The results and operations of one project do not affect or compromise other projects on the platform. Your participation in each project is treated as a separate engagement.')</p>
                    </div>

                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" id="dontShowAgainCheck" style="width: 18px; height: 18px;">
                        <label class="form-check-label ms-2" for="dontShowAgainCheck">
                            @lang("Don't show this message again")
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--base w-100" id="acceptDisclaimerBtn" disabled>
                        <i class="las la-check-circle me-1"></i> @lang('I Understand, Continue')
                    </button>
                </div>
            </div>
        </div>
    </div>

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

@push('script')
<script>
    "use strict";
    (function($) {
        const DISCLAIMER_KEY = 'projects_disclaimer_accepted';

        // Check if user has already accepted the disclaimer
        function hasAcceptedDisclaimer() {
            return localStorage.getItem(DISCLAIMER_KEY) === 'true';
        }

        // Save disclaimer acceptance
        function saveDisclaimerAcceptance() {
            if ($('#dontShowAgainCheck').is(':checked')) {
                localStorage.setItem(DISCLAIMER_KEY, 'true');
            }
        }

        // Show modal if disclaimer not previously accepted
        $(document).ready(function() {
            if (!hasAcceptedDisclaimer()) {
                var disclaimerModal = new bootstrap.Modal(document.getElementById('projectsDisclaimerModal'));
                disclaimerModal.show();
            }

            // Enable button when checkbox is checked (for "Don't show again")
            // The button requires reading the disclaimer first (3 second delay)
            setTimeout(function() {
                $('#acceptDisclaimerBtn').removeAttr('disabled');
            }, 2000);

            // Handle accept button click
            $('#acceptDisclaimerBtn').on('click', function() {
                saveDisclaimerAcceptance();
                var modal = bootstrap.Modal.getInstance(document.getElementById('projectsDisclaimerModal'));
                modal.hide();
            });
        });
    })(jQuery);
</script>
@endpush
