/**
 * Component: Sidebar Group Accordion
 *
 * Patches Filament's Alpine sidebar store so opening one navigation
 * group automatically collapses the others (single-open accordion).
 *
 * @module components/accordion
 */
export function init() {
    window.addEventListener('alpine:initialized', function () {
        const sidebar = window.Alpine?.store('sidebar');
        if (!sidebar) return;

        const originalToggle = sidebar.toggleCollapsedGroup.bind(sidebar);

        sidebar.toggleCollapsedGroup = function (label) {
            const groups = document.querySelectorAll(
                '.fi-sidebar-group[data-group-label]'
            );

            groups.forEach(function (group) {
                const groupLabel = group.dataset.groupLabel;
                if (!groupLabel || groupLabel === label) return;
                if (!sidebar.groupIsCollapsed(groupLabel)) {
                    originalToggle(groupLabel);
                }
            });

            originalToggle(label);
        };
    });
}
