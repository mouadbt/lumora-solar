<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'tahefobu_render_header' ) ) {
    function tahefobu_render_header() {
        static $rendered = false;

        if ( $rendered ) {
            return;
        }

        if ( is_admin() || wp_doing_ajax() ){
            return;
        }

        // Avoid output while editing or previewing our CPTs in Elementor
        if ( is_singular( 'tahefobu_header' ) || is_singular( 'tahefobu_footer' ) ){
            return;
        }

        // Strict handling of Elementor preview param
        if ( defined( 'ELEMENTOR_VERSION' ) && \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
            $pid = get_the_ID();

            if ( $pid && in_array( get_post_type( $pid ), [ 'tahefobu_header', 'tahefobu_footer' ], true ) ) {
                $nonce = isset( $_GET['tahefobu_nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['tahefobu_nonce'] ) ) : '';

                if ( ! $nonce || ! wp_verify_nonce( $nonce, 'tahefobu_preview_' . $pid ) ) {
                    return;
                }

                if ( ! is_user_logged_in() || ! current_user_can( 'edit_post', $pid ) ) {
                    return;
                }

                return;
            }
        }

        require_once plugin_dir_path( __FILE__ ) . 'turbo-header-template.php';
        if ( ! function_exists( 'tahefobu_get_matching_header_template_id' ) ) return;

        $header_template_id = tahefobu_get_matching_header_template_id();

        if ( ! $header_template_id
            || ! class_exists( '\Elementor\Plugin' )
            || get_post_type( $header_template_id ) !== 'tahefobu_header'
        ) {
            return;
        }

        $elementor = \Elementor\Plugin::instance();

        // Render content with inline CSS.
        $content = $elementor->frontend->get_builder_content_for_display( $header_template_id, true );

        if ( empty( $content ) ) {
            return;
        }

        // Enqueue Elementor frontend assets.
        $elementor->frontend->enqueue_styles();
        $elementor->frontend->enqueue_scripts();

        $classes = [ 'turbo-header-template' ];

        $is_sticky     = get_post_meta( $header_template_id, '_tahefobu_is_sticky', true );
        $has_animation = get_post_meta( $header_template_id, '_tahefobu_has_animation', true );

        if ( ! empty( $is_sticky ) )     $classes[] = 'ta-sticky-header';
        if ( ! empty( $has_animation ) ) $classes[] = 'ta-header-scroll-animation';

        $sticky_attr = ! empty( $is_sticky ) ? '1' : '0';
        $anim_attr   = ! empty( $has_animation ) ? '1' : '0';

        echo '<div id="tahefobu-header" class="' . esc_attr( implode( ' ', $classes ) ) . '" data-sticky="' . esc_attr( $sticky_attr ) . '" data-animation="' . esc_attr( $anim_attr ) . '">';
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo $content;
        echo '</div>';

        $rendered = true;
    }
}

// Hook into header locations
add_action( 'astra_masthead', 'tahefobu_render_header' );
add_action( 'elementskit/header', 'tahefobu_render_header' );
