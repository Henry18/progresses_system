@extends($activeTemplate . 'layouts.master')

@section('content')
    <div class="mt-3">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="text-end">
                    <a href="{{ route('plan') }}" class="btn btn--base">
                        @lang('Investment Plan')
                    </a>
                </div>
            </div>
            @include($activeTemplate . 'partials.invest_history', ['invests' => $invests])
            <div class="col-12">

                @if ($invests->hasPages())
                    <div class="card-footer custom--pagination">
                        {{ paginateLinks($invests) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
