@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('admin.project.update', $project->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Project Name')</label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name', $project->name) }}" required />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Description')</label>
                                    <textarea class="form-control" name="description" rows="6" required>{{ old('description', $project->description) }}</textarea>
                                    <small class="text-muted">@lang('Describe the investment project, its goals, and expected returns.')</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Project Image')</label>
                                    <input type="file" class="form-control" name="image" accept="image/*" />
                                    <small class="text-muted">@lang('Leave empty to keep current image. Recommended: 800x600px. Max: 2MB')</small>
                                    @if($project->image)
                                        <div class="mt-2">
                                            <img src="{{ getImage(getFilePath('projectImage') . '/' . $project->image, getFileSize('projectImage')) }}" alt="Project Image" style="max-width: 200px; max-height: 150px;" class="img-thumbnail">
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Project PDF') <span class="text-muted">@lang('(Optional)')</span></label>
                                    <input type="file" class="form-control" name="pdf" accept=".pdf" />
                                    <small class="text-muted">@lang('Leave empty to keep current PDF. Max: 10MB')</small>
                                    @if($project->pdf)
                                        <div class="mt-2">
                                            <a href="{{ getImage(getFilePath('projectFile') . '/' . $project->pdf) }}" target="_blank" class="btn btn-sm btn-outline--primary">
                                                <i class="las la-file-pdf"></i> @lang('View Current PDF')
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="required">@lang('Minimum Investment')</label>
                                    <div class="input-group">
                                        <input type="number" step="any" class="form-control" name="minimum_investment" value="{{ old('minimum_investment', $project->minimum_investment) }}" required>
                                        <span class="input-group-text">{{ gs('cur_text') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="required">@lang('Maximum Investment')</label>
                                    <div class="input-group">
                                        <input type="number" step="any" class="form-control" name="maximum_investment" value="{{ old('maximum_investment', $project->maximum_investment) }}" required>
                                        <span class="input-group-text">{{ gs('cur_text') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Days to Init Payments')</label>
                                    <input type="number" class="form-control" name="days_to_init" min="1" value="{{ old('days_to_init', $project->days_to_init) }}" required>
                                    <small class="text-muted">@lang('Days before starting interest payments')</small>
                                </div>
                            </div>

                            <div class="col-md-6"></div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">@lang('Featured Project')</label>
                                    <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger"
                                        data-bs-toggle="toggle" data-on="@lang('Yes')" data-off="@lang('No')" name="featured" {{ old('featured', $project->featured) ? 'checked' : '' }}>
                                    <small class="form-text text-muted d-block">@lang('Featured projects appear prominently on the homepage')</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">@lang('Testing Mode')</label>
                                    <input type="checkbox" data-width="100%" data-onstyle="-warning" data-offstyle="-secondary"
                                        data-bs-toggle="toggle" data-on="@lang('Yes')" data-off="@lang('No')" name="testing" {{ old('testing', $project->testing) ? 'checked' : '' }}>
                                    <small class="form-text text-muted d-block">@lang('Testing projects are only visible to admins')</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">
                            <i class="las la-save"></i> @lang('Update Project')
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.project.index') }}" />
    <a href="{{ route('admin.project.show', $project->id) }}" class="btn btn-sm btn-outline--info">
        <i class="las la-eye"></i> @lang('View Details')
    </a>
@endpush
