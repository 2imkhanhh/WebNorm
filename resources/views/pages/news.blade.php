@extends('layouts.app')

@section('title', 'Bài viết | Brand Design Agency')
@section('body_class', 'news-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/news.css') }}?v=2">
    <link rel="stylesheet" href="{{ asset('assets/css/about-us.css') }}">
@endpush

@section('content')
    <section class="news-heading-section">
            <div class="container-fluid px-4 px-md-5">
                <div class="row">
                    <div class="col-12 testimonial-title-container">
                        <h2 class="testimonial-title news-text-left scroll-slide-left" data-studio-key="news_title1">
                            @normVal($pageContents['news_title1'] ?? null, 'TIN TỨC &')
                        </h2>
                        <h2 class="testimonial-title news-text-right scroll-slide-right" data-studio-key="news_title2">
                            @normVal($pageContents['news_title2'] ?? null, 'GÓC NHÌN')
                        </h2>
                    </div>
                </div>
            </div>
        </section>

        <section class="news-grid-section">
            <div class="container-fluid custom-padding">
                <div class="row gy-5">
                    @forelse($blogs as $blog)
                    <div class="col-12 col-md-4">
                        <div class="news-card">
                            <a href="{{ route('news.detail', $blog->slug) }}" class="news-image-wrapper">
                                <img src="{{ !empty($blog->thumbnail) ? (str_starts_with($blog->thumbnail, 'http') ? $blog->thumbnail : asset($blog->thumbnail)) : asset('assets/images/news1.png') }}" alt="{{ $blog->getTitle() }}" class="img-fluid w-100">
                            </a>
                            <div class="news-meta">
                                <span class="news-date">{{ $blog->published_at ? $blog->published_at->format('d/m/Y') : '' }}</span>
                            </div>
                            <h3 class="news-card-title">
                                <a href="{{ route('news.detail', $blog->slug) }}">{{ $blog->getTitle() }}</a>
                            </h3>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 py-5 text-center text-muted">Chưa có bài viết nào được đăng tải.</div>
                    @endforelse
                </div>
            </div>
        </section>

    @endsection

    @push('scripts')
        <script>
            //text animation
            const animatedTexts = document.querySelectorAll('.scroll-slide-left, .scroll-slide-right');

            function initTextObserver() {
                if (animatedTexts.length === 0) return;

                const textObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('is-visible');
                        } else {
                            entry.target.classList.remove('is-visible');
                        }
                    });
                }, {
                    threshold: 0.2
                });
                animatedTexts.forEach(text => textObserver.observe(text));
            }
            initTextObserver();

            //btn load more
            document.addEventListener("DOMContentLoaded", function() {
                const articles = document.querySelectorAll('.news-grid-section .row > .col-12');
                const loadMoreBtn = document.querySelector('.load-more-btn');

                const initialItems = 9;
                let currentItems = initialItems;
                const step = 9;

                function updateDisplay() {
                    articles.forEach((article, index) => {
                        if (index < currentItems) {
                            article.style.display = 'block';
                        } else {
                            article.style.display = 'none';
                        }
                    });

                    if (currentItems >= articles.length) {
                        if (loadMoreBtn) loadMoreBtn.style.display = 'none';
                    } else {
                        if (loadMoreBtn) loadMoreBtn.style.display = 'inline-flex';
                    }
                }

                if (articles.length > 0) {
                    updateDisplay();
                }

                if (loadMoreBtn) {
                    loadMoreBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        currentItems += step;

                        if (currentItems > articles.length) {
                            currentItems = articles.length;
                        }
                        updateDisplay();
                    });
                }
            });
        </script>
    @endpush
