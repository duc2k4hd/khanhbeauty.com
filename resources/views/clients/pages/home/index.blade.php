@extends('clients.layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/clients/home.css') }}">
@endpush

@section('content')
<!-- ═══ SECTION 1: HERO ═══ -->
<section class="khanhbeauty-hero">
  <div class="khanhbeauty-hero__decor-circle"></div>
  <div class="khanhbeauty-hero__decor-circle"></div>
  <div class="khanhbeauty-hero__decor-circle"></div>

  <svg class="khanhbeauty-hero__brush" viewBox="0 0 800 200" fill="none">
    <path d="M50 100C150 30 250 170 400 100C550 30 650 170 750 100" stroke="var(--pink-300)" stroke-width="80" stroke-linecap="round" opacity="0.15"/>
  </svg>

  <div class="khanhbeauty-hero__content">
    <div class="khanhbeauty-hero__badge">✦ Professional Makeup Artist ✦</div>
    <h1 class="khanhbeauty-hero__title">
      Khánh <em>Beauty</em>
    </h1>
    <p class="khanhbeauty-hero__subtitle">Nghệ thuật trang điểm — Tôn vinh vẻ đẹp của bạn</p>
    <div class="khanhbeauty-hero__cta-group">
      <button class="khanhbeauty-hero__cta khanhbeauty-hero__cta--primary" onclick="document.getElementById('booking').scrollIntoView({behavior:'smooth'})">Đặt Lịch Makeup</button>
      <button class="khanhbeauty-hero__cta khanhbeauty-hero__cta--secondary" onclick="document.getElementById('gallery').scrollIntoView({behavior:'smooth'})">Xem Portfolio</button>
    </div>
  </div>

  <div class="khanhbeauty-hero__scroll">
    <span>Cuộn xuống</span>
    <div class="khanhbeauty-hero__scroll-line"></div>
  </div>
</section>

<!-- ═══ SECTION 2: ABOUT ═══ -->
<section class="khanhbeauty-about" id="about">
  <div class="khanhbeauty-about__grid">
    <div class="khanhbeauty-about__image-wrap reveal-left">
      <div class="khanhbeauty-about__image-diamond">
        <img src="/images/clients/about.png" alt="Khánh Beauty" style="width: 100%; height: 100%; object-fit: cover;">
      </div>
      <div class="khanhbeauty-about__image-frame"></div>
      <div class="khanhbeauty-about__exp-badge reveal">
        <strong>5+</strong>
        <span>Năm kinh nghiệm</span>
      </div>
    </div>

    <div class="khanhbeauty-about__text reveal-right">
      <span class="section-label">Về tôi</span>
      <h2>Xin chào, mình là <em>Khánh</em> — người sẽ giúp bạn toả sáng</h2>
      <p>Với niềm đam mê trang điểm từ nhỏ, mình đã dành nhiều năm học hỏi và trau dồi kỹ năng để mang đến cho mỗi khách hàng một diện mạo hoàn hảo nhất. Từ cô dâu trong ngày trọng đại, đến các bạn sinh viên muốn tự tin hơn — mình luôn lắng nghe và thấu hiểu.</p>
      <p>Không chỉ là makeup, mình muốn mỗi lần ngồi trước gương cùng bạn là một trải nghiệm vui vẻ, thoải mái và đáng nhớ.</p>
      
      <div class="khanhbeauty-about__stats stagger-children">
        <div class="khanhbeauty-about__stat-item reveal">
          <div class="khanhbeauty-about__stat-num" data-count="500">0</div>
          <div class="khanhbeauty-about__stat-label">Khách hàng</div>
        </div>
        <div class="khanhbeauty-about__stat-item reveal">
          <div class="khanhbeauty-about__stat-num" data-count="200">0</div>
          <div class="khanhbeauty-about__stat-label">Cô dâu</div>
        </div>
        <div class="khanhbeauty-about__stat-item reveal">
          <div class="khanhbeauty-about__stat-num" data-count="50">0</div>
          <div class="khanhbeauty-about__stat-label">Học viên</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══ SECTION 3: SERVICES ═══ -->
<section class="khanhbeauty-services" id="services">
  <div class="khanhbeauty-services__header reveal">
    <span class="section-label">Dịch vụ</span>
    <h2>Mình có thể giúp bạn điều gì?</h2>
  </div>

  <div class="khanhbeauty-services__grid stagger-children">
    @foreach($featuredServices as $service)
    <a href="{{ route('services.show', $service->slug) }}" class="khanhbeauty-service-card-link">
      <div class="khanhbeauty-service-card reveal">
        <div class="khanhbeauty-service-card__icon">
          <img src="{{ $service->featuredImage?->file_url ?? '/images/no-image.webp' }}" 
               onerror="this.src='/images/no-image.webp'" 
               alt="{{ $service->featuredImage?->alt_text ?? $service->name }}">
        </div>
        <h3>{{ $service->name }}</h3>
        <p>{{ $service->short_description }}</p>
        <div class="khanhbeauty-service-card__price">
          @if($service->price > 0)
            Từ {{ number_format($service->price / 1000, 0, ',', '.') }}K <small>/ {{ $service->price_unit }}</small>
          @else
            Miễn phí <small>khi book makeup</small>
          @endif
        </div>
      </div>
    </a>
    @endforeach
  </div>
</section>

<!-- ═══ SECTION 4: SKILLS SHOWCASE ═══ -->
<section class="khanhbeauty-showcase" id="showcase">
  <div class="khanhbeauty-showcase__header reveal">
    <span class="section-label">Kỹ năng thực chiến</span>
    <h2>Từng đường nét, từng chi tiết — đều là nghệ thuật</h2>
  </div>

  <div class="khanhbeauty-showcase__item">
    <div class="khanhbeauty-showcase__image reveal-left">
      <img src="/images/clients/service-bridal.png" alt="Kỹ năng kẻ mắt" style="aspect-ratio: 6/5; object-fit: cover;">
      <div class="khanhbeauty-showcase__image-tag">✦ Makeup Cô Dâu</div>
    </div>
    <div class="khanhbeauty-showcase__content reveal-right">
      <div class="khanhbeauty-showcase__step-num">01</div>
      <h3>Kẻ Mắt — Đôi mắt kể câu chuyện</h3>
      <p>Mỗi đôi mắt có một hình dáng riêng. Mình không áp dụng công thức chung cho tất cả — mà sẽ phân tích dáng mắt, hốc mắt để tạo đường kẻ eyeliner phù hợp nhất. Từ cat-eye sắc sảo đến puppy-eye ngọt ngào.</p>
      <div class="khanhbeauty-showcase__skill-tags">
        <span class="khanhbeauty-showcase__skill-tag">Cat Eye</span>
        <span class="khanhbeauty-showcase__skill-tag">Puppy Eye</span>
        <span class="khanhbeauty-showcase__skill-tag">Smoky Eye</span>
        <span class="khanhbeauty-showcase__skill-tag">Cut Crease</span>
      </div>
    </div>
  </div>

  <div class="khanhbeauty-showcase__item">
    <div class="khanhbeauty-showcase__image reveal-right">
      <img src="/images/clients/service-event.png" alt="Kỹ năng đánh son" style="aspect-ratio: 6/5; object-fit: cover;">
      <div class="khanhbeauty-showcase__image-tag">✦ Makeup Sự Kiện</div>
    </div>
    <div class="khanhbeauty-showcase__content reveal-left">
      <div class="khanhbeauty-showcase__step-num">02</div>
      <h3>Đánh Son — Nụ cười thêm rạng rỡ</h3>
      <p>Son không chỉ là tô màu lên môi. Mình sẽ chọn tông son phù hợp tông da, bối cảnh, trang phục. Kỹ thuật ombre lips, gradient lips hay full lips — tất cả đều được thực hiện tỉ mỉ từng lớp.</p>
      <div class="khanhbeauty-showcase__skill-tags">
        <span class="khanhbeauty-showcase__skill-tag">Ombre Lips</span>
        <span class="khanhbeauty-showcase__skill-tag">Gradient Lips</span>
        <span class="khanhbeauty-showcase__skill-tag">Full Lips</span>
        <span class="khanhbeauty-showcase__skill-tag">Overlining</span>
      </div>
    </div>
  </div>

  <div class="khanhbeauty-showcase__item">
    <div class="khanhbeauty-showcase__image reveal-left">
      <img src="/images/clients/service-class.png" alt="Kỹ năng contour" style="aspect-ratio: 6/5; object-fit: cover;">
      <div class="khanhbeauty-showcase__image-tag">✦ Đào tạo Makeup</div>
    </div>
    <div class="khanhbeauty-showcase__content reveal-right">
      <div class="khanhbeauty-showcase__step-num">03</div>
      <h3>Contour & Highlight — Gương mặt 3D tự nhiên</h3>
      <p>Contour đúng cách không phải để "fake" mà để tôn vinh đường nét sẵn có. Mình sử dụng kỹ thuật blending chuyên sâu, kết hợp highlight tinh tế để gương mặt bạn sáng bừng dưới mọi ánh sáng.</p>
      <div class="khanhbeauty-showcase__skill-tags">
        <span class="khanhbeauty-showcase__skill-tag">Soft Contour</span>
        <span class="khanhbeauty-showcase__skill-tag">Baking</span>
        <span class="khanhbeauty-showcase__skill-tag">Strobing</span>
        <span class="khanhbeauty-showcase__skill-tag">Glass Skin</span>
      </div>
    </div>
  </div>
</section>

<!-- ═══ SECTION 5: BEFORE / AFTER ═══ -->
<section class="khanhbeauty-ba" id="beforeafter">
  <div class="khanhbeauty-ba__header reveal">
    <span class="section-label">Trước & Sau</span>
    <h2>Kéo thanh trượt để cảm nhận sự khác biệt</h2>
  </div>

  <div class="khanhbeauty-ba__grid stagger-children">
    @foreach($latestPortfolios as $portfolio)
    <div class="reveal">
      <div class="khanhbeauty-ba__slider" data-ba-slider>
        <img class="khanhbeauty-ba__img khanhbeauty-ba__img--before" src="{{ $portfolio->beforeImage?->file_url ?? '/images/no-image.webp' }}" alt="Before" style="object-fit: cover;">
        <img class="khanhbeauty-ba__img khanhbeauty-ba__img--after" src="{{ $portfolio->afterImage?->file_url ?? '/images/no-image.webp' }}" alt="After" style="object-fit: cover;">
        <div class="khanhbeauty-ba__divider"></div>
        <span class="khanhbeauty-ba__label khanhbeauty-ba__label--before">Trước</span>
        <span class="khanhbeauty-ba__label khanhbeauty-ba__label--after">Sau</span>
      </div>
      <p class="khanhbeauty-ba__slider-caption">{{ $portfolio->title }}</p>
    </div>
    @endforeach
  </div>
</section>

<!-- ═══ SECTION 6: GALLERY ═══ -->
<section class="khanhbeauty-gallery" id="gallery">
  <div class="khanhbeauty-gallery__header reveal">
    <span class="section-label">Portfolio</span>
    <h2>Bộ sưu tập các tác phẩm của mình</h2>
  </div>

  <div class="khanhbeauty-gallery__filters reveal">
    <button class="khanhbeauty-gallery__filter active" data-filter="all">Tất cả</button>
    <button class="khanhbeauty-gallery__filter" data-filter="bride">Cô dâu</button>
    <button class="khanhbeauty-gallery__filter" data-filter="event">Sự kiện</button>
    <button class="khanhbeauty-gallery__filter" data-filter="daily">Thường ngày</button>
    <button class="khanhbeauty-gallery__filter" data-filter="photo">Chụp ảnh</button>
  </div>

  <div class="khanhbeauty-gallery__masonry stagger-children" id="galleryGrid">
    @foreach($latestPortfolios as $portfolio)
    <div class="khanhbeauty-gallery__item reveal" data-category="{{ $portfolio->category }}">
      <img src="{{ $portfolio->after_image }}" alt="{{ $portfolio->title }}" style="width: 100%; height: 100%; object-fit: cover;">
      <div class="khanhbeauty-gallery__item-overlay">
        <div class="khanhbeauty-gallery__item-info">
          <h4>{{ $portfolio->title }}</h4>
          <span>{{ ucfirst($portfolio->category) }}</span>
        </div>
      </div>
    </div>
    @endforeach
    {{-- Thêm một vài ảnh tĩnh nghệ thuật để làm phong phú gallery --}}
    <div class="khanhbeauty-gallery__item reveal" data-category="event">
      <img src="/images/clients/service-event.png" alt="Event Look" style="width: 100%; height: 100%; object-fit: cover;">
      <div class="khanhbeauty-gallery__item-overlay">
        <div class="khanhbeauty-gallery__item-info"><h4>Lookbook 2026</h4><span>Sự kiện</span></div>
      </div>
    </div>
    <div class="khanhbeauty-gallery__item reveal" data-category="daily">
      <img src="/images/clients/portfolio-1.png" alt="Daily Look" style="width: 100%; height: 100%; object-fit: cover;">
      <div class="khanhbeauty-gallery__item-overlay">
        <div class="khanhbeauty-gallery__item-info"><h4>Natural Beauty</h4><span>Thường ngày</span></div>
      </div>
    </div>
  </div>
</section>

<!-- ═══ SECTION 7: TESTIMONIALS ═══ -->
<section class="khanhbeauty-testimonials" id="testimonials">
  <div class="khanhbeauty-testimonials__header reveal">
    <span class="section-label">Khách hàng nói gì</span>
    <h2>Những lời yêu thương mình nhận được</h2>
  </div>

  <div class="khanhbeauty-testimonials__track reveal">
    <div class="khanhbeauty-testimonial-card">
      <div class="khanhbeauty-testimonial-card__stars">★★★★★</div>
      <p class="khanhbeauty-testimonial-card__text">"Khánh makeup cho mình ngày cưới, ai cũng khen đẹp tự nhiên mà vẫn rạng rỡ. Quan trọng nhất là lớp makeup trụ được cả ngày dài, không bị chảy hay xuống tone!"</p>
      <div class="khanhbeauty-testimonial-card__author">
        <div class="khanhbeauty-testimonial-card__avatar">MA</div>
        <div>
          <div class="khanhbeauty-testimonial-card__name">Minh Anh</div>
          <div class="khanhbeauty-testimonial-card__role">Cô dâu — Hà Nội</div>
        </div>
      </div>
    </div>

    <div class="khanhbeauty-testimonial-card">
      <div class="khanhbeauty-testimonial-card__stars">★★★★★</div>
      <p class="khanhbeauty-testimonial-card__text">"Mình book Khánh cho buổi chụp kỷ yếu cả lớp. Bạn ấy makeup nhanh, đẹp mà giá cả hợp lý lắm. Cả lớp ai cũng xinh, ảnh lên lung linh luôn!"</p>
      <div class="khanhbeauty-testimonial-card__author">
        <div class="khanhbeauty-testimonial-card__avatar">TL</div>
        <div>
          <div class="khanhbeauty-testimonial-card__name">Thuỳ Linh</div>
          <div class="khanhbeauty-testimonial-card__role">Sinh viên — Đà Nẵng</div>
        </div>
      </div>
    </div>

    <div class="khanhbeauty-testimonial-card">
      <div class="khanhbeauty-testimonial-card__stars">★★★★★</div>
      <p class="khanhbeauty-testimonial-card__text">"Lần đầu book makeup online mà gặp được Khánh là may mắn. Bạn ấy rất tận tâm, lắng nghe mình muốn gì và tư vấn rất nhiệt tình. Chắc chắn sẽ quay lại!"</p>
      <div class="khanhbeauty-testimonial-card__author">
        <div class="khanhbeauty-testimonial-card__avatar">H</div>
        <div>
          <div class="khanhbeauty-testimonial-card__name">Chị Hương</div>
          <div class="khanhbeauty-testimonial-card__role">Khách book online — Sài Gòn</div>
        </div>
      </div>
    </div>

    <div class="khanhbeauty-testimonial-card">
      <div class="khanhbeauty-testimonial-card__stars">★★★★★</div>
      <p class="khanhbeauty-testimonial-card__text">"Mình học khoá makeup cá nhân với Khánh. Giờ mình tự tin trang điểm đi làm mỗi ngày rồi. Cách dạy dễ hiểu, thực hành nhiều, rất phù hợp cho người mới."</p>
      <div class="khanhbeauty-testimonial-card__author">
        <div class="khanhbeauty-testimonial-card__avatar">LN</div>
        <div>
          <div class="khanhbeauty-testimonial-card__name">Lan Ngọc</div>
          <div class="khanhbeauty-testimonial-card__role">Học viên — Hà Nội</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══ SECTION 8: NUMBERS ═══ -->
<section class="khanhbeauty-numbers">
  <div class="khanhbeauty-numbers__grid stagger-children">
    <div class="reveal">
      <div class="khanhbeauty-numbers__item-num" data-count="500">0+</div>
      <div class="khanhbeauty-numbers__item-label">Khách hàng hài lòng</div>
    </div>
    <div class="reveal">
      <div class="khanhbeauty-numbers__item-num" data-count="200">0+</div>
      <div class="khanhbeauty-numbers__item-label">Cô dâu xinh đẹp</div>
    </div>
    <div class="reveal">
      <div class="khanhbeauty-numbers__item-num" data-count="5">0+</div>
      <div class="khanhbeauty-numbers__item-label">Năm kinh nghiệm</div>
    </div>
    <div class="reveal">
      <div class="khanhbeauty-numbers__item-num" data-count="50">0+</div>
      <div class="khanhbeauty-numbers__item-label">Học viên đào tạo</div>
    </div>
  </div>
</section>

<!-- ═══ SECTION 9: BOOKING WIZARD ═══ -->
<section class="khanhbeauty-booking" id="booking">
  <div class="khanhbeauty-booking__inner reveal-scale">
      <h2 style="font-family: var(--font-display); font-size: 36px; margin-bottom: 20px;">Lên Lịch Hẹn <em>Trực Tuyến</em></h2>
      <p style="margin-bottom: 30px;">Chọn dịch vụ, tìm chuyên gia ưa thích và giữ chỗ – tất cả trong một luồng mượt mà.</p>
      
      <div class="kb-wizard">
          @include('clients.partials.booking-form')
      </div>
  </div>
</section>
@endsection

@push('scripts')
    <script src="{{ asset('js/clients/home.js') }}"></script>
    <script>
        // Inline code to override empty states in JS if variants missing
        document.getElementById('service_id').addEventListener('change', function() {
            if(!this.value) {
                document.getElementById('variantWrap').style.display = 'none';
            } else {
                document.getElementById('variantWrap').style.display = 'block';
            }
        });
    </script>
@endpush
