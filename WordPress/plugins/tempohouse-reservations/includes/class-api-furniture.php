<?php
defined( 'ABSPATH' ) || exit;

class THR_API_Furniture {

    private string $table;

    // All supported furniture types for the floor plan builder
    const TYPES = [
        // ── Simplified toolbar types (v6+ editor) ────────────────────────
        'table-round'      => [ 'label' => 'Round Table',          'cap' => [1,12], 'shape' => 'circle', 'joinable' => false ],
        'table-square'     => [ 'label' => 'Square Table',         'cap' => [1,4],  'shape' => 'rect',   'joinable' => true  ],
        'table-rect'       => [ 'label' => 'Rectangle Table',      'cap' => [1,10], 'shape' => 'rect',   'joinable' => true  ],
        'lounge'           => [ 'label' => 'Lounge',               'cap' => [2,8],  'shape' => 'rect',   'joinable' => false ],
        'bar-seat'         => [ 'label' => 'Bar Seat',             'cap' => [1,1],  'shape' => 'circle', 'joinable' => false ],
        'bar-table'        => [ 'label' => 'Bar Table',            'cap' => [1,6],  'shape' => 'rect',   'joinable' => false ],
        'zone'             => [ 'label' => 'Zone',                 'cap' => [0,0],  'shape' => 'rect',   'joinable' => false ],
        // ── Legacy types (backwards compat) ──────────────────────────────
        'table-round-2'    => [ 'label' => 'Round Table (2-top)',  'cap' => [1,2],  'shape' => 'circle', 'joinable' => false ],
        'table-round-4'    => [ 'label' => 'Round Table (4-top)',  'cap' => [2,4],  'shape' => 'circle', 'joinable' => false ],
        'table-round-6'    => [ 'label' => 'Round Table (6-top)',  'cap' => [4,6],  'shape' => 'circle', 'joinable' => false ],
        'table-round-8'    => [ 'label' => 'Round Table (8-top)',  'cap' => [6,8],  'shape' => 'circle', 'joinable' => false ],
        'table-rect-2'     => [ 'label' => 'Table (2-top)',        'cap' => [1,2],  'shape' => 'rect',   'joinable' => true  ],
        'table-rect-4'     => [ 'label' => 'Table (4-top)',        'cap' => [2,4],  'shape' => 'rect',   'joinable' => true  ],
        'table-rect-6'     => [ 'label' => 'Table (6-top)',        'cap' => [4,6],  'shape' => 'rect',   'joinable' => true  ],
        'table-rect-8'     => [ 'label' => 'Table (8-top)',        'cap' => [6,8],  'shape' => 'rect',   'joinable' => true  ],
        'booth-2'          => [ 'label' => 'Booth (2-top)',        'cap' => [1,2],  'shape' => 'rect',   'joinable' => true  ],
        'booth-4'          => [ 'label' => 'Booth (4-top)',        'cap' => [2,4],  'shape' => 'rect',   'joinable' => true  ],
        'booth-6'          => [ 'label' => 'Booth (6-top)',        'cap' => [4,6],  'shape' => 'rect',   'joinable' => true  ],
        'bar-stool'        => [ 'label' => 'Bar Stool',            'cap' => [1,1],  'shape' => 'circle', 'joinable' => false ],
        'bar-counter'      => [ 'label' => 'Bar Counter (section)','cap' => [1,4],  'shape' => 'rect',   'joinable' => false ],
        'high-top-2'       => [ 'label' => 'High-Top (2-top)',     'cap' => [1,2],  'shape' => 'circle', 'joinable' => false ],
        'high-top-4'       => [ 'label' => 'High-Top (4-top)',     'cap' => [2,4],  'shape' => 'circle', 'joinable' => false ],
        'lounge-sofa'      => [ 'label' => 'Lounge Sofa',          'cap' => [2,4],  'shape' => 'rect',   'joinable' => false ],
        'lounge-chair'     => [ 'label' => 'Lounge Chair',         'cap' => [1,2],  'shape' => 'rect',   'joinable' => false ],
        'banquette'        => [ 'label' => 'Banquette',            'cap' => [2,8],  'shape' => 'rect',   'joinable' => true  ],
        'outdoor-table'    => [ 'label' => 'Outdoor Table',        'cap' => [2,6],  'shape' => 'rect',   'joinable' => true  ],
        'stage'            => [ 'label' => 'Stage / Platform',     'cap' => [0,0],  'shape' => 'rect',   'joinable' => false ],
        'dj-booth'         => [ 'label' => 'DJ Booth',             'cap' => [0,0],  'shape' => 'rect',   'joinable' => false ],
        'area-vip'         => [ 'label' => 'VIP Area',             'cap' => [2,20], 'shape' => 'rect',   'joinable' => false ],
        'text_label'       => [ 'label' => 'Text Label',           'cap' => [0,0],  'shape' => 'rect',   'joinable' => false ],
    ];

    public function __construct() {
        $this->table = THR_Database::t( 'furniture' );
    }

    public function register(): void {
        $ns = THR_REST_NS;

        register_rest_route( $ns, '/floor-plans/(?P<floor_id>\d+)/furniture', [
            [ 'methods' => 'GET',  'callback' => [ $this, 'list' ],   'permission_callback' => fn() => current_user_can( 'thr_view_reservations' ) ],
            [ 'methods' => 'POST', 'callback' => [ $this, 'create' ], 'permission_callback' => fn() => current_user_can( 'thr_manage_floor_plans' ) ],
        ] );

        register_rest_route( $ns, '/furniture/(?P<id>\d+)', [
            [ 'methods' => 'PATCH',  'callback' => [ $this, 'update' ], 'permission_callback' => fn() => current_user_can( 'thr_manage_floor_plans' ) ],
            [ 'methods' => 'DELETE', 'callback' => [ $this, 'delete' ], 'permission_callback' => fn() => current_user_can( 'thr_manage_floor_plans' ) ],
        ] );

        // Return all supported furniture type definitions
        register_rest_route( $ns, '/furniture/types', [
            'methods' => 'GET', 'callback' => fn() => new WP_REST_Response( self::TYPES ),
            'permission_callback' => fn() => current_user_can( 'thr_view_reservations' ),
        ] );
    }

    public function list( WP_REST_Request $req ): WP_REST_Response {
        global $wpdb;
        $floor_id = (int) $req->get_param( 'floor_id' );
        $rows = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$this->table} WHERE floor_plan_id = %d ORDER BY id ASC", $floor_id
        ) );
        return new WP_REST_Response( array_map( [ $this, 'format' ], $rows ) );
    }

    public function create( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        global $wpdb;
        $body     = $req->get_json_params() ?? [];
        $floor_id = (int) $req->get_param( 'floor_id' );
        $type     = sanitize_text_field( $body['type'] ?? '' );

        if ( ! isset( self::TYPES[ $type ] ) ) {
            return THR_API::error( 'thr_invalid_type', 'Unknown furniture type.', 422 );
        }
        $defaults = self::TYPES[ $type ];
        $now      = THR_API::now_utc();

        $wpdb->insert( $this->table, [
            'floor_plan_id' => $floor_id,
            'type'          => $type,
            'label'         => sanitize_text_field( $body['label'] ?? $defaults['label'] ),
            'pos_x'         => (float) ( $body['pos_x'] ?? 100 ),
            'pos_y'         => (float) ( $body['pos_y'] ?? 100 ),
            'width'         => (float) ( $body['width'] ?? 80 ),
            'height'        => (float) ( $body['height'] ?? 80 ),
            'rotation_deg'  => (int)   ( $body['rotation_deg'] ?? 0 ),
            'capacity_min'  => (int)   ( $body['capacity_min'] ?? $defaults['cap'][0] ),
            'capacity_max'  => (int)   ( $body['capacity_max'] ?? $defaults['cap'][1] ),
            'shape'         => $defaults['shape'],
            'is_combinable' => (int)   ( $body['is_combinable'] ?? 1 ),
            'is_available'  => 1,
            'meta'          => isset( $body['meta'] ) ? json_encode( $body['meta'] ) : null,
            'created_at'    => $now,
            'updated_at'    => $now,
        ] );
        $new_id = $wpdb->insert_id;
        // Auto-generate element_key based on type and new ID
        if ( empty( $body['element_key'] ) ) {
            $prefix = ( strpos( $type, 'bar' ) === 0 || $type === 'bar-stool' ) ? 'B' : ( $type === 'zone' ? 'Z' : 'T' );
            $wpdb->update( $this->table, [ 'element_key' => $prefix . $new_id ], [ 'id' => $new_id ] );
        }
        // get fresh row after update
        $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->table} WHERE id = %d", $new_id ) );
        return new WP_REST_Response( $this->format( $row ), 201 );
    }

    public function update( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        global $wpdb;
        $id  = (int) $req->get_param( 'id' );
        $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->table} WHERE id = %d", $id ) );
        if ( ! $row ) return THR_API::error( 'thr_not_found', 'Furniture not found.', 404 );

        $body    = $req->get_json_params() ?? [];
        $allowed = [ 'label', 'pos_x', 'pos_y', 'width', 'height', 'rotation_deg',
                     'capacity_min', 'capacity_max', 'is_combinable', 'is_available', 'meta',
                     'element_key', 'group_id' ];
        $update  = array_intersect_key( $body, array_flip( $allowed ) );
        if ( isset( $update['label'] ) ) $update['label'] = sanitize_text_field( $update['label'] );
        if ( isset( $update['element_key'] ) ) $update['element_key'] = sanitize_text_field( $update['element_key'] );
        if ( array_key_exists( 'group_id', $update ) ) $update['group_id'] = is_null( $update['group_id'] ) ? null : (int) $update['group_id'];
        if ( isset( $update['meta'] ) && is_array( $update['meta'] ) ) $update['meta'] = json_encode( $update['meta'] );
        $update['updated_at'] = THR_API::now_utc();
        $wpdb->update( $this->table, $update, [ 'id' => $id ] );
        return new WP_REST_Response( $this->format( $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->table} WHERE id = %d", $id ) ) ) );
    }

    public function delete( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        global $wpdb;
        $id = (int) $req->get_param( 'id' );
        if ( ! $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$this->table} WHERE id = %d", $id ) ) ) {
            return THR_API::error( 'thr_not_found', 'Furniture not found.', 404 );
        }
        $wpdb->delete( $this->table, [ 'id' => $id ] );
        return new WP_REST_Response( null, 204 );
    }

    private function format( object $row ): array {
        return [
            'id'            => (int)   $row->id,
            'floor_plan_id' => (int)   $row->floor_plan_id,
            'type'          => $row->type,
            'type_label'    => self::TYPES[ $row->type ]['label'] ?? $row->type,
            'label'         => $row->label,
            'pos_x'         => (float) $row->pos_x,
            'pos_y'         => (float) $row->pos_y,
            'width'         => (float) $row->width,
            'height'        => (float) $row->height,
            'rotation_deg'  => (int)   $row->rotation_deg,
            'capacity_min'  => (int)   $row->capacity_min,
            'capacity_max'  => (int)   $row->capacity_max,
            'shape'         => $row->shape,
            'is_combinable' => (bool)  $row->is_combinable,
            'is_available'  => (bool)  $row->is_available,
            'meta'          => $row->meta ? json_decode( $row->meta, true ) : null,
            'element_key'   => $row->element_key ?? null,
            'group_id'      => $row->group_id ? (int) $row->group_id : null,
            'joinable'      => self::TYPES[ $row->type ]['joinable'] ?? false,
            'created_at'    => $row->created_at,
            'updated_at'    => $row->updated_at,
        ];
    }
}
