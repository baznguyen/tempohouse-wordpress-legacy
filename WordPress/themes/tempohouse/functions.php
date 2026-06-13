<?php
require_once get_template_directory() . '/inc/setup.php';
require_once get_template_directory() . '/inc/cpt-events.php';
require_once get_template_directory() . '/inc/acf-fields.php';
require_once get_template_directory() . '/inc/seo.php';

function tempohouse_enqueue_assets() {
    $ver = '3.15.1';
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
    wp_enqueue_style( 'tempohouse-spotlight',  $uri . '/assets/css/components/spotlight.css',  [ 'tempohouse-base' ],     $ver );
    wp_enqueue_style( 'tempohouse-time-theme', $uri . '/assets/css/components/time-theme.css', [ 'tempohouse-spotlight' ], $ver );

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
