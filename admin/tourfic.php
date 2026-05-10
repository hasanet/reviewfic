<?php
/**
 * Reviewfic — Tourfic Integration
 *
 * Features:
 *  1. Post-booking review request email (with configurable delay)
 *  2. Replace Tourfic review section with Reviewfic cards (hotels, tours, apartments)
 *  3. Auto-tag reviews by service type + name when submitted via review request link
 *
 * Settings stored in: reviewfic_tf_settings (array)
 *
 * Notes:
 *  - Car rental review replacement is NOT supported: Tourfic embeds its car review
 *    block directly in the template without calling comments_template(), so there
 *    is no standard hook to intercept. Email + auto-tag still work for car bookings.
 *  - Tourfic stores tour post IDs as `_tour_id` order item meta; hotel/apartment/car
 *    use `_post_id`.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

// Only load when both Tourfic and WooCommerce are active
add_action( 'plugins_loaded', 'rwf_tf_init' );

function rwf_tf_init() {
    if ( ! defined( 'TF_VERSION' ) || ! class_exists( 'WooCommerce' ) ) return;

    // Admin
    add_action( 'admin_menu',             'rwf_tf_register_page' );
    add_action( 'admin_post_rwf_save_tf', 'rwf_tf_save_settings' );

    // Email scheduling — fires when a WooCommerce order containing a Tourfic booking completes
    add_action( 'woocommerce_order_status_completed', 'rwf_tf_schedule_email' );
    add_action( 'reviewfic_send_tf_review_email',     'rwf_tf_send_email' );

    // Review section replacement — priority 20 runs after Tourfic's filter at priority 10
    add_filter( 'comments_template', 'rwf_tf_replace_comments_template', 20 );

    // Auto-tag by service name on form submit
    add_action( 'reviewfic_after_form_submit', 'rwf_tf_auto_tag_service', 10, 2 );
}


// ── Helpers ─────────────────────────────────────────────────────────────────

function rwf_tf_get_settings() {
    return wp_parse_args( get_option( 'reviewfic_tf_settings', array() ), array(
        'enabled'       => '0',
        'delay_days'    => '2',
        'review_page'   => '',
        'email_subject' => 'How was your experience with {site_name}?',
        'email_body'    => "Hi {customer_name},\n\nThank you for your recent booking (#{order_id}) with {site_name}.\n\nWe'd love to hear what you think! Your feedback helps us improve and helps other travellers make better decisions.\n\nClick the button below to leave your review — it only takes a minute.\n\nThank you for choosing {site_name}!",
        'replace_section'     => '0',
        'auto_tag'            => '1',
        'display_template'    => '1',
        'display_columns'     => '3',
        'display_slider'      => 'no',
        'display_show_avatar' => 'yes',
    ) );
}

/**
 * Get the Tourfic service (hotel / tour / apartment / car) from a WooCommerce order.
 * Returns array with 'type' and 'post_id', or null if no Tourfic item found.
 */
function rwf_tf_get_service_from_order( $order ) {
    foreach ( $order->get_items() as $item ) {
        $order_type = $item->get_meta( '_order_type', true );
        if ( ! in_array( $order_type, array( 'hotel', 'tour', 'apartment', 'car' ), true ) ) {
            continue;
        }
        // Tours store their post ID under _tour_id; everything else uses _post_id
        $post_id = ( 'tour' === $order_type )
            ? intval( $item->get_meta( '_tour_id', true ) )
            : intval( $item->get_meta( '_post_id', true ) );

        if ( $post_id ) {
            return array( 'type' => $order_type, 'post_id' => $post_id );
        }
    }
    return null;
}

/** Type label used for auto-tag category prefix (e.g. "Hotel", "Tour") */
function rwf_tf_type_label( $type ) {
    $labels = array(
        'hotel'     => __( 'Hotel',     'reviewfic' ),
        'tour'      => __( 'Tour',      'reviewfic' ),
        'apartment' => __( 'Apartment', 'reviewfic' ),
        'car'       => __( 'Car',       'reviewfic' ),
    );
    return $labels[ $type ] ?? ucfirst( $type );
}

function rwf_set_html_mail_tf() { return 'text/html'; }


// ══════════════════════════════════════════════════════════════════════════
//  ADMIN SETTINGS PAGE
// ══════════════════════════════════════════════════════════════════════════

function rwf_tf_register_page() {
    add_submenu_page(
        'edit.php?post_type=reviewfic_reviews',
        __( 'Tourfic', 'reviewfic' ),
        __( 'Tourfic', 'reviewfic' ),
        'manage_options',
        'reviewfic-tourfic',
        'rwf_tf_page'
    );
}

function rwf_tf_save_settings() {
    if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );
    check_admin_referer( 'rwf_save_tf' );

    $s = rwf_tf_get_settings();
    $s['enabled']         = isset( $_POST['rwf_tf_enabled'] ) ? '1' : '0';
    $s['delay_days']      = sanitize_text_field( wp_unslash( $_POST['rwf_delay_days']  ?? '2' ) );
    $s['review_page']     = intval( $_POST['rwf_review_page'] ?? 0 );
    $s['email_subject']   = sanitize_text_field( wp_unslash( $_POST['rwf_email_subject'] ?? '' ) );
    $s['email_body']      = sanitize_textarea_field( wp_unslash( $_POST['rwf_email_body'] ?? '' ) );
    $s['replace_section']     = isset( $_POST['rwf_replace_section'] ) ? '1' : '0';
    $s['auto_tag']            = isset( $_POST['rwf_auto_tag'] ) ? '1' : '0';
    $s['display_template']    = in_array( $_POST['rwf_display_template'] ?? '1', array('1','2','3','4','5','6','7','8','9','10'), true )
                                    ? sanitize_text_field( $_POST['rwf_display_template'] ) : '1';
    $s['display_columns']     = in_array( intval( $_POST['rwf_display_columns'] ?? 3 ), array(1,2,3,4), true )
                                    ? strval( intval( $_POST['rwf_display_columns'] ) ) : '3';
    $s['display_slider']      = isset( $_POST['rwf_display_slider'] ) ? 'yes' : 'no';
    $s['display_show_avatar'] = isset( $_POST['rwf_display_show_avatar'] ) ? 'yes' : 'no';

    update_option( 'reviewfic_tf_settings', $s );

    wp_safe_redirect( add_query_arg( array(
        'post_type' => 'reviewfic_reviews',
        'page'      => 'reviewfic-tourfic',
        'saved'     => '1',
    ), admin_url( 'edit.php' ) ) );
    exit;
}

function rwf_tf_page() {
    $s     = rwf_tf_get_settings();
    $saved = ! empty( $_GET['saved'] );
    $pages = get_pages( array( 'post_status' => 'publish' ) );

    $delay_options = array(
        '0'  => __( 'Immediately on order completion', 'reviewfic' ),
        '1'  => __( '1 day after order completion',   'reviewfic' ),
        '2'  => __( '2 days after order completion',  'reviewfic' ),
        '3'  => __( '3 days after order completion',  'reviewfic' ),
        '5'  => __( '5 days after order completion',  'reviewfic' ),
        '7'  => __( '7 days after order completion',  'reviewfic' ),
        '14' => __( '14 days after order completion', 'reviewfic' ),
    );
    ?>
    <div class="wrap rwf-ie-wrap">
        <h1 class="wp-heading-inline"><?php esc_html_e( 'Tourfic Integration', 'reviewfic' ); ?></h1>
        <hr class="wp-header-end">

        <?php if ( $saved ) : ?>
            <div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Settings saved.', 'reviewfic' ); ?></p></div>
        <?php endif; ?>

        <?php if ( ! defined( 'TF_VERSION' ) ) : ?>
            <div class="notice notice-warning"><p>
                <?php esc_html_e( 'Tourfic is not installed or activated. These settings will have no effect until Tourfic is active.', 'reviewfic' ); ?>
            </p></div>
        <?php endif; ?>

        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
            <?php wp_nonce_field( 'rwf_save_tf' ); ?>
            <input type="hidden" name="action" value="rwf_save_tf">

            <div class="rwf-wc-grid">

                <!-- ── Post-Booking Email ── -->
                <div class="rwf-ie-card">
                    <div class="rwf-ie-card-header">
                        <span class="dashicons dashicons-email-alt rwf-ie-card-icon"></span>
                        <div>
                            <h2><?php esc_html_e( 'Post-Booking Review Request', 'reviewfic' ); ?></h2>
                            <p><?php esc_html_e( 'Automatically email customers after their booking is confirmed, asking them to leave a review.', 'reviewfic' ); ?></p>
                        </div>
                    </div>

                    <div class="rwf-wc-field">
                        <label class="rwf-wc-toggle-label">
                            <input type="checkbox" name="rwf_tf_enabled" value="1" <?php checked( $s['enabled'], '1' ); ?>>
                            <strong><?php esc_html_e( 'Enable review request emails', 'reviewfic' ); ?></strong>
                        </label>
                    </div>

                    <div class="rwf-wc-field">
                        <label><?php esc_html_e( 'Send Delay', 'reviewfic' ); ?></label>
                        <select name="rwf_delay_days">
                            <?php foreach ( $delay_options as $val => $label ) : ?>
                                <option value="<?php echo esc_attr( $val ); ?>" <?php selected( $s['delay_days'], (string) $val ); ?>>
                                    <?php echo esc_html( $label ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="rwf-wc-field">
                        <label><?php esc_html_e( 'Review Landing Page', 'reviewfic' ); ?></label>
                        <select name="rwf_review_page">
                            <option value=""><?php esc_html_e( '— Select a page with [reviewfic_form] —', 'reviewfic' ); ?></option>
                            <?php foreach ( $pages as $page ) : ?>
                                <option value="<?php echo esc_attr( $page->ID ); ?>" <?php selected( $s['review_page'], $page->ID ); ?>>
                                    <?php echo esc_html( $page->post_title ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description"><?php esc_html_e( 'The customer is sent to this page to leave their review. Place [reviewfic_form] on it.', 'reviewfic' ); ?></p>
                    </div>

                    <div class="rwf-wc-field">
                        <label><?php esc_html_e( 'Email Subject', 'reviewfic' ); ?></label>
                        <input type="text" name="rwf_email_subject" value="<?php echo esc_attr( $s['email_subject'] ); ?>" class="large-text">
                        <p class="description"><?php esc_html_e( 'Placeholders: {customer_name}, {order_id}, {site_name}', 'reviewfic' ); ?></p>
                    </div>

                    <div class="rwf-wc-field">
                        <label><?php esc_html_e( 'Email Body', 'reviewfic' ); ?></label>
                        <textarea name="rwf_email_body" rows="6" class="large-text"><?php echo esc_textarea( $s['email_body'] ); ?></textarea>
                        <p class="description"><?php esc_html_e( 'Placeholders: {customer_name}, {order_id}, {site_name}. The review button is added automatically.', 'reviewfic' ); ?></p>
                    </div>

                    <div class="rwf-wc-preview-note">
                        <span class="dashicons dashicons-visibility"></span>
                        <?php esc_html_e( 'A styled "Leave a Review" button with your review page link is automatically appended to the email.', 'reviewfic' ); ?>
                    </div>
                </div>

                <!-- ── Display & Tagging ── -->
                <div>
                    <div class="rwf-ie-card" style="margin-bottom:20px;">
                        <div class="rwf-ie-card-header">
                            <span class="dashicons dashicons-star-filled rwf-ie-card-icon"></span>
                            <div>
                                <h2><?php esc_html_e( 'Listing Review Section', 'reviewfic' ); ?></h2>
                                <p><?php esc_html_e( 'Replace Tourfic\'s review section with Reviewfic\'s cards on hotel, tour, and apartment listings.', 'reviewfic' ); ?></p>
                            </div>
                        </div>
                        <div class="rwf-wc-field">
                            <label class="rwf-wc-toggle-label">
                                <input type="checkbox" name="rwf_replace_section" value="1" <?php checked( $s['replace_section'], '1' ); ?>>
                                <strong><?php esc_html_e( 'Replace the Tourfic review section', 'reviewfic' ); ?></strong>
                            </label>
                            <p class="description"><em><?php esc_html_e( 'Note: car rental listings use an embedded review block and are not affected by this setting.', 'reviewfic' ); ?></em></p>
                        </div>

                        <?php $tpl_names = function_exists( 'rwf_template_names' ) ? rwf_template_names() : array(); ?>
                        <div class="rwf-wc-field">
                            <label><?php esc_html_e( 'Display Template', 'reviewfic' ); ?></label>
                            <select name="rwf_display_template">
                                <?php foreach ( $tpl_names as $val => $label ) : ?>
                                    <option value="<?php echo esc_attr( $val ); ?>" <?php selected( $s['display_template'], $val ); ?>><?php echo esc_html( $label ); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="rwf-wc-field">
                            <label><?php esc_html_e( 'Columns', 'reviewfic' ); ?></label>
                            <select name="rwf_display_columns">
                                <?php foreach ( array('1','2','3','4') as $c ) : ?>
                                    <option value="<?php echo esc_attr( $c ); ?>" <?php selected( $s['display_columns'], $c ); ?>><?php echo esc_html( $c ); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="rwf-wc-field">
                            <label class="rwf-wc-toggle-label">
                                <input type="checkbox" name="rwf_display_slider" value="1" <?php checked( $s['display_slider'], 'yes' ); ?>>
                                <strong><?php esc_html_e( 'Enable slider (carousel)', 'reviewfic' ); ?></strong>
                            </label>
                        </div>

                        <div class="rwf-wc-field">
                            <label class="rwf-wc-toggle-label">
                                <input type="checkbox" name="rwf_display_show_avatar" value="1" <?php checked( $s['display_show_avatar'], 'yes' ); ?>>
                                <strong><?php esc_html_e( 'Show reviewer avatars', 'reviewfic' ); ?></strong>
                            </label>
                        </div>
                    </div>

                    <div class="rwf-ie-card">
                        <div class="rwf-ie-card-header">
                            <span class="dashicons dashicons-tag rwf-ie-card-icon"></span>
                            <div>
                                <h2><?php esc_html_e( 'Auto-Tag by Service', 'reviewfic' ); ?></h2>
                                <p><?php esc_html_e( 'Automatically categorise reviews by the booked service when submitted via the review request link.', 'reviewfic' ); ?></p>
                            </div>
                        </div>
                        <div class="rwf-wc-field">
                            <label class="rwf-wc-toggle-label">
                                <input type="checkbox" name="rwf_auto_tag" value="1" <?php checked( $s['auto_tag'], '1' ); ?>>
                                <strong><?php esc_html_e( 'Auto-tag reviews with service name as category', 'reviewfic' ); ?></strong>
                            </label>
                            <p class="description"><?php esc_html_e( 'When a customer clicks the review link from the email and submits a review, it is automatically tagged with a prefixed category (e.g. "Hotel: Grand Hyatt", "Tour: Desert Safari").', 'reviewfic' ); ?></p>
                        </div>
                    </div>
                </div>

            </div><!-- .rwf-wc-grid -->

            <p style="margin-top:20px;">
                <button type="submit" class="button button-primary rwf-ie-btn">
                    <?php esc_html_e( 'Save Tourfic Settings', 'reviewfic' ); ?>
                </button>
            </p>
        </form>
    </div>
    <?php
}


// ══════════════════════════════════════════════════════════════════════════
//  POST-BOOKING EMAIL
// ══════════════════════════════════════════════════════════════════════════

function rwf_tf_schedule_email( $order_id ) {
    $s = rwf_tf_get_settings();
    if ( $s['enabled'] !== '1' || empty( $s['review_page'] ) ) return;

    // Only schedule for orders that contain a Tourfic booking
    $order   = wc_get_order( $order_id );
    if ( ! $order ) return;
    $service = rwf_tf_get_service_from_order( $order );
    if ( ! $service ) return;

    $delay = max( 0, intval( $s['delay_days'] ) ) * DAY_IN_SECONDS;
    wp_schedule_single_event( time() + $delay, 'reviewfic_send_tf_review_email', array( $order_id ) );
}

function rwf_tf_send_email( $order_id ) {
    $order = wc_get_order( $order_id );
    if ( ! $order ) return;

    $s              = rwf_tf_get_settings();
    $customer_name  = $order->get_billing_first_name();
    $customer_email = $order->get_billing_email();
    $order_number   = $order->get_order_number();
    $site_name      = get_bloginfo( 'name' );
    $review_page_id = intval( $s['review_page'] );

    if ( ! $review_page_id || ! $customer_email ) return;

    // Build review URL — include service ID + type for auto-tagging
    $review_url = get_permalink( $review_page_id );
    $service    = rwf_tf_get_service_from_order( $order );
    if ( $service ) {
        $review_url = add_query_arg( array(
            'rwf_tf_service' => $service['post_id'],
            'rwf_tf_type'    => $service['type'],
        ), $review_url );
    }
    $review_url = add_query_arg( 'rwf_order', $order_id, $review_url );

    // Subject
    $subject = str_replace(
        array( '{customer_name}', '{order_id}', '{site_name}' ),
        array( $customer_name,    $order_number, $site_name   ),
        $s['email_subject']
    );

    // Body
    $body_text = str_replace(
        array( '{customer_name}', '{order_id}', '{site_name}' ),
        array( $customer_name,    $order_number, $site_name   ),
        $s['email_body']
    );

    $html = rwf_tf_build_email_html( $body_text, $review_url, $site_name );

    add_filter( 'wp_mail_content_type', 'rwf_set_html_mail_tf' );
    wp_mail( $customer_email, $subject, $html );
    remove_filter( 'wp_mail_content_type', 'rwf_set_html_mail_tf' );
}

function rwf_tf_build_email_html( $body_text, $review_url, $site_name ) {
    $body_html = nl2br( esc_html( $body_text ) );
    $btn_color = '#0E9F6E';

    return '<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:-apple-system,BlinkMacSystemFont,\'Segoe UI\',sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6;padding:40px 0;">
    <tr><td align="center">
      <table width="580" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:12px;overflow:hidden;max-width:580px;width:100%;">

        <!-- Header -->
        <tr>
          <td style="background:' . esc_attr( $btn_color ) . ';padding:24px 40px;text-align:center;">
            <span style="color:#fff;font-size:20px;font-weight:700;">' . esc_html( $site_name ) . '</span>
          </td>
        </tr>

        <!-- Body -->
        <tr>
          <td style="padding:36px 40px 28px;color:#374151;font-size:15px;line-height:1.7;">
            <p style="margin:0 0 24px;">' . $body_html . '</p>

            <!-- CTA Button -->
            <table cellpadding="0" cellspacing="0" style="margin:28px 0;">
              <tr>
                <td style="background:' . esc_attr( $btn_color ) . ';border-radius:8px;padding:0;">
                  <a href="' . esc_url( $review_url ) . '"
                     style="display:inline-block;padding:14px 32px;color:#fff;text-decoration:none;font-size:15px;font-weight:700;border-radius:8px;">
                    ⭐ Leave a Review
                  </a>
                </td>
              </tr>
            </table>

            <p style="margin:0;font-size:13px;color:#9ca3af;">
              If the button does not work, copy this link into your browser:<br>
              <a href="' . esc_url( $review_url ) . '" style="color:' . esc_attr( $btn_color ) . ';word-break:break-all;">' . esc_html( $review_url ) . '</a>
            </p>
          </td>
        </tr>

        <!-- Footer -->
        <tr>
          <td style="background:#f9fafb;padding:20px 40px;text-align:center;font-size:12px;color:#9ca3af;border-top:1px solid #f3f4f6;">
            &copy; ' . esc_html( date( 'Y' ) ) . ' ' . esc_html( $site_name ) . '. Powered by <a href="https://wordpress.org/plugins/reviewfic/" style="color:#9ca3af;">Reviewfic</a>.
          </td>
        </tr>

      </table>
    </td></tr>
  </table>
</body>
</html>';
}


// ══════════════════════════════════════════════════════════════════════════
//  REVIEW SECTION REPLACEMENT
// ══════════════════════════════════════════════════════════════════════════

/**
 * Intercept the comments_template filter at priority 20 (after Tourfic's priority 10).
 * Only active for tf_hotel, tf_tours, tf_apartment — car rental is not supported.
 */
function rwf_tf_replace_comments_template( $template ) {
    $s = rwf_tf_get_settings();
    if ( $s['replace_section'] !== '1' ) return $template;

    global $post;
    if ( ! $post ) return $template;

    $supported_types = array( 'tf_hotel', 'tf_tours', 'tf_apartment' );
    if ( ! in_array( $post->post_type, $supported_types, true ) ) return $template;

    $our_template = plugin_dir_path( __FILE__ ) . 'templates/tourfic-reviews.php';
    if ( file_exists( $our_template ) ) {
        return $our_template;
    }

    return $template;
}


// ══════════════════════════════════════════════════════════════════════════
//  AUTO-TAG BY SERVICE
// ══════════════════════════════════════════════════════════════════════════

function rwf_tf_auto_tag_service( $post_id, $post_data ) {
    $s = rwf_tf_get_settings();
    if ( $s['auto_tag'] !== '1' ) return;

    $service_id = intval( $post_data['rwf_tf_service_id'] ?? 0 );
    $type       = sanitize_key( $post_data['rwf_tf_type'] ?? '' );
    if ( ! $service_id || ! $type ) return;

    $service_post = get_post( $service_id );
    if ( ! $service_post ) return;

    $type_label  = rwf_tf_type_label( $type );
    $term_name   = $type_label . ': ' . $service_post->post_title;
    $term        = get_term_by( 'name', $term_name, 'reviewfic_category' );

    if ( ! $term ) {
        $result  = wp_insert_term( $term_name, 'reviewfic_category' );
        $term_id = ! is_wp_error( $result ) ? $result['term_id'] : 0;
    } else {
        $term_id = $term->term_id;
    }

    if ( $term_id ) {
        wp_set_post_terms( $post_id, array( $term_id ), 'reviewfic_category', true );
    }
}
