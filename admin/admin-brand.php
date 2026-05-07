<?php
if (!defined('ABSPATH')) exit;

// Detect if we're on a Reviewfic admin screen
function reviewfic_is_brand_screen() {
    $screen = get_current_screen();
    if (!$screen) return false;
    return in_array($screen->post_type, array('reviewfic_reviews', 'reviewfic_config'), true);
}

// Inject scoped CSS overrides for Reviewfic screens
function reviewfic_admin_brand_styles() {
    if (!reviewfic_is_brand_screen()) return;
    ?>
    <style id="rwf-admin-brand-css">

    /* ── Branded header bar ────────────────────────────────── */
    .rwf-admin-header {
        background: linear-gradient(135deg, #0E9F6E 0%, #057A55 100%);
        margin: -8px -20px 24px;
        padding: 15px 22px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: 0 2px 10px rgba(14,159,110,.25);
    }

    .rwf-admin-logo {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 17px;
        font-weight: 800;
        color: #fff;
        letter-spacing: -0.02em;
        text-shadow: 0 1px 2px rgba(0,0,0,.1);
    }

    .rwf-admin-logo svg {
        width: 20px;
        height: 20px;
        fill: #fff;
        filter: drop-shadow(0 1px 2px rgba(0,0,0,.15));
    }

    .rwf-admin-header-sep {
        width: 1px;
        height: 16px;
        background: rgba(255,255,255,.3);
    }

    .rwf-admin-page-label {
        font-size: 13px;
        color: rgba(255,255,255,.88);
        font-weight: 500;
    }

    .rwf-admin-header-badge {
        margin-left: auto;
        background: rgba(255,255,255,.15);
        border: 1px solid rgba(255,255,255,.25);
        color: rgba(255,255,255,.9);
        font-size: 11px;
        font-weight: 600;
        padding: 2px 9px;
        border-radius: 20px;
        letter-spacing: 0.03em;
    }

    /* ── "Add New" / "Create New" action buttons ─────────── */
    .post-type-reviewfic_reviews .page-title-action,
    .post-type-reviewfic_config .page-title-action {
        background: #0E9F6E !important;
        border-color: #057A55 !important;
        color: #fff !important;
        border-radius: 6px !important;
        font-weight: 600 !important;
        box-shadow: 0 1px 3px rgba(14,159,110,.35) !important;
        text-shadow: none !important;
    }
    .post-type-reviewfic_reviews .page-title-action:hover,
    .post-type-reviewfic_config .page-title-action:hover {
        background: #057A55 !important;
        border-color: #046648 !important;
        color: #fff !important;
    }

    /* ── Primary buttons (Publish, Copy Shortcode, etc.) ─── */
    .post-type-reviewfic_reviews .button-primary,
    .post-type-reviewfic_config .button-primary {
        background: #0E9F6E !important;
        border-color: #057A55 !important;
        text-shadow: none !important;
        box-shadow: 0 1px 2px rgba(0,0,0,.12), 0 1px 3px rgba(14,159,110,.3) !important;
    }
    .post-type-reviewfic_reviews .button-primary:hover,
    .post-type-reviewfic_config .button-primary:hover,
    .post-type-reviewfic_reviews .button-primary:focus,
    .post-type-reviewfic_config .button-primary:focus {
        background: #057A55 !important;
        border-color: #046648 !important;
        box-shadow: 0 1px 2px rgba(0,0,0,.12), 0 0 0 2px rgba(14,159,110,.25) !important;
    }

    /* ── List table: row title links ─────────────────────── */
    .post-type-reviewfic_reviews .column-title .row-title,
    .post-type-reviewfic_config .column-title .row-title {
        color: #0E9F6E !important;
        font-weight: 600;
    }
    .post-type-reviewfic_reviews .column-title .row-title:hover,
    .post-type-reviewfic_config .column-title .row-title:hover {
        color: #057A55 !important;
    }

    /* ── List table: sortable header links ───────────────── */
    .post-type-reviewfic_reviews .wp-list-table th a,
    .post-type-reviewfic_config .wp-list-table th a {
        color: #0E9F6E !important;
    }
    .post-type-reviewfic_reviews .wp-list-table th.sortedDesc a,
    .post-type-reviewfic_config .wp-list-table th.sortedDesc a,
    .post-type-reviewfic_reviews .wp-list-table th.sortedAsc a,
    .post-type-reviewfic_config .wp-list-table th.sortedAsc a {
        color: #057A55 !important;
    }

    /* ── List table: zebra & hover ───────────────────────── */
    .post-type-reviewfic_reviews .wp-list-table tbody tr:hover td,
    .post-type-reviewfic_config .wp-list-table tbody tr:hover td {
        background: #F0FDF4;
    }

    /* ── Subsubsub (All | Published | Draft) ─────────────── */
    .post-type-reviewfic_reviews .subsubsub a.current,
    .post-type-reviewfic_config .subsubsub a.current {
        color: #0E9F6E !important;
        font-weight: 700;
    }

    /* ── Post title input ────────────────────────────────── */
    .post-type-reviewfic_reviews #title,
    .post-type-reviewfic_config #title {
        border-radius: 6px !important;
        transition: border-color .15s, box-shadow .15s;
    }
    .post-type-reviewfic_reviews #title:focus,
    .post-type-reviewfic_config #title:focus {
        border-color: #0E9F6E !important;
        box-shadow: 0 0 0 2px rgba(14,159,110,.2) !important;
        outline: none;
    }

    /* ── Postbox header accent ───────────────────────────── */
    .post-type-reviewfic_reviews #reviewfic_meta_box,
    .post-type-reviewfic_reviews .postbox,
    .post-type-reviewfic_config .postbox {
        border-radius: 8px;
        overflow: hidden;
    }

    /* ── "Your Shortcode" sidebar box ────────────────────── */
    #rwf_config_shortcode {
        border-left: 3px solid #0E9F6E !important;
    }

    #rwf_config_shortcode .postbox-header {
        background: #ECFDF5 !important;
        border-bottom-color: #D1FAE5 !important;
    }

    #rwf_config_shortcode .postbox-header h2 {
        color: #057A55 !important;
    }

    .rwf-sc-display code {
        background: #1a2e2a !important;
        color: #6ee7b7 !important;
    }

    .rwf-sc-display .button-primary {
        width: 100%;
        text-align: center;
        justify-content: center;
    }

    /* ── Taxonomy term links in list table ───────────────── */
    .post-type-reviewfic_reviews .column-taxonomy-reviewfic_source a {
        color: #0E9F6E;
    }

    /* ── Action links (Edit | Quick Edit | Trash) ────────── */
    .post-type-reviewfic_reviews .row-actions span a,
    .post-type-reviewfic_config .row-actions span a {
        color: #0E9F6E;
    }
    .post-type-reviewfic_reviews .row-actions .trash a,
    .post-type-reviewfic_config .row-actions .trash a {
        color: #ef4444;
    }

    </style>
    <?php
}
add_action('admin_head', 'reviewfic_admin_brand_styles');


// Inject the branded header bar into .wrap via JS
function reviewfic_admin_brand_header() {
    if (!reviewfic_is_brand_screen()) return;
    $screen = get_current_screen();

    $labels = array(
        'reviewfic_reviews' => array(
            'add'  => 'Add New Review',
            'edit' => 'Edit Review',
            'list' => 'All Reviews',
        ),
        'reviewfic_config' => array(
            'add'  => 'Create Shortcode',
            'edit' => 'Edit Shortcode',
            'list' => 'Shortcode Generator',
        ),
    );

    $map   = $labels[$screen->post_type] ?? $labels['reviewfic_reviews'];
    $label = $screen->action === 'add' ? $map['add']
           : ($screen->base === 'post' ? $map['edit'] : $map['list']);

    $star_svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
    ?>
    <script>
    (function() {
        function injectHeader() {
            var wrap = document.querySelector('.wrap');
            if (!wrap || document.getElementById('rwf-admin-header')) return;

            var el = document.createElement('div');
            el.id = 'rwf-admin-header';
            el.className = 'rwf-admin-header';
            el.innerHTML =
                '<div class="rwf-admin-logo"><?php echo $star_svg; ?>Reviewfic</div>' +
                '<div class="rwf-admin-header-sep"></div>' +
                '<div class="rwf-admin-page-label"><?php echo esc_js($label); ?></div>' +
                '<span class="rwf-admin-header-badge">v<?php echo esc_js(get_plugin_data(WP_PLUGIN_DIR . "/reviewfic/reviewfic.php")["Version"] ?? ""); ?></span>';

            wrap.insertBefore(el, wrap.firstChild);
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', injectHeader);
        } else {
            injectHeader();
        }
    })();
    </script>
    <?php
}
add_action('admin_footer', 'reviewfic_admin_brand_header');
