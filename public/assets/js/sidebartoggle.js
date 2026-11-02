document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    // Only run if the toggle button exists on the current page
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', () => {
            if (window.innerWidth <= 992) {
                if (sidebar) sidebar.classList.toggle('mobile-active');
                if (sidebarOverlay) sidebarOverlay.classList.toggle('mobile-active');
            } else {
                if (sidebar) sidebar.classList.toggle('collapsed');
            }
        });
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', () => {
            if (sidebar) sidebar.classList.remove('mobile-active');
            sidebarOverlay.classList.remove('mobile-active');
        });
    }

    window.addEventListener('resize', () => {
        if (window.innerWidth > 992) {
            if (sidebar) sidebar.classList.remove('mobile-active');
            if (sidebarOverlay) sidebarOverlay.classList.remove('mobile-active');
        }
    });
});