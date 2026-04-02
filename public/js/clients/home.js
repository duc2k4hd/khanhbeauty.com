/* ═══════════════════════════════════════════
   KHANH BEAUTY - HOME-SPECIFIC JS
   ═══════════════════════════════════════════ */

(function() {
  'use strict';

  // ─── Scroll Reveal (IntersectionObserver) ───
  const revealEls = document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale');
  if (revealEls.length > 0) {
    const revealObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
        }
      });
    }, { threshold: 0.15, rootMargin: '0px 0px -50px 0px' });
    revealEls.forEach(el => revealObserver.observe(el));
  }

  // ─── Counter Animation ───
  const countEls = document.querySelectorAll('[data-count]');
  if (countEls.length > 0) {
    const countObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const el = entry.target;
          const target = parseInt(el.dataset.count);
          let current = 0;
          const duration = 1500; // 1.5s
          const steps = 60;
          const increment = target / steps;
          const interval = duration / steps;

          const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
              el.textContent = target + '+';
              clearInterval(timer);
            } else {
              el.textContent = Math.floor(current) + '+';
            }
          }, interval);
          countObserver.unobserve(el);
        }
      });
    }, { threshold: 0.5 });
    countEls.forEach(el => countObserver.observe(el));
  }

  // ─── Before/After Sliders ───
  const baSliders = document.querySelectorAll('[data-ba-slider]');
  baSliders.forEach(slider => {
    let isDragging = false;
    const afterImg = slider.querySelector('.khanhbeauty-ba__img--after');
    const divider = slider.querySelector('.khanhbeauty-ba__divider');

    // Chống hành vi kéo ảnh mặc định của trình duyệt gây kẹt isDragging
    slider.querySelectorAll('img').forEach(img => {
        img.addEventListener('dragstart', e => e.preventDefault());
    });

    function updateSlider(x) {
      const rect = slider.getBoundingClientRect();
      let percent = ((x - rect.left) / rect.width) * 100;
      percent = Math.max(0, Math.min(100, percent));
      if (afterImg) afterImg.style.clipPath = `inset(0 ${100 - percent}% 0 0)`;
      if (divider) divider.style.left = percent + '%';
    }

    slider.addEventListener('mousedown', (e) => { 
        isDragging = true; 
        updateSlider(e.clientX); 
    });
    
    window.addEventListener('mousemove', (e) => { 
        if (isDragging) updateSlider(e.clientX); 
    });
    window.addEventListener('mouseup', () => isDragging = false);

    slider.addEventListener('touchstart', (e) => { 
        isDragging = true; 
        if(e.touches[0]) updateSlider(e.touches[0].clientX); 
    }, {passive: true});
    window.addEventListener('touchmove', (e) => { 
        if (isDragging && e.touches[0]) updateSlider(e.touches[0].clientX); 
    }, {passive: true});
    window.addEventListener('touchend', () => isDragging = false);
  });

  // ─── Gallery Filter Click Effect ───
  const filters = document.querySelectorAll('.khanhbeauty-gallery__filter');
  filters.forEach(btn => {
    btn.addEventListener('click', () => {
      filters.forEach(f => f.classList.remove('active'));
      btn.classList.add('active');
    });
  });

  // ─── Floating CTA logic ───
  const floatingCta = document.querySelector('.floating-cta');
  const bookingSection = document.getElementById('booking');
  if (floatingCta && bookingSection) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          floatingCta.classList.add('hidden');
        } else {
          floatingCta.classList.remove('hidden');
        }
      });
    }, { threshold: 0.1 });
    observer.observe(bookingSection);
  }

  // ─── Parallax on Showcase Images ───

  // ─── Booking Wizard Logic (Moved to global booking.js) ───

  // ─── PREMIUM LIGHTBOX GALLERY LOGIC ───
  const galleryItems = document.querySelectorAll('.khanhbeauty-gallery__item');
  if (galleryItems.length > 0) {
    const lightboxHTML = `
      <div class="kb-lightbox" id="kbLightbox">
        <div class="kb-lightbox__overlay" id="kbLightboxOverlay"></div>
        
        <div class="kb-lightbox__topbar">
          <div class="kb-lightbox__counter"><span id="kbLightboxCurrent">1</span> / <span id="kbLightboxTotal">0</span></div>
          <div class="kb-lightbox__actions">
            <!-- Nút Close bằng SVG thanh thoát -->
            <button class="kb-lightbox__action" id="kbLightboxClose">
              <svg viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
          </div>
        </div>

        <div class="kb-lightbox__main" id="kbLightboxMain">
          <img class="kb-lightbox__img active" id="kbLightboxImg" src="" alt="Gallery Image">
        </div>
        
        <button class="kb-lightbox__nav prev" id="kbLightboxPrev"><svg viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg></button>
        <button class="kb-lightbox__nav next" id="kbLightboxNext"><svg viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg></button>

        <div class="kb-lightbox__thumbs" id="kbLightboxThumbs"></div>
      </div>
    `;
    document.body.insertAdjacentHTML('beforeend', lightboxHTML);

    const lightbox = document.getElementById('kbLightbox');
    const lightboxImg = document.getElementById('kbLightboxImg');
    const thumbsContainer = document.getElementById('kbLightboxThumbs');
    const txtCurrent = document.getElementById('kbLightboxCurrent');
    const txtTotal = document.getElementById('kbLightboxTotal');
    const prevBtn = document.getElementById('kbLightboxPrev');
    const nextBtn = document.getElementById('kbLightboxNext');
    const closeBtn = document.getElementById('kbLightboxClose');
    const overlay = document.getElementById('kbLightboxOverlay');

    let currentImages = [];
    let currentIndex = 0;

    // Lấy thông tin & Render Thumbnails
    let thumbsHTML = '';
    galleryItems.forEach((item, index) => {
      const img = item.querySelector('img');
      if (img) {
        currentImages.push(img.src);
        thumbsHTML += `<div class="kb-lightbox__thumb" data-index="${index}"><img src="${img.src}"></div>`;
        item.style.cursor = 'zoom-in';
        item.addEventListener('click', () => openLightbox(index));
      }
    });

    thumbsContainer.innerHTML = thumbsHTML;
    const thumbElements = document.querySelectorAll('.kb-lightbox__thumb');
    txtTotal.textContent = currentImages.length;

    // Logic Đổi hình (Animation Slide)
    const updateImage = (index, direction = 'next') => {
      currentIndex = index;
      txtCurrent.textContent = index + 1;

      // Xoá highlight thumbnail cũ
      thumbElements.forEach(el => el.classList.remove('active'));
      thumbElements[index].classList.add('active');
      
      // Auto Scroll thumbnail container
      const targetThumb = thumbElements[index];
      const scrollPos = targetThumb.offsetLeft - (thumbsContainer.offsetWidth / 2) + (targetThumb.offsetWidth / 2);
      thumbsContainer.scrollTo({ left: scrollPos, behavior: 'smooth' });

      // Cập nhật ảnh chính với animation (trượt ra - trượt vào)
      lightboxImg.classList.remove('active');
      lightboxImg.classList.add(direction === 'next' ? 'slide-left' : 'slide-right');
      
      setTimeout(() => {
        lightboxImg.src = currentImages[currentIndex];
        // Reset translate state instantly without transition
        lightboxImg.style.transition = 'none';
        lightboxImg.classList.remove('slide-left', 'slide-right');
        lightboxImg.classList.add(direction === 'next' ? 'slide-right-hidden' : 'slide-left-hidden'); // Custom push
        
        // Force reflow
        void lightboxImg.offsetWidth;
        
        // Restore transition and slide in
        lightboxImg.style.transition = '';
        lightboxImg.classList.remove('slide-right-hidden', 'slide-left-hidden');
        lightboxImg.classList.add('active');
      }, 150); // Thời gian chờ bằng một nhịp CSS transition
    };

    // Fix small trick for animation
    const styleTrick = document.createElement('style');
    styleTrick.innerHTML = `
      .kb-lightbox__img.slide-right-hidden { transform: scale(0.9) translateX(50px) !important; opacity: 0 !important; }
      .kb-lightbox__img.slide-left-hidden { transform: scale(0.9) translateX(-50px) !important; opacity: 0 !important; }
      .kb-lightbox__img.slide-right { transform: scale(0.9) translateX(30px); }
    `;
    document.head.appendChild(styleTrick);

    const openLightbox = (index) => {
      lightbox.classList.add('active');
      document.body.style.overflow = 'hidden';
      // Load current directly
      currentIndex = index;
      txtCurrent.textContent = index + 1;
      thumbElements.forEach(el => el.classList.remove('active'));
      if(thumbElements[index]) thumbElements[index].classList.add('active');
      lightboxImg.src = currentImages[index];
      setTimeout(() => lightboxImg.classList.add('active'), 50);
    };

    const closeLightbox = () => {
      lightbox.classList.remove('active');
      lightboxImg.classList.remove('active');
      document.body.style.overflow = '';
      setTimeout(() => { lightboxImg.src = ''; }, 400);
    };

    // Events Điều Hướng
    nextBtn.addEventListener('click', (e) => { e.stopPropagation(); updateImage(currentIndex === currentImages.length - 1 ? 0 : currentIndex + 1, 'next'); });
    prevBtn.addEventListener('click', (e) => { e.stopPropagation(); updateImage(currentIndex === 0 ? currentImages.length - 1 : currentIndex - 1, 'prev'); });
    closeBtn.addEventListener('click', closeLightbox);
    overlay.addEventListener('click', closeLightbox);

    thumbElements.forEach((thumb, index) => {
      thumb.addEventListener('click', () => {
        if(index > currentIndex) updateImage(index, 'next');
        else if(index < currentIndex) updateImage(index, 'prev');
      });
    });

    // Bàn phím
    document.addEventListener('keydown', (e) => {
      if (!lightbox.classList.contains('active')) return;
      if (e.key === 'Escape') closeLightbox();
      if (e.key === 'ArrowRight') updateImage(currentIndex === currentImages.length - 1 ? 0 : currentIndex + 1, 'next');
      if (e.key === 'ArrowLeft') updateImage(currentIndex === 0 ? currentImages.length - 1 : currentIndex - 1, 'prev');
    });
  }

})();
