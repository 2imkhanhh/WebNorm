// HAMBURGER & MENU MOBILE
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
            mobileMenu.classList.remove('open');
            hamburger.classList.remove('active');
            document.body.style.overflow = '';
        });
    });
}

// LANGUAGE TOGGLE
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

// REVEAL TEXT
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

// ACTIVE STATUS SERVICES
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

// FOOTER SCROLL TEXT
function initFooterScrollText() {
    const footerSection = document.getElementById('footerSection');
    const footerLines = document.querySelectorAll('.footer-line');
    const blogSection = document.querySelector('.blog-slider-section, .members-section');

    if (!footerSection || footerLines.length === 0) return;

    let targetFooterProgress = 0;
    let currentFooterProgress = 0;
    let footerSnapTimeout;
    let lastScrollY = window.scrollY;

    function renderFooterSmooth() {
        currentFooterProgress += (targetFooterProgress - currentFooterProgress) * 0.05;

        footerLines.forEach((line) => {
            const textNew = line.querySelector('.text-new');
            if (!textNew) return;
            let progress = Math.max(0, Math.min(1, currentFooterProgress));
            const insetTop = (1 - progress) * 100;
            textNew.style.webkitClipPath = `inset(${insetTop}% 0 0 0)`;
            textNew.style.clipPath = `inset(${insetTop}% 0 0 0)`;
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
            if (blogSection) blogSection.classList.add('dark-mode');
        } else {
            footerSection.classList.remove('dark-mode');
            if (blogSection) blogSection.classList.remove('dark-mode');
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
                    snapTargetY = targetFooterProgress >= 0.1 ? footerTop + maxScroll : footerTop;
                } else {
                    snapTargetY = targetFooterProgress <= 0.9 ? footerTop : footerTop + maxScroll;
                }
                if (Math.abs(scrollY - snapTargetY) > 5) {
                    window.scrollTo({ top: snapTargetY, behavior: 'smooth' });
                }
            }, 600);
        }
    }, { passive: true });
}

// FOOTER CURSOR BUTTON 
function initFooterCursorBtn() {
    const footerSection = document.getElementById('footerSection');
    const footerZone = document.getElementById('footerCursorZone');
    const footerCursorBtn = document.getElementById('footerCursorBtn');
    const textWrap = document.querySelector('.footer-text-wrap'); 

    if (!footerZone || !footerCursorBtn || window.innerWidth <= 1024) return;

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

    document.addEventListener('mousemove', (e) => {
        mouseX = e.clientX;
        mouseY = e.clientY;

        const zoneRect = footerZone.getBoundingClientRect();
        
        let triggerTop = zoneRect.top; 
        if (textWrap) {
            triggerTop = textWrap.getBoundingClientRect().top;
        }

        const isInZone = e.clientX >= zoneRect.left && e.clientX <= zoneRect.right
            && e.clientY >= triggerTop && e.clientY <= zoneRect.bottom;

        if (isInZone) {
            footerCursorBtn.classList.add('is-visible');
            if (footerSection && footerSection.classList.contains('dark-mode')) {
                footerCursorBtn.classList.add('dark');
            } else {
                footerCursorBtn.classList.remove('dark');
            }
        } else {
            footerCursorBtn.classList.remove('is-visible');
        }
    });

    window.addEventListener('scroll', () => {
        const zoneRect = footerZone.getBoundingClientRect();
        
        let triggerTop = zoneRect.top;
        if (textWrap) {
            triggerTop = textWrap.getBoundingClientRect().top;
        }

        const isInZone = mouseX >= zoneRect.left && mouseX <= zoneRect.right
            && mouseY >= triggerTop && mouseY <= zoneRect.bottom;
            
        if (!isInZone) {
            footerCursorBtn.classList.remove('is-visible');
        }
    }, { passive: true });
}

document.addEventListener('DOMContentLoaded', () => {
    initFooterScrollText();
    initFooterCursorBtn();
});