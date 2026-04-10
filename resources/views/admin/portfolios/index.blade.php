@extends('admin.layouts.app')
@section('title', 'Thư Viện Ảnh Portfolio')

@section('content')

<div class="kb-card">
    <div class="kb-card-header">
        <h3 class="kb-card-title">Thư Viện Portfolio <span style="color:#888;font-size:14px;">({{ $portfolios->total() }} mục)</span></h3>
        <a href="{{ route('admin.portfolios.create') }}" class="kb-btn kb-btn--primary">
            <svg viewBox="0 0 24 24" style="width:16px;height:16px;stroke:currentColor;stroke-width:2.5;fill:none"><path d="M12 5v14M5 12h14"/></svg>
            Thêm Ảnh Mới
        </a>
    </div>

    {{-- Grid Ảnh --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:20px;">
        @forelse($portfolios as $item)
        <div style="border:1px solid #eee;border-radius:12px;overflow:hidden;background:#fff;">
            {{-- Before/After Preview --}}
            <div style="position:relative;height:160px;background:#f8f8f8;display:flex;">
                @if($item->beforeImage?->file_url)
                    <img src="{{ $item->beforeImage->file_url }}" style="width:50%;object-fit:cover;" title="Trước">
                @else
                    <div style="width:50%;display:flex;align-items:center;justify-content:center;font-size:30px;color:#ddd;">📷</div>
                @endif
                @if($item->afterImage?->file_url)
                    <img src="{{ $item->afterImage->file_url }}" style="width:50%;object-fit:cover;border-left:2px solid white;" title="Sau">
                @else
                    <div style="width:50%;display:flex;align-items:center;justify-content:center;font-size:30px;color:#ddd;border-left:2px solid #f0f0f0;">✨</div>
                @endif
                <span style="position:absolute;top:8px;left:8px;background:rgba(0,0,0,0.5);color:#fff;font-size:10px;padding:2px 6px;border-radius:4px;">TRƯỚC</span>
                <span style="position:absolute;top:8px;right:8px;background:rgba(212,163,115,0.85);color:#fff;font-size:10px;padding:2px 6px;border-radius:4px;">SAU</span>
                @if($item->is_featured)
                    <span style="position:absolute;bottom:8px;left:8px;background:#fbbf24;color:#000;font-size:10px;padding:2px 6px;border-radius:4px;font-weight:700;">⭐ NỔI BẬT</span>
                @endif
            </div>
            <div style="padding:12px;">
                <div style="font-weight:600;font-size:14px;margin-bottom:4px;">{{ $item->title }}</div>
                @if($item->client_name)
                    <div style="font-size:12px;color:#888;">Khách: {{ $item->client_name }}</div>
                @endif
                <div style="display:flex;gap:8px;margin-top:12px;">
                    <a href="{{ route('admin.portfolios.edit', $item) }}" class="kb-btn kb-btn--sm kb-btn--edit" style="flex:1;justify-content:center;">Sửa</a>
                    <form method="POST" action="{{ route('admin.portfolios.destroy', $item) }}" onsubmit="return confirm('Xóa ảnh này?')" style="flex:1;">
                        @csrf @method('DELETE')
                        <button type="submit" class="kb-btn kb-btn--sm kb-btn--danger" style="width:100%;">Xóa</button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div style="grid-column:span 4;text-align:center;padding:60px;color:#888;">
            <div style="font-size:50px;margin-bottom:15px;">🌸</div>
            Chưa có ảnh nào. <a href="{{ route('admin.portfolios.create') }}" style="color:var(--primary)">Upload ảnh đầu tiên?</a>
        </div>
        @endforelse
    </div>

    <div style="margin-top:20px;">{{ $portfolios->links() }}</div>
</div>

@endsection
