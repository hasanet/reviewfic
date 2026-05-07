<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Register custom post type for reviews
function reviewfic_register_post_type() {
    $args = array(
        'labels' => array(
            'name'               => 'Reviews',
            'singular_name'      => 'Review',
            'add_new'            => 'Add New Review',
            'add_new_item'       => 'Add New Review',
            'edit_item'          => 'Edit Review',
            'new_item'           => 'New Review',
            'view_item'          => 'View Review',
            'view_items'         => 'View Reviews',
            'search_items'       => 'Search Reviews',
            'not_found'          => 'No reviews found.',
            'not_found_in_trash' => 'No reviews found in trash.',
            'all_items'          => 'Reviews',
            'menu_name'          => 'Reviewfic',
        ),
        'public'      => true,
        'supports'    => array('title', 'editor', 'custom-fields'),
        'has_archive' => true,
        'rewrite'     => array('slug' => 'reviewfic_reviews'),
        'menu_icon'   => 'dashicons-star-filled',
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

// Register custom taxonomy for review sources
function reviewfic_register_source_taxonomy() {
    $labels = array(
        'name'          => 'Review Sources',
        'singular_name' => 'Review Source',
        'search_items'  => 'Search Review Sources',
        'all_items'     => 'All Review Sources',
        'edit_item'     => 'Edit Review Source',
        'update_item'   => 'Update Review Source',
        'add_new_item'  => 'Add New Review Source',
        'new_item_name' => 'New Review Source Name',
        'menu_name'     => 'Review Sources',
    );
    $args = array(
        'labels'            => $labels,
        'hierarchical'      => false,
        'public'            => false,
        'show_ui'           => true,
        'show_in_menu'      => true,
        'show_admin_column' => true,
        'rewrite'           => false,
    );
    register_taxonomy('reviewfic_source', 'reviewfic_reviews', $args);
}
add_action('init', 'reviewfic_register_source_taxonomy');