@extends('admin.layouts.app')
@section('title', 'Quản Lý Trang Chủ')

@push('styles')
<style>
    .hp-tabs { display: flex; gap: 4px; border-bottom: 2px solid #eee; margin-bottom: 25px; flex-wrap: wrap; }
    .hp-tab { padding: 12px 22px; font-weight: 600; font-size: 14px; color: #888; border-bottom: 2px solid transparent; margin-bottom: -2px; transition: 0.2s; cursor: pointer; background: none; }
    .hp-tab:hover { color: var(--primary); }
    .hp-tab.active { color: var(--primary); border-bottom-color: var(--primary); }
    .hp-section { display: none; }
    .hp-section.active { display: block; }
    .hp-repeater { border: 1px solid #eee; border-radius: 10px; padding: 20px; margin-bottom: 15px; background: #fafafa; position: relative; }
    .hp-repeater__header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
    .hp-repeater__title { font-weight: 600; font-size: 15px; color: var(--text-main); }
    .hp-repeater__remove { color: #ef4444; font-size: 13px; font-weight: 600; padding: 4px 12px; border-radius: 6px; background: #fee2e2; }
    .hp-img-preview { width: 120px; height: 90px; object-fit: cover; border-radius: 8px; border: 1px solid #eee; margin-top: 8px; }
    .hp-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .hp-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; }
    .hp-grid-4 { display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 15px; }
    .hp-add-btn { display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px; background: #f0fdf4; color: #16a34a; font-weight: 600; font-size: 14px; border-radius: 8px; margin-top: 10px; }
    .hp-add-btn:hover { background: #dcfce7; }
    textarea.kb-form-control { min-height: 80px; resize: vertical; }
    .hp-help { font-size: 12px; color: #999; margin-top: 4px; }
    @media (max-width: 768px) {
        .hp-grid-2, .hp-grid-3, .hp-grid-4 { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<form method="POST" action="{{ route('admin.homepage.update') }}" enctype="multipart/form-data" id="homepageForm">
    @csrf

    <div class="kb-card">
        <div class="kb-card-header">
            <h3 class="kb-card-title">Quản Lý Nội Dung Trang Chủ</h3>
            <button type="submit" class="kb-btn kb-btn--primary">Lưu Tất Cả</button>
        </div>

        <!-- TABS -->
        <div class="hp-tabs">
            <button type="button" class="hp-tab active" data-tab="hero">Hero Banner</button>
            <button type="button" class="hp-tab" data-tab="about">Giới Thiệu</button>
            <button type="button" class="hp-tab" data-tab="showcase">Kỹ Năng</button>
            <button type="button" class="hp-tab" data-tab="testimonials">Đánh Giá</button>
            <button type="button" class="hp-tab" data-tab="numbers">Số Liệu</button>
        </div>

        <!-- ═══ TAB 1: HERO ═══ -->
        <div class="hp-section active" id="tab-hero">
            <div class="hp-grid-2">
                <div class="kb-form-group">
                    <label>Badge (dòng nhỏ phía trên)</label>
                    <input type="text" name="settings[hero_badge]" class="kb-form-control"
                           value="{{ \App\Models\SiteSetting::getValue('hero_badge', '✦ Professional Makeup Artist ✦') }}">
                </div>
                <div class="kb-form-group">
                    <label>Tiêu đề chính</label>
                    <input type="text" name="settings[hero_title]" class="kb-form-control"
                           value="{{ \App\Models\SiteSetting::getValue('hero_title', 'Khánh Beauty') }}">
                </div>
                <div class="kb-form-group" style="grid-column: span 2;">
                    <label>Phụ đề</label>
                    <input type="text" name="settings[hero_subtitle]" class="kb-form-control"
                           value="{{ \App\Models\SiteSetting::getValue('hero_subtitle', 'Nghệ thuật trang điểm — Tôn vinh vẻ đẹp của bạn') }}">
                </div>
                <div class="kb-form-group">
                    <label>Nút CTA chính</label>
                    <input type="text" name="settings[hero_cta_primary]" class="kb-form-control"
                           value="{{ \App\Models\SiteSetting::getValue('hero_cta_primary', 'Đặt Lịch Makeup') }}">
                </div>
                <div class="kb-form-group">
                    <label>Nút CTA phụ</label>
                    <input type="text" name="settings[hero_cta_secondary]" class="kb-form-control"
                           value="{{ \App\Models\SiteSetting::getValue('hero_cta_secondary', 'Xem Portfolio') }}">
                </div>
            </div>
        </div>

        <!-- ═══ TAB 2: ABOUT ═══ -->
        <div class="hp-section" id="tab-about">
            <div class="hp-grid-2">
                <div class="kb-form-group">
                    <label>Nhãn section</label>
                    <input type="text" name="settings[about_label]" class="kb-form-control"
                           value="{{ \App\Models\SiteSetting::getValue('about_label', 'Về tôi') }}">
                </div>
                <div class="kb-form-group">
                    <label>Tiêu đề</label>
                    <input type="text" name="settings[about_title]" class="kb-form-control"
                           value="{{ \App\Models\SiteSetting::getValue('about_title', 'Xin chào, mình là Khánh — người sẽ giúp bạn toả sáng') }}">
                    <p class="hp-help">Dùng &lt;em&gt;text&lt;/em&gt; để in nghiêng</p>
                </div>
                <div class="kb-form-group" style="grid-column: span 2;">
                    <label>Đoạn mô tả 1</label>
                    <textarea name="settings[about_desc_1]" class="kb-form-control">{{ \App\Models\SiteSetting::getValue('about_desc_1', 'Với niềm đam mê trang điểm từ nhỏ, mình đã dành nhiều năm học hỏi và trau dồi kỹ năng để mang đến cho mỗi khách hàng một diện mạo hoàn hảo nhất. Từ cô dâu trong ngày trọng đại, đến các bạn sinh viên muốn tự tin hơn — mình luôn lắng nghe và thấu hiểu.') }}</textarea>
                </div>
                <div class="kb-form-group" style="grid-column: span 2;">
                    <label>Đoạn mô tả 2</label>
                    <textarea name="settings[about_desc_2]" class="kb-form-control">{{ \App\Models\SiteSetting::getValue('about_desc_2', 'Không chỉ là makeup, mình muốn mỗi lần ngồi trước gương cùng bạn là một trải nghiệm vui vẻ, thoải mái và đáng nhớ.') }}</textarea>
                </div>
                <div class="kb-form-group">
                    <label>Ảnh giới thiệu</label>
                    <input type="file" name="about_image" class="kb-form-control kb-form-file" accept="image/*">
                    @error('about_image')<div style="color:red;font-size:12px;margin-top:4px">{{ $message }}</div>@enderror
                    @php $aboutImgUrl = \App\Services\MediaUploadService::url((int) \App\Models\SiteSetting::getValue('about_image_id')); @endphp
                    @if($aboutImgUrl)
                        <img src="{{ $aboutImgUrl }}" class="hp-img-preview" alt="About">
                    @else
                        <img src="/images/clients/about.png" class="hp-img-preview" alt="About (mặc định)">
                    @endif
                </div>
            </div>

            <h4 style="margin: 25px 0 15px; font-weight: 600;">Thống kê (hiển thị bên phải ảnh)</h4>
            @php
                $aboutStats = json_decode(\App\Models\SiteSetting::getValue('about_stats', '[]'), true) ?: [
                    ['value' => '500', 'label' => 'Khách hàng'],
                    ['value' => '200', 'label' => 'Cô dâu'],
                    ['value' => '50', 'label' => 'Học viên'],
                ];
            @endphp
            <div id="aboutStatsContainer">
                @foreach($aboutStats as $i => $stat)
                <div class="hp-repeater">
                    <div class="hp-repeater__header">
                        <span class="hp-repeater__title">Thống kê #{{ $i + 1 }}</span>
                        <button type="button" class="hp-repeater__remove" onclick="this.closest('.hp-repeater').remove()">Xoá</button>
                    </div>
                    <div class="hp-grid-2">
                        <div class="kb-form-group">
                            <label>Số</label>
                            <input type="text" name="about_stats[{{ $i }}][value]" class="kb-form-control" value="{{ $stat['value'] }}">
                        </div>
                        <div class="kb-form-group">
                            <label>Nhãn</label>
                            <input type="text" name="about_stats[{{ $i }}][label]" class="kb-form-control" value="{{ $stat['label'] }}">
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <button type="button" class="hp-add-btn" onclick="addAboutStat()">+ Thêm thống kê</button>
        </div>

        <!-- ═══ TAB 3: SHOWCASE ═══ -->
        <div class="hp-section" id="tab-showcase">
            <div class="hp-grid-2" style="margin-bottom: 20px;">
                <div class="kb-form-group">
                    <label>Nhãn section</label>
                    <input type="text" name="settings[showcase_label]" class="kb-form-control"
                           value="{{ \App\Models\SiteSetting::getValue('showcase_label', 'Kỹ năng thực chiến') }}">
                </div>
                <div class="kb-form-group">
                    <label>Tiêu đề section</label>
                    <input type="text" name="settings[showcase_title]" class="kb-form-control"
                           value="{{ \App\Models\SiteSetting::getValue('showcase_title', 'Từng đường nét, từng chi tiết — đều là nghệ thuật') }}">
                </div>
            </div>

            @php
                $showcaseItems = json_decode(\App\Models\SiteSetting::getValue('showcase_items', '[]'), true) ?: [
                    ['image_url' => '/images/clients/service-bridal.png', 'tag' => '✦ Makeup Cô Dâu', 'step' => '01', 'title' => 'Kẻ Mắt — Đôi mắt kể câu chuyện', 'description' => 'Mỗi đôi mắt có một hình dáng riêng. Mình không áp dụng công thức chung cho tất cả — mà sẽ phân tích dáng mắt, hốc mắt để tạo đường kẻ eyeliner phù hợp nhất. Từ cat-eye sắc sảo đến puppy-eye ngọt ngào.', 'skills' => ['Cat Eye', 'Puppy Eye', 'Smoky Eye', 'Cut Crease']],
                    ['image_url' => '/images/clients/service-event.png', 'tag' => '✦ Makeup Sự Kiện', 'step' => '02', 'title' => 'Đánh Son — Nụ cười thêm rạng rỡ', 'description' => 'Son không chỉ là tô màu lên môi. Mình sẽ chọn tông son phù hợp tông da, bối cảnh, trang phục. Kỹ thuật ombre lips, gradient lips hay full lips — tất cả đều được thực hiện tỉ mỉ từng lớp.', 'skills' => ['Ombre Lips', 'Gradient Lips', 'Full Lips', 'Overlining']],
                    ['image_url' => '/images/clients/service-class.png', 'tag' => '✦ Đào tạo Makeup', 'step' => '03', 'title' => 'Contour & Highlight — Gương mặt 3D tự nhiên', 'description' => 'Contour đúng cách không phải để "fake" mà để tôn vinh đường nét sẵn có. Mình sử dụng kỹ thuật blending chuyên sâu, kết hợp highlight tinh tế để gương mặt bạn sáng bừng dưới mọi ánh sáng.', 'skills' => ['Soft Contour', 'Baking', 'Strobing', 'Glass Skin']],
                ];
            @endphp
            <div id="showcaseContainer">
                @foreach($showcaseItems as $i => $item)
                <div class="hp-repeater">
                    <div class="hp-repeater__header">
                        <span class="hp-repeater__title">Kỹ năng #{{ $i + 1 }}</span>
                        <button type="button" class="hp-repeater__remove" onclick="this.closest('.hp-repeater').remove()">Xoá</button>
                    </div>
                    <div class="hp-grid-2">
                        <div class="kb-form-group">
                            <label>Tiêu đề</label>
                            <input type="text" name="showcase[{{ $i }}][title]" class="kb-form-control" value="{{ $item['title'] }}">
                        </div>
                        <div class="kb-form-group">
                            <label>Tag (ví dụ: ✦ Makeup Cô Dàu)</label>
                            <input type="text" name="showcase[{{ $i }}][tag]" class="kb-form-control" value="{{ $item['tag'] }}">
                        </div>
                        <div class="kb-form-group">
                            <label>Số thứ tự</label>
                            <input type="text" name="showcase[{{ $i }}][step]" class="kb-form-control" value="{{ $item['step'] }}">
                        </div>
                        <div class="kb-form-group">
                            <label>Skill tags (cách nhau bằng dấu phẩy)</label>
                            <input type="text" name="showcase[{{ $i }}][skills]" class="kb-form-control"
                                   value="{{ is_array($item['skills']) ? implode(', ', $item['skills']) : $item['skills'] }}">
                        </div>
                        <div class="kb-form-group" style="grid-column: span 2;">
                            <label>Mô tả</label>
                            <textarea name="showcase[{{ $i }}][description]" class="kb-form-control">{{ $item['description'] }}</textarea>
                        </div>
                        <div class="kb-form-group">
                            <label>Ảnh minh hoạ</label>
                            <input type="file" name="showcase[{{ $i }}][image]" class="kb-form-control kb-form-file" accept="image/*">
                            @error("showcase.$i.image")<div style="color:red;font-size:12px;margin-top:4px">{{ $message }}</div>@enderror
                            <input type="hidden" name="showcase[{{ $i }}][current_image]" value="{{ $item['image_url'] }}">
                            @if($item['image_url'])
                                <img src="{{ $item['image_url'] }}" class="hp-img-preview" alt="Showcase">
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <button type="button" class="hp-add-btn" onclick="addShowcase()">+ Thêm kỹ năng</button>
        </div>

        <!-- ═══ TAB 4: TESTIMONIALS ═══ -->
        <div class="hp-section" id="tab-testimonials">
            <div class="hp-grid-2" style="margin-bottom: 20px;">
                <div class="kb-form-group">
                    <label>Nhãn section</label>
                    <input type="text" name="settings[testimonials_label]" class="kb-form-control"
                           value="{{ \App\Models\SiteSetting::getValue('testimonials_label', 'Khách hàng nói gì') }}">
                </div>
                <div class="kb-form-group">
                    <label>Tiêu đề section</label>
                    <input type="text" name="settings[testimonials_title]" class="kb-form-control"
                           value="{{ \App\Models\SiteSetting::getValue('testimonials_title', 'Những lời yêu thương mình nhận được') }}">
                </div>
            </div>

            @php
                $testimonials = json_decode(\App\Models\SiteSetting::getValue('testimonials', '[]'), true) ?: [
                    ['stars' => 5, 'text' => 'Khánh makeup cho mình ngày cưới, ai cũng khen đẹp tự nhiên mà vẫn rạng rỡ. Quan trọng nhất là lớp makeup trụ được cả ngày dài, không bị chảy hay xuống tone!', 'name' => 'Minh Anh', 'role' => 'Cô dâu — Hà Nội', 'avatar' => 'MA'],
                    ['stars' => 5, 'text' => 'Mình book Khánh cho buổi chụp kỷ yếu cả lớp. Bạn ấy makeup nhanh, đẹp mà giá cả hợp lý lắm. Cả lớp ai cũng xinh, ảnh lên lung linh luôn!', 'name' => 'Thuỳ Linh', 'role' => 'Sinh viên — Đà Nẵng', 'avatar' => 'TL'],
                    ['stars' => 5, 'text' => 'Lần đầu book makeup online mà gặp được Khánh là may mắn. Bạn ấy rất tận tâm, lắng nghe mình muốn gì và tư vấn rất nhiệt tình. Chắc chắn sẽ quay lại!', 'name' => 'Chị Hương', 'role' => 'Khách book online — Sài Gòn', 'avatar' => 'H'],
                    ['stars' => 5, 'text' => 'Mình học khoá makeup cá nhân với Khánh. Giờ mình tự tin trang điểm đi làm mỗi ngày rồi. Cách dạy dễ hiểu, thực hành nhiều, rất phù hợp cho người mới.', 'name' => 'Lan Ngọc', 'role' => 'Học viên — Hà Nội', 'avatar' => 'LN'],
                ];
            @endphp
            <div id="testimonialsContainer">
                @foreach($testimonials as $i => $t)
                <div class="hp-repeater">
                    <div class="hp-repeater__header">
                        <span class="hp-repeater__title">Đánh giá #{{ $i + 1 }}</span>
                        <button type="button" class="hp-repeater__remove" onclick="this.closest('.hp-repeater').remove()">Xoá</button>
                    </div>
                    <div class="hp-grid-2">
                        <div class="kb-form-group">
                            <label>Tên khách hàng</label>
                            <input type="text" name="testimonials[{{ $i }}][name]" class="kb-form-control" value="{{ $t['name'] }}">
                        </div>
                        <div class="kb-form-group">
                            <label>Vai trò / Địa điểm</label>
                            <input type="text" name="testimonials[{{ $i }}][role]" class="kb-form-control" value="{{ $t['role'] }}">
                        </div>
                        <div class="kb-form-group">
                            <label>Avatar (viết tắt tên)</label>
                            <input type="text" name="testimonials[{{ $i }}][avatar]" class="kb-form-control" value="{{ $t['avatar'] }}">
                        </div>
                        <div class="kb-form-group">
                            <label>Số sao (1-5)</label>
                            <select name="testimonials[{{ $i }}][stars]" class="kb-form-control">
                                @for($s = 5; $s >= 1; $s--)
                                    <option value="{{ $s }}" {{ ($t['stars'] ?? 5) == $s ? 'selected' : '' }}>{{ $s }} sao</option>
                                @endfor
                            </select>
                        </div>
                        <div class="kb-form-group" style="grid-column: span 2;">
                            <label>Nội dung đánh giá</label>
                            <textarea name="testimonials[{{ $i }}][text]" class="kb-form-control" rows="3">{{ $t['text'] }}</textarea>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <button type="button" class="hp-add-btn" onclick="addTestimonial()">+ Thêm đánh giá</button>
        </div>

        <!-- ═══ TAB 5: NUMBERS ═══ -->
        <div class="hp-section" id="tab-numbers">
            @php
                $numbers = json_decode(\App\Models\SiteSetting::getValue('numbers_stats', '[]'), true) ?: [
                    ['value' => '500', 'label' => 'Khách hàng hài lòng'],
                    ['value' => '200', 'label' => 'Cô dàu xinh đẹp'],
                    ['value' => '5', 'label' => 'Năm kinh nghiệm'],
                    ['value' => '50', 'label' => 'Học viên đào tạo'],
                ];
            @endphp
            <div id="numbersContainer">
                @foreach($numbers as $i => $num)
                <div class="hp-repeater">
                    <div class="hp-repeater__header">
                        <span class="hp-repeater__title">Số liệu #{{ $i + 1 }}</span>
                        <button type="button" class="hp-repeater__remove" onclick="this.closest('.hp-repeater').remove()">Xoá</button>
                    </div>
                    <div class="hp-grid-2">
                        <div class="kb-form-group">
                            <label>Giá trị số</label>
                            <input type="text" name="numbers[{{ $i }}][value]" class="kb-form-control" value="{{ $num['value'] }}">
                        </div>
                        <div class="kb-form-group">
                            <label>Nhãn</label>
                            <input type="text" name="numbers[{{ $i }}][label]" class="kb-form-control" value="{{ $num['label'] }}">
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <button type="button" class="hp-add-btn" onclick="addNumber()">+ Thêm số liệu</button>
        </div>

    </div>

    <div style="text-align: right; margin-top: 20px;">
        <button type="submit" class="kb-btn kb-btn--primary" style="padding: 14px 40px; font-size: 15px;">Lưu Tất Cả Thay Đổi</button>
    </div>
</form>
@endsection

@push('scripts')
<script>
// ─── Tab switching ───
document.querySelectorAll('.hp-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.hp-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.hp-section').forEach(s => s.classList.remove('active'));
        this.classList.add('active');
        document.getElementById('tab-' + this.dataset.tab).classList.add('active');
    });
});

// ─── Dynamic repeater helpers ───
let aboutStatIdx = {{ count($aboutStats) }};
function addAboutStat() {
    const html = `<div class="hp-repeater">
        <div class="hp-repeater__header">
            <span class="hp-repeater__title">Thống kê mới</span>
            <button type="button" class="hp-repeater__remove" onclick="this.closest('.hp-repeater').remove()">Xoá</button>
        </div>
        <div class="hp-grid-2">
            <div class="kb-form-group"><label>Số</label><input type="text" name="about_stats[${aboutStatIdx}][value]" class="kb-form-control"></div>
            <div class="kb-form-group"><label>Nhãn</label><input type="text" name="about_stats[${aboutStatIdx}][label]" class="kb-form-control"></div>
        </div>
    </div>`;
    document.getElementById('aboutStatsContainer').insertAdjacentHTML('beforeend', html);
    aboutStatIdx++;
}

let showcaseIdx = {{ count($showcaseItems) }};
function addShowcase() {
    const step = String(showcaseIdx + 1).padStart(2, '0');
    const html = `<div class="hp-repeater">
        <div class="hp-repeater__header">
            <span class="hp-repeater__title">Kỹ năng #${showcaseIdx + 1}</span>
            <button type="button" class="hp-repeater__remove" onclick="this.closest('.hp-repeater').remove()">Xoá</button>
        </div>
        <div class="hp-grid-2">
            <div class="kb-form-group"><label>Tiêu đề</label><input type="text" name="showcase[${showcaseIdx}][title]" class="kb-form-control"></div>
            <div class="kb-form-group"><label>Tag</label><input type="text" name="showcase[${showcaseIdx}][tag]" class="kb-form-control"></div>
            <div class="kb-form-group"><label>Số thứ tự</label><input type="text" name="showcase[${showcaseIdx}][step]" class="kb-form-control" value="${step}"></div>
            <div class="kb-form-group"><label>Skill tags (cách nhau bằng dấu phẩy)</label><input type="text" name="showcase[${showcaseIdx}][skills]" class="kb-form-control"></div>
            <div class="kb-form-group" style="grid-column: span 2;"><label>Mô tả</label><textarea name="showcase[${showcaseIdx}][description]" class="kb-form-control"></textarea></div>
            <div class="kb-form-group"><label>Ảnh minh hoạ</label><input type="file" name="showcase[${showcaseIdx}][image]" class="kb-form-control kb-form-file" accept="image/*"><input type="hidden" name="showcase[${showcaseIdx}][current_image]" value=""></div>
        </div>
    </div>`;
    document.getElementById('showcaseContainer').insertAdjacentHTML('beforeend', html);
    showcaseIdx++;
}

let testimonialIdx = {{ count($testimonials) }};
function addTestimonial() {
    const html = `<div class="hp-repeater">
        <div class="hp-repeater__header">
            <span class="hp-repeater__title">Đánh giá #${testimonialIdx + 1}</span>
            <button type="button" class="hp-repeater__remove" onclick="this.closest('.hp-repeater').remove()">Xoá</button>
        </div>
        <div class="hp-grid-2">
            <div class="kb-form-group"><label>Tên khách hàng</label><input type="text" name="testimonials[${testimonialIdx}][name]" class="kb-form-control"></div>
            <div class="kb-form-group"><label>Vai trò / Địa điểm</label><input type="text" name="testimonials[${testimonialIdx}][role]" class="kb-form-control"></div>
            <div class="kb-form-group"><label>Avatar (viết tắt tên)</label><input type="text" name="testimonials[${testimonialIdx}][avatar]" class="kb-form-control"></div>
            <div class="kb-form-group"><label>Số sao (1-5)</label>
                <select name="testimonials[${testimonialIdx}][stars]" class="kb-form-control">
                    <option value="5">5 sao</option><option value="4">4 sao</option><option value="3">3 sao</option><option value="2">2 sao</option><option value="1">1 sao</option>
                </select>
            </div>
            <div class="kb-form-group" style="grid-column: span 2;"><label>Nội dung đánh giá</label><textarea name="testimonials[${testimonialIdx}][text]" class="kb-form-control" rows="3"></textarea></div>
        </div>
    </div>`;
    document.getElementById('testimonialsContainer').insertAdjacentHTML('beforeend', html);
    testimonialIdx++;
}

let numberIdx = {{ count($numbers) }};
function addNumber() {
    const html = `<div class="hp-repeater">
        <div class="hp-repeater__header">
            <span class="hp-repeater__title">Số liệu #${numberIdx + 1}</span>
            <button type="button" class="hp-repeater__remove" onclick="this.closest('.hp-repeater').remove()">Xoá</button>
        </div>
        <div class="hp-grid-2">
            <div class="kb-form-group"><label>Giá trị số</label><input type="text" name="numbers[${numberIdx}][value]" class="kb-form-control"></div>
            <div class="kb-form-group"><label>Nhãn</label><input type="text" name="numbers[${numberIdx}][label]" class="kb-form-control"></div>
        </div>
    </div>`;
    document.getElementById('numbersContainer').insertAdjacentHTML('beforeend', html);
    numberIdx++;
}
</script>
@endpush
