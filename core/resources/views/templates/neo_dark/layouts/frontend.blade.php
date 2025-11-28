@extends('Template::layouts.app')
@section('panel')

<main class="color-version-one">
    @include('Template::partials.header')
    @if(!request()->routeIs('home'))
    @include('Template::partials.breadcrumb')
    @endif
    @yield('content')
    @include('Template::partials.footer')
</main>

@endsection
