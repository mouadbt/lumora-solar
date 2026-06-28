<?php
/**
 * Plugin Name: Simple Header Footer Builder for Elementor
 * Plugin URI: https://wordpress.org/plugins/header-footer-builder-for-elementor/
 * Description: Simple Header Footer Builder for Elementor. Easy, customizable plugin for headers/footers with display rules, sticky header & include/exclude. Custom personal edition.
 * Version: 1.2.4
 * Requires at least: 4.7.0
 * Author: Personal Custom Version
 * Author URI: https://wordpress.org/
 * License: GPLv3
 * License URI: https://opensource.org/licenses/GPL-3.0
 * Text Domain: header-footer-builder-for-elementor
 * Elementor tested up to: 4.1.4
 * Elementor Pro tested up to: 4.1.4
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


/**
 * Main Plugin Class
 * @since 1.0.0
 */
final class TAHEFOBU_Header_Footer_Builder_For_Elementor {
    const TAHEFOBU_HEADER_FOOTER_BUILDER_FOR_ELEMENTOR_MIN_ELEMENTOR_VERSION = '3.0.0';
    const TAHEFOBU_HEADER_FOOTER_BUILDER_FOR_ELEMENTOR_MIN_PHP_VERSION = '7.4';
    
    private static $_instance = null;

    /**
     * Singleton Instance Method
     * @since 1.0.0
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Constructor
     * @since 1.0.0
     */
    public function __construct() {
        // Load helper once
        include_once plugin_dir_path( __FILE__ ) . 'helper/helper.php';
        $this->define_constants();

        // Frontend assets
        add_action( 'wp_enqueue_scripts', [ $this, 'tahefobu_header_footer_builder_for_elementor_enqueue_scripts_styles' ] );
        add_action( 'plugins_loaded', [ $this, 'init' ] );
        add_action( 'elementor/editor/after_enqueue_styles', [ $this, 'tahefobu_header_footer_builder_for_elementor_editor_icon_enqueue_scripts' ] );

        // Widget category
        add_action( 'elementor/elements/categories_registered', [ $this, 'register_widgets_category' ] );

        // widgets = style + script
        add_action( 'elementor/widgets/register', [ $this, 'register_new_hf_widgets' ] );
        add_action( 'wp_enqueue_scripts', 'tahefobu_register_assets' );
        add_action( 'elementor/frontend/before_enqueue_scripts', 'tahefobu_register_assets' );
    }
    
    /**
     * Define Plugin Constants
     * @since 1.0.0
     */
    private function define_constants() {
        define( 'TAHEFOBU_HEADER_FOOTER_BUILDER_FOR_ELEMENTOR_PLUGIN_URL', trailingslashit( plugins_url( '/', __FILE__ ) ) );
        define( 'TAHEFOBU_HEADER_FOOTER_BUILDER_FOR_ELEMENTOR_PLUGIN_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
        define( 'TAHEFOBU_HEADER_FOOTER_BUILDER_FOR_ELEMENTOR_PLUGIN_VERSION', '1.2.4' );
    }

    /**
     * Enqueue Scripts & Styles
     * Only loads on pages where a header template is active to avoid
     * unnecessary asset loading on every frontend page.
     * @since 1.0.0
     */
    public function tahefobu_header_footer_builder_for_elementor_enqueue_scripts_styles() {
        // Only enqueue when our header will actually render on this page.
        if ( empty( $GLOBALS['tahefobu_header_will_render'] ) ) {
            return;
        }

        // turbo header css
        wp_enqueue_style(
            'tahefobu-header-style',
            TAHEFOBU_HEADER_FOOTER_BUILDER_FOR_ELEMENTOR_PLUGIN_URL . 'assets/css/turbo-header-style.css',
            [],
            TAHEFOBU_HEADER_FOOTER_BUILDER_FOR_ELEMENTOR_PLUGIN_VERSION,
            'all'
        );

        // turbo header js
        wp_enqueue_script(
            'tahefobu-header-behavior',
            TAHEFOBU_HEADER_FOOTER_BUILDER_FOR_ELEMENTOR_PLUGIN_URL . 'assets/js/turbo-header-behavior.js',
            [ 'jquery' ],
            TAHEFOBU_HEADER_FOOTER_BUILDER_FOR_ELEMENTOR_PLUGIN_VERSION,
            true
        );
    }

    /**
     * Enqueue Styles For Widget Icon
     * @since 1.0.0
     */
    public function tahefobu_header_footer_builder_for_elementor_editor_icon_enqueue_scripts() {
        wp_enqueue_style(
            'tahefobu-editor-icon',
            TAHEFOBU_HEADER_FOOTER_BUILDER_FOR_ELEMENTOR_PLUGIN_URL . 'assets/css/editor-warning.css',
            [],
            TAHEFOBU_HEADER_FOOTER_BUILDER_FOR_ELEMENTOR_PLUGIN_VERSION,
            'all'
        );
    }

    /**
     * Load Text Domain for Translations
     * @since 1.0.0
     */
    // public function tahefobu_header_footer_builder_for_elementor_load_textdomain() {
    //     load_plugin_textdomain( 'header-footer-builder-for-elementor', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    // }

    /**
     * Initialize the plugin
     * @since 1.0.0
     */
    public function init() {
        if ( ! did_action( 'elementor/loaded' ) ) {
            add_action( 'admin_notices', [ $this, 'tahefobu_header_footer_builder_for_elementor_admin_notice_missing_main_plugin' ] );
            return;
        }

        if ( ! version_compare( ELEMENTOR_VERSION, self::TAHEFOBU_HEADER_FOOTER_BUILDER_FOR_ELEMENTOR_MIN_ELEMENTOR_VERSION, '>=' ) ) {
            add_action( 'admin_notices', [ $this, 'tahefobu_header_footer_builder_for_elementor_admin_notice_minimum_elementor_version' ] );
            return;
        }

        if ( ! version_compare( PHP_VERSION, self::TAHEFOBU_HEADER_FOOTER_BUILDER_FOR_ELEMENTOR_MIN_PHP_VERSION, '>=' ) ) {
            add_action( 'admin_notices', [ $this, 'tahefobu_header_footer_builder_for_elementor_admin_notice_minimum_php_version' ] );
            return;
        }
        // Auto-append the preview nonce for your CPTs (prevents broken preview)
        add_filter( 'elementor/document/urls/preview', function( $url, $document ) {
            $post_id = 0;
            if ( method_exists( $document, 'get_main_id' ) ) {
                $post_id = (int) $document->get_main_id();
            }
            if ( ! $post_id && method_exists( $document, 'get_id' ) ) {
                $post_id = (int) $document->get_id();
            }
            if ( ! $post_id ) {
                return $url;
            }

            $pt = get_post_type( $post_id );
            if ( in_array( $pt, [ 'tahefobu_header', 'tahefobu_footer' ], true ) ) {
                $url = add_query_arg(
                    'tahefobu_nonce',
                    wp_create_nonce( 'tahefobu_preview_' . $post_id ),
                    $url
                );
            }
            return $url;
        }, 10, 2 );

        // Load header and footer template functionality
        $this->load_header_footer_templates();

    }

    /**
     * Load Header and Footer Template Files
     * @since 1.0.0
     */
    private function load_header_footer_templates() {
        // Load header template functionality
        require_once TAHEFOBU_HEADER_FOOTER_BUILDER_FOR_ELEMENTOR_PLUGIN_PATH . 'header-footer-template/header-builder/turbo-header-template.php';
        require_once TAHEFOBU_HEADER_FOOTER_BUILDER_FOR_ELEMENTOR_PLUGIN_PATH . 'header-footer-template/header-builder/turbo-header-render.php';
        
        // Load footer template functionality
        require_once TAHEFOBU_HEADER_FOOTER_BUILDER_FOR_ELEMENTOR_PLUGIN_PATH . 'header-footer-template/footer-builder/turbo-footer-template.php';
        require_once TAHEFOBU_HEADER_FOOTER_BUILDER_FOR_ELEMENTOR_PLUGIN_PATH . 'header-footer-template/footer-builder/turbo-footer-render.php';
        
        // Load admin menu functionality
        require_once TAHEFOBU_HEADER_FOOTER_BUILDER_FOR_ELEMENTOR_PLUGIN_PATH . 'header-footer-template/header-footer-menu/header-footer-menu.php';

        // Note: helper.php is already loaded in __construct() — no need to load it again here.


        // Ensure Elementor CSS for the matched Header is enqueued in <head> to avoid FOUC
       add_action( 'wp_enqueue_scripts', function () {
            // Register a base stylesheet (can be empty if you don’t have a file)
            wp_register_style(
                'tahefobu-frontend',
                false, // no file, just for inline use
                [],
                TAHEFOBU_HEADER_FOOTER_BUILDER_FOR_ELEMENTOR_PLUGIN_VERSION
            );
            wp_enqueue_style( 'tahefobu-frontend' );

            // Start header visually hidden (opacity 0) but present in layout; apply a very short fade when ready.
            $dynamic_css = '#tahefobu-header { opacity: 0; transform: none; pointer-events: none; } #tahefobu-header.tahefobu-ready { opacity: 1; pointer-events: auto; transition: opacity .25s linear; }';
            wp_add_inline_style( 'tahefobu-frontend', $dynamic_css );
        }, 1 );


        // Ensure Elementor preview has the_content() for our CPTs on any theme
        add_filter( 'template_include', function ( $template ) {

            // Elementor preview handling — must be nonce + caps gated
            if ( isset( $_GET['elementor-preview'] ) ) {
                $raw_id = filter_input( INPUT_GET, 'elementor-preview', FILTER_SANITIZE_NUMBER_INT );
                $pid    = absint( $raw_id );
                $nonce  = filter_input( INPUT_GET, 'tahefobu_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

                // Fail early if nonce missing/invalid
                if ( ! $pid || ! $nonce || ! wp_verify_nonce( $nonce, 'tahefobu_preview_' . $pid ) ) {
                    return $template;
                }

                // Capability check (nonces aren’t auth)
                if ( ! is_user_logged_in() || ! current_user_can( 'edit_post', $pid ) ) {
                    return $template;
                }

                $pt = get_post_type( $pid );
                if ( in_array( $pt, [ 'tahefobu_header', 'tahefobu_footer' ], true ) ) {
                    return ( 'tahefobu_header' === $pt )
                        ? TAHEFOBU_HEADER_FOOTER_BUILDER_FOR_ELEMENTOR_PLUGIN_PATH . 'templates/single-tahefobu_header_template.php'
                        : TAHEFOBU_HEADER_FOOTER_BUILDER_FOR_ELEMENTOR_PLUGIN_PATH . 'templates/single-tahefobu_footer_template.php';
                }
            }

            // Normal singular views (safe)
            if ( is_singular( 'tahefobu_header' ) ) {
                return TAHEFOBU_HEADER_FOOTER_BUILDER_FOR_ELEMENTOR_PLUGIN_PATH . 'templates/single-tahefobu_header_template.php';
            }
            if ( is_singular( 'tahefobu_footer' ) ) {
                return TAHEFOBU_HEADER_FOOTER_BUILDER_FOR_ELEMENTOR_PLUGIN_PATH . 'templates/single-tahefobu_footer_template.php';
            }

            return $template;
        }, 99 );
    }

     /**
     * Admin Notice: Elementor not installed/activated
     * @since 1.0.0
     */
    public function tahefobu_header_footer_builder_for_elementor_admin_notice_missing_main_plugin() {
        if ( ! current_user_can( 'activate_plugins' ) ) {
            return;
        }

        printf(
            '<div class="notice notice-warning is-dismissible"><p>%s</p></div>',
            wp_kses_post( sprintf(
                /* translators: 1: Plugin name (Header Footer Builder), 2: Dependency name (Elementor) */
                esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'header-footer-builder-for-elementor' ),
                '<strong>' . esc_html__( 'Turbo Header Footer Builder For Elementor', 'header-footer-builder-for-elementor' ) . '</strong>',
                '<strong>' . esc_html__( 'Elementor', 'header-footer-builder-for-elementor' ) . '</strong>'
            ) )
        );
    }

    /**
     * Admin Notice for Minimum Elementor Version
     * @since 1.0.0
     */
    public function tahefobu_header_footer_builder_for_elementor_admin_notice_minimum_elementor_version() {
            if ( ! current_user_can( 'activate_plugins' ) ) {
                return;
            }

            printf(
                '<div class="notice notice-warning is-dismissible"><p>%s</p></div>',
                wp_kses_post( sprintf(
                    /* translators: 1: Plugin name (Header Footer Builder), 2: Dependency name (Elementor), 3: Minimum required Elementor version */
                    esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'header-footer-builder-for-elementor' ),
                    '<strong>' . esc_html__( 'Turbo Header Footer Builder For Elementor', 'header-footer-builder-for-elementor' ) . '</strong>',
                    '<strong>' . esc_html__( 'Elementor', 'header-footer-builder-for-elementor' ) . '</strong>',
                    esc_html( self::TAHEFOBU_HEADER_FOOTER_BUILDER_FOR_ELEMENTOR_MIN_ELEMENTOR_VERSION )
                ) )
            );
        }

   /**
     * Admin Notice for Minimum PHP Version
     * @since 1.0.0
     */
    public function tahefobu_header_footer_builder_for_elementor_admin_notice_minimum_php_version() {
        if ( ! current_user_can( 'activate_plugins' ) ) {
            return;
        }

        printf(
            '<div class="notice notice-warning is-dismissible"><p>%s</p></div>',
            wp_kses_post( sprintf(
                /* translators: 1: Plugin name (Header Footer Builder), 2: Software name (PHP), 3: Minimum required PHP version */
                esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'header-footer-builder-for-elementor' ),
                '<strong>' . esc_html__( 'Turbo Header Footer Builder For Elementor', 'header-footer-builder-for-elementor' ) . '</strong>',
                '<strong>' . esc_html__( 'PHP', 'header-footer-builder-for-elementor' ) . '</strong>',
                esc_html( self::TAHEFOBU_HEADER_FOOTER_BUILDER_FOR_ELEMENTOR_MIN_PHP_VERSION )
            ) )
        );
    }

    // category register//
    public function register_widgets_category( $elements_manager ) {

        $elements_manager->add_category(
            'tahefobu-hf-widgets',
            [
                'title' => __( 'Turbo H&F Builder', 'header-footer-builder-for-elementor' ),
                'icon'  => 'fa fa-plug',
            ]
        );
    }

    public function register_new_hf_widgets( $widgets_manager ) {

        $new_widgets = [
            'navigation-menu-hf.php',
            'icon-button-hf.php',
            'top-bar-hf.php',
            'copy-right-hf.php',
            'site-logo-hf.php',
        ];

        foreach ( $new_widgets as $file ) {
            $path = TAHEFOBU_HEADER_FOOTER_BUILDER_FOR_ELEMENTOR_PLUGIN_PATH . 'widgets/' . $file;

            if ( file_exists( $path ) ) {
                require_once $path;
            }
        }

        // Register one by one
        if ( class_exists('TAHEFOBU_Navigation_Menu') )
            $widgets_manager->register( new \TAHEFOBU_Navigation_Menu() );

        if ( class_exists('TAHEFOBU_Icon_Button') )
            $widgets_manager->register( new \TAHEFOBU_Icon_Button() );

        if ( class_exists('TAHEFOBU_Top_Bar') )
            $widgets_manager->register( new \TAHEFOBU_Top_Bar() );

        if ( class_exists('TAHEFOBU_Copy_Right') )
            $widgets_manager->register( new \TAHEFOBU_Copy_Right() );

        if ( class_exists('TAHEFOBU_Site_Logo') )
            $widgets_manager->register( new \TAHEFOBU_Site_Logo() );
    }

}

/**
 * On plugin activation — set a flag so we can redirect to our dashboard.
 * Uses register_activation_hook (runs before headers are sent, so we use
 * a transient and redirect on the next admin_init).
 */
function tahefobu_plugin_activate() {
    set_transient( 'tahefobu_activation_redirect', true, 30 );
}
register_activation_hook( __FILE__, 'tahefobu_plugin_activate' );

/**
 * Redirect to our dashboard after activation.
 * Skips bulk-activation (multiple plugins activated at once).
 */
add_action( 'admin_init', function () {
    if ( ! get_transient( 'tahefobu_activation_redirect' ) ) {
        return;
    }
    delete_transient( 'tahefobu_activation_redirect' );

    // Don't redirect during bulk activation
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only GET param check, no data written
    if ( isset( $_GET['activate-multi'] ) ) {
        return;
    }

    wp_safe_redirect( admin_url( 'admin.php?page=tahefobu_templates' ) );
    exit;
} );

/**
 * Redirect the old CPT list pages to our dashboard.
 * Users who bookmarked edit.php?post_type=tahefobu_header will land on the dashboard.
 */
add_action( 'admin_init', function () {
    if ( ! is_admin() || wp_doing_ajax() ) {
        return;
    }

    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only GET param, used only for navigation redirect
    $screen_id = isset( $_GET['post_type'] ) ? sanitize_key( $_GET['post_type'] ) : '';
    $base      = isset( $_SERVER['SCRIPT_NAME'] ) ? basename( sanitize_text_field( wp_unslash( $_SERVER['SCRIPT_NAME'] ) ) ) : '';

    // Only intercept the list table pages (edit.php), not post.php (Elementor editor)
    if ( $base === 'edit.php'
        && in_array( $screen_id, [ 'tahefobu_header', 'tahefobu_footer' ], true )
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only GET param check, no data written
        && ! isset( $_GET['page'] )
    ) {
        wp_safe_redirect( admin_url( 'admin.php?page=tahefobu_templates' ) );
        exit;
    }
} );


/**
 * Initializes the Plugin
 * @since 1.0.0
 */
/**
 * Initializes the Plugin only if Turbo Addons Pro is NOT active
 */
function tahefobu_header_footer_builder_for_elementor() {

    return TAHEFOBU_Header_Footer_Builder_For_Elementor::instance();
}

tahefobu_header_footer_builder_for_elementor();

