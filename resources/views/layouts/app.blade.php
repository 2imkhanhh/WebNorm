<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="format-detection" content="telephone=no">

    <!-- SEO -->
    <title>@yield('title', 'Brand Design Agency')</title>
    <meta name="description" content="@yield('description', 'Brand Design Agency cung cấp giải pháp thiết kế thương hiệu, logo, bộ nhận diện thương hiệu chuyên nghiệp và sáng tạo.')">
    <meta name="keywords"
        content="thiết kế thương hiệu, thiết kế logo, brand design, agency thiết kế, bộ nhận diện thương hiệu">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="@yield('canonical', 'https://norm.vn/')">

    <meta property="og:type" content="website">
    <meta property="og:url" content="@yield('og_url', 'https://norm.vn/')">
    <meta property="og:title" content="@yield('title', 'Brand Design Agency')">
    <meta property="og:description" content="@yield('description', 'Brand Design Agency cung cấp giải pháp thiết kế thương hiệu, logo, bộ nhận diện thương hiệu chuyên nghiệp và sáng tạo.')">
    <meta property="og:image" content="{{ asset('assets/images/logo.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="Brand Design Agency">
    <meta property="og:locale" content="vi_VN">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'Brand Design Agency')">
    <meta name="twitter:description" content="@yield('description', 'Brand Design Agency cung cấp giải pháp thiết kế thương hiệu, logo, bộ nhận diện thương hiệu chuyên nghiệp và sáng tạo.')">
    <meta name="twitter:image" content="{{ asset('assets/images/logo.png') }}">

    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}" />
    <link rel="apple-touch-icon" href="{{ asset('assets/images/logo.png') }}">

    <!-- CSS & Scripts -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}?v={{ filemtime(public_path('assets/css/style.css')) }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/contact.css') }}?v={{ filemtime(public_path('assets/css/contact.css')) }}" />
    @stack('styles')

    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@type": "ProfessionalService",
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

<body class="@yield('body_class')">
    @yield('preloader')

    @include('partials.navbar')

    @yield('content')

    @include('partials.final-footer')

    @include('partials.contact-sidebar')

    @if (!empty($isPreviewDraft))
    <div id="draftPreviewNotification" style="position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%); z-index: 99999; background: #0f172a; color: #f8fafc; padding: 9px 18px; border-radius: 999px; box-shadow: 0 10px 30px rgba(0,0,0,0.35); font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 10px; border: 1px solid rgba(255,255,255,0.15); font-family: sans-serif;">
        <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #3b82f6; box-shadow: 0 0 8px #3b82f6;"></span>
        <span>Đang xem <strong>Bản nháp (Draft Preview)</strong></span>
        <a href="{{ request()->url() }}" style="margin-left: 8px; color: #38bdf8; text-decoration: none; font-size: 12px; background: rgba(56,189,248,0.15); padding: 4px 10px; border-radius: 6px; font-weight: 600;">Xem bản Live</a>
    </div>
    <script>
        if (window.self !== window.top) {
            var el = document.getElementById('draftPreviewNotification');
            if (el) el.style.display = 'none';
        }
    </script>
    @endif

    <script src="https://unpkg.com/lenis@1.1.13/dist/lenis.min.js"></script>
    <script src="{{ asset('assets/js/script.js') }}"></script>

    <!-- Realtime Bridge Listener for Website Studio -->
    <script>
        window.addEventListener('message', (event) => {
            if (event.data && event.data.type === 'STUDIO_LIVE_UPDATE') {
                const { fieldKey, value } = event.data;

                // Handle footer words animation
                if (fieldKey === 'footer_line1_old') {
                    const el = document.querySelector('.footer-line:nth-child(1) .text-old');
                    if (el) el.textContent = value;
                } else if (fieldKey === 'footer_line1_new') {
                    const el = document.querySelector('.footer-line:nth-child(1) .text-new');
                    if (el) el.textContent = value;
                } else if (fieldKey === 'footer_line2_old') {
                    const el = document.querySelector('.footer-line:nth-child(2) .text-old');
                    if (el) el.textContent = value;
                } else if (fieldKey === 'footer_line2_new') {
                    const el = document.querySelector('.footer-line:nth-child(2) .text-new');
                    if (el) el.textContent = value;
                } else if (fieldKey === 'footer_line3_old') {
                    const el = document.querySelector('.footer-line:nth-child(3) .text-old');
                    if (el) el.textContent = value;
                } else if (fieldKey === 'footer_line3_new') {
                    const el = document.querySelector('.footer-line:nth-child(3) .text-new');
                    if (el) el.textContent = value;
                } else if (fieldKey === 'hero_heading') {
                    const lines = (value || '').split(/\r?\n/).map(s => s.trim()).filter(Boolean);
                    const w0 = document.querySelector('.stagger-word[data-word-index="0"]');
                    const w1 = document.querySelector('.stagger-word[data-word-index="1"]');
                    const w2 = document.querySelector('.stagger-word[data-word-index="2"]');
                    if (w0 && lines[0]) {
                        w0.textContent = lines[0];
                        if (typeof window.splitIntoChars === 'function') window.splitIntoChars(w0);
                    }
                    if (w1 && lines[1]) {
                        w1.textContent = lines[1];
                        if (typeof window.splitIntoChars === 'function') window.splitIntoChars(w1);
                    }
                    if (w2 && lines[2]) {
                        w2.textContent = lines[2];
                        if (typeof window.splitIntoChars === 'function') window.splitIntoChars(w2);
                    }
                } else if (fieldKey === 'hero_video') {
                    const cleanVal = (value || '').trim();
                    const fullSrc = cleanVal.startsWith('http') ? cleanVal : '/' + cleanVal.replace(/^\//, '');
                    const cleanLower = cleanVal.split('?')[0].toLowerCase();
                    const isVid = cleanLower.endsWith('.mp4') || cleanLower.endsWith('.webm') || cleanLower.endsWith('.mov') || cleanLower.endsWith('.avi');

                    const container = document.querySelector('[data-studio-container="hero_video"]');
                    if (container) {
                        if (isVid) {
                            container.innerHTML = `
                                <video class="w-100 d-block hero-media-el" style="border-radius: 8px;" autoplay loop muted playsinline data-studio-key="hero_video">
                                    <source src="${fullSrc}" type="video/mp4">
                                </video>
                            `;
                            const newVid = container.querySelector('video');
                            if (newVid) {
                                newVid.load();
                                newVid.play().catch(() => {});
                            }
                        } else {
                            container.innerHTML = `
                                <img class="w-100 d-block hero-media-el" style="border-radius: 8px; object-fit: cover;" src="${fullSrc}" alt="Hero Media" data-studio-key="hero_video">
                            `;
                        }
                    } else {
                        const existingVid = document.querySelector('video[data-studio-key="hero_video"]');
                        if (existingVid) {
                            const srcEl = existingVid.querySelector('source');
                            if (srcEl) srcEl.src = fullSrc;
                            existingVid.src = fullSrc;
                            existingVid.load();
                            existingVid.play().catch(() => {});
                        }
                    }
                } else if (fieldKey === 'about_btn_url') {
                    const btn = document.querySelector('a[data-studio-key="about_btn_text"]');
                    if (btn) btn.href = value;
                } else {
                    // Try generic matching with data-studio-key or class
                    const target = document.querySelector(`[data-studio-key="${fieldKey}"]`);
                    if (target) target.textContent = value;
                }
            }
        });
    </script>

    @stack('scripts')
</body>

</html>
