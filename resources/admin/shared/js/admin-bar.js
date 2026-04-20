/**
 * OnFlaude — Admin Bar (shared)
 *
 * Top toolbar visible to logged-in administrators on the public site.
 * Handles dropdown menus and click-outside behavior.
 *
 * Loaded by theme layout via @vite('resources/admin/shared/js/admin-bar.js').
 * This is a standalone entry point — it self-initializes on DOM ready.
 *
 * @module admin/shared/admin-bar
 */

function init() {
    const dropdowns = document.querySelectorAll('.of-admin-bar__item--dropdown');

    dropdowns.forEach(item => {
        const trigger = item.querySelector('.of-admin-bar__trigger');
        const dropdown = item.querySelector('.of-admin-bar__dropdown');

        if (!trigger || !dropdown) return;

        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = dropdown.style.display === 'block';

            // Close all other dropdowns first
            document.querySelectorAll('.of-admin-bar__dropdown').forEach(d => {
                d.style.display = 'none';
            });

            // Toggle current
            dropdown.style.display = isOpen ? 'none' : 'block';
        });
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', () => {
        document.querySelectorAll('.of-admin-bar__dropdown').forEach(d => {
            d.style.display = 'none';
        });
    });
}

// Self-initialization (this is a Vite entry point, not a module)
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}
