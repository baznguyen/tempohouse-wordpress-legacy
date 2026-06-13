<?php get_header(); ?>
<main class="page-whats-on">
  <header class="page-whats-on__header container">
    <p class="page-whats-on__eyebrow">Programming</p>
    <h1 class="page-whats-on__title">What&rsquo;s On</h1>
  </header>

  <div class="page-whats-on__grid container">
    <?php
    $events_query = new WP_Query([
        'post_type'      => 'event',
        'posts_per_page' => 12,
        'paged'          => max( 1, get_query_var( 'paged' ) ),
        'meta_query'     => [[
            'key'     => 'event_is_active',
            'value'   => '1',
            'compare' => '=',
        ]],
    ]);

    if ( $events_query->have_posts() ) :
        while ( $events_query->have_posts() ) : $events_query->the_post();
            $interior = get_field( 'event_interior' ) ?: 'dark';
            $category = get_field( 'event_category' ) ?: '';
            $month    = get_field( 'event_month' )    ?: '';
            $time     = get_field( 'event_time' )     ?: '';
            $href     = get_field( 'event_href' )     ?: get_permalink();
    ?>
    <article class="event-listing-card" data-interior="<?php echo esc_attr( $interior ); ?>">
      <?php if ( has_post_thumbnail() ) : ?>
        <div class="event-listing-card__thumb">
          <?php the_post_thumbnail( 'event-card' ); ?>
        </div>
      <?php endif; ?>
      <div class="event-listing-card__body">
        <?php if ( $category ) : ?>
          <p class="event-listing-card__category"><?php echo esc_html( $category ); ?></p>
        <?php endif; ?>
        <h2 class="event-listing-card__title">
          <a href="<?php echo esc_url( $href ); ?>"><?php the_title(); ?></a>
        </h2>
        <?php if ( $month || $time ) : ?>
          <p class="event-listing-card__meta">
            <?php echo esc_html( $month ); ?><?php if ( $month && $time ) : ?> &middot; <?php endif; ?><?php echo esc_html( $time ); ?>
          </p>
        <?php endif; ?>
      </div>
    </article>
    <?php
        endwhile;
        wp_reset_postdata();
    else :
    ?>
    <p class="page-whats-on__empty">No events scheduled at this time. <a href="<?php echo esc_url( home_url( '/#newsletter' ) ); ?>">Subscribe for updates &rarr;</a></p>
    <?php endif; ?>
  </div>

  <?php if ( isset( $events_query ) && $events_query->max_num_pages > 1 ) : ?>
  <div class="page-whats-on__pagination container">
    <?php
    echo paginate_links([
        'total'   => $events_query->max_num_pages,
        'current' => max( 1, get_query_var( 'paged' ) ),
    ]);
    ?>
  </div>
  <?php endif; ?>
</main>
<?php get_footer();
