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
