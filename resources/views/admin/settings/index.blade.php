@extends('admin.layouts.app')
@section('title', 'Cài Đặt Cấu Hình Hệ Thống')

@push('styles')
<style>
    .st-tabs { display: flex; gap: 4px; border-bottom: 2px solid #eee; margin-bottom: 25px; flex-wrap: wrap; }
    .st-tab { padding: 12px 22px; font-weight: 600; font-size: 14px; color: #888; border-bottom: 2px solid transparent; margin-bottom: -2px; transition: 0.2s; cursor: pointer; background: none; }
    .st-tab:hover { color: var(--primary); }
    .st-tab.active { color: var(--primary); border-bottom-color: var(--primary); }
    .st-section { display: none; }
    .st-section.active { display: block; }
    .st-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .st-help { font-size: 12px; color: #999; margin-top: 4px; }
    .st-img-preview { width: 120px; height: 80px; object-fit: contain; border-radius: 8px; border: 1px solid #eee; margin-top: 8px; background: #f9f9f9; }
    .st-favicon-preview { width: 32px; height: 32px; object-fit: contain; margin-top: 8px; }
    .st-divider { border: none; border-top: 1px solid #eee; margin: 25px 0; }
    @media (max-width: 768px) { .st-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" id="formSettings">
    @csrf

    <div class="kb-card">
        <div class="kb-card-header">
            <h3 class="kb-card-title">Cài Đặt Cấu Hình Hệ Thống</h3>
            <button type="submit" class="kb-btn kb-btn--primary">Lưu Thay Đổi</button>
        </div>

        <div class="st-tabs">
            <button type="button" class="st-tab active" data-tab="general">Thông Tin Chung</button>
            <button type="button" class="st-tab" data-tab="social">Mạng Xã Hội</button>
            <button type="button" class="st-tab" data-tab="seo">SEO & Meta</button>
        </div>

        <!-- ═══ TAB 1: THÔNG TIN CHUNG ═══ -->
        <div class="st-section active" id="tab-general">
            <div class="st-grid">
                <div class="kb-form-group">
                    <label>Tên website</label>
                    <input type="text" name="settings[site_name]" class="kb-form-control"
                           value="{{ \App\Models\SiteSetting::getValue('site_name', 'Khánh Beauty') }}">
                </div>
                <div class="kb-form-group">
                    <label>Slogan / Mô tả ngắn</label>
                    <input type="text" name="settings[site_tagline]" class="kb-form-control"
                           value="{{ \App\Models\SiteSetting::getValue('site_tagline', 'Makeup Artist chuyên nghiệp') }}">
                </div>
                <div class="kb-form-group">
                    <label>Số điện thoại liên hệ</label>
                    <input type="text" name="settings[phone]" class="kb-form-control"
                           value="{{ \App\Models\SiteSetting::getValue('phone') }}">
                </div>
                <div class="kb-form-group">
                    <label>Email hỗ trợ</label>
                    <input type="email" name="settings[email]" class="kb-form-control"
                           value="{{ \App\Models\SiteSetting::getValue('email') }}">
                </div>
                <div class="kb-form-group" style="grid-column: span 2;">
                    <label>Địa chỉ</label>
                    <input type="text" name="settings[address]" class="kb-form-control"
                           value="{{ \App\Models\SiteSetting::getValue('address') }}">
                </div>
                <div class="kb-form-group">
                    <label>Giờ hoạt động</label>
                    <input type="text" name="settings[working_hours]" class="kb-form-control"
                           value="{{ \App\Models\SiteSetting::getValue('working_hours', '08:00 - 20:00') }}">
                </div>
                <div class="kb-form-group">
                    <label>Số Zalo (nếu khác SĐT)</label>
                    <input type="text" name="settings[zalo_phone]" class="kb-form-control"
                           value="{{ \App\Models\SiteSetting::getValue('zalo_phone') }}">
                </div>

                <hr class="st-divider" style="grid-column: span 2;">

                <div class="kb-form-group">
                    <label>Favicon</label>
                    <input type="file" name="favicon" class="kb-form-control kb-form-file" accept="image/png,image/x-icon,image/svg+xml">
                    @error('favicon')<div style="color:red;font-size:12px;margin-top:4px">{{ $message }}</div>@enderror
                    <p class="st-help">PNG, ICO hoặc SVG. Kích thước khuyến nghị: 32x32 hoặc 64x64</p>
                    @php $faviconUrl = \App\Models\SiteSetting::getValue('favicon_url'); @endphp
                    @if($faviconUrl)
                        <img src="{{ $faviconUrl }}" class="st-favicon-preview" alt="Favicon">
                    @endif
                </div>
                <div class="kb-form-group">
                    <label>Logo website</label>
                    <input type="file" name="logo" class="kb-form-control kb-form-file" accept="image/*">
                    @error('logo')<div style="color:red;font-size:12px;margin-top:4px">{{ $message }}</div>@enderror
                    <p class="st-help">Ảnh logo hiển thị trên header (nếu dùng)</p>
                    @php $logoUrl = \App\Models\SiteSetting::getValue('logo_url'); @endphp
                    @if($logoUrl)
                        <img src="{{ $logoUrl }}" class="st-img-preview" alt="Logo">
                    @endif
                </div>
            </div>
        </div>

        <!-- ═══ TAB 2: MẠNG XÃ HỘI ═══ -->
        <div class="st-section" id="tab-social">
            <div class="st-grid">
                <div class="kb-form-group">
                    <label>Facebook (URL)</label>
                    <input type="url" name="settings[facebook_url]" class="kb-form-control"
                           value="{{ \App\Models\SiteSetting::getValue('facebook_url') }}"
                           placeholder="https://facebook.com/khanhbeauty">
                </div>
                <div class="kb-form-group">
                    <label>Instagram (URL)</label>
                    <input type="url" name="settings[instagram_url]" class="kb-form-control"
                           value="{{ \App\Models\SiteSetting::getValue('instagram_url') }}"
                           placeholder="https://instagram.com/khanhbeauty">
                </div>
                <div class="kb-form-group">
                    <label>TikTok (URL)</label>
                    <input type="url" name="settings[tiktok_url]" class="kb-form-control"
                           value="{{ \App\Models\SiteSetting::getValue('tiktok_url') }}"
                           placeholder="https://tiktok.com/@khanhbeauty">
                </div>
                <div class="kb-form-group">
                    <label>YouTube (URL)</label>
                    <input type="url" name="settings[youtube_url]" class="kb-form-control"
                           value="{{ \App\Models\SiteSetting::getValue('youtube_url') }}"
                           placeholder="https://youtube.com/@khanhbeauty">
                </div>
                <div class="kb-form-group" style="grid-column: span 2;">
                    <label>Zalo OA (URL)</label>
                    <input type="url" name="settings[zalo_url]" class="kb-form-control"
                           value="{{ \App\Models\SiteSetting::getValue('zalo_url') }}"
                           placeholder="https://zalo.me/khanhbeauty">
                </div>
            </div>
        </div>

        <!-- ═══ TAB 3: SEO & META ═══ -->
        <div class="st-section" id="tab-seo">
            <div class="st-grid">
                <div class="kb-form-group" style="grid-column: span 2;">
                    <label>Meta Title mặc định</label>
                    <input type="text" name="settings[meta_title]" class="kb-form-control"
                           value="{{ \App\Models\SiteSetting::getValue('meta_title', 'Khánh Beauty — Dịch Vụ Trang Điểm Chuyên Nghiệp Tại Nhà') }}">
                    <p class="st-help">Tiêu đề hiển thị trên tab trình duyệt và kết quả Google (50-60 ký tự tối ưu)</p>
                </div>
                <div class="kb-form-group" style="grid-column: span 2;">
                    <label>Meta Description mặc định</label>
                    <textarea name="settings[meta_description]" class="kb-form-control" rows="3">{{ \App\Models\SiteSetting::getValue('meta_description', 'Khám phá dịch vụ makeup chuyên nghiệp tại Khánh Beauty. Trang điểm dự tiệc, cô dâu, sự kiện với phong cách tự nhiên, sang trọng ngay tại không gian của bạn.') }}</textarea>
                    <p class="st-help">Mô tả hiển thị trên kết quả Google (150-160 ký tự tối ưu)</p>
                </div>
                <div class="kb-form-group" style="grid-column: span 2;">
                    <label>Meta Keywords mặc định</label>
                    <input type="text" name="settings[meta_keywords]" class="kb-form-control"
                           value="{{ \App\Models\SiteSetting::getValue('meta_keywords', 'makeup, trang điểm, cô dâu, makeup tại nhà, khánh beauty') }}">
                    <p class="st-help">Từ khoá cách nhau bằng dấu phẩy</p>
                </div>

                <hr class="st-divider" style="grid-column: span 2;">

                <div class="kb-form-group">
                    <label>Ảnh OG mặc định (Open Graph)</label>
                    <input type="file" name="og_image" class="kb-form-control kb-form-file" accept="image/*">
                    @error('og_image')<div style="color:red;font-size:12px;margin-top:4px">{{ $message }}</div>@enderror
                    <p class="st-help">Ảnh hiển thị khi chia sẻ link lên Facebook/Zalo. Khuyến nghị: 1200x630px</p>
                    @php $ogUrl = \App\Models\SiteSetting::getValue('og_image_url'); @endphp
                    @if($ogUrl)
                        <img src="{{ $ogUrl }}" class="st-img-preview" alt="OG Image">
                    @endif
                </div>
                <div class="kb-form-group">
                    <label>Canonical URL</label>
                    <input type="url" name="settings[canonical_url]" class="kb-form-control"
                           value="{{ \App\Models\SiteSetting::getValue('canonical_url') }}"
                           placeholder="https://khanhbeauty.com">
                    <p class="st-help">URL chính thức của website (cho SEO)</p>
                </div>

                <hr class="st-divider" style="grid-column: span 2;">
                <h4 style="grid-column: span 2; font-weight: 600; margin-bottom: -10px;">Tracking & Xác minh</h4>

                <div class="kb-form-group">
                    <label>Google Analytics ID</label>
                    <input type="text" name="settings[google_analytics_id]" class="kb-form-control"
                           value="{{ \App\Models\SiteSetting::getValue('google_analytics_id') }}"
                           placeholder="G-XXXXXXXXXX">
                </div>
                <div class="kb-form-group">
                    <label>Google Search Console (verification)</label>
                    <input type="text" name="settings[google_site_verification]" class="kb-form-control"
                           value="{{ \App\Models\SiteSetting::getValue('google_site_verification') }}"
                           placeholder="meta tag content value">
                </div>
                <div class="kb-form-group">
                    <label>Facebook Pixel ID</label>
                    <input type="text" name="settings[facebook_pixel_id]" class="kb-form-control"
                           value="{{ \App\Models\SiteSetting::getValue('facebook_pixel_id') }}"
                           placeholder="123456789012345">
                </div>
                <div class="kb-form-group">
                    <label>Robots Meta</label>
                    <select name="settings[robots_meta]" class="kb-form-control">
                        @php $robots = \App\Models\SiteSetting::getValue('robots_meta', 'index, follow'); @endphp
                        <option value="index, follow" {{ $robots === 'index, follow' ? 'selected' : '' }}>index, follow (mặc định)</option>
                        <option value="noindex, follow" {{ $robots === 'noindex, follow' ? 'selected' : '' }}>noindex, follow</option>
                        <option value="index, nofollow" {{ $robots === 'index, nofollow' ? 'selected' : '' }}>index, nofollow</option>
                        <option value="noindex, nofollow" {{ $robots === 'noindex, nofollow' ? 'selected' : '' }}>noindex, nofollow</option>
                    </select>
                    <p class="st-help">Chỉ thị cho bot Google. Chọn "noindex" khi chưa muốn xuất hiện trên Google</p>
                </div>
            </div>
        </div>
    </div>

    <div style="text-align: right; margin-top: 20px;">
        <button type="submit" class="kb-btn kb-btn--primary" style="padding: 14px 40px; font-size: 15px;">Lưu Thay Đổi</button>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.st-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.st-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.st-section').forEach(s => s.classList.remove('active'));
        this.classList.add('active');
        document.getElementById('tab-' + this.dataset.tab).classList.add('active');
    });
});
</script>
@endpush
