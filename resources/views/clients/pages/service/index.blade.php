@extends('clients.layouts.app')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/clients/service.css') }}">
@endpush

@section('content')

{{-- HERO --}}
<div class="sv-hero">
    <div class="sv-hero-eyebrow">✦ Khánh Beauty</div>
    <h1>Dịch vụ <span>Làm đẹp</span><br>Chuyên Nghiệp</h1>
    <p>"Nơi mỗi nét cọ là một tác phẩm, mang lại vẻ đẹp hoàn hảo và tự tin nhất cho bạn."</p>
</div>

<div class="sv-page">
    {{-- FILTER BAR --}}
    <div class="sv-filter-wrap sv-reveal">
        <div class="sv-filter-bar">
            <button class="sv-filter-btn active" data-cat="all">Tất cả</button>
            @foreach($categories as $cat)
            <button class="sv-filter-btn" data-cat="{{ $cat->slug ?? $cat->id }}">{{ $cat->name }}</button>
            @endforeach
        </div>
    </div>

    {{-- SERVICES GRID --}}
    <div class="sv-grid-wrap sv-reveal" style="transition-delay: 0.1s">
        <div class="sv-grid" id="svGrid">
            @foreach($services as $service)
            <div class="sv-card" data-cat="{{ $service->category->slug ?? $service->category->id }}">
                <div class="sv-card-img">
                    <img src="{{ $service->featuredImage?->file_url ?? '/images/no-image.webp' }}" alt="{{ $service->featuredImage?->alt_text ?? $service->name }}" loading="lazy">
                    <div class="sv-card-img-overlay"></div>
                    <span class="sv-card-cat">{{ $service->category->name }}</span>
                    @if($service->sale_price)
                    <span class="sv-card-sale-badge">Ưu đãi</span>
                    @endif
                    <div class="sv-card-price-overlay">
                        <span class="price-val">{{ number_format($service->sale_price ?: $service->price) }}đ</span>
                        <span class="price-unit">/ {{ $service->price_unit }}</span>
                    </div>
                </div>
                <div class="sv-card-body">
                    <h3 class="sv-card-name">{{ $service->name }}</h3>
                    <p class="sv-card-desc">{{ $service->short_description }}</p>
                    <a href="{{ route('services.show', $service->slug) }}" class="sv-card-link">
                        Xem chi tiết <i class="fas fa-arrow-right" style="font-size:10px"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- WHY US --}}
    <div class="sv-why sv-reveal" style="transition-delay: 0.2s; padding-bottom: 0;">
        <div class="sv-why-inner">
            <div class="sv-why-item">
                <div class="sv-why-icon"><i class="fas fa-gem"></i></div>
                <h4>Mỹ phẩm High-end</h4>
                <p>100% sản phẩm chính hãng từ các thương hiệu danh tiếng Chanel, Dior, MAC, YSL.</p>
            </div>
            <div class="sv-why-item">
                <div class="sv-why-icon"><i class="fas fa-paint-brush"></i></div>
                <h4>Nghệ thuật Tinh tế</h4>
                <p>Mỗi gương mặt là một tác phẩm được thiết kế riêng, phù hợp với cá tính của bạn.</p>
            </div>
            <div class="sv-why-item">
                <div class="sv-why-icon"><i class="fas fa-heart"></i></div>
                <h4>Phục vụ Tận tâm</h4>
                <p>Sự hài lòng của khách hàng là giá trị cốt lõi và niềm tự hào của Khánh Beauty.</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Scroll reveal
    const els = document.querySelectorAll('.sv-reveal');
    const io = new IntersectionObserver((entries) => {
        entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); io.unobserve(e.target); } });
    }, { threshold: 0.1 });
    els.forEach(el => io.observe(el));

    // Category filter
    const btns = document.querySelectorAll('.sv-filter-btn');
    const cards = document.querySelectorAll('.sv-card');
    btns.forEach(btn => {
        btn.addEventListener('click', () => {
            btns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            const cat = btn.dataset.cat;
            cards.forEach(card => {
                if (cat === 'all' || card.dataset.cat === cat) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
});
</script>
@endpush

@endsection
