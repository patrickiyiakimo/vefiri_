export function initSearch() {
    document.querySelectorAll('[data-search]').forEach((form) => {
        const input = form.querySelector('[data-search-input]');
        const panel = form.querySelector('[data-search-panel]');
        const categorySelect = form.querySelector('[data-search-category-select]');

        if (!input || !panel) {
            return;
        }

        const suggestionItems = [...panel.querySelectorAll('[data-search-suggestion]')];

        const open = () => panel.classList.remove('hidden');
        const close = () => panel.classList.add('hidden');

        const filterSuggestions = () => {
            const query = input.value.trim().toLowerCase();

            suggestionItems.forEach((item) => {
                const matches = query === '' || item.dataset.searchSuggestion.toLowerCase().includes(query);
                item.classList.toggle('hidden', !matches);
            });
        };

        input.addEventListener('focus', () => {
            filterSuggestions();
            open();
        });

        input.addEventListener('input', () => {
            filterSuggestions();
            open();
        });

        input.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                close();
            }
        });

        suggestionItems.forEach((item) => {
            if (item.hasAttribute('data-search-category-link')) {
                return;
            }

            item.addEventListener('click', () => {
                input.value = item.dataset.searchSuggestion;
                form.requestSubmit();
            });
        });

        categorySelect?.addEventListener('change', () => {
            const url = categorySelect.selectedOptions[0]?.dataset.url;

            if (url) {
                window.location.assign(url);
            }
        });

        document.addEventListener('click', (event) => {
            if (!form.contains(event.target)) {
                close();
            }
        });
    });
}
