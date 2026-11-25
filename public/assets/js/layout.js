document.addEventListener('DOMContentLoaded', () => {
    const body = document.body;
    const toggle = document.getElementById('sidebarToggle');
    const backdrop = document.getElementById('sidebarBackdrop');
    const sidebar = document.getElementById('appSidebar');
    if (!toggle || !backdrop || !sidebar) return;

    const mobileViewport = window.matchMedia('(max-width: 767.98px)');
    const savedState = localStorage.getItem('redes-sidebar-collapsed') === 'true';

    if (!mobileViewport.matches && savedState) {
        body.classList.add('sidebar-collapsed');
    }

    function updateToggleState(expanded) {
        toggle.setAttribute('aria-expanded', String(expanded));
        toggle.setAttribute('aria-label', expanded ? 'Contraer menú' : 'Expandir menú');
    }

    function closeMobileMenu() {
        body.classList.remove('sidebar-mobile-open');
        updateToggleState(false);
    }

    updateToggleState(!body.classList.contains('sidebar-collapsed'));

    toggle.addEventListener('click', () => {
        if (mobileViewport.matches) {
            const isOpen = body.classList.toggle('sidebar-mobile-open');
            updateToggleState(isOpen);
            return;
        }

        const isCollapsed = body.classList.toggle('sidebar-collapsed');
        body.classList.remove('sidebar-hover-expanded');
        localStorage.setItem('redes-sidebar-collapsed', String(isCollapsed));
        updateToggleState(!isCollapsed);
    });

    sidebar.addEventListener('mouseenter', () => {
        if (!mobileViewport.matches && body.classList.contains('sidebar-collapsed')) {
            body.classList.add('sidebar-hover-expanded');
            updateToggleState(true);
        }
    });

    sidebar.addEventListener('mouseleave', () => {
        if (body.classList.contains('sidebar-collapsed')) {
            body.classList.remove('sidebar-hover-expanded');
            updateToggleState(false);
        }
    });

    backdrop.addEventListener('click', closeMobileMenu);

    sidebar.querySelectorAll('.sidebar-link').forEach((link) => {
        link.addEventListener('click', () => {
            if (mobileViewport.matches) closeMobileMenu();
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && body.classList.contains('sidebar-mobile-open')) {
            closeMobileMenu();
            toggle.focus();
        }
    });

    mobileViewport.addEventListener('change', (event) => {
        body.classList.remove('sidebar-mobile-open');
        body.classList.remove('sidebar-hover-expanded');
        if (event.matches) {
            updateToggleState(false);
        } else {
            updateToggleState(!body.classList.contains('sidebar-collapsed'));
        }
    });
});
