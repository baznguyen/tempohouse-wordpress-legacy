<?php
// Query standard Posts tagged 'event' AND 'active' — up to 3 for the homepage carousel.
$events_query = new WP_Query([
    'post_type'      => 'post',
    'posts_per_page' => 3,
    'tax_query'      => [
        'relation' => 'AND',
        [
            'taxonomy' => 'post_tag',
            'field'    => 'slug',
            'terms'    => 'event',
        ],
        [
            'taxonomy' => 'post_tag',
            'field'    => 'slug',
            'terms'    => 'active',
        ],
    ],
    'orderby' => 'date',
    'order'   => 'DESC',
]);

$event_items = [];

if ( $events_query->have_posts() ) :
    while ( $events_query->have_posts() ) : $events_query->the_post();
        $date_raw   = get_field( 'event_date' );
        $recurrence = get_field( 'event_recurrence' ) ?: '';

        // "Month" label: dated events show "Jul 2025", recurring shows recurrence text.
        if ( $date_raw ) {
            $month_label = date_i18n( 'M Y', strtotime( $date_raw ) );
        } elseif ( $recurrence ) {
            $labels = [
                'one-time' => 'One Night Only',
                'weekly'   => 'Weekly',
                'monthly'  => 'Monthly',
                'ongoing'  => 'Ongoing',
            ];
            $month_label = $labels[ $recurrence ] ?? ucfirst( $recurrence );
        } else {
            $month_label = '';
        }

        $event_items[] = [
            'title'      => get_the_title(),
            'category'   => get_field( 'event_category' )  ?: 'Event',
            'month'      => $month_label,
            'time'       => get_field( 'event_time' )       ?: '',
            'interior'   => get_field( 'event_interior' )   ?: 'dark',
            'href'       => get_permalink(),
            'media_type' => get_field( 'event_media_type' ) ?: 'none',
            'media_id'   => get_field( 'event_media_id' )   ?: 0,
        ];
    endwhile;
    wp_reset_postdata();
endif;

$has_events  = ! empty( $event_items );
$track_items = $has_events ? array_merge( $event_items, $event_items ) : [];
$total       = count( $event_items );
?>
<section class="events" aria-label="What's on">
  <div class="container">
    <header class="events__header">
      <p class="events__eyebrow">Programming</p>
      <h2 class="events__title">What&rsquo;s On</h2>
    </header>
  </div>

  <?php if ( $has_events ) : ?>

  <div class="events__viewport">
    <div class="events__track">
      <?php foreach ( $track_items as $i => $ev ) : ?>
      <article class="event-card" data-interior="<?php echo esc_attr( $ev['interior'] ); ?>">
        <a href="<?php echo esc_url( $ev['href'] ); ?>" class="event-card__link"
           aria-label="<?php echo esc_attr( $ev['title'] . ( $ev['time'] ? ' — ' . $ev['time'] : '' ) ); ?>"></a>

        <div class="event-card__frame-art">
          <div class="event-card__mat">
            <div class="event-card__artwork">

              <?php if ( $ev['media_type'] !== 'none' && $ev['media_id'] ) : ?>
              <div class="event-card__media-layer">
                <?php if ( $ev['media_type'] === 'video' ) : ?>
                  <video class="event-card__media" muted loop playsinline preload="none"
                    src="<?php echo esc_url( wp_get_attachment_url( $ev['media_id'] ) ); ?>"></video>
                <?php else : ?>
                  <?php echo wp_get_attachment_image( $ev['media_id'], 'event-card', false, [ 'class' => 'event-card__media' ] ); ?>
                <?php endif; ?>
              </div>
              <?php else : ?>
              <span class="event-card__category-ghost"><?php echo esc_html( $ev['category'] ); ?></span>
              <?php endif; ?>

              <div class="event-card__title-bar">
                <p class="event-card__title"><?php echo esc_html( $ev['title'] ); ?></p>
              </div>

              <div class="event-card__date-reveal">
                <span class="event-card__month"><?php echo esc_html( $ev['month'] ); ?></span>
                <span class="event-card__time"><?php echo esc_html( $ev['time'] ); ?></span>
              </div>

            </div>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>

  <nav class="events__carousel-nav" aria-label="Events navigation">
    <button class="events__nav-btn events__nav-prev" aria-label="Previous event" disabled>
      <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
        <path d="M10 12L6 8l4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </button>
    <div class="events__dots">
      <?php for ( $d = 0; $d < $total; $d++ ) : ?>
      <button class="events__dot<?php echo $d === 0 ? ' events__dot--active' : ''; ?>"
              aria-label="Event <?php echo $d + 1; ?>"></button>
      <?php endfor; ?>
    </div>
    <button class="events__nav-btn events__nav-next" aria-label="Next event">
      <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
        <path d="M6 12l4-4-4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </button>
  </nav>

  <?php else : ?>

  <!-- No live events — clean coming-soon state, no fake placeholder cards -->
  <div class="events__empty">
    <p class="events__empty-body">The programme is taking shape. Subscribe and you&rsquo;ll hear first.</p>
    <a href="<?php echo esc_url( home_url( '/#newsletter' ) ); ?>" class="events__empty-cta">Stay in the loop</a>
  </div>

  <?php endif; ?>

  <div class="container">
    <div class="events__footer">
      <p class="events__footer-note"><em>The programme rotates. The list gets first notice.</em></p>
      <a href="<?php echo esc_url( home_url( '/whats-on' ) ); ?>" class="events__footer-cta">See all events &rarr;</a>
    </div>
  </div>
</section>
