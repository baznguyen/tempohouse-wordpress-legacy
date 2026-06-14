<?php
/**
 * Template Name: Event Type Landing Page
 * Description: Reusable sub-page template for all private event type pages.
 *              Uses ACF fields when available; falls back to post title/content.
 *
 * Intended sub-pages:
 *   /events/product-launch        → "Product Launch Venue Ho Chi Minh City"
 *   /events/corporate-events      → "Corporate Event Space HCMC"
 *   /events/birthday-celebration  → "Birthday Venue Saigon District 3"
 *   /events/intimate-gatherings   → "Intimate Gathering Space HCMC"
 *   /events/intimate-weddings     → "Intimate Wedding Venue Ho Chi Minh City"
 *   /events/engagement-party      → "Engagement Party Venue Saigon"
 *   /events/art-exhibitions       → "Art Exhibition Space HCMC"
 *   /events/brand-activation      → "Brand Activation Venue Ho Chi Minh City"
 */
get_header();

// ── ACF field helpers (fall back gracefully if ACF not active) ──────────────

$event_eyebrow    = function_exists( 'get_field' ) ? get_field( 'event_eyebrow' )    : '';
$event_lead       = function_exists( 'get_field' ) ? get_field( 'event_lead' )       : '';
$event_hero_image = function_exists( 'get_field' ) ? get_field( 'event_hero_image' ) : null;

if ( ! $event_eyebrow ) {
	$event_eyebrow = 'Private Events';
}
?>

<main class="page-event-type" id="main" role="main">

  <!-- ── Breadcrumb ──────────────────────────────── -->
  <nav class="page-event-type__breadcrumb" aria-label="Breadcrumb">
    <ol class="page-event-type__breadcrumb-list">
      <li class="page-event-type__breadcrumb-item">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
      </li>
      <li class="page-event-type__breadcrumb-sep" aria-hidden="true">/</li>
      <li class="page-event-type__breadcrumb-item">
        <a href="<?php echo esc_url( home_url( '/events' ) ); ?>">Events</a>
      </li>
      <li class="page-event-type__breadcrumb-sep" aria-hidden="true">/</li>
      <li class="page-event-type__breadcrumb-item page-event-type__breadcrumb-item--current" aria-current="page">
        <?php echo esc_html( get_the_title() ); ?>
      </li>
    </ol>
  </nav>

  <!-- ── Top Enquiry Strip ───────────────────────── -->
  <div class="page-event-type__banner-enquire">
    <div class="page-inner__container">
      <div class="page-event-type__banner-enquire-inner">
        <p class="page-event-type__banner-enquire-text">Planning this type of event? We&rsquo;d love to hear about it.</p>
        <a href="<?php echo esc_url( home_url( '/event-enquiry' ) ); ?>" class="page-inner__cta-primary">Start Your Enquiry</a>
      </div>
    </div>
  </div>

  <!-- ── Page Banner ─────────────────────────────── -->
  <header class="page-inner__banner page-event-type__banner">
    <p class="page-inner__eyebrow"><?php echo esc_html( $event_eyebrow ); ?></p>
    <h1 class="page-inner__title"><?php echo esc_html( get_the_title() ); ?></h1>

    <?php if ( $event_lead ) : ?>
      <p class="page-inner__lead"><?php echo esc_html( $event_lead ); ?></p>
    <?php else : ?>
      <?php
      $excerpt = get_the_excerpt();
      if ( $excerpt ) : ?>
        <p class="page-inner__lead"><?php echo wp_kses_post( $excerpt ); ?></p>
      <?php endif; ?>
    <?php endif; ?>
  </header>

  <!-- ── Hero Image ──────────────────────────────── -->
  <?php if ( $event_hero_image ) : ?>
  <div class="page-event-type__hero-img">
    <img
      src="<?php echo esc_url( $event_hero_image['url'] ); ?>"
      alt="<?php echo esc_attr( $event_hero_image['alt'] ? $event_hero_image['alt'] : get_the_title() . ' at TEMPO House, District 3' ); ?>"
      loading="eager"
      decoding="async"
    >
  </div>
  <?php else : ?>
  <div class="page-inner__img-placeholder page-event-type__hero-placeholder" role="img" aria-label="<?php echo esc_attr( get_the_title() ); ?> at TEMPO House">
    <span><?php echo esc_html( get_the_title() ); ?> &mdash; Image Coming Soon</span>
  </div>
  <?php endif; ?>

  <!-- ── Main Content ────────────────────────────── -->
  <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

    <?php if ( get_the_content() ) : ?>
    <section class="page-inner__section page-event-type__content-section" aria-labelledby="event-content-title">
      <div class="page-inner__container">
        <div class="page-inner__split">

          <div class="page-event-type__content-body page-inner__section-body">
            <p class="page-inner__section-head">About This Event</p>
            <div class="page-event-type__wp-content">
              <?php the_content(); ?>
            </div>
          </div>

          <!-- ── Why TEMPO House feature list ──── -->
          <div class="page-event-type__why">
            <p class="page-inner__section-head">Why TEMPO House</p>
            <h2 class="page-inner__section-title page-event-type__why-title">Built for events<br>like this one.</h2>
            <ul class="page-inner__feature-list">
              <li class="page-inner__feature-item">Level 1 gallery, ground floor, and outdoor area</li>
              <li class="page-inner__feature-item">Capacity from 20 to 150+ guests</li>
              <li class="page-inner__feature-item">Catering and vendor partnerships &mdash; we do the sourcing</li>
              <li class="page-inner__feature-item">Flexible non-fixed layout &mdash; the space adapts to your format</li>
              <li class="page-inner__feature-item">Dedicated event coordination from enquiry to pack-down</li>
              <li class="page-inner__feature-item">Central District 3 location &mdash; 218c Pasteur, HCMC</li>
            </ul>
          </div>

        </div>
      </div>
    </section>
    <?php else : ?>

    <!-- No content editor body — show Why TEMPO House full-width -->
    <section class="page-inner__section page-event-type__why-section" aria-labelledby="why-tempo-title">
      <div class="page-inner__container">
        <p class="page-inner__section-head">Why TEMPO House</p>
        <h2 class="page-inner__section-title" id="why-tempo-title">Built for events<br>like this one.</h2>
        <ul class="page-inner__feature-list">
          <li class="page-inner__feature-item">Level 1 gallery, ground floor, and outdoor area</li>
          <li class="page-inner__feature-item">Capacity from 20 to 150+ guests</li>
          <li class="page-inner__feature-item">Catering and vendor partnerships &mdash; we do the sourcing</li>
          <li class="page-inner__feature-item">Flexible non-fixed layout &mdash; the space adapts to your format</li>
          <li class="page-inner__feature-item">Dedicated event coordination from enquiry to pack-down</li>
          <li class="page-inner__feature-item">Central District 3 location &mdash; 218c Pasteur, HCMC</li>
        </ul>
      </div>
    </section>

    <?php endif; ?>

  <?php endwhile; endif; ?>

  <!-- ── Info Strip ──────────────────────────────── -->
  <section class="page-event-type__info-strip page-inner__section--alt" aria-label="Venue quick facts">
    <div class="page-inner__container">
      <div class="page-inner__info-grid">

        <div>
          <p class="page-inner__info-label">Capacity</p>
          <p class="page-inner__info-value">
            20 &ndash; 150+ guests<br>
            Gallery L1: up to 80 standing / 40 seated<br>
            Ground floor: up to 60 seated / 100 standing
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
          <p class="page-inner__info-label">Events Contact</p>
          <p class="page-inner__info-value">
            <a href="mailto:events@tempohouse.com.vn">events@tempohouse.com.vn</a>
          </p>
        </div>

      </div>
    </div>
  </section>

  <!-- ── Bottom CTA ──────────────────────────────── -->
  <section class="page-event-type__bottom-cta page-inner__section" aria-label="Start your event enquiry">
    <div class="page-inner__container page-event-type__bottom-cta-inner">
      <div>
        <p class="page-inner__section-head">Ready to plan?</p>
        <h2 class="page-event-type__bottom-cta-title">Let&rsquo;s make it happen.</h2>
        <p class="page-event-type__bottom-cta-body">Tell us about your event and we&rsquo;ll come back to you with the right space, the right package, and a timeline that works.</p>
      </div>
      <div class="page-inner__cta-row">
        <a href="<?php echo esc_url( home_url( '/event-enquiry' ) ); ?>" class="page-inner__cta-primary">Start Your Enquiry</a>
        <a href="<?php echo esc_url( home_url( '/events' ) ); ?>" class="page-inner__cta-secondary">All Event Types &rarr;</a>
      </div>
    </div>
  </section>

</main>

<?php get_footer(); ?>
