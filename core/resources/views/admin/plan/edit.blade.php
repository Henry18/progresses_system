@extends('admin.layouts.app')

@section('panel')
    @if($plan->project)
    <div class="row mb-3">
        <div class="col-lg-12">
            <div class="alert alert-info d-flex align-items-center">
                <i class="las la-info-circle fs-3 me-3"></i>
                <div>
                    <strong>@lang('This plan belongs to project'):</strong> {{ $plan->project->name }}
                    <br>
                    <small>@lang('Plan is associated with the selected project')</small>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="alert alert-warning d-flex align-items-center">
                <i class="las la-exclamation-triangle fs-3 me-3"></i>
                <div>
                    <strong>@lang('Investment Limits'):</strong>
                    <br>
                    <small>
                        @lang('Minimum'): <strong>{{ showAmount($plan->project->minimum_investment) }}</strong>
                        &nbsp;|&nbsp;
                        @lang('Maximum'): <strong>{{ showAmount($plan->project->maximum_investment) }}</strong>
                    </small>
                    <br>
                    <small class="text-muted">@lang('Plan investment amounts must be within these limits')</small>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('admin.plan.update', $plan->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Plan Name')</label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name', $plan->name) }}" required />
                                    <small class="text-muted">@lang('e.g., "6 Months", "Annual Premium", "Quarterly Returns"')</small>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Plan Description')</label>
                                    <textarea class="form-control" name="description" rows="3">{{ old('description', $plan->description) }}</textarea>
                                    <small class="text-muted">@lang('Brief description of the plan benefits and features')</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Invest type')</label>
                                    <select name="invest_type" class="form-control" id="invest_type" required>
                                        <option value="1" {{ (old('invest_type', $plan->fixed_amount == 0 ? 1 : 2) == 1) ? 'selected' : '' }}>@lang('Range')</option>
                                        <option value="2" {{ (old('invest_type', $plan->fixed_amount == 0 ? 1 : 2) == 2) ? 'selected' : '' }}>@lang('Fixed')</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row amount-fields">
                            <!-- Dynamic amount fields will be inserted here -->
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Interest type')</label>
                                    <select name="interest_type" class="form-control" id="interest_type" required>
                                        <option value="1" {{ old('interest_type', $plan->interest_type) == 1 ? 'selected' : '' }}>@lang('Percent')</option>
                                        <option value="2" {{ old('interest_type', $plan->interest_type) == 2 ? 'selected' : '' }}>@lang('Fixed')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Interest')</label>
                                    <div class="input-group">
                                        <input type="number" step="any" class="form-control" name="interest" value="{{ old('interest', $plan->interest) }}" required>
                                        <span class="input-group-text interest-type">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Frequency of pay')</label>
                                    <select name="time" class="form-control" required>
                                        <option value="">@lang('Select One')</option>
                                        @foreach(App\Models\TimeSetting::active()->get() as $time)
                                            <option value="{{ $time->id }}" {{ old('time', $plan->time_setting_id) == $time->id ? 'selected' : '' }}>{{ __($time->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Return type')</label>
                                    <select name="return_type" class="form-control" id="return_type" required>
                                        <option value="0" selected>@lang('Repeat')</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row repeat-time">
                            <!-- Dynamic repeat time fields will be inserted here -->
                        </div>

                        <div class="row">
                            <div class="col-md-6 col-lg-4 holdCapitalGroup" style="display: none;">
                                <div class="form-group">
                                    <label for="">@lang('Hold Capital') <i class="las la-info-circle"
                                        title="@lang('Investor\'s investment capital will be hold after completing the invest. Investors will be able to reinvest or withdraw the capital.')"></i></label>
                                    <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger"
                                        data-bs-toggle="toggle" data-on="@lang('Yes')" data-off="@lang('No')" name="hold_capital" {{ old('hold_capital', $plan->hold_capital) ? 'checked' : '' }}>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label for="">@lang('Interest Distribution') <i class="las la-info-circle"
                                        title="@lang('Configure how interest will be distributed across different time periods')"></i></label>
                                    <input type="checkbox" data-width="100%" data-onstyle="-warning" data-offstyle="-secondary"
                                        data-bs-toggle="toggle" data-on="@lang('Enabled')" data-off="@lang('Disabled')" name="distribution_enabled" id="distribution_enabled" value="1" {{ old('distribution_enabled', $plan->interest_distribution ? 1 : 0) ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>

                        <!-- Interest Distribution Section -->
                        <div class="row mt-4 distribution-section">
                                <div class="distribution-config" style="display: none;">
                                    <div class="alert alert-info">
                                        <i class="las la-info-circle"></i>
                                        @lang('Configure how interest will be distributed across different time periods. The sum of all segments must equal the total months and total interest percentage.')
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <small class="text-muted">@lang('Total Plan Duration')</small>
                                                    <h4 class="mb-0"><span id="total_months_display">{{ $plan->repeat_time }}</span> @lang('months')</h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <small class="text-muted">@lang('Total Interest')</small>
                                                    <h4 class="mb-0"><span id="total_interest_display">{{ $plan->interest }}</span>%</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="segments-container"></div>

                                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-segment">
                                        <i class="las la-plus"></i> @lang('Add Segment')
                                    </button>

                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <div class="alert alert-warning mb-0">
                                                <small><strong>@lang('Segments Total'):</strong> <span id="segments_months_total">0</span> @lang('months')</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="alert alert-warning mb-0">
                                                <small><strong>@lang('Percentage Total'):</strong> <span id="segments_percentage_total">0</span>%</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Preview Button -->
                        <div class="row mt-3 preview-distribution-btn" style="display: none;">
                            <div class="col-md-12 text-center">
                                <button type="button" class="btn btn-outline--info" data-bs-toggle="modal" data-bs-target="#distributionPreviewModal">
                                    <i class="las la-eye"></i> @lang('Preview Distribution')
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Distribution Preview Modal -->
    <div class="modal fade" id="distributionPreviewModal" tabindex="-1" aria-labelledby="distributionPreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg--dark">
                    <h5 class="modal-title text-white" id="distributionPreviewModalLabel">
                        <i class="las la-chart-area"></i> @lang('Interest Distribution Preview')
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="distribution-preview-content">
                        <!-- Alert if no segments -->
                        <div class="alert alert-warning no-segments-alert" style="display: none;">
                            <i class="las la-exclamation-triangle"></i>
                            @lang('No distribution segments configured yet. Add segments to see the preview.')
                        </div>

                        <!-- Preview Content -->
                        <div class="segments-preview" style="display: none;">
                            <!-- Plan Summary -->
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="card bg--primary">
                                        <div class="card-body text-center">
                                            <h6 class="text-white">@lang('Total Duration')</h6>
                                            <h3 class="text-white mb-0"><span id="preview_total_months">0</span> @lang('months')</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg--success">
                                        <div class="card-body text-center">
                                            <h6 class="text-white">@lang('Total Interest')</h6>
                                            <h3 class="text-white mb-0"><span id="preview_total_interest">0</span>%</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg--info">
                                        <div class="card-body text-center">
                                            <h6 class="text-white">@lang('Segments')</h6>
                                            <h3 class="text-white mb-0"><span id="preview_segment_count">0</span></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Timeline Visual -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">@lang('Distribution Timeline')</h6>
                                </div>
                                <div class="card-body">
                                    <div class="progress" style="height: 50px;" id="preview_timeline"></div>
                                    <div class="d-flex justify-content-between mt-2">
                                        <small class="text-muted">@lang('Month') 1</small>
                                        <small class="text-muted">@lang('Month') <span id="preview_last_month">12</span></small>
                                    </div>
                                </div>
                            </div>

                            <!-- Segments Details -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">@lang('Segment Details')</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row" id="preview_segments_cards"></div>
                                </div>
                            </div>

                            <!-- Monthly Breakdown Table -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">@lang('Monthly Breakdown')</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped" id="preview_monthly_table">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>@lang('Month')</th>
                                                    <th>@lang('Segment')</th>
                                                    <th>@lang('Monthly Rate')</th>
                                                    <th>@lang('Cumulative')</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Investment Simulator -->
                            <div class="card">
                                <div class="card-header bg--warning">
                                    <h6 class="mb-0 text-white">
                                        <i class="las la-calculator"></i> @lang('Investment Simulator')
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label>@lang('Investment Amount')</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="preview_sim_amount" value="1000" min="1">
                                                <span class="input-group-text">{{ gs('cur_text') }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 d-flex align-items-end">
                                            <button type="button" class="btn btn--warning w-100" id="preview_calculate_btn">
                                                <i class="las la-calculator"></i> @lang('Calculate')
                                            </button>
                                        </div>
                                    </div>
                                    <div id="preview_simulation_results"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    @if($plan->project)
        <a href="{{ route('admin.project.show', $plan->project->id) }}" class="btn btn-sm btn-outline--info">
            <i class="las la-arrow-left"></i> @lang('Back to Project')
        </a>
    @else
        <x-back route="{{ route('admin.plan.index') }}" />
    @endif
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            var planData = @json($plan);

            // Límites del proyecto (si existe)
            @if($plan->project)
            const projectLimits = {
                minimum: {{ $plan->project->minimum_investment }},
                maximum: {{ $plan->project->maximum_investment }}
            };
            @else
            const projectLimits = null;
            @endif

            // Handle invest type change
            $('[name=invest_type]').on('change', function() {
                getInvestType($(this).val());
            }).trigger('change');

            // Handle interest type change
            $('[name=interest_type]').on('change', function() {
                getInterestType($(this).val());
            }).trigger('change');

            // Handle return type change
            $('[name=return_type]').on('change', function() {
                getReturnType($(this).val());
            }).trigger('change');

            // Handle capital back change
            $(document).on('change', '[name=capital_back]', function() {
                holdCapitalView();
            });

            function getInvestType(type) {
                var html = '';
                if (type == 1) {
                    html = `
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="required">@lang('Minimum Invest')</label>
                                <div class="input-group">
                                    <input type="number" step="any" class="form-control" name="minimum" value="${planData.minimum || ''}" required>
                                    <span class="input-group-text">{{ gs('cur_text') }}</span>
                                </div>
                                <small class="text-muted amount-validation-message"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="required">@lang('Maximum Invest')</label>
                                <div class="input-group">
                                    <input type="number" step="any" class="form-control" name="maximum" value="${planData.maximum || ''}" required>
                                    <span class="input-group-text">{{ gs('cur_text') }}</span>
                                </div>
                                <small class="text-muted amount-validation-message"></small>
                            </div>
                        </div>
                    `;
                } else {
                    html = `
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="required">@lang('Amount')</label>
                                <div class="input-group">
                                    <input type="number" step="any" class="form-control" name="amount" value="${planData.fixed_amount || ''}" required>
                                    <span class="input-group-text">{{ gs('cur_text') }}</span>
                                </div>
                                <small class="text-muted amount-validation-message"></small>
                            </div>
                        </div>
                    `;
                }
                $('.amount-fields').html(html);

                // Agregar validación en tiempo real si hay límites de proyecto
                if (projectLimits) {
                    attachAmountValidation();
                }
            }

            // Función para validar montos en tiempo real
            function attachAmountValidation() {
                $(document).on('input', '[name=minimum], [name=maximum], [name=amount]', function() {
                    validateAmounts();
                });
            }

            function validateAmounts() {
                if (!projectLimits) return true;

                let isValid = true;
                const minimum = parseFloat($('[name=minimum]').val()) || 0;
                const maximum = parseFloat($('[name=maximum]').val()) || 0;
                const amount = parseFloat($('[name=amount]').val()) || 0;

                // Validar mínimo
                if ($('[name=minimum]').length && minimum > 0) {
                    if (minimum < projectLimits.minimum) {
                        $('[name=minimum]').addClass('is-invalid').removeClass('is-valid');
                        $('[name=minimum]').closest('.form-group').find('.amount-validation-message')
                            .html('<span class="text-danger"><i class="las la-exclamation-circle"></i> Must be at least {{ gs("cur_sym") }}' + projectLimits.minimum + '</span>');
                        isValid = false;
                    } else {
                        $('[name=minimum]').removeClass('is-invalid').addClass('is-valid');
                        $('[name=minimum]').closest('.form-group').find('.amount-validation-message')
                            .html('<span class="text-success"><i class="las la-check-circle"></i> Valid</span>');
                    }
                }

                // Validar máximo
                if ($('[name=maximum]').length && maximum > 0) {
                    if (maximum > projectLimits.maximum) {
                        $('[name=maximum]').addClass('is-invalid').removeClass('is-valid');
                        $('[name=maximum]').closest('.form-group').find('.amount-validation-message')
                            .html('<span class="text-danger"><i class="las la-exclamation-circle"></i> Cannot exceed {{ gs("cur_sym") }}' + projectLimits.maximum + '</span>');
                        isValid = false;
                    } else if (maximum >= projectLimits.minimum) {
                        $('[name=maximum]').removeClass('is-invalid').addClass('is-valid');
                        $('[name=maximum]').closest('.form-group').find('.amount-validation-message')
                            .html('<span class="text-success"><i class="las la-check-circle"></i> Valid</span>');
                    }
                }

                // Validar monto fijo
                if ($('[name=amount]').length && amount > 0) {
                    if (amount < projectLimits.minimum || amount > projectLimits.maximum) {
                        $('[name=amount]').addClass('is-invalid').removeClass('is-valid');
                        $('[name=amount]').closest('.form-group').find('.amount-validation-message')
                            .html('<span class="text-danger"><i class="las la-exclamation-circle"></i> Must be between {{ gs("cur_sym") }}' + projectLimits.minimum + ' and {{ gs("cur_sym") }}' + projectLimits.maximum + '</span>');
                        isValid = false;
                    } else {
                        $('[name=amount]').removeClass('is-invalid').addClass('is-valid');
                        $('[name=amount]').closest('.form-group').find('.amount-validation-message')
                            .html('<span class="text-success"><i class="las la-check-circle"></i> Valid</span>');
                    }
                }

                return isValid;
            }

            function getInterestType(type) {
                if (type == 1) {
                    $('.interest-type').text('%');
                } else {
                    $('.interest-type').text('{{ gs('cur_text') }}');
                }
            }

            function getReturnType(type) {
                var html = '';
                if (type == 0) {
                    html = `
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="required">@lang('Total Months')</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="repeat_time" value="${planData.repeat_time || ''}" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('Capital back')</label>
                                <select name="capital_back" class="form-control" required>
                                    <option value="1" ${planData.capital_back == 1 ? 'selected' : ''}>@lang('Yes')</option>
                                    <option value="0" ${planData.capital_back == 0 ? 'selected' : ''}>@lang('No')</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 capital-months-group" style="display: none;">
                            <div class="form-group">
                                <label>@lang('Months back capital') <i class="las la-info-circle" title="@lang('Time that must pass before starting to repay the capital.')"></i></label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="capital_months_return" min="1" value="${planData.capital_months_return || 1}">
                                </div>
                            </div>
                        </div>
                    `;
                }
                $('.repeat-time').html(html);
                holdCapitalView();
            }

            function holdCapitalView() {
                var capitalBack = $('[name=capital_back]').val();
                if (capitalBack == '1') {
                    $('[name=compound_interest]').closest('.col-md-6').removeClass('col-lg-6').addClass('col-lg-4');
                    $('[name=featured]').closest('.col-md-6').removeClass('col-lg-6').addClass('col-lg-4');
                    $('.holdCapitalGroup').show();
                    $('.capital-months-group').show();
                    $('[name=capital_months_return]').prop('required', true);
                } else {
                    $('[name=compound_interest]').closest('.col-md-6').removeClass('col-lg-4').addClass('col-lg-6');
                    $('[name=featured]').closest('.col-md-6').removeClass('col-lg-4').addClass('col-lg-6');
                    $('.holdCapitalGroup').hide();
                    $('.capital-months-group').hide();
                    $('[name=capital_months_return]').prop('required', false).val('');
                    if ($('[name=hold_capital]').data('bs.toggle')) {
                        $('[name=hold_capital]').bootstrapToggle('off');
                    }
                }
            }

            // =============== Interest Distribution Management ===============
            let segmentCounter = 0;

            // Distribution section is always visible now

            // Toggle distribution config (with bootstrap-toggle compatibility)
            $('#distribution_enabled').on('change', function() {
                if ($(this).is(':checked')) {
                    $('.distribution-config').slideDown();
                    updateTotalDisplays();
                } else {
                    $('.distribution-config').slideUp();
                    $('#segments-container').empty();
                    segmentCounter = 0;
                }
            });

            // Update total displays when repeat_time or interest changes
            $('[name=repeat_time], [name=interest]').on('input', function() {
                updateTotalDisplays();
                calculateTotals();
            });

            function updateTotalDisplays() {
                let months = $('[name=repeat_time]').val() || 0;
                let interest = $('[name=interest]').val() || 0;
                $('#total_months_display').text(months);
                $('#total_interest_display').text(interest);
            }

            // Add new segment
            $('#add-segment').on('click', function() {
                segmentCounter++;
                addSegmentHTML(segmentCounter, {});
            });

            function addSegmentHTML(index, data) {
                let html = `
                    <div class="segment-item card mb-3" data-segment="${index}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">@lang('Segment') ${index}</h6>
                                <button type="button" class="btn btn-sm btn-danger remove-segment">
                                    <i class="las la-times"></i>
                                </button>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang('Duration (months)')</label>
                                        <input type="number" class="form-control segment-months" name="segment_months[]" min="1" value="${data.months || ''}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang('Interest (%)')</label>
                                        <input type="number" step="0.01" class="form-control segment-percentage" name="segment_percentage[]" min="0" value="${data.percentage || ''}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang('Description')</label>
                                        <input type="text" class="form-control" name="segment_description[]" value="${data.description || ''}" placeholder="@lang('e.g., Initial period')">
                                    </div>
                                </div>
                            </div>
                            <div class="segment-info small text-muted mt-2">
                                <i class="las la-calculator"></i> @lang('Monthly rate'): <span class="monthly-rate">0</span>%
                            </div>
                        </div>
                    </div>
                `;
                $('#segments-container').append(html);

                // Update monthly rate for this segment
                if (data.months && data.percentage) {
                    let monthlyRate = (data.percentage / data.months).toFixed(4);
                    $(`[data-segment="${index}"] .monthly-rate`).text(monthlyRate);
                }

                calculateTotals();
            }

            // Remove segment
            $(document).on('click', '.remove-segment', function() {
                $(this).closest('.segment-item').remove();
                calculateTotals();
            });

            // Calculate totals when segment values change
            $(document).on('input', '.segment-months, .segment-percentage', function() {
                calculateTotals();
                updateMonthlyRate($(this).closest('.segment-item'));
            });

            function updateMonthlyRate($segment) {
                let months = $segment.find('.segment-months').val() || 0;
                let percentage = $segment.find('.segment-percentage').val() || 0;
                let monthlyRate = months > 0 ? (percentage / months).toFixed(4) : 0;
                $segment.find('.monthly-rate').text(monthlyRate);
            }

            function calculateTotals() {
                let totalMonths = 0;
                let totalPercentage = 0;

                $('.segment-item').each(function() {
                    let months = parseFloat($(this).find('.segment-months').val()) || 0;
                    let percentage = parseFloat($(this).find('.segment-percentage').val()) || 0;
                    totalMonths += months;
                    totalPercentage += percentage;
                });

                $('#segments_months_total').text(totalMonths);
                $('#segments_percentage_total').text(totalPercentage.toFixed(2));

                // Validate totals
                let planMonths = parseFloat($('[name=repeat_time]').val()) || 0;
                let planInterest = parseFloat($('[name=interest]').val()) || 0;

                if (totalMonths == planMonths) {
                    $('#segments_months_total').parent().removeClass('alert-warning').addClass('alert-success');
                } else {
                    $('#segments_months_total').parent().removeClass('alert-success').addClass('alert-warning');
                }

                if (Math.abs(totalPercentage - planInterest) < 0.01) {
                    $('#segments_percentage_total').parent().removeClass('alert-warning').addClass('alert-success');
                } else {
                    $('#segments_percentage_total').parent().removeClass('alert-success').addClass('alert-warning');
                }
            }

            // Load existing distribution if any
            function loadExistingDistribution() {
                @if($plan->interest_distribution && isset($plan->interest_distribution['segments']))
                    let distribution = @json($plan->interest_distribution);
                    if (distribution.enabled && distribution.segments) {
                        // Enable the toggle first
                        $('#distribution_enabled').prop('checked', true);

                        // Show the distribution config manually
                        $('.distribution-config').show();

                        // Load each segment
                        distribution.segments.forEach(function(segment, index) {
                            segmentCounter++;
                            addSegmentHTML(segmentCounter, segment);
                        });

                        // Update displays
                        updateTotalDisplays();
                        calculateTotals();

                        // Initialize the toggle UI after loading data
                        setTimeout(function() {
                            $('#distribution_enabled').bootstrapToggle('on');
                        }, 100);
                    }
                @endif
            }

            $(document).ready(function() {
                // Load existing distribution after a slight delay to ensure DOM is ready
                setTimeout(loadExistingDistribution, 200);
            });

            // =============== Preview Modal Functions ===============
            // Show/hide preview button based on distribution enabled
            $('#distribution_enabled').on('change', function() {
                if ($(this).is(':checked')) {
                    $('.preview-distribution-btn').show();
                } else {
                    $('.preview-distribution-btn').hide();
                }
            });

            // Update preview when modal is opened
            $('#distributionPreviewModal').on('show.bs.modal', function() {
                updatePreviewModal();
            });

            // Update preview when calculate button is clicked
            $('#preview_calculate_btn').on('click', function() {
                calculateSimulation();
            });

            function updatePreviewModal() {
                const segments = [];
                $('.segment-item').each(function() {
                    const months = parseFloat($(this).find('.segment-months').val()) || 0;
                    const percentage = parseFloat($(this).find('.segment-percentage').val()) || 0;
                    const description = $(this).find('input[name="segment_description[]"]').val() || 'Segment';

                    if (months > 0 && percentage >= 0) {
                        segments.push({ months, percentage, description });
                    }
                });

                if (segments.length === 0) {
                    $('.no-segments-alert').show();
                    $('.segments-preview').hide();
                    return;
                }

                $('.no-segments-alert').hide();
                $('.segments-preview').show();

                // Update summary
                const totalMonths = segments.reduce((sum, s) => sum + s.months, 0);
                const totalInterest = segments.reduce((sum, s) => sum + s.percentage, 0);
                $('#preview_total_months').text(totalMonths);
                $('#preview_total_interest').text(totalInterest.toFixed(2));
                $('#preview_segment_count').text(segments.length);
                $('#preview_last_month').text(totalMonths);

                // Update timeline
                updateTimeline(segments, totalMonths);

                // Update segment cards
                updateSegmentCards(segments);

                // Update monthly table
                updateMonthlyTable(segments);

                // Run initial simulation
                calculateSimulation();
            }

            function updateTimeline(segments, totalMonths) {
                const colors = ['#28a745', '#17a2b8', '#ffc107', '#dc3545', '#6f42c1', '#fd7e14'];
                let html = '';

                segments.forEach((segment, index) => {
                    const widthPercentage = (segment.months / totalMonths) * 100;
                    const color = colors[index % colors.length];
                    html += `
                        <div class="progress-bar d-flex align-items-center justify-content-center"
                             style="width: ${widthPercentage}%; background-color: ${color};">
                            <strong class="text-white">${segment.percentage}%</strong>
                        </div>
                    `;
                });

                $('#preview_timeline').html(html);
            }

            function updateSegmentCards(segments) {
                const colors = ['#28a745', '#17a2b8', '#ffc107', '#dc3545', '#6f42c1', '#fd7e14'];
                let html = '';
                let accumulatedMonths = 0;

                segments.forEach((segment, index) => {
                    const startMonth = accumulatedMonths + 1;
                    const endMonth = accumulatedMonths + segment.months;
                    const monthlyRate = (segment.percentage / segment.months).toFixed(4);
                    const color = colors[index % colors.length];

                    html += `
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="segment-icon me-3">
                                            <i class="las la-calendar-check" style="font-size: 32px; color: ${color};"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">Segment ${index + 1}</h6>
                                            <small class="text-muted">${segment.description}</small>
                                        </div>
                                    </div>
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-2">
                                            <i class="las la-clock text-primary"></i>
                                            <strong>Period:</strong> Months ${startMonth}-${endMonth}
                                        </li>
                                        <li class="mb-2">
                                            <i class="las la-hourglass-half text-info"></i>
                                            <strong>Duration:</strong> ${segment.months} months
                                        </li>
                                        <li class="mb-2">
                                            <i class="las la-percentage text-success"></i>
                                            <strong>Total Interest:</strong> ${segment.percentage}%
                                        </li>
                                        <li class="mb-0">
                                            <i class="las la-calculator text-warning"></i>
                                            <strong>Monthly Rate:</strong> ${monthlyRate}%
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    `;

                    accumulatedMonths += segment.months;
                });

                $('#preview_segments_cards').html(html);
            }

            function updateMonthlyTable(segments) {
                let html = '';
                let accumulatedMonths = 0;
                let cumulativePercentage = 0;

                segments.forEach((segment, index) => {
                    const monthlyRate = segment.percentage / segment.months;

                    for (let i = 1; i <= segment.months; i++) {
                        const month = accumulatedMonths + i;
                        cumulativePercentage += monthlyRate;

                        html += `
                            <tr>
                                <td><strong>Month ${month}</strong></td>
                                <td><span class="badge badge--info">Segment ${index + 1}</span></td>
                                <td>${monthlyRate.toFixed(4)}%</td>
                                <td>${cumulativePercentage.toFixed(4)}%</td>
                            </tr>
                        `;
                    }

                    accumulatedMonths += segment.months;
                });

                $('#preview_monthly_table tbody').html(html);
            }

            function calculateSimulation() {
                const amount = parseFloat($('#preview_sim_amount').val()) || 0;

                if (amount <= 0) {
                    $('#preview_simulation_results').html(`
                        <div class="alert alert-danger">
                            <i class="las la-exclamation-circle"></i> Please enter a valid investment amount.
                        </div>
                    `);
                    return;
                }

                const segments = [];
                $('.segment-item').each(function() {
                    const months = parseFloat($(this).find('.segment-months').val()) || 0;
                    const percentage = parseFloat($(this).find('.segment-percentage').val()) || 0;
                    const description = $(this).find('input[name="segment_description[]"]').val() || 'Segment';

                    if (months > 0 && percentage >= 0) {
                        segments.push({ months, percentage, description });
                    }
                });

                if (segments.length === 0) {
                    return;
                }

                let html = `
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-warning">
                                <tr>
                                    <th>Period</th>
                                    <th>Duration</th>
                                    <th>Monthly Earnings</th>
                                    <th>Period Total</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                let totalEarnings = 0;
                let accumulatedMonths = 0;

                segments.forEach((segment, index) => {
                    const startMonth = accumulatedMonths + 1;
                    const endMonth = accumulatedMonths + segment.months;
                    const monthlyEarnings = amount * (segment.percentage / segment.months / 100);
                    const periodTotal = amount * (segment.percentage / 100);
                    totalEarnings += periodTotal;

                    html += `
                        <tr>
                            <td><strong>${segment.description}</strong><br><small class="text-muted">Months ${startMonth}-${endMonth}</small></td>
                            <td>${segment.months} months</td>
                            <td>{{ gs('cur_sym') }}${monthlyEarnings.toFixed(2)}</td>
                            <td><strong>{{ gs('cur_sym') }}${periodTotal.toFixed(2)}</strong></td>
                        </tr>
                    `;

                    accumulatedMonths += segment.months;
                });

                html += `
                            </tbody>
                            <tfoot class="table-success">
                                <tr>
                                    <th colspan="3">Total Earnings</th>
                                    <th>{{ gs('cur_sym') }}${totalEarnings.toFixed(2)}</th>
                                </tr>
                                <tr>
                                    <th colspan="3">Total Payout (Investment + Earnings)</th>
                                    <th>{{ gs('cur_sym') }}${(amount + totalEarnings).toFixed(2)}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                `;

                $('#preview_simulation_results').html(html);
            }
        })(jQuery);
    </script>
@endpush
