<?php
// Register Custom Post Type
function reviewfic_custom_post_type() {
    $args = array(
        'label' => 'Reviews',
        'public' => true,
        'supports' => array('title', 'editor'),
        'show_in_menu' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'reviews'),
        'menu_position' => 5,
        'menu_icon' => 'dashicons-format-chat',
    );
    register_post_type('reviews', $args);
}

// Register Custom Taxonomy
function reviewfic_register_taxonomy() {
    register_taxonomy(
        'review_category',
        'reviews',
        array(
            'label' => 'Review Categories',
            'hierarchical' => true,
            'public' => true,
            'rewrite' => array('slug' => 'review-category'),
        )
    );
}

// Hook both custom post type and taxonomy into 'init'
function reviewfic_init() {
    reviewfic_custom_post_type();
    reviewfic_register_taxonomy();
}
add_action('init', 'reviewfic_init');