<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Recommend Turbo Addons plugin if not active.
 */
class HFB_Recommend_Turbo_Addons {

    public function __construct() {
        add_action( 'admin_notices',        [ $this, 'show_recommendation_notice' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_notice_styles' ] );
    }

    /* ── Check if Turbo Addons FREE is active ─────────────── */
    private function hfbfe_is_turbo_addons_free_version_active() {
        if ( ! function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $active_plugins = get_option( 'active_plugins', [] );
        $all_plugins    = get_plugins();

        foreach ( $all_plugins as $plugin_file => $plugin_data ) {
            if (
                in_array( $plugin_file, $active_plugins, true ) &&
                isset( $plugin_data['Name'] ) &&
                $plugin_data['Name'] === 'Turbo Addons Elementor'
            ) {
                return true;
            }
        }
        return false;
    }

    /* ── Enqueue external CSS ──────────────────────────────── */
    public function enqueue_notice_styles() {
        if ( $this->hfbfe_is_turbo_addons_free_version_active() ) {
            return;
        }
        wp_enqueue_style(
            'hfb-recommendation-notice',
            plugins_url( 'assets/css/recomendation-noticeboard.css', dirname( __FILE__ ) ),
            [],
            defined( 'TAHEFOBU_HEADER_FOOTER_BUILDER_FOR_ELEMENTOR_PLUGIN_VERSION' )
                ? TAHEFOBU_HEADER_FOOTER_BUILDER_FOR_ELEMENTOR_PLUGIN_VERSION
                : '1.0.0'
        );
    }

    /* ── Render notice HTML ────────────────────────────────── */
    public function show_recommendation_notice() {

        if ( $this->hfbfe_is_turbo_addons_free_version_active() ) {
            return;
        }

        // Session-based dismiss — hides immediately before paint
        ?>
        <script>
        if ( sessionStorage.getItem( 'hfb_turbo_notice_dismissed' ) === '1' ) {
            document.write( '<style>#hfb-turbo-notice{display:none!important;}<\/style>' );
        }
        </script>
        <?php

        include_once ABSPATH . 'wp-admin/includes/plugin.php';

        $install_url = wp_nonce_url(
            self_admin_url( 'update.php?action=install-plugin&plugin=turbo-addons-elementor' ),
            'install-plugin_turbo-addons-elementor'
        );
        $activate_url = wp_nonce_url(
            self_admin_url( 'plugins.php?action=activate&plugin=turbo-addons-elementor%2Fturbo-addons-elementor.php' ),
            'activate-plugin_turbo-addons-elementor/turbo-addons-elementor.php'
        );
        $is_installed = file_exists( WP_PLUGIN_DIR . '/turbo-addons-elementor/turbo-addons-elementor.php' );
        $action_url   = $is_installed ? $activate_url : $install_url;
        $action_label = $is_installed
            ? esc_html__( '🗲 Activate Turbo Addons — Free', 'header-footer-builder-for-elementor' )
            : esc_html__( '🗲 Install Turbo Addons — Free', 'header-footer-builder-for-elementor' );
        $banner_src = esc_url( plugins_url( 'assets/images/promotion-banner.webp', dirname( __FILE__ ) ) );
        ?>

        <div id="hfb-turbo-notice" class="notice is-dismissible">
            <div class="hfb-notice-inner">

                <div class="hfb-notice-stripe"></div>

                <div class="hfb-notice-body">

                    <div class="hfb-social-proof">
                        <!-- <span class="hfb-stars">★★★★★</span> -->
                      <h3>  <?php esc_html_e( 'Thanks for Installing Header Footer Builder! ', 'header-footer-builder-for-elementor' ); ?></h3>
                    </div>

                    <p class="hfb-notice-headline">
                        <?php esc_html_e( 'Add Turbo Addons → 200+ full website templates + library upgrades constantly + weekly fresh designs – offer active now', 'header-footer-builder-for-elementor' ); ?>
                        <span class="hfb-badge"><?php esc_html_e( '60% OFF', 'header-footer-builder-for-elementor' ); ?></span>
                    </p>

                    <ul class="hfb-notice-features">
                        <li><?php esc_html_e( 'Unlock WooCommerce Features', 'header-footer-builder-for-elementor' ); ?></li>
                        <li><?php esc_html_e( '1-Click Import', 'header-footer-builder-for-elementor' ); ?></li>
                        <li><?php esc_html_e( 'New Designs Added Weekly', 'header-footer-builder-for-elementor' ); ?></li>
                        <li><?php esc_html_e( 'Works with Free Elementor', 'header-footer-builder-for-elementor' ); ?></li>
                    </ul>

                    <div class="hfb-notice-actions">
                        <a href="<?php echo esc_url( $action_url ); ?>" class="hfb-btn-primary">
                            <?php echo esc_html( $action_label ); ?>
                        </a>
                        <a href="https://turbo-addons.com/templates/" target="_blank" rel="noopener noreferrer" class="hfb-btn-secondary">
                            <?php esc_html_e( 'Browse All Templates →', 'header-footer-builder-for-elementor' ); ?>
                        </a>
                    </div>

                </div>

                <div class="hfb-notice-image">
                    <img src="<?php echo $banner_src; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already esc_url'd ?>"
                         alt="<?php esc_attr_e( 'Turbo Addons Templates', 'header-footer-builder-for-elementor' ); ?>">
                </div>

            </div>
        </div>

        <script>
        ( function () {
            var notice = document.getElementById( 'hfb-turbo-notice' );
            if ( ! notice ) return;
            notice.addEventListener( 'click', function ( e ) {
                if ( e.target.classList.contains( 'notice-dismiss' ) ) {
                    sessionStorage.setItem( 'hfb_turbo_notice_dismissed', '1' );
                }
            } );
        } )();
        </script>
        <?php
    }
}

new HFB_Recommend_Turbo_Addons();
