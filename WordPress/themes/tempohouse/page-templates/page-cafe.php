<?php
/**
 * Template Name: Café
 * Description: Specialty café page — Melbourne-inspired coffee, kitchen, and space.
 */
get_header();
?>

<main class="page-cafe" id="main" role="main">

  <!-- ── 1. Page Banner ──────────────────────────── -->
  <header class="page-inner__banner page-cafe__banner">
    <span class="page-cafe__banner-hour" aria-hidden="true">07</span>
    <p class="page-inner__eyebrow">The Café</p>
    <h1 class="page-inner__title">Specialty coffee &amp; matcha, District 3.<br><em class="page-cafe__title-em">The kind that earns a regular.</em></h1>
    <p class="page-inner__lead">Vietnam Highlands coffee, roasted to an Australian standard. Ito En matcha from Uji, Kyoto. Affogato at the crossroads. And a kitchen of rotating smalls if you&rsquo;re staying for the morning. 218c Pasteur, from 08:00.</p>
  </header>

  <!-- ── 1b. Provenance strip (gallery attribution style) ── -->
  <div class="page-cafe__provenance" aria-hidden="true">
    <div class="page-inner__container">
      <dl class="page-cafe__provenance-grid">
        <div class="page-cafe__provenance-item">
          <dt class="page-cafe__provenance-label">Coffee</dt>
          <dd class="page-cafe__provenance-value">Vietnam Highlands &mdash; Australian roast</dd>
        </div>
        <div class="page-cafe__provenance-item">
          <dt class="page-cafe__provenance-label">Matcha</dt>
          <dd class="page-cafe__provenance-value">Ito En &mdash; Uji, Kyoto</dd>
        </div>
        <div class="page-cafe__provenance-item">
          <dt class="page-cafe__provenance-label">Menu</dt>
          <dd class="page-cafe__provenance-value">Espresso &middot; Matcha &middot; Affogato</dd>
        </div>
        <div class="page-cafe__provenance-item">
          <dt class="page-cafe__provenance-label">Open</dt>
          <dd class="page-cafe__provenance-value">08:00 &ndash; 17:00, daily</dd>
        </div>
      </dl>
    </div>
  </div>

  <!-- ── 1c. Brew methods strip ───────────────────── -->
  <div class="page-cafe__brew-strip" aria-label="Coffee served at TEMPO House">
    <div class="page-inner__container">
      <div class="page-cafe__brew-track">
        <span class="page-cafe__brew-item">Espresso</span>
        <span class="page-cafe__brew-sep" aria-hidden="true">&middot;</span>
        <span class="page-cafe__brew-item">B&#7841;c X&#7881;u</span>
        <span class="page-cafe__brew-sep" aria-hidden="true">&middot;</span>
        <span class="page-cafe__brew-item">Matcha</span>
        <span class="page-cafe__brew-sep" aria-hidden="true">&middot;</span>
        <span class="page-cafe__brew-item">Affogato</span>
        <span class="page-cafe__brew-sep" aria-hidden="true">&middot;</span>
        <span class="page-cafe__brew-item">Espresso Tonic</span>
      </div>
      <p class="page-cafe__brew-source" aria-hidden="true">Vietnam Highlands &middot; Ito En Matcha, Uji, Kyoto</p>
    </div>
  </div>

  <!-- ── 2. The Coffee ────────────────────────────── -->
  <section class="page-inner__section page-cafe__coffee" aria-labelledby="coffee-title">
    <div class="page-inner__container">
      <div class="page-inner__split">

        <div class="page-cafe__coffee-text">
          <p class="page-inner__section-head">The Coffee</p>
          <h2 class="page-inner__section-title" id="coffee-title">Vietnam Highlands beans.<br><em class="page-cafe__title-em">Done properly.</em></h2>

          <div class="page-inner__section-body">
            <p>The coffee runs on Vietnam Highlands beans, roasted to an Australian standard. Espresso, Americano, Flat White (hot only &mdash; milk temperature matters), Latte, Cappuccino. Plus the Vietnamese classics: C&agrave; Ph&ecirc; S&#7919;a &Đ&aacute;, B&#7841;c X&#7881;u, Iced Salted Coffee, and an Iced Coconut Coffee. The Espresso Tonic is on the list. Cold Brew is slow-steeped in-house.</p>
            <p>The Affogato runs three directions: coffee, matcha, or with r&#432;&#7907;u espresso &mdash; that&rsquo;s the Naughty Affogato. Same counter. The matcha programme has its own section below.</p>
          </div>

          <ul class="page-inner__feature-list page-cafe__menu-list" aria-label="Coffee menu highlights">
            <li class="page-inner__feature-item">C&agrave; Ph&ecirc; S&#7919;a &Đ&aacute; &amp; B&#7841;c X&#7881;u &mdash; Vietnamese classics</li>
            <li class="page-inner__feature-item">Flat white, latte, cappuccino &amp; Americano</li>
            <li class="page-inner__feature-item">Iced Salted Coffee &amp; Iced Coconut Coffee</li>
            <li class="page-inner__feature-item">Espresso Tonic &amp; cold brew</li>
            <li class="page-inner__feature-item">Coffee &amp; Naughty Affogato</li>
          </ul>

          <div class="page-inner__cta-row">
            <a href="<?php echo esc_url( home_url( '/reservations' ) ); ?>" class="page-inner__cta-primary">Reserve a Table</a>
            <a href="<?php echo esc_url( home_url( '/whats-on' ) ); ?>" class="page-inner__cta-secondary">See What&rsquo;s On</a>
          </div>
        </div>

        <div class="tempo-frame tempo-frame--placeholder page-cafe__coffee-img" data-interactive aria-label="Specialty coffee at TEMPO House">
          <div class="tempo-frame__mat">
            <div class="tempo-frame__artwork">
              <span class="tempo-frame__label">Coffee Photography</span>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- ── 2b. The Matcha — gallery grid of six ──────── -->
  <section class="page-inner__section page-inner__section--alt page-cafe__matcha" aria-labelledby="matcha-title">
    <div class="page-inner__container">

      <div class="page-cafe__matcha-head">
        <p class="page-inner__section-head">The Matcha Programme</p>
        <div class="page-cafe__matcha-intro">
          <h2 class="page-inner__section-title" id="matcha-title">Ito En &mdash; Uji, Kyoto.<br>Six variations.</h2>
          <p class="page-cafe__matcha-source">Stone-ground. Not a flavoured powder. The same Ito En matcha runs through every drink on this list and into the Matcha Affogato. Oat milk on request.</p>
        </div>
      </div>

      <div class="page-cafe__matcha-grid">

        <div class="page-cafe__matcha-item">
          <div class="tempo-frame tempo-frame--placeholder page-cafe__matcha-frame" data-interactive aria-label="Matcha Latte at TEMPO House">
            <div class="tempo-frame__mat"><div class="tempo-frame__artwork"><span class="tempo-frame__label">Matcha Latte</span></div></div>
          </div>
          <div class="page-cafe__matcha-label">
            <span class="page-cafe__matcha-num" aria-hidden="true">01</span>
            <h3 class="page-cafe__matcha-name">Matcha Latte</h3>
            <p class="page-cafe__matcha-variant">Hot / Iced</p>
          </div>
        </div>

        <div class="page-cafe__matcha-item">
          <div class="tempo-frame tempo-frame--placeholder page-cafe__matcha-frame" data-interactive aria-label="Matcha Coconut Cloud at TEMPO House">
            <div class="tempo-frame__mat"><div class="tempo-frame__artwork"><span class="tempo-frame__label">Coconut Cloud</span></div></div>
          </div>
          <div class="page-cafe__matcha-label">
            <span class="page-cafe__matcha-num" aria-hidden="true">02</span>
            <h3 class="page-cafe__matcha-name">Coconut Cloud</h3>
            <p class="page-cafe__matcha-variant">Coconut milk &middot; Iced</p>
          </div>
        </div>

        <div class="page-cafe__matcha-item">
          <div class="tempo-frame tempo-frame--placeholder page-cafe__matcha-frame" data-interactive aria-label="Matcha Jasmine Cloud at TEMPO House">
            <div class="tempo-frame__mat"><div class="tempo-frame__artwork"><span class="tempo-frame__label">Jasmine Cloud</span></div></div>
          </div>
          <div class="page-cafe__matcha-label">
            <span class="page-cafe__matcha-num" aria-hidden="true">03</span>
            <h3 class="page-cafe__matcha-name">Jasmine Cloud</h3>
            <p class="page-cafe__matcha-variant">Jasmine tea &middot; Iced</p>
          </div>
        </div>

        <div class="page-cafe__matcha-item">
          <div class="tempo-frame tempo-frame--placeholder page-cafe__matcha-frame" data-interactive aria-label="Matcha Yuzu at TEMPO House">
            <div class="tempo-frame__mat"><div class="tempo-frame__artwork"><span class="tempo-frame__label">Matcha Yuzu</span></div></div>
          </div>
          <div class="page-cafe__matcha-label">
            <span class="page-cafe__matcha-num" aria-hidden="true">04</span>
            <h3 class="page-cafe__matcha-name">Matcha Yuzu</h3>
            <p class="page-cafe__matcha-variant">Yuzu citrus &middot; Iced</p>
          </div>
        </div>

        <div class="page-cafe__matcha-item">
          <div class="tempo-frame tempo-frame--placeholder page-cafe__matcha-frame" data-interactive aria-label="Matcha Strawberry at TEMPO House">
            <div class="tempo-frame__mat"><div class="tempo-frame__artwork"><span class="tempo-frame__label">Matcha Strawberry</span></div></div>
          </div>
          <div class="page-cafe__matcha-label">
            <span class="page-cafe__matcha-num" aria-hidden="true">05</span>
            <h3 class="page-cafe__matcha-name">Matcha Strawberry</h3>
            <p class="page-cafe__matcha-variant">Fresh strawberry &middot; Iced</p>
          </div>
        </div>

        <div class="page-cafe__matcha-item">
          <div class="tempo-frame tempo-frame--placeholder page-cafe__matcha-frame" data-interactive aria-label="Matcha Mango at TEMPO House">
            <div class="tempo-frame__mat"><div class="tempo-frame__artwork"><span class="tempo-frame__label">Matcha Mango</span></div></div>
          </div>
          <div class="page-cafe__matcha-label">
            <span class="page-cafe__matcha-num" aria-hidden="true">06</span>
            <h3 class="page-cafe__matcha-name">Matcha Mango</h3>
            <p class="page-cafe__matcha-variant">Mango &middot; Iced</p>
          </div>
        </div>

      </div>

      <p class="page-cafe__matcha-footnote">Add oat milk to any variation &mdash; on request. Matcha Affogato available from the counter.</p>
      <div class="page-cafe__matcha-dots" aria-hidden="true"></div>

    </div>
  </section>

  <!-- ── 3. The Kitchen ───────────────────────────── -->
  <section class="page-inner__section" aria-labelledby="kitchen-title">
    <div class="page-inner__container">
      <div class="page-inner__split">

        <div class="tempo-frame tempo-frame--placeholder" data-interactive aria-label="Small plates and seasonal snacks at TEMPO House">
          <div class="tempo-frame__mat">
            <div class="tempo-frame__artwork">
              <span class="tempo-frame__label">Kitchen Photography</span>
            </div>
          </div>
        </div>

        <div class="page-cafe__kitchen-text">
          <p class="page-inner__section-head">The Kitchen</p>
          <h2 class="page-inner__section-title" id="kitchen-title">Smalls. Snacks.<br>Things worth eating.</h2>

          <div class="page-inner__section-body">
            <p>The kitchen runs a rotating menu of tapas-style small plates built around seasonal availability. Nothing unnecessary — food that earns its place alongside whatever you&rsquo;re drinking. Designed to share across a long café morning or an early-evening wind-down before the bar opens.</p>
            <p>The smalls format holds up in the daytime beside a flat white, and again in the evening next to one of the cocktails. Same kitchen, different mood. For private hire and events, we work with select Saigon caterers to extend beyond the daily menu — reach us at <a href="mailto:hello@tempohouse.com.vn">hello@tempohouse.com.vn</a> for details.</p>
          </div>

          <ul class="page-inner__feature-list page-cafe__menu-list" aria-label="Kitchen menu highlights">
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

        <div class="tempo-frame tempo-frame--placeholder" data-interactive aria-label="Interior of TEMPO House café in District 3">
          <div class="tempo-frame__mat">
            <div class="tempo-frame__artwork">
              <span class="tempo-frame__label">Space Photography</span>
            </div>
          </div>
        </div>

        <div class="page-cafe__space-text">
          <p class="page-inner__section-head">The Space</p>
          <h2 class="page-inner__section-title" id="space-title">Good WiFi. Natural light.<br>No time limit.</h2>

          <div class="page-inner__section-body">
            <p>Furniture is non-fixed — tables, chairs, indoor lounges, and an outdoor terrace — so the room can be arranged around what you actually need. TEMPO House is WFH-friendly without being a co-working office: reliable WiFi, accessible power points, and a noise level that lets you concentrate without feeling like you need to whisper.</p>
            <p>Natural light carries through the space from the morning until afternoon. The interiors are considered and clean — the kind of WFH café in Ho Chi Minh City that content creators and KOLs find useful for exactly the same reasons someone working through a pitch deck does. No announcement required. Just come in and use it.</p>
            <p class="page-cafe__floorplan-note">Floor plan coming soon &mdash; email us at <a href="mailto:hello@tempohouse.com.vn">hello@tempohouse.com.vn</a> for layout details.</p>
          </div>

          <ul class="page-inner__feature-list page-cafe__menu-list" aria-label="Space features">
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

  <!-- ── 5. The Day — Rhythm Strip ───────────────── -->
  <section class="page-cafe__rhythm" aria-label="The daily rhythm at TEMPO House">
    <div class="page-inner__container">
      <p class="page-inner__eyebrow">The Day</p>

      <div class="page-cafe__rhythm-grid">
        <div class="page-cafe__rhythm-marker page-cafe__rhythm-marker--open">
          <time class="page-cafe__rhythm-time">08:00</time>
          <strong class="page-cafe__rhythm-label">Morning filter.</strong>
          <span class="page-cafe__rhythm-detail">First coffee. The best window.</span>
        </div>
        <div class="page-cafe__rhythm-marker">
          <time class="page-cafe__rhythm-time">09:00</time>
          <strong class="page-cafe__rhythm-label">Kitchen opens.</strong>
          <span class="page-cafe__rhythm-detail">Smalls &amp; pastry from the kitchen.</span>
        </div>
        <div class="page-cafe__rhythm-marker">
          <time class="page-cafe__rhythm-time">17:00</time>
          <strong class="page-cafe__rhythm-label">Caf&eacute; closes.</strong>
          <span class="page-cafe__rhythm-detail">Last coffee. Settle up.</span>
        </div>
        <div class="page-cafe__rhythm-marker">
          <time class="page-cafe__rhythm-time">18:00</time>
          <strong class="page-cafe__rhythm-label">Bar opens.</strong>
          <span class="page-cafe__rhythm-detail">Same room. Different tempo.</span>
        </div>
      </div>

      <div class="page-cafe__rhythm-info">
        <div>
          <p class="page-inner__info-label">Address</p>
          <p class="page-inner__info-value">
            218c Pasteur, Xuân Ho&agrave;, Qu&#7853;n 3<br>
            Ho Chi Minh City, Vietnam
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
