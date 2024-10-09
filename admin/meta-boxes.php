<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Add custom meta fields for reviews
function reviewfic_add_meta_boxes() {
    add_meta_box('reviewfic_meta_box', 'Review Details', 'reviewfic_meta_box_callback', 'reviewfic_reviews', 'normal', 'high');
}
add_action('add_meta_boxes', 'reviewfic_add_meta_boxes');

// Meta box callback function (including nonce field)
function reviewfic_meta_box_callback($post) {
    // Add a nonce field for security
    wp_nonce_field('reviewfic_save_meta_box_data', 'reviewfic_meta_box_nonce');

    $stars = get_post_meta($post->ID, 'reviewfic_review_stars', true);
    $client_name = get_post_meta($post->ID, 'reviewfic_client_name', true);
    $client_company = get_post_meta($post->ID, 'reviewfic_client_company', true);

    ?>
    <p>
        <label for="reviewfic_review_stars">Review Stars:</label>
        <input type="number" name="reviewfic_review_stars" id="reviewfic_review_stars" value="<?php echo esc_attr($stars); ?>" step="0.1" max="5" min="1" />
    </p>
    <p>
        <label for="reviewfic_client_name">Client Name:</label>
        <input type="text" name="reviewfic_client_name" id="reviewfic_client_name" value="<?php echo esc_attr($client_name); ?>" />
    </p>
    <p>
        <label for="reviewfic_client_company">Client Company:</label>
        <input type="text" name="reviewfic_client_company" id="reviewfic_client_company" value="<?php echo esc_attr($client_company); ?>" />
    </p>
    <?php
}

// Save meta box data (with nonce verification and unslash before sanitization)
function reviewfic_save_meta_boxes($post_id) {
    // Check if nonce is set
    if (!isset($_POST['reviewfic_meta_box_nonce'])) {
        return;
    }

    // Unslash and sanitize nonce before verification
    $nonce = sanitize_text_field(wp_unslash($_POST['reviewfic_meta_box_nonce']));

    // Verify nonce
    if (!wp_verify_nonce($nonce, 'reviewfic_save_meta_box_data')) {
        return;
    }

    // Check for autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check user permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Sanitize and save review stars
    if (isset($_POST['reviewfic_review_stars'])) {
        $review_stars = sanitize_text_field(wp_unslash($_POST['reviewfic_review_stars']));
        update_post_meta($post_id, 'reviewfic_review_stars', $review_stars);
    }

    // Sanitize and save client name
    if (isset($_POST['reviewfic_client_name'])) {
        $client_name = sanitize_text_field(wp_unslash($_POST['reviewfic_client_name']));
        update_post_meta($post_id, 'reviewfic_client_name', $client_name);
    }

    // Sanitize and save client company
    if (isset($_POST['reviewfic_client_company'])) {
        $client_company = sanitize_text_field(wp_unslash($_POST['reviewfic_client_company']));
        update_post_meta($post_id, 'reviewfic_client_company', $client_company);
    }
}
add_action('save_post', 'reviewfic_save_meta_boxes');