@extends('admin.layouts.app')
@section('title', 'Thêm Ảnh Portfolio')

@section('content')
<div style="max-width:800px;">
    <div class="kb-card">
        <div class="kb-card-header">
            <h3 class="kb-card-title">Thêm Ảnh Portfolio Mới</h3>
            <a href="{{ route('admin.portfolios.index') }}" class="kb-btn kb-btn--sm" style="background:#f0f0f0;">← Quay lại</a>
        </div>
        <form method="POST" action="{{ route('admin.portfolios.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="kb-grid-dashboard" style="grid-template-columns:1fr 1fr;gap:20px;">
                <div class="kb-form-group" style="grid-column:span 2;">
                    <label>Tiêu đề <span style="color:red">*</span></label>
                    <input type="text" name="title" class="kb-form-control" value="{{ old('title') }}" placeholder="VD: Trang Điểm Cô Dâu Sang Trọng" required>
                    @error('title')<div style="color:red;font-size:13px;margin-top:5px;">{{ $message }}</div>@enderror
                </div>
                <div class="kb-form-group">
                    <label>Tên Khách Hàng</label>
                    <input type="text" name="client_name" class="kb-form-control" value="{{ old('client_name') }}" placeholder="Nguyễn Thị Lan">
                </div>
                <div class="kb-form-group">
                    <label>Danh mục</label>
                    <input type="text" name="category" class="kb-form-control" value="{{ old('category') }}" placeholder="VD: Cô Dâu, Kỷ Yếu, Sự Kiện...">
                </div>
                <div class="kb-form-group">
                    <label>Ảnh TRƯỚC (Before)</label>
                    <input type="file" name="before_image" class="kb-form-control kb-form-file" accept="image/*" id="beforeInput">
                    <img id="beforePreview" src="" style="max-width:100%;border-radius:8px;margin-top:8px;display:none;">
                    @error('before_image')<div style="color:red;font-size:13px;margin-top:5px;">{{ $message }}</div>@enderror
                </div>
                <div class="kb-form-group">
                    <label>Ảnh SAU (After) <span style="color:var(--primary)">✨</span></label>
                    <input type="file" name="after_image" class="kb-form-control kb-form-file" accept="image/*" id="afterInput">
                    <img id="afterPreview" src="" style="max-width:100%;border-radius:8px;margin-top:8px;display:none;">
                    @error('after_image')<div style="color:red;font-size:13px;margin-top:5px;">{{ $message }}</div>@enderror
                </div>
                <div class="kb-form-group" style="grid-column:span 2;">
                    <label>Mô tả thêm</label>
                    <textarea name="description" class="kb-form-control" rows="3" placeholder="Ghi chú về ca makeup này...">{{ old('description') }}</textarea>
                </div>
                <div class="kb-form-group">
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer;">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} style="width:18px;height:18px;accent-color:var(--primary);">
                        <span>Hiển thị ở mục Nổi Bật</span>
                    </label>
                </div>
            </div>
            <div style="display:flex;gap:15px;margin-top:10px;">
                <button type="submit" class="kb-btn kb-btn--primary">Đăng Ảnh Portfolio</button>
                <a href="{{ route('admin.portfolios.index') }}" class="kb-btn" style="background:#f0f0f0;">Hủy bỏ</a>
            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script>
function previewImg(inputId, previewId) {
    document.getElementById(inputId).addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = ev => {
                const el = document.getElementById(previewId);
                el.src = ev.target.result;
                el.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });
}
previewImg('beforeInput', 'beforePreview');
previewImg('afterInput', 'afterPreview');
</script>
@endpush
