/* ═══════════════════════════════════════════
   KHANH BEAUTY - MAIN JS (SHARED)
   ═══════════════════════════════════════════ */

(function() {
  'use strict';

  // ─── Navigation Scroll Effect + Scroll-to-Top ───
  const nav = document.getElementById('mainNav');
  const floatingCta = document.getElementById('floatingCta');
  const scrollTopBtn = document.getElementById('scrollTopBtn');

  let lastScrollY = 0;
  let isScrollingUp = false;
  
  const progressBar = document.getElementById('readingProgressBar');
  const sections = document.querySelectorAll('section[id]');
  const navLinks = document.querySelectorAll('.khanhbeauty-nav__menu a');

  if (nav || floatingCta || scrollTopBtn || progressBar || sections.length > 0) {
    window.addEventListener('scroll', () => {
      const currentY = window.scrollY;
      isScrollingUp = currentY < lastScrollY;

      // Nav effect
      if (nav) nav.classList.toggle('scrolled', currentY > 80);
      
      // Floating CTA
      if (floatingCta) floatingCta.classList.toggle('show', currentY > 600);

      // Scroll to Top
      if (scrollTopBtn) {
        const shouldShow = isScrollingUp && currentY > 300;
        scrollTopBtn.classList.toggle('visible', shouldShow);
      }

      // Reading Progress Bar
      if (progressBar) {
        const docHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const scrolled = docHeight > 0 ? (currentY / docHeight) * 100 : 0;
        progressBar.style.width = scrolled + '%';
      }

      // Scroll Spy
      if (sections.length > 0 && navLinks.length > 0) {
        let currentSectionId = '';
        sections.forEach(section => {
          const sectionTop = section.offsetTop;
          if (currentY >= (sectionTop - 200)) {
            currentSectionId = section.getAttribute('id');
          }
        });

        if (currentY < 100) currentSectionId = 'hero'; // Fallback for very top

        navLinks.forEach(a => {
          a.classList.remove('active');
          const href = a.getAttribute('href');
          if (href && currentSectionId !== '' && href.endsWith('#' + currentSectionId)) {
            a.classList.add('active');
          }
        });
      }

      lastScrollY = currentY;
    }, { passive: true });
  }

  // Click → cuộn mượt về đầu trang
  if (scrollTopBtn) {
    scrollTopBtn.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  // ─── Mega Menu Logic ───
  const openMenuBtnDesktop = document.getElementById('openMegaMenuDesktop');
  const openMenuBtnMobile = document.getElementById('openMegaMenuMobile');
  const closeMenuBtn = document.getElementById('closeMegaMenu');
  const megaMenu = document.getElementById('megaMenu');
  const overlay = document.getElementById('megaMenuOverlay');

  let _savedScrollY = 0;

  const lockBodyScroll = () => {
    _savedScrollY = window.scrollY;
    document.body.style.overflow   = 'hidden';
    document.body.style.position   = 'fixed';
    document.body.style.top        = `-${_savedScrollY}px`;
    document.body.style.width      = '100%';
  };

  const unlockBodyScroll = () => {
    document.body.style.overflow = '';
    document.body.style.position = '';
    document.body.style.top      = '';
    document.body.style.width    = '';
    window.scrollTo(0, _savedScrollY);
  };

  const toggleMenu = (isActive) => {
    if (!megaMenu) return;
    megaMenu.classList.toggle('active', isActive);
    if (isActive) {
      lockBodyScroll();
    } else {
      unlockBodyScroll();
      megaMenu.classList.remove('show-level2');
    }
  };

  if (openMenuBtnDesktop) openMenuBtnDesktop.addEventListener('click', () => toggleMenu(true));
  if (openMenuBtnMobile) openMenuBtnMobile.addEventListener('click', () => toggleMenu(true));
  if (closeMenuBtn) closeMenuBtn.addEventListener('click', () => toggleMenu(false));
  if (overlay) overlay.addEventListener('click', () => toggleMenu(false));

  // ─── Global Booking Trigger (Scroll or Modal) ───
  const bookingTriggers = document.querySelectorAll('.js-booking-trigger');
  const bookingModal = document.getElementById('bookingModal');
  const closeBookingModal = document.getElementById('closeBookingModal');
  const bookingModalOverlay = document.getElementById('bookingModalOverlay');

  const toggleBookingModal = (show) => {
    if (!bookingModal) return;
    if (show) {
      bookingModal.classList.add('active');
      document.body.style.overflow = 'hidden';
    } else {
      bookingModal.classList.remove('active');
      document.body.style.overflow = '';
    }
  };

  bookingTriggers.forEach(btn => {
    btn.addEventListener('click', (e) => {
      const homeBookingSection = document.getElementById('booking');
      if (homeBookingSection) {
        // Trang chủ: Cuộn mượt
        homeBookingSection.scrollIntoView({ behavior: 'smooth' });
      } else {
        // Trang khác: Bật Modal
        toggleBookingModal(true);
      }
    });
  });

  if (closeBookingModal) closeBookingModal.addEventListener('click', () => toggleBookingModal(false));
  if (bookingModalOverlay) bookingModalOverlay.addEventListener('click', () => toggleBookingModal(false));

  // ─── Mega Menu Revision 2 Logic ───
  const subMenuDataYody = {
    'son-moi': {
      title: 'SON MÔI',
      content: `
        <div class="kb-accordion-item-yody active">
          <div class="kb-accordion-header-yody"><span>TẤT CẢ SON MÔI</span><svg class="kb-row-chevron-yody" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg></div>
          <div class="kb-accordion-content-yody" style="display:block">
            <a href="#" class="kb-link-l3-yody">Bán chạy nhất</a>
            <a href="#" class="kb-link-l3-yody">Sản phẩm mới</a>
          </div>
        </div>
        <div class="kb-accordion-item-yody">
          <div class="kb-accordion-header-yody"><span>SON KEM LÌ</span><svg class="kb-row-chevron-yody" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg></div>
          <div class="kb-accordion-content-yody">
            <a href="#" class="kb-link-l3-yody">Velvet Lip Tint</a>
            <a href="#" class="kb-link-l3-yody">Matte Liquid</a>
          </div>
        </div>
        <div class="kb-accordion-item-yody">
          <div class="kb-accordion-header-yody"><span>SON THỎI</span><svg class="kb-row-chevron-yody" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg></div>
          <div class="kb-accordion-content-yody">
            <a href="#" class="kb-link-l3-yody">Satin Lipstick</a>
            <a href="#" class="kb-link-l3-yody">Matte Stick</a>
          </div>
        </div>
      `
    },
    'nails': {
      title: 'NAILS & MI',
      content: `
        <div class="kb-accordion-item-yody active">
          <div class="kb-accordion-header-yody"><span>NAIL BOX</span><svg class="kb-row-chevron-yody" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"/></svg></div>
          <div class="kb-accordion-content-yody" style="display:block">
            <a href="#" class="kb-link-l3-yody">Thiết kế cao cấp</a>
            <a href="#" class="kb-link-l3-yody">Nail trơn</a>
          </div>
        </div>
      `
    }
  };

  const listRows = document.querySelectorAll('.kb-list-row-yody');
  const btnBackYody = document.getElementById('btnBackToL1');
  const l2ContentYody = document.getElementById('l2ContentYody');
  const l2TitleYody = document.getElementById('currentCategoryTitleYody');
  const menuContainer = document.querySelector('.kb-mega-menu__container');

  // Prevent clicks inside container from closing menu (bubbling to overlay)
  if (menuContainer) {
    menuContainer.addEventListener('click', (e) => e.stopPropagation());
  }

  if (listRows && listRows.length > 0) {
    // Fill mặc định nội dung của mục đầu tiên vào Level 2 khi load trang (dành cho Desktop)
    const firstTarget = listRows[0].getAttribute('data-target');
    if (firstTarget && subMenuDataYody[firstTarget]) {
      l2TitleYody.innerText = subMenuDataYody[firstTarget].title;
      l2ContentYody.innerHTML = subMenuDataYody[firstTarget].content;
      attachAccordionEventsYody();
    }

    listRows.forEach(row => {
      // Dành cho Mobile: Click
      row.addEventListener('click', function(e) {
        if (window.innerWidth <= 1024) {
          e.preventDefault();
          e.stopPropagation();
          const target = this.getAttribute('data-target');
          if (subMenuDataYody[target]) {
            l2TitleYody.innerText = subMenuDataYody[target].title;
            l2ContentYody.innerHTML = subMenuDataYody[target].content;
            megaMenu.classList.add('show-level2');
            attachAccordionEventsYody();
          }
        }
      });

      // Dành cho Desktop: Hover
      row.addEventListener('mouseenter', function() {
        if (window.innerWidth > 1024) {
          const target = this.getAttribute('data-target');
          if (subMenuDataYody[target]) {
            l2TitleYody.innerText = subMenuDataYody[target].title;
            l2ContentYody.innerHTML = subMenuDataYody[target].content;
            attachAccordionEventsYody();
          }
        }
      });
    });
  }

  if (btnBackYody) {
    btnBackYody.addEventListener('click', () => {
      megaMenu.classList.remove('show-level2');
    });
  }

  function attachAccordionEventsYody() {
    document.querySelectorAll('.kb-accordion-header-yody').forEach(header => {
      header.addEventListener('click', function() {
        const item = this.parentElement;
        const content = item.querySelector('.kb-accordion-content-yody');
        const isOpen = item.classList.toggle('active');
        if (content) content.style.display = isOpen ? 'block' : 'none';
        
        // Rotate chevron
        const svg = this.querySelector('svg');
        if (svg) svg.style.transform = isOpen ? 'rotate(180deg)' : 'rotate(0deg)';
      });
    });
  }

  // ─── Smooth Scroll for Anchors ───

  // ─── Smooth Scroll for Anchors ───
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
      const targetId = this.getAttribute('href');
      if (targetId === '#') return;
      
      const targetEl = document.querySelector(targetId);
      if (targetEl) {
        e.preventDefault();
        targetEl.scrollIntoView({ behavior: 'smooth' });
      }
    });
  });

  // ─── Scroll Reveal Animation ───
  const revealElements = document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale');
  const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        revealObserver.unobserve(entry.target); // Chạy 1 lần duy nhất
      }
    });
  }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

  revealElements.forEach(el => revealObserver.observe(el));

})();
