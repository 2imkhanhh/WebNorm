@extends('layouts.app')

@section('title', ($blog ? $blog->getTitle() . ' | ' : '') . __('site.title_news_detail'))
@section('description', $blog && $blog->getExcerpt() ? \Illuminate\Support\Str::limit(strip_tags($blog->getExcerpt()), 160) : __('site.meta_description_default'))
@section('body_class', 'news-detail-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/news-detail.css') }}?v=2">
@endpush

@section('content')
    <section class="article-section">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12 col-md-10 col-lg-7">

                    <div class="article-header">
                        <p class="article-date">
                            @if($blog && $blog->published_at)
                                {{ app()->getLocale() === 'en' ? $blog->published_at->format('F d, Y') : $blog->published_at->format('d \T\h\á\n\g m Y') }}
                            @else
                                29 Tháng 01 2026
                            @endif
                        </p>
                        <h1 class="article-title">{{ $blog ? $blog->getTitle() : 'Hiểu về tư duy thiết kế (design thinking) để nổi bật trong ngành công nghiệp sáng tạo' }}</h1>
                    </div>

                    <div class="article-meta-bar d-flex flex-wrap justify-content-between align-items-center">

                        <div class="author-info d-flex align-items-center">
                            <img src="{{ !empty($blog->author_avatar) ? (str_starts_with($blog->author_avatar, 'http') ? $blog->author_avatar : asset(ltrim($blog->author_avatar, '/'))) : asset('assets/images/avatar.png') }}" alt="{{ $blog && $blog->author ? $blog->author : 'Nguyễn Minh Anh' }}" class="author-avatar">
                            <div class="author-text">
                                <h4 class="author-name">{{ $blog && $blog->author ? $blog->author : 'Nguyễn Minh Anh' }}</h4>
                                <p class="author-role">{{ $blog && $blog->getAuthorRole() ? $blog->getAuthorRole() : __('site.default_author_role') }}</p>
                            </div>
                        </div>

                        <div class="share-actions d-flex align-items-center">
                            <span class="share-text">{{ __('site.news_share') }}</span>
                            <a href="#" class="share-icon"><i class='bx bxl-linkedin'></i></a>
                            <a href="#" class="share-icon text-icon">X</a>
                            <a href="#" class="share-icon"><i class='bx bxl-facebook'></i></a>
                        </div>

                    </div>

                    <div class="article-content">
                        @php
                            $articleBody = $blog ? $blog->getContent() : '';
                        @endphp
                        @if(!empty($articleBody))
                            {!! \App\Helpers\NormHelper::sanitizeHtml($articleBody) !!}
                        @else
                            <p>{{ __('site.news_content_updating') }}</p>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </section>

    <section class="blog-slider-section">
        <div class="container-fluid px-4 px-md-5">
            <div class="d-flex justify-content-end mb-4">
                <a href="{{ \App\Helpers\NormHelper::localizedRoute('news') }}" class="link-more text-uppercase" style="color: #111; margin-top: 100px;">{{ __('site.news_more_articles') }}</a>
            </div>

            <div class="blog-slider-container">
                <div class="blog-track" id="blogTrack">
                    @forelse($relatedBlogs as $rel)
                    @php
                        $relThumb = !empty($rel->thumbnail) ? (str_starts_with($rel->thumbnail, 'http') ? $rel->thumbnail : asset(ltrim($rel->thumbnail, '/'))) : asset('assets/images/project1.png');
                    @endphp
                    <div class="blog-card">
                        <div class="blog-img-wrap">
                            <a href="{{ \App\Helpers\NormHelper::localizedRoute('news.detail', $rel->slug) }}" class="d-block w-100 h-100">
                                <img src="{{ $relThumb }}" alt="{{ $rel->getTitle() }}">
                            </a>
                        </div>
                        <h3 class="blog-title">
                            <a href="{{ \App\Helpers\NormHelper::localizedRoute('news.detail', $rel->slug) }}" class="text-decoration-none" style="color: inherit;">
                                {{ $rel->getTitle() }}
                            </a>
                        </h3>
                    </div>
                    @empty
                    @endforelse
                </div>
            </div>

            <div class="blog-nav-btns mt-5 d-flex justify-content-end gap-3 pe-lg-4">
                <button class="nav-btn blog-prev" id="blogPrev" ontouchstart="">
                    <i class='bx bx-left-arrow-alt'></i>
                </button>

                <button class="nav-btn blog-next" id="blogNext" ontouchstart="">
                    <i class='bx bx-right-arrow-alt'></i>
                </button>
            </div>

        </div>
    </section>

    @endsection

@push('scripts')

@endpush
