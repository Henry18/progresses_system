@php
    $howItWork = getContent('how_it_work.content', true);
    $howItWorkElements = getContent('how_it_work.element', orderById:true);
@endphp

<section class="how-work my-120">
    <div class="container">
        <div class="section-heading">
            <h1 class="section-heading__title wow fadeInUp" data-wow-delay="0.1s">{{ __(@$howItWork->data_values->heading) }}</h1>
            <p class="section-heading__desc wow fadeInUp" data-wow-delay="0.2s">
                {{ __(@$howItWork->data_values->subheading) }}
            </p>
        </div>
        <div class="row gy-5">
            @foreach($howItWorkElements as $howItWorkElement)
                <div class="col-sm-6 col-xl-3">
                    <div class="how-work-card wow fadeInUp" data-wow-delay="0.3s">
                        <h5 class="how-work-card__title">{{ __(@$howItWorkElement->data_values->title) }}</h5>
                        <p class="how-work-card__desc">
                            {{ __(@$howItWorkElement->data_values->description) }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>