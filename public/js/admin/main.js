// Admin Javascript Main
document.addEventListener('DOMContentLoaded', function() {
    'use strict';
    
    // Sidebar Toggle Logic
    const toggleBtn = document.getElementById('toggleSidebar');
    const toggleCloseBtn = document.getElementById('toggleSidebarClose');
    const sidebar = document.getElementById('adminSidebar');
    const adminMain = document.getElementById('adminMain');
    
    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function() {
            if (window.innerWidth <= 992) {
                // Mobile behavior
                sidebar.classList.toggle('show');
            } else {
                // Desktop behavior
                sidebar.classList.toggle('kb-admin-sidebar--hidden');
                adminMain.classList.toggle('kb-admin-main--expanded');
            }
        });
    }

    if (toggleCloseBtn && sidebar) {
        toggleCloseBtn.addEventListener('click', function() {
            sidebar.classList.remove('show');
        });
    }

    // Tự động đóng alert sau 3 giây
    const alerts = document.querySelectorAll('.kb-alert');
    if (alerts.length > 0) {
        setTimeout(() => {
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            });
        }, 3000);
    }
});
