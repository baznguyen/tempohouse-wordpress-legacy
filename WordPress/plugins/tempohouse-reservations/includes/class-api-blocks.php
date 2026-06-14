<?php
defined( 'ABSPATH' ) || exit;

/**
 * CRUD for availability_blocks — block the entire venue, a floor, or specific furniture
 * for arbitrary date/time windows (private events, maintenance, closures, etc.)
 */
class THR_API_Blocks {

    private string $table;

    public function __construct() {
        $this->table = THR_Database::t( 'availability_blocks' );
    }

    public function register(): void {
        $ns = THR_REST_NS;

        register_rest_route( $ns, '/blocks', [
            [ 'methods' => 'GET',  'callback' => [ $this, 'list' ],   'permission_callback' => fn() => current_user_can( 'thr_view_reservations' ) ],
            [ 'methods' => 'POST', 'callback' => [ $this, 'create' ], 'permission_callback' => fn() => current_user_can( 'thr_manage_settings' ) ],
        ] );

        register_rest_route( $ns, '/blocks/(?P<id>\d+)', [
            [ 'methods' => 'GET',    'callback' => [ $this, 'get_one' ], 'permission_callback' => fn() => current_user_can( 'thr_view_reservations' ) ],
            [ 'methods' => 'PATCH',  'callback' => [ $this, 'update' ],  'permission_callback' => fn() => current_user_can( 'thr_manage_settings' ) ],
            [ 'methods' => 'DELETE', 'callback' => [ $this, 'delete' ],  'permission_callback' => fn() => current_user_can( 'thr_manage_settings' ) ],
        ] );
    }

    // ── GET /blocks ───────────────────────────────────────────────────────────
    public function list( WP_REST_Request $req ): WP_REST_Response {
        global $wpdb;
        $from  = sanitize_text_field( $req->get_param( 'from' ) ?: '' );
        $to    = sanitize_text_field( $req->get_param( 'to' )   ?: '' );
        $scope = sanitize_text_field( $req->get_param( 'scope' ) ?: '' );

        $where  = [ '1=1' ];
        $values = [];

        if ( $from )  { $where[] = 'blocked_to > %s';   $values[] = $from . ' 00:00:00'; }
        if ( $to )    { $where[] = 'blocked_from < %s'; $values[] = $to   . ' 23:59:59'; }
        if ( $scope ) { $where[] = 'scope = %s';        $values[] = $scope; }

        $sql = "SELECT * FROM {$this->table} WHERE " . implode( ' AND ', $where ) . " ORDER BY blocked_from ASC LIMIT 200";
        $rows = $values
            ? $wpdb->get_results( $wpdb->prepare( $sql, $values ) )
            : $wpdb->get_results( $sql );

        return new WP_REST_Response( array_map( [ $this, 'format' ], $rows ) );
    }

    // ── POST /blocks ──────────────────────────────────────────────────────────
    public function create( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        global $wpdb;
        $body = $req->get_json_params() ?? [];
        $err  = $this->validate( $body );
        if ( $err ) return $err;

        $now = THR_API::now_utc();
        $wpdb->insert( $this->table, [
            'scope'        => sanitize_text_field( $body['scope'] ),
            'scope_id'     => isset( $body['scope_id'] ) ? (int) $body['scope_id'] : null,
            'blocked_from' => sanitize_text_field( $body['blocked_from'] ),
            'blocked_to'   => sanitize_text_field( $body['blocked_to'] ),
            'reason'       => sanitize_text_field( $body['reason'] ?? '' ),
            'created_by'   => get_current_user_id(),
            'created_at'   => $now,
        ] );
        $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->table} WHERE id = %d", $wpdb->insert_id ) );
        return new WP_REST_Response( $this->format( $row ), 201 );
    }

    // ── GET /blocks/{id} ──────────────────────────────────────────────────────
    public function get_one( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        $row = $this->find( (int) $req->get_param( 'id' ) );
        if ( ! $row ) return THR_API::error( 'thr_not_found', 'Block not found.', 404 );
        return new WP_REST_Response( $this->format( $row ) );
    }

    // ── PATCH /blocks/{id} ────────────────────────────────────────────────────
    public function update( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        global $wpdb;
        $row = $this->find( (int) $req->get_param( 'id' ) );
        if ( ! $row ) return THR_API::error( 'thr_not_found', 'Block not found.', 404 );

        $body    = $req->get_json_params() ?? [];
        $allowed = [ 'scope', 'scope_id', 'blocked_from', 'blocked_to', 'reason' ];
        $update  = array_intersect_key( $body, array_flip( $allowed ) );
        if ( isset( $update['scope'] ) )        $update['scope']        = sanitize_text_field( $update['scope'] );
        if ( isset( $update['scope_id'] ) )     $update['scope_id']     = (int) $update['scope_id'];
        if ( isset( $update['blocked_from'] ) ) $update['blocked_from'] = sanitize_text_field( $update['blocked_from'] );
        if ( isset( $update['blocked_to'] ) )   $update['blocked_to']   = sanitize_text_field( $update['blocked_to'] );
        if ( isset( $update['reason'] ) )       $update['reason']       = sanitize_text_field( $update['reason'] );

        $wpdb->update( $this->table, $update, [ 'id' => $row->id ] );
        return new WP_REST_Response( $this->format( $this->find( $row->id ) ) );
    }

    // ── DELETE /blocks/{id} ───────────────────────────────────────────────────
    public function delete( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        global $wpdb;
        $row = $this->find( (int) $req->get_param( 'id' ) );
        if ( ! $row ) return THR_API::error( 'thr_not_found', 'Block not found.', 404 );
        $wpdb->delete( $this->table, [ 'id' => $row->id ] );
        return new WP_REST_Response( null, 204 );
    }

    // ── Helpers ───────────────────────────────────────────────────────────────
    private function find( int $id ): ?object {
        global $wpdb;
        return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->table} WHERE id = %d", $id ) ) ?: null;
    }

    private function validate( array $body ): ?WP_Error {
        $valid_scopes = [ 'venue', 'floor', 'furniture' ];
        $scope = sanitize_text_field( $body['scope'] ?? '' );
        if ( ! in_array( $scope, $valid_scopes, true ) ) {
            return THR_API::error( 'thr_validation', 'Scope must be venue, floor, or furniture.', 422 );
        }
        if ( in_array( $scope, [ 'floor', 'furniture' ], true ) && empty( $body['scope_id'] ) ) {
            return THR_API::error( 'thr_validation', 'scope_id is required for floor and furniture blocks.', 422 );
        }
        if ( empty( $body['blocked_from'] ) || empty( $body['blocked_to'] ) ) {
            return THR_API::error( 'thr_validation', 'blocked_from and blocked_to are required.', 422 );
        }
        if ( strtotime( $body['blocked_from'] ) >= strtotime( $body['blocked_to'] ) ) {
            return THR_API::error( 'thr_validation', 'blocked_to must be after blocked_from.', 422 );
        }
        return null;
    }

    private function format( object $row ): array {
        return [
            'id'           => (int) $row->id,
            'scope'        => $row->scope,
            'scope_id'     => $row->scope_id ? (int) $row->scope_id : null,
            'blocked_from' => $row->blocked_from,
            'blocked_to'   => $row->blocked_to,
            'reason'       => $row->reason,
            'created_by'   => $row->created_by ? (int) $row->created_by : null,
            'created_at'   => $row->created_at,
        ];
    }
}
