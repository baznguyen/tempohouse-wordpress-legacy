<?php
/**
 * Template Name: Bar
 * Description: Cocktail bar page — District 3, Ho Chi Minh City
 */
get_header();
?>

<main class="page-bar" id="main" role="main">

  <!-- ── 1. Page banner ──────────────────────────────── -->
  <header class="page-inner__banner page-bar__banner">
    <span class="page-bar__banner-hour" aria-hidden="true">18</span>
    <p class="page-inner__eyebrow">The Bar &mdash; 218c Pasteur</p>
    <h1 class="page-inner__title">Cocktails &amp; wine,<br>District 3.<br><em class="page-bar__title-em">Built on classics. Served until late.</em></h1>
    <p class="page-inner__lead page-bar__banner-lead">Vietnamese ingredients pulling classical foundations somewhere new. Lychee, pandan, yuzu, Vietnamese rum. A considered wine list that earns its place. The bar opens at 18:00 — and earns its last order. 218c Pasteur.</p>
  </header>

  <!-- ── 1b. Bar provenance strip ────────────────────── -->
  <div class="page-bar__provenance" aria-hidden="true">
    <div class="page-inner__container">
      <dl class="page-bar__provenance-grid">
        <div class="page-bar__provenance-item">
          <dt class="page-bar__provenance-label">Spirits</dt>
          <dd class="page-bar__provenance-value">Vietnamese &amp; international craft</dd>
        </div>
        <div class="page-bar__provenance-item">
          <dt class="page-bar__provenance-label">Cocktails</dt>
          <dd class="page-bar__provenance-value">17 classics &amp; originals</dd>
        </div>
        <div class="page-bar__provenance-item">
          <dt class="page-bar__provenance-label">Wine</dt>
          <dd class="page-bar__provenance-value">Natural, old world &amp; Australian house pours</dd>
        </div>
        <div class="page-bar__provenance-item">
          <dt class="page-bar__provenance-label">Open</dt>
          <dd class="page-bar__provenance-value">18:00 &ndash; 01:00 nightly</dd>
        </div>
      </dl>
    </div>
  </div>


  <!-- ── 2. The Cocktail Programme ──────────────────── -->
  <section class="page-inner__section page-inner__section--dark page-bar__programme" aria-labelledby="bar-programme-title">
    <div class="page-inner__container">
      <div class="page-inner__split">

        <div class="page-bar__programme-text">
          <p class="page-inner__section-head">The Cocktail Programme</p>
          <h2 class="page-inner__section-title" id="bar-programme-title">Classics. Originals.<br>Things that taste like somewhere.</h2>
          <div class="page-inner__section-body">
            <p>Every drink starts from where it should &mdash; the canon. Negronis stirred to ratio. Sours balanced without shortcuts. Highballs built on ice that matters. Then we pull in what&rsquo;s around us: lychee from the delta, pandan from the wet market, yuzu citrus, Vietnamese rum. Saigon Spirit One in the Pornstar Martini. The cocktail list runs to seventeen classics and originals, plus four Spritz variations and four Mocktails that aren&rsquo;t an afterthought.</p>
            <p>The menu rotates with the season, not the trend cycle. When something leaves, something better takes its place.</p>
          </div>
          <ul class="page-inner__feature-list page-bar__programme-list" aria-label="Bar programme highlights">
            <li class="page-inner__feature-item">Espresso Martini &mdash; Vietnamese coffee meets the canon</li>
            <li class="page-inner__feature-item">Lychee Martini &mdash; Mekong Delta, Vodka &amp; Lychee Liqueur</li>
            <li class="page-inner__feature-item">Panpan Spritz &mdash; Saigon Pandan, Lime, Coconut Soda</li>
            <li class="page-inner__feature-item">Negroni &mdash; Gin, Campari, Sweet Vermouth</li>
            <li class="page-inner__feature-item">Manhattan &mdash; Rye, Vermouth Ng&#7885;t, Angostura</li>
            <li class="page-inner__feature-item">Mocktails &mdash; four that earn their place</li>
          </ul>
        </div>

        <div class="tempo-frame page-bar__programme-img" data-interactive aria-label="Cocktails at TEMPO House Bar">
          <div class="tempo-frame__mat">
            <div class="tempo-frame__artwork">
              <img class="tempo-frame__img" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/bar/bar-programme.jpg" alt="Cocktail programme at TEMPO House Bar" loading="lazy">
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- ── 2b. Signature Cocktails Gallery ─────────────── -->
  <section class="page-inner__section page-inner__section--alt page-bar__signatures" aria-labelledby="bar-sigs-title">
    <div class="page-inner__container">

      <div class="page-bar__signatures-head">
        <p class="page-inner__section-head">Six to Know</p>
        <div class="page-bar__signatures-intro">
          <h2 class="page-inner__section-title" id="bar-sigs-title">The signatures.<br>Where to start.</h2>
          <p class="page-bar__signatures-source">Seventeen cocktails on the list. These are the six that earn their entry &mdash; pulled local where they can be, classical where they can&rsquo;t be anything else.</p>
        </div>
      </div>

      <div class="page-bar__signatures-grid">

        <div class="page-bar__sig-item">
          <div class="tempo-frame page-bar__sig-frame" data-interactive aria-label="Espresso Martini at TEMPO House Bar">
            <div class="tempo-frame__mat"><div class="tempo-frame__artwork"><img class="tempo-frame__img" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/bar/bar-espresso-martini.jpg" alt="Espresso Martini at TEMPO House Bar" loading="lazy"></div></div>
          </div>
          <div class="page-bar__sig-label">
            <span class="page-bar__sig-num" aria-hidden="true">01</span>
            <div>
              <h3 class="page-bar__sig-name">Espresso Martini</h3>
              <p class="page-bar__sig-notes">Vodka &middot; Coffee Liqueur &middot; Vietnamese Espresso</p>
            </div>
          </div>
        </div>

        <div class="page-bar__sig-item">
          <div class="tempo-frame page-bar__sig-frame" data-interactive aria-label="Lychee Martini at TEMPO House Bar">
            <div class="tempo-frame__mat"><div class="tempo-frame__artwork"><img class="tempo-frame__img" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/bar/bar-lychee-martini.jpg" alt="Lychee Martini at TEMPO House Bar" loading="lazy"></div></div>
          </div>
          <div class="page-bar__sig-label">
            <span class="page-bar__sig-num" aria-hidden="true">02</span>
            <div>
              <h3 class="page-bar__sig-name">Lychee Martini</h3>
              <p class="page-bar__sig-notes">Vodka &middot; Lychee Liqueur &middot; Mekong Delta</p>
            </div>
          </div>
        </div>

        <div class="page-bar__sig-item">
          <div class="tempo-frame page-bar__sig-frame" data-interactive aria-label="Panpan Spritz at TEMPO House Bar">
            <div class="tempo-frame__mat"><div class="tempo-frame__artwork"><img class="tempo-frame__img" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/bar/bar-panpan-spritz.jpg" alt="Panpan Spritz at TEMPO House Bar" loading="lazy"></div></div>
          </div>
          <div class="page-bar__sig-label">
            <span class="page-bar__sig-num" aria-hidden="true">03</span>
            <div>
              <h3 class="page-bar__sig-name">Panpan Spritz</h3>
              <p class="page-bar__sig-notes">Saigon Pandan &middot; Lime &middot; Coconut Soda</p>
            </div>
          </div>
        </div>

        <div class="page-bar__sig-item">
          <div class="tempo-frame page-bar__sig-frame" data-interactive aria-label="Negroni at TEMPO House Bar">
            <div class="tempo-frame__mat"><div class="tempo-frame__artwork"><img class="tempo-frame__img" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/bar/bar-negroni.jpg" alt="Negroni at TEMPO House Bar" loading="lazy"></div></div>
          </div>
          <div class="page-bar__sig-label">
            <span class="page-bar__sig-num" aria-hidden="true">04</span>
            <div>
              <h3 class="page-bar__sig-name">Negroni</h3>
              <p class="page-bar__sig-notes">Gin &middot; Sweet Vermouth &middot; Campari</p>
            </div>
          </div>
        </div>

        <div class="page-bar__sig-item">
          <div class="tempo-frame page-bar__sig-frame" data-interactive aria-label="Manhattan at TEMPO House Bar">
            <div class="tempo-frame__mat"><div class="tempo-frame__artwork"><img class="tempo-frame__img" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/bar/bar-manhattan.jpg" alt="Manhattan at TEMPO House Bar" loading="lazy"></div></div>
          </div>
          <div class="page-bar__sig-label">
            <span class="page-bar__sig-num" aria-hidden="true">05</span>
            <div>
              <h3 class="page-bar__sig-name">Manhattan</h3>
              <p class="page-bar__sig-notes">Rye Whiskey &middot; Vermouth Ng&#7885;t &middot; Angostura</p>
            </div>
          </div>
        </div>

        <div class="page-bar__sig-item">
          <div class="tempo-frame page-bar__sig-frame" data-interactive aria-label="Yuzu Spritz at TEMPO House Bar">
            <div class="tempo-frame__mat"><div class="tempo-frame__artwork"><img class="tempo-frame__img" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/bar/bar-yuzu-spritz.jpg" alt="Yuzu Spritz at TEMPO House Bar" loading="lazy"></div></div>
          </div>
          <div class="page-bar__sig-label">
            <span class="page-bar__sig-num" aria-hidden="true">06</span>
            <div>
              <h3 class="page-bar__sig-name">Yuzu Spritz</h3>
              <p class="page-bar__sig-notes">Gin &middot; Yuzu Pur&eacute;e &middot; Ros&eacute; Syrup &middot; Soda</p>
            </div>
          </div>
        </div>

      </div>

      <p class="page-bar__signatures-footnote">Full cocktail list available at the bar. Menu rotates seasonally &mdash; cocktails from 220k, Manhattan 300k.</p>
      <nav class="page-bar__signatures-nav" aria-label="Cocktail carousel navigation">
        <button class="page-bar__signatures-nav-btn page-bar__signatures-nav-prev" type="button" aria-label="Previous cocktails" disabled>
          <svg viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M8.5 2L3.5 7L8.5 12" stroke="currentColor" stroke-width="1.2" stroke-linecap="square" stroke-linejoin="miter"/></svg>
        </button>
        <div class="page-bar__signatures-dots" aria-hidden="true"></div>
        <button class="page-bar__signatures-nav-btn page-bar__signatures-nav-next" type="button" aria-label="Next cocktails">
          <svg viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M5.5 2L10.5 7L5.5 12" stroke="currentColor" stroke-width="1.2" stroke-linecap="square" stroke-linejoin="miter"/></svg>
        </button>
      </nav>

    </div>
  </section>

  <!-- ── 2c. Manifesto strip ───────────────────────── -->
  <div class="page-bar__manifesto" aria-hidden="true">
    <div class="page-inner__container">
      <p class="page-bar__manifesto-text">&ldquo;Classics first.<br>Somewhere second.&rdquo;</p>
      <span class="page-bar__manifesto-attr">218c Pasteur &mdash; District 3 &mdash; Ho Chi Minh City</span>
    </div>
  </div>

  <!-- ── 3. Happy Hour — time installation ────────── -->
  <section class="page-inner__section page-bar__happy-hour" aria-labelledby="happy-hour-title">
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
          <div class="page-bar__deal-item">
            <span class="page-bar__deal-label">Wine by the glass</span>
            <span class="page-bar__deal-value">20% off</span>
          </div>
          <div class="page-bar__deal-item">
            <span class="page-bar__deal-label">Spritz</span>
            <span class="page-bar__deal-value">190k</span>
          </div>
          <div class="page-bar__deal-item">
            <span class="page-bar__deal-label">Cocktails</span>
            <span class="page-bar__deal-value">200k</span>
          </div>
          <p class="page-bar__deal-note">Excluding the Manhattan &mdash; some things don&rsquo;t go on special.</p>
        </div>

      </div>
    </div>
  </section>

  <!-- ── 4. The Wine List ─────────────────────────────── -->
  <section class="page-inner__section page-inner__section--alt page-bar__wine" aria-labelledby="bar-wine-title">
    <div class="page-inner__container">
      <div class="page-inner__split">

        <div class="tempo-frame page-bar__wine-img" data-interactive aria-label="Wine selection at TEMPO House Bar">
          <div class="tempo-frame__mat">
            <div class="tempo-frame__artwork">
              <img class="tempo-frame__img" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/bar/bar-wine.jpg" alt="Wine selection at TEMPO House Bar" loading="lazy">
            </div>
          </div>
        </div>

        <div class="page-bar__wine-text">
          <p class="page-inner__section-head">The Wine List</p>
          <h2 class="page-inner__section-title" id="bar-wine-title">House pours worth pouring.<br>A list worth exploring.</h2>
          <div class="page-inner__section-body">
            <p>House pours that deserve to be poured by the glass. Celestia Chardonnay from Perricoota, Australia &mdash; clean, mineral, easy to commit to (140k glass / 600k bottle). Celestia Shiraz for the same reasons in red. Beyond the house: natural and organic selections from Sicily and the Rh&ocirc;ne Valley, old world bottles from Rioja and Burgundy, Marlborough Sauvignon Blanc, a Napa Cabernet when the occasion calls. The bottle list is long enough to be interesting, short enough to trust.</p>
          </div>
          <ul class="page-inner__feature-list page-bar__wine-list" aria-label="Wine list highlights">
            <li class="page-inner__feature-item">House pours &mdash; Chardonnay &amp; Shiraz, Perricoota, Australia</li>
            <li class="page-inner__feature-item">Natural &amp; organic &mdash; Sicilian Nero d&rsquo;Avola, C&ocirc;tes du Rh&ocirc;ne</li>
            <li class="page-inner__feature-item">Old world &mdash; Rioja, Burgundy, Bordeaux, Marlborough</li>
            <li class="page-inner__feature-item">Sparkling &mdash; Prosecco DOC, Champagne Charles Mignon</li>
            <li class="page-inner__feature-item">By the glass from 140k &mdash; bottles from 600k</li>
          </ul>
        </div>

      </div>
    </div>
  </section>

  <!-- ── 5. The Night Programme — concert bill ──────── -->
  <section class="page-inner__section page-inner__section--dark page-bar__night" aria-labelledby="bar-night-title">
    <div class="page-inner__container">

      <div class="page-bar__night-header">
        <p class="page-inner__section-head">The Night Programme</p>
        <h2 class="page-inner__section-title" id="bar-night-title">Live music.<br>DJ sets.<br>Comedy nights.</h2>
      </div>

      <ul class="page-bar__night-bill" aria-label="Night programme">
        <li class="page-bar__night-bill-item">
          <span class="page-bar__bill-label">Late Jazz &amp; Soul</span>
          <span class="page-bar__bill-note">Rotating residency &mdash; resident and guest performers</span>
        </li>
        <li class="page-bar__night-bill-item">
          <span class="page-bar__bill-label">DJ Nights</span>
          <span class="page-bar__bill-note">Selective &mdash; not every weekend, every one worth it</span>
        </li>
        <li class="page-bar__night-bill-item">
          <span class="page-bar__bill-label">Comedy &amp; Spoken Word</span>
          <span class="page-bar__bill-note">Bar floor sessions &mdash; evenings when the room changes</span>
        </li>
        <li class="page-bar__night-bill-item">
          <span class="page-bar__bill-label">Cocktail Openings</span>
          <span class="page-bar__bill-note">Seasonal debuts &mdash; tastings, listening, first pours</span>
        </li>
        <li class="page-bar__night-bill-item">
          <span class="page-bar__bill-label">Gallery After Dark</span>
          <span class="page-bar__bill-note">Level&nbsp;1 open during events &mdash; the show stays up</span>
        </li>
      </ul>

      <div class="page-bar__night-footer">
        <p class="page-bar__night-body">The schedule runs on quality, not frequency. When something is on, it&rsquo;s worth being there. Check What&rsquo;s On for upcoming dates.</p>
        <div class="page-inner__cta-row page-bar__night-cta">
          <a href="<?php echo esc_url( home_url( '/whats-on' ) ); ?>" class="page-inner__cta-primary">See What&rsquo;s On</a>
          <a href="<?php echo esc_url( home_url( '/event-enquiry' ) ); ?>" class="page-inner__cta-secondary">Private Event Enquiry</a>
        </div>
      </div>

    </div>
  </section>

  <!-- ── 6. The Atmosphere ──────────────────────────── -->
  <section class="page-inner__section page-inner__section--alt page-bar__atmosphere" aria-labelledby="bar-atmosphere-title">
    <div class="page-inner__container">
      <div class="page-inner__split">

        <div class="page-bar__atmosphere-text">
          <p class="page-inner__section-head">The Space</p>
          <h2 class="page-inner__section-title" id="bar-atmosphere-title">A bar you want to stay at.</h2>
          <div class="page-inner__section-body">
            <p>Evening kicks in, the lights drop, the playlist shifts. The ground floor opens to the outdoor terrace &mdash; one of the few genuinely open-air spots in Qu&#7853;n&nbsp;3. Seating is non-fixed. Pull chairs together, spread out, rearrange as the night calls for it. Works for small groups, overdue catch-ups, a first date that needs somewhere with atmosphere. The kind of bar that earns the second round without trying to.</p>
          </div>
          <ul class="page-inner__feature-list page-bar__menu-list" aria-label="Atmosphere features">
            <li class="page-inner__feature-item">Indoor &amp; outdoor terrace seating</li>
            <li class="page-inner__feature-item">Playlist that shifts with the hour</li>
            <li class="page-inner__feature-item">Gallery access on Level&nbsp;1</li>
            <li class="page-inner__feature-item">Available for private cocktail receptions</li>
          </ul>
        </div>

        <div class="tempo-frame page-bar__atmosphere-img" data-interactive aria-label="Atmosphere at TEMPO House Bar">
          <div class="tempo-frame__mat">
            <div class="tempo-frame__artwork">
              <img class="tempo-frame__img" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/bar/bar-atmosphere.jpg" alt="Evening atmosphere at TEMPO House Bar" loading="lazy">
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- ── 7. Info strip ─────────────────────────────── -->
  <section class="page-inner__section page-bar__info" aria-label="Visiting information">
    <div class="page-inner__container">
      <dl class="page-inner__info-grid">

        <div class="page-bar__info-block">
          <dt class="page-inner__info-label">Hours</dt>
          <dd class="page-inner__info-value">
            Bar &mdash; 18:00 &ndash; 01:00 nightly<br>
            Caf&eacute; &mdash; 08:00 &ndash; 17:00 daily
          </dd>
        </div>

        <div class="page-bar__info-block">
          <dt class="page-inner__info-label">Address</dt>
          <dd class="page-inner__info-value">
            218c Pasteur, Xu&acirc;n Ho&agrave;<br>
            Qu&#7853;n 3, Ho Chi Minh City
          </dd>
        </div>

        <div class="page-bar__info-block">
          <dt class="page-inner__info-label">Reservations</dt>
          <dd class="page-inner__info-value">
            Recommended for groups of 6+<br>
            <a href="mailto:hello@tempohouse.com.vn">hello@tempohouse.com.vn</a>
          </dd>
        </div>

      </dl>
    </div>
  </section>

  <!-- ── 8. Footer CTA ────────────────────────────── -->
  <section class="page-bar__footer-cta" aria-label="Come in tonight">
    <div class="page-inner__container">
      <p class="page-bar__footer-cta-text">Come in tonight.<br><em>The bar opens at six.</em></p>
      <div class="page-bar__footer-cta-row">
        <a href="<?php echo esc_url( home_url( '/reservations' ) ); ?>" class="page-inner__cta-primary">Make a Reservation</a>
        <a href="<?php echo esc_url( home_url( '/whats-on' ) ); ?>" class="page-inner__cta-secondary">See What&rsquo;s On</a>
      </div>
    </div>
  </section>

</main>

<?php get_footer(); ?>
