@php
  $howWorkCaption = getContent('how_work.content',true);
  $howWorElements = getContent('how_work.element',null,false,true);
@endphp
<section class="pt-120 pb-120 bg_img" data-background="{{ frontendImage('how_work', @$howWorkCaption->data_values->image,'1920x832') }}">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-6 text-center">
            <div class="section-header">
              <h2 class="section-title"><span class="font-weight-normal">{{ __(@$howWorkCaption->data_values->heading_w_1) }}</span> <b class="base--color">{{ __(@$howWorkCaption->data_values->heading_c) }}</b> <span class="font-weight-normal">{{ __(@$howWorkCaption->data_values->heading_w_2) }}</span></h2>
              <p>{{ __(@$howWorkCaption->data_values->sub_heading) }}</p>
            </div>
          </div>
        </div><!-- row end -->
        <div class="row justify-content-center gy-5">
          @foreach($howWorElements as $howWorElement)
          <div class="col-lg-4 col-md-6 work-item">
            <div class="work-card text-center">
              <div class="work-card__icon base--color">
                @php echo @$howWorElement->data_values->icon @endphp
                <span class="step-number">{{ __($loop->iteration) }}</span>
              </div>
              <div class="work-card__content">
                <h4 class="base--color">{{ __(@$howWorElement->data_values->title) }}</h4>
              </div>
            </div><!-- work-card end -->
          </div>
          @endforeach
        </div>
      </div>
</section>
