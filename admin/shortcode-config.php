<?php
if (!defined('ABSPATH')) exit;

// ── Register CPT ──────────────────────────────────────────────
function reviewfic_register_config_cpt() {
    register_post_type('reviewfic_config', array(
        'labels' => array(
            'name'          => 'Shortcode Generator',
            'singular_name' => 'Shortcode',
            'add_new'       => 'Create New',
            'add_new_item'  => 'Create New Shortcode',
            'edit_item'     => 'Edit Shortcode',
            'all_items'     => 'Shortcode Generator',
            'menu_name'     => 'Shortcode Generator',
        ),
        'public'          => false,
        'show_ui'         => true,
        'show_in_menu'    => 'edit.php?post_type=reviewfic_reviews',
        'supports'        => array('title'),
        'capability_type' => 'post',
    ));
}
add_action('init', 'reviewfic_register_config_cpt');

// ── Meta boxes ────────────────────────────────────────────────
function reviewfic_add_config_meta_boxes() {
    add_meta_box('rwf_config_shortcode', 'Your Shortcode',
        'rwf_config_shortcode_cb', 'reviewfic_config', 'side', 'high');
    add_meta_box('rwf_config_options', 'Shortcode Options',
        'rwf_config_options_cb', 'reviewfic_config', 'normal', 'high');
}
add_action('add_meta_boxes', 'reviewfic_add_config_meta_boxes');

// ── Sidebar: shortcode display ────────────────────────────────
function rwf_config_shortcode_cb($post) {
    if ($post->post_status === 'auto-draft') {
        echo '<p style="color:#6b7280;font-size:13px;">Save this config first to get your shortcode.</p>';
        return;
    }
    $sc = '[reviewfic id="' . $post->ID . '"]';
    ?>
    <div class="rwf-sc-display">
        <code id="rwf-sc-code"><?php echo esc_html($sc); ?></code>
        <button type="button" class="button button-primary rwf-sc-copy" data-copy="<?php echo esc_attr($sc); ?>">
            Copy Shortcode
        </button>
    </div>
    <p class="description">Paste into any page or post. Changing options here updates all instances automatically — no need to touch the shortcode again.</p>
    <script>
    document.querySelector('.rwf-sc-copy') && document.querySelector('.rwf-sc-copy').addEventListener('click', function() {
        navigator.clipboard.writeText(this.dataset.copy).then(() => {
            var orig = this.textContent;
            this.textContent = '✓ Copied!';
            setTimeout(() => { this.textContent = orig; }, 2000);
        });
    });
    </script>
    <?php
}

// ── Main: options meta box ────────────────────────────────────
function rwf_config_options_cb($post) {
    wp_nonce_field('rwf_save_config', 'rwf_config_nonce');

    $get = function($key, $default) use ($post) {
        $v = get_post_meta($post->ID, $key, true);
        return $v !== '' && $v !== false ? $v : $default;
    };

    $template    = $get('rwf_template',     '1');
    $columns     = $get('rwf_columns',      '3');
    $max_items   = $get('rwf_max_items',    '');
    $show_avatar = $get('rwf_show_avatar',  'yes');
    $category    = $get('rwf_category',     'all');
    $source      = $get('rwf_source',       'all');
    $slider      = $get('rwf_slider',       'no');
    $slider_nav  = $get('rwf_slider_nav',   'yes');
    $slider_dots = $get('rwf_slider_dots',  'yes');
    $slider_auto = $get('rwf_slider_auto',  'no');
    $slider_speed= $get('rwf_slider_speed', '4000');
    $slider_loop = $get('rwf_slider_loop',  'yes');
    $pagination  = $get('rwf_pagination',   'no');
    $per_page    = $get('rwf_per_page',     '6');
    // Design
    $card_bg      = $get('rwf_card_bg',      '');
    $text_color   = $get('rwf_text_color',   '');
    $star_color   = $get('rwf_star_color',   '');
    $accent_color = $get('rwf_accent_color', '');
    $meta_color   = $get('rwf_meta_color',   '');
    $name_color   = $get('rwf_name_color',   '');
    $card_border  = $get('rwf_card_border',  '');
    $card_shadow  = $get('rwf_card_shadow',  'none');
    $card_radius  = $get('rwf_card_radius',  '10');

    $categories   = get_terms(array('taxonomy' => 'reviewfic_category', 'hide_empty' => false));
    $source_terms = get_terms(array('taxonomy' => 'reviewfic_source',   'hide_empty' => false));

    $templates = array(
        '1' => array(
            'label' => 'Classic',
            'desc'  => 'Stars → title → content → client row',
            'cls'   => 'rwf-mp-1',
            'html'  => '<div class="rwf-mp-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                        <div class="rwf-mp-line"></div>
                        <div class="rwf-mp-line short"></div>
                        <div class="rwf-mp-avatar-row">
                            <div class="rwf-mp-circle"></div>
                            <div class="rwf-mp-lines">
                                <div class="rwf-mp-line short"></div>
                                <div class="rwf-mp-line xshort"></div>
                            </div>
                        </div>',
        ),
        '2' => array(
            'label' => 'Quote',
            'desc'  => 'Client at top, large italic quote, orange left border',
            'cls'   => 'rwf-mp-2',
            'html'  => '<div class="rwf-mp-avatar-row">
                            <div class="rwf-mp-circle"></div>
                            <div class="rwf-mp-lines">
                                <div class="rwf-mp-line short"></div>
                                <div class="rwf-mp-line xshort"></div>
                            </div>
                        </div>
                        <div class="rwf-mp-quote">&ldquo;</div>
                        <div class="rwf-mp-line"></div>
                        <div class="rwf-mp-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>',
        ),
        '3' => array(
            'label' => 'Minimal',
            'desc'  => 'Borderless card with orange top accent line',
            'cls'   => 'rwf-mp-3',
            'html'  => '<div class="rwf-mp-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                        <div class="rwf-mp-line"></div>
                        <div class="rwf-mp-line short"></div>
                        <div class="rwf-mp-avatar-row">
                            <div class="rwf-mp-circle"></div>
                            <div class="rwf-mp-lines">
                                <div class="rwf-mp-line short"></div>
                            </div>
                        </div>',
        ),
        '4' => array(
            'label' => 'Dark',
            'desc'  => 'Dark card, white text, gold stars',
            'cls'   => 'rwf-mp-4',
            'html'  => '<div class="rwf-mp-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                        <div class="rwf-mp-line light"></div>
                        <div class="rwf-mp-line light short"></div>
                        <div class="rwf-mp-avatar-row">
                            <div class="rwf-mp-circle orange-ring"></div>
                            <div class="rwf-mp-lines">
                                <div class="rwf-mp-line light short"></div>
                                <div class="rwf-mp-line light xshort"></div>
                            </div>
                        </div>',
        ),
        '5' => array(
            'label' => 'Centered',
            'desc'  => 'Everything centered, large avatar at top',
            'cls'   => 'rwf-mp-5',
            'html'  => '<div class="rwf-mp-circle centered"></div>
                        <div class="rwf-mp-line centered short"></div>
                        <div class="rwf-mp-line centered xshort"></div>
                        <div class="rwf-mp-stars centered">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                        <div class="rwf-mp-line centered"></div>',
        ),
    );
    ?>
    <div class="rwf-config-wrap">

        <!-- Template -->
        <div class="rwf-config-section">
            <div class="rwf-config-section-title">
                <span class="dashicons dashicons-layout"></span> Template
            </div>
            <div class="rwf-tpl-list" id="rwf-tpl-list">
                <?php foreach ($templates as $val => $tpl) : ?>
                <div class="rwf-tpl-row <?php echo $template === $val ? 'active' : ''; ?>" data-value="<?php echo esc_attr($val); ?>">
                    <div class="rwf-tpl-num"><?php echo esc_html($val); ?></div>
                    <div class="rwf-tpl-mini-preview <?php echo esc_attr($tpl['cls']); ?>">
                        <?php echo $tpl['html']; ?>
                    </div>
                    <div class="rwf-tpl-info">
                        <strong><?php echo esc_html($tpl['label']); ?></strong>
                        <span><?php echo esc_html($tpl['desc']); ?></span>
                    </div>
                    <div class="rwf-tpl-check dashicons dashicons-yes"></div>
                </div>
                <?php endforeach; ?>
            </div>
            <input type="hidden" name="rwf_template" id="rwf_template" value="<?php echo esc_attr($template); ?>" />
        </div>

        <div class="rwf-config-grid">

            <!-- Display -->
            <div class="rwf-config-section">
                <div class="rwf-config-section-title">
                    <span class="dashicons dashicons-grid-view"></span> Display
                </div>
                <div class="rwf-config-row">
                    <label class="rwf-config-label">Columns</label>
                    <div class="rwf-columns-toggle">
                        <?php foreach (array('1','2','3','4') as $col) : ?>
                        <button type="button" class="rwf-col-btn <?php echo $columns === $col ? 'active' : ''; ?>" data-value="<?php echo $col; ?>"><?php echo $col; ?></button>
                        <?php endforeach; ?>
                    </div>
                    <input type="hidden" name="rwf_columns" id="rwf_columns" value="<?php echo esc_attr($columns); ?>" />
                </div>
                <div class="rwf-config-row">
                    <label class="rwf-config-label" for="rwf_max_items">Max Items</label>
                    <input type="number" name="rwf_max_items" id="rwf_max_items" value="<?php echo esc_attr($max_items); ?>" min="1" placeholder="Unlimited" class="small-text" />
                </div>
                <div class="rwf-config-row">
                    <label class="rwf-config-label">Show Avatar</label>
                    <label class="rwf-toggle">
                        <input type="checkbox" name="rwf_show_avatar" id="rwf_show_avatar" <?php checked($show_avatar, 'yes'); ?> />
                        <span class="rwf-toggle-track"><span class="rwf-toggle-thumb"></span></span>
                        <span class="rwf-toggle-label"><?php echo $show_avatar === 'yes' ? 'Yes' : 'No'; ?></span>
                    </label>
                </div>
            </div>

            <!-- Filter -->
            <div class="rwf-config-section">
                <div class="rwf-config-section-title">
                    <span class="dashicons dashicons-filter"></span> Filter
                </div>
                <div class="rwf-config-row">
                    <label class="rwf-config-label" for="rwf_category">Category</label>
                    <select name="rwf_category" id="rwf_category" class="rwf-select">
                        <option value="all">All Categories</option>
                        <?php if (!is_wp_error($categories)) foreach ($categories as $cat) : ?>
                        <option value="<?php echo esc_attr($cat->slug); ?>" <?php selected($category, $cat->slug); ?>><?php echo esc_html($cat->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="rwf-config-row">
                    <label class="rwf-config-label" for="rwf_source">Review Source</label>
                    <select name="rwf_source" id="rwf_source" class="rwf-select">
                        <option value="all">All Sources</option>
                        <?php if (!is_wp_error($source_terms)) foreach ($source_terms as $t) : ?>
                        <option value="<?php echo esc_attr($t->slug); ?>" <?php selected($source, $t->slug); ?>><?php echo esc_html($t->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

        </div>

        <!-- Pagination -->
        <div class="rwf-config-section">
            <div class="rwf-config-section-title">
                <span class="dashicons dashicons-controls-forward"></span> Pagination
            </div>
            <div class="rwf-config-row">
                <label class="rwf-config-label">Enable Pagination</label>
                <label class="rwf-toggle">
                    <input type="checkbox" name="rwf_pagination" id="rwf_pagination" <?php checked($pagination, 'yes'); ?> />
                    <span class="rwf-toggle-track"><span class="rwf-toggle-thumb"></span></span>
                    <span class="rwf-toggle-label"><?php echo $pagination === 'yes' ? 'Yes' : 'No'; ?></span>
                </label>
            </div>
            <div class="rwf-slider-subopts <?php echo $pagination !== 'yes' ? 'rwf-collapsed' : ''; ?>" id="rwf-pagination-subopts">
                <div class="rwf-subopts-grid">
                    <div class="rwf-config-row">
                        <label class="rwf-config-label" for="rwf_per_page">Reviews Per Page</label>
                        <input type="number" name="rwf_per_page" id="rwf_per_page" value="<?php echo esc_attr($per_page); ?>" min="1" max="100" class="small-text" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Design -->
        <div class="rwf-config-section">
            <div class="rwf-config-section-title">
                <span class="dashicons dashicons-art"></span> Design
                <span class="rwf-section-hint">Leave blank to use template defaults</span>
            </div>
            <div class="rwf-config-row">
                <label class="rwf-config-label">Card Background</label>
                <input type="text" name="rwf_card_bg" class="rwf-color-picker" value="<?php echo esc_attr($card_bg); ?>" data-default-color="" />
            </div>
            <div class="rwf-config-row">
                <label class="rwf-config-label">Text Color</label>
                <input type="text" name="rwf_text_color" class="rwf-color-picker" value="<?php echo esc_attr($text_color); ?>" data-default-color="" />
            </div>
            <div class="rwf-config-row">
                <label class="rwf-config-label">Reviewer Name Color</label>
                <input type="text" name="rwf_name_color" class="rwf-color-picker" value="<?php echo esc_attr($name_color); ?>" data-default-color="" />
            </div>
            <div class="rwf-config-row">
                <label class="rwf-config-label">Designation &amp; Company Color</label>
                <input type="text" name="rwf_meta_color" class="rwf-color-picker" value="<?php echo esc_attr($meta_color); ?>" data-default-color="" />
            </div>
            <div class="rwf-config-row">
                <label class="rwf-config-label">Star Color</label>
                <input type="text" name="rwf_star_color" class="rwf-color-picker" value="<?php echo esc_attr($star_color); ?>" data-default-color="" />
            </div>
            <div class="rwf-config-row">
                <label class="rwf-config-label">Accent Color</label>
                <input type="text" name="rwf_accent_color" class="rwf-color-picker" value="<?php echo esc_attr($accent_color); ?>" data-default-color="" />
                <span style="font-size:12px;color:#6b7280;">Template 2 left border · Template 3 top bar · Source badges</span>
            </div>
            <div class="rwf-config-row">
                <label class="rwf-config-label">Card Border Color</label>
                <input type="text" name="rwf_card_border" class="rwf-color-picker" value="<?php echo esc_attr($card_border); ?>" data-default-color="" />
            </div>
            <div class="rwf-config-row">
                <label class="rwf-config-label" for="rwf_card_shadow">Box Shadow</label>
                <select name="rwf_card_shadow" id="rwf_card_shadow" class="rwf-select-sm">
                    <option value="none"   <?php selected($card_shadow, 'none');   ?>>None</option>
                    <option value="sm"     <?php selected($card_shadow, 'sm');     ?>>Subtle</option>
                    <option value="md"     <?php selected($card_shadow, 'md');     ?>>Medium</option>
                    <option value="lg"     <?php selected($card_shadow, 'lg');     ?>>Strong</option>
                </select>
            </div>
            <div class="rwf-config-row">
                <label class="rwf-config-label" for="rwf_card_radius">Border Radius</label>
                <div class="rwf-range-wrap">
                    <input type="range" name="rwf_card_radius" id="rwf_card_radius" min="0" max="24" value="<?php echo esc_attr($card_radius); ?>" class="rwf-range" />
                    <span class="rwf-range-val" id="rwf-radius-val"><?php echo esc_attr($card_radius); ?>px</span>
                </div>
            </div>
        </div>

        <!-- Slider -->
        <div class="rwf-config-section">
            <div class="rwf-config-section-title">
                <span class="dashicons dashicons-slides"></span> Slider
            </div>
            <div class="rwf-config-row">
                <label class="rwf-config-label">Enable Slider</label>
                <label class="rwf-toggle">
                    <input type="checkbox" name="rwf_slider" id="rwf_slider" <?php checked($slider, 'yes'); ?> />
                    <span class="rwf-toggle-track"><span class="rwf-toggle-thumb"></span></span>
                    <span class="rwf-toggle-label"><?php echo $slider === 'yes' ? 'Yes' : 'No'; ?></span>
                </label>
            </div>
            <div class="rwf-slider-subopts <?php echo $slider !== 'yes' ? 'rwf-collapsed' : ''; ?>" id="rwf-slider-subopts">
                <div class="rwf-subopts-grid">
                    <div class="rwf-config-row">
                        <label class="rwf-config-label">Navigation Arrows</label>
                        <label class="rwf-toggle">
                            <input type="checkbox" name="rwf_slider_nav" id="rwf_slider_nav" <?php checked($slider_nav, 'yes'); ?> />
                            <span class="rwf-toggle-track"><span class="rwf-toggle-thumb"></span></span>
                            <span class="rwf-toggle-label"><?php echo $slider_nav === 'yes' ? 'Show' : 'Hide'; ?></span>
                        </label>
                    </div>
                    <div class="rwf-config-row">
                        <label class="rwf-config-label">Dot Indicators</label>
                        <label class="rwf-toggle">
                            <input type="checkbox" name="rwf_slider_dots" id="rwf_slider_dots" <?php checked($slider_dots, 'yes'); ?> />
                            <span class="rwf-toggle-track"><span class="rwf-toggle-thumb"></span></span>
                            <span class="rwf-toggle-label"><?php echo $slider_dots === 'yes' ? 'Show' : 'Hide'; ?></span>
                        </label>
                    </div>
                    <div class="rwf-config-row">
                        <label class="rwf-config-label">Autoplay</label>
                        <label class="rwf-toggle">
                            <input type="checkbox" name="rwf_slider_auto" id="rwf_slider_auto" <?php checked($slider_auto, 'yes'); ?> />
                            <span class="rwf-toggle-track"><span class="rwf-toggle-thumb"></span></span>
                            <span class="rwf-toggle-label"><?php echo $slider_auto === 'yes' ? 'On' : 'Off'; ?></span>
                        </label>
                    </div>
                    <div class="rwf-config-row" id="rwf-speed-row" style="<?php echo $slider_auto !== 'yes' ? 'opacity:.45;pointer-events:none;' : ''; ?>">
                        <label class="rwf-config-label" for="rwf_slider_speed">Speed (ms)</label>
                        <input type="number" name="rwf_slider_speed" id="rwf_slider_speed" value="<?php echo esc_attr($slider_speed); ?>" min="500" step="500" class="small-text" /> <span style="color:#6b7280;font-size:12px;">milliseconds</span>
                    </div>
                    <div class="rwf-config-row">
                        <label class="rwf-config-label">Infinite Loop</label>
                        <label class="rwf-toggle">
                            <input type="checkbox" name="rwf_slider_loop" id="rwf_slider_loop" <?php checked($slider_loop, 'yes'); ?> />
                            <span class="rwf-toggle-track"><span class="rwf-toggle-thumb"></span></span>
                            <span class="rwf-toggle-label"><?php echo $slider_loop === 'yes' ? 'On' : 'Off'; ?></span>
                        </label>
                    </div>
                    <div class="rwf-config-row">
                        <label class="rwf-config-label">Pause on Hover</label>
                        <label class="rwf-toggle">
                            <input type="checkbox" name="rwf_slider_pause" id="rwf_slider_pause" <?php checked($get('rwf_slider_pause','yes'), 'yes'); ?> />
                            <span class="rwf-toggle-track"><span class="rwf-toggle-thumb"></span></span>
                            <span class="rwf-toggle-label"><?php echo $get('rwf_slider_pause','yes') === 'yes' ? 'On' : 'Off'; ?></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
    jQuery(document).ready(function($) {
        // Template row picker — restore active state on load, then handle clicks
        function setActiveTpl(val) {
            $('.rwf-tpl-row').removeClass('active');
            $('.rwf-tpl-row[data-value="' + val + '"]').addClass('active');
            $('#rwf_template').val(val);
        }
        setActiveTpl($('#rwf_template').val() || '1');

        $('.rwf-tpl-row').on('click', function() {
            setActiveTpl($(this).data('value'));
        });

        // Column toggle
        $('.rwf-col-btn').on('click', function() {
            $('.rwf-col-btn').removeClass('active');
            $(this).addClass('active');
            $('#rwf_columns').val($(this).data('value'));
        });

        // Toggle label helper
        function syncLabel($cb, on, off) {
            $cb.closest('.rwf-toggle').find('.rwf-toggle-label').text($cb.is(':checked') ? on : off);
        }

        $('#rwf_show_avatar').on('change', function() { syncLabel($(this), 'Yes', 'No'); });
        $('#rwf_slider_nav').on('change',  function() { syncLabel($(this), 'Show', 'Hide'); });
        $('#rwf_slider_dots').on('change', function() { syncLabel($(this), 'Show', 'Hide'); });
        $('#rwf_slider_loop').on('change', function() { syncLabel($(this), 'On', 'Off'); });
        $('#rwf_slider_pause').on('change',function() { syncLabel($(this), 'On', 'Off'); });

        $('#rwf_slider_auto').on('change', function() {
            syncLabel($(this), 'On', 'Off');
            var on = $(this).is(':checked');
            $('#rwf-speed-row').css({'opacity': on ? '1' : '.45', 'pointer-events': on ? '' : 'none'});
        });

        $('#rwf_slider').on('change', function() {
            syncLabel($(this), 'Yes', 'No');
            var sliderOn = $(this).is(':checked');
            if (sliderOn) {
                $('#rwf-slider-subopts').removeClass('rwf-collapsed');
                // Disable pagination when slider is on
                if ($('#rwf_pagination').is(':checked')) {
                    $('#rwf_pagination').prop('checked', false);
                    syncLabel($('#rwf_pagination'), 'Yes', 'No');
                    $('#rwf-pagination-subopts').addClass('rwf-collapsed');
                }
                // Grey out columns — irrelevant in slider mode
                $('.rwf-col-btn').prop('disabled', true).css('opacity', '0.4');
            } else {
                $('#rwf-slider-subopts').addClass('rwf-collapsed');
                $('.rwf-col-btn').prop('disabled', false).css('opacity', '1');
            }
        });

        // Init column state on load
        if ($('#rwf_slider').is(':checked')) {
            $('.rwf-col-btn').prop('disabled', true).css('opacity', '0.4');
        }

        // Pagination toggle
        $('#rwf_pagination').on('change', function() {
            syncLabel($(this), 'Yes', 'No');
            var paginationOn = $(this).is(':checked');
            if (paginationOn) {
                $('#rwf-pagination-subopts').removeClass('rwf-collapsed');
                // Disable slider when pagination is on
                if ($('#rwf_slider').is(':checked')) {
                    $('#rwf_slider').prop('checked', false);
                    syncLabel($('#rwf_slider'), 'Yes', 'No');
                    $('#rwf-slider-subopts').addClass('rwf-collapsed');
                    $('.rwf-col-btn').prop('disabled', false).css('opacity', '1');
                }
            } else {
                $('#rwf-pagination-subopts').addClass('rwf-collapsed');
            }
        });

        // Border radius range slider
        $('#rwf_card_radius').on('input', function() {
            $('#rwf-radius-val').text($(this).val() + 'px');
        });

        // Color pickers
        if ($.fn.wpColorPicker) {
            $('.rwf-color-picker').wpColorPicker();
        }
    });
    </script>
    <?php
}

// ── Save ──────────────────────────────────────────────────────
function rwf_save_config_meta($post_id) {
    if (!isset($_POST['rwf_config_nonce'])) return;
    if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['rwf_config_nonce'])), 'rwf_save_config')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $checkboxes = array('rwf_slider','rwf_slider_nav','rwf_slider_dots','rwf_slider_auto','rwf_slider_loop','rwf_slider_pause','rwf_show_avatar','rwf_pagination');
    foreach ($checkboxes as $key) {
        update_post_meta($post_id, $key, isset($_POST[$key]) ? 'yes' : 'no');
    }

    $text_fields = array('rwf_template', 'rwf_columns', 'rwf_category', 'rwf_source', 'rwf_card_bg', 'rwf_text_color', 'rwf_star_color', 'rwf_accent_color', 'rwf_meta_color', 'rwf_name_color', 'rwf_card_border', 'rwf_card_shadow');
    foreach ($text_fields as $key) {
        if (isset($_POST[$key])) {
            update_post_meta($post_id, $key, sanitize_hex_color(wp_unslash($_POST[$key])) ?: sanitize_text_field(wp_unslash($_POST[$key])));
        }
    }

    $number_fields = array('rwf_slider_speed', 'rwf_max_items', 'rwf_per_page', 'rwf_card_radius');
    foreach ($number_fields as $key) {
        if (isset($_POST[$key]) && $_POST[$key] !== '') {
            update_post_meta($post_id, $key, absint($_POST[$key]));
        } else {
            update_post_meta($post_id, $key, '');
        }
    }
}
add_action('save_post_reviewfic_config', 'rwf_save_config_meta');
