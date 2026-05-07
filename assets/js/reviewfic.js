"use strict";

document.addEventListener('DOMContentLoaded', function () {

    // ── Template picker ───────────────────────────────────────
    var templateInput   = document.getElementById('reviewfic-template');
    var templateOptions = document.querySelectorAll('.rwf-template-option');

    templateOptions.forEach(function (opt) {
        opt.addEventListener('click', function () {
            templateOptions.forEach(function (o) { o.classList.remove('active'); });
            opt.classList.add('active');
            if (templateInput) templateInput.value = opt.dataset.value;
        });
    });

    // ── Column toggle ─────────────────────────────────────────
    var colBtns = document.querySelectorAll('.rwf-col-btn');
    colBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            colBtns.forEach(function (b) { b.classList.remove('active'); });
            btn.classList.add('active');
            document.getElementById('reviewfic-columns').value = btn.dataset.value;
        });
    });

    // ── Avatar toggle label ───────────────────────────────────
    var avatarToggle = document.getElementById('reviewfic-show-avatar');
    var avatarLabel  = document.getElementById('rwf-avatar-label');
    if (avatarToggle && avatarLabel) {
        avatarToggle.addEventListener('change', function () {
            avatarLabel.textContent = this.checked ? 'Yes — show reviewer photo' : 'No — hide reviewer photo';
        });
    }

    // ── Slider toggle label ───────────────────────────────────
    var sliderToggle = document.getElementById('reviewfic-slider');
    var sliderLabel  = document.getElementById('rwf-slider-label');
    if (sliderToggle && sliderLabel) {
        sliderToggle.addEventListener('change', function () {
            sliderLabel.textContent = this.checked ? 'Yes — show as slider' : 'No — show as grid';
        });
    }

    // ── Generate shortcode ────────────────────────────────────
    var generateBtn = document.getElementById('reviewfic-generate-shortcode');
    if (generateBtn) {
        generateBtn.addEventListener('click', function () {
            var template   = templateInput ? templateInput.value : '1';
            var category   = document.getElementById('reviewfic-category').value;
            var source     = document.getElementById('reviewfic-source').value;
            var columns    = document.getElementById('reviewfic-columns').value;
            var maxItems   = document.getElementById('reviewfic-max-items').value;
            var showAvatar = document.getElementById('reviewfic-show-avatar').checked ? 'yes' : 'no';
            var slider     = document.getElementById('reviewfic-slider').checked ? 'yes' : 'no';

            var shortcode = '[reviewfic'
                + ' template="'    + template   + '"'
                + ' slider="'      + slider     + '"'
                + ' category="'    + category   + '"'
                + ' source="'      + source     + '"'
                + ' columns="'     + columns    + '"'
                + ' max_items="'   + (maxItems !== '' ? maxItems : '-1') + '"'
                + ' show_avatar="' + showAvatar + '"'
                + ']';

            document.getElementById('reviewfic-shortcode-result').textContent = shortcode;
            document.getElementById('rwf-output-placeholder').style.display = 'none';
            document.getElementById('rwf-code-wrap').style.display           = 'block';
            document.getElementById('rwf-output-footer').style.display       = 'flex';
        });
    }

    // ── Copy to clipboard ─────────────────────────────────────
    var copyBtn  = document.getElementById('reviewfic-copy-shortcode');
    var copyText = document.getElementById('rwf-copy-text');
    var copyIcon = document.getElementById('rwf-copy-icon');

    if (copyBtn) {
        copyBtn.addEventListener('click', function () {
            var code = document.getElementById('reviewfic-shortcode-result').textContent;
            if (!code) return;
            navigator.clipboard.writeText(code).then(function () {
                copyText.textContent = 'Copied!';
                copyIcon.classList.remove('dashicons-clipboard');
                copyIcon.classList.add('dashicons-yes');
                copyBtn.classList.add('copied');
                setTimeout(function () {
                    copyText.textContent = 'Copy to Clipboard';
                    copyIcon.classList.remove('dashicons-yes');
                    copyIcon.classList.add('dashicons-clipboard');
                    copyBtn.classList.remove('copied');
                }, 2000);
            });
        });
    }
});
