<aside class="kb-admin-sidebar" id="adminSidebar">
    <div class="kb-admin-sidebar__logo">
        <a href="{{ route('admin.dashboard') }}">KHÁNH <span>BEAUTY</span></a>
        <button id="toggleSidebarClose" class="kb-admin-sidebar__close">&times;</button>
    </div>

    <nav class="kb-admin-sidebar__nav">
        <ul>
            <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}">
                    <svg viewBox="0 0 24 24"><path stroke="currentColor" stroke-width="2" fill="none" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path></svg>
                    <span>Bảng Điều Khiển</span>
                </a>
            </li>
            
            <li class="nav-heading">QUẢN LÝ NỘI DUNG</li>
            <li class="{{ request()->routeIs('admin.homepage.*') ? 'active' : '' }}">
                <a href="{{ route('admin.homepage.index') }}">
                    <svg viewBox="0 0 24 24"><path stroke="currentColor" stroke-width="2" fill="none" d="M3 3h7v7H3zM14 3h7v7h-7zM3 14h7v7H3zM14 14h7v7h-7z"></path></svg>
                    <span>Quản Lý Trang Chủ</span>
                </a>
            </li>

            <li class="nav-heading">QUẢN LÝ DỊCH VỤ</li>
            <li class="{{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                <a href="{{ route('admin.services.index') }}">
                    <svg viewBox="0 0 24 24"><path stroke="currentColor" stroke-width="2" fill="none" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path></svg>
                    <span>Sản phẩm & Dịch vụ</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('admin.portfolios.*') ? 'active' : '' }}">
                <a href="{{ route('admin.portfolios.index') }}">
                    <svg viewBox="0 0 24 24"><path stroke="currentColor" stroke-width="2" fill="none" d="M3 3h18v18H3z M8 21v-5 M16 21v-5"></path></svg>
                    <span>Thư Viện Ảnh (Portfolio)</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('admin.media.*') ? 'active' : '' }}">
                <a href="{{ route('admin.media.view') }}">
                    <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2" fill="none"></rect><circle cx="8.5" cy="8.5" r="1.5" stroke="currentColor" stroke-width="2" fill="none"></circle><path d="M21 15l-5-5L5 21" stroke="currentColor" stroke-width="2" fill="none"></path></svg>
                    <span>Quản Lý Media (Pro)</span>
                </a>
            </li>

            <li class="nav-heading">HỆ THỐNG</li>
            <li class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <a href="{{ route('admin.settings.index') }}">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2" fill="none"></circle><path stroke="currentColor" stroke-width="2" fill="none" d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 11-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 11-2.83-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 110-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 112.83-2.83l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51h.09a1.65 1.65 0 001.82-.33l.06-.06a2 2 0 112.83 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"></path></svg>
                    <span>Cài Đặt Cấu Hình</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>
