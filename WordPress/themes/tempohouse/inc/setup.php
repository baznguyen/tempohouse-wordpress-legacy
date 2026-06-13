<?php
function tempohouse_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', [
        'search-form', 'comment-form', 'comment-list',
        'gallery', 'caption', 'style', 'script',
    ] );
    add_theme_support( 'custom-logo', [
        'height'      => 48,
        'width'       => 120,
        'flex-height' => true,
        'flex-width'  => true,
    ] );

    register_nav_menus([
        'primary' => __( 'Primary Navigation', 'tempohouse' ),
        'footer'  => __( 'Footer Navigation', 'tempohouse' ),
    ]);

    add_image_size( 'event-card',   400, 540, true );
    add_image_size( 'space-frame',  560, 720, true );
}
add_action( 'after_setup_theme', 'tempohouse_setup' );

function tempohouse_body_classes( $classes ) {
    $classes[] = 'tempo-site';
    return $classes;
}
add_filter( 'body_class', 'tempohouse_body_classes' );

function tempohouse_content_width() {
    global $content_width;
    if ( ! isset( $content_width ) ) {
        $content_width = 1400;
    }
}
add_action( 'after_setup_theme', 'tempohouse_content_width', 0 );
