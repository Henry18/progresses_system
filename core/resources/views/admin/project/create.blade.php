@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('admin.project.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Project Name')</label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name') }}" required />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Description')</label>
                                    <textarea class="form-control nicEdit"  name="description" rows="6" required>{{ old('description') }}</textarea>
                                    <small class="text-muted">@lang('Describe the investment project, its goals, and expected returns.')</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Project Image')</label>
                                    <input type="file" class="form-control" name="image" accept="image/*" required />
                                    <small class="text-muted">@lang('Recommended: 800x600px. Max: 2MB')</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Project PDF') <span class="text-muted">@lang('(Optional)')</span></label>
                                    <input type="file" class="form-control" name="pdf" accept=".pdf" />
                                    <small class="text-muted">@lang('Upload project documentation or brochure. Max: 10MB')</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="required">@lang('Minimum Investment')</label>
                                    <div class="input-group">
                                        <input type="number" step="any" class="form-control" name="minimum_investment" value="{{ old('minimum_investment') }}" required>
                                        <span class="input-group-text">{{ gs('cur_text') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="required">@lang('Maximum Investment')</label>
                                    <div class="input-group">
                                        <input type="number" step="any" class="form-control" name="maximum_investment" value="{{ old('maximum_investment') }}" required>
                                        <span class="input-group-text">{{ gs('cur_text') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Days to Init Payments')</label>
                                    <input type="number" class="form-control" name="days_to_init" min="1" value="{{ old('days_to_init', 1) }}" required>
                                    <small class="text-muted">@lang('Days before starting interest payments')</small>
                                </div>
                            </div>

                            <div class="col-md-6"></div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">@lang('Featured Project')</label>
                                    <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger"
                                        data-bs-toggle="toggle" data-on="@lang('Yes')" data-off="@lang('No')" name="featured" {{ old('featured') ? 'checked' : '' }}>
                                    <small class="form-text text-muted d-block">@lang('Featured projects appear prominently on the homepage')</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">@lang('Testing Mode')</label>
                                    <input type="checkbox" data-width="100%" data-onstyle="-warning" data-offstyle="-secondary"
                                        data-bs-toggle="toggle" data-on="@lang('Yes')" data-off="@lang('No')" name="testing" {{ old('testing') ? 'checked' : '' }}>
                                    <small class="form-text text-muted d-block">@lang('Testing projects are only visible to admins')</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">
                            <i class="las la-save"></i> @lang('Create Project')
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.project.index') }}" />
@endpush
