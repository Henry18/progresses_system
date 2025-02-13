@extends($activeTemplate.'layouts.frontend')
@section('content')
@php
    $bannerCaption = getContent('banner.content',true);
@endphp
<!-- hero-section start -->
<section class="hero-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-7">
                <div class="hero-content">
                    <h2 class="hero__title">{{__(@$bannerCaption->data_values->heading)}}</h2>
                    <p>{{__(strip_tags(@$bannerCaption->data_values->sub_heading))}}</p>

                    <div class="btn-area">

                        @if(@$bannerCaption->data_values->button_link)
                            <a href="{{@$bannerCaption->data_values->button_link}}" class="btn btn-primary">{{@__($bannerCaption->data_values->button_name)}}</a>
                        @endif
                        @if(@$bannerCaption->data_values->button_two_link)
                            <a href="{{@$bannerCaption->data_values->button_two_link}}" class="btn btn-primary">{{@__($bannerCaption->data_values->button_two_name)}}</a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="hero-thumb pulse-animation"><img src="{{frontendImage('banner',@$bannerCaption->data_values->image)}}" alt="image"></div>
            </div>
        </div>
    </div>
</section>
<!-- hero-section end -->
    @if(@$sections->secs != null)
        @foreach(json_decode($sections->secs) as $sec)
            @include($activeTemplate.'sections.'.$sec)
        @endforeach
    @endif
@endsection
