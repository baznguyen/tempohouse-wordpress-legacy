<?php
/**
 * Template Name: Gallery
 * Description: Art gallery and creative event space on Level 1 of TEMPO House.
 */
get_header();
?>

<main class="page-gallery" id="main" role="main">

  <!-- ── 1. Page Banner ──────────────────────────── -->
  <header class="page-inner__banner">
    <p class="page-inner__eyebrow">The Gallery</p>
    <h1 class="page-inner__title">Contemporary art in Saigon.<br>Level 1 is always open.</h1>
    <p class="page-inner__lead">The gallery at TEMPO House runs a year-round programme of rotating exhibitions across painting, photography, installation, and mixed media. Open to café and bar guests — no appointment, no admission fee.</p>
  </header>

  <!-- ── 1b. Gallery provenance strip ─────────────── -->
  <div class="page-gallery__provenance" aria-hidden="true">
    <div class="page-inner__container">
      <dl class="page-gallery__provenance-grid">
        <div class="page-gallery__provenance-item">
          <dt class="page-gallery__provenance-label">Programme</dt>
          <dd class="page-gallery__provenance-value">4&ndash;6 rotating exhibitions per year</dd>
        </div>
        <div class="page-gallery__provenance-item">
          <dt class="page-gallery__provenance-label">Medium</dt>
          <dd class="page-gallery__provenance-value">Painting &middot; Photography &middot; Installation</dd>
        </div>
        <div class="page-gallery__provenance-item">
          <dt class="page-gallery__provenance-label">Artists</dt>
          <dd class="page-gallery__provenance-value">Regional &amp; Southeast Asian</dd>
        </div>
        <div class="page-gallery__provenance-item">
          <dt class="page-gallery__provenance-label">Access</dt>
          <dd class="page-gallery__provenance-value">Free entry &mdash; no appointment</dd>
        </div>
      </dl>
    </div>
  </div>

  <!-- ── 2. Current Exhibition ───────────────────── -->
  <section class="page-inner__section page-inner__section--alt" aria-labelledby="exhibition-title">
    <div class="page-inner__container">
      <div class="page-inner__split">

        <div class="page-gallery__exhibition-text">
          <p class="page-inner__section-head">On Show</p>
          <h2 class="page-inner__section-title" id="exhibition-title">Rotating Programme.<br>Regional Artists. Year-Round.</h2>

          <div class="page-inner__section-body">
            <p>The gallery runs four to six exhibitions per year, each showing for four to six weeks with an opening night open to the public. Artists are drawn from Vietnam and the broader Southeast Asian region, working across painting, photography, installation, and mixed media. We show emerging and mid-career practitioners — not only those with gallery representation. The programme is built around the work, not the CV.</p>
            <p>The gallery is open to all café and bar guests during trading hours. Walk in off Pasteur Street, take the stairs to Level 1, and spend time with the work. There is no appointment system, no ticketed entry, and no obligation to buy. For artists and organisations interested in the exhibition programme — or in using the space for a creative project — contact us via the form below.</p>
          </div>

          <div class="page-inner__cta-row">
            <a href="<?php echo esc_url( home_url( '/contact' ) ); ?>" class="page-inner__cta-primary">Enquire About Exhibiting</a>
            <a href="<?php echo esc_url( home_url( '/whats-on' ) ); ?>" class="page-inner__cta-secondary">See What&rsquo;s On</a>
          </div>
        </div>

        <div class="tempo-frame tempo-frame--placeholder" data-interactive aria-label="Current exhibition at TEMPO House Gallery">
          <div class="tempo-frame__mat">
            <div class="tempo-frame__artwork">
              <span class="tempo-frame__label">Current Exhibition</span>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- ── 3. The Space ─────────────────────────────── -->
  <section class="page-inner__section" aria-labelledby="space-title">
    <div class="page-inner__container">
      <div class="page-inner__split">

        <div class="page-gallery__space-text">
          <p class="page-inner__section-head">The Venue</p>
          <h2 class="page-inner__section-title" id="space-title">Level 1 Gallery<br>&middot; Ground Floor &middot; Outdoor</h2>

          <div class="page-inner__section-body">
            <p>The gallery floor at Level 1 is a column-free space designed to hold work without competing with it. Neutral exhibition walls, adjustable track lighting, and natural light from street-facing windows during the day. The floor is non-fixed — furniture and layout reconfigure for exhibitions, talks, film screenings, and events up to 80 guests standing. Combined with the ground floor café and bar and the connecting outdoor area, the full venue at 218c Pasteur accommodates 150 or more for standing events.</p>
          </div>

          <ul class="page-inner__feature-list page-gallery__menu-list" aria-label="Gallery venue features">
            <li class="page-inner__feature-item">Level 1: up to 80 guests (standing)</li>
            <li class="page-inner__feature-item">Ground floor + outdoor: full venue to 150+</li>
            <li class="page-inner__feature-item">Natural light + adjustable track lighting</li>
            <li class="page-inner__feature-item">Neutral exhibition walls</li>
            <li class="page-inner__feature-item">Sound system</li>
            <li class="page-inner__feature-item">Flexible layout (non-fixed)</li>
          </ul>

          <div class="page-inner__cta-row">
            <a href="<?php echo esc_url( home_url( '/event-enquiry' ) ); ?>" class="page-inner__cta-primary">Enquire About the Space</a>
          </div>
        </div>

        <div class="tempo-frame tempo-frame--placeholder" data-interactive aria-label="Level 1 gallery floor at TEMPO House District 3">
          <div class="tempo-frame__mat">
            <div class="tempo-frame__artwork">
              <span class="tempo-frame__label">Gallery Space</span>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- ── 4. Events at the Gallery ─────────────────── -->
  <section class="page-inner__section page-inner__section--alt" aria-labelledby="events-title">
    <div class="page-inner__container">

      <div class="page-gallery__events-head">
        <p class="page-inner__section-head">Events at the Gallery</p>
        <h2 class="page-inner__section-title" id="events-title">The space works as hard as the ideas inside it.</h2>
      </div>

      <ul class="page-inner__card-grid" aria-label="Event types at TEMPO House Gallery">

        <li class="page-inner__card">
          <span class="page-inner__card-num" aria-hidden="true">01</span>
          <h3 class="page-inner__card-title">Art Openings &amp; Exhibitions</h3>
          <p class="page-inner__card-body">The gallery floor is built for opening nights. Good wall space, good light, room to circulate without feeling crowded. We handle the venue setup, bar service, and coordination with catering partners so the artist and their team can focus on the work. The evening programme — from doors to close — runs without your intervention.</p>
          <a href="<?php echo esc_url( home_url( '/event-enquiry' ) ); ?>" class="page-inner__card-cta" aria-label="Enquire about art openings and exhibitions">Enquire &rarr;</a>
        </li>

        <li class="page-inner__card">
          <span class="page-inner__card-num" aria-hidden="true">02</span>
          <h3 class="page-inner__card-title">Product Launches</h3>
          <p class="page-inner__card-body">Level 1 gives a launch an editorial quality that a hotel function room does not. Brands and creative studios use the gallery space because the setting itself communicates intent — the architecture, the art programme context, the address on Pasteur Street. The venue becomes part of the story, not just where it was told.</p>
          <a href="<?php echo esc_url( home_url( '/event-enquiry' ) ); ?>" class="page-inner__card-cta" aria-label="Enquire about product launches at the gallery">Enquire &rarr;</a>
        </li>

        <li class="page-inner__card">
          <span class="page-inner__card-num" aria-hidden="true">03</span>
          <h3 class="page-inner__card-title">Intimate Gatherings</h3>
          <p class="page-inner__card-body">Birthday dinners, engagement celebrations, brand gatherings of 20 to 60 people — the gallery is more considered than a restaurant hire and smaller in scale than an event hall. The room has genuine character without demanding attention. For occasions where the setting should feel earned rather than booked, this is the right space in District 3.</p>
          <a href="<?php echo esc_url( home_url( '/event-enquiry' ) ); ?>" class="page-inner__card-cta" aria-label="Enquire about intimate gatherings at the gallery">Enquire &rarr;</a>
        </li>

      </ul>
    </div>
  </section>

  <!-- ── 5. Partnership Callout ────────────────────── -->
  <section class="page-inner__section" aria-labelledby="partnership-title">
    <div class="page-inner__container">
      <div class="page-gallery__partnership-callout">
        <p class="page-inner__section-head">Working Together</p>
        <h2 class="page-inner__section-title" id="partnership-title">You bring the occasion.<br>We bring everything else.</h2>
        <p class="page-gallery__partnership-body">Tell us what you&rsquo;re planning — we&rsquo;ll put together the right team of vendors, caterers, and crew from our network of trusted partners in Ho Chi Minh City. One point of contact. No chasing.</p>
        <div class="page-inner__cta-row page-gallery__partnership-cta-row">
          <a href="<?php echo esc_url( home_url( '/event-enquiry' ) ); ?>" class="page-inner__cta-primary">Start the Conversation</a>
        </div>
      </div>
    </div>
  </section>

  <!-- ── 6. Info Strip ────────────────────────────── -->
  <section class="page-gallery__info-strip" aria-label="TEMPO House Gallery information">
    <div class="page-inner__container">
      <div class="page-inner__info-grid">

        <div>
          <p class="page-inner__info-label">Gallery Hours</p>
          <p class="page-inner__info-value">
            Open during caf&eacute; hours<br>
            + event programme &middot; Level 1
          </p>
        </div>

        <div>
          <p class="page-inner__info-label">Location</p>
          <p class="page-inner__info-value">
            218c Pasteur, Xu&acirc;n Ho&agrave;<br>
            Qu&#7853;n 3, Ho Chi Minh City
          </p>
        </div>

        <div>
          <p class="page-inner__info-label">Contact</p>
          <p class="page-inner__info-value">
            <a href="mailto:events@tempohouse.com.vn">events@tempohouse.com.vn</a><br>
            <a href="<?php echo esc_url( home_url( '/event-enquiry' ) ); ?>">Event enquiry form</a>
          </p>
        </div>

      </div>
    </div>
  </section>

</main>

<?php get_footer(); ?>
