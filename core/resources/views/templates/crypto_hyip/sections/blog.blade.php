@php
    $blog = getContent('blog.content', true);
    $blogElements = getContent('blog.element', false, 3);
@endphp

<!-- ==================== Blog Start Here ==================== -->
<section class="blog my-120">
    <div class="container">
        <div class="section-heading">
            <h1 class="section-heading__title wow fadeInUp" data-wow-delay="0.1s">{{ __(@$blog->data_values->heading) }}
            </h1>
            <p class="section-heading__desc wow fadeInUp" data-wow-delay="0.2s">{{ __(@$blog->data_values->subheading) }}
            </p>
        </div>
        <div class="row gy-4 justify-content-center">
            @foreach ($blogElements as $blogElement)
                <div class="col-lg-4 col-md-6">
                    <div class="blog-item wow fadeInUp" data-wow-delay="0.3s">
                        <div class="blog-item__thumb">
                            <a href="{{ route('blog.details', $blogElement->slug) }}" class="link">
                                <img src="{{ frontendImage('blog', 'thumb_' . @$blogElement->data_values->image, '415x270') }}"
                                    class="fit-image" alt="image">
                            </a>
                        </div>
                        <div class="blog-item__content">
                            <p class="blog-item__date">{{ showDateTime($blogElement->created_at, 'M d, Y') }}</p>
                            <h5 class="blog-item__title">
                                <a href="{{ route('blog.details', $blogElement->slug) }}" class="link">
                                    {{ strLimit(__(@$blogElement->data_values->title), 55) }}
                                </a>
                            </h5>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
<!-- ==================== Blog End Here ==================== -->
