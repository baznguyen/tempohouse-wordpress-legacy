<?php
/**
 * Template Name: Café
 * Description: Specialty café page — Melbourne-inspired coffee, kitchen, and space.
 */
get_header();
?>

<main class="page-cafe" id="main" role="main">

  <!-- ── 1. Page Banner ──────────────────────────── -->
  <header class="page-inner__banner">
    <p class="page-inner__eyebrow">The Café</p>
    <h1 class="page-inner__title">Specialty café, District 3.<br>Open from 07:00. No rush.</h1>
    <p class="page-inner__lead">Single-origin coffee, rotating smalls, and a space that works for a two-hour laptop session or a long catch-up over a flat white. Melbourne-style coffee in Saigon, on Pasteur.</p>
  </header>

  <!-- ── 2. The Coffee ────────────────────────────── -->
  <section class="page-inner__section" aria-labelledby="coffee-title">
    <div class="page-inner__container">
      <div class="page-inner__split">

        <div class="page-cafe__coffee-text">
          <p class="page-inner__section-head">The Coffee</p>
          <h2 class="page-inner__section-title" id="coffee-title">Specialty Coffee,<br>Done Properly.</h2>

          <div class="page-inner__section-body">
            <p>We source single-origin espresso and rotating filter coffees from producers whose growing practices we actually care about — the kind of supply chain where a barista can name the farm. No house blends designed to hide behind milk. No volume café culture.</p>
            <p>Batch brew changes with the season. Cold brew is slow-steeped in-house, not poured from a bottle. If you know specialty coffee in District 3, you know how rare that is. If you&rsquo;re new to it, the team will walk you through it without the sermon.</p>
            <p>The bar follows a Melbourne coffee approach: milk drinks made with care, extraction dialled correctly, and enough restraint on the menu that the coffee itself stays the point. We&rsquo;re at 218c Pasteur, Quận 3. Before 10am is the best window for morning filter.</p>
          </div>

          <ul class="page-inner__feature-list" aria-label="Coffee menu highlights">
            <li class="page-inner__feature-item">Espresso &amp; espresso-based drinks</li>
            <li class="page-inner__feature-item">Flat white</li>
            <li class="page-inner__feature-item">Filter &amp; batch brew (rotating origin)</li>
            <li class="page-inner__feature-item">Seasonal cold brew</li>
            <li class="page-inner__feature-item">Matcha &amp; non-coffee alternatives</li>
          </ul>

          <div class="page-inner__cta-row">
            <a href="<?php echo esc_url( home_url( '/reservations' ) ); ?>" class="page-inner__cta-primary">Reserve a Table</a>
            <a href="<?php echo esc_url( home_url( '/whats-on' ) ); ?>" class="page-inner__cta-secondary">See What&rsquo;s On</a>
          </div>
        </div>

        <div class="page-inner__img-placeholder" role="img" aria-label="Specialty coffee at TEMPO House">
          <span>Coffee &mdash; Image Coming Soon</span>
        </div>

      </div>
    </div>
  </section>

  <!-- ── 3. The Kitchen ───────────────────────────── -->
  <section class="page-inner__section page-inner__section--alt" aria-labelledby="kitchen-title">
    <div class="page-inner__container">
      <div class="page-inner__split">

        <div class="page-inner__img-placeholder" role="img" aria-label="Small plates and seasonal snacks at TEMPO House">
          <span>Kitchen &mdash; Image Coming Soon</span>
        </div>

        <div class="page-cafe__kitchen-text">
          <p class="page-inner__section-head">The Kitchen</p>
          <h2 class="page-inner__section-title" id="kitchen-title">Smalls. Snacks.<br>Things worth eating.</h2>

          <div class="page-inner__section-body">
            <p>The kitchen runs a rotating menu of tapas-style small plates built around seasonal availability. Nothing unnecessary — food that earns its place alongside whatever you&rsquo;re drinking. Designed to share across a long café morning or an early-evening wind-down before the bar opens.</p>
            <p>The smalls format holds up in the daytime beside a flat white, and again in the evening next to one of the cocktails. Same kitchen, different mood. For private hire and events, we work with select Saigon caterers to extend beyond the daily menu — reach us at <a href="mailto:hello@tempohouse.com.vn">hello@tempohouse.com.vn</a> for details.</p>
          </div>

          <ul class="page-inner__feature-list" aria-label="Kitchen menu highlights">
            <li class="page-inner__feature-item">Seasonal smalls (rotating weekly)</li>
            <li class="page-inner__feature-item">House toast &amp; spreads</li>
            <li class="page-inner__feature-item">Pastry selection</li>
            <li class="page-inner__feature-item">Grazing plates (available to order)</li>
          </ul>
        </div>

      </div>
    </div>
  </section>

  <!-- ── 4. The Space ─────────────────────────────── -->
  <section class="page-inner__section" aria-labelledby="space-title">
    <div class="page-inner__container">
      <div class="page-inner__split">

        <div class="page-inner__img-placeholder" role="img" aria-label="Interior of TEMPO House café in District 3">
          <span>Space &mdash; Image Coming Soon</span>
        </div>

        <div class="page-cafe__space-text">
          <p class="page-inner__section-head">The Space</p>
          <h2 class="page-inner__section-title" id="space-title">Good WiFi. Natural light.<br>No time limit.</h2>

          <div class="page-inner__section-body">
            <p>Furniture is non-fixed — tables, chairs, indoor lounges, and an outdoor terrace — so the room can be arranged around what you actually need. TEMPO House is WFH-friendly without being a co-working office: reliable WiFi, accessible power points, and a noise level that lets you concentrate without feeling like you need to whisper.</p>
            <p>Natural light carries through the space from the morning until afternoon. The interiors are considered and clean — the kind of WFH café in Ho Chi Minh City that content creators and KOLs find useful for exactly the same reasons someone working through a pitch deck does. No announcement required. Just come in and use it.</p>
            <p class="page-cafe__floorplan-note">Floor plan coming soon &mdash; email us at <a href="mailto:hello@tempohouse.com.vn">hello@tempohouse.com.vn</a> for layout details.</p>
          </div>

          <ul class="page-inner__feature-list" aria-label="Space features">
            <li class="page-inner__feature-item">Flexible seating (indoor + outdoor)</li>
            <li class="page-inner__feature-item">Fast WiFi + power outlets</li>
            <li class="page-inner__feature-item">Natural light throughout</li>
            <li class="page-inner__feature-item">Quiet working environment</li>
            <li class="page-inner__feature-item">Photography-ready interiors</li>
          </ul>
        </div>

      </div>
    </div>
  </section>

  <!-- ── 5. Info Strip ────────────────────────────── -->
  <section class="page-cafe__info-strip" aria-label="TEMPO House café information">
    <div class="page-inner__container">
      <div class="page-inner__info-grid">

        <div>
          <p class="page-inner__info-label">Address</p>
          <p class="page-inner__info-value">
            218c Pasteur, Xuân Ho&agrave;, Qu&#7853;n 3<br>
            Ho Chi Minh City, Vietnam
          </p>
        </div>

        <div>
          <p class="page-inner__info-label">Hours</p>
          <p class="page-inner__info-value">
            <span class="page-cafe__hours-mode">Caf&eacute;</span> 07:00 &ndash; 17:00 daily<br>
            <span class="page-cafe__hours-mode">Bar</span> opens 18:00
          </p>
        </div>

        <div>
          <p class="page-inner__info-label">Contact</p>
          <p class="page-inner__info-value">
            <a href="mailto:hello@tempohouse.com.vn">hello@tempohouse.com.vn</a><br>
            <a href="https://www.instagram.com/tempohouse.sgn/" target="_blank" rel="noopener noreferrer">@tempohouse.sgn</a>
          </p>
        </div>

      </div>
    </div>
  </section>

  <!-- ── 6. Footer CTA Strip ──────────────────────── -->
  <section class="page-cafe__footer-cta" aria-label="Book or enquire">
    <div class="page-inner__container">
      <h2 class="page-cafe__footer-cta-title">Come in. Table or no table.</h2>
      <div class="page-inner__cta-row page-cafe__footer-cta-row">
        <a href="<?php echo esc_url( home_url( '/reservations' ) ); ?>" class="page-inner__cta-primary">Reserve a Table</a>
        <a href="<?php echo esc_url( home_url( '/events' ) ); ?>" class="page-inner__cta-secondary">Private Events</a>
      </div>
    </div>
  </section>

</main>

<?php get_footer(); ?>
