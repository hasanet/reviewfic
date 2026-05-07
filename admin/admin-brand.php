<?php
if (!defined('ABSPATH')) exit;

// Detect if we're on a Reviewfic admin screen
function reviewfic_is_brand_screen() {
    $screen = get_current_screen();
    if (!$screen) return false;
    return in_array($screen->post_type, array('reviewfic_reviews', 'reviewfic_config'), true);
}

// Inject scoped CSS — only what we want to change, nothing more
function reviewfic_admin_brand_styles() {
    if (!reviewfic_is_brand_screen()) return;
    ?>
    <style id="rwf-admin-brand-css">

    /* ── Branded header bar ─────────────────────────── */
    #rwf-admin-header {
        background: linear-gradient(135deg, #0E9F6E 0%, #057A55 100%);
        padding: 15px 22px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: 0 2px 10px rgba(14,159,110,.25);
        /* Full-width: cancel #wpbody-content padding (20px each side) */
        margin: -1px -20px 0;
        width: calc(100% + 40px);
        box-sizing: border-box;
    }

    .rwf-admin-logo {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 17px;
        font-weight: 800;
        color: #fff;
        letter-spacing: -0.02em;
    }

    .rwf-admin-logo svg {
        width: 20px;
        height: 20px;
        fill: #fff;
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

    /* ── "Add New" / "Create New" action buttons ─────── */
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

    /* ── Primary buttons (Publish, Copy Shortcode) ───── */
    .post-type-reviewfic_reviews .button-primary,
    .post-type-reviewfic_config .button-primary {
        background: #0E9F6E !important;
        border-color: #057A55 !important;
        text-shadow: none !important;
        box-shadow: 0 1px 2px rgba(0,0,0,.1), 0 1px 3px rgba(14,159,110,.3) !important;
    }
    .post-type-reviewfic_reviews .button-primary:hover,
    .post-type-reviewfic_config .button-primary:hover,
    .post-type-reviewfic_reviews .button-primary:focus,
    .post-type-reviewfic_config .button-primary:focus {
        background: #057A55 !important;
        border-color: #046648 !important;
    }

    /* ── Post title input focus ───────────────────────── */
    .post-type-reviewfic_reviews #title:focus,
    .post-type-reviewfic_config #title:focus {
        border-color: #0E9F6E !important;
        box-shadow: 0 0 0 2px rgba(14,159,110,.2) !important;
        outline: none;
    }

    /* ── "Your Shortcode" sidebar meta box ───────────── */
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

    </style>
    <?php
}
add_action('admin_head', 'reviewfic_admin_brand_styles');


// Inject branded header bar — inserted before .wrap inside #wpbody-content
// so it fills the full width of the content area naturally.
function reviewfic_admin_brand_header() {
    if (!reviewfic_is_brand_screen()) return;

    $screen = get_current_screen();

    $labels = array(
        'reviewfic_reviews' => array(
            'add'  => 'Add New Review',
            'edit' => 'Edit Review',
            'list' => 'All Reviews',
        ),
        'reviewfic_config'  => array(
            'add'  => 'Create Shortcode',
            'edit' => 'Edit Shortcode',
            'list' => 'Shortcode Generator',
        ),
    );

    $map   = $labels[$screen->post_type] ?? $labels['reviewfic_reviews'];
    $label = $screen->action === 'add'    ? $map['add']
           : ($screen->base === 'post'   ? $map['edit'] : $map['list']);

    $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/reviewfic/reviewfic.php');
    $version     = $plugin_data['Version'] ?? '';
    ?>
    <script>
    (function () {
        function injectHeader() {
            if (document.getElementById('rwf-admin-header')) return;

            // Insert BEFORE .wrap inside #wpbody-content for true full-width
            var bodyContent = document.getElementById('wpbody-content');
            var wrap = bodyContent ? bodyContent.querySelector('.wrap') : null;
            if (!bodyContent || !wrap) return;

            var el = document.createElement('div');
            el.id = 'rwf-admin-header';
            el.innerHTML =
                '<div class="rwf-admin-logo">' +
                    '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>' +
                    'Reviewfic' +
                '</div>' +
                '<div class="rwf-admin-header-sep"></div>' +
                '<div class="rwf-admin-page-label"><?php echo esc_js($label); ?></div>' +
                '<span class="rwf-admin-header-badge">v<?php echo esc_js($version); ?></span>';

            bodyContent.insertBefore(el, wrap);
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
