@php
    $breadcrumb = getContent('breadcrumb.content', true);
@endphp

<section class="breadcrumb">
    <div class="breadcrumb-group"></div>
    <div class="breadcrumb-group-two"></div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="breadcrumb__wrapper">
                    <h1 class="breadcrumb__title wow fadeInUp" data-wow-delay="0.1s">{{ __($pageTitle) }}</h1>
                    <ul class="breadcrumb__list wow fadeInUp" data-wow-delay="0.2s">
                        <li class="breadcrumb__item">
                            <a href="{{ route('home') }}" class="breadcrumb__link">@lang('Home')</a> 
                        </li>
                        <li class="breadcrumb__item">/</li>
                        <li class="breadcrumb__item"> 
                            <span class="breadcrumb__item-text"> 
                                @if(request()->routeIs('blog.details'))
                                    @lang('Blog Details')
                                @else 
                                    {{ __($pageTitle) }} 
                                @endif
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

@push('style')
    <style>
        .breadcrumb::before {
            background-image: url("{{ frontendImage('breadcrumb', @$breadcrumb->data_values->image, '1920x360') }}");
        }
    </style>
@endpush
