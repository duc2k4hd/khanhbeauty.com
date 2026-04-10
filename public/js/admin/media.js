const MediaApp = {
    nextCursor: null,
    hasMore: true,
    currentFolder: '',
    currentFilter: 'all',
    perPage: 50,
    searchQuery: '',
    isLoading: false,
    activeRequest: null,
    selectedItems: new Set(),
    mediaMap: new Map(),
    rawFolders: [],
    expandedFolders: new Set(),
    placeholderCache: {},
    uploadQueue: [],
    activeUploadControllers: new Set(),
    isUploadingQueue: false,
    cancelUploadRequested: false,
    uploadConfig: {
        concurrency: 3,
        maxFilesPerBatch: 10,
        maxBatchBytes: 32 * 1024 * 1024,
        maxPreviewRows: 60,
        maxLogRows: 40,
    },

    routes: {
        list: '/admin/media',
        upload: '/admin/media/upload',
        update: '/admin/media/update',
        delete: '/admin/media/delete',
        bulkDelete: '/admin/media/bulk-delete',
        move: '/admin/media/move',
        folders: '/admin/media/folders',
    },

    init() {
        this.cacheDOM();
        this.bindEvents();
        this.initToast();
        this.renderInspectorEmpty();
        this.loadFolders();
        this.reloadMedia();
    },

    cacheDOM() {
        this.grid = document.getElementById('mediaGrid');
        this.loader = document.getElementById('mediaLoader');
        this.searchInput = document.getElementById('mediaSearch');
        this.openUploadModalButton = document.getElementById('btnOpenUploadModal');
        this.uploadModal = document.getElementById('uploadModal');
        this.uploadInput = document.getElementById('mediaUploadInput');
        this.uploadDropzone = document.getElementById('uploadDropzone');
        this.selectUploadFilesButton = document.getElementById('btnSelectUploadFiles');
        this.closeUploadModalButton = document.getElementById('btnCloseUploadModal');
        this.clearUploadQueueButton = document.getElementById('btnClearUploadQueue');
        this.startUploadButton = document.getElementById('btnStartUpload');
        this.uploadQueueList = document.getElementById('uploadQueueList');
        this.uploadQueueStats = document.getElementById('uploadQueueStats');
        this.uploadTargetFolder = document.getElementById('uploadTargetFolder');
        this.folderList = document.getElementById('folderList');
        this.drawer = document.getElementById('mediaDetailDrawer');
        this.drawerContent = document.getElementById('mediaDetailContent');
        this.inspectorTitle = document.getElementById('mediaInspectorTitle');
        this.contextLabel = document.getElementById('mediaContextLabel');
        this.contextNote = document.getElementById('mediaContextNote');
        this.selectionActions = document.getElementById('selectionActions');
        this.selectCountText = document.getElementById('selectCount');
        this.perPageSelect = document.getElementById('mediaPerPage');
        this.gridWrapper = document.querySelector('.kb-media-grid-wrapper');
        this.libraryColumn = document.querySelector('.kb-media-library');

        this.moveModal = document.getElementById('moveFolderModal');
        this.moveFolderList = document.getElementById('moveFolderList');
        this.confirmDeleteModal = document.getElementById('confirmDeleteModal');
        this.folderNameModal = document.getElementById('folderNameModal');
        this.newFolderNameInput = document.getElementById('newFolderNameInput');

        this.progressModal = document.getElementById('uploadProgressModal');
        this.progressBarFill = document.getElementById('uploadProgressBarFill');
        this.progressMsg = document.getElementById('uploadStatusMsg');
        this.progressMeta = document.getElementById('uploadProgressMeta');
        this.uploadFileList = document.getElementById('uploadFileList');
        this.cancelUploadButton = document.getElementById('btnCancelUpload');
    },

    bindEvents() {
        let searchTimeout;

        this.searchInput.addEventListener('input', (event) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.searchQuery = event.target.value.trim();
                this.reloadMedia();
            }, 300);
        });

        this.perPageSelect.addEventListener('change', (event) => {
            this.perPage = Number(event.target.value) || 50;
            this.reloadMedia();
        });

        document.querySelectorAll('.kb-media-nav li[data-filter]').forEach((item) => {
            item.addEventListener('click', () => {
                document.querySelectorAll('.kb-media-nav li[data-filter]').forEach((node) => node.classList.remove('active'));
                item.classList.add('active');
                this.currentFilter = item.dataset.filter;
                this.reloadMedia();
            });
        });

        this.libraryColumn.addEventListener('scroll', () => {
            const reachedBottom = this.libraryColumn.scrollTop + this.libraryColumn.clientHeight >= this.libraryColumn.scrollHeight - 120;
            if (reachedBottom && !this.isLoading && this.hasMore && this.nextCursor) {
                this.fetchMedia(this.nextCursor);
            }
        });

        this.openUploadModalButton.addEventListener('click', () => this.openUploadModal());
        this.closeUploadModalButton.addEventListener('click', () => this.closeUploadModal());
        this.selectUploadFilesButton.addEventListener('click', () => this.uploadInput.click());
        this.uploadInput.addEventListener('change', (event) => this.enqueueUploadFiles(event.target.files));
        this.clearUploadQueueButton.addEventListener('click', () => this.clearUploadQueue());
        this.startUploadButton.addEventListener('click', () => this.startQueuedUpload());
        this.cancelUploadButton.addEventListener('click', () => this.cancelUploadQueue());
        document.getElementById('btnCloseDrawer').addEventListener('click', () => this.clearSelection());
        document.getElementById('btnBulkDelete').addEventListener('click', () => this.handleBulkDelete());
        document.getElementById('btnBulkMove').addEventListener('click', () => this.handleBulkMove());
        document.getElementById('btnCreateFolder').addEventListener('click', () => {
            this.folderNameModal.classList.add('active');
            this.newFolderNameInput.value = '';
            this.newFolderNameInput.focus();
        });
        document.getElementById('btnConfirmCreateFolder').addEventListener('click', () => this.createFolderFromInput());
        this.newFolderNameInput.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                this.createFolderFromInput();
            }
        });

        ['dragenter', 'dragover'].forEach((eventName) => {
            this.uploadDropzone.addEventListener(eventName, (event) => {
                event.preventDefault();
                event.stopPropagation();
                this.uploadDropzone.classList.add('is-dragover');
            });
        });

        ['dragleave', 'drop'].forEach((eventName) => {
            this.uploadDropzone.addEventListener(eventName, (event) => {
                event.preventDefault();
                event.stopPropagation();

                if (eventName === 'dragleave' && this.uploadDropzone.contains(event.relatedTarget)) {
                    return;
                }

                this.uploadDropzone.classList.remove('is-dragover');
            });
        });

        this.uploadDropzone.addEventListener('drop', (event) => {
            const files = event.dataTransfer?.files;
            if (files?.length) {
                this.enqueueUploadFiles(files);
            }
        });

        document.querySelectorAll('.kb-modal-overlay').forEach((modal) => {
            modal.addEventListener('click', (event) => {
                if (event.target === modal) {
                    if (modal === this.progressModal && this.isUploadingQueue) {
                        return;
                    }
                    modal.classList.remove('active');
                }
            });
        });
    },

    reloadMedia() {
        return this.fetchMedia(null, true);
    },

    async fetchMedia(cursorId = null, reset = false) {
        if (this.isLoading && !reset) {
            return;
        }

        if (reset && this.activeRequest) {
            this.activeRequest.abort();
        }

        const controller = new AbortController();
        this.activeRequest = controller;
        this.isLoading = true;
        this.loader.style.display = 'block';
        this.updateContext();

        if (reset) {
            this.grid.innerHTML = '';
            this.nextCursor = null;
            this.hasMore = true;
            this.mediaMap.clear();
            this.clearSelection();
            this.libraryColumn.scrollTop = 0;
        }

        const effectiveSearch = this.searchQuery.length >= 2 ? this.searchQuery : '';
        const params = new URLSearchParams({
            folder: this.currentFolder,
            per_page: String(this.perPage),
            search: effectiveSearch,
            type: this.currentFilter === 'all' ? '' : this.currentFilter,
        });

        if (cursorId) {
            params.set('cursor_id', String(cursorId));
        }

        try {
            const response = await fetch(`${this.routes.list}?${params.toString()}`, {
                signal: controller.signal,
            });

            if (!response.ok) {
                throw new Error('Không thể tải danh sách media.');
            }

            const result = await response.json();
            this.hasMore = Boolean(result.has_more);
            this.nextCursor = Number(result.next_cursor) || null;
            this.renderItems(Array.isArray(result.data) ? result.data : [], reset);
        } catch (error) {
            if (error.name === 'AbortError') {
                return;
            }

            this.toast('Lỗi tải dữ liệu', 'Không thể tải danh sách tệp. Vui lòng thử lại.', 'error');
        } finally {
            if (this.activeRequest === controller) {
                this.activeRequest = null;
                this.isLoading = false;
                this.loader.style.display = 'none';
            }
        }
    },

    renderItems(items, reset = false) {
        if (!items.length && reset) {
            this.grid.innerHTML = '<div class="kb-media-empty-grid">Chưa có tệp nào trong bộ lọc hiện tại.</div>';
            return;
        }

        const template = document.getElementById('mediaItemTemplate');
        const fragment = document.createDocumentFragment();

        items.forEach((item) => {
            this.mediaMap.set(item.id, item);

            const clone = template.content.cloneNode(true);
            const card = clone.querySelector('.kb-media-item');
            const image = clone.querySelector('img');
            const name = clone.querySelector('.kb-media-item__name');

            card.dataset.id = item.id;
            name.textContent = item.file_name || `Tệp #${item.id}`;

            const preview = this.resolvePreview(item);
            image.alt = item.file_name || 'Media';
            image.loading = 'lazy';
            image.decoding = 'async';
            image.style.objectFit = preview.objectFit;
            image.style.padding = preview.padding;
            this.applyManagedImage(image, preview.sources, {
                placeholderType: preview.placeholderType,
                label: preview.label,
                objectFit: preview.objectFit,
                padding: preview.padding,
            });

            card.addEventListener('click', (event) => this.toggleSelection(card, item, event));
            card.addEventListener('dblclick', () => {
                this.selectOnly(item.id);
                this.syncInspector();
            });

            if (this.selectedItems.has(item.id)) {
                card.classList.add('selected');
            }

            fragment.appendChild(clone);
        });

        this.grid.appendChild(fragment);
    },

    resolvePreview(item) {
        const mime = item.mime_type || '';
        const thumbnails = item.thumbnails && typeof item.thumbnails === 'object' ? item.thumbnails : {};

        if (mime.startsWith('image/')) {
            return {
                sources: [thumbnails.admin, thumbnails.medium, thumbnails.small, item.file_url],
                placeholderType: 'image',
                label: 'áº¢NH',
                objectFit: 'cover',
                padding: '0',
            };
        }

        if (mime.startsWith('video/')) {
            return {
                sources: [],
                placeholderType: 'video',
                label: 'VIDEO',
                objectFit: 'contain',
                padding: '22px',
            };
        }

        return {
            sources: [],
            placeholderType: 'file',
            label: this.placeholderLabelFromItem(item),
            objectFit: 'contain',
            padding: '24px',
        };
    },    applyManagedImage(image, sources, options = {}) {
        const queue = (sources || [])
            .filter(Boolean)
            .map((src) => this.normalizeMediaUrl(src));

        const applyFallback = () => {
            image.onerror = null;
            image.src = this.getPlaceholderSrc(options.placeholderType || 'file', options.label || 'FILE');
            image.style.objectFit = 'contain';
            image.style.padding = options.padding || '24px';
        };

        const loadNext = () => {
            const nextSrc = queue.shift();
            if (!nextSrc) {
                applyFallback();
                return;
            }

            image.src = nextSrc;
        };

        image.onerror = () => {
            loadNext();
        };

        loadNext();
    },

    normalizeMediaUrl(url = '') {
        if (!url) {
            return '';
        }

        try {
            const parsed = new URL(url, window.location.origin);
            const sameOrigin = parsed.origin === window.location.origin;
            const sameLocalPair = ['127.0.0.1', 'localhost'].includes(parsed.hostname)
                && ['127.0.0.1', 'localhost'].includes(window.location.hostname)
                && parsed.port === window.location.port
                && parsed.protocol === window.location.protocol;

            if (sameOrigin || sameLocalPair) {
                return `${parsed.pathname}${parsed.search}${parsed.hash}`;
            }

            return parsed.href;
        } catch (error) {
            return url;
        }
    },

    placeholderLabelFromItem(item) {
        const mime = item.mime_type || '';

        if (mime.startsWith('image/')) {
            return 'áº¢NH';
        }

        if (mime.startsWith('video/')) {
            return 'VIDEO';
        }

        if (mime.startsWith('audio/')) {
            return 'AUDIO';
        }

        const extension = (item.file_name || '').split('.').pop();
        return extension ? extension.slice(0, 6).toUpperCase() : 'FILE';
    },

    getPlaceholderSrc(type = 'file', label = 'FILE') {
        const key = `${type}:${label}`;
        if (this.placeholderCache[key]) {
            return this.placeholderCache[key];
        }

        const palette = {
            image: { bg: '#fdf2f8', border: '#f9a8d4', text: '#be185d' },
            video: { bg: '#eff6ff', border: '#93c5fd', text: '#1d4ed8' },
            file: { bg: '#f8fafc', border: '#cbd5e1', text: '#334155' },
        };

        const tone = palette[type] || palette.file;
        const safeLabel = this.escapeHtml(label);
        const svg = `
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 320">
                <rect width="320" height="320" rx="28" fill="${tone.bg}" />
                <rect x="24" y="24" width="272" height="272" rx="24" fill="none" stroke="${tone.border}" stroke-width="4" stroke-dasharray="10 10" />
                <text x="160" y="150" text-anchor="middle" font-size="26" font-family="Arial, sans-serif" fill="${tone.text}" font-weight="700">${safeLabel}</text>
                <text x="160" y="188" text-anchor="middle" font-size="14" font-family="Arial, sans-serif" fill="${tone.text}">Không có preview</text>
            </svg>
        `;

        const uri = `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(svg.replace(/\s+/g, ' ').trim())}`;
        this.placeholderCache[key] = uri;
        return uri;
    },

    toggleSelection(card, item, event) {
        const isMulti = event.ctrlKey || event.metaKey;

        if (isMulti) {
            if (this.selectedItems.has(item.id)) {
                this.selectedItems.delete(item.id);
                card.classList.remove('selected');
            } else {
                this.selectedItems.add(item.id);
                card.classList.add('selected');
            }
        } else {
            this.selectOnly(item.id);
        }

        this.updateSelectionUI();
        this.syncInspector();
    },

    selectOnly(id) {
        this.selectedItems.clear();
        this.selectedItems.add(id);
        this.refreshSelectedCards();
        this.updateSelectionUI();
    },

    clearSelection() {
        this.selectedItems.clear();
        this.refreshSelectedCards();
        this.updateSelectionUI();
        this.renderInspectorEmpty();
    },

    refreshSelectedCards() {
        document.querySelectorAll('.kb-media-item').forEach((card) => {
            const id = Number(card.dataset.id);
            card.classList.toggle('selected', this.selectedItems.has(id));
        });
    },

    updateSelectionUI() {
        const count = this.selectedItems.size;
        this.drawer.classList.toggle('active', count > 0);
        this.selectCountText.textContent = `${count} tệp được chọn`;

        if (count > 1) {
            this.selectionActions.classList.add('active');
        } else {
            this.selectionActions.classList.remove('active');
        }
    },

    syncInspector() {
        const ids = Array.from(this.selectedItems);

        if (ids.length === 0) {
            this.renderInspectorEmpty();
            return;
        }

        if (ids.length === 1) {
            const item = this.mediaMap.get(ids[0]);
            if (item) {
                this.showDetail(item);
                return;
            }
        }

        this.renderBulkSummary(ids);
    },

    renderInspectorEmpty(message = 'Nhấp vào một tệp ở cột giữa để xem trước, sửa metadata và thao tác trực tiếp.') {
        this.inspectorTitle.textContent = 'Chưa chọn tệp';
        this.drawerContent.innerHTML = `
            <div class="kb-media-empty-state">
                <div class="kb-media-empty-state__icon">MEDIA</div>
                <h4>Chọn một tệp để thao tác</h4>
                <p>${this.escapeHtml(message)}</p>
                <ul>
                    <li>Nhấp một tệp để mở thông tin chi tiết.</li>
                    <li>Giữ Ctrl hoặc Cmd để chọn nhiều tệp.</li>
                    <li>Dùng cột bên phải để thao tác mà không làm che lưới media bên trái.</li>
                </ul>
            </div>
        `;
    },

    renderBulkSummary(ids) {
        const names = ids
            .map((id) => this.mediaMap.get(id)?.file_name)
            .filter(Boolean)
            .slice(0, 6)
            .map((name) => `<li>${this.escapeHtml(name)}</li>`)
            .join('');

        this.inspectorTitle.textContent = `${ids.length} tệp đang được chọn`;
        this.drawerContent.innerHTML = `
            <div class="kb-media-bulk-state">
                <h4>Thao tác hàng loạt</h4>
                <p>Bạn đang chọn nhiều tệp. Hãy di chuyển hoặc xóa ở đây để không phải quay lại thanh công cụ phía trên.</p>
                <div class="kb-media-detail__actions">
                    <button class="kb-btn kb-btn--action" type="button" onclick="MediaApp.handleBulkMove()">Di chuyển thư mục</button>
                    <button class="kb-btn kb-btn--danger" type="button" onclick="MediaApp.handleBulkDelete()">Xóa các tệp đã chọn</button>
                </div>
                <ul>${names || '<li>Danh sách tệp sẽ được áp dụng theo lựa chọn hiện tại.</li>'}</ul>
            </div>
        `;
    },

    showDetail(item) {
        const mime = item.mime_type || 'application/octet-stream';
        const folder = item.folder || 'Gốc';
        const size = this.formatBytes(item.file_size_bytes || 0);
        const preview = mime.startsWith('image/')
            ? `<div class="kb-media-detail__preview"><img data-detail-preview alt="${this.escapeHtml(item.file_name || 'Media')}"></div>`
            : `<div class="kb-media-detail__placeholder">${this.escapeHtml(this.placeholderLabelFromItem(item))}</div>`;

        this.inspectorTitle.textContent = item.file_name || `Media #${item.id}`;
        this.drawerContent.innerHTML = `
            <div class="kb-media-detail">
                ${preview}
                <div class="kb-media-detail__meta">
                    <span class="kb-chip">${this.escapeHtml(mime)}</span>
                    <span class="kb-chip">Thư mục: ${this.escapeHtml(folder)}</span>
                    <span class="kb-chip">Dung lượng: ${this.escapeHtml(size)}</span>
                </div>
                <p class="kb-media-detail__caption">Sử dụng bảng này để thao tác với ảnh mà không làm che lưới media bên trái.</p>
                <div class="kb-media-detail__actions">
                    <button class="kb-btn kb-btn--ghost" type="button" onclick="MediaApp.copyUrl('${this.escapeJs(item.file_url || '')}')">Copy URL</button>
                    <button class="kb-btn kb-btn--action" type="button" onclick="MediaApp.moveSingle(${item.id})">Di chuyển thư mục</button>
                </div>
                <div class="kb-form-group">
                    <label>Tên tệp</label>
                    <input type="text" class="kb-form-control" value="${this.escapeHtml(item.file_name || '')}" readonly>
                </div>
                <div class="kb-form-group">
                    <label>Alt text</label>
                    <input type="text" id="meta_alt" class="kb-form-control" value="${this.escapeHtml(item.alt_text || '')}" placeholder="Mô tả ảnh phục vụ SEO">
                </div>
                <div class="kb-form-group">
                    <label>Tiêu đề</label>
                    <input type="text" id="meta_title" class="kb-form-control" value="${this.escapeHtml(item.title || '')}" placeholder="Tiêu đề ngáº¯n gá»n cho áº£nh">
                </div>
                <div class="kb-media-detail__actions">
                    <button class="kb-btn kb-btn--primary" type="button" onclick="MediaApp.saveMeta(${item.id})">Lưu metadata</button>
                    <button class="kb-btn kb-btn--danger" type="button" onclick="MediaApp.deleteSingle(${item.id})">Xóa tệp</button>
                </div>
            </div>
        `;

        const detailPreview = this.drawerContent.querySelector('[data-detail-preview]');
        if (detailPreview) {
            detailPreview.decoding = 'async';
            this.applyManagedImage(detailPreview, [item.file_url], {
                placeholderType: 'image',
                label: this.placeholderLabelFromItem(item),
                objectFit: 'cover',
                padding: '0',
            });
        }
    },

    async loadFolders() {
        try {
            const response = await fetch(this.routes.folders);
            if (!response.ok) {
                throw new Error('Không thể tải danh sách thư mục.');
            }

            const folders = await response.json();
            this.rawFolders = Array.isArray(folders) ? folders : [];
        } catch (error) {
            this.rawFolders = [];
        }

        const defaultFolders = ['uploads', 'services', 'portfolios'];
        const allFolders = Array.from(new Set([...defaultFolders, ...this.rawFolders]))
            .filter(Boolean);

        this.ensureExpandedPath(this.currentFolder);
        this.folderList.innerHTML = '';

        const allItem = document.createElement('li');
        allItem.className = this.currentFolder === '' ? 'folder-item active' : 'folder-item';
        allItem.innerHTML = `
            <span class="kb-folder-toggle kb-folder-toggle--spacer" aria-hidden="true"></span>
            <span class="kb-media-nav__icon">DIR</span>
            <span class="kb-folder-label">Tất cả thư mục</span>
        `;
        allItem.addEventListener('click', () => {
            this.currentFolder = '';
            this.loadFolders();
            this.reloadMedia();
        });
        this.folderList.appendChild(allItem);

        const tree = this.buildFolderTree(allFolders);
        this.flattenFolderTree(tree).forEach(({ node, depth }) => {
            const item = document.createElement('li');
            const active = node.path === this.currentFolder;
            const hasChildren = node.children.length > 0;
            const expanded = this.expandedFolders.has(node.path);

            item.className = [
                'folder-item',
                active ? 'active' : '',
                hasChildren ? 'folder-item--expandable' : '',
            ].filter(Boolean).join(' ');
            item.style.setProperty('--folder-depth', String(depth + 1));
            item.dataset.folder = node.path;
            item.innerHTML = `
                ${hasChildren
                    ? `<button class="kb-folder-toggle" type="button" data-role="toggle" aria-label="${expanded ? 'Thu gọn thư mục con' : 'Mở thư mục con'}">${expanded ? '-' : '+'}</button>`
                    : '<span class="kb-folder-toggle kb-folder-toggle--spacer" aria-hidden="true"></span>'}
                <span class="kb-media-nav__icon">DIR</span>
                <span class="kb-folder-label" title="${this.escapeHtml(node.path)}">${this.escapeHtml(this.folderDisplayName(node, depth))}</span>
            `;

            item.addEventListener('click', (event) => {
                if (event.target.closest('[data-role="toggle"]')) {
                    return;
                }

                if (hasChildren && !this.expandedFolders.has(node.path)) {
                    this.expandedFolders.add(node.path);
                }

                this.currentFolder = node.path;
                this.loadFolders();
                this.reloadMedia();
            });

            const toggle = item.querySelector('[data-role="toggle"]');
            if (toggle) {
                toggle.addEventListener('click', (event) => {
                    event.preventDefault();
                    event.stopPropagation();
                    this.toggleFolderExpansion(node.path);
                });
            }

            this.folderList.appendChild(item);
        });

        this.updateContext();
    },

    buildFolderTree(folders) {
        const roots = [];
        const nodeMap = new Map();

        folders.forEach((folder) => {
            const segments = folder.split('/').filter(Boolean);
            let parentPath = '';
            let branch = roots;

            segments.forEach((segment) => {
                const path = parentPath ? `${parentPath}/${segment}` : segment;
                let node = nodeMap.get(path);

                if (!node) {
                    node = {
                        path,
                        name: segment,
                        children: [],
                    };

                    nodeMap.set(path, node);
                    branch.push(node);
                }

                parentPath = path;
                branch = node.children;
            });
        });

        return roots;
    },

    flattenFolderTree(nodes, depth = 0, rows = []) {
        nodes.forEach((node) => {
            rows.push({ node, depth });

            if (node.children.length > 0 && this.expandedFolders.has(node.path)) {
                this.flattenFolderTree(node.children, depth + 1, rows);
            }
        });

        return rows;
    },

    folderDisplayName(node, depth) {
        return depth === 0 ? node.path : node.name;
    },

    toggleFolderExpansion(folder) {
        if (this.expandedFolders.has(folder)) {
            this.expandedFolders.delete(folder);
        } else {
            this.expandedFolders.add(folder);
        }

        this.loadFolders();
    },

    ensureExpandedPath(folder) {
        if (!folder) {
            return;
        }

        const segments = folder.split('/').filter(Boolean);
        let path = '';

        segments.slice(0, -1).forEach((segment) => {
            path = path ? `${path}/${segment}` : segment;
            this.expandedFolders.add(path);
        });
    },

    createFolderFromInput() {
        const name = this.newFolderNameInput.value.trim();
        if (!name) {
            this.toast('Thiếu tên thư mục', 'Vui lòng nhập tên thư mục trước khi tạo.', 'error');
            return;
        }

        this.currentFolder = name;
        this.folderNameModal.classList.remove('active');
        this.loadFolders();
        this.reloadMedia();
        this.toast('Đã tạo thư mục', `Thư mục ${name} đã sẵn sàng để tải tệp vào.`, 'success');
        this.syncUploadDestination();
    },

    openUploadModal() {
        this.syncUploadDestination();
        this.renderUploadQueue();
        this.uploadModal.classList.add('active');
    },

    closeUploadModal() {
        if (this.isUploadingQueue) {
            return;
        }

        this.uploadModal.classList.remove('active');
    },

    syncUploadDestination() {
        const folder = this.currentFolder || 'uploads';
        this.uploadTargetFolder.textContent = folder;
    },

    enqueueUploadFiles(fileList) {
        const files = Array.from(fileList || []);
        if (!files.length) {
            return;
        }

        this.uploadQueue.push(...files);
        this.uploadInput.value = '';
        this.syncUploadDestination();
        this.renderUploadQueue();
        this.uploadModal.classList.add('active');
        this.toast('Đã thêm vào hàng đợi', `Đã thêm ${files.length} tệp chờ tải lên.`, 'info');
    },

    clearUploadQueue() {
        if (this.isUploadingQueue) {
            return;
        }

        this.uploadQueue = [];
        this.uploadInput.value = '';
        this.renderUploadQueue();
    },

    renderUploadQueue() {
        const folder = this.currentFolder || 'uploads';
        const totalFiles = this.uploadQueue.length;
        const totalBytes = this.uploadQueue.reduce((sum, file) => sum + (Number(file.size) || 0), 0);
        const batches = this.buildUploadBatches(this.uploadQueue);

        this.uploadTargetFolder.textContent = folder;

        if (!totalFiles) {
            this.uploadQueueStats.textContent = 'Chưa có tệp nào trong hàng đợi.';
            this.uploadQueueList.innerHTML = '<div class="kb-upload-empty">Chưa có tệp nào được chọn.</div>';
            this.startUploadButton.disabled = true;
            this.clearUploadQueueButton.disabled = true;
            return;
        }

        this.uploadQueueStats.innerHTML = `
            <strong>${this.formatNumber(totalFiles)} tệp</strong>, tổng ${this.formatBytes(totalBytes)}.<br>
            Dự kiến ${this.formatNumber(batches.length)} đợt, tối đa ${this.uploadConfig.maxFilesPerBatch} tệp hoặc ${this.formatBytes(this.uploadConfig.maxBatchBytes)} mỗi đợt, chạy ${this.uploadConfig.concurrency} luồng song song.
        `;

        const previewFiles = this.uploadQueue.slice(0, this.uploadConfig.maxPreviewRows);
        this.uploadQueueList.innerHTML = previewFiles.map((file) => `
            <div class="kb-upload-file-row">
                <strong title="${this.escapeHtml(file.name)}">${this.escapeHtml(file.name)}</strong>
                <span>${this.formatBytes(file.size)}</span>
            </div>
        `).join('');

        if (totalFiles > previewFiles.length) {
            this.uploadQueueList.insertAdjacentHTML('beforeend', `
                <div class="kb-upload-empty">
                    Còn ${this.formatNumber(totalFiles - previewFiles.length)} tệp nữa trong hàng đợi. Danh sách chỉ hiển thị một phần để giữ giao diện mượt.
                </div>
            `);
        }

        this.startUploadButton.disabled = false;
        this.clearUploadQueueButton.disabled = false;
    },

    buildUploadBatches(files) {
        const batches = [];
        let currentBatch = [];
        let currentBytes = 0;

        files.forEach((file) => {
            const fileSize = Number(file.size) || 0;
            const exceedsFileCount = currentBatch.length >= this.uploadConfig.maxFilesPerBatch;
            const exceedsBatchBytes = currentBatch.length > 0 && currentBytes + fileSize > this.uploadConfig.maxBatchBytes;

            if (exceedsFileCount || exceedsBatchBytes) {
                batches.push(currentBatch);
                currentBatch = [];
                currentBytes = 0;
            }

            currentBatch.push(file);
            currentBytes += fileSize;

            if (currentBatch.length === 1 && fileSize >= this.uploadConfig.maxBatchBytes) {
                batches.push(currentBatch);
                currentBatch = [];
                currentBytes = 0;
            }
        });

        if (currentBatch.length) {
            batches.push(currentBatch);
        }

        return batches;
    },

    async startQueuedUpload() {
        if (this.isUploadingQueue) {
            return;
        }

        if (!this.uploadQueue.length) {
            this.toast('Chưa có tệp', 'Hãy chọn hoặc kéo tệp vào hàng đợi trước khi tải lên.', 'error');
            return;
        }

        const folder = this.currentFolder || 'uploads';
        const batches = this.buildUploadBatches(this.uploadQueue);
        const totalFiles = this.uploadQueue.length;

        this.isUploadingQueue = true;
        this.cancelUploadRequested = false;
        this.progressModal.classList.add('active');
        this.uploadModal.classList.remove('active');
        this.cancelUploadButton.textContent = 'Hủy';
        this.progressBarFill.style.width = '0%';
        this.progressMsg.textContent = `Chuẩn bị tải ${this.formatNumber(totalFiles)} tệp vào thư mục ${folder}...`;
        this.renderUploadProgressMeta({
            uploadedFiles: 0,
            failedFiles: 0,
            processedFiles: 0,
            totalFiles,
            completedBatches: 0,
            totalBatches: batches.length,
        });
        this.uploadFileList.innerHTML = '<div class="kb-upload-empty">Đang chuẩn bị gửi các đợt đầu tiên...</div>';

        try {
            const summary = await this.processUploadQueue(batches, folder, totalFiles);
            this.uploadQueue = summary.retryFiles;
            this.renderUploadQueue();
            await this.reloadMedia();

            if (this.cancelUploadRequested) {
                this.progressMsg.textContent = 'Đã dừng tải lên theo yêu cầu.';
                this.toast('Đã hủy tải lên', `Còn ${this.formatNumber(this.uploadQueue.length)} tệp trong hàng đợi để bạn chạy lại sau.`, 'info');
                return;
            }

            if (summary.failedFiles > 0) {
                this.progressMsg.textContent = `Hoàn tất một phần: ${this.formatNumber(summary.uploadedFiles)} thành công, ${this.formatNumber(summary.failedFiles)} lỗi.`;
                this.toast(
                    'Tải lên hoàn tất một phần',
                    `Đã tải ${this.formatNumber(summary.uploadedFiles)} tệp. Còn ${this.formatNumber(summary.retryFiles.length)} tệp trong hàng đợi để thử lại.`,
                    'error'
                );
                return;
            }

            this.progressMsg.textContent = `Đã tải lên hoàn tất ${this.formatNumber(summary.uploadedFiles)} tệp.`;
            this.toast('Tải lên thành công', `Đã tải ${this.formatNumber(summary.uploadedFiles)} tệp vào thư mục ${folder}.`, 'success');
        } finally {
            this.isUploadingQueue = false;
            this.cancelUploadRequested = false;
            this.activeUploadControllers.clear();
            this.uploadInput.value = '';
            this.cancelUploadButton.textContent = 'Đóng';
        }
    },

    async processUploadQueue(batches, folder, totalFiles) {
        const summary = {
            uploadedFiles: 0,
            failedFiles: 0,
            processedFiles: 0,
            completedBatches: 0,
            totalBatches: batches.length,
            retryFiles: [],
        };
        const succeededBatchIndexes = new Set();
        let nextBatchIndex = 0;

        const workers = Array.from({ length: Math.min(this.uploadConfig.concurrency, batches.length) }, async () => {
            while (true) {
                if (this.cancelUploadRequested) {
                    return;
                }

                const batchIndex = nextBatchIndex;
                nextBatchIndex += 1;

                if (batchIndex >= batches.length) {
                    return;
                }

                const batch = batches[batchIndex];
                this.appendUploadLog(
                    `Đang gửi đợt ${batchIndex + 1}/${batches.length} (${this.formatNumber(batch.length)} tệp, ${this.formatBytes(batch.reduce((sum, file) => sum + (Number(file.size) || 0), 0))}).`,
                    'info'
                );

                try {
                    await this.uploadBatch(batch, folder);
                    succeededBatchIndexes.add(batchIndex);
                    summary.uploadedFiles += batch.length;
                    this.appendUploadLog(`Hoàn tất đợt ${batchIndex + 1}/${batches.length}.`, 'success');
                } catch (error) {
                    if (error.name === 'AbortError' && this.cancelUploadRequested) {
                        this.appendUploadLog(`Đã dừng đợt ${batchIndex + 1}/${batches.length}.`, 'error');
                    } else {
                        summary.failedFiles += batch.length;
                        this.appendUploadLog(`Lỗi đợt ${batchIndex + 1}/${batches.length}: ${error.message}`, 'error');
                    }
                } finally {
                    summary.completedBatches += 1;
                    summary.processedFiles = summary.uploadedFiles + summary.failedFiles;
                    this.progressMsg.textContent = this.cancelUploadRequested
                        ? 'Đang dừng các request đang chạy...'
                        : `Đã xử lý ${this.formatNumber(summary.processedFiles)}/${this.formatNumber(totalFiles)} tệp vào thư mục ${folder}.`;
                    this.progressBarFill.style.width = `${totalFiles ? Math.round((summary.processedFiles / totalFiles) * 100) : 0}%`;
                    this.renderUploadProgressMeta({ ...summary, totalFiles });
                }
            }
        });

        await Promise.all(workers);

        summary.retryFiles = batches
            .map((batch, index) => ({ batch, index }))
            .filter(({ index }) => !succeededBatchIndexes.has(index))
            .flatMap(({ batch }) => batch);

        return summary;
    },

    async uploadBatch(batch, folder) {
        const formData = new FormData();
        batch.forEach((file) => formData.append('files[]', file));
        formData.append('folder', folder);

        const controller = new AbortController();
        this.activeUploadControllers.add(controller);

        try {
            const response = await fetch(this.routes.upload, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': this.csrfToken() },
                body: formData,
                signal: controller.signal,
            });

            const result = await response.json().catch(() => ({}));
            if (!response.ok) {
                throw new Error(result.message || 'Máy chủ từ chối một đợt tải lên.');
            }

            return result;
        } finally {
            this.activeUploadControllers.delete(controller);
        }
    },

    cancelUploadQueue() {
        if (!this.isUploadingQueue) {
            this.progressModal.classList.remove('active');
            return;
        }

        this.cancelUploadRequested = true;
        this.progressMsg.textContent = 'Đang hủy các request đang chạy...';
        this.activeUploadControllers.forEach((controller) => controller.abort());
    },

    renderUploadProgressMeta(summary) {
        this.progressMeta.innerHTML = `
            <div class="kb-upload-progress__metric">
                <strong>${this.formatNumber(summary.processedFiles || 0)}/${this.formatNumber(summary.totalFiles || 0)}</strong>
                <span>Tệp đã xử lý</span>
            </div>
            <div class="kb-upload-progress__metric">
                <strong>${this.formatNumber(summary.completedBatches || 0)}/${this.formatNumber(summary.totalBatches || 0)}</strong>
                <span>Đợt đã chạy</span>
            </div>
            <div class="kb-upload-progress__metric">
                <strong>${this.formatNumber(summary.failedFiles || 0)}</strong>
                <span>Tệp lỗi</span>
            </div>
        `;
    },

    appendUploadLog(message, type = 'info') {
        if (this.uploadFileList.querySelector('.kb-upload-empty')) {
            this.uploadFileList.innerHTML = '';
        }

        const row = document.createElement('div');
        row.className = `kb-upload-file-row kb-upload-file-row--log-${type}`;
        row.innerHTML = `
            <strong>${this.escapeHtml(message)}</strong>
            <span>${new Date().toLocaleTimeString('vi-VN')}</span>
        `;

        this.uploadFileList.prepend(row);

        while (this.uploadFileList.children.length > this.uploadConfig.maxLogRows) {
            this.uploadFileList.removeChild(this.uploadFileList.lastElementChild);
        }
    },
    async saveMeta(id) {
        const alt = document.getElementById('meta_alt')?.value || '';
        const title = document.getElementById('meta_title')?.value || '';

        try {
            const response = await fetch(`${this.routes.update}/${id}`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken(),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ alt_text: alt, title }),
            });

            if (!response.ok) {
                throw new Error('Không thể lưu metadata.');
            }

            const item = this.mediaMap.get(id);
            if (item) {
                item.alt_text = alt;
                item.title = title;
                this.mediaMap.set(id, item);
            }

            this.toast('Đã lưu metadata', 'Thông tin ảnh đã được cập nhật.', 'success');
        } catch (error) {
            this.toast('Lỗi lưu dữ liệu', error.message || 'Không thể lưu metadata.', 'error');
        }
    },

    copyUrl(url = '') {
        if (!url) {
            this.toast('Thiếu URL', 'Tệp này hiện chưa có đường dẫn khả dụng để sao chép.', 'error');
            return;
        }

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(url)
                .then(() => this.toast('Đã copy URL', 'Đường dẫn ảnh đã được lưu vào bộ nhớ tạm.', 'info'))
                .catch(() => this.fallbackCopyUrl(url));
            return;
        }

        this.fallbackCopyUrl(url);
    },

    fallbackCopyUrl(url) {
        const input = document.createElement('input');
        input.value = url;
        document.body.appendChild(input);
        input.select();
        document.execCommand('copy');
        input.remove();
        this.toast('Đã copy URL', 'Đường dẫn ảnh đã được lưu vào bộ nhớ tạm.', 'info');
    },

    moveSingle(id) {
        this.selectOnly(id);
        this.syncInspector();
        this.handleBulkMove();
    },

    async deleteSingle(id) {
        this.confirmDeleteModal.classList.add('active');
        document.getElementById('confirmDeleteText').textContent = 'Tệp này sẽ bị xóa khỏi hệ thống cùng toàn bộ liên kết vật lý tương ứng.';

        document.getElementById('btnConfirmDeleteExec').onclick = async () => {
            this.confirmDeleteModal.classList.remove('active');

            try {
                const response = await fetch(`${this.routes.delete}/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': this.csrfToken() },
                });

                if (!response.ok) {
                    throw new Error('Không thể xóa tệp.');
                }

                this.toast('Đã xóa tệp', 'Tệp đã được loại bỏ khỏi hệ thống.', 'success');
                this.reloadMedia();
            } catch (error) {
                this.toast('Lỗi xóa tệp', error.message || 'Không thể xóa tệp.', 'error');
            }
        };
    },

    async handleBulkDelete() {
        if (!this.selectedItems.size) {
            return;
        }

        const count = this.selectedItems.size;
        this.confirmDeleteModal.classList.add('active');
        document.getElementById('confirmDeleteText').textContent = `Bạn sắp xóa ${count} tệp đã chọn. Hành động này không thể hoàn tác.`;

        document.getElementById('btnConfirmDeleteExec').onclick = async () => {
            this.confirmDeleteModal.classList.remove('active');

            try {
                const response = await fetch(this.routes.bulkDelete, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken(),
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ ids: Array.from(this.selectedItems) }),
                });

                if (!response.ok) {
                    throw new Error('Không thể xóa các tệp đã chọn.');
                }

                this.toast('Đã xóa hàng loạt', `Đã xóa ${count} tệp khỏi thư viện.`, 'success');
                this.reloadMedia();
            } catch (error) {
                this.toast('Lỗi xóa hàng loạt', error.message || 'Không thể xóa các tệp đã chọn.', 'error');
            }
        };
    },

    handleBulkMove() {
        if (!this.selectedItems.size) {
            return;
        }

        this.moveModal.classList.add('active');
        this.renderMoveFolderList();
    },

    renderMoveFolderList() {
        const defaultFolders = ['uploads', 'services', 'portfolios'];
        const folders = Array.from(new Set([...defaultFolders, ...(this.rawFolders || [])]));
        this.moveFolderList.innerHTML = '';

        folders.forEach((folder) => {
            const item = document.createElement('li');
            item.textContent = folder;
            item.addEventListener('click', () => this.confirmBulkMove(folder));
            this.moveFolderList.appendChild(item);
        });
    },

    async confirmBulkMove(folder) {
        const ids = Array.from(this.selectedItems);
        this.moveModal.classList.remove('active');

        try {
            const response = await fetch(this.routes.move, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken(),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ ids, folder }),
            });

            if (!response.ok) {
                throw new Error('Không thể di chuyển các tệp đã chọn.');
            }

            this.toast('Đã di chuyển', `Đã chuyển ${ids.length} tệp sang thư mục ${folder}.`, 'success');
            this.loadFolders();
            this.reloadMedia();
        } catch (error) {
            this.toast('Lỗi di chuyển', error.message || 'Không thể di chuyển các tệp đã chọn.', 'error');
        }
    },    updateContext() {
        const filterLabels = {
            all: 'Tất cả tệp',
            image: 'Hình ảnh',
            video: 'Video',
            audio: 'Âm thanh',
        };

        const folderLabel = this.currentFolder || 'mọi thư mục';
        this.contextLabel.textContent = `${filterLabels[this.currentFilter] || 'Media'} · ${folderLabel}`;
        this.contextNote.textContent = this.currentSearchSummary();
    },

    currentSearchSummary() {
        if (this.searchQuery) {
            if (this.searchQuery.length < 2) {
                return 'Nhập ít nhất 2 ký tự để bật tìm kiếm nhanh.';
            }

            return `Đang lọc theo từ khóa: "${this.searchQuery}".`;
        }

        if (this.currentFolder) {
            return `Đang xem các tệp trong thư mục ${this.currentFolder}.`;
        }

        return 'Chọn tệp ở cột giữa, thao tác ở cột phải.';
    },

    initToast() {
        this.toastContainer = document.createElement('div');
        this.toastContainer.className = 'kb-toast-container';
        document.body.appendChild(this.toastContainer);
    },

    toast(title, message, type = 'info') {
        const icons = {
            success: 'OK',
            error: '!',
            info: 'i',
        };

        const toast = document.createElement('div');
        toast.className = `kb-toast kb-toast--${type}`;
        toast.innerHTML = `
            <div class="kb-toast__icon">${icons[type] || 'i'}</div>
            <div class="kb-toast__content">
                <h5>${this.escapeHtml(title)}</h5>
                <p>${this.escapeHtml(message)}</p>
            </div>
        `;

        this.toastContainer.appendChild(toast);
        requestAnimationFrame(() => toast.classList.add('active'));
        setTimeout(() => {
            toast.classList.remove('active');
            setTimeout(() => toast.remove(), 350);
        }, 3800);
    },

    csrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.content || '';
    },

    formatBytes(bytes) {
        if (!bytes) {
            return '0 B';
        }

        const units = ['B', 'KB', 'MB', 'GB'];
        let value = Number(bytes);
        let unitIndex = 0;

        while (value >= 1024 && unitIndex < units.length - 1) {
            value /= 1024;
            unitIndex += 1;
        }

        return `${value.toFixed(value >= 10 || unitIndex === 0 ? 0 : 1)} ${units[unitIndex]}`;
    },

    formatNumber(value) {
        return new Intl.NumberFormat('vi-VN').format(Number(value) || 0);
    },

    escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    },

    escapeJs(value) {
        return String(value ?? '').replace(/\\/g, '\\\\').replace(/'/g, "\\'");
    },
};

document.addEventListener('DOMContentLoaded', () => MediaApp.init());
