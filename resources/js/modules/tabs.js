export function initTabs() {
    document.querySelectorAll('[data-tabs]').forEach((group) => {
        const buttons = [...group.querySelectorAll('[data-tab-target]')];
        const panels = [...group.querySelectorAll('[data-tab-panel]')];

        if (buttons.length === 0 || panels.length === 0) {
            return;
        }

        const activate = (target, moveFocus = false) => {
            buttons.forEach((button) => {
                const isActive = button.dataset.tabTarget === target;
                button.dataset.active = String(isActive);
                button.setAttribute('aria-selected', String(isActive));
                button.tabIndex = isActive ? 0 : -1;

                if (isActive && moveFocus) {
                    button.focus({ preventScroll: true });
                }
            });

            panels.forEach((panel) => {
                const isActive = panel.dataset.tabPanel === target;
                panel.hidden = !isActive;
                panel.classList.toggle('hidden', !isActive);
            });
        };

        buttons.forEach((button, buttonIndex) => {
            button.tabIndex = button.dataset.active === 'true' ? 0 : -1;

            button.addEventListener('click', () => activate(button.dataset.tabTarget));

            button.addEventListener('keydown', (event) => {
                let nextIndex = null;

                if (event.key === 'ArrowRight') {
                    nextIndex = (buttonIndex + 1) % buttons.length;
                } else if (event.key === 'ArrowLeft') {
                    nextIndex = (buttonIndex - 1 + buttons.length) % buttons.length;
                } else if (event.key === 'Home') {
                    nextIndex = 0;
                } else if (event.key === 'End') {
                    nextIndex = buttons.length - 1;
                }

                if (nextIndex === null) {
                    return;
                }

                event.preventDefault();
                activate(buttons[nextIndex].dataset.tabTarget, true);
            });
        });
    });
}
