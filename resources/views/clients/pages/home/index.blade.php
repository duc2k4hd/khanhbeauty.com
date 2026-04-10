@extends('clients.layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/clients/home.css') }}">
@endpush

@section('content')
<!-- ═══ SECTION 1: HERO ═══ -->
<section class="khanhbeauty-hero" id="hero">
  <div class="khanhbeauty-hero__decor-circle"></div>
  <div class="khanhbeauty-hero__decor-circle"></div>
  <div class="khanhbeauty-hero__decor-circle"></div>

  <svg class="khanhbeauty-hero__brush" viewBox="0 0 800 200" fill="none">
    <path d="M50 100C150 30 250 170 400 100C550 30 650 170 750 100" stroke="var(--pink-300)" stroke-width="80" stroke-linecap="round" opacity="0.15"/>
  </svg>

  <div class="khanhbeauty-hero__content">
    <div class="khanhbeauty-hero__badge">{{ $hero['badge'] }}</div>
    <h1 class="khanhbeauty-hero__title">{!! $hero['title'] !!}</h1>
    <p class="khanhbeauty-hero__subtitle">{{ $hero['subtitle'] }}</p>
    <div class="khanhbeauty-hero__cta-group">
      <button class="khanhbeauty-hero__cta khanhbeauty-hero__cta--primary" onclick="document.getElementById('booking').scrollIntoView({behavior:'smooth'})">{{ $hero['cta_primary'] }}</button>
      <button class="khanhbeauty-hero__cta khanhbeauty-hero__cta--secondary" onclick="document.getElementById('gallery').scrollIntoView({behavior:'smooth'})">{{ $hero['cta_secondary'] }}</button>
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
        <img src="{{ $about['image'] }}" alt="Kh&#225;nh Beauty" style="width: 100%; height: 100%; object-fit: cover;">
      </div>
      <div class="khanhbeauty-about__image-frame"></div>
      @php
        $expStat = collect($about['stats'])->first(
            fn ($stat) => str_contains(\Illuminate\Support\Str::ascii(mb_strtolower($stat['label'])), 'kinh nghiem')
                || str_contains(\Illuminate\Support\Str::ascii(mb_strtolower($stat['label'])), 'nam')
        );
      @endphp
      <div class="khanhbeauty-about__exp-badge reveal">
        <strong>{{ $expStat['value'] ?? '5' }}+</strong>
        <span>{{ $expStat['label'] ?? "N\u{0103}m kinh nghi\u{1EC7}m" }}</span>
      </div>
    </div>

    <div class="khanhbeauty-about__text reveal-right">
      <span class="section-label">{{ $about['label'] }}</span>
      <h2>{!! $about['title'] !!}</h2>
      <p>{{ $about['desc_1'] }}</p>
      <p>{{ $about['desc_2'] }}</p>

      <div class="khanhbeauty-about__stats stagger-children">
        @foreach($about['stats'] as $stat)
        <div class="khanhbeauty-about__stat-item reveal">
          <div class="khanhbeauty-about__stat-num" data-count="{{ $stat['value'] }}">0</div>
          <div class="khanhbeauty-about__stat-label">{{ $stat['label'] }}</div>
        </div>
        @endforeach
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
          <img src="{{ data_get($service, 'featuredImage.file_url', '/images/no-image.webp') }}"
               onerror="this.src='/images/no-image.webp'"
               alt="{{ data_get($service, 'featuredImage.alt_text', $service->name) }}">
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
    <span class="section-label">{{ $showcase['label'] }}</span>
    <h2>{{ $showcase['title'] }}</h2>
  </div>

  @foreach($showcase['items'] as $index => $item)
  <div class="khanhbeauty-showcase__item">
    <div class="khanhbeauty-showcase__image {{ $index % 2 === 0 ? 'reveal-left' : 'reveal-right' }}">
      <img src="{{ $item['image_url'] }}" alt="{{ $item['title'] }}" style="aspect-ratio: 6/5; object-fit: cover;">
      <div class="khanhbeauty-showcase__image-tag">{{ $item['tag'] }}</div>
    </div>
    <div class="khanhbeauty-showcase__content {{ $index % 2 === 0 ? 'reveal-right' : 'reveal-left' }}">
      <div class="khanhbeauty-showcase__step-num">{{ $item['step'] }}</div>
      <h3>{{ $item['title'] }}</h3>
      <p>{{ $item['description'] }}</p>
      <div class="khanhbeauty-showcase__skill-tags">
        @foreach($item['skills'] as $skill)
        <span class="khanhbeauty-showcase__skill-tag">{{ $skill }}</span>
        @endforeach
      </div>
    </div>
  </div>
  @endforeach
</section>

<!-- ═══ SECTION 5: BEFORE / AFTER ═══ -->
<section class="khanhbeauty-ba" id="beforeafter">
  <div class="khanhbeauty-ba__header reveal">
    <span class="section-label">Tr&#432;&#7899;c &amp; Sau</span>
    <h2>K&#233;o thanh tr&#432;&#7907;t &#273;&#7875; c&#7843;m nh&#7853;n s&#7921; kh&#225;c bi&#7879;t</h2>
  </div>

  <div class="khanhbeauty-ba__grid stagger-children">
    @foreach($latestPortfolios as $portfolio)
    @php
      $beforeUrl = data_get($portfolio, 'beforeImage.file_url');
      $afterUrl = data_get($portfolio, 'afterImage.file_url');
      $hasBefore = filled($beforeUrl);
      $hasAfter = filled($afterUrl);
      $singleUrl = $afterUrl ?: $beforeUrl;
      $singleLabel = $hasAfter ? 'Sau' : "Tr\u{01B0}\u{1EDB}c";
      $singleLabelClass = $hasAfter ? 'khanhbeauty-ba__label--after' : 'khanhbeauty-ba__label--before';
    @endphp
    <div class="reveal">
      <div class="khanhbeauty-ba__slider{{ $hasBefore && $hasAfter ? '' : ' khanhbeauty-ba__slider--single' }}" @if($hasBefore && $hasAfter) data-ba-slider @endif>
        @if($hasBefore && $hasAfter)
        <img class="khanhbeauty-ba__img khanhbeauty-ba__img--before" src="{{ $beforeUrl }}" alt="Before" style="object-fit: cover;">
        <img class="khanhbeauty-ba__img khanhbeauty-ba__img--after" src="{{ $afterUrl }}" alt="After" style="object-fit: cover;">
        <div class="khanhbeauty-ba__divider"></div>
        <span class="khanhbeauty-ba__label khanhbeauty-ba__label--before">Tr&#432;&#7899;c</span>
        <span class="khanhbeauty-ba__label khanhbeauty-ba__label--after">Sau</span>
        @elseif($singleUrl)
        <img class="khanhbeauty-ba__img khanhbeauty-ba__img--single" src="{{ $singleUrl }}" alt="{{ $portfolio->title }}" style="object-fit: cover;">
        <span class="khanhbeauty-ba__label {{ $singleLabelClass }}">{{ $singleLabel }}</span>
        @else
        <div class="khanhbeauty-ba__empty">
          <span>Ch&#432;a c&#243; &#7843;nh</span>
        </div>
        @endif
      </div>
      <p class="khanhbeauty-ba__slider-caption">{{ $portfolio->title }}</p>
    </div>
    @endforeach
  </div>
</section>

<!-- ═══ SECTION 6: GALLERY ═══ -->
@if($galleryImages->isNotEmpty())
<section class="khanhbeauty-gallery" id="gallery">
  <div class="khanhbeauty-gallery__header reveal">
    <span class="section-label">Portfolio</span>
    <h2>Bộ sưu tập các tác phẩm của mình</h2>
  </div>

  <div class="khanhbeauty-gallery__filters reveal">
    @foreach($galleryTabs as $key => $label)
      <button class="khanhbeauty-gallery__filter {{ $loop->first ? 'active' : '' }}" data-filter="{{ $key }}">{{ $label }}</button>
    @endforeach
  </div>

  <div class="khanhbeauty-gallery__masonry stagger-children" id="galleryGrid">
    @foreach($galleryImages as $img)
    <div class="khanhbeauty-gallery__item reveal" data-category="{{ $img->category }}">
      <img src="{{ $img->url }}" alt="{{ $img->title }}" style="width: 100%; height: 100%; object-fit: cover;" loading="lazy">
      <div class="khanhbeauty-gallery__item-overlay">
        <div class="khanhbeauty-gallery__item-info">
          <h4>{{ $img->title }}</h4>
          <span>{{ $galleryTabs[$img->category] ?? ucfirst($img->category) }}</span>
        </div>
      </div>
    </div>
    @endforeach
  </div>
</section>
@endif

<!-- ═══ SECTION 7: TESTIMONIALS ═══ -->
<section class="khanhbeauty-testimonials" id="testimonials">
  <div class="khanhbeauty-testimonials__header reveal">
    <span class="section-label">{{ $testimonialsSection['label'] }}</span>
    <h2>{{ $testimonialsSection['title'] }}</h2>
  </div>

  <div class="khanhbeauty-testimonials__track reveal">
    @foreach($testimonialsSection['items'] as $item)
    <div class="khanhbeauty-testimonial-card">
      <div class="khanhbeauty-testimonial-card__stars">{!! str_repeat("\u{2605}", $item['stars'] ?? 5) !!}{!! str_repeat("\u{2606}", 5 - ($item['stars'] ?? 5)) !!}</div>
      <p class="khanhbeauty-testimonial-card__text">&quot;{{ $item['text'] }}&quot;</p>
      <div class="khanhbeauty-testimonial-card__author">
        <div class="khanhbeauty-testimonial-card__avatar">{{ $item['avatar'] }}</div>
        <div>
          <div class="khanhbeauty-testimonial-card__name">{{ $item['name'] }}</div>
          <div class="khanhbeauty-testimonial-card__role">{{ $item['role'] }}</div>
        </div>
      </div>
    </div>
    @endforeach
  </div>
</section>

<!-- ═══ SECTION 8: NUMBERS ═══ -->
<section class="khanhbeauty-numbers">
  <div class="khanhbeauty-numbers__grid stagger-children">
    @foreach($numbersStats as $item)
    <div class="reveal">
      <div class="khanhbeauty-numbers__item-num" data-count="{{ $item['value'] }}">0+</div>
      <div class="khanhbeauty-numbers__item-label">{{ $item['label'] }}</div>
    </div>
    @endforeach
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
