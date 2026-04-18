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

    window.ofToggleSidebar = function() {
        Alpine.store('postSidebar').hidden = !Alpine.store('postSidebar').hidden;
    };
}