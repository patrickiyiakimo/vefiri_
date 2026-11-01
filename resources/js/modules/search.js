export function initSearch() {
    document.querySelectorAll('[data-search]').forEach((form) => {
        const input = form.querySelector('[data-search-input]');
        const panel = form.querySelector('[data-search-panel]');

        if (!input || !panel) {
            return;
        }

        const suggestionButtons = [...panel.querySelectorAll('[data-search-suggestion]')];

        const open = () => panel.classList.remove('hidden');
        const close = () => panel.classList.add('hidden');

        const filterSuggestions = () => {
            const query = input.value.trim().toLowerCase();

            suggestionButtons.forEach((button) => {
                const matches = query === '' || button.dataset.searchSuggestion.toLowerCase().includes(query);
                button.classList.toggle('hidden', !matches);
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

        suggestionButtons.forEach((button) => {
            button.addEventListener('click', () => {
                input.value = button.dataset.searchSuggestion;
                form.requestSubmit();
            });
        });

        document.addEventListener('click', (event) => {
            if (!form.contains(event.target)) {
                close();
            }
        });
    });
}
