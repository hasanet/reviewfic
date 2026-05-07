<?php
/**
 * Reviewfic — Review Submission Form & CF7 Integration
 *
 * Shortcode: [reviewfic_form]
 * Attributes:
 *   success_message  — Text shown after a successful submission.
 *   require_approval — yes (default) | no. Whether new reviews are saved as pending.
 *   show_source      — yes (default) | no. Whether to show the platform/source dropdown.
 *   redirect         — URL to redirect to after a successful submission (optional).
 */
if ( ! defined( 'ABSPATH' ) ) exit;


// ── Built-in Review Submission Form ────────────────────────────────────────

function reviewfic_form_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'success_message'  => 'Thank you! Your review has been submitted and is pending approval.',
        'require_approval' => 'yes',
        'show_source'      => 'yes',
        'redirect'         => '',
    ), $atts, 'reviewfic_form' );

    $errors  = array();
    $success = false;

    // ── Handle POST ───────────────────────────────────────────
    if (
        $_SERVER['REQUEST_METHOD'] === 'POST'
        && isset( $_POST['reviewfic_form_nonce'] )
        && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['reviewfic_form_nonce'] ) ), 'reviewfic_submit_review' )
    ) {
        $name        = sanitize_text_field( wp_unslash( $_POST['rwf_name']        ?? '' ) );
        $designation = sanitize_text_field( wp_unslash( $_POST['rwf_designation'] ?? '' ) );
        $company     = sanitize_text_field( wp_unslash( $_POST['rwf_company']     ?? '' ) );
        $rating      = (float) ( $_POST['rwf_rating'] ?? 0 );
        $title       = sanitize_text_field( wp_unslash( $_POST['rwf_title']       ?? '' ) );
        $content     = sanitize_textarea_field( wp_unslash( $_POST['rwf_content'] ?? '' ) );
        $source_slug = sanitize_text_field( wp_unslash( $_POST['rwf_source']      ?? '' ) );

        if ( empty( $name ) )                         $errors[] = __( 'Your name is required.',         'reviewfic' );
        if ( $rating < 1 || $rating > 5 )             $errors[] = __( 'Please select a star rating.',   'reviewfic' );
        if ( empty( $content ) )                      $errors[] = __( 'Review content is required.',    'reviewfic' );

        if ( empty( $errors ) ) {
            $status  = ( $atts['require_approval'] === 'yes' ) ? 'pending' : 'publish';
            $post_id = wp_insert_post( array(
                'post_type'    => 'reviewfic_reviews',
                'post_title'   => $title ?: $name . "'s Review",
                'post_content' => $content,
                'post_status'  => $status,
            ) );

            if ( $post_id && ! is_wp_error( $post_id ) ) {
                update_post_meta( $post_id, 'reviewfic_review_stars',       $rating );
                update_post_meta( $post_id, 'reviewfic_client_name',        $name );
                update_post_meta( $post_id, 'reviewfic_client_designation', $designation );
                update_post_meta( $post_id, 'reviewfic_client_company',     $company );

                if ( ! empty( $source_slug ) ) {
                    wp_set_post_terms( $post_id, array( $source_slug ), 'reviewfic_source', false );
                }

                // ── Handle photo upload ───────────────────────
                if ( ! empty( $_FILES['rwf_photo']['name'] ) && $_FILES['rwf_photo']['error'] === UPLOAD_ERR_OK ) {
                    require_once ABSPATH . 'wp-admin/includes/image.php';
                    require_once ABSPATH . 'wp-admin/includes/file.php';
                    require_once ABSPATH . 'wp-admin/includes/media.php';

                    $allowed_types = array( 'image/jpeg', 'image/png', 'image/gif', 'image/webp' );
                    $file_type     = $_FILES['rwf_photo']['type'];

                    if ( in_array( $file_type, $allowed_types, true ) && $_FILES['rwf_photo']['size'] <= 5 * 1024 * 1024 ) {
                        $attachment_id = media_handle_upload( 'rwf_photo', $post_id );
                        if ( $attachment_id && ! is_wp_error( $attachment_id ) ) {
                            update_post_meta( $post_id, 'reviewfic_reviewer_photo', $attachment_id );
                        }
                    }
                }

                if ( ! empty( $atts['redirect'] ) ) {
                    wp_safe_redirect( esc_url( $atts['redirect'] ) );
                    exit;
                }
                $success = true;
            } else {
                $errors[] = __( 'Something went wrong. Please try again.', 'reviewfic' );
            }
        }
    }

    // ── Output ────────────────────────────────────────────────
    ob_start();

    if ( $success ) {
        echo '<div class="rwf-form-success" role="alert">' . esc_html( $atts['success_message'] ) . '</div>';
    } else {
        if ( ! empty( $errors ) ) {
            echo '<div class="rwf-form-errors" role="alert">';
            foreach ( $errors as $e ) {
                echo '<p>' . esc_html( $e ) . '</p>';
            }
            echo '</div>';
        }

        $sources = get_terms( array( 'taxonomy' => 'reviewfic_source', 'hide_empty' => false ) );
        ?>
        <form class="rwf-submission-form" method="post" enctype="multipart/form-data" novalidate>
            <?php wp_nonce_field( 'reviewfic_submit_review', 'reviewfic_form_nonce' ); ?>

            <div class="rwf-form-row">
                <label for="rwf_name"><?php esc_html_e( 'Your Name', 'reviewfic' ); ?> <span class="rwf-required" aria-hidden="true">*</span></label>
                <input type="text" id="rwf_name" name="rwf_name" value="<?php echo esc_attr( $_POST['rwf_name'] ?? '' ); ?>" required autocomplete="name">
            </div>

            <div class="rwf-form-row rwf-form-row-half">
                <div>
                    <label for="rwf_designation"><?php esc_html_e( 'Designation', 'reviewfic' ); ?></label>
                    <input type="text" id="rwf_designation" name="rwf_designation" value="<?php echo esc_attr( $_POST['rwf_designation'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'e.g. CEO', 'reviewfic' ); ?>" autocomplete="organization-title">
                </div>
                <div>
                    <label for="rwf_company"><?php esc_html_e( 'Company', 'reviewfic' ); ?></label>
                    <input type="text" id="rwf_company" name="rwf_company" value="<?php echo esc_attr( $_POST['rwf_company'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'e.g. Acme Corp', 'reviewfic' ); ?>" autocomplete="organization">
                </div>
            </div>

            <div class="rwf-form-row">
                <label><?php esc_html_e( 'Star Rating', 'reviewfic' ); ?> <span class="rwf-required" aria-hidden="true">*</span></label>
                <div class="rwf-star-picker" role="group" aria-label="<?php esc_attr_e( 'Star rating', 'reviewfic' ); ?>">
                    <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
                        <button type="button" class="rwf-star-btn" data-value="<?php echo esc_attr( $i ); ?>" aria-label="<?php echo esc_attr( sprintf( _n( '%d star', '%d stars', $i, 'reviewfic' ), $i ) ); ?>">&#9733;</button>
                    <?php endfor; ?>
                    <input type="hidden" id="rwf_rating" name="rwf_rating" value="<?php echo esc_attr( $_POST['rwf_rating'] ?? '' ); ?>">
                </div>
            </div>

            <div class="rwf-form-row">
                <label for="rwf_title"><?php esc_html_e( 'Review Title', 'reviewfic' ); ?></label>
                <input type="text" id="rwf_title" name="rwf_title" value="<?php echo esc_attr( $_POST['rwf_title'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'e.g. Great experience!', 'reviewfic' ); ?>">
            </div>

            <div class="rwf-form-row">
                <label for="rwf_content"><?php esc_html_e( 'Your Review', 'reviewfic' ); ?> <span class="rwf-required" aria-hidden="true">*</span></label>
                <textarea id="rwf_content" name="rwf_content" rows="5" required><?php echo esc_textarea( $_POST['rwf_content'] ?? '' ); ?></textarea>
            </div>

            <?php if ( $atts['show_source'] === 'yes' && ! empty( $sources ) && ! is_wp_error( $sources ) ) : ?>
            <div class="rwf-form-row">
                <label for="rwf_source"><?php esc_html_e( 'Review Platform', 'reviewfic' ); ?></label>
                <select id="rwf_source" name="rwf_source">
                    <option value=""><?php esc_html_e( '— Select platform (optional) —', 'reviewfic' ); ?></option>
                    <?php foreach ( $sources as $source ) : ?>
                        <option value="<?php echo esc_attr( $source->slug ); ?>" <?php selected( ( $_POST['rwf_source'] ?? '' ), $source->slug ); ?>>
                            <?php echo esc_html( $source->name ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <div class="rwf-form-row">
                <label for="rwf_photo"><?php esc_html_e( 'Your Photo', 'reviewfic' ); ?></label>
                <div class="rwf-file-upload">
                    <input type="file" id="rwf_photo" name="rwf_photo" accept="image/jpeg,image/png,image/gif,image/webp">
                    <p class="rwf-file-hint"><?php esc_html_e( 'JPG, PNG, GIF or WebP — max 5 MB (optional)', 'reviewfic' ); ?></p>
                    <div class="rwf-photo-preview" id="rwf-photo-preview"></div>
                </div>
            </div>

            <div class="rwf-form-row">
                <button type="submit" class="rwf-submit-btn"><?php esc_html_e( 'Submit Review', 'reviewfic' ); ?></button>
            </div>
        </form>
        <?php
    }

    return ob_get_clean();
}
add_shortcode( 'reviewfic_form', 'reviewfic_form_shortcode' );


// ── Contact Form 7 Integration ─────────────────────────────────────────────

/**
 * Only hook CF7 features when CF7 is active.
 */
add_action( 'plugins_loaded', 'reviewfic_cf7_init' );

function reviewfic_cf7_init() {
    if ( ! class_exists( 'WPCF7_ContactForm' ) ) return;

    add_filter( 'wpcf7_editor_panels', 'reviewfic_cf7_add_panel' );
    add_action( 'wpcf7_save_contact_form', 'reviewfic_cf7_save_panel' );
    add_action( 'wpcf7_mail_sent', 'reviewfic_cf7_on_submit' );
}

/**
 * Add a "Reviewfic" tab to the CF7 form editor.
 */
function reviewfic_cf7_add_panel( $panels ) {
    $panels['reviewfic-panel'] = array(
        'title'    => __( 'Reviewfic', 'reviewfic' ),
        'callback' => 'reviewfic_cf7_panel_content',
    );
    return $panels;
}

/**
 * Render the Reviewfic panel inside the CF7 editor.
 */
function reviewfic_cf7_panel_content( $post ) {
    $form_id   = $post->id();
    $enabled   = get_post_meta( $form_id, '_rwf_cf7_enabled', true );
    $field_map = get_post_meta( $form_id, '_rwf_cf7_fields',  true );
    if ( ! is_array( $field_map ) ) $field_map = array();

    $fields = array(
        'name'        => __( 'Reviewer Name',          'reviewfic' ),
        'designation' => __( 'Designation',            'reviewfic' ),
        'company'     => __( 'Company',                'reviewfic' ),
        'rating'      => __( 'Star Rating (1–5)',      'reviewfic' ),
        'title'       => __( 'Review Title',           'reviewfic' ),
        'content'     => __( 'Review Content',         'reviewfic' ),
        'source'      => __( 'Review Source (slug)',   'reviewfic' ),
    );
    ?>
    <div class="rwf-cf7-panel">
        <h2><?php esc_html_e( 'Reviewfic Integration', 'reviewfic' ); ?></h2>
        <p><?php esc_html_e( 'Automatically create a review in Reviewfic when this form is submitted.', 'reviewfic' ); ?></p>

        <table class="form-table">
            <tr>
                <th scope="row"><?php esc_html_e( 'Enable', 'reviewfic' ); ?></th>
                <td>
                    <label>
                        <input type="checkbox" name="rwf_cf7_enabled" value="1" <?php checked( $enabled, '1' ); ?>>
                        <?php esc_html_e( 'Send submissions from this form to Reviewfic', 'reviewfic' ); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e( 'Review Status', 'reviewfic' ); ?></th>
                <td>
                    <select name="rwf_cf7_status">
                        <option value="pending" <?php selected( $field_map['status'] ?? 'pending', 'pending' ); ?>><?php esc_html_e( 'Pending — require manual approval', 'reviewfic' ); ?></option>
                        <option value="publish" <?php selected( $field_map['status'] ?? 'pending', 'publish' ); ?>><?php esc_html_e( 'Published — auto-approve',          'reviewfic' ); ?></option>
                    </select>
                </td>
            </tr>
        </table>

        <h3><?php esc_html_e( 'Field Mapping', 'reviewfic' ); ?></h3>
        <p class="description">
            <?php esc_html_e( 'Enter the CF7 field name (e.g. your-name) that maps to each Reviewfic field. Leave blank to skip that field.', 'reviewfic' ); ?>
        </p>

        <table class="form-table">
            <?php foreach ( $fields as $key => $label ) : ?>
            <tr>
                <th scope="row"><?php echo esc_html( $label ); ?></th>
                <td>
                    <input
                        type="text"
                        name="rwf_cf7_field[<?php echo esc_attr( $key ); ?>]"
                        value="<?php echo esc_attr( $field_map[ $key ] ?? '' ); ?>"
                        class="regular-text"
                        placeholder="<?php esc_attr_e( 'cf7-field-name', 'reviewfic' ); ?>"
                    >
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <?php
}

/**
 * Save CF7 panel settings when the CF7 form is saved.
 */
function reviewfic_cf7_save_panel( $contact_form ) {
    if ( ! isset( $_POST['rwf_cf7_enabled'] ) && ! isset( $_POST['rwf_cf7_field'] ) ) return;

    $form_id = $contact_form->id();
    $enabled = isset( $_POST['rwf_cf7_enabled'] ) ? '1' : '0';
    update_post_meta( $form_id, '_rwf_cf7_enabled', $enabled );

    $field_map = array();
    if ( isset( $_POST['rwf_cf7_field'] ) && is_array( $_POST['rwf_cf7_field'] ) ) {
        foreach ( $_POST['rwf_cf7_field'] as $key => $val ) {
            $field_map[ sanitize_key( $key ) ] = sanitize_text_field( wp_unslash( $val ) );
        }
    }
    $field_map['status'] = sanitize_text_field( wp_unslash( $_POST['rwf_cf7_status'] ?? 'pending' ) );

    update_post_meta( $form_id, '_rwf_cf7_fields', $field_map );
}

/**
 * On CF7 mail_sent: create a Reviewfic review from the mapped fields.
 */
function reviewfic_cf7_on_submit( $contact_form ) {
    $form_id = $contact_form->id();
    $enabled = get_post_meta( $form_id, '_rwf_cf7_enabled', true );
    if ( $enabled !== '1' ) return;

    $field_map = get_post_meta( $form_id, '_rwf_cf7_fields', true );
    if ( ! is_array( $field_map ) || empty( $field_map ) ) return;

    $submission = WPCF7_Submission::get_instance();
    if ( ! $submission ) return;

    $data = $submission->get_posted_data();

    $get = function ( $key ) use ( $field_map, $data ) {
        $cf7_key = $field_map[ $key ] ?? '';
        if ( ! $cf7_key ) return '';
        $value = $data[ $cf7_key ] ?? '';
        return is_array( $value ) ? sanitize_text_field( implode( ', ', $value ) ) : sanitize_text_field( wp_unslash( $value ) );
    };

    $name    = $get( 'name' );
    $content = $get( 'content' );
    if ( empty( $name ) && empty( $content ) ) return;

    $rating = (float) $get( 'rating' );
    if ( $rating < 1 || $rating > 5 ) $rating = 5.0;

    $status  = in_array( $field_map['status'] ?? '', array( 'publish', 'pending' ), true )
               ? $field_map['status']
               : 'pending';

    $post_id = wp_insert_post( array(
        'post_type'    => 'reviewfic_reviews',
        'post_title'   => $get( 'title' ) ?: ( $name ? $name . "'s Review" : __( 'Review', 'reviewfic' ) ),
        'post_content' => $content,
        'post_status'  => $status,
    ) );

    if ( $post_id && ! is_wp_error( $post_id ) ) {
        update_post_meta( $post_id, 'reviewfic_review_stars',       $rating );
        update_post_meta( $post_id, 'reviewfic_client_name',        $name );
        update_post_meta( $post_id, 'reviewfic_client_designation', $get( 'designation' ) );
        update_post_meta( $post_id, 'reviewfic_client_company',     $get( 'company' ) );

        $source_slug = $get( 'source' );
        if ( $source_slug ) {
            wp_set_post_terms( $post_id, array( $source_slug ), 'reviewfic_source', false );
        }
    }
}
