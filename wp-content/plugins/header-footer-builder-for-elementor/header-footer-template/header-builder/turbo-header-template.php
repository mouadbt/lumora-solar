<?php
if (!defined('ABSPATH')) exit;

/**
 * 1. Register "Header Template" Custom Post Type
 */
add_action('init', function () {
    register_post_type('tahefobu_header', [
        'labels' => [
            'name' => __('Header Templates', 'header-footer-builder-for-elementor'),
            'singular_name' => __('Header Template', 'header-footer-builder-for-elementor'),
            'menu_name' => __('Header Template', 'header-footer-builder-for-elementor'),
            'add_new' => __('Add Header Template', 'header-footer-builder-for-elementor'),
            'add_new_item' => __('Add New Header Template', 'header-footer-builder-for-elementor'),
            'edit_item' => __('Edit Header Template', 'header-footer-builder-for-elementor'),
            'new_item' => __('New Header Template', 'header-footer-builder-for-elementor'),
            'view_item' => __('View Header Template', 'header-footer-builder-for-elementor'),
            'all_items' => __('Header Templates', 'header-footer-builder-for-elementor'),
        ],
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => false,
        'supports' => ['title', 'editor', 'elementor'],
        'show_in_rest' => true,
        'exclude_from_search' => true,
        'rewrite' => false,
        'capability_type' => 'post',
    ]);
});

/**
 * 2. Enable Elementor Editor for this CPT
 */
add_action('elementor/init', function () {
    add_post_type_support('tahefobu_header', 'elementor');
});

/**
 * 3. Inject the Name Popup Modal in Admin
 */
add_action('admin_footer-edit.php', 'tahefobu_render_header_template_popup');
function tahefobu_render_header_template_popup() {
    $screen = get_current_screen();
    if ($screen->post_type !== 'tahefobu_header') return;

    $pages = get_pages();
    ?>
    <div id="tahefobu-header-template-popup" class="tahefobu-header-popup-overlay" style="display:none;">
        <div class="tahefobu-header-popup-modal">
            <div class="modal-header-style">
                <h2 class="tahefobu-create-header-popup-headline"><?php esc_html_e('Create New Header', 'header-footer-builder-for-elementor'); ?></h2>
            </div>

            <p class="header-title-modal"><?php esc_html_e('Header Name', 'header-footer-builder-for-elementor'); ?></p>
            <input type="text" id="tahefobu-header-template-title" placeholder="Type header name" />

            <div class="modal-include-exclude-style">
                <label><?php esc_html_e('Include Pages:', 'header-footer-builder-for-elementor'); ?><span><?php esc_html_e(' Optional – use this to show the header on specific pages.', 'header-footer-builder-for-elementor'); ?></span></label><br>
                <label><input type="checkbox" id="select_all_include"> <?php esc_html_e('Select All','header-footer-builder-for-elementor'); ?></label><br>
            </div>

            <select class="tahefobu-header-template-title" id="tahefobu_include_pages" multiple>
                <?php foreach ($pages as $page): ?>
                    <option value="<?php echo esc_attr($page->ID); ?>"><?php echo esc_html($page->post_title); ?></option>
                <?php endforeach; ?>
            </select>

            <div class="modal-include-exclude-style">
                <label><?php esc_html_e('Set Display Condition:', 'header-footer-builder-for-elementor'); ?></label>
            </div>

            <select id="tahefobu_display_targets" multiple>
                <option value="entire_site"><?php esc_html_e('Entire Site', 'header-footer-builder-for-elementor'); ?></option>
                <option value="all_posts"><?php esc_html_e('All Blog Posts', 'header-footer-builder-for-elementor'); ?></option>
                <option value="all_archives"><?php esc_html_e('All Archive Pages', 'header-footer-builder-for-elementor'); ?></option>
                <?php if (class_exists('WooCommerce')) : ?>
                    <option value="all_products"><?php esc_html_e('All WooCommerce Products', 'header-footer-builder-for-elementor'); ?></option>
                    <option value="all_woo"><?php esc_html_e('All WooCommerce Pages', 'header-footer-builder-for-elementor'); ?></option>
                <?php endif; ?>
            </select>

            <div class="modal-include-exclude-style">
                <label><?php esc_html_e('Exclude Pages:', 'header-footer-builder-for-elementor'); ?><span><?php esc_html_e(' Optional – use this to hide the header on specific pages.', 'header-footer-builder-for-elementor'); ?></span></label><br>
                <label><input type="checkbox" id="select_all_exclude"> <?php esc_html_e('Select All','header-footer-builder-for-elementor'); ?></label><br>
            </div>

            <select id="tahefobu_exclude_pages" multiple style="width:100%; min-height:100px;">
                <?php foreach ($pages as $page): ?>
                    <option value="<?php echo esc_attr($page->ID); ?>"><?php echo esc_html($page->post_title); ?></option>
                <?php endforeach; ?>
            </select>

            <div class="tahefobu-header-popup-header-style">
                <h3><?php esc_html_e('Header Style', 'header-footer-builder-for-elementor'); ?></h3>
                <div class="tahefobu-style-options">
                    <label><input type="checkbox" id="tahefobu_is_sticky" /> <?php esc_html_e('Make Header Sticky', 'header-footer-builder-for-elementor'); ?></label>
                    <label><input type="checkbox" id="tahefobu_has_animation" /> <?php esc_html_e('Enable Scroll Animation', 'header-footer-builder-for-elementor'); ?></label>
                </div>
                <div class="tahefobu-header-popup-actions" style="margin-top: 15px;">
                    <button class="button tahefobu-header-creat-edit-button" id="tahefobu-create-template"><?php esc_html_e('Create', 'header-footer-builder-for-elementor'); ?></button>
                    <button class="button tahefobu-header-cancel-button" id="tahefobu-cancel-template"><?php esc_html_e('Cancel', 'header-footer-builder-for-elementor'); ?></button>
                </div>
            </div>
        </div>
    </div>
    <?php
}

/**
 * 4. Ajax Handler to Create Template
 */
add_action('wp_ajax_tahefobu_create_header_template', function () {
    check_ajax_referer('tahefobu_save_conditions_nonce', '_ajax_nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Permission denied']);
    }

    $title = isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : '';
    if (empty($title)) {
        wp_send_json_error(['message' => 'Template name is required']);
    }

    $post_id = wp_insert_post([
        'post_title'   => $title,
        'post_type'    => 'tahefobu_header',
        'post_status'  => 'publish',
    ]);

    if (is_wp_error($post_id)) {
        wp_send_json_error(['message' => 'Could not create template']);
    }

    update_post_meta( $post_id, '_tahefobu_is_enabled', '1' ); // default Active

    $include_pages = array_map('intval', (array) ($_POST['include_pages'] ?? []));
    $exclude_pages = array_map('intval', (array) ($_POST['exclude_pages'] ?? []));
    update_post_meta($post_id, '_tahefobu_include_pages', $include_pages);
    update_post_meta($post_id, '_tahefobu_exclude_pages', $exclude_pages);

    $is_sticky = !empty($_POST['is_sticky']) ? 1 : 0;
    $has_animation = !empty($_POST['has_animation']) ? 1 : 0;
    update_post_meta($post_id, '_tahefobu_is_sticky', $is_sticky);
    update_post_meta($post_id, '_tahefobu_has_animation', $has_animation);

    $display_targets = [];
    if ( isset( $_POST['display_targets'] ) && is_array( $_POST['display_targets'] ) ) {
        $display_targets = array_map( 'sanitize_text_field', wp_unslash( $_POST['display_targets'] ) );
    }
    update_post_meta( $post_id, '_tahefobu_display_targets', $display_targets );

    // Bust the frontend matching cache so the new template is picked up immediately.
    delete_transient( 'tahefobu_header_templates_meta' );

    $edit_link = admin_url("post.php?post={$post_id}&action=elementor");
    wp_send_json_success(['edit_link' => $edit_link]);
});

/**
 * Add Nonce for Security (enqueue only on the tahefobu_header list screen)
 */
add_action('admin_enqueue_scripts', function ($hook) {
    if ( ! function_exists('get_current_screen') ) return;
    $screen = get_current_screen();
    if ( ! $screen ) return;

    if ( $screen->base !== 'edit' || $screen->post_type !== 'tahefobu_header' ) return;

    wp_enqueue_style(
        'select2',
        TAHEFOBU_HEADER_FOOTER_BUILDER_FOR_ELEMENTOR_PLUGIN_URL . 'assets/vendor/select2/select2.min.css',
        [],
        '4.1.0'
    );
    wp_enqueue_script(
        'select2',
        TAHEFOBU_HEADER_FOOTER_BUILDER_FOR_ELEMENTOR_PLUGIN_URL . 'assets/vendor/select2/select2.min.js',
        ['jquery'],
        '4.1.0',
        true
    );

    wp_enqueue_style(
        'tahefobu-popup-css',
        TAHEFOBU_HEADER_FOOTER_BUILDER_FOR_ELEMENTOR_PLUGIN_URL . 'assets/css/turbo-header-template-popup.css',
        [],
        TAHEFOBU_HEADER_FOOTER_BUILDER_FOR_ELEMENTOR_PLUGIN_VERSION
    );
    wp_enqueue_script(
        'tahefobu-popup',
        TAHEFOBU_HEADER_FOOTER_BUILDER_FOR_ELEMENTOR_PLUGIN_URL . 'assets/js/turbo-header-template-popup.js',
        ['jquery', 'select2'],
        TAHEFOBU_HEADER_FOOTER_BUILDER_FOR_ELEMENTOR_PLUGIN_VERSION,
        true
    );
    wp_localize_script(
        'tahefobu-popup',
        'tahefobu_header_condition_nonce',
        [ 'nonce' => wp_create_nonce( 'tahefobu_save_conditions_nonce' ) ]
    );
});

/**
 * 5. Hidden Admin Page (Optional)
 */
add_action('admin_menu', function () {
    add_submenu_page(null, __('Create Header Template', 'header-footer-builder-for-elementor'), '', 'manage_options', 'tahefobu_header_template_popup', 'tahefobu_render_header_popup');
});
function tahefobu_render_header_popup() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Create Header Template', 'header-footer-builder-for-elementor'); ?></h1>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="tahefobu_create_header_template">
            <?php wp_nonce_field('tahefobu_create_header_template_nonce'); ?>
            <input type="text" name="tahefobu_header_template_title" placeholder="Enter header name" required>
            <input type="submit" value="<?php esc_attr_e('Create & Edit with Elementor','header-footer-builder-for-elementor'); ?>" class="button button-primary">
        </form>
    </div>
    <?php
}

/**
 * 6. Manual Redirect on POST Create (via form)
 */
add_action('admin_post_tahefobu_create_header_template', function () {
    if (!current_user_can('manage_options') || !check_admin_referer('tahefobu_create_header_template_nonce')) {
        wp_die( esc_html__( 'Permission denied', 'header-footer-builder-for-elementor' ) );
    }

    $title = isset( $_POST['tahefobu_header_template_title'] ) ? sanitize_text_field( wp_unslash( $_POST['tahefobu_header_template_title'] ) ) : '';
    $post_id = wp_insert_post([
        'post_type' => 'tahefobu_header',
        'post_title' => $title,
        'post_status' => 'publish',
    ]);
    update_post_meta( $post_id, '_tahefobu_is_enabled', '1' );

        if ( $post_id && ! is_wp_error( $post_id ) ) {

        $redirect_url = admin_url(
            'post.php?post=' . intval( $post_id ) . '&action=elementor'
        );

        wp_safe_redirect( $redirect_url );
        exit;

    } else {

        wp_die(
            esc_html__( 'Error creating template', 'header-footer-builder-for-elementor' )
        );
    }
});

/**
 * Matching function (covers WooCommerce, archives, includes/excludes)
 * – No direct input; safe without nonce.
 * – Uses a transient to cache template meta for performance.
 */
function tahefobu_get_matching_header_template_id() {
    if ( is_admin() || wp_doing_ajax() ) return null;

    $current_page_id = get_queried_object_id();

    // If the current page uses the Elementor 'Canvas' page layout, do not match headers (Canvas intentionally has no theme header).
    if ( $current_page_id ) {
        $page_settings = get_post_meta( $current_page_id, '_elementor_page_settings', true );
        if ( is_array( $page_settings ) && isset( $page_settings['page_layout'] ) && 'elementor_canvas' === $page_settings['page_layout'] ) {
            return null;
        }
        $wp_template = get_post_meta( $current_page_id, '_wp_page_template', true );
        if ( ! empty( $wp_template ) && false !== strpos( $wp_template, 'elementor_canvas' ) ) {
            return null;
        }
    }

    // Load all header template meta from cache or DB.
    $cached = get_transient( 'tahefobu_header_templates_meta' );
    if ( false === $cached ) {
        $posts = get_posts( [
            'post_type'      => 'tahefobu_header',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'no_found_rows'  => true,
        ] );

        $cached = [];
        foreach ( $posts as $post ) {
            // Fetch all meta in one call per post instead of 3 separate get_post_meta() calls.
            $all_meta = get_post_custom( $post->ID );
            $cached[] = [
                'id'      => $post->ID,
                'include' => isset( $all_meta['_tahefobu_include_pages'][0] )
                    ? array_map( 'intval', (array) maybe_unserialize( $all_meta['_tahefobu_include_pages'][0] ) )
                    : [],
                'exclude' => isset( $all_meta['_tahefobu_exclude_pages'][0] )
                    ? array_map( 'intval', (array) maybe_unserialize( $all_meta['_tahefobu_exclude_pages'][0] ) )
                    : [], // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- array key for template data, not a WP_Query parameter
                'targets' => isset( $all_meta['_tahefobu_display_targets'][0] )
                    ? array_map( 'sanitize_key', (array) maybe_unserialize( $all_meta['_tahefobu_display_targets'][0] ) )
                    : [],
            ];
        }
        // Cache for 12 hours; busted on save/delete via tahefobu_bust_header_template_cache().
        set_transient( 'tahefobu_header_templates_meta', $cached, 12 * HOUR_IN_SECONDS );
    }

    $woo_pages = class_exists( 'WooCommerce' ) && function_exists( 'wc_get_page_id' ) ? [
        'shop'      => wc_get_page_id( 'shop' ),
        'cart'      => wc_get_page_id( 'cart' ),
        'checkout'  => wc_get_page_id( 'checkout' ),
        'myaccount' => wc_get_page_id( 'myaccount' ),
    ] : [];

    $global_fallback = null;

    foreach ( $cached as $header ) {
        $include         = $header['include'];
        $exclude         = $header['exclude'];
        $display_targets = $header['targets'];

        // Exclusions
        if ( $current_page_id && in_array( $current_page_id, $exclude, true ) ) {
            continue;
        }

        // --- WooCommerce first ---
        if ( in_array( 'all_products', $display_targets, true )
            && class_exists( 'WooCommerce' )
            && is_singular( 'product' )
        ) {
            return $header['id'];
        }

        if ( in_array( 'all_woo', $display_targets, true ) && class_exists( 'WooCommerce' ) ) {
            if ( ( function_exists( 'is_shop' ) && is_shop() )
                || ( function_exists( 'is_cart' ) && is_cart() )
                || ( function_exists( 'is_checkout' ) && is_checkout() )
                || ( function_exists( 'is_account_page' ) && is_account_page() )
                || ( function_exists( 'is_singular' ) && is_singular( 'product' ) )
                || ( function_exists( 'is_product_taxonomy' ) && is_product_taxonomy() )
            ) {
                return $header['id'];
            }
        }

        // --- Other targets ---
        if ( in_array( 'all_posts', $display_targets, true ) && is_singular( 'post' ) ) return $header['id'];
        if ( in_array( 'all_archives', $display_targets, true ) && is_archive() ) return $header['id'];

        // --- Specific include rules ---
        if ( $current_page_id > 0 && ! empty( $include ) ) {
            if ( in_array( $current_page_id, $include, true ) ) return $header['id'];

            foreach ( $woo_pages as $woo_id ) {
                if ( $woo_id && in_array( $woo_id, $include, true ) && class_exists( 'WooCommerce' ) ) {
                    if ( is_page( $woo_id )
                        || ( function_exists( 'is_shop' ) && is_shop() && $woo_id === wc_get_page_id( 'shop' ) )
                    ) {
                        return $header['id'];
                    }
                }
            }
        }

        // --- Entire site as last fallback ---
        if ( in_array( 'entire_site', $display_targets, true ) ) {
            $global_fallback = $header['id'];
        }
    }

    return $global_fallback;
}

/**
 * Bust the header template matching cache.
 * Called on save_post and delete_post for our CPT.
 */
function tahefobu_bust_header_template_cache( $post_id ) {
    if ( get_post_type( $post_id ) === 'tahefobu_header' ) {
        delete_transient( 'tahefobu_header_templates_meta' );
    }
}
add_action( 'save_post',   'tahefobu_bust_header_template_cache' );
add_action( 'delete_post', 'tahefobu_bust_header_template_cache' );

/**
 * NEW: Decide early if header will render (so CSS/body_class can apply in time)
 * Security: valid Elementor preview requests skip our header.
 */
add_action( 'template_redirect', function () {
    if ( is_admin() || wp_doing_ajax() ) return;
    if ( is_singular( 'tahefobu_header' ) || is_singular( 'tahefobu_footer' ) ) return;

    // Elementor preview gating
    if ( isset( $_GET['elementor-preview'] ) ) {
        $raw_id = filter_input( INPUT_GET, 'elementor-preview', FILTER_SANITIZE_NUMBER_INT );
        if ( ! $raw_id ) return;
        $pid   = absint( $raw_id );
        $nonce = filter_input( INPUT_GET, 'tahefobu_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

        // If the preview is specifically for one of our CPTs, require nonce + caps and skip marking for render.
        if ( $pid && in_array( get_post_type( $pid ), [ 'tahefobu_header', 'tahefobu_footer' ], true ) ) {
            if ( ! $nonce || ! wp_verify_nonce( $nonce, 'tahefobu_preview_' . $pid ) ) return;
            if ( ! is_user_logged_in() || ! current_user_can( 'edit_post', $pid ) ) return;
            // Valid preview of our CPT → do not mark for render
            return;
        }

        // For previews of normal pages (editor/preview), continue so our header can be matched/rendered inside editor.
    }

    $header_id = tahefobu_get_matching_header_template_id();
    if ( $header_id && get_post_type( $header_id ) === 'tahefobu_header' ) {
        $GLOBALS['tahefobu_header_will_render'] = true;
        $GLOBALS['tahefobu_header_template_id'] = $header_id;

        // Enqueue the Elementor post CSS for the header template.
        if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
            $css_file = new \Elementor\Core\Files\CSS\Post( $header_id );
            $css_file->enqueue();
        }

        // Tell Elementor's atomic styles system that this post will be rendered.
        // This registers the post ID so atomic CSS gets generated during wp_head().
        // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Elementor's own hook
        do_action( 'elementor/post/render', $header_id );
    }
}, 9 );

/**
 * Add body class when our header will render
 */
add_filter( 'body_class', function ( $classes ) {
    if ( ! empty( $GLOBALS['tahefobu_header_will_render'] ) ) {
        $classes[] = 'turbo-hide-theme-header';
    }
    return $classes;
} );

/**
 * Enqueue CSS to hide theme headers when our header will render
 */
add_action( 'wp_enqueue_scripts', function () {
    if ( empty( $GLOBALS['tahefobu_header_will_render'] ) ) {
        return;
    }

    $handle = 'tahefobu-header-style';
    if ( ! wp_style_is( $handle, 'registered' ) ) {
        wp_register_style( $handle, false, [], TAHEFOBU_HEADER_FOOTER_BUILDER_FOR_ELEMENTOR_PLUGIN_VERSION );
    }
    if ( ! wp_style_is( $handle, 'enqueued' ) ) {
        wp_enqueue_style( $handle );
    }

    // Scope every selector under the body class so it only applies when we render
    $css = '
    body.turbo-hide-theme-header header,
    body.turbo-hide-theme-header .site-header,
    body.turbo-hide-theme-header .main-header,
    body.turbo-hide-theme-header #masthead,
    body.turbo-hide-theme-header .ast-site-header,
    body.turbo-hide-theme-header .storefront-header,
    body.turbo-hide-theme-header .generatepress-header,
    body.turbo-hide-theme-header .neve-header,
    body.turbo-hide-theme-header .hello-elementor-header,
    body.turbo-hide-theme-header .elementor-location-header {
        display: none !important;
    }';
    wp_add_inline_style( $handle, $css );
    // If Elementor is active, ensure its frontend assets are enqueued early so header appears correctly without delay.
    if ( class_exists( '\Elementor\Plugin' ) && did_action( 'elementor/loaded' ) ) {
        $elementor = \Elementor\Plugin::instance();
        if ( isset( $elementor->frontend ) ) {
            // Enqueue Elementor styles and scripts on the normal enqueue stage to avoid late-loading via footer.
            $elementor->frontend->enqueue_styles();
            $elementor->frontend->enqueue_scripts();
        }
    }
}, 20 );

// Print critical inline CSS early in <head> so theme header hides before render (prevents FOUC)
add_action( 'wp_head', function () {
    if ( empty( $GLOBALS['tahefobu_header_will_render'] ) ) {
        return;
    }

    echo "<style>\n" .
        "body.turbo-hide-theme-header .elementor-location-header{display:block!important;}\n" .
        "body.turbo-hide-theme-header header,\n" .
        "body.turbo-hide-theme-header .site-header,\n" .
        "body.turbo-hide-theme-header .main-header,\n" .
        "body.turbo-hide-theme-header #masthead,\n" .
        "body.turbo-hide-theme-header .ast-site-header,\n" .
        "body.turbo-hide-theme-header .storefront-header,\n" .
        "body.turbo-hide-theme-header .generatepress-header,\n" .
        "body.turbo-hide-theme-header .neve-header,\n" .
        "body.turbo-hide-theme-header .hello-elementor-header{display:none!important;}\n" .
    "</style>\n";
}, 1 );

/**
 * Return header markup (used for JS fallback insertion). Does not echo.
 * @param bool $fallback True to generate a fallback container id
 * @return string HTML or empty string
 */
function tahefobu_get_header_markup( $fallback = false ) {
    if ( is_admin() || wp_doing_ajax() ) return '';
    if ( ! function_exists( 'tahefobu_get_matching_header_template_id' ) ) return '';

    $header_template_id = tahefobu_get_matching_header_template_id();
    if ( ! $header_template_id || ! class_exists( '\Elementor\Plugin' ) || get_post_type( $header_template_id ) !== 'tahefobu_header' ) {
        return '';
    }

    $content = \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $header_template_id, true );
    if ( empty( $content ) ) return '';

    $classes = [ 'turbo-header-template' ];
    $is_sticky     = get_post_meta( $header_template_id, '_tahefobu_is_sticky', true );
    $has_animation = get_post_meta( $header_template_id, '_tahefobu_has_animation', true );
    if ( ! empty( $is_sticky ) )     $classes[] = 'ta-sticky-header';
    if ( ! empty( $has_animation ) ) $classes[] = 'ta-header-scroll-animation';

    // Use the canonical id for both fallback and real header; mark fallback with a flag class
    $id = 'tahefobu-header';
    if ( $fallback ) {
        $classes[] = 'tahefobu-fallback';
        $classes[] = 'tahefobu-ready';
    }

    $sticky_attr = ! empty( $is_sticky ) ? '1' : '0';
    $anim_attr   = ! empty( $has_animation ) ? '1' : '0';

    $html  = '<div id="' . esc_attr( $id ) . '" class="' . esc_attr( implode( ' ', $classes ) ) . '" data-sticky="' . esc_attr( $sticky_attr ) . '" data-animation="' . esc_attr( $anim_attr ) . '">';
    $html .= $content;
    $html .= '</div>';

    return $html;
}

// Early JS fallback: insert header HTML as soon as possible if theme doesn't render it early.
add_action( 'wp_head', function () {
    if ( empty( $GLOBALS['tahefobu_header_will_render'] ) ) return;

    $html = tahefobu_get_header_markup( true );
    if ( empty( $html ) ) return;

    // Safely JSON-encode the HTML for JS (inlined below)

    // Insert fallback header as early as possible. It uses the canonical id so the real renderer
    // can replace it later without producing duplicates. Use `wp_add_inline_script()` to avoid
    // direct echo of unescaped content and satisfy security linting.
    $inline = '(function(){var headerHTML=' . wp_json_encode( $html ) . ';function insert(){var b=document.body;if(!b){setTimeout(insert,10);return;}var wrapper=document.createElement("div");wrapper.innerHTML=headerHTML;var node=wrapper.firstElementChild;if(node){b.insertBefore(node,b.firstChild);} }insert();})();';

    if ( ! wp_script_is( 'tahefobu-inline-fallback', 'registered' ) ) {
        wp_register_script( 'tahefobu-inline-fallback', false, [], TAHEFOBU_HEADER_FOOTER_BUILDER_FOR_ELEMENTOR_PLUGIN_VERSION, false );
    }
    if ( ! wp_script_is( 'tahefobu-inline-fallback', 'enqueued' ) ) {
        wp_enqueue_script( 'tahefobu-inline-fallback' );
    }
    wp_add_inline_script( 'tahefobu-inline-fallback', $inline );
}, 2 );

/**
 * ---------------- Columns/UI for "Edit Conditions"
 */
add_filter('manage_tahefobu_header_posts_columns', function ($columns) {
    $columns['tahefobu_display_conditions'] = __('Display Conditions', 'header-footer-builder-for-elementor');
    return $columns;
});
add_action('manage_tahefobu_header_posts_custom_column', function ($column, $post_id) {
    if ($column === 'tahefobu_display_conditions') {
        // Get all condition data
        $include = get_post_meta($post_id, '_tahefobu_include_pages', true) ?: [];
        $exclude = get_post_meta($post_id, '_tahefobu_exclude_pages', true) ?: [];
        $is_sticky = (int) get_post_meta($post_id, '_tahefobu_is_sticky', true);
        $has_animation = (int) get_post_meta($post_id, '_tahefobu_has_animation', true);
        $display_targets = get_post_meta($post_id, '_tahefobu_display_targets', true) ?: [];
        
        // Encode data as JSON for the button
        $data = [
            'include' => $include,
            'exclude' => $exclude, // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- array key for JSON data, not a WP_Query parameter
            'is_sticky' => $is_sticky,
            'has_animation' => $has_animation,
            'display_targets' => $display_targets,
        ];
        
        echo '<button type="button" class="button tahefobu-edit-conditions-button" 
            data-post-id="' . esc_attr($post_id) . '" 
            data-conditions="' . esc_attr(wp_json_encode($data)) . '">'.esc_html__('Edit Conditions','header-footer-builder-for-elementor').'</button>';
    }
}, 10, 2);

/**
 * Edit Conditions Modal (admin)
 */
add_action('admin_footer-edit.php', function () {
    $screen = get_current_screen();
    if ($screen->post_type !== 'tahefobu_header') return;
    $pages = get_pages();
    ?>
    <div id="tahefobu-conditions-modal" class="tahefobu-header-popup-overlay" style="display:none;">
        <div class="tahefobu-header-popup-modal">
            <div class="modal-header-style">
                <h2 class="tahefobu-create-header-popup-headline"><?php esc_html_e('Edit Header Conditions', 'header-footer-builder-for-elementor'); ?></h2>
            </div>

            <div class="modal-display-conditions-field-style">
                <input type="hidden" id="tahefobu_conditions_post_id" value="">
                <label><?php esc_html_e('Include Pages:','header-footer-builder-for-elementor'); ?><span><?php esc_html_e(' Optional – use this to show the header on specific pages.', 'header-footer-builder-for-elementor'); ?></span></label>
                <select id="tahefobu_edit_include_pages" multiple style="width:100%; min-height:100px;">
                    <?php foreach ($pages as $page): ?>
                        <option value="<?php echo esc_attr($page->ID); ?>"><?php echo esc_html($page->post_title); ?></option>
                    <?php endforeach; ?>
                </select>

                <div class="modal-include-exclude-style">
                    <label><?php esc_html_e('Set Display Condition:', 'header-footer-builder-for-elementor'); ?></label>
                </div>
                <select id="tahefobu_edit_display_targets" multiple>
                    <option value="entire_site"><?php esc_html_e('Entire Site', 'header-footer-builder-for-elementor'); ?></option>
                    <option value="all_posts"><?php esc_html_e('All Blog Posts', 'header-footer-builder-for-elementor'); ?></option>
                    <option value="all_archives"><?php esc_html_e('All Archive Pages', 'header-footer-builder-for-elementor'); ?></option>
                    <?php if (class_exists('WooCommerce')) : ?>
                        <option value="all_products"><?php esc_html_e('All WooCommerce Products', 'header-footer-builder-for-elementor'); ?></option>
                        <option value="all_woo"><?php esc_html_e('All WooCommerce Pages', 'header-footer-builder-for-elementor'); ?></option>
                    <?php endif; ?>
                </select>
            </div>

            <div class="modal-display-conditions-field-style">
                <label><?php esc_html_e('Exclude Pages:','header-footer-builder-for-elementor'); ?><span><?php esc_html_e(' Optional – use this to hide the header on specific pages.', 'header-footer-builder-for-elementor'); ?></span></label>
                <select id="tahefobu_edit_exclude_pages" multiple style="width:100%; min-height:100px;">
                    <?php foreach ($pages as $page): ?>
                        <option value="<?php echo esc_attr($page->ID); ?>"><?php echo esc_html($page->post_title); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="tahefobu-header-popup-header-style">
                <h3><?php esc_html_e('Header Style', 'header-footer-builder-for-elementor'); ?></h3>
                <div class="tahefobu-style-options">
                    <label><input type="checkbox" id="tahefobu_edit_is_sticky" /> <?php esc_html_e('Make Header Sticky', 'header-footer-builder-for-elementor'); ?></label>
                    <label><input type="checkbox" id="tahefobu_edit_has_animation" /> <?php esc_html_e('Enable Scroll Animation', 'header-footer-builder-for-elementor'); ?></label>
                </div>
            </div>

            <div class="tahefobu-header-popup-actions">
                <div>
                    <button class="button tahefobu-header-creat-edit-button" id="tahefobu-save-condition-edit"><?php esc_html_e('Update', 'header-footer-builder-for-elementor'); ?></button>
                </div>
                <button class="button tahefobu-header-cancel-button" id="tahefobu-cancel-condition-edit"><?php esc_html_e('Cancel', 'header-footer-builder-for-elementor'); ?></button>
            </div>
        </div>
    </div>
    <?php
});

/**
 * AJAX: Get Header Conditions
 */
add_action( 'wp_ajax_tahefobu_get_header_conditions', function () {
    check_ajax_referer( 'tahefobu_save_conditions_nonce', '_ajax_nonce' );

    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( [ 'message' => __( 'Permission denied', 'header-footer-builder-for-elementor' ) ] );
    }

    $post_id = isset( $_POST['post_id'] ) ? intval( wp_unslash( $_POST['post_id'] ) ) : 0;

    // Ownership check: post must exist, be our CPT, and user must be able to edit it.
    if ( ! $post_id
        || get_post_type( $post_id ) !== 'tahefobu_header'
        || ! current_user_can( 'edit_post', $post_id )
    ) {
        wp_send_json_error( [ 'message' => __( 'Invalid request', 'header-footer-builder-for-elementor' ) ] );
    }

    $include         = get_post_meta( $post_id, '_tahefobu_include_pages',   true ) ?: [];
    $exclude         = get_post_meta( $post_id, '_tahefobu_exclude_pages',   true ) ?: [];
    $is_sticky       = (int) get_post_meta( $post_id, '_tahefobu_is_sticky',     true );
    $has_animation   = (int) get_post_meta( $post_id, '_tahefobu_has_animation', true );
    $display_targets = get_post_meta( $post_id, '_tahefobu_display_targets', true ) ?: [];

    wp_send_json_success( [
        'include'         => array_map( 'strval', (array) $include ),
        // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- array key for JSON data, not a WP_Query parameter
        'exclude'         => array_map( 'strval', (array) $exclude ),
        'is_sticky'       => $is_sticky,
        'has_animation'   => $has_animation,
        'display_targets' => array_map( 'strval', (array) $display_targets ),
    ] );
} );

add_action('wp_ajax_tahefobu_save_header_conditions', function () {
    check_ajax_referer('tahefobu_save_conditions_nonce', '_ajax_nonce');

    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( [ 'message' => __( 'Permission denied', 'header-footer-builder-for-elementor' ) ] );
    }

    $post_id = isset( $_POST['post_id'] ) ? intval( wp_unslash( $_POST['post_id'] ) ) : 0;

    // Ownership check: ensure the post exists, belongs to our CPT, and the user can edit it.
    if ( ! $post_id
        || get_post_type( $post_id ) !== 'tahefobu_header'
        || ! current_user_can( 'edit_post', $post_id )
    ) {
        wp_send_json_error( [ 'message' => __( 'Invalid request', 'header-footer-builder-for-elementor' ) ] );
    }

    $include_pages = isset( $_POST['include_pages'] ) ? array_map( 'intval', (array) wp_unslash( $_POST['include_pages'] ) ) : [];
    $exclude_pages = isset( $_POST['exclude_pages'] ) ? array_map( 'intval', (array) wp_unslash( $_POST['exclude_pages'] ) ) : [];

    $is_sticky     = isset( $_POST['is_sticky'] )     ? intval( $_POST['is_sticky'] )     : 0;
    $has_animation = isset( $_POST['has_animation'] ) ? intval( $_POST['has_animation'] ) : 0;

    $display_targets = [];
    if ( isset( $_POST['display_targets'] ) && is_array( $_POST['display_targets'] ) ) {
        $display_targets = array_map( 'sanitize_text_field', (array) wp_unslash( $_POST['display_targets'] ) );
    }

    update_post_meta( $post_id, '_tahefobu_include_pages',   $include_pages );
    update_post_meta( $post_id, '_tahefobu_exclude_pages',   $exclude_pages );
    update_post_meta( $post_id, '_tahefobu_is_sticky',       $is_sticky );
    update_post_meta( $post_id, '_tahefobu_has_animation',   $has_animation );
    update_post_meta( $post_id, '_tahefobu_display_targets', $display_targets );

    // Bust the frontend matching cache so changes take effect immediately.
    delete_transient( 'tahefobu_header_templates_meta' );

    wp_send_json_success();
});
