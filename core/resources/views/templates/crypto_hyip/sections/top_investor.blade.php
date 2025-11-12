@php
    $topInvestor = getContent('top_investor.content', true);
    $topInvestorElements = getContent('top_investor.element', false, null, true);
@endphp

<section class="top-investor my-120">
    <div class="container">
        <div class="flex-between gap-3 mb-60">
            <div class="section-heading style-left mb-0">
                <h1 class="section-heading__title wow fadeInUp" data-wow-delay="0.1s">{{ __(@$topInvestor->data_values->heading) }}</h1>
                <p class="section-heading__desc wow fadeInUp" data-wow-delay="0.2s">{{ __(@$topInvestor->data_values->subheading) }}</p>
            </div>
            <div class="top-investor-slider-arrow wow fadeInUp" data-wow-delay="0.3s"></div>
        </div>

        <div class="top-investor-slider">
            @foreach($topInvestorElements as $k => $data)
                <div class="top-investor-item wow fadeInUp" data-wow-delay="0.3s">
                    <h1 class="count">{{ ++$k }}</h1>
                    <h5 class="name">{{ @$data->data_values->name }}</h5>
                    <p class="title">{{ __($data->data_values->designation) }}</p>
                    <h3 class="amount">{{ gs('cur_sym') }}{{ showAmount(@$data->data_values->amount,0, currencyFormat:false) }} </h3>
                </div>
            @endforeach
        </div>
    </div>
</section>