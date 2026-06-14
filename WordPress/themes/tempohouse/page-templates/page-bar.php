<?php
/**
 * Template Name: Bar
 * Description: Cocktail bar page — District 3, Ho Chi Minh City
 */
get_header();
?>

<main class="page-bar" id="main" role="main">

  <!-- ── 1. Page banner ───────────────────────────── -->
  <header class="page-inner__banner">
    <p class="page-inner__eyebrow">The Bar</p>
    <h1 class="page-inner__title">When the café closes,<br>the bar opens. That&rsquo;s when<br>the night actually starts.</h1>
    <p class="page-inner__lead">Cocktails built on classical foundations, pulled local with Vietnamese ingredients. District&nbsp;3&rsquo;s evening address for people who want a proper drink and somewhere worth staying.</p>
  </header>

  <!-- ── 2. The Programme (dark atmospheric) ─────── -->
  <section class="page-inner__section page-inner__section--dark page-bar__programme" aria-labelledby="bar-programme-title">
    <div class="page-inner__container">
      <div class="page-inner__split">

        <div class="page-bar__programme-text">
          <p class="page-inner__section-head">The Programme</p>
          <h2 class="page-inner__section-title" id="bar-programme-title">Classics. Originals.<br>Things that taste like somewhere.</h2>
          <div class="page-inner__section-body">
            <p>Every drink starts from a classical foundation — Negronis, Sours, Highballs — then we pull in Vietnamese ingredients to make them ours. Lychee from the Mekong Delta. Pandan. Calamansi in place of standard citrus. A gentle bitterness that isn&rsquo;t in the recipe books. The menu rotates with the season, not the trend cycle.</p>
            <p>Natural wine, local craft beer, and a considered low-and-no ABV selection sit alongside the cocktail list, so nobody is stuck with soda water and apologies. As the evening moves, the tempo shifts — measured, never frantic. This is a cocktail bar in Saigon, not a nightclub. The distinction matters.</p>
          </div>
          <ul class="page-inner__feature-list page-bar__programme-list" aria-label="Bar menu highlights">
            <li class="page-inner__feature-item">House Originals</li>
            <li class="page-inner__feature-item">Classic Foundations</li>
            <li class="page-inner__feature-item">Natural Wine &amp; Bubbles</li>
            <li class="page-inner__feature-item">Low &amp; No ABV</li>
            <li class="page-inner__feature-item">Snacks &amp; Plates</li>
          </ul>
        </div>

        <div class="page-inner__img-placeholder page-bar__programme-img" aria-hidden="true">
          <span>Bar photography</span>
        </div>

      </div>
    </div>
  </section>

  <!-- ── 3. The Atmosphere (split, alt bg) ────────── -->
  <section class="page-inner__section page-inner__section--alt page-bar__atmosphere" aria-labelledby="bar-atmosphere-title">
    <div class="page-inner__container">
      <div class="page-inner__split">

        <div class="page-bar__atmosphere-text">
          <h2 class="page-inner__section-title" id="bar-atmosphere-title">A bar you want to stay at.</h2>
          <div class="page-inner__section-body">
            <p>Evening kicks in, the lights drop, the playlist shifts, and the ground floor opens to the outdoor terrace — one of the few genuinely open-air spots in Quận 3. Seating is non-fixed. Pull chairs together, spread out, rearrange as the night calls for it.</p>
            <p>Works for small groups, overdue catch-ups, a first date that needs somewhere with atmosphere, or a proper cocktail hour before heading upstairs to the gallery on Level&nbsp;1. The kind of bar that earns the second round without trying to.</p>
          </div>
          <ul class="page-inner__feature-list" aria-label="Atmosphere features">
            <li class="page-inner__feature-item">Indoor &amp; outdoor seating</li>
            <li class="page-inner__feature-item">Playlist that shifts with the hour</li>
            <li class="page-inner__feature-item">Gallery access on Level&nbsp;1</li>
            <li class="page-inner__feature-item">Available for private cocktail receptions</li>
          </ul>
        </div>

        <div class="page-inner__img-placeholder page-bar__atmosphere-img" aria-hidden="true">
          <span>Atmosphere photography</span>
        </div>

      </div>
    </div>
  </section>

  <!-- ── 4. Private Cocktail Events ──────────────── -->
  <section class="page-inner__section page-bar__private" aria-labelledby="bar-private-title">
    <div class="page-inner__container">
      <div class="page-inner__split">

        <div class="page-bar__private-text">
          <p class="page-inner__section-head">Private Hire</p>
          <h2 class="page-inner__section-title" id="bar-private-title">The bar. The night. Yours.</h2>
          <div class="page-inner__section-body">
            <p>The bar is available for private hire — cocktail receptions, product launches, brand events, birthday dinners, post-wrap drinks. Full venue buyout covers the bar floor, gallery on Level&nbsp;1, and the outdoor terrace. Or the bar on its own for something more contained.</p>
            <p>We work with trusted caterer partners and can build a bar package around your brief. Tell us the occasion, the headcount, and what you need it to feel like. We&rsquo;ll handle the rest.</p>
          </div>
          <div class="page-inner__cta-row">
            <a href="<?php echo esc_url( home_url( '/event-enquiry' ) ); ?>" class="page-inner__cta-primary">Enquire About Private Hire</a>
            <a href="<?php echo esc_url( home_url( '/events' ) ); ?>" class="page-inner__cta-secondary">See All Events</a>
          </div>
        </div>

        <div class="page-inner__img-placeholder page-bar__private-img" aria-hidden="true">
          <span>Private hire photography</span>
        </div>

      </div>
    </div>
  </section>

  <!-- ── 5. Info strip ────────────────────────────── -->
  <section class="page-inner__section page-inner__section--alt page-bar__info" aria-label="Visiting information">
    <div class="page-inner__container">
      <dl class="page-inner__info-grid">

        <div class="page-bar__info-block">
          <dt class="page-inner__info-label">Hours</dt>
          <dd class="page-inner__info-value">
            Bar &mdash; 18:00 &ndash; 01:00 daily<br>
            Caf&eacute; &mdash; 07:00 &ndash; 17:00 daily
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

  <!-- ── 6. Footer CTA ────────────────────────────── -->
  <section class="page-bar__footer-cta" aria-label="Reserve a table">
    <div class="page-inner__container">
      <p class="page-bar__footer-cta-text">Come in tonight. Reserve a table if you&rsquo;re bringing people.</p>
      <a href="<?php echo esc_url( home_url( '/reservations' ) ); ?>" class="page-inner__cta-primary">Make a Reservation</a>
    </div>
  </section>

</main>

<?php get_footer(); ?>
