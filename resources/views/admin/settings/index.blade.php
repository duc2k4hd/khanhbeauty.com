@extends('admin.layouts.app')
@section('title', 'Cài Đặt Cấu Hình Hệ Thống')

@section('content')

<div class="kb-card">
    <div class="kb-card-header">
        <h3 class="kb-card-title">Cấu hình chung</h3>
        <button class="kb-btn kb-btn--primary" onclick="document.getElementById('formSettings').submit();">
            Lưu Thay Đổi
        </button>
    </div>

    <form id="formSettings" method="POST" action="{{ route('admin.settings.update') }}">
        @csrf
        <div class="kb-grid-dashboard" style="grid-template-columns: 1fr 1fr;">
            
            <div class="kb-form-group">
                <label>Số điện thoại liên hệ</label>
                <input type="text" name="settings[phone]" class="kb-form-control" value="{{ \App\Models\SiteSetting::getValue('phone') }}">
            </div>

            <div class="kb-form-group">
                <label>Email hỗ trợ</label>
                <input type="email" name="settings[email]" class="kb-form-control" value="{{ \App\Models\SiteSetting::getValue('email') }}">
            </div>

            <div class="kb-form-group">
                <label>Link Fanpage Facebook</label>
                <input type="url" name="settings[facebook_url]" class="kb-form-control" value="{{ \App\Models\SiteSetting::getValue('facebook_url') }}">
            </div>

            <div class="kb-form-group">
                <label>Địa chỉ văn phòng</label>
                <input type="text" name="settings[address]" class="kb-form-control" value="{{ \App\Models\SiteSetting::getValue('address') }}">
            </div>
            
            <div class="kb-form-group" style="grid-column: span 2;">
                <label>Giờ hoạt động</label>
                <input type="text" name="settings[opening_hours]" class="kb-form-control" value="{{ \App\Models\SiteSetting::getValue('opening_hours') }}">
            </div>

        </div>
    </form>
</div>

@endsection
