<?php
defined( 'ABSPATH' ) || exit;

class THR_API_Tags {

    private string $table;

    public function __construct() {
        $this->table = THR_Database::t( 'tags' );
    }

    public function register(): void {
        $ns = THR_REST_NS;

        register_rest_route( $ns, '/tags', [
            [ 'methods' => 'GET',  'callback' => [ $this, 'list' ],   'permission_callback' => fn() => current_user_can( 'thr_view_reservations' ) ],
            [ 'methods' => 'POST', 'callback' => [ $this, 'create' ], 'permission_callback' => fn() => current_user_can( 'thr_manage_tags' ) ],
        ] );

        register_rest_route( $ns, '/tags/(?P<id>\d+)', [
            [ 'methods' => 'PATCH',  'callback' => [ $this, 'update' ], 'permission_callback' => fn() => current_user_can( 'thr_manage_tags' ) ],
            [ 'methods' => 'DELETE', 'callback' => [ $this, 'delete' ], 'permission_callback' => fn() => current_user_can( 'thr_manage_tags' ) ],
        ] );
    }

    public function list(): WP_REST_Response {
        global $wpdb;
        $rows = $wpdb->get_results( "SELECT * FROM {$this->table} ORDER BY is_system DESC, name ASC" );
        return new WP_REST_Response( array_map( [ $this, 'format' ], $rows ) );
    }

    public function create( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        global $wpdb;
        $body  = $req->get_json_params() ?? [];
        $name  = sanitize_text_field( $body['name'] ?? '' );
        if ( ! $name ) return THR_API::error( 'thr_validation', 'Tag name is required.', 422 );

        $slug = sanitize_title( $name );
        if ( $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$this->table} WHERE slug = %s", $slug ) ) ) {
            return THR_API::error( 'thr_duplicate', 'A tag with this name already exists.', 409 );
        }

        $wpdb->insert( $this->table, [
            'name'       => $name,
            'slug'       => $slug,
            'color'      => sanitize_hex_color( $body['color'] ?? '#666666' ) ?: '#666666',
            'is_system'  => 0,
            'created_at' => THR_API::now_utc(),
        ] );
        $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->table} WHERE id = %d", $wpdb->insert_id ) );
        return new WP_REST_Response( $this->format( $row ), 201 );
    }

    public function update( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        global $wpdb;
        $id  = (int) $req->get_param( 'id' );
        $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->table} WHERE id = %d", $id ) );
        if ( ! $row ) return THR_API::error( 'thr_not_found', 'Tag not found.', 404 );

        $body   = $req->get_json_params() ?? [];
        $update = [];
        if ( isset( $body['name'] ) )  $update['name']  = sanitize_text_field( $body['name'] );
        if ( isset( $body['color'] ) ) $update['color'] = sanitize_hex_color( $body['color'] ) ?: $row->color;

        if ( $update ) $wpdb->update( $this->table, $update, [ 'id' => $id ] );
        return new WP_REST_Response( $this->format( $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->table} WHERE id = %d", $id ) ) ) );
    }

    public function delete( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        global $wpdb;
        $id  = (int) $req->get_param( 'id' );
        $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->table} WHERE id = %d", $id ) );
        if ( ! $row ) return THR_API::error( 'thr_not_found', 'Tag not found.', 404 );
        if ( $row->is_system ) return THR_API::error( 'thr_forbidden', 'System tags cannot be deleted.', 403 );

        $wpdb->delete( THR_Database::t( 'reservation_tags' ), [ 'tag_id' => $id ] );
        $wpdb->delete( $this->table, [ 'id' => $id ] );
        return new WP_REST_Response( null, 204 );
    }

    private function format( object $row ): array {
        return [
            'id'         => (int)  $row->id,
            'name'       => $row->name,
            'slug'       => $row->slug,
            'color'      => $row->color,
            'is_system'  => (bool) $row->is_system,
            'created_at' => $row->created_at,
        ];
    }
}
