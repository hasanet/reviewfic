<?php
// Add custom meta fields for reviews
function reviewfic_add_meta_boxes() {
    add_meta_box('reviewfic_meta_box', 'Review Details', 'reviewfic_meta_box_callback', 'reviews', 'normal', 'high');
}
add_action('add_meta_boxes', 'reviewfic_add_meta_boxes');

// Meta box callback function
function reviewfic_meta_box_callback($post) {
    // Add nonce for security and verification
    wp_nonce_field('reviewfic_save_meta_box_data', 'reviewfic_meta_box_nonce');
    
    $stars = get_post_meta($post->ID, 'review_stars', true);
    $client_name = get_post_meta($post->ID, 'client_name', true);
    $client_company = get_post_meta($post->ID, 'client_company', true);
    
    ?>
    <p>
        <label for="review_stars">Review Stars:</label>
        <input type="number" name="review_stars" id="review_stars" value="<?php echo esc_attr($stars); ?>" step="0.1" max="5" min="1" />
    </p>
    <p>
        <label for="client_name">Client Name:</label>
        <input type="text" name="client_name" id="client_name" value="<?php echo esc_attr($client_name); ?>" />
    </p>
    <p>
        <label for="client_company">Client Company:</label>
        <input type="text" name="client_company" id="client_company" value="<?php echo esc_attr($client_company); ?>" />
    </p>
    <?php
}

// Save meta box data
function reviewfic_save_meta_boxes($post_id) {
    // Check if nonce is set
    if (!isset($_POST['reviewfic_meta_box_nonce'])) {
        return;
    }

    // Verify that the nonce is valid
    if (isset($_POST['reviewfic_meta_box_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['reviewfic_meta_box_nonce'])), 'reviewfic_save_meta_box_data') === false) {
        return;
    }    

    // Check if it's an autosave, no need to save the data
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check the user's permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Unslash, sanitize, and save review stars
    if (isset($_POST['review_stars'])) {
        $review_stars = sanitize_text_field(wp_unslash($_POST['review_stars']));
        update_post_meta($post_id, 'review_stars', $review_stars);
    }

    // Unslash, sanitize, and save client name
    if (isset($_POST['client_name'])) {
        $client_name = sanitize_text_field(wp_unslash($_POST['client_name']));
        update_post_meta($post_id, 'client_name', $client_name);
    }

    // Unslash, sanitize, and save client company name
    if (isset($_POST['client_company'])) {
        $client_company = sanitize_text_field(wp_unslash($_POST['client_company']));
        update_post_meta($post_id, 'client_company', $client_company);
    }
}
add_action('save_post', 'reviewfic_save_meta_boxes');
