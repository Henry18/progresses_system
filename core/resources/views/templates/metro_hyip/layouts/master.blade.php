@extends($activeTemplate . 'layouts.app')
@section('panel')
    <div class="prgr-layout">
        @include($activeTemplate . 'partials.topnav')
        <div class="prgr-content-area">
            @yield('content')
        </div>
    </div>
@endsection
