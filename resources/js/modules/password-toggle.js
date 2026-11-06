export function initPasswordToggles() {
    document.addEventListener('click', (event) => {
        const toggle = event.target.closest('[data-password-toggle]');

        if (!toggle) {
            return;
        }

        const field = toggle.closest('[data-password-field]');
        const input = field?.querySelector('input');

        if (!input) {
            return;
        }

        const reveal = input.type === 'password';

        input.type = reveal ? 'text' : 'password';
        toggle.setAttribute('aria-pressed', String(reveal));
        toggle.setAttribute('aria-label', reveal ? 'Hide password' : 'Show password');
        toggle.querySelector('[data-password-icon-show]')?.classList.toggle('hidden', reveal);
        toggle.querySelector('[data-password-icon-hide]')?.classList.toggle('hidden', !reveal);

        input.focus({ preventScroll: true });
        input.setSelectionRange?.(input.value.length, input.value.length);
    });
}
