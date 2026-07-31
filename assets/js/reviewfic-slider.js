/**
 * Reviewfic — Frontend Slider
 * Supports single and multi-column (page-by-page) modes.
 * Uses pixel-based widths and translateX so column gap is fully supported.
 */
(function () {
    'use strict';

    function initSlider(slider) {
        var track  = slider.querySelector('.reviewfic-slider-track');
        var slides = track ? track.querySelectorAll('.reviewfic-item') : [];
        var navEl  = slider.querySelector('.reviewfic-slider-nav');
        var prevEl = slider.querySelector('.reviewfic-slider-prev');
        var nextEl = slider.querySelector('.reviewfic-slider-next');
        var dotsEl = slider.querySelector('.reviewfic-slider-dots');
        var total  = slides.length;
        var current = 0;
        var autoTimer = null;

        if (!track || total === 0) return;

        var showNav    = slider.dataset.nav     !== 'no';
        var showDots   = slider.dataset.dots    !== 'no';
        var autoPlay   = slider.dataset.auto    === 'yes';
        var speed      = parseInt(slider.dataset.speed, 10) || 4000;
        var loop       = slider.dataset.loop    !== 'no';
        var pauseHover = slider.dataset.pause   !== 'no';
        var columns    = Math.max(1, parseInt(slider.dataset.columns, 10) || 1);

        // Read the gap from the track's computed style (set via --rwf-col-gap CSS var)
        function getGap() {
            var cs = window.getComputedStyle(track);
            return parseFloat(cs.columnGap || cs.gap) || 0;
        }

        // Item width in pixels accounting for the gap between columns
        function calcItemWidth() {
            var g = getGap();
            return (slider.offsetWidth - g * (columns - 1)) / columns;
        }

        // Apply pixel widths to all slides
        function applyWidths() {
            var w = calcItemWidth();
            for (var s = 0; s < slides.length; s++) {
                slides[s].style.minWidth = w + 'px';
                slides[s].style.maxWidth = w + 'px';
            }
            return w;
        }

        applyWidths();

        var pages   = Math.ceil(total / columns);
        var maxPage = pages - 1;

        // Nav/dots visibility
        if (navEl) {
            if (prevEl) prevEl.style.display = showNav ? '' : 'none';
            if (nextEl) nextEl.style.display = showNav ? '' : 'none';
            if (dotsEl) dotsEl.style.display = showDots ? '' : 'none';
            if (!showNav && !showDots) navEl.style.display = 'none';
        }

        if (pages <= 1) {
            if (navEl) navEl.style.display = 'none';
            return;
        }

        // Build dots
        if (showDots && dotsEl) {
            for (var i = 0; i < pages; i++) {
                (function (index) {
                    var dot = document.createElement('button');
                    dot.className = 'reviewfic-slider-dot' + (index === 0 ? ' active' : '');
                    dot.setAttribute('aria-label', 'Go to page ' + (index + 1));
                    dot.addEventListener('click', function () { goTo(index); resetAuto(); });
                    dotsEl.appendChild(dot);
                })(i);
            }
        }

        function updateDots() {
            if (!showDots || !dotsEl) return;
            var allDots = dotsEl.querySelectorAll('.reviewfic-slider-dot');
            for (var d = 0; d < allDots.length; d++) {
                allDots[d].classList.toggle('active', d === current);
            }
        }

        // Pixel offset for a given page:
        // Each item occupies (itemWidth + gap) px; a page advances columns items.
        // offset = page * columns * (itemWidth + gap)
        function pageOffset(pageIndex) {
            var g = getGap();
            var w = calcItemWidth();
            return pageIndex * columns * (w + g);
        }

        function goTo(pageIndex) {
            if (loop) {
                current = ((pageIndex % pages) + pages) % pages;
            } else {
                current = Math.max(0, Math.min(pageIndex, maxPage));
                if (prevEl) prevEl.disabled = current === 0;
                if (nextEl) nextEl.disabled = current === maxPage;
            }
            track.style.transform = 'translateX(-' + pageOffset(current) + 'px)';
            updateDots();
        }

        function startAuto() {
            if (!autoPlay) return;
            autoTimer = setInterval(function () {
                goTo(loop ? current + 1 : (current < maxPage ? current + 1 : 0));
            }, speed);
        }

        function stopAuto()  { if (autoTimer) clearInterval(autoTimer); }
        function resetAuto() { stopAuto(); startAuto(); }

        // Recalculate on resize so pixel widths and offsets stay accurate
        var resizeTimer;
        window.addEventListener('resize', function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function () {
                applyWidths();
                track.style.transition = 'none';
                track.style.transform  = 'translateX(-' + pageOffset(current) + 'px)';
                setTimeout(function () { track.style.transition = ''; }, 50);
            }, 100);
        });

        if (prevEl) prevEl.addEventListener('click', function () { goTo(current - 1); resetAuto(); });
        if (nextEl) nextEl.addEventListener('click', function () { goTo(current + 1); resetAuto(); });

        var touchStartX = 0;
        track.addEventListener('touchstart', function (e) { touchStartX = e.touches[0].clientX; }, { passive: true });
        track.addEventListener('touchend', function (e) {
            var diff = touchStartX - e.changedTouches[0].clientX;
            if (Math.abs(diff) > 50) { goTo(diff > 0 ? current + 1 : current - 1); resetAuto(); }
        }, { passive: true });

        slider.setAttribute('tabindex', '0');
        slider.addEventListener('keydown', function (e) {
            if (e.key === 'ArrowLeft')  { goTo(current - 1); resetAuto(); }
            if (e.key === 'ArrowRight') { goTo(current + 1); resetAuto(); }
        });

        if (autoPlay && pauseHover) {
            slider.addEventListener('mouseenter', stopAuto);
            slider.addEventListener('mouseleave', startAuto);
        }

        goTo(0);
        startAuto();
    }

    function initAll() {
        var sliders = document.querySelectorAll('.reviewfic-slider');
        for (var i = 0; i < sliders.length; i++) initSlider(sliders[i]);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }
})();
