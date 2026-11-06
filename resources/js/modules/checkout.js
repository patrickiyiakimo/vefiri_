const csrfToken = () => {
    return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
};

export function initCheckout() {
    const form = document.querySelector('[data-checkout-form]');

    if (!form) {
        return;
    }

    const submitButton = form.querySelector('[data-checkout-submit]');
    const submitLabel = form.querySelector('[data-checkout-submit-label]');
    const submitIcon = form.querySelector('[data-checkout-submit-icon]');
    const errorContainer = form.querySelector('[data-checkout-errors]');
    const errorList = form.querySelector('[data-checkout-error-list]');
    let submitting = false;

    let items = [];

    try {
        items = JSON.parse(form.dataset.checkoutItems ?? '[]');
    } catch {
        items = [];
    }

    const fieldValue = (name) => {
        return form.querySelector(`[name="${name}"]`)?.value.trim() ?? '';
    };

    const clearErrors = () => {
        errorContainer?.setAttribute('hidden', '');
        errorList?.replaceChildren();
    };

    const showErrors = (messages) => {
        if (!errorContainer || !errorList) {
            return;
        }

        errorList.replaceChildren(
            ...messages.map((message) => {
                const item = document.createElement('li');
                item.textContent = message;

                return item;
            }),
        );

        errorContainer.removeAttribute('hidden');
    };

    const setLoading = (loading) => {
        if (!submitButton) {
            return;
        }

        submitButton.disabled = loading;

        if (submitLabel) {
            submitLabel.textContent = loading
                ? 'Redirecting to Paystack...'
                : submitLabel.dataset.originalLabel ?? submitLabel.textContent;
        }

        submitIcon?.classList.toggle('hidden', loading);
    };

    if (submitLabel) {
        submitLabel.dataset.originalLabel = submitLabel.textContent;
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        if (submitting) {
            return;
        }

        if (!items.length) {
            showErrors(['Your cart is empty. Add an item before checking out.']);

            return;
        }

        clearErrors();
        submitting = true;
        setLoading(true);

        try {
            const response = await fetch(form.dataset.checkoutUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken(),
                    'X-Idempotency-Key': form.dataset.idempotencyKey ?? '',
                },
                body: JSON.stringify({
                    items,
                    coupon_code: fieldValue('coupon_code'),
                    customer_name: fieldValue('customer_name'),
                    customer_email: fieldValue('customer_email'),
                    customer_phone: fieldValue('customer_phone'),
                    delivery_address: fieldValue('delivery_address'),
                    delivery_address_line_2: fieldValue('delivery_address_line_2'),
                    delivery_city: fieldValue('delivery_city'),
                    delivery_state: fieldValue('delivery_state'),
                    delivery_postal_code: fieldValue('delivery_postal_code'),
                    delivery_notes: fieldValue('delivery_notes'),
                }),
            });

            const data = await response.json().catch(() => ({}));

            if (!response.ok) {
                const messages = Object.values(data.errors ?? {}).flat();

                throw new Error(
                    messages.length
                        ? messages.join(' ')
                        : data.message ?? 'We could not start your payment. Please try again.',
                );
            }

            if (!data.checkout_url) {
                throw new Error('Paystack did not return a checkout URL. Please try again.');
            }

            window.location.assign(data.checkout_url);
        } catch (error) {
            showErrors([error.message]);
            submitting = false;
            setLoading(false);
        }
    });
}
