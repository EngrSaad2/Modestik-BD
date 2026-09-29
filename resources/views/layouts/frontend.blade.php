<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- SEO Meta Tags -->
    <title>@yield('title', 'Modestik - Premium Mehendi, Nail Art & Modest Beauty Shop in Bangladesh')</title>
    <meta name="description" content="@yield('meta_description', 'Modestik — Dream of Muslimah. Shop 100% natural & organic mehendi, nail powder, halal beauty products. Pure, chemical-free, premium quality. Free shipping across Bangladesh.')">
    <meta name="keywords" content="@yield('meta_keywords', 'Modestik, organic mehendi, natural henna, halal beauty, nail powder, modest beauty, muslimah beauty, mehndi Bangladesh, chemical free mehendi, মেহেদি')">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', 'Modestik - Premium Mehendi, Nail Art & Modest Beauty Shop in Bangladesh')">
    <meta property="og:description" content="@yield('meta_description', 'Modestik — Dream of Muslimah. Shop 100% natural & organic mehendi, nail powder, halal beauty products. Pure, chemical-free, premium quality. Free shipping across Bangladesh.')">
    <meta property="og:image" content="{{ asset('images/seo-share.webp') }}">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="@yield('title', 'Modestik - Premium Mehendi, Nail Art & Modest Beauty Shop in Bangladesh')">
    <meta name="twitter:description" content="@yield('meta_description', 'Modestik — Dream of Muslimah. Shop 100% natural & organic mehendi, nail powder, halal beauty products. Pure, chemical-free, premium quality. Free shipping across Bangladesh.')">
    <meta name="twitter:image" content="{{ asset('images/seo-share.webp') }}">

    @yield('meta_tags')

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" media="print" onload="this.media='all'">

    <!-- Preconnect CDN origins -->
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="preconnect" href="https://code.jquery.com" crossorigin>
    <link rel="preconnect" href="https://unpkg.com" crossorigin>

    <!-- Preload LCP image (first slider banner) -->
    @if(isset($sliders) && $sliders->count() > 0)
    @php
        $lcpWebp = preg_replace('/\.(png|jpe?g)$/i', '.webp', $sliders->first()->image);
        $lcpSrc = file_exists(storage_path('app/public/' . $lcpWebp)) ? asset('storage/' . $lcpWebp) : asset('storage/' . $sliders->first()->image);
    @endphp
    <link rel="preload" as="image" href="{{ $lcpSrc }}" fetchpriority="high">
    @endif

    <!-- Preload custom CSS -->
    <link rel="preload" href="{{ asset('css/frontend.css') }}?v=1.9" as="style">

    <!-- Critical inline CSS (above-fold styles to prevent render-blocking) -->
    <style>
        :root{--primary:#d97d8c;--primary-dark:#c25f6f;--primary-light:#fff1f2;--primary-gradient:linear-gradient(135deg,#d97d8c,#e89da8);--accent:#b05b68;--dark:#2d2325;--gray-900:#1f1819;--gray-100:#fffbfb;--gray-200:#f9f2f2;--gray-400:#cbbabc;--gray-500:#a39092;--gray-600:#7b686a;--gray-700:#554447;--white:#fff;--shadow-sm:0 1px 3px rgba(45,35,37,.08);--shadow-md:0 4px 12px rgba(45,35,37,.1);--radius-sm:6px;--radius-md:10px;--radius-lg:16px;--radius-xl:24px;--transition:all .3s ease;--font-family:'Inter',-apple-system,BlinkMacSystemFont,sans-serif}
        .owl-carousel:not(.owl-loaded) {
            display: flex !important;
            overflow: hidden !important;
            max-height: 600px !important;
        }
        .owl-carousel:not(.owl-loaded) > *:not(:first-child) {
            display: none !important;
        }
        *{box-sizing:border-box}body{font-family:var(--font-family);color:var(--dark);background:var(--gray-100);line-height:1.6;overflow-x:hidden;margin:0}
        a{color:var(--primary);text-decoration:none}img{max-width:100%;height:auto}
        .promo-bar{background:var(--primary-gradient);color:#fff;text-align:center;padding:8px 0;font-size:13px;font-weight:500;position:relative}
        .promo-bar .container{position:relative}.promo-close{position:absolute;right:0;top:50%;transform:translateY(-50%);background:0 0;border:none;color:#fff;font-size:14px;cursor:pointer;opacity:.7}
        .site-header{background:#fff;box-shadow:var(--shadow-sm)}.header-main{padding:15px 0}
        .site-logo{display:flex;align-items:center}
        .main-nav{background:#d97d8c;transition:var(--transition)}.main-nav.sticky{position:fixed;top:0;left:0;right:0;z-index:1000;box-shadow:var(--shadow-md)}
        .nav-wrapper{display:flex;align-items:stretch}.nav-menu{display:flex;list-style:none;margin:0;padding:0;flex:1}
        .nav-menu li a{display:block;padding:12px 20px;color:#fff;font-weight:500;font-size:14px}
        .hero-section{padding:0!important;margin:0!important}.hero-slide{height:auto!important;border-radius:var(--radius-lg);overflow:hidden}
        .hero-slider .owl-item img{border-radius:var(--radius-lg);width:100%;height:auto!important;max-height:none!important;object-fit:cover}
        .service-feature-card{padding:15px;gap:12px;border-radius:var(--radius-lg);min-height:70px}
        .service-feature-card i{font-size:28px;min-width:28px}
        .container{width:100%;padding-right:var(--bs-gutter-x,.75rem);padding-left:var(--bs-gutter-x,.75rem);margin-right:auto;margin-left:auto}
        @media(min-width:576px){.container{max-width:540px}}@media(min-width:768px){.container{max-width:720px}}@media(min-width:992px){.container{max-width:960px}}@media(min-width:1200px){.container{max-width:1140px}}@media(min-width:1400px){.container{max-width:1320px}}
        .row{display:flex;flex-wrap:wrap;margin-right:calc(var(--bs-gutter-x,1.5rem)*-.5);margin-left:calc(var(--bs-gutter-x,1.5rem)*-.5)}
        .col-6,.col-md-3,.col-md-4,.col-lg-3,.col-lg-4,.col-lg-9,.col-12{position:relative;width:100%;padding-right:calc(var(--bs-gutter-x,1.5rem)*.5);padding-left:calc(var(--bs-gutter-x,1.5rem)*.5)}
        .col-6{flex:0 0 auto;width:50%}.col-12{flex:0 0 auto;width:100%}
        @media(min-width:768px){.col-md-3{flex:0 0 auto;width:25%}.col-md-4{flex:0 0 auto;width:33.333%}}
        @media(min-width:992px){.col-lg-3{flex:0 0 auto;width:25%}.col-lg-4{flex:0 0 auto;width:33.333%}.col-lg-9{flex:0 0 auto;width:75%}}
        .d-flex{display:flex!important}.align-items-center{align-items:center!important}.justify-content-between{justify-content:space-between!important}
        .bg-white{background-color:#fff!important}.rounded-3{border-radius:var(--radius-md)!important}.shadow-sm{box-shadow:var(--shadow-sm)!important}
        .h-100{height:100%!important}.mb-0{margin-bottom:0!important}.me-1{margin-right:.25rem!important}.me-2{margin-right:.5rem!important}
        .g-3{--bs-gutter-x:1rem;--bs-gutter-y:1rem}.g-3>*{padding-right:calc(var(--bs-gutter-x)*.5);padding-left:calc(var(--bs-gutter-x)*.5);margin-top:var(--bs-gutter-y)}
        .g-4{--bs-gutter-x:1.5rem;--bs-gutter-y:1.5rem}.g-4>*{padding-right:calc(var(--bs-gutter-x)*.5);padding-left:calc(var(--bs-gutter-x)*.5);margin-top:var(--bs-gutter-y)}
        .text-dark{color:var(--dark)!important}.fw-semibold{font-weight:600!important}.fw-medium{font-weight:500!important}.text-muted{color:var(--gray-600)!important}
        .d-block{display:block!important}.d-none{display:none!important}
        @media(min-width:576px){.d-sm-inline{display:inline!important}}@media(min-width:768px){.d-md-none{display:none!important}}
        @media(min-width:992px){.d-lg-block{display:block!important}.d-lg-flex{display:flex!important}.d-lg-inline{display:inline!important}.d-none.d-lg-block{display:block!important}}
        .text-primary{color:var(--primary)!important}.position-relative{position:relative!important}.overflow-hidden{overflow:hidden!important}
        .border{border:1px solid var(--gray-200)!important}.py-3{padding-top:1rem!important;padding-bottom:1rem!important}.py-4{padding-top:1.5rem!important;padding-bottom:1.5rem!important}
        /* Accessibility Contrast and Touch Target Fixes */
        body { background-color: #f8f9fa !important; color: #1a1d21 !important; }
        .footer-social { display: flex !important; gap: 10px !important; margin-top: 15px !important; }
        .footer-social a {
            width: 36px !important; height: 36px !important; border-radius: 50% !important;
            background: rgba(255,255,255,0.1) !important; color: white !important;
            display: flex !important; align-items: center !important; justify-content: center !important;
            transition: var(--transition) !important;
        }
        .section-title {
            font-size: 24px !important;
            font-weight: 700 !important;
            color: var(--dark) !important;
            position: relative !important;
            padding-bottom: 10px !important;
        }
        @media (max-width: 767px) {
            .section-title {
                font-size: 18px !important;
            }
        }
        .product-card-buttons {
            display: flex !important;
            gap: 6px !important;
            margin-top: 10px !important;
            width: 100% !important;
        }
        .product-card-buttons .add-to-cart-btn,
        .product-card-buttons .buy-now-btn {
            flex: 1 !important;
            width: auto !important;
            padding: 8px 4px !important;
            font-size: 11px !important;
            font-weight: 700 !important;
            border-radius: 4px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 4px !important;
            cursor: pointer !important;
            transition: all 0.3s ease !important;
            margin: 0 !important;
            border: 1px solid var(--primary) !important;
            white-space: nowrap !important;
        }
        .product-card-buttons .add-to-cart-btn {
            background-color: #fff !important;
            color: var(--primary) !important;
        }
        .product-card-buttons .add-to-cart-btn:hover {
            background-color: var(--primary) !important;
            color: #fff !important;
        }
        .product-card-buttons .buy-now-btn {
            background-color: var(--primary) !important;
            color: #fff !important;
        }
        .product-card-buttons .buy-now-btn:hover {
            background-color: var(--primary-dark) !important;
            color: #fff !important;
        }
        @media (max-width: 767px) {
            .product-card-buttons {
                flex-direction: column !important;
                gap: 4px !important;
            }
            .product-card-buttons .add-to-cart-btn,
            .product-card-buttons .buy-now-btn {
                width: 100% !important;
                padding: 6px 4px !important;
                font-size: 11px !important;
            }
        }
        .product-card {
            display: flex !important;
            flex-direction: column !important;
            height: 100% !important;
        }
        .product-info {
            display: flex !important;
            flex-direction: column !important;
            flex-grow: 1 !important;
            justify-content: flex-start !important;
        }
        .product-name {
            height: 40px !important;
            margin-bottom: 6px !important;
            overflow: hidden !important;
            display: -webkit-box !important;
            -webkit-line-clamp: 2 !important;
            -webkit-box-orient: vertical !important;
        }
        .product-rating-container {
            height: 20px !important;
            margin-bottom: 6px !important;
            display: flex !important;
            align-items: center !important;
        }
        .product-price {
            margin-top: auto !important;
            margin-bottom: 10px !important;
            height: 24px !important;
            display: flex !important;
            align-items: center !important;
        }
        .product-card-buttons {
            margin-top: 0 !important;
        }
        .add-to-cart-btn {
            white-space: nowrap !important;
            font-size: 11px !important;
            padding: 8px 4px !important;
        }
        @media (min-width: 576px) {
            .add-to-cart-btn {
                font-size: 13px !important;
                padding: 8px 10px !important;
            }
        }
        @media (max-width: 767.98px) {
            .section.py-5.bg-white {
                padding-top: 10px !important;
                padding-bottom: 10px !important;
            }
            .section.py-5.bg-white .row.g-4 {
                --bs-gutter-y: 10px !important;
                margin-top: -10px !important;
            }
            .section.py-5.bg-white .col-md-6 {
                margin-top: 10px !important;
            }
            .section.py-5.bg-white img {
                min-height: 250px !important;
                max-height: 280px !important;
            }
        }
        .nav-menu li a { color: #ffffff !important; }
        .nav-menu li a:hover, .nav-menu li a.active { color: #ffffff !important; background: rgba(255, 255, 255, 0.2) !important; }
        .section-link { color: #064d2e !important; font-weight: 700 !important; }
        .section-link:hover { color: #0a6338 !important; }
        .header-action-item {
            color: var(--primary) !important;
            transition: var(--transition);
        }
        .header-action-item i {
            color: var(--primary) !important;
            transition: var(--transition);
        }
        .header-action-item:hover, .header-action-item:hover i {
            color: var(--primary-dark) !important;
        }
        html, body { max-width: 100% !important; overflow-x: hidden !important; position: relative !important; width: 100% !important; }
        .category-card { height: 130px !important; display: flex !important; flex-direction: column !important; align-items: center !important; justify-content: center !important; }
        .category-card span { color: #1a1d21 !important; display: block !important; white-space: nowrap !important; overflow: hidden !important; text-overflow: ellipsis !important; font-size: 12px !important; padding: 0 4px !important; width: 100% !important; text-align: center !important; }
        .mobile-bottom-nav a, .mobile-bottom-nav button { color: #1a1d21 !important; }
        .mobile-bottom-nav a.active, .mobile-bottom-nav button.active { color: #064d2e !important; }
        .owl-dot { min-width: 44px !important; min-height: 44px !important; display: inline-flex !important; align-items: center !important; justify-content: center !important; background: transparent !important; border: none !important; cursor: pointer; padding: 0 !important; }
        .owl-dot span { display: block !important; margin: 0 4px !important; }
        .site-header.sticky { position: fixed !important; top: 0; left: 0; right: 0; z-index: 1000; box-shadow: var(--shadow-lg); animation: slideDown 0.3s ease; background: white !important; }
        .site-header.sticky .main-nav { display: none !important; }
        @media (max-width: 767px) {
            .mobile-search { display: none !important; }
            .mobile-search.show { display: block !important; }
        }
        @media (max-width: 991.98px) {
            .promo-bar { display: none !important; }
            .main-nav { display: none !important; }
            .floating-cart-btn { display: none !important; }
            .hero-slide { height: 60vw !important; }
            .hero-slider .owl-item img { height: 60vw !important; max-height: none !important; object-fit: fill !important; }
            body .hero-section { padding-top: 0 !important; padding-bottom: 0 !important; margin-top: 0 !important; margin-bottom: 0 !important; }
            body .hero-section.py-4 { padding-top: 0 !important; padding-bottom: 0 !important; margin-top: 0 !important; margin-bottom: 0 !important; }
            .section { padding: 15px 0 !important; }
            section.py-3 { padding-top: 8px !important; padding-bottom: 8px !important; }
            .countdown-item { padding: 4px 6px !important; min-width: 35px !important; }
            .countdown-item .number { font-size: 14px !important; }
            .countdown-item .label { font-size: 7px !important; }
            /* Mobile header: black bg, white icons */
            .site-header { background: #000 !important; box-shadow: none !important; border-bottom: none !important; }
            .site-header .header-main { background: #000 !important; }
            .site-header .header-action-item,
            .site-header .header-action-item i,
            .site-header .mobile-search-toggle,
            .site-header .mobile-search-toggle i,
            .site-header .mobile-menu-toggle,
            .site-header .mobile-menu-toggle i { color: #fff !important; }
            .site-header.sticky { background: #000 !important; }
        }
        /* Mobile Category Sidebar styles */
        .mobile-sidebar-overlay {
            position: fixed; inset: 0; background: rgba(0,0,0,0.5);
            z-index: 1060; display: none; opacity: 0; transition: opacity 0.3s ease;
        }
        .mobile-sidebar-overlay.open {
            display: block !important; opacity: 1 !important;
        }
        .mobile-menu-sidebar {
            position: fixed; top: 0; left: 0; width: 280px; height: 100vh;
            background: #fff; z-index: 1061; transition: transform 0.3s ease;
            display: flex; flex-direction: column; box-shadow: 2px 0 10px rgba(0,0,0,0.15);
            transform: translateX(-100%);
        }
        .mobile-menu-sidebar.open {
            transform: translateX(0) !important;
        }
        /* Cart Fly Animation Classes */
        @keyframes cartBouncePulse {
            0%, 100% { transform: scale(1); }
            30% { transform: scale(1.3) translateY(-5px); }
            50% { transform: scale(0.9) translateY(2px); }
            80% { transform: scale(1.1); }
        }
        .cart-bounce-pulse {
            animation: cartBouncePulse 0.6s ease-in-out !important;
        }
        @keyframes badgeHighlight {
            0%, 100% { background-color: #000; transform: scale(1); }
            50% { background-color: #064d2e; transform: scale(1.4); }
        }
        /* Soften and reduce placeholder font size */
        ::placeholder {
            color: #b5aeb0 !important;
            font-size: 13px !important;
            opacity: 1 !important;
        }
        :-ms-input-placeholder {
            color: #b5aeb0 !important;
            font-size: 13px !important;
        }
        ::-ms-input-placeholder {
            color: #b5aeb0 !important;
            font-size: 13px !important;
        }
        /* Floating WhatsApp Button */
        .floating-whatsapp-btn {
            position: fixed;
            bottom: 80px;
            right: 20px;
            z-index: 999;
            width: 44px;
            height: 44px;
            background-color: #25d366;
            color: white !important;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
        }
        .floating-whatsapp-btn:hover {
            background-color: #128c7e;
            transform: scale(1.1);
            color: white !important;
            box-shadow: 0 6px 14px rgba(0,0,0,0.25);
        }
        @media (max-width: 767px) {
            .floating-whatsapp-btn {
                bottom: 80px;
                right: 15px;
                width: 44px;
                height: 44px;
                font-size: 24px;
            }
        }
    </style>
    <!-- Bootstrap 5 (deferred - not render-blocking) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></noscript>

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" media="print" onload="this.media='all'">

    <!-- Owl Carousel -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">

    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" media="print" onload="this.media='all'">

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" media="print" onload="this.media='all'">

    <!-- Custom CSS & Typography System -->
    <link rel="stylesheet" href="{{ asset('css/typography.css') }}?v=1.0">
    <link rel="stylesheet" href="{{ asset('css/frontend.css') }}?v=1.9">

    @yield('styles')

    <!-- JSON-LD Structured Data -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "WebSite",
        "name": "Modestik",
        "url": "{{ url('/') }}",
        "potentialAction": {
            "@@type": "SearchAction",
            "target": "{{ url('/search') }}?q={search_term_string}",
            "query-input": "required name=search_term_string"
        }
    }
    </script>
    @yield('structured_data')

    <!-- Pixel & Analytics Tracking Scripts -->
    @if(\App\Models\Setting::get('fb_pixel_enabled') === '1' && \App\Models\Setting::get('fb_pixel_id'))
        <!-- Facebook Pixel Code -->
        <script>
          !function(f,b,e,v,n,t,s)
          {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
          n.callMethod.apply(n,arguments):n.queue.push(arguments)};
          if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
          n.queue=[];t=b.createElement(e);t.async=!0;
          t.src=v;s=b.getElementsByTagName(e)[0];
          s.parentNode.insertBefore(t,s)}(window, document,'script',
          'https://connect.facebook.net/en_US/fbevents.js');
          fbq('init', '{{ \App\Models\Setting::get('fb_pixel_id') }}');
          fbq('track', 'PageView');
        </script>
        <noscript>
          <img height="1" width="1" style="display:none" 
               src="https://www.facebook.com/tr?id={{ \App\Models\Setting::get('fb_pixel_id') }}&ev=PageView&noscript=1"/>
        </noscript>
        <!-- End Facebook Pixel Code -->
    @endif

    @if(\App\Models\Setting::get('google_analytics_enabled') === '1' && \App\Models\Setting::get('google_analytics_id'))
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ \App\Models\Setting::get('google_analytics_id') }}"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());
          gtag('config', '{{ \App\Models\Setting::get('google_analytics_id') }}');
        </script>
    @endif

    @if(\App\Models\Setting::get('tiktok_pixel_enabled') === '1' && \App\Models\Setting::get('tiktok_pixel_id'))
        <!-- TikTok Pixel -->
        <script>
          !function (w, d, t) {
            w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.n;n++)ttq.setAndDefer(e,ttq.methods[n]);return e};ttq.load=function(e,n){var o="https://analytics.tiktok.com/i18n/pixel/events.js";ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=o,ttq._t=ttq._t||+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};var a=d.createElement("script");a.type="text/javascript",a.async=!0,a.src=o+"?sdkid="+e+"&lib="+t;var c=d.getElementsByTagName("script")[0];c.parentNode.insertBefore(a,c)};
            ttq.load('{{ \App\Models\Setting::get('tiktok_pixel_id') }}');
            ttq.page();
          }(window, document, 'ttq');
        </script>
    @endif

    @if(\App\Models\Setting::get('pinterest_pixel_enabled') === '1' && \App\Models\Setting::get('pinterest_pixel_id'))
        <!-- Pinterest Tag -->
        <script>
          !function(e){if(!window.pintrk){window.pintrk=function(){window.pintrk.queue.push(
          Array.prototype.slice.call(arguments))};var n=window.pintrk;n.queue=[],n.version="3.0";
          var t=document.createElement("script");t.async=!0,t.src=e;var r=document.getElementsByTagName("script")[0];
          r.parentNode.insertBefore(t,r)}}("https://s.shadowstatic.com/pinterest/pintrk.js");
          pintrk('load', '{{ \App\Models\Setting::get('pinterest_pixel_id') }}');
          pintrk('page');
        </script>
        <noscript>
          <img height="1" width="1" style="display:none" alt=""
               src="https://ct.pinterest.com/v3/?event=init&tid={{ \App\Models\Setting::get('pinterest_pixel_id') }}&noscript=1" />
        </noscript>
    @endif

    @if(\App\Models\Setting::get('snapchat_pixel_enabled') === '1' && \App\Models\Setting::get('snapchat_pixel_id'))
        <!-- Snapchat Pixel -->
        <script type="text/javascript">
          (function(e,t,n){if(e.snaptr)return;var r=e.snaptr=function(){r.handleRequest?r.handleRequest.apply(r,arguments):r.queue.push(arguments)};
          r.queue=[];var a=t.createElement(n);a.async=!0;a.src="https://sc-static.net/sce/one/messenger.js";
          var s=t.getElementsByTagName(n)[0];s.parentNode.insertBefore(a,s)})(window,document,"script");
          snaptr('init', '{{ \App\Models\Setting::get('snapchat_pixel_id') }}');
          snaptr('track', 'PAGE_VIEW');
        </script>
    @endif

    @if(\App\Models\Setting::get('clarity_enabled') === '1' && \App\Models\Setting::get('clarity_project_id'))
        <!-- Microsoft Clarity -->
        <script type="text/javascript">
            (function(c,l,a,r,i,t,y){
                c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
                t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
                y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
            })(window, document, "clarity", "script", "{{ \App\Models\Setting::get('clarity_project_id') }}");
        </script>
    @endif

    @if(\App\Models\Setting::get('search_console_enabled') === '1' && \App\Models\Setting::get('search_console_key'))
        <!-- Google Search Console HTML Verification -->
        {!! \App\Models\Setting::get('search_console_key') !!}
    @endif
</head>
<body>
    </div>

    <!-- Header -->
    <header class="site-header" id="siteHeader">
        <!-- Top Header -->
        <div class="header-main">
            <div class="container">
                <div class="row align-items-center justify-content-between">
                    <!-- Logo (Left) -->
                    <div class="col-lg-3 col-md-3 col-6">
                        <a href="{{ route('home') }}" class="site-logo" style="text-decoration: none;">
                            <img src="{{ asset('images/logo.webp') }}" alt="Modestik Logo" width="181" height="70" style="height: 50px; width: auto; object-fit: contain;">
                        </a>
                    </div>

                    <!-- Search (Center) -->
                    <div class="col-lg-5 col-md-5 d-none d-md-block">
                        <div class="header-search">
                            <form action="{{ route('search') }}" method="GET" class="search-form" id="searchForm" style="border: 1.5px solid #0e8c4f; border-radius: 4px; display: flex; overflow: hidden; height: 42px;">
                                <input type="text" name="q" class="form-control search-input" id="searchInput"
                                       placeholder="Search Product..." autocomplete="off" style="border: none; border-radius: 0; box-shadow: none; flex-grow: 1; padding: 10px 15px; font-size: 14px;">
                                <button type="submit" class="search-btn" aria-label="Search" style="background: #000; color: #fff; border: none; border-radius: 0; padding: 0 25px; cursor: pointer;"><i class="fas fa-search"></i></button>
                            </form>
                            <div class="search-suggestions" id="searchSuggestions"></div>
                        </div>
                    </div>

                    <!-- Actions (Right) -->
                    <div class="col-lg-4 col-md-4 col-6">
                        <div class="header-actions" style="display: flex; align-items: center; justify-content: flex-end; gap: 20px;">
                            <a href="{{ route('track-order') }}" class="header-action-item d-none d-md-inline-flex align-items-center gap-2 text-primary fw-semibold" style="text-decoration: none; font-size: 14px;" aria-label="Track Order">
                                <i class="fas fa-shipping-fast text-primary" style="font-size: 18px;" aria-hidden="true"></i>
                            </a>

                            @auth
                                <a href="{{ route('customer.wishlist') }}" class="header-action-item d-none d-md-inline-flex align-items-center gap-2 text-primary fw-semibold" style="text-decoration: none; font-size: 14px;">
                                    <i class="fas fa-heart text-primary" style="font-size: 18px;"></i>
                                </a>
                            @endauth
                            <button type="button" class="header-action-item mobile-search-toggle text-primary fw-semibold d-inline-flex d-md-none" style="background: none; border: none; cursor: pointer; padding: 0;" aria-label="Toggle Search">
                                <i class="fas fa-search text-primary" style="font-size: 18px;"></i>
                            </button>
                            <button type="button" class="header-action-item mobile-menu-toggle text-primary fw-semibold d-inline-flex d-lg-none" style="background: none; border: none; cursor: pointer; padding: 0; margin-left: 12px;" aria-label="Open categories menu">
                                <i class="fas fa-bars text-primary" style="font-size: 20px;"></i>
                            </button>

                            <button type="button" class="header-action-item cart-toggle text-primary fw-semibold position-relative d-none d-md-inline-flex" style="text-decoration: none; font-size: 14px; background: none; border: none; cursor: pointer; padding: 0;" aria-label="Open shopping cart">
                                <i class="fas fa-shopping-basket text-primary" style="font-size: 20px;" aria-hidden="true"></i>
                                <span class="cart-count" id="cartCount" style="background: var(--primary); color: #fff; border-radius: 50%; font-size: 10px; width: 18px; height: 18px; display: flex; align-items: center; justify-content: center; position: absolute; top: -8px; right: -8px; font-weight: 700;">0</span>
                            </button>
                            @guest
                                <a href="{{ route('login') }}" class="header-action-item d-none d-md-inline-flex align-items-center gap-2 text-primary fw-semibold" style="text-decoration: none; font-size: 14px;" aria-label="Login">
                                    <i class="fas fa-user text-primary" style="font-size: 18px;" aria-hidden="true"></i>
                                    <span class="d-none d-lg-inline">লগইন</span>
                                </a>
                                {{-- Desktop Social Icons (beside login) --}}
                                @if(request()->getHost() != 'modestik.triangletech.com.bd')
                                <div class="d-none d-md-inline-flex align-items-center gap-2" style="margin-left: 5px;">
                                    <a href="{{ \App\Models\Setting::get('facebook_url', '#') }}" target="_blank" style="color: var(--primary); font-size: 15px;" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                                    <a href="{{ \App\Models\Setting::get('facebook_group_url', '#') }}" target="_blank" style="color: var(--primary); font-size: 15px;" aria-label="Facebook Group"><i class="fas fa-users"></i></a>
                                    <a href="{{ \App\Models\Setting::get('instagram_url', '#') }}" target="_blank" style="color: var(--primary); font-size: 15px;" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                                    <a href="{{ \App\Models\Setting::get('tiktok_url', '#') }}" target="_blank" style="color: var(--primary); font-size: 15px;" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
                                    <a href="{{ \App\Models\Setting::get('youtube_url', '#') }}" target="_blank" style="color: var(--primary); font-size: 15px;" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                                </div>
                                @endif
                            @else
                                <div class="dropdown d-none d-md-inline-block">
                                    <a href="#" class="header-action-item dropdown-toggle text-primary fw-semibold d-flex align-items-center gap-1" data-bs-toggle="dropdown" style="text-decoration: none; font-size: 14px;">
                                        <i class="fas fa-user-circle text-primary" style="font-size: 20px;"></i>
                                        <span class="d-none d-lg-inline">{{ Str::limit(auth()->user()->name, 10) }}</span>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="{{ route('customer.dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i>ড্যাশবোর্ড</a></li>
                                        <li><a class="dropdown-item" href="{{ route('customer.orders') }}"><i class="fas fa-box me-2"></i>আমার অর্ডার</a></li>
                                        <li><a class="dropdown-item" href="{{ route('customer.profile') }}"><i class="fas fa-user me-2"></i>প্রোফাইল</a></li>
                                        @if(auth()->user()->hasRole('admin'))
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-primary" href="{{ route('admin.dashboard') }}"><i class="fas fa-cog me-2"></i>অ্যাডমিন প্যানেল</a></li>
                                        @endif
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('logout') }}" method="POST">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-danger"><i class="fas fa-sign-out-alt me-2"></i>লগআউট</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                                                                {{-- Desktop Social Icons (beside user menu) --}}
                                @if(request()->getHost() != 'modestik.triangletech.com.bd')
                                <div class="d-none d-md-inline-flex align-items-center gap-2" style="margin-left: 5px;">
                                    <a href="{{ \App\Models\Setting::get('facebook_url', '#') }}" target="_blank" style="color: var(--primary); font-size: 15px;" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                                    <a href="{{ \App\Models\Setting::get('facebook_group_url', '#') }}" target="_blank" style="color: var(--primary); font-size: 15px;" aria-label="Facebook Group"><i class="fas fa-users"></i></a>
                                    <a href="{{ \App\Models\Setting::get('instagram_url', '#') }}" target="_blank" style="color: var(--primary); font-size: 15px;" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                                    <a href="{{ \App\Models\Setting::get('tiktok_url', '#') }}" target="_blank" style="color: var(--primary); font-size: 15px;" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
                                    <a href="{{ \App\Models\Setting::get('youtube_url', '#') }}" target="_blank" style="color: var(--primary); font-size: 15px;" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                                </div>
                                @endif
@endguest
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="main-nav" id="mainNav">
            <div class="container">
                <div class="nav-wrapper">
                    <div class="categories-dropdown" id="categoriesDropdown">
                        <button class="categories-btn" id="categoriesBtn" style="background: #d97d8c; color: #fff; border: none; font-weight: 700; font-size: 14px; width: 280px; display: flex; justify-content: space-between; align-items: center; padding: 12px 20px; height: 100%;">
                            <span>সব ক্যাটাগরি</span> <i class="fas fa-bars"></i>
                        </button>
                        <div class="categories-menu" id="categoriesMenu">
                            @php
                                $navCategories = \Illuminate\Support\Facades\Cache::rememberForever('nav_categories', function () {
                                    return \App\Models\Category::active()->parents()->with('children')->orderBy('order')->get();
                                });
                            @endphp
                            @foreach($navCategories as $cat)
                                <div class="category-item {{ $cat->children->count() ? 'has-children' : '' }}">
                                    <a href="{{ route('products.category', $cat->slug) }}">
                                        @if($cat->icon)
                                            <span class="cat-icon">{!! $cat->icon !!}</span>
                                        @else
                                            <i class="fas fa-tag cat-icon"></i>
                                        @endif
                                        {{ $cat->name }}
                                        @if($cat->children->count())
                                            <i class="fas fa-chevron-right float-end"></i>
                                        @endif
                                    </a>
                                    @if($cat->children->count())
                                        <div class="subcategory-menu">
                                            @foreach($cat->children as $child)
                                                <a href="{{ route('products.category', $child->slug) }}">{{ $child->name }}</a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Main Menu -->
                    <ul class="nav-menu">
                        <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">হোম</a></li>
                        <li><a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">শপ</a></li>
                        <li><a href="{{ route('products.flash-sale') }}" class="{{ request()->routeIs('products.flash-sale') ? 'active' : '' }}"><i class="fas fa-bolt text-warning"></i> ফ্ল্যাশ সেল</a></li>
                        <li><a href="{{ route('blogs.index') }}" class="{{ request()->routeIs('blogs.*') ? 'active' : '' }}">ব্লগ</a></li>
                        <li><a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">যোগাযোগ</a></li>
                        <li><a href="{{ route('complaints.create') }}" class="{{ request()->routeIs('complaints.create') ? 'active' : '' }}">অভিযোগ বক্স</a></li>
                    </ul>

                    <!-- Right Nav -->
                    <div class="nav-right d-none d-lg-flex">
                        @auth
                            <a href="{{ route('customer.dashboard') }}"><i class="fas fa-user me-1"></i> আমার অ্যাকাউন্ট</a>
                        @else
                            <a href="{{ route('login') }}"><i class="fas fa-sign-in-alt me-1"></i> লগইন / রেজিস্টার</a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Mobile Search -->
        <div class="mobile-search d-md-none">
            <div class="container">
                <form action="{{ route('search') }}" method="GET">
                    <div class="input-group">
                        <input type="text" name="q" class="form-control" placeholder="পণ্য খুঁজুন...">
                        <button class="btn btn-primary" type="submit" aria-label="Search"><i class="fas fa-search" aria-hidden="true"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </header>

    <!-- Cart Sidebar -->
    <div class="cart-overlay" id="cartOverlay"></div>
    <div class="cart-sidebar" id="cartSidebar">
        <div class="cart-sidebar-header">
            <h5><i class="fas fa-shopping-cart me-2"></i>শপিং কার্ট</h5>
            <button class="cart-close" id="cartClose" aria-label="Close shopping cart"><i class="fas fa-times" aria-hidden="true"></i></button>
        </div>
        <div class="cart-sidebar-body" id="cartSidebarBody">
            <!-- Loaded via AJAX -->
        </div>
    </div>

    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="footer-top">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="footer-widget">
                            <img src="{{ asset('images/logo.webp') }}" alt="Modestik Logo" class="mb-3" width="150" height="40" style="height: 40px; width: auto; object-fit: contain;" loading="lazy">
                            <p>Your one-stop online shop for quality products. We deliver across Bangladesh with the best prices and fastest delivery.</p>
                            <div class="footer-social" style="display: flex !important; gap: 15px !important; margin-top: 15px !important; align-items: center !important;">
                                <a href="{{ \App\Models\Setting::get('facebook_url', '#') }}" target="_blank" aria-label="Facebook" style="color: #1877f2 !important; font-size: 24px !important; background: none !important; width: auto !important; height: auto !important; display: inline-flex !important;"><i class="fab fa-facebook" aria-hidden="true"></i></a>
                                <a href="{{ \App\Models\Setting::get('facebook_group_url', '#') }}" target="_blank" aria-label="Facebook Group" style="color: #25d366 !important; font-size: 24px !important; background: none !important; width: auto !important; height: auto !important; display: inline-flex !important;"><i class="fas fa-users" aria-hidden="true"></i></a>
                                <a href="{{ \App\Models\Setting::get('instagram_url', '#') }}" target="_blank" aria-label="Instagram" style="color: #e1306c !important; font-size: 24px !important; background: none !important; width: auto !important; height: auto !important; display: inline-flex !important;"><i class="fab fa-instagram" aria-hidden="true"></i></a>
                                <a href="{{ \App\Models\Setting::get('youtube_url', '#') }}" target="_blank" aria-label="YouTube" style="color: #ff0000 !important; font-size: 24px !important; background: none !important; width: auto !important; height: auto !important; display: inline-flex !important;"><i class="fab fa-youtube" aria-hidden="true"></i></a>
                                <a href="{{ \App\Models\Setting::get('tiktok_url', '#') }}" target="_blank" aria-label="TikTok" style="color: #ffffff !important; font-size: 24px !important; background: none !important; width: auto !important; height: auto !important; display: inline-flex !important;"><i class="fab fa-tiktok" aria-hidden="true"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6 mb-4">
                        <div class="footer-widget">
                            <h3 class="h5">Quick Links</h3>
                            <ul>
                                <li><a href="{{ route('home') }}">Home</a></li>
                                <li><a href="{{ route('products.index') }}">Shop</a></li>
                                <li><a href="{{ route('products.flash-sale') }}">Flash Sale</a></li>
                                <li><a href="{{ route('track-order') }}">Track Order</a></li>
                                <li><a href="{{ route('contact') }}">Contact Us</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6 mb-4">
                        <div class="footer-widget">
                            <h3 class="h5">My Account</h3>
                            <ul>
                                <li><a href="{{ route('login') }}">Login</a></li>
                                <li><a href="{{ route('register') }}">Register</a></li>
                                <li><a href="{{ route('customer.orders') }}">Order History</a></li>
                                <li><a href="{{ route('customer.wishlist') }}">Wishlist</a></li>
                                <li><a href="{{ route('customer.profile') }}">Profile Settings</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6 mb-4">
                        <div class="footer-widget">
                            <h3 class="h5">Information</h3>
                            <ul>
                                <li><a href="{{ route('page.show', 'about-us') }}">About Us</a></li>
                                <li><a href="{{ route('page.show', 'privacy-policy') }}">Privacy Policy</a></li>
                                <li><a href="{{ route('page.show', 'terms-conditions') }}">Terms & Conditions</a></li>
                                <li><a href="{{ route('page.show', 'return-policy') }}">Return Policy</a></li>
                                <li><a href="{{ route('contact') }}">Customer Support</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="footer-widget">
                            <h3 class="h5">Newsletter</h3>
                            <p>Subscribe to get updates on new products & offers.</p>
                            <form class="newsletter-form" id="newsletterForm">
                                @csrf
                                <div class="input-group">
                                    <input type="email" name="email" class="form-control" placeholder="Your email" required>
                                    <button type="submit" class="btn btn-primary" aria-label="Subscribe to newsletter"><i class="fas fa-paper-plane" aria-hidden="true"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-12 text-center">
                        <p>&copy; {{ date('Y') }} Modestik. All rights reserved | Developed by <strong><a href="https://engr-saad.com/" target="_blank" style="color: #ffff00 !important; text-decoration: none;">Engr Saad</a></strong></p>
                    </div>
                </div>
            </div>
        </div>
    </footer>



    <!-- Floating Cart Button -->
    <div class="floating-cart-btn" id="floatingCartBtn" style="display:none;">
        <span class="floating-cart-badge" id="floatingCartCount">0</span>
        <i class="fas fa-shopping-cart"></i>
    </div>

    <!-- Floating Contact Action Widget -->
    <style>
        .floating-contact-widget {
            position: fixed;
            bottom: 80px;
            right: 25px;
            z-index: 1050;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .contact-buttons-group {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 12px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(20px);
            transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        }
        .floating-contact-widget.active .contact-buttons-group {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        .contact-btn {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            color: #fff !important;
            font-size: 20px;
            transition: all 0.25s ease;
            text-decoration: none;
            position: relative;
        }
        .contact-btn:hover {
            transform: scale(1.1);
        }
        .phone-btn {
            background-color: #00e676; /* Call */
        }
        .whatsapp-btn {
            background-color: #25d366; /* WhatsApp */
        }
        .messenger-btn {
            background-color: #0084ff; /* Messenger */
        }
        .contact-toggle-btn {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            color: #fff;
            font-size: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: #ff5e62; /* Pink-red color */
            padding: 0;
        }
        .contact-toggle-btn:hover {
            transform: scale(1.05);
        }
        .floating-contact-widget.active .contact-toggle-btn {
            background-color: #ff3366;
        }
        .contact-btn::after {
            content: attr(title);
            position: absolute;
            right: 60px;
            background: rgba(0,0,0,0.8);
            color: #fff;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s ease;
            pointer-events: none;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .contact-btn:hover::after {
            opacity: 1;
            visibility: visible;
        }
    </style>
    
    <div class="floating-contact-widget" id="floatingContactWidget">
        <div class="contact-buttons-group">
            <a href="tel:+8801621106653" class="contact-btn phone-btn" title="+880 1621-106653">
                <i class="fas fa-phone-alt"></i>
            </a>
            <a href="https://wa.me/8801621106653" class="contact-btn whatsapp-btn" title="WhatsApp" target="_blank">
                <i class="fab fa-whatsapp"></i>
            </a>
            <a href="https://www.messenger.com/t/ModestikBD" class="contact-btn messenger-btn" title="Messenger" target="_blank">
                <i class="fab fa-facebook-messenger"></i>
            </a>
        </div>
        <button type="button" class="contact-toggle-btn" id="contactToggleBtn" title="Contact Us">
            <i class="far fa-comment-dots toggle-chat-icon" id="toggleChatIcon"></i>
        </button>
    </div>

    <!-- Mobile Category Sidebar -->
    <div class="mobile-sidebar-overlay" id="mobileMenuOverlay"></div>
    <div class="mobile-menu-sidebar" id="mobileMenuSidebar" style="background: #fff; width: 300px; height: 100vh; position: fixed; top: 0; left: 0; z-index: 1061; box-shadow: 4px 0 15px rgba(0,0,0,0.1); transform: translateX(-100%); transition: transform 0.3s cubic-bezier(0.165, 0.84, 0.44, 1); display: flex; flex-direction: column;">
        <div class="mobile-sidebar-header" style="background: #000; color: #fff; padding: 18px 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #1f2937;">
            <h5 class="mb-0 fw-bold" style="font-size: 16px; letter-spacing: 0.5px;">ALL CATEGORIES</h5>
            <button id="mobileMenuClose" style="background: none; border: none; color: #fff; font-size: 18px; cursor: pointer; padding: 5px; display: flex; align-items: center; justify-content: center;" aria-label="Close categories menu">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        <div class="mobile-sidebar-body" style="flex: 1; overflow-y: auto; padding: 0;">
            <div class="mobile-categories-list">
                @php
                    $navCategories = \Illuminate\Support\Facades\Cache::rememberForever('nav_categories', function () {
                        return \App\Models\Category::active()->parents()->with('children')->orderBy('order')->get();
                    });
                @endphp
                @foreach($navCategories as $cat)
                    <div class="mobile-cat-item">
                        @if($cat->children->count())
                            <div class="mobile-cat-item-header d-flex justify-content-between align-items-center" style="padding: 14px 20px; cursor: pointer; border-bottom: 1px solid #f1f5f9; background: #fff; transition: background 0.2s;">
                                <div style="display: flex; align-items: center; gap: 12px; color: #334155; font-weight: 600; font-size: 14px;">
                                    @if($cat->icon)
                                        <span style="color: #64748b; width: 20px; text-align: center; font-size: 16px; display: inline-flex; align-items: center; justify-content: center;">{!! $cat->icon !!}</span>
                                    @else
                                        <i class="fas fa-tag" style="color: #64748b; width: 20px; text-align: center; font-size: 16px;"></i>
                                    @endif
                                    {{ $cat->name }}
                                </div>
                                <span class="toggle-icon" style="color: #94a3b8; font-size: 12px;"><i class="fas fa-chevron-right" style="transition: transform 0.3s;"></i></span>
                            </div>
                            <div class="mobile-subcat-list" style="background: #f8fafc; padding-left: 32px; display: none; border-bottom: 1px solid #e2e8f0;">
                                <a href="{{ route('products.category', $cat->slug) }}" style="display: block; padding: 12px 20px; color: var(--primary); text-decoration: none; font-size: 13px; font-weight: 600; border-bottom: 1px solid #f1f5f9;">
                                    <i class="fas fa-th-list me-2"></i>View All Products
                                </a>
                                @foreach($cat->children as $child)
                                    <a href="{{ route('products.category', $child->slug) }}" style="display: block; padding: 12px 20px; color: #475569; text-decoration: none; font-size: 13px; border-bottom: 1px solid #f1f5f9;">
                                        {{ $child->name }}
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <a href="{{ route('products.category', $cat->slug) }}" style="display: flex; align-items: center; gap: 12px; padding: 14px 20px; text-decoration: none; color: #334155; font-weight: 600; font-size: 14px; border-bottom: 1px solid #f1f5f9; background: #fff; transition: background 0.2s;">
                                @if($cat->icon)
                                    <span style="color: #64748b; width: 20px; text-align: center; font-size: 16px; display: inline-flex; align-items: center; justify-content: center;">{!! $cat->icon !!}</span>
                                @else
                                    <i class="fas fa-tag" style="color: #64748b; width: 20px; text-align: center; font-size: 16px;"></i>
                                @endif
                                {{ $cat->name }}
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
            <!-- About Us, Contact Us and Social Links in Mobile Sidebar -->
            <div class="mobile-sidebar-extra" style="padding: 25px 20px; border-top: 1px solid #f1f5f9; background: #fafafa; margin-top: 15px;">
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <a href="{{ route('page.show', 'about-us') }}" style="color: #475569; font-weight: 600; font-size: 14px; text-decoration: none; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-info-circle" style="color: var(--primary);"></i> About Us
                    </a>
                    <a href="{{ route('blogs.index') }}" style="color: #475569; font-weight: 600; font-size: 14px; text-decoration: none; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-blog" style="color: var(--primary);"></i> Blog
                    </a>
                    <a href="{{ route('contact') }}" style="color: #475569; font-weight: 600; font-size: 14px; text-decoration: none; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-envelope" style="color: var(--primary);"></i> Contact Us
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Bottom Nav -->
    <nav class="mobile-bottom-nav d-md-none">
        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">
            <i class="fas fa-home"></i><span>Home</span>
        </a>
        <a href="javascript:void(0)" class="mobile-menu-toggle">
            <i class="fas fa-th-large"></i><span>Categories</span>
        </a>
        <a href="{{ route('track-order') }}">
            <i class="fas fa-shipping-fast"></i><span>Track</span>
        </a>
        <button type="button" class="cart-toggle" aria-label="Open shopping cart" style="background:none;border:none;cursor:pointer;padding:4px 10px;">
            <i class="fas fa-shopping-cart" aria-hidden="true"></i>
            <span class="mobile-cart-badge" id="mobileCartCount">0</span>
            <span>Cart</span>
        </button>
        @auth
            <a href="{{ route('customer.dashboard') }}">
                <i class="fas fa-user"></i><span>Account</span>
            </a>
        @else
            <a href="{{ route('login') }}">
                <i class="fas fa-sign-in-alt"></i><span>Login</span>
            </a>
        @endauth
    </nav>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Global AJAX setup
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        // AOS Init
        AOS.init({ duration: 600, once: true, offset: 50 });

        // Owl Carousel accessibility: add aria-labels to dot buttons
        $(document).on('initialized.owl.carousel translated.owl.carousel', function() {
            $('.owl-dot').each(function(i) {
                $(this).attr('aria-label', 'Go to slide ' + (i + 1));
            });
        });

        // Sticky Header
        $(window).scroll(function() {
            if ($(this).scrollTop() > 130) {
                $('#siteHeader').addClass('sticky');
            } else {
                $('#siteHeader').removeClass('sticky');
            }
        });

        // Mobile Search Toggle
        $(document).on('click', '.mobile-search-toggle', function() {
            $('.mobile-search').toggleClass('show');
        });

        // Categories Dropdown
        $('#categoriesBtn').click(function(e) {
            e.stopPropagation();
            $('#categoriesMenu').toggleClass('show');
        });
        $(document).click(function() { $('#categoriesMenu').removeClass('show'); });

        // Cart Sidebar
        $(document).on('click', '.cart-toggle', function(e) {
            e.preventDefault();
            loadCartSidebar();
            $('#cartSidebar, #cartOverlay').addClass('open');
            $('body').addClass('cart-open');
        });
        $('#cartClose, #cartOverlay').click(function() {
            $('#cartSidebar, #cartOverlay').removeClass('open');
            $('body').removeClass('cart-open');
        });

        // Mobile Categories Sidebar Toggle
        $(document).on('click', '.mobile-menu-toggle', function() {
            $('#mobileMenuSidebar, #mobileMenuOverlay').addClass('open');
            $('body').addClass('menu-open');
        });
        $('#mobileMenuClose, #mobileMenuOverlay').click(function() {
            $('#mobileMenuSidebar, #mobileMenuOverlay').removeClass('open');
            $('body').removeClass('menu-open');
        });
        $(document).on('click', '.mobile-cat-item-header', function(e) {
            e.preventDefault();
            e.stopPropagation();
            let parent = $(this).closest('.mobile-cat-item');
            let list = parent.find('.mobile-subcat-list');
            let icon = $(this).find('.toggle-icon i');
            
            list.slideToggle(250);
            if (icon.hasClass('fa-chevron-right')) {
                icon.removeClass('fa-chevron-right').addClass('fa-chevron-down');
            } else {
                icon.removeClass('fa-chevron-down').addClass('fa-chevron-right');
            }
        });

        function loadCartSidebar() {
            $.get('{{ route("cart.sidebar") }}', function(html) {
                $('#cartSidebarBody').html(html);
            });
        }

        function getVisibleCartTarget() {
            let target = null;
            if (window.innerWidth < 768) {
                target = $('#mobileCartCount');
                if (target.length && target.is(':visible')) return target;
            }
            target = $('#floatingCartBtn');
            if (target.length && target.is(':visible') && target.css('display') !== 'none') {
                return target;
            }
            target = $('#cartCount');
            if (target.length && target.is(':visible')) {
                return target;
            }
            return null;
        }

        function runFlyToCartAnimation(imgElement, targetElement) {
            if (!imgElement.length || !targetElement.length) return;
            let clone = imgElement.clone();
            let imgOffset = imgElement.offset();
            let targetOffset = targetElement.offset();
            let imgWidth = imgElement.outerWidth();
            let imgHeight = imgElement.outerHeight();
            let targetWidth = targetElement.outerWidth();
            let targetHeight = targetElement.outerHeight();

            let flyWrapper = $('<div class="fly-to-cart-wrapper"></div>').css({
                position: 'absolute',
                left: imgOffset.left,
                top: imgOffset.top,
                width: imgWidth,
                height: imgHeight,
                zIndex: 99999,
                pointerEvents: 'none',
                transition: 'transform 0.8s cubic-bezier(0.25, 0.1, 0.25, 1.0)'
            });

            let flyInner = $('<div class="fly-to-cart-inner"></div>').css({
                width: '100%',
                height: '100%',
                transition: 'transform 0.8s cubic-bezier(0.55, 0.055, 0.675, 0.19), opacity 0.8s ease'
            });

            clone.css({
                width: '100%',
                height: '100%',
                objectFit: 'cover',
                borderRadius: '8px'
            });

            flyInner.append(clone);
            flyWrapper.append(flyInner);
            $('body').append(flyWrapper);

            // Force reflow
            flyWrapper.width();

            let startX = imgOffset.left + imgWidth / 2;
            let startY = imgOffset.top + imgHeight / 2;
            let targetX = targetOffset.left + targetWidth / 2;
            let targetY = targetOffset.top + targetHeight / 2;

            let deltaX = targetX - startX;
            let deltaY = targetY - startY;

            flyWrapper.css('transform', `translateX(${deltaX}px)`);
            flyInner.css({
                'transform': `translateY(${deltaY}px) scale(0.1)`,
                'opacity': '0.1'
            });

            setTimeout(function() {
                flyWrapper.remove();
                targetElement.addClass('cart-bounce-pulse');
                let badge = targetElement.hasClass('cart-count') || targetElement.hasClass('mobile-cart-badge') || targetElement.hasClass('floating-cart-badge')
                    ? targetElement 
                    : targetElement.find('.cart-count, .mobile-cart-badge, .floating-cart-badge');
                if (badge.length) {
                    badge.addClass('badge-highlight');
                } else {
                    targetElement.addClass('badge-highlight');
                }
                setTimeout(function() {
                    targetElement.removeClass('cart-bounce-pulse');
                    if (badge.length) badge.removeClass('badge-highlight');
                    targetElement.removeClass('badge-highlight');
                }, 600);
            }, 800);
        }

        function addToCart(event, productId, variantId = null, quantity = 1) {
            if (typeof event === 'number') {
                quantity = variantId || 1;
                variantId = productId || null;
                productId = event;
                event = null;
            }

            $.post('{{ route("cart.add") }}', {
                product_id: productId,
                variant_id: variantId,
                quantity: quantity
            }, function(res) {
                $('#cartCount, #mobileCartCount, #floatingCartCount').text(res.count);
                
                // Show clean toast
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true
                });
                Toast.fire({
                    icon: 'success',
                    title: 'Added to Cart'
                });

                if (event) {
                    let btn = $(event.target).closest('button, a');
                    if (btn.length) {
                        let card = btn.closest('.product-card');
                        let img = card.find('.product-image img');
                        if (!img.length) {
                            img = $('#mainProductImg');
                        }
                        let target = getVisibleCartTarget();
                        if (img.length && target.length) {
                            runFlyToCartAnimation(img, target);
                        }
                    }
                }
            }).fail(function(xhr) {
                Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Failed to add to cart' });
            });
        }

        function buyNow(event, productId, variantId = null, quantity = 1) {
            if (typeof event === 'number') {
                quantity = variantId || 1;
                variantId = productId || null;
                productId = event;
                event = null;
            }
            $.post('{{ route("cart.add") }}', {
                product_id: productId,
                variant_id: variantId,
                quantity: quantity
            }, function(res) {
                window.location.href = '{{ route("checkout.index") }}';
            }).fail(function(xhr) {
                Swal.fire({ icon: 'error', title: 'Error', text: xhr.responseJSON?.message || 'Failed to process checkout' });
            });
        }

        function updateCartCount() {
            $.get('{{ route("cart.count") }}', function(data) {
                $('#cartCount, #mobileCartCount, #floatingCartCount').text(data.count);
            });
        }

        $(document).on('click', '.wishlist-btn', function() {
            let btn = $(this);
            let productId = btn.data('product');
            $.post('{{ route("customer.wishlist.toggle") }}', { product_id: productId }, function(res) {
                btn.find('i').toggleClass('far fas');
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true
                });
                Toast.fire({
                    icon: 'success',
                    title: res.message
                });
            });
        });

        $(document).on('click', '#floatingCartBtn', function() {
            loadCartSidebar();
            $('#cartSidebar, #cartOverlay').addClass('open');
            $('body').addClass('cart-open');
        });

        // Show floating cart only on desktop
        function toggleFloatingCart() {
            if (window.innerWidth >= 992) {
                $('#floatingCartBtn').css('display', 'flex');
            } else {
                $('#floatingCartBtn').css('display', 'none');
            }
        }
        toggleFloatingCart();
        $(window).on('resize', toggleFloatingCart);

        // Live Search
        let searchTimer;
        $('#searchInput').on('input', function() {
            clearTimeout(searchTimer);
            let q = $(this).val();
            if (q.length < 2) { $('#searchSuggestions').hide(); return; }
            searchTimer = setTimeout(function() {
                $.get('{{ route("search.suggestions") }}', { q: q }, function(html) {
                    $('#searchSuggestions').html(html).show();
                });
            }, 300);
        });
        $(document).click(function(e) {
            if (!$(e.target).closest('.header-search').length) $('#searchSuggestions').hide();
        });

        // Newsletter
        $('#newsletterForm').submit(function(e) {
            e.preventDefault();
            $.post('{{ route("newsletter.subscribe") }}', $(this).serialize(), function(res) {
                Swal.fire({ icon: 'success', title: 'Subscribed!', text: res.message, timer: 2000, showConfirmButton: false });
            }).fail(function(xhr) {
                Swal.fire({ icon: 'error', title: 'Oops!', text: xhr.responseJSON?.message || 'Something went wrong' });
            });
        });

        // Update cart count on page load
        updateCartCount();

        // Floating Contact Widget Toggle
        $(document).on('click', '#contactToggleBtn', function(e) {
            e.stopPropagation();
            $('#floatingContactWidget').toggleClass('active');
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('#floatingContactWidget').length) {
                $('#floatingContactWidget').removeClass('active');
            }
        });
    </script>

    <script src="{{ asset('js/typography.js') }}"></script>
    @stack('scripts')
    @yield('scripts')
</body>
</html>
