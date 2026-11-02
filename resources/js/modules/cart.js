import { openDrawer } from './drawer';
import { showToast } from './toast';

const currency = new Intl.NumberFormat('en-NG', {
    style: 'currency',
    currency: 'NGN',
    maximumFractionDigits: 0,
});

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
        error.errors = data.errors ?? {};
        throw error;
    }

    return data;
};

const openAuth = () => {
    document.dispatchEvent(new CustomEvent('vefiri:open-auth', { detail: { view: 'login' } }));
};

export function initCart() {
    const itemsContainer = document.querySelector('[data-cart-items]');
    const template = document.querySelector('[data-cart-item-template]');
    const emptyState = document.querySelector('[data-cart-empty]');
    const footer = document.querySelector('[data-cart-footer]');
    const subtotalElement = document.querySelector('[data-cart-subtotal]');
    const cartUrl = document.body.dataset.cartUrl;
    const itemsUrl = document.body.dataset.cartItemsUrl;

    if (!itemsContainer || !template || !cartUrl || !itemsUrl) {
        return;
    }

    let items = [];
    let count = 0;
    let subtotal = 0;

    const updateCounts = () => {
        document.querySelectorAll('[data-cart-count]').forEach((element) => {
            element.textContent = String(count);
        });

        const hasItems = items.length > 0;

        emptyState?.classList.toggle('hidden', hasItems);
        itemsContainer.classList.toggle('hidden', !hasItems);
        footer?.classList.toggle('hidden', !hasItems);

        if (subtotalElement) {
            subtotalElement.textContent = currency.format(subtotal);
        }
    };

    const buildItem = (item) => {
        const node = template.content.firstElementChild.cloneNode(true);

        node.dataset.cartItemId = item.id;
        node.querySelector('[data-cart-item-image]').src = item.image;
        node.querySelector('[data-cart-item-image]').alt = item.title;
        node.querySelector('[data-cart-item-title]').textContent = item.title;
        node.querySelector('[data-cart-item-store]').textContent = item.store;
        node.querySelector('[data-cart-item-qty]').textContent = String(item.quantity);
        node.querySelector('[data-cart-item-price]').textContent = currency.format(item.line_total);

        return node;
    };

    const apply = (cart) => {
        items = cart.items ?? [];
        count = cart.count ?? 0;
        subtotal = cart.subtotal ?? 0;

        itemsContainer.replaceChildren(...items.map(buildItem));
        updateCounts();
    };

    const refresh = async () => {
        if (!isAuthenticated()) {
            apply({ items: [], count: 0, subtotal: 0 });
            return;
        }

        try {
            apply(await request(cartUrl));
        } catch {
            apply({ items: [], count: 0, subtotal: 0 });
        }
    };

    const addProduct = async (button) => {
        if (!isAuthenticated()) {
            showToast('Sign in to add items to your cart.');
            openAuth();
            return;
        }

        const quantityField = button.closest('[data-product-purchase]')?.querySelector('[data-quantity-select]');
        const quantity = Math.min(99, Math.max(1, Number(quantityField?.value ?? 1)));

        try {
            const cart = await request(itemsUrl, {
                method: 'POST',
                body: JSON.stringify({
                    product_id: button.dataset.productId,
                    quantity,
                }),
            });

            apply(cart);
            showToast('Added to your cart.');
            openDrawer('cart-drawer');
        } catch (error) {
            if (error.status === 401) {
                openAuth();
                return;
            }

            showToast(error.message);
        }
    };

    const changeQuantity = async (item, quantity) => {
        if (quantity < 1) {
            await removeItem(item);
            return;
        }

        try {
            apply(await request(`${itemsUrl}/${encodeURIComponent(item.id)}`, {
                method: 'PATCH',
                body: JSON.stringify({ quantity }),
            }));
        } catch (error) {
            showToast(error.message);
        }
    };

    const removeItem = async (item) => {
        try {
            apply(await request(`${itemsUrl}/${encodeURIComponent(item.id)}`, {
                method: 'DELETE',
            }));

            showToast('Item removed from your cart.');
        } catch (error) {
            showToast(error.message);
        }
    };

    document.addEventListener('click', (event) => {
        const addButton = event.target.closest('[data-add-to-cart]');

        if (addButton) {
            event.preventDefault();
            addProduct(addButton);
        }
    });

    itemsContainer.addEventListener('click', (event) => {
        const itemNode = event.target.closest('[data-cart-item]');

        if (!itemNode) {
            return;
        }

        const item = items.find((cartItem) => cartItem.id === itemNode.dataset.cartItemId);

        if (!item) {
            return;
        }

        if (event.target.closest('[data-cart-item-increase]')) {
            changeQuantity(item, Math.min(99, item.quantity + 1));
        } else if (event.target.closest('[data-cart-item-decrease]')) {
            changeQuantity(item, item.quantity - 1);
        } else if (event.target.closest('[data-cart-item-remove]')) {
            removeItem(item);
        }
    });

    document.querySelector('[data-cart-checkout]')?.addEventListener('click', () => {
        showToast('Checkout is not available in this demo.');
    });

    refresh();
}
