/**
 * Component: Sidebar
 *
 * Left navigation sidebar collapse/expand toggle.
 * - Persists state in localStorage ('of_sidebar_collapsed').
 * - Manipulates DOM widths on collapse to free vertical space for icons.
 * - Restores state on DOMContentLoaded, alpine:initialized, and window load
 *   (Alpine re-renders sidebar after navigation).
 *
 * Exposes window.ofToggleSidebar() — invoked from the sidebar toggle button
 * rendered by the panels::sidebar.footer renderHook.
 *
 * @module components/sidebar
 */

function applyCollapsed() {
    const nav = document.querySelector('.fi-sidebar-nav');
    const navGroups = document.querySelector('.fi-sidebar-nav-groups');

    if (nav) {
        nav.classList.remove('overflow-x-hidden');
        nav.classList.add('overflow-x-visible');
    }
    if (navGroups) {
        navGroups.style.marginLeft = '0';
        navGroups.style.marginRight = '0';
        navGroups.style.width = '100%';
    }

    document.querySelectorAll('.fi-sidebar-group-items').forEach(el => {
        el.style.width = '100%';
    });
    document.querySelectorAll('.fi-sidebar-item').forEach(el => {
        el.style.width = '100%';
    });
    document.querySelectorAll('a.fi-sidebar-item-button').forEach(el => {
        el.style.width = '100%';
        el.style.display = 'flex';
    });
}

function removeCollapsed() {
    const nav = document.querySelector('.fi-sidebar-nav');
    const navGroups = document.querySelector('.fi-sidebar-nav-groups');

    if (nav) {
        nav.classList.add('overflow-x-hidden');
        nav.classList.remove('overflow-x-visible');
    }
    if (navGroups) {
        navGroups.style.marginLeft = '';
        navGroups.style.marginRight = '';
        navGroups.style.width = '';
    }

    document.querySelectorAll(
        '.fi-sidebar-group-items, .fi-sidebar-item, a.fi-sidebar-item-button'
    ).forEach(el => {
        el.style.width = '';
        el.style.display = '';
    });
}

function toggle() {
    const collapsed = document.body.classList.contains('of-sidebar-collapsed');
    if (collapsed) {
        document.body.classList.remove('of-sidebar-collapsed');
        localStorage.setItem('of_sidebar_collapsed', 'false');
        removeCollapsed();
    } else {
        document.body.classList.add('of-sidebar-collapsed');
        localStorage.setItem('of_sidebar_collapsed', 'true');
        applyCollapsed();
    }
}

function restore() {
    if (localStorage.getItem('of_sidebar_collapsed') !== 'true') return;
    document.body.classList.add('of-sidebar-collapsed');
    applyCollapsed();
}

export function init() {
    document.addEventListener('DOMContentLoaded', function () {
        restore();
        document.querySelectorAll('.fi-sidebar-item').forEach(function (item) {
            const label = item.querySelector('.fi-sidebar-item-label');
            if (label) {
                item.setAttribute('data-label', label.textContent.trim());
            }
        });
    });

    document.addEventListener('alpine:initialized', restore);

    window.addEventListener('load', function () {
        requestAnimationFrame(function () {
            requestAnimationFrame(restore);
        });
    });

    window.ofToggleSidebar = toggle;
}
