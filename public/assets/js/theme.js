document.addEventListener('DOMContentLoaded', () => {
    const body = document.body;
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-icon');

    // Check for saved user preference
    if (localStorage.getItem('theme') === 'dark') {
        if (body) body.classList.add('dark-mode');
        if (themeIcon) {
            themeIcon.classList.replace('ph-moon', 'ph-sun');
        }
    }

    // Only attach listener if the toggle button exists on this page
    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            if (body) body.classList.toggle('dark-mode');
            
            // Check if the icon exists before changing its classes
            if (themeIcon) {
                if (body && body.classList.contains('dark-mode')) {
                    themeIcon.classList.replace('ph-moon', 'ph-sun');
                    localStorage.setItem('theme', 'dark');
                } else {
                    themeIcon.classList.replace('ph-sun', 'ph-moon');
                    localStorage.setItem('theme', 'light');
                }
            } else {
                // If there's no icon, still save the preference to localStorage
                if (body && body.classList.contains('dark-mode')) {
                    localStorage.setItem('theme', 'dark');
                } else {
                    localStorage.setItem('theme', 'light');
                }
            }
        });
    }
});