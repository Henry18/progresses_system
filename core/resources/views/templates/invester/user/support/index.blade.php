@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="dashboard-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="text-end mb-3 d-flex flex-wrap justify-content-between gap-1">
                    <h3>{{ __($pageTitle) }}</h3>
                    <a href="{{ route('ticket.open') }}" class="btn btn--base btn--smd">@lang('Open Support Ticket')</a>
                </div>
                <div class="card">
                    @if (!blank($supports))
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table--responsive--md">
                                    <thead>
                                        <tr>
                                            <th>@lang('Subject')</th>
                                            <th>@lang('Status')</th>
                                            <th>@lang('Priority')</th>
                                            <th>@lang('Last Reply')</th>
                                            <th>@lang('Action')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($supports as $support)
                                            <tr>
                                                <td> <a href="{{ route('ticket.view', $support->ticket) }}" class="fw-bold"> [@lang('Ticket')#{{ $support->ticket }}]
                                                        {{ __($support->subject) }} </a></td>
                                                <td>
                                                    @php echo $support->statusBadge; @endphp
                                                </td>
                                                <td>
                                                    @if ($support->priority == 1)
                                                        <span class="badge badge--dark">@lang('Low')</span>
                                                    @elseif($support->priority == 2)
                                                        <span class="badge badge--success">@lang('Medium')</span>
                                                    @elseif($support->priority == 3)
                                                        <span class="badge badge--primary">@lang('High')</span>
                                                    @endif
                                                </td>
                                                <td> {{ diffForHumans($support->last_reply) }} </td>
                                                <td>
                                                    <a href="{{ route('ticket.view', $support->ticket) }}" class="btn btn--icon btn--primary">
                                                        <i class="fa fa-desktop"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="card-body text-center">
                            <h4 class="text--muted"><i class="far fa-frown"></i> {{ __($emptyMessage) }}</h4>
                        </div>
                    @endif

                    @if ($supports->hasPages())
                        <div class="custom--pagination">
                            {{ paginateLinks($supports) }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection
