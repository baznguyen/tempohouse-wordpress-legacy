<?php
defined( 'ABSPATH' ) || exit;

class THR_Email {

    // ── Public send methods ────────────────────────────────────────────────────

    public static function send_confirmation( object $reservation ): bool {
        $subject = self::localise(
            $reservation->diner_lang,
            'Your reservation at TEMPO House is confirmed — ' . $reservation->reference_code,
            'Đặt bàn của bạn tại TEMPO House đã được xác nhận — ' . $reservation->reference_code
        );
        return self::send( $reservation->diner_email, $subject, self::render( 'confirmation', $reservation ) );
    }

    public static function send_booking_pending( object $reservation ): bool {
        $subject = self::localise(
            $reservation->diner_lang,
            'We received your booking request — ' . $reservation->reference_code,
            'Chúng tôi đã nhận yêu cầu đặt bàn của bạn — ' . $reservation->reference_code
        );
        return self::send( $reservation->diner_email, $subject, self::render( 'pending', $reservation ) );
    }

    public static function send_reminder( object $reservation, string $type = '24h' ): bool {
        $when = $type === '2h'
            ? self::localise( $reservation->diner_lang, 'in 2 hours',  'trong 2 giờ nữa' )
            : self::localise( $reservation->diner_lang, 'tomorrow',     'ngày mai' );
        $subject = self::localise(
            $reservation->diner_lang,
            "Reminder: Your TEMPO House reservation is $when",
            "Nhắc nhở: Bàn của bạn tại TEMPO House vào $when"
        );
        return self::send( $reservation->diner_email, $subject, self::render( 'reminder', $reservation, compact( 'when' ) ) );
    }

    public static function send_cancellation( object $reservation ): bool {
        $subject = self::localise(
            $reservation->diner_lang,
            'Your reservation at TEMPO House has been cancelled — ' . $reservation->reference_code,
            'Đặt bàn của bạn tại TEMPO House đã bị hủy — ' . $reservation->reference_code
        );
        return self::send( $reservation->diner_email, $subject, self::render( 'cancellation', $reservation ) );
    }

    public static function send_feedback( object $reservation ): bool {
        $subject = self::localise(
            $reservation->diner_lang,
            'How was your evening at TEMPO House?',
            'Buổi tối của bạn tại TEMPO House như thế nào?'
        );
        return self::send( $reservation->diner_email, $subject, self::render( 'feedback', $reservation ) );
    }

    public static function send_waitlist_joined( object $entry ): bool {
        $subject = self::localise(
            $entry->diner_lang,
            'You\'re on the TEMPO House waitlist — ' . $entry->reference_code,
            'Bạn đã vào danh sách chờ của TEMPO House — ' . $entry->reference_code
        );
        return self::send( $entry->diner_email, $subject, self::render_waitlist( 'waitlist-joined', $entry ) );
    }

    public static function send_waitlist_notification( object $entry ): bool {
        $subject = self::localise(
            $entry->diner_lang,
            'Good news — a table may be available at TEMPO House',
            'Tin vui — có thể có bàn trống tại TEMPO House'
        );
        return self::send( $entry->diner_email, $subject, self::render_waitlist( 'waitlist-notify', $entry ) );
    }

    public static function send_shift_report( string $to, string $date_local ): bool {
        global $wpdb;
        $table    = THR_Database::t( 'reservations' );
        $tags_t   = THR_Database::t( 'tags' );
        $pivot_t  = THR_Database::t( 'reservation_tags' );

        $rows = $wpdb->get_results( $wpdb->prepare(
            "SELECT r.*, DATE_ADD(r.reservation_dt, INTERVAL 7 HOUR) AS dt_local,
                    GROUP_CONCAT(t.name ORDER BY t.name SEPARATOR ', ') AS tag_names
             FROM $table r
             LEFT JOIN $pivot_t rt ON rt.reservation_id = r.id
             LEFT JOIN $tags_t t ON t.id = rt.tag_id
             WHERE DATE(DATE_ADD(r.reservation_dt, INTERVAL 7 HOUR)) = %s
               AND r.status NOT IN ('cancelled','no_show')
             GROUP BY r.id
             ORDER BY r.reservation_dt ASC",
            $date_local
        ) );

        $subject  = '[TEMPO House] Shift Report — ' . $date_local;
        $body     = self::render_shift_report( $date_local, $rows );
        $venue_name   = THR_Settings::get( 'venue_name', 'TEMPO House' );
        $from_name    = THR_Settings::get( 'email_from_name', $venue_name );
        $from_email   = THR_Settings::get( 'email_from_address' ) ?: get_option( 'admin_email' );
        $headers      = [
            'Content-Type: text/html; charset=UTF-8',
            "From: {$from_name} <{$from_email}>",
        ];
        return wp_mail( $to, $subject, $body, $headers );
    }

    // ── Event enquiry emails ───────────────────────────────────────────────────

    public static function send_event_enquiry_auto_reply( object $enquiry ): bool {
        $subject = 'Thank you for your event enquiry — ' . $enquiry->reference_code;
        $body    = self::render_event_enquiry_reply( $enquiry );
        return self::send( $enquiry->contact_email, $subject, $body );
    }

    public static function send_event_enquiry_notification( object $enquiry ): bool {
        $venue_email = THR_Settings::get( 'venue_email' ) ?: get_option( 'admin_email' );
        $subject     = '[TEMPO House] New Event Enquiry — ' . $enquiry->reference_code;
        $body        = self::render_event_enquiry_reply( $enquiry, true );
        return self::send( $venue_email, $subject, $body );
    }

    private static function render_event_enquiry_reply( object $enquiry, bool $internal = false ): string {
        $file = THR_PLUGIN_DIR . 'templates/emails/event-enquiry-reply.php';
        if ( ! file_exists( $file ) ) return '';

        $logo_url    = THR_Settings::get( 'email_logo_url' );
        $accent      = THR_Settings::get( 'email_accent_color', '#DDAA62' );
        $venue_name  = THR_Settings::get( 'venue_name', 'TEMPO House' );
        $venue_address = THR_Settings::get( 'venue_address' );

        $v = compact( 'enquiry', 'logo_url', 'accent', 'venue_name', 'venue_address', 'internal' );

        ob_start();
        extract( $v, EXTR_SKIP );
        include $file;
        return ob_get_clean();
    }

    // ── Private helpers ────────────────────────────────────────────────────────

    private static function send( string $to, string $subject, string $body ): bool {
        $from_name  = THR_Settings::get( 'email_from_name', 'TEMPO House' );
        $from_email = THR_Settings::get( 'email_from_address' ) ?: get_option( 'admin_email' );
        $reply_to   = THR_Settings::get( 'email_reply_to' ) ?: $from_email;

        $headers = [
            "Content-Type: text/html; charset=UTF-8",
            "From: {$from_name} <{$from_email}>",
            "Reply-To: {$reply_to}",
        ];

        return wp_mail( $to, $subject, $body, $headers );
    }

    private static function render( string $template, object $reservation, array $extra = [] ): string {
        $file = THR_PLUGIN_DIR . "templates/emails/{$template}.php";
        if ( ! file_exists( $file ) ) return '';

        // Build vars available inside template
        $v = self::build_vars( $reservation, $extra );

        ob_start();
        extract( $v, EXTR_SKIP );
        include $file;
        return ob_get_clean();
    }

    private static function build_vars( object $r, array $extra ): array {
        $date_local = $r->reservation_dt ?? '';
        // Convert UTC to venue local for display (GMT+7)
        if ( $date_local ) {
            $ts         = strtotime( $date_local . ' UTC' );
            $date_local = date( 'l, F j, Y', $ts + 7 * 3600 );
            $time_local = date( 'g:ia', $ts + 7 * 3600 );
        } else {
            $date_local = '';
            $time_local = '';
        }

        $cancel_url     = home_url( '/reservations/cancel/?ref=' . urlencode( $r->reference_code ) );
        $feedback_url   = self::build_feedback_url( $r );
        $google_rev_url = THR_Settings::get( 'google_review_url' );
        $logo_url       = THR_Settings::get( 'email_logo_url' );
        $accent         = THR_Settings::get( 'email_accent_color', '#DDAA62' );
        $venue_name     = THR_Settings::get( 'venue_name', 'TEMPO House' );
        $venue_address  = THR_Settings::get( 'venue_address' );
        $venue_phone    = THR_Settings::get( 'venue_phone' );
        $venue_email    = THR_Settings::get( 'venue_email' );
        $policy         = THR_Settings::get( 'cancel_policy_text' );

        return array_merge( [
            'r'             => $r,
            'date_local'    => $date_local,
            'time_local'    => $time_local,
            'cancel_url'    => $cancel_url,
            'feedback_url'  => $feedback_url,
            'google_rev_url'=> $google_rev_url,
            'logo_url'      => $logo_url,
            'accent'        => $accent,
            'venue_name'    => $venue_name,
            'venue_address' => $venue_address,
            'venue_phone'   => $venue_phone,
            'venue_email'   => $venue_email,
            'policy'        => $policy,
        ], $extra );
    }

    private static function build_feedback_url( object $r ): string {
        $base = THR_Settings::get( 'feedback_form_url' );
        if ( ! $base ) return '';
        // Append reference code as pre-fill param
        $sep = strpos( $base, '?' ) !== false ? '&' : '?';
        return $base . $sep . 'ref=' . urlencode( $r->reference_code );
    }

    private static function render_waitlist( string $template, object $entry, array $extra = [] ): string {
        $file = THR_PLUGIN_DIR . "templates/emails/{$template}.php";
        if ( ! file_exists( $file ) ) return '';

        $logo_url    = THR_Settings::get( 'email_logo_url' );
        $accent      = THR_Settings::get( 'email_accent_color', '#DDAA62' );
        $venue_name  = THR_Settings::get( 'venue_name', 'TEMPO House' );
        $venue_address = THR_Settings::get( 'venue_address' );
        $booking_url = home_url( '/reservations/' );

        $v = array_merge( compact( 'entry', 'logo_url', 'accent', 'venue_name', 'venue_address', 'booking_url' ), $extra );

        ob_start();
        extract( $v, EXTR_SKIP );
        include $file;
        return ob_get_clean();
    }

    private static function render_shift_report( string $date_local, array $rows ): string {
        $file = THR_PLUGIN_DIR . 'templates/emails/shift-report.php';
        if ( ! file_exists( $file ) ) {
            // Fallback plain-text style HTML if template missing
            return '<p>No template found.</p>';
        }
        $covers = array_sum( array_column( $rows, 'party_size' ) );
        $vip_count = count( array_filter( $rows, fn( $r ) => $r->is_vip ) );
        $accent  = THR_Settings::get( 'email_accent_color', '#DDAA62' );
        $logo_url  = THR_Settings::get( 'email_logo_url' );
        $venue_name = THR_Settings::get( 'venue_name', 'TEMPO House' );
        ob_start();
        extract( compact( 'rows', 'date_local', 'covers', 'vip_count', 'accent', 'logo_url', 'venue_name' ), EXTR_SKIP );
        include $file;
        return ob_get_clean();
    }

    private static function localise( string $lang, string $en, string $vi ): string {
        return $lang === 'vi' ? $vi : $en;
    }
}
