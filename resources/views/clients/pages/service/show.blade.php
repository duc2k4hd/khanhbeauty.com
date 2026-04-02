@extends('clients.layouts.app')

@section('content')

<style>
/* ── PAGE: SERVICE DETAIL ─────────────────────────────── */
.sd-page { background: #fdf8f4; min-height: 100vh; padding-top: 80px; }
.sd-page-stats { display: flex; margin-top: 30px; gap: 16px; align-items: center; color: rgba(255,255,255,0.8); font-size: 12px; letter-spacing: 1px;}
.sd-page-stats i { color: #c9a96e; margin-right: 4px; }

/* ── HERO ─────── */
.sd-hero {
    position: relative;
    height: clamp(500px, 65vh, 700px);
    overflow: hidden;
}
.sd-hero-img {
    width: 100%; height: 100%; object-fit: cover;
    display: block;
    transition: transform 8s ease;
}
.sd-hero:hover .sd-hero-img { transform: scale(1.04); }
.sd-hero-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(
        to right,
        rgba(20,10,15,0.92) 0%,
        rgba(20,10,15,0.65) 50%,
        rgba(20,10,15,0.2) 100%
    );
}
.sd-hero-content {
    position: absolute; inset: 0;
    display: flex; flex-direction: column; justify-content: center;
    padding: 0 7vw;
    max-width: 780px;
}
.sd-breadcrumb {
    font-size: 10px; font-weight: 700; letter-spacing: 2.5px; text-transform: uppercase;
    color: rgba(255,255,255,0.45);
    margin-bottom: 24px;
    display: flex; align-items: center; gap: 10px;
}
.sd-breadcrumb a { color: inherit; text-decoration: none; transition: color 0.2s; }
.sd-breadcrumb a:hover { color: #c9a96e; }
.sd-breadcrumb span { opacity: 0.35; }

.sd-cat-tag {
    display: inline-block;
    font-size: 10px; font-weight: 700; letter-spacing: 3px; text-transform: uppercase;
    color: #c9a96e;
    border: 1px solid rgba(201,169,110,0.4);
    padding: 5px 16px; border-radius: 100px;
    margin-bottom: 20px;
}
.sd-hero-title {
    font-family: 'Playfair Display', serif;
    font-size: clamp(34px, 5vw, 62px);
    font-weight: 400; font-style: italic;
    color: #fff;
    line-height: 1.18;
    margin-bottom: 20px;
}
.sd-hero-desc {
    font-family: 'Cormorant Garamond', serif;
    font-size: 18px; color: rgba(255,255,255,0.7);
    line-height: 1.75; font-style: italic;
    margin-bottom: 28px;
    max-width: 580px;
}
.sd-hero-badges {
    display: flex; flex-wrap: wrap; gap: 12px;
}
.sd-badge {
    display: flex; align-items: center; gap: 10px;
    background: rgba(255,255,255,0.05);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 12px;
    padding: 10px 18px;
}
.sd-badge-icon { color: #c9a96e; font-size: 14px; }
.sd-badge-label { font-size: 9px; text-transform: uppercase; letter-spacing: 2px; color: rgba(255,255,255,0.45); display: block; }
.sd-badge-value { font-size: 15px; font-weight: 700; color: #fff; display: block; }

/* ── LAYOUT BODY ─────── */
.sd-body {
    max-width: 1300px; margin: 0 auto;
    padding: 64px 24px 100px;
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 56px;
    align-items: start;
}
@media (max-width: 1024px) {
    .sd-body { grid-template-columns: 1fr; }
    .sd-sidebar { order: -1; }
}

/* ── MAIN CONTENT ─────── */
.sd-section {
    background: #fff;
    border-radius: 20px;
    padding: 48px;
    margin-bottom: 32px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.03);
    border: 1px solid rgba(183,110,121,0.06);
}
@media (max-width: 768px) {
    .sd-section { padding: 32px 24px; }
}
.sd-section-eyebrow {
    display: flex; align-items: center; gap: 16px; margin-bottom: 28px;
}
.sd-section-eyebrow-line {
    width: 32px; height: 2px; background: #b76e79; flex-shrink: 0;
}
.sd-section-eyebrow-text {
    font-size: 10px; font-weight: 700; letter-spacing: 3px; text-transform: uppercase;
    color: #b76e79;
}
.sd-section h2 {
    font-family: 'Playfair Display', serif;
    font-size: 32px; font-weight: 600; color: #2d1515;
    margin-bottom: 24px; line-height: 1.3;
}

/* Base Content */
.sd-content { color: #5a4a46; line-height: 1.9; font-size: 16px; }
.sd-content p { margin-bottom: 18px; }
.sd-content h2, .sd-content h3 {
    font-family: 'Playfair Display', serif;
    color: #2d1515; margin: 32px 0 14px;
    font-size: 24px;
}
.sd-content ul { list-style: none; padding: 0; margin: 20px 0; }
.sd-content li { position: relative; padding-left: 24px; margin-bottom: 12px; }
.sd-content li::before {
    content: '◆'; position: absolute; left: 0; top: 0;
    color: #b76e79; font-size: 9px; margin-top: 6px;
}
.sd-content strong { color: #b76e79; font-weight: 700; }

/* ── VIDEO BLOCKS ─────── */
.sd-video-wrapper {
    position: relative; padding-bottom: 56.25%; height: 0;
    border-radius: 16px; overflow: hidden; margin-top: 24px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}
.sd-video-wrapper iframe, .sd-video-wrapper video {
    position: absolute; top:0; left:0; width:100%; height:100%;
    border: none;
}

/* ── FEATURES: INCLUDES & BENEFITS ─────── */
.sd-features { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 24px; }
@media (max-width: 768px) { .sd-features { grid-template-columns: 1fr; } }
.sd-feature-box { background: #fdf8f4; border-radius: 16px; padding: 30px; border: 1px solid rgba(183,110,121,0.08); }
.sd-feature-box h3 { font-family: 'Playfair Display', serif; font-size: 22px; color: #2d1515; margin-top: 0; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid rgba(183,110,121,0.1); }
.sd-benefit-list { list-style: none; padding: 0; margin: 0; }
.sd-benefit-list li { margin-bottom: 20px; line-height: 1.6; color: #5a4a46; }
.sd-benefit-list h5 { font-size: 16px; color: #b76e79; margin: 0 0 6px 0; display: flex; align-items: center; gap: 8px;}
.sd-include-list { list-style: none; padding: 0; margin: 0; }
.sd-include-list li { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; color: #2d1515; font-weight: 500;}
.sd-include-list i { color: #c9a96e; font-size: 16px; }

/* ── VARIANTS ─────── */
.sd-variants { display: grid; gap: 16px; margin-top: 24px; }
.sd-variant-card {
    border: 1px solid rgba(183,110,121,0.15); border-radius: 16px; padding: 24px;
    display: flex; justify-content: space-between; align-items: center;
    background: #fff; transition: all 0.3s;
    cursor: pointer; position: relative; overflow: hidden;
}
.sd-variant-card:hover { border-color: #b76e79; transform: translateY(-3px); box-shadow: 0 10px 30px rgba(183,110,121,0.1); }
.sd-variant-card::before {
    content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 4px;
    background: transparent; transition: background 0.3s;
}
.sd-variant-card:hover::before { background: #b76e79; }
.sd-var-info h4 { margin:0 0 6px; font-family: 'Playfair Display', serif; font-size: 20px; color:#2d1515; }
.sd-var-info p { margin:0; font-size: 13px; color:#7a6a65; display: flex; gap: 12px; align-items: center;}
.sd-var-info p span { display: flex; align-items: center; gap: 4px; }
.sd-var-price { font-family: 'Playfair Display', serif; font-size: 24px; font-weight: 700; color:#b76e79; text-align: right;}
.sd-var-price del { font-size: 14px; color:#aaa; font-weight: 400; display:block; }

/* ── PROCESS STEPS ─────── */
.sd-steps { display: flex; flex-direction: column; gap: 20px; }
.sd-step {
    display: flex; gap: 24px; align-items: flex-start;
    padding: 24px; background: #fff;
    border-radius: 16px;
    border: 1px solid rgba(183,110,121,0.08);
    transition: box-shadow 0.25s ease, transform 0.25s ease;
}
.sd-step:hover { box-shadow: 0 8px 30px rgba(183,110,121,0.12); transform: translateX(4px); }
.sd-step-num {
    width: 48px; height: 48px; flex-shrink: 0;
    border-radius: 14px;
    background: linear-gradient(135deg, #b76e79, #d4a0a7);
    display: flex; align-items: center; justify-content: center;
    font-family: 'Playfair Display', serif;
    font-size: 19px; font-weight: 700; color: #fff;
}
.sd-step h4 { font-family: 'Playfair Display', serif; font-size: 19px; color: #2d1515; margin-bottom: 6px; margin-top: 0; }
.sd-step p { font-size: 15px; color: #7a6a65; line-height: 1.7; margin: 0;}

/* ── GALLERY ─────── */
.sd-gallery { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px; margin-top: 24px; }
.sd-gallery-item { border-radius: 12px; overflow: hidden; aspect-ratio: 16/9; cursor: pointer; position: relative;}
.sd-gallery-item img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s cubic-bezier(0.2, 0.8, 0.2, 1); }
.sd-gallery-item:hover img { transform: scale(1.1); }
.sd-gallery-item::after { content: ''; position: absolute; inset: 0; background: rgba(0,0,0,0.2); opacity: 0; transition: opacity 0.3s; }
.sd-gallery-item:hover::after { opacity: 1; }
.sd-gallery-item i { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) scale(0.5); color: #fff; font-size: 24px; opacity: 0; transition: all 0.3s; z-index: 2; pointer-events:none;}
.sd-gallery-item:hover i { opacity: 1; transform: translate(-50%, -50%) scale(1); }

/* ── LIGHTBOX ─────── */
.sd-lightbox {
    position: fixed; inset: 0; z-index: 9999;
    background: rgba(10, 5, 8, 0.95); backdrop-filter: blur(25px);
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    opacity: 0; pointer-events: none;
    transition: opacity 0.5s ease;
}
.sd-lightbox.active { opacity: 1; pointer-events: auto; }

.sd-lb-close {
    position: absolute; top: 30px; right: 40px;
    background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);
    width: 44px; height: 44px; border-radius: 50%;
    color: #fff; font-size: 18px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; transition: all 0.4s cubic-bezier(0.8, 0, 0.2, 1); z-index: 10;
}
.sd-lb-close:hover { background: #b76e79; transform: rotate(180deg) scale(1.1); border-color: #b76e79;}

.sd-lb-wrap {
    position: relative; width: 90%; max-width: 1100px;
    display: flex; align-items: center; justify-content: center;
}

.sd-lb-main {
    position: relative; width: 100%;
    aspect-ratio: 16/9; border-radius: 12px; overflow: hidden;
    box-shadow: 0 30px 60px rgba(0,0,0,0.6);
    transform: scale(0.9) translateY(40px); transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    touch-action: pan-y; user-select: none; cursor: grab;
}
.sd-lb-main:active { cursor: grabbing; }
.sd-lightbox.active .sd-lb-main { transform: scale(1) translateY(0); }

.sd-lb-track {
    display: flex; width: 100%; height: 100%;
    transition: transform 0.5s cubic-bezier(0.2, 0.8, 0.2, 1);
}
.sd-lb-slide {
    flex: 0 0 100%; width: 100%; height: 100%; position: relative;
}
.sd-lb-slide img {
    width: 100%; height: 100%; object-fit: cover; pointer-events: none;
}

.sd-lb-nav {
    position: absolute; top: 50%; transform: translateY(-50%);
    background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);
    width: 50px; height: 50px; border-radius: 50%;
    color: #fff; font-size: 16px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; transition: all 0.3s cubic-bezier(0.8, 0, 0.2, 1); z-index: 10;
}
.sd-lb-prev { left: -70px; }
.sd-lb-next { right: -70px; }
@media(max-width:1250px) {
    .sd-lb-prev { left: 20px; background: rgba(0,0,0,0.3); border:none;}
    .sd-lb-next { right: 20px; background: rgba(0,0,0,0.3); border:none;}
}
.sd-lb-nav:hover { background: #b76e79; border-color: #b76e79; transform: translateY(-50%) scale(1.15); }

.sd-lb-thumbs {
    position: absolute; bottom: 30px; left: 50%; transform: translateX(-50%);
    display: flex; gap: 10px;
    padding: 10px 16px; background: rgba(0,0,0,0.4); border-radius: 100px;
    max-width: 90vw; overflow-x: auto; scrollbar-width: none;
}
.sd-lb-thumbs::-webkit-scrollbar { display: none; }
.sd-lb-thumb {
    width: 64px; height: 36px; border-radius: 4px; overflow: hidden; flex-shrink: 0;
    cursor: pointer; opacity: 0.3; transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    border: 2px solid transparent; transform: translateY(0);
}
.sd-lb-thumb img { width: 100%; height: 100%; object-fit: cover; }
.sd-lb-thumb.active { opacity: 1; border-color: #c9a96e; transform: scale(1.15) translateY(-4px); box-shadow: 0 10px 20px rgba(0,0,0,0.4); z-index: 2;}
.sd-lb-thumb:hover { opacity: 0.8; }

/* ── FAQS ─────── */
.sd-faqs { margin-top: 24px; border: 1px solid rgba(183,110,121,0.15); border-radius: 16px; overflow: hidden;}
.sd-faq-item { border-bottom: 1px solid rgba(183,110,121,0.1); background: #fff;}
.sd-faq-item:last-child { border-bottom: none; }
.sd-faq-q { padding: 20px 24px; display: flex; justify-content: space-between; align-items: center; cursor: pointer; font-family: 'Playfair Display', serif; font-size: 18px; color: #2d1515; font-weight: 600; transition: background 0.3s;}
.sd-faq-q:hover { background: #fdf8f4; }
.sd-faq-q i { color: #b76e79; transition: transform 0.3s; }
.sd-faq-a { max-height: 0; overflow: hidden; transition: max-height 0.4s ease; }
.sd-faq-a-inner { padding: 0 24px 24px; color: #5a4a46; line-height: 1.7; font-size: 15px;}
.sd-faq-item.active .sd-faq-q { background: #fdf8f4; border-bottom: 1px dashed rgba(183,110,121,0.15); margin-bottom: 16px;}
.sd-faq-item.active .sd-faq-q i { transform: rotate(180deg); }


/* ── REVIEWS ─────── */
.sd-reviews { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
@media (max-width: 680px) { .sd-reviews { grid-template-columns: 1fr; } }
.sd-review-card {
    background: #fdf8f4; border-radius: 16px; padding: 28px;
    border: 1px solid rgba(183,110,121,0.08); position: relative;
}
.sd-review-quote {
    font-size: 60px; line-height: 1; color: rgba(183,110,121,0.12);
    font-family: 'Playfair Display', serif; position: absolute; top: 16px; left: 20px;
}
.sd-review-text {
    position: relative; z-index: 1; font-family: 'Cormorant Garamond', serif;
    font-size: 17px; font-style: italic; color: #5a4a46; line-height: 1.7;
    margin-bottom: 20px; margin-top: 16px;
}
.sd-review-footer { display: flex; align-items: center; gap: 12px; }
.sd-review-avatar {
    width: 40px; height: 40px; border-radius: 50%;
    background: linear-gradient(135deg, #b76e79, #d4a0a7);
    display: flex; align-items: center; justify-content: center;
    font-family: 'Playfair Display', serif; font-size: 18px; color: #fff; font-style: italic; flex-shrink: 0;
}
.sd-review-name { font-size: 13px; font-weight: 700; color: #2d1515; line-height: 1.4;}
.sd-review-date { font-size: 11px; color: #9a8a85; }
.sd-stars { display: flex; gap: 3px; margin-bottom: 2px; }
.sd-stars i { font-size: 11px; color: #c9a96e; }
.sd-stars i.empty { color: #e0d5d0; }

/* ── SIDEBAR ─────── */
.sd-sidebar { position: sticky; top: 100px; display: flex; flex-direction: column; gap: 24px; }
.sd-booking-card {
    background: #fff; border-radius: 20px; padding: 36px;
    box-shadow: 0 4px 32px rgba(183,110,121,0.12); border: 1px solid rgba(183,110,121,0.1);
}
.sd-price-block { text-align: center; margin-bottom: 28px; padding-bottom: 28px; border-bottom: 1px solid #f5ede9; }
.sd-price-label { font-size: 10px; font-weight: 700; letter-spacing: 3px; text-transform: uppercase; color: #9a8a85; margin-bottom: 12px; }
.sd-price-main { font-family: 'Playfair Display', serif; font-size: 40px; font-weight: 700; color: #b76e79; line-height: 1; margin-bottom: 6px; }
.sd-price-old { font-size: 14px; color: #9a8a85; text-decoration: line-through; margin-bottom: 6px; }
.sd-price-unit { font-size: 12px; color: #9a8a85; }

.sd-info-list { display: flex; flex-direction: column; gap: 0; margin-bottom: 28px; }
.sd-info-row { display: flex; justify-content: space-between; align-items: center; padding: 14px 0; border-bottom: 1px solid #f5ede9; }
.sd-info-row:last-child { border-bottom: none; }
.sd-info-key { font-size: 12px; font-weight: 600; color: #9a8a85; text-transform: uppercase; letter-spacing: 1px; }
.sd-info-val { font-size: 14px; font-weight: 700; color: #2d1515; }

.sd-btn-zalo {
    display: flex; align-items: center; justify-content: center; gap: 10px;
    width: 100%; padding: 16px; background: linear-gradient(135deg, #b76e79, #c9849a);
    color: #fff; border-radius: 14px; font-size: 13px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase;
    text-decoration: none; transition: all 0.3s ease; margin-bottom: 16px; box-shadow: 0 6px 20px rgba(183,110,121,0.3);
}
.sd-btn-zalo:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(183,110,121,0.4); }
.sd-btn-phone {
    display: flex; align-items: center; justify-content: center; gap: 10px; width: 100%; padding: 14px;
    border: 1.5px solid rgba(183,110,121,0.2); border-radius: 14px; background: transparent;
    font-family: 'Playfair Display', serif; font-size: 20px; color: #2d1515; text-decoration: none; transition: all 0.25s ease;
}
.sd-btn-phone:hover { border-color: #b76e79; color: #b76e79; background: rgba(183,110,121,0.04); }

.sd-why-card { background: linear-gradient(135deg, #2d1b35 0%, #1a1a2e 100%); border-radius: 20px; padding: 32px; color: #fff; }
.sd-why-card h4 { font-family: 'Playfair Display', serif; font-size: 20px; margin-bottom: 16px; margin-top:0; font-style: italic; }
.sd-why-list { list-style: none; padding: 0; margin:0;}
.sd-why-list li { display: flex; align-items: flex-start; gap: 12px; padding: 10px 0; border-bottom: 1px solid rgba(255,255,255,0.07); font-size: 14px; color: rgba(255,255,255,0.7); line-height: 1.6; }
.sd-why-list li:last-child { border-bottom: none; padding-bottom: 0;}
.sd-why-list i { color: #c9a96e; flex-shrink: 0; margin-top: 3px; }

/* ── RELATED SERVICES ─────── */
.sd-related { max-width: 1300px; margin: 0 auto 80px; padding: 0 24px; }
.sd-related-head { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 36px; }
.sd-related-head h3 { font-family: 'Playfair Display', serif; font-size: 32px; font-weight: 400; font-style: italic; color: #2d1515; margin:0;}
.sd-related-head a { font-size: 12px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; color: #b76e79; text-decoration: none; }
.sd-related-head a:hover { text-decoration: underline; }
.sd-related-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
@media (max-width: 900px) { .sd-related-grid { grid-template-columns: 1fr 1fr; } }
@media (max-width: 580px) { .sd-related-grid { grid-template-columns: 1fr; } }
.sd-rel-card { border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.07); transition: transform 0.3s ease, box-shadow 0.3s ease; text-decoration: none; display: block; background: #fff; }
.sd-rel-card:hover { transform: translateY(-5px); box-shadow: 0 12px 36px rgba(183,110,121,0.15); }
.sd-rel-card-img { position: relative; height: 200px; overflow: hidden; }
.sd-rel-card-img img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.8s ease; }
.sd-rel-card:hover .sd-rel-card-img img { transform: scale(1.06); }
.sd-rel-card-overlay { position: absolute; inset: 0; background: linear-gradient(to top, rgba(20,10,15,0.5) 0%, transparent 60%); }
.sd-rel-card-cat { position: absolute; top: 12px; left: 12px; background: rgba(255,255,255,0.9); backdrop-filter: blur(8px); font-size: 9px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: #b76e79; padding: 4px 12px; border-radius: 100px; }
.sd-rel-card-body { padding: 20px 22px 24px; }
.sd-rel-card-body h4 { font-family: 'Playfair Display', serif; font-size: 18px; color: #2d1515; margin:0 0 6px; transition: color 0.2s; }
.sd-rel-card:hover h4 { color: #b76e79; }
.sd-rel-card-body p { font-size: 13px; color: #7a6a65; line-height: 1.6; margin:0 0 14px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.sd-rel-card-cta { font-size: 11px; font-weight: 700; color: #b76e79; letter-spacing: 1.5px; text-transform: uppercase; }

/* ── ANIMATIONS ─────── */
.sd-reveal { opacity: 0; transform: translateY(30px); transition: opacity 0.8s ease, transform 0.8s ease; }
.sd-reveal.visible { opacity: 1; transform: translateY(0); }
</style>

<div class="sd-page">
    {{-- HERO --}}
    <div class="sd-hero">
        <img class="sd-hero-img" src="{{ $service->featuredImage?->file_url ?? '/images/no-image.webp' }}" alt="{{ $service->featuredImage?->alt_text ?? $service->name }}">
        <div class="sd-hero-overlay"></div>
        <div class="sd-hero-content">
            <div class="sd-breadcrumb">
                <a href="{{ route('home') }}">Trang chủ</a>
                <span>/</span>
                <a href="{{ route('services.index') }}">Dịch vụ</a>
                <span>/</span>
                <span style="color:rgba(255,255,255,0.7)">{{ $service->name }}</span>
            </div>
            <span class="sd-cat-tag">{{ $service->category->name }}</span>
            <h1 class="sd-hero-title">{{ $service->name }}</h1>
            <p class="sd-hero-desc">"{{ $service->short_description }}"</p>
            <div class="sd-hero-badges">
                <div class="sd-badge">
                    <span class="sd-badge-icon"><i class="far fa-clock"></i></span>
                    <div>
                        <span class="sd-badge-label">Thời gian</span>
                        <span class="sd-badge-value">{{ $service->duration_minutes }} phút</span>
                    </div>
                </div>
                <div class="sd-badge">
                    <span class="sd-badge-icon"><i class="fas fa-tag"></i></span>
                    <div>
                        <span class="sd-badge-label">Chi phí từ</span>
                        <span class="sd-badge-value">{{ number_format($service->sale_price ?: $service->price) }}đ</span>
                    </div>
                </div>
                @if($service->avg_rating > 0)
                <div class="sd-badge">
                    <span class="sd-badge-icon"><i class="fas fa-star" style="color:#fadb14"></i></span>
                    <div>
                        <span class="sd-badge-label">Đánh giá</span>
                        <span class="sd-badge-value">{{ $service->avg_rating }}/5.0</span>
                    </div>
                </div>
                @endif
            </div>
            <div class="sd-page-stats">
                <span><i class="fas fa-eye"></i> {{ number_format($service->view_count) }} lượt xem</span>
                @if($service->booking_count > 0)
                <span style="opacity:0.3">|</span>
                <span><i class="fas fa-shopping-bag"></i> Đã đặt: {{ number_format($service->booking_count) }} lượt</span>
                @endif
            </div>
        </div>
    </div>

    {{-- BODY --}}
    <div class="sd-body">
        {{-- MAIN --}}
        <div class="sd-main">
            
            {{-- DESCRIPTION --}}
            <div class="sd-section sd-reveal">
                <div class="sd-section-eyebrow">
                    <div class="sd-section-eyebrow-line"></div>
                    <span class="sd-section-eyebrow-text">Giới thiệu dịch vụ</span>
                </div>
                <h2>Thông tin <em>Chi tiết</em></h2>
                <div class="sd-content">
                    {!! $service->description !!}
                </div>
                
                {{-- VIDEO YOUTUBE IF EXISTS --}}
                @if($service->video_url)
                <div class="sd-video-wrapper">
                    @php
                        // Đơn giản hóa chuyển URL youtube thành nhúng
                        $videoUrl = str_replace('watch?v=', 'embed/', $service->video_url);
                        $videoUrl = str_replace('youtu.be/', 'youtube.com/embed/', $videoUrl);
                    @endphp
                    <iframe src="{{ $videoUrl }}" allowfullscreen></iframe>
                </div>
                @endif
            </div>

            {{-- BENEFITS & INCLUDES --}}
            @if((is_array($service->benefits) && count($service->benefits) > 0) || (is_array($service->includes) && count($service->includes) > 0))
            <div class="sd-features sd-reveal">
                @if(is_array($service->includes) && count($service->includes) > 0)
                <div class="sd-feature-box">
                    <h3>Gói Dịch Vụ Bao Gồm</h3>
                    <ul class="sd-include-list">
                        @foreach($service->includes as $inc)
                            @if(trim($inc))
                            <li><i class="fas fa-check-circle"></i> {{ $inc }}</li>
                            @endif
                        @endforeach
                    </ul>
                </div>
                @endif

                @if(is_array($service->benefits) && count($service->benefits) > 0)
                <div class="sd-feature-box" style="background:#fff;">
                    <h3>Giá Trị Nhận Được</h3>
                    <ul class="sd-benefit-list">
                        @foreach($service->benefits as $b)
                        <li>
                            <h5><i class="fas fa-gem" style="font-size:12px; color:#c9a96e"></i> {{ $b['title'] ?? '' }}</h5>
                            {{ $b['description'] ?? '' }}
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
            @endif

            {{-- VARIANTS (PACKAGES) --}}
            @if($service->variants && $service->variants->count() > 0)
            <div class="sd-section sd-reveal" style="margin-top:32px;">
                <div class="sd-section-eyebrow">
                    <div class="sd-section-eyebrow-line"></div>
                    <span class="sd-section-eyebrow-text">Lựa chọn của bạn</span>
                </div>
                <h2>Các Gói <em>Phân Loại</em></h2>
                <div class="sd-variants">
                    @foreach($service->variants as $variant)
                    <div class="sd-variant-card">
                        <div class="sd-var-info">
                            <h4>{{ $variant->variant_name }}</h4>
                            <p>
                                @if($variant->duration_minutes)
                                <span><i class="far fa-clock"></i> {{ $variant->duration_minutes }} phút</span>
                                @endif
                                <span><i class="fas fa-barcode"></i> SKU: {{ $variant->sku }}</span>
                            </p>
                        </div>
                        <div class="sd-var-price">
                            @if($variant->sale_price)
                            <del>{{ number_format($variant->price) }}đ</del>
                            {{ number_format($variant->sale_price) }}đ
                            @else
                            {{ number_format($variant->price) }}đ
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- PROCESS --}}
            @if(is_array($service->process_steps) && count($service->process_steps) > 0)
            <div class="sd-section sd-reveal">
                <div class="sd-section-eyebrow">
                    <div class="sd-section-eyebrow-line"></div>
                    <span class="sd-section-eyebrow-text">Tận tâm từng bước</span>
                </div>
                <h2>Quy trình <em>Trải nghiệm</em></h2>
                <div class="sd-steps">
                    @foreach($service->process_steps as $index => $step)
                    <div class="sd-step">
                        <div class="sd-step-num">{{ sprintf('%02d', $index + 1) }}</div>
                        <div>
                            <h4>{{ $step['title'] ?? '' }}</h4>
                            <p>{{ $step['description'] ?? '' }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- GALLERY --}}
            @if($service->gallery_media && $service->gallery_media->count() > 0)
            <div class="sd-section sd-reveal">
                <div class="sd-section-eyebrow">
                    <div class="sd-section-eyebrow-line"></div>
                    <span class="sd-section-eyebrow-text">Góc nhìn</span>
                </div>
                <h2>Thư Viện <em>Hình Ảnh</em></h2>
                <div class="sd-gallery">
                    @foreach($service->gallery_media as $index => $media)
                    <div class="sd-gallery-item" data-index="{{ $index }}">
                        <img src="{{ $media->file_url }}" alt="{{ $media->alt_text ?? $service->name }}" loading="lazy" decoding="async">
                        <i class="fas fa-expand"></i>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- FAQS --}}
            @if($service->faqs && $service->faqs->count() > 0)
            <div class="sd-section sd-reveal">
                <div class="sd-section-eyebrow">
                    <div class="sd-section-eyebrow-line"></div>
                    <span class="sd-section-eyebrow-text">Giải đáp thắc mắc</span>
                </div>
                <h2>Câu Hỏi <em>Thường Gặp</em></h2>
                <div class="sd-faqs">
                    @foreach($service->faqs as $faq)
                    <div class="sd-faq-item">
                        <div class="sd-faq-q">
                            {{ $faq->question }}
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="sd-faq-a">
                            <div class="sd-faq-a-inner">
                                {!! nl2br(e($faq->answer)) !!}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- REVIEWS --}}
            @if($service->reviews && $service->reviews->count() > 0)
            <div class="sd-section sd-reveal" style="transition-delay: 0.15s">
                <div class="sd-section-eyebrow">
                    <div class="sd-section-eyebrow-line"></div>
                    <span class="sd-section-eyebrow-text">Cảm nhận khách hàng</span>
                </div>
                <h2>Lời <em>Tin Yêu</em></h2>
                <div class="sd-reviews">
                    @foreach($service->reviews as $review)
                    <div class="sd-review-card">
                        <div class="sd-review-quote">"</div>
                        <p class="sd-review-text">{{ $review->content }}</p>
                        <div class="sd-review-footer">
                            <div class="sd-review-avatar">{{ substr($review->guest_name, 0, 1) }}</div>
                            <div>
                                <div class="sd-stars">
                                    @for($i = 0; $i < 5; $i++)
                                    <i class="fas fa-star {{ $i < ($review->rating ?? 5) ? '' : 'empty' }}"></i>
                                    @endfor
                                </div>
                                <div class="sd-review-name">{{ $review->guest_name }}</div>
                                <div class="sd-review-date">{{ $review->created_at->format('d/m/Y') }}</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- SIDEBAR --}}
        <div class="sd-sidebar">
            <div class="sd-booking-card sd-reveal">
                {{-- PRICE --}}
                <div class="sd-price-block">
                    <p class="sd-price-label">Giá dịch vụ trọn gói</p>
                    @if($service->sale_price)
                    <div class="sd-price-old">{{ number_format($service->price) }}đ</div>
                    <div class="sd-price-main">{{ number_format($service->sale_price) }}đ</div>
                    @else
                    <div class="sd-price-main">{{ number_format($service->price) }}đ</div>
                    @endif
                    <div class="sd-price-unit">/ {{ $service->price_unit }}</div>
                </div>

                {{-- INFO --}}
                <div class="sd-info-list">
                    <div class="sd-info-row">
                        <span class="sd-info-key"><i class="far fa-clock" style="margin-right:8px;color:#b76e79"></i>Thời gian</span>
                        <span class="sd-info-val">{{ $service->duration_minutes }} phút</span>
                    </div>
                    <div class="sd-info-row">
                        <span class="sd-info-key"><i class="fas fa-home" style="margin-right:8px;color:#c9a96e"></i>Hỗ trợ</span>
                        <span class="sd-info-val">Tại Studio / Tận nơi</span>
                    </div>
                    <div class="sd-info-row">
                        <span class="sd-info-key"><i class="fas fa-shield-alt" style="margin-right:8px;color:#b76e79"></i>Chất lượng</span>
                        <span class="sd-info-val">Cao cấp 100%</span>
                    </div>
                </div>

                {{-- CTA --}}
                <a href="https://zalo.me/{{ \App\Models\SiteSetting::getValue('phone', '0987654321') }}" target="_blank" class="sd-btn-zalo">
                    <i class="fas fa-comment-dots"></i>
                    Đặt lịch qua Zalo
                </a>
                <a href="tel:{{ \App\Models\SiteSetting::getValue('phone', '0987654321') }}" class="sd-btn-phone">
                    <i class="fas fa-phone" style="color:#b76e79;font-size:14px"></i>
                    {{ \App\Models\SiteSetting::getValue('phone', '0987.654.321') }}
                </a>
            </div>

            {{-- WHY US --}}
            <div class="sd-why-card sd-reveal" style="transition-delay: 0.1s">
                <h4>Tại sao chọn Khánh Beauty?</h4>
                <ul class="sd-why-list">
                    <li><i class="fas fa-check-circle"></i> Đội ngũ chuyên gia tay nghề cao, tâm huyết với nghề.</li>
                    <li><i class="fas fa-check-circle"></i> Sử dụng mỹ phẩm danh tiếng, an toàn cho mọi loại da.</li>
                    <li><i class="fas fa-check-circle"></i> Thiết kế Layout theo đúng phong thủy và tỷ lệ vàng.</li>
                    <li><i class="fas fa-check-circle"></i> Khách hàng là trung tâm, hỗ trợ dặm phấn liên tục.</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- RELATED --}}
    @if(isset($relatedServices) && $relatedServices->count() > 0)
    <div class="sd-related sd-reveal">
        <div class="sd-related-head">
            <h3>Dịch vụ <em>Liên quan</em></h3>
            <a href="{{ route('services.index') }}">Xem tất cả →</a>
        </div>
        <div class="sd-related-grid">
            @foreach($relatedServices as $rel)
            <a href="{{ route('services.show', $rel->slug) }}" class="sd-rel-card">
                <div class="sd-rel-card-img">
                    <img src="{{ $rel->featuredImage?->file_url ?? '/images/no-image.webp' }}" alt="{{ $rel->featuredImage?->alt_text ?? $rel->name }}" loading="lazy">
                    <div class="sd-rel-card-overlay"></div>
                    <span class="sd-rel-card-cat">{{ $rel->category->name ?? 'Dịch vụ' }}</span>
                </div>
                <div class="sd-rel-card-body">
                    <h4>{{ $rel->name }}</h4>
                    <p>{{ $rel->short_description }}</p>
                    <span class="sd-rel-card-cta">Xem chi tiết →</span>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>

@if($service->gallery_media && $service->gallery_media->count() > 0)
{{-- LIGHTBOX OVERLAY --}}
<div id="sd-lightbox" class="sd-lightbox">
    <div class="sd-lb-close"><i class="fas fa-times"></i></div>
    
    <div class="sd-lb-wrap">
        <div class="sd-lb-prev sd-lb-nav"><i class="fas fa-chevron-left"></i></div>
        
        <div class="sd-lb-main" id="sd-lb-main-drag">
            <div class="sd-lb-track" id="sd-lb-track">
                @foreach($service->gallery_media as $index => $media)
                <div class="sd-lb-slide">
                    <img src="{{ $media->file_url }}" alt="Gallery Image {{ $index }}" draggable="false">
                </div>
                @endforeach
            </div>
        </div>

        <div class="sd-lb-next sd-lb-nav"><i class="fas fa-chevron-right"></i></div>
    </div>

    <div class="sd-lb-thumbs">
        @foreach($service->gallery_media as $index => $media)
        <div class="sd-lb-thumb">
            <img src="{{ $media->file_url }}" alt="Thumb {{ $index }}">
        </div>
        @endforeach
    </div>
</div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Scroll reveal animation
    const io = new IntersectionObserver((entries) => {
        entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); io.unobserve(e.target); } });
    }, { threshold: 0.08 });
    document.querySelectorAll('.sd-reveal').forEach(el => io.observe(el));

    // FAQ Accordion logic
    document.querySelectorAll('.sd-faq-q').forEach(question => {
        question.addEventListener('click', () => {
            const item = question.parentNode;
            const answer = question.nextElementSibling;
            
            // Toggle current
            if (item.classList.contains('active')) {
                item.classList.remove('active');
                answer.style.maxHeight = null;
            } else {
                // Close others (optional)
                document.querySelectorAll('.sd-faq-item').forEach(other => {
                    other.classList.remove('active');
                    other.querySelector('.sd-faq-a').style.maxHeight = null;
                });
                
                item.classList.add('active');
                answer.style.maxHeight = answer.scrollHeight + "px";
            }
        });
    });

    // Lightbox Logic
    const galleryItems = document.querySelectorAll('.sd-gallery-item');
    if (galleryItems.length > 0) {
        const lightbox = document.getElementById('sd-lightbox');
        const track = document.getElementById('sd-lb-track');
        const thumbs = lightbox.querySelectorAll('.sd-lb-thumb');
        let currentIndex = 0;
        let isDragging = false;
        let startPos = 0;
        let currentTranslate = 0;
        let prevTranslate = 0;
        let animationID;
        const totalSlides = galleryItems.length;

        const updatePosition = () => {
            track.style.transition = 'transform 0.5s cubic-bezier(0.2, 0.8, 0.2, 1)';
            currentTranslate = currentIndex * -100;
            prevTranslate = currentTranslate;
            track.style.transform = `translateX(${currentTranslate}%)`;
            
            thumbs.forEach(el => el.classList.remove('active'));
            if(thumbs[currentIndex]) {
                thumbs[currentIndex].classList.add('active');
                thumbs[currentIndex].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
            }
        };

        const showSlide = (index) => {
            currentIndex = index;
            updatePosition();
        };

        galleryItems.forEach(item => {
            item.addEventListener('click', () => {
                const index = parseInt(item.getAttribute('data-index'));
                showSlide(index);
                lightbox.classList.add('active');
                document.body.style.overflow = 'hidden';
            });
        });

        lightbox.querySelector('.sd-lb-close').addEventListener('click', () => {
            lightbox.classList.remove('active');
            document.body.style.overflow = '';
        });

        lightbox.querySelector('.sd-lb-prev').addEventListener('click', () => {
            if(currentIndex > 0) showSlide(currentIndex - 1);
            else showSlide(totalSlides - 1);
        });

        lightbox.querySelector('.sd-lb-next').addEventListener('click', () => {
            if(currentIndex < totalSlides - 1) showSlide(currentIndex + 1);
            else showSlide(0);
        });

        thumbs.forEach((thumb, index) => {
            thumb.addEventListener('click', () => showSlide(index));
        });

        // Swipe Drag Logic
        const dragStart = (e) => {
            isDragging = true;
            startPos = e.type.includes('mouse') ? e.pageX : e.touches[0].clientX;
            track.style.transition = 'none';
            animationID = requestAnimationFrame(animation);
        };

        const dragMove = (e) => {
            if (isDragging) {
                const currentPosition = e.type.includes('mouse') ? e.pageX : e.touches[0].clientX;
                const diff = currentPosition - startPos;
                const diffPercentage = (diff / track.clientWidth) * 100;
                currentTranslate = prevTranslate + diffPercentage;
            }
        };

        const dragEnd = () => {
            isDragging = false;
            cancelAnimationFrame(animationID);
            
            const movedBy = currentTranslate - prevTranslate;
            
            if (movedBy < -15 && currentIndex < totalSlides - 1) currentIndex += 1;
            if (movedBy > 15 && currentIndex > 0) currentIndex -= 1;
            
            updatePosition();
        };

        const animation = () => {
            track.style.transform = `translateX(${currentTranslate}%)`;
            if (isDragging) requestAnimationFrame(animation);
        };

        const mainDrag = document.getElementById('sd-lb-main-drag');
        mainDrag.addEventListener('touchstart', dragStart, {passive: true});
        mainDrag.addEventListener('touchmove', dragMove, {passive: true});
        mainDrag.addEventListener('touchend', dragEnd);
        
        mainDrag.addEventListener('mousedown', dragStart);
        mainDrag.addEventListener('mousemove', dragMove);
        mainDrag.addEventListener('mouseup', dragEnd);
        mainDrag.addEventListener('mouseleave', () => { if(isDragging) dragEnd() });
    }
});
</script>
@endpush

@endsection
