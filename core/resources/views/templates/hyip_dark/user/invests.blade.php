@extends('Template::layouts.master')

@section('content')
    <div class="row justify-content-center">
        @include('Template::partials.invest_history',['invests'=>$invests])
        @if ($invests->hasPages())
            {{ paginateLinks($invests) }}
        @endif
    </div>
@endsection