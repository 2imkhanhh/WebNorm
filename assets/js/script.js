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