<?php
/**
 * Plugin Name: TEMPO House — Elementor Widgets
 * Description: Custom Elementor widgets for TEMPO House — Hero, Events Carousel, Spaces Carousel, Tempo Frame, Cocktail Carousel, Reserve CTA, Newsletter, Gallery Walk.
 * Version: 1.0.0
 * Author: Raging Monk
 * Requires Plugins: elementor
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'THR_ELEMENTOR_PATH', plugin_dir_path( __FILE__ ) );
define( 'THR_ELEMENTOR_URI',  plugin_dir_url( __FILE__ ) );

// Guard: Elementor must be active
add_action( 'plugins_loaded', function () {
    if ( ! did_action( 'elementor/loaded' ) ) return;
    require_once THR_ELEMENTOR_PATH . 'includes/class-widgets-loader.php';
    new THR_Elementor_Widgets_Loader();
} );
