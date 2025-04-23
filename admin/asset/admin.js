// Admin panel JavaScript functionality

// Sidebar toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    
    // Function to check window width and set classes accordingly
    function checkWidth() {
        if (window.innerWidth < 992) {
            sidebar.classList.remove('active');
            mainContent.classList.remove('sidebar-active');
        } else {
            sidebar.classList.add('active');
            sidebarOverlay.classList.remove('active');
        }
    }
    
    // Initialize on page load
    checkWidth();
    
    // Check width when window is resized
    window.addEventListener('resize', checkWidth);
    
    // Toggle sidebar when button is clicked
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            sidebarOverlay.classList.toggle('active');
            
            if (window.innerWidth < 992) {
                mainContent.classList.toggle('sidebar-active');
            }
        });
    }
    
    // Close sidebar when clicking on overlay
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
            mainContent.classList.remove('sidebar-active');
        });
    }
    
    // Close sidebar when clicking on a menu item (mobile only)
    const navLinks = document.querySelectorAll('.sidebar .nav-link');
    navLinks.forEach(function(link) {
        link.addEventListener('click', function() {
            if (window.innerWidth < 992) {
                sidebar.classList.remove('active');
                sidebarOverlay.classList.remove('active');
                mainContent.classList.remove('sidebar-active');
            }
        });
    });
});

// Enable tooltips everywhere
document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Enable toasts everywhere
document.addEventListener('DOMContentLoaded', function() {
    const toastElList = [].slice.call(document.querySelectorAll('.toast'));
    toastElList.map(function(toastEl) {
        return new bootstrap.Toast(toastEl);
    });
});

// Add active class to current page link
document.addEventListener('DOMContentLoaded', function() {
    const currentPath = window.location.pathname;
    const filename = currentPath.substring(currentPath.lastIndexOf('/') + 1);
    
    const navLinks = document.querySelectorAll('.sidebar .nav-link');
    navLinks.forEach(function(link) {
        const href = link.getAttribute('href');
        if (href === filename) {
            link.classList.add('active');
        }
    });
});

// Handle dark mode toggle if exists
document.addEventListener('DOMContentLoaded', function() {
    const darkModeToggle = document.getElementById('darkModeToggle');
    if (darkModeToggle) {
        // Check for saved theme preference or use preferred color scheme
        const prefersDarkMode = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        const savedTheme = localStorage.getItem('darkMode');
        
        // If there's a saved preference, use that
        if (savedTheme === 'true') {
            document.body.classList.add('dark-mode');
            darkModeToggle.checked = true;
        } else if (savedTheme === null && prefersDarkMode) {
            // If no saved preference but user's OS prefers dark mode
            document.body.classList.add('dark-mode');
            darkModeToggle.checked = true;
            localStorage.setItem('darkMode', 'true');
        }
        
        // Toggle dark mode on change
        darkModeToggle.addEventListener('change', function() {
            if (this.checked) {
                document.body.classList.add('dark-mode');
                localStorage.setItem('darkMode', 'true');
            } else {
                document.body.classList.remove('dark-mode');
                localStorage.setItem('darkMode', 'false');
            }
        });
    }
});
