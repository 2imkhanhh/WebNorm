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
                        <h2 class="testimonial-title news-text-left scroll-slide-left">
                            TIN TỨC &
                        </h2>
                        <h2 class="testimonial-title news-text-right scroll-slide-right">
                            GÓC NHÌN
                        </h2>
                    </div>
                </div>
            </div>
        </section>

        <section class="news-grid-section">
            <div class="container-fluid custom-padding">
                <div class="row gy-5">
                    <div class="col-12 col-md-4">
                        <div class="news-card">
                            <a href="{{ route('news.detail') }}" class="news-image-wrapper">
                                <img src="{{ asset('assets/images/news1.png') }}" alt="Food Container"
                                    class="img-fluid w-100">
                            </a>
                            <div class="news-meta">
                                <span class="news-date">29 Thg1 2026</span>
                            </div>
                            <h3 class="news-card-title">
                                <a href="#">Hiểu về tư duy thiết kế (design thinking) để nổi bật trong ngành công
                                    nghiệp
                                    sáng tạo</a>
                            </h3>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="news-card">
                            <a href="#" class="news-image-wrapper">
                                <img src="{{ asset('assets/images/project2.png') }}" alt="Pleo Billboard"
                                    class="img-fluid w-100">
                            </a>
                            <div class="news-meta">
                                <span class="news-date">29 Thg1 2026</span>
                            </div>
                            <h3 class="news-card-title">
                                <a href="{{ route('news.detail') }}">Hiểu về tư duy thiết kế (design thinking) để nổi bật
                                    trong ngành công nghiệp
                                    sáng tạo</a>
                            </h3>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="news-card">
                            <a href="#" class="news-image-wrapper">
                                <img src="{{ asset('assets/images/project3.png') }}" alt="22 23 blocks"
                                    class="img-fluid w-100">
                            </a>
                            <div class="news-meta">
                                <span class="news-date">29 Thg1 2026</span>
                            </div>
                            <h3 class="news-card-title">
                                <a href="{{ route('news.detail') }}">Hiểu về tư duy thiết kế (design thinking) để nổi bật
                                    trong ngành công nghiệp
                                    sáng tạo</a>
                            </h3>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="news-card">
                            <a href="{{ route('news.detail') }}" class="news-image-wrapper">
                                <img src="{{ asset('assets/images/project4.png') }}" alt="Food Container"
                                    class="img-fluid w-100">
                            </a>
                            <div class="news-meta">
                                <span class="news-date">29 Thg1 2026</span>
                            </div>
                            <h3 class="news-card-title">
                                <a href="#">Hiểu về tư duy thiết kế (design thinking) để nổi bật trong ngành công
                                    nghiệp
                                    sáng tạo</a>
                            </h3>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="news-card">
                            <a href="{{ route('news.detail') }}" class="news-image-wrapper">
                                <img src="{{ asset('assets/images/project5.png') }}" alt="Pleo Billboard"
                                    class="img-fluid w-100">
                            </a>
                            <div class="news-meta">
                                <span class="news-date">29 Thg1 2026</span>
                            </div>
                            <h3 class="news-card-title">
                                <a href="#">Hiểu về tư duy thiết kế (design thinking) để nổi bật trong ngành công
                                    nghiệp
                                    sáng tạo</a>
                            </h3>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="news-card">
                            <a href="{{ route('news.detail') }}" class="news-image-wrapper">
                                <img src="{{ asset('assets/images/project6.png') }}" alt="22 23 blocks"
                                    class="img-fluid w-100">
                            </a>
                            <div class="news-meta">
                                <span class="news-date">29 Thg1 2026</span>
                            </div>
                            <h3 class="news-card-title">
                                <a href="#">Hiểu về tư duy thiết kế (design thinking) để nổi bật trong ngành công
                                    nghiệp
                                    sáng tạo</a>
                            </h3>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="news-card">
                            <a href="{{ route('news.detail') }}" class="news-image-wrapper">
                                <img src="{{ asset('assets/images/project1.png') }}" alt="Food Container"
                                    class="img-fluid w-100">
                            </a>
                            <div class="news-meta">
                                <span class="news-date">29 Thg1 2026</span>
                            </div>
                            <h3 class="news-card-title">
                                <a href="#">Hiểu về tư duy thiết kế (design thinking) để nổi bật trong ngành công
                                    nghiệp
                                    sáng tạo</a>
                            </h3>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="news-card">
                            <a href="{{ route('news.detail') }}" class="news-image-wrapper">
                                <img src="{{ asset('assets/images/project2.png') }}" alt="Pleo Billboard"
                                    class="img-fluid w-100">
                            </a>
                            <div class="news-meta">
                                <span class="news-date">29 Thg1 2026</span>
                            </div>
                            <h3 class="news-card-title">
                                <a href="#">Hiểu về tư duy thiết kế (design thinking) để nổi bật trong ngành công
                                    nghiệp
                                    sáng tạo</a>
                            </h3>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="news-card">
                            <a href="{{ route('news.detail') }}" class="news-image-wrapper">
                                <img src="{{ asset('assets/images/project3.png') }}" alt="22 23 blocks"
                                    class="img-fluid w-100">
                            </a>
                            <div class="news-meta">
                                <span class="news-date">29 Thg1 2026</span>
                            </div>
                            <h3 class="news-card-title">
                                <a href="#">Hiểu về tư duy thiết kế (design thinking) để nổi bật trong ngành công
                                    nghiệp
                                    sáng tạo</a>
                            </h3>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="news-card">
                            <a href="{{ route('news.detail') }}" class="news-image-wrapper">
                                <img src="{{ asset('assets/images/project1.png') }}" alt="Food Container"
                                    class="img-fluid w-100">
                            </a>
                            <div class="news-meta">
                                <span class="news-date">29 Thg1 2026</span>
                            </div>
                            <h3 class="news-card-title">
                                <a href="#">Hiểu về tư duy thiết kế (design thinking) để nổi bật trong ngành công
                                    nghiệp
                                    sáng tạo</a>
                            </h3>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="news-card">
                            <a href="{{ route('news.detail') }}" class="news-image-wrapper">
                                <img src="{{ asset('assets/images/project2.png') }}" alt="Pleo Billboard"
                                    class="img-fluid w-100">
                            </a>
                            <div class="news-meta">
                                <span class="news-date">29 Thg1 2026</span>
                            </div>
                            <h3 class="news-card-title">
                                <a href="#">Hiểu về tư duy thiết kế (design thinking) để nổi bật trong ngành công
                                    nghiệp
                                    sáng tạo</a>
                            </h3>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="news-card">
                            <a href="{{ route('news.detail') }}" class="news-image-wrapper">
                                <img src="{{ asset('assets/images/project3.png') }}" alt="22 23 blocks"
                                    class="img-fluid w-100">
                            </a>
                            <div class="news-meta">
                                <span class="news-date">29 Thg1 2026</span>
                            </div>
                            <h3 class="news-card-title">
                                <a href="#">Hiểu về tư duy thiết kế (design thinking) để nổi bật trong ngành công
                                    nghiệp
                                    sáng tạo</a>
                            </h3>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="news-card">
                            <a href="{{ route('news.detail') }}" class="news-image-wrapper">
                                <img src="{{ asset('assets/images/project4.png') }}" alt="Food Container"
                                    class="img-fluid w-100">
                            </a>
                            <div class="news-meta">
                                <span class="news-date">29 Thg1 2026</span>
                            </div>
                            <h3 class="news-card-title">
                                <a href="#">Hiểu về tư duy thiết kế (design thinking) để nổi bật trong ngành công
                                    nghiệp
                                    sáng tạo</a>
                            </h3>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="news-card">
                            <a href="{{ route('news.detail') }}" class="news-image-wrapper">
                                <img src="{{ asset('assets/images/project5.png') }}" alt="Pleo Billboard"
                                    class="img-fluid w-100">
                            </a>
                            <div class="news-meta">
                                <span class="news-date">29 Thg1 2026</span>
                            </div>
                            <h3 class="news-card-title">
                                <a href="#">Hiểu về tư duy thiết kế (design thinking) để nổi bật trong ngành công
                                    nghiệp
                                    sáng tạo</a>
                            </h3>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="news-card">
                            <a href="{{ route('news.detail') }}" class="news-image-wrapper">
                                <img src="{{ asset('assets/images/project6.png') }}" alt="22 23 blocks"
                                    class="img-fluid w-100">
                            </a>
                            <div class="news-meta">
                                <span class="news-date">29 Thg1 2026</span>
                            </div>
                            <h3 class="news-card-title">
                                <a href="#">Hiểu về tư duy thiết kế (design thinking) để nổi bật trong ngành công
                                    nghiệp
                                    sáng tạo</a>
                            </h3>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="news-card">
                            <a href="{{ route('news.detail') }}" class="news-image-wrapper">
                                <img src="{{ asset('assets/images/project1.png') }}" alt="Food Container"
                                    class="img-fluid w-100">
                            </a>
                            <div class="news-meta">
                                <span class="news-date">29 Thg1 2026</span>
                            </div>
                            <h3 class="news-card-title">
                                <a href="#">Hiểu về tư duy thiết kế (design thinking) để nổi bật trong ngành công
                                    nghiệp
                                    sáng tạo</a>
                            </h3>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="news-card">
                            <a href="{{ route('news.detail') }}" class="news-image-wrapper">
                                <img src="{{ asset('assets/images/project2.png') }}" alt="Pleo Billboard"
                                    class="img-fluid w-100">
                            </a>
                            <div class="news-meta">
                                <span class="news-date">29 Thg1 2026</span>
                            </div>
                            <h3 class="news-card-title">
                                <a href="#">Hiểu về tư duy thiết kế (design thinking) để nổi bật trong ngành công
                                    nghiệp
                                    sáng tạo</a>
                            </h3>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="news-card">
                            <a href="{{ route('news.detail') }}" class="news-image-wrapper">
                                <img src="{{ asset('assets/images/project3.png') }}" alt="22 23 blocks"
                                    class="img-fluid w-100">
                            </a>
                            <div class="news-meta">
                                <span class="news-date">29 Thg1 2026</span>
                            </div>
                            <h3 class="news-card-title">
                                <a href="#">Hiểu về tư duy thiết kế (design thinking) để nổi bật trong ngành công
                                    nghiệp
                                    sáng tạo</a>
                            </h3>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="news-card">
                            <a href="{{ route('news.detail') }}" class="news-image-wrapper">
                                <img src="{{ asset('assets/images/project1.png') }}" alt="Food Container"
                                    class="img-fluid w-100">
                            </a>
                            <div class="news-meta">
                                <span class="news-date">29 Thg1 2026</span>
                            </div>
                            <h3 class="news-card-title">
                                <a href="#">Hiểu về tư duy thiết kế (design thinking) để nổi bật trong ngành công
                                    nghiệp
                                    sáng tạo</a>
                            </h3>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="news-card">
                            <a href="{{ route('news.detail') }}" class="news-image-wrapper">
                                <img src="{{ asset('assets/images/project2.png') }}" alt="Pleo Billboard"
                                    class="img-fluid w-100">
                            </a>
                            <div class="news-meta">
                                <span class="news-date">29 Thg1 2026</span>
                            </div>
                            <h3 class="news-card-title">
                                <a href="#">Hiểu về tư duy thiết kế (design thinking) để nổi bật trong ngành công
                                    nghiệp
                                    sáng tạo</a>
                            </h3>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="news-card">
                            <a href="{{ route('news.detail') }}" class="news-image-wrapper">
                                <img src="{{ asset('assets/images/project3.png') }}" alt="22 23 blocks"
                                    class="img-fluid w-100">
                            </a>
                            <div class="news-meta">
                                <span class="news-date">29 Thg1 2026</span>
                            </div>
                            <h3 class="news-card-title">
                                <a href="#">Hiểu về tư duy thiết kế (design thinking) để nổi bật trong ngành công
                                    nghiệp
                                    sáng tạo</a>
                            </h3>
                        </div>
                    </div>
                </div>

                <div class="load-more-wrap">
                    <a href="#" class="load-more-btn">XEM THÊM</a>
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
