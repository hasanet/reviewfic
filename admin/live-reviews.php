<?php
/**
 * Reviewfic — Live Reviews
 * Shortcodes: [reviewfic_google] and [reviewfic_yelp]
 *
 * Settings stored in:
 *   reviewfic_google_api_key
 *   reviewfic_yelp_api_key
 *
 * Responses cached as transients for 12 hours.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

// ── Admin page ─────────────────────────────────────────────────────────────

add_action( 'admin_menu', 'rwf_live_reviews_menu' );

function rwf_live_reviews_menu() {
    add_submenu_page(
        'edit.php?post_type=reviewfic_reviews',
        __( 'Live Reviews', 'reviewfic' ),
        __( 'Live Reviews', 'reviewfic' ),
        'manage_options',
        'reviewfic-live-reviews',
        'rwf_live_reviews_page'
    );
}

// Save API keys
add_action( 'admin_post_rwf_save_api_keys', function () {
    if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );
    check_admin_referer( 'rwf_save_api_keys' );

    update_option( 'reviewfic_google_api_key', sanitize_text_field( wp_unslash( $_POST['rwf_google_key'] ?? '' ) ) );
    update_option( 'reviewfic_yelp_api_key',   sanitize_text_field( wp_unslash( $_POST['rwf_yelp_key']   ?? '' ) ) );

    rwf_clear_live_review_cache();

    wp_safe_redirect( add_query_arg( array(
        'post_type' => 'reviewfic_reviews',
        'page'      => 'reviewfic-live-reviews',
        'saved'     => '1',
    ), admin_url( 'edit.php' ) ) );
    exit;
} );

// Standalone "Clear Cache" button — doesn't require re-saving API keys,
// and covers Google, Yelp, AND WordPress.org (which has no key to re-save).
add_action( 'admin_post_rwf_clear_live_cache', function () {
    if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );
    check_admin_referer( 'rwf_clear_live_cache' );

    $cleared = rwf_clear_live_review_cache();

    wp_safe_redirect( add_query_arg( array(
        'post_type'     => 'reviewfic_reviews',
        'page'          => 'reviewfic-live-reviews',
        'cache_cleared' => $cleared,
    ), admin_url( 'edit.php' ) ) );
    exit;
} );

/**
 * Clear every cached live-review response — Google, Yelp, and WordPress.org.
 * Returns the number of transient rows deleted.
 */
function rwf_clear_live_review_cache() {
    global $wpdb;
    return (int) $wpdb->query(
        "DELETE FROM {$wpdb->options}
         WHERE option_name LIKE '\_transient\_reviewfic\_google\_%'
            OR option_name LIKE '\_transient\_reviewfic\_yelp\_%'
            OR option_name LIKE '\_transient\_reviewfic\_wporg\_%'
            OR option_name LIKE '\_transient\_timeout\_reviewfic\_google\_%'
            OR option_name LIKE '\_transient\_timeout\_reviewfic\_yelp\_%'
            OR option_name LIKE '\_transient\_timeout\_reviewfic\_wporg\_%'"
    );
}

function rwf_live_reviews_page() {
    $google_key   = get_option( 'reviewfic_google_api_key', '' );
    $yelp_key     = get_option( 'reviewfic_yelp_api_key',   '' );
    $saved        = ! empty( $_GET['saved'] );
    $cache_cleared = isset( $_GET['cache_cleared'] ) ? intval( $_GET['cache_cleared'] ) : null;
    ?>
    <div class="wrap rwf-ie-wrap">
        <h1 class="wp-heading-inline"><?php esc_html_e( 'Live Reviews', 'reviewfic' ); ?></h1>
        <hr class="wp-header-end">

        <?php if ( $saved ) : ?>
            <div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'API keys saved.', 'reviewfic' ); ?></p></div>
        <?php endif; ?>

        <?php if ( $cache_cleared !== null ) : ?>
            <div class="notice notice-success is-dismissible"><p>
                <?php echo esc_html( sprintf(
                    /* translators: %d: number of cached responses cleared */
                    _n( 'Live review cache cleared (%d cached response removed). Fresh data will be fetched on next page load.', 'Live review cache cleared (%d cached responses removed). Fresh data will be fetched on next page load.', $cache_cleared, 'reviewfic' ),
                    $cache_cleared
                ) ); ?>
            </p></div>
        <?php endif; ?>

        <div class="rwf-live-grid">

            <!-- API Keys -->
            <div class="rwf-ie-card">
                <div class="rwf-ie-card-header">
                    <span class="dashicons dashicons-admin-network rwf-ie-card-icon"></span>
                    <div>
                        <h2><?php esc_html_e( 'API Keys', 'reviewfic' ); ?></h2>
                        <p><?php esc_html_e( 'Enter your API credentials to enable live review easily.', 'reviewfic' ); ?></p>
                    </div>
                </div>

                <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                    <?php wp_nonce_field( 'rwf_save_api_keys' ); ?>
                    <input type="hidden" name="action" value="rwf_save_api_keys">

                    <div class="rwf-live-field">
                        <label>
                            <span class="rwf-live-platform-badge rwf-badge-google">Google</span>
                            <?php esc_html_e( 'Places API Key', 'reviewfic' ); ?>
                        </label>
                        <input type="password" name="rwf_google_key" value="<?php echo esc_attr( $google_key ); ?>" class="regular-text" autocomplete="off" placeholder="AIza...">
                        <p class="description">
                            <a href="https://developers.google.com/maps/documentation/places/web-service/get-api-key" target="_blank" rel="noopener">
                                <?php esc_html_e( 'Get a Google Places API key →', 'reviewfic' ); ?>
                            </a>
                        </p>
                    </div>

                    <div class="rwf-live-field">
                        <label>
                            <span class="rwf-live-platform-badge rwf-badge-yelp">Yelp</span>
                            <?php esc_html_e( 'Fusion API Key', 'reviewfic' ); ?>
                        </label>
                        <input type="password" name="rwf_yelp_key" value="<?php echo esc_attr( $yelp_key ); ?>" class="regular-text" autocomplete="off" placeholder="Your Yelp API key...">
                        <p class="description">
                            <a href="https://www.yelp.com/developers/documentation/v3/authentication" target="_blank" rel="noopener">
                                <?php esc_html_e( 'Get a Yelp Fusion API key →', 'reviewfic' ); ?>
                            </a>
                        </p>
                    </div>

                    <button type="submit" class="button button-primary rwf-ie-btn">
                        <?php esc_html_e( 'Save API Keys', 'reviewfic' ); ?>
                    </button>
                </form>

                <hr style="margin:20px 0;border-color:#f0f0f0;">

                <p class="description" style="margin-bottom:10px;">
                    <?php esc_html_e( 'Google, Yelp, and WordPress.org responses are cached for 12 hours. If you\'ve just fixed a Place ID, business listing, or plugin slug — or updated the plugin itself — clear the cache to fetch fresh data immediately instead of waiting.', 'reviewfic' ); ?>
                </p>
                <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                    <?php wp_nonce_field( 'rwf_clear_live_cache' ); ?>
                    <input type="hidden" name="action" value="rwf_clear_live_cache">
                    <button type="submit" class="button rwf-ie-btn">
                        <span class="dashicons dashicons-update"></span>
                        <?php esc_html_e( 'Clear Live Review Cache', 'reviewfic' ); ?>
                    </button>
                </form>
            </div>

            <!-- Shortcode Reference -->
            <div class="rwf-ie-card">
                <div class="rwf-ie-card-header">
                    <span class="dashicons dashicons-shortcode rwf-ie-card-icon"></span>
                    <div>
                        <h2><?php esc_html_e( 'Shortcode Reference', 'reviewfic' ); ?></h2>
                        <p><?php esc_html_e( 'Embed live reviews on any page or post.', 'reviewfic' ); ?></p>
                    </div>
                </div>

                <div class="rwf-live-shortcode-block">
                    <div class="rwf-live-platform-badge rwf-badge-google" style="margin-bottom:8px;">Google</div>
                    <code class="rwf-live-code">[reviewfic_google place_id="ChIJ..." id="12" max="5"]</code>
                    <table class="rwf-live-attr-table">
                        <tr><td><code>place_id</code></td><td><?php esc_html_e( 'Required. Google Place ID.', 'reviewfic' ); ?> <a href="https://developers.google.com/maps/documentation/javascript/examples/places-placeid-finder" target="_blank" rel="noopener"><?php esc_html_e( 'Find it here', 'reviewfic' ); ?> ↗</a></td></tr>
                        <tr><td><code>max</code></td><td><?php esc_html_e( '1–5 (Google returns up to 5 reviews). Default: 5', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>id</code></td><td><?php esc_html_e( 'Optional. ID of a saved Shortcode Config — when set, it controls template, columns, slider (with all sub-options), pagination, and design colours, overriding any of the attributes below.', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>columns</code></td><td><?php esc_html_e( '1–4. Default: 3', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>template</code></td><td><?php esc_html_e( '1–10. Default: 1', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>slider</code></td><td><?php esc_html_e( 'yes / no. Default: no — see Slider & Pagination below for sub-options.', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>show_avatar</code></td><td><?php esc_html_e( 'yes / no. Default: yes', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>show_source</code></td><td><?php esc_html_e( 'yes / no. Default: yes.', 'reviewfic' ); ?> <strong><?php esc_html_e( 'Keep this on — Google requires attribution to stay visible when displaying its content.', 'reviewfic' ); ?></strong></td></tr>
                    </table>
                </div>

                <div class="rwf-live-shortcode-block" style="margin-top:20px;">
                    <div class="rwf-live-platform-badge rwf-badge-yelp" style="margin-bottom:8px;">Yelp</div>
                    <code class="rwf-live-code">[reviewfic_yelp business_id="..." id="12" max="3"]</code>
                    <table class="rwf-live-attr-table">
                        <tr><td><code>business_id</code></td><td><?php esc_html_e( 'Required. Yelp Business ID or alias (e.g. gary-danko-san-francisco).', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>max</code></td><td><?php esc_html_e( '1–3 (Yelp free tier limit). Default: 3', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>id</code></td><td><?php esc_html_e( 'Optional. ID of a saved Shortcode Config — same as Google, overrides the attributes below.', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>columns</code></td><td><?php esc_html_e( '1–4. Default: 3', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>template</code></td><td><?php esc_html_e( '1–10. Default: 1', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>slider</code></td><td><?php esc_html_e( 'yes / no. Default: no', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>show_avatar</code></td><td><?php esc_html_e( 'yes / no. Default: yes', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>show_source</code></td><td><?php esc_html_e( 'yes / no. Default: yes.', 'reviewfic' ); ?> <strong><?php esc_html_e( 'Keep this on — Yelp\'s Display Requirements mandate Yelp attribution stay visible when showing Yelp content.', 'reviewfic' ); ?></strong></td></tr>
                    </table>
                </div>

                <div class="rwf-live-shortcode-block" style="margin-top:20px;">
                    <div class="rwf-live-platform-badge" style="background:#96588a;margin-bottom:8px;">WooCommerce</div>
                    <code class="rwf-live-code">[reviewfic_woocommerce product_id="123" id="12" max="10"]</code>
                    <table class="rwf-live-attr-table">
                        <tr><td><code>product_id</code></td><td><?php esc_html_e( 'Required. WooCommerce product ID. Display existing product reviews anywhere on the site.', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>max</code></td><td><?php esc_html_e( 'Maximum reviews to show. Default: 10', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>id</code></td><td><?php esc_html_e( 'Optional. ID of a saved Shortcode Config — same as Google, overrides the attributes below.', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>columns</code></td><td><?php esc_html_e( '1–4. Default: 3', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>template</code></td><td><?php esc_html_e( '1–10. Default: 1', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>slider</code></td><td><?php esc_html_e( 'yes / no. Default: no', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>show_avatar</code></td><td><?php esc_html_e( 'yes / no. Default: yes', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>show_source</code></td><td><?php esc_html_e( 'yes / no. Default: yes. Safe to disable — these are your own site\'s reviews, no third-party attribution applies.', 'reviewfic' ); ?></td></tr>
                    </table>
                    <?php if ( ! class_exists( 'WooCommerce' ) ) : ?>
                        <p class="description" style="color:#b32d2e;margin-top:8px;"><?php esc_html_e( 'WooCommerce is not active — this shortcode will show an error until it is installed.', 'reviewfic' ); ?></p>
                    <?php endif; ?>
                </div>

                <div class="rwf-live-shortcode-block" style="margin-top:20px;">
                    <div class="rwf-live-platform-badge" style="background:#21759b;margin-bottom:8px;">WordPress.org</div>
                    <code class="rwf-live-code">[reviewfic_wporg plugin="reviewfic" id="12" max="5"]</code>
                    <table class="rwf-live-attr-table">
                        <tr><td><code>plugin</code></td><td><?php esc_html_e( 'Required. The plugin slug as it appears on WordPress.org (e.g. "contact-form-7"). No API key needed.', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>max</code></td><td><?php esc_html_e( 'Maximum reviews to show. Default: 5', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>id</code></td><td><?php esc_html_e( 'Optional. ID of a saved Shortcode Config — same as Google, overrides the attributes below.', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>columns</code></td><td><?php esc_html_e( '1–4. Default: 3', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>template</code></td><td><?php esc_html_e( '1–10. Default: 1', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>slider</code></td><td><?php esc_html_e( 'yes / no. Default: no', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>show_avatar</code></td><td><?php esc_html_e( 'yes / no. Default: yes', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>show_source</code></td><td><?php esc_html_e( 'yes / no. Default: yes. Safe to disable — WordPress.org has no attribution requirement for review excerpts.', 'reviewfic' ); ?></td></tr>
                    </table>
                </div>

                <div class="rwf-live-shortcode-block" style="margin-top:20px;">
                    <div class="rwf-live-platform-badge" style="background:#374151;margin-bottom:8px;"><?php esc_html_e( 'Slider & Pagination (all four shortcodes)', 'reviewfic' ); ?></div>
                    <p class="description" style="margin-bottom:10px;">
                        <?php esc_html_e( 'Every live review shortcode above supports the exact same slider and pagination attributes as the regular [reviewfic] shortcode. These work standalone, or are fully overridden if an id (saved config) is supplied.', 'reviewfic' ); ?>
                    </p>
                    <table class="rwf-live-attr-table">
                        <tr><td><code>slider_nav</code></td><td><?php esc_html_e( 'yes / no — show prev/next arrows. Default: yes', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>slider_dots</code></td><td><?php esc_html_e( 'yes / no — show navigation dots. Default: yes', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>slider_auto</code></td><td><?php esc_html_e( 'yes / no — autoplay. Default: no', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>slider_speed</code></td><td><?php esc_html_e( 'Autoplay interval in milliseconds. Default: 4000', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>slider_loop</code></td><td><?php esc_html_e( 'yes / no — loop back to the start. Default: yes', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>slider_pause</code></td><td><?php esc_html_e( 'yes / no — pause autoplay on hover. Default: yes', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>pagination</code></td><td><?php esc_html_e( 'yes / no — paginate results instead of showing them all at once. Ignored when slider="yes". Default: no', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>per_page</code></td><td><?php esc_html_e( 'Items per page when pagination is enabled. Default: 6', 'reviewfic' ); ?></td></tr>
                    </table>
                </div>

                <div class="rwf-live-notice">
                    <span class="dashicons dashicons-clock"></span>
                    <?php esc_html_e( 'Live reviews are cached for 12 hours to avoid unnecessary API calls.', 'reviewfic' ); ?>
                </div>
            </div>
        </div>
    </div>
    <?php
}


// ── Shared render helper ───────────────────────────────────────────────────

/**
 * Shared template name map used by WC, Tourfic, and any future
 * integration settings pages to render the template <select>.
 */
function rwf_template_names() {
    return array(
        '1'  => __( '1 – Classic',    'reviewfic' ),
        '2'  => __( '2 – Quote',      'reviewfic' ),
        '3'  => __( '3 – Minimal',    'reviewfic' ),
        '4'  => __( '4 – Dark',       'reviewfic' ),
        '5'  => __( '5 – Centered',   'reviewfic' ),
        '6'  => __( '6 – Split',      'reviewfic' ),
        '7'  => __( '7 – Glow',       'reviewfic' ),
        '8'  => __( '8 – Score',      'reviewfic' ),
        '9'  => __( '9 – Magazine',   'reviewfic' ),
        '10' => __( '10 – Neon Dark', 'reviewfic' ),
    );
}

/**
 * Resolve display settings for any live-review shortcode (Google, Yelp,
 * WooCommerce on-site, WordPress.org) from an optional saved Shortcode
 * Config post — exact same mechanism as the regular [reviewfic id="X"]
 * shortcode. This gives every live integration full parity: all 10
 * templates, every slider sub-option, pagination, and the full design
 * colour/shadow/radius/gap system — without duplicating that logic.
 *
 * If 'id' is absent/0/invalid, $atts is returned unchanged except for
 * an added empty 'design_style' key, so existing direct-attribute usage
 * (template="2" columns="3" slider="yes" etc.) keeps working exactly
 * as before.
 */
function rwf_resolve_live_display_atts( $atts ) {
    $atts['design_style'] = '';

    $config_id = intval( $atts['id'] ?? 0 );
    if ( $config_id <= 0 ) {
        return $atts;
    }

    $config = get_post( $config_id );
    if ( ! $config || $config->post_type !== 'reviewfic_config' || $config->post_status !== 'publish' ) {
        return $atts;
    }

    $get = function ( $key, $fallback ) use ( $config_id ) {
        $v = get_post_meta( $config_id, $key, true );
        return ( $v !== '' && $v !== false ) ? $v : $fallback;
    };

    $atts['template']     = $get( 'rwf_template',     $atts['template']     ?? '1' );
    $atts['columns']      = $get( 'rwf_columns',      $atts['columns']      ?? 3 );
    $atts['show_avatar']  = $get( 'rwf_show_avatar',  $atts['show_avatar']  ?? 'yes' );
    $atts['show_source']  = $get( 'rwf_show_source',  $atts['show_source']  ?? 'yes' );
    $atts['slider']       = $get( 'rwf_slider',       $atts['slider']       ?? 'no' );
    $atts['slider_nav']   = $get( 'rwf_slider_nav',   $atts['slider_nav']   ?? 'yes' );
    $atts['slider_dots']  = $get( 'rwf_slider_dots',  $atts['slider_dots']  ?? 'yes' );
    $atts['slider_auto']  = $get( 'rwf_slider_auto',  $atts['slider_auto']  ?? 'no' );
    $atts['slider_speed'] = $get( 'rwf_slider_speed', $atts['slider_speed'] ?? '4000' );
    $atts['slider_loop']  = $get( 'rwf_slider_loop',  $atts['slider_loop']  ?? 'yes' );
    $atts['slider_pause'] = $get( 'rwf_slider_pause', $atts['slider_pause'] ?? 'yes' );
    $atts['pagination']   = $get( 'rwf_pagination',   $atts['pagination']   ?? 'no' );
    $atts['per_page']     = $get( 'rwf_per_page',     $atts['per_page']     ?? 6 );

    // ── Design CSS variables — identical mapping to admin/shortcode.php ──
    $shadow_map = array(
        'sm' => '0 1px 4px rgba(0,0,0,.08)',
        'md' => '0 4px 16px rgba(0,0,0,.12)',
        'lg' => '0 8px 32px rgba(0,0,0,.18)',
    );

    $card_bg      = get_post_meta( $config_id, 'rwf_card_bg', true );
    $text_color   = get_post_meta( $config_id, 'rwf_text_color', true );
    $star_color   = get_post_meta( $config_id, 'rwf_star_color', true );
    $accent_color = get_post_meta( $config_id, 'rwf_accent_color', true );
    $meta_color   = get_post_meta( $config_id, 'rwf_meta_color', true );
    $name_color   = get_post_meta( $config_id, 'rwf_name_color', true );
    $card_border  = get_post_meta( $config_id, 'rwf_card_border', true );
    $card_shadow  = get_post_meta( $config_id, 'rwf_card_shadow', true );
    $card_radius  = get_post_meta( $config_id, 'rwf_card_radius', true );
    $col_gap      = get_post_meta( $config_id, 'rwf_col_gap', true );

    $vars = array();
    if ( $card_bg )            $vars[] = '--rwf-card-bg:'      . esc_attr( $card_bg );
    if ( $text_color )         $vars[] = '--rwf-text-color:'   . esc_attr( $text_color );
    if ( $star_color )         $vars[] = '--rwf-star-color:'   . esc_attr( $star_color );
    if ( $accent_color )       $vars[] = '--rwf-accent-color:' . esc_attr( $accent_color );
    if ( $meta_color )         $vars[] = '--rwf-meta-color:'   . esc_attr( $meta_color );
    if ( $name_color )         $vars[] = '--rwf-name-color:'   . esc_attr( $name_color );
    if ( $card_border )        $vars[] = '--rwf-card-border:'  . esc_attr( $card_border );
    if ( ! empty( $shadow_map[ $card_shadow ] ) ) $vars[] = '--rwf-card-shadow:' . $shadow_map[ $card_shadow ];
    if ( $card_radius !== '' ) $vars[] = '--rwf-card-radius:' . intval( $card_radius ) . 'px';
    if ( $col_gap     !== '' ) $vars[] = '--rwf-col-gap:'     . intval( $col_gap )      . 'px';

    if ( $vars ) $atts['design_style'] = ' style="' . implode( ';', $vars ) . '"';

    return $atts;
}

function rwf_render_live_cards( $reviews, $atts, $source_slug, $source_name ) {
    static $slider_id = 1000; // offset to avoid collision with CPT slider IDs

    $valid_templates = array( '1','2','3','4','5','6','7','8','9','10' );
    $template      = in_array( $atts['template'], $valid_templates, true ) ? $atts['template'] : '1';
    $use_slider    = ( $atts['slider'] ?? 'no' ) === 'yes';
    $use_pagination= ! $use_slider && ( $atts['pagination'] ?? 'no' ) === 'yes';
    $per_page      = max( 1, intval( $atts['per_page'] ?? 6 ) );
    $design_style  = $atts['design_style'] ?? '';
    $show_avatar   = $atts['show_avatar'] !== 'no';
    $columns       = max( 1, min( 4, intval( $atts['columns'] ) ) );
    $known_sources = array('google','trustpilot','g2','capterra','facebook','yelp','amazon');

    // ── Pagination: slice the already-fetched array (no DB query here) ──
    $total_items = count( $reviews );
    $total_pages = 1;
    $current_page = 1;
    if ( $use_pagination ) {
        $page_key     = 'rwf_lp_' . abs( crc32( $source_slug . serialize( $atts ) ) );
        $current_page = max( 1, intval( $_GET[ $page_key ] ?? 1 ) );
        $total_pages  = $total_items > 0 ? (int) ceil( $total_items / $per_page ) : 1;
        $reviews      = array_slice( $reviews, ( $current_page - 1 ) * $per_page, $per_page );
    }

    $star_path = plugin_dir_path( __FILE__ ) . 'assets/img/';

    $get_star_svg = function ( $type ) use ( $star_path ) {
        $map  = array('full' => 'star-solid.svg', 'half' => 'star-half-stroke-solid.svg', 'empty' => 'star-regular.svg');
        $file = $star_path . ( $map[ $type ] ?? 'star-regular.svg' );
        return file_exists( $file ) ? file_get_contents( $file ) : '★';
    };

    $show_source  = ( $atts['show_source'] ?? 'yes' ) !== 'no';

    $source_class = in_array( $source_slug, $known_sources, true )
                    ? 'reviewfic-source-' . $source_slug
                    : 'reviewfic-source-custom';
    $badge_markup = $show_source
        ? '<span class="reviewfic-source-badge ' . esc_attr( $source_class ) . '">' . esc_html( $source_name ) . '</span>'
        : '';

    ob_start();

    if ( $use_slider ) {
        $slider_id++;
        echo '<div class="reviewfic-slider" id="reviewfic-slider-' . $slider_id . '"'
            . ' data-nav="'   . ( ( $atts['slider_nav']   ?? 'yes' ) === 'yes' ? 'yes' : 'no' ) . '"'
            . ' data-dots="'  . ( ( $atts['slider_dots']  ?? 'yes' ) === 'yes' ? 'yes' : 'no' ) . '"'
            . ' data-auto="'  . ( ( $atts['slider_auto']  ?? 'no'  ) === 'yes' ? 'yes' : 'no' ) . '"'
            . ' data-speed="' . intval( $atts['slider_speed'] ?? 4000 ) . '"'
            . ' data-loop="'  . ( ( $atts['slider_loop']  ?? 'yes' ) === 'yes' ? 'yes' : 'no' ) . '"'
            . ' data-pause="' . ( ( $atts['slider_pause'] ?? 'yes' ) === 'yes' ? 'yes' : 'no' ) . '"'
            . ' data-columns="' . esc_attr( $columns ) . '"'
            . $design_style . '>';
        echo '<div class="reviewfic-slider-track">';
    } else {
        echo '<div class="reviewfic-columns reviewfic-columns-' . esc_attr( $columns ) . '"' . $design_style . '>';
    }

    foreach ( $reviews as $r ) {
        $stars      = floatval( $r['stars'] );
        $whole      = floor( $stars );
        $dec        = $stars - $whole;
        $star_html  = '';
        for ( $i = 1; $i <= 5; $i++ ) {
            if ( $i <= $whole )                           $star_html .= $get_star_svg('full');
            elseif ( $i == $whole + 1 && $dec >= 0.5 )   $star_html .= $get_star_svg('half');
            else                                          $star_html .= $get_star_svg('empty');
        }
        $stars_markup = '<p class="reviewfic-stars">' . $star_html . '<span class="reviewfic-star-score">(' . esc_html( $stars ) . ')</span></p>';

        // Avatar — real photo if available, otherwise a generated initials badge
        $avatar_markup = '';
        if ( $show_avatar ) {
            if ( ! empty( $r['avatar'] ) ) {
                $avatar_markup = '<div class="reviewfic-avatar">'
                    . '<img src="' . esc_url( $r['avatar'] ) . '" alt="' . esc_attr( $r['name'] ) . '" class="reviewfic-avatar-img" loading="lazy">'
                    . '</div>';
            } else {
                $avatar_markup = rwf_initials_avatar_markup( $r['name'] );
            }
        }

        // Client meta
        $meta_parts  = array_filter( array( $r['meta'] ?? '' ) );
        if ( ! empty( $r['time'] ) ) $meta_parts[] = $r['time'];
        $client_meta = ! empty( $meta_parts )
            ? '<span class="reviewfic-client-meta">' . esc_html( implode( ' · ', $meta_parts ) ) . '</span>'
            : '';

        $client_markup = '<div class="reviewfic-client-row">'
            . $avatar_markup
            . '<div class="reviewfic-client-info">'
            . '<span class="reviewfic-client-name">' . esc_html( $r['name'] ) . '</span>'
            . $client_meta
            . '</div></div>';

        $content_html = '<div class="reviewfic-content">' . wp_kses_post( $r['content'] ) . '</div>';
        $title_html   = ! empty( $r['title'] ) ? '<h3 class="reviewfic-title">' . esc_html( $r['title'] ) . '</h3>' : '';

        echo '<div class="reviewfic-item reviewfic-template-' . esc_attr( $template ) . '">';
        switch ( $template ) {
            case '2':
                echo $client_markup . '<div class="reviewfic-content reviewfic-quote">' . wp_kses_post( $r['content'] ) . '</div>' . $stars_markup . $badge_markup;
                break;
            case '3':
                echo $stars_markup . $content_html . $badge_markup . $client_markup;
                break;
            case '4':
                echo $badge_markup . $stars_markup . $title_html . $content_html . $client_markup;
                break;
            case '5':
                echo $client_markup . $stars_markup . '<div class="reviewfic-content reviewfic-quote">' . wp_kses_post( $r['content'] ) . '</div>' . $badge_markup;
                break;
            case '6': // Horizontal Split
                $t6_img = ( $show_avatar && ! empty( $r['avatar'] ) )
                    ? '<img src="' . esc_url( $r['avatar'] ) . '" alt="' . esc_attr( $r['name'] ) . '" class="rwf-t6-photo" loading="lazy">'
                    : '';
                echo '<div class="rwf-t6-left">';
                echo $t6_img ?: '<div class="rwf-t6-initials">' . esc_html( mb_substr( $r['name'], 0, 1 ) ) . '</div>';
                echo '<span class="rwf-t6-name">' . esc_html( $r['name'] ) . '</span>';
                if ( $client_meta ) echo $client_meta;
                echo $badge_markup . '</div>';
                echo '<div class="rwf-t6-right">' . $stars_markup . $title_html . $content_html . '</div>';
                break;
            case '7': // Gradient Glow
                echo '<div class="rwf-t7-top">' . $stars_markup . $badge_markup . '</div>';
                echo '<div class="rwf-t7-quote-mark">&ldquo;</div>';
                echo $content_html;
                echo '<div class="rwf-t7-footer">' . $avatar_markup
                    . '<div class="rwf-t7-client"><span class="reviewfic-client-name">' . esc_html( $r['name'] ) . '</span>'
                    . $client_meta . '</div></div>';
                break;
            case '8': // Score Card
                echo '<div class="rwf-t8-score-bubble">' . esc_html( number_format( $stars, 1 ) ) . '</div>';
                echo $badge_markup . $stars_markup . $title_html . $content_html . $client_markup;
                break;
            case '9': // Magazine / Pull Quote
                echo '<div class="rwf-t9-quote-mark" aria-hidden="true">&ldquo;</div>';
                echo '<div class="reviewfic-content rwf-t9-content">' . wp_kses_post( $r['content'] ) . '</div>';
                echo '<div class="rwf-t9-byline"><div class="rwf-t9-author">' . $avatar_markup
                    . '<div><span class="reviewfic-client-name">' . esc_html( $r['name'] ) . '</span>' . $client_meta . '</div>'
                    . '</div><div class="rwf-t9-right">' . $stars_markup . $badge_markup . '</div></div>';
                break;
            case '10': // Neon Dark Gradient
                echo '<div class="rwf-t10-header">' . $avatar_markup
                    . '<div class="rwf-t10-client"><span class="rwf-t10-name">' . esc_html( $r['name'] ) . '</span>'
                    . $client_meta . '</div></div>';
                echo $content_html;
                echo '<div class="rwf-t10-footer">' . $stars_markup . $badge_markup . '</div>';
                break;
            default:
                echo $badge_markup . $stars_markup . $title_html . $content_html . $client_markup;
        }
        echo '</div>';
    }

    if ( $use_slider ) {
        echo '</div>';
        echo '<div class="reviewfic-slider-nav">';
        echo '<button class="reviewfic-slider-prev" aria-label="Previous">&#8249;</button>';
        echo '<div class="reviewfic-slider-dots"></div>';
        echo '<button class="reviewfic-slider-next" aria-label="Next">&#8250;</button>';
        echo '</div></div>';
    } else {
        echo '</div>';
    }

    // ── Pagination ────────────────────────────────────────────
    if ( $use_pagination && $total_pages > 1 ) {
        echo '<div class="reviewfic-pagination">';

        if ( $current_page > 1 ) {
            echo '<a href="' . esc_url( add_query_arg( $page_key, $current_page - 1 ) ) . '" class="rwf-page-btn rwf-page-prev">&laquo;</a>';
        }

        $range = 2;
        for ( $p = 1; $p <= $total_pages; $p++ ) {
            if ( $p === 1 || $p === $total_pages || ( $p >= $current_page - $range && $p <= $current_page + $range ) ) {
                $active = $p === $current_page ? ' active' : '';
                echo '<a href="' . esc_url( add_query_arg( $page_key, $p ) ) . '" class="rwf-page-btn' . $active . '">' . $p . '</a>';
            } elseif ( $p === $current_page - $range - 1 || $p === $current_page + $range + 1 ) {
                echo '<span class="rwf-page-ellipsis">&hellip;</span>';
            }
        }

        if ( $current_page < $total_pages ) {
            echo '<a href="' . esc_url( add_query_arg( $page_key, $current_page + 1 ) ) . '" class="rwf-page-btn rwf-page-next">&raquo;</a>';
        }

        echo '</div>';
    }

    return ob_get_clean();
}

/**
 * Generate a colored "initials" avatar badge for reviewers with no real
 * photo URL — used whenever a live source doesn't expose one (currently
 * WordPress.org reviews; future no-avatar sources get this automatically).
 * The background color is deterministic per name so the same reviewer
 * always gets the same color across page loads.
 */
function rwf_initials_avatar_markup( $name ) {
    $palette = array( '#0E9F6E', '#2563EB', '#DB2777', '#D97706', '#7C3AED', '#DC2626', '#0891B2', '#65A30D' );
    $name    = trim( (string) $name );
    $initial = $name !== '' ? mb_strtoupper( mb_substr( $name, 0, 1 ) ) : '?';
    $color   = $palette[ abs( crc32( $name ) ) % count( $palette ) ];

    return '<div class="reviewfic-avatar reviewfic-avatar-initial" style="background:' . esc_attr( $color ) . ';">'
        . esc_html( $initial )
        . '</div>';
}

function rwf_live_error( $msg ) {
    return '<p class="rwf-live-error">' . esc_html( $msg ) . '</p>';
}


// ══════════════════════════════════════════════════════════════════════════
//  GOOGLE PLACES SHORTCODE
// ══════════════════════════════════════════════════════════════════════════

add_shortcode( 'reviewfic_google', 'rwf_google_shortcode' );

function rwf_google_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'id'           => 0,
        'place_id'     => '',
        'max'          => 5,
        'columns'      => 3,
        'template'     => '1',
        'show_avatar'  => 'yes',
        'show_source'  => 'yes',
        'slider'       => 'no',
        'slider_nav'   => 'yes',
        'slider_dots'  => 'yes',
        'slider_auto'  => 'no',
        'slider_speed' => '4000',
        'slider_loop'  => 'yes',
        'slider_pause' => 'yes',
        'pagination'   => 'no',
        'per_page'     => 6,
    ), $atts, 'reviewfic_google' );
    $atts = rwf_resolve_live_display_atts( $atts );

    $place_id = sanitize_text_field( $atts['place_id'] );
    if ( empty( $place_id ) ) {
        return rwf_live_error( __( '[reviewfic_google] requires a place_id attribute.', 'reviewfic' ) );
    }

    $api_key = get_option( 'reviewfic_google_api_key', '' );
    if ( empty( $api_key ) ) {
        return rwf_live_error( __( 'Google Places API key is not configured. Go to Reviewfic → Live Reviews to add it.', 'reviewfic' ) );
    }

    $max = max( 1, min( 5, intval( $atts['max'] ) ) );

    // ── Fetch / cache ──────────────────────────────────────────────
    $cache_key = 'reviewfic_google_' . md5( $place_id );
    $cached    = get_transient( $cache_key );

    if ( $cached === false ) {
        $url = add_query_arg( array(
            'place_id' => $place_id,
            'fields'   => 'reviews',
            'key'      => $api_key,
            'reviews_sort' => 'newest',
            'language' => get_locale(),
        ), 'https://maps.googleapis.com/maps/api/place/details/json' );

        $response = wp_remote_get( $url, array( 'timeout' => 10 ) );

        if ( is_wp_error( $response ) ) {
            return rwf_live_error( __( 'Could not connect to Google Places API.', 'reviewfic' ) );
        }

        $body = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( empty( $body['result']['reviews'] ) ) {
            $status        = $body['status'] ?? 'UNKNOWN';
            $error_message = $body['error_message'] ?? '';

            switch ( $status ) {
                case 'REQUEST_DENIED':
                    $msg = __( 'Google API key is invalid, missing the Places API permission, or restricted in a way that blocks server-side requests (e.g. an "HTTP referrer" restriction — use "IP addresses" or "None" instead since this request comes from your server, not a browser).', 'reviewfic' );
                    break;
                case 'OVER_QUERY_LIMIT':
                    $msg = __( 'Google API quota exceeded, or billing is not enabled on this Google Cloud project. The Places API requires an active billing account even within the free usage tier.', 'reviewfic' );
                    break;
                case 'INVALID_REQUEST':
                case 'NOT_FOUND':
                    $msg = __( 'This Place ID was not recognised by Google. Copy it from Google\'s Place ID Finder tool — it should look like "ChIJ..." and not be a Maps URL, CID, or business name.', 'reviewfic' );
                    break;
                case 'OK':
                    // The call succeeded, but Google returned zero reviews for this place.
                    $msg = __( 'Google did not return any reviews for this place. This is a known Google API limitation: Place Details only returns up to 5 "most relevant" reviews chosen by Google\'s own algorithm, and for some places it returns none even though reviews are visible on Google Maps.', 'reviewfic' );
                    break;
                default:
                    $msg = __( 'No reviews found for this Place ID.', 'reviewfic' );
            }

            if ( current_user_can( 'manage_options' ) ) {
                $msg .= ' ' . sprintf(
                    /* translators: 1: Google API status code, 2: optional error message from Google */
                    __( '(Admin debug — Google API status: %1$s%2$s)', 'reviewfic' ),
                    $status,
                    $error_message ? ' — ' . $error_message : ''
                );
            }

            return rwf_live_error( $msg );
        }

        $cached = $body['result']['reviews'];
        set_transient( $cache_key, $cached, 12 * HOUR_IN_SECONDS );
    }

    // ── Normalise ──────────────────────────────────────────────────
    $reviews = array();
    foreach ( array_slice( $cached, 0, $max ) as $r ) {
        $reviews[] = array(
            'title'   => '',
            'content' => $r['text'] ?? '',
            'stars'   => floatval( $r['rating'] ?? 5 ),
            'name'    => $r['author_name'] ?? __( 'Anonymous', 'reviewfic' ),
            'meta'    => '',
            'avatar'  => $r['profile_photo_url'] ?? '',
            'time'    => $r['relative_time_description'] ?? '',
        );
    }

    if ( empty( $reviews ) ) {
        return rwf_live_error( __( 'No reviews to display.', 'reviewfic' ) );
    }

    return rwf_render_live_cards( $reviews, $atts, 'google', 'Google' );
}


// ══════════════════════════════════════════════════════════════════════════
//  YELP FUSION SHORTCODE
// ══════════════════════════════════════════════════════════════════════════

add_shortcode( 'reviewfic_yelp', 'rwf_yelp_shortcode' );

function rwf_yelp_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'id'           => 0,
        'business_id'  => '',
        'max'          => 3,
        'columns'      => 3,
        'template'     => '1',
        'show_avatar'  => 'yes',
        'show_source'  => 'yes',
        'slider'       => 'no',
        'slider_nav'   => 'yes',
        'slider_dots'  => 'yes',
        'slider_auto'  => 'no',
        'slider_speed' => '4000',
        'slider_loop'  => 'yes',
        'slider_pause' => 'yes',
        'pagination'   => 'no',
        'per_page'     => 6,
    ), $atts, 'reviewfic_yelp' );
    $atts = rwf_resolve_live_display_atts( $atts );

    $business_id = sanitize_text_field( $atts['business_id'] );
    if ( empty( $business_id ) ) {
        return rwf_live_error( __( '[reviewfic_yelp] requires a business_id attribute.', 'reviewfic' ) );
    }

    $api_key = get_option( 'reviewfic_yelp_api_key', '' );
    if ( empty( $api_key ) ) {
        return rwf_live_error( __( 'Yelp API key is not configured. Go to Reviewfic → Live Reviews to add it.', 'reviewfic' ) );
    }

    $max = max( 1, min( 3, intval( $atts['max'] ) ) );

    // ── Fetch / cache ──────────────────────────────────────────────
    $cache_key = 'reviewfic_yelp_' . md5( $business_id );
    $cached    = get_transient( $cache_key );

    if ( $cached === false ) {
        $response = wp_remote_get(
            'https://api.yelp.com/v3/businesses/' . rawurlencode( $business_id ) . '/reviews',
            array(
                'timeout' => 10,
                'headers' => array( 'Authorization' => 'Bearer ' . $api_key ),
            )
        );

        if ( is_wp_error( $response ) ) {
            return rwf_live_error( __( 'Could not connect to Yelp API.', 'reviewfic' ) );
        }

        $body = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( ! empty( $body['error'] ) ) {
            $code = $body['error']['code'] ?? 'UNKNOWN';
            return rwf_live_error(
                $code === 'TOKEN_INVALID'
                    ? __( 'Yelp API key is invalid.', 'reviewfic' )
                    : sprintf( __( 'Yelp API error: %s', 'reviewfic' ), $code )
            );
        }

        if ( empty( $body['reviews'] ) ) {
            return rwf_live_error( __( 'No reviews found for this Yelp business ID.', 'reviewfic' ) );
        }

        $cached = $body['reviews'];
        set_transient( $cache_key, $cached, 12 * HOUR_IN_SECONDS );
    }

    // ── Normalise ──────────────────────────────────────────────────
    $reviews = array();
    foreach ( array_slice( $cached, 0, $max ) as $r ) {
        $reviews[] = array(
            'title'   => '',
            'content' => $r['text'] ?? '',
            'stars'   => floatval( $r['rating'] ?? 5 ),
            'name'    => $r['user']['name'] ?? __( 'Anonymous', 'reviewfic' ),
            'meta'    => '',
            'avatar'  => $r['user']['image_url'] ?? '',
            'time'    => isset( $r['time_created'] ) ? date_i18n( get_option( 'date_format' ), strtotime( $r['time_created'] ) ) : '',
        );
    }

    if ( empty( $reviews ) ) {
        return rwf_live_error( __( 'No reviews to display.', 'reviewfic' ) );
    }

    return rwf_render_live_cards( $reviews, $atts, 'yelp', 'Yelp' );
}


// ══════════════════════════════════════════════════════════════════════════
//  WOOCOMMERCE ON-SITE REVIEWS SHORTCODE
//  Reads native WooCommerce product reviews (wp_comments, type='review')
//  and renders them with any Reviewfic template — no API key needed.
// ══════════════════════════════════════════════════════════════════════════

add_shortcode( 'reviewfic_woocommerce', 'rwf_woocommerce_shortcode' );

function rwf_woocommerce_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'id'           => 0,
        'product_id'   => 0,
        'max'          => 10,
        'columns'      => 3,
        'template'     => '1',
        'show_avatar'  => 'yes',
        'show_source'  => 'yes',
        'slider'       => 'no',
        'slider_nav'   => 'yes',
        'slider_dots'  => 'yes',
        'slider_auto'  => 'no',
        'slider_speed' => '4000',
        'slider_loop'  => 'yes',
        'slider_pause' => 'yes',
        'pagination'   => 'no',
        'per_page'     => 6,
    ), $atts, 'reviewfic_woocommerce' );
    $atts = rwf_resolve_live_display_atts( $atts );

    if ( ! class_exists( 'WooCommerce' ) ) {
        return rwf_live_error( __( 'WooCommerce is not active. Install and activate WooCommerce to use this shortcode.', 'reviewfic' ) );
    }

    $product_id = intval( $atts['product_id'] );
    if ( ! $product_id ) {
        return rwf_live_error( __( '[reviewfic_woocommerce] requires a product_id attribute.', 'reviewfic' ) );
    }

    $max = max( 1, intval( $atts['max'] ) );

    $comments = get_comments( array(
        'post_id' => $product_id,
        'type'    => 'review',
        'status'  => 'approve',
        'order'   => 'DESC',
        'number'  => $max,
    ) );

    if ( empty( $comments ) ) {
        return rwf_live_error( __( 'No reviews found for this product.', 'reviewfic' ) );
    }

    $product_name = get_the_title( $product_id ) ?: get_bloginfo( 'name' );
    $reviews      = array();

    foreach ( $comments as $comment ) {
        $rating    = intval( get_comment_meta( $comment->comment_ID, 'rating', true ) );
        $reviews[] = array(
            'title'   => '',
            'content' => $comment->comment_content,
            'stars'   => $rating ? (float) $rating : 5.0,
            'name'    => $comment->comment_author,
            'meta'    => '',
            'avatar'  => get_avatar_url( $comment->comment_author_email, array( 'size' => 96 ) ),
            'time'    => human_time_diff( strtotime( $comment->comment_date ), current_time( 'timestamp' ) )
                         . ' ' . __( 'ago', 'reviewfic' ),
        );
    }

    return rwf_render_live_cards( $reviews, $atts, 'custom', $product_name );
}


// ══════════════════════════════════════════════════════════════════════════
//  WORDPRESS.ORG PLUGIN REVIEWS SHORTCODE
//  Fetches reviews via the public WP.org RSS feed — no API key needed.
//  Feed: https://wordpress.org/support/plugin/{slug}/reviews/feed/
//  Star ratings are encoded as ★ characters at the start of the item title.
// ══════════════════════════════════════════════════════════════════════════

add_shortcode( 'reviewfic_wporg', 'rwf_wporg_shortcode' );

function rwf_wporg_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'id'           => 0,
        'plugin'       => '',
        'max'          => 5,
        'columns'      => 3,
        'template'     => '1',
        'show_avatar'  => 'yes',
        'show_source'  => 'yes',
        'slider'       => 'no',
        'slider_nav'   => 'yes',
        'slider_dots'  => 'yes',
        'slider_auto'  => 'no',
        'slider_speed' => '4000',
        'slider_loop'  => 'yes',
        'slider_pause' => 'yes',
        'pagination'   => 'no',
        'per_page'     => 6,
    ), $atts, 'reviewfic_wporg' );
    $atts = rwf_resolve_live_display_atts( $atts );

    $slug = sanitize_title( $atts['plugin'] );
    if ( empty( $slug ) ) {
        return rwf_live_error( __( '[reviewfic_wporg] requires a plugin attribute (e.g. plugin="contact-form-7").', 'reviewfic' ) );
    }

    $max       = max( 1, intval( $atts['max'] ) );
    $cache_key = 'reviewfic_wporg_' . md5( $slug );
    $cached    = get_transient( $cache_key );

    if ( $cached === false ) {
        $feed_url = 'https://wordpress.org/support/plugin/' . $slug . '/reviews/feed/';
        $response = wp_remote_get( $feed_url, array( 'timeout' => 10 ) );

        if ( is_wp_error( $response ) ) {
            return rwf_live_error( __( 'Could not connect to WordPress.org.', 'reviewfic' ) );
        }

        $body = wp_remote_retrieve_body( $response );
        if ( empty( $body ) ) {
            return rwf_live_error( __( 'No review data returned from WordPress.org.', 'reviewfic' ) );
        }

        // Suppress XML parse warnings; handle malformed feeds gracefully
        libxml_use_internal_errors( true );
        $xml = simplexml_load_string( $body );
        libxml_clear_errors();

        if ( ! $xml || ! isset( $xml->channel->item ) ) {
            return rwf_live_error(
                sprintf(
                    /* translators: %s: plugin slug */
                    __( 'No reviews found for plugin "%s" on WordPress.org. Check the plugin slug is correct.', 'reviewfic' ),
                    $slug
                )
            );
        }

        $raw = array();
        foreach ( $xml->channel->item as $item ) {
            $raw_title = (string) $item->title;

            // Star rating is encoded as leading ★ characters, e.g. "★★★★★ Excellent plugin"
            preg_match( '/^(★+)/u', $raw_title, $m );
            $star_count  = ! empty( $m[1] ) ? mb_strlen( $m[1] ) : 5;
            $clean_title = trim( preg_replace( '/^★+\s*/u', '', $raw_title ) );

            // Author from dc:creator namespace
            $dc   = $item->children( 'http://purl.org/dc/elements/1.1/' );
            $name = ! empty( $dc->creator ) ? (string) $dc->creator : __( 'Anonymous', 'reviewfic' );

            // Content: prefer <description>, strip HTML tags
            $content = trim( strip_tags( html_entity_decode( (string) $item->description, ENT_QUOTES, 'UTF-8' ) ) );

            // WordPress.org's feed prepends forum metadata like "Replies: 0
            // Rating: 5 stars " directly into the description text — strip it.
            $content = preg_replace( '/^Replies:\s*\d+\s*Rating:\s*\d+(?:\.\d+)?\s*stars?\s*/i', '', $content );
            $content = trim( $content );

            $pub_date = (string) $item->pubDate;
            $time     = $pub_date
                ? date_i18n( get_option( 'date_format' ), strtotime( $pub_date ) )
                : '';

            $raw[] = array(
                'title'   => $clean_title,
                'content' => $content,
                'stars'   => (float) max( 1, min( 5, $star_count ) ),
                'name'    => $name,
                'meta'    => '',
                'avatar'  => '',
                'time'    => $time,
            );
        }

        if ( empty( $raw ) ) {
            return rwf_live_error( __( 'No reviews found for this plugin.', 'reviewfic' ) );
        }

        set_transient( $cache_key, $raw, 12 * HOUR_IN_SECONDS );
        $cached = $raw;
    }

    $reviews = array_slice( $cached, 0, $max );

    // Derive a readable plugin name from the feed title if possible, else use slug
    $source_name = ucwords( str_replace( '-', ' ', $slug ) );

    return rwf_render_live_cards( $reviews, $atts, 'custom', $source_name );
}
