@extends('admin.layouts.app')
@section('title', 'Thêm Dịch Vụ Mới')

@section('content')

<style>
.tab-nav { display:flex; gap:4px; margin-bottom:0; border-bottom:2px solid #f0f0f0; }
.tab-btn {
    padding:11px 22px; font-size:13px; font-weight:600; border:none; background:transparent;
    color:#888; cursor:pointer; border-bottom:2px solid transparent; margin-bottom:-2px;
    transition:all 0.2s;
}
.tab-btn.active { color:var(--primary); border-color:var(--primary); }
.tab-pane { display:none; padding-top:28px; }
.tab-pane.active { display:block; }
.field-group { display:grid; grid-template-columns:1fr 1fr; gap:20px; }
.span-2 { grid-column: span 2; }
.card-section {
    background:#fafafa; border:1px solid #f0effb; border-radius:16px;
    padding:22px; margin-bottom:22px;
}
.card-section-title {
    font-family:'Playfair Display',serif; font-size:15px; font-weight:600;
    margin:0 0 16px; display:flex; align-items:center; gap:10px;
}
.repeater-row {
    display:flex; gap:10px; align-items:flex-start;
    padding:12px; background:#fff; border-radius:10px;
    border:1px solid #eeeff5; margin-bottom:10px;
    animation: fadeInRow 0.25s ease;
}
@keyframes fadeInRow { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:translateY(0)} }
.repeater-row:last-child { margin-bottom:0; }
.btn-remove { background:none; border:none; color:#ff4d4f; cursor:pointer; padding:6px; font-size:16px; flex-shrink:0; }
.variant-row {
    display:grid; grid-template-columns:2fr 1fr 1fr 1fr 60px; gap:10px; align-items:center;
    padding:12px; background:#fff; border-radius:10px; border:1px solid #eeeff5; margin-bottom:10px;
    animation: fadeInRow 0.25s ease;
}
.hint { font-size:11px; color:#aaa; margin-top:4px; }
</style>

<div style="max-width:1000px;">
    <div class="kb-card">
        <div class="kb-card-header">
            <h3 class="kb-card-title">Thêm Dịch Vụ Mới</h3>
            <a href="{{ route('admin.services.index') }}" class="kb-btn kb-btn--sm" style="background:#f0f0f0;">← Quay lại</a>
        </div>

        <form method="POST" action="{{ route('admin.services.store') }}" enctype="multipart/form-data" id="serviceForm">
            @csrf

            {{-- TABS --}}
            <div class="tab-nav">
                <button type="button" class="tab-btn active" data-tab="tab-basic">📋 Thông tin cơ bản</button>
                <button type="button" class="tab-btn" data-tab="tab-media">🖼 Hình ảnh & Media</button>
                <button type="button" class="tab-btn" data-tab="tab-content">✍️ Nội dung Landing Page</button>
                <button type="button" class="tab-btn" data-tab="tab-variants">📦 Gói dịch vụ</button>
                <button type="button" class="tab-btn" data-tab="tab-faq">❓ Câu hỏi FAQ</button>
                <button type="button" class="tab-btn" data-tab="tab-seo">⚙️ SEO</button>
            </div>

            {{-- TAB 1: BASIC --}}
            <div class="tab-pane active" id="tab-basic">
                <div class="field-group">
                    <div class="kb-form-group">
                        <label>Tên dịch vụ <span style="color:red">*</span></label>
                        <input type="text" name="name" class="kb-form-control" value="{{ old('name') }}" placeholder="VD: Trang Điểm Cô Dâu Cao Cấp" required id="nameInput">
                        @error('name')<div style="color:red;font-size:12px;margin-top:4px">{{ $message }}</div>@enderror
                    </div>
                    <div class="kb-form-group">
                        <label>Đường dẫn (Slug)</label>
                        <input type="text" name="slug" class="kb-form-control" value="{{ old('slug') }}" placeholder="trang-diem-co-dau" id="slugInput">
                        <div class="hint">Để trống sẽ tự tạo từ tên</div>
                    </div>
                    <div class="kb-form-group">
                        <label>Danh mục <span style="color:red">*</span></label>
                        <select name="category_id" class="kb-form-control" required>
                            <option value="">-- Chọn danh mục --</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')<div style="color:red;font-size:12px;margin-top:4px">{{ $message }}</div>@enderror
                    </div>
                    <div class="kb-form-group">
                        <label>Thứ tự hiển thị</label>
                        <input type="number" name="sort_order" class="kb-form-control" value="{{ old('sort_order', 0) }}" min="0">
                        <div class="hint">Số nhỏ hơn hiển thị trước</div>
                    </div>

                    {{-- PRICE --}}
                    <div class="kb-form-group">
                        <label>Giá gốc (VNĐ) <span style="color:red">*</span></label>
                        <div style="display:flex;gap:10px">
                            <input type="number" name="price" class="kb-form-control" value="{{ old('price') }}" placeholder="1500000" min="0" required style="flex:2">
                            <input type="text" name="price_unit" class="kb-form-control" value="{{ old('price_unit','buổi') }}" placeholder="buổi" style="flex:1" list="unitList">
                            <datalist id="unitList">
                                <option value="buổi"><option value="khóa"><option value="người"><option value="lượt"><option value="ngày">
                            </datalist>
                        </div>
                        @error('price')<div style="color:red;font-size:12px;margin-top:4px">{{ $message }}</div>@enderror
                    </div>
                    <div class="kb-form-group">
                        <label>Giá khuyến mãi (VNĐ)</label>
                        <input type="number" name="sale_price" class="kb-form-control" value="{{ old('sale_price') }}" placeholder="Để trống nếu không KM" min="0">
                    </div>
                    <div class="kb-form-group">
                        <label>Thời gian thực hiện (phút)</label>
                        <input type="number" name="duration_minutes" class="kb-form-control" value="{{ old('duration_minutes', 60) }}" min="0">
                    </div>
                    <div class="kb-form-group" style="display:flex;flex-direction:column;gap:12px;justify-content:center">
                        <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-weight:600">
                            <input type="checkbox" name="is_active" value="1" checked style="width:18px;height:18px;accent-color:var(--primary)">
                            Đang hiển thị (Bật)
                        </label>
                        <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-weight:600">
                            <input type="checkbox" name="is_featured" value="1" style="width:18px;height:18px;accent-color:var(--primary)">
                            Dịch vụ nổi bật (Featured)
                        </label>
                    </div>

                    <div class="kb-form-group span-2">
                        <label>Mô tả ngắn (1-2 câu, hiển thị ở thẻ dịch vụ)</label>
                        <input type="text" name="short_description" class="kb-form-control" value="{{ old('short_description') }}" placeholder="Trang điểm bền đẹp suốt 12 giờ, mang lại vẻ rạng rỡ hoàn hảo cho ngày trọng đại.">
                    </div>
                </div>
            </div>

            {{-- TAB 2: MEDIA --}}
            <div class="tab-pane" id="tab-media">
                <div class="field-group">
                    <div class="kb-form-group span-2">
                        <label>Ảnh đại diện chính <span style="color:red">*</span></label>
                        <input type="file" name="featured_image" class="kb-form-control kb-form-file" accept="image/*" id="imgPreviewInput">
                        @error('featured_image')<div style="color:red;font-size:12px;margin-top:4px">{{ $message }}</div>@enderror
                        <div id="imgPreview" style="margin-top:10px;display:none">
                            <img id="imgPreviewEl" src="" style="max-width:220px;border-radius:10px;">
                        </div>
                    </div>

                    <div class="kb-form-group span-2">
                        <label>Bộ ảnh Gallery (nhiều ảnh)</label>
                        <input type="file" name="gallery_files[]" class="kb-form-control kb-form-file" accept="image/*" multiple id="galleryInput">
                        @php
                            $galleryError = null;
                            foreach ($errors->getMessages() as $key => $messages) {
                                if ($key === 'gallery_files' || str_starts_with($key, 'gallery_files.')) {
                                    $galleryError = $messages[0] ?? null;
                                    break;
                                }
                            }
                        @endphp
                        @if($galleryError)
                            <div style="color:red;font-size:12px;margin-top:4px">{{ $galleryError }}</div>
                        @endif
                        <div class="hint">Có thể chọn nhiều ảnh cùng lúc. Ảnh gallery hiển thị trên trang chi tiết dịch vụ.</div>
                        <div id="galleryPreview" style="display:flex;flex-wrap:wrap;gap:10px;margin-top:12px"></div>
                    </div>

                    <div class="kb-form-group span-2">
                        <label>Video URL (YouTube / Vimeo)</label>
                        <input type="url" name="video_url" class="kb-form-control" value="{{ old('video_url') }}" placeholder="https://www.youtube.com/watch?v=...">
                        <div class="hint">Nhúng video giới thiệu dịch vụ lên trang Landing Page</div>
                    </div>
                </div>
            </div>

            {{-- TAB 3: CONTENT --}}
            <div class="tab-pane" id="tab-content">
                <div class="kb-form-group" style="margin-bottom:24px">
                    <label>Nội dung mô tả đầy đủ (Landing Page) <span style="color:red">*</span></label>
                    <textarea name="description" id="full_description" class="kb-form-control" rows="15" placeholder="Mô tả chi tiết về dịch vụ...">{{ old('description') }}</textarea>
                </div>

                {{-- BENEFITS --}}
                <div class="card-section">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
                        <h4 class="card-section-title" style="margin:0">💎 Lợi ích nổi bật của dịch vụ</h4>
                        <button type="button" onclick="addRepeater('benefits-wrapper','benefits')" class="kb-btn kb-btn--sm" style="background:#B76E79;color:#fff">+ Thêm lợi ích</button>
                    </div>
                    <div id="benefits-wrapper">
                        <div class="repeater-row">
                            <div style="flex:1;display:flex;flex-direction:column;gap:6px">
                                <input type="text" name="benefits[0][title]" class="kb-form-control" placeholder="Tiêu đề (VD: Mỹ phẩm High-end)">
                                <input type="text" name="benefits[0][description]" class="kb-form-control" placeholder="Mô tả ngắn (VD: 100% hàng chính hãng từ Chanel, Dior...)">
                            </div>
                            <button type="button" class="btn-remove" onclick="this.closest('.repeater-row').remove()">✕</button>
                        </div>
                    </div>
                </div>

                {{-- PROCESS STEPS --}}
                <div class="card-section" style="background:#f8f9ff;border-color:#e8ebff">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
                        <h4 class="card-section-title" style="margin:0;color:#5c6bc0">📋 Quy trình thực hiện</h4>
                        <button type="button" onclick="addRepeater('process-wrapper','process_steps',true)" class="kb-btn kb-btn--sm" style="background:#5c6bc0;color:#fff">+ Thêm bước</button>
                    </div>
                    <div id="process-wrapper">
                        <div class="repeater-row">
                            <div style="flex:1;display:flex;flex-direction:column;gap:6px">
                                <input type="text" name="process_steps[0][title]" class="kb-form-control" placeholder="Tên bước (VD: Làm sạch & Dưỡng ẩm)">
                                <textarea name="process_steps[0][description]" class="kb-form-control" rows="2" placeholder="Mô tả chi tiết bước này..."></textarea>
                            </div>
                            <button type="button" class="btn-remove" onclick="this.closest('.repeater-row').remove()">✕</button>
                        </div>
                    </div>
                </div>

                {{-- INCLUDES --}}
                <div class="card-section" style="background:#f8fff8;border-color:#d9f3da">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
                        <h4 class="card-section-title" style="margin:0;color:#389e47">✅ Dịch vụ bao gồm</h4>
                        <button type="button" onclick="addInclude()" class="kb-btn kb-btn--sm" style="background:#389e47;color:#fff">+ Thêm mục</button>
                    </div>
                    <div id="includes-wrapper">
                        <div class="repeater-row">
                            <input type="text" name="includes[]" class="kb-form-control" placeholder="VD: Tư vấn phong cách makeup miễn phí">
                            <button type="button" class="btn-remove" onclick="this.closest('.repeater-row').remove()">✕</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TAB 4: VARIANTS --}}
            <div class="tab-pane" id="tab-variants">
                <div style="margin-bottom:16px;display:flex;justify-content:space-between;align-items:center">
                    <div>
                        <h4 style="margin:0;font-family:'Playfair Display',serif">📦 Gói / Biến thể dịch vụ</h4>
                        <p style="color:#888;font-size:13px;margin:6px 0 0">Ví dụ: Gói cơ bản, Gói nâng cao, Gói cô dâu VIP...</p>
                    </div>
                    <button type="button" onclick="addVariant()" class="kb-btn kb-btn--sm kb-btn--primary">+ Thêm gói</button>
                </div>

                <div style="background:#f7f8fa;border-radius:12px;padding:4px 12px 12px;margin-bottom:8px">
                    <div style="display:grid;grid-template-columns:2fr 1fr 1fr 1fr 60px;gap:10px;padding:10px 0;font-size:11px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:1px">
                        <span>Tên gói</span><span>Giá gốc</span><span>Giá KM</span><span>Thời gian (phút)</span><span></span>
                    </div>
                    <div id="variants-wrapper"></div>
                    <div id="variants-empty" style="text-align:center;padding:32px;color:#ccc;font-size:14px">
                        Chưa có gói nào. Nhấn "+ Thêm gói" để tạo biến thể.
                    </div>
                </div>
                <div class="hint">Nếu không tạo gói, dịch vụ sẽ dùng giá chính ở tab "Thông tin cơ bản".</div>
            </div>

            {{-- TAB 5: FAQ --}}
            <div class="tab-pane" id="tab-faq">
                <div style="margin-bottom:16px;display:flex;justify-content:space-between;align-items:center">
                    <div>
                        <h4 style="margin:0;font-family:'Playfair Display',serif">❓ Câu hỏi thường gặp (FAQ)</h4>
                        <p style="color:#888;font-size:13px;margin:6px 0 0">FAQ sẽ hiển thị ở trang Landing Page và được đưa vào Schema JSON-LD giúp SEO.</p>
                    </div>
                    <button type="button" onclick="addFaq()" class="kb-btn kb-btn--sm" style="background:#f0a000;color:#fff">+ Thêm câu hỏi</button>
                </div>
                <div id="faq-wrapper"></div>
                <div id="faq-empty" style="text-align:center;padding:32px;color:#ccc;font-size:14px">
                    Chưa có câu hỏi nào. Nhấn "+ Thêm câu hỏi" để bắt đầu.
                </div>
            </div>

            {{-- TAB 6: SEO --}}
            <div class="tab-pane" id="tab-seo">
                <div class="field-group">
                    <div class="kb-form-group span-2">
                        <label>SEO Title</label>
                        <input type="text" name="meta_title" class="kb-form-control" value="{{ old('meta_title') }}" placeholder="Tiêu đề hiển thị trên Google (để trống = lấy tên dịch vụ)">
                        <div class="hint">Tối đa 60 ký tự để hiển thị đầy đủ trên Google</div>
                    </div>
                    <div class="kb-form-group span-2">
                        <label>SEO Description</label>
                        <textarea name="meta_description" class="kb-form-control" rows="3" placeholder="Đoạn mô tả ngắn xuất hiện dưới tiêu đề trên Google...">{{ old('meta_description') }}</textarea>
                        <div class="hint">Tối đa 155 ký tự. Nên có từ khoá chính và lời kêu gọi hành động.</div>
                    </div>
                    <div class="kb-form-group span-2">
                        <label>SEO Keywords (phân cách bằng dấu phẩy)</label>
                        <input type="text" name="meta_keywords" class="kb-form-control" value="{{ old('meta_keywords') }}" placeholder="trang diem co dau, makeup khanh beauty, trang diem chuyen nghiep ha noi">
                    </div>
                </div>
            </div>

            {{-- SUBMIT --}}
            <div style="display:flex;gap:15px;margin-top:32px;padding-top:24px;border-top:1px solid #eee">
                <button type="submit" class="kb-btn kb-btn--primary" style="padding:13px 40px;font-size:15px">✓ Tạo Dịch Vụ</button>
                <a href="{{ route('admin.services.index') }}" class="kb-btn" style="background:#f0f0f0;padding:13px 24px">Hủy bỏ</a>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
// TinyMCE
tinymce.init({
    selector: '#full_description',
    plugins: 'advlist autolink lists link image charmap preview anchor searchreplace wordcount visualblocks code fullscreen insertdatetime media table emoticons',
    toolbar: 'undo redo | blocks fontsize | bold italic underline | align numlist bullist | link image | forecolor backcolor removeformat | code fullscreen',
    height: 480, branding: false, promotion: false
});

// Tab switching
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById(btn.dataset.tab).classList.add('active');
    });
});

// Auto slug from name
document.getElementById('nameInput').addEventListener('input', function() {
    const slugInput = document.getElementById('slugInput');
    if (!slugInput.dataset.manual) {
        slugInput.value = this.value.toLowerCase()
            .normalize('NFD').replace(/[\u0300-\u036f]/g,'')
            .replace(/[đĐ]/g,'d')
            .replace(/[^a-z0-9\s-]/g,'')
            .trim().replace(/\s+/g,'-');
    }
});
document.getElementById('slugInput').addEventListener('input', function() {
    this.dataset.manual = '1';
});

// Image preview
document.getElementById('imgPreviewInput').addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('imgPreviewEl').src = e.target.result;
            document.getElementById('imgPreview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});

// Gallery preview
document.getElementById('galleryInput').addEventListener('change', function() {
    const preview = document.getElementById('galleryPreview');
    preview.innerHTML = '';
    Array.from(this.files).forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.cssText = 'width:80px;height:80px;object-fit:cover;border-radius:8px;border:2px solid #eee';
            preview.appendChild(img);
        };
        reader.readAsDataURL(file);
    });
});

// Benefits & Process repeater
let benefitsCount = 1, processCount = 1;
function addRepeater(wrapperId, prefix, isTextarea = false) {
    const wrapper = document.getElementById(wrapperId);
    const index = prefix === 'benefits' ? benefitsCount++ : processCount++;
    const descField = isTextarea
        ? `<textarea name="${prefix}[${index}][description]" class="kb-form-control" rows="2" placeholder="Mô tả chi tiết..."></textarea>`
        : `<input type="text" name="${prefix}[${index}][description]" class="kb-form-control" placeholder="Mô tả ngắn...">`;
    wrapper.insertAdjacentHTML('beforeend', `
        <div class="repeater-row">
            <div style="flex:1;display:flex;flex-direction:column;gap:6px">
                <input type="text" name="${prefix}[${index}][title]" class="kb-form-control" placeholder="Tiêu đề...">
                ${descField}
            </div>
            <button type="button" class="btn-remove" onclick="this.closest('.repeater-row').remove()">✕</button>
        </div>
    `);
}

// Includes repeater
let includeCount = 1;
function addInclude() {
    document.getElementById('includes-wrapper').insertAdjacentHTML('beforeend', `
        <div class="repeater-row">
            <input type="text" name="includes[]" class="kb-form-control" placeholder="VD: Bộ mỹ phẩm dưỡng da sau makeup">
            <button type="button" class="btn-remove" onclick="this.closest('.repeater-row').remove()">✕</button>
        </div>
    `);
}

// Variants
let variantCount = 0;
function addVariant() {
    const wrapper = document.getElementById('variants-wrapper');
    const empty = document.getElementById('variants-empty');
    empty.style.display = 'none';
    const i = variantCount++;
    wrapper.insertAdjacentHTML('beforeend', `
        <div class="variant-row" id="vrow${i}">
            <input type="text" name="variants[${i}][variant_name]" class="kb-form-control" placeholder="VD: Gói cô dâu VIP">
            <input type="number" name="variants[${i}][price]" class="kb-form-control" placeholder="Giá gốc" min="0">
            <input type="number" name="variants[${i}][sale_price]" class="kb-form-control" placeholder="Giá KM">
            <input type="number" name="variants[${i}][duration_minutes]" class="kb-form-control" placeholder="Phút" min="0">
            <button type="button" class="btn-remove" onclick="removeVariant('vrow${i}')">✕</button>
        </div>
    `);
}
function removeVariant(id) {
    document.getElementById(id).remove();
    if (!document.getElementById('variants-wrapper').children.length) {
        document.getElementById('variants-empty').style.display = 'block';
    }
}

// FAQs
let faqCount = 0;
function addFaq() {
    const wrapper = document.getElementById('faq-wrapper');
    document.getElementById('faq-empty').style.display = 'none';
    const i = faqCount++;
    wrapper.insertAdjacentHTML('beforeend', `
        <div class="repeater-row" id="frow${i}" style="flex-direction:column;gap:8px">
            <div style="display:flex;justify-content:space-between;align-items:center">
                <strong style="font-size:13px;color:#555">Câu hỏi ${i+1}</strong>
                <button type="button" class="btn-remove" onclick="removeFaq('frow${i}')">✕ Xóa</button>
            </div>
            <input type="text" name="faqs[${i}][question]" class="kb-form-control" placeholder="Câu hỏi (VD: Dịch vụ có đến tận nhà không?)">
            <textarea name="faqs[${i}][answer]" class="kb-form-control" rows="3" placeholder="Câu trả lời chi tiết..."></textarea>
        </div>
    `);
}
function removeFaq(id) {
    document.getElementById(id).remove();
    if (!document.getElementById('faq-wrapper').children.length) {
        document.getElementById('faq-empty').style.display = 'block';
    }
}
</script>
@endpush
