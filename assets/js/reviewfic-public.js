/**
 * Reviewfic — Frontend Slider v1.2.6
 * Reads options from data-* attributes on .reviewfic-slider
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

        // Read options from data attributes
        var showNav   = slider.dataset.nav   !== 'no';
        var showDots  = slider.dataset.dots  !== 'no';
        var autoPlay  = slider.dataset.auto  === 'yes';
        var speed     = parseInt(slider.dataset.speed, 10) || 4000;
        var loop      = slider.dataset.loop  !== 'no';
        var pauseHover= slider.dataset.pause !== 'no';

        // Apply nav/dots visibility
        if (navEl) {
            if (prevEl) prevEl.style.display = showNav ? '' : 'none';
            if (nextEl) nextEl.style.display = showNav ? '' : 'none';
            if (dotsEl) dotsEl.style.display = showDots ? '' : 'none';
            // Hide entire nav bar if both are off
            if (!showNav && !showDots) navEl.style.display = 'none';
        }

        // Only 1 slide — hide nav entirely
        if (total === 1) {
            if (navEl) navEl.style.display = 'none';
            return;
        }

        // Build dots
        if (showDots && dotsEl) {
            for (var i = 0; i < total; i++) {
                (function (index) {
                    var dot = document.createElement('button');
                    dot.className = 'reviewfic-slider-dot' + (index === 0 ? ' active' : '');
                    dot.setAttribute('aria-label', 'Go to review ' + (index + 1));
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

        function goTo(index) {
            if (loop) {
                current = ((index % total) + total) % total;
            } else {
                current = Math.max(0, Math.min(index, total - 1));
                if (prevEl) prevEl.disabled = current === 0;
                if (nextEl) nextEl.disabled = current === total - 1;
            }
            track.style.transform = 'translateX(-' + (current * 100) + '%)';
            updateDots();
        }

        function startAuto() {
            if (!autoPlay) return;
            autoTimer = setInterval(function () {
                goTo(loop ? current + 1 : (current < total - 1 ? current + 1 : 0));
            }, speed);
        }

        function stopAuto()  { if (autoTimer) clearInterval(autoTimer); }
        function resetAuto() { stopAuto(); startAuto(); }

        // Prev / Next
        if (prevEl) prevEl.addEventListener('click', function () { goTo(current - 1); resetAuto(); });
        if (nextEl) nextEl.addEventListener('click', function () { goTo(current + 1); resetAuto(); });

        // Touch / swipe
        var touchStartX = 0;
        track.addEventListener('touchstart', function (e) { touchStartX = e.touches[0].clientX; }, { passive: true });
        track.addEventListener('touchend', function (e) {
            var diff = touchStartX - e.changedTouches[0].clientX;
            if (Math.abs(diff) > 50) { goTo(diff > 0 ? current + 1 : current - 1); resetAuto(); }
        }, { passive: true });

        // Keyboard
        slider.setAttribute('tabindex', '0');
        slider.addEventListener('keydown', function (e) {
            if (e.key === 'ArrowLeft')  { goTo(current - 1); resetAuto(); }
            if (e.key === 'ArrowRight') { goTo(current + 1); resetAuto(); }
        });

        // Pause on hover
        if (autoPlay && pauseHover) {
            slider.addEventListener('mouseenter', stopAuto);
            slider.addEventListener('mouseleave', startAuto);
        }

        // Init
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

// ── Review Submission Form — Star Picker ────────────────────────────────────
(function () {
    'use strict';

    function initStarPicker(form) {
        var picker  = form.querySelector('.rwf-star-picker');
        if (!picker) return;
        var buttons = picker.querySelectorAll('.rwf-star-btn');
        var input   = picker.querySelector('#rwf_rating');
        if (!buttons.length || !input) return;

        function setRating(value) {
            input.value = value;
            buttons.forEach(function (btn) {
                btn.classList.toggle('selected', parseInt(btn.dataset.value, 10) <= value);
            });
        }

        if (input.value) setRating(parseInt(input.value, 10));

        buttons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                setRating(parseInt(btn.dataset.value, 10));
            });
            btn.addEventListener('mouseenter', function () {
                var hoverVal = parseInt(btn.dataset.value, 10);
                buttons.forEach(function (b) {
                    b.classList.toggle('selected', parseInt(b.dataset.value, 10) <= hoverVal);
                });
            });
        });

        picker.addEventListener('mouseleave', function () {
            var current = parseInt(input.value, 10) || 0;
            buttons.forEach(function (b) {
                b.classList.toggle('selected', parseInt(b.dataset.value, 10) <= current);
            });
        });
    }

    function initAll() {
        document.querySelectorAll('.rwf-submission-form').forEach(initStarPicker);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }
})();

// ── Review Form — Photo Preview ─────────────────────────────────────────────
(function () {
    'use strict';

    function initPhotoPreview(form) {
        var input   = form.querySelector('#rwf_photo');
        var preview = form.querySelector('#rwf-photo-preview');
        if (!input || !preview) return;

        input.addEventListener('change', function () {
            var file = input.files[0];
            if (!file || !file.type.startsWith('image/')) {
                preview.style.display = 'none';
                preview.innerHTML = '';
                return;
            }
            var reader = new FileReader();
            reader.onload = function (e) {
                preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        });
    }

    function initAll() {
        document.querySelectorAll('.rwf-submission-form').forEach(initPhotoPreview);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }
})();
