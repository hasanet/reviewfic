<?php
/**
 * Reviewfic — WPForms, Fluent Forms & Gravity Forms Integrations
 *
 * Each integration:
 *   - Adds a "Form Integrations" submenu page under Reviewfic
 *   - Stores per-form settings (enabled, status, source, field map)
 *   - Hooks into the plugin's submission action to create reviews
 *   - Localises enabled form IDs to the frontend for styling
 */
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'RWF_INTEGRATIONS_OPTION', 'reviewfic_form_integrations' );

// ── Helpers ────────────────────────────────────────────────────────────────

function rwf_get_integrations() {
    $defaults = array( 'wpforms' => array(), 'fluent' => array(), 'gravity' => array() );
    return wp_parse_args( get_option( RWF_INTEGRATIONS_OPTION, array() ), $defaults );
}

function rwf_get_form_integration( $plugin, $form_id ) {
    $all = rwf_get_integrations();
    return $all[ $plugin ][ $form_id ] ?? array();
}

/**
 * Sideload a photo URL as a WP attachment and attach it to a review post.
 */
function rwf_sideload_photo_url( $url, $post_id ) {
    if ( empty( $url ) || ! filter_var( $url, FILTER_VALIDATE_URL ) ) return;

    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $tmp = download_url( $url );
    if ( is_wp_error( $tmp ) ) return;

    $file_array = array(
        'name'     => basename( parse_url( $url, PHP_URL_PATH ) ),
        'tmp_name' => $tmp,
    );

    $attachment_id = media_handle_sideload( $file_array, $post_id );
    @unlink( $tmp );

    if ( $attachment_id && ! is_wp_error( $attachment_id ) ) {
        update_post_meta( $post_id, 'reviewfic_reviewer_photo', $attachment_id );
    }
}

/**
 * Create a review post from a field value map + saved integration config.
 */
function rwf_create_review_from_integration( $integration, $get_value ) {
    if ( empty( $integration['enabled'] ) || $integration['enabled'] !== '1' ) return;

    $fields    = $integration['fields'] ?? array();
    $status    = in_array( $integration['status'] ?? '', array( 'publish', 'pending' ), true )
                 ? $integration['status'] : 'pending';

    $name    = sanitize_text_field( $get_value( 'name',    $fields ) );
    $content = sanitize_textarea_field( $get_value( 'content', $fields ) );
    if ( empty( $name ) && empty( $content ) ) return null;

    $rating = (float) $get_value( 'rating', $fields );
    if ( $rating < 1 || $rating > 5 ) $rating = 5.0;

    $title = sanitize_text_field( $get_value( 'title', $fields ) )
             ?: ( $name ? $name . "'s Review" : __( 'Review', 'reviewfic' ) );

    $post_id = wp_insert_post( array(
        'post_type'    => 'reviewfic_reviews',
        'post_title'   => $title,
        'post_content' => $content,
        'post_status'  => $status,
    ) );

    if ( ! $post_id || is_wp_error( $post_id ) ) return null;

    update_post_meta( $post_id, 'reviewfic_review_stars',       $rating );
    update_post_meta( $post_id, 'reviewfic_client_name',        $name );
    update_post_meta( $post_id, 'reviewfic_client_designation', sanitize_text_field( $get_value( 'designation', $fields ) ) );
    update_post_meta( $post_id, 'reviewfic_client_company',     sanitize_text_field( $get_value( 'company',     $fields ) ) );

    $source = sanitize_text_field( $integration['source'] ?? '' );
    if ( $source ) {
        wp_set_post_terms( $post_id, array( $source ), 'reviewfic_source', false );
    }

    return $post_id;
}


// ── Admin submenu page ─────────────────────────────────────────────────────

add_action( 'admin_menu', 'rwf_register_integrations_page' );

function rwf_register_integrations_page() {
    add_submenu_page(
        'edit.php?post_type=reviewfic_reviews',
        __( 'Form Integrations', 'reviewfic' ),
        __( 'Form Integrations', 'reviewfic' ),
        'manage_options',
        'reviewfic-integrations',
        'rwf_integrations_page'
    );
}

// ── Save handler ───────────────────────────────────────────────────────────

add_action( 'admin_post_rwf_save_integrations', function () {
    if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );
    check_admin_referer( 'rwf_save_integrations' );

    $plugin  = sanitize_key( $_POST['rwf_plugin'] ?? '' );
    $allowed = array( 'wpforms', 'fluent', 'gravity' );
    if ( ! in_array( $plugin, $allowed, true ) ) wp_die( 'Invalid plugin' );

    $all = rwf_get_integrations();
    $all[ $plugin ] = array();

    $raw_forms = $_POST['rwf_forms'][ $plugin ] ?? array();
    if ( is_array( $raw_forms ) ) {
        foreach ( $raw_forms as $form_id => $data ) {
            $form_id = sanitize_key( $form_id );
            $fields  = array();
            foreach ( $data['fields'] ?? array() as $key => $val ) {
                $fields[ sanitize_key( $key ) ] = sanitize_text_field( wp_unslash( $val ) );
            }
            $all[ $plugin ][ $form_id ] = array(
                'enabled' => isset( $data['enabled'] ) ? '1' : '0',
                'status'  => in_array( $data['status'] ?? '', array( 'publish', 'pending' ), true ) ? $data['status'] : 'pending',
                'source'  => sanitize_text_field( wp_unslash( $data['source'] ?? '' ) ),
                'fields'  => $fields,
            );
        }
    }

    update_option( RWF_INTEGRATIONS_OPTION, $all );

    wp_safe_redirect( add_query_arg( array(
        'post_type' => 'reviewfic_reviews',
        'page'      => 'reviewfic-integrations',
        'tab'       => $plugin,
        'saved'     => '1',
    ), admin_url( 'edit.php' ) ) );
    exit;
} );


// ── Frontend: pass enabled form IDs to JS for styling ─────────────────────

add_action( 'wp_enqueue_scripts', 'rwf_integrations_frontend_scripts' );

function rwf_integrations_frontend_scripts() {
    if ( ! wp_script_is( 'reviewfic-frontend', 'enqueued' ) ) return;

    $all     = rwf_get_integrations();
    $enabled = array( 'wpforms' => array(), 'fluent' => array(), 'gravity' => array() );

    foreach ( $all as $plugin => $forms ) {
        foreach ( $forms as $form_id => $config ) {
            if ( ( $config['enabled'] ?? '0' ) === '1' ) {
                $enabled[ $plugin ][] = (int) $form_id;
            }
        }
    }

    if ( array_filter( $enabled ) ) {
        wp_localize_script( 'reviewfic-frontend', 'rwfForms', $enabled );
    }
}


// ══════════════════════════════════════════════════════════════════════════
//  WPFORMS INTEGRATION
// ══════════════════════════════════════════════════════════════════════════

add_action( 'wpforms_process_complete', 'rwf_wpforms_on_submit', 10, 4 );

function rwf_wpforms_on_submit( $fields, $entry, $form_data, $entry_id ) {
    $form_id     = (string) ( $form_data['id'] ?? '' );
    $integration = rwf_get_form_integration( 'wpforms', $form_id );
    if ( empty( $integration ) ) return;

    $get_value = function ( $key, $field_map ) use ( $fields ) {
        $field_id = trim( $field_map[ $key ] ?? '' );
        if ( $field_id === '' ) return '';
        $field = $fields[ $field_id ] ?? null;
        if ( ! $field ) return '';
        return is_array( $field['value'] ) ? implode( ', ', $field['value'] ) : (string) $field['value'];
    };

    $post_id = rwf_create_review_from_integration( $integration, $get_value );
    if ( ! $post_id ) return;

    // Photo — WPForms file field value is a URL
    $photo_id = trim( $integration['fields']['photo'] ?? '' );
    if ( $photo_id !== '' && isset( $fields[ $photo_id ]['value'] ) ) {
        rwf_sideload_photo_url( $fields[ $photo_id ]['value'], $post_id );
    }
}

/**
 * Get all WPForms forms.
 */
function rwf_get_wpforms_forms() {
    if ( ! function_exists( 'wpforms' ) ) return array();
    $posts = get_posts( array(
        'post_type'      => 'wpforms',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    ) );
    $forms = array();
    foreach ( $posts as $post ) {
        $forms[] = array( 'id' => $post->ID, 'title' => $post->post_title );
    }
    return $forms;
}


// ══════════════════════════════════════════════════════════════════════════
//  FLUENT FORMS INTEGRATION
// ══════════════════════════════════════════════════════════════════════════

add_action( 'fluentform/submission_inserted',   'rwf_fluent_on_submit', 10, 3 );
add_action( 'fluentForms/submission_inserted',  'rwf_fluent_on_submit', 10, 3 ); // legacy hook

function rwf_fluent_on_submit( $entry_id, $form_data, $form ) {
    $form_id     = (string) ( is_object( $form ) ? $form->id : ( $form['id'] ?? '' ) );
    $integration = rwf_get_form_integration( 'fluent', $form_id );
    if ( empty( $integration ) ) return;

    $get_value = function ( $key, $field_map ) use ( $form_data ) {
        $field_key = trim( $field_map[ $key ] ?? '' );
        if ( $field_key === '' ) return '';
        $value = $form_data[ $field_key ] ?? '';
        return is_array( $value ) ? implode( ', ', $value ) : (string) $value;
    };

    $post_id = rwf_create_review_from_integration( $integration, $get_value );
    if ( ! $post_id ) return;

    // Photo — Fluent Forms file field value is a URL (or array of URLs)
    $photo_key = trim( $integration['fields']['photo'] ?? '' );
    if ( $photo_key !== '' && ! empty( $form_data[ $photo_key ] ) ) {
        $url = is_array( $form_data[ $photo_key ] ) ? $form_data[ $photo_key ][0] : $form_data[ $photo_key ];
        rwf_sideload_photo_url( $url, $post_id );
    }
}

/**
 * Get all Fluent Forms forms.
 */
function rwf_get_fluent_forms() {
    if ( ! defined( 'FLUENTFORM' ) && ! class_exists( 'FluentForm\App\Models\Form' ) ) return array();

    $forms = array();
    try {
        if ( function_exists( 'wpFluent' ) ) {
            $rows = wpFluent()->table( 'fluentform_forms' )->select( array( 'id', 'title' ) )->get();
            foreach ( $rows as $row ) {
                $forms[] = array( 'id' => $row->id, 'title' => $row->title );
            }
        } elseif ( class_exists( 'FluentForm\App\Models\Form' ) ) {
            $rows = \FluentForm\App\Models\Form::select( array( 'id', 'title' ) )->get();
            foreach ( $rows as $row ) {
                $forms[] = array( 'id' => $row->id, 'title' => $row->title );
            }
        }
    } catch ( Exception $e ) {
        // Fluent Forms not available
    }
    return $forms;
}


// ══════════════════════════════════════════════════════════════════════════
//  GRAVITY FORMS INTEGRATION
// ══════════════════════════════════════════════════════════════════════════

add_action( 'gform_after_submission', 'rwf_gravity_on_submit', 10, 2 );

function rwf_gravity_on_submit( $entry, $form ) {
    $form_id     = (string) ( $form['id'] ?? '' );
    $integration = rwf_get_form_integration( 'gravity', $form_id );
    if ( empty( $integration ) ) return;

    $get_value = function ( $key, $field_map ) use ( $entry ) {
        $field_id = trim( $field_map[ $key ] ?? '' );
        if ( $field_id === '' ) return '';
        $value = $entry[ $field_id ] ?? '';
        return is_array( $value ) ? implode( ', ', $value ) : (string) $value;
    };

    $post_id = rwf_create_review_from_integration( $integration, $get_value );
    if ( ! $post_id ) return;

    // Photo — Gravity Forms file field value is a URL
    $photo_id = trim( $integration['fields']['photo'] ?? '' );
    if ( $photo_id !== '' && ! empty( $entry[ $photo_id ] ) ) {
        rwf_sideload_photo_url( $entry[ $photo_id ], $post_id );
    }
}

/**
 * Get all Gravity Forms forms.
 */
function rwf_get_gravity_forms() {
    if ( ! class_exists( 'GFAPI' ) ) return array();
    $forms  = GFAPI::get_forms();
    $result = array();
    foreach ( $forms as $form ) {
        $result[] = array( 'id' => $form['id'], 'title' => $form['title'] );
    }
    return $result;
}


// ══════════════════════════════════════════════════════════════════════════
//  ADMIN PAGE UI
// ══════════════════════════════════════════════════════════════════════════

function rwf_integrations_page() {
    $active_tab  = sanitize_key( $_GET['tab'] ?? 'wpforms' );
    $saved       = ! empty( $_GET['saved'] );
    $all         = rwf_get_integrations();
    $sources     = get_terms( array( 'taxonomy' => 'reviewfic_source', 'hide_empty' => false ) );

    $field_keys = array(
        'name'        => __( 'Reviewer Name',     'reviewfic' ),
        'designation' => __( 'Designation',        'reviewfic' ),
        'company'     => __( 'Company',            'reviewfic' ),
        'rating'      => __( 'Star Rating (1–5)',  'reviewfic' ),
        'title'       => __( 'Review Title',       'reviewfic' ),
        'content'     => __( 'Review Content',     'reviewfic' ),
        'photo'       => __( 'Photo (file field)', 'reviewfic' ),
    );

    $tabs = array(
        'wpforms' => array(
            'label'     => 'WPForms',
            'active'    => function_exists( 'wpforms' ),
            'forms'     => rwf_get_wpforms_forms(),
            'id_hint'   => __( 'Use the numeric Field ID shown in the form builder (e.g. 1, 2, 3)', 'reviewfic' ),
        ),
        'fluent'  => array(
            'label'     => 'Fluent Forms',
            'active'    => defined( 'FLUENTFORM' ) || class_exists( 'FluentForm\App\Models\Form' ),
            'forms'     => rwf_get_fluent_forms(),
            'id_hint'   => __( 'Use the field name/key shown in the form editor (e.g. names, message)', 'reviewfic' ),
        ),
        'gravity' => array(
            'label'     => 'Gravity Forms',
            'active'    => class_exists( 'GFAPI' ),
            'forms'     => rwf_get_gravity_forms(),
            'id_hint'   => __( 'Use the numeric Field ID shown in the form editor (e.g. 1, 2, 3)', 'reviewfic' ),
        ),
    );
    ?>
    <div class="wrap rwf-ie-wrap">
        <h1 class="wp-heading-inline"><?php esc_html_e( 'Form Integrations', 'reviewfic' ); ?></h1>
        <hr class="wp-header-end">

        <?php if ( $saved ) : ?>
            <div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Integration settings saved.', 'reviewfic' ); ?></p></div>
        <?php endif; ?>

        <nav class="nav-tab-wrapper" style="margin-bottom:20px;">
            <?php foreach ( $tabs as $slug => $tab ) : ?>
                <a href="<?php echo esc_url( add_query_arg( array( 'post_type' => 'reviewfic_reviews', 'page' => 'reviewfic-integrations', 'tab' => $slug ), admin_url( 'edit.php' ) ) ); ?>"
                   class="nav-tab <?php echo $active_tab === $slug ? 'nav-tab-active' : ''; ?>">
                    <?php echo esc_html( $tab['label'] ); ?>
                    <?php if ( ! $tab['active'] ) : ?>
                        <span class="rwf-tab-inactive"><?php esc_html_e( 'not installed', 'reviewfic' ); ?></span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <?php
        $tab = $tabs[ $active_tab ] ?? $tabs['wpforms'];
        if ( ! $tab['active'] ) :
        ?>
            <div class="rwf-ie-card" style="max-width:600px;">
                <p><?php printf(
                    /* translators: %s: plugin name */
                    esc_html__( '%s is not installed or activated. Please install it to use this integration.', 'reviewfic' ),
                    '<strong>' . esc_html( $tab['label'] ) . '</strong>'
                ); ?></p>
            </div>
        <?php elseif ( empty( $tab['forms'] ) ) : ?>
            <div class="rwf-ie-card" style="max-width:600px;">
                <p><?php printf(
                    esc_html__( 'No %s forms found. Create a form first, then come back to configure the integration.', 'reviewfic' ),
                    esc_html( $tab['label'] )
                ); ?></p>
            </div>
        <?php else : ?>
            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                <?php wp_nonce_field( 'rwf_save_integrations' ); ?>
                <input type="hidden" name="action" value="rwf_save_integrations">
                <input type="hidden" name="rwf_plugin" value="<?php echo esc_attr( $active_tab ); ?>">

                <p class="description" style="margin-bottom:16px;">
                    <strong><?php esc_html_e( 'Field ID hint:', 'reviewfic' ); ?></strong>
                    <?php echo esc_html( $tab['id_hint'] ); ?>
                </p>

                <div class="rwf-integrations-list">
                    <?php foreach ( $tab['forms'] as $form ) :
                        $fid    = (string) $form['id'];
                        $config = $all[ $active_tab ][ $fid ] ?? array();
                        $fields = $config['fields'] ?? array();
                        $en     = ( $config['enabled'] ?? '0' ) === '1';
                    ?>
                    <div class="rwf-int-form-card <?php echo $en ? 'rwf-int-enabled' : ''; ?>">
                        <div class="rwf-int-form-header">
                            <label class="rwf-int-toggle-label">
                                <input type="checkbox"
                                    name="rwf_forms[<?php echo esc_attr( $active_tab ); ?>][<?php echo esc_attr( $fid ); ?>][enabled]"
                                    value="1"
                                    class="rwf-int-enable-cb"
                                    <?php checked( $en ); ?>>
                                <strong><?php echo esc_html( $form['title'] ); ?></strong>
                                <span class="rwf-int-id-badge">ID: <?php echo esc_html( $fid ); ?></span>
                            </label>
                            <button type="button" class="rwf-int-toggle-btn <?php echo $en ? '' : 'collapsed'; ?>">
                                <?php esc_html_e( 'Configure', 'reviewfic' ); ?> <span class="dashicons dashicons-arrow-down-alt2"></span>
                            </button>
                        </div>

                        <div class="rwf-int-form-body <?php echo $en ? '' : 'rwf-int-collapsed'; ?>">
                            <div class="rwf-int-row">
                                <label><?php esc_html_e( 'Review Status', 'reviewfic' ); ?></label>
                                <select name="rwf_forms[<?php echo esc_attr( $active_tab ); ?>][<?php echo esc_attr( $fid ); ?>][status]">
                                    <option value="pending" <?php selected( $config['status'] ?? 'pending', 'pending' ); ?>><?php esc_html_e( 'Pending — require approval', 'reviewfic' ); ?></option>
                                    <option value="publish" <?php selected( $config['status'] ?? 'pending', 'publish' ); ?>><?php esc_html_e( 'Published — auto-approve',    'reviewfic' ); ?></option>
                                </select>
                            </div>

                            <div class="rwf-int-row">
                                <label><?php esc_html_e( 'Review Source', 'reviewfic' ); ?></label>
                                <select name="rwf_forms[<?php echo esc_attr( $active_tab ); ?>][<?php echo esc_attr( $fid ); ?>][source]">
                                    <option value=""><?php esc_html_e( '— None —', 'reviewfic' ); ?></option>
                                    <?php if ( ! empty( $sources ) && ! is_wp_error( $sources ) ) : ?>
                                        <?php foreach ( $sources as $source ) : ?>
                                            <option value="<?php echo esc_attr( $source->slug ); ?>" <?php selected( $config['source'] ?? '', $source->slug ); ?>>
                                                <?php echo esc_html( $source->name ); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="rwf-int-field-map">
                                <p class="rwf-int-map-heading"><?php esc_html_e( 'Field Mapping', 'reviewfic' ); ?></p>
                                <?php foreach ( $field_keys as $key => $label ) : ?>
                                <div class="rwf-int-row rwf-int-map-row">
                                    <label><?php echo esc_html( $label ); ?></label>
                                    <input
                                        type="text"
                                        name="rwf_forms[<?php echo esc_attr( $active_tab ); ?>][<?php echo esc_attr( $fid ); ?>][fields][<?php echo esc_attr( $key ); ?>]"
                                        value="<?php echo esc_attr( $fields[ $key ] ?? '' ); ?>"
                                        placeholder="<?php esc_attr_e( 'field ID or name', 'reviewfic' ); ?>"
                                        class="regular-text"
                                    >
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <p style="margin-top:20px;">
                    <button type="submit" class="button button-primary rwf-ie-btn">
                        <?php printf( esc_html__( 'Save %s Settings', 'reviewfic' ), esc_html( $tab['label'] ) ); ?>
                    </button>
                </p>
            </form>
        <?php endif; ?>
    </div>

    <script>
    (function () {
        document.querySelectorAll('.rwf-int-toggle-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var body = btn.closest('.rwf-int-form-card').querySelector('.rwf-int-form-body');
                body.classList.toggle('rwf-int-collapsed');
                btn.classList.toggle('collapsed');
            });
        });
        // Auto-expand when checkbox is ticked
        document.querySelectorAll('.rwf-int-enable-cb').forEach(function (cb) {
            cb.addEventListener('change', function () {
                var card = cb.closest('.rwf-int-form-card');
                var body = card.querySelector('.rwf-int-form-body');
                var btn  = card.querySelector('.rwf-int-toggle-btn');
                if (cb.checked) {
                    body.classList.remove('rwf-int-collapsed');
                    btn.classList.remove('collapsed');
                    card.classList.add('rwf-int-enabled');
                } else {
                    card.classList.remove('rwf-int-enabled');
                }
            });
        });
    })();
    </script>
    <?php
}
