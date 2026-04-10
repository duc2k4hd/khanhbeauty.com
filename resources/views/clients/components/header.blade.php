{{-- FLOATING CTA --}}
<button class="floating-cta js-booking-trigger" id="floatingCta">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
    Đặt Lịch Ngay
</button>

{{-- NAVIGATION --}}
<nav class="khanhbeauty-nav" id="mainNav">
    {{-- LOGO & DANH MỤC (DESKTOP) --}}
    <div class="khanhbeauty-nav__left">
        <div class="khanhbeauty-nav__logo" onclick="window.location='{{ route('home') }}'" style="cursor:pointer">
            KHÁNH <span>BEAUTY</span>
        </div>
        
        <button class="kb-nav__categories-desktop d-none-mobile" id="openMegaMenuDesktop">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M3 12h18M3 6h18M3 18h18"/>
            </svg>
            <span>Danh mục</span>
        </button>
    </div>


    {{-- MOBILE ICONS (YODY STYLE) --}}
    <div class="kb-nav__mobile-actions">
        <button class="kb-nav__icon-btn"><svg viewBox="0 0 24 24"><path d="M11 19a8 8 0 100-16 8 8 0 000 16zM21 21l-4.35-4.35"/></svg></button>
        <button class="kb-nav__icon-btn"><svg viewBox="0 0 24 24"><path d="M6 21v-2a4 4 0 014-4h4a4 4 0 014 4v2M12 7a4 4 0 110-8 4 4 0 010 8z"/></svg></button>
    </div>

    {{-- MENU CHÍNH (DESKTOP) --}}
    <ul class="khanhbeauty-nav__menu d-none-mobile">
        <li><a href="{{ route('home') }}#hero">Trang chủ</a></li>
        <li><a href="{{ route('home') }}#about">Về chúng tôi</a></li>
        <li><a href="{{ route('home') }}#services">Dịch vụ</a></li>
        <li><a href="{{ route('home') }}#gallery">Portfolio</a></li>
        <li><a href="#">Tin tức</a></li>
        <li><a href="#">Liên hệ</a></li>
    </ul>

    <button class="khanhbeauty-nav__book d-none-mobile js-booking-trigger">Đặt Lịch</button>

    {{-- Thanh tiến trình đọc (Reading Progress) --}}
    <div class="kb-reading-progress">
        <div class="kb-reading-progress-bar" id="readingProgressBar"></div>
    </div>
</nav>

{{-- NÚT DANH MỤC - NẰNG NGOÀI nav để tránh backdrop-filter bug (CHỈ HIỆN MOBILE) --}}
<button class="kb-nav__categories-mobile" id="openMegaMenuMobile">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <path d="M3 12h18M3 6h18M3 18h18"/>
    </svg>
    <span>Danh mục</span>
</button>


@include('clients.partials.mega-menu')
