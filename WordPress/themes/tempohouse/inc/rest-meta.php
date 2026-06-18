<?php
/**
 * TEMPO House — REST API meta registration
 *
 * Exposes all ACF post meta fields via the WordPress REST API.
 * Required for:
 *   - AI-to-WordPress content creation pipeline (POST to /wp-json/wp/v2/posts)
 *   - Headless or decoupled consumers reading event/post data
 *   - Gutenberg editor accessing meta in JS context
 *
 * Without this, ACF field values are stored as post meta but return {} from
 * the REST API because WordPress hides non-registered meta by default.
 *
 * Auth: Requests must include an Application Password in the Authorization header.
 * Route: POST /wp-json/wp/v2/posts  (standard Posts tagged 'event')
 *        POST /wp-json/wp/v2/event  (Event CPT)
 */

function tempohouse_register_rest_meta() {

    // ── Fields shared across both post types (same meta key, different ACF field key)
    $shared = [
        'event_category'  => 'string',
        'event_time'      => 'string',
        'event_interior'  => 'string',
        'event_media_type'=> 'string',
        'event_media_id'  => 'integer',
    ];

    // ── Event CPT — fields from group_event_details
    $event_cpt_fields = array_merge( $shared, [
        'event_month'    => 'string',  // Schedule label (e.g. "Monthly", "Weekly")
        'event_is_active'=> 'boolean', // Show on homepage carousel
        'event_href'     => 'string',  // External link URL
    ] );

    foreach ( $event_cpt_fields as $key => $type ) {
        register_post_meta( 'event', $key, [
            'show_in_rest'  => true,
            'single'        => true,
            'type'          => $type,
            'auth_callback' => fn() => current_user_can( 'edit_posts' ),
        ] );
    }

    // ── Standard Posts — fields from group_event_post_details
    $post_fields = array_merge( $shared, [
        'event_date'       => 'string',  // ACF date_picker returns Ymd string
        'event_end_date'   => 'string',
        'event_recurrence' => 'string',  // one-time|weekly|monthly|ongoing
        'event_poster'     => 'integer', // WP attachment ID
        'event_price'      => 'string',  // e.g. "Free", "150,000 VND"
        'event_ticket_url' => 'string',
    ] );

    foreach ( $post_fields as $key => $type ) {
        register_post_meta( 'post', $key, [
            'show_in_rest'  => true,
            'single'        => true,
            'type'          => $type,
            'auth_callback' => fn() => current_user_can( 'edit_posts' ),
        ] );
    }
}
add_action( 'init', 'tempohouse_register_rest_meta' );
