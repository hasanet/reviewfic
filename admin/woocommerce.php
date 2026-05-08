<?php
/**
 * Reviewfic — WooCommerce Integration
 *
 * Features:
 *  1. Post-purchase review request email (with configurable delay)
 *  2. Replace WooCommerce reviews tab with Reviewfic templates
 *  3. Auto-tag reviews by product when submitted via review request link
 *
 * Settings stored in: reviewfic_wc_settings (array)
 */
if ( ! defined( 'ABSPATH' ) ) exit;

// Only load when WooCommerce is active
add_action( 'plugins_loaded', 'rwf_wc_init' );

function rwf_wc_init() {
    if ( ! class_exists( 'WooCommerce' ) ) return;

    // Admin
    add_action( 'admin_menu',                'rwf_wc_register_page' );
    add_action( 'admin_post_rwf_save_wc',    'rwf_wc_save_settings' );

    // Email scheduling
    add_action( 'woocommerce_order_status_completed', 'rwf_wc_schedule_email' );
    add_action( 'reviewfic_send_review_email',        'rwf_wc_send_email' );

    // Tab replacement
    add_filter( 'woocommerce_product_tabs', 'rwf_wc_replace_review_tab' );

    // Auto-tag by product
    add_action( 'reviewfic_after_form_submit', 'rwf_wc_auto_tag_product', 10, 2 );
}


// ── Helpers ────────────────────────────────────────────────────────────────

function rwf_wc_get_settings() {
    return wp_parse_args( get_option( 'reviewfic_wc_settings', array() ), array(
        'enabled'       => '0',
        'delay_days'    => '2',
        'review_page'   => '',
        'email_subject' => 'How was your experience with {site_name}?',
        'email_body'    => "Hi {customer_name},\n\nThank you for your recent order #{order_id} from {site_name}.\n\nWe'd love to hear what you think! Your feedback helps us improve and helps other customers make better decisions.\n\nClick the button below to leave your review — it only takes a minute.\n\nThank you for your support!",
        'replace_tab'   => '0',
        'auto_tag'      => '1',
    ) );
}

function rwf_set_html_mail() { return 'text/html'; }


// ══════════════════════════════════════════════════════════════════════════
//  ADMIN SETTINGS PAGE
// ══════════════════════════════════════════════════════════════════════════

function rwf_wc_register_page() {
    add_submenu_page(
        'edit.php?post_type=reviewfic_reviews',
        __( 'WooCommerce', 'reviewfic' ),
        __( 'WooCommerce', 'reviewfic' ),
        'manage_options',
        'reviewfic-woocommerce',
        'rwf_wc_page'
    );
}

function rwf_wc_save_settings() {
    if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );
    check_admin_referer( 'rwf_save_wc' );

    $s = rwf_wc_get_settings();
    $s['enabled']       = isset( $_POST['rwf_wc_enabled'] ) ? '1' : '0';
    $s['delay_days']    = sanitize_text_field( wp_unslash( $_POST['rwf_delay_days']  ?? '2' ) );
    $s['review_page']   = intval( $_POST['rwf_review_page'] ?? 0 );
    $s['email_subject'] = sanitize_text_field( wp_unslash( $_POST['rwf_email_subject'] ?? '' ) );
    $s['email_body']    = sanitize_textarea_field( wp_unslash( $_POST['rwf_email_body'] ?? '' ) );
    $s['replace_tab']   = isset( $_POST['rwf_replace_tab'] ) ? '1' : '0';
    $s['auto_tag']      = isset( $_POST['rwf_auto_tag'] ) ? '1' : '0';

    update_option( 'reviewfic_wc_settings', $s );

    wp_safe_redirect( add_query_arg( array(
        'post_type' => 'reviewfic_reviews',
        'page'      => 'reviewfic-woocommerce',
        'saved'     => '1',
    ), admin_url( 'edit.php' ) ) );
    exit;
}

function rwf_wc_page() {
    $s     = rwf_wc_get_settings();
    $saved = ! empty( $_GET['saved'] );
    $pages = get_pages( array( 'post_status' => 'publish' ) );

    $delay_options = array(
        '0'  => __( 'Immediately on order completion', 'reviewfic' ),
        '1'  => __( '1 day after order completion', 'reviewfic' ),
        '2'  => __( '2 days after order completion', 'reviewfic' ),
        '3'  => __( '3 days after order completion', 'reviewfic' ),
        '5'  => __( '5 days after order completion', 'reviewfic' ),
        '7'  => __( '7 days after order completion', 'reviewfic' ),
        '14' => __( '14 days after order completion', 'reviewfic' ),
    );
    ?>
    <div class="wrap rwf-ie-wrap">
        <h1 class="wp-heading-inline"><?php esc_html_e( 'WooCommerce Integration', 'reviewfic' ); ?></h1>
        <hr class="wp-header-end">

        <?php if ( $saved ) : ?>
            <div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Settings saved.', 'reviewfic' ); ?></p></div>
        <?php endif; ?>

        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
            <?php wp_nonce_field( 'rwf_save_wc' ); ?>
            <input type="hidden" name="action" value="rwf_save_wc">

            <div class="rwf-wc-grid">

                <!-- ── Post-Purchase Email ── -->
                <div class="rwf-ie-card">
                    <div class="rwf-ie-card-header">
                        <span class="dashicons dashicons-email-alt rwf-ie-card-icon"></span>
                        <div>
                            <h2><?php esc_html_e( 'Post-Purchase Review Request', 'reviewfic' ); ?></h2>
                            <p><?php esc_html_e( 'Automatically email customers after their order is completed asking them to leave a review.', 'reviewfic' ); ?></p>
                        </div>
                    </div>

                    <div class="rwf-wc-field">
                        <label class="rwf-wc-toggle-label">
                            <input type="checkbox" name="rwf_wc_enabled" value="1" <?php checked( $s['enabled'], '1' ); ?>>
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
                                <h2><?php esc_html_e( 'Product Reviews Tab', 'reviewfic' ); ?></h2>
                                <p><?php esc_html_e( 'Replace WooCommerce\'s default review tab with Reviewfic\'s templates.', 'reviewfic' ); ?></p>
                            </div>
                        </div>
                        <div class="rwf-wc-field">
                            <label class="rwf-wc-toggle-label">
                                <input type="checkbox" name="rwf_replace_tab" value="1" <?php checked( $s['replace_tab'], '1' ); ?>>
                                <strong><?php esc_html_e( 'Replace the WooCommerce reviews tab', 'reviewfic' ); ?></strong>
                            </label>
                            <p class="description"><?php esc_html_e( 'Product reviews will be rendered using Template 1 (Classic) with 3 columns. A "Write a Review" button links to your review landing page.', 'reviewfic' ); ?></p>
                        </div>
                    </div>

                    <div class="rwf-ie-card">
                        <div class="rwf-ie-card-header">
                            <span class="dashicons dashicons-tag rwf-ie-card-icon"></span>
                            <div>
                                <h2><?php esc_html_e( 'Auto-Tag by Product', 'reviewfic' ); ?></h2>
                                <p><?php esc_html_e( 'Automatically categorise reviews by the product in the order.', 'reviewfic' ); ?></p>
                            </div>
                        </div>
                        <div class="rwf-wc-field">
                            <label class="rwf-wc-toggle-label">
                                <input type="checkbox" name="rwf_auto_tag" value="1" <?php checked( $s['auto_tag'], '1' ); ?>>
                                <strong><?php esc_html_e( 'Auto-tag reviews with product name as category', 'reviewfic' ); ?></strong>
                            </label>
                            <p class="description"><?php esc_html_e( 'When a customer clicks the review link from the email and submits a review, it is automatically tagged with the product name as a Reviewfic category. Works for single-product orders.', 'reviewfic' ); ?></p>
                        </div>
                    </div>
                </div>

            </div><!-- .rwf-wc-grid -->

            <p style="margin-top:20px;">
                <button type="submit" class="button button-primary rwf-ie-btn">
                    <?php esc_html_e( 'Save WooCommerce Settings', 'reviewfic' ); ?>
                </button>
            </p>
        </form>
    </div>
    <?php
}


// ══════════════════════════════════════════════════════════════════════════
//  POST-PURCHASE EMAIL
// ══════════════════════════════════════════════════════════════════════════

function rwf_wc_schedule_email( $order_id ) {
    $s = rwf_wc_get_settings();
    if ( $s['enabled'] !== '1' || empty( $s['review_page'] ) ) return;

    $delay = max( 0, intval( $s['delay_days'] ) ) * DAY_IN_SECONDS;
    wp_schedule_single_event( time() + $delay, 'reviewfic_send_review_email', array( $order_id ) );
}

function rwf_wc_send_email( $order_id ) {
    $order = wc_get_order( $order_id );
    if ( ! $order ) return;

    $s              = rwf_wc_get_settings();
    $customer_name  = $order->get_billing_first_name();
    $customer_email = $order->get_billing_email();
    $order_number   = $order->get_order_number();
    $site_name      = get_bloginfo( 'name' );
    $review_page_id = intval( $s['review_page'] );

    if ( ! $review_page_id || ! $customer_email ) return;

    // Build review URL — include product ID for auto-tagging (single-product orders)
    $review_url = get_permalink( $review_page_id );
    $items      = $order->get_items();
    if ( count( $items ) === 1 ) {
        $item    = reset( $items );
        $pid     = $item->get_product_id();
        if ( $pid ) $review_url = add_query_arg( 'rwf_product', $pid, $review_url );
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

    $html = rwf_wc_build_email_html( $body_text, $review_url, $site_name );

    add_filter( 'wp_mail_content_type', 'rwf_set_html_mail' );
    wp_mail( $customer_email, $subject, $html );
    remove_filter( 'wp_mail_content_type', 'rwf_set_html_mail' );
}

function rwf_wc_build_email_html( $body_text, $review_url, $site_name ) {
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
//  WC REVIEWS TAB REPLACEMENT
// ══════════════════════════════════════════════════════════════════════════

function rwf_wc_replace_review_tab( $tabs ) {
    $s = rwf_wc_get_settings();
    if ( $s['replace_tab'] !== '1' ) return $tabs;

    if ( isset( $tabs['reviews'] ) ) {
        $tabs['reviews']['callback'] = 'rwf_wc_reviews_tab_content';
    }
    return $tabs;
}

function rwf_wc_reviews_tab_content() {
    global $product;
    if ( ! $product ) return;

    $comments = get_comments( array(
        'post_id'  => $product->get_id(),
        'type'     => 'review',
        'status'   => 'approve',
        'order'    => 'DESC',
    ) );

    if ( empty( $comments ) ) {
        echo '<p class="rwf-wc-no-reviews">' . esc_html__( 'No reviews yet. Be the first to leave one!', 'reviewfic' ) . '</p>';
    } else {
        $reviews = array();
        foreach ( $comments as $comment ) {
            $rating    = intval( get_comment_meta( $comment->comment_ID, 'rating', true ) );
            $reviews[] = array(
                'title'   => '',
                'content' => $comment->comment_content,
                'stars'   => $rating ?: 5,
                'name'    => $comment->comment_author,
                'meta'    => '',
                'avatar'  => get_avatar_url( $comment->comment_author_email, array( 'size' => 96 ) ),
                'time'    => human_time_diff( strtotime( $comment->comment_date ), current_time( 'timestamp' ) ) . ' ' . __( 'ago', 'reviewfic' ),
            );
        }

        // Use rwf_render_live_cards from live-reviews.php
        if ( function_exists( 'rwf_render_live_cards' ) ) {
            echo rwf_render_live_cards( $reviews, array(
                'columns'     => '3',
                'template'    => '1',
                'slider'      => 'no',
                'show_avatar' => 'yes',
            ), 'custom', get_bloginfo( 'name' ) );
        }
    }

    // Write a Review CTA
    $s              = rwf_wc_get_settings();
    $review_page_id = intval( $s['review_page'] );
    if ( $review_page_id ) {
        $url = add_query_arg( 'rwf_product', $product->get_id(), get_permalink( $review_page_id ) );
        echo '<div class="rwf-wc-write-review">';
        echo '<a href="' . esc_url( $url ) . '" class="rwf-wc-review-btn">';
        echo '<span class="dashicons dashicons-edit"></span> ' . esc_html__( 'Write a Review', 'reviewfic' );
        echo '</a>';
        echo '</div>';
    }
}


// ══════════════════════════════════════════════════════════════════════════
//  AUTO-TAG BY PRODUCT
// ══════════════════════════════════════════════════════════════════════════

function rwf_wc_auto_tag_product( $post_id, $post_data ) {
    $s = rwf_wc_get_settings();
    if ( $s['auto_tag'] !== '1' ) return;

    $product_id = intval( $post_data['rwf_product_id'] ?? 0 );
    if ( ! $product_id ) return;

    $product = wc_get_product( $product_id );
    if ( ! $product ) return;

    $term_name = $product->get_name();
    $term      = get_term_by( 'name', $term_name, 'reviewfic_category' );

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
