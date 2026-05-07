<?php
/*
Plugin Name: Reviewfic  – The Ultimate Testimonial Slider, Carousel, Grid Plugin
Plugin URI: https://themefic.com/reviewfic/
Description: A plugin to create and manage client reviews with custom post types and shortcodes.
Version: 1.2.9
Author: Themefic
Author URI: https://themefic.com
Text Domain: reviewfic
Domain Path: /languages
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Tested up to: 6.8
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

    // Load admin CSS on review edits and config edits
    if (($on_review_edit || $on_config_edit) && file_exists($admin_css_file)) {
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
