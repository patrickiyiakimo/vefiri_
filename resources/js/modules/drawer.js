const focusableSelector = [
    'a[href]',
    'button:not([disabled])',
    'input:not([disabled])',
    'select:not([disabled])',
    'textarea:not([disabled])',
    '[tabindex]:not([tabindex="-1"])',
].join(',');

let activeDrawer = null;
let lastFocusedElement = null;
let closeTimer = null;

const focusableElements = (drawer) => {
    return [...drawer.querySelectorAll(focusableSelector)].filter((element) => {
        return element.offsetParent !== null;
    });
};

export function openDrawer(id) {
    const drawer = document.getElementById(id);

    if (!drawer) {
        return;
    }

    window.clearTimeout(closeTimer);

    document.querySelectorAll('[data-drawer][data-open="true"]').forEach((openDrawerElement) => {
        delete openDrawerElement.dataset.open;
        openDrawerElement.classList.add('hidden');
    });

    activeDrawer = drawer;
    lastFocusedElement = document.activeElement;
    drawer.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    window.requestAnimationFrame(() => {
        drawer.dataset.open = 'true';

        const firstFocusable = focusableElements(drawer)[0];
        firstFocusable?.focus({ preventScroll: true });
    });
}

export function closeDrawer(drawer = activeDrawer, restoreFocus = true) {
    if (!drawer) {
        return;
    }

    delete drawer.dataset.open;

    if (activeDrawer === drawer) {
        activeDrawer = null;
    }

    closeTimer = window.setTimeout(() => {
        drawer.classList.add('hidden');

        if (activeDrawer) {
            return;
        }

        if (!document.querySelector('[data-auth-modal][data-open="true"]')) {
            document.body.style.overflow = '';
        }

        if (restoreFocus && !document.querySelector('[data-auth-modal][data-open="true"]') && lastFocusedElement instanceof HTMLElement) {
            lastFocusedElement.focus({ preventScroll: true });
        }
    }, 300);
}

export function initDrawers() {
    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-drawer-open]');

        if (trigger) {
            event.preventDefault();
            openDrawer(trigger.dataset.drawerOpen);
            return;
        }

        const closeTrigger = event.target.closest('[data-drawer-close]');

        if (closeTrigger) {
            const drawer = closeTrigger.closest('[data-drawer]');

            if (drawer) {
                event.preventDefault();
                closeDrawer(drawer);
            }
        }
    });

    document.addEventListener('keydown', (event) => {
        if (!activeDrawer) {
            return;
        }

        if (event.key === 'Escape') {
            event.preventDefault();
            closeDrawer();
            return;
        }

        if (event.key !== 'Tab') {
            return;
        }

        const focusables = focusableElements(activeDrawer);

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
}
