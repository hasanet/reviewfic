<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Register custom post type for reviews
function reviewfic_register_post_type() {
    $args = array(
        'public' => true,
        'label'  => 'Reviews',
        'supports' => array('title', 'editor', 'custom-fields'),
        'has_archive' => true,
        'rewrite' => array('slug' => 'reviewfic_reviews'),
    );
    register_post_type('reviewfic_reviews', $args);
}
add_action('init', 'reviewfic_register_post_type');

// Register custom taxonomy for reviews
function reviewfic_register_taxonomy() {
    $args = array(
        'label' => 'Review Categories',
        'hierarchical' => true,
        'public' => true,
        'rewrite' => array('slug' => 'reviewfic-category'),
    );
    register_taxonomy('reviewfic_category', 'reviewfic_reviews', $args);
}
add_action('init', 'reviewfic_register_taxonomy');