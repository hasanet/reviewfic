<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function reviewfic_add_meta_boxes() {
    add_meta_box(
        'reviewfic_meta_box',
        'Review Details',
        'reviewfic_meta_box_callback',
        'reviewfic_reviews',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'reviewfic_add_meta_boxes');

function reviewfic_meta_box_callback($post) {
    wp_nonce_field('reviewfic_save_meta_box_data', 'reviewfic_meta_box_nonce');

    $stars               = get_post_meta($post->ID, 'reviewfic_review_stars', true);
    $client_name         = get_post_meta($post->ID, 'reviewfic_client_name', true);
    $client_designation  = get_post_meta($post->ID, 'reviewfic_client_designation', true);
    $client_company      = get_post_meta($post->ID, 'reviewfic_client_company', true);
    $reviewer_photo = get_post_meta($post->ID, 'reviewfic_reviewer_photo', true);
    $photo_url      = $reviewer_photo ? wp_get_attachment_image_url($reviewer_photo, 'thumbnail') : '';

    $source_terms      = wp_get_post_terms($post->ID, 'reviewfic_source', array('fields' => 'ids'));
    $current_source_id = (!empty($source_terms) && !is_wp_error($source_terms)) ? $source_terms[0] : '';
    $all_sources       = get_terms(array('taxonomy' => 'reviewfic_source', 'hide_empty' => false));

    // Build slug map for JS badge preview
    $source_slug_map = array();
    if (!is_wp_error($all_sources)) {
        foreach ($all_sources as $t) {
            $source_slug_map[$t->term_id] = $t->slug;
        }
    }
    ?>
    <div class="rwf-meta-wrap">

        <!-- Section: Review Info -->
        <div class="rwf-section">
            <div class="rwf-section-header">
                <span class="rwf-section-icon"><span class="dashicons dashicons-star-filled"></span></span>
                Review Info
            </div>
            <div class="rwf-section-body">

                <div class="rwf-field">
                    <label class="rwf-label" for="reviewfic_review_stars">Star Rating</label>
                    <div class="rwf-control">
                        <div class="rwf-stars-wrap">
                            <input type="number" name="reviewfic_review_stars" id="reviewfic_review_stars"
                                   value="<?php echo esc_attr($stars); ?>"
                                   step="0.5" min="1" max="5" class="rwf-number-input" />
                            <span class="rwf-stars-visual" id="rwf-stars-visual">
                                <?php
                                $s = floatval($stars);
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= floor($s)) echo '<span class="rwf-star full">&#9733;</span>';
                                    elseif ($i == ceil($s) && fmod($s, 1) >= 0.5) echo '<span class="rwf-star half">&#9733;</span>';
                                    else echo '<span class="rwf-star empty">&#9733;</span>';
                                }
                                ?>
                            </span>
                        </div>
                        <p class="rwf-hint">Enter 1–5, increments of 0.5</p>
                    </div>
                </div>

                <div class="rwf-field">
                    <label class="rwf-label" for="reviewfic_review_source">Review Source</label>
                    <div class="rwf-control">
                        <div class="rwf-source-row">
                            <?php if (!empty($all_sources) && !is_wp_error($all_sources)) : ?>
                                <select name="reviewfic_review_source" id="reviewfic_review_source" class="rwf-select">
                                    <option value="">— None —</option>
                                    <?php foreach ($all_sources as $t) : ?>
                                        <option value="<?php echo esc_attr($t->term_id); ?>"
                                            <?php selected($current_source_id, $t->term_id); ?>>
                                            <?php echo esc_html($t->name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <span id="rwf-source-preview" class="rwf-badge-preview" style="<?php echo $current_source_id ? '' : 'display:none;'; ?>"></span>
                            <?php else : ?>
                                <p class="rwf-hint">No sources yet. Go to <strong>Reviews → Sources</strong> to add some.</p>
                            <?php endif; ?>
                        </div>
                        <p class="rwf-hint">Manage platforms under <a href="<?php echo esc_url(admin_url('edit-tags.php?taxonomy=reviewfic_source&post_type=reviewfic_reviews')); ?>">Reviews → Sources</a>.</p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Section: Reviewer -->
        <div class="rwf-section">
            <div class="rwf-section-header">
                <span class="rwf-section-icon"><span class="dashicons dashicons-admin-users"></span></span>
                Reviewer
            </div>
            <div class="rwf-section-body">

                <div class="rwf-field">
                    <label class="rwf-label" for="reviewfic_client_name">Name</label>
                    <div class="rwf-control">
                        <input type="text" name="reviewfic_client_name" id="reviewfic_client_name"
                               value="<?php echo esc_attr($client_name); ?>"
                               class="rwf-text-input" placeholder="e.g. Jane Smith" />
                    </div>
                </div>

                <div class="rwf-field">
                    <label class="rwf-label" for="reviewfic_client_designation">Designation</label>
                    <div class="rwf-control">
                        <input type="text" name="reviewfic_client_designation" id="reviewfic_client_designation"
                               value="<?php echo esc_attr($client_designation); ?>"
                               class="rwf-text-input" placeholder="e.g. CEO, Marketing Manager" />
                        <p class="rwf-hint">Job title or role shown below the reviewer's name.</p>
                    </div>
                </div>

                <div class="rwf-field">
                    <label class="rwf-label" for="reviewfic_client_company">Company</label>
                    <div class="rwf-control">
                        <input type="text" name="reviewfic_client_company" id="reviewfic_client_company"
                               value="<?php echo esc_attr($client_company); ?>"
                               class="rwf-text-input" placeholder="e.g. Acme Corp" />
                    </div>
                </div>

                <div class="rwf-field">
                    <label class="rwf-label">Photo</label>
                    <div class="rwf-control">
                        <div class="rwf-avatar-upload">
                            <div class="rwf-avatar-preview <?php echo $photo_url ? 'has-image' : ''; ?>" id="rwf-avatar-preview">
                                <?php if ($photo_url) : ?>
                                    <img src="<?php echo esc_url($photo_url); ?>" id="rwf-avatar-img" />
                                <?php else : ?>
                                    <span class="rwf-avatar-placeholder" id="rwf-avatar-placeholder"><span class="dashicons dashicons-admin-users"></span></span>
                                    <img src="" id="rwf-avatar-img" style="display:none;" />
                                <?php endif; ?>
                            </div>
                            <div class="rwf-avatar-actions">
                                <input type="hidden" name="reviewfic_reviewer_photo" id="reviewfic_reviewer_photo"
                                       value="<?php echo esc_attr($reviewer_photo); ?>" />
                                <button type="button" id="rwf-upload-btn" class="rwf-btn rwf-btn-secondary">
                                    <?php echo $reviewer_photo ? 'Change Photo' : 'Upload Photo'; ?>
                                </button>
                                <button type="button" id="rwf-remove-btn" class="rwf-btn rwf-btn-danger"
                                        <?php echo !$reviewer_photo ? 'style="display:none;"' : ''; ?>>
                                    Remove
                                </button>
                                <p class="rwf-hint">Displayed as a circular avatar on the review card.</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <script>
    jQuery(document).ready(function($) {

        // ── Star visual preview ───────────────────────────────
        $('#reviewfic_review_stars').on('input change', function() {
            var val = parseFloat($(this).val()) || 0;
            var html = '';
            for (var i = 1; i <= 5; i++) {
                if (i <= Math.floor(val)) {
                    html += '<span class="rwf-star full">&#9733;</span>';
                } else if (i === Math.ceil(val) && (val % 1) >= 0.5) {
                    html += '<span class="rwf-star half">&#9733;</span>';
                } else {
                    html += '<span class="rwf-star empty">&#9733;</span>';
                }
            }
            $('#rwf-stars-visual').html(html);
        });

        // ── Source badge live preview ─────────────────────────
        var slugMap = <?php echo wp_json_encode($source_slug_map); ?>;
        var knownSources = ['google','trustpilot','g2','capterra','facebook','yelp','amazon'];

        function updateSourcePreview() {
            var termId = $('#reviewfic_review_source').val();
            var $preview = $('#rwf-source-preview');
            if (!termId) { $preview.hide().text(''); return; }
            var slug  = slugMap[termId] || '';
            var label = $('#reviewfic_review_source option:selected').text();
            var cls   = knownSources.indexOf(slug) !== -1 ? 'rwf-source-' + slug : 'rwf-source-custom';
            $preview.removeClass().addClass('rwf-badge-preview reviewfic-source-badge ' + cls).text(label).show();
        }

        $('#reviewfic_review_source').on('change', updateSourcePreview);
        updateSourcePreview();

        // ── Avatar media uploader ─────────────────────────────
        var mediaUploader;

        $('#rwf-upload-btn').on('click', function(e) {
            e.preventDefault();
            if (mediaUploader) { mediaUploader.open(); return; }
            mediaUploader = wp.media({
                title: 'Select Reviewer Photo',
                button: { text: 'Use this photo' },
                multiple: false
            });
            mediaUploader.on('select', function() {
                var att = mediaUploader.state().get('selection').first().toJSON();
                $('#reviewfic_reviewer_photo').val(att.id);
                $('#rwf-avatar-img').attr('src', att.url).show();
                $('#rwf-avatar-placeholder').hide();
                $('#rwf-avatar-preview').addClass('has-image');
                $('#rwf-upload-btn').text('Change Photo');
                $('#rwf-remove-btn').show();
            });
            mediaUploader.open();
        });

        $('#rwf-remove-btn').on('click', function(e) {
            e.preventDefault();
            $('#reviewfic_reviewer_photo').val('');
            $('#rwf-avatar-img').attr('src', '').hide();
            $('#rwf-avatar-placeholder').show();
            $('#rwf-avatar-preview').removeClass('has-image');
            $('#rwf-upload-btn').text('Upload Photo');
            $(this).hide();
        });
    });
    </script>
    <?php
}

function reviewfic_save_meta_boxes($post_id) {
    if (!isset($_POST['reviewfic_meta_box_nonce'])) return;

    $nonce = sanitize_text_field(wp_unslash($_POST['reviewfic_meta_box_nonce']));
    if (!wp_verify_nonce($nonce, 'reviewfic_save_meta_box_data')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['reviewfic_review_stars'])) {
        update_post_meta($post_id, 'reviewfic_review_stars',
            sanitize_text_field(wp_unslash($_POST['reviewfic_review_stars'])));
    }
    if (isset($_POST['reviewfic_client_name'])) {
        update_post_meta($post_id, 'reviewfic_client_name',
            sanitize_text_field(wp_unslash($_POST['reviewfic_client_name'])));
    }
    if (isset($_POST['reviewfic_client_designation'])) {
        update_post_meta($post_id, 'reviewfic_client_designation',
            sanitize_text_field(wp_unslash($_POST['reviewfic_client_designation'])));
    }
    if (isset($_POST['reviewfic_client_company'])) {
        update_post_meta($post_id, 'reviewfic_client_company',
            sanitize_text_field(wp_unslash($_POST['reviewfic_client_company'])));
    }

    if (isset($_POST['reviewfic_reviewer_photo'])) {
        $photo_id = absint(wp_unslash($_POST['reviewfic_reviewer_photo']));
        if ($photo_id > 0) {
            update_post_meta($post_id, 'reviewfic_reviewer_photo', $photo_id);
        } else {
            delete_post_meta($post_id, 'reviewfic_reviewer_photo');
        }
    }

    if (isset($_POST['reviewfic_review_source'])) {
        $source_id = absint(wp_unslash($_POST['reviewfic_review_source']));
        wp_set_post_terms($post_id, $source_id > 0 ? array($source_id) : array(), 'reviewfic_source');
    }
}
add_action('save_post', 'reviewfic_save_meta_boxes');
