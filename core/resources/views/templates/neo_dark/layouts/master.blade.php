@extends('Template::layouts.app')
@section('panel')


    <main class="color-version-one">
        @include('Template::partials.user_header')
        @if (!request()->routeIs('home'))
            @include('Template::partials.breadcrumb')
        @endif
        @include('Template::partials.sidebar')
        @yield('content')
        @include('Template::partials.footer')
    </main>
@endsection
