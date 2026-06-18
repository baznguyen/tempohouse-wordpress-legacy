<?php
/**
 * TEMPO House — Demo Event Posts Seed Script
 *
 * Creates the three starter events that match the homepage "What's On" carousel.
 * Events are standard WordPress Posts tagged 'event' and 'active'.
 * ACF fields are set via update_field() — requires ACF to be active.
 *
 * Run from the WordPress root via WP-CLI:
 *   wp eval-file wp-content/themes/tempohouse/../../scripts/seed-demo-events.php
 *
 * Or from the project root:
 *   wp --path=WordPress eval-file WordPress/scripts/seed-demo-events.php
 *
 * Safe to run multiple times — skips posts that already exist (matched by title + tag).
 *
 * To remove all seeded posts:
 *   wp --path=WordPress eval-file WordPress/scripts/seed-demo-events.php --remove
 */

if ( ! defined( 'ABSPATH' ) ) {
    die( "Must be run inside WordPress (via WP-CLI: wp eval-file ...)\n" );
}

$remove_mode = in_array( '--remove', $GLOBALS['argv'] ?? [], true );

// ── Event definitions ────────────────────────────────────────────────────────

$events = [

    // ── 1. TEMPO Sessions ────────────────────────────────────────────────────
    [
        'title'      => 'TEMPO Sessions',
        'slug'       => 'tempo-sessions',
        'excerpt'    => 'Our monthly live music programme. Local and regional acts who care about the room. Acoustic, jazz, ambient — it changes. The standard doesn\'t.',
        'content'    => '<!-- wp:paragraph -->
<p>TEMPO Sessions is our monthly live music programme at the bar. Doors open at 18:00. Music starts at 20:00 and runs until last call.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>We bring in local and regional acts who care about the room — acoustic sets, jazz trios, ambient electronic, and the occasional surprise. The playlist changes each month. The standard doesn\'t. Every act is invited because someone here heard something worth sharing.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>No support acts. No light show. No over-produced set list. Just the music, the room, and a good drink.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>The bar menu runs in full. Reserve a table if you\'d like to eat — the kitchen closes at 22:00. Walk-ins welcome at the bar.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3>What to expect</h3>
<!-- /wp:heading -->

<!-- wp:list -->
<ul>
<li>Live music 20:00 – 23:00</li>
<li>Full cocktail and wine menu</li>
<li>Kitchen open until 22:00</li>
<li>No cover charge — walk in at the bar, or reserve a table</li>
<li>Runs the second Saturday of each month</li>
</ul>
<!-- /wp:list -->

<!-- wp:paragraph -->
<p>Subscribe to the TEMPO Letter and you\'ll hear about the next artist before anyone else.</p>
<!-- /wp:paragraph -->',
        'category'   => 'Live Music',
        'date'       => '',             // Recurring — no fixed date
        'time'       => '20:00 – 23:00',
        'recurrence' => 'monthly',
        'interior'   => 'dark',
        'price'      => 'Free entry',
        'ticket_url' => '',
        'tags'       => [ 'event', 'active' ],
    ],

    // ── 2. Works on Paper — Current Exhibition ───────────────────────────────
    [
        'title'      => 'Works on Paper — Current Exhibition',
        'slug'       => 'works-on-paper-current-exhibition',
        'excerpt'    => 'Five emerging Vietnamese artists working in ink, graphite, and watercolour. Level 2 gallery. No ticket. Open during café and bar hours.',
        'content'    => '<!-- wp:paragraph -->
<p>The gallery on Level 2 is always open. The work changes.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Our current show brings together five emerging Vietnamese artists working in ink, graphite, and watercolour — all pieces made within the last two years, all available to acquire. The works span intimate studies and large-format pieces. Some are quiet. Some insist on being heard.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>We don\'t do guided tours or wall text explanations. The works earn their place on their own terms. Walk in, take your time, let the pieces find you.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3>Artists in this show</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Full artist notes and acquisition inquiries available at the bar. Ask for Lan or drop us a message via the contact page.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3>Visiting</h3>
<!-- /wp:heading -->

<!-- wp:list -->
<ul>
<li>Level 2, 218c Pasteur, District 3</li>
<li>Open daily 07:00 – 01:00 (café from 07:00, bar from 18:00)</li>
<li>No ticket required — walk in</li>
<li>Programme rotates. Subscribe to hear when the next show opens.</li>
</ul>
<!-- /wp:list -->

<!-- wp:paragraph -->
<p>The gallery is a different pace from the floors below it. That\'s intentional.</p>
<!-- /wp:paragraph -->',
        'category'   => 'Exhibition',
        'date'       => '',             // Ongoing
        'time'       => 'By programme',
        'recurrence' => 'ongoing',
        'interior'   => 'sand',
        'price'      => 'Free entry',
        'ticket_url' => '',
        'tags'       => [ 'event', 'active' ],
    ],

    // ── 3. Cocktail Masterclass ──────────────────────────────────────────────
    [
        'title'      => 'Cocktail Masterclass',
        'slug'       => 'cocktail-masterclass',
        'excerpt'    => 'Two hours with our lead bartender. Build three cocktails from scratch — technique, ratios, reasoning. Drink in hand the whole time. Eight guests max.',
        'content'    => '<!-- wp:paragraph -->
<p>Each month we open the bar early for a two-hour hands-on session with our lead bartender, Minh.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>You\'ll build three cocktails from scratch — a classic Negroni, a TEMPO original from our current menu, and one you pick. Minh covers ratios, technique, and the reasoning behind each choice: why this spirit, why this ratio, what the ice is actually doing. There\'s a drink in your hand the whole time.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>This isn\'t a show. It\'s a working session. You leave with muscle memory, not just a recipe.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3>What\'s included</h3>
<!-- /wp:heading -->

<!-- wp:list -->
<ul>
<li>Two-hour guided session with Minh</li>
<li>All spirits, mixers, and tools provided</li>
<li>Three cocktails you build yourself (and drink)</li>
<li>A printed card with ratios and technique notes to take home</li>
<li>15% off the bar menu for the rest of the evening</li>
</ul>
<!-- /wp:list -->

<!-- wp:heading {"level":3} -->
<h3>Booking</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Limited to eight guests per session. 150,000 VND per person. Runs the first Thursday of each month, 18:00 – 21:00. Book your spot via the link above — sessions sell out a week or two ahead.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Group bookings (6–8 people) can arrange a private session on a different date — use the <a href="/event-enquiry">private event enquiry form</a>.</p>
<!-- /wp:paragraph -->',
        'category'   => 'Workshop',
        'date'       => '',             // Recurring — no fixed date
        'time'       => '18:00 – 21:00',
        'recurrence' => 'monthly',
        'interior'   => 'cream',
        'price'      => '150,000 VND',
        'ticket_url' => '',
        'tags'       => [ 'event', 'active' ],
    ],

];

// ── Helpers ──────────────────────────────────────────────────────────────────

/**
 * Find an existing post by title + 'event' tag.
 * Returns post ID or 0.
 */
function tempo_find_event_post( string $title ): int {
    $q = new WP_Query([
        'post_type'   => 'post',
        'title'       => $title,
        'tax_query'   => [[
            'taxonomy' => 'post_tag',
            'field'    => 'slug',
            'terms'    => 'event',
        ]],
        'fields'           => 'ids',
        'posts_per_page'   => 1,
        'no_found_rows'    => true,
        'suppress_filters' => true,
    ]);
    return $q->posts[0] ?? 0;
}

// ── Remove mode ──────────────────────────────────────────────────────────────

if ( $remove_mode ) {
    foreach ( $events as $ev ) {
        $id = tempo_find_event_post( $ev['title'] );
        if ( $id ) {
            wp_delete_post( $id, true );
            WP_CLI::success( "Deleted '{$ev['title']}' (ID {$id})" );
        } else {
            WP_CLI::log( "Not found: '{$ev['title']}' — skipped" );
        }
    }
    WP_CLI::success( 'Remove complete.' );
    return;
}

// ── Create mode ──────────────────────────────────────────────────────────────

$created = 0;
$skipped = 0;

foreach ( $events as $ev ) {
    $existing = tempo_find_event_post( $ev['title'] );
    if ( $existing ) {
        WP_CLI::log( "Skipping '{$ev['title']}' — already exists (ID {$existing})" );
        $skipped++;
        continue;
    }

    // 1. Insert the post.
    $post_id = wp_insert_post([
        'post_title'   => $ev['title'],
        'post_name'    => $ev['slug'],
        'post_content' => $ev['content'],
        'post_excerpt' => $ev['excerpt'],
        'post_status'  => 'publish',
        'post_type'    => 'post',
        'post_author'  => 1,
    ], true );

    if ( is_wp_error( $post_id ) ) {
        WP_CLI::warning( "Failed to create '{$ev['title']}': " . $post_id->get_error_message() );
        continue;
    }

    // 2. Set ACF event fields.
    // update_field() requires ACF to be loaded — it is when running under wp eval-file.
    if ( function_exists( 'update_field' ) ) {
        update_field( 'event_category',   $ev['category'],   $post_id );
        update_field( 'event_time',       $ev['time'],       $post_id );
        update_field( 'event_recurrence', $ev['recurrence'], $post_id );
        update_field( 'event_interior',   $ev['interior'],   $post_id );
        update_field( 'event_price',      $ev['price'],      $post_id );
        if ( $ev['date'] ) {
            update_field( 'event_date', $ev['date'], $post_id );
        }
        if ( $ev['ticket_url'] ) {
            update_field( 'event_ticket_url', $ev['ticket_url'], $post_id );
        }
        update_field( 'event_media_type', 'none', $post_id );
    } else {
        // ACF not available — write raw post meta as fallback.
        update_post_meta( $post_id, 'event_category',   $ev['category'] );
        update_post_meta( $post_id, 'event_time',       $ev['time'] );
        update_post_meta( $post_id, 'event_recurrence', $ev['recurrence'] );
        update_post_meta( $post_id, 'event_interior',   $ev['interior'] );
        update_post_meta( $post_id, 'event_price',      $ev['price'] );
        update_post_meta( $post_id, 'event_media_type', 'none' );
        WP_CLI::warning( "ACF not available — wrote raw meta for '{$ev['title']}'. ACF field UI will be blank until ACF syncs." );
    }

    // 3. Add tags: 'event' always, 'active' marks it for homepage + "Happening Now".
    wp_set_post_tags( $post_id, $ev['tags'], false );

    WP_CLI::success( "Created '{$ev['title']}' — ID {$post_id}, tags: " . implode( ', ', $ev['tags'] ) );
    $created++;
}

WP_CLI::success( "Done. Created: {$created}  |  Skipped (already exist): {$skipped}" );
WP_CLI::log( '' );
WP_CLI::log( 'To remove all seeded events:' );
WP_CLI::log( '  wp --path=WordPress eval-file WordPress/scripts/seed-demo-events.php --remove' );
WP_CLI::log( '' );
WP_CLI::log( 'Visit /whats-on to see the live events page.' );
WP_CLI::log( 'Events also appear in the homepage carousel immediately.' );
