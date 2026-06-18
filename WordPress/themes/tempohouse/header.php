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

    <!-- CENTER: Brand logo — day/afternoon = terracotta mark, night = white mark -->
    <a class="site-nav__brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="TEMPO House — home">
      <img
        class="site-nav__logo-img site-nav__logo-img--day"
        src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/logo-mark.svg' ); ?>"
        alt="TEMPO House"
        width="60"
        height="60"
      >
      <img
        class="site-nav__logo-img site-nav__logo-img--night"
        src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/logo-mark-white.svg' ); ?>"
        alt=""
        aria-hidden="true"
        width="60"
        height="60"
      >
    </a>

    <!-- RIGHT: theme switcher + reserve -->
    <div class="site-nav__actions">

      <!-- Theme switcher icon -->
      <div class="site-nav__theme-wrap">
        <button class="site-nav__theme-btn" id="theme-switch-btn" type="button"
                aria-haspopup="true" aria-expanded="false" aria-label="Change time theme">
          <!-- Day icon — sun -->
          <svg class="theme-icon theme-icon--day" width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-linecap="round" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <circle cx="9" cy="9" r="3" stroke-width="1.5"/>
            <path d="M9 1.5v2M9 14.5v2M1.5 9h2M14.5 9h2M3.4 3.4l1.4 1.4M13.2 13.2l1.4 1.4M14.6 3.4l-1.4 1.4M4.8 13.2l-1.4 1.4" stroke-width="1.4"/>
          </svg>
          <!-- Afternoon icon — sun on horizon -->
          <svg class="theme-icon theme-icon--afternoon" width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-linecap="round" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path d="M4.2 11.5a4.8 4.8 0 0 1 9.6 0" stroke-width="1.5" fill="none"/>
            <line x1="1.5" y1="11.5" x2="16.5" y2="11.5" stroke-width="1.5"/>
            <path d="M9 2.5v1.8M5.4 4.9l1.2 1.2M12.6 4.9l-1.2 1.2M2.8 8.8h1.8M13.4 8.8h1.8" stroke-width="1.4"/>
          </svg>
          <!-- Night icon — crescent moon -->
          <svg class="theme-icon theme-icon--night" width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path d="M12.5 3A6.5 6.5 0 1 0 12.5 15A5 5 0 0 1 12.5 3z" stroke-width="1.5"/>
          </svg>
        </button>

        <!-- Theme popup -->
        <div class="site-nav__theme-popup" id="theme-switch-popup" hidden role="menu" aria-label="Time of day theme">
          <button class="theme-popup__opt" data-period="day" type="button" role="menuitem">
            <svg width="16" height="16" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-linecap="round" aria-hidden="true">
              <circle cx="9" cy="9" r="3" stroke-width="1.5"/>
              <path d="M9 1.5v2M9 14.5v2M1.5 9h2M14.5 9h2M3.4 3.4l1.4 1.4M13.2 13.2l1.4 1.4M14.6 3.4l-1.4 1.4M4.8 13.2l-1.4 1.4" stroke-width="1.4"/>
            </svg>
            Day
          </button>
          <button class="theme-popup__opt" data-period="afternoon" type="button" role="menuitem">
            <svg width="16" height="16" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-linecap="round" aria-hidden="true">
              <path d="M4.2 11.5a4.8 4.8 0 0 1 9.6 0" stroke-width="1.5" fill="none"/>
              <line x1="1.5" y1="11.5" x2="16.5" y2="11.5" stroke-width="1.5"/>
              <path d="M9 2.5v1.8M5.4 4.9l1.2 1.2M12.6 4.9l-1.2 1.2M2.8 8.8h1.8M13.4 8.8h1.8" stroke-width="1.4"/>
            </svg>
            Afternoon
          </button>
          <button class="theme-popup__opt" data-period="night" type="button" role="menuitem">
            <svg width="16" height="16" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M12.5 3A6.5 6.5 0 1 0 12.5 15A5 5 0 0 1 12.5 3z" stroke-width="1.5"/>
            </svg>
            Night
          </button>
        </div>
      </div>

      <!-- Reserve pill -->
      <a class="site-nav__reserve" href="<?php echo esc_url( home_url( '/reservations' ) ); ?>">
        &middot; RESERVE &middot;
      </a>

    </div>

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
