@php
    $faq = getContent('faq.content', true);
    $faqElements = getContent('faq.element', orderById:true);
@endphp

<section class="faq-section my-120">
    <div class="container">
        <div class="row gy-5 align-items-center">
            <div class="col-lg-6">
                <div class="faq-section__thumb wow fadeInUp" data-wow-delay="0.1s">
                    <img src="{{ frontendImage('faq', @$faq->data_values->image, '835x570') }}" alt="faq">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="section-heading style-left wow fadeInUp" data-wow-delay="0.1s">
                    <h1 class="section-heading__title">{{ __(@$faq->data_values->heading) }}</h1>
                    <p class="section-heading__desc wow fadeInUp" data-wow-delay="0.2s">
                        {{ __(@$faq->data_values->subheading) }}
                    </p>
                </div>
                <div class="custom--accordion accordion wow fadeInUp" data-wow-delay="0.3s" id="accordionExample">
                    @foreach($faqElements as $faqElement)
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapseFour{{ $loop->index }}"
                                    aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="collapseFour{{ $loop->index }}">
                                    {{ __(@$faqElement->data_values->question) }}
                                </button>
                            </h2>
                            <div id="collapseFour{{ $loop->index }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <p>{{ __(@$faqElement->data_values->answer) }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>