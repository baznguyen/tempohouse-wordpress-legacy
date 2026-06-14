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
    <h1 class="page-inner__title">When the café closes,<br>the bar opens. That&rsquo;s when<br>the city really starts.</h1>
    <p class="page-inner__lead">Cocktails made with intention. Evenings with enough music to set a mood without killing conversation. District&nbsp;3&rsquo;s late-night address for those who know.</p>
  </header>

  <!-- ── 2. The Programme (dark atmospheric) ─────── -->
  <section class="page-inner__section page-inner__section--dark page-bar__programme" aria-labelledby="bar-programme-title">
    <div class="page-inner__container">
      <div class="page-inner__split">

        <div class="page-bar__programme-text">
          <p class="page-inner__section-head">The Programme</p>
          <h2 class="page-inner__section-title" id="bar-programme-title">Classics. Originals.<br>Things that taste like somewhere.</h2>
          <div class="page-inner__section-body">
            <p>Every drink on the menu starts from a classical foundation &mdash; Negronis, Sours, Highballs &mdash; then we pull in local Vietnamese ingredients to make them ours. Tropical herbs, lychee from the Mekong Delta, Vietnamese citrus, a gentle bitterness you won&rsquo;t find in the recipe books. The list rotates with the season, not the trend.</p>
            <p>Natural wine, local craft beer, and a thoughtful low-and-no ABV selection mean nobody is left without something worth nursing. As the night deepens the tempo rises &mdash; measured, never frantic. This isn&rsquo;t a nightclub. It isn&rsquo;t a dive. It&rsquo;s somewhere in between, and better for it.</p>
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
            <p>As evening falls, the music shifts up, the lights soften, and the space transforms. The ground floor opens to the outdoor area &mdash; a rare exhale in the middle of Qu&#7853;n 3. Seating is non-fixed; the kind of arrangement you rearrange for yourself, pull chairs together, spread out, settle in.</p>
            <p>Ideal for small groups, long-overdue catch-ups, a proper date, or a cocktail hour before a gallery opening on Level&nbsp;1. The kind of bar that earns the second round.</p>
          </div>
          <ul class="page-inner__feature-list" aria-label="Atmosphere features">
            <li class="page-inner__feature-item">Indoor &amp; outdoor seating</li>
            <li class="page-inner__feature-item">Curated playlist (tempo varies by hour)</li>
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
            <p>The bar can be privately hired for cocktail receptions, launch events, and celebrations. Full venue buyout options are available &mdash; bar floor, gallery on Level&nbsp;1, and the outdoor terrace &mdash; or just the bar on its own for more intimate occasions.</p>
            <p>We work with trusted caterer partners and offer curated bar packages tailored to the event. Tell us the occasion and we&rsquo;ll design the experience around it.</p>
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
      <p class="page-bar__footer-cta-text">Reserve a table for the evening.</p>
      <a href="<?php echo esc_url( home_url( '/reservations' ) ); ?>" class="page-inner__cta-primary">Make a Reservation</a>
    </div>
  </section>

</main>

<?php get_footer(); ?>
