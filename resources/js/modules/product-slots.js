const numberFormatter = new Intl.NumberFormat();

export function initProductSlots() {
    const form = document.querySelector('[data-product-slots-form]');

    if (!form) {
        return;
    }

    const unitPrice = Number(form.dataset.unitPrice ?? 0);
    const currencySymbol = form.dataset.currencySymbol ?? '';
    const amountEl = form.querySelector('[data-product-slots-amount]');
    const submitAmountEl = form.querySelector('[data-product-slots-submit-amount]');
    const radios = form.querySelectorAll('input[name="quantity"]');

    const update = () => {
        const selected = form.querySelector('input[name="quantity"]:checked');
        const quantity = Number(selected?.value ?? 0);
        const amount = quantity * unitPrice;

        if (amountEl) {
            amountEl.textContent = `${currencySymbol}${numberFormatter.format(amount / 100)}`;
        }

        if (submitAmountEl) {
            submitAmountEl.textContent = numberFormatter.format(amount / 100);
        }
    };

    radios.forEach((radio) => radio.addEventListener('change', update));
    update();
}
