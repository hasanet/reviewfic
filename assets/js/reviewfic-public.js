/**
 * Reviewfic — Frontend Slider
 * Lightweight, dependency-free. Auto-initialises all .reviewfic-slider elements.
 */
(function () {
    'use strict';

    function initSlider(slider) {
        var track  = slider.querySelector('.reviewfic-slider-track');
        var slides = track ? track.querySelectorAll('.reviewfic-item') : [];
        var prev   = slider.querySelector('.reviewfic-slider-prev');
        var next   = slider.querySelector('.reviewfic-slider-next');
        var dots   = slider.querySelector('.reviewfic-slider-dots');
        var total  = slides.length;
        var current = 0;

        if (!track || total === 0) return;

        // Hide nav if only one slide
        if (total === 1) {
            var nav = slider.querySelector('.reviewfic-slider-nav');
            if (nav) nav.style.display = 'none';
            return;
        }

        // Build dots
        for (var i = 0; i < total; i++) {
            (function (index) {
                var dot = document.createElement('button');
                dot.className = 'reviewfic-slider-dot' + (index === 0 ? ' active' : '');
                dot.setAttribute('aria-label', 'Go to review ' + (index + 1));
                dot.addEventListener('click', function () { goTo(index); });
                dots.appendChild(dot);
            })(i);
        }

        function goTo(index) {
            current = ((index % total) + total) % total;
            track.style.transform = 'translateX(-' + (current * 100) + '%)';
            var allDots = slider.querySelectorAll('.reviewfic-slider-dot');
            for (var d = 0; d < allDots.length; d++) {
                allDots[d].classList.toggle('active', d === current);
            }
        }

        if (prev) prev.addEventListener('click', function () { goTo(current - 1); });
        if (next) next.addEventListener('click', function () { goTo(current + 1); });

        // Touch / swipe support
        var touchStartX = 0;
        track.addEventListener('touchstart', function (e) {
            touchStartX = e.touches[0].clientX;
        }, { passive: true });
        track.addEventListener('touchend', function (e) {
            var diff = touchStartX - e.changedTouches[0].clientX;
            if (Math.abs(diff) > 50) goTo(diff > 0 ? current + 1 : current - 1);
        }, { passive: true });

        // Keyboard support
        slider.setAttribute('tabindex', '0');
        slider.addEventListener('keydown', function (e) {
            if (e.key === 'ArrowLeft')  goTo(current - 1);
            if (e.key === 'ArrowRight') goTo(current + 1);
        });
    }

    function initAll() {
        var sliders = document.querySelectorAll('.reviewfic-slider');
        for (var i = 0; i < sliders.length; i++) {
            initSlider(sliders[i]);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }
})();
