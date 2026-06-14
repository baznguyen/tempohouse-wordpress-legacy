<?php
require_once get_template_directory() . '/inc/setup.php';
require_once get_template_directory() . '/inc/cpt-events.php';
require_once get_template_directory() . '/inc/acf-fields.php';
require_once get_template_directory() . '/inc/seo.php';
require_once get_template_directory() . '/inc/enquiry.php';

function tempohouse_enqueue_assets() {
    $ver = '3.18.1';
    $uri = get_template_directory_uri();

    wp_enqueue_style( 'tempohouse-tokens',     $uri . '/assets/css/tokens.css',                [],                        $ver );
    wp_enqueue_style( 'tempohouse-base',       $uri . '/assets/css/base.css',                  [ 'tempohouse-tokens' ],   $ver );
    wp_enqueue_style( 'tempohouse-nav',        $uri . '/assets/css/components/nav.css',        [ 'tempohouse-base' ],     $ver );
    wp_enqueue_style( 'tempohouse-hero',       $uri . '/assets/css/components/hero.css',       [ 'tempohouse-base' ],     $ver );
    wp_enqueue_style( 'tempohouse-moods',      $uri . '/assets/css/components/moods.css',      [ 'tempohouse-base' ],     $ver );
    wp_enqueue_style( 'tempohouse-events',     $uri . '/assets/css/components/events.css',     [ 'tempohouse-base' ],     $ver );
    wp_enqueue_style( 'tempohouse-reserve',    $uri . '/assets/css/components/reserve.css',    [ 'tempohouse-base' ],     $ver );
    wp_enqueue_style( 'tempohouse-newsletter', $uri . '/assets/css/components/newsletter.css', [ 'tempohouse-base' ],     $ver );
    wp_enqueue_style( 'tempohouse-footer',     $uri . '/assets/css/components/footer.css',     [ 'tempohouse-base' ],     $ver );
    wp_enqueue_style( 'tempohouse-events-venue',   $uri . '/assets/css/components/events-venue.css',   [ 'tempohouse-base' ], $ver );
    wp_enqueue_style( 'tempohouse-spotlight',      $uri . '/assets/css/components/spotlight.css',      [ 'tempohouse-base' ], $ver );
    wp_enqueue_style( 'tempohouse-time-theme',     $uri . '/assets/css/components/time-theme.css',     [ 'tempohouse-spotlight' ], $ver );

    // Events enquiry page — load only on that template
    if ( is_page_template( 'page-templates/events-enquiry.php' ) ) {
        wp_enqueue_style( 'tempohouse-events-enquiry', $uri . '/assets/css/components/events-enquiry.css', [ 'tempohouse-base' ], $ver );
    }

    // Café page — load only on that template
    if ( is_page_template( 'page-templates/page-cafe.php' ) ) {
        wp_enqueue_style( 'tempohouse-inner-page', $uri . '/assets/css/pages/inner-page.css', [ 'tempohouse-base' ], $ver );
        wp_enqueue_style( 'tempohouse-cafe',       $uri . '/assets/css/pages/cafe.css',       [ 'tempohouse-inner-page' ], $ver );
    }

    // Bar page — load only on that template
    if ( is_page_template( 'page-templates/page-bar.php' ) ) {
        wp_enqueue_style( 'tempohouse-inner-page', $uri . '/assets/css/pages/inner-page.css', [ 'tempohouse-base' ], $ver );
        wp_enqueue_style( 'tempohouse-bar',        $uri . '/assets/css/pages/bar.css',        [ 'tempohouse-inner-page' ], $ver );
    }

    // Gallery page — load only on that template
    if ( is_page_template( 'page-templates/page-gallery.php' ) ) {
        wp_enqueue_style( 'tempohouse-inner-page', $uri . '/assets/css/pages/inner-page.css',    [ 'tempohouse-base' ],       $ver );
        wp_enqueue_style( 'tempohouse-gallery',    $uri . '/assets/css/pages/gallery.css',        [ 'tempohouse-inner-page' ], $ver );
    }

    // What's On page — load only on that template
    if ( is_page_template( 'page-templates/page-whats-on.php' ) ) {
        wp_enqueue_style( 'tempohouse-inner-page', $uri . '/assets/css/pages/inner-page.css', [ 'tempohouse-base' ],       $ver );
        wp_enqueue_style( 'tempohouse-whats-on',   $uri . '/assets/css/pages/whats-on.css',   [ 'tempohouse-inner-page' ], $ver );
    }

    // Reservations page — load only on that template
    if ( is_page_template( 'page-templates/page-reservations.php' ) ) {
        wp_enqueue_style( 'tempohouse-inner-page',    $uri . '/assets/css/pages/inner-page.css',    [ 'tempohouse-base' ],          $ver );
        wp_enqueue_style( 'tempohouse-reservations',  $uri . '/assets/css/pages/reservations.css',  [ 'tempohouse-inner-page' ],    $ver );
    }

    // Events overview page — load only on that template
    if ( is_page_template( 'page-templates/page-events.php' ) ) {
        wp_enqueue_style( 'tempohouse-inner-page',    $uri . '/assets/css/pages/inner-page.css',    [ 'tempohouse-base' ],          $ver );
        wp_enqueue_style( 'tempohouse-events-pages',  $uri . '/assets/css/pages/events-pages.css',  [ 'tempohouse-inner-page' ],    $ver );
    }

    // Event type sub-pages — load only on that template
    if ( is_page_template( 'page-templates/page-event-type.php' ) ) {
        wp_enqueue_style( 'tempohouse-inner-page',    $uri . '/assets/css/pages/inner-page.css',    [ 'tempohouse-base' ],          $ver );
        wp_enqueue_style( 'tempohouse-events-pages',  $uri . '/assets/css/pages/events-pages.css',  [ 'tempohouse-inner-page' ],    $ver );
    }

    // Venue page — load only on that template
    if ( is_page_template( 'page-templates/page-venue.php' ) ) {
        wp_enqueue_style( 'tempohouse-inner-page', $uri . '/assets/css/pages/inner-page.css', [ 'tempohouse-base' ],       $ver );
        wp_enqueue_style( 'tempohouse-venue',      $uri . '/assets/css/pages/venue.css',      [ 'tempohouse-inner-page' ], $ver );
    }

    // Contact page — load only on that template
    if ( is_page_template( 'page-templates/page-contact.php' ) ) {
        wp_enqueue_style( 'tempohouse-inner-page', $uri . '/assets/css/pages/inner-page.css', [ 'tempohouse-base' ],       $ver );
        wp_enqueue_style( 'tempohouse-contact',    $uri . '/assets/css/pages/contact.css',    [ 'tempohouse-inner-page' ], $ver );
    }

    wp_enqueue_script( 'tempohouse-drag',         $uri . '/assets/js/drag-scroll.js',   [],                    $ver, true );
    wp_enqueue_script( 'tempohouse-hero-js',      $uri . '/assets/js/hero.js',          [],                    $ver, true );
    wp_enqueue_script( 'tempohouse-moods-js',     $uri . '/assets/js/moods.js',         [ 'tempohouse-drag' ], $ver, true );
    wp_enqueue_script( 'tempohouse-events-js',    $uri . '/assets/js/events.js',        [ 'tempohouse-drag' ], $ver, true );
    wp_enqueue_script( 'tempohouse-time-switcher', $uri . '/assets/js/time-switcher.js', [],                        $ver, true );
    wp_enqueue_script( 'tempohouse-spotlight',     $uri . '/assets/js/spotlight.js',     [ 'tempohouse-moods-js' ], $ver, true );
}
add_action( 'wp_enqueue_scripts', 'tempohouse_enqueue_assets' );

// Inline <head> snippet — sets html[data-tempo-time] before first paint to prevent FOUC
add_action( 'wp_head', function () {
    echo '<script>(function(){try{var t=localStorage.getItem("tempo-time"),h=new Date().getHours(),a=h>=5&&h<13?"day":h>=13&&h<18?"afternoon":"night";document.documentElement.setAttribute("data-tempo-time",t||a);}catch(e){}})()</script>' . "\n";
}, 1 );
