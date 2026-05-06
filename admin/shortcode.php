<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Shortcode to display reviews
function reviewfic_shortcode($atts) {
    $atts = shortcode_atts(array(
        'category'    => 'all',
        'columns'     => 1,
        'max_items'   => -1,
        'show_avatar' => 'yes',
        'source'      => 'all',
    ), $atts, 'reviewfic');

    $args = array(
        'post_type'      => 'reviewfic_reviews',
        'posts_per_page' => intval($atts['max_items']),
    );

    // Build tax_query — supports both category and source filters together
    $tax_queries = array();

    if ($atts['category'] !== 'all') {
        $tax_queries[] = array(
            'taxonomy' => 'reviewfic_category',
            'field'    => 'slug',
            'terms'    => sanitize_text_field($atts['category']),
        );
    }

    if (!empty($atts['source']) && $atts['source'] !== 'all') {
        $tax_queries[] = array(
            'taxonomy' => 'reviewfic_source',
            'field'    => 'slug',
            'terms'    => sanitize_text_field($atts['source']),
        );
    }

    if (!empty($tax_queries)) {
        if (count($tax_queries) > 1) {
            $tax_queries['relation'] = 'AND';
        }
        $args['tax_query'] = $tax_queries;
    }

    $known_sources = array('google', 'trustpilot', 'g2', 'capterra', 'facebook', 'yelp', 'amazon');

    $query  = new WP_Query($args);
    $output = '<div class="reviewfic-columns reviewfic-columns-' . esc_attr($atts['columns']) . '">';

    while ($query->have_posts()) : $query->the_post();
        $post_id        = get_the_ID();
        $stars          = get_post_meta($post_id, 'reviewfic_review_stars', true);
        $client_name    = get_post_meta($post_id, 'reviewfic_client_name', true);
        $client_company = get_post_meta($post_id, 'reviewfic_client_company', true);
        $reviewer_photo = get_post_meta($post_id, 'reviewfic_reviewer_photo', true);

        // Get source from taxonomy
        $source_terms  = wp_get_post_terms($post_id, 'reviewfic_source');
        $review_source = (!empty($source_terms) && !is_wp_error($source_terms)) ? $source_terms[0] : null;

        $output .= '<div class="reviewfic-item">';

        // Source badge
        if ($review_source) {
            $slug      = $review_source->slug;
            $css_class = in_array($slug, $known_sources, true)
                ? 'reviewfic-source-' . $slug
                : 'reviewfic-source-custom';
            $output .= '<span class="reviewfic-source-badge ' . esc_attr($css_class) . '">' . esc_html($review_source->name) . '</span>';
        }

        // Star rating
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

        $star_output  = '';
        $whole_stars  = floor($stars);
        $decimal_part = $stars - $whole_stars;

        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $whole_stars) {
                $star_output .= get_star_svg('full');
            } elseif ($i == $whole_stars + 1 && $decimal_part >= 0.5) {
                $star_output .= get_star_svg('half');
            } else {
                $star_output .= get_star_svg('empty');
            }
        }

        $output .= '<p class="reviewfic-stars">' . $star_output . ' <span class="reviewfic-star-score">(' . esc_html($stars) . ')</span></p>';
        $output .= '<h3>' . get_the_title() . '</h3>';
        $output .= '<div class="reviewfic-content">' . get_the_content() . '</div>';

        // Avatar + client info
        $output .= '<div class="reviewfic-client-row">';

        if ($atts['show_avatar'] === 'yes' && !empty($reviewer_photo)) {
            $img = wp_get_attachment_image(
                $reviewer_photo,
                'thumbnail',
                false,
                array(
                    'class' => 'reviewfic-avatar-img',
                    'alt'   => esc_attr($client_name),
                )
            );
            if ($img) {
                $output .= '<div class="reviewfic-avatar">' . $img . '</div>';
            }
        }

        $output .= '<p class="client">' . esc_html($client_name) . ', <span class="company">' . esc_html($client_company) . '</span></p>';
        $output .= '</div>';

        $output .= '</div>';
    endwhile;

    $output .= '</div>';
    wp_reset_postdata();
    return $output;
}
add_shortcode('reviewfic', 'reviewfic_shortcode');
