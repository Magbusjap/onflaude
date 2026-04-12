function ofApplyCollapsed() {
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

function ofRemoveCollapsed() {
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
    
    document.querySelectorAll('.fi-sidebar-group-items, .fi-sidebar-item, a.fi-sidebar-item-button').forEach(el => {
        el.style.width = '';
        el.style.display = '';
    });
}

function ofToggleSidebar() {
    const collapsed = document.body.classList.contains('of-sidebar-collapsed');
    if (collapsed) {
        document.body.classList.remove('of-sidebar-collapsed');
        localStorage.setItem('of_sidebar_collapsed', 'false');
        ofRemoveCollapsed();
    } else {
        document.body.classList.add('of-sidebar-collapsed');
        localStorage.setItem('of_sidebar_collapsed', 'true');
        ofApplyCollapsed();
    }
}

function ofRestoreSidebar() {
    if (localStorage.getItem('of_sidebar_collapsed') !== 'true') return;
    document.body.classList.add('of-sidebar-collapsed');
    ofApplyCollapsed();
}

document.addEventListener('DOMContentLoaded', function () {
    ofRestoreSidebar();
    document.querySelectorAll('.fi-sidebar-item').forEach(function (item) {
        const label = item.querySelector('.fi-sidebar-item-label');
        if (label) {
            item.setAttribute('data-label', label.textContent.trim());
        }
    });
});

document.addEventListener('alpine:initialized', ofRestoreSidebar);

window.addEventListener('load', function () {
    requestAnimationFrame(function () {
        requestAnimationFrame(ofRestoreSidebar);
    });
});