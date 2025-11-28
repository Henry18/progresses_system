@php
    $whyChoose = getContent('why_choose.content', true);
    $whyChooseElements = getContent('why_choose.element', orderById:true);
@endphp

<section class="why-choose-section my-120">
    <div class="container">
        <div class="row gy-5 align-items-center flex-lg-row-reverse">
            <div class="col-lg-6">
                <div class="why-choose-image wow fadeInRight" data-wow-delay="0.1s">
                    <img src="{{ frontendImage('why_choose', @$whyChoose->data_values->image, '570x600') }}" alt="@lang('why-choose')">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="section-heading style-left mb-0">
                    <h1 class="section-heading__title wow fadeInUp" data-wow-delay="0.1s">
                        {{ __(@$whyChoose->data_values->heading) }}
                    </h1>
                    <p class="section-heading__desc text--body wow fadeInUp" data-wow-delay="0.2s">
                        {{ __(@$whyChoose->data_values->subheading) }}
                    </p>
                </div>
                <div class="why-choose-list">
                    @foreach($whyChooseElements as $whyChooseElement)
                        <div class="why-choose-item wow fadeInUp" data-wow-delay="0.3s">
                            <span class="icon">
                                @php echo @$whyChooseElement->data_values->icon; @endphp
                            </span>
                            <p class="title">{{ __(@$whyChooseElement->data_values->title) }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="why-choose-statistics">
                    <div class="why-choose-statistics-item wow fadeInUp" data-wow-delay="0.3s">
                        <h1 class="count"> <span class="text-black">{{ __(@$whyChoose->data_values->first_counter_prefix) }}</span>{{ __(@$whyChoose->data_values->first_counter_digit) }}</h1>
                        <h4 class="title">{{ __(@$whyChoose->data_values->first_counter_text) }}</h4>
                    </div>
                    <div class="why-choose-statistics-item wow fadeInUp" data-wow-delay="0.3s">
                        <h1 class="count"> <span class="text-black">{{ __(@$whyChoose->data_values->second_counter_prefix) }}</span>{{ __(@$whyChoose->data_values->second_counter_digit) }}</h1>
                        <h4 class="title">{{ __(@$whyChoose->data_values->second_counter_text) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>