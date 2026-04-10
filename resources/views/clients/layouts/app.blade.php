<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>document.documentElement.classList.add('js-enabled');</script>
    <link rel="icon" type="image/png" href="{{ \App\Models\SiteSetting::getValue('favicon_url', asset('images/clients/uploads/khanhbeauty.png')) }}">

    @if(\App\Models\SiteSetting::getValue('google_site_verification'))
    <meta name="google-site-verification" content="{{ \App\Models\SiteSetting::getValue('google_site_verification') }}">
    @endif

    {!! SEO::generate() !!}

    {{-- Core Assets (MUST LOAD FIRST to prevent FOUC) --}}
    <link rel="stylesheet" href="{{ asset('css/clients/main.css') }}">
    
    {{-- FontAwesome Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- Page Specific Assets --}}
    @stack('styles')

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400&family=Quicksand:wght@300;400;500;600&display=swap" rel="stylesheet">

    {{-- Google Analytics --}}
    @if(\App\Models\SiteSetting::getValue('google_analytics_id'))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ \App\Models\SiteSetting::getValue('google_analytics_id') }}"></script>
    <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','{{ \App\Models\SiteSetting::getValue('google_analytics_id') }}');</script>
    @endif

    {{-- Facebook Pixel --}}
    @if(\App\Models\SiteSetting::getValue('facebook_pixel_id'))
    <script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init','{{ \App\Models\SiteSetting::getValue('facebook_pixel_id') }}');fbq('track','PageView');</script>
    @endif
</head>
<body>

    {{-- HEADER COMPONENT --}}
    @include('clients.components.header')

    <main>
        @yield('content')
    </main>

    {{-- FOOTER COMPONENT --}}
    @include('clients.components.footer')

    {{-- MODALS --}}
    @include('clients.partials.booking-modal')
    @include('clients.partials.mega-menu')

    {{-- Core Scripts --}}
    <script src="{{ asset('js/clients/main.js') }}"></script>
    <script src="{{ asset('js/clients/booking.js') }}"></script>
    
    {{-- Page Specific Scripts --}}
    @stack('scripts')
</body>
</html>
