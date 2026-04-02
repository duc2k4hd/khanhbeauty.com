<!-- ═══════════════════════════════════════════
     BOOKING MODAL (PREMIUM GLASSMORPHISM)
     ═══════════════════════════════════════════ -->
<div class="kb-modal" id="bookingModal">
    <div class="kb-modal__overlay" id="bookingModalOverlay"></div>
    <div class="kb-modal__container reveal-scale">
        <button class="kb-modal__close" id="closeBookingModal" aria-label="Close modal">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6L6 18M6 6l12 12"/></svg>
        </button>
        
        <div class="kb-modal__header">
            <h2 class="font-heading">Lên Lịch Hẹn <em>Trực Tuyến</em></h2>
            <p>Trải nghiệm vẻ đẹp hoàn hảo cùng Khánh Beauty</p>
        </div>

        <div class="kb-modal__body">
            @include('clients.partials.booking-form')
        </div>
    </div>
</div>
