@extends('admin.layouts.app')
@section('title', 'Sửa Portfolio')

@section('content')
<div style="max-width:800px;">
    <div class="kb-card">
        <div class="kb-card-header">
            <h3 class="kb-card-title">Sửa: {{ $portfolio->title }}</h3>
            <a href="{{ route('admin.portfolios.index') }}" class="kb-btn kb-btn--sm" style="background:#f0f0f0;">← Quay lại</a>
        </div>
        <form method="POST" action="{{ route('admin.portfolios.update', $portfolio) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="kb-grid-dashboard" style="grid-template-columns:1fr 1fr;gap:20px;">
                <div class="kb-form-group" style="grid-column:span 2;">
                    <label>Tiêu đề <span style="color:red">*</span></label>
                    <input type="text" name="title" class="kb-form-control" value="{{ old('title', $portfolio->title) }}" required>
                </div>
                <div class="kb-form-group">
                    <label>Tên Khách Hàng</label>
                    <input type="text" name="client_name" class="kb-form-control" value="{{ old('client_name', $portfolio->client_name) }}">
                </div>
                <div class="kb-form-group">
                    <label>Danh mục</label>
                    <input type="text" name="category" class="kb-form-control" value="{{ old('category', $portfolio->category) }}">
                </div>
                <div class="kb-form-group">
                    <label>Ảnh TRƯỚC (thay đổi nếu muốn)</label>
                    @if($portfolio->beforeImage?->file_url)
                        <img src="{{ $portfolio->beforeImage->file_url }}" style="max-width:150px;border-radius:8px;margin-bottom:8px;display:block;">
                    @endif
                    <input type="file" name="before_image" class="kb-form-control kb-form-file" accept="image/*">
                    @error('before_image')<div style="color:red;font-size:13px;margin-top:5px;">{{ $message }}</div>@enderror
                </div>
                <div class="kb-form-group">
                    <label>Ảnh SAU (thay đổi nếu muốn)</label>
                    @if($portfolio->afterImage?->file_url)
                        <img src="{{ $portfolio->afterImage->file_url }}" style="max-width:150px;border-radius:8px;margin-bottom:8px;display:block;">
                    @endif
                    <input type="file" name="after_image" class="kb-form-control kb-form-file" accept="image/*">
                    @error('after_image')<div style="color:red;font-size:13px;margin-top:5px;">{{ $message }}</div>@enderror
                </div>
                <div class="kb-form-group" style="grid-column:span 2;">
                    <label>Mô tả thêm</label>
                    <textarea name="description" class="kb-form-control" rows="3">{{ old('description', $portfolio->description) }}</textarea>
                </div>
                <div class="kb-form-group">
                    <label style="display:flex;align-items:center;gap:10px;cursor:pointer;">
                        <input type="checkbox" name="is_featured" value="1" {{ $portfolio->is_featured ? 'checked' : '' }} style="width:18px;height:18px;accent-color:var(--primary);">
                        <span>Hiển thị ở mục Nổi Bật</span>
                    </label>
                </div>
            </div>
            <div style="display:flex;gap:15px;margin-top:10px;">
                <button type="submit" class="kb-btn kb-btn--primary">Lưu Thay Đổi</button>
                <a href="{{ route('admin.portfolios.index') }}" class="kb-btn" style="background:#f0f0f0;">Hủy bỏ</a>
            </div>
        </form>
    </div>
</div>
@endsection
