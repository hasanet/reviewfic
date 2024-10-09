<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Add a submenu for the shortcode generator under "Reviews"
function reviewfic_add_shortcode_submenu() {
    add_submenu_page(
        'edit.php?post_type=reviewfic_reviews', // Parent slug (Reviews menu)
        'Shortcode Generator',        // Page title
        'Shortcode Generator',        // Menu title
        'manage_options',             // Capability
        'reviewfic_shortcode_generator', // Menu slug
        'reviewfic_shortcode_generator_page' // Callback function
    );
}
add_action('admin_menu', 'reviewfic_add_shortcode_submenu');

// Display the shortcode generator page
function reviewfic_shortcode_generator_page() {
    ?>
    <div class="wrap">
        <h1>Reviewfic Shortcode Generator</h1>
        <p>Use this form to generate a shortcode for displaying reviews.</p>

        <form id="reviewfic-shortcode-form">
            <label for="reviewfic-category">Category:</label>
            <select id="reviewfic-category" name="reviewfic-category">
                <option value="all">-Select category-</option>
                <?php
                $categories = get_terms(array('taxonomy' => 'reviewfic_category', 'hide_empty' => false));
                foreach ($categories as $category) {
                    echo '<option value="' . esc_attr($category->slug) . '">' . esc_html($category->name) . '</option>';
                }
                ?>
            </select>

            <label for="reviewfic-columns">Columns:</label>
            <select id="reviewfic-columns" name="reviewfic-columns">
                <option value="1">1 Column</option>
                <option value="2">2 Columns</option>
                <option value="3">3 Columns</option>
                <option value="4">4 Columns</option>
            </select>

            <label for="reviewfic-max-items">Max Items:</label>
            <input type="number" id="reviewfic-max-items" name="reviewfic-max-items" value="Unlimited" placeholder="Unlimited">

            <button type="button" id="reviewfic-generate-shortcode" class="button button-primary">Generate Shortcode</button>
        </form>

        <h3>Generated Shortcode:</h3>
        <input type="text" id="reviewfic-shortcode-result" readonly style="width: 100%;" />
    </div>
    <?php
}