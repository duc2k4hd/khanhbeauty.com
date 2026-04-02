<!-- ═══════════════════════════════════════════
     MEGA MENU PIXEL-PERFECT (YODY STYLE REVISION 2)
     ═══════════════════════════════════════════ -->
<div class="kb-mega-menu" id="megaMenu">
    <div class="kb-mega-menu__overlay" id="megaMenuOverlay"></div>
    <div class="kb-mega-menu__container">
        
        <!-- YODY HEADER (STICKY AT TOP OF MENU) -->
        <div class="kb-mega-menu__header-yody">
            <div class="kb-mega-menu__logo-yody">
                <span class="kb-logo-main">KHÁNH</span><span class="kb-logo-sub">BEAUTY</span>
            </div>
            <div class="kb-mega-menu__actions-yody">
                <button class="kb-action-icon-yody" title="Tìm kiếm">
                    <svg viewBox="0 0 24 24"><path d="M11 19a8 8 0 100-16 8 8 0 000 16zM21 21l-4.35-4.35"/></svg>
                </button>
                <div class="kb-action-icon-yody kb-cart-icon-yody">
                    <svg viewBox="0 0 24 24"><path d="M6 21v-2a4 4 0 014-4h4a4 4 0 014 4v2M12 7a4 4 0 110-8 4 4 0 010 8z"/></svg>
                </div>
            </div>
        </div>

        <!-- SLIDER SYSTEM (FLUID 0.3s) -->
        <div class="kb-mega-menu__slider">
            
            <!-- LEVEL 1: OVERVIEW -->
            <div class="kb-mega-menu__page kb-mega-menu__page--l1 active" id="menuL1">
                <div class="kb-menu-scroll-area">
                    <!-- QUICK GRID (2 COLUMNS - YODY STYLE) -->
                    <div class="kb-quick-grid-yody">
                        <div class="kb-grid-item-yody">
                            <span class="kb-grid-icon-yody">🏷️</span>
                            <span class="kb-grid-text-yody">BST COSMETIC</span>
                        </div>
                        <div class="kb-grid-item-yody">
                            <span class="kb-grid-icon-yody">🏪</span>
                            <span class="kb-grid-text-yody">CỬA HÀNG</span>
                        </div>
                        <div class="kb-grid-item-yody">
                            <span class="kb-grid-icon-yody">📰</span>
                            <span class="kb-grid-text-yody">TIN TỨC</span>
                        </div>
                        <div class="kb-grid-item-yody">
                            <span class="kb-grid-icon-yody">🔥</span>
                            <span class="kb-grid-text-yody">ƯU ĐÃI %</span>
                        </div>
                        <div class="kb-grid-item-yody">
                            <span class="kb-grid-icon-yody">✨</span>
                            <span class="kb-grid-text-yody">MỚI VỀ</span>
                        </div>
                        <div class="kb-grid-item-yody">
                            <span class="kb-grid-icon-yody">🎁</span>
                            <span class="kb-grid-text-yody">QUÀ TẶNG</span>
                        </div>
                    </div>

                    <!-- MAIN CATEGORIES LIST (80px HEIGHT) -->
                    <div class="kb-category-list-yody">
                        <div class="kb-list-row-yody" data-target="son-moi">
                            <div class="kb-row-info-yody">
                                <img src="/images/clients/mega-menu/lipstick-bst.png" class="kb-row-img-yody" alt="">
                                <span>SON MÔI</span>
                            </div>
                            <svg class="kb-row-chevron-yody" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                        </div>
                        <div class="kb-list-row-yody" data-target="nails">
                            <div class="kb-row-info-yody">
                                <img src="/images/clients/mega-menu/nail-bst.png" class="kb-row-img-yody" alt="">
                                <span>NAILS & MI</span>
                            </div>
                            <svg class="kb-row-chevron-yody" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                        </div>
                        <div class="kb-list-row-yody" data-target="dung-cu">
                            <div class="kb-row-info-yody">
                                <img src="/images/clients/mega-menu/tools-bst.png" class="kb-row-img-yody" alt="">
                                <span>DỤNG CỤ</span>
                            </div>
                            <svg class="kb-row-chevron-yody" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                        </div>
                    </div>

                    <!-- PERSISTENT SEARCH (BOTTOM OF CONTENT) -->
                    <div class="kb-mega-menu__search-yody">
                        <svg class="kb-search-icon-yody" viewBox="0 0 24 24"><path d="M11 19a8 8 0 100-16 8 8 0 000 16zM21 21l-4.35-4.35"/></svg>
                        <input type="text" placeholder="Tìm sản phẩm của bạn...">
                    </div>
                </div>
            </div>

            <!-- LEVEL 2: DETAILED VIEW -->
            <div class="kb-mega-menu__page kb-mega-menu__page--l2" id="menuL2">
                <div class="kb-l2-header-yody" id="btnBackToL1">
                    <svg viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>
                    <span id="currentCategoryTitleYody">DANH MỤC</span>
                </div>
                <div class="kb-l2-scroll-area" id="l2ContentYody">
                    <!-- Accordions injected via JS -->
                </div>
            </div>

        </div>

        <!-- STICKY FLOATING PILL (YODY COLOR #E5E7EB GLASS) -->
        <div class="kb-mega-menu__floating-yody">
            <button class="kb-floating-pill-yody" id="closeMegaMenu">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6L6 18M6 6l12 12"/></svg>
                <span>Đóng</span>
            </button>
        </div>
    </div>
</div>




