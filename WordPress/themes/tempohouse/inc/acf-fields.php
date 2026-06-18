<?php
if ( ! function_exists( 'acf_add_local_field_group' ) ) {
    return;
}

// ── Event Details ─────────────────────────────────────────────────────────

acf_add_local_field_group([
    'key'      => 'group_event_details',
    'title'    => 'Event Details',
    'location' => [[[ 'param' => 'post_type', 'operator' => '==', 'value' => 'event' ]]],
    'fields'   => [
        [
            'key'         => 'field_event_category',
            'name'        => 'event_category',
            'label'       => 'Category',
            'type'        => 'text',
            'placeholder' => 'Live Music',
        ],
        [
            'key'         => 'field_event_month',
            'name'        => 'event_month',
            'label'       => 'Schedule',
            'type'        => 'text',
            'placeholder' => 'Monthly',
        ],
        [
            'key'         => 'field_event_time',
            'name'        => 'event_time',
            'label'       => 'Time',
            'type'        => 'text',
            'placeholder' => '20:00 – 23:00',
        ],
        [
            'key'           => 'field_event_interior',
            'name'          => 'event_interior',
            'label'         => 'Card Colour',
            'type'          => 'select',
            'choices'       => [
                'dark'  => 'Dark Ink',
                'sand'  => 'Warm Sand',
                'cream' => 'Soft Cream',
            ],
            'default_value' => 'dark',
        ],
        [
            'key'           => 'field_event_is_active',
            'name'          => 'event_is_active',
            'label'         => 'Show on Homepage',
            'type'          => 'true_false',
            'default_value' => 0,
            'message'       => 'Display in the What\'s On carousel',
        ],
        [
            'key'         => 'field_event_href',
            'name'        => 'event_href',
            'label'       => 'Link URL',
            'type'        => 'url',
            'placeholder' => 'https://',
        ],
        [
            'key'           => 'field_event_media_type',
            'name'          => 'event_media_type',
            'label'         => 'Media Type',
            'type'          => 'select',
            'choices'       => [
                'none'  => 'None',
                'image' => 'Image',
                'video' => 'Video',
            ],
            'default_value' => 'none',
        ],
        [
            'key'               => 'field_event_media_id',
            'name'              => 'event_media_id',
            'label'             => 'Media',
            'type'              => 'image',
            'return_format'     => 'id',
            'conditional_logic' => [[[ 'field' => 'field_event_media_type', 'operator' => '!=', 'value' => 'none' ]]],
        ],
    ],
]);

// ── Event Post Details (standard Posts tagged 'event') ───────────────────
// Field keys use 'field_ep_*' prefix to avoid collision with CPT group.
// Field *names* intentionally match CPT names where possible so get_field()
// works the same way in templates regardless of post type.

acf_add_local_field_group([
    'key'      => 'group_event_post_details',
    'title'    => 'Event Details',
    'location' => [[[ 'param' => 'post_type', 'operator' => '==', 'value' => 'post' ]]],
    'fields'   => [
        [
            'key'          => 'field_ep_category',
            'name'         => 'event_category',
            'label'        => 'Category',
            'type'         => 'text',
            'placeholder'  => 'Live Music',
            'instructions' => 'e.g. Live Music, Exhibition, Workshop, Private Dining',
        ],
        [
            'key'            => 'field_ep_date',
            'name'           => 'event_date',
            'label'          => 'Event Date',
            'type'           => 'date_picker',
            'display_format' => 'd/m/Y',
            'return_format'  => 'Ymd',
            'instructions'   => 'Leave blank for ongoing or recurring events without a fixed date.',
        ],
        [
            'key'            => 'field_ep_end_date',
            'name'           => 'event_end_date',
            'label'          => 'End Date',
            'type'           => 'date_picker',
            'display_format' => 'd/m/Y',
            'return_format'  => 'Ymd',
            'instructions'   => 'For multi-day events — leave blank for single-day.',
        ],
        [
            'key'         => 'field_ep_time',
            'name'        => 'event_time',
            'label'       => 'Time',
            'type'        => 'text',
            'placeholder' => '20:00 – 23:00',
        ],
        [
            'key'           => 'field_ep_recurrence',
            'name'          => 'event_recurrence',
            'label'         => 'Recurrence',
            'type'          => 'select',
            'choices'       => [
                ''         => '— Select —',
                'one-time' => 'One Night Only',
                'weekly'   => 'Weekly',
                'monthly'  => 'Monthly',
                'ongoing'  => 'Ongoing',
            ],
            'default_value' => '',
            'allow_null'    => 1,
        ],
        [
            'key'           => 'field_ep_interior',
            'name'          => 'event_interior',
            'label'         => 'Card Colour',
            'type'          => 'select',
            'choices'       => [
                'dark'  => 'Dark Ink',
                'sand'  => 'Warm Sand',
                'cream' => 'Soft Cream',
            ],
            'default_value' => 'dark',
            'instructions'  => 'Background colour of the event card on the What\'s On page.',
        ],
        [
            'key'           => 'field_ep_poster',
            'name'          => 'event_poster',
            'label'         => 'Poster Image',
            'type'          => 'image',
            'return_format' => 'id',
            'preview_size'  => 'medium',
            'instructions'  => 'Hero image on the event detail page. Landscape (1920×1080) preferred.',
        ],
        [
            'key'          => 'field_ep_price',
            'name'         => 'event_price',
            'label'        => 'Admission',
            'type'         => 'text',
            'placeholder'  => 'Free',
            'instructions' => 'e.g. Free, 150,000 VND, Ticketed — shows in the detail page meta bar.',
        ],
        [
            'key'          => 'field_ep_ticket_url',
            'name'         => 'event_ticket_url',
            'label'        => 'Ticket / RSVP Link',
            'type'         => 'url',
            'placeholder'  => 'https://',
            'instructions' => 'Optional — if set, a "Get Tickets" button appears on the detail page.',
        ],
        [
            'key'           => 'field_ep_media_type',
            'name'          => 'event_media_type',
            'label'         => 'Card Artwork Type',
            'type'          => 'select',
            'choices'       => [
                'none'  => 'None (show category text)',
                'image' => 'Image',
                'video' => 'Video (MP4)',
            ],
            'default_value' => 'none',
            'instructions'  => 'Media shown inside the card on the What\'s On page.',
        ],
        [
            'key'               => 'field_ep_media_id',
            'name'              => 'event_media_id',
            'label'             => 'Card Artwork',
            'type'              => 'image',
            'return_format'     => 'id',
            'preview_size'      => 'thumbnail',
            'conditional_logic' => [[[ 'field' => 'field_ep_media_type', 'operator' => '!=', 'value' => 'none' ]]],
            'instructions'      => 'Portrait image for the card artwork (600×800px recommended).',
        ],
    ],
]);

// ── Space Frame ───────────────────────────────────────────────────────────

acf_add_local_field_group([
    'key'      => 'group_space_frame',
    'title'    => 'Space Frame',
    'location' => [[[ 'param' => 'page_template', 'operator' => '==', 'value' => 'page-templates/space-frame.php' ]]],
    'fields'   => [
        [
            'key'         => 'field_space_mode',
            'name'        => 'space_mode',
            'label'       => 'Mode Label',
            'type'        => 'text',
            'placeholder' => 'Day',
        ],
        [
            'key'         => 'field_space_time',
            'name'        => 'space_time',
            'label'       => 'Hours',
            'type'        => 'text',
            'placeholder' => '08:00 – 17:00',
        ],
        [
            'key'         => 'field_space_frame_num',
            'name'        => 'space_frame_num',
            'label'       => 'Frame Number',
            'type'        => 'text',
            'placeholder' => '01',
        ],
        [
            'key'           => 'field_space_speed',
            'name'          => 'space_speed',
            'label'         => 'Parallax Speed',
            'type'          => 'number',
            'default_value' => -0.07,
            'step'          => 0.01,
        ],
        [
            'key'           => 'field_space_artwork_bg',
            'name'          => 'space_artwork_bg',
            'label'         => 'Artwork Background',
            'type'          => 'select',
            'choices'       => [
                'cream-dark' => 'Cream',
                'ink'        => 'Ink Black',
                'sand'       => 'Sand',
            ],
            'default_value' => 'cream-dark',
        ],
        [
            'key'           => 'field_space_frame_color',
            'name'          => 'space_frame_color',
            'label'         => 'Frame Colour',
            'type'          => 'select',
            'choices'       => [
                'terracotta' => 'Terracotta',
                'dark'       => 'Dark',
                'sage'       => 'Sage',
            ],
            'default_value' => 'terracotta',
        ],
        [
            'key'         => 'field_space_cta_text',
            'name'        => 'space_cta_text',
            'label'       => 'CTA Label',
            'type'        => 'text',
            'placeholder' => 'Explore the Café',
        ],
        [
            'key'           => 'field_space_order',
            'name'          => 'space_order',
            'label'         => 'Display Order',
            'type'          => 'number',
            'default_value' => 1,
            'min'           => 1,
            'max'           => 3,
        ],
    ],
]);
