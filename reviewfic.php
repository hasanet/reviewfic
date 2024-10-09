<?php
/*
Plugin Name: Reviewfic
Plugin URI: https://themefic.com/reviewfic/
Description: A plugin to create and manage client reviews with custom post types and shortcodes.
Version: 1.0
Author: Mirza Md Hasan
Author URI: https://m-hasan.com
Tested up to: 6.6
License: GPLv2 or later
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Enqueue Frontend Scripts and Styles
function reviewfic_enqueue_styles() {
    $css_file = plugin_dir_path(__FILE__) . 'assets/css/reviewfic.css';
    wp_enqueue_style( 
        'reviewfic-style', 
        plugin_dir_url(__FILE__) . 'assets/css/reviewfic.css', 
        array(),  
        filemtime($css_file)  // Version based on file modification time
    );
}
add_action('wp_enqueue_scripts', 'reviewfic_enqueue_styles');


// Enqueue Admin Scripts and Styles (if needed for admin pages)
function reviewfic_admin_enqueue($hook) {
    // Now checking for the correct hook value based on the var_dump result
    if ('reviewfic_reviews_page_reviewfic_shortcode_generator' !== $hook) {
        return;
    }

    // Enqueue custom JavaScript for shortcode generator in the admin
    $js_file = plugin_dir_path(__FILE__) . 'assets/js/reviewfic.js';
    if (file_exists($js_file)) {
        wp_enqueue_script(
            'reviewfic-js',
            plugin_dir_url(__FILE__) . 'assets/js/reviewfic.js',
            array(), 
            filemtime($js_file), 
            true 
        );
    }

    // Enqueue custom admin CSS if needed
    $admin_css_file = plugin_dir_path(__FILE__) . 'assets/css/reviewfic-admin.css';
    if (file_exists($admin_css_file)) {
        wp_enqueue_style( 
            'reviewfic-admin-style', 
            plugin_dir_url(__FILE__) . 'assets/css/reviewfic-admin.css', 
            array(),  
            filemtime($admin_css_file)  
        );
    }
}
add_action('admin_enqueue_scripts', 'reviewfic_admin_enqueue');


// Include admin files
require_once plugin_dir_path(__FILE__) . 'admin/post-types-taxonomy.php';
require_once plugin_dir_path(__FILE__) . 'admin/meta-boxes.php';
require_once plugin_dir_path(__FILE__) . 'admin/shortcode-generator.php';
require_once plugin_dir_path(__FILE__) . 'admin/shortcode.php';