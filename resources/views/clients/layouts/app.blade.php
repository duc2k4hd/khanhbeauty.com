<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/clients/uploads/khanhbeauty.png') }}">
    
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
