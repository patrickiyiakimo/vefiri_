const MAX_TOASTS = 3;
const AUTO_DISMISS_MS = 4200;

const removeToast = (toast) => {
    if (toast.dataset.leaving === 'true') {
        return;
    }

    toast.dataset.leaving = 'true';
    toast.classList.remove('toast-enter');
    toast.classList.add('toast-leave');

    window.setTimeout(() => toast.remove(), 180);
};

export function showToast(message) {
    const container = document.querySelector('[data-toast-container]');

    if (!container || !message) {
        return;
    }

    const toasts = container.querySelectorAll('[data-toast]');

    if (toasts.length >= MAX_TOASTS) {
        removeToast(toasts[0]);
    }

    const toast = document.createElement('div');
    toast.dataset.toast = 'true';
    toast.className = 'toast-enter pointer-events-auto flex w-full items-start gap-3 rounded-2xl border border-line bg-white p-3.5 shadow-float';

    const icon = document.createElement('span');
    icon.className = 'mt-0.5 flex size-8 shrink-0 items-center justify-center rounded-full bg-[#e7f8f2] text-choice-dark';
    icon.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4" aria-hidden="true"><path d="m5 12 4.5 4.5L19 7"/></svg>';

    const text = document.createElement('p');
    text.className = 'flex-1 pt-1 text-xs leading-5 font-medium text-ink';
    text.textContent = message;

    const close = document.createElement('button');
    close.type = 'button';
    close.className = 'flex size-7 shrink-0 items-center justify-center rounded-full text-muted transition hover:bg-surface hover:text-ink';
    close.setAttribute('aria-label', 'Dismiss notification');
    close.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" class="size-3.5" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg>';
    close.addEventListener('click', () => removeToast(toast));

    toast.append(icon, text, close);
    container.append(toast);

    let timer = window.setTimeout(() => removeToast(toast), AUTO_DISMISS_MS);

    toast.addEventListener('mouseenter', () => window.clearTimeout(timer));
    toast.addEventListener('mouseleave', () => {
        timer = window.setTimeout(() => removeToast(toast), 1800);
    });
}

export function initToasts() {
    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-toast]');

        if (trigger) {
            showToast(trigger.dataset.toast);
        }
    });
}
