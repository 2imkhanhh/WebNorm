@extends('layouts.app')

@section('title', 'Brand Design Agency')

@push('styles')
    <script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/4.0.0/model-viewer.min.js"></script>
@endpush

@section('preloader')
<div id="preloader">
        <script>
            if (sessionStorage.getItem('norm_preloader_shown')) {
                document.getElementById('preloader').style.display = 'none';
            }
        </script>
        <svg class="loading-svg" width="739" height="300" viewBox="0 0 739 300" fill="none"
            xmlns="http://www.w3.org/2000/svg">
            <mask id="mask0_654_5" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="739"
                height="300">
                <path
                    d="M369.5 0C512.512 0 654.699 71.4345 733.016 132.613L739 137.21V298.703L720.737 291.159C598.259 240.472 480.423 233.398 371.151 233.398C370.635 233.516 370.016 233.517 369.5 233.517L367.85 233.398C257.134 233.516 107.621 248.606 18.8828 290.924L0 300V137.328L5.98438 132.73C84.3006 71.4338 226.488 6.15988e-05 369.5 0Z"
                    fill="#E0E0E0" />
            </mask>
            <g mask="url(#mask0_654_5)">
                <rect class="loading-step" y="-106.78" width="185" height="447.458" fill="#E0E0E0" />
                <rect class="loading-step" x="184" y="-106.78" width="188" height="447.458" fill="#E0E0E0" />
                <rect class="loading-step" x="370" y="-106.78" width="186" height="447.458" fill="#E0E0E0" />
                <rect class="loading-step" x="553" y="-106.78" width="188" height="447.458" fill="#E0E0E0" />
            </g>
        </svg>
    </div>
@endsection

@section('content')
<section class="hero-section">
        @php
            $model3dVal = !empty($settings['model_3d']) ? $settings['model_3d'] : 'storage/uploads/norm.glb';
            $model3dSrc = str_starts_with($model3dVal, 'http') ? $model3dVal : asset(ltrim($model3dVal, '/'));
        @endphp
        <model-viewer src="{{ $model3dSrc }}" alt="3D Banner" camera-controls disable-zoom
            interaction-prompt="none" disable-pan class="hero-3d-model" style="--poster-color: transparent;"
            shadow-intensity="1" camera-orbit="0deg 75deg 140%" min-camera-orbit="-30deg 60deg 140%"
            max-camera-orbit="30deg 90deg 140%"></model-viewer>
        <div class="container-fluid px-4 px-md-5 h-100">
            <div class="row h-100 align-items-end">

                <div class="col-auto hero-tagline">
                    <p data-studio-key="hero_tagline_1">@normVal($pageContents['hero_tagline_1'] ?? null, 'Brand Design Agency')</p>
                    <p data-studio-key="hero_tagline_2">@normVal($pageContents['hero_tagline_2'] ?? null, 'Based In Vietnam')</p>
                    <p data-studio-key="hero_tagline_3">@normVal($pageContents['hero_tagline_3'] ?? null, 'Culture Your Brand')</p>
                </div>

                <div class="col hero-image-wrap d-flex justify-content-center align-items-center">
                    <img src="{{ asset('assets/images/banner.png') }}" alt="Placeholder" class="hero-img hero-img-placeholder">
                </div>

                <div class="col-auto hero-ctas d-flex gap-5">
                    <a href="{{ \App\Helpers\NormHelper::localizedRoute('work') }}" class="cta-btn">{{ __('site.hero_cta_work') }}</a>
                    <a href="#" class="cta-btn nav-contact-trigger">{{ __('site.hero_cta_contact') }}</a>
                </div>

            </div>
        </div>

        @php
            $heroHeadingRaw = \App\Helpers\NormHelper::val($pageContents['hero_heading'] ?? null, "NHẬN DIỆN\nCHIẾN LƯỢC\nVĂN HOÁ");
            $headingLines = preg_split('/\r\n|\r|\n/', trim($heroHeadingRaw));
            $headingLines = array_values(array_filter(array_map('trim', $headingLines), 'strlen'));
            $headingWord1 = $headingLines[0] ?? 'NHẬN DIỆN';
            $headingWord2 = $headingLines[1] ?? 'CHIẾN LƯỢC';
            $headingWord3 = $headingLines[2] ?? 'VĂN HOÁ';
        @endphp

        <div class="big-bottom-text" data-studio-key="hero_heading">
            <div class="bbt-row bbt-row-1">
                <span class="stagger-word" data-word-index="0">{{ $headingWord1 }}</span>
                <span class="stagger-word" data-word-index="1">{{ $headingWord2 }}</span>
            </div>
            <div class="bbt-row bbt-row-2">
                <span class="stagger-word" data-word-index="2">{{ $headingWord3 }}</span>
            </div>
        </div>

        <div class="hero-ctas-mobile">
            <a href="{{ \App\Helpers\NormHelper::localizedRoute('work') }}" class="cta-btn">{{ __('site.hero_cta_work') }}</a>
            <a href="#" class="cta-btn nav-contact-trigger">{{ __('site.hero_cta_contact') }}</a>
        </div>

    </section>

    <section class="intro-section">
        <div class="container-fluid norm-container">
            <div class="reveal-wrapper">
                <p class="reveal-text" data-studio-key="hero_desc">
                    @normVal($pageContents['hero_desc'] ?? null, 'Chúng tôi giúp doanh nghiệp xây dựng bản sắc thương hiệu rõ ràng, nhất quán và phù hợp với định hướng kinh doanh.')
                </p>
            </div>

            <div class="row align-items-start" style="margin-top: 150px; margin-bottom: 100px;">
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    @php
                        $heroMediaVal = \App\Helpers\NormHelper::val($pageContents['hero_video'] ?? null, 'storage/uploads/main-feature.mp4');
                        $heroMediaSrc = str_starts_with($heroMediaVal, 'http') ? $heroMediaVal : asset(ltrim($heroMediaVal, '/'));
                        $heroExt = strtolower(pathinfo(parse_url($heroMediaVal, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
                        $isHeroVideo = in_array($heroExt, ['mp4', 'webm', 'mov', 'avi']) || str_ends_with(strtolower($heroMediaVal), '.mp4');
                    @endphp
                    <div class="hero-media-wrapper" data-studio-container="hero_video">
                        @if($isHeroVideo)
                            <video class="w-100 d-block hero-media-el" style="border-radius: 8px;" autoplay loop muted playsinline data-studio-key="hero_video">
                                <source src="{{ $heroMediaSrc }}" type="video/{{ $heroExt === 'webm' ? 'webm' : 'mp4' }}">
                                {{ __('site.video_unsupported') }}
                            </video>
                        @else
                            <img class="w-100 d-block hero-media-el" style="border-radius: 8px; object-fit: cover;" src="{{ $heroMediaSrc }}" alt="Hero Media" data-studio-key="hero_video">
                        @endif
                    </div>
                </div>
                <div class="col-md-3"></div>
            </div>

            <div class="row align-items-start content-footer" style="margin-bottom: 200px;">
                <div class="col-md-3">
                    <span class="si-label">• <span data-studio-key="about_label">{{ ltrim(\App\Helpers\NormHelper::val($pageContents['about_label'] ?? null, 'Về chúng tôi'), '• ') }}</span></span>
                </div>

                <div class="col-md-6">
                    <p class="desc-text m-0" data-studio-key="about_desc">
                        @normVal($pageContents['about_desc'] ?? null, 'Có rất nhiều lý do tại sao xây dựng thương hiệu và tiếp thị lại quan trọng. Vô số sách viết về chủ đề này, nhưng tất cả đều quy về một điều đơn giản: tại sao mọi người nên quan tâm đến sự tồn tại của bạn? Chúng tôi tạo ra những trải nghiệm khiến mọi người chú ý và quan tâm.')
                    </p>
                </div>

                <div class="col-md-3 text-start text-md-end mt-4 mt-md-0">
                    <a href="{{ \App\Helpers\NormHelper::localizeUrl(\App\Helpers\NormHelper::val($pageContents['about_btn_url'] ?? null, '/about-us')) }}" class="link-more" data-studio-key="about_btn_text">
                        @normVal($pageContents['about_btn_text'] ?? null, 'ĐỌC THÊM')
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="project-showcase" id="projectShowcase">
        <div class="ps-sticky-wrap">
            <div class="ps-content-wrapper">
                <div class="ps-counter-top">
                    <div>
                        <span class="ps-num-current" id="psCurrent">01</span>
                        <span class="ps-num-total" id="psTotal">/{{ str_pad(max(count($projects), 1), 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <a href="{{ \App\Helpers\NormHelper::localizedRoute('work') }}" class="link-more text-uppercase ps-view-all">{{ __('site.view_all_projects') }}</a>
                </div>

                <div class="ps-horizontal-viewport">
                    <div class="ps-horizontal-track" id="psTrack">
                        @forelse($projects as $p)
                        <div class="ps-project-item">
                            <a href="{{ \App\Helpers\NormHelper::localizedRoute('work.detail', $p->slug) }}" class="ps-card-inner text-decoration-none text-reset">
                                <div class="ps-img-wrap">
                                    <img src="{{ !empty($p->thumbnail) ? (str_starts_with($p->thumbnail, 'http') ? $p->thumbnail : asset($p->thumbnail)) : asset('assets/images/project1.png') }}" alt="{{ $p->getTitle() }}">
                                </div>
                                <div class="ps-info-wrap">
                                    <div class="ps-title">{{ $p->getTitle() }}</div>
                                    <div class="ps-categories">
                                        <span>{{ $p->getCategory() }}</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @empty
                        <div class="ps-project-item">
                            <div class="ps-card-inner">
                                <div class="ps-img-wrap">
                                    <img src="{{ asset('assets/images/project1.png') }}" alt="{{ __('site.default_project_title') }}">
                                </div>
                                <div class="ps-info-wrap">
                                    <div class="ps-title">{{ __('site.default_project_title') }}</div>
                                    <div class="ps-categories">
                                        <span>Brand identity</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('partials.services-section', ['introContainerClass' => 'norm-container'])

    <section class="testimonial-section">
        <div class="container-fluid px-4 px-md-5">
            <div class="row">
                <div class="col-12 testimonial-title-container" id="testimonialTitleWrap">
                    <h2 class="testimonial-title text-left scroll-slide-left">
                        <span class="quote-mark">“</span><span data-studio-key="testimonial_title_1">{{ ltrim(\App\Helpers\NormHelper::val($pageContents['testimonial_title_1'] ?? null, 'TRẢI NGHIỆM'), '“" ') }}</span>
                    </h2>

                    <h2 class="testimonial-title text-right scroll-slide-right">
                        <span data-studio-key="testimonial_title_2">@normVal($pageContents['testimonial_title_2'] ?? null, 'KHÁCH HÀNG')</span>
                    </h2>
                </div>
            </div>

            <div class="testimonial-main-content">
                <div class="testi-slider-container">
                    <div class="testi-track" id="testiTrack">
                        @forelse($reviews as $idx => $rev)
                        <div class="testi-slide {{ $idx === 0 ? 'active' : '' }}">
                            <div class="row align-items-start">
                                <div class="col-md-5 d-flex justify-content-md-end mb-4 mb-md-0 pe-md-5">
                                    <div class="testimonial-avatar">
                                        <img src="{{ !empty($rev->avatar) ? (str_starts_with($rev->avatar, 'http') ? $rev->avatar : asset($rev->avatar)) : asset('assets/images/avatar.png') }}" alt="{{ $rev->author }}">
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="testimonial-quote">
                                        <p class="quote-text">
                                            "{{ $rev->getContent() }}"
                                        </p>
                                        <div class="quote-author">
                                            <h4 class="author-name">{{ $rev->author }}</h4>
                                            <p class="author-position">{{ $rev->getRole() }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="testi-slide active">
                            <div class="row align-items-start">
                                <div class="col-md-5 d-flex justify-content-md-end mb-4 mb-md-0 pe-md-5">
                                    <div class="testimonial-avatar">
                                        <img src="{{ asset('assets/images/avatar.png') }}" alt="Nguyễn Minh Anh">
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="testimonial-quote">
                                        <p class="quote-text">
                                            "{{ __('site.default_review_quote') }}"
                                        </p>
                                        <div class="quote-author">
                                            <h4 class="author-name">Nguyễn Minh Anh</h4>
                                            <p class="author-position">{{ __('site.default_author_role') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="testimonial-footer">
                <div class="row align-items-center">
                    <div class="col-md-4 d-none d-md-block"></div>
                    <div class="col-md-6 d-flex align-items-center gap-4">
                        <div class="testimonial-pagination">
                            <span class="current">01</span><span class="total">/{{ str_pad(max(count($reviews), 1), 2, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="testimonial-nav-btns">
                            <button class="nav-btn prev" aria-label="Previous">
                                <span class="icon-wrap">
                                    <i class='bx bx-left-arrow-alt icon-main'></i>
                                    <i class='bx bx-left-arrow-alt icon-alt'></i>
                                </span>
                            </button>

                            <div class="next-btn-wrapper">
                                <svg class="progress-ring" width="56" height="56" viewBox="0 0 56 56"
                                    style="overflow: visible;">
                                    <circle class="progress-ring-bg" stroke="#E0E0E0" stroke-width="1"
                                        fill="transparent" r="27" cx="28" cy="28" />
                                    <circle class="progress-ring-circle" stroke="#111111" stroke-width="1.5"
                                        fill="transparent" r="27" cx="28" cy="28" />
                                </svg>
                                <button class="nav-btn next" aria-label="Next">
                                    <span class="icon-wrap">
                                        <i class='bx bx-right-arrow-alt icon-main'></i>
                                        <i class='bx bx-right-arrow-alt icon-alt'></i>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <section class="blog-intro-section">
        <div class="container-fluid norm-container">
            <div class="row services-intro-row">

                <div class="col-12 col-lg-3 mb-4 mb-lg-0">
                    <span class="si-label">• <span data-studio-key="blog_label">{{ ltrim(\App\Helpers\NormHelper::val($pageContents['blog_label'] ?? null, 'Blog'), '• ') }}</span></span>
                </div>

                <div class="col-12 col-lg-9 mb-4 mb-lg-0">
                    <h2 class="si-statement reveal-text-multi" data-studio-key="blog_statement">
                        @normVal($pageContents['blog_statement'] ?? null, 'Chia sẻ góc nhìn về thương hiệu, thiết kế và chiến lược giúp doanh nghiệp phát triển bền vững.')
                    </h2>
                </div>

            </div>
        </div>
    </section>

    <section class="blog-slider-section">
        <div class="container-fluid blog-custom-container">
            <div class="d-flex justify-content-end mb-4">
                <a href="{{ \App\Helpers\NormHelper::localizeUrl(\App\Helpers\NormHelper::val($pageContents['blog_btn_url'] ?? null, '/news')) }}" class="link-more text-uppercase" style="margin-top: 100px;" data-studio-key="blog_btn_text">
                    @normVal($pageContents['blog_btn_text'] ?? null, 'XEM THÊM BÀI VIẾT')
                </a>
            </div>

            <div class="blog-slider-container">
                <div class="blog-track" id="blogTrack">
                    @forelse($blogs as $blog)
                    @php
                        $thumbUrl = !empty($blog->thumbnail) ? (str_starts_with($blog->thumbnail, 'http') ? $blog->thumbnail : asset(ltrim($blog->thumbnail, '/'))) : asset('assets/images/project1.png');
                        $detailUrl = \App\Helpers\NormHelper::localizedRoute('news.detail', $blog->slug);
                        $blogTitle = $blog->getTitle();
                    @endphp
                    <div class="blog-card">
                        <div class="blog-img-wrap">
                            <a href="{{ $detailUrl }}" class="d-block w-100 h-100">
                                <img src="{{ $thumbUrl }}" alt="{{ $blogTitle }}" loading="lazy">
                            </a>
                        </div>
                        <h3 class="blog-title">
                            <a href="{{ $detailUrl }}" class="text-decoration-none" style="color: inherit;">
                                {{ $blogTitle }}
                            </a>
                        </h3>
                    </div>
                    @empty
                    <div class="blog-card">
                        <div class="blog-img-wrap">
                            <a href="{{ \App\Helpers\NormHelper::localizedRoute('news') }}" class="d-block w-100 h-100">
                                <img src="{{ asset('assets/images/project1.png') }}" alt="Blog">
                            </a>
                        </div>
                        <h3 class="blog-title">
                            <a href="{{ \App\Helpers\NormHelper::localizedRoute('news') }}" class="text-decoration-none" style="color: inherit;">
                                {{ __('site.no_blogs_home') }}
                            </a>
                        </h3>
                    </div>
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
<script>
        document.addEventListener("DOMContentLoaded", () => {
            const hero3dModel = document.querySelector('.hero-3d-model');
            if (hero3dModel) {
                const updateZoom = () => {
                    if (window.innerWidth <= 1024) {
                        hero3dModel.removeAttribute('disable-zoom');
                    } else {
                        hero3dModel.setAttribute('disable-zoom', '');
                    }
                };
                updateZoom();
                window.addEventListener('resize', updateZoom);
            }

            // banner hover
            /*
            const bannerImg = document.querySelector('.hero-img');
            if (bannerImg) {
                const banners = [
                    'assets/images/banner.png',
                    'assets/images/banner2.png',
                    'assets/images/banner3.png',
                ];
                let bannerIndex = 0;
                let bannerIntervalId = null;

                bannerImg.addEventListener('mouseenter', () => {
                    bannerIntervalId = setInterval(() => {
                        bannerIndex = (bannerIndex + 1) % banners.length;
                        bannerImg.src = banners[bannerIndex];
                    }, 400);
                });

                bannerImg.addEventListener('mouseleave', () => {
                    clearInterval(bannerIntervalId);
                    bannerIntervalId = null;
                });
            }
            */

            // animation text
            function splitIntoChars(el) {
                const text = el.textContent;
                const wordIndex = parseInt(el.dataset.wordIndex);
                const wordDelay = wordIndex * 80;

                el.innerHTML = '';
                el.style.display = 'inline-block';
                el.style.overflow = 'visible';

                [...text].forEach((char, i) => {
                    const wrapper = document.createElement('span');
                    wrapper.className = 'char-clip';

                    const inner = document.createElement('span');
                    inner.className = 'stagger-char';
                    inner.textContent = char === ' ' ? '\u00A0' : char;
                    inner.style.animationDelay = `${wordDelay + i * 60}ms`;

                    wrapper.appendChild(inner);
                    el.appendChild(wrapper);
                });
            }
            window.splitIntoChars = splitIntoChars;

            const staggerObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate');
                        staggerObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.4 });

            document.querySelectorAll('.stagger-word').forEach(el => {
                splitIntoChars(el);
                staggerObserver.observe(el);
            });

            // animation testimonial
            const animatedTexts = document.querySelectorAll('.scroll-slide-left, .scroll-slide-right');
            if (animatedTexts.length > 0) {
                const textObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('is-visible');
                        } else {
                            entry.target.classList.remove('is-visible');
                        }
                    });
                }, { threshold: 0.5, rootMargin: "0px" });

                animatedTexts.forEach(text => textObserver.observe(text));
            }

            // project showcase 
            const showcase = document.getElementById('projectShowcase');
            const psTrack = document.getElementById('psTrack');
            const projectItems = document.querySelectorAll('.ps-project-item');
            const psCurrent = document.getElementById('psCurrent');
            const psTotal = document.getElementById('psTotal');

            if (showcase && psTrack && projectItems.length > 0) {
                const totalProjects = projectItems.length;

                if (psTotal) {
                    psTotal.textContent = '/' + String(totalProjects).padStart(2, '0');
                }

                const projectData = Array.from(projectItems).map(item => ({
                    el: item,
                    infoWrap: item.querySelector('.ps-info-wrap'),
                    title: item.querySelector('.ps-title'),
                    categories: item.querySelector('.ps-categories')
                }));

                let cachedShowcaseTop = 0;
                let cachedWindowHeight = 0;
                let cachedWindowWidth = 0;
                let cachedMaxScroll = 0;
                let cachedItemWidth = 0;
                let cachedItemTotalWidth = 0;
                let cachedPaddingLeft = 0;

                function calculateMetrics() {
                    const newWidth = window.innerWidth;

                    if (cachedWindowHeight === 0 || Math.abs(cachedWindowWidth - newWidth) > 50) {
                        const dummy = document.createElement('div'); dummy.style.height = '100vh'; dummy.style.position = 'absolute'; dummy.style.visibility = 'hidden'; document.body.appendChild(dummy); cachedWindowHeight = dummy.offsetHeight; document.body.removeChild(dummy);
                    }
                    cachedWindowWidth = newWidth;

                    showcase.style.height = `${totalProjects * cachedWindowHeight}px`;
                    const stickyWrap = showcase.querySelector('.ps-sticky-wrap');
                    if (stickyWrap) stickyWrap.style.height = `${cachedWindowHeight}px`;

                    if (projectItems.length > 0) {
                        cachedItemWidth = projectItems[0].offsetWidth;
                        const gap = parseFloat(window.getComputedStyle(projectItems[0]).marginRight) || 0;
                        cachedItemTotalWidth = cachedItemWidth + gap;
                    }
                    cachedPaddingLeft = parseFloat(window.getComputedStyle(psTrack).paddingLeft) || 0;

                    const rect = showcase.getBoundingClientRect();
                    cachedShowcaseTop = rect.top + window.scrollY;
                    cachedMaxScroll = (totalProjects - 1) * cachedWindowHeight;
                }

                calculateMetrics();
                window.addEventListener('resize', calculateMetrics, { passive: true });

                function updateCarousel() {
                    const scrollY = window.scrollY;

                    let scrolledInside = scrollY - cachedShowcaseTop;
                    if (scrolledInside < 0) scrolledInside = 0;
                    if (scrolledInside > cachedMaxScroll) scrolledInside = cachedMaxScroll;

                    let progress = cachedMaxScroll > 0 ? scrolledInside / cachedMaxScroll : 0;

                    const maxTranslate = cachedItemTotalWidth * (totalProjects - 1);
                    const trackTranslateX = -(progress * maxTranslate);

                    let activeIndex = Math.round(progress * (totalProjects - 1));
                    if (psCurrent) {
                        psCurrent.textContent = String(activeIndex + 1).padStart(2, '0');
                    }

                    const viewportCenter = cachedWindowWidth / 2;
                    const maxDist = cachedWindowWidth / 1.5;

                    psTrack.style.transform = `translate3d(${trackTranslateX}px, 0, 0)`;

                    projectData.forEach((data, i) => {
                        const itemLeft = cachedPaddingLeft + i * cachedItemTotalWidth;
                        const itemCenter = itemLeft + cachedItemWidth / 2 + trackTranslateX;
                        const distance = Math.abs(viewportCenter - itemCenter);

                        let scale = 1 - (distance / maxDist) * 0.45;
                        if (scale < 0.65) scale = 0.65;
                        if (scale > 1) scale = 1;

                        data.el.style.transform = `scale(${scale}) translateZ(0)`;

                        if (data.infoWrap) {
                            if (cachedWindowWidth > 1024) {
                                let opacity = 1 - (distance / (cachedWindowWidth / 3));
                                if (opacity < 0) opacity = 0;

                                let translateY = (distance / cachedWindowWidth) * 50;

                                data.infoWrap.style.opacity = opacity;

                                if (data.title) data.title.style.transform = `translateY(${translateY * 0.5}px)`;
                                if (data.categories) data.categories.style.transform = `translateY(${translateY}px)`;
                            } else {
                                data.infoWrap.style.opacity = '1';
                                if (data.title) data.title.style.transform = 'none';
                                if (data.categories) data.categories.style.transform = 'none';
                            }
                        }
                    });
                }

                let psScrollTicking = false;
                function onPsScroll() {
                    if (!psScrollTicking) {
                        window.requestAnimationFrame(() => {
                            updateCarousel();
                            psScrollTicking = false;
                        });
                        psScrollTicking = true;
                    }
                }

                if (typeof lenis !== 'undefined') {
                    lenis.on('scroll', updateCarousel);
                } else {
                    window.addEventListener('scroll', onPsScroll, { passive: true });
                }

                window.addEventListener('resize', onPsScroll, { passive: true });

                updateCarousel();
            }

            // testimonial slider
            function initTestimonialSlider() {
                const track = document.getElementById('testiTrack');
                const prevBtn = document.querySelector('.testimonial-nav-btns .prev');
                const nextBtn = document.querySelector('.next-btn-wrapper');
                const currentElem = document.querySelector('.testimonial-pagination .current');
                const totalElem = document.querySelector('.testimonial-pagination .total');
                const progressCircle = document.querySelector('.progress-ring-circle');
                const sliderContainer = document.querySelector('.testi-slider-container');
                const testimonialSection = document.querySelector('.testimonial-section');

                let initialSlides = Array.from(document.querySelectorAll('.testi-slide'));
                if (!track || initialSlides.length === 0) return;

                initialSlides.forEach((slide, index) => {
                    slide.dataset.index = index + 1;
                });

                if (totalElem) {
                    totalElem.textContent = '/' + String(initialSlides.length).padStart(2, '0');
                }

                if (initialSlides.length > 1) {
                    track.insertBefore(initialSlides[initialSlides.length - 1], initialSlides[0]);
                }

                let isAnimating = false;
                let isInView = false;
                let isScrolling = false;
                let scrollTimeout;

                const intervalTime = 5000;
                const transitionTime = 600;
                let currentProgressTime = 0;
                let lastFrameTime = 0;
                let animationFrameId;

                let progressState = 'filling';
                let rewindProgressTime = 0;
                const rewindDuration = 400;

                function getCircumference() {
                    return 2 * Math.PI * 27;
                }
                let circleCircumference = getCircumference();

                function alignFooter() {
                    const activeSlide = track.querySelector('.testi-slide.active');
                    if (!activeSlide) return;

                    const avatarEl = activeSlide.querySelector('.testimonial-avatar');
                    const footerWrap = document.querySelector('.testimonial-footer .gap-4');
                    const currentNumberText = document.querySelector('.testimonial-pagination .current');

                    if (avatarEl && footerWrap && currentNumberText) {
                        footerWrap.style.marginLeft = '0px';
                        footerWrap.offsetHeight;

                        const avatarRight = avatarEl.getBoundingClientRect().right;
                        const numberRight = currentNumberText.getBoundingClientRect().right;
                        const diff = avatarRight - numberRight;

                        if (window.innerWidth >= 768) {
                            footerWrap.style.marginLeft = `${diff}px`;
                        }
                    }
                }

                function updateCarousel(instant = true) {
                    const currentSlides = Array.from(track.querySelectorAll('.testi-slide'));
                    if (currentSlides.length === 0) return;

                    const activeIndex = currentSlides.length > 1 ? 1 : 0;

                    if (currentElem) {
                        currentElem.textContent = String(currentSlides[activeIndex].dataset.index).padStart(2, '0');
                    }

                    currentSlides.forEach((slide, index) => {
                        slide.classList.toggle('active', index === activeIndex);
                    });

                    const containerWidth = sliderContainer.clientWidth;
                    const slideWidth = currentSlides[0].offsetWidth;
                    const centerOffset = (containerWidth - slideWidth) / 2;
                    const translateX = centerOffset - (activeIndex * slideWidth);

                    if (instant) track.style.transition = 'none';
                    track.style.transform = `translate3d(${translateX}px, 0, 0)`;

                    if (instant) {
                        track.offsetHeight;
                        alignFooter();
                    }
                }

                document.fonts.ready.then(() => {
                    alignFooter();
                });

                // next slide
                function moveToNextSlide() {
                    if (isAnimating) return;
                    isAnimating = true;

                    const currentSlides = Array.from(track.querySelectorAll('.testi-slide'));
                    const containerWidth = sliderContainer.clientWidth;
                    const slideWidth = currentSlides[0].offsetWidth;
                    const centerOffset = (containerWidth - slideWidth) / 2;

                    currentSlides.forEach(s => s.classList.remove('active'));
                    if (currentSlides[2]) currentSlides[2].classList.add('active');
                    if (currentElem && currentSlides[2]) currentElem.textContent = String(currentSlides[2].dataset.index).padStart(2, '0');

                    track.style.transition = `transform ${transitionTime}ms ease-in-out`;
                    track.style.transform = `translate3d(${centerOffset - (slideWidth * 2)}px, 0, 0)`;

                    setTimeout(() => {
                        track.appendChild(currentSlides[0]);

                        track.style.transition = 'none';
                        track.style.transform = `translate3d(${centerOffset - slideWidth}px, 0, 0)`;
                        isAnimating = false;
                    }, transitionTime);

                    progressState = 'rewinding';
                    rewindProgressTime = 0;
                }

                // prev slide
                function moveToPrevSlide() {
                    if (isAnimating) return;
                    isAnimating = true;

                    const currentSlides = Array.from(track.querySelectorAll('.testi-slide'));
                    const containerWidth = sliderContainer.clientWidth;
                    const slideWidth = currentSlides[0].offsetWidth;
                    const centerOffset = (containerWidth - slideWidth) / 2;

                    currentSlides.forEach(s => s.classList.remove('active'));
                    currentSlides[0].classList.add('active');
                    if (currentElem) currentElem.textContent = String(currentSlides[0].dataset.index).padStart(2, '0');

                    track.style.transition = `transform ${transitionTime}ms ease-in-out`;
                    track.style.transform = `translate3d(${centerOffset}px, 0, 0)`;

                    setTimeout(() => {
                        const lastSlide = currentSlides[currentSlides.length - 1];
                        track.insertBefore(lastSlide, currentSlides[0]);

                        track.style.transition = 'none';
                        track.style.transform = `translate3d(${centerOffset - slideWidth}px, 0, 0)`;
                        isAnimating = false;
                    }, transitionTime);

                    progressState = 'rewinding';
                    rewindProgressTime = 0;
                }

                // auto play
                function progressLoop(currentTime) {
                    if (!lastFrameTime) lastFrameTime = currentTime;
                    let deltaTime = currentTime - lastFrameTime;
                    if (deltaTime > 100) deltaTime = 16;
                    lastFrameTime = currentTime;

                    if (isInView && !isScrolling) {
                        if (progressState === 'filling') {
                            currentProgressTime += deltaTime;

                            if (currentProgressTime >= intervalTime) {
                                moveToNextSlide();
                            }

                            if (progressCircle) {
                                const progressRatio = Math.min(currentProgressTime / intervalTime, 1);
                                const offset = circleCircumference - (progressRatio * circleCircumference);
                                progressCircle.style.strokeDashoffset = offset;
                            }
                        }
                        else if (progressState === 'rewinding') {
                            rewindProgressTime += deltaTime;

                            if (rewindProgressTime >= rewindDuration) {
                                progressState = 'filling';
                                currentProgressTime = 0;
                                rewindProgressTime = 0;

                                if (progressCircle) {
                                    progressCircle.style.strokeDashoffset = circleCircumference;
                                }
                            } else {
                                if (progressCircle) {
                                    const rewindRatio = Math.min(rewindProgressTime / rewindDuration, 1);
                                    const offset = rewindRatio * circleCircumference;
                                    progressCircle.style.strokeDashoffset = offset;
                                }
                            }
                        }
                    }
                    animationFrameId = requestAnimationFrame(progressLoop);
                }

                if (progressCircle) {
                    progressCircle.style.transition = 'none';
                    progressCircle.style.strokeDasharray = `${circleCircumference} ${circleCircumference}`;
                    progressCircle.style.strokeDashoffset = circleCircumference;
                }

                window.addEventListener('scroll', () => {
                    isScrolling = true;
                    clearTimeout(scrollTimeout);
                    scrollTimeout = setTimeout(() => {
                        isScrolling = false;
                        lastFrameTime = performance.now();
                    }, 150);
                }, { passive: true });

                window.addEventListener('resize', () => {
                    updateCarousel(true);
                }, { passive: true });

                if (testimonialSection) {
                    const observer = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            isInView = entry.isIntersecting;
                            if (isInView) {
                                lastFrameTime = performance.now();
                            }
                        });
                    }, { threshold: 0 });
                    observer.observe(testimonialSection);
                } else {
                    isInView = true;
                }

                updateCarousel(true);
                animationFrameId = requestAnimationFrame(progressLoop);

                if (prevBtn) prevBtn.addEventListener('click', moveToPrevSlide);
                if (nextBtn) nextBtn.addEventListener('click', moveToNextSlide);
            }
            initTestimonialSlider();
        });
    </script>

    <!-- 3D model -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modelViewer = document.querySelector('.hero-3d-model');
            if (!modelViewer) return;

            let isCtrlPressed = false;

            window.addEventListener('keydown', (e) => {
                if (e.key === 'Control' && !isCtrlPressed) {
                    isCtrlPressed = true;
                    modelViewer.removeAttribute('disable-zoom');
                }
            });

            window.addEventListener('keyup', (e) => {
                if (e.key === 'Control') {
                    isCtrlPressed = false;
                    modelViewer.setAttribute('disable-zoom', '');
                }
            });
        });
    </script>
    <script>
        (function () {
            const preloader = document.getElementById('preloader');
            if (!preloader || preloader.style.display === 'none') return;

            const steps = preloader.querySelectorAll('.loading-step');
            let currentStep = 0;
            let isCompleted = false;

            function fillStep(index) {
                if (index < steps.length && index >= 0) {
                    steps[index].classList.add('filled');
                }
            }

            const mediaElements = Array.from(document.querySelectorAll('model-viewer'));
            let totalWeight = 0;
            let loadedWeight = 0;

            const mediaData = mediaElements.map(media => {
                let weight = 40;
                totalWeight += weight;
                return { el: media, weight: weight, loaded: false };
            });

            let targetPercent = 0;
            let visualPercent = 0;

            function updateRealProgress() {
                if (totalWeight === 0) {
                    targetPercent = 1;
                    return;
                }
                targetPercent = loadedWeight / totalWeight;
            }

            function animateProgress() {
                if (isCompleted) return;

                let diff = targetPercent - visualPercent;
                if (diff > 0) {
                    visualPercent += Math.min(diff, 0.015);
                }

                if (visualPercent >= 0.25 && currentStep < 1) {
                    fillStep(0);
                    currentStep = 1;
                }
                if (visualPercent >= 0.50 && currentStep < 2) {
                    fillStep(1);
                    currentStep = 2;
                }
                if (visualPercent >= 0.75 && currentStep < 3) {
                    fillStep(2);
                    currentStep = 3;
                }

                if (visualPercent >= 0.99 && targetPercent >= 1) {
                    completeLoading();
                } else {
                    requestAnimationFrame(animateProgress);
                }
            }

            function completeLoading() {
                if (isCompleted) return;
                isCompleted = true;
                sessionStorage.setItem('norm_preloader_shown', 'true');
                fillStep(0);
                fillStep(1);
                fillStep(2);
                fillStep(3);

                setTimeout(() => {
                    preloader.classList.add('loaded');
                    setTimeout(() => { preloader.remove(); }, 400);
                }, 100);
            }

            function markLoaded(item) {
                if (!item.loaded) {
                    item.loaded = true;
                    loadedWeight += item.weight;
                    updateRealProgress();
                }
            }

            if (totalWeight > 0) {
                mediaData.forEach(item => {
                    const media = item.el;
                    if (media.tagName === 'IMG') {
                        if (media.complete) {
                            markLoaded(item);
                        } else {
                            media.addEventListener('load', () => markLoaded(item), { once: true });
                            media.addEventListener('error', () => markLoaded(item), { once: true });
                        }
                    } else if (media.tagName === 'VIDEO') {
                        if (media.readyState >= 3) {
                            markLoaded(item);
                        } else {
                            media.addEventListener('canplay', () => markLoaded(item), { once: true });
                            media.addEventListener('error', () => markLoaded(item), { once: true });
                        }
                    } else if (media.tagName === 'MODEL-VIEWER') {
                        media.addEventListener('load', () => markLoaded(item), { once: true });
                        media.addEventListener('error', () => markLoaded(item), { once: true });
                    }
                });
                updateRealProgress();
                requestAnimationFrame(animateProgress);
            } else {
                targetPercent = 1;
                requestAnimationFrame(animateProgress);
            }

            setTimeout(() => {
                targetPercent = 1;
            }, 8000);

        })();
    </script>
@endpush
