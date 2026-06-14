<?php
/**
 * Custom full-width page template for the public booking form.
 * Used when WordPress serves the /reservations/ or /book/ page.
 */
defined( 'ABSPATH' ) || exit;
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Reserve a table — TEMPO House</title>
<?php wp_head(); ?>
<style>
  body { background: #0F0E0C; margin: 0; padding: 0; }
</style>
</head>
<body>

<div class="thr-booking-page">

  <!-- Minimal nav -->
  <header class="thr-bp-header">
    <a href="<?= home_url() ?>" class="thr-bp-logo">TEMPO House</a>
  </header>

  <main class="thr-bp-main">
    <?php echo do_shortcode( '[th_booking_form title="Reserve a table"]' ); ?>
  </main>

  <footer class="thr-bp-footer">
    <p>TEMPO House · Ho Chi Minh City · <a href="<?= home_url() ?>">tempohouse.com.vn</a></p>
  </footer>

</div>

<?php wp_footer(); ?>
</body>
</html>
