<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="format-detection" content="telephone=no">

    <!-- SEO -->
    <title>@yield('title', __('site.meta_title_default'))</title>
    <meta name="description" content="@yield('description', __('site.meta_description_default'))">
    <meta name="keywords" content="{{ __('site.meta_keywords_default') }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="@yield('canonical', 'https://norm.vn/')">

    <meta property="og:type" content="website">
    <meta property="og:url" content="@yield('og_url', 'https://norm.vn/')">
    <meta property="og:title" content="@yield('title', __('site.meta_title_default'))">
    <meta property="og:description" content="@yield('description', __('site.meta_description_default'))">
    <meta property="og:image" content="{{ asset('assets/images/logo.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="Brand Design Agency">
    <meta property="og:locale" content="{{ __('site.og_locale') }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', __('site.meta_title_default'))">
    <meta name="twitter:description" content="@yield('description', __('site.meta_description_default'))">
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
      "description": "{{ __('site.schema_description') }}",
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
    <script src="{{ asset('assets/js/script.js') }}?v={{ filemtime(public_path('assets/js/script.js')) }}"></script>

    <!-- Realtime Bridge Listener for Website Studio -->
    <script>
        window.addEventListener('message', (event) => {
            if (!event.data) return;

            if (event.data.type === 'STUDIO_LANG_UPDATE') {
                window.__CURRENT_LOCALE__ = event.data.lang;
                document.documentElement.lang = event.data.lang;
            }

            if (event.data.type === 'STUDIO_SCROLL_TO_SECTION') {
                const secId = event.data.sectionId;
                const container = document.querySelector(`[data-studio-container="${secId}"]`) || document.getElementById(secId);
                if (container) {
                    if (window.lenis && typeof window.lenis.scrollTo === 'function') {
                        window.lenis.scrollTo(container, { offset: -40, duration: 1.0 });
                    } else {
                        container.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }
            }

            if (event.data.type === 'STUDIO_SCROLL_TO_WORKFLOW_STEP') {
                const sIdx = Number(event.data.stepIndex);
                const wfSec = document.getElementById('workflowSection');
                if (wfSec && !isNaN(sIdx)) {
                    const isDesktop = window.innerWidth > 1024;
                    if (isDesktop) {
                        const track = document.getElementById('workflowSliderTrack');
                        const total = track ? track.querySelectorAll('.workflow-step').length : 3;
                        const p = total > 1 ? (sIdx / (total - 1)) : 0;
                        const maxScroll = wfSec.offsetHeight - window.innerHeight;
                        const targetY = wfSec.offsetTop + (p * maxScroll);
                        if (window.lenis && typeof window.lenis.scrollTo === 'function') {
                            window.lenis.scrollTo(targetY, { duration: 0.8 });
                        } else {
                            window.scrollTo({ top: targetY, behavior: 'smooth' });
                        }
                    } else {
                        const track = document.getElementById('workflowSliderTrack');
                        const steps = track ? track.querySelectorAll('.workflow-step') : [];
                        if (steps[sIdx]) {
                            if (window.lenis && typeof window.lenis.scrollTo === 'function') {
                                window.lenis.scrollTo(steps[sIdx], { offset: -60, duration: 0.8 });
                            } else {
                                steps[sIdx].scrollIntoView({ behavior: 'smooth', block: 'center' });
                            }
                        }
                    }
                }
            }

            if (event.data.type === 'STUDIO_LIVE_UPDATE') {
                const { fieldKey, value } = event.data;
                const currentLang = event.data.lang || window.__CURRENT_LOCALE__ || document.documentElement.lang || 'vi';
                window.__CURRENT_LOCALE__ = currentLang;

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
                } else if (fieldKey === 'workflow_steps') {
                    const track = document.getElementById('workflowSliderTrack');
                    if (track) {
                        let steps = value;
                        if (typeof steps === 'string') {
                            try { steps = JSON.parse(steps); } catch (e) {}
                        }
                        if (steps && typeof steps === 'object' && !Array.isArray(steps)) {
                            steps = Object.values(steps);
                        }

                        if (Array.isArray(steps)) {
                            const getLangVal = (obj) => {
                                if (obj === null || obj === undefined) return '';
                                if (typeof obj === 'string') return obj;
                                if (typeof obj === 'object') {
                                    if (obj[currentLang] !== undefined && obj[currentLang] !== null) return obj[currentLang];
                                    if (obj.vi !== undefined && obj.vi !== null) return obj.vi;
                                    if (obj.en !== undefined && obj.en !== null) return obj.en;
                                    return '';
                                }
                                return String(obj);
                            };

                            const existingSteps = track.querySelectorAll('.workflow-step');
                            if (existingSteps.length === steps.length) {
                                // Smooth in-place DOM update without resetting classes, scroll, or layout
                                existingSteps.forEach((stepEl, idx) => {
                                    const step = steps[idx] || {};
                                    const numEl = stepEl.querySelector('.step-number');
                                    if (numEl) numEl.textContent = String(idx + 1).padStart(2, '0') + '/';

                                    const titleEl = stepEl.querySelector('.step-title');
                                    if (titleEl) titleEl.textContent = getLangVal(step.title);

                                    const descEl = stepEl.querySelector('.step-desc');
                                    if (descEl) descEl.textContent = getLangVal(step.desc);

                                    const imgEl = stepEl.querySelector('.step-image-wrapper img');
                                    if (imgEl) {
                                        let rawImg = getLangVal(step.image) || ('/assets/images/gallery' + ((idx % 9) + 1) + '.jpg');
                                        const imgSrc = rawImg.startsWith('http') ? rawImg : '/' + rawImg.replace(/^\//, '');
                                        if (imgEl.getAttribute('src') !== imgSrc) {
                                            imgEl.src = imgSrc;
                                        }
                                    }
                                });
                            } else {
                                // Steps added or removed -> re-render track items
                                track.innerHTML = steps.map((step, idx) => {
                                    const num = String(idx + 1).padStart(2, '0') + '/';
                                    const isActive = idx === 0 ? 'is-active' : '';
                                    const title = getLangVal(step.title);
                                    const desc = getLangVal(step.desc);
                                    let rawImg = getLangVal(step.image) || ('/assets/images/gallery' + ((idx % 9) + 1) + '.jpg');
                                    const imgSrc = rawImg.startsWith('http') ? rawImg : '/' + rawImg.replace(/^\//, '');
                                    return `
                                        <div class="col-12 workflow-step ${isActive}">
                                            <div class="step-top">
                                                <span class="step-number d-block sync-gap">${num}</span>
                                                <h3 class="step-title m-0">${title}</h3>
                                            </div>
                                            <p class="step-desc">${desc}</p>
                                            <div class="step-image-wrapper">
                                                <img src="${imgSrc}" alt="Bước ${idx + 1}" class="img-fluid">
                                            </div>
                                        </div>
                                    `;
                                }).join('');
                                window.dispatchEvent(new Event('resize'));
                            }
                        }
                    }
                } else if (fieldKey === 'members_list') {
                    const grid = document.getElementById('membersGridContainer');
                    if (grid) {
                        let members = value;
                        if (typeof members === 'string') {
                            try { members = JSON.parse(members); } catch (e) {}
                        }
                        if (members && typeof members === 'object' && !Array.isArray(members)) {
                            members = Object.values(members);
                        }

                        if (Array.isArray(members)) {
                            const getLangVal = (obj) => {
                                if (obj === null || obj === undefined) return '';
                                if (typeof obj === 'string') return obj;
                                if (typeof obj === 'object') {
                                    if (obj[currentLang] !== undefined && obj[currentLang] !== null) return obj[currentLang];
                                    if (obj.vi !== undefined && obj.vi !== null) return obj.vi;
                                    if (obj.en !== undefined && obj.en !== null) return obj.en;
                                    return '';
                                }
                                return String(obj);
                            };
                            const escHtml = (str) => String(str || '')
                                .replace(/&/g, '&amp;')
                                .replace(/</g, '&lt;')
                                .replace(/>/g, '&gt;')
                                .replace(/"/g, '&quot;')
                                .replace(/'/g, '&#39;');

                            const patterns = [
                                ['member', 'spacer', 'member', 'member'],
                                ['spacer', 'member', 'spacer', 'member'],
                                ['member', 'spacer', 'member', 'spacer'],
                            ];
                            let cursor = 0;
                            let pIdx = 0;
                            let rowsHtml = '';

                            while (cursor < members.length) {
                                const pattern = patterns[pIdx % patterns.length];
                                let slotsHtml = '';
                                for (const slotType of pattern) {
                                    if (slotType === 'member' && cursor < members.length) {
                                        const m = members[cursor] || {};
                                        const name = getLangVal(m.name);
                                        const role = getLangVal(m.role);
                                        const rawImg = getLangVal(m.image) || '/assets/images/team1.png';
                                        const imgSrc = rawImg.startsWith('http') ? rawImg : '/' + rawImg.replace(/^\//, '');
                                        slotsHtml += `
                                            <div class="col-6 col-md-3 member-item">
                                                <div class="member-card">
                                                    <img src="${escHtml(imgSrc)}" alt="${escHtml(name)}">
                                                    <h4 class="member-name">${escHtml(name)}</h4>
                                                    <p class="member-role">${escHtml(role)}</p>
                                                </div>
                                            </div>
                                        `;
                                        cursor++;
                                    } else {
                                        slotsHtml += '<div class="col-md-3 d-none d-md-block"></div>';
                                    }
                                }
                                rowsHtml += `<div class="row member-row">${slotsHtml}</div>`;
                                pIdx++;
                            }

                            grid.innerHTML = rowsHtml;
                        }
                    }
                } else {
                    // Try generic matching with data-studio-key or class
                    const target = document.querySelector(`[data-studio-key="${fieldKey}"]`);
                    if (target) {
                        let textVal = value;
                        if (textVal && typeof textVal === 'object') {
                            textVal = textVal[currentLang] ?? textVal.vi ?? textVal.en ?? '';
                        }
                        if (fieldKey === 'workflow_label' || fieldKey === 'story_label') {
                            textVal = String(textVal || '').replace(/^[•\s\-\*]+/u, '').trim();
                        }
                        if (target.tagName === 'IMG') {
                            const fullSrc = (textVal || '').startsWith('http') ? textVal : '/' + (textVal || '').replace(/^\//, '');
                            target.src = fullSrc;
                        } else {
                            target.textContent = textVal;
                        }
                    }
                }
            }
        });
    </script>

    @stack('scripts')
</body>

</html>
