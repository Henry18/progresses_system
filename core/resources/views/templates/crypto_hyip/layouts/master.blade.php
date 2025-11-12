@extends('Template::layouts.app')

@section('panel')
    <div class="dashboard position-relative">
        <div class="dashboard__inner flex-wrap">

            @include('Template::partials.user_sidebar')

            <div class="dashboard__right">

                @include('Template::partials.user_header')

                <div class="dashboard-body">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
@endsection
