@extends('admin.layouts.app')
@section('title', 'Thư Viện Media')

@section('content')
<div class="kb-media-container">
    <aside class="kb-media-sidebar">
        <div class="kb-media-sidebar__section kb-media-sidebar__section--brand">
            <span class="kb-media-sidebar__eyebrow">Quản lý thư viện</span>
            <h2>Media Pro</h2>
            <p>Quản lý ảnh, video và tệp theo cách trực quan hơn. Chọn tệp ở cột giữa, thao tác ở cột phải.</p>
        </div>

        <div class="kb-media-sidebar__section">
            <h4>Loại tệp</h4>
            <ul class="kb-media-nav">
                <li class="active" data-filter="all">
                    <span class="kb-media-nav__icon">All</span>
                    <span>Tất cả tệp</span>
                </li>
                <li data-filter="image">
                    <span class="kb-media-nav__icon">IMG</span>
                    <span>Hình ảnh</span>
                </li>
                <li data-filter="video">
                    <span class="kb-media-nav__icon">VID</span>
                    <span>Video</span>
                </li>
                <li data-filter="audio">
                    <span class="kb-media-nav__icon">AUD</span>
                    <span>Âm thanh</span>
                </li>
            </ul>
        </div>

        <div class="kb-media-sidebar__section">
            <div class="kb-media-sidebar__heading-row">
                <h4>Thư mục</h4>
                <button id="btnCreateFolder" class="kb-icon-button" type="button" title="Tạo thư mục mới">+</button>
            </div>
            <ul id="folderList" class="kb-media-nav kb-media-folders">
                <li data-folder="uploads" class="folder-item">
                    <span class="kb-media-nav__icon">DIR</span>
                    <span>uploads</span>
                </li>
            </ul>
        </div>
    </aside>

    <main class="kb-media-main">
        <header class="kb-media-toolbar">
            <div class="kb-media-toolbar__left">
                <div class="kb-search-box">
                    <input type="text" id="mediaSearch" placeholder="Tìm kiếm theo tên tệp, tiêu đề hoặc thư mục...">
                </div>
                <div class="kb-media-context">
                    <strong id="mediaContextLabel">Tất cả tệp</strong>
                    <span id="mediaContextNote">Chọn tệp ở cột giữa, thao tác ở cột phải.</span>
                </div>
            </div>

            <div class="kb-media-toolbar__right">
                <div class="kb-per-page-selector">
                    <select id="mediaPerPage">
                        <option value="50">Hiển thị 50</option>
                        <option value="100">Hiển thị 100</option>
                        <option value="200">Hiển thị 200</option>
                    </select>
                </div>

                <button id="btnOpenUploadModal" class="kb-btn kb-btn--primary" type="button">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                    <span>Tải lên</span>
                </button>
            </div>
        </header>

        <div class="kb-media-workspace">
            <section class="kb-media-library">
                <div id="selectionActions" class="kb-selection-actions">
                    <div class="kb-selection-actions__meta">
                        <strong id="selectCount">0 tệp được chọn</strong>
                        <span>Chọn nhiều tệp để di chuyển hoặc xóa hàng loạt.</span>
                    </div>
                    <div class="kb-selection-actions__buttons">
                        <button id="btnBulkMove" class="kb-btn kb-btn--action" type="button">Di chuyển</button>
                        <button id="btnBulkDelete" class="kb-btn kb-btn--danger" type="button">Xóa</button>
                    </div>
                </div>

                <div class="kb-media-grid-wrapper">
                    <div id="mediaGrid" class="kb-media-grid"></div>
                    <div id="mediaLoader" class="kb-media-loader" style="display:none;">
                        <div class="kb-spinner"></div>
                        <p>Đang tải thêm tệp...</p>
                    </div>
                </div>
            </section>

            <aside id="mediaDetailDrawer" class="kb-media-drawer">
                <div class="kb-media-drawer__header">
                    <div>
                        <span class="kb-media-drawer__eyebrow">Bảng thao tác</span>
                        <h3 id="mediaInspectorTitle">Chưa chọn tệp</h3>
                    </div>
                    <button id="btnCloseDrawer" class="kb-btn kb-btn--ghost" type="button">Bỏ chọn</button>
                </div>
                <div id="mediaDetailContent" class="kb-media-drawer__body">
                    <div class="kb-media-empty-state">
                        <div class="kb-media-empty-state__icon">MEDIA</div>
                        <h4>Chọn một tệp để thao tác</h4>
                        <p>Nhấp vào ảnh hoặc tệp ở cột giữa để xem trước, chỉnh metadata, sao chép URL, di chuyển thư mục hoặc xóa.</p>
                        <ul>
                            <li>Nhấp 1 lần để xem chi tiết và thao tác.</li>
                            <li>Giữ Ctrl hoặc Cmd để chọn nhiều tệp.</li>
                            <li>Nhấp đúp để mở nhanh bảng chi tiết cho tệp đó.</li>
                        </ul>
                    </div>
                </div>
            </aside>
        </div>
    </main>
</div>

<div id="uploadModal" class="kb-modal-overlay">
    <div class="kb-modal-box kb-modal-box--large">
        <div class="kb-modal-box__head">
            <div>
                <h3>Tải tệp lên thư viện</h3>
                <p class="kb-modal-helper">Kéo thả hoặc chọn nhiều tệp. Hệ thống sẽ tự chia thành các đợt nhỏ và tải song song để tránh request quá lớn.</p>
            </div>
            <button id="btnCloseUploadModal" class="kb-close-modal" type="button">X</button>
        </div>

        <div class="kb-upload-shell">
            <section id="uploadDropzone" class="kb-upload-dropzone">
                <div class="kb-upload-dropzone__icon">UP</div>
                <h4>Kéo và thả tệp vào đây</h4>
                <p>Hoặc chọn từ máy tính. Hàng đợi có thể rất lớn, hệ thống sẽ tự chia lô nhỏ và chạy song song có giới hạn.</p>
                <div class="kb-upload-dropzone__actions">
                    <button id="btnSelectUploadFiles" class="kb-btn kb-btn--primary" type="button">Chọn tệp</button>
                    <span class="kb-upload-dropzone__hint">Hỗ trợ ảnh, video và âm thanh.</span>
                </div>
                <input type="file" id="mediaUploadInput" multiple hidden accept="image/*,video/*,audio/*">
            </section>

            <aside class="kb-upload-queue">
                <div class="kb-upload-queue__summary">
                    <div class="kb-upload-queue__summary-block">
                        <span class="kb-media-sidebar__eyebrow">Đích tải lên</span>
                        <strong id="uploadTargetFolder">uploads</strong>
                    </div>
                    <div id="uploadQueueStats" class="kb-upload-queue__stats">Chưa có tệp nào trong hàng đợi.</div>
                </div>
                <div id="uploadQueueList" class="kb-upload-file-list kb-upload-file-list--scroll">
                    <div class="kb-upload-empty">Chưa có tệp nào được chọn.</div>
                </div>
            </aside>
        </div>

        <div class="kb-modal-actions">
            <button id="btnClearUploadQueue" class="kb-btn kb-btn--ghost" type="button">Xóa danh sách</button>
            <button id="btnStartUpload" class="kb-btn kb-btn--primary" type="button" disabled>Bắt đầu tải lên</button>
        </div>
    </div>
</div>

<div id="uploadProgressModal" class="kb-modal-overlay">
    <div class="kb-modal-box kb-modal-box--medium">
        <div class="kb-modal-box__head">
            <div>
                <h3>Đang tải lên</h3>
                <p id="uploadStatusMsg" class="kb-modal-helper">Đang xử lý các tệp bạn vừa chọn.</p>
            </div>
            <button id="btnCancelUpload" class="kb-btn kb-btn--ghost" type="button">Hủy</button>
        </div>
        <div class="kb-progress-bar">
            <div id="uploadProgressBarFill" class="kb-progress-bar-fill"></div>
        </div>
        <div id="uploadProgressMeta" class="kb-upload-progress__meta"></div>
        <div id="uploadFileList" class="kb-upload-file-list kb-upload-file-list--scroll"></div>
    </div>
</div>

<div id="confirmDeleteModal" class="kb-modal-overlay">
    <div class="kb-modal-box kb-modal-box--small kb-modal-box--center">
        <div class="kb-modal-danger-icon">X</div>
        <h3>Xác nhận xóa tệp</h3>
        <p id="confirmDeleteText">Hành động này sẽ xóa tệp khỏi hệ thống và không thể hoàn tác.</p>
        <div class="kb-modal-actions">
            <button class="kb-btn kb-btn--ghost" type="button" onclick="document.getElementById('confirmDeleteModal').classList.remove('active')">Hủy</button>
            <button id="btnConfirmDeleteExec" class="kb-btn kb-btn--danger" type="button">Xóa vĩnh viễn</button>
        </div>
    </div>
</div>

<div id="folderNameModal" class="kb-modal-overlay">
    <div class="kb-modal-box kb-modal-box--small">
        <div class="kb-modal-box__head">
            <h3>Tạo thư mục mới</h3>
            <button class="kb-close-modal" type="button" onclick="document.getElementById('folderNameModal').classList.remove('active')">X</button>
        </div>
        <div class="kb-form-group">
            <label for="newFolderNameInput">Tên thư mục</label>
            <input type="text" id="newFolderNameInput" class="kb-form-control" placeholder="Ví dụ: bo-suu-tap-2026" autofocus>
            <small>Ưu tiên chữ thường, không dấu và không có ký tự đặc biệt để ổn định đường dẫn.</small>
        </div>
        <div class="kb-modal-actions">
            <button class="kb-btn kb-btn--ghost" type="button" onclick="document.getElementById('folderNameModal').classList.remove('active')">Hủy</button>
            <button id="btnConfirmCreateFolder" class="kb-btn kb-btn--primary" type="button">Tạo thư mục</button>
        </div>
    </div>
</div>

<div id="moveFolderModal" class="kb-modal-overlay">
    <div class="kb-modal-box kb-modal-box--small">
        <div class="kb-modal-box__head">
            <h3>Chuyển sang thư mục</h3>
            <button class="kb-close-modal" type="button" onclick="document.getElementById('moveFolderModal').classList.remove('active')">X</button>
        </div>
        <p class="kb-modal-helper">Chọn thư mục đích cho các tệp đang được chọn.</p>
        <ul id="moveFolderList" class="kb-move-folder-list"></ul>
        <div class="kb-modal-actions kb-modal-actions--end">
            <button class="kb-btn kb-btn--ghost" type="button" onclick="document.getElementById('moveFolderModal').classList.remove('active')">Đóng</button>
        </div>
    </div>
</div>

<template id="mediaItemTemplate">
    <div class="kb-media-item" data-id="">
        <div class="kb-media-item__preview">
            <img src="" alt="">
            <div class="kb-media-item__check">OK</div>
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
