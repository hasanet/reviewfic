<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Shortcode to display reviews
function reviewfic_shortcode($atts) {
    $atts = shortcode_atts(array(
        'category' => 'all', 
        'columns' => 1,
        'max_items' => -1, // -1 means no limit (all items)
    ), $atts, 'reviewfic');

    $args = array(
        'post_type' => 'reviewfic_reviews',
        'posts_per_page' => $atts['max_items'], // Max items to display
    );

    if ($atts['category'] != 'all') {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'reviewfic_category',
                'field' => 'slug',
                'terms' => $atts['category'],
            )
        );
    }

    $query = new WP_Query($args);
    $output = '<div class="reviewfic-columns reviewfic-columns-' . esc_attr($atts['columns']) . '">';
   
    while ($query->have_posts()) : $query->the_post();
        $stars = get_post_meta(get_the_ID(), 'reviewfic_review_stars', true);
        $client_name = get_post_meta(get_the_ID(), 'reviewfic_client_name', true);
        $client_company = get_post_meta(get_the_ID(), 'reviewfic_client_company', true);

        $output .= '<div class="reviewfic-item">';
        $output .= '<h3>' . get_the_title() . '</h3>';

        // Function to load SVG files for star icons
        if (!function_exists('get_star_svg')) {
            function get_star_svg($type = 'full') {
                if ($type === 'full') {
                    return file_get_contents(plugin_dir_path(__FILE__) . 'assets/img/star-solid.svg');
                } elseif ($type === 'half') {
                    return file_get_contents(plugin_dir_path(__FILE__) . 'assets/img/star-half-stroke-solid.svg');
                } elseif ($type === 'empty') {
                    return file_get_contents(plugin_dir_path(__FILE__) . 'assets/img/star-regular.svg');
                }
            }
        }

        // Display stars with partial support using SVGs
        $star_output = '';
        $whole_stars = floor($stars); // Get whole number of stars
        $decimal_part = $stars - $whole_stars; // Get the decimal part

        // Loop to display full, half, or empty stars
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $whole_stars) {
                $star_output .= get_star_svg('full');  // Full star SVG
            } elseif ($i == $whole_stars + 1 && $decimal_part >= 0.5) {
                $star_output .= get_star_svg('half');  // Half star SVG
            } else {
                $star_output .= get_star_svg('empty');  // Half star SVG
            }
        }

        $output .= '<p>' . $star_output . ' (' . esc_html($stars) . ')</p>';
        $output .= '<p>' . get_the_content() . '</p>';
        $output .= '<p class="client">' . esc_html($client_name) . ', <span class="company"> ' . esc_html($client_company) . '</span></p>';
        $output .= '</div>';
    endwhile;

    $output .= '</div>';
    wp_reset_postdata();
    return $output;
}
add_shortcode('reviewfic', 'reviewfic_shortcode');