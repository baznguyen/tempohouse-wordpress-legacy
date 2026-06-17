<?php
$spaces = new WP_Query([
    'post_type'      => 'page',
    'posts_per_page' => 3,
    'orderby'        => 'meta_value_num',
    'meta_key'       => 'space_order',
    'order'          => 'ASC',
    'meta_query'     => [[
        'key'     => '_wp_page_template',
        'value'   => 'page-templates/space-frame.php',
        'compare' => '=',
    ]],
]);

$bg_map = [
    'cream-dark' => 'var(--tempo-cream-dark)',
    'ink'        => 'var(--tempo-ink)',
    'sand'       => 'var(--tempo-sand)',
];

$fallback = [
    [ 'slug' => 'cafe',    'num' => '01', 'mode' => 'Day',     'time' => '08:00 – 17:00', 'title' => 'Specialty Café',  'cta' => 'Explore the Café',  'bg' => 'var(--tempo-cream-dark)', 'speed' => '-0.07', 'href' => '/cafe' ],
    [ 'slug' => 'bar',     'num' => '02', 'mode' => 'Night',   'time' => '18:00 – 01:00', 'title' => 'Cocktail Bar',    'cta' => 'Explore the Bar',   'bg' => 'var(--tempo-ink)',        'speed' => '0.05',  'href' => '/bar' ],
    [ 'slug' => 'gallery', 'num' => '03', 'mode' => 'Gallery', 'time' => 'By programme',  'title' => 'Gallery',         'cta' => 'See the Gallery',   'bg' => 'var(--tempo-sand)',       'speed' => '-0.04', 'href' => '/gallery' ],
    [ 'slug' => 'events',  'num' => '04', 'mode' => 'Private', 'time' => 'By enquiry',    'title' => 'Private Events',  'cta' => 'Plan Your Event',   'bg' => '#1E1610',                 'speed' => '0.06',  'href' => '/events' ],
];
?>
<section class="moods" aria-label="The space">
  <p class="moods__eyebrow">The Space</p>

  <div class="moods__bleed-text" aria-hidden="true">
    <span class="moods__bleed-line">Creating experiences</span>
    <span class="moods__bleed-line">to be shared</span>
  </div>

  <div class="moods__frames-wrap">
    <?php if ( $spaces->have_posts() ) : ?>

      <?php while ( $spaces->have_posts() ) : $spaces->the_post();
        $slug      = get_post_field( 'post_name', get_the_ID() );
        $mode      = get_field( 'space_mode' )      ?: 'Day';
        $time      = get_field( 'space_time' )      ?: '';
        $frame_num = get_field( 'space_frame_num' ) ?: '01';
        $speed     = get_field( 'space_speed' )     ?: -0.07;
        $cta_text  = get_field( 'space_cta_text' )  ?: get_the_title();
        $artwork_bg = get_field( 'space_artwork_bg' ) ?: 'cream-dark';
        $bg_val    = $bg_map[ $artwork_bg ] ?? 'var(--tempo-cream-dark)';
      ?>
      <article class="moods__frame" data-frame="<?php echo esc_attr( $slug ); ?>" style="--speed: <?php echo esc_attr( $speed ); ?>">
        <a href="<?php the_permalink(); ?>" class="moods__frame-link" aria-label="<?php the_title_attribute(); ?>"></a>
        <div class="moods__frame-art">
          <div class="moods__mat">
            <div class="moods__artwork" style="background: <?php echo esc_attr( $bg_val ); ?>">
              <span class="moods__num" aria-hidden="true"><?php echo esc_html( $frame_num ); ?></span>
              <div class="moods__title-bar">
                <p class="moods__label-mode">
                  <?php echo esc_html( $mode ); ?><span class="moods__label-sep"> &middot; </span><?php echo esc_html( $time ); ?>
                </p>
                <h3 class="moods__label-title"><?php the_title(); ?></h3>
                <span class="moods__label-cta"><?php echo esc_html( $cta_text ); ?> &rarr;</span>
              </div>
            </div>
          </div>
        </div>
      </article>
      <?php endwhile; wp_reset_postdata(); ?>

    <?php else : ?>

      <?php foreach ( $fallback as $f ) : ?>
      <article class="moods__frame" data-frame="<?php echo esc_attr( $f['slug'] ); ?>" style="--speed: <?php echo esc_attr( $f['speed'] ); ?>">
        <a href="<?php echo esc_url( home_url( $f['href'] ) ); ?>" class="moods__frame-link" aria-label="<?php echo esc_attr( $f['title'] ); ?>"></a>
        <div class="moods__frame-art">
          <div class="moods__mat">
            <div class="moods__artwork" style="background: <?php echo esc_attr( $f['bg'] ); ?>">
              <span class="moods__num" aria-hidden="true"><?php echo esc_html( $f['num'] ); ?></span>
              <div class="moods__title-bar">
                <p class="moods__label-mode">
                  <?php echo esc_html( $f['mode'] ); ?><span class="moods__label-sep"> &middot; </span><?php echo esc_html( $f['time'] ); ?>
                </p>
                <h3 class="moods__label-title"><?php echo esc_html( $f['title'] ); ?></h3>
                <span class="moods__label-cta"><?php echo esc_html( $f['cta'] ); ?> &rarr;</span>
              </div>
            </div>
          </div>
        </div>
      </article>
      <?php endforeach; ?>

    <?php endif; ?>
  </div>

  <nav class="moods__carousel-nav" aria-label="Space carousel navigation">
    <button class="moods__nav-btn moods__nav-prev" aria-label="Previous" disabled>
      <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
        <path d="M10 12L6 8l4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </button>
    <div class="moods__dots">
      <button class="moods__dot moods__dot--active" aria-label="Frame 1"></button>
      <button class="moods__dot" aria-label="Frame 2"></button>
      <button class="moods__dot" aria-label="Frame 3"></button>
      <button class="moods__dot" aria-label="Frame 4"></button>
    </div>
    <button class="moods__nav-btn moods__nav-next" aria-label="Next">
      <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
        <path d="M6 12l4-4-4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </button>
  </nav>
</section>
