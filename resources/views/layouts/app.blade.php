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
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/contact.css') }}" />
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

    <script src="https://unpkg.com/lenis@1.1.13/dist/lenis.min.js"></script>
    <script src="{{ asset('assets/js/script.js') }}"></script>
    @stack('scripts')
</body>

</html>
