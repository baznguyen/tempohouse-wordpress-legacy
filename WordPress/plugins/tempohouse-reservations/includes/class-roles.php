<?php
defined( 'ABSPATH' ) || exit;

class THR_Roles {

    // All plugin capabilities
    const CAPS = [
        'thr_view_reservations',
        'thr_create_reservations',
        'thr_edit_reservations',
        'thr_cancel_reservations',
        'thr_delete_reservations',
        'thr_mark_seated',
        'thr_manage_floor_plans',
        'thr_manage_settings',
        'thr_view_reports',
        'thr_manage_tags',
        'thr_manage_blocks',
        'thr_manage_users',
    ];

    // Called on plugin activation
    public static function install(): void {
        self::add_roles();
        self::grant_caps_to_admins();
    }

    private static function add_roles(): void {
        // Remove stale roles before re-adding (handles version upgrades)
        remove_role( 'thr_admin' );
        remove_role( 'thr_manager' );
        remove_role( 'thr_staff' );

        add_role( 'thr_admin', __( 'Reservations Admin', 'tempohouse-res' ), [
            // All reservation caps
            'thr_view_reservations'   => true,
            'thr_create_reservations' => true,
            'thr_edit_reservations'   => true,
            'thr_cancel_reservations' => true,
            'thr_delete_reservations' => true,
            'thr_mark_seated'         => true,
            'thr_manage_floor_plans'  => true,
            'thr_manage_settings'     => true,
            'thr_view_reports'        => true,
            'thr_manage_tags'         => true,
            'thr_manage_blocks'       => true,
            'thr_manage_users'        => true,
            // Minimal WP caps for admin access
            'read'                    => true,
        ] );

        add_role( 'thr_manager', __( 'Reservations Manager', 'tempohouse-res' ), [
            'thr_view_reservations'   => true,
            'thr_create_reservations' => true,
            'thr_edit_reservations'   => true,
            'thr_cancel_reservations' => true,
            'thr_delete_reservations' => false,
            'thr_mark_seated'         => true,
            'thr_manage_floor_plans'  => true,
            'thr_manage_settings'     => false,
            'thr_view_reports'        => true,
            'thr_manage_tags'         => true,
            'thr_manage_blocks'       => true,
            'thr_manage_users'        => false,
            'read'                    => true,
        ] );

        add_role( 'thr_staff', __( 'Reservations Staff', 'tempohouse-res' ), [
            'thr_view_reservations'   => true,
            'thr_create_reservations' => true,
            'thr_edit_reservations'   => true,
            'thr_cancel_reservations' => false,
            'thr_delete_reservations' => false,
            'thr_mark_seated'         => true,
            'thr_manage_floor_plans'  => false,
            'thr_manage_settings'     => false,
            'thr_view_reports'        => true,
            'thr_manage_tags'         => false,
            'thr_manage_blocks'       => false,
            'thr_manage_users'        => false,
            'read'                    => true,
        ] );
    }

    // Give WP admins all plugin caps so they don't need a separate role
    private static function grant_caps_to_admins(): void {
        $admin = get_role( 'administrator' );
        if ( ! $admin ) return;
        foreach ( self::CAPS as $cap ) {
            $admin->add_cap( $cap );
        }
    }

    // Shorthand used across the plugin
    public static function current_user_can( string $cap ): bool {
        return current_user_can( $cap );
    }
}
