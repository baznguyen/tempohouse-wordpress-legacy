<?php
defined( 'ABSPATH' ) || exit;

class THR_API_Reports {

    public function register(): void {
        $ns = THR_REST_NS;

        // Pre-shift report for a given date (default: today in venue TZ)
        register_rest_route( $ns, '/reports/shift', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'shift_report' ],
            'permission_callback' => fn() => current_user_can( 'thr_view_reports' ),
        ] );

        // Summary stats for the dashboard
        register_rest_route( $ns, '/reports/dashboard', [
            'methods'             => 'GET',
            'callback'            => [ $this, 'dashboard' ],
            'permission_callback' => fn() => current_user_can( 'thr_view_reservations' ),
        ] );
    }

    public function shift_report( WP_REST_Request $req ): WP_REST_Response {
        global $wpdb;
        $table  = THR_Database::t( 'reservations' );
        // Date param in venue local time (YYYY-MM-DD), default today
        $date   = $req->get_param( 'date' ) ?: gmdate( 'Y-m-d', time() + 7 * 3600 );
        $shift  = $req->get_param( 'shift' ) ?: 'all';  // all | lunch | dinner | late

        // Shift time ranges in local venue time (compared against DATE_ADD +7h)
        $shift_ranges = [
            'lunch'  => [ '10:00', '16:00' ],
            'dinner' => [ '16:00', '22:00' ],
            'late'   => [ '21:00', '02:00' ],  // crosses midnight — handled separately
        ];

        $where  = $wpdb->prepare(
            "DATE(DATE_ADD(reservation_dt, INTERVAL 7 HOUR)) = %s
             AND status NOT IN ('cancelled','no_show')", $date
        );

        if ( $shift !== 'all' && isset( $shift_ranges[ $shift ] ) ) {
            [ $from, $to ] = $shift_ranges[ $shift ];
            $where .= $wpdb->prepare(
                " AND TIME(DATE_ADD(reservation_dt, INTERVAL 7 HOUR)) >= %s
                  AND TIME(DATE_ADD(reservation_dt, INTERVAL 7 HOUR)) < %s",
                $from, $to
            );
        }

        $rows = $wpdb->get_results(
            "SELECT r.*,
                    DATE_ADD(r.reservation_dt, INTERVAL 7 HOUR) AS reservation_dt_local,
                    GROUP_CONCAT(t.name ORDER BY t.name SEPARATOR ', ') AS tag_names
             FROM {$table} r
             LEFT JOIN " . THR_Database::t( 'reservation_tags' ) . " rt ON rt.reservation_id = r.id
             LEFT JOIN " . THR_Database::t( 'tags' ) . " t ON t.id = rt.tag_id
             WHERE $where
             GROUP BY r.id
             ORDER BY r.reservation_dt ASC"
        );

        $total_covers = array_sum( array_map( fn( $r ) => (int) $r->party_size, $rows ) );
        $vip_count    = count( array_filter( $rows, fn( $r ) => $r->is_vip ) );
        $by_status    = array_count_values( array_column( $rows, 'status' ) );

        return new WP_REST_Response( [
            'date'         => $date,
            'shift'        => $shift,
            'generated_at' => THR_API::now_utc(),
            'summary'      => [
                'total_reservations' => count( $rows ),
                'total_covers'       => $total_covers,
                'vip_count'          => $vip_count,
                'by_status'          => $by_status,
            ],
            'reservations' => array_map( fn( $r ) => [
                'id'               => (int) $r->id,
                'reference_code'   => $r->reference_code,
                'status'           => $r->status,
                'time_local'       => substr( $r->reservation_dt_local, 11, 5 ),
                'diner_name'       => $r->diner_name,
                'party_size'       => (int) $r->party_size,
                'occasion'         => $r->occasion,
                'area_label'       => $r->area_label,
                'is_vip'           => (bool) $r->is_vip,
                'tags'             => $r->tag_names ?: '',
                'notes_diner'      => $r->notes_diner,
                'notes_internal'   => $r->notes_internal,
                'diner_phone'      => $r->diner_phone,
            ], $rows ),
        ] );
    }

    public function dashboard( WP_REST_Request $req ): WP_REST_Response {
        global $wpdb;
        $table = THR_Database::t( 'reservations' );
        $today = gmdate( 'Y-m-d', time() + 7 * 3600 );

        $today_count = (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM $table
             WHERE DATE(DATE_ADD(reservation_dt, INTERVAL 7 HOUR)) = %s
               AND status NOT IN ('cancelled','no_show')", $today
        ) );

        $today_covers = (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT SUM(party_size) FROM $table
             WHERE DATE(DATE_ADD(reservation_dt, INTERVAL 7 HOUR)) = %s
               AND status NOT IN ('cancelled','no_show')", $today
        ) );

        $pending_count = (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM $table WHERE status = 'pending'"
        );

        $week_start = gmdate( 'Y-m-d', time() + 7 * 3600 );
        $week_end   = gmdate( 'Y-m-d', time() + 7 * 3600 + 7 * 86400 );
        $week_count = (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM $table
             WHERE DATE(DATE_ADD(reservation_dt, INTERVAL 7 HOUR)) BETWEEN %s AND %s
               AND status NOT IN ('cancelled','no_show')", $week_start, $week_end
        ) );

        $upcoming = $wpdb->get_results( $wpdb->prepare(
            "SELECT id, reference_code, diner_name, party_size, occasion, status, is_vip,
                    DATE_ADD(reservation_dt, INTERVAL 7 HOUR) AS reservation_dt_local
             FROM $table
             WHERE DATE(DATE_ADD(reservation_dt, INTERVAL 7 HOUR)) = %s
               AND status IN ('pending','confirmed','seated')
             ORDER BY reservation_dt ASC
             LIMIT 20", $today
        ) );

        return new WP_REST_Response( [
            'today_date'    => $today,
            'today_count'   => $today_count,
            'today_covers'  => $today_covers ?: 0,
            'pending_count' => $pending_count,
            'week_count'    => $week_count,
            'upcoming'      => $upcoming,
        ] );
    }
}
