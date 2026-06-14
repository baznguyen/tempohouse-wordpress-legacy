<?php
defined( 'ABSPATH' ) || exit;

class THR_API {

    public function init(): void {
        add_action( 'rest_api_init', [ $this, 'register_routes' ] );
        add_filter( 'rest_pre_serve_request', [ $this, 'add_cors_headers' ], 10, 4 );
    }

    public function register_routes(): void {
        ( new THR_API_Reservations() )->register();
        ( new THR_API_Floors() )->register();
        ( new THR_API_Furniture() )->register();
        ( new THR_API_Tags() )->register();
        ( new THR_API_Availability() )->register();
        ( new THR_API_Blocks() )->register();
        ( new THR_API_Waitlist() )->register();
        ( new THR_API_Reports() )->register();
        ( new THR_API_Settings() )->register();
    }

    // Allow the Next.js frontend and booking subdomain to call the API
    public function add_cors_headers( $served, $result, $request, $server ): bool {
        $allowed_origins = [
            'https://tempohouse.com.vn',
            'https://reservations.tempohouse.com.vn',
            'http://localhost:3000',
        ];
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        if ( in_array( $origin, $allowed_origins, true ) ) {
            header( "Access-Control-Allow-Origin: $origin" );
            header( 'Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, OPTIONS' );
            header( 'Access-Control-Allow-Headers: Authorization, Content-Type, X-WP-Nonce' );
            header( 'Access-Control-Allow-Credentials: true' );
        }
        return $served;
    }

    // ── Shared helpers used by all sub-API classes ─────────────────────────────

    public static function now_utc(): string {
        return current_time( 'mysql', true );
    }

    public static function error( string $code, string $message, int $status = 400 ): WP_Error {
        return new WP_Error( $code, $message, [ 'status' => $status ] );
    }

    public static function auth_check( string $cap ): bool|WP_Error {
        if ( ! is_user_logged_in() ) {
            return self::error( 'thr_unauthenticated', 'Authentication required.', 401 );
        }
        if ( ! current_user_can( $cap ) ) {
            return self::error( 'thr_forbidden', 'You do not have permission for this action.', 403 );
        }
        return true;
    }
}
