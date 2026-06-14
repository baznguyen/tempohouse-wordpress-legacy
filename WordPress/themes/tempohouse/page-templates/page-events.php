<?php
/**
 * Template Name: Events
 * Description: Private events overview — spaces, event types, catering, floor plan, and how it works.
 */
get_header();
?>

<main class="page-events" id="main" role="main">

  <!-- ── 1. Page Banner ──────────────────────────── -->
  <header class="page-inner__banner page-events__banner">
    <p class="page-inner__eyebrow">Private Events</p>
    <h1 class="page-inner__title">Your event. Our venue.<br>A space that does the heavy lifting.</h1>
    <p class="page-inner__lead">TEMPO House is available for private hire &mdash; the whole venue or by the floor. Product launches, gallery openings, birthdays, corporate dinners, weddings, and everything in between.</p>
    <div class="page-inner__cta-row">
      <a href="<?php echo esc_url( home_url( '/event-enquiry' ) ); ?>" class="page-inner__cta-primary">Start Your Enquiry</a>
      <a href="<?php echo esc_url( home_url( '/venue' ) ); ?>" class="page-inner__cta-secondary">See the Spaces &rarr;</a>
    </div>
  </header>

  <!-- ── 2. The Spaces ───────────────────────────── -->
  <section class="page-inner__section" aria-labelledby="spaces-title">
    <div class="page-inner__container">

      <div class="page-inner__section-head">
        <p class="page-inner__section-head">Available Spaces</p>
        <h2 class="page-inner__section-title" id="spaces-title">Three spaces.<br>One address.</h2>
      </div>

      <div class="page-events__spaces-grid">

        <article class="page-events__space-card">
          <div class="page-events__space-card-inner">
            <div class="page-inner__img-placeholder page-events__space-img" role="img" aria-label="Level 1 Gallery at TEMPO House">
              <span>Gallery L1 &mdash; Image Coming Soon</span>
            </div>
            <div class="page-events__space-card-body">
              <p class="page-inner__info-label">Space 01</p>
              <h3 class="page-events__space-title">Level 1 Gallery</h3>
              <p class="page-events__space-desc">Column-free space with neutral walls and adjustable track lighting. The cleanest blank canvas in the building.</p>
              <dl class="page-events__space-meta">
                <div>
                  <dt class="page-inner__info-label">Capacity</dt>
                  <dd class="page-inner__info-value">Up to 80 standing &bull; 40 seated</dd>
                </div>
                <div>
                  <dt class="page-inner__info-label">Best For</dt>
                  <dd class="page-inner__info-value">Exhibitions, launches, cocktail receptions</dd>
                </div>
              </dl>
            </div>
          </div>
        </article>

        <article class="page-events__space-card">
          <div class="page-events__space-card-inner">
            <div class="page-inner__img-placeholder page-events__space-img" role="img" aria-label="Ground Floor café and bar at TEMPO House">
              <span>Ground Floor &mdash; Image Coming Soon</span>
            </div>
            <div class="page-events__space-card-body">
              <p class="page-inner__info-label">Space 02</p>
              <h3 class="page-events__space-title">Ground Floor</h3>
              <p class="page-events__space-desc">Caf&eacute; and bar floor with indoor and outdoor flow. A setting with genuine character &mdash; counter, terrace, and street-side light.</p>
              <dl class="page-events__space-meta">
                <div>
                  <dt class="page-inner__info-label">Capacity</dt>
                  <dd class="page-inner__info-value">Up to 60 seated &bull; 100 standing</dd>
                </div>
                <div>
                  <dt class="page-inner__info-label">Best For</dt>
                  <dd class="page-inner__info-value">Dinners, cocktail hours, daytime events</dd>
                </div>
              </dl>
            </div>
          </div>
        </article>

        <article class="page-events__space-card">
          <div class="page-events__space-card-inner">
            <div class="page-inner__img-placeholder page-events__space-img" role="img" aria-label="Full venue hire at TEMPO House, District 3 HCMC">
              <span>Full Venue &mdash; Image Coming Soon</span>
            </div>
            <div class="page-events__space-card-body">
              <p class="page-inner__info-label">Space 03</p>
              <h3 class="page-events__space-title">Full Venue</h3>
              <p class="page-events__space-desc">Both floors plus the outdoor area under exclusive hire. The building becomes yours for the night &mdash; bar equipment, lighting rig, and all.</p>
              <dl class="page-events__space-meta">
                <div>
                  <dt class="page-inner__info-label">Capacity</dt>
                  <dd class="page-inner__info-value">150+ standing</dd>
                </div>
                <div>
                  <dt class="page-inner__info-label">Best For</dt>
                  <dd class="page-inner__info-value">Large receptions, multi-format events, exclusive hire</dd>
                </div>
              </dl>
            </div>
          </div>
        </article>

      </div>
    </div>
  </section>

  <!-- ── 3. Event Types ──────────────────────────── -->
  <section class="page-inner__section page-inner__section--alt" aria-labelledby="event-types-title">
    <div class="page-inner__container">

      <div class="page-inner__section-head">
        <p class="page-inner__section-head">What We Host</p>
        <h2 class="page-inner__section-title" id="event-types-title">Every type of occasion.<br>One address in District 3.</h2>
      </div>

      <div class="page-events__event-types-grid">

        <article class="page-inner__card">
          <span class="page-inner__card-num">01</span>
          <h3 class="page-inner__card-title">Product Launches</h3>
          <p class="page-inner__card-body">Gallery L1 gives your product the setting it deserves &mdash; clean walls, focused lighting, and an audience that&rsquo;s primed to be impressed.</p>
          <a href="<?php echo esc_url( home_url( '/events/product-launch' ) ); ?>" class="page-inner__card-cta">Learn more &rarr;</a>
        </article>

        <article class="page-inner__card">
          <span class="page-inner__card-num">02</span>
          <h3 class="page-inner__card-title">Corporate Events</h3>
          <p class="page-inner__card-body">Team dinners, client entertaining, and off-sites that feel nothing like a hotel conference room. Flexible layout, full AV, proper food.</p>
          <a href="<?php echo esc_url( home_url( '/events/corporate-events' ) ); ?>" class="page-inner__card-cta">Learn more &rarr;</a>
        </article>

        <article class="page-inner__card">
          <span class="page-inner__card-num">03</span>
          <h3 class="page-inner__card-title">Birthday Celebrations</h3>
          <p class="page-inner__card-body">Whether you&rsquo;re after cocktails for 30 or a seated dinner for 60, we make birthdays feel like the occasion they are.</p>
          <a href="<?php echo esc_url( home_url( '/events/birthday-celebration' ) ); ?>" class="page-inner__card-cta">Learn more &rarr;</a>
        </article>

        <article class="page-inner__card">
          <span class="page-inner__card-num">04</span>
          <h3 class="page-inner__card-title">Intimate Gatherings</h3>
          <p class="page-inner__card-body">Smaller doesn&rsquo;t mean lesser. From 20-person dinner parties to exclusive bar nights, we curate private moments that feel genuinely special.</p>
          <a href="<?php echo esc_url( home_url( '/events/intimate-gatherings' ) ); ?>" class="page-inner__card-cta">Learn more &rarr;</a>
        </article>

        <article class="page-inner__card">
          <span class="page-inner__card-num">05</span>
          <h3 class="page-inner__card-title">Weddings</h3>
          <p class="page-inner__card-body">A French-colonial shophouse on one of Saigon&rsquo;s best streets. The kind of wedding venue that photographs like it was made for it.</p>
          <a href="<?php echo esc_url( home_url( '/events/intimate-weddings' ) ); ?>" class="page-inner__card-cta">Learn more &rarr;</a>
        </article>

        <article class="page-inner__card">
          <span class="page-inner__card-num">06</span>
          <h3 class="page-inner__card-title">Engagement Parties</h3>
          <p class="page-inner__card-body">Celebrate the start of something with a space that already feels like a destination. Cocktails, great food, a room worth sharing.</p>
          <a href="<?php echo esc_url( home_url( '/events/engagement-party' ) ); ?>" class="page-inner__card-cta">Learn more &rarr;</a>
        </article>

        <article class="page-inner__card">
          <span class="page-inner__card-num">07</span>
          <h3 class="page-inner__card-title">Art Exhibitions</h3>
          <p class="page-inner__card-body">Gallery L1 is designed to hold rotating exhibitions. Column-free, neutral, and lit for art. The space moves out of the work&rsquo;s way.</p>
          <a href="<?php echo esc_url( home_url( '/events/art-exhibitions' ) ); ?>" class="page-inner__card-cta">Learn more &rarr;</a>
        </article>

        <article class="page-inner__card">
          <span class="page-inner__card-num">08</span>
          <h3 class="page-inner__card-title">Brand Activations</h3>
          <p class="page-inner__card-body">A crowd-drawing address in inner Saigon. Flexible layout and high visual impact make TEMPO House the right stage for experiential campaigns.</p>
          <a href="<?php echo esc_url( home_url( '/events/brand-activation' ) ); ?>" class="page-inner__card-cta">Learn more &rarr;</a>
        </article>

      </div>
    </div>
  </section>

  <!-- ── 4. Catering & Partners ──────────────────── -->
  <section class="page-inner__section" aria-labelledby="catering-title">
    <div class="page-inner__container">
      <div class="page-inner__split">

        <div class="page-events__catering-text">
          <p class="page-inner__section-head">Catering &amp; Partners</p>
          <h2 class="page-inner__section-title" id="catering-title">We work with<br>the best.</h2>
          <div class="page-inner__section-body">
            <p>We partner with leading HCMC caterers and event vendors so you don&rsquo;t have to source them separately. Food and beverage packages, floral, photography, A/V, and event styling can all be arranged through us or your own suppliers &mdash; we work both ways.</p>
          </div>
          <ul class="page-inner__feature-list">
            <li class="page-inner__feature-item">Catering partners &mdash; full canaph&eacute; to sit-down</li>
            <li class="page-inner__feature-item">Bar packages from our cocktail menu</li>
            <li class="page-inner__feature-item">Floral &amp; event styling coordination</li>
            <li class="page-inner__feature-item">A/V &amp; sound systems</li>
            <li class="page-inner__feature-item">Photography referral network</li>
          </ul>
        </div>

        <div class="page-inner__img-placeholder page-events__catering-img" role="img" aria-label="Catering and partners at TEMPO House private events">
          <span>Catering &mdash; Image Coming Soon</span>
        </div>

      </div>
    </div>
  </section>

  <!-- ── 5. Floor Plan ───────────────────────────── -->
  <section class="page-inner__section page-inner__section--alt" aria-labelledby="floorplan-title">
    <div class="page-inner__container">

      <div class="page-inner__section-head">
        <p class="page-inner__section-head">Space &amp; Layout</p>
        <h2 class="page-inner__section-title" id="floorplan-title">The floor plan.</h2>
      </div>

      <div class="page-events__floorplan-placeholder" role="img" aria-label="TEMPO House floor plan placeholder">
        <span class="page-events__floorplan-label">Floor Plan &mdash; Coming Soon</span>
      </div>

      <p class="page-events__floorplan-caption">Full floor plan available on request &mdash; email <a href="mailto:events@tempohouse.com.vn">events@tempohouse.com.vn</a></p>

    </div>
  </section>

  <!-- ── 6. Pricing & Process ────────────────────── -->
  <section class="page-inner__section" aria-labelledby="process-title">
    <div class="page-inner__container">

      <div class="page-inner__section-head">
        <p class="page-inner__section-head">The Process</p>
        <h2 class="page-inner__section-title" id="process-title">How it works.</h2>
      </div>

      <ol class="page-events__steps" aria-label="Event planning steps">

        <li class="page-events__step">
          <span class="page-events__step-num" aria-hidden="true">1</span>
          <div class="page-events__step-body">
            <h3 class="page-events__step-title">Tell us about your event</h3>
            <p class="page-events__step-desc">Use our enquiry form to share the basics &mdash; date, guest count, type of occasion, and anything else that matters. No commitment required.</p>
            <a href="<?php echo esc_url( home_url( '/event-enquiry' ) ); ?>" class="page-inner__cta-secondary">Start the enquiry &rarr;</a>
          </div>
        </li>

        <li class="page-events__step">
          <span class="page-events__step-num" aria-hidden="true">2</span>
          <div class="page-events__step-body">
            <h3 class="page-events__step-title">We match you to the right space and package</h3>
            <p class="page-events__step-desc">We&rsquo;ll come back to you with the best-fit floor, layout options, and catering and vendor combinations that suit your brief and budget.</p>
          </div>
        </li>

        <li class="page-events__step">
          <span class="page-events__step-num" aria-hidden="true">3</span>
          <div class="page-events__step-body">
            <h3 class="page-events__step-title">We handle the details &mdash; you focus on the occasion</h3>
            <p class="page-events__step-desc">From vendor coordination to day-of setup, our events team takes care of the logistics so you can show up to something already running smoothly.</p>
          </div>
        </li>

      </ol>

      <div class="page-inner__cta-row page-events__process-cta">
        <a href="<?php echo esc_url( home_url( '/event-enquiry' ) ); ?>" class="page-inner__cta-primary">Start the Conversation</a>
        <a href="mailto:events@tempohouse.com.vn" class="page-inner__cta-secondary">events@tempohouse.com.vn</a>
      </div>

    </div>
  </section>

</main>

<?php get_footer(); ?>
