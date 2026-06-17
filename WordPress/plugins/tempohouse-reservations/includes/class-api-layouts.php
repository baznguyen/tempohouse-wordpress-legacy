<?php
defined( 'ABSPATH' ) || exit;

class THR_API_Layouts {

    private string $tLayouts;
    private string $tPeriods;
    private string $tSlots;
    private string $tFurniture;
    private string $tFloors;

    public function __construct() {
        $this->tLayouts   = THR_Database::t( 'layouts' );
        $this->tPeriods   = THR_Database::t( 'layout_periods' );
        $this->tSlots     = THR_Database::t( 'layout_slots' );
        $this->tFurniture = THR_Database::t( 'furniture' );
        $this->tFloors    = THR_Database::t( 'floor_plans' );
    }

    public function register(): void {
        $ns  = THR_REST_NS;
        $cap = 'thr_manage_floor_plans';

        // Layouts CRUD
        register_rest_route( $ns, '/layouts', [
            [ 'methods' => 'GET',  'callback' => [ $this, 'list' ],   'permission_callback' => fn() => current_user_can( $cap ) ],
            [ 'methods' => 'POST', 'callback' => [ $this, 'create' ], 'permission_callback' => fn() => current_user_can( $cap ) ],
        ] );
        register_rest_route( $ns, '/layouts/(?P<id>\d+)', [
            [ 'methods' => 'GET',    'callback' => [ $this, 'get_one' ], 'permission_callback' => fn() => current_user_can( $cap ) ],
            [ 'methods' => 'PATCH',  'callback' => [ $this, 'update' ],  'permission_callback' => fn() => current_user_can( $cap ) ],
            [ 'methods' => 'DELETE', 'callback' => [ $this, 'delete' ],  'permission_callback' => fn() => current_user_can( $cap ) ],
        ] );

        // Copy base furniture into a layout (snapshot)
        register_rest_route( $ns, '/layouts/(?P<id>\d+)/snapshot', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'snapshot' ],
            'permission_callback' => fn() => current_user_can( $cap ),
        ] );

        // Activate layout on a floor plan
        register_rest_route( $ns, '/layouts/(?P<id>\d+)/activate', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'activate' ],
            'permission_callback' => fn() => current_user_can( $cap ),
        ] );

        // Deactivate (return to base) — POST to floor-plan level
        register_rest_route( $ns, '/floor-plans/(?P<id>\d+)/deactivate-layout', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'deactivate' ],
            'permission_callback' => fn() => current_user_can( $cap ),
        ] );

        // Periods CRUD
        register_rest_route( $ns, '/layouts/(?P<layout_id>\d+)/periods', [
            [ 'methods' => 'GET',  'callback' => [ $this, 'list_periods' ],  'permission_callback' => fn() => current_user_can( $cap ) ],
            [ 'methods' => 'POST', 'callback' => [ $this, 'create_period' ], 'permission_callback' => fn() => current_user_can( $cap ) ],
        ] );
        register_rest_route( $ns, '/periods/(?P<id>\d+)', [
            [ 'methods' => 'PATCH',  'callback' => [ $this, 'update_period' ], 'permission_callback' => fn() => current_user_can( $cap ) ],
            [ 'methods' => 'DELETE', 'callback' => [ $this, 'delete_period' ], 'permission_callback' => fn() => current_user_can( $cap ) ],
        ] );

        // Slots CRUD
        register_rest_route( $ns, '/layouts/(?P<layout_id>\d+)/slots', [
            [ 'methods' => 'GET',  'callback' => [ $this, 'list_slots' ],  'permission_callback' => fn() => current_user_can( $cap ) ],
            [ 'methods' => 'POST', 'callback' => [ $this, 'create_slot' ], 'permission_callback' => fn() => current_user_can( $cap ) ],
        ] );
        register_rest_route( $ns, '/slots/(?P<id>\d+)', [
            [ 'methods' => 'PATCH',  'callback' => [ $this, 'update_slot' ], 'permission_callback' => fn() => current_user_can( $cap ) ],
            [ 'methods' => 'DELETE', 'callback' => [ $this, 'delete_slot' ], 'permission_callback' => fn() => current_user_can( $cap ) ],
        ] );

        // Bulk-save all slots for a layout (replaces publish/saveLayout endpoint)
        register_rest_route( $ns, '/layouts/(?P<layout_id>\d+)/slots/bulk', [
            'methods'             => 'POST',
            'callback'            => [ $this, 'bulk_save_slots' ],
            'permission_callback' => fn() => current_user_can( $cap ),
        ] );
    }

    // ── Layouts ──────────────────────────────────────────────────────────────

    public function list( WP_REST_Request $req ): WP_REST_Response {
        global $wpdb;
        $floor_plan_id = (int) $req->get_param( 'floor_plan_id' );
        $where = $floor_plan_id ? $wpdb->prepare( 'WHERE floor_plan_id = %d', $floor_plan_id ) : '';
        $rows  = $wpdb->get_results( "SELECT * FROM {$this->tLayouts} {$where} ORDER BY sort_order ASC, id ASC" );
        return new WP_REST_Response( array_map( [ $this, 'format_layout' ], $rows ) );
    }

    public function get_one( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        global $wpdb;
        $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->tLayouts} WHERE id = %d", $req->get_param( 'id' ) ) );
        if ( ! $row ) return THR_API::error( 'thr_not_found', 'Layout not found.', 404 );
        return new WP_REST_Response( $this->format_layout( $row ) );
    }

    public function create( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        global $wpdb;
        $body          = $req->get_json_params() ?? [];
        $floor_plan_id = (int) ( $body['floor_plan_id'] ?? 0 );
        $name          = sanitize_text_field( $body['name'] ?? '' );
        if ( ! $floor_plan_id || ! $name ) {
            return THR_API::error( 'thr_validation', 'floor_plan_id and name are required.', 422 );
        }
        $now = THR_API::now_utc();
        $wpdb->insert( $this->tLayouts, [
            'floor_plan_id' => $floor_plan_id,
            'name'          => $name,
            'is_default'    => (int) ( $body['is_default'] ?? 0 ),
            'sort_order'    => (int) ( $body['sort_order'] ?? 0 ),
            'note'          => sanitize_textarea_field( $body['note'] ?? '' ) ?: null,
            'created_at'    => $now,
            'updated_at'    => $now,
        ] );
        $layout_id = $wpdb->insert_id;

        // If copy_from_base is true, snapshot current base furniture immediately
        if ( ! empty( $body['copy_from_base'] ) ) {
            $this->do_snapshot( $layout_id, $floor_plan_id );
        }

        // Mark as default if requested (clear others first)
        if ( ! empty( $body['is_default'] ) ) {
            $wpdb->query( $wpdb->prepare(
                "UPDATE {$this->tLayouts} SET is_default = 0 WHERE floor_plan_id = %d AND id != %d",
                $floor_plan_id, $layout_id
            ) );
        }

        $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->tLayouts} WHERE id = %d", $layout_id ) );
        return new WP_REST_Response( $this->format_layout( $row ), 201 );
    }

    public function update( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        global $wpdb;
        $id  = (int) $req->get_param( 'id' );
        $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->tLayouts} WHERE id = %d", $id ) );
        if ( ! $row ) return THR_API::error( 'thr_not_found', 'Layout not found.', 404 );

        $body    = $req->get_json_params() ?? [];
        $allowed = [ 'name', 'is_default', 'sort_order', 'note' ];
        $update  = array_intersect_key( $body, array_flip( $allowed ) );
        if ( isset( $update['name'] ) ) $update['name'] = sanitize_text_field( $update['name'] );
        if ( isset( $update['note'] ) ) $update['note'] = sanitize_textarea_field( $update['note'] ) ?: null;
        $update['updated_at'] = THR_API::now_utc();

        if ( ! empty( $update['is_default'] ) ) {
            $wpdb->query( $wpdb->prepare(
                "UPDATE {$this->tLayouts} SET is_default = 0 WHERE floor_plan_id = %d AND id != %d",
                $row->floor_plan_id, $id
            ) );
        }

        $wpdb->update( $this->tLayouts, $update, [ 'id' => $id ] );
        return new WP_REST_Response( $this->format_layout( $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->tLayouts} WHERE id = %d", $id ) ) ) );
    }

    public function delete( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        global $wpdb;
        $id = (int) $req->get_param( 'id' );
        if ( ! $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$this->tLayouts} WHERE id = %d", $id ) ) ) {
            return THR_API::error( 'thr_not_found', 'Layout not found.', 404 );
        }
        $wpdb->delete( $this->tPeriods, [ 'layout_id' => $id ] );
        $wpdb->delete( $this->tSlots,   [ 'layout_id' => $id ] );
        $wpdb->delete( $this->tLayouts, [ 'id' => $id ] );
        // Clear active_layout_id if this was active
        $wpdb->query( $wpdb->prepare(
            "UPDATE {$this->tFloors} SET active_layout_id = NULL WHERE active_layout_id = %d", $id
        ) );
        return new WP_REST_Response( null, 204 );
    }

    public function snapshot( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        global $wpdb;
        $id  = (int) $req->get_param( 'id' );
        $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->tLayouts} WHERE id = %d", $id ) );
        if ( ! $row ) return THR_API::error( 'thr_not_found', 'Layout not found.', 404 );
        $count = $this->do_snapshot( $id, (int) $row->floor_plan_id );
        return new WP_REST_Response( [ 'layout_id' => $id, 'slots_created' => $count ] );
    }

    public function activate( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        global $wpdb;
        $id  = (int) $req->get_param( 'id' );
        $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->tLayouts} WHERE id = %d", $id ) );
        if ( ! $row ) return THR_API::error( 'thr_not_found', 'Layout not found.', 404 );
        $wpdb->update( $this->tFloors, [ 'active_layout_id' => $id, 'updated_at' => THR_API::now_utc() ], [ 'id' => $row->floor_plan_id ] );
        return new WP_REST_Response( [ 'floor_plan_id' => (int) $row->floor_plan_id, 'active_layout_id' => $id ] );
    }

    public function deactivate( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        global $wpdb;
        $floor_id = (int) $req->get_param( 'id' );
        $wpdb->update( $this->tFloors, [ 'active_layout_id' => null, 'updated_at' => THR_API::now_utc() ], [ 'id' => $floor_id ] );
        return new WP_REST_Response( [ 'floor_plan_id' => $floor_id, 'active_layout_id' => null ] );
    }

    // ── Periods ──────────────────────────────────────────────────────────────

    public function list_periods( WP_REST_Request $req ): WP_REST_Response {
        global $wpdb;
        $lid  = (int) $req->get_param( 'layout_id' );
        $rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$this->tPeriods} WHERE layout_id = %d ORDER BY start_time ASC", $lid ) );
        return new WP_REST_Response( array_map( [ $this, 'format_period' ], $rows ) );
    }

    public function create_period( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        global $wpdb;
        $lid  = (int) $req->get_param( 'layout_id' );
        $body = $req->get_json_params() ?? [];
        $name = sanitize_text_field( $body['name'] ?? '' );
        if ( ! $lid || ! $name ) return THR_API::error( 'thr_validation', 'name is required.', 422 );
        $wpdb->insert( $this->tPeriods, [
            'layout_id'    => $lid,
            'name'         => $name,
            'days_of_week' => (int) ( $body['days_of_week'] ?? 127 ),
            'start_time'   => $body['start_time'] ?? '00:00:00',
            'end_time'     => $body['end_time']   ?? '23:59:00',
            'is_enabled'   => (int) ( $body['is_enabled'] ?? 1 ),
        ] );
        $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->tPeriods} WHERE id = %d", $wpdb->insert_id ) );
        return new WP_REST_Response( $this->format_period( $row ), 201 );
    }

    public function update_period( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        global $wpdb;
        $id      = (int) $req->get_param( 'id' );
        $body    = $req->get_json_params() ?? [];
        $allowed = [ 'name', 'days_of_week', 'start_time', 'end_time', 'is_enabled' ];
        $update  = array_intersect_key( $body, array_flip( $allowed ) );
        if ( isset( $update['name'] ) ) $update['name'] = sanitize_text_field( $update['name'] );
        if ( ! $update ) return THR_API::error( 'thr_validation', 'Nothing to update.', 422 );
        $wpdb->update( $this->tPeriods, $update, [ 'id' => $id ] );
        $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->tPeriods} WHERE id = %d", $id ) );
        if ( ! $row ) return THR_API::error( 'thr_not_found', 'Period not found.', 404 );
        return new WP_REST_Response( $this->format_period( $row ) );
    }

    public function delete_period( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        global $wpdb;
        $id = (int) $req->get_param( 'id' );
        $wpdb->delete( $this->tPeriods, [ 'id' => $id ] );
        return new WP_REST_Response( null, 204 );
    }

    // ── Slots ────────────────────────────────────────────────────────────────

    public function list_slots( WP_REST_Request $req ): WP_REST_Response {
        global $wpdb;
        $lid  = (int) $req->get_param( 'layout_id' );
        $rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$this->tSlots} WHERE layout_id = %d ORDER BY id ASC", $lid ) );
        return new WP_REST_Response( array_map( [ $this, 'format_slot' ], $rows ) );
    }

    public function create_slot( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        global $wpdb;
        $lid  = (int) $req->get_param( 'layout_id' );
        $body = $req->get_json_params() ?? [];
        if ( ! $lid ) return THR_API::error( 'thr_validation', 'layout_id required.', 422 );
        $wpdb->insert( $this->tSlots, $this->slot_data( array_merge( $body, [ 'layout_id' => $lid ] ) ) );
        $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->tSlots} WHERE id = %d", $wpdb->insert_id ) );
        return new WP_REST_Response( $this->format_slot( $row ), 201 );
    }

    public function update_slot( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        global $wpdb;
        $id      = (int) $req->get_param( 'id' );
        $body    = $req->get_json_params() ?? [];
        $allowed = [ 'label', 'pos_x', 'pos_y', 'width', 'height', 'rotation_deg',
                     'capacity_min', 'capacity_max', 'element_key', 'group_id', 'is_visible', 'meta' ];
        $update  = array_intersect_key( $body, array_flip( $allowed ) );
        if ( ! $update ) return THR_API::error( 'thr_validation', 'Nothing to update.', 422 );
        if ( isset( $update['label'] ) )       $update['label']       = sanitize_text_field( $update['label'] );
        if ( isset( $update['element_key'] ) ) $update['element_key'] = sanitize_text_field( $update['element_key'] );
        $wpdb->update( $this->tSlots, $update, [ 'id' => $id ] );
        $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->tSlots} WHERE id = %d", $id ) );
        if ( ! $row ) return THR_API::error( 'thr_not_found', 'Slot not found.', 404 );
        return new WP_REST_Response( $this->format_slot( $row ) );
    }

    public function delete_slot( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        global $wpdb;
        $wpdb->delete( $this->tSlots, [ 'id' => (int) $req->get_param( 'id' ) ] );
        return new WP_REST_Response( null, 204 );
    }

    public function bulk_save_slots( WP_REST_Request $req ): WP_REST_Response|WP_Error {
        global $wpdb;
        $lid   = (int) $req->get_param( 'layout_id' );
        $body  = $req->get_json_params() ?? [];
        $items = $body['items'] ?? [];
        if ( ! is_array( $items ) ) return THR_API::error( 'thr_validation', 'items must be an array.', 422 );

        // Delete all existing slots and re-insert
        $wpdb->delete( $this->tSlots, [ 'layout_id' => $lid ] );
        foreach ( $items as $item ) {
            $wpdb->insert( $this->tSlots, $this->slot_data( array_merge( (array) $item, [ 'layout_id' => $lid ] ) ) );
        }
        $rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$this->tSlots} WHERE layout_id = %d ORDER BY id ASC", $lid ) );
        return new WP_REST_Response( array_map( [ $this, 'format_slot' ], $rows ) );
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function do_snapshot( int $layout_id, int $floor_plan_id ): int {
        global $wpdb;
        // Clear existing slots for this layout first
        $wpdb->delete( $this->tSlots, [ 'layout_id' => $layout_id ] );
        $furniture = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$this->tFurniture} WHERE floor_plan_id = %d AND is_available = 1 ORDER BY id ASC",
            $floor_plan_id
        ) );
        $count = 0;
        foreach ( $furniture as $f ) {
            $wpdb->insert( $this->tSlots, [
                'layout_id'    => $layout_id,
                'furniture_id' => (int) $f->id,
                'type'         => $f->type,
                'label'        => $f->label,
                'pos_x'        => (float) $f->pos_x,
                'pos_y'        => (float) $f->pos_y,
                'width'        => (float) $f->width,
                'height'       => (float) $f->height,
                'rotation_deg' => (int)   $f->rotation_deg,
                'capacity_min' => (int)   $f->capacity_min,
                'capacity_max' => (int)   $f->capacity_max,
                'element_key'  => $f->element_key ?? null,
                'group_id'     => $f->group_id ? (int) $f->group_id : null,
                'is_visible'   => 1,
                'meta'         => $f->meta ?? null,
            ] );
            $count++;
        }
        return $count;
    }

    private function slot_data( array $body ): array {
        return [
            'layout_id'    => (int)   ( $body['layout_id']    ?? 0 ),
            'furniture_id' => isset( $body['furniture_id'] ) ? (int) $body['furniture_id'] : null,
            'type'         => sanitize_text_field( $body['type']  ?? 'table-rect' ),
            'label'        => sanitize_text_field( $body['label'] ?? '' ),
            'pos_x'        => (float) ( $body['pos_x']        ?? 0 ),
            'pos_y'        => (float) ( $body['pos_y']        ?? 0 ),
            'width'        => (float) ( $body['width']        ?? 80 ),
            'height'       => (float) ( $body['height']       ?? 80 ),
            'rotation_deg' => (int)   ( $body['rotation_deg'] ?? 0 ),
            'capacity_min' => (int)   ( $body['capacity_min'] ?? 1 ),
            'capacity_max' => (int)   ( $body['capacity_max'] ?? 4 ),
            'element_key'  => isset( $body['element_key'] ) ? sanitize_text_field( $body['element_key'] ) : null,
            'group_id'     => isset( $body['group_id'] ) && $body['group_id'] ? (int) $body['group_id'] : null,
            'is_visible'   => (int)   ( $body['is_visible']   ?? 1 ),
            'meta'         => isset( $body['meta'] )
                ? ( is_string( $body['meta'] ) ? $body['meta'] : wp_json_encode( $body['meta'] ) )
                : null,
        ];
    }

    private function format_layout( object $row ): array {
        return [
            'id'            => (int)  $row->id,
            'floor_plan_id' => (int)  $row->floor_plan_id,
            'name'          => $row->name,
            'is_default'    => (bool) $row->is_default,
            'sort_order'    => (int)  $row->sort_order,
            'note'          => $row->note ?? null,
            'created_at'    => $row->created_at,
            'updated_at'    => $row->updated_at,
        ];
    }

    private function format_period( object $row ): array {
        return [
            'id'           => (int)  $row->id,
            'layout_id'    => (int)  $row->layout_id,
            'name'         => $row->name,
            'days_of_week' => (int)  $row->days_of_week,
            'start_time'   => $row->start_time,
            'end_time'     => $row->end_time,
            'is_enabled'   => (bool) $row->is_enabled,
        ];
    }

    private function format_slot( object $row ): array {
        return [
            'id'           => (int)   $row->id,
            'layout_id'    => (int)   $row->layout_id,
            'furniture_id' => $row->furniture_id ? (int) $row->furniture_id : null,
            'type'         => $row->type,
            'label'        => $row->label,
            'pos_x'        => (float) $row->pos_x,
            'pos_y'        => (float) $row->pos_y,
            'width'        => (float) $row->width,
            'height'       => (float) $row->height,
            'rotation_deg' => (int)   $row->rotation_deg,
            'capacity_min' => (int)   $row->capacity_min,
            'capacity_max' => (int)   $row->capacity_max,
            'element_key'  => $row->element_key ?? null,
            'group_id'     => $row->group_id ? (int) $row->group_id : null,
            'is_visible'   => (bool)  $row->is_visible,
            'meta'         => $row->meta ?? null,
        ];
    }
}
