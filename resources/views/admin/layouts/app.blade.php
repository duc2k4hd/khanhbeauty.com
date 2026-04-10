<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - Khánh Beauty</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Quicksand:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin/main.css') }}">
    
    @stack('styles')
</head>
<body>
    <div class="kb-admin-layout">
        
        <!-- SIDEBAR -->
        @include('admin.layouts.sidebar')

        <!-- MAIN CONTENT WRAPPER -->
        <div class="kb-admin-main" id="adminMain">
            <!-- HEADER -->
            @include('admin.layouts.header')

            <!-- PAGE CONTENT -->
            <div class="kb-admin-content">
                @if (session('success'))
                    <div class="kb-alert kb-alert--success">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="kb-alert kb-alert--danger">
                        {{ session('error') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="kb-alert kb-alert--danger">
                        {{ $errors->first() }}
                    </div>
                @endif

                @yield('content')
            </div>
        </div>

    </div>

    <!-- Core Scripts -->
    <script src="{{ asset('js/admin/main.js') }}"></script>
    @stack('scripts')
</body>
</html>
