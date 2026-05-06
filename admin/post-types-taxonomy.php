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

// Register custom taxonomy for review sources
function reviewfic_register_source_taxonomy() {
    $labels = array(
        'name'              => 'Review Sources',
        'singular_name'     => 'Review Source',
        'search_items'      => 'Search Sources',
        'all_items'         => 'All Sources',
        'edit_item'         => 'Edit Source',
        'update_item'       => 'Update Source',
        'add_new_item'      => 'Add New Source',
        'new_item_name'     => 'New Source Name',
        'menu_name'         => 'Sources',
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