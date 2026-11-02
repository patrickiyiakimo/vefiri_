import { showToast } from './toast';

const csrfToken = () => {
    return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
};

const isAuthenticated = () => {
    return document.body.dataset.authenticated === 'true';
};

const request = async (url, options = {}) => {
    const response = await fetch(url, {
        credentials: 'same-origin',
        ...options,
        headers: {
            Accept: 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
            ...(options.body ? { 'Content-Type': 'application/json' } : {}),
            ...(options.headers ?? {}),
        },
    });

    const data = await response.json().catch(() => ({}));

    if (!response.ok) {
        const error = new Error(data.message ?? 'Something went wrong. Please try again.');
        error.status = response.status;
        throw error;
    }

    return data;
};

const openAuth = () => {
    document.dispatchEvent(new CustomEvent('vefiri:open-auth', { detail: { view: 'login' } }));
};

export function initWishlist() {
    const favouritesUrl = document.body.dataset.favouritesUrl;

    if (!favouritesUrl) {
        return;
    }

    const wishlist = new Set();

    const applyState = (button, isActive) => {
        button.dataset.active = String(isActive);
        button.setAttribute('aria-pressed', String(isActive));
    };

    const applyToAll = () => {
        document.querySelectorAll('[data-wishlist]').forEach((button) => {
            applyState(button, wishlist.has(button.dataset.productId));
        });
    };

    const load = async () => {
        if (!isAuthenticated()) {
            applyToAll();
            return;
        }

        try {
            const data = await request(favouritesUrl);
            wishlist.clear();
            (data.ids ?? []).forEach((id) => wishlist.add(id));
            applyToAll();
        } catch {
            applyToAll();
        }
    };

    document.addEventListener('click', async (event) => {
        const button = event.target.closest('[data-wishlist]');

        if (!button) {
            return;
        }

        event.preventDefault();

        if (!isAuthenticated()) {
            showToast('Sign in to save items to your wishlist.');
            openAuth();
            return;
        }

        const productId = button.dataset.productId;
        const isActive = wishlist.has(productId);

        button.disabled = true;

        try {
            const data = await request(
                isActive ? `${favouritesUrl}/${encodeURIComponent(productId)}` : favouritesUrl,
                isActive
                    ? { method: 'DELETE' }
                    : { method: 'POST', body: JSON.stringify({ product_id: productId }) },
            );

            wishlist.clear();
            (data.ids ?? []).forEach((id) => wishlist.add(id));

            applyState(button, data.active === true);
            showToast(data.active ? 'Saved to your wishlist.' : 'Removed from your wishlist.');
        } catch (error) {
            if (error.status === 401) {
                openAuth();
                return;
            }

            showToast(error.message);
        } finally {
            button.disabled = false;
        }
    });

    load();
}
