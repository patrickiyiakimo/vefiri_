export function initBackToTop() {
    const button = document.querySelector('[data-back-to-top]');

    if (!button) {
        return;
    }

    const updateVisibility = () => {
        const isVisible = window.scrollY > 500;
        button.classList.toggle('hidden', !isVisible);
        button.classList.toggle('flex', isVisible);
    };

    window.addEventListener('scroll', updateVisibility, { passive: true });

    button.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    updateVisibility();
}
