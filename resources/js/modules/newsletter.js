import { showToast } from './toast';

export function initNewsletters() {
    document.querySelectorAll('[data-newsletter-form]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            event.preventDefault();

            if (!form.reportValidity()) {
                return;
            }

            showToast('You are in! Check your inbox for coupon code WELCOME5.');
            form.reset();
        });
    });
}
