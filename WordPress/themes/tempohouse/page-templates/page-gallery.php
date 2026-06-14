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
    <h1 class="page-inner__title">A gallery on the first floor.<br>An art-filled city below it.</h1>
    <p class="page-inner__lead">Level 1 of TEMPO House is a dedicated gallery space for rotating exhibitions, creative programming, and intimate events. Accessible from the café below and the street above.</p>
  </header>

  <!-- ── 2. Current Exhibition ───────────────────── -->
  <section class="page-inner__section page-inner__section--alt" aria-labelledby="exhibition-title">
    <div class="page-inner__container">
      <div class="page-inner__split">

        <div class="page-gallery__exhibition-text">
          <p class="page-inner__section-head">On Show</p>
          <h2 class="page-inner__section-title" id="exhibition-title">Rotating Exhibitions.<br>New Work. Ongoing Programme.</h2>

          <div class="page-inner__section-body">
            <p>The gallery runs a rotating programme of exhibitions from emerging and established regional artists — spanning painting, photography, installation, and mixed media. Exhibitions typically run four to six weeks, each with an opening night event open to the community. As an art gallery in Ho Chi Minh City focused on access, we work with artists at different stages of their practice, not just those with institutional backing.</p>
            <p>We believe art should be part of everyday life, not locked behind appointments or admission gates. The gallery at TEMPO House is open to all café and bar guests — walk in, look at the work, stay for a coffee. No prior arrangement needed. For those interested in showing at our gallery in Ho Chi Minh City, or partnering on a creative programme, we welcome conversations via the enquiry form below.</p>
          </div>

          <div class="page-inner__cta-row">
            <a href="<?php echo esc_url( home_url( '/contact' ) ); ?>" class="page-inner__cta-primary">Enquire About Exhibiting</a>
            <a href="<?php echo esc_url( home_url( '/whats-on' ) ); ?>" class="page-inner__cta-secondary">See What&rsquo;s On</a>
          </div>
        </div>

        <div class="page-inner__img-placeholder" role="img" aria-label="Current exhibition at TEMPO House Gallery">
          <span>Current Exhibition &mdash; Photography Available</span>
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
            <p>The gallery floor (Level 1) is a flexible, column-free space suited to exhibitions, talks, film screenings, and intimate events accommodating up to 80 guests standing. Combined with the café and bar ground floor and our outdoor area, the full venue accommodates up to 150 guests for standing events. Natural light runs through street-facing windows during the day. Neutral exhibition walls are designed to put the work first. Adjustable track lighting adapts to every programme and mood.</p>
          </div>

          <ul class="page-inner__feature-list" aria-label="Gallery venue features">
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

        <div class="page-inner__img-placeholder" role="img" aria-label="Level 1 gallery floor at TEMPO House District 3">
          <span>Gallery Space &mdash; Photography Available</span>
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
          <p class="page-inner__card-body">A proper setting for an opening night. The gallery floor is designed for work that deserves attention — good wall space, good light, room to move. We provide full room service, canapes through our catering partners, and curated music so the evening runs without friction.</p>
          <a href="<?php echo esc_url( home_url( '/event-enquiry' ) ); ?>" class="page-inner__card-cta" aria-label="Enquire about art openings and exhibitions">Enquire &rarr;</a>
        </li>

        <li class="page-inner__card">
          <span class="page-inner__card-num" aria-hidden="true">02</span>
          <h3 class="page-inner__card-title">Product Launches</h3>
          <p class="page-inner__card-body">Level 1 gives product launches a considered, editorial backdrop that a hotel ballroom cannot replicate. Brands and creative studios use the gallery space to position a launch with intent — the venue becomes part of the narrative, not just the backdrop for it.</p>
          <a href="<?php echo esc_url( home_url( '/event-enquiry' ) ); ?>" class="page-inner__card-cta" aria-label="Enquire about product launches at the gallery">Enquire &rarr;</a>
        </li>

        <li class="page-inner__card">
          <span class="page-inner__card-num" aria-hidden="true">03</span>
          <h3 class="page-inner__card-title">Intimate Gatherings</h3>
          <p class="page-inner__card-body">Birthday celebrations, engagement parties, brand dinners — the gallery is a creative space for moments that matter. Smaller than a banquet hall, more considered than a restaurant hire. An environment that makes the occasion feel earned.</p>
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
        <h2 class="page-inner__section-title" id="partnership-title">We partner with leading HCMC caterers<br>and event vendors.</h2>
        <p class="page-gallery__partnership-body">Tell us what you&rsquo;re imagining and we&rsquo;ll build it.</p>
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
