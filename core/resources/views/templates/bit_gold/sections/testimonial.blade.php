@php
$testimonialCaption = getContent('testimonial.content',true);
$testimonialElements = getContent('testimonial.element',false,null,true);
@endphp
<!-- testimonial section start -->
  <section class="pt-120 pb-120 bg_img overlay--radial" data-background="{{ frontendImage('testimonial', @$testimonialCaption->data_values->image) }}">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-6 text-center">
          <div class="section-header">
            <h2 class="section-title"><span class="font-weight-normal">{{ __(@$testimonialCaption->data_values->heading_w) }}</span> <b class="base--color">{{ __(@$testimonialCaption->data_values->heading_c) }}</b></h2>
            <p>{{ __(@$testimonialCaption->data_values->sub_heading) }}</p>
          </div>
        </div>
      </div><!-- row end -->
      <div class="row">
        <div class="col-lg-12">
          <div class="testimonial-slider">
            @foreach($testimonialElements as $testimonialElement)
            <div class="single-slide">
              <div class="testimonial-card">
                <div class="testimonial-card__content">
                  <p>{{ __(@$testimonialElement->data_values->quote) }}</p>
                </div>
                <div class="testimonial-card__client">
                  <div class="thumb">
                    <img src="{{ frontendImage('testimonial', @$testimonialElement->data_values->image,'69x75') }}" alt="image">
                  </div>
                  <div class="content">
                    <h6 class="name">{{ __(@$testimonialElement->data_values->name) }}</h6>
                    <span class="designation">{{ __(@$testimonialElement->data_values->designation) }}</span>
                  </div>
                </div>
              </div><!-- testimonial-card end -->
            </div>
            @endforeach
          </div>
        </div>
      </div><!-- row end -->
    </div>
  </section>
  <!-- testimonial section end -->
