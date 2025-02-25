@php
    $planContent = getContent('plan.content', true);
    $plans = App\Models\Plan::with('timeSetting')
        ->whereHas('timeSetting', function ($time) {
            $time->where('status', 1);
        })
        ->where('status', 1)
        ->where('testing', 0)
        ->get();
@endphp
@extends($activeTemplate . 'layouts.' . $layout)
@section('content')
    <section class="@if($layout == 'frontend') pt-120 pb-120 @else pt-4 pb-60 @endif">
        <div class="@if($layout == 'frontend') container @endif">
            <div class="row justify-content-center gy-4">
                @auth
                    <div class="col-md-12">
                        <div class="text-end">
                            <a href="{{ route('user.invest.statistics') }}" class="btn btn--base">
                                @lang('My Investments')
                            </a>
                        </div>
                    </div>
                @endauth
                @include($activeTemplate.'partials.plan', ['plans' => $plans])
            </div>
        </div>
    </section>

    @guest
        @if ($sections && $sections->secs != null)
            @foreach (json_decode($sections->secs) as $sec)
                @include($activeTemplate . 'sections.' . $sec)
            @endforeach
        @endif
    @endguest

@endsection

