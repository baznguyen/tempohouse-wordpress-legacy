<?php
defined( 'ABSPATH' ) || exit;

class THR_API_Events {

    private string $table;

    public function __construct() {
        $this->table = THR_Database::t( 'event_enquiries' );
    }

    public function register(): void {
        $ns = THR_REST_NS;

        register_rest_route( $ns, '/public/event-enquiry', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'submit' ],
            'permission_callback' => '__return_true',
        ] );

        register_rest_route( $ns, '/event-enquiries', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'list' ],
            'permission_callback' => fn() => current_user_can( 'thr_view_reservations' ),
        ] );

        register_rest_route( $ns, '/event-enquiries/(?P<id>\d+)/status', [
            'methods'             => 'PATCH',
            'callback'            => [ $this, 'update_status' ],
            'permission_callback' => fn() => current_user_can( 'thr_edit_reservations' ),
        ] );
    }

    // ── POST /public/event-enquiry (unauthenticated) ───────────────────────────
    public function submit( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        $body = $req->get_json_params() ?? $req->get_body_params();

        // Required field validation
        $contact_name  = sanitize_text_field( $body['contact_name'] ?? '' );
        $contact_email = sanitize_email( $body['contact_email'] ?? '' );
        $event_type    = sanitize_text_field( $body['event_type'] ?? 'corporate' );
        $guest_count   = (int) ( $body['guest_count'] ?? 0 );

        $errors = [];
        if ( ! $contact_name )          $errors[] = 'Contact name is required.';
        if ( ! is_email( $contact_email ) ) $errors[] = 'A valid email address is required.';
        if ( $guest_count < 1 )         $errors[] = 'Guest count must be at least 1.';

        if ( $errors ) {
            return new WP_Error( 'thr_validation', implode( ' ', $errors ), [ 'status' => 422 ] );
        }

        $allowed_types = [ 'corporate', 'product_launch', 'brand_activation', 'birthday', 'anniversary', 'team_event', 'other' ];
        if ( ! in_array( $event_type, $allowed_types, true ) ) $event_type = 'other';

        $preferred_date = sanitize_text_field( $body['preferred_date'] ?? '' );
        if ( $preferred_date && ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $preferred_date ) ) {
            $preferred_date = '';
        }

        $budget_range    = sanitize_text_field( $body['budget_range'] ?? '' );
        $catering_needed = ! empty( $body['catering_needed'] ) ? 1 : 0;
        $contact_phone   = sanitize_text_field( $body['contact_phone'] ?? '' );
        $contact_zalo    = sanitize_text_field( $body['contact_zalo'] ?? '' );
        $company_name    = sanitize_text_field( $body['company_name'] ?? '' );
        $notes           = sanitize_textarea_field( $body['notes'] ?? '' );

        $now           = current_time( 'mysql', true );
        $ref           = $this->generate_reference();

        global $wpdb;
        $result = $wpdb->insert( $this->table, [
            'reference_code'  => $ref,
            'status'          => 'new',
            'event_type'      => $event_type,
            'preferred_date'  => $preferred_date ?: null,
            'guest_count'     => $guest_count,
            'budget_range'    => $budget_range ?: null,
            'catering_needed' => $catering_needed,
            'contact_name'    => $contact_name,
            'contact_email'   => $contact_email,
            'contact_phone'   => $contact_phone ?: null,
            'contact_zalo'    => $contact_zalo ?: null,
            'company_name'    => $company_name ?: null,
            'notes'           => $notes ?: null,
            'created_at'      => $now,
            'updated_at'      => $now,
        ] );

        if ( ! $result ) {
            return THR_API::error( 'thr_insert_failed', 'Failed to submit enquiry. Please try again.', 500 );
        }

        $id      = $wpdb->insert_id;
        $enquiry = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->table} WHERE id = %d", $id ) );

        // Send auto-reply and venue notification
        $sent = THR_Email::send_event_enquiry_auto_reply( $enquiry );

        // Update auto_reply_sent_at if email sent
        if ( $sent ) {
            $wpdb->update( $this->table, [ 'auto_reply_sent_at' => $now ], [ 'id' => $id ] );
        }

        THR_Email::send_event_enquiry_notification( $enquiry );

        return new WP_REST_Response( [
            'reference_code' => $ref,
            'message'        => 'Thank you for your enquiry. We will be in touch within 24 hours.',
        ], 201 );
    }

    // ── GET /event-enquiries ──────────────────────────────────────────────────
    public function list( WP_REST_Request $req ): WP_REST_Response {
        global $wpdb;

        $status = sanitize_text_field( $req->get_param( 'status' ) ?? '' );
        $where  = $status ? $wpdb->prepare( 'WHERE status = %s', $status ) : '';

        $rows = $wpdb->get_results(
            "SELECT * FROM {$this->table} $where ORDER BY created_at DESC LIMIT 200"
        );

        return new WP_REST_Response( [
            'data'  => array_map( [ $this, 'format_row' ], $rows ),
            'total' => count( $rows ),
        ] );
    }

    // ── PATCH /event-enquiries/{id}/status ────────────────────────────────────
    public function update_status( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        global $wpdb;

        $id  = (int) $req->get_param( 'id' );
        $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->table} WHERE id = %d", $id ) );
        if ( ! $row ) {
            return THR_API::error( 'thr_not_found', 'Enquiry not found.', 404 );
        }

        $new_status = sanitize_text_field( $req->get_param( 'status' ) );
        $valid      = [ 'new', 'reviewing', 'quoted', 'confirmed', 'declined', 'closed' ];
        if ( ! in_array( $new_status, $valid, true ) ) {
            return THR_API::error( 'thr_invalid_status', 'Invalid status value.', 422 );
        }

        $wpdb->update(
            $this->table,
            [ 'status' => $new_status, 'updated_at' => current_time( 'mysql', true ) ],
            [ 'id' => $id ]
        );

        return new WP_REST_Response( $this->format_row( $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->table} WHERE id = %d", $id ) ) ) );
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function format_row( object $row ): array {
        return [
            'id'               => (int) $row->id,
            'reference_code'   => $row->reference_code,
            'status'           => $row->status,
            'event_type'       => $row->event_type,
            'preferred_date'   => $row->preferred_date,
            'guest_count'      => (int) $row->guest_count,
            'budget_range'     => $row->budget_range,
            'catering_needed'  => (bool) $row->catering_needed,
            'contact_name'     => $row->contact_name,
            'contact_email'    => $row->contact_email,
            'contact_phone'    => $row->contact_phone,
            'contact_zalo'     => $row->contact_zalo,
            'company_name'     => $row->company_name,
            'notes'            => $row->notes,
            'auto_reply_sent_at' => $row->auto_reply_sent_at,
            'created_at'       => $row->created_at,
            'updated_at'       => $row->updated_at,
        ];
    }

    private function generate_reference(): string {
        global $wpdb;
        do {
            $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
            $code  = 'EV-';
            for ( $i = 0; $i < 6; $i++ ) $code .= $chars[ random_int( 0, strlen( $chars ) - 1 ) ];
            $exists = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$this->table} WHERE reference_code = %s", $code ) );
        } while ( $exists );
        return $code;
    }
}
