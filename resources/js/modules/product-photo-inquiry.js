import { showToast } from './toast';

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

export function initProductPhotoInquiry() {
    const modal = document.querySelector('[data-photo-inquiry-modal]');

    if (!modal) {
        return;
    }

    const form = modal.querySelector('[data-photo-inquiry-form]');
    const submitButton = form.querySelector('[data-photo-inquiry-submit]');
    const submitLabel = submitButton.textContent.trim();
    let lastFocusedElement = null;
    let closeTimer = null;

    const clearErrors = () => {
        form.querySelectorAll('[data-photo-inquiry-error]').forEach((element) => {
            element.textContent = '';
            element.classList.add('hidden');
        });

        form.querySelectorAll('[aria-invalid="true"]').forEach((element) => {
            element.setAttribute('aria-invalid', 'false');
        });
    };

    const showErrors = (errors = {}, message = '') => {
        let firstInvalidInput = null;

        Object.entries(errors).forEach(([field, messages]) => {
            const errorElement = form.querySelector(`[data-photo-inquiry-error="${field}"]`);
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
        } else if (message) {
            const generalError = form.querySelector('[data-photo-inquiry-error="general"]');

            if (generalError) {
                generalError.textContent = message;
                generalError.classList.remove('hidden');
            }
        }
    };

    const open = () => {
        window.clearTimeout(closeTimer);
        clearErrors();

        lastFocusedElement = document.activeElement;
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        window.requestAnimationFrame(() => {
            modal.dataset.open = 'true';
            form.querySelector('[name="image"]')?.focus({ preventScroll: true });
        });
    };

    const close = () => {
        delete modal.dataset.open;

        closeTimer = window.setTimeout(() => {
            modal.classList.add('hidden');

            const hasOtherOverlay = document.querySelector('[data-auth-modal][data-open="true"], [data-drawer][data-open="true"]');

            if (!hasOtherOverlay) {
                document.body.style.overflow = '';
            }

            if (lastFocusedElement instanceof HTMLElement) {
                lastFocusedElement.focus({ preventScroll: true });
            }
        }, 300);
    };

    document.addEventListener('click', (event) => {
        if (event.target.closest('[data-photo-inquiry-open]')) {
            event.preventDefault();
            open();

            return;
        }

        if (event.target.closest('[data-photo-inquiry-close]')) {
            event.preventDefault();
            close();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (modal.dataset.open !== 'true') {
            return;
        }

        if (event.key === 'Escape') {
            close();

            return;
        }

        if (event.key !== 'Tab') {
            return;
        }

        const elements = focusableElements(modal);

        if (elements.length === 0) {
            return;
        }

        const first = elements[0];
        const last = elements[elements.length - 1];

        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearErrors();

        submitButton.disabled = true;
        submitButton.textContent = 'Sending...';

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken(),
                },
            });
            const payload = await response.json().catch(() => ({}));

            if (!response.ok) {
                showErrors(payload.errors ?? {}, payload.message ?? 'Please check the highlighted fields.');

                return;
            }

            form.reset();
            close();
            showToast(payload.message ?? 'Photo sent. Our team will review it shortly.');
        } catch {
            showErrors({}, 'Unable to send right now. Please try again.');
        } finally {
            submitButton.disabled = false;
            submitButton.textContent = submitLabel;
        }
    });
}
