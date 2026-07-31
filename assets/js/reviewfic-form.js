/**
 * Reviewfic — Review Submission Form
 * Star picker, drag & drop photo uploader, and connected-form styling
 * (Contact Form 7, WPForms, Fluent Forms, Gravity Forms).
 * Only loaded on pages where [reviewfic_form] actually renders.
 */
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

// ── Review Form — Drag & Drop Photo Uploader ───────────────────────────────
(function () {
    'use strict';

    function initDropzone(form) {
        var zone   = form.querySelector('#rwf-dropzone');
        var input  = form.querySelector('#rwf_photo');
        var avatar = form.querySelector('#rwf-dropzone-avatar');
        var browse = form.querySelector('.rwf-dropzone-browse');
        if (!zone || !input || !avatar) return;

        // Add remove button dynamically
        var removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'rwf-dropzone-remove';
        removeBtn.textContent = '✕ Remove photo';
        zone.appendChild(removeBtn);

        function previewFile(file) {
            if (!file || !file.type.startsWith('image/')) return;
            var reader = new FileReader();
            reader.onload = function (e) {
                avatar.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
                zone.classList.add('rwf-has-file');
            };
            reader.readAsDataURL(file);
        }

        function reset() {
            input.value = '';
            avatar.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>';
            zone.classList.remove('rwf-has-file');
        }

        input.addEventListener('change', function () {
            if (input.files[0]) previewFile(input.files[0]);
        });

        // Browse button click — relay to hidden input, stay above the input overlay
        browse.addEventListener('click', function (e) {
            e.stopPropagation();
            input.click();
        });

        removeBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            reset();
        });

        // Drag events
        zone.addEventListener('dragover', function (e) {
            e.preventDefault();
            zone.classList.add('rwf-drag-over');
        });
        zone.addEventListener('dragleave', function () {
            zone.classList.remove('rwf-drag-over');
        });
        zone.addEventListener('drop', function (e) {
            e.preventDefault();
            zone.classList.remove('rwf-drag-over');
            var file = e.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) {
                // Transfer to the real input via DataTransfer
                var dt = new DataTransfer();
                dt.items.add(file);
                input.files = dt.files;
                previewFile(file);
            }
        });
    }

    function initAll() {
        document.querySelectorAll('.rwf-submission-form').forEach(initDropzone);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }
})();

// ── CF7 Connected Form Styling & File Drag-Drop ─────────────────────────────
(function () {
    'use strict';

    var AVATAR_SVG = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>';

    function initCF7Dropzone(fileInput) {
        // Wrap the native file input in a drag-drop zone
        var zone = document.createElement('div');
        zone.className = 'rwf-cf7-dropzone';

        var avatar = document.createElement('div');
        avatar.className = 'rwf-cf7-avatar';
        avatar.innerHTML = AVATAR_SVG;

        var dropText = document.createElement('p');
        dropText.className = 'rwf-cf7-drop-text';
        dropText.innerHTML = 'Drag &amp; drop your photo here ' +
            '<span>or <button type="button" class="rwf-cf7-browse">Browse to Upload</button></span>';

        var hint = document.createElement('p');
        hint.className = 'rwf-cf7-drop-hint';
        hint.textContent = 'JPG, PNG, GIF or WebP — max 5 MB (optional)';

        var removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'rwf-cf7-remove';
        removeBtn.textContent = '✕ Remove photo';

        zone.appendChild(avatar);
        zone.appendChild(dropText);
        zone.appendChild(hint);
        zone.appendChild(removeBtn);

        // Insert zone before the hidden file input
        fileInput.parentNode.insertBefore(zone, fileInput);
        // Move file input inside zone (keeps it in the form)
        zone.appendChild(fileInput);

        var browse = zone.querySelector('.rwf-cf7-browse');

        function previewFile(file) {
            if (!file || !file.type.startsWith('image/')) return;
            var reader = new FileReader();
            reader.onload = function (e) {
                avatar.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
                zone.classList.add('rwf-has-file');
            };
            reader.readAsDataURL(file);
        }

        function reset() {
            fileInput.value = '';
            avatar.innerHTML = AVATAR_SVG;
            zone.classList.remove('rwf-has-file');
        }

        fileInput.addEventListener('change', function () {
            if (fileInput.files[0]) previewFile(fileInput.files[0]);
        });

        browse.addEventListener('click', function (e) {
            e.stopPropagation();
            fileInput.click();
        });

        removeBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            reset();
        });

        zone.addEventListener('dragover', function (e) {
            e.preventDefault();
            zone.classList.add('rwf-drag-over');
        });
        zone.addEventListener('dragleave', function () {
            zone.classList.remove('rwf-drag-over');
        });
        zone.addEventListener('drop', function (e) {
            e.preventDefault();
            zone.classList.remove('rwf-drag-over');
            var file = e.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) {
                var dt = new DataTransfer();
                dt.items.add(file);
                fileInput.files = dt.files;
                previewFile(file);
            }
        });
    }

    function initStyledForm(form) {
        form.classList.add('rwf-styled-cf7-form');
        // Init drag-drop on any file inputs
        var fileInputs = form.querySelectorAll('input[type="file"]');
        for (var i = 0; i < fileInputs.length; i++) {
            initCF7Dropzone(fileInputs[i]);
        }
    }

    function initCF7Styling() {
        if (!window.rwfCF7 || !rwfCF7.forms || !rwfCF7.forms.length) return;

        rwfCF7.forms.forEach(function (id) {
            // CF7 wraps each form in a div with id="wpcf7-f{id}-p{page}-o{n}"
            var wrappers = document.querySelectorAll('[id^="wpcf7-f' + id + '-"]');
            wrappers.forEach(function (wrapper) {
                var form = wrapper.querySelector('form.wpcf7-form');
                if (form) initStyledForm(form);
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCF7Styling);
    } else {
        initCF7Styling();
    }
})();

// ── WPForms / Fluent Forms / Gravity Forms styling ──────────────────────────
(function () {
    'use strict';

    function styleForms() {
        if (!window.rwfForms) return;

        // WPForms: wrapper id="wpforms-{id}" contains form.wpforms-form
        (rwfForms.wpforms || []).forEach(function (id) {
            var wrapper = document.getElementById('wpforms-' + id);
            if (wrapper) {
                var form = wrapper.querySelector('form');
                if (form) initRwfForm(form);
            }
        });

        // Fluent Forms: form element has id="fluentform_{id}"
        (rwfForms.fluent || []).forEach(function (id) {
            var form = document.getElementById('fluentform_' + id);
            if (form) initRwfForm(form);
        });

        // Gravity Forms: form element has id="gform_{id}"
        (rwfForms.gravity || []).forEach(function (id) {
            var form = document.getElementById('gform_' + id);
            if (form) initRwfForm(form);
        });
    }

    function initRwfForm(form) {
        if (form.classList.contains('rwf-styled-cf7-form')) return; // already done
        form.classList.add('rwf-styled-cf7-form');
        // Apply drag-drop to any file inputs
        var fileInputs = form.querySelectorAll('input[type="file"]');
        for (var i = 0; i < fileInputs.length; i++) {
            if (!fileInputs[i].closest('.rwf-cf7-dropzone')) {
                initCF7Dropzone(fileInputs[i]);
            }
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', styleForms);
    } else {
        styleForms();
    }
})();
