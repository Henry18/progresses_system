@extends($activeTemplate.'layouts.frontend')
@section('content')
@php
    $banner = getContent('banner.content',true);
@endphp
<script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
<script src="https://threejs.org/examples/js/libs/stats.min.js"></script>
<!--========================== Banner Section Start ==========================-->
<section class="banner-section"">
<div id="particles-js"></div>
  <div class="planet-bg">
      <img src="{{ asset($activeTemplateTrue . 'images/shapes/planet-bg.png') }}" alt="">
  </div>
  <div class="planet-small">
      <img src=" {{ asset($activeTemplateTrue . 'images/shapes/planet-small.png') }}" alt="">
  </div>
  <span class="banner-section__icon animated">
    <i class="las la-star"></i>
</span>
<span class="banner-section__icon-one animated">
    <i class="las la-star"></i>
</span>
<span class="banner-section__icon-two animated">
    <i class="las la-star"></i>
</span>
<span class="banner-section__icon-three animated">
    <i class="las la-star"></i>
</span>
<span class="banner-section__icon-four animated">
    <i class="las la-star"></i>
</span> 
<span class="banner-section__icon-five animated">
    <i class="las la-star"></i>
</span>
<span class="banner-section__icon-six animated">
    <i class="las la-star"></i>
</span>
<span class="banner-section__icon-seven animated">
    <i class="las la-star"></i>
</span>
<div class="container">
    <div class="row justify-content-center align-items-center">
        <div class="col-xl-5 col-md-7">
            <div class="banner-content">
                <h2 class="banner-content__title">  {{ __(@$banner->data_values->heading) }}</h2>
                <p>{{ __(@$banner->data_values->sub_heading) }}</p>
                <!--<div class="banner-content__buttons">
                    <a href="{{ @$banner->data_values->button_link }}" class="btn btn--base">
                        {{ __(@$banner->data_values->button_name) }}</a>
                    <a href="{{@$banner->data_values->button_two_link }}" class="btn btn--base">
                        {{ __(@$banner->data_values->button_two_name) }}</a>
                </div>
                -->
            </div>
        </div>
        <div class="col-xl-7 col-md-5">
          <div class="city-scene">
            <!--
              <div class="bd-1">
                  <img src="{{ asset($activeTemplateTrue . 'images/shapes/bd-1-4.png') }}">
              </div>
               <div class="bd-2">
                  <img src="{{ asset($activeTemplateTrue . 'images/shapes/bd-2.png') }}">
              </div>
            <div class="bd-3">
                  <img src="{{ asset($activeTemplateTrue . 'images/shapes/bd-3-10.png') }}">
              </div>
              <div class="bd-4">
                  <img src="{{ asset($activeTemplateTrue . 'images/shapes/bd-1-4.png') }}">
              </div>
              <div class="bd-5">
                  <img src="{{ asset($activeTemplateTrue . 'images/shapes/bd-5.png') }}">
              </div>
            <div class="bd-6">
                  <img src="{{ asset($activeTemplateTrue . 'images/shapes/bd-6.png') }}">
              </div>
              <div class="bd-7">
                  <img src="{{ asset($activeTemplateTrue . 'images/shapes/bd-7.png') }}">
              </div>
              <div class="bd-8">
                  <img src="{{ asset($activeTemplateTrue . 'images/shapes/bd-3-10.png') }}">
              </div>
              <div class="bd-9">
                  <img src="{{ asset($activeTemplateTrue . 'images/shapes/bd-9-11.p') }}ng">
              </div>
              <div class="bd-10">
                  <img src="{{ asset($activeTemplateTrue . 'images/shapes/bd-8.png') }}">
              </div>
              <div class="bd-11">
                  <img src="{{ asset($activeTemplateTrue . 'images/shapes/bd-9-11.png') }}">
              </div>
              <div class="bd-12">
                  <img src="{{ asset($activeTemplateTrue . 'images/shapes/bd-12.png') }}">
              </div>
              <div class="bd-13">
                  <img src="{{ asset($activeTemplateTrue . 'images/shapes/bd-13.png') }}">
              </div>
              <div class="bd-14">
                  <img src="{{ asset($activeTemplateTrue . 'images/shapes/bd-14.png') }}">
              </div>
              <div class="bd-15">
                  <img src="{{ asset($activeTemplateTrue . 'images/shapes/london-bridge-14.png') }}" >
              </div>
              <div class="bd-16">
                  <img src="{{ asset($activeTemplateTrue . 'images/shapes/bd-16.png') }}">
              </div>
              <div class="bd-17">
                  <img src="{{ asset($activeTemplateTrue . 'images/shapes/bd-17.png') }}">
              </div>
             <div class="bd-18">
                  <img src="{{ asset($activeTemplateTrue . 'images/shapes/bd-18.png') }}">
              </div>
              <div class="bd-19">
                  <img src="{{ asset($activeTemplateTrue . 'images/shapes/bd-19.png') }}">
              </div>
              <div class="bd-20">
                  <img src="{{ asset($activeTemplateTrue . 'images/shapes/bd-20.png') }}">
              </div>
              -->
          </div>
        </div>
    </div>
</div>
</section>
<!--========================== Banner Section End ==========================-->
<!-- =========================start train section =============================-->

<div class="train-section">
  <div class="train-wrapper">
      <div class="train" bg="{{ asset($activeTemplateTrue . 'images/shapes/train.png') }}">
          <img class="imgnave" src="{{ asset($activeTemplateTrue . 'images/shapes/nave.svg') }}" alt="">
      </div>
  </div>
</div>
<style>
    body{ overflow-x:hidden } 
canvas{ display: block; vertical-align: bottom; } 
#particles-js{ position:absolute; width: 100%; height: 100%; background-repeat: no-repeat; background-size: 20%; background-position: 50% 50%; }
</style>
<script>
  document.addEventListener("DOMContentLoaded", function () {
      if (typeof particlesJS !== "undefined") {
        particlesJS("particles-js", {"particles":{"number":{"value":76,"density":{"enable":true,"value_area":315.65905665290904}},"color":{"value":"#ffffff"},"shape":{"type":"star","stroke":{"width":0,"color":"#000000"},"polygon":{"nb_sides":5},"image":{"src":"img/github.svg","width":100,"height":100}},"opacity":{"value":1,"random":true,"anim":{"enable":true,"speed":1,"opacity_min":0,"sync":false}},"size":{"value":2,"random":true,"anim":{"enable":false,"speed":4,"size_min":0.3,"sync":false}},"line_linked":{"enable":false,"distance":150,"color":"#ffffff","opacity":0.4,"width":1},"move":{"enable":true,"speed":1,"direction":"none","random":true,"straight":false,"out_mode":"out","bounce":false,"attract":{"enable":false,"rotateX":600,"rotateY":600}}},"interactivity":{"detect_on":"canvas","events":{"onhover":{"enable":true,"mode":"bubble"},"onclick":{"enable":true,"mode":"repulse"},"resize":true},"modes":{"grab":{"distance":400,"line_linked":{"opacity":1}},"bubble":{"distance":250,"size":0,"duration":2,"opacity":0,"speed":3},"repulse":{"distance":400,"duration":0.4},"push":{"particles_nb":4},"remove":{"particles_nb":2}}},"retina_detect":true});var count_particles, stats, update; stats = new Stats; stats.setMode(0); stats.domElement.style.position = 'absolute'; stats.domElement.style.left = '0px'; stats.domElement.style.top = '0px'; document.body.appendChild(stats.domElement); count_particles = document.querySelector('.js-count-particles'); update = function() { stats.begin(); stats.end(); if (window.pJSDom[0].pJS.particles && window.pJSDom[0].pJS.particles.array) { count_particles.innerText = window.pJSDom[0].pJS.particles.array.length; } requestAnimationFrame(update); }; requestAnimationFrame(update);;
      } else {
          console.error("particles.js no se ha cargado correctamente.");
      }
  });
</script>

<!-- ========================end train section =========================-->

    @if($sections && $sections->secs != null)
        @foreach(json_decode($sections->secs) as $sec)
            @include($activeTemplate.'sections.'.$sec)
        @endforeach
    @endif
@endsection
