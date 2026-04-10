@extends('admin.layouts.app')
@section('title', 'Sửa Dịch Vụ: ' . $service->name)

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
.span-2 { grid-column:span 2; }
.card-section { background:#fafafa; border:1px solid #f0effb; border-radius:16px; padding:22px; margin-bottom:22px; }
.card-section-title { font-family:'Playfair Display',serif; font-size:15px; font-weight:600; margin:0 0 16px; display:flex; align-items:center; gap:10px; }
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
    padding:12px; background:#fff; border-radius:10px; border:1px solid #eeeff5;
    margin-bottom:10px; animation: fadeInRow 0.25s ease;
}
.hint { font-size:11px; color:#aaa; margin-top:4px; }
.gallery-thumb { width:80px; height:80px; object-fit:cover; border-radius:8px; border:2px solid #eee; position:relative; }
.gallery-grid { display:flex; flex-wrap:wrap; gap:10px; margin-top:12px; }
.gallery-item { position:relative; }
.gallery-item.is-removed { display:none; }
.gallery-item .del-btn {
    position:absolute; top:-6px; right:-6px;
    width:20px; height:20px; border-radius:50%;
    background:#ff4d4f; color:#fff; border:none;
    font-size:10px; cursor:pointer;
    display:flex; align-items:center; justify-content:center;
}
</style>

<div style="max-width:1000px;">
    <div class="kb-card">
        <div class="kb-card-header">
            <h3 class="kb-card-title">✏️ Sửa: {{ $service->name }}</h3>
            <div style="display:flex;gap:8px">
                <a href="{{ route('services.show', $service->slug) }}" target="_blank" class="kb-btn kb-btn--sm" style="background:#e8f5e9;color:#388e3c">👁 Xem trang</a>
                <a href="{{ route('admin.services.index') }}" class="kb-btn kb-btn--sm" style="background:#f0f0f0">← Quay lại</a>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.services.update', $service) }}" enctype="multipart/form-data">
            @csrf @method('PUT')

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
                        <input type="text" name="name" class="kb-form-control" value="{{ old('name', $service->name) }}" required>
                        @error('name')<div style="color:red;font-size:12px;margin-top:4px">{{ $message }}</div>@enderror
                    </div>
                    <div class="kb-form-group">
                        <label>Đường dẫn (Slug)</label>
                        <input type="text" name="slug" class="kb-form-control" value="{{ old('slug', $service->slug) }}">
                        <div class="hint">Cảnh báo: đổi slug sẽ làm hỏng link cũ</div>
                    </div>
                    <div class="kb-form-group">
                        <label>Danh mục <span style="color:red">*</span></label>
                        <select name="category_id" class="kb-form-control" required>
                            <option value="">-- Chọn danh mục --</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id',$service->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="kb-form-group">
                        <label>Thứ tự hiển thị</label>
                        <input type="number" name="sort_order" class="kb-form-control" value="{{ old('sort_order', $service->sort_order) }}" min="0">
                        <div class="hint">Số nhỏ hơn hiển thị trước</div>
                    </div>

                    <div class="kb-form-group">
                        <label>Giá gốc (VNĐ) <span style="color:red">*</span></label>
                        <div style="display:flex;gap:10px">
                            <input type="number" name="price" class="kb-form-control" value="{{ old('price', $service->price) }}" min="0" required style="flex:2">
                            <input type="text" name="price_unit" class="kb-form-control" value="{{ old('price_unit', $service->price_unit) }}" style="flex:1" list="unitList">
                            <datalist id="unitList">
                                <option value="buổi"><option value="khóa"><option value="người"><option value="lượt"><option value="ngày">
                            </datalist>
                        </div>
                    </div>
                    <div class="kb-form-group">
                        <label>Giá khuyến mãi (VNĐ)</label>
                        <input type="number" name="sale_price" class="kb-form-control" value="{{ old('sale_price', $service->sale_price) }}" min="0" placeholder="Để trống nếu không KM">
                    </div>
                    <div class="kb-form-group">
                        <label>Thời gian thực hiện (phút)</label>
                        <input type="number" name="duration_minutes" class="kb-form-control" value="{{ old('duration_minutes', $service->duration_minutes) }}" min="0">
                    </div>
                    <div class="kb-form-group" style="display:flex;flex-direction:column;gap:12px;justify-content:center">
                        <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-weight:600">
                            <input type="checkbox" name="is_active" value="1" {{ $service->is_active ? 'checked' : '' }} style="width:18px;height:18px;accent-color:var(--primary)">
                            Đang hiển thị (Bật)
                        </label>
                        <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-weight:600">
                            <input type="checkbox" name="is_featured" value="1" {{ $service->is_featured ? 'checked' : '' }} style="width:18px;height:18px;accent-color:var(--primary)">
                            Dịch vụ nổi bật (Featured)
                        </label>
                    </div>

                    <div class="kb-form-group span-2">
                        <label>Mô tả ngắn</label>
                        <input type="text" name="short_description" class="kb-form-control" value="{{ old('short_description', $service->short_description) }}">
                    </div>
                </div>

                {{-- Thông tin thống kê (readonly) --}}
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;margin-top:24px;padding:16px;background:#f9f9f9;border-radius:12px">
                    <div style="text-align:center">
                        <div style="font-size:24px;font-weight:700;color:var(--primary)">{{ number_format($service->view_count) }}</div>
                        <div style="font-size:11px;color:#888;text-transform:uppercase;letter-spacing:1px">Lượt xem</div>
                    </div>
                    <div style="text-align:center">
                        <div style="font-size:24px;font-weight:700;color:#52c41a">{{ number_format($service->booking_count ?? 0) }}</div>
                        <div style="font-size:11px;color:#888;text-transform:uppercase;letter-spacing:1px">Lượt đặt lịch</div>
                    </div>
                    <div style="text-align:center">
                        <div style="font-size:24px;font-weight:700;color:#faad14">{{ $service->avg_rating ?? '—' }}</div>
                        <div style="font-size:11px;color:#888;text-transform:uppercase;letter-spacing:1px">Đánh giá TB</div>
                    </div>
                </div>
            </div>

            {{-- TAB 2: MEDIA --}}
            <div class="tab-pane" id="tab-media">
                <div class="field-group">
                    <div class="kb-form-group span-2">
                        <label>Ảnh đại diện chính</label>
                        @if($service->featuredImage)
                        <div style="margin-bottom:12px">
                            <img src="{{ $service->featuredImage->file_url }}" style="max-width:200px;border-radius:10px;border:2px solid #eee">
                            <div class="hint">Ảnh hiện tại. Tải mới để thay thế.</div>
                        </div>
                        @endif
                        <input type="file" name="featured_image" class="kb-form-control kb-form-file" accept="image/*">
                        @error('featured_image')<div style="color:red;font-size:12px;margin-top:4px">{{ $message }}</div>@enderror
                    </div>

                    <div class="kb-form-group span-2">
                        <label>Bộ ảnh Gallery (thêm / thay thế)</label>
                        {{-- Existing gallery --}}
                        @php $gallery = $service->gallery_media ?? collect(); @endphp
                        @if($gallery->count() > 0)
                        <div style="margin-bottom:12px">
                            <div class="hint" style="margin-bottom:8px">Ảnh gallery hiện tại (tích xoá để gỡ bỏ):</div>
                            <div class="gallery-grid">
                                @foreach($gallery as $gItem)
                                <div class="gallery-item">
                                    <img src="{{ $gItem->file_url }}" class="gallery-thumb">
                                    <label style="position:absolute;top:-6px;right:-6px;width:20px;height:20px;border-radius:50%;background:#ff4d4f;display:flex;align-items:center;justify-content:center;cursor:pointer">
                                        <input type="checkbox" name="gallery_remove_ids[]" value="{{ $gItem->id }}" style="display:none">
                                        <span style="color:#fff;font-size:10px">✕</span>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
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
                        <div class="hint">Chọn nhiều ảnh để thêm vào gallery hiện có</div>
                        <div id="galleryPreview" class="gallery-grid"></div>
                    </div>

                    <div class="kb-form-group span-2">
                        <label>Video URL (YouTube / Vimeo)</label>
                        <input type="url" name="video_url" class="kb-form-control" value="{{ old('video_url', $service->video_url ?? '') }}" placeholder="https://www.youtube.com/watch?v=...">
                        <div class="hint">Video giới thiệu hiển thị trên Landing Page</div>
                    </div>
                </div>
            </div>

            {{-- TAB 3: CONTENT --}}
            <div class="tab-pane" id="tab-content">
                <div class="kb-form-group" style="margin-bottom:24px">
                    <label>Nội dung mô tả đầy đủ (Landing Page) <span style="color:red">*</span></label>
                    <textarea name="description" id="full_description" class="kb-form-control" rows="15">{{ old('description', $service->description) }}</textarea>
                </div>

                {{-- BENEFITS --}}
                <div class="card-section">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
                        <h4 class="card-section-title" style="margin:0">💎 Lợi ích nổi bật</h4>
                        <button type="button" onclick="addRepeater('benefits-wrapper','benefits')" class="kb-btn kb-btn--sm" style="background:#B76E79;color:#fff">+ Thêm</button>
                    </div>
                    <div id="benefits-wrapper">
                        @php $benefits = old('benefits', $service->benefits) ?? []; @endphp
                        @foreach($benefits as $i => $b)
                        <div class="repeater-row">
                            <div style="flex:1;display:flex;flex-direction:column;gap:6px">
                                <input type="text" name="benefits[{{ $i }}][title]" value="{{ $b['title'] ?? '' }}" class="kb-form-control" placeholder="Tiêu đề lợi ích">
                                <input type="text" name="benefits[{{ $i }}][description]" value="{{ $b['description'] ?? '' }}" class="kb-form-control" placeholder="Mô tả ngắn">
                            </div>
                            <button type="button" class="btn-remove" onclick="this.closest('.repeater-row').remove()">✕</button>
                        </div>
                        @endforeach
                        @if(empty($benefits))
                        <div class="repeater-row">
                            <div style="flex:1;display:flex;flex-direction:column;gap:6px">
                                <input type="text" name="benefits[0][title]" class="kb-form-control" placeholder="Tiêu đề lợi ích">
                                <input type="text" name="benefits[0][description]" class="kb-form-control" placeholder="Mô tả ngắn">
                            </div>
                            <button type="button" class="btn-remove" onclick="this.closest('.repeater-row').remove()">✕</button>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- PROCESS STEPS --}}
                <div class="card-section" style="background:#f8f9ff;border-color:#e8ebff">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
                        <h4 class="card-section-title" style="margin:0;color:#5c6bc0">📋 Quy trình thực hiện</h4>
                        <button type="button" onclick="addRepeater('process-wrapper','process_steps',true)" class="kb-btn kb-btn--sm" style="background:#5c6bc0;color:#fff">+ Thêm bước</button>
                    </div>
                    <div id="process-wrapper">
                        @php $steps = old('process_steps', $service->process_steps) ?? []; @endphp
                        @foreach($steps as $i => $s)
                        <div class="repeater-row">
                            <div style="flex:1;display:flex;flex-direction:column;gap:6px">
                                <input type="text" name="process_steps[{{ $i }}][title]" value="{{ $s['title'] ?? '' }}" class="kb-form-control" placeholder="Tên bước">
                                <textarea name="process_steps[{{ $i }}][description]" class="kb-form-control" rows="2" placeholder="Mô tả chi tiết">{{ $s['description'] ?? '' }}</textarea>
                            </div>
                            <button type="button" class="btn-remove" onclick="this.closest('.repeater-row').remove()">✕</button>
                        </div>
                        @endforeach
                        @if(empty($steps))
                        <div class="repeater-row">
                            <div style="flex:1;display:flex;flex-direction:column;gap:6px">
                                <input type="text" name="process_steps[0][title]" class="kb-form-control" placeholder="Tên bước">
                                <textarea name="process_steps[0][description]" class="kb-form-control" rows="2" placeholder="Mô tả chi tiết"></textarea>
                            </div>
                            <button type="button" class="btn-remove" onclick="this.closest('.repeater-row').remove()">✕</button>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- INCLUDES --}}
                <div class="card-section" style="background:#f8fff8;border-color:#d9f3da">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
                        <h4 class="card-section-title" style="margin:0;color:#389e47">✅ Dịch vụ bao gồm</h4>
                        <button type="button" onclick="addInclude()" class="kb-btn kb-btn--sm" style="background:#389e47;color:#fff">+ Thêm mục</button>
                    </div>
                    <div id="includes-wrapper">
                        @php $includes = old('includes', $service->includes ?? []); @endphp
                        @foreach((array)$includes as $inc)
                        <div class="repeater-row">
                            <input type="text" name="includes[]" value="{{ $inc }}" class="kb-form-control" placeholder="VD: Tư vấn makeup cá nhân hoá">
                            <button type="button" class="btn-remove" onclick="this.closest('.repeater-row').remove()">✕</button>
                        </div>
                        @endforeach
                        @if(empty($includes))
                        <div class="repeater-row">
                            <input type="text" name="includes[]" class="kb-form-control" placeholder="VD: Tư vấn makeup cá nhân hoá">
                            <button type="button" class="btn-remove" onclick="this.closest('.repeater-row').remove()">✕</button>
                        </div>
                        @endif
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

                {{-- Existing variants from DB --}}
                @if($service->variants->count() > 0)
                <div style="margin-bottom:20px">
                    <div style="font-size:12px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:1px;margin-bottom:8px">Gói hiện tại</div>
                    <div style="background:#f7f8fa;border-radius:12px;padding:12px">
                        <div style="display:grid;grid-template-columns:2fr 1fr 1fr 1fr 60px;gap:10px;padding:8px 12px;font-size:11px;font-weight:700;color:#aaa;text-transform:uppercase">
                            <span>Tên gói</span><span>Giá gốc</span><span>Giá KM</span><span>Thời gian</span><span></span>
                        </div>
                        @foreach($service->variants as $v)
                        <div style="display:grid;grid-template-columns:2fr 1fr 1fr 1fr 60px;gap:10px;align-items:center;padding:10px 12px;background:#fff;border-radius:8px;margin-bottom:8px;border:1px solid #eee">
                            <span style="font-weight:600;font-size:14px">{{ $v->variant_name }}</span>
                            <span>{{ number_format($v->price) }}đ</span>
                            <span>{{ $v->sale_price ? number_format($v->sale_price).'đ' : '—' }}</span>
                            <span>{{ $v->duration_minutes ? $v->duration_minutes.' phút' : '—' }}</span>
                            <td><button type="button" onclick="deleteVariant({{ $v->id }}, this)" class="btn-remove" title="Xóa gói">✕</button></td>
                        </div>
                        <input type="hidden" name="delete_variants[]" id="del_v_{{ $v->id }}" value="" disabled>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- New variants --}}
                <div style="background:#f7f8fa;border-radius:12px;padding:4px 12px 12px">
                    <div style="font-size:12px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:1px;padding:10px 0 6px">Thêm gói mới</div>
                    <div style="display:grid;grid-template-columns:2fr 1fr 1fr 1fr 60px;gap:10px;padding:6px 0 6px;font-size:11px;font-weight:700;color:#aaa;text-transform:uppercase;letter-spacing:1px">
                        <span>Tên gói</span><span>Giá gốc</span><span>Giá KM</span><span>Thời gian (phút)</span><span></span>
                    </div>
                    <div id="variants-wrapper"></div>
                    <div id="variants-empty" style="text-align:center;padding:24px;color:#ccc;font-size:13px">
                        Nhấn "+ Thêm gói" để tạo gói mới
                    </div>
                </div>
            </div>

            {{-- TAB 5: FAQ --}}
            <div class="tab-pane" id="tab-faq">
                <div style="margin-bottom:16px;display:flex;justify-content:space-between;align-items:center">
                    <div>
                        <h4 style="margin:0;font-family:'Playfair Display',serif">❓ Câu hỏi thường gặp (FAQ)</h4>
                        <p style="color:#888;font-size:13px;margin:6px 0 0">FAQ giúp SEO và hỗ trợ khách hàng trước khi đặt lịch.</p>
                    </div>
                    <button type="button" onclick="addFaq()" class="kb-btn kb-btn--sm" style="background:#f0a000;color:#fff">+ Thêm câu hỏi</button>
                </div>

                {{-- Existing FAQs --}}
                @if($service->faqs->count() > 0)
                <div style="margin-bottom:20px">
                    <div style="font-size:12px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:1px;margin-bottom:10px">FAQ hiện có</div>
                    @foreach($service->faqs as $faqItem)
                    <div style="background:#fffbf2;border:1px solid #ffefc9;border-radius:12px;padding:16px;margin-bottom:10px">
                        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px">
                            <div style="flex:1">
                                <div style="font-weight:700;font-size:14px;margin-bottom:6px">Q: {{ $faqItem->question }}</div>
                                <div style="font-size:13px;color:#666">A: {{ Str::limit(strip_tags($faqItem->answer), 120) }}</div>
                            </div>
                            <div style="display:flex;gap:8px;flex-shrink:0">
                                <label style="display:flex;align-items:center;gap:6px;background:#fff3f3;padding:6px 12px;border-radius:8px;cursor:pointer;font-size:12px;color:#ff4d4f;border:1px solid #ffccc7">
                                    <input type="checkbox" name="delete_faqs[]" value="{{ $faqItem->id }}" style="accent-color:#ff4d4f">
                                    Xóa
                                </label>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                <div id="faq-empty" style="text-align:center;padding:24px;color:#ccc;font-size:13px;{{ $service->faqs->count() > 0 ? 'display:none' : '' }}">
                    Chưa có câu hỏi nào. Nhấn "+ Thêm câu hỏi" để tạo.
                </div>
                <div id="faq-wrapper"></div>
            </div>

            {{-- TAB 6: SEO --}}
            <div class="tab-pane" id="tab-seo">
                <div class="field-group">
                    <div class="kb-form-group span-2">
                        <label>SEO Title</label>
                        <input type="text" name="meta_title" class="kb-form-control" value="{{ old('meta_title', $service->meta_title) }}" placeholder="Để trống = lấy tên dịch vụ">
                        <div class="hint">Tối đa 60 ký tự để hiển thị đầy đủ trên Google</div>
                    </div>
                    <div class="kb-form-group span-2">
                        <label>SEO Description</label>
                        <textarea name="meta_description" class="kb-form-control" rows="3" placeholder="Đoạn mô tả ngắn xuất hiện dưới tiêu đề trên Google...">{{ old('meta_description', $service->meta_description) }}</textarea>
                        <div class="hint">Tối đa 155 ký tự</div>
                    </div>
                    <div class="kb-form-group span-2">
                        <label>SEO Keywords</label>
                        <input type="text" name="meta_keywords" class="kb-form-control" value="{{ old('meta_keywords', $service->meta_keywords) }}" placeholder="trang diem co dau, makeup khanh beauty">
                    </div>
                </div>
            </div>

            {{-- SUBMIT --}}
            <div style="display:flex;gap:15px;margin-top:32px;padding-top:24px;border-top:1px solid #eee">
                <button type="submit" class="kb-btn kb-btn--primary" style="padding:13px 40px;font-size:15px">✓ Lưu Thay Đổi</button>
                <a href="{{ route('admin.services.index') }}" class="kb-btn" style="background:#f0f0f0;padding:13px 24px">Hủy bỏ</a>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
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

// Gallery preview
const galleryInput = document.getElementById('galleryInput');
if (galleryInput) {
    galleryInput.addEventListener('change', function() {
        const preview = document.getElementById('galleryPreview');
        preview.innerHTML = '';
        Array.from(this.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = e => {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'gallery-thumb';
                preview.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    });
}

// Gallery remove checkbox toggle visual
document.querySelectorAll('input[name="gallery_remove_ids[]"]').forEach(cb => {
    cb.addEventListener('change', function() {
        const item = this.closest('.gallery-item');
        if (!item) {
            return;
        }

        item.classList.toggle('is-removed', this.checked);
    });
});

// Repeaters
let benefitsCount = {{ count(old('benefits', $service->benefits) ?? []) }};
let processCount = {{ count(old('process_steps', $service->process_steps) ?? []) }};
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
    document.getElementById('variants-empty').style.display = 'none';
    const i = variantCount++;
    wrapper.insertAdjacentHTML('beforeend', `
        <div class="variant-row" id="vrow${i}">
            <input type="text" name="variants[${i}][variant_name]" class="kb-form-control" placeholder="Tên gói (VD: Gói VIP)">
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
function deleteVariant(id, btn) {
    const inp = document.getElementById('del_v_' + id);
    if (inp) { inp.value = id; inp.disabled = false; }
    const row = btn.closest('[style]');
    if (row) row.style.opacity = '0.35';
    btn.style.color = '#ff4d4f';
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
                <strong style="font-size:13px;color:#555">Câu hỏi mới ${i+1}</strong>
                <button type="button" class="btn-remove" style="font-size:13px" onclick="this.closest('#frow${i}').remove()">✕ Xóa</button>
            </div>
            <input type="text" name="faqs[${i}][question]" class="kb-form-control" placeholder="Câu hỏi thường gặp...">
            <textarea name="faqs[${i}][answer]" class="kb-form-control" rows="3" placeholder="Câu trả lời chi tiết..."></textarea>
        </div>
    `);
}
</script>
@endpush
