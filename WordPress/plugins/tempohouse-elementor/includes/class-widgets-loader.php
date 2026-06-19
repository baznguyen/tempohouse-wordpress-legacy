<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class THR_Elementor_Widgets_Loader {

    public function __construct() {
        add_action( 'elementor/elements/categories_registered', [ $this, 'register_category' ] );
        add_action( 'elementor/widgets/register',               [ $this, 'register_widgets' ] );
        add_action( 'elementor/frontend/after_enqueue_styles',  [ $this, 'enqueue_styles' ] );
        add_action( 'elementor/frontend/after_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
        add_action( 'elementor/editor/after_enqueue_scripts',   [ $this, 'enqueue_editor_scripts' ] );
        add_action( 'elementor/editor/after_enqueue_styles',    [ $this, 'enqueue_editor_styles' ] );
        add_action( 'admin_menu',                               [ 'THR_Kit_Setup', 'register_admin_page' ] );

        // Fallback: if a page has no Elementor data yet, serve the classic PHP
        // template on the frontend so the site stays functional during migration.
        add_filter( 'template_include', [ $this, 'fallback_to_php_template' ], 99 );

        // Add the original page-class to <body> so existing CSS selectors still
        // work when Elementor is rendering the page (e.g. .page-bar .page-bar__banner).
        add_filter( 'body_class', [ $this, 'add_page_body_classes' ] );

        require_once THR_ELEMENTOR_PATH . 'includes/class-kit-setup.php';
    }

    public function register_category( $manager ) {
        $manager->add_category( 'tempohouse', [
            'title' => 'TEMPO House',
            'icon'  => 'eicon-logo',
        ] );
    }

    public function register_widgets( $manager ) {
        $widgets = [
            'hero',
            'events-carousel',
            'moods-carousel',
            'tempo-frame',
            'cocktail-carousel',
            'reserve-cta',
            'newsletter',
            'gallery-walk',
        ];

        foreach ( $widgets as $widget ) {
            $file = THR_ELEMENTOR_PATH . 'widgets/class-widget-' . $widget . '.php';
            if ( file_exists( $file ) ) {
                require_once $file;
            }
        }

        $manager->register( new THR_Widget_Hero() );
        $manager->register( new THR_Widget_Events_Carousel() );
        $manager->register( new THR_Widget_Moods_Carousel() );
        $manager->register( new THR_Widget_Tempo_Frame() );
        $manager->register( new THR_Widget_Cocktail_Carousel() );
        $manager->register( new THR_Widget_Reserve_CTA() );
        $manager->register( new THR_Widget_Newsletter() );
        $manager->register( new THR_Widget_Gallery_Walk() );
    }

    // ── Google Fonts URL ─────────────────────────────────────────────────────
    private static function google_fonts_url() {
        return 'https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@400;500;600&family=Cormorant+Garamond:ital,wght@0,300;0,400;1,300;1,400&family=Space+Grotesk:wght@300;400;500&display=swap';
    }

    public function enqueue_styles() {
        $theme_uri = get_template_directory_uri();
        $ver       = '3.75.1';

        // Fonts — loads in the editor preview canvas AND on the live frontend.
        // header.php also has the link tags for the classic theme; that's a
        // temporary overlap that disappears once we switch to Hello Elementor.
        wp_enqueue_style( 'tempo-fonts-preconnect-1', 'https://fonts.googleapis.com', [], null );
        wp_enqueue_style( 'tempo-fonts-preconnect-2', 'https://fonts.gstatic.com',    [], null );
        wp_enqueue_style( 'tempo-google-fonts', self::google_fonts_url(), [ 'tempo-fonts-preconnect-1', 'tempo-fonts-preconnect-2' ], null );

        // Core design system — must load after Elementor's widget CSS
        wp_enqueue_style( 'tempohouse-tokens',        $theme_uri . '/assets/css/tokens.css',                            [],                          $ver );
        wp_enqueue_style( 'tempohouse-base',          $theme_uri . '/assets/css/base.css',                              ['tempohouse-tokens'],        $ver );
        wp_enqueue_style( 'tempohouse-nav',           $theme_uri . '/assets/css/components/nav.css',                    ['tempohouse-base'],          $ver );
        wp_enqueue_style( 'tempohouse-hero',          $theme_uri . '/assets/css/components/hero.css',                   ['tempohouse-base'],          $ver );
        wp_enqueue_style( 'tempohouse-moods',         $theme_uri . '/assets/css/components/moods.css',                  ['tempohouse-base'],          $ver );
        wp_enqueue_style( 'tempohouse-events',        $theme_uri . '/assets/css/components/events.css',                 ['tempohouse-base'],          $ver );
        wp_enqueue_style( 'tempohouse-reserve',       $theme_uri . '/assets/css/components/reserve.css',                ['tempohouse-base'],          $ver );
        wp_enqueue_style( 'tempohouse-newsletter',    $theme_uri . '/assets/css/components/newsletter.css',             ['tempohouse-base'],          $ver );
        wp_enqueue_style( 'tempohouse-footer',        $theme_uri . '/assets/css/components/footer.css',                 ['tempohouse-base'],          $ver );
        wp_enqueue_style( 'tempohouse-time-theme',    $theme_uri . '/assets/css/components/time-theme.css',             ['tempohouse-base'],          $ver );
        wp_enqueue_style( 'tempohouse-tempo-frame',   $theme_uri . '/assets/css/components/tempo-frame.css',            ['tempohouse-base'],          $ver );
        wp_enqueue_style( 'tempohouse-tempo-carousel',$theme_uri . '/assets/css/components/tempo-carousel.css',         ['tempohouse-base'],          $ver );
        wp_enqueue_style( 'tempohouse-inner-page',    $theme_uri . '/assets/css/pages/inner-page.css',                  ['tempohouse-base'],          $ver );
        wp_enqueue_style( 'tempohouse-bar',           $theme_uri . '/assets/css/pages/bar.css',                         ['tempohouse-inner-page'],    $ver );
        wp_enqueue_style( 'tempohouse-cafe',          $theme_uri . '/assets/css/pages/cafe.css',                        ['tempohouse-inner-page'],    $ver );
        wp_enqueue_style( 'tempohouse-gallery',       $theme_uri . '/assets/css/pages/gallery.css',                     ['tempohouse-inner-page'],    $ver );
        wp_enqueue_style( 'tempohouse-venue',         $theme_uri . '/assets/css/pages/venue.css',                       ['tempohouse-inner-page'],    $ver );
        wp_enqueue_style( 'tempohouse-events-pages',  $theme_uri . '/assets/css/pages/events-pages.css',                ['tempohouse-inner-page'],    $ver );
        wp_enqueue_style( 'tempohouse-whats-on',      $theme_uri . '/assets/css/pages/whats-on.css',                    ['tempohouse-inner-page'],    $ver );
        wp_enqueue_style( 'tempohouse-contact',       $theme_uri . '/assets/css/pages/contact.css',                     ['tempohouse-inner-page'],    $ver );
        wp_enqueue_style( 'tempohouse-venue-fp',      $theme_uri . '/assets/css/components/venue-floorplan.css',        ['tempohouse-tempo-frame'],   $ver );
    }

    public function enqueue_scripts() {
        $theme_uri = get_template_directory_uri();
        $ver       = '3.75.1';

        wp_enqueue_script( 'tempohouse-drag',          $theme_uri . '/assets/js/drag-scroll.js',    [],                         $ver, true );
        wp_enqueue_script( 'tempohouse-hero-js',       $theme_uri . '/assets/js/hero.js',           [],                         $ver, true );
        wp_enqueue_script( 'tempohouse-moods-js',      $theme_uri . '/assets/js/moods.js',          ['tempohouse-drag'],         $ver, true );
        wp_enqueue_script( 'tempohouse-events-js',     $theme_uri . '/assets/js/events.js',         ['tempohouse-drag'],         $ver, true );
        wp_enqueue_script( 'tempohouse-time-switcher', $theme_uri . '/assets/js/time-switcher.js',  [],                         $ver, true );
        wp_enqueue_script( 'tempohouse-spotlight',     $theme_uri . '/assets/js/spotlight.js',      ['tempohouse-moods-js'],     $ver, true );
        wp_enqueue_script( 'tempohouse-tempo-frame',   $theme_uri . '/assets/js/tempo-frame.js',    [],                         $ver, true );
        wp_enqueue_script( 'tempohouse-tempo-carousel',$theme_uri . '/assets/js/tempo-carousel.js', [],                         $ver, true );
        wp_enqueue_script( 'tempohouse-bar-js',        $theme_uri . '/assets/js/page-bar.js',       [],                         $ver, true );
        wp_enqueue_script( 'tempohouse-cafe-js',       $theme_uri . '/assets/js/page-cafe.js',      [],                         $ver, true );
        wp_enqueue_script( 'tempohouse-gallery-js',    $theme_uri . '/assets/js/page-gallery.js',   [],                         $ver, true );
        wp_enqueue_script( 'tempohouse-whats-on-js',   $theme_uri . '/assets/js/whats-on.js',       ['tempohouse-drag'],         $ver, true );
        wp_enqueue_script( 'tempohouse-venue-fp',      $theme_uri . '/assets/js/venue-floorplan.js',  [],                       $ver, true );
        wp_enqueue_script( 'tempohouse-events-spaces', $theme_uri . '/assets/js/events-spaces.js',    [],                       $ver, true );

        // Adapter: reinitialises each widget's JS when Elementor drops it on canvas
        wp_enqueue_script(
            'thr-elementor-adapter',
            THR_ELEMENTOR_URI . 'assets/js/elementor-adapter.js',
            ['elementor-frontend'],
            '1.0.0',
            true
        );
    }

    /**
     * If a page's _wp_page_template is set to an Elementor template but the page
     * has no Elementor data yet, fall back to the original theme PHP template so
     * the frontend stays intact during the migration.
     *
     * Once a page is saved with Elementor content (_elementor_data is populated),
     * Elementor takes over and this filter is a no-op for that page.
     */
    public function add_page_body_classes( $classes ) {
        $map = [
            4  => 'page-cafe',
            5  => 'page-bar',
            6  => 'page-gallery',
            7  => 'page-venue',
            8  => 'page-whats-on',
            9  => 'page-events',
            10 => 'page-reservations',
            11 => 'page-contact',
        ];
        $id = get_the_ID();
        if ( isset( $map[ $id ] ) ) {
            $classes[] = $map[ $id ];
        }
        return $classes;
    }

    public function fallback_to_php_template( $template ) {
        if ( ! is_singular() ) return $template;
        if ( \Elementor\Plugin::$instance->preview->is_preview_mode() ) return $template;
        if ( \Elementor\Plugin::$instance->editor->is_edit_mode() )     return $template;

        $post_id       = get_the_ID();
        $page_template = get_post_meta( $post_id, '_wp_page_template', true );

        // Only intercept pages we set to an Elementor template
        if ( ! in_array( $page_template, [ 'elementor_header_footer', 'elementor_canvas' ], true ) ) {
            return $template;
        }

        // Check if Elementor has actually built this page yet
        $elementor_data = get_post_meta( $post_id, '_elementor_data', true );
        if ( ! empty( $elementor_data ) && $elementor_data !== '[]' ) {
            return $template; // Elementor has content — let it render
        }

        $theme_dir = get_template_directory();

        // Home page — WP uses front-page.php in the theme root
        if ( is_front_page() ) {
            $fp = $theme_dir . '/front-page.php';
            if ( file_exists( $fp ) ) return $fp;
        }

        // Inner pages — map to their original PHP templates
        $php_template_map = [
            4  => 'page-templates/page-cafe.php',
            5  => 'page-templates/page-bar.php',
            6  => 'page-templates/page-gallery.php',
            7  => 'page-templates/page-venue.php',
            8  => 'page-templates/page-whats-on.php',
            9  => 'page-templates/page-events.php',
            10 => 'page-templates/page-reservations.php',
            11 => 'page-templates/page-contact.php',
        ];

        if ( isset( $php_template_map[ $post_id ] ) ) {
            $php_path = $theme_dir . '/' . $php_template_map[ $post_id ];
            if ( file_exists( $php_path ) ) return $php_path;
        }

        return $template;
    }

    public function enqueue_editor_scripts() {
        // Inline head snippet — prevent FOUC in the editor preview iframe
        wp_add_inline_script(
            'elementor-editor',
            '(function(){try{var t=localStorage.getItem("tempo-time");document.documentElement.setAttribute("data-tempo-time",t||"day");}catch(e){}})()',
            'before'
        );
    }

    /**
     * Loads fonts and design tokens into the Elementor editor panel itself
     * (the admin-side UI, not the preview iframe). This ensures widget labels,
     * inline text inputs, and the panel font picker preview use the brand fonts.
     */
    public function enqueue_editor_styles() {
        // Google Fonts in the editor panel
        wp_enqueue_style( 'tempo-editor-fonts', self::google_fonts_url(), [], null );

        // Inline token block so --tempo-* and --color-* resolve in the editor panel
        wp_add_inline_style( 'tempo-editor-fonts', '
:root {
  --tempo-cream:      #F7F3EE;
  --tempo-ink:        #1A1816;
  --tempo-terracotta: #7C3B3B;
  --tempo-amber:      #DDAA62;
  --tempo-sage:       #6B7B5E;
  --tempo-sand:       #C8B89A;
  --color-accent:     #7C3B3B;
  --font-display:     \'Bricolage Grotesque\', sans-serif;
  --font-accent:      \'Cormorant Garamond\', serif;
  --font-body:        \'Space Grotesk\', sans-serif;
}' );
    }
}
