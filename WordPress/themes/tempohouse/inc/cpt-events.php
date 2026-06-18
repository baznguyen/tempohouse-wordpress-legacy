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
        'menu_icon'       => 'dashicons-calendar-alt',
        'show_in_rest'    => true,
        'capability_type' => 'post',
        'menu_position'   => 5,

        // 'editor' enables the Gutenberg block editor content area alongside ACF sidebar fields.
        // Without it, event posts show ACF fields only — no body content possible.
        'supports' => [ 'title', 'thumbnail', 'excerpt', 'editor' ],

        // Locked template: team can edit block content but cannot add or remove blocks.
        // ACF sidebar fields (date, time, price, card colour, ticket URL) live in the sidebar.
        // This block area is for the rich event description shown on the detail page.
        'template_lock' => 'insert',
        'template'      => [

            // Category eyebrow — mirrors the ACF event_category field for the detail page header
            [ 'core/paragraph', [
                'placeholder' => 'Category label — e.g. Live Music, Exhibition, Workshop',
                'style'       => [
                    'typography' => [
                        'fontSize'      => '0.75rem',
                        'letterSpacing' => '0.15em',
                        'textTransform' => 'uppercase',
                    ],
                    'color' => [ 'text' => '#7c3b3b' ],
                ],
            ]],

            // Main event description — what will guests experience?
            [ 'core/paragraph', [
                'placeholder' => 'Describe the event — the atmosphere, performers, what to expect. Two to three sentences.',
            ]],

            // Second description paragraph for longer copy
            [ 'core/paragraph', [
                'placeholder' => 'Additional context, artist notes, or programme details (optional — leave empty if not needed).',
            ]],

            // Visual separator before the detail meta row
            [ 'core/separator', [
                'backgroundColor' => 'sand',
            ]],

            // Detail row: date / time / admission — quick-scan meta for the reader
            [ 'core/group', [
                'layout' => [
                    'type'           => 'flex',
                    'flexWrap'       => 'wrap',
                    'justifyContent' => 'left',
                ],
                'style' => [ 'spacing' => [ 'blockGap' => '2rem' ] ],
            ], [
                [ 'core/paragraph', [
                    'placeholder' => '📅  Date: e.g. Saturday 19 July 2026',
                    'style'       => [ 'typography' => [ 'fontSize' => '0.875rem' ] ],
                ]],
                [ 'core/paragraph', [
                    'placeholder' => '🕗  Time: e.g. 20:00 – 23:00',
                    'style'       => [ 'typography' => [ 'fontSize' => '0.875rem' ] ],
                ]],
                [ 'core/paragraph', [
                    'placeholder' => '🎟  Admission: e.g. Free / 150,000 VND',
                    'style'       => [ 'typography' => [ 'fontSize' => '0.875rem' ] ],
                ]],
            ]],

            // CTA — ticket/RSVP button (URL comes from the ACF event_href / event_ticket_url field)
            [ 'core/buttons', [], [
                [ 'core/button', [
                    'text'            => 'Reserve a Seat',
                    'backgroundColor' => 'terracotta',
                    'textColor'       => 'cream',
                ]],
            ]],
        ],
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
