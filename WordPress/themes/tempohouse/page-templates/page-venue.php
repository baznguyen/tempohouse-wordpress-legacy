<?php
/**
 * Template Name: Venue
 * Description: Master venue overview — café, bar, gallery, and private events at 218c Pasteur, District 3.
 */
get_header();
?>

<main class="page-venue" id="main" role="main">

  <!-- ── 1. Page Banner ──────────────────────────── -->
  <header class="page-inner__banner">
    <p class="page-inner__eyebrow">The Venue</p>
    <h1 class="page-inner__title">218c Pasteur, District 3.<br>Café, bar, gallery, events.</h1>
    <p class="page-inner__lead">TEMPO House is a specialty café by day, a cocktail bar by night, an art gallery on Level 1, and a private event venue when you need the whole building. One address in Ho Chi Minh City&rsquo;s most characterful inner-city neighbourhood.</p>
  </header>

  <!-- ── 2. Spaces Overview ──────────────────────── -->
  <section class="page-inner__section" aria-labelledby="spaces-title">
    <div class="page-inner__container">

      <div class="page-inner__section-head">
        <p class="page-inner__section-head">The Spaces</p>
        <h2 class="page-inner__section-title" id="spaces-title">Four experiences.<br>One address.</h2>
      </div>

      <div class="page-venue__spaces-grid">

        <article class="page-inner__card">
          <span class="page-inner__card-num">01</span>
          <h3 class="page-inner__card-title">Specialty Café</h3>
          <p class="page-inner__card-body">Ground floor, open from 07:00. Single-origin filter coffee, espresso-based drinks, and seasonal food. The furniture is non-fixed — the room works for a solo laptop session, a group catch-up, or a working breakfast. Light pours through the front from morning to mid-afternoon.</p>
          <a href="<?php echo esc_url( home_url( '/cafe' ) ); ?>" class="page-inner__card-cta">Explore the Café &rarr;</a>
        </article>

        <article class="page-inner__card">
          <span class="page-inner__card-num">02</span>
          <h3 class="page-inner__card-title">Cocktail Bar</h3>
          <p class="page-inner__card-body">The same ground floor counter that runs espresso service by day opens as a full cocktail bar from 18:00. The drinks programme is original — not a generic spirits list. Table service, walk-ins welcome, and a sound programme that moves as the evening does. Closes at 01:00.</p>
          <a href="<?php echo esc_url( home_url( '/bar' ) ); ?>" class="page-inner__card-cta">Explore the Bar &rarr;</a>
        </article>

        <article class="page-inner__card">
          <span class="page-inner__card-num">03</span>
          <h3 class="page-inner__card-title">Art Gallery</h3>
          <p class="page-inner__card-body">Level 1 runs a rotating exhibition programme year-round — emerging and mid-career artists from Vietnam and Southeast Asia, across painting, photography, and installation. The gallery is free to enter for all café and bar guests. No appointment, no ticketed entry. Take the stairs from the ground floor.</p>
          <a href="<?php echo esc_url( home_url( '/gallery' ) ); ?>" class="page-inner__card-cta">See the Gallery &rarr;</a>
        </article>

        <article class="page-inner__card">
          <span class="page-inner__card-num">04</span>
          <h3 class="page-inner__card-title">Private Events</h3>
          <p class="page-inner__card-body">The full building — both floors, outdoor area, bar, coffee counter, and gallery space — is available for exclusive hire. Product launches, private celebrations, corporate dinners, brand activations. Up to 150 guests standing. One of the few multi-format venue hire options in central Ho Chi Minh City with genuine architectural character.</p>
          <a href="<?php echo esc_url( home_url( '/events' ) ); ?>" class="page-inner__card-cta">Plan Your Event &rarr;</a>
        </article>

      </div>
    </div>
  </section>

  <!-- ── 3. The Building ─────────────────────────── -->
  <section class="page-inner__section page-inner__section--alt" aria-labelledby="building-title">
    <div class="page-inner__container">
      <div class="page-inner__split">

        <div class="page-venue__building-text">
          <p class="page-inner__section-head">The Space</p>
          <h2 class="page-inner__section-title" id="building-title">A French-colonial shophouse.<br>Both floors. Outdoor area.</h2>

          <div class="page-inner__section-body">
            <p>TEMPO House occupies a narrow shophouse on Pasteur Street, one of District 3&rsquo;s most intact colonial addresses. The structure has been restored with some care: exposed brick, high ceilings, and original timber detailing are present throughout. The building reads as itself — not as a renovation that has erased what made it worth keeping.</p>
            <p>The ground floor holds the café and bar in a single open room. Counter service at the front, flexible seating throughout, natural light from both ends of the space. The counter transitions from espresso bar to cocktail bar as the day shifts — the same physical space, different programme. Level 1 is a column-free gallery floor with neutral walls and adjustable track lighting, designed to hold rotating exhibitions without competing with them. A connecting outdoor area links the two levels and functions as a reception zone, spill-out space, or pre-event gathering point.</p>
            <p>For exclusive hire, the full building is available across both floors and the outdoor area, along with bar, coffee, lighting, and sound equipment. For multi-format venue hire in Ho Chi Minh City — product launches, private celebrations, corporate events — 218c Pasteur offers a setting with actual architectural character rather than a hotel ballroom dressed for the occasion.</p>
          </div>
        </div>

        <div class="page-inner__img-placeholder" role="img" aria-label="Interior of TEMPO House, District 3 Saigon">
          <span>Building &mdash; Image Coming Soon</span>
        </div>

      </div>
    </div>
  </section>

  <!-- ── 4. The Neighbourhood ────────────────────── -->
  <section class="page-inner__section" aria-labelledby="neighbourhood-title">
    <div class="page-inner__container">
      <div class="page-inner__split">

        <div class="page-inner__img-placeholder" role="img" aria-label="Pasteur Street, District 3, Ho Chi Minh City">
          <span>Neighbourhood &mdash; Image Coming Soon</span>
        </div>

        <div class="page-venue__neighbourhood-text">
          <p class="page-inner__section-head">District 3</p>
          <h2 class="page-inner__section-title" id="neighbourhood-title">Pasteur Street.<br>District 3, Ho Chi Minh City.</h2>

          <div class="page-inner__section-body">
            <p>Pasteur Street runs through the heart of District 3 under a canopy of old-growth trees. The streetscape is predominantly colonial — low-rise facades, independent businesses, residences above shops. It is one of the few streets in central Saigon that has retained this quality while remaining genuinely active. 218c sits on a stretch that includes some of the neighbourhood&rsquo;s best independent cafés and restaurants.</p>
            <p>T&#7841;o &ETH;&agrave;n park is a four-minute walk — one of the city&rsquo;s largest inner-city green spaces, used daily by residents for morning exercise and evening gatherings. B&#7871;n Th&agrave;nh market is 10 minutes on foot. The area is well covered by Grab and ride-share, with a direct drop-off point on Pasteur Street. Street parking is available in the surrounding blocks, with additional lots on Nguy&#7877;n Th&#7883; Minh Khai.</p>
          </div>

          <div class="page-inner__info-grid page-venue__neighbourhood-grid">

            <div>
              <p class="page-inner__info-label">Getting Here</p>
              <p class="page-inner__info-value">Grab &amp; ride-share drop-off on Pasteur. 10 min walk from B&#7871;n Th&agrave;nh.</p>
            </div>

            <div>
              <p class="page-inner__info-label">Parking</p>
              <p class="page-inner__info-value">Street parking on Pasteur &amp; surrounding blocks. Nearby lots on Nguy&#7877;n Th&#7883; Minh Khai.</p>
            </div>

            <div>
              <p class="page-inner__info-label">Neighbourhood</p>
              <p class="page-inner__info-value">T&#7841;o &ETH;&agrave;n park, Pasteur dining strip, colonial streetscape, D3 café scene.</p>
            </div>

          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- ── 5. Venue Facts Strip ─────────────────────── -->
  <section class="page-venue__facts-strip" aria-label="TEMPO House venue facts">
    <div class="page-inner__container">
      <div class="page-inner__info-grid">

        <div>
          <p class="page-inner__info-label">Capacity</p>
          <p class="page-inner__info-value">
            Gallery L1 &mdash; 80 standing<br>
            Full venue &mdash; 150+ standing<br>
            Caf&eacute; floor &mdash; 60 seated
          </p>
        </div>

        <div>
          <p class="page-inner__info-label">Caf&eacute; Hours</p>
          <p class="page-inner__info-value">07:00 &ndash; 17:00 daily</p>
        </div>

        <div>
          <p class="page-inner__info-label">Bar Hours</p>
          <p class="page-inner__info-value">18:00 &ndash; 01:00 daily</p>
        </div>

        <div>
          <p class="page-inner__info-label">Address</p>
          <p class="page-inner__info-value">
            218c Pasteur, Xu&acirc;n Ho&agrave;<br>
            Qu&#7853;n 3, Ho Chi Minh City
          </p>
        </div>

        <div>
          <p class="page-inner__info-label">General Enquiries</p>
          <p class="page-inner__info-value">
            <a href="mailto:hello@tempohouse.com.vn">hello@tempohouse.com.vn</a>
          </p>
        </div>

        <div>
          <p class="page-inner__info-label">Events Enquiries</p>
          <p class="page-inner__info-value">
            <a href="mailto:events@tempohouse.com.vn">events@tempohouse.com.vn</a>
          </p>
        </div>

      </div>
    </div>
  </section>

  <!-- ── 6. Footer CTA Strip ──────────────────────── -->
  <section class="page-venue__footer-cta" aria-label="Book or enquire">
    <div class="page-inner__container">
      <h2 class="page-venue__footer-cta-title">Coming in, or planning something?</h2>
      <div class="page-inner__cta-row page-venue__footer-cta-row">
        <a href="<?php echo esc_url( home_url( '/reservations' ) ); ?>" class="page-inner__cta-primary">Book a Table</a>
        <a href="<?php echo esc_url( home_url( '/event-enquiry' ) ); ?>" class="page-inner__cta-secondary">Host Your Event</a>
      </div>
    </div>
  </section>

</main>

<?php get_footer(); ?>
