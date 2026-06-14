<?php
defined( 'ABSPATH' ) || exit;

/**
 * Availability endpoint — returns bookable time slots for a given date and party size.
 * Used by the public booking widget to populate the time picker.
 */
class THR_API_Availability {

    public function register(): void {
        register_rest_route( THR_REST_NS, '/availability', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'get_slots' ],
            'permission_callback' => '__return_true',
            'args'                => [
                'date'       => [ 'required' => true, 'sanitize_callback' => 'sanitize_text_field' ],
                'party_size' => [ 'required' => true, 'sanitize_callback' => 'absint' ],
            ],
        ] );
    }

    public function get_slots( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        $date       = $req->get_param( 'date' );  // YYYY-MM-DD in venue local time
        $party_size = (int) $req->get_param( 'party_size' );

        if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) ) {
            return THR_API::error( 'thr_invalid_date', 'Date must be YYYY-MM-DD.', 422 );
        }

        $slots       = THR_Settings::time_slots();
        $duration    = (int) THR_Settings::get( 'default_duration', 120 );
        $advance_min = (int) THR_Settings::get( 'booking_advance_min', 60 );
        $now_unix    = time() + 7 * 3600; // Approximate "now" in GMT+7

        $available_slots = [];

        foreach ( $slots as $time ) {
            $slot_unix = strtotime( "$date $time:00 +0700" );
            if ( $slot_unix === false ) continue;

            // Must be far enough in the future
            if ( $slot_unix < $now_unix + $advance_min * 60 ) continue;

            // Check if there's capacity at this slot (any furniture combination can fit party_size)
            $has_capacity = $this->slot_has_capacity( $date, $time, $duration, $party_size );
            if ( ! $has_capacity ) continue;

            $available_slots[] = [
                'time'      => $time,
                'available' => true,
            ];
        }

        // If no floor plans are configured yet, return all future slots as available
        // This ensures the booking widget works before the floor plan is set up
        if ( empty( $available_slots ) ) {
            foreach ( $slots as $time ) {
                $slot_unix = strtotime( "$date $time:00 +0700" );
                if ( $slot_unix === false || $slot_unix < $now_unix + $advance_min * 60 ) continue;
                $available_slots[] = [ 'time' => $time, 'available' => true ];
            }
        }

        return new WP_REST_Response( [
            'date'   => $date,
            'slots'  => $available_slots,
        ] );
    }

    private function slot_has_capacity( string $date, string $time, int $duration_min, int $party_size ): bool {
        global $wpdb;

        $floors_table    = THR_Database::t( 'floor_plans' );
        $furniture_table = THR_Database::t( 'furniture' );
        $blocks_table    = THR_Database::t( 'availability_blocks' );
        $res_table       = THR_Database::t( 'reservations' );

        // If no active floor plan exists, skip capacity check
        $floor_count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $floors_table WHERE is_active = 1" );
        if ( $floor_count === 0 ) return true;

        // Slot window in UTC
        $slot_start_ts = strtotime( "$date $time:00 +0700" );
        $slot_end_ts   = $slot_start_ts + $duration_min * 60;
        $slot_start    = gmdate( 'Y-m-d H:i:s', $slot_start_ts );
        $slot_end      = gmdate( 'Y-m-d H:i:s', $slot_end_ts );

        // If the whole venue is blocked, return false immediately
        $venue_blocked = (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM $blocks_table
             WHERE scope = 'venue'
               AND blocked_from < %s AND blocked_to > %s",
            $slot_end, $slot_start
        ) );
        if ( $venue_blocked > 0 ) return false;

        // IDs of floors that are blocked for this slot
        $blocked_floor_ids = $wpdb->get_col( $wpdb->prepare(
            "SELECT scope_id FROM $blocks_table
             WHERE scope = 'floor' AND scope_id IS NOT NULL
               AND blocked_from < %s AND blocked_to > %s",
            $slot_end, $slot_start
        ) );

        // IDs of individual furniture pieces blocked for this slot
        $blocked_furniture_ids = $wpdb->get_col( $wpdb->prepare(
            "SELECT scope_id FROM $blocks_table
             WHERE scope = 'furniture' AND scope_id IS NOT NULL
               AND blocked_from < %s AND blocked_to > %s",
            $slot_end, $slot_start
        ) );

        // Fetch furniture_ids JSON strings for overlapping reservations (MySQL 5.7-safe — no JSON_TABLE)
        $booked_json_rows = $wpdb->get_col( $wpdb->prepare(
            "SELECT furniture_ids FROM $res_table
             WHERE furniture_ids IS NOT NULL
               AND status NOT IN ('cancelled','no_show','completed')
               AND reservation_dt < %s
               AND DATE_ADD(reservation_dt, INTERVAL duration_min MINUTE) > %s",
            $slot_end, $slot_start
        ) );

        // PHP-side flatten: decode each JSON array and union all booked furniture IDs
        $reservation_booked_ids = [];
        foreach ( $booked_json_rows as $json ) {
            $ids = json_decode( $json, true );
            if ( is_array( $ids ) ) {
                foreach ( $ids as $fid ) $reservation_booked_ids[] = (int) $fid;
            }
        }

        $all_blocked_furniture = array_unique( array_merge(
            array_map( 'intval', $blocked_furniture_ids ),
            $reservation_booked_ids
        ) );

        // Fetch available furniture not on a blocked floor and not in blocked set
        $furniture = $wpdb->get_results(
            "SELECT id, capacity_max, floor_plan_id FROM $furniture_table
             WHERE is_available = 1
               AND capacity_max >= 1
               AND floor_plan_id IN (SELECT id FROM $floors_table WHERE is_active = 1)"
        );

        if ( empty( $furniture ) ) return false;

        $blocked_floor_set = array_map( 'intval', $blocked_floor_ids );

        $total_max = 0;
        foreach ( $furniture as $piece ) {
            if ( in_array( (int) $piece->id, $all_blocked_furniture, true ) ) continue;
            if ( in_array( (int) $piece->floor_plan_id, $blocked_floor_set, true ) ) continue;
            $total_max += (int) $piece->capacity_max;
            if ( $total_max >= $party_size ) return true;
        }

        return false;
    }
}
