@php
  $filosofia = getContent('filosofia.content',true);
  $filosofiaElement = getContent('filosofia.element',null,false,true);
@endphp

<section class="program-section pt-120">
  <div class="program-section__shape">
      <img src="{{ asset($activeTemplateTrue . 'images/shapes/program.png') }}" alt="">
  </div>
  <div class="container">
      <div class="row align-items-center gy-4">
          <div class="col-lg-6 col-md-6 ">
              <div class="section-heading">
                  <span class="section-heading__subtitle"> {{ __(@$filosofia->data_values->sub_heading) }} </span>
                  <h2 class="section-heading__title">{{ __(@$filosofia->data_values->heading) }} </h2>
             </div>
            <div class="program-thumb">
              <iframe class="elementor-video" frameborder="0" allowfullscreen="" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" title="Como utilizar paso a paso el nuevo backoffice. de la plataforma progresses." width="100%" height="360" src="{{ __(@$filosofia->data_values->url_video) }}" id="widget2"></iframe>  
            </div>
          </div>
          <div class="col-lg-6 col-md-6 ps-lg-5">
          @foreach ($filosofiaElement as $item)
             <div class="program-item">
                <span class="program-item__icon">
                 @php echo @$item->data_values->icon @endphp
                </span>
                <div class="program-item__content">
                  <h5 class="program-item__title"> {{ __(@$item->data_values->title) }} </h5>
                  <p class="program-item__desc"> {{ __(@$item->data_values->content) }} </p>
                </div>
             </div>
             @endforeach
          </div> 
      </div>
  </div>
</section>
