<header class="kb-admin-header">
    <div class="kb-admin-header__left">
        <button id="toggleSidebar" class="kb-admin-header__btn">
            <svg viewBox="0 0 24 24"><path stroke="currentColor" stroke-width="2" stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>
        <h2 class="kb-admin-header__title">@yield('title', 'Bảng Điều Khiển')</h2>
    </div>

    <div class="kb-admin-header__right">
        <div class="kb-admin-header__user">
            <span>Xin chào, <strong>{{ Auth::user()->full_name ?? 'Admin' }}</strong></span>
            
            <form action="{{ route('admin.logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="kb-btn-logout">Đăng Xuất</button>
            </form>
        </div>
    </div>
</header>
