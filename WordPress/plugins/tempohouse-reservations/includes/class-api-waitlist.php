<?php
defined( 'ABSPATH' ) || exit;

/**
 * Waitlist — guests join when no slots are available for their requested date.
 * Admin can notify them when a cancellation opens a slot, or convert to a reservation.
 */
class THR_API_Waitlist {

    private string $table;

    public function __construct() {
        $this->table = THR_Database::t( 'waitlist' );
    }

    public function register(): void {
        $ns = THR_REST_NS;

        // Admin endpoints
        register_rest_route( $ns, '/waitlist', [
            [ 'methods' => 'GET', 'callback' => [ $this, 'list' ], 'permission_callback' => fn() => current_user_can( 'thr_view_reservations' ) ],
        ] );
        register_rest_route( $ns, '/waitlist/(?P<id>\d+)', [
            [ 'methods' => 'GET',    'callback' => [ $this, 'get_one' ], 'permission_callback' => fn() => current_user_can( 'thr_view_reservations' ) ],
            [ 'methods' => 'PATCH',  'callback' => [ $this, 'update' ],  'permission_callback' => fn() => current_user_can( 'thr_edit_reservations' ) ],
            [ 'methods' => 'DELETE', 'callback' => [ $this, 'delete' ],  'permission_callback' => fn() => current_user_can( 'thr_edit_reservations' ) ],
        ] );
        register_rest_route( $ns, '/waitlist/(?P<id>\d+)/notify', [
            'methods' => 'POST', 'callback' => [ $this, 'notify' ],
            'permission_callback' => fn() => current_user_can( 'thr_edit_reservations' ),
        ] );
        register_rest_route( $ns, '/waitlist/(?P<id>\d+)/convert', [
            'methods' => 'POST', 'callback' => [ $this, 'convert_to_reservation' ],
            'permission_callback' => fn() => current_user_can( 'thr_create_reservations' ),
        ] );

        // Public — no auth
        register_rest_route( $ns, '/public/waitlist', [
            'methods' => 'POST', 'callback' => [ $this, 'public_join' ], 'permission_callback' => '__return_true',
        ] );
    }

    // ── GET /waitlist ─────────────────────────────────────────────────────────
    public function list( WP_REST_Request $req ): WP_REST_Response {
        global $wpdb;
        $status = sanitize_text_field( $req->get_param( 'status' ) ?: '' );
        $date   = sanitize_text_field( $req->get_param( 'date' )   ?: '' );

        $where  = [ '1=1' ];
        $values = [];
        if ( $status ) { $where[] = 'status = %s'; $values[] = $status; }
        if ( $date )   { $where[] = 'requested_date = %s'; $values[] = $date; }

        $sql  = "SELECT * FROM {$this->table} WHERE " . implode( ' AND ', $where ) . " ORDER BY requested_date ASC, created_at ASC LIMIT 200";
        $rows = $values ? $wpdb->get_results( $wpdb->prepare( $sql, $values ) ) : $wpdb->get_results( $sql );

        return new WP_REST_Response( array_map( [ $this, 'format' ], $rows ) );
    }

    // ── GET /waitlist/{id} ────────────────────────────────────────────────────
    public function get_one( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        $row = $this->find( (int) $req->get_param( 'id' ) );
        if ( ! $row ) return THR_API::error( 'thr_not_found', 'Waitlist entry not found.', 404 );
        return new WP_REST_Response( $this->format( $row ) );
    }

    // ── PATCH /waitlist/{id} ──────────────────────────────────────────────────
    public function update( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        global $wpdb;
        $row  = $this->find( (int) $req->get_param( 'id' ) );
        if ( ! $row ) return THR_API::error( 'thr_not_found', 'Waitlist entry not found.', 404 );

        $body    = $req->get_json_params() ?? [];
        $allowed = [ 'status', 'notes_diner' ];
        $update  = array_intersect_key( $body, array_flip( $allowed ) );
        $update['updated_at'] = THR_API::now_utc();

        $wpdb->update( $this->table, $update, [ 'id' => $row->id ] );
        return new WP_REST_Response( $this->format( $this->find( $row->id ) ) );
    }

    // ── DELETE /waitlist/{id} ─────────────────────────────────────────────────
    public function delete( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        global $wpdb;
        $row = $this->find( (int) $req->get_param( 'id' ) );
        if ( ! $row ) return THR_API::error( 'thr_not_found', 'Waitlist entry not found.', 404 );
        $wpdb->delete( $this->table, [ 'id' => $row->id ] );
        return new WP_REST_Response( null, 204 );
    }

    // ── POST /waitlist/{id}/notify ────────────────────────────────────────────
    public function notify( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        global $wpdb;
        $row = $this->find( (int) $req->get_param( 'id' ) );
        if ( ! $row ) return THR_API::error( 'thr_not_found', 'Waitlist entry not found.', 404 );
        if ( $row->status !== 'waiting' ) {
            return THR_API::error( 'thr_invalid_state', 'Can only notify entries in waiting status.', 409 );
        }

        $now = THR_API::now_utc();
        THR_Email::send_waitlist_notification( $row );
        $wpdb->update( $this->table, [
            'status'               => 'notified',
            'notification_sent_at' => $now,
            'updated_at'           => $now,
        ], [ 'id' => $row->id ] );

        return new WP_REST_Response( $this->format( $this->find( $row->id ) ) );
    }

    // ── POST /waitlist/{id}/convert ───────────────────────────────────────────
    // Turns a waitlist entry into a confirmed reservation
    public function convert_to_reservation( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        global $wpdb;
        $row  = $this->find( (int) $req->get_param( 'id' ) );
        if ( ! $row ) return THR_API::error( 'thr_not_found', 'Waitlist entry not found.', 404 );

        $body = $req->get_json_params() ?? [];
        $time = sanitize_text_field( $body['time'] ?? $row->requested_time ?? '' );
        if ( ! $time ) return THR_API::error( 'thr_validation', 'A specific time is required to convert.', 422 );

        // Build a UTC datetime from the requested date + time (GMT+7)
        $dt_utc = gmdate( 'Y-m-d H:i:s', strtotime( $row->requested_date . ' ' . $time . ':00 +0700' ) );

        $res_table = THR_Database::t( 'reservations' );
        $ref       = $this->generate_reference();
        $now       = THR_API::now_utc();

        $wpdb->insert( $res_table, [
            'reference_code' => $ref,
            'status'         => 'confirmed',
            'reservation_dt' => $dt_utc,
            'duration_min'   => (int) THR_Settings::get( 'default_duration', 120 ),
            'party_size'     => (int) $row->party_size,
            'diner_name'     => $row->diner_name,
            'diner_email'    => $row->diner_email,
            'diner_phone'    => $row->diner_phone,
            'diner_lang'     => $row->diner_lang,
            'occasion'       => $row->occasion,
            'notes_diner'    => $row->notes_diner,
            'deposit_amount' => 0.00,
            'deposit_paid'   => 0,
            'created_by'     => get_current_user_id(),
            'created_at'     => $now,
            'updated_at'     => $now,
        ] );
        $res_id      = $wpdb->insert_id;
        $reservation = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $res_table WHERE id = %d", $res_id ) );

        THR_Email::send_confirmation( $reservation );

        // Mark waitlist entry as converted
        $wpdb->update( $this->table, [
            'status'     => 'converted',
            'updated_at' => $now,
        ], [ 'id' => $row->id ] );

        return new WP_REST_Response( [
            'reservation_id'   => $res_id,
            'reference_code'   => $ref,
            'waitlist_status'  => 'converted',
        ], 201 );
    }

    // ── POST /public/waitlist ─────────────────────────────────────────────────
    public function public_join( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        global $wpdb;
        $body  = $req->get_json_params() ?? $req->get_body_params();
        $email = sanitize_email( $body['diner_email'] ?? '' );

        // Rate limit — max 2 waitlist joins per email per venue-local day (GMT+7)
        $venue_today = gmdate( 'Y-m-d', time() + 7 * 3600 );
        $today_start = gmdate( 'Y-m-d H:i:s', strtotime( $venue_today . ' 00:00:00 +0700' ) ); // UTC equiv of midnight GMT+7
        $existing = (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$this->table}
             WHERE diner_email = %s AND created_at >= %s",
            $email, $today_start
        ) );
        if ( $existing >= 2 ) {
            return THR_API::error( 'thr_rate_limited', 'You are already on the waitlist for today.', 429 );
        }

        // Validate
        $name  = sanitize_text_field( $body['diner_name'] ?? '' );
        $date  = sanitize_text_field( $body['requested_date'] ?? '' );
        $size  = (int) ( $body['party_size'] ?? 0 );

        if ( ! $name )             return THR_API::error( 'thr_validation', 'Name is required.', 422 );
        if ( ! is_email( $email ) ) return THR_API::error( 'thr_validation', 'Valid email is required.', 422 );
        if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) ) return THR_API::error( 'thr_validation', 'Date must be YYYY-MM-DD.', 422 );
        if ( $size < 1 )           return THR_API::error( 'thr_validation', 'Party size must be at least 1.', 422 );
        if ( strtotime( $date ) < strtotime( 'today' ) ) return THR_API::error( 'thr_validation', 'Date must be today or in the future.', 422 );

        $occasion_types = array_keys( THR_Settings::occasion_types() );
        $occasion       = sanitize_text_field( $body['occasion'] ?? 'dinner' );
        if ( ! in_array( $occasion, $occasion_types, true ) ) $occasion = 'dinner';

        $now = THR_API::now_utc();
        $ref = $this->generate_reference();

        $wpdb->insert( $this->table, [
            'reference_code' => $ref,
            'status'         => 'waiting',
            'requested_date' => $date,
            'requested_time' => sanitize_text_field( $body['requested_time'] ?? '' ) ?: null,
            'party_size'     => $size,
            'diner_name'     => $name,
            'diner_email'    => $email,
            'diner_phone'    => sanitize_text_field( $body['diner_phone'] ?? '' ),
            'diner_lang'     => in_array( $body['diner_lang'] ?? '', [ 'en', 'vi' ], true ) ? $body['diner_lang'] : 'en',
            'occasion'       => $occasion,
            'notes_diner'    => sanitize_textarea_field( $body['notes_diner'] ?? '' ),
            'created_at'     => $now,
            'updated_at'     => $now,
        ] );

        $entry = $this->find( $wpdb->insert_id );
        THR_Email::send_waitlist_joined( $entry );
        $wpdb->update( $this->table, [ 'joined_email_sent_at' => $now ], [ 'id' => $entry->id ] );

        return new WP_REST_Response( [
            'reference_code' => $ref,
            'message'        => "You're on the waitlist. We'll contact you if a table opens up.",
        ], 201 );
    }

    // ── Helpers ───────────────────────────────────────────────────────────────
    private function find( int $id ): ?object {
        global $wpdb;
        return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->table} WHERE id = %d", $id ) ) ?: null;
    }

    private function format( object $row ): array {
        return [
            'id'                   => (int) $row->id,
            'reference_code'       => $row->reference_code,
            'status'               => $row->status,
            'requested_date'       => $row->requested_date,
            'requested_time'       => $row->requested_time,
            'party_size'           => (int) $row->party_size,
            'diner_name'           => $row->diner_name,
            'diner_email'          => $row->diner_email,
            'diner_phone'          => $row->diner_phone,
            'diner_lang'           => $row->diner_lang,
            'occasion'             => $row->occasion,
            'notes_diner'          => $row->notes_diner,
            'joined_email_sent_at' => $row->joined_email_sent_at,
            'notification_sent_at' => $row->notification_sent_at,
            'created_at'           => $row->created_at,
            'updated_at'           => $row->updated_at,
        ];
    }

    private function generate_reference(): string {
        global $wpdb;
        do {
            $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
            $code  = 'WL-';
            for ( $i = 0; $i < 6; $i++ ) $code .= $chars[ random_int( 0, strlen( $chars ) - 1 ) ];
            $exists = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$this->table} WHERE reference_code = %s", $code ) );
        } while ( $exists );
        return $code;
    }
}
