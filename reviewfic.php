<?php
/*
Plugin Name: Reviewfic – Testimonial Slider, Testimonial Grid & Customer Reviews
Plugin URI: https://themefic.com/reviewfic/
Description: A plugin to create and manage client reviews with custom post types and shortcodes.
Version: 1.2.38
Author: Themefic
Author URI: https://themefic.com
Text Domain: reviewfic
Domain Path: /languages
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Tested up to: 6.9
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// On activation, pre-populate default review sources
function reviewfic_activate() {
    reviewfic_register_source_taxonomy();
    reviewfic_seed_default_sources();
}
register_activation_hook(__FILE__, 'reviewfic_activate');

// Seed default sources — also runs on admin_init as a one-time upgrade fallback
// so existing installs get the defaults without needing to deactivate/reactivate.
function reviewfic_seed_default_sources() {
    if (get_option('reviewfic_sources_seeded')) {
        return;
    }
    $defaults = array(
        'Google', 'Trustpilot', 'G2', 'Capterra',
        'Facebook', 'Yelp', 'Amazon',
    );
    foreach ($defaults as $source) {
        if (!term_exists($source, 'reviewfic_source')) {
            wp_insert_term($source, 'reviewfic_source');
        }
    }
    update_option('reviewfic_sources_seeded', true);
}
add_action('admin_init', 'reviewfic_seed_default_sources');

// Enqueue Frontend Scripts and Styles
function reviewfic_enqueue_styles() {
    $css_file = plugin_dir_path(__FILE__) . 'assets/css/reviewfic.css';
    wp_enqueue_style(
        'reviewfic-style',
        plugin_dir_url(__FILE__) . 'assets/css/reviewfic.css',
        array(),
        filemtime($css_file)
    );

    $js_file = plugin_dir_path(__FILE__) . 'assets/js/reviewfic-public.js';
    wp_enqueue_script(
        'reviewfic-frontend',
        plugin_dir_url(__FILE__) . 'assets/js/reviewfic-public.js',
        array(),
        filemtime($js_file),
        true
    );
}
add_action('wp_enqueue_scripts', 'reviewfic_enqueue_styles');


// Enqueue Admin Scripts and Styles
function reviewfic_admin_enqueue($hook) {
    global $post;

    $admin_css_file = plugin_dir_path(__FILE__) . 'assets/css/reviewfic-admin.css';
    $on_review_edit = in_array($hook, array('post.php', 'post-new.php'), true)
                      && isset($post) && $post->post_type === 'reviewfic_reviews';
    $on_config_edit = in_array($hook, array('post.php', 'post-new.php'), true)
                      && isset($post) && $post->post_type === 'reviewfic_config';

    $on_import_export = $hook === 'reviewfic_reviews_page_reviewfic-import-export';
    $on_integrations  = $hook === 'reviewfic_reviews_page_reviewfic-integrations';
    $on_get_help      = $hook === 'reviewfic_reviews_page_reviewfic-get-help';
    $on_our_plugins   = $hook === 'reviewfic_reviews_page_reviewfic-our-plugins';
    $on_live_reviews  = $hook === 'reviewfic_reviews_page_reviewfic-live-reviews';
    $on_woocommerce   = $hook === 'reviewfic_reviews_page_reviewfic-woocommerce';
    $on_tourfic       = $hook === 'reviewfic_reviews_page_reviewfic-tourfic';

    // Load admin CSS on review edits, config edits, and import/export page
    if (($on_review_edit || $on_config_edit || $on_import_export || $on_integrations || $on_get_help || $on_our_plugins || $on_live_reviews || $on_woocommerce || $on_tourfic) && file_exists($admin_css_file)) {
        wp_enqueue_style(
            'reviewfic-admin-style',
            plugin_dir_url(__FILE__) . 'assets/css/reviewfic-admin.css',
            array(),
            filemtime($admin_css_file)
        );
    }

    // Media uploader — review post edit screen only
    if ($on_review_edit) {
        wp_enqueue_media();
    }

    // Color picker — config edit screen
    if ($on_config_edit) {
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
    }

    // Shortcode generator JS — config edit screen only
    if ($on_config_edit) {
        $js_file = plugin_dir_path(__FILE__) . 'assets/js/reviewfic.js';
        if (file_exists($js_file)) {
            wp_enqueue_script(
                'reviewfic-js',
                plugin_dir_url(__FILE__) . 'assets/js/reviewfic.js',
                array('jquery'),
                filemtime($js_file),
                true
            );
        }
    }
}
add_action('admin_enqueue_scripts', 'reviewfic_admin_enqueue');


// Include admin files
require_once plugin_dir_path(__FILE__) . 'admin/post-types-taxonomy.php';
require_once plugin_dir_path(__FILE__) . 'admin/meta-boxes.php';
require_once plugin_dir_path(__FILE__) . 'admin/shortcode-config.php';
require_once plugin_dir_path(__FILE__) . 'admin/admin-brand.php';
require_once plugin_dir_path(__FILE__) . 'admin/shortcode.php';
require_once plugin_dir_path(__FILE__) . 'admin/review-form.php';
require_once plugin_dir_path(__FILE__) . 'admin/import-export.php';
require_once plugin_dir_path(__FILE__) . 'admin/extra-pages.php';
require_once plugin_dir_path(__FILE__) . 'admin/live-reviews.php';
require_once plugin_dir_path(__FILE__) . 'admin/woocommerce.php';
require_once plugin_dir_path(__FILE__) . 'admin/tourfic.php';
require_once plugin_dir_path(__FILE__) . 'admin/form-integrations.php';
// ── Submenu reorder ────────────────────────────────────────────────────────
// Runs last so all submenus are registered before we sort.
// "Get Help" and "Our Plugins" are always pinned to the end.
add_action( 'admin_menu', function () {
    global $submenu;

    $key = 'edit.php?post_type=reviewfic_reviews';
    if ( empty( $submenu[ $key ] ) ) return;

    $known_order = array(
        'edit.php?post_type=reviewfic_reviews',
        'post-new.php?post_type=reviewfic_reviews',
        'edit-tags.php?taxonomy=reviewfic_category&post_type=reviewfic_reviews',
        'edit-tags.php?taxonomy=reviewfic_source&post_type=reviewfic_reviews',
        'edit.php?post_type=reviewfic_config',
        'reviewfic-live-reviews',
        'reviewfic-integrations',
        'reviewfic-import-export',
        'reviewfic-woocommerce',
        'reviewfic-tourfic',
    );
    $pinned_last = array( 'reviewfic-get-help', 'reviewfic-our-plugins' );

    // Index existing items by slug
    $by_slug = array();
    foreach ( $submenu[ $key ] as $item ) {
        $by_slug[ $item[2] ] = $item;
    }

    $sorted = array();

    // 1. Known ordered items first
    foreach ( $known_order as $slug ) {
        if ( isset( $by_slug[ $slug ] ) ) {
            $sorted[] = $by_slug[ $slug ];
            unset( $by_slug[ $slug ] );
        }
    }

    // 2. Any unknown items (future additions) come before the pinned last
    foreach ( $by_slug as $slug => $item ) {
        if ( ! in_array( $slug, $pinned_last, true ) ) {
            $sorted[] = $item;
            unset( $by_slug[ $slug ] );
        }
    }

    // 3. Pinned last — always at the end
    foreach ( $pinned_last as $slug ) {
        if ( isset( $by_slug[ $slug ] ) ) {
            $sorted[] = $by_slug[ $slug ];
        }
    }

    $submenu[ $key ] = $sorted;
}, 9999 );
