const BATCH_SIZE = 6;

export function initLoadMore() {
    document.addEventListener('click', (event) => {
        const button = event.target.closest('[data-load-more]');

        if (!button) {
            return;
        }

        event.preventDefault();

        const scope = button.closest('[data-load-more-scope]') ?? document;
        const hiddenItems = [...scope.querySelectorAll('[data-more-item].hidden')];

        hiddenItems.slice(0, BATCH_SIZE).forEach((item) => item.classList.remove('hidden'));

        if (scope.querySelectorAll('[data-more-item].hidden').length === 0) {
            button.closest('[data-load-more-controls]')?.classList.add('hidden');
        }
    });
}
