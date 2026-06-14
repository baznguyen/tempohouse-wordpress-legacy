<?php
defined( 'ABSPATH' ) || exit;

/**
 * Handles form POST actions from admin pages (non-AJAX).
 */
class THR_Admin_Actions {

    public static function init(): void {
        add_action( 'admin_post_thr_update_notes', [ __CLASS__, 'handle_update_notes' ] );
    }

    public static function handle_update_notes(): void {
        $id = (int) ( $_POST['reservation_id'] ?? 0 );
        if ( ! $id || ! current_user_can( 'thr_edit_reservations' ) ) wp_die( 'Forbidden', 403 );
        if ( ! wp_verify_nonce( $_POST['thr_nonce'] ?? '', 'thr_update_notes_' . $id ) ) wp_die( 'Security check failed' );

        global $wpdb;
        $wpdb->update(
            THR_Database::t( 'reservations' ),
            [ 'notes_internal' => sanitize_textarea_field( $_POST['notes_internal'] ?? '' ), 'updated_at' => current_time( 'mysql', true ) ],
            [ 'id' => $id ]
        );
        wp_safe_redirect( admin_url( "admin.php?page=thr-reservation&id=$id&updated=1" ) );
        exit;
    }
}

THR_Admin_Actions::init();
