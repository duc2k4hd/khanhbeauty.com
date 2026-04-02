@extends('admin.layouts.app')
@section('title', 'Bảng Điều Khiển Tổng Quan')

@section('content')

<!-- GRID THỐNG KÊ LỚN -->
<div class="kb-grid-dashboard">
    <!-- Card 1 -->
    <div class="kb-stat-card">
        <div class="kb-stat-card__icon">
            <svg viewBox="0 0 24 24"><path stroke="currentColor" stroke-width="2" fill="none" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline stroke="currentColor" stroke-width="2" fill="none" points="14 2 14 8 20 8"></polyline><line stroke="currentColor" stroke-width="2" fill="none" x1="16" y1="13" x2="8" y2="13"></line><line stroke="currentColor" stroke-width="2" fill="none" x1="16" y1="17" x2="8" y2="17"></line><polyline stroke="currentColor" stroke-width="2" fill="none" points="10 9 9 9 8 9"></polyline></svg>
        </div>
        <div class="kb-stat-card__info">
            <h4>TỔNG CHUYÊN MỤC DỊCH VỤ</h4>
            <p>{{ $stats['services'] }}</p>
        </div>
    </div>
    <!-- Card 2 -->
    <div class="kb-stat-card">
        <div class="kb-stat-card__icon" style="color: #6366f1; background: rgba(99,102,241,0.1)">
            <svg viewBox="0 0 24 24"><rect stroke="currentColor" stroke-width="2" fill="none" x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle stroke="currentColor" stroke-width="2" fill="none" cx="8.5" cy="8.5" r="1.5"></circle><polyline stroke="currentColor" stroke-width="2" fill="none" points="21 15 16 10 5 21"></polyline></svg>
        </div>
        <div class="kb-stat-card__info">
            <h4>HÌNH ẢNH PORTFOLIO</h4>
            <p>{{ $stats['portfolios'] }}</p>
        </div>
    </div>
    <!-- Card 3 -->
    <div class="kb-stat-card">
        <div class="kb-stat-card__icon" style="color: #10b981; background: rgba(16,185,129,0.1)">
            <svg viewBox="0 0 24 24"><rect stroke="currentColor" stroke-width="2" fill="none" x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line stroke="currentColor" stroke-width="2" fill="none" x1="16" y1="2" x2="16" y2="6"></line><line stroke="currentColor" stroke-width="2" fill="none" x1="8" y1="2" x2="8" y2="6"></line><line stroke="currentColor" stroke-width="2" fill="none" x1="3" y1="10" x2="21" y2="10"></line></svg>
        </div>
        <div class="kb-stat-card__info">
            <h4>LỊCH ĐẶT MỚI (CHỜ)</h4>
            <p>{{ $stats['bookings'] }}</p>
        </div>
    </div>
    <!-- Card 4 -->
    <div class="kb-stat-card">
        <div class="kb-stat-card__icon" style="color: #ec4899; background: rgba(236,72,153,0.1)">
            <svg viewBox="0 0 24 24"><path stroke="currentColor" stroke-width="2" fill="none" d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle stroke="currentColor" stroke-width="2" fill="none" cx="9" cy="7" r="4"></circle><path stroke="currentColor" stroke-width="2" fill="none" d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path stroke="currentColor" stroke-width="2" fill="none" d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
        </div>
        <div class="kb-stat-card__info">
            <h4>VIEW (PHÁI SINH)</h4>
            <p>{{ number_format($stats['visitors']) }}</p>
        </div>
    </div>
</div>

<div class="kb-card">
    <div class="kb-card-header">
        <h3 class="kb-card-title">Hướng Dẫn Khởi Đầu (Quick Start)</h3>
    </div>
    <div>
        <p style="margin-bottom: 20px; line-height: 1.6;">Xin chào Khách Hàng, đây là hệ thống được phát triển chuyên biệt "Made for You" (Không sử dụng bất kì Frame/Template tải sẵn nào) nhằm đạt tốc độ tải 100% Google Insight PageSpeed. Dưới là các quy trình thông dụng:</p>
        <ul style="padding-left: 20px; line-height: 1.8;">
            <li><strong>Cập nhật Điện Thoại / Fanpage: </strong> Truy cập [Hệ Thống] > Cài Đặt Cấu Hình.</li>
            <li><strong>Up ảnh Gallery (Portfolio slider): </strong> Nhấp vào Menu [Thư Viện Ảnh], tạo Category trước rồi Upload Album sau.</li>
            <li><strong>Chỉnh giá Dịch Vụ Mới: </strong> Vào Menu [Sản Phẩm & Dịch Vụ], Nhấn sửa hoặc Bật tắt công tắc ẩn hiển để tạm ngưng dịp Tết.</li>
        </ul>
        <br>
        <p style="color: var(--primary); font-weight: 500;">Made with ❤️ by Agent AI Deepmind System.</p>
    </div>
</div>

@endsection
