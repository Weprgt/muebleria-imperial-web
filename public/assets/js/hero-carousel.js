(() => {
    const carouselElement = document.getElementById('imperialHeroCarousel');

    if (!carouselElement) {
        return;
    }

    const motionPreference = window.matchMedia('(prefers-reduced-motion: reduce)');
    const carousel = new bootstrap.Carousel(carouselElement, {
        interval: 6000,
        pause: 'hover',
        touch: true,
    });

    if (!motionPreference.matches) {
        carousel.cycle();
    }

    if (typeof motionPreference.addEventListener === 'function') {
        motionPreference.addEventListener('change', (event) => {
            if (event.matches) {
                carousel.pause();
                return;
            }

            carousel.cycle();
        });
    }
})();
