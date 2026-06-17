<?php
defined( 'ABSPATH' ) || exit;

class THR_API_Floors {

    private string $table;

    public function __construct() {
        $this->table = THR_Database::t( 'floor_plans' );
    }

    public function register(): void {
        $ns = THR_REST_NS;

        register_rest_route( $ns, '/floor-plans', [
            [ 'methods' => 'GET',  'callback' => [ $this, 'list' ],   'permission_callback' => fn() => current_user_can( 'thr_view_reservations' ) ],
            [ 'methods' => 'POST', 'callback' => [ $this, 'create' ], 'permission_callback' => fn() => current_user_can( 'thr_manage_floor_plans' ) ],
        ] );

        register_rest_route( $ns, '/floor-plans/(?P<id>\d+)', [
            [ 'methods' => 'GET',    'callback' => [ $this, 'get_one' ], 'permission_callback' => fn() => current_user_can( 'thr_view_reservations' ) ],
            [ 'methods' => 'PATCH',  'callback' => [ $this, 'update' ],  'permission_callback' => fn() => current_user_can( 'thr_manage_floor_plans' ) ],
            [ 'methods' => 'DELETE', 'callback' => [ $this, 'delete' ],  'permission_callback' => fn() => current_user_can( 'thr_manage_floor_plans' ) ],
        ] );

        // Upload floor plan background image/PDF
        register_rest_route( $ns, '/floor-plans/(?P<id>\d+)/background', [
            'methods' => 'POST', 'callback' => [ $this, 'upload_background' ],
            'permission_callback' => fn() => current_user_can( 'thr_manage_floor_plans' ),
        ] );
    }

    public function list(): WP_REST_Response {
        global $wpdb;
        $rows = $wpdb->get_results( "SELECT * FROM {$this->table} ORDER BY sort_order ASC, floor_number ASC" );
        return new WP_REST_Response( array_map( [ $this, 'format' ], $rows ) );
    }

    public function get_one( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        global $wpdb;
        $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->table} WHERE id = %d", $req->get_param( 'id' ) ) );
        if ( ! $row ) return THR_API::error( 'thr_not_found', 'Floor plan not found.', 404 );
        return new WP_REST_Response( $this->format( $row ) );
    }

    public function create( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        global $wpdb;
        $body = $req->get_json_params() ?? [];
        $name = sanitize_text_field( $body['name'] ?? '' );
        if ( ! $name ) return THR_API::error( 'thr_validation', 'Floor plan name is required.', 422 );

        $now = THR_API::now_utc();
        $wpdb->insert( $this->table, [
            'name'         => $name,
            'floor_number' => (int) ( $body['floor_number'] ?? 1 ),
            'width_px'     => (int) ( $body['width_px'] ?? 1200 ),
            'height_px'    => (int) ( $body['height_px'] ?? 800 ),
            'is_active'    => 1,
            'sort_order'   => (int) ( $body['sort_order'] ?? 0 ),
            'created_at'   => $now,
            'updated_at'   => $now,
        ] );
        $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->table} WHERE id = %d", $wpdb->insert_id ) );
        return new WP_REST_Response( $this->format( $row ), 201 );
    }

    public function delete( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        global $wpdb;
        $id = (int) $req->get_param( 'id' );
        if ( ! $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$this->table} WHERE id = %d", $id ) ) ) {
            return THR_API::error( 'thr_not_found', 'Floor plan not found.', 404 );
        }
        // Delete furniture too
        $wpdb->delete( THR_Database::t( 'furniture' ), [ 'floor_plan_id' => $id ] );
        $wpdb->delete( $this->table, [ 'id' => $id ] );
        return new WP_REST_Response( null, 204 );
    }

    public function upload_background( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        global $wpdb;
        if ( ! function_exists( 'wp_handle_upload' ) ) require_once ABSPATH . 'wp-admin/includes/file.php';

        $id    = (int) $req->get_param( 'id' );
        $files = $req->get_file_params();
        if ( empty( $files['background'] ) ) return THR_API::error( 'thr_no_file', 'No file uploaded.', 422 );

        $file          = $files['background'];
        $allowed_types = [ 'image/jpeg', 'image/png', 'image/webp' ];
        if ( ! in_array( $file['type'], $allowed_types, true ) ) {
            return THR_API::error( 'thr_invalid_file', 'Only JPEG, PNG, or WebP images are accepted.', 422 );
        }
        if ( $file['size'] > 20 * 1024 * 1024 ) {
            return THR_API::error( 'thr_file_too_large', 'File must be under 20 MB.', 422 );
        }

        $overrides = [ 'test_form' => false, 'test_type' => false ];
        $result    = wp_handle_upload( $file, $overrides );
        if ( isset( $result['error'] ) ) return THR_API::error( 'thr_upload_error', $result['error'], 500 );

        $wpdb->update( $this->table,
            [
                'background_url' => $result['url'],
                'bg_scale'       => 1.0,
                'bg_scale_y'     => 0.0,
                'bg_opacity'     => 0.5,
                'bg_offset_x'    => 0.0,
                'bg_offset_y'    => 0.0,
                'updated_at'     => THR_API::now_utc(),
            ],
            [ 'id' => $id ]
        );
        $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->table} WHERE id = %d", $id ) );
        return new WP_REST_Response( $this->format( $row ) );
    }

    public function update( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        global $wpdb;
        $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->table} WHERE id = %d", $req->get_param( 'id' ) ) );
        if ( ! $row ) return THR_API::error( 'thr_not_found', 'Floor plan not found.', 404 );

        $body    = $req->get_json_params() ?? [];
        $allowed = [ 'name', 'floor_number', 'width_px', 'height_px', 'is_active', 'sort_order',
                     'bg_scale', 'bg_scale_y', 'bg_opacity', 'bg_offset_x', 'bg_offset_y' ];
        $update  = array_intersect_key( $body, array_flip( $allowed ) );
        if ( isset( $update['name'] ) ) $update['name'] = sanitize_text_field( $update['name'] );
        // Clamp float bg fields to sane ranges
        if ( isset( $update['bg_scale'] ) )   $update['bg_scale']   = max( 0.05, min( 20.0, (float) $update['bg_scale'] ) );
        if ( isset( $update['bg_scale_y'] ) ) $update['bg_scale_y'] = max( 0.0,  min( 20.0, (float) $update['bg_scale_y'] ) );
        if ( isset( $update['bg_opacity'] ) )  $update['bg_opacity']  = max( 0.0, min( 1.0,  (float) $update['bg_opacity'] ) );
        if ( isset( $update['bg_offset_x'] ) ) $update['bg_offset_x'] = (float) $update['bg_offset_x'];
        if ( isset( $update['bg_offset_y'] ) ) $update['bg_offset_y'] = (float) $update['bg_offset_y'];
        $update['updated_at'] = THR_API::now_utc();

        $wpdb->update( $this->table, $update, [ 'id' => $row->id ] );
        return new WP_REST_Response( $this->format( $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->table} WHERE id = %d", $row->id ) ) ) );
    }

    private function format( object $row ): array {
        return [
            'id'             => (int)   $row->id,
            'name'           => $row->name,
            'floor_number'   => (int)   $row->floor_number,
            'background_url' => $row->background_url ?? null,
            'bg_scale'       => (float) ( $row->bg_scale    ?? 1.0 ),
            'bg_scale_y'     => (float) ( $row->bg_scale_y  ?? 0.0 ),
            'bg_opacity'     => (float) ( $row->bg_opacity   ?? 0.5 ),
            'bg_offset_x'    => (float) ( $row->bg_offset_x  ?? 0.0 ),
            'bg_offset_y'    => (float) ( $row->bg_offset_y  ?? 0.0 ),
            'width_px'       => (int)   $row->width_px,
            'height_px'      => (int)   $row->height_px,
            'is_active'      => (bool)  $row->is_active,
            'sort_order'     => (int)   $row->sort_order,
            'created_at'     => $row->created_at,
            'updated_at'     => $row->updated_at,
        ];
    }
}
