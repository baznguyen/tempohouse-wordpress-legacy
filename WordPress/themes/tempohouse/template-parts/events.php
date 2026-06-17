<?php
$events_query = new WP_Query([
    'post_type'      => 'event',
    'posts_per_page' => 3,
    'meta_query'     => [[
        'key'     => 'event_is_active',
        'value'   => '1',
        'compare' => '=',
    ]],
    'orderby' => 'date',
    'order'   => 'DESC',
]);

$event_items = [];

if ( $events_query->have_posts() ) :
    while ( $events_query->have_posts() ) : $events_query->the_post();
        $event_items[] = [
            'title'      => get_the_title(),
            'category'   => get_field( 'event_category' )  ?: 'Event',
            'month'      => get_field( 'event_month' )      ?: '',
            'time'       => get_field( 'event_time' )       ?: '',
            'interior'   => get_field( 'event_interior' )   ?: 'dark',
            'href'       => get_field( 'event_href' )       ?: get_permalink(),
            'media_type' => get_field( 'event_media_type' ) ?: 'none',
            'media_id'   => get_field( 'event_media_id' )   ?: 0,
        ];
    endwhile;
    wp_reset_postdata();
endif;

if ( empty( $event_items ) ) {
    $event_items = [
        [ 'title' => 'TEMPO Sessions',  'category' => 'Live Music',     'month' => 'Monthly',  'time' => '20:00 – 23:00', 'interior' => 'dark',  'href' => '/#newsletter',    'media_type' => 'none', 'media_id' => 0 ],
        [ 'title' => 'Gallery Opening', 'category' => 'Exhibition',     'month' => 'Rotating', 'time' => 'By programme',  'interior' => 'sand',  'href' => '/#newsletter',    'media_type' => 'none', 'media_id' => 0 ],
        [ 'title' => 'Tasting Menu',    'category' => 'Private Dining', 'month' => 'Weekly',   'time' => '19:00 – 22:00', 'interior' => 'cream', 'href' => '/events/enquiry', 'media_type' => 'none', 'media_id' => 0 ],
    ];
}

$track_items = array_merge( $event_items, $event_items );
$total       = count( $event_items );
?>
<section class="events" aria-label="What's on">
  <div class="container">
    <header class="events__header">
      <p class="events__eyebrow">Programming</p>
      <h2 class="events__title">What&rsquo;s On</h2>
    </header>
  </div>

  <div class="events__viewport">
    <div class="events__track">
      <?php foreach ( $track_items as $i => $ev ) : ?>
      <article class="event-card" data-interior="<?php echo esc_attr( $ev['interior'] ); ?>">
        <a href="<?php echo esc_url( $ev['href'] ); ?>" class="event-card__link"
           aria-label="<?php echo esc_attr( $ev['title'] . ' — ' . $ev['time'] ); ?>"></a>

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

  <div class="container">
    <div class="events__footer">
      <p class="events__footer-note"><em>The programme rotates. The list gets first notice.</em></p>
      <a href="<?php echo esc_url( home_url( '/whats-on' ) ); ?>" class="events__footer-cta">See all events &rarr;</a>
    </div>
  </div>
</section>
