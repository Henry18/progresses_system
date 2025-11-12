@extends('Template::layouts.frontend')

@section('content')
    <section class="blog-detials my-60">
        <div class="container">
            <div class="row gy-5 justify-content-center">
                <div class="col-xl-9 col-lg-8">
                    <div class="blog-details">
                        <div class="blog-details__thumb">
                            <img src="{{ frontendImage('blog', @$blog->data_values->image, '965x500') }}" class="fit-image"
                                alt="image">
                        </div>
                        <div class="blog-details__content">
                            <p class="blog-details__date mb-3">
                                {{ showDateTime(@$blog->created_at, 'M d, Y') }}
                            </p>
                            <h2 class="blog-details__title mb-3">{{ __(@$blog->data_values->title) }}</h2>
                            <div class="blog-details__desc mb-3">
                                @php echo @$blog->data_values->description @endphp
                            </div>
                            <ul class="social-list mt-4">
                                <li class="social-list__item">
                                    <a target="_blank" class="social-list__link flex-center"
                                        href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}">
                                        <i class="fab fa-facebook-f"></i>
                                    </a>
                                </li>
                                <li class="social-list__item">
                                    <a target="_blank" class="social-list__link flex-center"
                                        href="https://twitter.com/intent/tweet?text={{ __(@$blog->data_values->title) }}&amp;url={{ urlencode(url()->current()) }}">
                                        <i class="fa-brands fa-x-twitter"></i>
                                    </a>
                                </li>
                                <li class="social-list__item">
                                    <a target="_blank" class="social-list__link flex-center"
                                        href="http://www.linkedin.com/shareArticle?mini=true&amp;url={{ urlencode(url()->current()) }}&amp;title={{ __(@$blog->data_values->title) }}&amp;summary=dit is de linkedin summary">
                                        <i class="fab fa-linkedin"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="comments-area">
                            <div class="comment-area comments-list">
                                <div class="fb-comments" data-href="{{ url()->current() }}" data-numposts="5"></div>
                            </div>
                        </div><!-- comments-area end -->
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4">
                    <!-- ============================= Blog Details Sidebar Start ======================== -->
                    <div class="blog-sidebar-wrapper">
                        <h5 class="blog-sidebar-wrapper__title"> @lang('Latest blogs') </h5>
                        <div class="blog-sidebar">

                            @foreach ($blogs as $data)
                                <div class="latest-blog">
                                    <div class="latest-blog__thumb">
                                        <a href="{{ route('blog.details', $data->slug) }}">
                                            <img src="{{ frontendImage('blog', 'thumb_' . @$data->data_values->image, '415x270') }}"
                                                class="fit-image" alt="image">
                                        </a>
                                    </div>
                                    <div class="latest-blog__content">
                                        <h6 class="latest-blog__title">
                                            <a
                                                href="{{ route('blog.details', $data->slug) }}">{{ strLimit(@$data->data_values->title, 35) }}</a>
                                        </h6>
                                        <span class="latest-blog__date fs-12">
                                            {{ showDateTime(@$blog->created_at, 'd M Y') }}</span>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                    <!-- ============================= Blog Details Sidebar End ======================== -->
                </div>
            </div>
        </div>
    </section>
@endsection

@push('fbComment')
    @php echo loadExtension('fb-comment') @endphp
@endpush
