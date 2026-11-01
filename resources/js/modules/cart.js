import { openDrawer } from './drawer';
import { showToast } from './toast';

const STORAGE_KEY = 'aliexpress-clone-cart';

const currency = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
});

const readCart = () => {
    try {
        const stored = window.localStorage.getItem(STORAGE_KEY);
        return stored ? JSON.parse(stored) : [];
    } catch {
        return [];
    }
};

export function initCart() {
    const itemsContainer = document.querySelector('[data-cart-items]');
    const template = document.querySelector('[data-cart-item-template]');
    const emptyState = document.querySelector('[data-cart-empty]');
    const footer = document.querySelector('[data-cart-footer]');
    const subtotalElement = document.querySelector('[data-cart-subtotal]');

    if (!itemsContainer || !template) {
        return;
    }

    let items = readCart();

    const save = () => {
        try {
            window.localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
        } catch {
            return;
        }
    };

    const totalQuantity = () => {
        return items.reduce((total, item) => total + item.quantity, 0);
    };

    const subtotal = () => {
        return items.reduce((total, item) => total + item.price * item.quantity, 0);
    };

    const updateCounts = () => {
        document.querySelectorAll('[data-cart-count]').forEach((element) => {
            element.textContent = String(totalQuantity());
        });

        const hasItems = items.length > 0;

        emptyState?.classList.toggle('hidden', hasItems);
        itemsContainer.classList.toggle('hidden', !hasItems);
        footer?.classList.toggle('hidden', !hasItems);

        if (subtotalElement) {
            subtotalElement.textContent = currency.format(subtotal());
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
        node.querySelector('[data-cart-item-price]').textContent = currency.format(item.price * item.quantity);

        return node;
    };

    const render = () => {
        itemsContainer.replaceChildren(...items.map(buildItem));
        updateCounts();
        save();
    };

    const addProduct = (button) => {
        const id = button.dataset.productId;
        const existing = items.find((item) => item.id === id);

        if (existing) {
            existing.quantity += 1;
        } else {
            items.push({
                id,
                title: button.dataset.productTitle,
                image: button.dataset.productImage,
                price: Number.parseFloat(button.dataset.productPrice),
                store: button.dataset.productStore,
                quantity: 1,
            });
        }

        render();
        showToast('Added to your cart.');
        openDrawer('cart-drawer');
    };

    document.addEventListener('click', (event) => {
        const addButton = event.target.closest('[data-add-to-cart]');

        if (addButton) {
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
            item.quantity = Math.min(99, item.quantity + 1);
        } else if (event.target.closest('[data-cart-item-decrease]')) {
            item.quantity -= 1;

            if (item.quantity < 1) {
                items = items.filter((cartItem) => cartItem.id !== item.id);
            }
        } else if (event.target.closest('[data-cart-item-remove]')) {
            items = items.filter((cartItem) => cartItem.id !== item.id);
            showToast('Item removed from your cart.');
        } else {
            return;
        }

        render();
    });

    document.querySelector('[data-cart-checkout]')?.addEventListener('click', () => {
        showToast('Checkout is not available in this demo.');
    });

    render();
}
