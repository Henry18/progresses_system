@extends('Template::layouts.master')

@section('content')
    <section class="pt-60 pb-60">
        <div class="container">
            <div class="row justify-content-center gy-4">
                <div class="col-md-12">
                    <div class="text-end">
                        <a href="{{ route('user.pool.invests') }}" class="btn btn--base">
                            @lang('My Pools')
                        </a>
                    </div>
                </div>
                @include('Template::partials.pool', ['pools' => $pools])
            </div>
        </div>
    </section>
@endsection
