<header class="navbar navbar-expand-lg">
    <div class="navbar-inner">
        <a href="{{ route('about') }}" class="nav-link-item {{ request()->routeIs('about') ? 'active' : '' }}">VỀ CHÚNG TÔI</a>
        <a href="{{ route('work') }}" class="nav-link-item {{ request()->routeIs('work*') ? 'active' : '' }}">DỰ ÁN</a>
        <a href="{{ route('home') }}" class="navbar-logo">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" />
        </a>
        <a href="{{ route('news') }}" class="nav-link-item {{ request()->routeIs('news*') ? 'active' : '' }}">BÀI VIẾT</a>
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
        <a href="{{ route('about') }}" class="mobile-menu-item {{ request()->routeIs('about') ? 'active' : '' }}">VỀ CHÚNG TÔI</a>
        <a href="{{ route('work') }}" class="mobile-menu-item {{ request()->routeIs('work*') ? 'active' : '' }}">DỰ ÁN</a>
        <a href="{{ route('news') }}" class="mobile-menu-item {{ request()->routeIs('news*') ? 'active' : '' }}">BÀI VIẾT</a>
        <a href="#" class="mobile-menu-item">LIÊN HỆ</a>
        <a href="#" class="mobile-menu-item mobile-menu-lang" id="langToggleMobile">VN</a>
    </div>
</div>
