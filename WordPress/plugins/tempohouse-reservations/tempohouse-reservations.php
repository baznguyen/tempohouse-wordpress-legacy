<?php
/**
 * Plugin Name:  TEMPO House Reservations
 * Plugin URI:   https://tempohouse.com.vn
 * Description:  Full-stack reservation management for TEMPO House — floor plan builder, multi-role admin, diner booking widget, email notifications.
 * Version:      1.4.0
 * Author:       Raging Monk
 * Text Domain:  tempohouse-res
 * Domain Path:  /languages
 * Requires PHP: 8.0
 * Requires at least: 6.4
 */

defined( 'ABSPATH' ) || exit;

define( 'THR_VERSION',       '1.4.0' );
define( 'THR_DB_VERSION',    '1.3' );
define( 'THR_PLUGIN_FILE',   __FILE__ );
define( 'THR_PLUGIN_DIR',    plugin_dir_path( __FILE__ ) );
define( 'THR_PLUGIN_URL',    plugin_dir_url( __FILE__ ) );
define( 'THR_REST_NS',       'thr/v1' );
define( 'THR_OPTION_PREFIX', 'thr_' );

// ── Autoloader ────────────────────────────────────────────────────────────────
spl_autoload_register( function ( string $class ) {
    if ( strpos( $class, 'THR_' ) !== 0 ) return;
    $slug = strtolower( str_replace( [ 'THR_', '_' ], [ '', '-' ], $class ) );
    $file = THR_PLUGIN_DIR . 'includes/class-' . $slug . '.php';
    if ( file_exists( $file ) ) require_once $file;
} );

// ── Activation / deactivation ─────────────────────────────────────────────────
register_activation_hook( __FILE__, [ 'THR_Database', 'install' ] );
register_activation_hook( __FILE__, [ 'THR_Roles',    'install' ] );
register_activation_hook( __FILE__, [ 'THR_Cron',     'schedule_events' ] );
register_deactivation_hook( __FILE__, [ 'THR_Cron',   'unschedule_events' ] );

// ── Bootstrap on plugins_loaded ───────────────────────────────────────────────
add_action( 'plugins_loaded', function () {

    // DB upgrade check
    if ( get_option( 'thr_db_version' ) !== THR_DB_VERSION ) {
        THR_Database::install();
    }

    // Default settings on first run
    THR_Settings::maybe_set_defaults();

    // Admin POST actions (non-AJAX form handlers)
    require_once THR_PLUGIN_DIR . 'includes/class-admin-actions.php';

    // Init modules
    ( new THR_API() )->init();
    ( new THR_Admin() )->init();
    ( new THR_Booking_Widget() )->init();
    ( new THR_Cron() )->init();

}, 10 );
