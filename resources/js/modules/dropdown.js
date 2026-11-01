export function initDropdowns() {
    const closeDropdown = (root) => {
        const trigger = root.querySelector('[data-dropdown-trigger]');
        const panel = root.querySelector('[data-dropdown-panel]');

        trigger?.setAttribute('aria-expanded', 'false');
        panel?.classList.add('hidden');
        root.dataset.open = 'false';
    };

    const closeAll = (except = null) => {
        document.querySelectorAll('[data-dropdown]').forEach((root) => {
            if (root !== except) {
                closeDropdown(root);
            }
        });
    };

    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-dropdown-trigger]');

        if (trigger) {
            const root = trigger.closest('[data-dropdown]');
            const panel = root?.querySelector('[data-dropdown-panel]');
            const willOpen = root?.dataset.open !== 'true';

            closeAll(root);

            if (root && panel) {
                root.dataset.open = String(willOpen);
                trigger.setAttribute('aria-expanded', String(willOpen));
                panel.classList.toggle('hidden', !willOpen);
            }

            return;
        }

        if (!event.target.closest('[data-dropdown]')) {
            closeAll();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') {
            return;
        }

        const openDropdown = document.querySelector('[data-dropdown][data-open="true"]');

        if (openDropdown) {
            const trigger = openDropdown.querySelector('[data-dropdown-trigger]');
            closeDropdown(openDropdown);
            trigger?.focus();
        }
    });
}
