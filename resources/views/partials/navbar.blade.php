@php
    $isEn = app()->getLocale() === 'en';
    $switchTargetLocale = $isEn ? 'vi' : 'en';
    $switchLabel = $isEn ? 'VN' : 'EN';
    $switchUrl = \App\Helpers\NormHelper::switchLocaleUrl($switchTargetLocale);
@endphp
<header class="navbar navbar-expand-lg">
    <div class="navbar-inner">
        <a href="{{ \App\Helpers\NormHelper::localizedRoute('about') }}" class="nav-link-item {{ request()->routeIs('about', 'en.about') ? 'active' : '' }}">{{ __('site.nav_about') }}</a>
        <a href="{{ \App\Helpers\NormHelper::localizedRoute('work') }}" class="nav-link-item {{ request()->routeIs('work*', 'en.work*') ? 'active' : '' }}">{{ __('site.nav_work') }}</a>
        <a href="{{ \App\Helpers\NormHelper::localizedRoute('home') }}" class="navbar-logo">
            <img src="{{ !empty($settings['logo']) ? asset($settings['logo']) : asset('assets/images/logo.png') }}" alt="Logo" />
        </a>
        <a href="{{ \App\Helpers\NormHelper::localizedRoute('news') }}" class="nav-link-item {{ request()->routeIs('news*', 'en.news*') ? 'active' : '' }}">{{ __('site.nav_news') }}</a>
        <div class="nav-contact-group">
            <a href="#" class="nav-link-item nav-contact-trigger">{{ __('site.nav_contact') }}</a>
            <a href="{{ $switchUrl }}" class="nav-link-item lang-toggle" id="langToggle" hreflang="{{ $switchTargetLocale }}">{{ $switchLabel }}</a>
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
        <a href="{{ \App\Helpers\NormHelper::localizedRoute('about') }}" class="mobile-menu-item {{ request()->routeIs('about', 'en.about') ? 'active' : '' }}">{{ __('site.nav_about') }}</a>
        <a href="{{ \App\Helpers\NormHelper::localizedRoute('work') }}" class="mobile-menu-item {{ request()->routeIs('work*', 'en.work*') ? 'active' : '' }}">{{ __('site.nav_work') }}</a>
        <a href="{{ \App\Helpers\NormHelper::localizedRoute('news') }}" class="mobile-menu-item {{ request()->routeIs('news*', 'en.news*') ? 'active' : '' }}">{{ __('site.nav_news') }}</a>
        <a href="#" class="mobile-menu-item nav-contact-trigger">{{ __('site.nav_contact') }}</a>
        <a href="{{ $switchUrl }}" class="mobile-menu-item mobile-menu-lang" id="langToggleMobile" hreflang="{{ $switchTargetLocale }}">{{ $switchLabel }}</a>
    </div>
</div>
