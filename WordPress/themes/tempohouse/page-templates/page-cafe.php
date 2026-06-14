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
    <h1 class="page-inner__title">Slow mornings. Good coffee.<br>The kind of café Melbourne would approve of.</h1>
    <p class="page-inner__lead">Specialty coffee, smalls-style seasonal snacks, and a space designed for whatever you need it to be — a long work session, a catch-up, or just a reason to linger.</p>
  </header>

  <!-- ── 2. The Coffee ────────────────────────────── -->
  <section class="page-inner__section" aria-labelledby="coffee-title">
    <div class="page-inner__container">
      <div class="page-inner__split">

        <div class="page-cafe__coffee-text">
          <p class="page-inner__section-head">The Coffee</p>
          <h2 class="page-inner__section-title" id="coffee-title">Specialty Coffee,<br>Done Properly.</h2>

          <div class="page-inner__section-body">
            <p>At TEMPO House, every cup starts with intention. We source single-origin espresso and rotating filter coffees from producers whose growing practices we actually care about — the kind of supply chains you'd find in a Melbourne-style café where the barista can name the farm. No house blends designed to hide behind milk. No conveyor-belt café culture.</p>
            <p>Our batch brew changes with the season, and our cold brew is slow-steeped in-house rather than poured from a bottle. If you know specialty coffee in District 3, you know how rare that is. If you're new to it, our team is happy to walk you through it without the pretension.</p>
            <p>The bar follows a Melbourne-influenced approach: milk drinks made with care, extraction dialled properly, and enough quiet on the menu that the coffee itself can be the star. We're at 218c Pasteur in Quận 3 — come in before 10am for the best of the morning filter.</p>
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
            <p>The kitchen at TEMPO House runs a rotating menu of tapas-style small plates built around seasonal availability. Nothing fussy, nothing unnecessary — considered food that earns its place alongside whatever you&rsquo;re drinking. Designed to share and graze across a long café morning or an early-evening wind-down.</p>
            <p>The smalls format works in the daytime with a flat white and again in the evening beside one of our cocktails. Same kitchen, different mood. For private events and hired sessions, we partner with select Saigon caterers to extend the food offering beyond what the daily menu carries — reach us at <a href="mailto:hello@tempohouse.com.vn">hello@tempohouse.com.vn</a> for catering conversations.</p>
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
          <h2 class="page-inner__section-title" id="space-title">A space that works as hard as you don&rsquo;t want to.</h2>

          <div class="page-inner__section-body">
            <p>Furniture is non-fixed — tables, chairs, indoor lounges, and an outdoor terrace — arranged so the room breathes differently depending on the time of day and what you need from it. TEMPO House is WFH-friendly without being a co-working office: good WiFi, accessible power points, and a level of quiet that lets you concentrate without feeling like you&rsquo;ve walked into a library.</p>
            <p>Natural light runs through the space from morning until the afternoon crossover. The interiors are photography-worthy by design — an aesthetic Melbourne-style venue in Saigon that works as well for KOLs and content creators as it does for a two-hour laptop session or a relaxed catch-up over coffee. A space that doesn&rsquo;t need to announce itself.</p>
            <p class="page-cafe__floorplan-note">Floor plan coming soon &mdash; email us at <a href="mailto:hello@tempohouse.com.vn">hello@tempohouse.com.vn</a> for layout details.</p>
          </div>

          <ul class="page-inner__feature-list" aria-label="Space features">
            <li class="page-inner__feature-item">Flexible seating (indoor + outdoor)</li>
            <li class="page-inner__feature-item">Fast WiFi + power outlets</li>
            <li class="page-inner__feature-item">Natural light throughout</li>
            <li class="page-inner__feature-item">Quiet working environment</li>
            <li class="page-inner__feature-item">Photography-worthy interiors</li>
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
      <h2 class="page-cafe__footer-cta-title">Want to book a table or enquire about the space?</h2>
      <div class="page-inner__cta-row page-cafe__footer-cta-row">
        <a href="<?php echo esc_url( home_url( '/reservations' ) ); ?>" class="page-inner__cta-primary">Reserve a Table</a>
        <a href="<?php echo esc_url( home_url( '/events' ) ); ?>" class="page-inner__cta-secondary">Private Events</a>
      </div>
    </div>
  </section>

</main>

<?php get_footer(); ?>
