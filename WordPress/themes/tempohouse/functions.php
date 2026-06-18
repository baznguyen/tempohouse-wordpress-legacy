<?php
require_once get_template_directory() . '/inc/setup.php';
require_once get_template_directory() . '/inc/cpt-events.php';
require_once get_template_directory() . '/inc/acf-fields.php';
require_once get_template_directory() . '/inc/seo.php';
require_once get_template_directory() . '/inc/enquiry.php';

function tempohouse_enqueue_assets() {
    $ver = '3.63.3';
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

    // Global UI components — loaded sitewide
    wp_enqueue_style( 'tempohouse-tempo-frame',    $uri . '/assets/css/components/tempo-frame.css',    [ 'tempohouse-base' ],     $ver );
    wp_enqueue_style( 'tempohouse-tempo-carousel', $uri . '/assets/css/components/tempo-carousel.css', [ 'tempohouse-base' ],     $ver );

    // Events enquiry page — load only on that template
    if ( is_page_template( 'page-templates/events-enquiry.php' ) ) {
        wp_enqueue_style( 'tempohouse-events-enquiry', $uri . '/assets/css/components/events-enquiry.css', [ 'tempohouse-base' ], $ver );
    }

    // Café page — load only on that template
    if ( is_page_template( 'page-templates/page-cafe.php' ) ) {
        wp_enqueue_style( 'tempohouse-inner-page', $uri . '/assets/css/pages/inner-page.css', [ 'tempohouse-base' ], $ver );
        wp_enqueue_style( 'tempohouse-cafe',       $uri . '/assets/css/pages/cafe.css',       [ 'tempohouse-inner-page' ], $ver );
        wp_enqueue_script( 'tempohouse-cafe-js',   $uri . '/assets/js/page-cafe.js',          [], $ver, true );
    }

    // Bar page — load only on that template
    if ( is_page_template( 'page-templates/page-bar.php' ) ) {
        wp_enqueue_style( 'tempohouse-inner-page', $uri . '/assets/css/pages/inner-page.css', [ 'tempohouse-base' ], $ver );
        wp_enqueue_style( 'tempohouse-bar',        $uri . '/assets/css/pages/bar.css',        [ 'tempohouse-inner-page' ], $ver );
        wp_enqueue_script( 'tempohouse-bar-js',    $uri . '/assets/js/page-bar.js',           [], $ver, true );
    }

    // Gallery page — load only on that template
    if ( is_page_template( 'page-templates/page-gallery.php' ) ) {
        wp_enqueue_style( 'tempohouse-inner-page',  $uri . '/assets/css/pages/inner-page.css',    [ 'tempohouse-base' ],       $ver );
        wp_enqueue_style( 'tempohouse-gallery',     $uri . '/assets/css/pages/gallery.css',        [ 'tempohouse-inner-page' ], $ver );
        wp_enqueue_script( 'tempohouse-gallery-js', $uri . '/assets/js/page-gallery.js',           [],                          $ver, true );
    }

    // What's On page — also served as the CPT event archive, so is_page_template() returns false there.
    if ( is_page_template( 'page-templates/page-whats-on.php' ) || is_post_type_archive( 'event' ) || is_page( 'whats-on' ) ) {
        wp_enqueue_style( 'tempohouse-inner-page', $uri . '/assets/css/pages/inner-page.css', [ 'tempohouse-base' ],       $ver );
        wp_enqueue_style( 'tempohouse-whats-on',   $uri . '/assets/css/pages/whats-on.css',   [ 'tempohouse-inner-page' ], $ver );
        wp_enqueue_script( 'tempohouse-whats-on-js', $uri . '/assets/js/whats-on.js',         [ 'tempohouse-drag' ],       $ver, true );
    }

    // Reservations page — load only on that template
    if ( is_page_template( 'page-templates/page-reservations.php' ) ) {
        wp_enqueue_style( 'tempohouse-inner-page',    $uri . '/assets/css/pages/inner-page.css',    [ 'tempohouse-base' ],          $ver );
        wp_enqueue_style( 'tempohouse-reservations',  $uri . '/assets/css/pages/reservations.css',  [ 'tempohouse-inner-page' ],    $ver );
    }

    // Events overview page — load only on that template
    if ( is_page_template( 'page-templates/page-events.php' ) ) {
        wp_enqueue_style( 'tempohouse-inner-page',    $uri . '/assets/css/pages/inner-page.css',              [ 'tempohouse-base' ],          $ver );
        wp_enqueue_style( 'tempohouse-venue-fp',      $uri . '/assets/css/components/venue-floorplan.css',    [ 'tempohouse-tempo-frame' ],   $ver );
        wp_enqueue_style( 'tempohouse-events-pages',  $uri . '/assets/css/pages/events-pages.css',            [ 'tempohouse-inner-page' ],    $ver );
        wp_enqueue_style( 'tempohouse-events-faq',    $uri . '/assets/css/pages/events-faq.css',              [ 'tempohouse-inner-page' ],    $ver );
        wp_enqueue_script( 'tempohouse-events-spaces', $uri . '/assets/js/events-spaces.js',                  [],                             $ver, true );
        wp_enqueue_script( 'tempohouse-events-faq-js', $uri . '/assets/js/events-faq.js',                     [],                             $ver, true );
    }

    // FAQ page — load only on that template
    if ( is_page_template( 'page-templates/page-faq.php' ) ) {
        wp_enqueue_style( 'tempohouse-inner-page',    $uri . '/assets/css/pages/inner-page.css',  [ 'tempohouse-base' ],        $ver );
        wp_enqueue_style( 'tempohouse-events-faq',    $uri . '/assets/css/pages/events-faq.css',  [ 'tempohouse-inner-page' ],  $ver );
        wp_enqueue_script( 'tempohouse-events-faq-js', $uri . '/assets/js/events-faq.js',          [],                           $ver, true );
    }

    // Event type sub-pages — load only on that template
    if ( is_page_template( 'page-templates/page-event-type.php' ) ) {
        wp_enqueue_style( 'tempohouse-inner-page',    $uri . '/assets/css/pages/inner-page.css',    [ 'tempohouse-base' ],          $ver );
        wp_enqueue_style( 'tempohouse-events-pages',  $uri . '/assets/css/pages/events-pages.css',  [ 'tempohouse-inner-page' ],    $ver );
    }

    // Venue page — load only on that template
    if ( is_page_template( 'page-templates/page-venue.php' ) ) {
        wp_enqueue_style( 'tempohouse-inner-page',    $uri . '/assets/css/pages/inner-page.css',             [ 'tempohouse-base' ],             $ver );
        wp_enqueue_style( 'tempohouse-venue',         $uri . '/assets/css/pages/venue.css',                  [ 'tempohouse-inner-page' ],       $ver );
        wp_enqueue_style( 'tempohouse-venue-fp',      $uri . '/assets/css/components/venue-floorplan.css',   [ 'tempohouse-tempo-frame' ],      $ver );
    }

    // Contact page — load only on that template
    if ( is_page_template( 'page-templates/page-contact.php' ) ) {
        wp_enqueue_style( 'tempohouse-inner-page', $uri . '/assets/css/pages/inner-page.css', [ 'tempohouse-base' ],       $ver );
        wp_enqueue_style( 'tempohouse-contact',    $uri . '/assets/css/pages/contact.css',    [ 'tempohouse-inner-page' ], $ver );
    }

    // Event detail page — load on single Posts tagged 'event'.
    if ( is_singular( 'post' ) && has_tag( 'event' ) ) {
        wp_enqueue_style( 'tempohouse-inner-page',  $uri . '/assets/css/pages/inner-page.css',    [ 'tempohouse-base' ],          $ver );
        wp_enqueue_style( 'tempohouse-event-single', $uri . '/assets/css/pages/event-single.css', [ 'tempohouse-inner-page' ],    $ver );
    }

    wp_enqueue_script( 'tempohouse-drag',         $uri . '/assets/js/drag-scroll.js',   [],                    $ver, true );
    wp_enqueue_script( 'tempohouse-hero-js',      $uri . '/assets/js/hero.js',          [],                    $ver, true );
    wp_enqueue_script( 'tempohouse-moods-js',     $uri . '/assets/js/moods.js',         [ 'tempohouse-drag' ], $ver, true );
    wp_enqueue_script( 'tempohouse-events-js',    $uri . '/assets/js/events.js',        [ 'tempohouse-drag' ], $ver, true );
    wp_enqueue_script( 'tempohouse-time-switcher', $uri . '/assets/js/time-switcher.js', [],                        $ver, true );
    wp_enqueue_script( 'tempohouse-spotlight',     $uri . '/assets/js/spotlight.js',     [ 'tempohouse-moods-js' ], $ver, true );

    // Global UI components — loaded sitewide
    wp_enqueue_script( 'tempohouse-tempo-frame',    $uri . '/assets/js/tempo-frame.js',    [], $ver, true );
    wp_enqueue_script( 'tempohouse-tempo-carousel', $uri . '/assets/js/tempo-carousel.js', [], $ver, true );

    // Venue floor plan — only on venue template
    if ( is_page_template( 'page-templates/page-venue.php' ) ) {
        wp_enqueue_script( 'tempohouse-venue-fp', $uri . '/assets/js/venue-floorplan.js', [], $ver, true );
    }
}
add_action( 'wp_enqueue_scripts', 'tempohouse_enqueue_assets' );

// Register image sizes used by event cards and the event detail page hero.
add_action( 'after_setup_theme', function () {
    add_image_size( 'event-card',   800,  800, false ); // Card artwork — soft crop, preserves natural aspect ratio
    add_image_size( 'event-poster', 1920, 1080, true ); // Wide hero poster
    add_image_size( 'event-og',     1200, 630, true );  // Open Graph social share
} );

// Inline <head> snippet — sets html[data-tempo-time] before first paint to prevent FOUC
add_action( 'wp_head', function () {
    echo '<script>(function(){try{var t=localStorage.getItem("tempo-time"),h=new Date().getHours(),a=h>=5&&h<13?"day":h>=13&&h<18?"afternoon":"night";document.documentElement.setAttribute("data-tempo-time",t||a);}catch(e){}})()</script>' . "\n";
}, 1 );
