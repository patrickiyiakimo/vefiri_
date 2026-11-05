const focusableSelector = [
    'a[href]',
    'button:not([disabled])',
    'input:not([disabled])',
    'select:not([disabled])',
    'textarea:not([disabled])',
    '[tabindex]:not([tabindex="-1"])',
].join(',');

const focusableElements = (container) => {
    return [...container.querySelectorAll(focusableSelector)].filter((element) => {
        return element.offsetParent !== null;
    });
};

export function initLimitModal() {
    const modal = document.querySelector('[data-limit-modal]');

    if (!modal) {
        return;
    }

    let lastFocusedElement = null;
    let closeTimer = null;

    const open = () => {
        window.clearTimeout(closeTimer);

        lastFocusedElement = document.activeElement;
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        window.requestAnimationFrame(() => {
            modal.dataset.open = 'true';
            modal.querySelector('[data-limit-modal-primary]')?.focus({ preventScroll: true });
        });
    };

    const close = (restoreFocus = true) => {
        delete modal.dataset.open;

        closeTimer = window.setTimeout(() => {
            modal.classList.add('hidden');

            if (!document.querySelector('[data-auth-modal][data-open="true"]') && !document.querySelector('[data-drawer][data-open="true"]')) {
                document.body.style.overflow = '';
            }

            if (restoreFocus && lastFocusedElement instanceof HTMLElement) {
                lastFocusedElement.focus({ preventScroll: true });
            }
        }, 300);
    };

    document.addEventListener('click', (event) => {
        if (event.target.closest('[data-limit-modal-close]')) {
            event.preventDefault();
            close();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (modal.classList.contains('hidden')) {
            return;
        }

        if (event.key === 'Escape') {
            event.preventDefault();
            close();
            return;
        }

        if (event.key !== 'Tab') {
            return;
        }

        const focusables = focusableElements(modal);

        if (focusables.length === 0) {
            return;
        }

        const first = focusables[0];
        const last = focusables[focusables.length - 1];

        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    });

    open();
}
