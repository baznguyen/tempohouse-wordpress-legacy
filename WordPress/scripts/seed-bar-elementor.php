<?php
/**
 * Seed Bar page (post ID 5) with native Elementor widgets.
 *
 * Key design decisions from reading bar.css + inner-page.css:
 *
 * FONTS (corrected — previous seed used wrong font for section titles):
 *   .page-inner__section-title { font-family: var(--font-accent) }  → Cormorant Garamond, 300
 *   .page-inner__title (H1)    { font-family: var(--font-accent) }  → Cormorant Garamond, 300
 *   .page-inner__section-head  { font-family: var(--font-display) } → Bricolage Grotesque, 500, uppercase
 *   .page-inner__eyebrow       { font-family: var(--font-display) } → Bricolage Grotesque, 500, uppercase
 *
 * COLORS (all set explicitly in widget settings to override Elementor kit defaults):
 *   Banner eyebrow: amber #DDAA62 — bar.css line 16: .page-bar .page-inner__eyebrow
 *   Most section-heads: rgba(26,24,22,0.45) — inner-page.css line 95: color-text-muted
 *   Night Programme section-head: amber #DDAA62 — bar.css line 505
 *   All section titles: #1A1816 dark ink
 *
 * BACKGROUNDS (explicit hex values — not relying on CSS variable cascade):
 *   Provenance:         #F0EBE3 — bar.css line 102: background: var(--color-bg-alt)
 *   Cocktail Programme: #F0EBE3 — inner-page.css .page-inner__section--dark
 *   Manifesto:          #F0EBE3 — bar.css line 339: .page-bar__manifesto
 *   Night Programme:    #F0EBE3 — bar.css line 499: .page-bar__night
 *   Footer CTA:         #F7F3EE — bar.css line 613: background: var(--color-bg)
 *
 * Run:
 *   docker cp WordPress/scripts/seed-bar-elementor.php wp-env-wordpress-3f695f1c-cli-1:/tmp/ \
 *   && docker exec wp-env-wordpress-3f695f1c-cli-1 wp eval-file /tmp/seed-bar-elementor.php
 */

$post_id   = 5;
$theme_uri = get_template_directory_uri();
$home      = rtrim( home_url(), '/' );

// ── Design tokens ─────────────────────────────────────────────────────────────
$T = [
    'INK'       => '#1A1816',
    'CREAM'     => '#F7F3EE',
    'AMBER'     => '#DDAA62',
    'TERRA'     => '#7C3B3B',
    'BG_ALT'    => '#F0EBE3',
    'MUTED'     => 'rgba(26, 24, 22, 0.45)',
    'F_DISPLAY' => 'Bricolage Grotesque',
    'F_ACCENT'  => 'Cormorant Garamond',
    'F_BODY'    => 'Space Grotesk',
];

function eid() {
    static $n = 6000;
    return 'f' . base_convert( ++$n, 10, 36 );
}

function es( $css, $cols, $bg = '' ) {
    $s = [ 'css_classes' => $css ];
    if ( $bg !== '' ) { $s['background_background'] = 'classic'; $s['background_color'] = $bg; }
    return [ 'id' => eid(), 'elType' => 'section', 'settings' => $s, 'elements' => $cols ];
}

function ec( $pct, $widgets, $css = '' ) {
    return [ 'id' => eid(), 'elType' => 'column',
             'settings' => [ '_column_size' => $pct, 'css_classes' => $css ], 'elements' => $widgets ];
}

function eh( $text, $tag, $css, $color, $font, $weight, $extra = [] ) {
    return [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'heading',
             'settings' => array_merge( [
                 'title' => $text, 'header_size' => $tag, 'css_classes' => $css,
                 'title_color' => $color,
                 'typography_typography' => 'custom',
                 'typography_font_family' => $font,
                 'typography_font_weight' => $weight,
                 'typography_font_style' => 'normal',
             ], $extra ), 'elements' => [] ];
}

// Banner eyebrow — amber, Bricolage 500 uppercase
function eyebrow( $text, $T ) {
    return eh( $text, 'p', 'page-inner__eyebrow', $T['AMBER'], $T['F_DISPLAY'], '500', [
        'typography_text_transform'  => 'uppercase',
        'typography_letter_spacing'  => [ 'unit' => 'em', 'size' => 0.14, 'sizes' => [] ],
        'typography_font_size'       => [ 'unit' => 'px', 'size' => 11, 'sizes' => [] ],
        'typography_line_height'     => [ 'unit' => 'em', 'size' => 1.0, 'sizes' => [] ],
    ] );
}

// Section head label — muted or amber (Night Programme only)
function section_head( $text, $T, $color = null ) {
    return eh( $text, 'p', 'page-inner__section-head', $color ?? $T['MUTED'], $T['F_DISPLAY'], '500', [
        'typography_text_transform'  => 'uppercase',
        'typography_letter_spacing'  => [ 'unit' => 'em', 'size' => 0.14, 'sizes' => [] ],
        'typography_font_size'       => [ 'unit' => 'px', 'size' => 11, 'sizes' => [] ],
        'typography_line_height'     => [ 'unit' => 'em', 'size' => 1.0, 'sizes' => [] ],
    ] );
}

// Section title H2 — Cormorant Garamond weight-300 (inner-page.css line 112)
function section_title( $text, $T, $tag = 'h2', $extra = [] ) {
    return eh( $text, $tag, 'page-inner__section-title', $T['INK'], $T['F_ACCENT'], '300',
        array_merge( [
            'typography_font_style'     => 'normal',
            'typography_letter_spacing' => [ 'unit' => 'em', 'size' => -0.01, 'sizes' => [] ],
            'typography_line_height'    => [ 'unit' => 'em', 'size' => 1.15, 'sizes' => [] ],
        ], $extra )
    );
}

// H1 — Cormorant Garamond weight-300 (inner-page.css line 35)
function banner_h1( $text, $T ) {
    return eh( $text, 'h1', 'page-inner__title', $T['INK'], $T['F_ACCENT'], '300', [
        'typography_font_style'     => 'normal',
        'typography_letter_spacing' => [ 'unit' => 'em', 'size' => -0.02, 'sizes' => [] ],
        'typography_line_height'    => [ 'unit' => 'em', 'size' => 1.1, 'sizes' => [] ],
    ] );
}

function et( $html, $css = '' ) {
    return [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'text-editor',
             'settings' => [ 'editor' => $html, 'css_classes' => $css ], 'elements' => [] ];
}

// Button — explicit terracotta brand styles (overrides Elementor default green)
function eb( $label, $url, $T, $v = 'primary' ) {
    $p = $v === 'primary';
    return [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'button',
             'settings' => [
                 'text' => $label, 'link' => [ 'url' => $url ], 'css_classes' => "page-inner__cta-{$v}",
                 'background_color'           => $p ? $T['TERRA'] : '',
                 'button_text_color'          => $p ? $T['CREAM'] : $T['TERRA'],
                 'hover_background_color'     => $p ? '#6A3232' : 'rgba(123,59,59,0.06)',
                 'hover_color'                => $p ? $T['CREAM'] : '#6A3232',
                 'border_border'              => $p ? 'none' : 'solid',
                 'border_color'               => $p ? '' : $T['TERRA'],
                 'border_width'               => [ 'top'=>'1','right'=>'1','bottom'=>'1','left'=>'1','unit'=>'px','isLinked'=>true ],
                 'border_radius'              => [ 'top'=>'0','right'=>'0','bottom'=>'0','left'=>'0','unit'=>'px','isLinked'=>true ],
                 'typography_typography'      => 'custom',
                 'typography_font_family'     => $T['F_DISPLAY'],
                 'typography_font_weight'     => '500',
                 'typography_text_transform'  => 'uppercase',
                 'typography_letter_spacing'  => [ 'unit' => 'em', 'size' => 0.12, 'sizes' => [] ],
                 'typography_font_size'       => [ 'unit' => 'px', 'size' => 13, 'sizes' => [] ],
             ], 'elements' => [] ];
}

function ef( $url, $alt, $aria, $extra_class = '' ) {
    return [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'tempohouse-tempo-frame',
             'settings' => [ 'image' => [ 'url' => $url, 'id' => 0 ], 'alt' => $alt,
                             'aria_label' => $aria, 'interactive' => 'yes',
                             'extra_class' => $extra_class, 'loading' => 'lazy' ],
             'elements' => [] ];
}

function ehtml( $html ) {
    return [ 'id' => eid(), 'elType' => 'widget', 'widgetType' => 'html',
             'settings' => [ 'html' => trim( $html ) ], 'elements' => [] ];
}

// ── PAGE SECTIONS ─────────────────────────────────────────────────────────────

$data = [];

// 1. BANNER
$data[] = es( 'page-inner__banner page-bar__banner', [
    ec( 100, [
        ehtml( '<span class="page-bar__banner-hour" aria-hidden="true">18</span>' ),
        eyebrow( 'The Bar &mdash; 218c Pasteur', $T ),
        banner_h1(
            'Cocktails &amp; wine,<br>District 3.<br><em class="page-bar__title-em">Built on classics. Served until late.</em>',
            $T
        ),
        et( '<p>Vietnamese ingredients pulling classical foundations somewhere new. Lychee, pandan, yuzu, Vietnamese rum. A considered wine list that earns its place. The bar opens at 18:00 &mdash; and earns its last order. 218c Pasteur.</p>',
            'page-inner__lead page-bar__banner-lead' ),
    ] ),
] );

// 2. PROVENANCE STRIP — bg: #F0EBE3
$data[] = es( 'page-bar__provenance', [
    ec( 100, [
        ehtml( <<<HTML
<div class="page-inner__container">
  <dl class="page-bar__provenance-grid" aria-label="Bar at a glance">
    <div class="page-bar__provenance-item"><dt class="page-bar__provenance-label">Spirits</dt><dd class="page-bar__provenance-value">Vietnamese &amp; international craft</dd></div>
    <div class="page-bar__provenance-item"><dt class="page-bar__provenance-label">Cocktails</dt><dd class="page-bar__provenance-value">17 classics &amp; originals</dd></div>
    <div class="page-bar__provenance-item"><dt class="page-bar__provenance-label">Wine</dt><dd class="page-bar__provenance-value">Natural, old world &amp; Australian house pours</dd></div>
    <div class="page-bar__provenance-item"><dt class="page-bar__provenance-label">Open</dt><dd class="page-bar__provenance-value">18:00 &ndash; 01:00 nightly</dd></div>
  </dl>
</div>
HTML
        ),
    ] ),
], $T['BG_ALT'] );

// 3. COCKTAIL PROGRAMME — bg: #F0EBE3
$data[] = es( 'page-inner__section page-inner__section--dark page-bar__programme', [
    ec( 50, [
        section_head( 'The Cocktail Programme', $T ),
        section_title( 'Classics. Originals.<br>Things that taste like somewhere.', $T ),
        et( '<p>Every drink starts from where it should &mdash; the canon. Negronis stirred to ratio. Sours balanced without shortcuts. Highballs built on ice that matters. Then we pull in what&rsquo;s around us: lychee from the delta, pandan from the wet market, yuzu citrus, Vietnamese rum. Saigon Spirit One in the Pornstar Martini.</p><p>The menu rotates with the season, not the trend cycle. When something leaves, something better takes its place.</p>',
            'page-inner__section-body' ),
        ehtml( <<<HTML
<ul class="page-inner__feature-list page-bar__programme-list" aria-label="Bar programme highlights">
  <li class="page-inner__feature-item">Espresso Martini &mdash; Vietnamese coffee meets the canon</li>
  <li class="page-inner__feature-item">Lychee Martini &mdash; Mekong Delta, Vodka &amp; Lychee Liqueur</li>
  <li class="page-inner__feature-item">Panpan Spritz &mdash; Saigon Pandan, Lime, Coconut Soda</li>
  <li class="page-inner__feature-item">Negroni &mdash; Gin, Campari, Sweet Vermouth</li>
  <li class="page-inner__feature-item">Manhattan &mdash; Rye, Vermouth Ng&#7885;t, Angostura</li>
  <li class="page-inner__feature-item">Mocktails &mdash; four that earn their place</li>
</ul>
HTML
        ),
    ], 'page-bar__programme-text' ),
    ec( 50, [
        ef( "{$theme_uri}/assets/images/bar/bar-programme.jpg",
            'Cocktail programme at TEMPO House Bar', 'Cocktails at TEMPO House Bar', 'page-bar__programme-img' ),
    ] ),
], $T['BG_ALT'] );

// 4. SIGNATURE COCKTAILS — custom widget
$data[] = es( 'page-inner__section page-bar__signatures', [
    ec( 100, [
        [
            'id' => eid(), 'elType' => 'widget', 'widgetType' => 'tempohouse-cocktail-carousel',
            'settings' => [
                'section_head' => 'Six to Know',
                'title'        => 'The signatures.<br>Where to start.',
                'source_note'  => "Seventeen cocktails on the list. These are the six that earn their entry.",
                'items' => [
                    [ 'name' => 'Espresso Martini', 'notes' => 'Vodka &middot; Coffee Liqueur &middot; Vietnamese Espresso', 'image' => [ 'url' => "{$theme_uri}/assets/images/bar/bar-espresso-martini.jpg", 'id' => 0 ], 'alt' => 'Espresso Martini' ],
                    [ 'name' => 'Lychee Martini',   'notes' => 'Vodka &middot; Lychee Liqueur &middot; Mekong Delta',       'image' => [ 'url' => "{$theme_uri}/assets/images/bar/bar-lychee-martini.jpg",   'id' => 0 ], 'alt' => 'Lychee Martini' ],
                    [ 'name' => 'Panpan Spritz',    'notes' => 'Saigon Pandan &middot; Lime &middot; Coconut Soda',        'image' => [ 'url' => "{$theme_uri}/assets/images/bar/bar-panpan-spritz.jpg",   'id' => 0 ], 'alt' => 'Panpan Spritz' ],
                    [ 'name' => 'Negroni',          'notes' => 'Gin &middot; Sweet Vermouth &middot; Campari',             'image' => [ 'url' => "{$theme_uri}/assets/images/bar/bar-negroni.jpg",         'id' => 0 ], 'alt' => 'Negroni' ],
                    [ 'name' => 'Manhattan',        'notes' => 'Rye Whiskey &middot; Vermouth Ngọt &middot; Angostura',   'image' => [ 'url' => "{$theme_uri}/assets/images/bar/bar-manhattan.jpg",       'id' => 0 ], 'alt' => 'Manhattan' ],
                    [ 'name' => 'Yuzu Spritz',      'notes' => 'Gin &middot; Yuzu Purée &middot; Rosé Syrup &middot; Soda', 'image' => [ 'url' => "{$theme_uri}/assets/images/bar/bar-yuzu-spritz.jpg", 'id' => 0 ], 'alt' => 'Yuzu Spritz' ],
                ],
            ],
            'elements' => [],
        ],
    ] ),
] );

// 5. MANIFESTO — bg: #F0EBE3 (bar.css line 339)
$data[] = es( 'page-bar__manifesto', [
    ec( 100, [
        ehtml( <<<HTML
<div class="page-inner__container" aria-hidden="true">
  <p class="page-bar__manifesto-text">&ldquo;Classics first.<br>Somewhere second.&rdquo;</p>
  <span class="page-bar__manifesto-attr">218c Pasteur &mdash; District 3 &mdash; Ho Chi Minh City</span>
</div>
HTML
        ),
    ] ),
], $T['BG_ALT'] );

// 6. HAPPY HOUR — .page-bar__hh-time targeted by page-bar.js (exact classes preserved)
$data[] = es( 'page-inner__section page-bar__happy-hour', [
    ec( 100, [
        ehtml( <<<HTML
<div class="page-inner__container">
  <div class="page-bar__happy-hour-inner">
    <div class="page-bar__hh-left">
      <p class="page-inner__section-head">Every Night</p>
      <div class="page-bar__hh-times" aria-label="18:00 to 20:00">
        <span class="page-bar__hh-time" aria-hidden="true">18</span>
        <span class="page-bar__hh-sep" aria-hidden="true">&mdash;</span>
        <span class="page-bar__hh-time" aria-hidden="true">20</span>
      </div>
      <p class="page-bar__hh-sub">Come early. Settle in. First two hours of the evening &mdash; this is how the night is supposed to start.</p>
    </div>
    <div class="page-bar__happy-hour-deals">
      <h2 class="sr-only" id="happy-hour-title">Happy Hour 18:00 to 20:00</h2>
      <div class="page-bar__deal-item"><span class="page-bar__deal-label">Wine by the glass</span><span class="page-bar__deal-value">20% off</span></div>
      <div class="page-bar__deal-item"><span class="page-bar__deal-label">Spritz</span><span class="page-bar__deal-value">190k</span></div>
      <div class="page-bar__deal-item"><span class="page-bar__deal-label">Cocktails</span><span class="page-bar__deal-value">200k</span></div>
      <p class="page-bar__deal-note">Excluding the Manhattan &mdash; some things don&rsquo;t go on special.</p>
    </div>
  </div>
</div>
HTML
        ),
    ] ),
] );

// 7. WINE LIST — .page-inner__section--alt CSS class handles alt bg (rgba cream)
$data[] = es( 'page-inner__section page-inner__section--alt page-bar__wine', [
    ec( 50, [
        ef( "{$theme_uri}/assets/images/bar/bar-wine.jpg", 'Wine at TEMPO House Bar', 'Wine at TEMPO House Bar', 'page-bar__wine-img' ),
    ] ),
    ec( 50, [
        section_head( 'The Wine List', $T ),
        section_title( 'House pours worth pouring.<br>A list worth exploring.', $T ),
        et( '<p>House pours that deserve to be poured by the glass. Celestia Chardonnay from Perricoota, Australia &mdash; clean, mineral, easy to commit to (140k glass / 600k bottle). Celestia Shiraz for the same reasons in red.</p><p>Beyond the house: natural and organic from Sicily and the Rh&ocirc;ne, old world bottles from Rioja and Burgundy, Marlborough Sauvignon Blanc. The bottle list is long enough to be interesting, short enough to trust.</p>',
            'page-inner__section-body' ),
        ehtml( <<<HTML
<ul class="page-inner__feature-list page-bar__wine-list" aria-label="Wine list highlights">
  <li class="page-inner__feature-item">House pours &mdash; Chardonnay &amp; Shiraz, Perricoota, Australia</li>
  <li class="page-inner__feature-item">Natural &amp; organic &mdash; Sicilian Nero d&rsquo;Avola, C&ocirc;tes du Rh&ocirc;ne</li>
  <li class="page-inner__feature-item">Old world &mdash; Rioja, Burgundy, Bordeaux, Marlborough</li>
  <li class="page-inner__feature-item">Sparkling &mdash; Prosecco DOC, Champagne Charles Mignon</li>
  <li class="page-inner__feature-item">By the glass from 140k &mdash; bottles from 600k</li>
</ul>
HTML
        ),
    ], 'page-bar__wine-text' ),
] );

// 8. NIGHT PROGRAMME — bg: #F0EBE3 (bar.css line 499)
//    Section head is AMBER here — bar.css line 505: .page-bar__night .page-inner__section-head
//    Section title large — CSS cascade from .page-bar__night .page-inner__section-title (clamp 2.8rem–8rem)
//    No unclosed HTML divs — concert bill is a self-contained HTML widget
$data[] = es( 'page-inner__section page-bar__night', [
    ec( 100, [
        section_head( 'The Night Programme', $T, $T['AMBER'] ),
        section_title( 'Live music.<br>DJ sets.<br>Comedy nights.', $T, 'h2', [
            'typography_letter_spacing' => [ 'unit' => 'em', 'size' => -0.03, 'sizes' => [] ],
            'typography_line_height'    => [ 'unit' => 'em', 'size' => 1.0, 'sizes' => [] ],
        ] ),
        ehtml( <<<HTML
<ul class="page-bar__night-bill" aria-label="Night programme">
  <li class="page-bar__night-bill-item"><span class="page-bar__bill-label">Late Jazz &amp; Soul</span><span class="page-bar__bill-note">Rotating residency &mdash; resident and guest performers</span></li>
  <li class="page-bar__night-bill-item"><span class="page-bar__bill-label">DJ Nights</span><span class="page-bar__bill-note">Selective &mdash; not every weekend, every one worth it</span></li>
  <li class="page-bar__night-bill-item"><span class="page-bar__bill-label">Comedy &amp; Spoken Word</span><span class="page-bar__bill-note">Bar floor sessions &mdash; evenings when the room changes</span></li>
  <li class="page-bar__night-bill-item"><span class="page-bar__bill-label">Cocktail Openings</span><span class="page-bar__bill-note">Seasonal debuts &mdash; tastings, listening, first pours</span></li>
  <li class="page-bar__night-bill-item"><span class="page-bar__bill-label">Gallery After Dark</span><span class="page-bar__bill-note">Level&nbsp;1 open during events &mdash; the show stays up</span></li>
</ul>
HTML
        ),
        et( '<p>The schedule runs on quality, not frequency. When something is on, it&rsquo;s worth being there. Check What&rsquo;s On for upcoming dates.</p>', 'page-bar__night-body' ),
        eb( "See What's On",        "{$home}/whats-on",      $T, 'primary' ),
        eb( 'Private Event Enquiry', "{$home}/event-enquiry", $T, 'secondary' ),
    ] ),
], $T['BG_ALT'] );

// 9. ATMOSPHERE — .page-inner__section--alt CSS class handles alt bg
$data[] = es( 'page-inner__section page-inner__section--alt page-bar__atmosphere', [
    ec( 50, [
        section_head( 'The Space', $T ),
        section_title( 'A bar you want to stay at.', $T ),
        et( '<p>Evening kicks in, the lights drop, the playlist shifts. The ground floor opens to the outdoor terrace &mdash; one of the few genuinely open-air spots in Qu&#7853;n 3. Seating is non-fixed. Works for small groups, overdue catch-ups, a first date that needs somewhere with atmosphere.</p>',
            'page-inner__section-body' ),
        ehtml( <<<HTML
<ul class="page-inner__feature-list" aria-label="Bar highlights">
  <li class="page-inner__feature-item">Indoor &amp; outdoor terrace seating</li>
  <li class="page-inner__feature-item">Playlist that shifts with the hour</li>
  <li class="page-inner__feature-item">Gallery access on Level&nbsp;1</li>
  <li class="page-inner__feature-item">Available for private cocktail receptions</li>
</ul>
HTML
        ),
    ], 'page-bar__atmosphere-text' ),
    ec( 50, [
        ef( "{$theme_uri}/assets/images/bar/bar-atmosphere.jpg",
            'Evening atmosphere at TEMPO House Bar', 'Atmosphere at TEMPO House Bar', 'page-bar__atmosphere-img' ),
    ] ),
] );

// 10. INFO STRIP
$data[] = es( 'page-inner__section page-bar__info', [
    ec( 100, [
        ehtml( <<<HTML
<div class="page-inner__container">
  <dl class="page-inner__info-grid">
    <div><dt class="page-inner__info-label">Hours</dt><dd class="page-inner__info-value">Bar &mdash; 18:00 &ndash; 01:00 nightly<br>Caf&eacute; &mdash; 08:00 &ndash; 17:00 daily</dd></div>
    <div><dt class="page-inner__info-label">Address</dt><dd class="page-inner__info-value">218c Pasteur, Xu&acirc;n Ho&agrave;<br>Qu&#7853;n 3, Ho Chi Minh City</dd></div>
    <div><dt class="page-inner__info-label">Reservations</dt><dd class="page-inner__info-value">Recommended for groups of 6+<br><a href="mailto:hello@tempohouse.com.vn">hello@tempohouse.com.vn</a></dd></div>
  </dl>
</div>
HTML
        ),
    ] ),
] );

// 11. FOOTER CTA — bg: #F7F3EE (bar.css line 613: background: var(--color-bg))
$data[] = es( 'page-bar__footer-cta', [
    ec( 100, [
        et( '<p>Come in tonight.<br><em>The bar opens at six.</em></p>', 'page-bar__footer-cta-text' ),
        eb( 'Make a Reservation', "{$home}/reservations", $T, 'primary' ),
        eb( "See What's On",      "{$home}/whats-on",     $T, 'secondary' ),
    ] ),
], $T['CREAM'] );

// ── Save ──────────────────────────────────────────────────────────────────────
$json = wp_json_encode( $data );
update_post_meta( $post_id, '_elementor_data',          wp_slash( $json ) );
update_post_meta( $post_id, '_elementor_edit_mode',     'builder' );
update_post_meta( $post_id, '_elementor_version',       '3.0.0' );
update_post_meta( $post_id, '_elementor_template_type', 'wp-page' );

// Apply brand colours + typography to Elementor's active Kit
if ( class_exists( 'THR_Kit_Setup' ) ) {
    THR_Kit_Setup::apply();
    WP_CLI::log( 'Kit settings applied.' );
}

if ( class_exists( '\Elementor\Plugin' ) ) {
    \Elementor\Plugin::instance()->files_manager->clear_cache();
}

// Verify JSON + summarise widget types
$d2    = json_decode( get_post_meta( $post_id, '_elementor_data', true ), true );
$types = [];
function count_types( $els, &$t ) {
    foreach ( $els as $e ) {
        if ( ! empty( $e['widgetType'] ) ) $t[ $e['widgetType'] ] = ( $t[ $e['widgetType'] ] ?? 0 ) + 1;
        if ( ! empty( $e['elements'] ) ) count_types( $e['elements'], $t );
    }
}
count_types( $d2, $types );
arsort( $types );
$summary = implode( ', ', array_map( fn( $t, $n ) => "{$t}:{$n}", array_keys( $types ), $types ) );
WP_CLI::success( sprintf( 'JSON valid. %d sections. %s', count( $data ), $summary ) );
