<?php
/* Template Name: Space Frame */

get_header();

$mode        = get_field( 'space_mode' )       ?: 'Day';
$time        = get_field( 'space_time' )       ?: '';
$frame_num   = get_field( 'space_frame_num' )  ?: '01';
$artwork_bg  = get_field( 'space_artwork_bg' ) ?: 'cream-dark';
$frame_color = get_field( 'space_frame_color') ?: 'terracotta';
$cta_text    = get_field( 'space_cta_text' )   ?: 'Explore';

$bg_map = [
    'cream-dark' => 'var(--tempo-cream-dark)',
    'ink'        => 'var(--tempo-ink)',
    'sand'       => 'var(--tempo-sand)',
];
$frame_map = [
    'terracotta' => 'var(--tempo-terracotta)',
    'dark'       => '#2D2420',
    'sage'       => 'var(--tempo-sage)',
];

$bg_val    = $bg_map[ $artwork_bg ]  ?? 'var(--tempo-cream-dark)';
$frame_val = $frame_map[ $frame_color ] ?? 'var(--tempo-terracotta)';
?>

<main class="space-page">
  <div class="space-page__back container">
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="space-page__back-link">&larr; Back to spaces</a>
  </div>

  <div class="space-page__frame-wrap container">
    <div class="space-page__frame" style="border-color: <?php echo esc_attr( $frame_val ); ?>">
      <div class="space-page__mat">
        <div class="space-page__artwork" style="background: <?php echo esc_attr( $bg_val ); ?>">
          <span class="space-page__num" aria-hidden="true"><?php echo esc_html( $frame_num ); ?></span>
          <div class="space-page__title-bar">
            <p class="space-page__label-mode">
              <?php echo esc_html( $mode ); ?>
              <?php if ( $time ) : ?>
                <span class="space-page__label-sep"> &middot; </span><?php echo esc_html( $time ); ?>
              <?php endif; ?>
            </p>
            <h1 class="space-page__label-title"><?php the_title(); ?></h1>
            <a href="<?php echo esc_url( home_url( '/reservations' ) ); ?>" class="space-page__label-cta">
              <?php echo esc_html( $cta_text ); ?> &rarr;
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    <div class="space-page__content container container--narrow">
      <?php the_content(); ?>
    </div>
  <?php endwhile; endif; ?>
</main>

<?php get_footer();
