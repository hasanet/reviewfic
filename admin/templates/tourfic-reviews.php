<?php
/**
 * Reviewfic — Tourfic Review Section Template
 *
 * Returned by the `comments_template` filter (priority 20) when
 * "Replace the Tourfic review section" is enabled. Renders Reviewfic
 * review cards for the current hotel / tour / apartment listing,
 * followed by a "Write a Review" CTA that links to the configured
 * Reviewfic review landing page.
 *
 * This file is included by WordPress inside a proper post context
 * (via the comments_template() call in the Tourfic template), so
 * $post and $comments are available.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

global $post;
if ( ! $post ) return;

$s              = rwf_tf_get_settings();
$review_page_id = intval( $s['review_page'] );

// Pull approved comments for this listing using the same args Tourfic uses
$comments = get_comments( array(
    'post_id' => $post->ID,
    'status'  => 'approve',
    'order'   => 'DESC',
) );

// ── Render review cards via Reviewfic ──────────────────────────────────────
echo '<div class="rwf-tf-reviews-wrap">';

if ( empty( $comments ) ) {
    echo '<p class="rwf-tf-no-reviews">' . esc_html__( 'No reviews yet. Be the first to leave one!', 'reviewfic' ) . '</p>';
} else {
    $reviews = array();
    foreach ( $comments as $comment ) {

        // Tourfic stores sub-ratings in `tf_comment_meta` (associative array).
        // Fall back to `tf_total_ratings` (pre-computed average) if present.
        $total_rating = floatval( get_comment_meta( $comment->comment_ID, 'tf_total_ratings', true ) );
        if ( ! $total_rating ) {
            $sub_ratings = get_comment_meta( $comment->comment_ID, 'tf_comment_meta', true );
            if ( is_array( $sub_ratings ) && ! empty( $sub_ratings ) ) {
                $values = array_filter( array_map( 'floatval', $sub_ratings ) );
                $total_rating = ! empty( $values ) ? array_sum( $values ) / count( $values ) : 5.0;
            } else {
                $total_rating = 5.0;
            }
        }

        // Clamp to 1–5
        $stars = max( 1, min( 5, round( $total_rating ) ) );

        $reviews[] = array(
            'title'   => '',
            'content' => $comment->comment_content,
            'stars'   => $stars,
            'name'    => $comment->comment_author,
            'meta'    => '',
            'avatar'  => get_avatar_url( $comment->comment_author_email, array( 'size' => 96 ) ),
            'time'    => human_time_diff( strtotime( $comment->comment_date ), current_time( 'timestamp' ) ) . ' ' . __( 'ago', 'reviewfic' ),
        );
    }

    if ( function_exists( 'rwf_render_live_cards' ) ) {
        $live_atts = array(
            'id'           => intval( $s['display_config_id'] ?? 0 ),
            'columns'      => $s['display_columns'],
            'template'    => $s['display_template'],
            'slider'      => $s['display_slider'],
            'show_avatar' => $s['display_show_avatar'],
            'slider_nav'   => 'yes',
            'slider_dots'  => 'yes',
            'slider_auto'  => 'no',
            'slider_speed' => '4000',
            'slider_loop'  => 'yes',
            'slider_pause' => 'yes',
            'pagination'   => 'no',
            'per_page'     => 6,
        );
        if ( function_exists( 'rwf_resolve_live_display_atts' ) ) {
            $live_atts = rwf_resolve_live_display_atts( $live_atts );
        }
        echo rwf_render_live_cards( $reviews, $live_atts, 'custom', get_bloginfo( 'name' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
    }
}

// ── Write a Review CTA ────────────────────────────────────────────────────
if ( $review_page_id ) {
    $url = add_query_arg( array(
        'rwf_tf_service' => $post->ID,
        'rwf_tf_type'    => rwf_tf_type_from_post_type( $post->post_type ),
    ), get_permalink( $review_page_id ) );
    ?>
    <div class="rwf-wc-write-review">
        <a href="<?php echo esc_url( $url ); ?>" class="rwf-wc-review-btn">
            <span class="dashicons dashicons-edit"></span>
            <?php esc_html_e( 'Write a Review', 'reviewfic' ); ?>
        </a>
    </div>
    <?php
}

echo '</div><!-- .rwf-tf-reviews-wrap -->';

/**
 * Map a Tourfic post type to its order type slug.
 * Defined inline so the template is self-contained.
 */
function rwf_tf_type_from_post_type( $post_type ) {
    $map = array(
        'tf_hotel'     => 'hotel',
        'tf_tours'     => 'tour',
        'tf_apartment' => 'apartment',
        'tf_carrental' => 'car',
    );
    return $map[ $post_type ] ?? '';
}
