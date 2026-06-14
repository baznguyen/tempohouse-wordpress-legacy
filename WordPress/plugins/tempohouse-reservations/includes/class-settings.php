<?php
defined( 'ABSPATH' ) || exit;

class THR_Settings {

    private static array $defaults = [
        // Venue
        'venue_name'          => 'TEMPO House',
        'venue_address'       => 'Ho Chi Minh City, Vietnam',
        'venue_phone'         => '',
        'venue_email'         => '',
        'venue_timezone'      => 'Asia/Ho_Chi_Minh',
        // Email notifications
        'email_from_name'     => 'TEMPO House',
        'email_from_address'  => '',
        'email_reply_to'      => '',
        'email_logo_url'      => '',
        'email_accent_color'  => '#DDAA62',
        // Feedback
        'feedback_form_url'   => '',   // Google Forms URL with ?usp=pp_url&entry.XXXXX={ref}
        'google_review_url'   => '',
        // Booking rules
        'booking_advance_min' => 60,   // minutes — minimum advance notice
        'booking_advance_max' => 60,   // days — how far ahead guests can book
        'party_size_min'      => 1,
        'party_size_max'      => 20,
        'default_duration'    => 120,  // minutes per reservation slot
        // Time slots (comma-separated HH:MM in venue local time)
        'slots_lunch'         => '11:30,12:00,12:30,13:00,13:30,14:00',
        'slots_dinner'        => '17:30,18:00,18:30,19:00,19:30,20:00,20:30,21:00',
        'slots_late'          => '21:30,22:00,22:30',
        'slots_enabled'       => 'lunch,dinner',   // which groups are active
        // Floor status thresholds (minutes seated before colour change)
        'status_orange_min'   => 45,
        'status_red_min'      => 90,
        // Occasion types (pipe-separated label:slug pairs)
        'occasion_types'      => 'Dinner:dinner|Bar:bar|Birthday:birthday|Anniversary:anniversary|Corporate:corporate|Event:event|Custom:custom',
        // Reminder email timing
        'reminder_24h'        => true,  // send 24h before
        'reminder_4h'         => true,  // send 4h before
        'feedback_delay_min'  => 120,   // send feedback email N minutes after reservation ends
        // Cancellation policy copy
        'cancel_policy_text'  => 'Reservations may be cancelled up to 24 hours before the booking time.',
        // Public booking behaviour
        'auto_confirm_public' => true,  // auto-confirm public bookings; set false for manual review
        // Shift report email
        'shift_report_enabled' => false,
        'shift_report_email'   => '',
        'shift_report_time'    => '22:00',  // venue local time (GMT+7) to send the report
    ];

    public static function maybe_set_defaults(): void {
        $existing = get_option( 'thr_settings', [] );
        if ( empty( $existing ) ) {
            update_option( 'thr_settings', self::$defaults, false );
        } else {
            // Merge in any new defaults added by plugin updates
            $merged = array_merge( self::$defaults, $existing );
            update_option( 'thr_settings', $merged, false );
        }
    }

    public static function get( string $key, $fallback = null ) {
        $settings = get_option( 'thr_settings', [] );
        return $settings[ $key ] ?? self::$defaults[ $key ] ?? $fallback;
    }

    public static function all(): array {
        return array_merge( self::$defaults, get_option( 'thr_settings', [] ) );
    }

    public static function update( array $data ): bool {
        $current  = self::all();
        $allowed  = array_keys( self::$defaults );
        $filtered = array_intersect_key( $data, array_flip( $allowed ) );
        return update_option( 'thr_settings', array_merge( $current, $filtered ), false );
    }

    public static function time_slots(): array {
        $enabled = array_filter( explode( ',', self::get( 'slots_enabled' ) ) );
        $all     = [];
        foreach ( $enabled as $group ) {
            $key   = "slots_{$group}";
            $times = array_filter( explode( ',', self::get( $key, '' ) ) );
            foreach ( $times as $t ) {
                $all[] = trim( $t );
            }
        }
        sort( $all );
        return array_unique( $all );
    }

    public static function occasion_types(): array {
        $raw  = self::get( 'occasion_types' );
        $out  = [];
        foreach ( explode( '|', $raw ) as $pair ) {
            [ $label, $slug ] = array_pad( explode( ':', $pair, 2 ), 2, '' );
            if ( $slug ) $out[ trim( $slug ) ] = trim( $label );
        }
        return $out;
    }
}
