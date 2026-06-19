<?php
defined( 'ABSPATH' ) || exit;

class THR_Booking_Widget {

    public function init(): void {
        add_shortcode( 'th_booking_form',      [ $this, 'render_shortcode' ] );
        add_shortcode( 'th_events_enquiry',    [ $this, 'render_events_shortcode' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
        add_action( 'init', [ $this, 'register_page_template' ] );
        add_filter( 'page_template', [ $this, 'load_page_template' ] );

        // Handle guest cancel form (GET /reserve/cancel/)
        add_action( 'wp', [ $this, 'handle_cancel_page' ] );
    }

    public function enqueue_assets(): void {
        if ( ! $this->is_booking_context() ) return;

        if ( $this->is_widget_page() ) {
            // Standalone booking widget — assets only, thrBooking is output inline by booking-widget.php
            wp_enqueue_style(
                'th-booking-widget',
                THR_PLUGIN_URL . 'assets/css/booking-widget.css',
                [],
                THR_VERSION
            );
            wp_enqueue_script(
                'th-booking-widget',
                THR_PLUGIN_URL . 'assets/js/booking-widget.js',
                [],
                THR_VERSION,
                true
            );
            return;
        }

        // Legacy shortcode booking form
        wp_enqueue_style(
            'th-booking',
            THR_PLUGIN_URL . 'assets/css/booking.css',
            [],
            THR_VERSION
        );
        wp_enqueue_script(
            'th-booking',
            THR_PLUGIN_URL . 'assets/js/booking.js',
            [],
            THR_VERSION,
            true
        );
        wp_localize_script( 'th-booking', 'thrBooking', [
            'apiUrl'   => rest_url( THR_REST_NS . '/' ),
            'nonce'    => wp_create_nonce( 'wp_rest' ),
            'siteUrl'  => home_url(),
            'config'   => [
                'partySizeMin'    => (int) THR_Settings::get( 'party_size_min', 1 ),
                'partySizeMax'    => (int) THR_Settings::get( 'party_size_max', 20 ),
                'advanceMin'      => (int) THR_Settings::get( 'booking_advance_min', 60 ),
                'advanceMax'      => (int) THR_Settings::get( 'booking_advance_max', 60 ),
                'occasionTypes'   => THR_Settings::occasion_types(),
                'cancelPolicy'    => THR_Settings::get( 'cancel_policy_text' ),
                'defaultLang'     => THR_Settings::get( 'booking_default_lang', 'vi' ),
            ],
        ] );

        // Events enquiry form assets (only when shortcode is present)
        $post = get_post();
        if ( $post && has_shortcode( $post->post_content, 'th_events_enquiry' ) ) {
            wp_enqueue_script(
                'th-events-enquiry',
                THR_PLUGIN_URL . 'assets/js/events-enquiry.js',
                [],
                THR_VERSION,
                true
            );
            wp_localize_script( 'th-events-enquiry', 'thrEvents', [
                'apiUrl' => rest_url( THR_REST_NS . '/' ),
                'nonce'  => wp_create_nonce( 'wp_rest' ),
            ] );
        }
    }

    public function render_shortcode( array $atts = [] ): string {
        $atts = shortcode_atts( [ 'title' => 'Make a Reservation' ], $atts );
        ob_start();
        include THR_PLUGIN_DIR . 'templates/booking-form.php';
        return ob_get_clean();
    }

    public function render_events_shortcode( array $atts = [] ): string {
        $atts = shortcode_atts( [], $atts );
        ob_start();
        include THR_PLUGIN_DIR . 'templates/events-enquiry-form.php';
        return ob_get_clean();
    }

    public function register_page_template(): void {
        // Nothing to register — template is identified by page slug
    }

    public function load_page_template( string $template ): string {
        if ( $this->is_widget_page() ) {
            $custom = THR_PLUGIN_DIR . 'templates/booking-widget.php';
            if ( file_exists( $custom ) ) return $custom;
        }
        if ( is_page( 'cancel' ) ) {
            $custom = THR_PLUGIN_DIR . 'templates/cancel-page.php';
            if ( file_exists( $custom ) ) return $custom;
        }
        return $template;
    }

    public function handle_cancel_page(): void {
        // Handled by cancel-page.php template via load_page_template()
    }

    private function is_booking_context(): bool {
        if ( $this->is_widget_page() || is_page( 'cancel' ) ) return true;
        $post = get_post();
        if ( ! $post ) return false;
        return has_shortcode( $post->post_content, 'th_booking_form' )
            || has_shortcode( $post->post_content, 'th_events_enquiry' );
    }

    private function is_widget_page(): bool {
        return is_page( 'reserve' );
    }
}
