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

    // Clear cached responses when keys change
    global $wpdb;
    $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_reviewfic_google_%' OR option_name LIKE '_transient_reviewfic_yelp_%'" );

    wp_safe_redirect( add_query_arg( array(
        'post_type' => 'reviewfic_reviews',
        'page'      => 'reviewfic-live-reviews',
        'saved'     => '1',
    ), admin_url( 'edit.php' ) ) );
    exit;
} );

function rwf_live_reviews_page() {
    $google_key = get_option( 'reviewfic_google_api_key', '' );
    $yelp_key   = get_option( 'reviewfic_yelp_api_key',   '' );
    $saved      = ! empty( $_GET['saved'] );
    ?>
    <div class="wrap rwf-ie-wrap">
        <h1 class="wp-heading-inline"><?php esc_html_e( 'Live Reviews', 'reviewfic' ); ?></h1>
        <hr class="wp-header-end">

        <?php if ( $saved ) : ?>
            <div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'API keys saved.', 'reviewfic' ); ?></p></div>
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
                    <code class="rwf-live-code">[reviewfic_google place_id="ChIJ..." columns="3" template="1" max="5"]</code>
                    <table class="rwf-live-attr-table">
                        <tr><td><code>place_id</code></td><td><?php esc_html_e( 'Required. Google Place ID.', 'reviewfic' ); ?> <a href="https://developers.google.com/maps/documentation/javascript/examples/places-placeid-finder" target="_blank" rel="noopener"><?php esc_html_e( 'Find it here', 'reviewfic' ); ?> ↗</a></td></tr>
                        <tr><td><code>max</code></td><td><?php esc_html_e( '1–5 (Google returns up to 5 reviews). Default: 5', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>columns</code></td><td><?php esc_html_e( '1–4. Default: 3', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>template</code></td><td><?php esc_html_e( '1–10. Default: 1', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>slider</code></td><td><?php esc_html_e( 'yes / no. Default: no', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>show_avatar</code></td><td><?php esc_html_e( 'yes / no. Default: yes', 'reviewfic' ); ?></td></tr>
                    </table>
                </div>

                <div class="rwf-live-shortcode-block" style="margin-top:20px;">
                    <div class="rwf-live-platform-badge rwf-badge-yelp" style="margin-bottom:8px;">Yelp</div>
                    <code class="rwf-live-code">[reviewfic_yelp business_id="..." columns="3" template="1" max="3"]</code>
                    <table class="rwf-live-attr-table">
                        <tr><td><code>business_id</code></td><td><?php esc_html_e( 'Required. Yelp Business ID or alias (e.g. gary-danko-san-francisco).', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>max</code></td><td><?php esc_html_e( '1–3 (Yelp free tier limit). Default: 3', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>columns</code></td><td><?php esc_html_e( '1–4. Default: 3', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>template</code></td><td><?php esc_html_e( '1–10. Default: 1', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>slider</code></td><td><?php esc_html_e( 'yes / no. Default: no', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>show_avatar</code></td><td><?php esc_html_e( 'yes / no. Default: yes', 'reviewfic' ); ?></td></tr>
                    </table>
                </div>

                <div class="rwf-live-shortcode-block" style="margin-top:20px;">
                    <div class="rwf-live-platform-badge" style="background:#96588a;margin-bottom:8px;">WooCommerce</div>
                    <code class="rwf-live-code">[reviewfic_woocommerce product_id="123" columns="3" template="1" max="10"]</code>
                    <table class="rwf-live-attr-table">
                        <tr><td><code>product_id</code></td><td><?php esc_html_e( 'Required. WooCommerce product ID. Display existing product reviews anywhere on the site.', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>max</code></td><td><?php esc_html_e( 'Maximum reviews to show. Default: 10', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>columns</code></td><td><?php esc_html_e( '1–4. Default: 3', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>template</code></td><td><?php esc_html_e( '1–10. Default: 1', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>slider</code></td><td><?php esc_html_e( 'yes / no. Default: no', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>show_avatar</code></td><td><?php esc_html_e( 'yes / no. Default: yes', 'reviewfic' ); ?></td></tr>
                    </table>
                    <?php if ( ! class_exists( 'WooCommerce' ) ) : ?>
                        <p class="description" style="color:#b32d2e;margin-top:8px;"><?php esc_html_e( 'WooCommerce is not active — this shortcode will show an error until it is installed.', 'reviewfic' ); ?></p>
                    <?php endif; ?>
                </div>

                <div class="rwf-live-shortcode-block" style="margin-top:20px;">
                    <div class="rwf-live-platform-badge" style="background:#21759b;margin-bottom:8px;">WordPress.org</div>
                    <code class="rwf-live-code">[reviewfic_wporg plugin="reviewfic" columns="3" template="1" max="5"]</code>
                    <table class="rwf-live-attr-table">
                        <tr><td><code>plugin</code></td><td><?php esc_html_e( 'Required. The plugin slug as it appears on WordPress.org (e.g. "contact-form-7"). No API key needed.', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>max</code></td><td><?php esc_html_e( 'Maximum reviews to show. Default: 5', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>columns</code></td><td><?php esc_html_e( '1–4. Default: 3', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>template</code></td><td><?php esc_html_e( '1–10. Default: 1', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>slider</code></td><td><?php esc_html_e( 'yes / no. Default: no', 'reviewfic' ); ?></td></tr>
                        <tr><td><code>show_avatar</code></td><td><?php esc_html_e( 'yes / no. Default: yes', 'reviewfic' ); ?></td></tr>
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

function rwf_render_live_cards( $reviews, $atts, $source_slug, $source_name ) {
    static $slider_id = 1000; // offset to avoid collision with CPT slider IDs

    $valid_templates = array( '1','2','3','4','5','6','7','8','9','10' );
    $template     = in_array( $atts['template'], $valid_templates, true ) ? $atts['template'] : '1';
    $use_slider   = $atts['slider'] === 'yes';
    $show_avatar  = $atts['show_avatar'] !== 'no';
    $columns      = max( 1, min( 4, intval( $atts['columns'] ) ) );
    $known_sources = array('google','trustpilot','g2','capterra','facebook','yelp','amazon');

    $star_path = plugin_dir_path( __FILE__ ) . 'assets/img/';

    $get_star_svg = function ( $type ) use ( $star_path ) {
        $map  = array('full' => 'star-solid.svg', 'half' => 'star-half-stroke-solid.svg', 'empty' => 'star-regular.svg');
        $file = $star_path . ( $map[ $type ] ?? 'star-regular.svg' );
        return file_exists( $file ) ? file_get_contents( $file ) : '★';
    };

    $source_class = in_array( $source_slug, $known_sources, true )
                    ? 'reviewfic-source-' . $source_slug
                    : 'reviewfic-source-custom';
    $badge_markup = '<span class="reviewfic-source-badge ' . esc_attr( $source_class ) . '">' . esc_html( $source_name ) . '</span>';

    ob_start();

    if ( $use_slider ) {
        $slider_id++;
        echo '<div class="reviewfic-slider" id="reviewfic-slider-' . $slider_id . '"'
            . ' data-nav="yes" data-dots="yes" data-auto="no" data-speed="4000"'
            . ' data-loop="yes" data-pause="yes"'
            . ' data-columns="' . esc_attr( $columns ) . '">';
        echo '<div class="reviewfic-slider-track">';
    } else {
        echo '<div class="reviewfic-columns reviewfic-columns-' . esc_attr( $columns ) . '">';
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

        // Avatar
        $avatar_markup = '';
        if ( $show_avatar && ! empty( $r['avatar'] ) ) {
            $avatar_markup = '<div class="reviewfic-avatar">'
                . '<img src="' . esc_url( $r['avatar'] ) . '" alt="' . esc_attr( $r['name'] ) . '" class="reviewfic-avatar-img" loading="lazy">'
                . '</div>';
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

    return ob_get_clean();
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
        'place_id'    => '',
        'max'         => 5,
        'columns'     => 3,
        'template'    => '1',
        'slider'      => 'no',
        'show_avatar' => 'yes',
    ), $atts, 'reviewfic_google' );

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
        'business_id' => '',
        'max'         => 3,
        'columns'     => 3,
        'template'    => '1',
        'slider'      => 'no',
        'show_avatar' => 'yes',
    ), $atts, 'reviewfic_yelp' );

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
        'product_id'  => 0,
        'max'         => 10,
        'columns'     => 3,
        'template'    => '1',
        'slider'      => 'no',
        'show_avatar' => 'yes',
    ), $atts, 'reviewfic_woocommerce' );

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
        'plugin'      => '',
        'max'         => 5,
        'columns'     => 3,
        'template'    => '1',
        'slider'      => 'no',
        'show_avatar' => 'yes',
    ), $atts, 'reviewfic_wporg' );

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
