@extends($activeTemplate . 'layouts.master')
@section('content')
    <section class="pt-3">
        <div class="row justify-content-center gy-4">
        <style>#dexscreener-embed{position:relative;width:100%;padding-bottom:125%;}@media(min-width:1400px){#dexscreener-embed{padding-bottom:65%;}}#dexscreener-embed iframe{position:absolute;width:100%;height:100%;top:0;left:0;border:0;}</style><div id="dexscreener-embed"><iframe src="https://dexscreener.com/bsc/0x6931f1BFFf484C708a1f0566C52c586E0824d5e3?embed=1&loadChartSettings=0&trades=0&tabs=0&info=0&chartLeftToolbar=0&chartDefaultOnMobile=1&chartTheme=dark&theme=dark&chartStyle=0&chartType=usd&interval=15"></iframe></div>
        </div>
    </section>
@endsection

@push('style')
    <style>
        textarea.form--control {
            min-height: auto !important;
        }
    </style>
@endpush
