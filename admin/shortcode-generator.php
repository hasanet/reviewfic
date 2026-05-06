<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function reviewfic_add_shortcode_submenu() {
    add_submenu_page(
        'edit.php?post_type=reviewfic_reviews',
        'Shortcode Generator',
        'Shortcode Generator',
        'manage_options',
        'reviewfic_shortcode_generator',
        'reviewfic_shortcode_generator_page'
    );
}
add_action('admin_menu', 'reviewfic_add_shortcode_submenu');

function reviewfic_shortcode_generator_page() {
    $categories   = get_terms(array('taxonomy' => 'reviewfic_category', 'hide_empty' => false));
    $source_terms = get_terms(array('taxonomy' => 'reviewfic_source',   'hide_empty' => false));
    ?>
    <div class="wrap rwf-generator-wrap">

        <div class="rwf-generator-header">
            <span class="rwf-generator-header-icon"><span class="dashicons dashicons-megaphone"></span></span>
            <div>
                <h1 class="rwf-generator-title">Shortcode Generator</h1>
                <p class="rwf-generator-subtitle">Configure your options below and copy the generated shortcode into any page or post.</p>
            </div>
        </div>

        <div class="rwf-generator-layout">

            <!-- Left: Controls -->
            <div class="rwf-generator-form-card">
                <form id="reviewfic-shortcode-form">

                    <div class="rwf-gen-field">
                        <label class="rwf-gen-label" for="reviewfic-category">
                            <span class="dashicons dashicons-category"></span> Category
                        </label>
                        <select id="reviewfic-category" class="rwf-gen-select">
                            <option value="all">All Categories</option>
                            <?php if (!is_wp_error($categories)) : foreach ($categories as $cat) : ?>
                                <option value="<?php echo esc_attr($cat->slug); ?>"><?php echo esc_html($cat->name); ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>

                    <div class="rwf-gen-field">
                        <label class="rwf-gen-label" for="reviewfic-source">
                            <span class="dashicons dashicons-admin-site-alt3"></span> Source
                        </label>
                        <select id="reviewfic-source" class="rwf-gen-select">
                            <option value="all">All Sources</option>
                            <?php if (!is_wp_error($source_terms)) : foreach ($source_terms as $t) : ?>
                                <option value="<?php echo esc_attr($t->slug); ?>"><?php echo esc_html($t->name); ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                        <span class="rwf-gen-hint">
                            Add platforms under <a href="<?php echo esc_url(admin_url('edit-tags.php?taxonomy=reviewfic_source&post_type=reviewfic_reviews')); ?>">Reviews &rarr; Sources</a>
                        </span>
                    </div>

                    <div class="rwf-gen-field">
                        <label class="rwf-gen-label">
                            <span class="dashicons dashicons-columns"></span> Columns
                        </label>
                        <div class="rwf-columns-toggle" id="rwf-columns-toggle">
                            <button type="button" class="rwf-col-btn active" data-value="1">1</button>
                            <button type="button" class="rwf-col-btn" data-value="2">2</button>
                            <button type="button" class="rwf-col-btn" data-value="3">3</button>
                            <button type="button" class="rwf-col-btn" data-value="4">4</button>
                        </div>
                        <input type="hidden" id="reviewfic-columns" value="1" />
                    </div>

                    <div class="rwf-gen-field">
                        <label class="rwf-gen-label" for="reviewfic-max-items">
                            <span class="dashicons dashicons-list-view"></span> Max Items
                        </label>
                        <input type="number" id="reviewfic-max-items" class="rwf-gen-number"
                               placeholder="Unlimited" min="1" />
                        <span class="rwf-gen-hint">Leave blank to show all reviews</span>
                    </div>

                    <div class="rwf-gen-field">
                        <label class="rwf-gen-label">
                            <span class="dashicons dashicons-format-image"></span> Show Avatar
                        </label>
                        <label class="rwf-toggle">
                            <input type="checkbox" id="reviewfic-show-avatar" checked />
                            <span class="rwf-toggle-track">
                                <span class="rwf-toggle-thumb"></span>
                            </span>
                            <span class="rwf-toggle-label" id="rwf-avatar-label">Yes — show reviewer photo</span>
                        </label>
                    </div>

                    <button type="button" id="reviewfic-generate-shortcode" class="rwf-gen-btn">
                        <span class="dashicons dashicons-shortcode"></span>
                        Generate Shortcode
                    </button>

                </form>
            </div>

            <!-- Right: Output -->
            <div class="rwf-generator-output-card">
                <div class="rwf-output-header">Generated Shortcode</div>
                <div class="rwf-output-body">
                    <div class="rwf-output-placeholder" id="rwf-output-placeholder">
                        Configure your options and click <strong>Generate Shortcode</strong>
                    </div>
                    <div class="rwf-code-wrap" id="rwf-code-wrap" style="display:none;">
                        <code id="reviewfic-shortcode-result"></code>
                    </div>
                </div>
                <div class="rwf-output-footer" id="rwf-output-footer" style="display:none;">
                    <button type="button" id="reviewfic-copy-shortcode" class="rwf-copy-btn">
                        <span class="dashicons dashicons-clipboard" id="rwf-copy-icon"></span>
                        <span id="rwf-copy-text">Copy to Clipboard</span>
                    </button>
                </div>

                <div class="rwf-usage-card">
                    <div class="rwf-usage-title">
                        <span class="dashicons dashicons-book"></span> Shortcode Parameters
                    </div>
                    <table class="rwf-usage-table">
                        <tr><td><code>category</code></td><td>Category slug or <code>all</code></td></tr>
                        <tr><td><code>source</code></td><td>Source slug or <code>all</code></td></tr>
                        <tr><td><code>columns</code></td><td>1, 2, 3, or 4</td></tr>
                        <tr><td><code>max_items</code></td><td>Number or <code>-1</code> for unlimited</td></tr>
                        <tr><td><code>show_avatar</code></td><td><code>yes</code> or <code>no</code></td></tr>
                    </table>
                </div>
            </div>

        </div>
    </div>
    <?php
}
