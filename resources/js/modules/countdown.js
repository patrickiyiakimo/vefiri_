const pad = (value) => String(value).padStart(2, '0');

export function initCountdowns() {
    const countdowns = [...document.querySelectorAll('[data-countdown]')];

    if (countdowns.length === 0) {
        return;
    }

    const end = new Date();
    end.setHours(24, 0, 0, 0);

    const update = () => {
        const remaining = Math.max(0, end.getTime() - Date.now());
        const hours = Math.floor(remaining / 3_600_000);
        const minutes = Math.floor((remaining % 3_600_000) / 60_000);
        const seconds = Math.floor((remaining % 60_000) / 1_000);
        const value = `${pad(hours)}:${pad(minutes)}:${pad(seconds)}`;

        countdowns.forEach((countdown) => {
            const valueElement = countdown.querySelector('[data-countdown-value]');

            if (valueElement) {
                valueElement.textContent = value;
            }
        });
    };

    update();
    window.setInterval(update, 1_000);
}
