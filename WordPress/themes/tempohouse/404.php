<?php get_header(); ?>
<main class="page-404 container container--narrow">
  <p class="page-404__eyebrow">404</p>
  <h1 class="page-404__title">Page not found</h1>
  <p class="page-404__body">The page you&rsquo;re looking for has moved or doesn&rsquo;t exist.</p>
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="page-404__home">&larr; Back to TEMPO House</a>
</main>
<?php get_footer();
