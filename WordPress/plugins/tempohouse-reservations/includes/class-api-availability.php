<?php
defined( 'ABSPATH' ) || exit;

/**
 * Availability endpoints:
 *   GET /availability              — bookable slots for a specific date + party size (with optional period filter)
 *   GET /public/available-dates    — per-date open/full status for a calendar month
 *   GET /public/alternatives       — nearby alternative slots when requested slot is unavailable
 */
class THR_API_Availability {

    public function register(): void {
        $ns = THR_REST_NS;

        register_rest_route( $ns, '/availability', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'get_slots' ],
            'permission_callback' => '__return_true',
            'args'                => [
                'date'       => [ 'required' => true,  'sanitize_callback' => 'sanitize_text_field' ],
                'party_size' => [ 'required' => true,  'sanitize_callback' => 'absint' ],
                'period'     => [ 'required' => false, 'sanitize_callback' => 'sanitize_text_field' ],
            ],
        ] );

        register_rest_route( $ns, '/public/available-dates', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'available_dates' ],
            'permission_callback' => '__return_true',
            'args'                => [
                'year'       => [ 'required' => true,  'sanitize_callback' => 'absint' ],
                'month'      => [ 'required' => true,  'sanitize_callback' => 'absint' ],
                'party_size' => [ 'required' => true,  'sanitize_callback' => 'absint' ],
                'period'     => [ 'required' => false, 'sanitize_callback' => 'sanitize_text_field' ],
            ],
        ] );

        register_rest_route( $ns, '/public/alternatives', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'get_alternatives' ],
            'permission_callback' => '__return_true',
            'args'                => [
                'date'       => [ 'required' => true,  'sanitize_callback' => 'sanitize_text_field' ],
                'time'       => [ 'required' => true,  'sanitize_callback' => 'sanitize_text_field' ],
                'party_size' => [ 'required' => true,  'sanitize_callback' => 'absint' ],
                'period'     => [ 'required' => false, 'sanitize_callback' => 'sanitize_text_field' ],
            ],
        ] );
    }

    // ── GET /availability ─────────────────────────────────────────────────────
    public function get_slots( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        $date       = $req->get_param( 'date' );
        $party_size = (int) $req->get_param( 'party_size' );
        $period     = sanitize_text_field( $req->get_param( 'period' ) ?? '' );

        if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) ) {
            return THR_API::error( 'thr_invalid_date', 'Date must be YYYY-MM-DD.', 422 );
        }

        $slots       = $period ? THR_Settings::slots_for_period( $period ) : THR_Settings::time_slots();
        $duration    = (int) THR_Settings::get( 'default_duration', 120 );
        $advance_min = (int) THR_Settings::get( 'booking_advance_min', 60 );
        $now_unix    = time() + 7 * 3600;

        $available = [];
        foreach ( $slots as $time ) {
            $slot_unix = strtotime( "$date $time:00 +0700" );
            if ( $slot_unix === false ) continue;
            if ( $slot_unix < $now_unix + $advance_min * 60 ) continue;
            if ( ! $this->slot_has_capacity( $date, $time, $duration, $party_size ) ) continue;
            $available[] = [ 'time' => $time, 'available' => true ];
        }

        if ( empty( $available ) ) {
            foreach ( $slots as $time ) {
                $slot_unix = strtotime( "$date $time:00 +0700" );
                if ( $slot_unix === false || $slot_unix < $now_unix + $advance_min * 60 ) continue;
                $available[] = [ 'time' => $time, 'available' => true ];
            }
        }

        return new WP_REST_Response( [ 'date' => $date, 'slots' => $available ] );
    }

    // ── GET /public/available-dates ───────────────────────────────────────────
    public function available_dates( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        $year       = (int) $req->get_param( 'year' );
        $month      = (int) $req->get_param( 'month' );
        $party_size = (int) $req->get_param( 'party_size' );
        $period     = sanitize_text_field( $req->get_param( 'period' ) ?? '' );

        if ( $year < 2020 || $year > 2100 || $month < 1 || $month > 12 ) {
            return THR_API::error( 'thr_invalid_date', 'Invalid year/month.', 422 );
        }

        $closed_days = array_filter( array_map( 'intval', explode( ',', THR_Settings::get( 'closed_days', '' ) ) ) );
        $days_in_month = (int) date( 't', mktime( 0, 0, 0, $month, 1, $year ) );
        $slots   = $period ? THR_Settings::slots_for_period( $period ) : THR_Settings::time_slots();
        $duration    = (int) THR_Settings::get( 'default_duration', 120 );
        $advance_min = (int) THR_Settings::get( 'booking_advance_min', 60 );
        $advance_max = (int) THR_Settings::get( 'booking_advance_max', 60 );
        $now_unix    = time() + 7 * 3600;
        $max_unix    = $now_unix + $advance_max * 86400;

        $dates = [];
        for ( $d = 1; $d <= $days_in_month; $d++ ) {
            $date     = sprintf( '%04d-%02d-%02d', $year, $month, $d );
            $day_ts   = mktime( 0, 0, 0, $month, $d, $year );
            $day_of_week = (int) date( 'w', $day_ts );

            // Closed day of week
            if ( in_array( $day_of_week, $closed_days, true ) ) {
                $dates[ $date ] = 'closed';
                continue;
            }

            // Past dates or beyond booking window
            $last_slot_unix = strtotime( "$date " . end( $slots ) . ":00 +0700" );
            if ( $last_slot_unix <= $now_unix + $advance_min * 60 ) {
                $dates[ $date ] = 'past';
                continue;
            }
            if ( $day_ts > $max_unix ) {
                $dates[ $date ] = 'unavailable';
                continue;
            }

            // Check if at least one slot has capacity
            $has_slot = false;
            foreach ( $slots as $time ) {
                $slot_unix = strtotime( "$date $time:00 +0700" );
                if ( $slot_unix === false || $slot_unix < $now_unix + $advance_min * 60 ) continue;
                if ( $this->slot_has_capacity( $date, $time, $duration, $party_size ) ) {
                    $has_slot = true;
                    break;
                }
            }
            $dates[ $date ] = $has_slot ? 'available' : 'full';
        }

        return new WP_REST_Response( [
            'year'  => $year,
            'month' => $month,
            'dates' => $dates,
        ] );
    }

    // ── GET /public/alternatives ──────────────────────────────────────────────
    public function get_alternatives( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        $date       = $req->get_param( 'date' );
        $time       = $req->get_param( 'time' );
        $party_size = (int) $req->get_param( 'party_size' );
        $period     = sanitize_text_field( $req->get_param( 'period' ) ?? '' );

        if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) ) {
            return THR_API::error( 'thr_invalid_date', 'Date must be YYYY-MM-DD.', 422 );
        }

        $duration    = (int) THR_Settings::get( 'default_duration', 120 );
        $advance_min = (int) THR_Settings::get( 'booking_advance_min', 60 );
        $advance_max = (int) THR_Settings::get( 'booking_advance_max', 60 );
        $now_unix    = time() + 7 * 3600;
        $max_unix    = $now_unix + $advance_max * 86400;
        $target_unix = strtotime( "$date $time:00 +0700" ) ?: $now_unix;
        $slots_base  = $period ? THR_Settings::slots_for_period( $period ) : THR_Settings::time_slots();

        $candidates = [];

        // Check same day first (different times), then ±4 days
        for ( $offset = 0; $offset <= 4; $offset++ ) {
            foreach ( [ 0, -$offset, $offset ] as $day_delta ) {
                if ( $day_delta === 0 && $offset > 0 ) continue; // already checked day 0 on first pass
                $check_ts = strtotime( "$date +$day_delta days" );
                if ( ! $check_ts || $check_ts > $max_unix ) continue;
                $check_date = date( 'Y-m-d', $check_ts );
                $closed_days = array_filter( array_map( 'intval', explode( ',', THR_Settings::get( 'closed_days', '' ) ) ) );
                if ( in_array( (int) date( 'w', $check_ts ), $closed_days, true ) ) continue;

                foreach ( $slots_base as $slot_time ) {
                    $slot_unix = strtotime( "$check_date $slot_time:00 +0700" );
                    if ( ! $slot_unix || $slot_unix < $now_unix + $advance_min * 60 ) continue;
                    if ( abs( $slot_unix - $target_unix ) < 60 ) continue; // skip the original slot
                    if ( ! $this->slot_has_capacity( $check_date, $slot_time, $duration, $party_size ) ) continue;

                    $candidates[] = [
                        'date'     => $check_date,
                        'time'     => $slot_time,
                        'distance' => abs( $slot_unix - $target_unix ),
                    ];
                    if ( count( $candidates ) >= 20 ) break 3; // enough to sort and take top 5
                }
            }
        }

        usort( $candidates, fn( $a, $b ) => $a['distance'] <=> $b['distance'] );
        $top = array_slice( $candidates, 0, 5 );

        return new WP_REST_Response( [
            'requested_date' => $date,
            'requested_time' => $time,
            'alternatives'   => array_map( fn( $c ) => [ 'date' => $c['date'], 'time' => $c['time'] ], $top ),
        ] );
    }

    // ── shared capacity check ─────────────────────────────────────────────────
    private function slot_has_capacity( string $date, string $time, int $duration_min, int $party_size ): bool {
        global $wpdb;

        $floors_table    = THR_Database::t( 'floor_plans' );
        $furniture_table = THR_Database::t( 'furniture' );
        $blocks_table    = THR_Database::t( 'availability_blocks' );
        $res_table       = THR_Database::t( 'reservations' );

        $floor_count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $floors_table WHERE is_active = 1" );
        if ( $floor_count === 0 ) return true;

        $slot_start_ts = strtotime( "$date $time:00 +0700" );
        $slot_end_ts   = $slot_start_ts + $duration_min * 60;
        $slot_start    = gmdate( 'Y-m-d H:i:s', $slot_start_ts );
        $slot_end      = gmdate( 'Y-m-d H:i:s', $slot_end_ts );

        $venue_blocked = (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM $blocks_table WHERE scope = 'venue' AND blocked_from < %s AND blocked_to > %s",
            $slot_end, $slot_start
        ) );
        if ( $venue_blocked > 0 ) return false;

        $blocked_floor_ids = $wpdb->get_col( $wpdb->prepare(
            "SELECT scope_id FROM $blocks_table WHERE scope = 'floor' AND scope_id IS NOT NULL AND blocked_from < %s AND blocked_to > %s",
            $slot_end, $slot_start
        ) );

        $blocked_furniture_ids = $wpdb->get_col( $wpdb->prepare(
            "SELECT scope_id FROM $blocks_table WHERE scope = 'furniture' AND scope_id IS NOT NULL AND blocked_from < %s AND blocked_to > %s",
            $slot_end, $slot_start
        ) );

        $booked_json_rows = $wpdb->get_col( $wpdb->prepare(
            "SELECT furniture_ids FROM $res_table
             WHERE furniture_ids IS NOT NULL
               AND status NOT IN ('cancelled','no_show','completed')
               AND reservation_dt < %s
               AND DATE_ADD(reservation_dt, INTERVAL duration_min MINUTE) > %s",
            $slot_end, $slot_start
        ) );

        $reservation_booked_ids = [];
        foreach ( $booked_json_rows as $json ) {
            $ids = json_decode( $json, true );
            if ( is_array( $ids ) ) foreach ( $ids as $fid ) $reservation_booked_ids[] = (int) $fid;
        }

        $all_blocked = array_unique( array_merge(
            array_map( 'intval', $blocked_furniture_ids ),
            $reservation_booked_ids
        ) );

        $furniture = $wpdb->get_results(
            "SELECT id, capacity_max, floor_plan_id FROM $furniture_table
             WHERE is_available = 1 AND capacity_max >= 1
               AND floor_plan_id IN (SELECT id FROM $floors_table WHERE is_active = 1)"
        );

        if ( empty( $furniture ) ) return false;

        $blocked_floors = array_map( 'intval', $blocked_floor_ids );
        $total_max = 0;
        foreach ( $furniture as $piece ) {
            if ( in_array( (int) $piece->id, $all_blocked, true ) ) continue;
            if ( in_array( (int) $piece->floor_plan_id, $blocked_floors, true ) ) continue;
            $total_max += (int) $piece->capacity_max;
            if ( $total_max >= $party_size ) return true;
        }

        return false;
    }
}
