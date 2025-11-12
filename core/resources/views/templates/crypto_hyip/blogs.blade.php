@extends('Template::layouts.frontend')

@section('content')
    <section class="blog my-120">
        <div class="container">
            <div class="row gy-4 justify-content-center">
                @forelse ($blogs as $k => $data)
                    <div class="col-lg-4 col-md-6">
                        <div class="blog-item wow fadeInUp" data-wow-delay="0.1s">
                            <div class="blog-item__thumb">
                                <a href="{{ route('blog.details', $data->slug) }}" class="link">
                                    <img src="{{ frontendImage('blog', 'thumb_' . @$data->data_values->image, '415x270') }}"
                                        class="fit-image" alt="image">
                                </a>
                            </div>
                            <div class="blog-item__content">
                                <p class="blog-item__date">{{ showDateTime($data->created_at, 'M d, Y') }}</p>
                                <h5 class="blog-item__title">
                                    <a href="{{ route('blog.details', $data->slug) }}" class="link">
                                        {{ strLimit(__(@$data->data_values->title), 55) }}
                                    </a>
                                </h5>
                            </div>
                        </div>
                    </div>
                @empty
                    @include('Template::partials.empty')
                @endforelse
            </div>
            @if ($blogs->hasPages())
                <div class="pagination-wrapper">
                    {{ paginateLinks($blogs) }}
                </div>
            @endif
        </div>
    </section>

    @if ($sections != null)
        @foreach (json_decode($sections) as $sec)
            @include('Template::sections.' . $sec)
        @endforeach
    @endif
@endsection
