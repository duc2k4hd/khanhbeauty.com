const MediaApp = {
    currentPage: 1,
    lastPage: 1,
    currentFolder: '', // Mặc định là 'all'
    currentFilter: 'all',
    perPage: 50,
    searchQuery: '',
    isLoading: false,
    selectedItems: new Set(),
    
    // API Routes
    routes: {
        list: '/admin/media',
        upload: '/admin/media/upload',
        update: '/admin/media/update',
        delete: '/admin/media/delete',
        bulkDelete: '/admin/media/bulk-delete',
        move: '/admin/media/move',
        folders: '/admin/media/folders'
    },

    init() {
        this.cacheDOM();
        this.bindEvents();
        this.loadFolders();
        this.fetchMedia(1, true);
        this.initToast();
    },

    cacheDOM() {
        this.grid = document.getElementById('mediaGrid');
        this.loader = document.getElementById('mediaLoader');
        this.searchInput = document.getElementById('mediaSearch');
        this.uploadInput = document.getElementById('mediaUploadInput');
        this.folderList = document.getElementById('folderList');
        this.drawer = document.getElementById('mediaDetailDrawer');
        this.drawerContent = document.getElementById('mediaDetailContent');
        this.selectionActions = document.getElementById('selectionActions');
        this.selectCountText = document.getElementById('selectCount');
        this.perPageSelect = document.getElementById('mediaPerPage');
        
        // Modals
        this.moveModal = document.getElementById('moveFolderModal');
        this.moveFolderList = document.getElementById('moveFolderList');
        this.confirmDeleteModal = document.getElementById('confirmDeleteModal');
        this.folderNameModal = document.getElementById('folderNameModal');
        this.newFolderNameInput = document.getElementById('newFolderNameInput');
        
        // Progress Modal
        this.progressModal = document.getElementById('uploadProgressModal');
        this.progressBarFill = document.getElementById('uploadProgressBarFill');
        this.progressMsg = document.getElementById('uploadStatusMsg');
    },

    bindEvents() {
        // Search
        let searchTimeout;
        this.searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.searchQuery = e.target.value;
                this.fetchMedia(1, true);
            }, 500);
        });

        // Per Page Change
        this.perPageSelect.addEventListener('change', (e) => {
            this.perPage = e.target.value;
            this.fetchMedia(1, true);
        });

        // Filter Nav (Images, Videos, etc.)
        document.querySelectorAll('.kb-media-nav li[data-filter]').forEach(li => {
            li.addEventListener('click', () => {
                document.querySelectorAll('.kb-media-nav li').forEach(el => el.classList.remove('active'));
                li.classList.add('active');
                this.currentFilter = li.dataset.filter;
                
                // Nếu chọn "Tất cả tệp" ở hàng đầu (Quản lý), tự động reset folder về Gốc
                if (this.currentFilter === 'all') {
                    this.currentFolder = '';
                    this.loadFolders(); // Cập nhật lại UI Sidebar Folders
                }
                
                this.fetchMedia(1, true);
            });
        });

        // Infinite Scroll
        const gridWrapper = document.querySelector('.kb-media-grid-wrapper');
        gridWrapper.addEventListener('scroll', () => {
            if (gridWrapper.scrollTop + gridWrapper.clientHeight >= gridWrapper.scrollHeight - 100) {
                if (!this.isLoading && this.currentPage < this.lastPage) {
                    this.fetchMedia(this.currentPage + 1);
                }
            }
        });

        // Upload
        this.uploadInput.addEventListener('change', (e) => this.handleUpload(e));

        // Close Drawer
        document.getElementById('btnCloseDrawer').onclick = () => this.drawer.classList.remove('active');

        // Selection Actions
        document.getElementById('btnBulkDelete').onclick = () => this.handleBulkDelete();
        document.getElementById('btnBulkMove').onclick = () => this.handleBulkMove();
        
        // Folder Creation
        document.getElementById('btnCreateFolder').onclick = () => {
            this.folderNameModal.classList.add('active');
            this.newFolderNameInput.value = '';
            this.newFolderNameInput.focus();
        };

        document.getElementById('btnConfirmCreateFolder').onclick = () => {
            const name = this.newFolderNameInput.value.trim();
            if (name) {
                this.currentFolder = name;
                this.folderNameModal.classList.remove('active');
                this.loadFolders(); 
                this.fetchMedia(1, true);
            }
        };
    },

    async fetchMedia(page = 1, reset = false) {
        if (this.isLoading) return;
        this.isLoading = true;
        this.loader.style.display = 'block';
        if (reset) {
            this.grid.innerHTML = '';
            this.currentPage = 1;
            this.selectedItems.clear();
            this.updateSelectionUI();
        }

        const params = new URLSearchParams({
            page: page,
            folder: this.currentFolder,
            per_page: this.perPage,
            search: this.searchQuery,
            type: this.currentFilter === 'all' ? '' : this.currentFilter
        });

        try {
            const resp = await fetch(`${this.routes.list}?${params}`);
            const result = await resp.json();
            
            this.currentPage = result.current_page;
            this.lastPage = result.last_page;
            this.renderItems(result.data);
        } catch (err) {
            this.toast('Lỗi', 'Không thể tải danh sách tệp.', 'error');
        } finally {
            this.isLoading = false;
            this.loader.style.display = 'none';
        }
    },

    renderItems(items) {
        if (items.length === 0 && this.currentPage === 1) {
            this.grid.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:50px;color:#a0aec0;">Chưa có tệp tin nào trong thư mục này.</div>';
            return;
        }

        const template = document.getElementById('mediaItemTemplate');
        items.forEach(item => {
            const clone = template.content.cloneNode(true);
            const div = clone.querySelector('.kb-media-item');
            div.dataset.id = item.id;
            
            const img = clone.querySelector('img');
            if (item.mime_type.includes('image')) {
                img.src = item.file_url;
            } else if (item.mime_type.includes('video')) {
                img.src = '/images/admin/icon-video.png';
                img.style.padding = '20px';
                img.style.objectFit = 'contain';
            } else {
                img.src = '/images/admin/icon-file.png';
                img.style.padding = '25px';
            }

            clone.querySelector('.kb-media-item__name').textContent = item.file_name;
            
            div.onclick = (e) => this.toggleSelection(div, item, e);
            div.ondblclick = () => this.showDetail(item);

            this.grid.appendChild(clone);
        });
    },

    toggleSelection(el, item, event) {
        if (event.ctrlKey || event.metaKey) {
            if (this.selectedItems.has(item.id)) {
                this.selectedItems.delete(item.id);
                el.classList.remove('selected');
            } else {
                this.selectedItems.add(item.id);
                el.classList.add('selected');
            }
        } else {
            document.querySelectorAll('.kb-media-item').forEach(i => i.classList.remove('selected'));
            this.selectedItems.clear();
            this.selectedItems.add(item.id);
            el.classList.add('selected');
            this.showDetail(item);
        }
        this.updateSelectionUI();
    },

    updateSelectionUI() {
        const count = this.selectedItems.size;
        if (count > 0) {
            this.selectionActions.style.display = 'flex';
            this.selectCountText.textContent = `${count} chọn`;
        } else {
            this.selectionActions.style.display = 'none';
        }
    },

    async loadFolders() {
        const resp = await fetch(this.routes.folders);
        const folders = await resp.json();
        this.rawFolders = folders; // Lưu lại để dùng cho Move Modal
        this.folderList.innerHTML = '';
        
        // Add "Tất cả tệp" link as first folder if needed or just handle it
        const allLi = document.createElement('li');
        allLi.className = this.currentFolder === '' ? 'folder-item active' : 'folder-item';
        allLi.innerHTML = `<span>🏠</span> Tất cả tệp`;
        allLi.onclick = () => {
            this.currentFolder = '';
            this.folderList.querySelectorAll('li').forEach(l => l.classList.remove('active'));
            allLi.classList.add('active');
            this.fetchMedia(1, true);
        };
        this.folderList.appendChild(allLi);

        const defaultFolders = ['uploads', 'services', 'portfolios'];
        const allSet = new Set([...defaultFolders, ...folders]);
        
        allSet.forEach(f => {
            const li = document.createElement('li');
            li.className = f === this.currentFolder ? 'folder-item active' : 'folder-item';
            li.innerHTML = `<span>📂</span> ${f}`;
            li.dataset.folder = f;
            li.onclick = () => {
                this.currentFolder = f;
                this.folderList.querySelectorAll('li').forEach(l => l.classList.remove('active'));
                li.classList.add('active');
                this.fetchMedia(1, true);
            };
            this.folderList.appendChild(li);
        });
    },

    async handleUpload(e) {
        const files = Array.from(e.target.files);
        if (files.length === 0) return;

        this.progressModal.classList.add('active');
        this.progressBarFill.style.width = '0%';
        
        const chunkSize = 20;
        const totalChunks = Math.ceil(files.length / chunkSize);
        
        for (let i = 0; i < totalChunks; i++) {
            const chunk = files.slice(i * chunkSize, (i + 1) * chunkSize);
            this.progressMsg.textContent = `Đang tải đợt ${i + 1}/${totalChunks}...`;
            
            const formData = new FormData();
            chunk.forEach(file => formData.append('files[]', file));
            formData.append('folder', this.currentFolder || 'uploads');

            try {
                const resp = await fetch(this.routes.upload, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content },
                    body: formData
                });
                
                const result = await resp.json();

                if (!resp.ok) {
                    this.toast('Lỗi tải lên', result.message || 'Máy chủ từ chối yêu cầu.', 'error');
                    this.progressModal.classList.remove('active');
                    return; // Dừng ngay nếu có lỗi
                }
                
                const progress = ((i + 1) / totalChunks) * 100;
                this.progressBarFill.style.width = `${progress}%`;
                
                if (i === totalChunks - 1) {
                    const destination = this.currentFolder || 'gốc';
                    this.toast('Thành công', `Đã tải lên ${files.length} tệp vào thư mục ${destination}.`, 'success');
                    setTimeout(() => {
                        this.progressModal.classList.remove('active');
                        this.fetchMedia(1, true);
                    }, 1000);
                }
            } catch (err) {
                this.toast('Lỗi kết nối', 'Không thể kết nối đến máy chủ.', 'error');
                this.progressModal.classList.remove('active');
                break;
            }
        }
        this.uploadInput.value = '';
    },

    showDetail(item) {
        this.drawer.classList.add('active');
        this.drawerContent.innerHTML = `
            <div class="kb-media-detail-preview">
                <img src="${item.file_url}" style="width:100%; border-radius:12px; margin-bottom:15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
            </div>
            <div class="kb-form-group">
                <label>Tên tệp</label>
                <input type="text" class="kb-form-control" value="${item.file_name}" readonly>
            </div>
            <div class="kb-form-group">
                <label>Vị trí (Folder)</label>
                <input type="text" class="kb-form-control" value="${item.folder || 'Gốc'}" readonly>
            </div>
            <div class="kb-form-group">
                <label>URL</label>
                <div style="display:flex; gap:5px;">
                    <input type="text" id="copyUrlInput" class="kb-form-control" value="${item.file_url}" readonly>
                    <button class="kb-btn kb-btn--sm" onclick="MediaApp.copyUrl()" style="white-space:nowrap;">Copy</button>
                </div>
            </div>
            <hr style="margin:20px 0; border:0; border-top:1px solid #eee;">
            <div class="kb-form-group">
                <label>Alt (SEO)</label>
                <input type="text" id="meta_alt" class="kb-form-control" value="${item.alt_text || ''}">
            </div>
            <div class="kb-form-group">
                <label>Tiêu đề</label>
                <input type="text" id="meta_title" class="kb-form-control" value="${item.title || ''}">
            </div>
            <button class="kb-btn kb-btn--primary" onclick="MediaApp.saveMeta(${item.id})" style="width:100%; margin-top:15px;">Lưu Meta-data</button>
            <button class="kb-btn" onclick="MediaApp.deleteSingle(${item.id})" style="width:100%; margin-top:10px; background:#fff1f2; color:#e11d48; border:none;">Xóa Vĩnh Viễn</button>
        `;
    },

    copyUrl() {
        const input = document.getElementById('copyUrlInput');
        input.select();
        document.execCommand('copy');
        this.toast('Đã Copy', 'Đường dẫn ảnh đã được lưu vào bộ nhớ tạm.', 'info');
    },

    async saveMeta(id) {
        const alt = document.getElementById('meta_alt').value;
        const title = document.getElementById('meta_title').value;
        const resp = await fetch(`${this.routes.update}/${id}`, {
            method: 'PATCH',
            headers: { 
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ alt_text: alt, title: title })
        });
        if (resp.ok) this.toast('Thành công', 'Đã lưu thông tin ảnh.', 'success');
    },

    async deleteSingle(id) {
        this.confirmDeleteModal.classList.add('active');
        document.getElementById('confirmDeleteText').textContent = 'Hành động này sẽ xóa tệp tin này vĩnh viễn khỏi hệ thống của bạn.';
        
        document.getElementById('btnConfirmDeleteExec').onclick = async () => {
            this.confirmDeleteModal.classList.remove('active');
            this.drawer.classList.remove('active'); // Đóng ngay lập tức box phải để tránh treo UI
            
            const resp = await fetch(`${this.routes.delete}/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content }
            });
            if (resp.ok) {
                this.toast('Đã xóa', 'Tệp đã được loại bỏ.', 'success');
                this.fetchMedia(1, true);
            }
        };
    },

    async handleBulkDelete() {
        if (this.selectedItems.size === 0) return;
        
        this.confirmDeleteModal.classList.add('active');
        document.getElementById('confirmDeleteText').textContent = `Bạn có chắc chắn muốn xóa ${this.selectedItems.size} tệp đã chọn? Hành động này không thể hoàn tác.`;
        
        document.getElementById('btnConfirmDeleteExec').onclick = async () => {
            this.confirmDeleteModal.classList.remove('active');
            const resp = await fetch(this.routes.bulkDelete, {
                method: 'POST',
                headers: { 
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ ids: Array.from(this.selectedItems) })
            });
            if (resp.ok) {
                this.toast('Thành công', `Đã xóa ${this.selectedItems.size} tệp.`, 'success');
                this.fetchMedia(1, true);
            }
        };
    },

    async handleBulkMove() {
        if (this.selectedItems.size === 0) return;
        this.moveModal.classList.add('active');
        this.renderMoveFolderList();
    },

    renderMoveFolderList() {
        const defaultFolders = ['uploads', 'services', 'portfolios'];
        const allFolders = Array.from(new Set([...defaultFolders, ...this.rawFolders || []]));
        
        this.moveFolderList.innerHTML = '';
        allFolders.forEach(f => {
            const li = document.createElement('li');
            li.innerHTML = `<span>📁</span> ${f}`;
            li.onclick = () => this.confirmBulkMove(f);
            this.moveFolderList.appendChild(li);
        });
    },

    async confirmBulkMove(folder) {
        this.moveModal.classList.remove('active');
        const ids = Array.from(this.selectedItems);
        
        const resp = await fetch(this.routes.move, {
            method: 'POST',
            headers: { 
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ ids: ids, folder: folder })
        });
        
        if (resp.ok) {
            this.toast('Thành công', `Đã chuyển ${ids.length} tệp vào thư mục ${folder}.`, 'success');
            this.selectedItems.clear();
            document.querySelectorAll('.kb-media-item').forEach(i => i.classList.remove('selected'));
            this.updateSelectionUI();
            this.loadFolders();
            this.fetchMedia(1, true);
        } else {
            this.toast('Lỗi', 'Không thể di chuyển các tệp.', 'error');
        }
    },

    initToast() {
        this.toastContainer = document.createElement('div');
        this.toastContainer.className = 'kb-toast-container';
        document.body.appendChild(this.toastContainer);
    },

    toast(title, message, type = 'info') {
        const toastEl = document.createElement('div');
        toastEl.className = `kb-toast kb-toast--${type}`;
        const icon = type === 'success' ? '✅' : (type === 'error' ? '❌' : 'ℹ️');
        toastEl.innerHTML = `
            <div class="kb-toast__icon">${icon}</div>
            <div class="kb-toast__content">
                <h5>${title}</h5>
                <p>${message}</p>
            </div>
        `;
        this.toastContainer.appendChild(toastEl);
        setTimeout(() => toastEl.classList.add('active'), 10);
        setTimeout(() => {
            toastEl.classList.remove('active');
            setTimeout(() => toastEl.remove(), 500);
        }, 4000);
    }
};

document.addEventListener('DOMContentLoaded', () => MediaApp.init());
