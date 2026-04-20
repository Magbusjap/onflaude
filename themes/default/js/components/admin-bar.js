export function init() {
    const dropdowns = document.querySelectorAll('.of-admin-bar__item--dropdown');

    dropdowns.forEach(item => {
        const trigger = item.querySelector('.of-admin-bar__trigger');
        const dropdown = item.querySelector('.of-admin-bar__dropdown');

        if (!trigger || !dropdown) return;

        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = dropdown.style.display === 'block';
            document.querySelectorAll('.of-admin-bar__dropdown').forEach(d => {
                d.style.display = 'none';
            });
            dropdown.style.display = isOpen ? 'none' : 'block';
        });
    });

    document.addEventListener('click', () => {
        document.querySelectorAll('.of-admin-bar__dropdown').forEach(d => {
            d.style.display = 'none';
        });
    });
}