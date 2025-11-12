@extends('Template::layouts.app')

@section('panel')
    <main>
        @include('Template::partials.header')

        @if (request()->routeIs('home'))
            @include('Template::partials.banner')
        @else
            @include('Template::partials.breadcrumb') 
        @endif
        
        @yield('content')

        @include('Template::partials.footer')
    </main>
@endsection
