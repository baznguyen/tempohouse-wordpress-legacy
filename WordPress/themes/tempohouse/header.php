<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="<?php echo esc_url( get_template_directory_uri() . '/assets/images/tempo-house-icon.svg' ); ?>" type="image/svg+xml">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@400;500;600&family=Cormorant+Garamond:ital,wght@0,300;0,400;1,300;1,400&family=Space+Grotesk:wght@300;400;500&display=swap" rel="stylesheet">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-nav" role="banner">
  <div class="site-nav__grid">

    <!-- LEFT: MENU pill -->
    <button class="site-nav__menu-trigger" id="site-nav-trigger" aria-expanded="false" aria-controls="site-drawer" aria-label="Open navigation">
      MENU
    </button>

    <!-- CENTER: Brand logo -->
    <a class="site-nav__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="TEMPO House — home">
      <img
        class="site-nav__logo-img"
        src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/logo.png' ); ?>"
        alt="TEMPO House"
        width="60"
        height="60"
      >
    </a>

    <!-- RIGHT: Reserve pill -->
    <a class="site-nav__reserve" href="<?php echo esc_url( home_url( '/reservations' ) ); ?>">
      &middot; RESERVE &middot;
    </a>

  </div>
</header>

<!-- Drawer overlay -->
<div class="site-nav__overlay" id="site-nav-overlay" aria-hidden="true"></div>

<!-- Slide-out drawer -->
<nav class="site-drawer" id="site-drawer" role="navigation" aria-label="Main navigation" aria-hidden="true">

  <div class="site-drawer__header">
    <span class="site-drawer__label"><em>Navigation</em></span>
    <button class="site-drawer__close" id="site-nav-close" aria-label="Close navigation">&#215;</button>
  </div>

  <div class="site-drawer__nav">
    <a class="site-drawer__link" href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
    <a class="site-drawer__link" href="<?php echo esc_url( home_url( '/venue' ) ); ?>">Venue</a>
    <a class="site-drawer__link" href="<?php echo esc_url( home_url( '/cafe' ) ); ?>">Caf&eacute;</a>
    <a class="site-drawer__link" href="<?php echo esc_url( home_url( '/bar' ) ); ?>">Bar</a>
    <a class="site-drawer__link" href="<?php echo esc_url( home_url( '/gallery' ) ); ?>">Gallery</a>
    <a class="site-drawer__link" href="<?php echo esc_url( home_url( '/whats-on' ) ); ?>">What&rsquo;s On</a>
    <a class="site-drawer__link" href="<?php echo esc_url( home_url( '/events' ) ); ?>">Events</a>
    <a class="site-drawer__link" href="<?php echo esc_url( home_url( '/reservations' ) ); ?>">Reservations</a>
    <a class="site-drawer__link" href="<?php echo esc_url( home_url( '/contact' ) ); ?>">Contact</a>
  </div>

  <div class="site-drawer__foot">
    <a class="site-drawer__cta" href="<?php echo esc_url( home_url( '/reservations' ) ); ?>">Reserve a Table</a>
    <div class="site-drawer__meta">
      <span class="site-drawer__lang">EN &middot; VI</span>
    </div>
  </div>

</nav>
