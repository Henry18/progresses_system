@extends('Template::layouts.app')
@section('panel')
    <div class="page-wrapper">
        @include('Template::partials.user_header')
        @if (!request()->routeIs('home'))
            @include('Template::partials.breadcrumb')
        @endif
        <div class="section-wrapper">
            @yield('content')
        </div>
        @include('Template::partials.footer')
    </div>
@endsection
