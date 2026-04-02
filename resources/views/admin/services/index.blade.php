@extends('admin.layouts.app')
@section('title', 'Quản lý Dịch Vụ')

@section('content')

<div class="kb-card">
    <div class="kb-card-header">
        <h3 class="kb-card-title">Danh sách Dịch vụ <span style="color:#888;font-size:14px;">({{ $services->total() }} dịch vụ)</span></h3>
        <a href="{{ route('admin.services.create') }}" class="kb-btn kb-btn--primary">
            <svg viewBox="0 0 24 24" style="width:16px;height:16px;stroke:currentColor;stroke-width:2.5;fill:none"><path d="M12 5v14M5 12h14"/></svg>
            Thêm Dịch Vụ Mới
        </a>
    </div>

    <table class="kb-table">
        <thead>
            <tr>
                <th>ẢNH</th>
                <th>TÊN DỊCH VỤ</th>
                <th>DANH MỤC</th>
                <th>GIÁ</th>
                <th>HIỂN THỊ</th>
                <th>THAO TÁC</th>
            </tr>
        </thead>
        <tbody>
            @forelse($services as $service)
            <tr id="svc-row-{{ $service->id }}">
                <td>
                    @if($service->featuredImage)
                        <img src="{{ $service->featuredImage->file_url }}" style="width:56px;height:56px;object-fit:cover;border-radius:8px;">
                    @else
                        <div style="width:56px;height:56px;background:#f0f0f0;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:22px;">🌸</div>
                    @endif
                </td>
                <td>
                    <strong>{{ $service->name }}</strong>
                    <div style="font-size:13px;color:#888;margin-top:3px;">{{ Str::limit($service->short_description, 60) }}</div>
                </td>
                <td>
                    <span style="background:#f5f0ff;color:#7c3aed;padding:4px 10px;border-radius:20px;font-size:12px;font-weight:600;">
                        {{ $service->category->name ?? '—' }}
                    </span>
                </td>
                <td>
                    <strong>{{ number_format($service->price) }}đ</strong> 
                    <span style="color:#888;font-size:12px;">/ {{ $service->price_unit ?? 'buổi' }}</span>
                    @if($service->sale_price)
                        <div style="font-size:12px;color:#ef4444;text-decoration:line-through;">{{ number_format($service->sale_price) }}đ</div>
                    @endif
                </td>
                <td>
                    {{-- Toggle Switch AJAX --}}
                    <label class="kb-switch" title="Nhấn để bật/tắt hiển thị">
                        <input type="checkbox" 
                               class="js-toggle-service" 
                               data-id="{{ $service->id }}"
                               data-url="{{ route('admin.services.toggle_status', $service) }}"
                               {{ $service->is_active ? 'checked' : '' }}>
                        <span class="kb-switch-slider"></span>
                    </label>
                </td>
                <td>
                    <div style="display:flex;gap:8px;">
                        <a href="{{ route('admin.services.edit', $service) }}" class="kb-btn kb-btn--sm kb-btn--edit">Sửa</a>
                        <form method="POST" action="{{ route('admin.services.destroy', $service) }}" onsubmit="return confirm('Bạn chắc muốn xóa dịch vụ này?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="kb-btn kb-btn--sm kb-btn--danger">Xóa</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center;padding:40px;color:#888;">Chưa có dịch vụ nào. <a href="{{ route('admin.services.create') }}" style="color:var(--primary)">Tạo ngay?</a></td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top:20px;">
        {{ $services->links() }}
    </div>
</div>

<!-- Toast Thông Báo Nhanh -->
<div id="adminToast" style="
    position:fixed; bottom:30px; right:30px; z-index:9999;
    background:#1c1f23; color:#fff; padding:15px 25px; border-radius:10px;
    font-size:14px; opacity:0; transform:translateY(20px);
    transition:all 0.3s ease; pointer-events:none; max-width:350px;
    box-shadow:0 10px 30px rgba(0,0,0,0.2);
"></div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const toast = document.getElementById('adminToast');
    
    function showToast(msg, isSuccess) {
        toast.textContent = msg;
        toast.style.background = isSuccess ? '#15803d' : '#dc2626';
        toast.style.opacity = '1';
        toast.style.transform = 'translateY(0)';
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(20px)';
        }, 3000);
    }

    // AJAX Toggle Status cho từng hàng
    document.querySelectorAll('.js-toggle-service').forEach(function(toggle) {
        toggle.addEventListener('change', function() {
            const url = this.dataset.url;
            const row = document.getElementById('svc-row-' + this.dataset.id);
            
            fetch(url, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                }
            })
            .then(res => res.json())
            .then(data => {
                showToast(data.message, data.is_active);
            })
            .catch(() => {
                showToast('Có lỗi xảy ra, vui lòng thử lại!', false);
                // Rollback UI nếu request lỗi
                this.checked = !this.checked;
            });
        });
    });
});
</script>
@endpush
