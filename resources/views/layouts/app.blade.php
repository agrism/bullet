<!doctype html>
<html class="no-js" lang="{{ app()->getLocale() == 'en' ? 'en-US' : (app()->getLocale() == 'ru' ? 'ru-RU' : 'lv-LV') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Primary Meta Tags -->
    <title>{{ $title ?? (app()->getLocale() == 'en' ? 'Law Office BULLET | Legal Assistance and Defense' : (app()->getLocale() == 'ru' ? 'Адвокатское бюро BULLET | Юридическая помощь и защита' : 'Advokātu birojs BULLET | Juridiskā palīdzība un aizstāvība')) }}</title>
    <meta name="title" content="{{ $title ?? (app()->getLocale() == 'en' ? 'Law Office BULLET | Legal Assistance and Defense' : (app()->getLocale() == 'ru' ? 'Адвокатское бюро BULLET | Юридическая помощь и защита' : 'Advokātu birojs BULLET | Juridiskā palīdzība un aizstāvība')) }}">
    <meta name="description" content="{{ $description ?? (app()->getLocale() == 'en' ? 'Law Office BULLET provides legal assistance to businesses and individuals in Latvia and internationally' : (app()->getLocale() == 'ru' ? 'Адвокатское бюро BULLET оказывает юридическую помощь предприятиям и частным лицам в Латвии и на международном уровне' : 'Advokātu birojs BULLET sniedz juridisko palīdzību uzņēmumiem un privātpersonām Latvijā un starptautiski')) }}">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="referrer" content="no-referrer">
    
    <!-- Favicon & Touch Icons -->
    <link rel="icon" href="{{ asset('uploads/2025/02/cropped-bullet_symbol_blue-32x32.png') }}" sizes="32x32">
    <link rel="icon" href="{{ asset('uploads/2025/02/cropped-bullet_symbol_blue-192x192.png') }}" sizes="192x192">
    <link rel="apple-touch-icon" href="{{ asset('uploads/2025/02/cropped-bullet_symbol_blue-180x180.png') }}">
    
    <!-- Canonical & Multilingual Alternates -->
    <link rel="canonical" href="{{ url()->current() }}">
    @if(isset($alternate_urls))
    <link rel="alternate" hreflang="lv" href="{{ $alternate_urls['lv'] }}">
    <link rel="alternate" hreflang="en" href="{{ $alternate_urls['en'] }}">
    <link rel="alternate" hreflang="ru" href="{{ $alternate_urls['ru'] }}">
    <link rel="alternate" hreflang="x-default" href="{{ $alternate_urls['x-default'] }}">
    @else
    <link rel="alternate" hreflang="lv" href="{{ url('/' . (!empty($slug) ? $slug . '/' : '')) }}">
    <link rel="alternate" hreflang="en" href="{{ url('/en/' . (!empty($slug) ? $slug . '/' : '')) }}">
    <link rel="alternate" hreflang="ru" href="{{ url('/ru/' . (!empty($slug) ? $slug . '/' : '')) }}">
    <link rel="alternate" hreflang="x-default" href="{{ url('/' . (!empty($slug) ? $slug . '/' : '')) }}">
    @endif

    <!-- Open Graph / Facebook -->
    <meta property="og:locale" content="{{ app()->getLocale() == 'en' ? 'en_US' : (app()->getLocale() == 'ru' ? 'ru_RU' : 'lv_LV') }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $title ?? (app()->getLocale() == 'en' ? 'Law Office BULLET' : (app()->getLocale() == 'ru' ? 'Адвокатское бюро BULLET' : 'Advokātu birojs BULLET')) }}">
    <meta property="og:description" content="{{ $description ?? (app()->getLocale() == 'en' ? 'Law Office BULLET provides legal assistance' : (app()->getLocale() == 'ru' ? 'Адвокатское бюро BULLET оказывает юридическую помощь' : 'Advokātu birojs BULLET sniedz juridisko palīdzību')) }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ app()->getLocale() == 'en' ? 'Law Office BULLET' : (app()->getLocale() == 'ru' ? 'Адвокатское бюро BULLET' : 'Advokātu birojs BULLET') }}">
    <meta property="og:image" content="{{ $og_image ?? asset('uploads/2026/01/01_DSC_5365-scaled.jpg') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title ?? (app()->getLocale() == 'en' ? 'Law Office BULLET' : (app()->getLocale() == 'ru' ? 'Адвокатское бюро BULLET' : 'Advokātu birojs BULLET')) }}">
    <meta name="twitter:description" content="{{ $description ?? (app()->getLocale() == 'en' ? 'Law Office BULLET provides legal assistance' : (app()->getLocale() == 'ru' ? 'Адвокатское бюро BULLET оказывает юридическую помощь' : 'Advokātu birojs BULLET sniedz juridisko palīdzību')) }}">
    <meta name="twitter:image" content="{{ $og_image ?? asset('uploads/2026/01/01_DSC_5365-scaled.jpg') }}">

    <!-- Structured Data (Schema.org JSON-LD) -->
    <script type="application/ld+json">
    {!! json_encode([
        '@'.'context' => 'https://schema.org',
        '@'.'graph' => [
            [
                '@'.'type' => 'WebPage',
                '@'.'id' => url()->current(),
                'url' => url()->current(),
                'name' => $title ?? (app()->getLocale() == 'en' ? 'Law Office BULLET' : (app()->getLocale() == 'ru' ? 'Адвокатское бюро BULLET' : 'Advokātu birojs BULLET')),
                'description' => $description ?? (app()->getLocale() == 'en' ? 'Law Office BULLET provides legal assistance' : (app()->getLocale() == 'ru' ? 'Адвокатское бюро BULLET оказывает юридическую помощь' : 'Advokātu birojs BULLET sniedz juridisko palīdzību')),
                'inLanguage' => app()->getLocale() == 'en' ? 'en-US' : (app()->getLocale() == 'ru' ? 'ru-RU' : 'lv-LV'),
            ],
            [
                '@'.'type' => 'LegalService',
                '@'.'id' => url('/') . '/#organization',
                'name' => app()->getLocale() == 'en' ? 'Law Office BULLET' : (app()->getLocale() == 'ru' ? 'Адвокатское бюро BULLET' : 'Advokātu birojs BULLET'),
                'url' => url('/'),
                'logo' => asset('uploads/2025/01/Logo-Container.svg'),
                'telephone' => '+371 27 484 000',
                'email' => 'welcome@bullet.legal',
                'address' => [
                    '@'.'type' => 'PostalAddress',
                    'addressLocality' => app()->getLocale() == 'ru' ? 'Рига' : (app()->getLocale() == 'en' ? 'Riga' : 'Rīga'),
                    'addressCountry' => 'LV',
                ],
            ],
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
    
    <!-- Fonts & Preconnects -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('css/swiper-bundle.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/style.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/mobile-dropdown.css') }}" />
    
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        .header__logo img {
            max-height: 40px;
            width: auto;
        }
        img:is([sizes=auto i],[sizes^="auto," i]) {
            contain-intrinsic-size: 3000px 1500px;
        }
        /* Language switcher styling */
        .header__pll {
            position: relative !important;
            display: inline-flex !important;
            align-items: center !important;
            cursor: pointer !important;
            padding: 8px 12px !important;
            z-index: 1000 !important;
            user-select: none !important;
            gap: 6px !important;
        }
        .header__pll:hover .header__select,
        .header__pll.is-open .header__select {
            opacity: 1 !important;
            visibility: visible !important;
            pointer-events: auto !important;
            transform: translateY(0) !important;
            display: block !important;
        }
        .header__select {
            position: absolute !important;
            top: 100% !important;
            right: 0 !important;
            left: auto !important;
            width: auto !important;
            min-width: 68px !important;
            background: #16134a !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            border-radius: 8px !important;
            padding: 6px 10px !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4) !important;
            z-index: 99999 !important;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: all 0.2s ease;
            transform: translateY(-6px);
        }
        .header__select a.header__lang {
            color: #ffffff !important;
            font-size: 15px !important;
            font-weight: 700 !important;
            text-decoration: none !important;
            display: block !important;
            padding: 6px 8px !important;
            text-align: center !important;
            border-radius: 6px !important;
            transition: background 0.2s, color 0.2s !important;
        }
        .header__select a.header__lang:hover {
            background: rgba(255, 255, 255, 0.18) !important;
            color: #ffba00 !important;
        }

        /* Prevent hero content overflow across all locales and screen sizes */
        .hero,
        .hero--type-1,
        .hero--type-2 {
            height: auto !important;
            min-height: auto !important;
        }
        @media print, screen and (min-width: 64em) {
            .hero {
                padding: 160px 0 80px 0 !important;
                min-height: 600px !important;
                height: auto !important;
            }
            .hero--type-2 {
                padding: 160px 0 80px 0 !important;
                min-height: 600px !important;
                height: auto !important;
            }
            .hero__text {
                margin-bottom: 32px !important;
            }
        }
        .hero__corner-icon {
            pointer-events: none;
            max-height: 100%;
        }
    </style>
    @stack('styles')
</head>
<body class="{{ $body_class ?? 'home wp-singular page-template' }}">
<div class="page-wrapper">
    {!! $header ?? '' !!}

    <div class="page-content">
        @yield('content')
    </div>

    {!! $footer ?? '' !!}
</div>

<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/jquery-migrate.min.js') }}"></script>
<script src="{{ asset('js/swiper-bundle.min.js') }}"></script>
<script src="{{ asset('js/app.min.js') }}"></script>
<script src="{{ asset('js/mobile-dropdown.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.header__pll').forEach(function(pll) {
        pll.addEventListener('click', function(e) {
            if (e.target.closest('.header__lang')) {
                return;
            }
            e.stopPropagation();
            this.classList.toggle('is-open');
        });
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.header__pll')) {
            document.querySelectorAll('.header__pll').forEach(function(pll) {
                pll.classList.remove('is-open');
            });
        }
    });
});
</script>

@stack('scripts')
</body>
</html>
