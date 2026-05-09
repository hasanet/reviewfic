<?php
if (!defined('ABSPATH')) exit;

function reviewfic_shortcode($atts) {
    static $slider_id   = 0;
    static $grid_id     = 0;

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
        // Pagination
        'pagination'   => 'no',
        'per_page'     => 6,
    ), $atts, 'reviewfic');

    $config_id = intval($atts['id']);

    // ── Load from config post if ID provided ──────────────────
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
            $atts['pagination']   = $get('rwf_pagination',   'no');
            $atts['per_page']     = $get('rwf_per_page',     6);
        }
    }

    $template      = in_array($atts['template'], array('1','2','3','4','5','6','7','8','9','10'), true) ? $atts['template'] : '1';
    $use_slider    = $atts['slider'] === 'yes';
    $use_pagination= !$use_slider && $atts['pagination'] === 'yes';
    $per_page      = max(1, intval($atts['per_page']));
    $known_sources = array('google','trustpilot','g2','capterra','facebook','yelp','amazon');

    // ── Design CSS variables ──────────────────────────────────
    $design_style = '';
    if ($config_id > 0) {
        $vars = array();
        $card_bg      = get_post_meta($config_id, 'rwf_card_bg', true);
        $text_color   = get_post_meta($config_id, 'rwf_text_color', true);
        $star_color   = get_post_meta($config_id, 'rwf_star_color', true);
        $accent_color = get_post_meta($config_id, 'rwf_accent_color', true);
        $meta_color   = get_post_meta($config_id, 'rwf_meta_color', true);
        $name_color   = get_post_meta($config_id, 'rwf_name_color', true);
        $card_border  = get_post_meta($config_id, 'rwf_card_border', true);
        $card_shadow  = get_post_meta($config_id, 'rwf_card_shadow', true);
        $card_radius  = get_post_meta($config_id, 'rwf_card_radius', true);
        $col_gap      = get_post_meta($config_id, 'rwf_col_gap',     true);

        $shadow_map = array(
            'sm' => '0 1px 4px rgba(0,0,0,.08)',
            'md' => '0 4px 16px rgba(0,0,0,.12)',
            'lg' => '0 8px 32px rgba(0,0,0,.18)',
        );

        if ($card_bg)            $vars[] = '--rwf-card-bg:'      . esc_attr($card_bg);
        if ($text_color)         $vars[] = '--rwf-text-color:'   . esc_attr($text_color);
        if ($star_color)         $vars[] = '--rwf-star-color:'   . esc_attr($star_color);
        if ($accent_color)       $vars[] = '--rwf-accent-color:' . esc_attr($accent_color);
        if ($meta_color)         $vars[] = '--rwf-meta-color:'   . esc_attr($meta_color);
        if ($name_color)         $vars[] = '--rwf-name-color:'   . esc_attr($name_color);
        if ($card_border)        $vars[] = '--rwf-card-border:'  . esc_attr($card_border);
        if (!empty($shadow_map[$card_shadow])) $vars[] = '--rwf-card-shadow:' . $shadow_map[$card_shadow];
        if ($card_radius !== '') $vars[] = '--rwf-card-radius:' . intval($card_radius) . 'px';
        if ($col_gap      !== '') $vars[] = '--rwf-col-gap:'     . intval($col_gap)      . 'px';

        if ($vars) $design_style = ' style="' . implode(';', $vars) . '"';
    }

    // ── Pagination setup ──────────────────────────────────────
    $page_key    = 'rwf_p_' . ($config_id > 0 ? $config_id : abs(crc32(serialize($atts))));
    $current_page = $use_pagination ? max(1, intval(isset($_GET[$page_key]) ? $_GET[$page_key] : 1)) : 1;

    // ── Query ─────────────────────────────────────────────────
    $args = array(
        'post_type'      => 'reviewfic_reviews',
        'posts_per_page' => $use_pagination ? $per_page : intval($atts['max_items']),
    );

    if ($use_pagination) {
        $args['offset'] = ($current_page - 1) * $per_page;
    }

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

    // Total count for pagination
    $total_pages = 1;
    if ($use_pagination) {
        $count_args                    = $args;
        $count_args['posts_per_page']  = -1;
        $count_args['fields']          = 'ids';
        unset($count_args['offset']);
        $count_query  = new WP_Query($count_args);
        $total_posts  = $count_query->found_posts;
        $total_pages  = $total_posts > 0 ? ceil($total_posts / $per_page) : 1;
        wp_reset_postdata();
    }

    $query = new WP_Query($args);

    // ── Wrapper ───────────────────────────────────────────────
    if ($use_slider) {
        $slider_id++;
        $output = sprintf(
            '<div class="reviewfic-slider" id="reviewfic-slider-%d" data-nav="%s" data-dots="%s" data-auto="%s" data-speed="%d" data-loop="%s" data-pause="%s" data-columns="%d"%s>',
            $slider_id,
            $atts['slider_nav']   === 'yes' ? 'yes' : 'no',
            $atts['slider_dots']  === 'yes' ? 'yes' : 'no',
            $atts['slider_auto']  === 'yes' ? 'yes' : 'no',
            intval($atts['slider_speed']),
            $atts['slider_loop']  === 'yes' ? 'yes' : 'no',
            $atts['slider_pause'] === 'yes' ? 'yes' : 'no',
            max(1, intval($atts['columns'])),
            $design_style
        );
        $output .= '<div class="reviewfic-slider-track">';
    } else {
        $grid_id++;
        $output = '<div class="reviewfic-columns reviewfic-columns-' . esc_attr($atts['columns']) . '"' . $design_style . '>';
    }

    // ── Items ─────────────────────────────────────────────────
    while ($query->have_posts()) : $query->the_post();
        $post_id            = get_the_ID();
        $stars              = get_post_meta($post_id, 'reviewfic_review_stars', true);
        $client_name        = get_post_meta($post_id, 'reviewfic_client_name', true);
        $client_designation = get_post_meta($post_id, 'reviewfic_client_designation', true);
        $client_company     = get_post_meta($post_id, 'reviewfic_client_company', true);
        $reviewer_photo     = get_post_meta($post_id, 'reviewfic_reviewer_photo', true);

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
        $star_html = '';
        $whole     = floor((float) $stars);
        $dec       = (float) $stars - $whole;
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

        // Avatar
        $avatar_markup = '';
        if ($atts['show_avatar'] === 'yes' && !empty($reviewer_photo)) {
            $img = wp_get_attachment_image($reviewer_photo, 'thumbnail', false, array('class' => 'reviewfic-avatar-img', 'alt' => esc_attr($client_name)));
            if ($img) $avatar_markup = '<div class="reviewfic-avatar">' . $img . '</div>';
        }

        // Client meta: designation · company
        $meta_parts  = array_filter(array($client_designation, $client_company));
        $client_meta = !empty($meta_parts)
            ? '<span class="reviewfic-client-meta">' . esc_html(implode(' · ', $meta_parts)) . '</span>'
            : '';

        $client_markup = '<div class="reviewfic-client-row">'
            . $avatar_markup
            . '<div class="reviewfic-client-info">'
            . '<span class="reviewfic-client-name">' . esc_html($client_name) . '</span>'
            . $client_meta
            . '</div></div>';

        // Render by template
        $output .= '<div class="reviewfic-item reviewfic-template-' . $template . '">';
        switch ($template) {
            case '2':
                $output .= $client_markup;
                $output .= '<div class="reviewfic-content reviewfic-quote">' . get_the_content() . '</div>';
                $output .= $stars_markup;
                $output .= $badge_markup;
                break;
            case '3':
                $output .= $stars_markup;
                $output .= '<div class="reviewfic-content">' . get_the_content() . '</div>';
                $output .= $badge_markup;
                $output .= $client_markup;
                break;
            case '4':
                $output .= $badge_markup;
                $output .= $stars_markup;
                $output .= '<h3 class="reviewfic-title">' . get_the_title() . '</h3>';
                $output .= '<div class="reviewfic-content">' . get_the_content() . '</div>';
                $output .= $client_markup;
                break;
            case '5':
                $output .= $client_markup;
                $output .= $stars_markup;
                $output .= '<div class="reviewfic-content reviewfic-quote">' . get_the_content() . '</div>';
                $output .= $badge_markup;
                break;

            // ── Template 6: Horizontal Split ─────────────────────────────
            // Coloured left panel (avatar + name + meta + badge) | white right (stars + title + content)
            case '6':
                $t6_avatar = '';
                if ( $atts['show_avatar'] === 'yes' && ! empty( $reviewer_photo ) ) {
                    $t6_img = wp_get_attachment_image(
                        $reviewer_photo, 'thumbnail', false,
                        array( 'class' => 'rwf-t6-photo', 'alt' => esc_attr( $client_name ) )
                    );
                    if ( $t6_img ) $t6_avatar = $t6_img;
                }
                $t6_initials = '<div class="rwf-t6-initials">' . esc_html( mb_substr( $client_name, 0, 1 ) ) . '</div>';

                $output .= '<div class="rwf-t6-left">';
                $output .= $t6_avatar ?: $t6_initials;
                $output .= '<span class="rwf-t6-name">' . esc_html( $client_name ) . '</span>';
                if ( $client_meta ) $output .= $client_meta;
                if ( $badge_markup ) $output .= $badge_markup;
                $output .= '</div>';

                $output .= '<div class="rwf-t6-right">';
                $output .= $stars_markup;
                if ( get_the_title() ) $output .= '<h3 class="reviewfic-title">' . get_the_title() . '</h3>';
                $output .= '<div class="reviewfic-content">' . get_the_content() . '</div>';
                $output .= '</div>';
                break;

            // ── Template 7: Gradient Glow ────────────────────────────────
            // White card with green box-shadow border, italic content, avatar footer row
            case '7':
                $output .= '<div class="rwf-t7-top">';
                $output .= $stars_markup;
                if ( $badge_markup ) $output .= $badge_markup;
                $output .= '</div>';
                $output .= '<div class="rwf-t7-quote-mark">&ldquo;</div>';
                $output .= '<div class="reviewfic-content">' . get_the_content() . '</div>';
                $output .= '<div class="rwf-t7-footer">';
                if ( $avatar_markup ) $output .= $avatar_markup;
                $output .= '<div class="rwf-t7-client">';
                $output .= '<span class="reviewfic-client-name">' . esc_html( $client_name ) . '</span>';
                if ( $client_meta ) $output .= $client_meta;
                $output .= '</div>';
                $output .= '</div>';
                break;

            // ── Template 8: Score Card ───────────────────────────────────
            // Big numerical rating bubble (absolute top-right) + badge + stars + title + content + client
            case '8':
                $score_display = number_format( floatval( $stars ), 1 );
                $output .= '<div class="rwf-t8-score-bubble" aria-label="' . esc_attr( $score_display ) . ' out of 5">' . esc_html( $score_display ) . '</div>';
                if ( $badge_markup ) $output .= $badge_markup;
                $output .= $stars_markup;
                if ( get_the_title() ) $output .= '<h3 class="reviewfic-title">' . get_the_title() . '</h3>';
                $output .= '<div class="reviewfic-content">' . get_the_content() . '</div>';
                $output .= $client_markup;
                break;

            // ── Template 9: Magazine / Pull Quote ───────────────────────
            // Editorial style: no card, large opening quote, bold italic content, byline at bottom
            case '9':
                $output .= '<div class="rwf-t9-quote-mark" aria-hidden="true">&ldquo;</div>';
                $output .= '<div class="reviewfic-content rwf-t9-content">' . get_the_content() . '</div>';
                $output .= '<div class="rwf-t9-byline">';
                $output .= '<div class="rwf-t9-author">';
                if ( $avatar_markup ) $output .= $avatar_markup;
                $output .= '<div>';
                $output .= '<span class="reviewfic-client-name">' . esc_html( $client_name ) . '</span>';
                if ( $client_meta ) $output .= $client_meta;
                $output .= '</div></div>';
                $output .= '<div class="rwf-t9-right">';
                $output .= $stars_markup;
                if ( $badge_markup ) $output .= $badge_markup;
                $output .= '</div>';
                $output .= '</div>';
                break;

            // ── Template 10: Neon Dark Gradient ─────────────────────────
            // Deep dark card, teal-to-purple gradient header strip, white content, stars + badge footer
            case '10':
                $output .= '<div class="rwf-t10-header">';
                if ( $avatar_markup ) $output .= $avatar_markup;
                $output .= '<div class="rwf-t10-client">';
                $output .= '<span class="rwf-t10-name">' . esc_html( $client_name ) . '</span>';
                if ( $client_meta ) $output .= $client_meta;
                $output .= '</div></div>';
                $output .= '<div class="reviewfic-content">' . get_the_content() . '</div>';
                $output .= '<div class="rwf-t10-footer">';
                $output .= $stars_markup;
                if ( $badge_markup ) $output .= $badge_markup;
                $output .= '</div>';
                break;
            default:
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
        $output .= '</div></div>';
    } else {
        $output .= '</div>';
    }

    // ── Pagination ────────────────────────────────────────────
    if ($use_pagination && $total_pages > 1) {
        $output .= '<div class="reviewfic-pagination">';

        if ($current_page > 1) {
            $output .= '<a href="' . esc_url(add_query_arg($page_key, $current_page - 1)) . '" class="rwf-page-btn rwf-page-prev">&laquo;</a>';
        }

        $range = 2;
        for ($p = 1; $p <= $total_pages; $p++) {
            if ($p === 1 || $p === $total_pages || ($p >= $current_page - $range && $p <= $current_page + $range)) {
                $active = $p === $current_page ? ' active' : '';
                $output .= '<a href="' . esc_url(add_query_arg($page_key, $p)) . '" class="rwf-page-btn' . $active . '">' . $p . '</a>';
            } elseif ($p === $current_page - $range - 1 || $p === $current_page + $range + 1) {
                $output .= '<span class="rwf-page-ellipsis">&hellip;</span>';
            }
        }

        if ($current_page < $total_pages) {
            $output .= '<a href="' . esc_url(add_query_arg($page_key, $current_page + 1)) . '" class="rwf-page-btn rwf-page-next">&raquo;</a>';
        }

        $output .= '</div>';
    }

    return $output;
}
add_shortcode('reviewfic', 'reviewfic_shortcode');
