@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('admin.plan.update', $plan->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Name')</label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name', $plan->name) }}" required />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Description')</label>
                                    <textarea class="form-control" name="description" rows="4" required>{{ old('description', $plan->description) }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Plan Image')</label>
                                    <input type="file" class="form-control" name="image" accept="image/*" />
                                    <small class="text-muted">@lang('Supported formats: JPG, PNG, GIF. Max size: 2MB. Leave empty to keep current image.')</small>
                                    @if($plan->image)
                                        <div class="mt-2">
                                            <img src="{{ getImage(getFilePath('planImage') . '/' . $plan->image) }}" alt="Plan Image" style="max-width: 200px; max-height: 150px;" class="img-thumbnail">
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Plan PDF') <span class="text-muted">@lang('(Optional)')</span></label>
                                    <input type="file" class="form-control" name="pdf" accept=".pdf" />
                                    <small class="text-muted">@lang('Project explanation/documentation. Max size: 10MB. Leave empty to keep current PDF.')</small>
                                    @if($plan->pdf)
                                        <div class="mt-2">
                                            <a href="{{ getImage(getFilePath('planFile') . '/' . $plan->pdf) }}" target="_blank" class="btn btn-sm btn-outline--primary">
                                                <i class="las la-file-pdf"></i> @lang('View Current PDF')
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Days to init')</label>
                                    <input type="number" class="form-control" name="days_to_init" min="1" value="{{ old('days_to_init', $plan->days_to_init) }}" required>
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
                                        <option value="0" {{ old('return_type', $plan->lifetime == 0 ? 0 : 1) == 0 ? 'selected' : '' }}>@lang('Repeat')</option>
                                        <option value="1" {{ old('return_type', $plan->lifetime == 0 ? 0 : 1) == 1 ? 'selected' : '' }}>@lang('Lifetime')</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row repeat-time">
                            <!-- Dynamic repeat time fields will be inserted here -->
                        </div>

                        <div class="row">
                            <div class="col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label for="">@lang('Compound Interest') <i class="las la-info-circle"
                                        title="@lang('Provide investors with the choice to reinvest their earnings, allowing for compounding growth over time.')"></i></label>
                                    <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger"
                                        data-bs-toggle="toggle" data-on="@lang('Yes')" data-off="@lang('No')" name="compound_interest" {{ old('compound_interest', $plan->compound_interest) ? 'checked' : '' }}>
                                </div>
                            </div>
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
                                    <label for="">@lang('Testing')</label>
                                    <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger"
                                        data-bs-toggle="toggle" data-on="@lang('Yes')" data-off="@lang('No')" name="testing" {{ old('testing', $plan->testing) ? 'checked' : '' }}>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label for="">@lang('Featured')</label>
                                    <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger"
                                        data-bs-toggle="toggle" data-on="@lang('Yes')" data-off="@lang('No')" name="featured" {{ old('featured', $plan->featured) ? 'checked' : '' }}>
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

            var planData = @json($plan);

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
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="required">@lang('Maximum Invest')</label>
                                <div class="input-group">
                                    <input type="number" step="any" class="form-control" name="maximum" value="${planData.maximum || ''}" required>
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
                                    <input type="number" step="any" class="form-control" name="amount" value="${planData.fixed_amount || ''}" required>
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="required">@lang('Months back capital') <i class="las la-info-circle" title="@lang('Time that must pass before starting to repay the capital.')"></i></label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="capital_months_return" min="1" value="${planData.capital_months_return || 1}" required>
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
        })(jQuery);
    </script>
@endpush
