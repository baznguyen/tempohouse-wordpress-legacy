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
    <h1 class="page-inner__title">An event venue that doesn&rsquo;t look like one.<br>Private hire, District 3, Saigon.</h1>
    <p class="page-inner__lead">TEMPO House is a gallery, bar, and caf&eacute; available for exclusive hire &mdash; by the floor or the whole building. We host product launches, corporate dinners, art exhibitions, birthdays, weddings, and brand activations.</p>
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
        <h2 class="page-inner__section-title" id="spaces-title">Hire one floor<br>or the whole building.</h2>
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
              <p class="page-events__space-desc">Column-free floor with neutral white walls and adjustable track lighting. There&rsquo;s nothing in the room that competes with what you bring in. Suitable for exhibitions, installations, and receptions where the visual setup is doing the work.</p>
              <dl class="page-events__space-meta">
                <div>
                  <dt class="page-inner__info-label">Capacity</dt>
                  <dd class="page-inner__info-value">Up to 80 standing &bull; 40 seated</dd>
                </div>
                <div>
                  <dt class="page-inner__info-label">Best For</dt>
                  <dd class="page-inner__info-value">Exhibitions, product launches, cocktail receptions</dd>
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
              <p class="page-events__space-desc">The bar and caf&eacute; floor, with indoor seating, an outdoor terrace, and full bar access. The layout has character &mdash; counter, terrace, and street-side light from Pasteur. Works well for seated dinners, cocktail hours, and anything that benefits from a lived-in feel.</p>
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
              <p class="page-events__space-desc">Both floors plus the outdoor terrace under exclusive hire. You get the gallery upstairs, the bar and terrace on the ground floor, and the full lighting and sound setup throughout. The building is entirely yours &mdash; no shared access, no overlap with other guests.</p>
              <dl class="page-events__space-meta">
                <div>
                  <dt class="page-inner__info-label">Capacity</dt>
                  <dd class="page-inner__info-value">150+ standing</dd>
                </div>
                <div>
                  <dt class="page-inner__info-label">Best For</dt>
                  <dd class="page-inner__info-value">Large receptions, multi-format events, exclusive buyout</dd>
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
        <h2 class="page-inner__section-title" id="event-types-title">Eight types of event.<br>One venue hire in District 3.</h2>
      </div>

      <div class="page-events__event-types-grid">

        <article class="page-inner__card">
          <span class="page-inner__card-num">01</span>
          <h3 class="page-inner__card-title">Product Launches</h3>
          <p class="page-inner__card-body">A product launch in a gallery photographs differently to a hotel ballroom. Gallery L1 gives you a column-free room, controlled track lighting, and walls you can use. The space is blank by design &mdash; your product is the feature.</p>
          <a href="<?php echo esc_url( home_url( '/events/product-launch' ) ); ?>" class="page-inner__card-cta">Learn more &rarr;</a>
        </article>

        <article class="page-inner__card">
          <span class="page-inner__card-num">02</span>
          <h3 class="page-inner__card-title">Corporate Events</h3>
          <p class="page-inner__card-body">Team dinners, client entertainment, and off-sites that don&rsquo;t feel like a conference room. The ground floor seats up to 60, the gallery adds a second space if you need it. Full A/V, proper cocktails, and a kitchen partner for sit-down food.</p>
          <a href="<?php echo esc_url( home_url( '/events/corporate-events' ) ); ?>" class="page-inner__card-cta">Learn more &rarr;</a>
        </article>

        <article class="page-inner__card">
          <span class="page-inner__card-num">03</span>
          <h3 class="page-inner__card-title">Birthday Celebrations</h3>
          <p class="page-inner__card-body">Cocktails for 30 on the terrace or a seated dinner for 60 on the ground floor. The venue works for birthdays because it already feels like somewhere worth going &mdash; the decoration is in the bones of the building.</p>
          <a href="<?php echo esc_url( home_url( '/events/birthday-celebration' ) ); ?>" class="page-inner__card-cta">Learn more &rarr;</a>
        </article>

        <article class="page-inner__card">
          <span class="page-inner__card-num">04</span>
          <h3 class="page-inner__card-title">Intimate Gatherings</h3>
          <p class="page-inner__card-body">The ground floor holds events as small as 20 without feeling empty. Private dinner parties, exclusive bar nights, small group celebrations &mdash; we can close off the floor so the space is entirely yours.</p>
          <a href="<?php echo esc_url( home_url( '/events/intimate-gatherings' ) ); ?>" class="page-inner__card-cta">Learn more &rarr;</a>
        </article>

        <article class="page-inner__card">
          <span class="page-inner__card-num">05</span>
          <h3 class="page-inner__card-title">Weddings</h3>
          <p class="page-inner__card-body">A French-colonial shophouse on one of Saigon&rsquo;s best streets. The gallery floor makes a ceremony space that doesn&rsquo;t need to borrow from anywhere else. For couples who want something that photographs well without manufacturing it.</p>
          <a href="<?php echo esc_url( home_url( '/events/intimate-weddings' ) ); ?>" class="page-inner__card-cta">Learn more &rarr;</a>
        </article>

        <article class="page-inner__card">
          <span class="page-inner__card-num">06</span>
          <h3 class="page-inner__card-title">Engagement Parties</h3>
          <p class="page-inner__card-body">The terrace and ground floor work well together for this. Drinks outside, dinner inside, or both floors running at once for larger groups. A space that feels like a destination without requiring a lot of extra styling to get there.</p>
          <a href="<?php echo esc_url( home_url( '/events/engagement-party' ) ); ?>" class="page-inner__card-cta">Learn more &rarr;</a>
        </article>

        <article class="page-inner__card">
          <span class="page-inner__card-num">07</span>
          <h3 class="page-inner__card-title">Art Exhibitions</h3>
          <p class="page-inner__card-body">Gallery L1 was built to hold rotating exhibitions. Column-free floor plan, neutral walls, adjustable track lighting on a dimmer, and a separate entrance from the ground floor. The space gets out of the way of the work.</p>
          <a href="<?php echo esc_url( home_url( '/events/art-exhibitions' ) ); ?>" class="page-inner__card-cta">Learn more &rarr;</a>
        </article>

        <article class="page-inner__card">
          <span class="page-inner__card-num">08</span>
          <h3 class="page-inner__card-title">Brand Activations</h3>
          <p class="page-inner__card-body">218c Pasteur draws a crowd. The full venue &mdash; gallery, bar, and terrace &mdash; gives you a flexible footprint across two floors for experiential campaigns. The address does part of the work before a single person walks in.</p>
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
          <h2 class="page-inner__section-title" id="catering-title">Food, bar, florals,<br>A/V &mdash; sourced for you.</h2>
          <div class="page-inner__section-body">
            <p>We work with a curated network of HCMC caterers and event vendors. They arrive with their own team, their own equipment, and a clear brief. You can use our partners or bring your own &mdash; either works. Bar service runs from our in-house cocktail menu and can be packaged by consumption or by package rate.</p>
          </div>
          <ul class="page-inner__feature-list">
            <li class="page-inner__feature-item">Catering partners &mdash; cana&eacute;p&eacute;s to full sit-down menus</li>
            <li class="page-inner__feature-item">Bar packages drawn from our cocktail program</li>
            <li class="page-inner__feature-item">Floral and event styling coordination</li>
            <li class="page-inner__feature-item">A/V and sound system setup</li>
            <li class="page-inner__feature-item">Photography referrals from our network</li>
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

      <p class="page-events__floorplan-caption">Full floor plan and layout options available on request &mdash; email <a href="mailto:events@tempohouse.com.vn">events@tempohouse.com.vn</a></p>

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
            <h3 class="page-events__step-title">Send us the basics</h3>
            <p class="page-events__step-desc">Fill out the enquiry form with your date, guest count, type of event, and any specifics that matter. No commitment, no sales call. We read every enquiry before responding.</p>
            <a href="<?php echo esc_url( home_url( '/event-enquiry' ) ); ?>" class="page-inner__cta-secondary">Start the enquiry &rarr;</a>
          </div>
        </li>

        <li class="page-events__step">
          <span class="page-events__step-num" aria-hidden="true">2</span>
          <div class="page-events__step-body">
            <h3 class="page-events__step-title">We come back with a proposal</h3>
            <p class="page-events__step-desc">We&rsquo;ll recommend the right floor or combination, lay out layout options for your guest count, and give you a clear picture of catering and vendor costs so you can plan against a real number.</p>
          </div>
        </li>

        <li class="page-events__step">
          <span class="page-events__step-num" aria-hidden="true">3</span>
          <div class="page-events__step-body">
            <h3 class="page-events__step-title">We handle the setup &mdash; you arrive to a running event</h3>
            <p class="page-events__step-desc">Our events team coordinates with your vendors, manages the setup timeline, and is on-site for the duration. You don&rsquo;t troubleshoot on the night.</p>
          </div>
        </li>

      </ol>

      <div class="page-inner__cta-row page-events__process-cta">
        <a href="<?php echo esc_url( home_url( '/event-enquiry' ) ); ?>" class="page-inner__cta-primary">Send an Enquiry</a>
        <a href="mailto:events@tempohouse.com.vn" class="page-inner__cta-secondary">events@tempohouse.com.vn</a>
      </div>

    </div>
  </section>

</main>

<?php get_footer(); ?>
