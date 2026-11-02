const focusableSelector = [
    'a[href]',
    'button:not([disabled])',
    'input:not([disabled])',
    'select:not([disabled])',
    'textarea:not([disabled])',
    '[tabindex]:not([tabindex="-1"])',
].join(',');

const csrfToken = () => {
    return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
};

const focusableElements = (container) => {
    return [...container.querySelectorAll(focusableSelector)].filter((element) => {
        return element.offsetParent !== null;
    });
};

export function initAuthModal() {
    const modal = document.querySelector('[data-auth-modal]');

    if (!modal) {
        return;
    }

    let lastFocusedElement = null;
    let closeTimer = null;

    const tabs = [...modal.querySelectorAll('[data-auth-tab]')];
    const panels = [...modal.querySelectorAll('[data-auth-panel]')];

    const activePanel = () => {
        return panels.find((panel) => !panel.classList.contains('hidden')) ?? panels[0];
    };

    const setView = (view, focus = false) => {
        tabs.forEach((tab) => {
            const active = tab.dataset.authTab === view;

            tab.dataset.active = String(active);
            tab.setAttribute('aria-selected', String(active));
        });

        panels.forEach((panel) => {
            panel.classList.toggle('hidden', panel.dataset.authPanel !== view);
        });

        if (focus) {
            activePanel()?.querySelector('input:not([type="hidden"])')?.focus({ preventScroll: true });
        }
    };

    const open = (view = 'login') => {
        window.clearTimeout(closeTimer);

        lastFocusedElement = document.activeElement;
        setView(view);
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        window.requestAnimationFrame(() => {
            modal.dataset.open = 'true';

            const target = activePanel()?.querySelector('input:not([type="hidden"])');
            target?.focus({ preventScroll: true });
        });
    };

    const close = (restoreFocus = true) => {
        delete modal.dataset.open;

        closeTimer = window.setTimeout(() => {
            modal.classList.add('hidden');

            if (!document.querySelector('[data-drawer][data-open="true"]')) {
                document.body.style.overflow = '';
            }

            if (restoreFocus && lastFocusedElement instanceof HTMLElement) {
                lastFocusedElement.focus({ preventScroll: true });
            }
        }, 300);
    };

    const clearErrors = (form) => {
        form.querySelectorAll('[data-auth-error]').forEach((element) => {
            element.textContent = '';
            element.classList.add('hidden');
        });

        form.querySelectorAll('[aria-invalid="true"]').forEach((element) => {
            element.setAttribute('aria-invalid', 'false');
        });
    };

    const showErrors = (form, errors = {}, message = '') => {
        let firstInvalidInput = null;

        Object.entries(errors).forEach(([field, messages]) => {
            const errorElement = form.querySelector(`[data-auth-error="${field}"]`);
            const input = form.querySelector(`[name="${field}"]`);
            const text = Array.isArray(messages) ? messages[0] : messages;

            if (errorElement) {
                errorElement.textContent = text;
                errorElement.classList.remove('hidden');
            }

            if (input) {
                input.setAttribute('aria-invalid', 'true');
                firstInvalidInput ??= input;
            }
        });

        if (firstInvalidInput) {
            firstInvalidInput.focus({ preventScroll: true });
        }

        if (message && Object.keys(errors).length === 0) {
            const generalError = form.querySelector('[data-auth-error="general"]');

            if (generalError) {
                generalError.textContent = message;
                generalError.classList.remove('hidden');
            }
        }
    };

    const setSubmitting = (form, submitting) => {
        const button = form.querySelector('[data-auth-submit]');
        const label = form.querySelector('[data-auth-submit-label]');

        if (!button || !label) {
            return;
        }

        if (!button.dataset.originalLabel) {
            button.dataset.originalLabel = label.textContent.trim();
        }

        button.disabled = submitting;
        button.setAttribute('aria-busy', String(submitting));
        label.textContent = submitting ? 'Please wait...' : button.dataset.originalLabel;
    };

    const submit = async (form) => {
        clearErrors(form);
        setSubmitting(form, true);

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken(),
                },
                body: new FormData(form),
                credentials: 'same-origin',
            });

            const data = await response.json().catch(() => ({}));

            if (!response.ok) {
                showErrors(form, data.errors ?? {}, data.message ?? 'Something went wrong. Please try again.');
                return;
            }

            window.location.assign(data.redirect ?? window.location.href);
        } catch {
            showErrors(form, {}, 'Unable to connect. Please check your internet connection and try again.');
        } finally {
            setSubmitting(form, false);
        }
    };

    document.addEventListener('vefiri:open-auth', (event) => {
        open(event.detail?.view === 'register' ? 'register' : 'login');
    });

    modal.querySelectorAll('[data-auth-form]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            submit(form);
        });
    });

    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-auth-open]');

        if (trigger) {
            event.preventDefault();
            open(trigger.dataset.authOpen === 'register' ? 'register' : 'login');
            return;
        }

        const tab = event.target.closest('[data-auth-tab]');

        if (tab && modal.contains(tab)) {
            event.preventDefault();
            setView(tab.dataset.authTab, true);
            return;
        }

        if (event.target.closest('[data-auth-close]')) {
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

    setView(modal.dataset.initialView === 'register' ? 'register' : 'login');

    if (modal.dataset.autoOpen) {
        open(modal.dataset.autoOpen === 'register' ? 'register' : 'login');
    }
}
