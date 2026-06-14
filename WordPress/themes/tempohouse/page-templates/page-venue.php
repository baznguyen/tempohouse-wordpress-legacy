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
    <h1 class="page-inner__title">One building.<br>Many ways to use it.</h1>
    <p class="page-inner__lead">TEMPO House is a specialty café by day, a cocktail bar by night, an art gallery upstairs, and a private event space whenever you need one. District 3, Ho Chi Minh City.</p>
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
          <p class="page-inner__card-body">Where the city slows down. Specialty coffee, seasonal snacks, flexible seating.</p>
          <a href="<?php echo esc_url( home_url( '/cafe' ) ); ?>" class="page-inner__card-cta">Explore the Café &rarr;</a>
        </article>

        <article class="page-inner__card">
          <span class="page-inner__card-num">02</span>
          <h3 class="page-inner__card-title">Cocktail Bar</h3>
          <p class="page-inner__card-body">Evening opens with cocktails and an upbeat tempo. District 3&rsquo;s bar of choice.</p>
          <a href="<?php echo esc_url( home_url( '/bar' ) ); ?>" class="page-inner__card-cta">Explore the Bar &rarr;</a>
        </article>

        <article class="page-inner__card">
          <span class="page-inner__card-num">03</span>
          <h3 class="page-inner__card-title">Art Gallery</h3>
          <p class="page-inner__card-body">Level 1 rotating exhibitions and creative programme. Open to café guests.</p>
          <a href="<?php echo esc_url( home_url( '/gallery' ) ); ?>" class="page-inner__card-cta">See the Gallery &rarr;</a>
        </article>

        <article class="page-inner__card">
          <span class="page-inner__card-num">04</span>
          <h3 class="page-inner__card-title">Private Events</h3>
          <p class="page-inner__card-body">Full venue hire for product launches, celebrations, corporate, and more.</p>
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
          <h2 class="page-inner__section-title" id="building-title">A 1920s shophouse.<br>Reimagined as a destination.</h2>

          <div class="page-inner__section-body">
            <p>TEMPO House occupies a French-colonial shophouse on Pasteur Street &mdash; one of District 3&rsquo;s most characterful addresses. The original structure has been carefully restored rather than renovated beyond recognition: exposed brick, high ceilings, and timber details remain. The streetscape context &mdash; shaded pavements, colonial facades, independent businesses &mdash; is part of what makes the building feel worth arriving at.</p>
            <p>Ground floor is the café and bar: non-fixed furniture that shifts throughout the day, natural light from front to back, and a counter that transitions from espresso bar in the morning to cocktail bar by evening. Level 1 is a column-free gallery space with neutral walls and adjustable track lighting &mdash; designed to hold rotating exhibitions without getting in the way of them. A connecting outdoor area ties the two levels together and works as a reception or spill-out zone.</p>
            <p>The entire building is available for exclusive hire &mdash; both floors, the outdoor area, the bar and coffee equipment, and the lighting rig. For product launches, private celebrations, or corporate events that need a setting with genuine character rather than a hotel ballroom, TEMPO House is one of the few multi-function venues in inner Saigon that earns the word&nbsp;<em>destination</em>.</p>
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
          <h2 class="page-inner__section-title" id="neighbourhood-title">Pasteur Street.<br>One of Saigon&rsquo;s best addresses.</h2>

          <div class="page-inner__section-body">
            <p>District 3 is Saigon&rsquo;s most liveable inner-city district &mdash; French colonial architecture, shaded tree-lined streets, and the city&rsquo;s best concentration of independent cafés, restaurants, and galleries. It moves at a pace slightly removed from the District 1 bustle while staying right at the centre of things. 218c Pasteur sits at the heart of it.</p>
            <p>B&#7871;n Th&agrave;nh market is a 10-minute walk. T&#7841;o &ETH;&agrave;n park &mdash; one of Saigon&rsquo;s greenest inner-city escapes &mdash; is minutes away. The neighbourhood rewards walking and cycling; Grab and ride-share drop-offs are seamless. Street parking is available directly on Pasteur and in the surrounding blocks.</p>
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
      <h2 class="page-venue__footer-cta-title">Ready to visit, or planning something bigger?</h2>
      <div class="page-inner__cta-row page-venue__footer-cta-row">
        <a href="<?php echo esc_url( home_url( '/reservations' ) ); ?>" class="page-inner__cta-primary">Book a Table</a>
        <a href="<?php echo esc_url( home_url( '/event-enquiry' ) ); ?>" class="page-inner__cta-secondary">Host Your Event</a>
      </div>
    </div>
  </section>

</main>

<?php get_footer(); ?>
