// menu mobile
const hamburger = document.querySelector('.nav-hamburger');
const mobileMenu = document.getElementById('mobileMenu');

if (hamburger && mobileMenu) {
    hamburger.addEventListener('click', () => {
        const isOpen = mobileMenu.classList.toggle('open');
        hamburger.classList.toggle('active', isOpen);
        document.body.style.overflow = isOpen ? 'hidden' : '';
    });

    document.querySelectorAll('.mobile-menu-item').forEach(link => {
        link.addEventListener('click', () => {
            const text = link.textContent.trim().toLowerCase();
            if (text === 'contact' || text === 'liên hệ' || text === 'vn' || text === 'en') {
                return;
            }
            mobileMenu.classList.remove('open');
            hamburger.classList.remove('active');
            document.body.style.overflow = '';
        });
    });
}

// language toogle
const langToggleMobile = document.getElementById('langToggleMobile');
const langToggle = document.getElementById('langToggle');

if (langToggleMobile && langToggle) {
    langToggleMobile.addEventListener('click', function (e) {
        e.preventDefault();
        const newLang = this.textContent.trim() === 'VN' ? 'EN' : 'VN';
        this.textContent = newLang;
        langToggle.textContent = newLang;
    });

    langToggle.addEventListener('click', function (e) {
        e.preventDefault();
        this.textContent = this.textContent.trim() === 'VN' ? 'EN' : 'VN';
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

    function updateChars() {
        const windowH = window.innerHeight;
        const scrollY = window.scrollY || window.pageYOffset;
        const rect = revealTextEl.getBoundingClientRect();
        const elemAbsTop = rect.top + scrollY;

        const rawStart = elemAbsTop - windowH + (windowH * 0.15);
        const animEnd = elemAbsTop + (windowH * -0.1);
        const animStart = Math.min(rawStart, animEnd - 50);
        const clampedStart = Math.max(animStart, 0);

        if (scrollY <= clampedStart) {
            charEls.forEach(el => el.classList.remove('lit'));
            return;
        }

        if (scrollY >= animEnd) {
            charEls.forEach(el => el.classList.add('lit'));
            return;
        }

        const progress = (scrollY - clampedStart) / (animEnd - clampedStart);
        const litCount = Math.round(progress * charEls.length);

        charEls.forEach((el, i) => {
            el.classList.toggle('lit', i < litCount);
        });
    }

    
    window.addEventListener('scroll', updateChars, { passive: true });
    window.addEventListener('resize', updateChars, { passive: true });

    requestAnimationFrame(() => {
        requestAnimationFrame(updateChars);
    });
});

// active status services
const serviceItems = document.querySelectorAll('.service-item');

function calculateActiveService() {
    let activeItem = null;
    let minDistance = Infinity;
    const viewportCenter = window.innerHeight / 2;

    serviceItems.forEach(item => {
        const rect = item.getBoundingClientRect();
        const itemCenter = rect.top + rect.height / 2;
        const distance = Math.abs(viewportCenter - itemCenter);

        if (distance < minDistance) {
            minDistance = distance;
            activeItem = item;
        }
    });

    serviceItems.forEach(item => {
        const rect = item.getBoundingClientRect();
        const isVisible = rect.top < window.innerHeight && rect.bottom > 0;

        if (item === activeItem && isVisible) {
            item.classList.add('active');
        } else {
            item.classList.remove('active');
        }
    });
}

window.addEventListener('scroll', calculateActiveService, { passive: true });
window.addEventListener('resize', calculateActiveService, { passive: true });
document.addEventListener('DOMContentLoaded', calculateActiveService);

// footer scroll text
function initFooterScrollText() {
    const footerSection = document.getElementById('footerSection');
    const footerLines = document.querySelectorAll('.footer-line');
    const sectionsToDarken = document.querySelectorAll('.blog-slider-section, .members-section, .work-list-section, .explore-section, .project-credits-section, .news-grid-section');

    if (!footerSection || footerLines.length === 0) return;

    let targetFooterProgress = 0;
    let currentFooterProgress = 0;
    let footerSnapTimeout;
    let lastScrollY = window.scrollY;

    function renderFooterSmooth() {
        currentFooterProgress += (targetFooterProgress - currentFooterProgress) * 0.15;

        if (currentFooterProgress > 0.6) {
            footerSection.classList.add('is-text-revealed');
        } else {
            footerSection.classList.remove('is-text-revealed');
        }

        footerLines.forEach((line) => {
            const textNew = line.querySelector('.text-new');
            const textOld = line.querySelector('.text-old');

            if (!textNew || !textOld) return;

            let progress = Math.max(0, Math.min(1, currentFooterProgress * 1.25));

            const insetTop = (1 - progress) * 100;
            textNew.style.webkitClipPath = `inset(${insetTop}% -10% -10% -10%)`;
            textNew.style.clipPath = `inset(${insetTop}% -10% -10% -10%)`;

            const insetBottom = progress * 100;
            textOld.style.webkitClipPath = `inset(-10% -10% ${insetBottom}% -10%)`;
            textOld.style.clipPath = `inset(-10% -10% ${insetBottom}% -10%)`;
        });

        requestAnimationFrame(renderFooterSmooth);
    }

    requestAnimationFrame(renderFooterSmooth);

    window.addEventListener('scroll', () => {
        const footerTop = footerSection.offsetTop;
        const scrollY = window.scrollY;
        const windowHeight = window.innerHeight;

        const scrollDirection = scrollY > lastScrollY ? 'down' : 'up';
        lastScrollY = scrollY;

        const scrolledIntoFooter = (scrollY + windowHeight) - footerTop;
        const triggerDistance = footerSection.offsetHeight * (window.innerWidth <= 1024 ? 0.25 : 0.2);

        if (scrolledIntoFooter >= triggerDistance) {
            footerSection.classList.add('dark-mode');
            sectionsToDarken.forEach(section => {
                section.classList.add('dark-mode');
            });
        } else {
            footerSection.classList.remove('dark-mode');
            sectionsToDarken.forEach(section => {
                section.classList.remove('dark-mode');
            });
        }

        let scrolledInside = Math.max(0, scrollY - footerTop);
        const maxScroll = footerSection.offsetHeight - windowHeight;
        if (scrolledInside > maxScroll) scrolledInside = maxScroll;

        if (maxScroll > 0) {
            targetFooterProgress = scrolledInside / maxScroll;
        }

        clearTimeout(footerSnapTimeout);

        if (scrollY > footerTop && scrollY < footerTop + maxScroll) {

            footerSnapTimeout = setTimeout(() => {
                let snapTargetY;

                if (scrollDirection === 'down') {
                    snapTargetY = targetFooterProgress >= 0.05 ? footerTop + maxScroll : footerTop;
                } else {
                    snapTargetY = targetFooterProgress <= 0.95 ? footerTop : footerTop + maxScroll;
                }

                if (Math.abs(scrollY - snapTargetY) > 5) {
                    if (typeof lenis !== 'undefined') {
                        lenis.scrollTo(snapTargetY, { duration: 1.5 });
                    }
                }
            }, 50);
        }
    }, { passive: true });
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

    function animateCursor() {
        btnX += (mouseX - btnX) * 0.12;
        btnY += (mouseY - btnY) * 0.12;
        footerCursorBtn.style.left = btnX + 'px';
        footerCursorBtn.style.top = btnY + 'px';
        requestAnimationFrame(animateCursor);
    }
    requestAnimationFrame(animateCursor);

    function isMouseOverHeader(clientY) {
        if (!header) return false;
        const headerRect = header.getBoundingClientRect();
        return clientY >= headerRect.top && clientY <= headerRect.bottom;
    }

    document.addEventListener('mousemove', (e) => {
        mouseX = e.clientX;
        mouseY = e.clientY;

        const zoneRect = footerZone.getBoundingClientRect();
        const textWrap = document.querySelector('.footer-text-wrap');

        let triggerTop = zoneRect.top;
        if (textWrap) {
            triggerTop = textWrap.getBoundingClientRect().top;
        }

        const isInZone = e.clientX >= zoneRect.left && e.clientX <= zoneRect.right
            && e.clientY >= triggerTop && e.clientY <= zoneRect.bottom;

        const isTextRevealed = footerSection && footerSection.classList.contains('is-text-revealed');

        const overHeader = isMouseOverHeader(e.clientY);
        const contactOverlay = document.getElementById('contactOverlay');
        const isContactOpen = contactOverlay && contactOverlay.classList.contains('active');

        if (isInZone && isTextRevealed && !overHeader && !isContactOpen) {
            footerCursorBtn.classList.add('is-visible');
            footerZone.classList.add('force-hide-cursor');

            if (footerSection && footerSection.classList.contains('dark-mode')) {
                footerCursorBtn.classList.add('dark');
            } else {
                footerCursorBtn.classList.remove('dark');
            }
        } else {
            footerCursorBtn.classList.remove('is-visible');
            footerZone.classList.remove('force-hide-cursor');
        }
    });

    window.addEventListener('scroll', () => {
        const zoneRect = footerZone.getBoundingClientRect();
        const textWrap = document.querySelector('.footer-text-wrap');

        let triggerTop = zoneRect.top;
        if (textWrap) {
            triggerTop = textWrap.getBoundingClientRect().top;
        }

        const isInZone = mouseX >= zoneRect.left && mouseX <= zoneRect.right
            && mouseY >= triggerTop && mouseY <= zoneRect.bottom;

        const isTextRevealed = footerSection && footerSection.classList.contains('is-text-revealed');
        const overHeader = isMouseOverHeader(mouseY);
        const contactOverlay = document.getElementById('contactOverlay');
        const isContactOpen = contactOverlay && contactOverlay.classList.contains('active');

        if (isInZone && isTextRevealed && !overHeader && !isContactOpen) {
            footerCursorBtn.classList.add('is-visible');
            footerZone.classList.add('force-hide-cursor');
        } else {
            footerCursorBtn.classList.remove('is-visible');
            footerZone.classList.remove('force-hide-cursor');
        }
    }, { passive: true });
}

document.addEventListener('DOMContentLoaded', () => {
    initFooterScrollText();
    initFooterCursorBtn();
});

// Blog slider
const blogTrack = document.getElementById('blogTrack');
const blogPrev = document.getElementById('blogPrev');
const blogNext = document.getElementById('blogNext');

if (blogTrack && blogPrev && blogNext) {
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

    blogNext.addEventListener('click', () => {
        if (isBlogAnimating) return;
        isBlogAnimating = true;

        const currentCards = Array.from(blogTrack.querySelectorAll('.blog-card'));
        if (currentCards.length === 0) return;

        const cardWidth = currentCards[0].offsetWidth;
        const gap = parseInt(window.getComputedStyle(blogTrack).gap) || 24;
        const moveAmount = cardWidth + gap;

        blogTrack.style.transition = `transform ${blogTransitionTime}ms ease-in-out`;
        blogTrack.style.transform = `translate3d(-${moveAmount}px, 0, 0)`;

        setTimeout(() => {
            blogTrack.appendChild(currentCards[0]);

            blogTrack.style.transition = 'none';
            blogTrack.style.transform = 'translate3d(0, 0, 0)';
            isBlogAnimating = false;
        }, blogTransitionTime);
    });

    blogPrev.addEventListener('click', () => {
        if (isBlogAnimating) return;
        isBlogAnimating = true;

        const currentCards = Array.from(blogTrack.querySelectorAll('.blog-card'));
        if (currentCards.length === 0) return;

        const cardWidth = currentCards[0].offsetWidth;
        const gap = parseInt(window.getComputedStyle(blogTrack).gap) || 24;
        const moveAmount = cardWidth + gap;

        const lastCard = currentCards[currentCards.length - 1];
        blogTrack.insertBefore(lastCard, currentCards[0]);

        blogTrack.style.transition = 'none';
        blogTrack.style.transform = `translate3d(-${moveAmount}px, 0, 0)`;

        blogTrack.offsetHeight;

        blogTrack.style.transition = `transform ${blogTransitionTime}ms ease-in-out`;
        blogTrack.style.transform = 'translate3d(0, 0, 0)';

        setTimeout(() => {
            isBlogAnimating = false;
        }, blogTransitionTime);
    });

    let startX = 0;
    let currentX = 0;
    let isDragging = false;

    blogTrack.addEventListener('touchstart', (e) => {
        if (isBlogAnimating) return;
        startX = e.touches[0].clientX;
        currentX = startX;
        isDragging = true;
    }, { passive: true });

    blogTrack.addEventListener('touchmove', (e) => {
        if (!isDragging) return;
        currentX = e.touches[0].clientX;
    }, { passive: true });

    blogTrack.addEventListener('touchend', () => {
        if (!isDragging) return;
        isDragging = false;
        handleSwipe();
    });

    function handleSwipe() {
        let diffX = startX - currentX;
        if (diffX > 50) {
            blogNext.click();
        }
        else if (diffX < -50) {
            blogPrev.click();
        }
    }

    window.addEventListener('resize', () => {
        blogTrack.style.transition = 'none';
        blogTrack.style.transform = 'translate3d(0, 0, 0)';
    }, { passive: true });
}

document.addEventListener('DOMContentLoaded', () => {
    const links = document.querySelectorAll('a');
    const contactTriggers = Array.from(links).filter(a => {
        const text = a.textContent.trim().toLowerCase();
        return text === 'contact' || text === 'liên hệ';
    });

    if (contactTriggers.length === 0) return;

    fetch('contact.html')
        .then(response => response.text())
        .then(html => {
            document.body.insertAdjacentHTML('beforeend', html);
            initContactPopup(contactTriggers);
        })
        .catch(err => console.error('Lỗi', err));
});

function initContactPopup(triggers) {
    const overlay = document.getElementById('contactOverlay');
    const sidebar = document.getElementById('contactSidebar');
    const closeBtn = document.getElementById('contactClose');

    const openPopup = (e) => {
        if (e) e.preventDefault();
        overlay.classList.add('active');
        sidebar.classList.add('active');
        document.body.style.overflow = 'hidden';
    };

    triggers.forEach(trigger => {
        trigger.addEventListener('click', openPopup);
    });

    const closePopup = () => {
        overlay.classList.remove('active');
        sidebar.classList.remove('active');
        const mobileMenu = document.getElementById('mobileMenu');
        if (!(mobileMenu && mobileMenu.classList.contains('open'))) {
            document.body.style.overflow = '';
        }
    };

    closeBtn.addEventListener('click', closePopup);
    overlay.addEventListener('click', closePopup);

    const optionGroups = document.querySelectorAll('.contact-options');
    optionGroups.forEach(group => {
        const pills = group.querySelectorAll('.contact-pill');
        pills.forEach(pill => {
            pill.addEventListener('click', () => {
                pills.forEach(p => p.classList.remove('active'));
                pill.classList.add('active');
            });
        });
    });

    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', (e) => {
            e.preventDefault();
            alert('Thông tin đã được gửi!');
            closePopup();
            contactForm.reset();
        });
    }
}

// Lenis Scroll
const lenis = new Lenis({
    lerp: 0.05,
    smoothWheel: true,
    wheelMultiplier: 1.1,
});

function raf(time) {
    lenis.raf(time);
    requestAnimationFrame(raf);
}

requestAnimationFrame(raf);