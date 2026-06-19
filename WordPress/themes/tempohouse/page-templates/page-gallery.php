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
    <p class="page-inner__eyebrow">The Gallery &mdash; Level 1</p>
    <h1 class="page-inner__title">Contemporary art in Saigon.<br>Level 1 is always open.</h1>
    <p class="page-inner__lead">A rotating gallery and platform for Vietnamese and Southeast Asian artists at 218c Pasteur, District 3. Free entry &mdash; open to everyone, every day.</p>
  </header>

  <!-- ── 1b. Catalogue strip ───────────────────────── -->
  <div class="page-gallery__catalogue" aria-hidden="true">
    <div class="page-inner__container">
      <dl class="page-gallery__catalogue-grid">
        <div class="page-gallery__catalogue-item">
          <dt class="page-gallery__catalogue-label">Programme</dt>
          <dd class="page-gallery__catalogue-value">4&ndash;6 exhibitions per year</dd>
        </div>
        <div class="page-gallery__catalogue-item">
          <dt class="page-gallery__catalogue-label">Medium</dt>
          <dd class="page-gallery__catalogue-value">Painting &middot; Photography &middot; Installation</dd>
        </div>
        <div class="page-gallery__catalogue-item">
          <dt class="page-gallery__catalogue-label">Artists</dt>
          <dd class="page-gallery__catalogue-value">Vietnamese &amp; Southeast Asian</dd>
        </div>
        <div class="page-gallery__catalogue-item">
          <dt class="page-gallery__catalogue-label">Admission</dt>
          <dd class="page-gallery__catalogue-value">Free entry &mdash; no appointment</dd>
        </div>
      </dl>
    </div>
  </div>

  <!-- ── 2. Gallery Wall — secondary hero (mirrors moods/spaces exactly) ── -->
  <section class="page-gallery__wall moods" aria-label="Gallery wall — current works">

    <p class="moods__eyebrow">Level 1 &mdash; The Gallery</p>

    <div class="moods__bleed-text" aria-hidden="true">
      <span class="moods__bleed-line">Works</span>
      <span class="moods__bleed-line">on view</span>
    </div>

    <div class="moods__frames-wrap">

      <article class="moods__frame" data-frame="gallery-left" style="--speed: -0.07;" aria-label="Work I">
        <div class="moods__frame-art">
          <div class="moods__mat">
            <div class="moods__artwork">
              <span class="moods__num">I</span>
              <div class="moods__title-bar">
                <p class="moods__label-mode">Current Exhibition</p>
                <h3 class="moods__label-title">Work I</h3>
              </div>
            </div>
          </div>
        </div>
      </article>

      <article class="moods__frame" data-frame="gallery-center" style="--speed: -0.04;" aria-label="Work II">
        <div class="moods__frame-art">
          <div class="moods__mat">
            <div class="moods__artwork">
              <span class="moods__num">II</span>
              <div class="moods__title-bar">
                <p class="moods__label-mode">Current Exhibition</p>
                <h3 class="moods__label-title">Work II</h3>
              </div>
            </div>
          </div>
        </div>
      </article>

      <article class="moods__frame" data-frame="gallery-right" style="--speed: 0.05;" aria-label="Work III">
        <div class="moods__frame-art">
          <div class="moods__mat">
            <div class="moods__artwork">
              <span class="moods__num">III</span>
              <div class="moods__title-bar">
                <p class="moods__label-mode">Current Exhibition</p>
                <h3 class="moods__label-title">Work III</h3>
              </div>
            </div>
          </div>
        </div>
      </article>

    </div>

    <nav class="moods__carousel-nav" aria-label="Gallery wall navigation">
      <button class="moods__nav-btn moods__nav-prev" aria-label="Previous" disabled>
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
          <path d="M10 12L6 8l4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
      <div class="moods__dots">
        <button class="moods__dot moods__dot--active" aria-label="Work I"></button>
        <button class="moods__dot" aria-label="Work II"></button>
        <button class="moods__dot" aria-label="Work III"></button>
      </div>
      <button class="moods__nav-btn moods__nav-next" aria-label="Next">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
          <path d="M6 12l4-4-4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
    </nav>

  </section>

  <!-- ── 3. Programme Statement ─────────────────────── -->
  <section class="page-gallery__statement" aria-labelledby="statement-title">
    <div class="page-inner__container">
      <p class="page-inner__section-head">The Programme</p>
      <blockquote class="page-gallery__statement-text" id="statement-title">
        We show work by Vietnamese and Southeast Asian artists &mdash; painting, photography, installation, and mixed media. Emerging and mid-career practitioners, not only those with gallery representation. The programme is built around the work, not the CV.
      </blockquote>
      <div class="page-gallery__statement-meta">
        <p class="page-gallery__statement-detail">Four to six exhibitions per year, each showing for four to six weeks with a public opening night. Walk in off Pasteur Street, take the stairs to Level 1, and spend time with the work. No ticket. No obligation to buy.</p>
        <div class="page-inner__cta-row">
          <a href="<?php echo esc_url( home_url( '/whats-on' ) ); ?>" class="page-inner__cta-primary">See What&rsquo;s On</a>
          <a href="<?php echo esc_url( home_url( '/contact' ) ); ?>" class="page-inner__cta-secondary">Propose a Show</a>
        </div>
      </div>
    </div>
  </section>

  <!-- ── 4. For Artists ──────────────────────────────── -->
  <section class="page-inner__section page-inner__section--alt page-gallery__artists" aria-labelledby="artists-title">
    <div class="page-inner__container">
      <div class="page-inner__split">

        <div class="page-gallery__artists-text">
          <p class="page-inner__section-head">For Artists</p>
          <h2 class="page-inner__section-title" id="artists-title">Your work deserves to be seen. We&rsquo;ll make sure it is.</h2>

          <div class="page-inner__section-body">
            <p>We read your statement before you arrive. Opening nights are built around the work &mdash; not the crowd. We handle bar service, coordination with caterers, and promotion through our channels so the artist and their team can focus on what matters. One point of contact, from enquiry to close.</p>
          </div>

          <ul class="page-inner__feature-list page-gallery__artists-specs" aria-label="Gallery technical specifications">
            <li class="page-inner__feature-item">Track lighting &mdash; adjustable, warm white (3000K)</li>
            <li class="page-inner__feature-item">Neutral exhibition walls &middot; floor-to-ceiling hang</li>
            <li class="page-inner__feature-item">Column-free floor &middot; flexible layout</li>
            <li class="page-inner__feature-item">Natural light from Pasteur Street windows</li>
            <li class="page-inner__feature-item">Sound system for openings, talks, and screenings</li>
            <li class="page-inner__feature-item">Bar service for opening nights</li>
            <li class="page-inner__feature-item">Promotion via newsletter &amp; social channels</li>
          </ul>

          <div class="page-inner__cta-row">
            <a href="<?php echo esc_url( home_url( '/contact' ) ); ?>" class="page-inner__cta-primary">Propose a Show</a>
            <a href="<?php echo esc_url( home_url( '/whats-on' ) ); ?>" class="page-inner__cta-secondary">See Current Programme</a>
          </div>
        </div>

        <div class="tempo-frame page-gallery__artists-frame" data-interactive aria-label="TEMPO House Gallery — Level 1 exhibition space">
          <div class="tempo-frame__mat">
            <div class="tempo-frame__artwork">
              <img class="tempo-frame__img" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/gallery/gallery-artists.jpg" alt="TEMPO House Gallery Level 1 exhibition space" loading="lazy">
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- ── 5. Gallery Walk — wall (desktop) + carousel (mobile) ─────────── -->
  <section class="page-gallery__walk" aria-label="Gallery walk — works on show">

    <div class="page-gallery__walk-viewport" data-gallery-walk>
      <div class="page-gallery__walk-track">

        <div class="tempo-frame page-gallery__walk-frame" data-interactive aria-label="Work I">
          <div class="tempo-frame__mat">
            <div class="tempo-frame__artwork"><img class="tempo-frame__img" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/gallery/gallery-walk-I.jpg" alt="Gallery work I" loading="lazy"></div>
          </div>
        </div>

        <div class="tempo-frame page-gallery__walk-frame page-gallery__walk-frame--wide" data-interactive aria-label="Work II">
          <div class="tempo-frame__mat">
            <div class="tempo-frame__artwork"><img class="tempo-frame__img" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/gallery/gallery-walk-II.jpg" alt="Gallery work II" loading="lazy"></div>
          </div>
        </div>

        <div class="tempo-frame page-gallery__walk-frame page-gallery__walk-frame--tall" data-interactive aria-label="Work III">
          <div class="tempo-frame__mat">
            <div class="tempo-frame__artwork"><img class="tempo-frame__img" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/gallery/gallery-walk-III.jpg" alt="Gallery work III" loading="lazy"></div>
          </div>
        </div>

        <div class="tempo-frame page-gallery__walk-frame page-gallery__walk-frame--wide" data-interactive aria-label="Work IV">
          <div class="tempo-frame__mat">
            <div class="tempo-frame__artwork"><img class="tempo-frame__img" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/gallery/gallery-walk-IV.jpg" alt="Gallery work IV" loading="lazy"></div>
          </div>
        </div>

        <div class="tempo-frame page-gallery__walk-frame" data-interactive aria-label="Work V">
          <div class="tempo-frame__mat">
            <div class="tempo-frame__artwork"><img class="tempo-frame__img" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/gallery/gallery-walk-V.jpg" alt="Gallery work V" loading="lazy"></div>
          </div>
        </div>

      </div>
    </div>

    <nav class="page-gallery__walk-nav" aria-label="Gallery walk navigation">
      <button class="page-gallery__walk-nav-btn page-gallery__walk-nav-prev" aria-label="Previous" disabled>
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
          <path d="M10 12L6 8l4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
      <div class="page-gallery__walk-dots">
        <button class="page-gallery__walk-dot page-gallery__walk-dot--active" aria-label="Work I"></button>
        <button class="page-gallery__walk-dot" aria-label="Work II"></button>
        <button class="page-gallery__walk-dot" aria-label="Work III"></button>
        <button class="page-gallery__walk-dot" aria-label="Work IV"></button>
        <button class="page-gallery__walk-dot" aria-label="Work V"></button>
      </div>
      <button class="page-gallery__walk-nav-btn page-gallery__walk-nav-next" aria-label="Next">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
          <path d="M6 12l4-4-4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
    </nav>

    <p class="page-gallery__walk-caption" aria-hidden="true">Works currently hanging at Level 1, TEMPO House &mdash; 218c Pasteur, Qu&#7853;n 3</p>
  </section>

  <!-- ── 6. Manifesto ──────────────────────────────── -->
  <section class="page-gallery__manifesto" aria-hidden="true">
    <div class="page-inner__container">
      <p class="page-gallery__manifesto-text">Art belongs in the everyday.</p>
    </div>
  </section>

  <!-- ── 7. The Space ──────────────────────────────── -->
  <section class="page-inner__section page-inner__section--alt" aria-labelledby="space-title">
    <div class="page-inner__container">
      <div class="page-inner__split">

        <div class="page-gallery__space-text">
          <p class="page-inner__section-head">The Space</p>
          <h2 class="page-inner__section-title" id="space-title">Level 1 &mdash; a column-free floor built to hold work.</h2>

          <div class="page-inner__section-body">
            <p>Neutral exhibition walls, adjustable track lighting at warm white (3000K) for accurate colour rendering, and natural light from street-facing windows for daytime viewing and documentation. The floor is non-fixed &mdash; layout reconfigures for exhibitions, artist talks, film screenings, and events to 80 standing.</p>
            <p>Combined with the ground floor caf&eacute;, bar, and outdoor terrace, the full venue at 218c Pasteur accommodates 150 or more for opening nights and standing events.</p>
          </div>

          <ul class="page-inner__feature-list page-gallery__space-specs" aria-label="Gallery space specifications">
            <li class="page-inner__feature-item">Level 1: up to 80 guests (standing)</li>
            <li class="page-inner__feature-item">Full venue: caf&eacute; + bar + outdoor to 150+</li>
            <li class="page-inner__feature-item">Track lighting &mdash; warm white 3000K</li>
            <li class="page-inner__feature-item">Neutral walls &middot; floor-to-ceiling &middot; flexible layout</li>
            <li class="page-inner__feature-item">Natural light &middot; Pasteur Street frontage</li>
            <li class="page-inner__feature-item">Sound system</li>
          </ul>

          <div class="page-inner__cta-row">
            <a href="<?php echo esc_url( home_url( '/event-enquiry' ) ); ?>" class="page-inner__cta-primary">Enquire About the Space</a>
            <a href="<?php echo esc_url( home_url( '/venue' ) ); ?>" class="page-inner__cta-secondary">View Venue Details</a>
          </div>
        </div>

        <div class="tempo-frame page-gallery__level1-img" data-interactive aria-label="Level 1 gallery floor at TEMPO House, District 3">
          <div class="tempo-frame__mat">
            <div class="tempo-frame__artwork">
              <img class="tempo-frame__img" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/gallery/gallery-level1.jpg" alt="Level 1 gallery floor at TEMPO House District 3" loading="lazy">
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- ── 8. Hire the Gallery ───────────────────────── -->
  <section class="page-inner__section" aria-labelledby="hire-title">
    <div class="page-inner__container">

      <div class="page-gallery__events-head">
        <p class="page-inner__section-head">Hire the Gallery</p>
        <h2 class="page-inner__section-title" id="hire-title">The space works as hard as the ideas inside it.</h2>
      </div>

      <ul class="page-gallery__programme-list" aria-label="Hire categories at TEMPO House Gallery">

        <li class="page-gallery__programme-item">
          <span class="page-gallery__programme-num" aria-hidden="true">01</span>
          <h3 class="page-gallery__programme-title">Art Openings &amp; Private Views</h3>
          <p class="page-gallery__programme-body">Opening nights built around the work. Good wall space, good light, room to circulate without crowding the art. Bar service, catering coordination, and venue setup handled &mdash; so the artist and their team can focus entirely on the work.</p>
          <a href="<?php echo esc_url( home_url( '/event-enquiry' ) ); ?>" class="page-gallery__programme-link" aria-label="Enquire about art openings and private views">Enquire &rarr;</a>
        </li>

        <li class="page-gallery__programme-item">
          <span class="page-gallery__programme-num" aria-hidden="true">02</span>
          <h3 class="page-gallery__programme-title">Talks, Screenings &amp; Performances</h3>
          <p class="page-gallery__programme-body">Artist talks, panel discussions, film screenings, live performances. Flexible layout, sound system, and an audience of 20 to 60 in a room built to take ideas seriously. Not a conference room with art on the walls &mdash; a gallery that also holds events.</p>
          <a href="<?php echo esc_url( home_url( '/event-enquiry' ) ); ?>" class="page-gallery__programme-link" aria-label="Enquire about talks and screenings">Enquire &rarr;</a>
        </li>

        <li class="page-gallery__programme-item">
          <span class="page-gallery__programme-num" aria-hidden="true">03</span>
          <h3 class="page-gallery__programme-title">Brand &amp; Creative Launches</h3>
          <p class="page-gallery__programme-body">Level 1 gives a launch an editorial quality a hotel function room cannot. Brands and creative studios use the gallery because the setting itself communicates intent &mdash; the architecture, the art programme context, the address on Pasteur Street. The venue becomes part of the story.</p>
          <a href="<?php echo esc_url( home_url( '/event-enquiry' ) ); ?>" class="page-gallery__programme-link" aria-label="Enquire about brand and creative launches">Enquire &rarr;</a>
        </li>

        <li class="page-gallery__programme-item">
          <span class="page-gallery__programme-num" aria-hidden="true">04</span>
          <h3 class="page-gallery__programme-title">Private Gatherings</h3>
          <p class="page-gallery__programme-body">Birthday dinners, collector evenings, brand gatherings of 20 to 60 people. More considered than a restaurant hire, smaller in scale than an event hall. For occasions where the setting should feel earned, not simply booked.</p>
          <a href="<?php echo esc_url( home_url( '/event-enquiry' ) ); ?>" class="page-gallery__programme-link" aria-label="Enquire about private gatherings">Enquire &rarr;</a>
        </li>

      </ul>
    </div>
  </section>

  <!-- ── 9. Callout ────────────────────────────────── -->
  <section class="page-gallery__callout" aria-labelledby="callout-title">
    <div class="page-inner__container">
      <p class="page-inner__section-head">Working Together</p>
      <h2 class="page-gallery__callout-title" id="callout-title">You bring the work.<br>We bring everything else.</h2>
      <p class="page-gallery__callout-body">Whether you&rsquo;re an artist proposing a solo show, a manager planning an opening night, or a brand looking for a space with genuine cultural context &mdash; one point of contact. Trusted vendors, caterers, and crew from our network in Ho Chi Minh City. No chasing.</p>
      <div class="page-inner__cta-row page-gallery__callout-cta">
        <a href="<?php echo esc_url( home_url( '/event-enquiry' ) ); ?>" class="page-inner__cta-primary">Start the Conversation</a>
        <a href="<?php echo esc_url( home_url( '/contact' ) ); ?>" class="page-inner__cta-secondary">Propose a Show</a>
      </div>
    </div>
  </section>

  <!-- ── 10. Info Strip ─────────────────────────────── -->
  <section class="page-gallery__info-strip" aria-label="TEMPO House Gallery information">
    <div class="page-inner__container">
      <div class="page-inner__info-grid">

        <div>
          <p class="page-inner__info-label">Gallery Hours</p>
          <p class="page-inner__info-value">
            Open during caf&eacute; &amp; bar hours<br>
            + event programme &middot; Level 1
          </p>
        </div>

        <div>
          <p class="page-inner__info-label">Admission</p>
          <p class="page-inner__info-value">
            Free entry<br>
            No appointment required
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
            <a href="mailto:gallery@tempohouse.com.vn">gallery@tempohouse.com.vn</a><br>
            <a href="<?php echo esc_url( home_url( '/event-enquiry' ) ); ?>">Exhibition enquiry</a>
          </p>
        </div>

      </div>
    </div>
  </section>

</main>

<?php get_footer(); ?>
