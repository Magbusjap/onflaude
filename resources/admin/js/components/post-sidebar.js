/**
 * Component: Post Editor Sidebar Toggle
 *
 * Toggles the right-hand metadata column in the Filament post editor
 * by setting body[data-sidebar-hidden]. CSS in components/post-editor.css
 * collapses the column and expands the main column when this attribute
 * is present.
 *
 * Exposes window.ofTogglePostSidebar() — invoked from a toolbar button
 * inside the post edit page. Renamed from ofToggleSidebar to avoid name
 * collision with components/sidebar.js (left navigation toggle).
 *
 * @module components/post-sidebar
 */

export function init() {
    const setup = () => {
        Alpine.store('postSidebar', { hidden: false });

        Alpine.effect(() => {
            if (Alpine.store('postSidebar').hidden) {
                document.body.setAttribute('data-sidebar-hidden', '');
            } else {
                document.body.removeAttribute('data-sidebar-hidden');
            }
        });
    };

    if (window.Alpine?.version) {
        setup();
    } else {
        document.addEventListener('alpine:initialized', setup);
    }

    window.ofTogglePostSidebar = function () {
        Alpine.store('postSidebar').hidden = !Alpine.store('postSidebar').hidden;
    };
}
