<?php
if (!defined('ABSPATH')) exit;

function reviewfic_shortcode($atts) {
    static $slider_id = 0;

    $atts = shortcode_atts(array(
        'id'           => 0,
        // Display
        'template'     => '1',
        'columns'      => 1,
        'max_items'    => -1,
        'show_avatar'  => 'yes',
        // Filter
        'category'     => 'all',
        'source'       => 'all',
        // Slider
        'slider'       => 'no',
        'slider_nav'   => 'yes',
        'slider_dots'  => 'yes',
        'slider_auto'  => 'no',
        'slider_speed' => '4000',
        'slider_loop'  => 'yes',
        'slider_pause' => 'yes',
    ), $atts, 'reviewfic');

    // ── Load from saved config if ID provided ─────────────────
    $config_id = intval($atts['id']);
    if ($config_id > 0) {
        $config = get_post($config_id);
        if ($config && $config->post_type === 'reviewfic_config' && $config->post_status === 'publish') {
            $get = function($key, $fallback) use ($config_id) {
                $v = get_post_meta($config_id, $key, true);
                return ($v !== '' && $v !== false) ? $v : $fallback;
            };
            $atts['template']     = $get('rwf_template',     '1');
            $atts['columns']      = $get('rwf_columns',      1);
            $atts['max_items']    = $get('rwf_max_items',    -1) ?: -1;
            $atts['show_avatar']  = $get('rwf_show_avatar',  'yes');
            $atts['category']     = $get('rwf_category',     'all');
            $atts['source']       = $get('rwf_source',       'all');
            $atts['slider']       = $get('rwf_slider',       'no');
            $atts['slider_nav']   = $get('rwf_slider_nav',   'yes');
            $atts['slider_dots']  = $get('rwf_slider_dots',  'yes');
            $atts['slider_auto']  = $get('rwf_slider_auto',  'no');
            $atts['slider_speed'] = $get('rwf_slider_speed', '4000');
            $atts['slider_loop']  = $get('rwf_slider_loop',  'yes');
            $atts['slider_pause'] = $get('rwf_slider_pause', 'yes');
        }
    }

    $template      = in_array($atts['template'], array('1','2','3','4','5'), true) ? $atts['template'] : '1';
    $use_slider    = $atts['slider'] === 'yes';
    $known_sources = array('google','trustpilot','g2','capterra','facebook','yelp','amazon');

    // ── Query ─────────────────────────────────────────────────
    $args = array(
        'post_type'      => 'reviewfic_reviews',
        'posts_per_page' => intval($atts['max_items']),
    );

    $tax_queries = array();
    if ($atts['category'] !== 'all') {
        $tax_queries[] = array('taxonomy' => 'reviewfic_category', 'field' => 'slug', 'terms' => sanitize_text_field($atts['category']));
    }
    if (!empty($atts['source']) && $atts['source'] !== 'all') {
        $tax_queries[] = array('taxonomy' => 'reviewfic_source', 'field' => 'slug', 'terms' => sanitize_text_field($atts['source']));
    }
    if (!empty($tax_queries)) {
        if (count($tax_queries) > 1) $tax_queries['relation'] = 'AND';
        $args['tax_query'] = $tax_queries;
    }

    $query = new WP_Query($args);

    // ── Wrapper ───────────────────────────────────────────────
    if ($use_slider) {
        $slider_id++;
        $output = sprintf(
            '<div class="reviewfic-slider" id="reviewfic-slider-%d" data-nav="%s" data-dots="%s" data-auto="%s" data-speed="%d" data-loop="%s" data-pause="%s">',
            $slider_id,
            $atts['slider_nav']   === 'yes' ? 'yes' : 'no',
            $atts['slider_dots']  === 'yes' ? 'yes' : 'no',
            $atts['slider_auto']  === 'yes' ? 'yes' : 'no',
            intval($atts['slider_speed']),
            $atts['slider_loop']  === 'yes' ? 'yes' : 'no',
            $atts['slider_pause'] === 'yes' ? 'yes' : 'no'
        );
        $output .= '<div class="reviewfic-slider-track">';
    } else {
        $output = '<div class="reviewfic-columns reviewfic-columns-' . esc_attr($atts['columns']) . '">';
    }

    // ── Items ─────────────────────────────────────────────────
    while ($query->have_posts()) : $query->the_post();
        $post_id        = get_the_ID();
        $stars          = get_post_meta($post_id, 'reviewfic_review_stars', true);
        $client_name    = get_post_meta($post_id, 'reviewfic_client_name', true);
        $client_company = get_post_meta($post_id, 'reviewfic_client_company', true);
        $reviewer_photo = get_post_meta($post_id, 'reviewfic_reviewer_photo', true);

        $source_terms  = wp_get_post_terms($post_id, 'reviewfic_source');
        $review_source = (!empty($source_terms) && !is_wp_error($source_terms)) ? $source_terms[0] : null;

        // Stars
        if (!function_exists('reviewfic_get_star_svg')) {
            function reviewfic_get_star_svg($type = 'full') {
                $map  = array('full' => 'star-solid.svg', 'half' => 'star-half-stroke-solid.svg', 'empty' => 'star-regular.svg');
                $file = plugin_dir_path(__FILE__) . 'assets/img/' . ($map[$type] ?? 'star-regular.svg');
                return file_exists($file) ? file_get_contents($file) : '';
            }
        }
        $star_html    = '';
        $whole        = floor((float) $stars);
        $dec          = (float) $stars - $whole;
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $whole) $star_html .= reviewfic_get_star_svg('full');
            elseif ($i == $whole + 1 && $dec >= 0.5) $star_html .= reviewfic_get_star_svg('half');
            else $star_html .= reviewfic_get_star_svg('empty');
        }
        $stars_markup = '<p class="reviewfic-stars">' . $star_html . '<span class="reviewfic-star-score">(' . esc_html($stars) . ')</span></p>';

        // Source badge
        $badge_markup = '';
        if ($review_source) {
            $slug      = $review_source->slug;
            $css_class = in_array($slug, $known_sources, true) ? 'reviewfic-source-' . $slug : 'reviewfic-source-custom';
            $badge_markup = '<span class="reviewfic-source-badge ' . esc_attr($css_class) . '">' . esc_html($review_source->name) . '</span>';
        }

        // Avatar + client
        $avatar_markup = '';
        if ($atts['show_avatar'] === 'yes' && !empty($reviewer_photo)) {
            $img = wp_get_attachment_image($reviewer_photo, 'thumbnail', false, array('class' => 'reviewfic-avatar-img', 'alt' => esc_attr($client_name)));
            if ($img) $avatar_markup = '<div class="reviewfic-avatar">' . $img . '</div>';
        }
        $client_markup = '<div class="reviewfic-client-row">'
            . $avatar_markup
            . '<div class="reviewfic-client-info">'
            . '<span class="reviewfic-client-name">' . esc_html($client_name) . '</span>'
            . '<span class="reviewfic-client-company">' . esc_html($client_company) . '</span>'
            . '</div></div>';

        // Render by template
        $output .= '<div class="reviewfic-item reviewfic-template-' . $template . '">';
        switch ($template) {
            case '2': // Quote
                $output .= $client_markup;
                $output .= '<div class="reviewfic-content reviewfic-quote">' . get_the_content() . '</div>';
                $output .= $stars_markup;
                $output .= $badge_markup;
                break;
            case '3': // Minimal
                $output .= $stars_markup;
                $output .= '<div class="reviewfic-content">' . get_the_content() . '</div>';
                $output .= $badge_markup;
                $output .= $client_markup;
                break;
            case '4': // Dark
                $output .= $badge_markup;
                $output .= $stars_markup;
                $output .= '<h3 class="reviewfic-title">' . get_the_title() . '</h3>';
                $output .= '<div class="reviewfic-content">' . get_the_content() . '</div>';
                $output .= $client_markup;
                break;
            case '5': // Centered
                $output .= $client_markup;
                $output .= $stars_markup;
                $output .= '<div class="reviewfic-content reviewfic-quote">' . get_the_content() . '</div>';
                $output .= $badge_markup;
                break;
            default: // Classic
                $output .= $badge_markup;
                $output .= $stars_markup;
                $output .= '<h3 class="reviewfic-title">' . get_the_title() . '</h3>';
                $output .= '<div class="reviewfic-content">' . get_the_content() . '</div>';
                $output .= $client_markup;
        }
        $output .= '</div>';
    endwhile;
    wp_reset_postdata();

    // ── Close wrapper ─────────────────────────────────────────
    if ($use_slider) {
        $output .= '</div>';
        $output .= '<div class="reviewfic-slider-nav">';
        $output .= '<button class="reviewfic-slider-prev" aria-label="Previous">&#8249;</button>';
        $output .= '<div class="reviewfic-slider-dots"></div>';
        $output .= '<button class="reviewfic-slider-next" aria-label="Next">&#8250;</button>';
        $output .= '</div>';
        $output .= '</div>';
    } else {
        $output .= '</div>';
    }

    return $output;
}
add_shortcode('reviewfic', 'reviewfic_shortcode');
