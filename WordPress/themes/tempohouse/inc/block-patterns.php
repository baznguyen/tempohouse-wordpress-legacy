<?php
/**
 * TEMPO House — Gutenberg block patterns
 *
 * Registers reusable patterns for the marketing team. Patterns appear in the
 * block inserter under the "TEMPO House" category and provide pre-built layouts
 * for events, journal articles, and exhibitions — no layout decisions required.
 *
 * Usage: Block inserter (+ icon) → Patterns → TEMPO House → click to insert.
 */

function tempohouse_register_block_patterns() {

    register_block_pattern_category(
        'tempohouse',
        [ 'label' => __( 'TEMPO House', 'tempohouse' ) ]
    );

    // ── Event Highlight ───────────────────────────────────────────────────────
    // Use for event detail page body content. The category label and CTA match
    // What's On card conventions — keep them in sync with ACF sidebar fields.
    register_block_pattern(
        'tempohouse/event-highlight',
        [
            'title'       => __( 'Event Highlight', 'tempohouse' ),
            'description' => __( 'Category label, event description, detail row (date / time / price), and CTA button.', 'tempohouse' ),
            'categories'  => [ 'tempohouse' ],
            'content'     => '<!-- wp:group {"backgroundColor":"ink","textColor":"cream","className":"event-highlight","layout":{"type":"constrained","contentSize":"720px"}} -->
<div class="wp-block-group has-ink-background-color has-cream-color has-background has-text-color event-highlight">

<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.75rem","letterSpacing":"0.15em","textTransform":"uppercase"}},"textColor":"terracotta"} -->
<p class="has-terracotta-color has-text-color" style="font-size:0.75rem;letter-spacing:0.15em;text-transform:uppercase">Live Music</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2,"style":{"typography":{"fontFamily":"var(--wp--preset--font-family--display)","lineHeight":"1.1"}}} -->
<h2 class="wp-block-heading">Event Title</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Describe the event here — performers, atmosphere, what guests can expect. Two to three sentences works well.</p>
<!-- /wp:paragraph -->

<!-- wp:separator {"backgroundColor":"sand"} -->
<hr class="wp-block-separator has-sand-background-color has-background"/>
<!-- /wp:separator -->

<!-- wp:group {"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"left"},"style":{"spacing":{"blockGap":"2rem"}}} -->
<div class="wp-block-group">
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.875rem"}}} --><p style="font-size:0.875rem">📅 &nbsp;Date: Saturday, 19 July 2026</p><!-- /wp:paragraph -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.875rem"}}} --><p style="font-size:0.875rem">🕗 &nbsp;Time: 20:00 – 23:00</p><!-- /wp:paragraph -->
<!-- wp:paragraph {"style":{"typography":{"fontSize":"0.875rem"}}} --><p style="font-size:0.875rem">🎟 &nbsp;Admission: Free</p><!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"2rem"}}}} -->
<div class="wp-block-buttons">
<!-- wp:button {"backgroundColor":"terracotta","textColor":"cream"} -->
<div class="wp-block-button"><a class="wp-block-button__link has-terracotta-background-color has-cream-color has-background has-text-color wp-element-button">Reserve a Seat</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->

</div>
<!-- /wp:group -->',
        ]
    );

    // ── Journal Article Body ──────────────────────────────────────────────────
    // Insert this at the top of a new journal post to get the correct structure.
    // Replace placeholder text throughout. The pull quote block is optional —
    // delete it if the article does not have a strong quote to highlight.
    register_block_pattern(
        'tempohouse/journal-article',
        [
            'title'       => __( 'Journal Article Body', 'tempohouse' ),
            'description' => __( 'Category eyebrow, title, excerpt, article body with subheading and pull quote.', 'tempohouse' ),
            'categories'  => [ 'tempohouse' ],
            'content'     => '<!-- wp:group {"layout":{"type":"constrained","contentSize":"720px"}} -->
<div class="wp-block-group">

<!-- wp:paragraph {"style":{"typography":{"textTransform":"uppercase","letterSpacing":"0.1em","fontSize":"0.75rem"}},"textColor":"terracotta"} -->
<p class="has-terracotta-color has-text-color" style="text-transform:uppercase;letter-spacing:0.1em;font-size:0.75rem">Category</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading">Article Title</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"typography":{"fontSize":"1.125rem","lineHeight":"1.5"}},"textColor":"ink"} -->
<p class="has-ink-color has-text-color" style="font-size:1.125rem;line-height:1.5">A compelling one-sentence excerpt that draws the reader in. This appears in search results and social shares.</p>
<!-- /wp:paragraph -->

<!-- wp:separator {"backgroundColor":"sand"} -->
<hr class="wp-block-separator has-sand-background-color has-background"/>
<!-- /wp:separator -->

<!-- wp:paragraph -->
<p>Opening paragraph goes here. Set the scene — where are we, what's the hook, why does this matter to a TEMPO House reader?</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">First Subheading</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Continue the article here. Add as many paragraphs as needed between subheadings.</p>
<!-- /wp:paragraph -->

<!-- wp:quote {"className":"is-style-large"} -->
<blockquote class="wp-block-quote is-style-large"><p>A key line worth pulling out as a quote — something memorable or surprising from this section.</p><cite>Optional attribution</cite></blockquote>
<!-- /wp:quote -->

<!-- wp:paragraph -->
<p>Closing thoughts — bring it back to TEMPO House, the guest experience, or an invitation to visit.</p>
<!-- /wp:paragraph -->

</div>
<!-- /wp:group -->',
        ]
    );

    // ── Exhibition Entry ──────────────────────────────────────────────────────
    // Use for gallery exhibition detail page content. Artists, dates, medium,
    // and the curatorial essay all go here. The opening event CTA is optional.
    register_block_pattern(
        'tempohouse/exhibition',
        [
            'title'       => __( 'Exhibition Entry', 'tempohouse' ),
            'description' => __( 'Exhibition title, subtitle, artists, dates, curatorial essay, and opening event CTA.', 'tempohouse' ),
            'categories'  => [ 'tempohouse' ],
            'content'     => '<!-- wp:group {"layout":{"type":"constrained","contentSize":"720px"}} -->
<div class="wp-block-group">

<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading">Exhibition Title</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"typography":{"fontFamily":"var(--wp--preset--font-family--accent)","fontSize":"1.25rem","fontStyle":"italic"}}} -->
<p style="font-family:var(--wp--preset--font-family--accent);font-size:1.25rem;font-style:italic">Exhibition subtitle or curatorial tagline</p>
<!-- /wp:paragraph -->

<!-- wp:separator {"backgroundColor":"sand"} -->
<hr class="wp-block-separator has-sand-background-color has-background"/>
<!-- /wp:separator -->

<!-- wp:group {"layout":{"type":"constrained"},"style":{"spacing":{"blockGap":"0.5rem"}}} -->
<div class="wp-block-group">
<!-- wp:paragraph {"style":{"typography":{"fontWeight":"600"}}} --><p><strong>Artists</strong></p><!-- /wp:paragraph -->
<!-- wp:paragraph --><p>Artist One, Artist Two, Artist Three, Artist Four, Artist Five</p><!-- /wp:paragraph -->
<!-- wp:paragraph {"style":{"typography":{"fontWeight":"600"}}} --><p><strong>Medium</strong></p><!-- /wp:paragraph -->
<!-- wp:paragraph --><p>Drawing, printmaking, and collage</p><!-- /wp:paragraph -->
<!-- wp:paragraph {"style":{"typography":{"fontWeight":"600"}}} --><p><strong>Dates</strong></p><!-- /wp:paragraph -->
<!-- wp:paragraph --><p>27 June – 9 August 2026</p><!-- /wp:paragraph -->
<!-- wp:paragraph {"style":{"typography":{"fontWeight":"600"}}} --><p><strong>Access</strong></p><!-- /wp:paragraph -->
<!-- wp:paragraph --><p>Open daily with café and bar. Free entry.</p><!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:separator {"backgroundColor":"sand"} -->
<hr class="wp-block-separator has-sand-background-color has-background"/>
<!-- /wp:separator -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Curatorial Essay</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Write the curatorial essay here. What is the exhibition about? What connects these artists and their work? What should a visitor take away?</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Continue the essay — as many paragraphs as needed. Pull quotes work well here too.</p>
<!-- /wp:paragraph -->

<!-- wp:separator {"backgroundColor":"sand"} -->
<hr class="wp-block-separator has-sand-background-color has-background"/>
<!-- /wp:separator -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Opening Event</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Join us for the opening reception on Friday, 27 June 2026 from 18:00 – 21:00. Free entry.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons">
<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button">RSVP for Opening Night →</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->

</div>
<!-- /wp:group -->',
        ]
    );

    // ── Reserve CTA ───────────────────────────────────────────────────────────
    // Reusable call-to-action section for bottom of any page.
    register_block_pattern(
        'tempohouse/reserve-cta',
        [
            'title'       => __( 'Reserve CTA Section', 'tempohouse' ),
            'description' => __( 'Reservation call-to-action with two buttons — book table and host event.', 'tempohouse' ),
            'categories'  => [ 'tempohouse' ],
            'content'     => '<!-- wp:group {"backgroundColor":"cream-dark","layout":{"type":"constrained","contentSize":"720px"},"style":{"spacing":{"padding":{"top":"4rem","bottom":"4rem"}}}} -->
<div class="wp-block-group has-cream-dark-background-color has-background" style="padding-top:4rem;padding-bottom:4rem">

<!-- wp:paragraph {"style":{"typography":{"textTransform":"uppercase","letterSpacing":"0.15em","fontSize":"0.75rem"}},"textColor":"terracotta"} -->
<p class="has-terracotta-color has-text-color" style="text-transform:uppercase;letter-spacing:0.15em;font-size:0.75rem">Dine with us</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Reserve a table</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Walk-ins are always welcome. For groups of six or more, or to guarantee your seat on event nights, we recommend booking ahead.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons">
<!-- wp:button {"backgroundColor":"terracotta","textColor":"cream"} -->
<div class="wp-block-button"><a class="wp-block-button__link has-terracotta-background-color has-cream-color has-background has-text-color wp-element-button" href="/reservations">Book a Table</a></div>
<!-- /wp:button -->
<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="/events">Host an Event →</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->

</div>
<!-- /wp:group -->',
        ]
    );
}
add_action( 'init', 'tempohouse_register_block_patterns' );
