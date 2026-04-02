{{-- FOOTER --}}
<footer class="khanhbeauty-footer">
    <div class="khanhbeauty-footer__grid">
        <div class="khanhbeauty-footer__brand">
            <h3>{{ \App\Models\SiteSetting::getValue('site_name', 'Khánh Beauty') }}</h3>
            <p>Makeup Artist chuyên nghiệp — Nơi nghệ thuật gặp gỡ vẻ đẹp. Đồng hành cùng bạn trong mọi khoảnh khắc đáng nhớ.</p>
        </div>
        <div class="khanhbeauty-footer__col">
            <h4>Dịch vụ</h4>
            <ul>
                <li><a href="#">Makeup Cô Dâu</a></li>
                <li><a href="#">Makeup Sự Kiện</a></li>
                <li><a href="#">Makeup Chụp Ảnh</a></li>
                <li><a href="#">Đào Tạo Makeup</a></li>
            </ul>
        </div>
        <div class="khanhbeauty-footer__col">
            <h4>Liên kết</h4>
            <ul>
                <li><a href="{{ \App\Models\SiteSetting::getValue('facebook_url', '#') }}" target="_blank">Facebook</a></li>
                <li><a href="{{ \App\Models\SiteSetting::getValue('instagram_url', '#') }}" target="_blank">Instagram</a></li>
                <li><a href="{{ \App\Models\SiteSetting::getValue('tiktok_url', '#') }}" target="_blank">TikTok</a></li>
            </ul>
        </div>
        <div class="khanhbeauty-footer__col">
            <h4>Liên hệ</h4>
            <ul>
                <li><a href="tel:{{ str_replace('.', '', \App\Models\SiteSetting::getValue('phone', '0987.654.321')) }}">{{ \App\Models\SiteSetting::getValue('phone', '0987.654.321') }}</a></li>
                <li><a href="mailto:{{ \App\Models\SiteSetting::getValue('email', 'booking@khanhbeauty.com') }}">{{ \App\Models\SiteSetting::getValue('email', 'booking@khanhbeauty.com') }}</a></li>
                <li><a href="#">{{ \App\Models\SiteSetting::getValue('address', 'Hồ Chí Minh, Việt Nam') }}</a></li>
                <li style="color:var(--text-medium); font-size:12px; margin-top:5px">{{ \App\Models\SiteSetting::getValue('working_hours', '08:00 - 20:00') }}</li>
            </ul>
        </div>
    </div>
    <div class="khanhbeauty-footer__bottom">
        &copy; {{ date('Y') }} Khánh Beauty. Thiết kế với ♥ 
    </div>
</footer>

{{-- SCROLL TO TOP --}}
<button class="kb-scroll-top" id="scrollTopBtn" aria-label="Cuộn lên đầu trang">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="M18 15l-6-6-6 6"/>
    </svg>
</button>
