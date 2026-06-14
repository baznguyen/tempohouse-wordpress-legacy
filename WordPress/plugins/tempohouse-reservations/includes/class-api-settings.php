<?php
defined( 'ABSPATH' ) || exit;

class THR_API_Settings {

    public function register(): void {
        $ns = THR_REST_NS;

        register_rest_route( $ns, '/settings', [
            [ 'methods' => 'GET',   'callback' => [ $this, 'get' ],    'permission_callback' => fn() => current_user_can( 'thr_manage_settings' ) ],
            [ 'methods' => 'PATCH', 'callback' => [ $this, 'update' ], 'permission_callback' => fn() => current_user_can( 'thr_manage_settings' ) ],
        ] );

        // Public endpoint — returns only settings needed by the booking widget
        register_rest_route( $ns, '/public/config', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'public_config' ],
            'permission_callback' => '__return_true',
        ] );
    }

    public function get(): WP_REST_Response {
        return new WP_REST_Response( THR_Settings::all() );
    }

    public function update( WP_REST_Request $req ): WP_REST_Response {
        $body = $req->get_json_params() ?? [];
        THR_Settings::update( $body );
        return new WP_REST_Response( THR_Settings::all() );
    }

    public function public_config(): WP_REST_Response {
        return new WP_REST_Response( [
            'venue_name'         => THR_Settings::get( 'venue_name' ),
            'party_size_min'     => (int) THR_Settings::get( 'party_size_min' ),
            'party_size_max'     => (int) THR_Settings::get( 'party_size_max' ),
            'booking_advance_min'=> (int) THR_Settings::get( 'booking_advance_min' ),
            'booking_advance_max'=> (int) THR_Settings::get( 'booking_advance_max' ),
            'occasion_types'     => THR_Settings::occasion_types(),
            'cancel_policy_text' => THR_Settings::get( 'cancel_policy_text' ),
        ] );
    }
}
