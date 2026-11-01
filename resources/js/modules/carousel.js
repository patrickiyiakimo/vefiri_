export function initCarousels() {
    document.querySelectorAll('[data-carousel]').forEach((carousel) => {
        const track = carousel.querySelector('[data-carousel-track]');
        const slides = [...carousel.querySelectorAll('[data-carousel-slide]')];
        const dots = [...carousel.querySelectorAll('[data-carousel-dot]')];
        const previous = carousel.querySelector('[data-carousel-prev]');
        const next = carousel.querySelector('[data-carousel-next]');

        if (!track || slides.length < 2) {
            return;
        }

        let index = 0;
        let timer;
        let isPaused = false;
        let touchStartX = 0;

        const render = () => {
            track.style.transform = `translate3d(-${index * 100}%, 0, 0)`;

            dots.forEach((dot, dotIndex) => {
                const isActive = dotIndex === index;
                dot.dataset.active = String(isActive);
                dot.setAttribute('aria-selected', String(isActive));
            });

            slides.forEach((slide, slideIndex) => {
                slide.setAttribute('aria-hidden', String(slideIndex !== index));
            });
        };

        const goTo = (nextIndex, shouldRestart = true) => {
            index = (nextIndex + slides.length) % slides.length;
            render();

            if (shouldRestart) {
                restart();
            }
        };

        const stop = () => {
            window.clearInterval(timer);
        };

        const restart = () => {
            stop();

            if (isPaused || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                return;
            }

            timer = window.setInterval(() => goTo(index + 1, false), 6500);
        };

        previous?.addEventListener('click', () => goTo(index - 1));
        next?.addEventListener('click', () => goTo(index + 1));

        dots.forEach((dot, dotIndex) => {
            dot.addEventListener('click', () => goTo(dotIndex));
        });

        carousel.addEventListener('mouseenter', () => {
            isPaused = true;
            stop();
        });

        carousel.addEventListener('mouseleave', () => {
            isPaused = false;
            restart();
        });

        carousel.addEventListener('focusin', () => {
            isPaused = true;
            stop();
        });

        carousel.addEventListener('focusout', (event) => {
            if (!carousel.contains(event.relatedTarget)) {
                isPaused = false;
                restart();
            }
        });

        carousel.addEventListener('touchstart', (event) => {
            touchStartX = event.changedTouches[0].clientX;
        }, { passive: true });

        carousel.addEventListener('touchend', (event) => {
            const distance = event.changedTouches[0].clientX - touchStartX;

            if (Math.abs(distance) > 45) {
                goTo(distance > 0 ? index - 1 : index + 1);
            }
        }, { passive: true });

        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                stop();
            } else {
                restart();
            }
        });

        render();
        restart();
    });
}
