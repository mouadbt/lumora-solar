<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'tahefobu_hf_allowed_html' ) ) {
    /**
     * Allowed HTML for rendering Elementor template output safely (with icon support).
     */
    function tahefobu_hf_allowed_html() {
        $allowed = wp_kses_allowed_html( 'post' );

        // ✅ Allow Elementor/FontAwesome <i> tags
        $allowed['i'] = [
            'class'       => true,
            'aria-hidden' => true,
            'data-*'      => true, // catch-all for Elementor’s dynamic data attributes
        ];

        // ✅ Allow Elementor <span> wrappers
        $allowed['span'] = [
            'class'       => true,
            'aria-hidden' => true,
            'data-*'      => true,
        ];

        // ✅ Allow SVG (used in Elementor icons)
        $allowed['svg'] = [
            'class'        => true,
            'xmlns'        => true,
            'xmlns:xlink'  => true,
            'xlink'        => true,
            'viewBox'      => true,
            'width'        => true,
            'height'       => true,
            'fill'         => true,
            'stroke'       => true,
            'aria-hidden'  => true,
            'role'         => true,
            'focusable'    => true,
            'data-*'       => true,
        ];

        // ✅ Allow <path> inside SVG
        $allowed['path'] = [
            'd'              => true,
            'fill'           => true,
            'fill-rule'      => true,
            'stroke'         => true,
            'stroke-width'   => true,
            'stroke-linecap' => true,
            'stroke-linejoin'=> true,
        ];

        // ✅ Allow <use> inside SVG for FA/Elementor icons
        $allowed['use'] = [
            'xlink:href' => true,
            'href'       => true,
        ];

        // ✅ Elementor lightbox attributes on <a>
        if ( isset( $allowed['a'] ) ) {
            $allowed['a']['data-elementor-open-lightbox']      = true;
            $allowed['a']['data-elementor-lightbox-slideshow'] = true;
            $allowed['a']['data-elementor-lightbox-title']     = true;
            $allowed['a']['data-*']                           = true;
        }

        // ✅ Extended <img> attributes
        $allowed['img'] = array_merge(
            $allowed['img'] ?? [],
            [
                'src'      => true,
                'alt'      => true,
                'srcset'   => true,
                'sizes'    => true,
                'loading'  => true,
                'decoding' => true,
                'data-*'   => true,
            ]
        );

        /**
         * Filters the allowed HTML tags/attributes for Header Footer Builder templates.
         *
         * @param array $allowed The list of allowed HTML.
         */
        return apply_filters( 'tahefobu_hf_allowed_html', $allowed );
    }
}


/**
 * Register all widget CSS & JS files from assets folder.
 * Elementor will load these when widget asks using get_style_depends().
 */

if ( ! function_exists( 'tahefobu_register_assets' ) ) {

    function tahefobu_register_assets() {

        // Use the plugin version constant for cache-busting instead of filemtime()
        // (avoids a filesystem stat call on every page load in production).
        $ver = defined( 'TAHEFOBU_HEADER_FOOTER_BUILDER_FOR_ELEMENTOR_PLUGIN_VERSION' )
            ? TAHEFOBU_HEADER_FOOTER_BUILDER_FOR_ELEMENTOR_PLUGIN_VERSION
            : '1.0.0';

        // CSS
        wp_register_style(
            'tahefobu-navigation-menu-style',
            TAHEFOBU_HEADER_FOOTER_BUILDER_FOR_ELEMENTOR_PLUGIN_URL . 'assets/css/navigation-menu-hf.css',
            [],
            $ver,
            'all'
        );
        wp_register_style(
            'tahefobu-icon-button-style',
            TAHEFOBU_HEADER_FOOTER_BUILDER_FOR_ELEMENTOR_PLUGIN_URL . 'assets/css/icon-button-hf.css',
            [],
            $ver,
            'all'
        );
        wp_register_style(
            'tahefobu-top-bar-widgets-style',
            TAHEFOBU_HEADER_FOOTER_BUILDER_FOR_ELEMENTOR_PLUGIN_URL . 'assets/css/top-bar-widgets-hf.css',
            [],
            $ver,
            'all'
        );

        // JS
        wp_register_script(
            'tahefobu-navigation-menu-script',
            TAHEFOBU_HEADER_FOOTER_BUILDER_FOR_ELEMENTOR_PLUGIN_URL . 'assets/js/navigation-menu-hf.js',
            [ 'jquery' ],
            $ver,
            true
        );
    }
}


