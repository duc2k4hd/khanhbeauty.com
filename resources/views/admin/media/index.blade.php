@extends('admin.layouts.app')
@section('title', 'Thư Viện Media')

@section('content')
<div class="kb-media-container">
    <!-- Sidebar: Folders & Filters -->
    <aside class="kb-media-sidebar">
        <div class="kb-media-sidebar__section">
            <h4>QUẢN LÝ</h4>
            <ul class="kb-media-nav">
                <li class="active" data-filter="all"><span class="icon">📁</span> Tất cả tệp</li>
                <li data-filter="image"><span class="icon">🖼️</span> Hình ảnh</li>
                <li data-filter="video"><span class="icon">🎬</span> Video</li>
                <li data-filter="audio"><span class="icon">🎵</span> Âm thanh</li>
            </ul>
        </div>
        
        <div class="kb-media-sidebar__section">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                <h4>THƯ MỤC</h4>
                <button id="btnCreateFolder" title="Thêm thư mục" style="background:none;border:none;color:var(--primary);cursor:pointer;font-size:18px;">+</button>
            </div>
            <ul id="folderList" class="kb-media-nav kb-media-folders">
                <!-- Folders loaded here via AJAX -->
                <li data-folder="uploads" class="folder-item"><span>📂</span> uploads</li>
            </ul>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="kb-media-main">
        <!-- Toolbar -->
        <header class="kb-media-toolbar">
            <div class="kb-media-toolbar__left">
                <div class="kb-search-box">
                    <input type="text" id="mediaSearch" placeholder="Tìm kiếm tệp tin...">
                </div>
            </div>
            
            <div class="kb-media-toolbar__right">
                <div id="selectionActions" class="kb-selection-actions">
                    <span id="selectCount">0 chọn</span>
                    <button id="btnBulkMove" class="kb-btn kb-btn--action">Di chuyển</button>
                    <button id="btnBulkDelete" class="kb-btn kb-btn--danger">Xóa</button>
                </div>

                <div class="kb-per-page-selector">
                    <select id="mediaPerPage">
                        <option value="50">Hiện 50</option>
                        <option value="200">Hiện 200</option>
                        <option value="500">Hiện 500</option>
                        <option value="2000">Hiện 2000</option>
                    </select>
                </div>
                
                <label for="mediaUploadInput" class="kb-btn kb-btn--primary">
                    <svg viewBox="0 0 24 24" style="width:16px;height:16px;stroke:currentColor;stroke-width:2.5;fill:none"><path d="M12 5v14M5 12h14"/></svg>
                    Tải Lên
                </label>
                <input type="file" id="mediaUploadInput" multiple hidden accept="image/*,video/*,audio/*">
            </div>
        </header>

        <!-- Media Grid -->
        <div class="kb-media-grid-wrapper">
            <div id="mediaGrid" class="kb-media-grid">
                <!-- Media items rendered here -->
            </div>
            <div id="mediaLoader" style="text-align:center;padding:20px;display:none;">
                <div class="kb-spinner"></div>
            </div>
        </div>
    </main>

    <!-- Detail Sidebar (Drawer) -->
    <div id="mediaDetailDrawer" class="kb-media-drawer">
        <div class="kb-media-drawer__header">
            <h3>Chi tiết tệp</h3>
            <button id="btnCloseDrawer">×</button>
        </div>
        <div id="mediaDetailContent" class="kb-media-drawer__body">
            <!-- Details loaded here -->
        </div>
    </div>
</div>

<!-- Upload Progress Modal -->
<div id="uploadProgressModal" class="kb-modal-overlay">
    <div class="kb-modal-box" style="max-width:500px;">
        <h3>Đang tải lên...</h3>
        <p id="uploadStatusMsg" style="font-size:14px;color:#666;margin:10px 0;">Đang xử lý các tệp tin của bạn.</p>
        <div class="kb-progress-bar">
            <div id="uploadProgressBarFill" class="kb-progress-bar-fill"></div>
        </div>
        <div id="uploadFileList" class="kb-upload-file-list"></div>
    </div>
</div>

<!-- MODAL: XÁC NHẬN XÓA (THAY CONFIRM) -->
<div id="confirmDeleteModal" class="kb-modal-overlay">
    <div class="kb-modal-box" style="max-width: 420px; text-align: center;">
        <div style="font-size: 56px; margin-bottom: 20px;">🗑️</div>
        <h3 style="margin-bottom: 12px; font-family: 'Playfair Display'; color: #1a202c; font-size: 20px;">Xác nhận xóa tệp?</h3>
        <p id="confirmDeleteText" style="color: #718096; margin-bottom: 28px; font-size: 14px; line-height: 1.6;">Hành động này không thể hoàn tác. Mọi dữ liệu vật lý và liên kết tệp tin sẽ bị gỡ bỏ vĩnh viễn.</p>
        <div style="display: flex; gap: 12px; justify-content: center;">
            <button class="kb-btn" onclick="document.getElementById('confirmDeleteModal').classList.remove('active')" style="flex: 1; background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; font-weight: 600;">Hủy bỏ</button>
            <button id="btnConfirmDeleteExec" class="kb-btn" style="flex: 1; background: #e11d48; color: #fff; border: none; font-weight: 700; box-shadow: 0 4px 12px rgba(225, 29, 72, 0.2);">Xóa vĩnh viễn</button>
        </div>
    </div>
</div>

<!-- MODAL: NHẬP TÊN THƯ MỤC (THAY PROMPT) -->
<div id="folderNameModal" class="kb-modal-overlay">
    <div class="kb-modal-box" style="max-width: 450px;">
        <div style="display:flex; justify-content: space-between; align-items:center; margin-bottom: 20px;">
            <h3 style="font-family: 'Playfair Display'; color: #1a202c; margin: 0;">Tạo thư mục mới</h3>
            <button class="kb-close-modal" onclick="document.getElementById('folderNameModal').classList.remove('active')">&times;</button>
        </div>
        <div class="kb-form-group">
            <label style="font-weight: 600; color: #4a5568; margin-bottom: 8px; display: block;">Tên thư mục</label>
            <input type="text" id="newFolderNameInput" class="kb-form-control" placeholder="Ví dụ: Bo-suu-tap-2024" autofocus>
            <small style="color: #a0aec0; margin-top: 8px; display: block;">Không nên dùng ký tự đặc biệt hoặc dấu tiếng Việt.</small>
        </div>
        <div style="display: flex; gap: 12px; margin-top: 30px;">
            <button class="kb-btn" onclick="document.getElementById('folderNameModal').classList.remove('active')" style="flex: 1; background: #fff; color: #4a5568; border: 1px solid #e2e8f0;">Hủy</button>
            <button id="btnConfirmCreateFolder" class="kb-btn kb-btn--primary" style="flex: 1; border: none;">Khởi tạo ngay</button>
        </div>
    </div>
</div>

<!-- Move Folder Modal -->
<div id="moveFolderModal" class="kb-modal-overlay">
    <div class="kb-modal-box" style="max-width:400px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h3 style="margin:0;">Di chuyển đến...</h3>
            <button class="kb-close-modal" onclick="document.getElementById('moveFolderModal').classList.remove('active')">×</button>
        </div>
        <p style="font-size:13px; color:#666; margin-bottom:15px;">Chọn thư mục bạn muốn chuyển các tệp này vào:</p>
        <ul id="moveFolderList" class="kb-move-folder-list">
            <!-- Folders will be listed here -->
        </ul>
        <div style="margin-top:20px; text-align:right;">
            <button class="kb-btn" onclick="document.getElementById('moveFolderModal').classList.remove('active')">Hủy bỏ</button>
        </div>
    </div>
</div>

<template id="mediaItemTemplate">
    <div class="kb-media-item" data-id="">
        <div class="kb-media-item__preview">
            <img src="" alt="">
            <div class="kb-media-item__check">✓</div>
        </div>
        <div class="kb-media-item__info">
            <span class="kb-media-item__name"></span>
        </div>
    </div>
</template>

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/media.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/admin/media.js') }}"></script>
@endpush
