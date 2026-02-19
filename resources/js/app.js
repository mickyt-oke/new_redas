document.addEventListener("DOMContentLoaded", function() {
    
    // ---------------------------------------------------
    // Sidebar Toggle Logic
    // ---------------------------------------------------
    var el = document.getElementById("wrapper");
    var toggleButton = document.getElementById("menu-toggle");

    if (toggleButton) {
        toggleButton.onclick = function () {
            el.classList.toggle("toggled");
        };
    }

    // ---------------------------------------------------
    // Dark Mode Logic
    // ---------------------------------------------------
    const darkModeSwitch = document.getElementById('darkModeSwitch');
    const body = document.body;

    // Check local storage for preference on load
    if (localStorage.getItem('theme') === 'dark') {
        document.documentElement.classList.add('dark');
        if(darkModeSwitch) darkModeSwitch.checked = true;
    }

    // Toggle event listener
    if (darkModeSwitch) {
        darkModeSwitch.addEventListener('change', () => {
            if (darkModeSwitch.checked) {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            }
        });
    }

    // ---------------------------------------------------
    // Tooltip Initialization (Bootstrap 5)
    // ---------------------------------------------------
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // ---------------------------------------------------
    // Back to Top Logic
    // ---------------------------------------------------
    const backToTopButton = document.getElementById("back-to-top");

    if (backToTopButton) {
        window.addEventListener("scroll", () => {
            if (window.scrollY > 300) {
                backToTopButton.style.display = "flex";
            } else {
                backToTopButton.style.display = "none";
            }
        });

        backToTopButton.addEventListener("click", () => {
            window.scrollTo({ top: 0, behavior: "smooth" });
        });
    }

    // ---------------------------------------------------
    // Transparent Navbar on Scroll (Landing Page)
    // ---------------------------------------------------
    const mainNavbar = document.getElementById('mainNavbar');
    if (mainNavbar) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                mainNavbar.classList.add('bg-white', 'shadow-sm', 'navbar-light');
                mainNavbar.classList.remove('navbar-dark');
            } else {
                mainNavbar.classList.remove('bg-white', 'shadow-sm', 'navbar-light');
                mainNavbar.classList.add('navbar-dark');
            }
        });
    }

    // ---------------------------------------------------
    // Dynamic Active Sidebar Link
    // ---------------------------------------------------
    const currentPath = window.location.pathname.split("/").pop();
    const sidebarLinks = document.querySelectorAll('#sidebar-wrapper .list-group-item-action');

    sidebarLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href && href !== '#' && !href.startsWith('#') && (href === currentPath || href.endsWith('/' + currentPath))) {
            sidebarLinks.forEach(l => l.classList.remove('active'));
            link.classList.add('active');
        }
    });
});

// System Status / Maintenance Logic
function setSystemStatus(status) {
    const overlay = document.getElementById('maintenanceOverlay');
    if (!overlay) return;
    status === 'Maintenance' ? overlay.classList.remove('d-none') : overlay.classList.add('d-none');
}