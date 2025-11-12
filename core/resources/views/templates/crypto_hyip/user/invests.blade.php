@extends('Template::layouts.master')

@section('content')
    <div class="row justify-content-center">
        @include('Template::partials.invest_history', ['invests' => $invests])
        @if ($invests->hasPages())
            <div class="pagination-wrapper">
                {{ paginateLinks($invests) }}
            </div>
        @endif
    </div>
@endsection
