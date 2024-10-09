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

// Add CSS for reviews
function reviewfic_enqueue_styles() {
    // Enqueue Font Awesome with a specific version
    wp_enqueue_style( 
        'font-awesome', 
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css', 
        array(),  // Dependencies (none in this case)
        '5.15.4'  // Version number
    );
    
    // Enqueue custom stylesheet with dynamic version based on file modification time to avoid caching issues
    $css_file = plugin_dir_path(__FILE__) . 'css/reviewfic.css';
    wp_enqueue_style( 
        'reviewfic-style', 
        plugin_dir_url(__FILE__) . 'css/reviewfic.css', 
        array(),  // Dependencies (none in this case)
        filemtime($css_file)  // Use file modification time as the version number
    );
}
add_action('wp_enqueue_scripts', 'reviewfic_enqueue_styles');

// Include admin files
require_once plugin_dir_path(__FILE__) . 'admin/post-types-taxonomy.php';
require_once plugin_dir_path(__FILE__) . 'admin/meta-boxes.php';
require_once plugin_dir_path(__FILE__) . 'admin/shortcode-generator.php';
require_once plugin_dir_path(__FILE__) . 'admin/shortcode.php';