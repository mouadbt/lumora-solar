<?php
/**
 * Admin Menu — Single-page Dashboard
 * Everything (create / edit / delete / conditions) lives here via AJAX.
 *
 * @package Header_Footer_Builder_For_Elementor
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* ─────────────────────────────────────────────────────────────
   1. Register single menu item — no submenus
───────────────────────────────────────────────────────────── */
add_action( 'admin_menu', function () {
    add_menu_page(
        esc_html__( 'Turbo H&F Builder', 'header-footer-builder-for-elementor' ),
        esc_html__( 'Turbo H&F Builder', 'header-footer-builder-for-elementor' ),
        'manage_options',
        'tahefobu_templates',
        'tahefobu_render_dashboard',
        TAHEFOBU_HEADER_FOOTER_BUILDER_FOR_ELEMENTOR_PLUGIN_URL . 'assets/images/turboFile.svg',
        21
    );
} );

/* ─────────────────────────────────────────────────────────────
   2. Enqueue assets only on our page
───────────────────────────────────────────────────────────── */
add_action( 'admin_enqueue_scripts', function ( $hook ) {
    if ( $hook !== 'toplevel_page_tahefobu_templates' ) {
        return;
    }
    $url = TAHEFOBU_HEADER_FOOTER_BUILDER_FOR_ELEMENTOR_PLUGIN_URL;
    $ver = TAHEFOBU_HEADER_FOOTER_BUILDER_FOR_ELEMENTOR_PLUGIN_VERSION;

    wp_enqueue_style( 'dashicons' );
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_style(
        'thfb-dashboard',
        $url . 'assets/css/turbo-admin-dashboard.css',
        [],
        $ver
    );
    // Select2 for condition selects
    wp_enqueue_style(
        'select2',
        $url . 'assets/vendor/select2/select2.min.css',
        [],
        '4.1.0'
    );
    wp_enqueue_script(
        'select2',
        $url . 'assets/vendor/select2/select2.min.js',
        [ 'jquery' ],
        '4.1.0',
        true
    );
    wp_enqueue_script(
        'thfb-dashboard',
        $url . 'assets/js/turbo-dashboard.js',
        [ 'jquery', 'select2' ],
        $ver,
        true
    );
    wp_localize_script( 'thfb-dashboard', 'thfbDash', [
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'tahefobu_dashboard_nonce' ),
        'pages'   => tahefobu_get_all_pages_for_js(),
        'strings' => [
            'confirm_delete' => __( 'Delete this template? This cannot be undone.', 'header-footer-builder-for-elementor' ),
            'saving'         => __( 'Saving…', 'header-footer-builder-for-elementor' ),
            'saved'          => __( 'Saved!', 'header-footer-builder-for-elementor' ),
            'creating'       => __( 'Creating…', 'header-footer-builder-for-elementor' ),
            'deleting'       => __( 'Deleting…', 'header-footer-builder-for-elementor' ),
            'error'          => __( 'Something went wrong. Please try again.', 'header-footer-builder-for-elementor' ),
            'select_all'     => __( 'Select All', 'header-footer-builder-for-elementor' ),
            'deselect_all'   => __( 'Deselect All', 'header-footer-builder-for-elementor' ),
        ],
    ] );
} );

/* ─────────────────────────────────────────────────────────────
   3. Helper: pages list for JS
───────────────────────────────────────────────────────────── */
function tahefobu_get_all_pages_for_js() {
    $pages = get_pages( [ 'post_status' => 'publish', 'sort_column' => 'post_title' ] );
    $out   = [];
    foreach ( $pages as $p ) {
        $out[] = [ 'id' => $p->ID, 'title' => $p->post_title ];
    }
    return $out;
}

/* ─────────────────────────────────────────────────────────────
   4. AJAX — Create template (header or footer)
───────────────────────────────────────────────────────────── */
add_action( 'wp_ajax_tahefobu_dashboard_create', function () {
    check_ajax_referer( 'tahefobu_dashboard_nonce', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( [ 'message' => __( 'Permission denied.', 'header-footer-builder-for-elementor' ) ] );
    }

    $type  = isset( $_POST['type'] ) && $_POST['type'] === 'footer' ? 'tahefobu_footer' : 'tahefobu_header';
    $title = isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : '';
    if ( empty( $title ) ) {
        wp_send_json_error( [ 'message' => __( 'Template name is required.', 'header-footer-builder-for-elementor' ) ] );
    }

    $post_id = wp_insert_post( [
        'post_type'   => $type,
        'post_title'  => $title,
        'post_status' => 'publish',
    ] );

    if ( is_wp_error( $post_id ) ) {
        wp_send_json_error( [ 'message' => __( 'Could not create template.', 'header-footer-builder-for-elementor' ) ] );
    }

    update_post_meta( $post_id, '_tahefobu_is_enabled', '1' );

    $include  = isset( $_POST['include_pages'] )   ? array_map( 'intval', (array) wp_unslash( $_POST['include_pages'] ) )   : [];
    $exclude  = isset( $_POST['exclude_pages'] )   ? array_map( 'intval', (array) wp_unslash( $_POST['exclude_pages'] ) )   : [];
    $targets  = isset( $_POST['display_targets'] ) && is_array( $_POST['display_targets'] )
        ? array_map( 'sanitize_text_field', (array) wp_unslash( $_POST['display_targets'] ) ) : [];
    $sticky   = ( $type === 'tahefobu_header' ) ? ( ! empty( $_POST['is_sticky'] ) ? 1 : 0 ) : 0;
    $anim     = ( $type === 'tahefobu_header' ) ? ( ! empty( $_POST['has_animation'] ) ? 1 : 0 ) : 0;

    update_post_meta( $post_id, '_tahefobu_include_pages',   $include );
    update_post_meta( $post_id, '_tahefobu_exclude_pages',   $exclude );
    update_post_meta( $post_id, '_tahefobu_display_targets', $targets );
    update_post_meta( $post_id, '_tahefobu_is_sticky',       $sticky );
    update_post_meta( $post_id, '_tahefobu_has_animation',   $anim );

    delete_transient( 'tahefobu_header_templates_meta' );
    delete_transient( 'tahefobu_footer_templates_meta' );

    wp_send_json_success( [
        'post_id'  => $post_id,
        'edit_url' => admin_url( 'post.php?post=' . $post_id . '&action=elementor' ),
        'template' => tahefobu_get_template_row_data( $post_id ),
    ] );
} );

/* ─────────────────────────────────────────────────────────────
   5. AJAX — Save conditions
───────────────────────────────────────────────────────────── */
add_action( 'wp_ajax_tahefobu_dashboard_save_conditions', function () {
    check_ajax_referer( 'tahefobu_dashboard_nonce', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( [ 'message' => __( 'Permission denied.', 'header-footer-builder-for-elementor' ) ] );
    }

    $post_id = isset( $_POST['post_id'] ) ? absint( wp_unslash( $_POST['post_id'] ) ) : 0;
    $pt      = get_post_type( $post_id );
    if ( ! $post_id || ! in_array( $pt, [ 'tahefobu_header', 'tahefobu_footer' ], true )
        || ! current_user_can( 'edit_post', $post_id ) ) {
        wp_send_json_error( [ 'message' => __( 'Invalid request.', 'header-footer-builder-for-elementor' ) ] );
    }

    $include = isset( $_POST['include_pages'] )   ? array_map( 'intval', (array) wp_unslash( $_POST['include_pages'] ) )   : [];
    $exclude = isset( $_POST['exclude_pages'] )   ? array_map( 'intval', (array) wp_unslash( $_POST['exclude_pages'] ) )   : [];
    $targets = isset( $_POST['display_targets'] ) && is_array( $_POST['display_targets'] )
        ? array_map( 'sanitize_text_field', (array) wp_unslash( $_POST['display_targets'] ) ) : [];
    $sticky  = ( $pt === 'tahefobu_header' ) ? absint( $_POST['is_sticky'] ?? 0 )     : 0;
    $anim    = ( $pt === 'tahefobu_header' ) ? absint( $_POST['has_animation'] ?? 0 ) : 0;

    update_post_meta( $post_id, '_tahefobu_include_pages',   $include );
    update_post_meta( $post_id, '_tahefobu_exclude_pages',   $exclude );
    update_post_meta( $post_id, '_tahefobu_display_targets', $targets );
    update_post_meta( $post_id, '_tahefobu_is_sticky',       $sticky );
    update_post_meta( $post_id, '_tahefobu_has_animation',   $anim );

    delete_transient( 'tahefobu_header_templates_meta' );
    delete_transient( 'tahefobu_footer_templates_meta' );

    wp_send_json_success( [ 'template' => tahefobu_get_template_row_data( $post_id ) ] );
} );

/* ─────────────────────────────────────────────────────────────
   6. AJAX — Delete template
───────────────────────────────────────────────────────────── */
add_action( 'wp_ajax_tahefobu_dashboard_delete', function () {
    check_ajax_referer( 'tahefobu_dashboard_nonce', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( [ 'message' => __( 'Permission denied.', 'header-footer-builder-for-elementor' ) ] );
    }

    $post_id = isset( $_POST['post_id'] ) ? absint( wp_unslash( $_POST['post_id'] ) ) : 0;
    $pt      = get_post_type( $post_id );
    if ( ! $post_id || ! in_array( $pt, [ 'tahefobu_header', 'tahefobu_footer' ], true )
        || ! current_user_can( 'delete_post', $post_id ) ) {
        wp_send_json_error( [ 'message' => __( 'Invalid request.', 'header-footer-builder-for-elementor' ) ] );
    }

    wp_delete_post( $post_id, true );
    delete_transient( 'tahefobu_header_templates_meta' );
    delete_transient( 'tahefobu_footer_templates_meta' );

    wp_send_json_success();
} );

/* ─────────────────────────────────────────────────────────────
   7. AJAX — Toggle active/draft
───────────────────────────────────────────────────────────── */
add_action( 'wp_ajax_tahefobu_dashboard_toggle_status', function () {
    check_ajax_referer( 'tahefobu_dashboard_nonce', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( [ 'message' => __( 'Permission denied.', 'header-footer-builder-for-elementor' ) ] );
    }

    $post_id = isset( $_POST['post_id'] ) ? absint( wp_unslash( $_POST['post_id'] ) ) : 0;
    $pt      = get_post_type( $post_id );
    if ( ! $post_id || ! in_array( $pt, [ 'tahefobu_header', 'tahefobu_footer' ], true )
        || ! current_user_can( 'edit_post', $post_id ) ) {
        wp_send_json_error( [ 'message' => __( 'Invalid request.', 'header-footer-builder-for-elementor' ) ] );
    }

    $current    = get_post_status( $post_id );
    $new_status = ( $current === 'publish' ) ? 'draft' : 'publish';
    wp_update_post( [ 'ID' => $post_id, 'post_status' => $new_status ] );
    delete_transient( 'tahefobu_header_templates_meta' );
    delete_transient( 'tahefobu_footer_templates_meta' );

    wp_send_json_success( [ 'new_status' => $new_status ] );
} );



/* ─────────────────────────────────────────────────────────────
   9. Helper — build template row data array for JS
───────────────────────────────────────────────────────────── */
function tahefobu_get_template_row_data( $post_id ) {
    $post    = get_post( $post_id );
    $pt      = get_post_type( $post_id );
    $targets = (array) ( get_post_meta( $post_id, '_tahefobu_display_targets', true ) ?: [] );
    $include = (array) ( get_post_meta( $post_id, '_tahefobu_include_pages',   true ) ?: [] );
    $exclude = (array) ( get_post_meta( $post_id, '_tahefobu_exclude_pages',   true ) ?: [] );
    $sticky  = (bool)    get_post_meta( $post_id, '_tahefobu_is_sticky',       true );
    $anim    = (bool)    get_post_meta( $post_id, '_tahefobu_has_animation',   true );

    return [
        'id'           => $post_id,
        'title'        => $post->post_title,
        'status'       => get_post_status( $post_id ),
        'type'         => $pt,
        'edit_url'     => admin_url( 'post.php?post=' . $post_id . '&action=elementor' ),
        'modified'     => get_the_modified_date( 'M j, Y', $post_id ),
        'targets'      => array_map( 'sanitize_key', $targets ),
        'include'      => array_map( 'strval', $include ),
        // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- array key for JSON data, not a WP_Query parameter
        'exclude'      => array_map( 'strval', $exclude ),
        'is_sticky'    => $sticky,
        'has_animation'=> $anim,
    ];
}

/* ─────────────────────────────────────────────────────────────
   9. Helper — render all templates as JSON for initial page load
───────────────────────────────────────────────────────────── */
function tahefobu_get_all_templates_json() {
    $types = [ 'tahefobu_header', 'tahefobu_footer' ];
    $out   = [];
    foreach ( $types as $pt ) {
        $posts = get_posts( [
            'post_type'      => $pt,
            'post_status'    => [ 'publish', 'draft' ],
            'posts_per_page' => -1,
            'orderby'        => 'modified',
            'order'          => 'DESC',
            'no_found_rows'  => true,
        ] );
        foreach ( $posts as $p ) {
            $out[] = tahefobu_get_template_row_data( $p->ID );
        }
    }
    return $out;
}

/* ─────────────────────────────────────────────────────────────
   10. Dashboard HTML
───────────────────────────────────────────────────────────── */
function tahefobu_render_dashboard() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'You do not have permission to view this page.', 'header-footer-builder-for-elementor' ) );
    }

    $ver       = defined( 'TAHEFOBU_HEADER_FOOTER_BUILDER_FOR_ELEMENTOR_PLUGIN_VERSION' )
                    ? TAHEFOBU_HEADER_FOOTER_BUILDER_FOR_ELEMENTOR_PLUGIN_VERSION : '';
    $templates = tahefobu_get_all_templates_json();
    $headers   = array_filter( $templates, fn($t) => $t['type'] === 'tahefobu_header' );
    $footers   = array_filter( $templates, fn($t) => $t['type'] === 'tahefobu_footer' );

    $tag_labels = [
        'entire_site'  => __( 'Entire Site',   'header-footer-builder-for-elementor' ),
        'all_posts'    => __( 'All Posts',     'header-footer-builder-for-elementor' ),
        'all_archives' => __( 'Archives',      'header-footer-builder-for-elementor' ),
        'all_products' => __( 'Products',      'header-footer-builder-for-elementor' ),
        'all_woo'      => __( 'WooCommerce',   'header-footer-builder-for-elementor' ),
        'all_pages'    => __( 'All Pages',     'header-footer-builder-for-elementor' ),
    ];
    ?>
    <div id="thfb-dashboard" class="wrap">
        <h1 class="wp-heading-inline"><?php esc_html_e( 'Header Footer Builder', 'header-footer-builder-for-elementor' ); ?></h1>
        <hr class="wp-header-end">

        <div class="thfb-main-grid">
            <div class="thfb-left-col">
                <!-- Header Templates Panel -->
                <div class="thfb-panel">
                    <div class="thfb-panel-header">
                        <h2 class="thfb-panel-title"><span class="dashicons dashicons-layout"></span><?php esc_html_e( 'Headers', 'header-footer-builder-for-elementor' ); ?></h2>
                        <button class="button button-primary" id="thfb-new-header-btn2">
                            <span class="dashicons dashicons-plus-alt2"></span><?php esc_html_e( 'Create New Header', 'header-footer-builder-for-elementor' ); ?>
                        </button>
                    </div>
                    <div class="thfb-panel-body thfb-table-wrap" id="thfb-header-table-wrap">
                        <?php tahefobu_render_template_rows( $headers, $tag_labels ); ?>
                    </div>
                </div>

                <!-- Footer Templates Panel -->
                <div class="thfb-panel">
                    <div class="thfb-panel-header">
                        <h2 class="thfb-panel-title"><span class="dashicons dashicons-align-center"></span><?php esc_html_e( 'Footers', 'header-footer-builder-for-elementor' ); ?></h2>
                        <button class="button button-primary" id="thfb-new-footer-btn2">
                            <span class="dashicons dashicons-plus-alt2"></span><?php esc_html_e( 'Create New Footer', 'header-footer-builder-for-elementor' ); ?>
                        </button>
                    </div>
                    <div class="thfb-panel-body thfb-table-wrap" id="thfb-footer-table-wrap">
                        <?php tahefobu_render_template_rows( $footers, $tag_labels ); ?>
                    </div>
                </div>
            </div><!-- /.thfb-left-col -->

            <div class="thfb-right-col">
                <!-- How it works -->
                <div class="thfb-panel">
                    <div class="thfb-panel-header">
                        <h2 class="thfb-panel-title"><span class="dashicons dashicons-info"></span><?php esc_html_e( 'How It Works', 'header-footer-builder-for-elementor' ); ?></h2>
                    </div>
                    <div class="thfb-panel-body">
                        <ul class="thfb-tip-list">
                            <li><strong>1. Create Template:</strong> Click on "Create New Header" or "Create New Footer".</li>
                            <li><strong>2. Set Conditions:</strong> Set the display conditions to determine which pages render the template.</li>
                            <li><strong>3. Design with Elementor:</strong> Click "Edit with Elementor" on the template row.</li>
                            <li><strong>4. Toggle Active:</strong> Use the Active/Draft badge to toggle templates on the fly.</li>
                        </ul>
                    </div>
                </div>
            </div><!-- /.thfb-right-col -->
        </div><!-- /.thfb-main-grid -->

        <?php tahefobu_render_create_modal(); ?>
        <?php tahefobu_render_conditions_modal(); ?>

        <!-- Pass template data to JS -->
        <script>
        window.thfbTemplates = <?php echo wp_json_encode( array_values( $templates ) ); ?>;
        window.thfbTagLabels = <?php echo wp_json_encode( $tag_labels ); ?>;
        </script>

    </div><!-- /#thfb-dashboard -->
    <?php
}

/* ─────────────────────────────────────────────────────────────
   11. Helper — render template rows HTML
───────────────────────────────────────────────────────────── */
function tahefobu_render_template_rows( $templates, $tag_labels ) {
    if ( empty( $templates ) ) {
        echo '<div class="thfb-empty-state">';
        echo '<div class="thfb-empty-icon"><span class="dashicons dashicons-layout"></span></div>';
        echo '<h3>' . esc_html__( 'No templates yet', 'header-footer-builder-for-elementor' ) . '</h3>';
        echo '<p>' . esc_html__( 'Click "Create New Heder/Footer" above to create your first template.', 'header-footer-builder-for-elementor' ) . '</p>';
        echo '</div>';
        return;
    }
    echo '<table class="thfb-template-table"><thead><tr>';
    echo '<th>' . esc_html__( 'Name', 'header-footer-builder-for-elementor' ) . '</th>';
    echo '<th>' . esc_html__( 'Status', 'header-footer-builder-for-elementor' ) . '</th>';
    echo '<th>' . esc_html__( 'Display Conditions', 'header-footer-builder-for-elementor' ) . '</th>';
    echo '<th>' . esc_html__( 'Actions', 'header-footer-builder-for-elementor' ) . '</th>';
    echo '</tr></thead><tbody>';

    foreach ( $templates as $t ) {
        $is_active = ( $t['status'] === 'publish' );
        $badge_cls = $is_active ? 'thfb-badge-active' : 'thfb-badge-draft';
        $badge_lbl = $is_active
            ? esc_html__( 'Active', 'header-footer-builder-for-elementor' )
            : esc_html__( 'Draft',  'header-footer-builder-for-elementor' );
        $is_header = ( $t['type'] === 'tahefobu_header' );
        // $icon      = $is_header ? 'dashicons-layout' : 'dashicons-align-center';

        echo '<tr data-id="' . esc_attr( $t['id'] ) . '" data-type="' . esc_attr( $t['type'] ) . '">';

        // Name
        echo '<td>';
        echo '<div class="thfb-template-name">';
        // echo '<span class="dashicons ' . esc_attr( $icon ) . '"></span>';
        echo '<div>';
        echo '<strong>' . esc_html( $t['title'] ) . '</strong>';
        if ( $is_header && ! empty( $t['is_sticky'] ) ) {
            echo ' <span class="thfb-badge" style="background:#f3f0ff;color:#7c3aed;font-size:10px;padding:2px 7px;">'
                . esc_html__( 'Sticky', 'header-footer-builder-for-elementor' ) . '</span>';
        }
        if ( $is_header && ! empty( $t['has_animation'] ) ) {
            echo ' <span class="thfb-badge" style="background:#fff8ee;color:#e67e22;font-size:10px;padding:2px 7px;">'
                . esc_html__( 'Animated', 'header-footer-builder-for-elementor' ) . '</span>';
        }
        echo '<div class="thfb-template-meta">' . esc_html__( 'Modified:', 'header-footer-builder-for-elementor' ) . ' ' . esc_html( $t['modified'] ) . '</div>';
        echo '</div></div></td>';

        // Status toggle
        echo '<td>';
        echo '<button class="thfb-badge ' . esc_attr( $badge_cls ) . ' thfb-toggle-status" '
            . 'data-id="' . esc_attr( $t['id'] ) . '" '
            . 'title="' . esc_attr__( 'Click to toggle', 'header-footer-builder-for-elementor' ) . '" '
            . 'style="cursor:pointer;border:none;background:none;padding:0;">'
            . esc_html( $badge_lbl ) . '</button>';
        echo '</td>';

        // Conditions
        echo '<td class="thfb-conditions-cell">';
        if ( ! empty( $t['targets'] ) ) {
            foreach ( $t['targets'] as $tgt ) {
                $lbl = isset( $tag_labels[ $tgt ] ) ? $tag_labels[ $tgt ] : $tgt;
                echo '<span class="thfb-condition-tag">' . esc_html( $lbl ) . '</span>';
            }
        } else {
            echo '<span class="thfb-condition-none">' . esc_html__( 'Not set', 'header-footer-builder-for-elementor' ) . '</span>';
        }
        echo '</td>';

        // Actions
        echo '<td><div class="thfb-row-actions">';
        echo '<a href="' . esc_url( $t['edit_url'] ) . '" class="thfb-action-btn thfb-action-edit">'
            . '<span class="dashicons dashicons-edit"></span>' . esc_html__( 'Edit With Elementor', 'header-footer-builder-for-elementor' ) . '</a>';
        echo '<button class="thfb-action-btn thfb-action-conditions thfb-open-conditions" data-id="' . esc_attr( $t['id'] ) . '">'
            . '<span class="dashicons dashicons-admin-settings"></span>' . esc_html__( 'Edit Conditions', 'header-footer-builder-for-elementor' ) . '</button>';
        echo '<button class="thfb-action-btn thfb-action-delete thfb-delete-tpl" data-id="' . esc_attr( $t['id'] ) . '">'
            . '<span class="dashicons dashicons-trash"></span>' . esc_html__( 'Delete', 'header-footer-builder-for-elementor' ) . '</button>';
        echo '</div></td>';

        echo '</tr>';
    }
    echo '</tbody></table>';
}

/* ─────────────────────────────────────────────────────────────
   12. Create Modal HTML
───────────────────────────────────────────────────────────── */
function tahefobu_render_create_modal() {
    $woo = class_exists( 'WooCommerce' );
    ?>
    <div id="thfb-create-modal" class="thfb-modal-overlay" style="display:none;">
        <div class="thfb-modal">
            <div class="thfb-modal-header">
                <h2 id="thfb-create-modal-title"><?php esc_html_e( 'Create New Template', 'header-footer-builder-for-elementor' ); ?></h2>
                <button class="thfb-modal-close" id="thfb-create-close">&times;</button>
            </div>
            <div class="thfb-modal-body">
                <input type="hidden" id="thfb-create-type" value="header">

                <label class="thfb-field-label" id="thfb-create-name-label"><?php esc_html_e( 'Header Name', 'header-footer-builder-for-elementor' ); ?> <span style="color:red">*</span></label>
                <input type="text" id="thfb-create-title" class="thfb-input" placeholder="<?php esc_attr_e( 'e.g. Main Header', 'header-footer-builder-for-elementor' ); ?>">
                <label class="thfb-field-label" style="margin-top:16px;"><?php esc_html_e( 'Display Conditions', 'header-footer-builder-for-elementor' ); ?></label>
                <select id="thfb-create-targets" multiple class="thfb-select">
                    <option value="entire_site"><?php esc_html_e( 'Entire Site', 'header-footer-builder-for-elementor' ); ?></option>
                    <option value="all_posts"><?php esc_html_e( 'All Blog Posts', 'header-footer-builder-for-elementor' ); ?></option>
                    <option value="all_archives"><?php esc_html_e( 'All Archive Pages', 'header-footer-builder-for-elementor' ); ?></option>
                    <option value="all_pages"><?php esc_html_e( 'All Pages', 'header-footer-builder-for-elementor' ); ?></option>
                    <?php if ( $woo ) : ?>
                    <option value="all_products"><?php esc_html_e( 'All WooCommerce Products', 'header-footer-builder-for-elementor' ); ?></option>
                    <option value="all_woo"><?php esc_html_e( 'All WooCommerce Pages', 'header-footer-builder-for-elementor' ); ?></option>
                    <?php endif; ?>
                </select>

                <div class="thfb-field-label-row" style="margin-top:16px;">
                    <label class="thfb-field-label"><?php esc_html_e( 'Include Specific Pages', 'header-footer-builder-for-elementor' ); ?> <span class="thfb-optional"><?php esc_html_e( '(optional)', 'header-footer-builder-for-elementor' ); ?></span></label>
                    <button type="button" class="thfb-select-all-btn" data-target="thfb-create-include" data-deselect="0"><?php esc_html_e( 'Select All', 'header-footer-builder-for-elementor' ); ?></button>
                </div>
                <select id="thfb-create-include" multiple class="thfb-select thfb-pages-select"></select>

                <div class="thfb-field-label-row" style="margin-top:16px;">
                    <label class="thfb-field-label"><?php esc_html_e( 'Exclude Specific Pages', 'header-footer-builder-for-elementor' ); ?> <span class="thfb-optional"><?php esc_html_e( '(optional)', 'header-footer-builder-for-elementor' ); ?></span></label>
                    <button type="button" class="thfb-select-all-btn" data-target="thfb-create-exclude" data-deselect="0"><?php esc_html_e( 'Select All', 'header-footer-builder-for-elementor' ); ?></button>
                </div>
                <select id="thfb-create-exclude" multiple class="thfb-select thfb-pages-select"></select>

                <div id="thfb-create-header-opts" class="thfb-header-opts">
                    <div class="thfb-divider"></div>
                    <label class="thfb-field-label"><?php esc_html_e( 'Header Style', 'header-footer-builder-for-elementor' ); ?></label>
                    <div class="thfb-toggle-row">
                        <label class="thfb-toggle-label">
                            <input type="checkbox" id="thfb-create-sticky">
                            <span class="thfb-toggle-switch"></span>
                            <?php esc_html_e( 'Sticky Header', 'header-footer-builder-for-elementor' ); ?>
                        </label>
                        <label class="thfb-toggle-label">
                            <input type="checkbox" id="thfb-create-animation">
                            <span class="thfb-toggle-switch"></span>
                            <?php esc_html_e( 'Scroll Animation', 'header-footer-builder-for-elementor' ); ?>
                        </label>
                    </div>
                </div>
            </div>
            <div class="thfb-modal-footer">
                <button class="thfb-btn thfb-btn-primary" id="thfb-create-submit">
                    <span class="dashicons dashicons-plus-alt2"></span>
                    <?php esc_html_e( 'Create & Edit with Elementor', 'header-footer-builder-for-elementor' ); ?>
                </button>
                <button class="thfb-btn thfb-btn-ghost" id="thfb-create-cancel"><?php esc_html_e( 'Cancel', 'header-footer-builder-for-elementor' ); ?></button>
            </div>
        </div>
    </div>
    <?php
}

/* ─────────────────────────────────────────────────────────────
   13. Conditions Modal HTML
───────────────────────────────────────────────────────────── */
function tahefobu_render_conditions_modal() {
    $woo = class_exists( 'WooCommerce' );
    ?>
    <div id="thfb-conditions-modal" class="thfb-modal-overlay" style="display:none;">
        <div class="thfb-modal">
            <div class="thfb-modal-header">
                <h2><?php esc_html_e( 'Edit Conditions', 'header-footer-builder-for-elementor' ); ?></h2>
                <button class="thfb-modal-close" id="thfb-cond-close">&times;</button>
            </div>
            <div class="thfb-modal-body">
                <input type="hidden" id="thfb-cond-post-id">
                <input type="hidden" id="thfb-cond-post-type">

                <label class="thfb-field-label"><?php esc_html_e( 'Display Conditions', 'header-footer-builder-for-elementor' ); ?></label>
                <select id="thfb-cond-targets" multiple class="thfb-select">
                    <option value="entire_site"><?php esc_html_e( 'Entire Site', 'header-footer-builder-for-elementor' ); ?></option>
                    <option value="all_posts"><?php esc_html_e( 'All Blog Posts', 'header-footer-builder-for-elementor' ); ?></option>
                    <option value="all_archives"><?php esc_html_e( 'All Archive Pages', 'header-footer-builder-for-elementor' ); ?></option>
                    <option value="all_pages"><?php esc_html_e( 'All Pages', 'header-footer-builder-for-elementor' ); ?></option>
                    <?php if ( $woo ) : ?>
                    <option value="all_products"><?php esc_html_e( 'All WooCommerce Products', 'header-footer-builder-for-elementor' ); ?></option>
                    <option value="all_woo"><?php esc_html_e( 'All WooCommerce Pages', 'header-footer-builder-for-elementor' ); ?></option>
                    <?php endif; ?>
                </select>

                <div class="thfb-field-label-row" style="margin-top:16px;">
                    <label class="thfb-field-label"><?php esc_html_e( 'Include Specific Pages', 'header-footer-builder-for-elementor' ); ?> <span class="thfb-optional"><?php esc_html_e( '(optional)', 'header-footer-builder-for-elementor' ); ?></span></label>
                    <button type="button" class="thfb-select-all-btn" data-target="thfb-cond-include" data-deselect="0"><?php esc_html_e( 'Select All', 'header-footer-builder-for-elementor' ); ?></button>
                </div>
                <select id="thfb-cond-include" multiple class="thfb-select thfb-pages-select"></select>

                <div class="thfb-field-label-row" style="margin-top:16px;">
                    <label class="thfb-field-label"><?php esc_html_e( 'Exclude Specific Pages', 'header-footer-builder-for-elementor' ); ?> <span class="thfb-optional"><?php esc_html_e( '(optional)', 'header-footer-builder-for-elementor' ); ?></span></label>
                    <button type="button" class="thfb-select-all-btn" data-target="thfb-cond-exclude" data-deselect="0"><?php esc_html_e( 'Select All', 'header-footer-builder-for-elementor' ); ?></button>
                </div>
                <select id="thfb-cond-exclude" multiple class="thfb-select thfb-pages-select"></select>

                <div id="thfb-cond-header-opts" class="thfb-header-opts">
                    <div class="thfb-divider"></div>
                    <label class="thfb-field-label"><?php esc_html_e( 'Header Style', 'header-footer-builder-for-elementor' ); ?></label>
                    <div class="thfb-toggle-row">
                        <label class="thfb-toggle-label">
                            <input type="checkbox" id="thfb-cond-sticky">
                            <span class="thfb-toggle-switch"></span>
                            <?php esc_html_e( 'Sticky Header', 'header-footer-builder-for-elementor' ); ?>
                        </label>
                        <label class="thfb-toggle-label">
                            <input type="checkbox" id="thfb-cond-animation">
                            <span class="thfb-toggle-switch"></span>
                            <?php esc_html_e( 'Scroll Animation', 'header-footer-builder-for-elementor' ); ?>
                        </label>
                    </div>
                </div>
            </div>
            <div class="thfb-modal-footer">
                <button class="thfb-btn thfb-btn-primary" id="thfb-cond-save">
                    <span class="dashicons dashicons-saved"></span>
                    <?php esc_html_e( 'Save Conditions', 'header-footer-builder-for-elementor' ); ?>
                </button>
                <button class="thfb-btn thfb-btn-ghost" id="thfb-cond-cancel"><?php esc_html_e( 'Cancel', 'header-footer-builder-for-elementor' ); ?></button>
            </div>
        </div>
    </div>
    <?php
}



/* Video Modal HTML Removed */

/* ─────────────────────────────────────────────────────────────
   14. Elementor / WooCommerce support (unchanged)
───────────────────────────────────────────────────────────── */
add_action( 'elementor/init', function () {
    if ( post_type_exists( 'tahefobu_single_template' ) ) {
        add_post_type_support( 'tahefobu_single_template', 'elementor' );
    }
} );

if ( class_exists( 'WooCommerce' ) ) {
    add_action( 'wp_enqueue_scripts', function () {
        if ( is_product() && class_exists( '\Elementor\Plugin' ) ) {
            $frontend = \Elementor\Plugin::instance()->frontend;
            $frontend->enqueue_styles();
            $frontend->enqueue_scripts();
        }
    } );
}

add_filter( 'elementor/frontend/print_css', '__return_true' );
