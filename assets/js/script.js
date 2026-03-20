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