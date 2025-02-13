@php
    $cta = getContent('cta.content', true);
@endphp
<section class="pb-120">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-8">
                <div class="cta-wrapper bg_img border-radius--10 text-center" data-background="{{ frontendImage('cta', @$cta->data_values->image, '1920x1097') }}">
                    <h2 class="title mb-3">{{ __(@$cta->data_values->heading) }}</h2>
                    <p>{{ __(@$cta->data_values->sub_heading) }}</p>
                    <a href="{{ __(@$cta->data_values->button_url) }}" class="btn--base mt-4">{{ __(@$cta->data_values->button_name) }}</a>
                </div>
            </div>
        </div>
    </div>
</section>
