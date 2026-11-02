import { showToast } from './toast';

const STORAGE_KEY = 'vefiri-wishlist';

const readWishlist = () => {
    try {
        return new Set(JSON.parse(window.localStorage.getItem(STORAGE_KEY) || '[]'));
    } catch {
        return new Set();
    }
};

export function initWishlist() {
    const wishlist = readWishlist();

    const save = () => {
        try {
            window.localStorage.setItem(STORAGE_KEY, JSON.stringify([...wishlist]));
        } catch {
            return;
        }
    };

    const applyState = (button, isActive) => {
        button.dataset.active = String(isActive);
        button.setAttribute('aria-pressed', String(isActive));
    };

    document.querySelectorAll('[data-wishlist]').forEach((button) => {
        applyState(button, wishlist.has(button.dataset.productId));
    });

    document.addEventListener('click', (event) => {
        const button = event.target.closest('[data-wishlist]');

        if (!button) {
            return;
        }

        const productId = button.dataset.productId;
        const isActive = !wishlist.has(productId);

        if (isActive) {
            wishlist.add(productId);
            showToast('Saved to your wishlist.');
        } else {
            wishlist.delete(productId);
            showToast('Removed from your wishlist.');
        }

        applyState(button, isActive);
        save();
    });
}
