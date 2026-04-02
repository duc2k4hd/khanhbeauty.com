<div class="kb-wizard">
    <div class="kb-wizard-progress">
        <div class="kb-wizard-step-ind active" data-step="1">1. Dịch vụ</div>
        <div class="kb-wizard-step-ind" data-step="2">2. Chuyên gia</div>
        <div class="kb-wizard-step-ind" data-step="3">3. Ngày giờ</div>
        <div class="kb-wizard-step-ind" data-step="4">4. Hoàn tất</div>
        <div class="kb-wizard-line" id="wizardLine"></div>
    </div>

    <form id="bookingFormAjax">
        @csrf
        
        <!-- STEP 1: SERVICE -->
        <div class="kb-wizard-pane active" id="pane-1">
            <h3 class="kb-wizard-title">Bạn muốn nhận dịch vụ gì?</h3>
            <div class="kb-form-group">
                <label class="kb-form-label">Chọn dịch vụ *</label>
                <select name="service_id" id="service_id" class="kb-form-input" required>
                    <option value="">-- Chọn một dịch vụ --</option>
                    @php 
                        $allCats = \App\Models\ServiceCategory::active()->with(['services' => fn($q) => $q->active()])->get();
                    @endphp
                    @foreach($allCats as $category)
                        @if($category->services->count() > 0)
                            <optgroup label="✦ {{ $category->name }}">
                            @foreach($category->services as $service)
                                <option value="{{ $service->id }}" data-variants="{{ $service->variants->toJson() }}">{{ $service->name }}</option>
                            @endforeach
                            </optgroup>
                        @endif
                    @endforeach
                </select>
            </div>
            
            <div class="kb-form-group" id="variantWrap" style="display:none">
                <label class="kb-form-label">Chọn gói (Variation) *</label>
                <select name="variant_id" id="variant_id" class="kb-form-input" required>
                </select>
            </div>
            
            <div class="kb-wizard-actions" style="justify-content: flex-end;">
                <button type="button" class="kb-btn-next btn-next">Tiếp tục ➔</button>
            </div>
        </div>

        <!-- STEP 2: STAFF -->
        <div class="kb-wizard-pane" id="pane-2">
            <h3 class="kb-wizard-title">Chọn Chuyên gia / Makeup Artist</h3>
            <div class="kb-staff-grid" id="staffGrid">
                <div style="grid-column: 1/-1; text-align:center; color:#999; padding:20px;">Đang tải danh sách...</div>
            </div>
            <input type="hidden" name="staff_id" id="staff_id" required>
            
            <div class="kb-wizard-actions">
                <button type="button" class="kb-btn-prev btn-prev">⟵ Quay lại</button>
                <button type="button" class="kb-btn-next btn-next" id="btnNextStep2" disabled>Tiếp tục ➔</button>
            </div>
        </div>

        <!-- STEP 3: TIME -->
        <div class="kb-wizard-pane" id="pane-3">
            <h3 class="kb-wizard-title">Chọn Thời gian Đặt Lịch</h3>
            <div class="kb-grid-2">
                <div>
                    <label class="kb-form-label">Ngày đặt lịch *</label>
                    <input type="date" name="booking_date" id="booking_date" class="kb-form-input" required min="{{ date('Y-m-d') }}">
                </div>
                <div>
                    <label class="kb-form-label">Khung giờ trống *</label>
                    <select name="booking_time" id="booking_time" class="kb-form-input" required disabled>
                        <option value="">-- Vui lòng chọn ngày trước --</option>
                    </select>
                </div>
            </div>
            
            <div class="kb-wizard-actions">
                <button type="button" class="kb-btn-prev btn-prev">⟵ Quay lại</button>
                <button type="button" class="kb-btn-next btn-next" id="btnNextStep3" disabled>Tiếp tục ➔</button>
            </div>
        </div>

        <!-- STEP 4: CONTACT -->
        <div class="kb-wizard-pane" id="pane-4">
            <h3 class="kb-wizard-title">Thông tin liên hệ của bạn</h3>
            <div class="kb-grid-2" style="margin-bottom: 20px;">
                <div>
                    <label class="kb-form-label">Họ và tên *</label>
                    <input type="text" name="guest_name" id="guest_name" placeholder="Nguyễn Văn A" class="kb-form-input" required>
                </div>
                <div>
                    <label class="kb-form-label">Số ĐT / Zalo *</label>
                    <input type="tel" name="guest_phone" id="guest_phone" placeholder="0987123..." class="kb-form-input" required>
                </div>
            </div>
            
            <div class="kb-form-group">
                <label class="kb-form-label">Ghi chú thêm (Tuỳ chọn)</label>
                <textarea name="notes" id="notes" rows="3" placeholder="Yêu cầu makeup tone nào..." class="kb-form-input"></textarea>
            </div>

            <div id="bookingAlert" class="kb-alert hidden"></div>
            
            <div class="kb-wizard-actions">
                <button type="button" class="kb-btn-prev btn-prev">⟵ Quay lại</button>
                <button type="submit" class="kb-btn-next" id="btnSubmit">Xác Nhận Đặt Lịch</button>
            </div>
        </div>

        <!-- STEP 5: SUCCESS -->
        <div class="kb-wizard-pane" id="pane-5">
            <div class="kb-success-screen">
                <div class="kb-success-icon">✓</div>
                <h3 class="kb-wizard-title">Đặt lịch thành công!</h3>
                <p>Mã đặt lịch của bạn: <span id="res_code" class="kb-res-code">---</span></p>
                <p style="margin-top:20px; color:var(--text-light); font-size:14px;">Mình sẽ sớm liên hệ xác nhận với bạn qua Zalo/SDT.</p>
                <button type="button" onclick="location.reload()" class="kb-btn-next" style="margin-top:30px;">Về trang chủ</button>
            </div>
        </div>
    </form>
</div>
