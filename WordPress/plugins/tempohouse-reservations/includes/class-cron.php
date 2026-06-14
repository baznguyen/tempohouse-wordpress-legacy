<?php
defined( 'ABSPATH' ) || exit;

class THR_Cron {

    const HOOK_REMINDER      = 'thr_send_reminders';
    const HOOK_FEEDBACK      = 'thr_send_feedback';
    const HOOK_SHIFT_REPORT  = 'thr_send_shift_report';
    const HOOK_WAITLIST_SWEEP= 'thr_waitlist_sweep';

    public function init(): void {
        add_action( self::HOOK_REMINDER,       [ $this, 'process_reminders' ] );
        add_action( self::HOOK_FEEDBACK,        [ $this, 'process_feedback' ] );
        add_action( self::HOOK_SHIFT_REPORT,    [ $this, 'process_shift_report' ] );
        add_action( self::HOOK_WAITLIST_SWEEP,  [ $this, 'process_waitlist_sweep' ] );

        // When a reservation is cancelled, fire waitlist check
        add_action( 'thr_reservation_status_changed', [ $this, 'on_status_changed' ], 10, 3 );
    }

    // ── Scheduling ─────────────────────────────────────────────────────────────

    public static function schedule_events(): void {
        if ( ! wp_next_scheduled( self::HOOK_REMINDER ) ) {
            wp_schedule_event( time(), 'hourly', self::HOOK_REMINDER );
        }
        if ( ! wp_next_scheduled( self::HOOK_FEEDBACK ) ) {
            wp_schedule_event( time(), 'hourly', self::HOOK_FEEDBACK );
        }
        if ( ! wp_next_scheduled( self::HOOK_WAITLIST_SWEEP ) ) {
            // Run waitlist sweep every 2 hours
            wp_schedule_event( time(), 'twicedaily', self::HOOK_WAITLIST_SWEEP );
        }
        // Shift report: schedule for ~10 PM venue time (15:00 UTC)
        // Recalculated each activation; admin can change via settings
        self::reschedule_shift_report();
    }

    public static function unschedule_events(): void {
        wp_clear_scheduled_hook( self::HOOK_REMINDER );
        wp_clear_scheduled_hook( self::HOOK_FEEDBACK );
        wp_clear_scheduled_hook( self::HOOK_SHIFT_REPORT );
        wp_clear_scheduled_hook( self::HOOK_WAITLIST_SWEEP );
    }

    public static function reschedule_shift_report(): void {
        wp_clear_scheduled_hook( self::HOOK_SHIFT_REPORT );
        if ( ! THR_Settings::get( 'shift_report_enabled', false ) ) return;
        if ( ! THR_Settings::get( 'shift_report_email', '' ) ) return;

        $time_str = THR_Settings::get( 'shift_report_time', '22:00' ); // GMT+7
        [ $h, $m ] = array_map( 'intval', explode( ':', $time_str ) );
        // Convert to UTC
        $utc_h = ( $h - 7 + 24 ) % 24;

        // Next occurrence
        $now   = time();
        $today = gmdate( 'Y-m-d', $now );
        $next  = strtotime( "{$today} {$utc_h}:{$m}:00 UTC" );
        if ( $next <= $now ) $next += DAY_IN_SECONDS;

        wp_schedule_event( $next, 'daily', self::HOOK_SHIFT_REPORT );
    }

    // ── Reminder processing (runs hourly) ──────────────────────────────────────

    public function process_reminders(): void {
        global $wpdb;
        $table = THR_Database::t( 'reservations' );
        $now   = time() + 7 * 3600;

        if ( THR_Settings::get( 'reminder_24h', true ) ) {
            $window_start = gmdate( 'Y-m-d H:i:s', $now + 23 * 3600 );
            $window_end   = gmdate( 'Y-m-d H:i:s', $now + 25 * 3600 );
            $rows = $wpdb->get_results( $wpdb->prepare(
                "SELECT * FROM $table
                 WHERE status IN ('confirmed')
                   AND reservation_dt BETWEEN %s AND %s
                   AND reminder_24h_sent_at IS NULL",
                $window_start, $window_end
            ) );
            foreach ( $rows as $r ) {
                THR_Email::send_reminder( $r, '24h' );
                $wpdb->update( $table, [ 'reminder_24h_sent_at' => current_time( 'mysql', true ) ], [ 'id' => $r->id ] );
            }
        }

        if ( THR_Settings::get( 'reminder_4h', true ) ) {
            $window_start = gmdate( 'Y-m-d H:i:s', $now + 3 * 3600 + 30 * 60 );
            $window_end   = gmdate( 'Y-m-d H:i:s', $now + 4 * 3600 + 30 * 60 );
            $rows = $wpdb->get_results( $wpdb->prepare(
                "SELECT * FROM $table
                 WHERE status IN ('confirmed')
                   AND reservation_dt BETWEEN %s AND %s
                   AND reminder_4h_sent_at IS NULL",
                $window_start, $window_end
            ) );
            foreach ( $rows as $r ) {
                THR_Email::send_reminder( $r, '4h' );
                $wpdb->update( $table, [ 'reminder_4h_sent_at' => current_time( 'mysql', true ) ], [ 'id' => $r->id ] );
            }
        }
    }

    // ── Feedback processing (runs hourly) ─────────────────────────────────────

    public function process_feedback(): void {
        global $wpdb;
        $table = THR_Database::t( 'reservations' );

        if ( ! THR_Settings::get( 'feedback_form_url' ) ) return;

        $delay_min    = (int) THR_Settings::get( 'feedback_delay_min', 120 );
        $ended_before = gmdate( 'Y-m-d H:i:s', time() - $delay_min * 60 );

        $rows = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM $table
             WHERE status IN ('completed','seated')
               AND DATE_ADD(reservation_dt, INTERVAL duration_min MINUTE) <= %s
               AND feedback_sent_at IS NULL",
            $ended_before
        ) );

        foreach ( $rows as $r ) {
            THR_Email::send_feedback( $r );
            $wpdb->update( $table, [ 'feedback_sent_at' => current_time( 'mysql', true ) ], [ 'id' => $r->id ] );
        }
    }

    // ── Daily shift report email ───────────────────────────────────────────────

    public function process_shift_report(): void {
        $to = THR_Settings::get( 'shift_report_email', '' );
        if ( ! $to ) return;
        THR_Email::send_shift_report( $to, gmdate( 'Y-m-d', time() + 7 * 3600 ) );
    }

    // ── Waitlist sweep — expire old entries ───────────────────────────────────

    public function process_waitlist_sweep(): void {
        global $wpdb;
        $table = THR_Database::t( 'waitlist' );
        // Expire waiting entries whose requested date has passed
        $yesterday = gmdate( 'Y-m-d', time() + 7 * 3600 - DAY_IN_SECONDS );
        $wpdb->query( $wpdb->prepare(
            "UPDATE $table SET status='expired', updated_at=%s
             WHERE status='waiting' AND requested_date < %s",
            current_time( 'mysql', true ), $yesterday
        ) );
    }

    // ── On reservation status change — notify waitlist ────────────────────────

    public function on_status_changed( object $reservation, string $old_status, string $new_status ): void {
        if ( $new_status !== 'cancelled' ) return;
        $this->notify_waitlist_for_date( $reservation );
    }

    private function notify_waitlist_for_date( object $reservation ): void {
        global $wpdb;
        $table = THR_Database::t( 'waitlist' );

        // Date in venue local time
        $date_local = gmdate( 'Y-m-d', strtotime( $reservation->reservation_dt . ' UTC' ) + 7 * 3600 );
        $party_size = (int) $reservation->party_size;

        // Find waiting entries for this date that can fit in the opening party size
        $entries = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM $table
             WHERE status = 'waiting'
               AND requested_date = %s
               AND party_size <= %d
             ORDER BY created_at ASC
             LIMIT 3",
            $date_local, $party_size
        ) );

        $now = current_time( 'mysql', true );
        foreach ( $entries as $entry ) {
            THR_Email::send_waitlist_notification( $entry );
            $wpdb->update( $table, [
                'status'               => 'notified',
                'notification_sent_at' => $now,
                'updated_at'           => $now,
            ], [ 'id' => $entry->id ] );
        }
    }
}
