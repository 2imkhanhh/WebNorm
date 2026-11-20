// menu mobile
const hamburger = document.querySelector('.nav-hamburger');
const mobileMenu = document.getElementById('mobileMenu');

if (hamburger && mobileMenu) {
    hamburger.addEventListener('click', () => {
        const isOpen = mobileMenu.classList.toggle('open');
        hamburger.classList.toggle('active', isOpen);
        document.documentElement.style.overflow = isOpen ? 'hidden' : '';
        if (window.lenis) {
            if (isOpen) window.lenis.stop();
            else window.lenis.start();
        }
    });

    document.querySelectorAll('.mobile-menu-item').forEach(link => {
        link.addEventListener('click', () => {
            if (link.classList.contains('nav-contact-trigger') || link.classList.contains('mobile-menu-lang')) {
                return;
            }
            mobileMenu.classList.remove('open');
            hamburger.classList.remove('active');
            document.documentElement.style.overflow = '';
            if (window.lenis) window.lenis.start();
        });
    });
}

// reveal text
const revealElements = document.querySelectorAll('.reveal-text, .reveal-text-multi');

revealElements.forEach(revealTextEl => {
    const text = revealTextEl.textContent.trim();
    const words = text.split(/\s+/);
    revealTextEl.innerHTML = '';

    const charEls = [];

    words.forEach((word, wIndex) => {
        const wordSpan = document.createElement('span');
        wordSpan.style.display = 'inline-block';

        if (wIndex < words.length - 1) {
            wordSpan.style.marginRight = '0.2em';
        }

        [...word].forEach((char) => {
            const charSpan = document.createElement('span');
            charSpan.classList.add('reveal-char');
            charSpan.textContent = char;
            wordSpan.appendChild(charSpan);
            charEls.push(charSpan);
        });

        revealTextEl.appendChild(wordSpan);
    });

    let elemAbsTop = 0;
    let windowH = window.innerHeight;
    let lastLitCount = -1;
    let charTicking = false;

    function measureRevealBounds() {
        windowH = window.innerHeight;
        const rect = revealTextEl.getBoundingClientRect();
        elemAbsTop = rect.top + (window.scrollY || window.pageYOffset);
    }

    function applyLitCount(targetCount) {
        const clamped = Math.max(0, Math.min(charEls.length, targetCount));
        if (clamped === lastLitCount) return;

        if (lastLitCount < 0) {
            for (let i = 0; i < charEls.length; i++) {
                charEls[i].classList.toggle('lit', i < clamped);
            }
        } else if (clamped > lastLitCount) {
            for (let i = lastLitCount; i < clamped; i++) {
                charEls[i].classList.add('lit');
            }
        } else {
            for (let i = clamped; i < lastLitCount; i++) {
                charEls[i].classList.remove('lit');
            }
        }
        lastLitCount = clamped;
    }

    function updateChars() {
        charTicking = false;
        const scrollY = window.scrollY || window.pageYOffset;

        const rawStart = elemAbsTop - windowH + (windowH * 0.15);
        const animEnd = elemAbsTop + (windowH * -0.1);
        const animStart = Math.min(rawStart, animEnd - 50);
        const clampedStart = Math.max(animStart, 0);

        if (scrollY <= clampedStart) {
            applyLitCount(0);
            return;
        }

        if (scrollY >= animEnd) {
            applyLitCount(charEls.length);
            return;
        }

        const progress = (scrollY - clampedStart) / (animEnd - clampedStart);
        applyLitCount(Math.round(progress * charEls.length));
    }

    function onRevealScroll() {
        if (!charTicking) {
            charTicking = true;
            requestAnimationFrame(updateChars);
        }
    }

    function onRevealResize() {
        measureRevealBounds();
        onRevealScroll();
    }

    measureRevealBounds();
    window.addEventListener('scroll', onRevealScroll, { passive: true });
    window.addEventListener('resize', onRevealResize, { passive: true });
    if (document.fonts && document.fonts.ready) {
        document.fonts.ready.then(onRevealResize);
    }
    if (typeof ResizeObserver !== 'undefined') {
        const revealObserver = new ResizeObserver(onRevealResize);
        revealObserver.observe(document.body);
    }

    requestAnimationFrame(() => {
        measureRevealBounds();
        updateChars();
    });
});

// active status services
const serviceItems = document.querySelectorAll('.service-item');
let serviceTicking = false;

function calculateActiveService() {
    serviceTicking = false;
    if (serviceItems.length === 0) return;

    const viewportH = window.innerHeight;
    const viewportCenter = viewportH / 2;
    let activeItem = null;
    let minDistance = Infinity;
    const measurements = [];

    for (let i = 0; i < serviceItems.length; i++) {
        const rect = serviceItems[i].getBoundingClientRect();
        const isVisible = rect.top < viewportH && rect.bottom > 0;
        measurements.push(isVisible);
        if (isVisible) {
            const itemCenter = rect.top + rect.height / 2;
            const distance = Math.abs(viewportCenter - itemCenter);
            if (distance < minDistance) {
                minDistance = distance;
                activeItem = serviceItems[i];
            }
        }
    }

    for (let i = 0; i < serviceItems.length; i++) {
        const item = serviceItems[i];
        const shouldBeActive = item === activeItem && measurements[i];
        if (shouldBeActive !== item.classList.contains('active')) {
            item.classList.toggle('active', shouldBeActive);
        }
    }
}

function scheduleActiveService() {
    if (!serviceTicking) {
        serviceTicking = true;
        requestAnimationFrame(calculateActiveService);
    }
}

if (serviceItems.length > 0) {
    window.addEventListener('scroll', scheduleActiveService, { passive: true });
    window.addEventListener('resize', scheduleActiveService, { passive: true });
    document.addEventListener('DOMContentLoaded', calculateActiveService);
}

// footer scroll text
function initFooterScrollText() {
    const footerSection = document.getElementById('footerSection');
    const finalFooterSection = document.querySelector('.final-footer-section');
    const footerLines = document.querySelectorAll('.footer-line');

    if (!footerSection || footerLines.length === 0) return;

    const linePairs = Array.from(footerLines).map((line) => ({
        textNew: line.querySelector('.text-new'),
        textOld: line.querySelector('.text-old'),
    })).filter(p => p.textNew && p.textOld);

    let targetFooterProgress = 0;
    let currentFooterProgress = 0;
    let lastRenderedProgress = -1;
    let footerSnapTimeout;
    let lastScrollY = window.scrollY;
    let footerRafId = null;
    let footerScrollTicking = false;

    let cachedFooterTop = 0;
    let cachedFooterHeight = 0;
    let cachedFinalFooterTop = 0;
    let cachedWindowHeight = window.innerHeight;
    let cachedTriggerDistance = 0;

    function measureFooterMetrics() {
        cachedWindowHeight = window.innerHeight;
        cachedFooterTop = footerSection.offsetTop;
        cachedFooterHeight = footerSection.offsetHeight;
        cachedFinalFooterTop = finalFooterSection ? finalFooterSection.offsetTop : (cachedFooterTop + cachedFooterHeight);
        cachedTriggerDistance = cachedFooterHeight * (window.innerWidth <= 1024 ? 0.25 : 0.2);
    }

    function applyFooterProgress(progressVal) {
        if (Math.abs(progressVal - lastRenderedProgress) < 0.0005) return;
        lastRenderedProgress = progressVal;

        if (progressVal > 0.6) {
            footerSection.classList.add('is-text-revealed');
        } else {
            footerSection.classList.remove('is-text-revealed');
        }

        const progress = Math.max(0, Math.min(1, progressVal * 1.25));
        const insetTop = (1 - progress) * 100;
        const insetBottom = progress * 100;
        const newClip = `inset(${insetTop}% -10% -10% -10%)`;
        const oldClip = `inset(-10% -10% ${insetBottom}% -10%)`;

        for (let i = 0; i < linePairs.length; i++) {
            const { textNew, textOld } = linePairs[i];
            textNew.style.webkitClipPath = newClip;
            textNew.style.clipPath = newClip;
            textOld.style.webkitClipPath = oldClip;
            textOld.style.clipPath = oldClip;
        }
    }

    function renderFooterSmooth() {
        const diff = targetFooterProgress - currentFooterProgress;
        if (Math.abs(diff) < 0.0005) {
            currentFooterProgress = targetFooterProgress;
            applyFooterProgress(currentFooterProgress);
            footerRafId = null;
            return;
        }

        currentFooterProgress += diff * 0.15;
        applyFooterProgress(currentFooterProgress);
        footerRafId = requestAnimationFrame(renderFooterSmooth);
    }

    function ensureFooterAnimation() {
        if (footerRafId === null && Math.abs(targetFooterProgress - currentFooterProgress) >= 0.0005) {
            footerRafId = requestAnimationFrame(renderFooterSmooth);
        }
    }

    function onFooterScroll() {
        footerScrollTicking = false;
        const scrollY = window.scrollY;
        const scrollDirection = scrollY > lastScrollY ? 'down' : 'up';
        lastScrollY = scrollY;

        const scrolledIntoFooter = (scrollY + cachedWindowHeight) - cachedFooterTop;

        if (scrolledIntoFooter >= cachedTriggerDistance) {
            if (!footerSection.classList.contains('dark-mode')) {
                footerSection.classList.add('dark-mode');
                document.body.classList.add('dark-mode');
            }
        } else if (footerSection.classList.contains('dark-mode')) {
            footerSection.classList.remove('dark-mode');
            document.body.classList.remove('dark-mode');
        }

        if ((scrollY + cachedWindowHeight) >= cachedFinalFooterTop - 50) {
            document.body.classList.add('dark-mode-instant');
        } else {
            document.body.classList.remove('dark-mode-instant');
        }

        let scrolledInside = Math.max(0, scrollY - cachedFooterTop);
        const maxScroll = cachedFooterHeight - cachedWindowHeight;
        if (scrolledInside > maxScroll) scrolledInside = maxScroll;

        if (maxScroll > 0) {
            targetFooterProgress = scrolledInside / maxScroll;
            ensureFooterAnimation();
        }

        clearTimeout(footerSnapTimeout);

        if (scrollY > cachedFooterTop && scrollY < cachedFooterTop + maxScroll) {
            footerSnapTimeout = setTimeout(() => {
                let snapTargetY;

                if (scrollDirection === 'down') {
                    snapTargetY = targetFooterProgress >= 0.05 ? cachedFooterTop + maxScroll : cachedFooterTop;
                } else {
                    snapTargetY = targetFooterProgress <= 0.95 ? cachedFooterTop : cachedFooterTop + maxScroll;
                }

                if (Math.abs(scrollY - snapTargetY) > 5) {
                    if (typeof lenis !== 'undefined') {
                        lenis.scrollTo(snapTargetY, { duration: 1.5 });
                    }
                }
            }, 50);
        }
    }

    measureFooterMetrics();
    applyFooterProgress(0);

    if (typeof ResizeObserver !== 'undefined') {
        const footerBodyObserver = new ResizeObserver(() => {
            measureFooterMetrics();
        });
        footerBodyObserver.observe(document.body);
    }

    window.addEventListener('scroll', () => {
        if (!footerScrollTicking) {
            footerScrollTicking = true;
            requestAnimationFrame(onFooterScroll);
        }
    }, { passive: true });

    window.addEventListener('resize', () => {
        measureFooterMetrics();
        onFooterScroll();
    }, { passive: true });

    window.addEventListener('load', () => {
        measureFooterMetrics();
        onFooterScroll();
    });
}

// footer stacked-alpha WebGL background video
function initFooterBgVideo() {
    const footerSection = document.getElementById('footerSection');
    const canvas = document.getElementById('footerBgCanvas');
    const video = document.getElementById('footerBgVideo');

    if (!footerSection || !canvas || !video) return;

    let isLoaded = false;
    let isInView = false;
    let rafId = null;
    let vfcId = null;
    let lastVideoTime = -1;

    function loadVideoSource() {
        if (isLoaded) return;
        const src = video.getAttribute('data-src');
        if (!src) return;
        isLoaded = true;
        video.src = src;
        video.load();
    }

    const gl = canvas.getContext('webgl', {
        alpha: true,
        premultipliedAlpha: true,
        antialias: false,
        powerPreference: 'default',
    }) || canvas.getContext('experimental-webgl', {
        alpha: true,
        premultipliedAlpha: true,
        antialias: false,
    });

    let renderFrameFn = null;
    let resizeCanvasFn = null;

    if (gl) {
        const vsSource = `
            attribute vec2 aPos;
            attribute vec2 aTex;
            varying vec2 vTex;
            void main() {
                gl_Position = vec4(aPos, 0.0, 1.0);
                vTex = aTex;
            }
        `;
        const fsSource = `
            precision mediump float;
            uniform sampler2D uTex;
            uniform vec2 uScale;
            varying vec2 vTex;
            void main() {
                vec2 uv = (vTex - 0.5) * uScale + 0.5;
                if (uv.x < 0.0 || uv.x > 1.0 || uv.y < 0.0 || uv.y > 1.0) {
                    discard;
                }
                float safeY = clamp(uv.y, 0.002, 0.998);
                vec3 rgb = texture2D(uTex, vec2(uv.x, safeY * 0.5)).rgb;
                float rawA = texture2D(uTex, vec2(uv.x, safeY * 0.5 + 0.5)).r;
                float alpha = clamp((rawA - 0.03) / 0.94, 0.0, 1.0);
                if (alpha < 0.02) {
                    discard;
                }
                gl_FragColor = vec4(rgb, alpha);
            }
        `;

        const compile = (type, source) => {
            const shader = gl.createShader(type);
            gl.shaderSource(shader, source);
            gl.compileShader(shader);
            if (!gl.getShaderParameter(shader, gl.COMPILE_STATUS)) {
                gl.deleteShader(shader);
                return null;
            }
            return shader;
        };

        const vs = compile(gl.VERTEX_SHADER, vsSource);
        const fs = compile(gl.FRAGMENT_SHADER, fsSource);

        if (vs && fs) {
            const program = gl.createProgram();
            gl.attachShader(program, vs);
            gl.attachShader(program, fs);
            gl.linkProgram(program);

            if (gl.getProgramParameter(program, gl.LINK_STATUS)) {
                gl.useProgram(program);

                const quadBuffer = gl.createBuffer();
                gl.bindBuffer(gl.ARRAY_BUFFER, quadBuffer);
                gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([
                    -1, -1, 0, 1,
                     1, -1, 1, 1,
                    -1,  1, 0, 0,
                     1,  1, 1, 0,
                ]), gl.STATIC_DRAW);

                const aPos = gl.getAttribLocation(program, 'aPos');
                const aTex = gl.getAttribLocation(program, 'aTex');
                gl.enableVertexAttribArray(aPos);
                gl.vertexAttribPointer(aPos, 2, gl.FLOAT, false, 16, 0);
                gl.enableVertexAttribArray(aTex);
                gl.vertexAttribPointer(aTex, 2, gl.FLOAT, false, 16, 8);

                const uScale = gl.getUniformLocation(program, 'uScale');
                const texture = gl.createTexture();
                gl.bindTexture(gl.TEXTURE_2D, texture);
                gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_S, gl.CLAMP_TO_EDGE);
                gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_T, gl.CLAMP_TO_EDGE);
                gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MIN_FILTER, gl.LINEAR);
                gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MAG_FILTER, gl.LINEAR);
                gl.pixelStorei(gl.UNPACK_FLIP_Y_WEBGL, false);

                let coverScaleX = 1;
                let coverScaleY = 1;

                const isLowCores = typeof navigator !== 'undefined' && navigator.hardwareConcurrency && navigator.hardwareConcurrency <= 4;
                const maxDpr = isLowCores ? 1 : 1.25;
                const maxCanvasW = isLowCores ? 800 : 960;

                resizeCanvasFn = () => {
                    const rect = canvas.getBoundingClientRect();
                    if (!rect.width || !rect.height) return;
                    const dpr = Math.min(window.devicePixelRatio || 1, maxDpr);
                    const targetW = Math.min(Math.round(rect.width * dpr), maxCanvasW);
                    const targetH = Math.round(targetW * (rect.height / rect.width));
                    if (canvas.width !== targetW || canvas.height !== targetH) {
                        canvas.width = targetW;
                        canvas.height = targetH;
                        gl.viewport(0, 0, targetW, targetH);
                    }

                    const videoW = video.videoWidth || 1280;
                    const videoH = (video.videoHeight ? video.videoHeight / 2 : 720);
                    const videoAspect = videoW / videoH;
                    const canvasAspect = rect.width / rect.height;

                    if (canvasAspect > videoAspect) {
                        coverScaleX = 1;
                        coverScaleY = videoAspect / canvasAspect;
                    } else {
                        coverScaleX = canvasAspect / videoAspect;
                        coverScaleY = 1;
                    }
                    gl.uniform2f(uScale, coverScaleX, coverScaleY);
                };

                renderFrameFn = (forceUpload = false) => {
                    if (video.readyState < 2 || video.videoWidth === 0) return;
                    if (!forceUpload && video.currentTime === lastVideoTime) return;
                    lastVideoTime = video.currentTime;

                    gl.bindTexture(gl.TEXTURE_2D, texture);
                    gl.texImage2D(gl.TEXTURE_2D, 0, gl.RGB, gl.RGB, gl.UNSIGNED_BYTE, video);
                    gl.clearColor(0, 0, 0, 0);
                    gl.clear(gl.COLOR_BUFFER_BIT);
                    gl.drawArrays(gl.TRIANGLE_STRIP, 0, 4);
                };
            }
        }
    }

    // Fallback 2D canvas if WebGL is unavailable
    if (!renderFrameFn) {
        const ctx2d = canvas.getContext('2d');
        if (ctx2d) {
            resizeCanvasFn = () => {
                const rect = canvas.getBoundingClientRect();
                if (!rect.width || !rect.height) return;
                const targetW = Math.min(Math.round(rect.width), 960);
                const targetH = Math.round(targetW * (rect.height / rect.width));
                canvas.width = targetW;
                canvas.height = targetH;
            };
            renderFrameFn = (forceUpload = false) => {
                if (video.readyState < 2 || video.videoWidth === 0) return;
                if (!forceUpload && video.currentTime === lastVideoTime) return;
                lastVideoTime = video.currentTime;
                const vw = video.videoWidth;
                const halfH = Math.floor(video.videoHeight / 2);
                const cw = canvas.width;
                const ch = canvas.height;
                const scale = Math.max(cw / vw, ch / halfH);
                const dw = vw * scale;
                const dh = halfH * scale;
                const dx = (cw - dw) / 2;
                const dy = (ch - dh) / 2;
                ctx2d.clearRect(0, 0, cw, ch);
                ctx2d.drawImage(video, 0, 0, vw, halfH, dx, dy, dw, dh);
            };
        }
    }

    if (!renderFrameFn || !resizeCanvasFn) return;

    function stopRenderLoop() {
        if (rafId !== null) {
            cancelAnimationFrame(rafId);
            rafId = null;
        }
        if (vfcId !== null && typeof video.cancelVideoFrameCallback === 'function') {
            video.cancelVideoFrameCallback(vfcId);
            vfcId = null;
        }
    }

    function startRenderLoop() {
        if (!isInView) return;
        stopRenderLoop();

        if (typeof video.requestVideoFrameCallback === 'function') {
            const onVideoFrame = () => {
                if (!isInView) {
                    vfcId = null;
                    return;
                }
                renderFrameFn(true);
                vfcId = video.requestVideoFrameCallback(onVideoFrame);
            };
            vfcId = video.requestVideoFrameCallback(onVideoFrame);
        } else {
            const onRaf = () => {
                if (!isInView) {
                    rafId = null;
                    return;
                }
                renderFrameFn(false);
                rafId = requestAnimationFrame(onRaf);
            };
            rafId = requestAnimationFrame(onRaf);
        }
    }

    video.addEventListener('loadedmetadata', () => {
        resizeCanvasFn();
        renderFrameFn(true);
    });

    video.addEventListener('loadeddata', () => {
        renderFrameFn(true);
        if (isInView) {
            video.play().catch(() => {});
            startRenderLoop();
        }
    });

    window.addEventListener('resize', () => {
        resizeCanvasFn();
        renderFrameFn(true);
    }, { passive: true });

    if (typeof IntersectionObserver !== 'undefined') {
        const preloadObserver = new IntersectionObserver((entries) => {
            if (entries.some(e => e.isIntersecting)) {
                loadVideoSource();
                preloadObserver.disconnect();
            }
        }, { rootMargin: '800px 0px' });
        preloadObserver.observe(footerSection);

        const playbackObserver = new IntersectionObserver((entries) => {
            isInView = entries.some(e => e.isIntersecting);
            if (isInView && !document.hidden) {
                loadVideoSource();
                resizeCanvasFn();
                video.play().catch(() => {});
                startRenderLoop();
            } else {
                video.pause();
                stopRenderLoop();
            }
        }, { rootMargin: '60px 0px' });
        playbackObserver.observe(footerSection);

        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                video.pause();
                stopRenderLoop();
            } else if (isInView) {
                video.play().catch(() => {});
                startRenderLoop();
            }
        });
    } else {
        loadVideoSource();
        isInView = true;
        resizeCanvasFn();
        video.play().catch(() => {});
        startRenderLoop();
    }
}

// footer cursor button
function initFooterCursorBtn() {
    const footerSection = document.getElementById('footerSection');
    const footerZone = document.getElementById('footerCursorZone');
    const footerCursorBtn = document.getElementById('footerCursorBtn');
    const header = document.querySelector('header.navbar');

    if (!footerZone || !footerCursorBtn || window.innerWidth <= 1024) return;

    if (!document.getElementById('forceHideCursorStyle')) {
        const style = document.createElement('style');
        style.id = 'forceHideCursorStyle';
        style.innerHTML = `
            .force-hide-cursor, .force-hide-cursor * {
                cursor: none !important;
            }
        `;
        document.head.appendChild(style);
    }

    let mouseX = 0, mouseY = 0;
    let btnX = 0, btnY = 0;
    let cursorRafId = null;
    let cursorVisible = false;
    let cursorScrollTicking = false;

    function animateCursor() {
        const dx = mouseX - btnX;
        const dy = mouseY - btnY;
        if (!cursorVisible || (Math.abs(dx) < 0.25 && Math.abs(dy) < 0.25)) {
            btnX = mouseX;
            btnY = mouseY;
            footerCursorBtn.style.left = btnX + 'px';
            footerCursorBtn.style.top = btnY + 'px';
            cursorRafId = null;
            return;
        }
        btnX += dx * 0.14;
        btnY += dy * 0.14;
        footerCursorBtn.style.left = btnX + 'px';
        footerCursorBtn.style.top = btnY + 'px';
        cursorRafId = requestAnimationFrame(animateCursor);
    }

    function ensureCursorLoop() {
        if (cursorRafId === null && cursorVisible) {
            cursorRafId = requestAnimationFrame(animateCursor);
        }
    }

    function isMouseOverHeader(clientY) {
        if (!header) return false;
        const headerRect = header.getBoundingClientRect();
        return clientY >= headerRect.top && clientY <= headerRect.bottom;
    }

    function updateFooterCursorVisibility(clientX, clientY) {
        const isTextRevealed = footerSection && footerSection.classList.contains('is-text-revealed');
        if (!isTextRevealed) {
            if (cursorVisible) {
                cursorVisible = false;
                footerCursorBtn.classList.remove('is-visible');
                footerZone.classList.remove('force-hide-cursor');
            }
            return;
        }

        const zoneRect = footerZone.getBoundingClientRect();
        const textWrap = document.querySelector('.footer-text-wrap');
        const triggerTop = textWrap ? textWrap.getBoundingClientRect().top : zoneRect.top;

        const isInZone = clientX >= zoneRect.left && clientX <= zoneRect.right
            && clientY >= triggerTop && clientY <= zoneRect.bottom;
        const overHeader = isMouseOverHeader(clientY);
        const contactOverlay = document.getElementById('contactOverlay');
        const isContactOpen = contactOverlay && contactOverlay.classList.contains('active');

        if (isInZone && !overHeader && !isContactOpen) {
            if (!cursorVisible) {
                cursorVisible = true;
                btnX = clientX;
                btnY = clientY;
                footerCursorBtn.style.left = btnX + 'px';
                footerCursorBtn.style.top = btnY + 'px';
            }
            footerCursorBtn.classList.add('is-visible');
            footerZone.classList.add('force-hide-cursor');
            footerCursorBtn.classList.toggle('dark', footerSection.classList.contains('dark-mode'));
            ensureCursorLoop();
        } else if (cursorVisible) {
            cursorVisible = false;
            footerCursorBtn.classList.remove('is-visible');
            footerZone.classList.remove('force-hide-cursor');
        }
    }

    document.addEventListener('mousemove', (e) => {
        mouseX = e.clientX;
        mouseY = e.clientY;
        updateFooterCursorVisibility(mouseX, mouseY);
    }, { passive: true });

    window.addEventListener('scroll', () => {
        if (!footerSection || !footerSection.classList.contains('is-text-revealed')) {
            if (cursorVisible) updateFooterCursorVisibility(mouseX, mouseY);
            return;
        }
        if (!cursorScrollTicking) {
            cursorScrollTicking = true;
            requestAnimationFrame(() => {
                cursorScrollTicking = false;
                updateFooterCursorVisibility(mouseX, mouseY);
            });
        }
    }, { passive: true });

    footerCursorBtn.addEventListener('click', () => {
        footerCursorBtn.classList.remove('is-visible');
        footerZone.classList.remove('force-hide-cursor');
    });

    footerZone.addEventListener('click', (e) => {
        if (!footerCursorBtn.classList.contains('is-visible')) return;
        if (e.target.closest('a, button')) return;
        if (isMouseOverHeader(e.clientY)) return;
        e.preventDefault();
        footerCursorBtn.click();
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initFooterScrollText();
    initFooterBgVideo();
    initFooterCursorBtn();
});

// Blog slider
const blogTrack = document.getElementById('blogTrack');
const blogPrev = document.getElementById('blogPrev');
const blogNext = document.getElementById('blogNext');

if (blogTrack && blogPrev && blogNext) {
    const blogContainer = blogTrack.closest('.blog-slider-container') || blogTrack.parentElement;
    let isBlogAnimating = false;
    const blogTransitionTime = 600;

    blogPrev.classList.remove('disabled');
    blogNext.classList.remove('disabled');

    let originalCards = Array.from(blogTrack.querySelectorAll('.blog-card'));
    if (originalCards.length > 0) {
        for (let i = 0; i < 2; i++) {
            originalCards.forEach(card => {
                let clone = card.cloneNode(true);
                blogTrack.appendChild(clone);
            });
        }
    }

    function getBlogStep() {
        const firstCard = blogTrack.querySelector('.blog-card');
        if (!firstCard) return 0;
        const cardWidth = firstCard.offsetWidth;
        const gap = parseInt(window.getComputedStyle(blogTrack).gap, 10) || 24;
        return cardWidth + gap;
    }

    function goBlogNext(duration = blogTransitionTime) {
        if (isBlogAnimating) return;
        const currentCards = Array.from(blogTrack.querySelectorAll('.blog-card'));
        if (currentCards.length <= 1) return;
        isBlogAnimating = true;

        const moveAmount = getBlogStep();
        blogTrack.style.transition = `transform ${duration}ms ease-in-out`;
        blogTrack.style.transform = `translate3d(-${moveAmount}px, 0, 0)`;

        setTimeout(() => {
            blogTrack.appendChild(currentCards[0]);
            blogTrack.style.transition = 'none';
            blogTrack.style.transform = 'translate3d(0, 0, 0)';
            isBlogAnimating = false;
        }, duration);
    }

    function goBlogPrev(alreadyPrepended = false, duration = blogTransitionTime) {
        if (isBlogAnimating) return;
        const currentCards = Array.from(blogTrack.querySelectorAll('.blog-card'));
        if (currentCards.length <= 1) return;
        isBlogAnimating = true;

        if (!alreadyPrepended) {
            const moveAmount = getBlogStep();
            const lastCard = currentCards[currentCards.length - 1];
            blogTrack.insertBefore(lastCard, currentCards[0]);

            blogTrack.style.transition = 'none';
            blogTrack.style.transform = `translate3d(-${moveAmount}px, 0, 0)`;
            blogTrack.offsetHeight;
        }

        blogTrack.style.transition = `transform ${duration}ms ease-in-out`;
        blogTrack.style.transform = 'translate3d(0, 0, 0)';

        setTimeout(() => {
            isBlogAnimating = false;
        }, duration);
    }

    blogNext.addEventListener('click', () => goBlogNext());
    blogPrev.addEventListener('click', () => goBlogPrev());

    // Swipe & drag support (touch, mouse drag, horizontal trackpad swipe)
    let isPointerDown = false;
    let isHorizontalDrag = false;
    let dragDirectionLocked = false;
    let prependedForRightDrag = false;
    let suppressNextClick = false;
    let dragStartX = 0;
    let dragStartY = 0;
    let dragDeltaX = 0;
    let dragStartTime = 0;

    function startBlogDrag(clientX, clientY) {
        if (isBlogAnimating) return;
        const currentCards = blogTrack.querySelectorAll('.blog-card');
        if (currentCards.length <= 1) return;

        isPointerDown = true;
        isHorizontalDrag = false;
        dragDirectionLocked = false;
        prependedForRightDrag = false;
        dragStartX = clientX;
        dragStartY = clientY;
        dragDeltaX = 0;
        dragStartTime = performance.now();
    }

    function moveBlogDrag(clientX, clientY, touchEvent = null) {
        if (!isPointerDown || isBlogAnimating) return;

        const dx = clientX - dragStartX;
        const dy = clientY - dragStartY;

        if (!dragDirectionLocked) {
            if (Math.abs(dx) < 8 && Math.abs(dy) < 8) return;
            dragDirectionLocked = true;
            if (Math.abs(dy) > Math.abs(dx)) {
                isPointerDown = false;
                isHorizontalDrag = false;
                return;
            }
            isHorizontalDrag = true;
            suppressNextClick = true;
            if (blogContainer) blogContainer.classList.add('is-dragging');
        }

        if (!isHorizontalDrag) return;
        if (touchEvent && touchEvent.cancelable) {
            touchEvent.preventDefault();
        }

        const step = getBlogStep();
        if (!step) return;

        dragDeltaX = Math.max(-step * 0.95, Math.min(step * 0.95, dx));

        if (dragDeltaX > 0 && !prependedForRightDrag) {
            const cards = Array.from(blogTrack.querySelectorAll('.blog-card'));
            blogTrack.insertBefore(cards[cards.length - 1], cards[0]);
            prependedForRightDrag = true;
        } else if (dragDeltaX <= 0 && prependedForRightDrag) {
            const cards = Array.from(blogTrack.querySelectorAll('.blog-card'));
            blogTrack.appendChild(cards[0]);
            prependedForRightDrag = false;
        }

        const baseX = prependedForRightDrag ? -step : 0;
        blogTrack.style.transition = 'none';
        blogTrack.style.transform = `translate3d(${baseX + dragDeltaX}px, 0, 0)`;
    }

    function endBlogDrag() {
        if (!isPointerDown) return;
        isPointerDown = false;
        if (blogContainer) blogContainer.classList.remove('is-dragging');

        if (!isHorizontalDrag) return;
        isHorizontalDrag = false;

        const step = getBlogStep();
        const elapsed = performance.now() - dragStartTime;
        const isQuickFlick = elapsed < 260 && Math.abs(dragDeltaX) > 28;
        const threshold = Math.min(step * 0.18, 60);

        if (dragDeltaX < -threshold || (isQuickFlick && dragDeltaX < 0)) {
            if (prependedForRightDrag) {
                const cards = Array.from(blogTrack.querySelectorAll('.blog-card'));
                blogTrack.appendChild(cards[0]);
                prependedForRightDrag = false;
            }
            goBlogNext(420);
        } else if (dragDeltaX > threshold || (isQuickFlick && dragDeltaX > 0)) {
            goBlogPrev(prependedForRightDrag, 420);
            prependedForRightDrag = false;
        } else if (prependedForRightDrag) {
            isBlogAnimating = true;
            blogTrack.style.transition = 'transform 260ms ease-out';
            blogTrack.style.transform = `translate3d(-${step}px, 0, 0)`;
            setTimeout(() => {
                const cards = Array.from(blogTrack.querySelectorAll('.blog-card'));
                blogTrack.appendChild(cards[0]);
                blogTrack.style.transition = 'none';
                blogTrack.style.transform = 'translate3d(0, 0, 0)';
                prependedForRightDrag = false;
                isBlogAnimating = false;
            }, 260);
        } else {
            blogTrack.style.transition = 'transform 260ms ease-out';
            blogTrack.style.transform = 'translate3d(0, 0, 0)';
        }

        dragDeltaX = 0;
        setTimeout(() => {
            suppressNextClick = false;
        }, 50);
    }

    const swipeTarget = blogContainer || blogTrack;

    swipeTarget.addEventListener('dragstart', (e) => {
        e.preventDefault();
    });

    swipeTarget.addEventListener('click', (e) => {
        if (suppressNextClick) {
            e.preventDefault();
            e.stopPropagation();
            suppressNextClick = false;
        }
    }, true);

    swipeTarget.addEventListener('touchstart', (e) => {
        if (e.touches.length !== 1) return;
        startBlogDrag(e.touches[0].clientX, e.touches[0].clientY);
    }, { passive: true });

    swipeTarget.addEventListener('touchmove', (e) => {
        if (e.touches.length !== 1) return;
        moveBlogDrag(e.touches[0].clientX, e.touches[0].clientY, e);
    }, { passive: false });

    swipeTarget.addEventListener('touchend', endBlogDrag);
    swipeTarget.addEventListener('touchcancel', endBlogDrag);

    swipeTarget.addEventListener('mousedown', (e) => {
        if (e.button !== 0) return;
        startBlogDrag(e.clientX, e.clientY);
    });

    window.addEventListener('mousemove', (e) => {
        moveBlogDrag(e.clientX, e.clientY);
    });

    window.addEventListener('mouseup', endBlogDrag);

    let lastBlogWheelTime = 0;
    swipeTarget.addEventListener('wheel', (e) => {
        if (isBlogAnimating) return;
        if (Math.abs(e.deltaX) <= Math.abs(e.deltaY) || Math.abs(e.deltaX) < 25) return;
        const currentCards = blogTrack.querySelectorAll('.blog-card');
        if (currentCards.length <= 1) return;

        e.preventDefault();
        const now = performance.now();
        if (now - lastBlogWheelTime < 700) return;
        lastBlogWheelTime = now;

        if (e.deltaX > 0) {
            goBlogNext();
        } else {
            goBlogPrev();
        }
    }, { passive: false });

    window.addEventListener('resize', () => {
        blogTrack.style.transition = 'none';
        blogTrack.style.transform = 'translate3d(0, 0, 0)';
    }, { passive: true });
}

document.addEventListener('DOMContentLoaded', () => {
    const links = document.querySelectorAll('a');
    const contactTriggers = Array.from(links).filter(a => {
        if (a.classList.contains('nav-contact-trigger')) return true;
        const text = a.textContent.trim().toLowerCase();
        return text === 'contact' || text === 'liên hệ';
    });

    if (contactTriggers.length === 0) return;

    if (document.getElementById('contactSidebar')) {
        initContactPopup(contactTriggers);
    } else {
        fetch('contact.html')
            .then(response => {
                if (!response.ok) throw new Error('Network error');
                return response.text();
            })
            .then(html => {
                document.body.insertAdjacentHTML('beforeend', html);
                initContactPopup(contactTriggers);
            })
            .catch(err => console.error('Lỗi load contact:', err));
    }
});

function initContactPopup(triggers) {
    const overlay = document.getElementById('contactOverlay');
    const sidebar = document.getElementById('contactSidebar');
    const closeBtn = document.getElementById('contactClose');

    const openPopup = (e) => {
        if (e) e.preventDefault();
        overlay.classList.add('active');
        sidebar.classList.add('active');
        document.documentElement.style.overflow = 'hidden';
        if (window.lenis) window.lenis.stop();
    };

    triggers.forEach(trigger => {
        trigger.addEventListener('click', openPopup);
    });

    const closePopup = () => {
        overlay.classList.remove('active');
        sidebar.classList.remove('active');
        const mobileMenu = document.getElementById('mobileMenu');
        if (!(mobileMenu && mobileMenu.classList.contains('open'))) {
            document.documentElement.style.overflow = '';
            if (window.lenis) window.lenis.start();
        }
    };

    closeBtn.addEventListener('click', closePopup);
    overlay.addEventListener('click', closePopup);

    const optionGroups = document.querySelectorAll('.contact-options');
    optionGroups.forEach(group => {
        const isMultiSelect = group.classList.contains('group-1');
        const pills = group.querySelectorAll('.contact-pill');
        pills.forEach(pill => {
            if (pill.dataset.pillBound) return;
            pill.dataset.pillBound = '1';
            pill.addEventListener('click', () => {
                if (isMultiSelect) {
                    const pillText = pill.textContent.trim().toLowerCase();
                    const isKhac = pill.dataset.other === '1' || pillText === 'khác' || pillText === 'other';
                    
                    if (isKhac) {
                        const wasActive = pill.classList.contains('active');
                        pills.forEach(p => p.classList.remove('active'));
                        if (!wasActive) {
                            pill.classList.add('active');
                        }
                    } else {
                        const khacPill = Array.from(pills).find(p => {
                            const t = p.textContent.trim().toLowerCase();
                            return p.dataset.other === '1' || t === 'khác' || t === 'other';
                        });
                        if (khacPill) {
                            khacPill.classList.remove('active');
                        }
                        pill.classList.toggle('active');
                    }
                } else {
                    pills.forEach(p => p.classList.remove('active'));
                    pill.classList.add('active');
                }
            });
        });
    });
}

// Lenis Scroll
const lenis = new Lenis({
    lerp: 0.09,
    smoothWheel: true,
    syncTouch: false,
    wheelMultiplier: 1.05,
});
window.lenis = lenis;

function raf(time) {
    lenis.raf(time);
    requestAnimationFrame(raf);
}

requestAnimationFrame(raf);

// dynamic header color on dark sections
function initDynamicHeader() {
    const header = document.querySelector('header.navbar');
    const darkSections = document.querySelectorAll('.project-showcase, .footer-section, .final-footer-section');

    if (!header || darkSections.length === 0) return;

    let headerTicking = false;
    let lastIsDark = null;

    function checkHeaderBackground() {
        headerTicking = false;
        const headerRect = header.getBoundingClientRect();
        const headerCenter = headerRect.top + headerRect.height / 2;

        let isDark = document.body.classList.contains('dark-mode');
        if (!isDark) {
            for (let i = 0; i < darkSections.length; i++) {
                const rect = darkSections[i].getBoundingClientRect();
                if (headerCenter >= rect.top && headerCenter <= rect.bottom) {
                    isDark = true;
                    break;
                }
            }
        }

        if (isDark !== lastIsDark) {
            lastIsDark = isDark;
            header.classList.toggle('navbar-dark', isDark);
        }
    }

    function scheduleHeaderCheck() {
        if (!headerTicking) {
            headerTicking = true;
            requestAnimationFrame(checkHeaderBackground);
        }
    }

    window.addEventListener('scroll', scheduleHeaderCheck, { passive: true });
    window.addEventListener('resize', scheduleHeaderCheck, { passive: true });

    setTimeout(checkHeaderBackground, 100);
}

document.addEventListener('DOMContentLoaded', initDynamicHeader);
