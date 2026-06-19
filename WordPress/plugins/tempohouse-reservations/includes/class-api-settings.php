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
            'venue_name'              => THR_Settings::get( 'venue_name' ),
            'party_size_min'          => (int) THR_Settings::get( 'party_size_min' ),
            'party_size_max'          => (int) THR_Settings::get( 'party_size_max' ),
            'private_room_min_party'  => (int) THR_Settings::get( 'private_room_min_party', 12 ),
            'private_room_max_party'  => (int) THR_Settings::get( 'private_room_max_party', 15 ),
            'booking_advance_min'     => (int) THR_Settings::get( 'booking_advance_min' ),
            'booking_advance_max'     => (int) THR_Settings::get( 'booking_advance_max' ),
            'slot_interval_min'       => (int) THR_Settings::get( 'slot_interval_min', 30 ),
            'default_duration'        => (int) THR_Settings::get( 'default_duration', 120 ),
            'closed_days'             => array_values( array_filter( array_map( 'intval', explode( ',', THR_Settings::get( 'closed_days', '' ) ) ) ) ),
            'occasion_types'          => THR_Settings::occasion_types(),
            'seating_sections'        => THR_Settings::seating_sections(),
            'periods'                 => THR_Settings::periods(),
            'referral_sources'        => THR_Settings::referral_sources(),
            'cancel_policy_text'      => THR_Settings::get( 'cancel_policy_text' ),
        ] );
    }
}
