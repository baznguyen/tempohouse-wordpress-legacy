<?php
defined( 'ABSPATH' ) || exit;

class THR_API_Reservations {

    private string $table;

    public function __construct() {
        $this->table = THR_Database::t( 'reservations' );
    }

    public function register(): void {
        $ns = THR_REST_NS;

        // Admin-authenticated endpoints
        register_rest_route( $ns, '/reservations', [
            [ 'methods' => 'GET',  'callback' => [ $this, 'list' ],   'permission_callback' => fn() => current_user_can( 'thr_view_reservations' ) ],
            [ 'methods' => 'POST', 'callback' => [ $this, 'create' ], 'permission_callback' => fn() => current_user_can( 'thr_create_reservations' ) ],
        ] );

        register_rest_route( $ns, '/reservations/(?P<id>\d+)', [
            [ 'methods' => 'GET',    'callback' => [ $this, 'get_one' ], 'permission_callback' => fn() => current_user_can( 'thr_view_reservations' ) ],
            [ 'methods' => 'PATCH',  'callback' => [ $this, 'update' ],  'permission_callback' => fn() => current_user_can( 'thr_edit_reservations' ) ],
            [ 'methods' => 'DELETE', 'callback' => [ $this, 'delete' ],  'permission_callback' => fn() => current_user_can( 'thr_delete_reservations' ) ],
        ] );

        register_rest_route( $ns, '/reservations/(?P<id>\d+)/status', [
            'methods' => 'PATCH', 'callback' => [ $this, 'update_status' ],
            'permission_callback' => fn() => current_user_can( 'thr_edit_reservations' ),
        ] );

        // Public — no auth required
        register_rest_route( $ns, '/public/booking', [
            [ 'methods' => 'POST',   'callback' => [ $this, 'public_create' ], 'permission_callback' => '__return_true' ],
        ] );

        // Guest self-cancel and lookup
        register_rest_route( $ns, '/public/cancel', [
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'public_cancel_lookup' ],
                'permission_callback' => '__return_true',
            ],
            [
                'methods'             => 'POST',
                'callback'            => [ $this, 'public_cancel' ],
                'permission_callback' => '__return_true',
            ],
        ] );
    }

    // ── GET /reservations ─────────────────────────────────────────────────────
    public function list( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        global $wpdb;

        $page     = max( 1, (int) $req->get_param( 'page' ) ?: 1 );
        $per_page = min( 100, max( 10, (int) $req->get_param( 'per_page' ) ?: 25 ) );
        $offset   = ( $page - 1 ) * $per_page;

        $where  = [ '1=1' ];
        $values = [];

        if ( $status = $req->get_param( 'status' ) ) {
            $where[]  = 'status = %s';
            $values[] = $status;
        }
        if ( $date = $req->get_param( 'date' ) ) {
            $where[]  = 'DATE(DATE_ADD(reservation_dt, INTERVAL 7 HOUR)) = %s';
            $values[] = $date;
        }
        if ( $search = $req->get_param( 'search' ) ) {
            $where[]  = '(diner_name LIKE %s OR diner_email LIKE %s OR reference_code = %s)';
            $values[] = "%{$search}%";
            $values[] = "%{$search}%";
            $values[] = strtoupper( $search );
        }
        if ( $from = $req->get_param( 'from' ) ) {
            $where[]  = 'reservation_dt >= %s';
            $values[] = $from . ' 00:00:00';
        }
        if ( $to = $req->get_param( 'to' ) ) {
            $where[]  = 'reservation_dt <= %s';
            $values[] = $to . ' 23:59:59';
        }

        $where_sql  = implode( ' AND ', $where );
        $count_sql  = "SELECT COUNT(*) FROM {$this->table} WHERE $where_sql";
        $select_sql = "SELECT * FROM {$this->table} WHERE $where_sql ORDER BY reservation_dt ASC LIMIT %d OFFSET %d";

        if ( $values ) {
            $count_sql  = $wpdb->prepare( $count_sql, $values );
            $values_paged = array_merge( $values, [ $per_page, $offset ] );
            $select_sql = $wpdb->prepare( $select_sql, $values_paged );
        } else {
            $select_sql = $wpdb->prepare( $select_sql, $per_page, $offset );
        }

        $total = (int) $wpdb->get_var( $count_sql );
        $rows  = $wpdb->get_results( $select_sql );

        return new WP_REST_Response( [
            'data'       => array_map( [ $this, 'format_row' ], $rows ),
            'total'      => $total,
            'page'       => $page,
            'per_page'   => $per_page,
            'total_pages'=> (int) ceil( $total / $per_page ),
        ] );
    }

    // ── POST /reservations (admin) ────────────────────────────────────────────
    public function create( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        $data = $this->validate_reservation_data( $req->get_json_params() ?? $req->get_body_params() );
        if ( is_wp_error( $data ) ) return $data;

        $id = $this->insert_reservation( $data, get_current_user_id() );
        if ( ! $id ) return THR_API::error( 'thr_insert_failed', 'Failed to create reservation.', 500 );

        $reservation = $this->find( $id );

        // Auto-confirm if created by admin/manager
        if ( current_user_can( 'thr_edit_reservations' ) && empty( $data['status'] ) ) {
            $this->transition_status( $reservation, 'confirmed' );
            $reservation = $this->find( $id );
        }

        return new WP_REST_Response( $this->format_row( $reservation ), 201 );
    }

    // ── GET /reservations/{id} ────────────────────────────────────────────────
    public function get_one( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        $row = $this->find( (int) $req->get_param( 'id' ) );
        if ( ! $row ) return THR_API::error( 'thr_not_found', 'Reservation not found.', 404 );
        return new WP_REST_Response( $this->format_row( $row ) );
    }

    // ── PATCH /reservations/{id} ──────────────────────────────────────────────
    public function update( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        global $wpdb;
        $row = $this->find( (int) $req->get_param( 'id' ) );
        if ( ! $row ) return THR_API::error( 'thr_not_found', 'Reservation not found.', 404 );

        $body    = $req->get_json_params() ?? [];
        $allowed = [ 'diner_name', 'diner_email', 'diner_phone', 'diner_zalo', 'party_size', 'reservation_dt',
                     'duration_min', 'occasion', 'notes_diner', 'notes_internal', 'is_vip',
                     'area_label', 'furniture_ids', 'floor_plan_id', 'diner_lang' ];
        $update  = array_intersect_key( $body, array_flip( $allowed ) );

        if ( isset( $update['furniture_ids'] ) && is_array( $update['furniture_ids'] ) ) {
            $update['furniture_ids'] = json_encode( $update['furniture_ids'] );
        }

        $update['updated_at'] = THR_API::now_utc();
        $wpdb->update( $this->table, $update, [ 'id' => $row->id ] );

        // Handle tag assignment
        if ( isset( $body['tag_ids'] ) && is_array( $body['tag_ids'] ) ) {
            $this->sync_tags( $row->id, array_map( 'intval', $body['tag_ids'] ) );
        }

        return new WP_REST_Response( $this->format_row( $this->find( $row->id ) ) );
    }

    // ── DELETE /reservations/{id} ─────────────────────────────────────────────
    public function delete( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        global $wpdb;
        $row = $this->find( (int) $req->get_param( 'id' ) );
        if ( ! $row ) return THR_API::error( 'thr_not_found', 'Reservation not found.', 404 );

        $wpdb->delete( THR_Database::t( 'reservation_tags' ), [ 'reservation_id' => $row->id ] );
        $wpdb->delete( $this->table, [ 'id' => $row->id ] );

        return new WP_REST_Response( null, 204 );
    }

    // ── PATCH /reservations/{id}/status ──────────────────────────────────────
    public function update_status( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        $row = $this->find( (int) $req->get_param( 'id' ) );
        if ( ! $row ) return THR_API::error( 'thr_not_found', 'Reservation not found.', 404 );

        $new_status = sanitize_text_field( $req->get_param( 'status' ) );
        $valid      = [ 'pending', 'confirmed', 'seated', 'completed', 'cancelled', 'no_show', 'late' ];
        if ( ! in_array( $new_status, $valid, true ) ) {
            return THR_API::error( 'thr_invalid_status', 'Invalid status value.', 422 );
        }

        // Permission check for cancel
        if ( $new_status === 'cancelled' && ! current_user_can( 'thr_cancel_reservations' ) ) {
            return THR_API::error( 'thr_forbidden', 'You cannot cancel reservations.', 403 );
        }

        $this->transition_status( $row, $new_status );
        return new WP_REST_Response( $this->format_row( $this->find( $row->id ) ) );
    }

    // ── POST /public/booking (unauthenticated) ────────────────────────────────
    public function public_create( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        // Rate limit: max 3 bookings per email per hour
        $body  = $req->get_json_params() ?? $req->get_body_params();
        $email = sanitize_email( $body['diner_email'] ?? '' );
        if ( $email && $this->rate_limit_exceeded( $email ) ) {
            return THR_API::error( 'thr_rate_limited', 'Too many booking attempts. Please try again later.', 429 );
        }

        $data = $this->validate_reservation_data( $body, true );
        if ( is_wp_error( $data ) ) return $data;

        // Capacity guard: check overlapping confirmed reservations
        $max_capacity = (int) THR_Settings::get( 'venue_capacity', 60 );
        if ( $max_capacity > 0 ) {
            $booked = $this->get_booked_covers( $data['reservation_dt'], (int) THR_Settings::get( 'default_duration', 120 ) );
            if ( $booked + (int) $data['party_size'] > $max_capacity ) {
                return THR_API::error( 'thr_no_capacity', 'Sorry, we are fully booked for this time.', 409 );
            }
        }

        // Public bookings always start as pending
        $data['status'] = 'pending';

        $id = $this->insert_reservation( $data, null );
        if ( ! $id ) return THR_API::error( 'thr_insert_failed', 'Booking failed. Please try again.', 500 );

        $reservation = $this->find( $id );

        // Auto-confirm for simplicity (can make manual for busy periods via settings)
        if ( THR_Settings::get( 'auto_confirm_public', true ) ) {
            $this->transition_status( $reservation, 'confirmed' );
            $reservation = $this->find( $id );
        } else {
            // Still send a "pending" acknowledgement email
            THR_Email::send_booking_pending( $reservation );
        }

        return new WP_REST_Response( [
            'reference_code' => $reservation->reference_code,
            'status'         => $reservation->status,
            'message'        => 'Your reservation has been received. Please check your email for confirmation.',
        ], 201 );
    }

    // ── GET /public/cancel?ref=TH-XXXXXX ─────────────────────────────────────
    public function public_cancel_lookup( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        global $wpdb;
        $ref = strtoupper( sanitize_text_field( $req->get_param( 'ref' ) ?? '' ) );
        if ( ! $ref ) return THR_API::error( 'thr_missing_ref', 'Reference code is required.', 422 );

        $row = $wpdb->get_row( $wpdb->prepare(
            "SELECT *, DATE_ADD(reservation_dt, INTERVAL 7 HOUR) AS dt_local
             FROM {$this->table} WHERE reference_code = %s", $ref
        ) );

        if ( ! $row ) return THR_API::error( 'thr_not_found', 'Reservation not found.', 404 );
        if ( in_array( $row->status, [ 'cancelled', 'completed', 'no_show' ], true ) ) {
            return THR_API::error( 'thr_invalid_state', 'This reservation cannot be cancelled.', 409 );
        }

        $ts = strtotime( $row->dt_local );
        return new WP_REST_Response( [
            'reference_code' => $row->reference_code,
            'diner_name'     => $row->diner_name,
            'date_local'     => date( 'l, F j, Y', $ts ),
            'time_local'     => date( 'g:ia', $ts ),
            'party_size'     => (int) $row->party_size . ' guest' . ( $row->party_size > 1 ? 's' : '' ),
            'status'         => ucfirst( $row->status ),
        ] );
    }

    // ── POST /public/cancel ───────────────────────────────────────────────────
    public function public_cancel( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        global $wpdb;
        $body = $req->get_json_params() ?? [];
        $ref  = strtoupper( sanitize_text_field( $body['reference_code'] ?? '' ) );
        if ( ! $ref ) return THR_API::error( 'thr_missing_ref', 'Reference code is required.', 422 );

        $row = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$this->table} WHERE reference_code = %s", $ref
        ) );

        if ( ! $row ) return THR_API::error( 'thr_not_found', 'Reservation not found.', 404 );
        if ( in_array( $row->status, [ 'cancelled', 'completed', 'no_show' ], true ) ) {
            return THR_API::error( 'thr_invalid_state', 'This reservation cannot be cancelled.', 409 );
        }

        $this->transition_status( $row, 'cancelled' );
        return new WP_REST_Response( [ 'message' => 'Your reservation has been cancelled.' ] );
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function validate_reservation_data( array $body, bool $public = false ): array|WP_Error {
        $errors = [];

        $name  = sanitize_text_field( $body['diner_name'] ?? '' );
        $email = sanitize_email( $body['diner_email'] ?? '' );
        $dt    = sanitize_text_field( $body['reservation_dt'] ?? '' );
        $size  = (int) ( $body['party_size'] ?? 0 );

        if ( ! $name )              $errors[] = 'Guest name is required.';
        if ( ! is_email( $email ) ) $errors[] = 'A valid email address is required.';
        if ( ! $dt )                $errors[] = 'Reservation date/time is required.';
        if ( $size < 1 )            $errors[] = 'Party size must be at least 1.';

        $max_size = (int) THR_Settings::get( 'party_size_max', 20 );
        if ( $size > $max_size )    $errors[] = "Maximum party size is {$max_size}.";

        if ( $errors ) {
            return new WP_Error( 'thr_validation', implode( ' ', $errors ), [ 'status' => 422 ] );
        }

        // For public bookings, enforce advance booking window
        if ( $public ) {
            $advance_min = (int) THR_Settings::get( 'booking_advance_min', 60 );
            $advance_max = (int) THR_Settings::get( 'booking_advance_max', 60 );
            $booking_ts  = strtotime( $dt );
            $now_ts      = time();

            if ( $booking_ts < $now_ts + $advance_min * 60 ) {
                return THR_API::error( 'thr_too_soon', "Reservations must be made at least {$advance_min} minutes in advance.", 422 );
            }
            if ( $booking_ts > $now_ts + $advance_max * 86400 ) {
                return THR_API::error( 'thr_too_far', "Reservations can only be made up to {$advance_max} days in advance.", 422 );
            }
        }

        $occasion_types = array_keys( THR_Settings::occasion_types() );
        $occasion       = sanitize_text_field( $body['occasion'] ?? 'dinner' );
        if ( ! in_array( $occasion, $occasion_types, true ) ) $occasion = 'dinner';

        return [
            'diner_name'   => $name,
            'diner_email'  => $email,
            'diner_phone'  => sanitize_text_field( $body['diner_phone'] ?? '' ),
            'diner_zalo'   => sanitize_text_field( $body['diner_zalo'] ?? '' ) ?: null,
            'diner_lang'   => in_array( $body['diner_lang'] ?? '', [ 'en', 'vi' ], true ) ? $body['diner_lang'] : 'en',
            'reservation_dt' => $dt,
            'party_size'   => $size,
            'duration_min' => (int) THR_Settings::get( 'default_duration', 120 ),
            'occasion'     => $occasion,
            'notes_diner'  => sanitize_textarea_field( $body['notes_diner'] ?? '' ),
            'notes_internal' => current_user_can( 'thr_edit_reservations' )
                               ? sanitize_textarea_field( $body['notes_internal'] ?? '' )
                               : '',
            'is_vip'       => current_user_can( 'thr_edit_reservations' ) ? (int) ( $body['is_vip'] ?? 0 ) : 0,
            'floor_plan_id'  => isset( $body['floor_plan_id'] ) ? (int) $body['floor_plan_id'] : null,
            'furniture_ids'  => isset( $body['furniture_ids'] ) && is_array( $body['furniture_ids'] )
                               ? json_encode( array_map( 'intval', $body['furniture_ids'] ) )
                               : null,
            'area_label'   => sanitize_text_field( $body['area_label'] ?? '' ),
        ];
    }

    private function insert_reservation( array $data, ?int $created_by ): int|false {
        global $wpdb;
        $now     = THR_API::now_utc();
        $ref     = $this->generate_reference();
        $payload = array_merge( $data, [
            'reference_code' => $ref,
            'status'         => $data['status'] ?? 'pending',
            'deposit_amount' => 0.00,
            'deposit_paid'   => 0,
            'created_by'     => $created_by,
            'created_at'     => $now,
            'updated_at'     => $now,
        ] );
        $result = $wpdb->insert( $this->table, $payload );
        return $result ? $wpdb->insert_id : false;
    }

    private function transition_status( object $reservation, string $new_status ): void {
        global $wpdb;
        $update = [ 'status' => $new_status, 'updated_at' => THR_API::now_utc() ];
        if ( $new_status === 'seated' ) $update['seated_at'] = THR_API::now_utc();
        $wpdb->update( $this->table, $update, [ 'id' => $reservation->id ] );

        // Trigger email
        $fresh = $this->find( $reservation->id );
        match ( $new_status ) {
            'confirmed' => THR_Email::send_confirmation( $fresh ),
            'cancelled' => THR_Email::send_cancellation( $fresh ),
            default     => null,
        };

        do_action( 'thr_reservation_status_changed', $fresh, $reservation->status, $new_status );
    }

    private function sync_tags( int $reservation_id, array $tag_ids ): void {
        global $wpdb;
        $pivot = THR_Database::t( 'reservation_tags' );
        $wpdb->delete( $pivot, [ 'reservation_id' => $reservation_id ] );
        foreach ( $tag_ids as $tag_id ) {
            $wpdb->insert( $pivot, [ 'reservation_id' => $reservation_id, 'tag_id' => $tag_id ] );
        }
    }

    private function find( int $id ): ?object {
        global $wpdb;
        return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->table} WHERE id = %d", $id ) ) ?: null;
    }

    private function format_row( object $row ): array {
        global $wpdb;
        $pivot = THR_Database::t( 'reservation_tags' );
        $tags_table = THR_Database::t( 'tags' );

        $tags = $wpdb->get_results( $wpdb->prepare(
            "SELECT t.* FROM $tags_table t
             JOIN $pivot rt ON rt.tag_id = t.id
             WHERE rt.reservation_id = %d", $row->id
        ) );

        // Convert UTC reservation_dt to venue local (GMT+7) — pure PHP, no extra DB query
        $local_dt = gmdate( 'Y-m-d H:i:s', strtotime( $row->reservation_dt . ' UTC' ) + 7 * 3600 );

        $ids = $row->furniture_ids ? json_decode( $row->furniture_ids, true ) : [];

        return [
            'id'               => (int) $row->id,
            'reference_code'   => $row->reference_code,
            'status'           => $row->status,
            'reservation_dt'   => $row->reservation_dt,  // UTC
            'reservation_dt_local' => $local_dt,          // GMT+7
            'duration_min'     => (int) $row->duration_min,
            'party_size'       => (int) $row->party_size,
            'floor_plan_id'    => $row->floor_plan_id ? (int) $row->floor_plan_id : null,
            'furniture_ids'    => $ids,
            'area_label'       => $row->area_label,
            'diner_name'       => $row->diner_name,
            'diner_email'      => $row->diner_email,
            'diner_phone'      => $row->diner_phone,
            'diner_zalo'       => $row->diner_zalo,
            'diner_lang'       => $row->diner_lang,
            'occasion'         => $row->occasion,
            'notes_diner'      => $row->notes_diner,
            'notes_internal'   => $row->notes_internal,
            'is_vip'           => (bool) $row->is_vip,
            'deposit_amount'   => (float) $row->deposit_amount,
            'deposit_paid'     => (bool) $row->deposit_paid,
            'seated_at'        => $row->seated_at,
            'created_by'       => $row->created_by ? (int) $row->created_by : null,
            'created_at'       => $row->created_at,
            'updated_at'       => $row->updated_at,
            'tags'             => $tags,
            'is_returning'     => $this->is_returning_guest( $row->diner_email, (int) $row->id ),
        ];
    }

    private function get_booked_covers( string $dt_utc, int $duration_min ): int {
        global $wpdb;
        $start = sanitize_text_field( $dt_utc );
        $end   = gmdate( 'Y-m-d H:i:s', strtotime( $dt_utc . ' UTC' ) + $duration_min * 60 );
        return (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COALESCE(SUM(party_size),0) FROM {$this->table}
             WHERE status IN ('confirmed','seated','pending')
               AND reservation_dt < %s
               AND DATE_ADD(reservation_dt, INTERVAL duration_min MINUTE) > %s",
            $end, $start
        ) );
    }

    private function is_returning_guest( string $email, int $current_id ): bool {
        global $wpdb;
        return (bool) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$this->table}
             WHERE diner_email = %s AND id != %d AND status IN ('confirmed','completed','seated')",
            $email, $current_id
        ) );
    }

    private function generate_reference(): string {
        global $wpdb;
        do {
            $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
            $code  = 'TH-';
            for ( $i = 0; $i < 6; $i++ ) $code .= $chars[ random_int( 0, strlen( $chars ) - 1 ) ];
            $exists = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$this->table} WHERE reference_code = %s", $code ) );
        } while ( $exists );
        return $code;
    }

    private function rate_limit_exceeded( string $email ): bool {
        global $wpdb;
        $count = (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$this->table}
             WHERE diner_email = %s AND created_at > DATE_SUB(UTC_TIMESTAMP(), INTERVAL 1 HOUR)",
            $email
        ) );
        return $count >= 3;
    }
}
