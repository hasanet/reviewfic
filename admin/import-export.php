<?php
/**
 * Reviewfic — Import / Export
 *
 * Export: CSV and JSON download of all reviews.
 * Import: Upload a CSV or JSON file to bulk-create reviews.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

// ── Register submenu page ──────────────────────────────────────────────────

function reviewfic_register_import_export_page() {
    add_submenu_page(
        'edit.php?post_type=reviewfic_reviews',
        __( 'Import / Export', 'reviewfic' ),
        __( 'Import / Export', 'reviewfic' ),
        'manage_options',
        'reviewfic-import-export',
        'reviewfic_import_export_page'
    );
}
add_action( 'admin_menu', 'reviewfic_register_import_export_page' );


// ── Export handlers ────────────────────────────────────────────────────────

function reviewfic_get_all_reviews_data() {
    $query = new WP_Query( array(
        'post_type'      => 'reviewfic_reviews',
        'posts_per_page' => -1,
        'post_status'    => array( 'publish', 'pending', 'draft' ),
    ) );

    $rows = array();
    while ( $query->have_posts() ) {
        $query->the_post();
        $id = get_the_ID();

        $source_terms   = wp_get_post_terms( $id, 'reviewfic_source' );
        $category_terms = wp_get_post_terms( $id, 'reviewfic_category' );

        $source   = ( ! empty( $source_terms )   && ! is_wp_error( $source_terms ) )   ? $source_terms[0]->slug   : '';
        $category = ( ! empty( $category_terms ) && ! is_wp_error( $category_terms ) ) ? $category_terms[0]->slug : '';

        $rows[] = array(
            'title'       => get_the_title(),
            'content'     => get_the_content(),
            'stars'       => get_post_meta( $id, 'reviewfic_review_stars',       true ),
            'name'        => get_post_meta( $id, 'reviewfic_client_name',        true ),
            'designation' => get_post_meta( $id, 'reviewfic_client_designation', true ),
            'company'     => get_post_meta( $id, 'reviewfic_client_company',     true ),
            'source'      => $source,
            'category'    => $category,
            'status'      => get_post_status(),
            'date'        => get_the_date( 'Y-m-d H:i:s' ),
        );
    }
    wp_reset_postdata();
    return $rows;
}

// CSV export
add_action( 'admin_post_reviewfic_export_csv', function () {
    if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );
    check_admin_referer( 'reviewfic_export' );

    $rows = reviewfic_get_all_reviews_data();

    header( 'Content-Type: text/csv; charset=utf-8' );
    header( 'Content-Disposition: attachment; filename="reviewfic-export-' . date( 'Y-m-d' ) . '.csv"' );
    header( 'Pragma: no-cache' );

    $out = fopen( 'php://output', 'w' );
    fputcsv( $out, array( 'title','content','stars','name','designation','company','source','category','status','date' ) );
    foreach ( $rows as $row ) {
        fputcsv( $out, $row );
    }
    fclose( $out );
    exit;
} );

// JSON export
add_action( 'admin_post_reviewfic_export_json', function () {
    if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );
    check_admin_referer( 'reviewfic_export' );

    $rows = reviewfic_get_all_reviews_data();

    header( 'Content-Type: application/json; charset=utf-8' );
    header( 'Content-Disposition: attachment; filename="reviewfic-export-' . date( 'Y-m-d' ) . '.json"' );
    header( 'Pragma: no-cache' );

    echo wp_json_encode( $rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
    exit;
} );


// ── Import handler ─────────────────────────────────────────────────────────

add_action( 'admin_post_reviewfic_import', function () {
    if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );
    check_admin_referer( 'reviewfic_import' );

    $redirect_base = admin_url( 'edit.php?post_type=reviewfic_reviews&page=reviewfic-import-export' );

    if ( empty( $_FILES['rwf_import_file']['name'] ) || $_FILES['rwf_import_file']['error'] !== UPLOAD_ERR_OK ) {
        wp_safe_redirect( add_query_arg( 'rwf_msg', 'no_file', $redirect_base ) );
        exit;
    }

    $file     = $_FILES['rwf_import_file'];
    $ext      = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );
    $tmp      = $file['tmp_name'];
    $imported = 0;
    $skipped  = 0;
    $errors   = array();

    $allowed_statuses = array( 'publish', 'pending', 'draft' );

    // ── Parse file ────────────────────────────────────────────
    $rows = array();

    if ( $ext === 'json' ) {
        $raw = file_get_contents( $tmp );
        $data = json_decode( $raw, true );
        if ( ! is_array( $data ) ) {
            wp_safe_redirect( add_query_arg( 'rwf_msg', 'invalid_json', $redirect_base ) );
            exit;
        }
        $rows = $data;

    } elseif ( $ext === 'csv' ) {
        $handle = fopen( $tmp, 'r' );
        $headers = fgetcsv( $handle );
        if ( ! $headers ) {
            wp_safe_redirect( add_query_arg( 'rwf_msg', 'invalid_csv', $redirect_base ) );
            exit;
        }
        $headers = array_map( 'trim', $headers );
        while ( ( $line = fgetcsv( $handle ) ) !== false ) {
            if ( count( $line ) === count( $headers ) ) {
                $rows[] = array_combine( $headers, $line );
            }
        }
        fclose( $handle );

    } else {
        wp_safe_redirect( add_query_arg( 'rwf_msg', 'bad_type', $redirect_base ) );
        exit;
    }

    // ── Insert reviews ────────────────────────────────────────
    foreach ( $rows as $i => $row ) {
        $content = sanitize_textarea_field( wp_unslash( $row['content'] ?? '' ) );
        $title   = sanitize_text_field( wp_unslash( $row['title']   ?? '' ) );

        if ( empty( $content ) && empty( $title ) ) {
            $skipped++;
            continue;
        }

        $stars       = (float) ( $row['stars']       ?? 5 );
        $stars       = min( 5, max( 0, $stars ) );
        $name        = sanitize_text_field( wp_unslash( $row['name']        ?? '' ) );
        $designation = sanitize_text_field( wp_unslash( $row['designation'] ?? '' ) );
        $company     = sanitize_text_field( wp_unslash( $row['company']     ?? '' ) );
        $source      = sanitize_text_field( wp_unslash( $row['source']      ?? '' ) );
        $category    = sanitize_text_field( wp_unslash( $row['category']    ?? '' ) );
        $status      = in_array( $row['status'] ?? '', $allowed_statuses, true ) ? $row['status'] : 'pending';
        $date        = sanitize_text_field( wp_unslash( $row['date'] ?? '' ) );

        $post_data = array(
            'post_type'    => 'reviewfic_reviews',
            'post_title'   => $title ?: ( $name ? $name . "'s Review" : __( 'Imported Review', 'reviewfic' ) ),
            'post_content' => $content,
            'post_status'  => $status,
        );
        if ( $date && strtotime( $date ) ) {
            $post_data['post_date']     = $date;
            $post_data['post_date_gmt'] = get_gmt_from_date( $date );
        }

        $post_id = wp_insert_post( $post_data );

        if ( is_wp_error( $post_id ) ) {
            $skipped++;
            continue;
        }

        update_post_meta( $post_id, 'reviewfic_review_stars',       $stars );
        update_post_meta( $post_id, 'reviewfic_client_name',        $name );
        update_post_meta( $post_id, 'reviewfic_client_designation', $designation );
        update_post_meta( $post_id, 'reviewfic_client_company',     $company );

        if ( $source ) {
            // Create the term if it doesn't exist
            if ( ! term_exists( $source, 'reviewfic_source' ) ) {
                wp_insert_term( $source, 'reviewfic_source' );
            }
            wp_set_post_terms( $post_id, array( $source ), 'reviewfic_source', false );
        }
        if ( $category ) {
            if ( ! term_exists( $category, 'reviewfic_category' ) ) {
                wp_insert_term( $category, 'reviewfic_category' );
            }
            wp_set_post_terms( $post_id, array( $category ), 'reviewfic_category', false );
        }

        $imported++;
    }

    wp_safe_redirect( add_query_arg( array(
        'rwf_msg'      => 'imported',
        'rwf_imported' => $imported,
        'rwf_skipped'  => $skipped,
    ), $redirect_base ) );
    exit;
} );


// ── Admin page UI ──────────────────────────────────────────────────────────

function reviewfic_import_export_page() {
    $total = wp_count_posts( 'reviewfic_reviews' );
    $count = array_sum( (array) $total );
    ?>
    <div class="wrap rwf-ie-wrap">
        <h1 class="wp-heading-inline"><?php esc_html_e( 'Import / Export', 'reviewfic' ); ?></h1>
        <hr class="wp-header-end">

        <?php reviewfic_ie_notices(); ?>

        <div class="rwf-ie-grid">

            <!-- ── Export ───────────────────────────────── -->
            <div class="rwf-ie-card">
                <div class="rwf-ie-card-header">
                    <span class="dashicons dashicons-download rwf-ie-card-icon"></span>
                    <div>
                        <h2><?php esc_html_e( 'Export Reviews', 'reviewfic' ); ?></h2>
                        <p><?php esc_html_e( 'Download your reviews as a backup or to import into another site.', 'reviewfic' ); ?></p>
                    </div>
                </div>

                <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                    <?php wp_nonce_field( 'reviewfic_export' ); ?>

                    <div class="rwf-ie-format-picker">
                        <label class="rwf-ie-format-option">
                            <input type="radio" name="action" value="reviewfic_export_csv" checked>
                            <div class="rwf-ie-format-body">
                                <span class="rwf-ie-format-badge rwf-ie-csv">CSV</span>
                                <div>
                                    <strong><?php esc_html_e( 'Spreadsheet', 'reviewfic' ); ?></strong>
                                    <span><?php esc_html_e( 'Opens in Excel or Google Sheets', 'reviewfic' ); ?></span>
                                </div>
                            </div>
                        </label>
                        <label class="rwf-ie-format-option">
                            <input type="radio" name="action" value="reviewfic_export_json">
                            <div class="rwf-ie-format-body">
                                <span class="rwf-ie-format-badge rwf-ie-json">JSON</span>
                                <div>
                                    <strong><?php esc_html_e( 'Structured Data', 'reviewfic' ); ?></strong>
                                    <span><?php esc_html_e( 'Ideal for developers or re-importing', 'reviewfic' ); ?></span>
                                </div>
                            </div>
                        </label>
                    </div>

                    <button type="submit" class="button button-primary rwf-ie-btn">
                        <span class="dashicons dashicons-download"></span>
                        <?php esc_html_e( 'Download Export', 'reviewfic' ); ?>
                    </button>
                </form>
            </div>

            <!-- ── Import ───────────────────────────────── -->
            <div class="rwf-ie-card">
                <div class="rwf-ie-card-header">
                    <span class="dashicons dashicons-upload rwf-ie-card-icon"></span>
                    <div>
                        <h2><?php esc_html_e( 'Import Reviews', 'reviewfic' ); ?></h2>
                        <p><?php esc_html_e( 'Upload a CSV or JSON file to bulk-create reviews. Must match the export format.', 'reviewfic' ); ?></p>
                    </div>
                </div>

                <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data">
                    <?php wp_nonce_field( 'reviewfic_import' ); ?>
                    <input type="hidden" name="action" value="reviewfic_import">

                    <div class="rwf-ie-dropzone" id="rwf-ie-dropzone">
                        <span class="dashicons dashicons-media-spreadsheet rwf-ie-drop-icon"></span>
                        <p class="rwf-ie-dropzone-text">
                            <strong><?php esc_html_e( 'Drag & drop your file here', 'reviewfic' ); ?></strong>
                            <span><?php esc_html_e( 'or', 'reviewfic' ); ?> <button type="button" class="rwf-ie-browse"><?php esc_html_e( 'Browse to Upload', 'reviewfic' ); ?></button></span>
                        </p>
                        <p class="rwf-ie-dropzone-hint"><?php esc_html_e( '.csv or .json — exported from Reviewfic', 'reviewfic' ); ?></p>
                        <input type="file" id="rwf_import_file" name="rwf_import_file" accept=".csv,.json" class="rwf-ie-file-input">
                        <p class="rwf-ie-filename" id="rwf-ie-filename"></p>
                    </div>

                    <details class="rwf-ie-format-guide">
                        <summary><?php esc_html_e( 'View required columns / fields', 'reviewfic' ); ?></summary>
                        <table class="rwf-ie-schema">
                            <thead><tr>
                                <th><?php esc_html_e( 'Field', 'reviewfic' ); ?></th>
                                <th><?php esc_html_e( 'Required', 'reviewfic' ); ?></th>
                                <th><?php esc_html_e( 'Notes', 'reviewfic' ); ?></th>
                            </tr></thead>
                            <tbody>
                                <tr><td><code>title</code></td><td><?php esc_html_e( 'No', 'reviewfic' ); ?></td><td><?php esc_html_e( 'Review headline', 'reviewfic' ); ?></td></tr>
                                <tr><td><code>content</code></td><td><?php esc_html_e( 'Yes*', 'reviewfic' ); ?></td><td><?php esc_html_e( 'Review body text', 'reviewfic' ); ?></td></tr>
                                <tr><td><code>stars</code></td><td><?php esc_html_e( 'No', 'reviewfic' ); ?></td><td><?php esc_html_e( '1–5, defaults to 5', 'reviewfic' ); ?></td></tr>
                                <tr><td><code>name</code></td><td><?php esc_html_e( 'No', 'reviewfic' ); ?></td><td><?php esc_html_e( 'Reviewer name', 'reviewfic' ); ?></td></tr>
                                <tr><td><code>designation</code></td><td><?php esc_html_e( 'No', 'reviewfic' ); ?></td><td><?php esc_html_e( 'Job title', 'reviewfic' ); ?></td></tr>
                                <tr><td><code>company</code></td><td><?php esc_html_e( 'No', 'reviewfic' ); ?></td><td><?php esc_html_e( 'Company name', 'reviewfic' ); ?></td></tr>
                                <tr><td><code>source</code></td><td><?php esc_html_e( 'No', 'reviewfic' ); ?></td><td><?php esc_html_e( 'Platform slug, e.g. google', 'reviewfic' ); ?></td></tr>
                                <tr><td><code>category</code></td><td><?php esc_html_e( 'No', 'reviewfic' ); ?></td><td><?php esc_html_e( 'Category slug', 'reviewfic' ); ?></td></tr>
                                <tr><td><code>status</code></td><td><?php esc_html_e( 'No', 'reviewfic' ); ?></td><td><?php esc_html_e( 'publish / pending / draft', 'reviewfic' ); ?></td></tr>
                                <tr><td><code>date</code></td><td><?php esc_html_e( 'No', 'reviewfic' ); ?></td><td><?php esc_html_e( 'Y-m-d H:i:s', 'reviewfic' ); ?></td></tr>
                            </tbody>
                        </table>
                        <p class="rwf-ie-schema-note"><?php esc_html_e( '* Rows where both title and content are empty are skipped.', 'reviewfic' ); ?></p>
                    </details>

                    <button type="submit" class="button button-primary rwf-ie-btn">
                        <span class="dashicons dashicons-upload"></span>
                        <?php esc_html_e( 'Import File', 'reviewfic' ); ?>
                    </button>
                </form>
            </div>

        </div><!-- .rwf-ie-grid -->
    </div>

    <script>
    (function () {
        var dropzone  = document.getElementById('rwf-ie-dropzone');
        var fileInput = document.getElementById('rwf_import_file');
        var fileLabel = document.getElementById('rwf-ie-filename');
        var browse    = dropzone ? dropzone.querySelector('.rwf-ie-browse') : null;
        if (!dropzone || !fileInput) return;

        function setFile(name) {
            fileLabel.textContent = name ? name : '';
            dropzone.classList.toggle('rwf-ie-has-file', !!name);
        }

        fileInput.addEventListener('change', function () {
            setFile(fileInput.files[0] ? fileInput.files[0].name : '');
        });

        if (browse) {
            browse.addEventListener('click', function (e) {
                e.stopPropagation();
                fileInput.click();
            });
        }

        dropzone.addEventListener('dragover', function (e) {
            e.preventDefault();
            dropzone.classList.add('rwf-ie-drag-over');
        });
        dropzone.addEventListener('dragleave', function () {
            dropzone.classList.remove('rwf-ie-drag-over');
        });
        dropzone.addEventListener('drop', function (e) {
            e.preventDefault();
            dropzone.classList.remove('rwf-ie-drag-over');
            var file = e.dataTransfer.files[0];
            if (file) {
                var dt = new DataTransfer();
                dt.items.add(file);
                fileInput.files = dt.files;
                setFile(file.name);
            }
        });
    })();
    </script>
    <?php
}

// ── Notices ────────────────────────────────────────────────────────────────

function reviewfic_ie_notices() {
    $msg = sanitize_text_field( $_GET['rwf_msg'] ?? '' );
    if ( ! $msg ) return;

    $messages = array(
        'imported'     => sprintf(
            __( '%d review(s) imported successfully. %d row(s) skipped (empty content).', 'reviewfic' ),
            intval( $_GET['rwf_imported'] ?? 0 ),
            intval( $_GET['rwf_skipped']  ?? 0 )
        ),
        'no_file'      => __( 'Please select a file to import.',                          'reviewfic' ),
        'bad_type'     => __( 'Unsupported file type. Please upload a .csv or .json file.', 'reviewfic' ),
        'invalid_json' => __( 'Could not parse the JSON file. Please check the format.',   'reviewfic' ),
        'invalid_csv'  => __( 'Could not parse the CSV file. Please check the format.',    'reviewfic' ),
    );

    $type = $msg === 'imported' ? 'success' : 'error';
    $text = $messages[ $msg ] ?? '';
    if ( ! $text ) return;

    printf(
        '<div class="notice notice-%s is-dismissible"><p>%s</p></div>',
        esc_attr( $type ),
        esc_html( $text )
    );
}
