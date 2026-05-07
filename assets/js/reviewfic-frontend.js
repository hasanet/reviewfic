(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.reviewfic-slider-wrap').forEach(function (wrap) {
            var track    = wrap.querySelector('.reviewfic-slider-track');
            var items    = track.querySelectorAll('.reviewfic-item');
            var prevBtn  = wrap.querySelector('.rwf-slider-prev');
            var nextBtn  = wrap.querySelector('.rwf-slider-next');
            var dotsWrap = wrap.querySelector('.rwf-slider-dots');
            var columns  = parseInt(wrap.dataset.columns, 10) || 1;
            var current  = 0;
            var total    = items.length;
            var startX   = 0;

            if (total === 0) return;

            function getVisible() {
                var w = wrap.offsetWidth;
                if (w < 540) return 1;
                if (w < 860) return Math.min(2, columns);
                return columns;
            }

            function maxIndex() {
                return Math.max(0, total - getVisible());
            }

            function goTo(idx) {
                var vis = getVisible();
                current = Math.max(0, Math.min(idx, total - vis));
                var pct = (100 / vis) * current;
                track.style.transform = 'translateX(-' + pct + '%)';

                // Dots
                dotsWrap.querySelectorAll('.rwf-dot').forEach(function (d, i) {
                    d.classList.toggle('active', i === current);
                });

                prevBtn.disabled = current === 0;
                nextBtn.disabled = current >= maxIndex();
            }

            function buildLayout() {
                var vis = getVisible();
                var w   = (100 / vis) + '%';
                items.forEach(function (item) {
                    item.style.minWidth = w;
                    item.style.maxWidth = w;
                });

                // Rebuild dots
                dotsWrap.innerHTML = '';
                var numDots = Math.max(1, total - vis + 1);
                for (var i = 0; i < numDots; i++) {
                    (function (idx) {
                        var dot = document.createElement('button');
                        dot.className = 'rwf-dot' + (idx === 0 ? ' active' : '');
                        dot.setAttribute('aria-label', 'Go to slide ' + (idx + 1));
                        dot.addEventListener('click', function () { goTo(idx); });
                        dotsWrap.appendChild(dot);
                    })(i);
                }

                goTo(current);
            }

            prevBtn.addEventListener('click', function () { goTo(current - 1); });
            nextBtn.addEventListener('click', function () { goTo(current + 1); });

            // Touch / swipe
            track.addEventListener('touchstart', function (e) {
                startX = e.touches[0].clientX;
            }, { passive: true });

            track.addEventListener('touchend', function (e) {
                var diff = startX - e.changedTouches[0].clientX;
                if (Math.abs(diff) > 40) {
                    goTo(diff > 0 ? current + 1 : current - 1);
                }
            }, { passive: true });

            // Keyboard
            wrap.setAttribute('tabindex', '0');
            wrap.addEventListener('keydown', function (e) {
                if (e.key === 'ArrowLeft')  goTo(current - 1);
                if (e.key === 'ArrowRight') goTo(current + 1);
            });

            var resizeTimer;
            window.addEventListener('resize', function () {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(buildLayout, 150);
            });

            buildLayout();
        });
    });
})();
