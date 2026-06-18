<?php
function tempohouse_register_event_cpt() {
    $labels = [
        'name'               => __( 'Events', 'tempohouse' ),
        'singular_name'      => __( 'Event', 'tempohouse' ),
        'menu_name'          => __( 'Events', 'tempohouse' ),
        'add_new'            => __( 'Add New Event', 'tempohouse' ),
        'add_new_item'       => __( 'Add New Event', 'tempohouse' ),
        'edit_item'          => __( 'Edit Event', 'tempohouse' ),
        'view_item'          => __( 'View Event', 'tempohouse' ),
        'all_items'          => __( 'All Events', 'tempohouse' ),
        'search_items'       => __( 'Search Events', 'tempohouse' ),
        'not_found'          => __( 'No events found.', 'tempohouse' ),
        'not_found_in_trash' => __( 'No events found in Trash.', 'tempohouse' ),
    ];

    register_post_type( 'event', [
        'labels'          => $labels,
        'public'          => true,
        'has_archive'     => true,
        'rewrite'         => [ 'slug' => 'whats-on' ],
        'supports'        => [ 'title', 'thumbnail', 'excerpt' ],
        'menu_icon'       => 'dashicons-calendar-alt',
        'show_in_rest'    => true,
        'capability_type' => 'post',
        'menu_position'   => 5,
    ] );
}
add_action( 'init', 'tempohouse_register_event_cpt' );

function tempohouse_event_flush_rewrite() {
    tempohouse_register_event_cpt();
    flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'tempohouse_event_flush_rewrite' );

// Route standard Posts tagged 'event' to the custom event detail template.
// Route the CPT 'event' archive (/whats-on/) to the What's On page template.
// Both use template_include because WordPress doesn't natively support tag-based
// single templates or CPT archive overrides without this filter.
add_filter( 'template_include', function ( $template ) {
    // Single Post tagged 'event' → custom single template.
    if ( is_singular( 'post' ) && has_tag( 'event' ) ) {
        $custom = get_template_directory() . '/single-event-post.php';
        if ( file_exists( $custom ) ) {
            return $custom;
        }
    }
    // CPT event archive /whats-on/ → What's On gallery template.
    // The CPT archive steals this URL from any WordPress Page with the same slug.
    if ( is_post_type_archive( 'event' ) || is_page( 'whats-on' ) ) {
        $custom = get_template_directory() . '/page-templates/page-whats-on.php';
        if ( file_exists( $custom ) ) {
            return $custom;
        }
    }
    return $template;
} );
