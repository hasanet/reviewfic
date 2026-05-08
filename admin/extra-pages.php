<?php
/**
 * Reviewfic — Get Help & Our Plugins pages
 */
if ( ! defined( 'ABSPATH' ) ) exit;

// ── Register submenus ──────────────────────────────────────────────────────

add_action( 'admin_menu', 'reviewfic_register_extra_pages' );

function reviewfic_register_extra_pages() {
    add_submenu_page(
        'edit.php?post_type=reviewfic_reviews',
        __( 'Get Help', 'reviewfic' ),
        __( 'Get Help', 'reviewfic' ),
        'manage_options',
        'reviewfic-get-help',
        'reviewfic_get_help_page'
    );
    add_submenu_page(
        'edit.php?post_type=reviewfic_reviews',
        __( 'Our Plugins', 'reviewfic' ),
        __( 'Our Plugins', 'reviewfic' ),
        'manage_options',
        'reviewfic-our-plugins',
        'reviewfic_our_plugins_page'
    );
}


// ══════════════════════════════════════════════════════════════════════════
//  GET HELP PAGE
// ══════════════════════════════════════════════════════════════════════════

function reviewfic_get_help_page() {
    $cards = array(
        array(
            'icon'   => 'dashicons-sos',
            'color'  => '#0E9F6E',
            'bg'     => '#ecfdf5',
            'title'  => __( 'Support', 'reviewfic' ),
            'desc'   => __( 'Having an issue? Our support team typically responds within a few hours. Submit a ticket and we\'ll get back to you.', 'reviewfic' ),
            'label'  => __( 'Open Support Ticket', 'reviewfic' ),
            'url'    => 'https://portal.themefic.com/support',
        ),
        array(
            'icon'   => 'dashicons-lightbulb',
            'color'  => '#f59e0b',
            'bg'     => '#fffbeb',
            'title'  => __( 'Feature Request', 'reviewfic' ),
            'desc'   => __( 'Got an idea that would make Reviewfic even better? We\'d love to hear it. Vote and submit feature requests directly.', 'reviewfic' ),
            'label'  => __( 'Request a Feature', 'reviewfic' ),
            'url'    => 'https://themefic.com/reviewfic/feature-request',
        ),
        array(
            'icon'   => 'dashicons-book-alt',
            'color'  => '#6366f1',
            'bg'     => '#eef2ff',
            'title'  => __( 'Documentation', 'reviewfic' ),
            'desc'   => __( 'Step-by-step guides, shortcode references, and integration docs. Find answers to the most common questions.', 'reviewfic' ),
            'label'  => __( 'Browse Docs', 'reviewfic' ),
            'url'    => 'https://themefic.com/reviewfic/docs',
        ),
    );
    ?>
    <div class="wrap rwf-ie-wrap">
        <h1 class="wp-heading-inline"><?php esc_html_e( 'Get Help', 'reviewfic' ); ?></h1>
        <hr class="wp-header-end">

        <p style="color:#6b7280;font-size:14px;margin:16px 0 24px;">
            <?php esc_html_e( 'We\'re here to help. Choose the best way to reach us or find what you need.', 'reviewfic' ); ?>
        </p>

        <div class="rwf-help-grid">
            <?php foreach ( $cards as $card ) : ?>
            <div class="rwf-help-card">
                <div class="rwf-help-icon" style="background:<?php echo esc_attr( $card['bg'] ); ?>;color:<?php echo esc_attr( $card['color'] ); ?>;">
                    <span class="dashicons <?php echo esc_attr( $card['icon'] ); ?>"></span>
                </div>
                <h2><?php echo esc_html( $card['title'] ); ?></h2>
                <p><?php echo esc_html( $card['desc'] ); ?></p>
                <a href="<?php echo esc_url( $card['url'] ); ?>" target="_blank" rel="noopener" class="rwf-help-btn" style="--rwf-btn-color:<?php echo esc_attr( $card['color'] ); ?>;">
                    <?php echo esc_html( $card['label'] ); ?>
                    <span class="dashicons dashicons-external" style="font-size:14px;width:14px;height:14px;margin-left:4px;vertical-align:middle;"></span>
                </a>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="rwf-help-footer">
            <p>
                <?php esc_html_e( 'Made by', 'reviewfic' ); ?>
                <a href="https://themefic.com" target="_blank" rel="noopener"><strong>Themefic</strong></a>
                &nbsp;·&nbsp;
                <a href="https://themefic.com/support-policy/" target="_blank" rel="noopener"><?php esc_html_e( 'Support Policy', 'reviewfic' ); ?></a>
                &nbsp;·&nbsp;
                <a href="https://www.facebook.com/groups/themefic" target="_blank" rel="noopener"><?php esc_html_e( 'Facebook Group', 'reviewfic' ); ?></a>
            </p>
        </div>
    </div>
    <?php
}


// ══════════════════════════════════════════════════════════════════════════
//  OUR PLUGINS PAGE
// ══════════════════════════════════════════════════════════════════════════

function reviewfic_our_plugins_page() {
    $plugins = array(
        array(
            'slug'     => 'hydra-booking',
            'name'     => 'Hydra Booking',
            'desc'     => 'All-in-one appointment scheduling and booking calendar. Reduce waiting times and manage appointments effortlessly for any business type.',
            'color'    => '#7c3aed',
            'init'     => 'HB',
            'icon_url' => 'https://ps.w.org/hydra-booking/assets/icon-128x128.jpg',
        ),
        array(
            'slug'     => 'instantio',
            'name'     => 'Instantio',
            'desc'     => 'Convert WooCommerce\'s multi-step checkout into a single-page instant checkout with a floating cart, side cart, and popup cart. Customers check out in seconds.',
            'color'    => '#0ea5e9',
            'init'     => 'IN',
            'icon_url' => 'https://ps.w.org/instantio/assets/icon-128x128.png',
        ),
        array(
            'slug'     => 'tourfic',
            'name'     => 'Tourfic',
            'desc'     => 'Build a complete travel booking website for hotels, tours, apartments, and car rentals — similar to Booking.com or Airbnb — with full WooCommerce integration.',
            'color'    => '#059669',
            'init'     => 'TF',
            'icon_url' => 'https://ps.w.org/tourfic/assets/icon-128x128.gif',
        ),
        array(
            'slug'     => 'beaf-before-and-after-gallery',
            'name'     => 'BEAF – Before & After Gallery',
            'desc'     => 'Showcase stunning visual transformations with responsive before-and-after image sliders and clean gallery layouts. Perfect for beauty, fitness, and renovation sites.',
            'color'    => '#e11d48',
            'init'     => 'BF',
            'icon_url' => 'https://ps.w.org/beaf-before-and-after-gallery/assets/icon-128x128.png',
        ),
        array(
            'slug'     => 'ultimate-addons-for-contact-form-7',
            'name'     => 'Ultimate Addons for Contact Form 7',
            'desc'     => 'Extend Contact Form 7 with 50+ powerful addons — advanced fields, dynamic dropdowns, conditional logic, multi-step forms, and much more.',
            'color'    => '#d97706',
            'init'     => 'UA',
            'icon_url' => 'https://ps.w.org/ultimate-addons-for-contact-form-7/assets/icon-128x128.png',
        ),
        array(
            'slug'     => 'ultra-addons-for-wpforms',
            'name'     => 'Ultra Addons for WPForms',
            'desc'     => 'Supercharge WPForms with advanced addons that give you more control, customization options, and smart form features beyond the defaults.',
            'color'    => '#0284c7',
            'init'     => 'UW',
            'icon_url' => 'https://ps.w.org/ultra-addons-for-wpforms/assets/icon-128x128.png',
        ),
    );

    // Gather installed plugins once
    if ( ! function_exists( 'get_plugins' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    $installed = get_plugins();

    // Helper: find plugin status by directory slug
    $get_status = function ( $slug ) use ( $installed ) {
        foreach ( $installed as $file => $data ) {
            if ( dirname( $file ) === $slug ) {
                return array(
                    'installed' => true,
                    'active'    => is_plugin_active( $file ),
                    'file'      => $file,
                );
            }
        }
        return array( 'installed' => false, 'active' => false, 'file' => null );
    };
    ?>
    <div class="wrap rwf-ie-wrap">
        <h1 class="wp-heading-inline"><?php esc_html_e( 'Our Plugins', 'reviewfic' ); ?></h1>
        <hr class="wp-header-end">

        <p style="color:#6b7280;font-size:14px;margin:16px 0 24px;">
            <?php esc_html_e( 'More free WordPress plugins from the Themefic team. Install directly from WordPress.org in one click.', 'reviewfic' ); ?>
        </p>

        <div class="rwf-plugins-grid">
            <?php foreach ( $plugins as $plugin ) :
                $status = $get_status( $plugin['slug'] );
            ?>
            <div class="rwf-plugin-card">
                <div class="rwf-plugin-header">
                    <div class="rwf-plugin-icon-wrap">
                        <img
                            src="<?php echo esc_url( $plugin['icon_url'] ); ?>"
                            alt="<?php echo esc_attr( $plugin['name'] ); ?>"
                            class="rwf-plugin-icon-img"
                            onerror="this.closest('.rwf-plugin-icon-wrap').classList.add('rwf-icon-fallback');"
                        >
                        <div class="rwf-plugin-icon-badge" style="background:<?php echo esc_attr( $plugin['color'] ); ?>;">
                            <?php echo esc_html( $plugin['init'] ); ?>
                        </div>
                    </div>
                    <div>
                        <h3><?php echo esc_html( $plugin['name'] ); ?></h3>
                        <a href="<?php echo esc_url( 'https://wordpress.org/plugins/' . $plugin['slug'] . '/' ); ?>" target="_blank" rel="noopener" class="rwf-plugin-wporg">
                            wordpress.org
                            <span class="dashicons dashicons-external"></span>
                        </a>
                    </div>
                </div>

                <p class="rwf-plugin-desc"><?php echo esc_html( $plugin['desc'] ); ?></p>

                <div class="rwf-plugin-footer">
                    <?php if ( $status['active'] ) : ?>
                        <span class="rwf-plugin-status rwf-status-active">
                            <span class="dashicons dashicons-yes-alt"></span>
                            <?php esc_html_e( 'Active', 'reviewfic' ); ?>
                        </span>
                    <?php elseif ( $status['installed'] ) : ?>
                        <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'plugins.php?action=activate&plugin=' . urlencode( $status['file'] ) ), 'activate-plugin_' . $status['file'] ) ); ?>" class="rwf-plugin-btn rwf-btn-activate">
                            <span class="dashicons dashicons-controls-play"></span>
                            <?php esc_html_e( 'Activate', 'reviewfic' ); ?>
                        </a>
                    <?php elseif ( current_user_can( 'install_plugins' ) ) : ?>
                        <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'update.php?action=install-plugin&plugin=' . urlencode( $plugin['slug'] ) ), 'install-plugin_' . $plugin['slug'] ) ); ?>" class="rwf-plugin-btn rwf-btn-install">
                            <span class="dashicons dashicons-download"></span>
                            <?php esc_html_e( 'Install Now', 'reviewfic' ); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php
}
