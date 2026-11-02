@extends('layouts.app')

@section('title', 'Bài viết | Brand Design Agency')
@section('body_class', 'news-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/news.css') }}?v=2">
    <link rel="stylesheet" href="{{ asset('assets/css/about-us.css') }}">
@endpush

@section('content')
    <!DOCTYPE html>
    <html lang="vi">

    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="format-detection" content="telephone=no">

        <!-- SEO -->
        <title>Tin tức | Brand Design Agency</title>
        <meta name="description"
            content="Brand Design Agency cung cấp giải pháp thiết kế thương hiệu, logo, bộ nhận diện thương hiệu chuyên nghiệp và sáng tạo.">
        <meta name="keywords"
            content="thiết kế thương hiệu, thiết kế logo, brand design, agency thiết kế, bộ nhận diện thương hiệu">
        <meta name="robots" content="index, follow">
        <link rel="canonical" href="https://norm.vn/">

        <meta property="og:type" content="website">
        <meta property="og:url" content="https://norm.vn/">
        <meta property="og:title" content="Tin tức | Brand Design Agency">
        <meta property="og:description"
            content="Brand Design Agency cung cấp giải pháp thiết kế thương hiệu, logo, bộ nhận diện thương hiệu chuyên nghiệp và sáng tạo.">
        <meta property="og:image" content="https://norm.vn/assets/images/logo.png">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:site_name" content="Brand Design Agency">
        <meta property="og:locale" content="vi_VN">

        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="Tin tức | Brand Design Agency">
        <meta name="twitter:description"
            content="Brand Design Agency cung cấp giải pháp thiết kế thương hiệu, logo, bộ nhận diện thương hiệu chuyên nghiệp và sáng tạo.">
        <meta name="twitter:image" content="https://norm.vn/assets/images/logo.png">

        <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}" />
        <link rel="apple-touch-icon" href="{{ asset('assets/images/logo.png') }}">

        <!-- CSS & Scripts -->
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/css/news.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/about-us.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/contact.css') }}" />

        <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "ProfessionalService",
      "name": "Brand Design Agency",
      "url": "https://norm.vn/",
      "description": "Đơn vị chuyên cung cấp giải pháp thiết kế thương hiệu và logo chuyên nghiệp.",
      "image": "https://norm.vn/assets/images/logo.png",
      "sameAs": [
        "https://www.facebook.com/",
        "https://www.instagram.com/"
      ],
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "110 Nguyễn Ngọc Nại",
        "addressLocality": "Hà Nội",
        "addressRegion": "HN",
        "addressCountry": "VN"
      },
      "telephone": "+84 816656718"
    }
    </script>
    </head>

    <body class="news-page">
        <header class="navbar navbar-expand-lg">
            <div class="navbar-inner">
                <a href="{{ route('about') }}" class="nav-link-item">VỀ CHÚNG TÔI</a>
                <a href="{{ route('work') }}" class="nav-link-item">DỰ ÁN</a>
                <a href="{{ route('home') }}" class="navbar-logo">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" />
                </a>
                <a href="{{ route('news') }}" class="nav-link-item active">BÀI VIẾT</a>
                <div class="nav-contact-group">
                    <a href="#" class="nav-link-item">LIÊN HỆ</a>
                    <a href="#" class="nav-link-item lang-toggle" id="langToggle">VN</a>
                </div>
                <button class="nav-hamburger" aria-label="Menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </header>

        <div class="mobile-menu" id="mobileMenu">
            <div class="mobile-menu-inner">
                <a href="{{ route('about') }}" class="mobile-menu-item">VỀ CHÚNG TÔI</a>
                <a href="{{ route('work') }}" class="mobile-menu-item">DỰ ÁN</a>
                <a href="{{ route('news') }}" class="mobile-menu-item active">BÀI VIẾT</a>
                <a href="#" class="mobile-menu-item">LIÊN HỆ</a>
                <a href="#" class="mobile-menu-item mobile-menu-lang" id="langToggleMobile">VN</a>
            </div>
        </div>

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
