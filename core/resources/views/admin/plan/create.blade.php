@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('admin.plan.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Name')</label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name') }}" required />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Description')</label>
                                    <textarea class="form-control" name="description" rows="4" required>{{ old('description') }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Plan Image')</label>
                                    <input type="file" class="form-control" name="image" accept="image/*" required />
                                    <small class="text-muted">@lang('Supported formats: JPG, PNG, GIF. Max size: 2MB')</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Plan PDF') <span class="text-muted">@lang('(Optional)')</span></label>
                                    <input type="file" class="form-control" name="pdf" accept=".pdf" />
                                    <small class="text-muted">@lang('Project explanation/documentation. Max size: 10MB')</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Days to init')</label>
                                    <input type="number" class="form-control" name="days_to_init" min="1" value="{{ old('days_to_init', 1) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Invest type')</label>
                                    <select name="invest_type" class="form-control" id="invest_type" required>
                                        <option value="1" {{ old('invest_type') == 1 ? 'selected' : '' }}>@lang('Range')</option>
                                        <option value="2" {{ old('invest_type') == 2 ? 'selected' : '' }}>@lang('Fixed')</option>
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
                                        <option value="1" {{ old('interest_type') == 1 ? 'selected' : '' }}>@lang('Percent')</option>
                                        <option value="2" {{ old('interest_type') == 2 ? 'selected' : '' }}>@lang('Fixed')</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Interest')</label>
                                    <div class="input-group">
                                        <input type="number" step="any" class="form-control" name="interest" value="{{ old('interest') }}" required>
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
                                            <option value="{{ $time->id }}" {{ old('time') == $time->id ? 'selected' : '' }}>{{ __($time->name) }}</option>
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
                                        data-bs-toggle="toggle" data-on="@lang('Yes')" data-off="@lang('No')" name="hold_capital" {{ old('hold_capital') ? 'checked' : '' }}>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label for="">@lang('Testing')</label>
                                    <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger"
                                        data-bs-toggle="toggle" data-on="@lang('Yes')" data-off="@lang('No')" name="testing" {{ old('testing') ? 'checked' : '' }}>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label for="">@lang('Featured')</label>
                                    <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger"
                                        data-bs-toggle="toggle" data-on="@lang('Yes')" data-off="@lang('No')" name="featured" {{ old('featured') ? 'checked' : '' }}>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label for="">@lang('Interest Distribution') <i class="las la-info-circle"
                                        title="@lang('Configure how interest will be distributed across different time periods')"></i></label>
                                    <input type="checkbox" data-width="100%" data-onstyle="-warning" data-offstyle="-secondary"
                                        data-bs-toggle="toggle" data-on="@lang('Enabled')" data-off="@lang('Disabled')" name="distribution_enabled" id="distribution_enabled" value="1" {{ old('distribution_enabled') ? 'checked' : '' }}>
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
                                                    <h4 class="mb-0"><span id="total_months_display">0</span> @lang('months')</h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <small class="text-muted">@lang('Total Interest')</small>
                                                    <h4 class="mb-0"><span id="total_interest_display">0</span>%</h4>
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
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.plan.index') }}" />
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

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
                                    <input type="number" step="any" class="form-control" name="minimum" value="{{ old('minimum') }}" required>
                                    <span class="input-group-text">{{ gs('cur_text') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="required">@lang('Maximum Invest')</label>
                                <div class="input-group">
                                    <input type="number" step="any" class="form-control" name="maximum" value="{{ old('maximum') }}" required>
                                    <span class="input-group-text">{{ gs('cur_text') }}</span>
                                </div>
                            </div>
                        </div>
                    `;
                } else {
                    html = `
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="required">@lang('Amount')</label>
                                <div class="input-group">
                                    <input type="number" step="any" class="form-control" name="amount" value="{{ old('amount') }}" required>
                                    <span class="input-group-text">{{ gs('cur_text') }}</span>
                                </div>
                            </div>
                        </div>
                    `;
                }
                $('.amount-fields').html(html);
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
                                    <input type="number" class="form-control" name="repeat_time" value="{{ old('repeat_time') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang('Capital back')</label>
                                <select name="capital_back" class="form-control" required>
                                    <option value="1" {{ old('capital_back') == 1 ? 'selected' : '' }}>@lang('Yes')</option>
                                    <option value="0" {{ old('capital_back') == 0 ? 'selected' : '' }}>@lang('No')</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="required">@lang('Months back capital') <i class="las la-info-circle" title="@lang('Time that must pass before starting to repay the capital.')"></i></label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="capital_months_return" min="1" value="{{ old('capital_months_return') }}" required>
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
                } else {
                    $('[name=compound_interest]').closest('.col-md-6').removeClass('col-lg-4').addClass('col-lg-6');
                    $('[name=featured]').closest('.col-md-6').removeClass('col-lg-4').addClass('col-lg-6');
                    $('.holdCapitalGroup').hide();
                    if ($('[name=hold_capital]').data('bs.toggle')) {
                        $('[name=hold_capital]').bootstrapToggle('off');
                    }
                }
            }

            // =============== Interest Distribution Management ===============
            let segmentCounter = 0;

            // Show distribution section only for repeat-type plans
            $('[name=return_type]').on('change', function() {
                if ($(this).val() == '0') {
                    $('.distribution-section').show();
                } else {
                    $('.distribution-section').hide();
                    $('#distribution_enabled').prop('checked', false).trigger('change');
                }
            });

            // Toggle distribution config
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
                let html = `
                    <div class="segment-item card mb-3" data-segment="${segmentCounter}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">@lang('Segment') ${segmentCounter}</h6>
                                <button type="button" class="btn btn-sm btn-danger remove-segment">
                                    <i class="las la-times"></i>
                                </button>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang('Duration (months)')</label>
                                        <input type="number" class="form-control segment-months" name="segment_months[]" min="1" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang('Interest (%)')</label>
                                        <input type="number" step="0.01" class="form-control segment-percentage" name="segment_percentage[]" min="0" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang('Description')</label>
                                        <input type="text" class="form-control" name="segment_description[]" placeholder="@lang('e.g., Initial period')">
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
                calculateTotals();
            });

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
        })(jQuery);
    </script>
@endpush
