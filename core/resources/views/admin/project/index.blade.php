@extends('admin.layouts.app')

@section('panel')
<div class="row">
  <div class="col-lg-12">
    <div class="card b-radius--10">
      <div class="card-body p-0">
        <div class="table-responsive--md table-responsive">
          <table class="table table--light style--two">
            <thead>
              <tr>
                <th>@lang('Project')</th>
                <th>@lang('Investment Range')</th>
                <th>@lang('Plans')</th>
                <th>@lang('Featured')</th>
                <th>@lang('Testing')</th>
                <th>@lang('Status')</th>
                <th>@lang('Action')</th>
              </tr>
            </thead>
            <tbody>
              @forelse($projects as $project)
              <tr>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="avatar avatar--sm me-2">
                      <img src="{{ getImage(getFilePath('projectImage') . '/' . $project->image, getFileSize('projectImage')) }}" alt="@lang('Project Image')">
                    </div>
                    <span class="fw-bold">{{ __($project->name) }}</span>
                  </div>
                </td>
                <td>
                  {{ showAmount($project->minimum_investment) }} - {{ showAmount($project->maximum_investment) }}
                </td>
                <td>
                  <a href="{{ route('admin.project.show', $project->id) }}" class="badge badge--primary">
                    {{ $project->plans_count }} @lang('Plans')
                  </a>
                </td>
                <td>
                  @if ($project->featured == 1)
                  <span class="badge badge--success">@lang('Yes')</span>
                  @else
                  <span class="badge badge--dark">@lang('No')</span>
                  @endif
                </td>
                <td>
                  @if ($project->testing == 1)
                  <span class="badge badge--warning">@lang('Yes')</span>
                  @else
                  <span class="badge badge--dark">@lang('No')</span>
                  @endif
                </td>
                <td>
                  @php echo $project->statusBadge @endphp
                </td>
                <td>
                  <div class="button-group">
                    <a href="{{ route('admin.project.show', $project->id) }}" class="btn btn-sm btn-outline--primary me-1" data-bs-toggle="tooltip" title="@lang('View Details')">
                      <i class="la la-eye"></i> @lang('Details')
                    </a>
                    <a href="{{ route('admin.project.edit', $project->id) }}" class="btn btn-sm btn-outline--primary me-1" data-bs-toggle="tooltip" title="@lang('Edit')">
                      <i class="la la-pen"></i>
                    </a>

                    @if($project->status == 1)
                    <button type="button" class="btn btn-sm btn-outline--danger confirmationBtn" data-action="{{ route('admin.project.status', $project->id) }}" data-question="@lang('Are you sure to disable this project?')" data-bs-toggle="tooltip" title="@lang('Disable')">
                      <i class="la la-eye-slash"></i>
                    </button>
                    @else
                    <button type="button" class="btn btn-sm btn-outline--success confirmationBtn" data-action="{{ route('admin.project.status', $project->id) }}" data-question="@lang('Are you sure to enable this project?')" data-bs-toggle="tooltip" title="@lang('Enable')">
                      <i class="la la-eye"></i>
                    </button>
                    @endif
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
      @if ($projects->hasPages())
      <div class="card-footer py-4">
        {{ paginateLinks($projects) }}
      </div>
      @endif
    </div>
  </div>
</div>

<x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
<a href="{{ route('admin.project.create') }}" class="btn btn-sm btn--primary">
  <i class="las la-plus"></i>@lang('Add New Project')
</a>
@endpush
