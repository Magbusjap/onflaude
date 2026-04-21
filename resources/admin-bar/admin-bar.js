/**
 * OnFlaude Admin Bar — Client behaviour
 *
 * Dropdown menus for the floating frontend toolbar (@see admin-bar.blade.php).
 * Inlined into the page by App\Http\Middleware\InjectAdminBar. Self-invoking
 * IIFE — no module syntax so it works when inlined as a classic <script>.
 *
 * Platform-level component: themes do not import this file.
 */
(function () {
    function init() {
        const dropdowns = document.querySelectorAll('.of-admin-bar__item--dropdown');

        dropdowns.forEach((item) => {
            const trigger = item.querySelector('.of-admin-bar__trigger');
            const dropdown = item.querySelector('.of-admin-bar__dropdown');
            if (!trigger || !dropdown) return;

            trigger.addEventListener('click', (e) => {
                e.stopPropagation();
                const isOpen = dropdown.style.display === 'block';
                document.querySelectorAll('.of-admin-bar__dropdown').forEach((d) => {
                    d.style.display = 'none';
                });
                dropdown.style.display = isOpen ? 'none' : 'block';
            });
        });

        document.addEventListener('click', () => {
            document.querySelectorAll('.of-admin-bar__dropdown').forEach((d) => {
                d.style.display = 'none';
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
