<?php
/**
 * Template Name: Venue
 * Description: Private event venue — 218c Pasteur, District 3, Ho Chi Minh City.
 */
get_header();
?>

<main class="page-venue" id="main" role="main">

  <!-- ── 1. Banner ──────────────────────────────────────── -->
  <header class="page-inner__banner">
    <p class="page-inner__eyebrow">Private Event Venue &middot; District 3 Ho Chi Minh City</p>
    <h1 class="page-inner__title">A venue built for the event.<br>Not the other way around.</h1>
    <p class="page-inner__lead">218c Pasteur, District 3. Hire the gallery level, the ground floor, or the full building. Up to 150 guests standing across two floors. Product launches, corporate events, intimate weddings, brand activations.</p>
    <div class="page-inner__cta-row">
      <a href="<?php echo esc_url( home_url( '/event-enquiry' ) ); ?>" class="page-inner__cta-primary">Enquire About Your Event</a>
      <a href="<?php echo esc_url( home_url( '/events' ) ); ?>" class="page-inner__cta-secondary">Explore events &rarr;</a>
    </div>
  </header>

  <!-- ── 2. Capacity Strip ──────────────────────────────── -->
  <section class="page-venue__capacity-strip" aria-label="Venue hire configurations">
    <div class="page-inner__container">
      <div class="page-venue__capacity-grid">

        <div class="page-venue__cap-item">
          <p class="page-venue__cap-label">Gallery &middot; Level 1</p>
          <p class="page-venue__cap-num">80</p>
          <p class="page-venue__cap-unit">standing &middot; column-free</p>
          <p class="page-venue__cap-sub">50 seated &middot; track lighting &middot; terrace access</p>
        </div>

        <div class="page-venue__cap-item">
          <p class="page-venue__cap-label">Full Venue &middot; Both Floors</p>
          <p class="page-venue__cap-num">150<span class="page-venue__cap-plus">+</span></p>
          <p class="page-venue__cap-unit">standing &middot; exclusive hire</p>
          <p class="page-venue__cap-sub">Gallery + caf&eacute; floor + outdoor terrace + bar</p>
        </div>

        <div class="page-venue__cap-item">
          <p class="page-venue__cap-label">Ground Floor &middot; Caf&eacute; Buyout</p>
          <p class="page-venue__cap-num">60</p>
          <p class="page-venue__cap-unit">seated &middot; bar service included</p>
          <p class="page-venue__cap-sub">Counter service &middot; natural light &middot; flexible seating</p>
        </div>

      </div>
    </div>
  </section>

  <!-- ── 3. The Building ────────────────────────────────── -->
  <section class="page-inner__section page-inner__section--alt" aria-labelledby="building-heading">
    <div class="page-inner__container">
      <div class="page-inner__split">

        <div>
          <p class="page-inner__section-head">The Building</p>
          <h2 class="page-inner__section-title" id="building-heading">A French-colonial shophouse.<br>Restored. Not reinvented.</h2>
          <div class="page-inner__section-body">
            <p>TEMPO House occupies a narrow colonial shophouse on Pasteur Street &mdash; one of District 3&rsquo;s most intact historical addresses. The building has been carefully restored: exposed brick, high ceilings, and original timber detailing throughout. It reads as itself, not as a renovation that erased what made it worth keeping.</p>
            <p>Level 1 is column-free, with neutral walls, adjustable track lighting, and a connection to the outdoor terrace. It was built to hold gallery exhibitions without competing with them &mdash; which makes it equally suited to events that need a room with real architecture, not a hotel function space dressed for the occasion.</p>
            <p>For exclusive hire, both floors, the outdoor terrace, bar, coffee counter, lighting, and sound are included. One building. Your event, end to end.</p>
          </div>
          <div class="page-inner__cta-row">
            <a href="<?php echo esc_url( home_url( '/event-enquiry' ) ); ?>" class="page-inner__cta-primary">Enquire Now</a>
            <a href="<?php echo esc_url( home_url( '/gallery' ) ); ?>" class="page-inner__cta-secondary">See the gallery floor &rarr;</a>
          </div>
        </div>

        <a href="<?php echo esc_url( home_url( '/gallery' ) ); ?>" class="tempo-frame page-venue__gallery-img" aria-label="Interior of TEMPO House gallery level, District 3 Saigon">
          <div class="tempo-frame__mat">
            <div class="tempo-frame__artwork">
              <img class="tempo-frame__img" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/venue/venue-gallery.jpg" alt="TEMPO House gallery Level 1 interior District 3" loading="lazy">
              <span class="tempo-frame__caption">Level 1 &middot; The Gallery</span>
            </div>
          </div>
        </a>

      </div>
    </div>
  </section>

  <!-- ── 4. Private Events ─────────────────────────────── -->
  <section class="page-inner__section" id="venue-event-types" aria-labelledby="venue-events-heading">
    <div class="page-inner__container">
      <div class="page-inner__split">

        <div>
          <p class="page-inner__section-head">Private Events</p>
          <h2 class="page-inner__section-title" id="venue-events-heading">The building is<br>the brief.</h2>
          <div class="page-inner__section-body">
            <p>TEMPO House takes on private events across both floors, the bar, and the outdoor terrace. No packages &mdash; each hire is shaped around what the event needs. Product launches and brand activations in the gallery. Dinners, cocktail receptions, and celebrations on the ground floor and Pasteur Street terrace.</p>
            <p>The space works because it isn&rsquo;t designed to be neutral. It&rsquo;s a restored colonial shophouse with real architecture &mdash; one that gives events a sense of place a hotel function room can&rsquo;t replicate.</p>
          </div>
          <div class="page-inner__cta-row">
            <a href="<?php echo esc_url( home_url( '/events' ) ); ?>" class="page-inner__cta-primary">See Events We Host</a>
            <a href="<?php echo esc_url( home_url( '/event-enquiry' ) ); ?>" class="page-inner__cta-secondary">Start an enquiry &rarr;</a>
          </div>
        </div>

        <div class="page-venue__event-types">
          <p class="page-inner__info-label">What works here</p>
          <ul class="page-inner__feature-list">
            <li class="page-inner__feature-item">Product launches &amp; brand activations</li>
            <li class="page-inner__feature-item">Corporate dinners &amp; presentations</li>
            <li class="page-inner__feature-item">Art exhibitions &amp; creative openings</li>
            <li class="page-inner__feature-item">Cocktail receptions &amp; standing events</li>
            <li class="page-inner__feature-item">Intimate weddings &amp; celebrations</li>
            <li class="page-inner__feature-item">Birthday &amp; milestone parties</li>
          </ul>
          <a href="<?php echo esc_url( home_url( '/events' ) ); ?>" class="page-venue__events-explore">Explore all event types &rarr;</a>
        </div>

      </div>
    </div>
  </section>

  <!-- ── 6. Interactive Floor Plan ──────────────────────── -->
  <section class="page-inner__section venue-floorplan" id="venue-floorplan" aria-labelledby="floorplan-heading">

    <div class="venue-floorplan__header">
      <p class="page-inner__section-head">Explore the Space</p>
      <h2 class="page-inner__section-title" id="floorplan-heading">The building,<br>floor by floor.</h2>
    </div>

    <!-- Mobile floor tabs (shown at ≤900px instead of SVG hover) -->
    <div class="venue-floorplan__tabs" role="tablist" aria-label="Select floor">
      <div class="venue-floorplan__tab-list">
        <button class="venue-floorplan__tab is-active" data-floor="level1" role="tab" aria-selected="true">The Gallery</button>
        <button class="venue-floorplan__tab" data-floor="ground" role="tab" aria-selected="false">Ground Floor</button>
        <button class="venue-floorplan__tab" data-floor="terrace" role="tab" aria-selected="false">Outdoor Area</button>
        <button class="venue-floorplan__tab" data-floor="level2" role="tab" aria-selected="false">Level 2 &mdash; Creators</button>
      </div>
    </div>

    <div class="venue-floorplan__split">

      <!-- Left: Building elevation SVG -->
      <!-- SWAP POINT: replace the SVG below with the real architectural drawing when provided.
           Maintain the .fp-floor[data-floor] groups and .fp-zone-hit / .fp-zone-border elements
           so the JS interactions continue to work. -->
      <div class="venue-floorplan__building" role="region" aria-label="Building cross-section — hover or click a floor to explore">

        <div class="venue-floorplan__svg-wrap">
          <svg class="venue-floorplan__svg" viewBox="0 0 590 465" fill="none" xmlns="http://www.w3.org/2000/svg"
               role="img" aria-label="TEMPO House building elevation — 218c Pasteur, District 3">

            <defs>
              <!-- Diagonal hatch — fades in on floor hover/active -->
              <pattern id="fp-hatch" x="0" y="0" width="6" height="6" patternUnits="userSpaceOnUse" patternTransform="rotate(45 0 0)">
                <line x1="0" y1="0" x2="0" y2="6" stroke="rgba(123,59,59,0.16)" stroke-width="0.65"/>
              </pattern>
              <!-- Amber ceiling glow — top-to-bottom per floor zone -->
              <linearGradient id="fp-glow-l1" x1="0" y1="0" x2="0" y2="1" gradientUnits="objectBoundingBox">
                <stop offset="0%"   stop-color="rgba(221,170,98,0.28)"/>
                <stop offset="50%"  stop-color="rgba(221,170,98,0.08)"/>
                <stop offset="100%" stop-color="rgba(221,170,98,0)"/>
              </linearGradient>
              <linearGradient id="fp-glow-l2" x1="0" y1="0" x2="0" y2="1" gradientUnits="objectBoundingBox">
                <stop offset="0%"   stop-color="rgba(221,170,98,0.28)"/>
                <stop offset="50%"  stop-color="rgba(221,170,98,0.08)"/>
                <stop offset="100%" stop-color="rgba(221,170,98,0)"/>
              </linearGradient>
              <linearGradient id="fp-glow-gnd" x1="0" y1="0" x2="0" y2="1" gradientUnits="objectBoundingBox">
                <stop offset="0%"   stop-color="rgba(221,170,98,0.28)"/>
                <stop offset="50%"  stop-color="rgba(221,170,98,0.08)"/>
                <stop offset="100%" stop-color="rgba(221,170,98,0)"/>
              </linearGradient>
            </defs>

            <!-- ═══ PARAPET / ROOF CROWN ════════════════════════ -->
            <g class="fp-walls">
              <rect x="38" y="2"  width="304" height="8"/>
              <rect x="48" y="10" width="284" height="6"/>
              <rect x="62" y="16" width="256" height="26"/>
              <rect x="48" y="42" width="284" height="5"/>
            </g>
            <g class="fp-detail">
              <line x1="66" y1="19" x2="314" y2="19"/>
              <line x1="66" y1="39" x2="314" y2="39"/>
              <line x1="66"  y1="20" x2="66"  y2="39"/><line x1="77"  y1="20" x2="77"  y2="39"/>
              <line x1="88"  y1="20" x2="88"  y2="39"/><line x1="99"  y1="20" x2="99"  y2="39"/>
              <line x1="110" y1="20" x2="110" y2="39"/><line x1="121" y1="20" x2="121" y2="39"/>
              <line x1="132" y1="20" x2="132" y2="39"/><line x1="143" y1="20" x2="143" y2="39"/>
              <line x1="154" y1="20" x2="154" y2="39"/><line x1="165" y1="20" x2="165" y2="39"/>
              <line x1="176" y1="20" x2="176" y2="39"/><line x1="187" y1="20" x2="187" y2="39"/>
              <line x1="198" y1="20" x2="198" y2="39"/><line x1="209" y1="20" x2="209" y2="39"/>
              <line x1="220" y1="20" x2="220" y2="39"/><line x1="231" y1="20" x2="231" y2="39"/>
              <line x1="242" y1="20" x2="242" y2="39"/><line x1="253" y1="20" x2="253" y2="39"/>
              <line x1="264" y1="20" x2="264" y2="39"/><line x1="275" y1="20" x2="275" y2="39"/>
              <line x1="286" y1="20" x2="286" y2="39"/><line x1="297" y1="20" x2="297" y2="39"/>
              <line x1="308" y1="20" x2="308" y2="39"/>
            </g>

            <!-- Building left + right outer walls -->
            <g class="fp-walls">
              <rect x="62"  y="45" width="8"  height="375"/>
              <rect x="310" y="45" width="8"  height="375"/>
            </g>

            <!-- Foundation -->
            <rect class="fp-foundation" x="50" y="420" width="280" height="7"/>

            <!-- ═══ FLOOR ZONE 1: OUTDOOR TERRACE (y=45–127) ═══ -->
            <g class="fp-floor" data-floor="level2" data-url="/creators"
               tabindex="0" role="button" aria-label="Level 2 Creators — tap to explore">

              <rect class="fp-floor-bg"    x="70" y="45"  width="248" height="82"/>
              <rect class="fp-zone-hatch"  x="62" y="45"  width="256" height="82"/>
              <rect class="fp-zone-glow"   x="62" y="45"  width="256" height="82" fill="url(#fp-glow-l2)"/>
              <rect class="fp-zone-hit"    x="62" y="45"  width="256" height="82"/>
              <rect class="fp-zone-border" x="62" y="45"  width="256" height="82"/>

              <!-- Overhead pergola: two horizontal beams -->
              <line class="fp-detail"      x1="70"  y1="51" x2="310" y2="51"/>
              <line class="fp-detail"      x1="70"  y1="58" x2="310" y2="58"/>
              <!-- Pergola slats (dashed, side view) -->
              <line class="fp-detail-dash" x1="82"  y1="51" x2="82"  y2="58"/>
              <line class="fp-detail-dash" x1="94"  y1="51" x2="94"  y2="58"/>
              <line class="fp-detail-dash" x1="106" y1="51" x2="106" y2="58"/>
              <line class="fp-detail-dash" x1="118" y1="51" x2="118" y2="58"/>
              <line class="fp-detail-dash" x1="130" y1="51" x2="130" y2="58"/>
              <line class="fp-detail-dash" x1="142" y1="51" x2="142" y2="58"/>
              <line class="fp-detail-dash" x1="154" y1="51" x2="154" y2="58"/>
              <line class="fp-detail-dash" x1="166" y1="51" x2="166" y2="58"/>
              <line class="fp-detail-dash" x1="178" y1="51" x2="178" y2="58"/>
              <line class="fp-detail-dash" x1="190" y1="51" x2="190" y2="58"/>
              <line class="fp-detail-dash" x1="202" y1="51" x2="202" y2="58"/>
              <line class="fp-detail-dash" x1="214" y1="51" x2="214" y2="58"/>
              <line class="fp-detail-dash" x1="226" y1="51" x2="226" y2="58"/>
              <line class="fp-detail-dash" x1="238" y1="51" x2="238" y2="58"/>
              <line class="fp-detail-dash" x1="250" y1="51" x2="250" y2="58"/>
              <line class="fp-detail-dash" x1="262" y1="51" x2="262" y2="58"/>
              <line class="fp-detail-dash" x1="274" y1="51" x2="274" y2="58"/>
              <line class="fp-detail-dash" x1="286" y1="51" x2="286" y2="58"/>
              <line class="fp-detail-dash" x1="298" y1="51" x2="298" y2="58"/>

              <!-- Left railing / parapet wall -->
              <rect class="fp-detail" x="70" y="58" width="6" height="66"/>

              <!-- Left monstera / tropical plant -->
              <path class="fp-detail" d="M82,122 C82,110 82,96 84,82"/>
              <path class="fp-detail" d="M84,88 C76,80 66,74 67,70 C71,74 80,82 84,88"/>
              <path class="fp-detail" d="M84,86 C92,76 100,70 98,66 C94,70 86,79 84,86"/>
              <path class="fp-detail" d="M83,97 C72,95 63,91 62,86 C67,87 76,93 83,97"/>
              <path class="fp-detail" d="M84,95 C95,92 103,89 104,84 C99,86 91,91 84,95"/>
              <path class="fp-detail" d="M83,106 C74,108 65,109 63,105 C67,104 76,105 83,106"/>
              <ellipse class="fp-detail" cx="77"  cy="118" rx="9"  ry="5"/>
              <ellipse class="fp-detail" cx="91"  cy="120" rx="7"  ry="4"/>

              <!-- Right plant cluster -->
              <path class="fp-detail" d="M290,122 C290,110 290,96 288,82"/>
              <path class="fp-detail" d="M288,88 C296,80 306,74 305,70 C301,74 292,82 288,88"/>
              <path class="fp-detail" d="M288,86 C280,76 272,70 274,66 C278,70 286,79 288,86"/>
              <path class="fp-detail" d="M289,97 C300,95 309,91 310,86 C305,87 296,93 289,97"/>
              <path class="fp-detail" d="M288,106 C298,108 307,109 309,105 C305,104 296,105 288,106"/>
              <ellipse class="fp-detail" cx="295" cy="118" rx="9"  ry="5"/>
              <ellipse class="fp-detail" cx="280" cy="120" rx="7"  ry="4"/>

              <!-- Lounge seating (profile view) -->
              <rect class="fp-seat" x="148" y="110" width="32" height="8"/>
              <path class="fp-seat" d="M148,111 L144,99"/>
              <line class="fp-seat" x1="150" y1="118" x2="148" y2="122"/>
              <line class="fp-seat" x1="178" y1="118" x2="178" y2="122"/>
              <rect class="fp-seat" x="194" y="110" width="32" height="8"/>
              <path class="fp-seat" d="M194,111 L190,99"/>
              <line class="fp-seat" x1="196" y1="118" x2="194" y2="122"/>
              <line class="fp-seat" x1="224" y1="118" x2="224" y2="122"/>
              <rect class="fp-seat" x="182" y="107" width="10" height="6"/>

              <!-- Staircase (right, terrace access) -->
              <line class="fp-detail" x1="294" y1="45"  x2="294" y2="127"/>
              <line class="fp-stair"  x1="294" y1="62"  x2="318" y2="62"/>
              <line class="fp-stair"  x1="294" y1="74"  x2="318" y2="74"/>
              <line class="fp-stair"  x1="294" y1="86"  x2="318" y2="86"/>
              <line class="fp-stair"  x1="294" y1="98"  x2="318" y2="98"/>
              <line class="fp-stair"  x1="294" y1="110" x2="318" y2="110"/>
              <line class="fp-stair"  x1="294" y1="122" x2="318" y2="122"/>

              <!-- Floor name label (visible on mobile only) -->
              <!-- Right-side floor label — bracket + tick + multi-line text -->
              <line class="fp-label-bracket" x1="322" y1="48"  x2="322" y2="124"/>
              <line class="fp-label-tick"    x1="322" y1="86"  x2="330" y2="86"/>
              <text class="fp-floor-label fp-floor-label--right" text-anchor="start">
                <tspan x="334" y="82">Level 2</tspan>
                <tspan x="334" dy="13">Creators</tspan>
              </text>

              <line class="fp-floor-line" x1="62" y1="127" x2="318" y2="127"/>
            </g>

            <!-- ═══ FLOOR ZONE 2: LEVEL 1 — GALLERY (y=127–275) ═══ -->
            <g class="fp-floor is-active" data-floor="level1" data-url="/art-gallery"
               tabindex="0" role="button" aria-label="The Gallery — tap to explore">

              <rect class="fp-floor-bg"    x="70" y="127" width="248" height="148"/>
              <rect class="fp-zone-hatch"  x="62" y="127" width="256" height="148"/>
              <rect class="fp-zone-glow"   x="62" y="127" width="256" height="148" fill="url(#fp-glow-l1)"/>
              <rect class="fp-zone-hit"    x="62" y="127" width="256" height="148"/>
              <rect class="fp-zone-border" x="62" y="127" width="256" height="148"/>

              <!-- Ceiling cornice -->
              <line class="fp-detail" x1="70"  y1="133" x2="292" y2="133"/>
              <line class="fp-detail" x1="70"  y1="138" x2="292" y2="138"/>

              <!-- Track lighting rail + five amber fixtures -->
              <line class="fp-detail" x1="82" y1="152" x2="284" y2="152"/>
              <circle class="fp-track-light" cx="108" cy="152" r="4.5"/>
              <circle class="fp-track-light" cx="144" cy="152" r="4.5"/>
              <circle class="fp-track-light" cx="180" cy="152" r="4.5"/>
              <circle class="fp-track-light" cx="216" cy="152" r="4.5"/>
              <circle class="fp-track-light" cx="252" cy="152" r="4.5"/>

              <!-- LEFT: Large colonial arched window (multi-pane) -->
              <path class="fp-window" d="M70,244 L70,166 Q88,144 106,166 L106,244 Z"/>
              <line class="fp-window" x1="70"  y1="180" x2="106" y2="180"/>
              <line class="fp-window" x1="70"  y1="198" x2="106" y2="198"/>
              <line class="fp-window" x1="70"  y1="216" x2="106" y2="216"/>
              <line class="fp-window" x1="70"  y1="232" x2="106" y2="232"/>
              <line class="fp-window" x1="88"  y1="166" x2="88"  y2="244"/>

              <!-- RIGHT: Paired arched window -->
              <path class="fp-window" d="M280,244 L280,174 Q292,156 304,174 L304,244 Z"/>
              <line class="fp-window" x1="280" y1="192" x2="304" y2="192"/>
              <line class="fp-window" x1="280" y1="212" x2="304" y2="212"/>
              <line class="fp-window" x1="280" y1="230" x2="304" y2="230"/>
              <line class="fp-window" x1="292" y1="175" x2="292" y2="244"/>

              <!-- Architrave / gallery wall top edge -->
              <line class="fp-detail" x1="106" y1="144" x2="280" y2="144" stroke-opacity="0.18"/>

              <!-- Base skirting cornice -->
              <line class="fp-detail" x1="70"  y1="268" x2="290" y2="268"/>
              <line class="fp-detail" x1="70"  y1="272" x2="290" y2="272"/>

              <!-- Staircase shaft + Level 1 treads -->
              <line class="fp-detail" x1="294" y1="127" x2="294" y2="275"/>
              <line class="fp-stair"  x1="294" y1="140" x2="318" y2="140"/>
              <line class="fp-stair"  x1="294" y1="152" x2="318" y2="152"/>
              <line class="fp-stair"  x1="294" y1="164" x2="318" y2="164"/>
              <line class="fp-stair"  x1="294" y1="176" x2="318" y2="176"/>
              <line class="fp-stair"  x1="294" y1="188" x2="318" y2="188"/>
              <line class="fp-stair"  x1="294" y1="200" x2="318" y2="200"/>
              <line class="fp-stair"  x1="294" y1="212" x2="318" y2="212"/>
              <line class="fp-stair"  x1="294" y1="224" x2="318" y2="224"/>
              <line class="fp-stair"  x1="294" y1="236" x2="318" y2="236"/>
              <line class="fp-stair"  x1="294" y1="248" x2="318" y2="248"/>
              <line class="fp-stair"  x1="294" y1="260" x2="318" y2="260"/>
              <line class="fp-stair"  x1="294" y1="272" x2="318" y2="272"/>

              <!-- Right-side floor label — bracket + tick + multi-line text -->
              <line class="fp-label-bracket" x1="322" y1="130" x2="322" y2="272"/>
              <line class="fp-label-tick"    x1="322" y1="201" x2="330" y2="201"/>
              <text class="fp-floor-label fp-floor-label--right" text-anchor="start">
                <tspan x="334" y="197">Level 1</tspan>
                <tspan x="334" dy="14">The Gallery</tspan>
              </text>

              <line class="fp-floor-line" x1="62" y1="275" x2="318" y2="275"/>
            </g>

            <!-- ═══ FLOOR ZONE 3: GROUND FLOOR (y=275–420) ═══ -->
            <g class="fp-floor" data-floor="ground" data-url="/cafe"
               tabindex="0" role="button" aria-label="Ground Floor Café and Bar — tap to explore">

              <rect class="fp-floor-bg"    x="70" y="275" width="248" height="145"/>
              <rect class="fp-zone-hatch"  x="62" y="275" width="256" height="145"/>
              <rect class="fp-zone-glow"   x="62" y="275" width="256" height="145" fill="url(#fp-glow-gnd)"/>
              <rect class="fp-zone-hit"    x="62" y="275" width="256" height="145"/>
              <rect class="fp-zone-border" x="62" y="275" width="256" height="145"/>

              <!-- Ceiling cornice -->
              <line class="fp-detail" x1="70"  y1="281" x2="290" y2="281"/>
              <line class="fp-detail" x1="70"  y1="286" x2="290" y2="286"/>

              <!-- LEFT: Narrow arched window -->
              <path class="fp-window" d="M70,382 L70,318 Q80,305 90,318 L90,382 Z"/>
              <line class="fp-window" x1="80"  y1="320" x2="80"  y2="382"/>
              <line class="fp-window" x1="70"  y1="336" x2="90"  y2="336"/>
              <line class="fp-window" x1="70"  y1="354" x2="90"  y2="354"/>
              <line class="fp-window" x1="70"  y1="372" x2="90"  y2="372"/>

              <!-- GRAND ENTRANCE ARCH — signature feature -->
              <!-- Outer arch surround (heavy weight) -->
              <g class="fp-walls">
                <path d="M120,420 L120,344 Q190,278 260,344 L260,420"/>
              </g>
              <!-- Inner arch opening -->
              <path class="fp-arch"   d="M132,420 L132,349 Q190,284 248,349 L248,420"/>
              <!-- Decorative inner arch frame line -->
              <path class="fp-detail" d="M140,420 L140,353 Q190,289 240,353 L240,420" stroke-opacity="0.40"/>
              <!-- Keystone at arch crown -->
              <path class="fp-detail" d="M184,280 L190,273 L196,280"/>
              <line class="fp-detail"  x1="190" y1="273" x2="190" y2="283" stroke-opacity="0.55"/>
              <!-- Jamb pilasters -->
              <line class="fp-detail" x1="128" y1="348" x2="128" y2="420" stroke-opacity="0.45"/>
              <line class="fp-detail" x1="252" y1="348" x2="252" y2="420" stroke-opacity="0.45"/>
              <!-- Centre door panel within arch -->
              <rect class="fp-door"   x="162" y="358" width="56" height="62"/>
              <path class="fp-arch"   d="M162,360 Q190,341 218,360"/>
              <line class="fp-door"   x1="190" y1="362" x2="190" y2="420"/>
              <line class="fp-detail" x1="166" y1="380" x2="188" y2="380" stroke-opacity="0.35"/>
              <line class="fp-detail" x1="192" y1="380" x2="214" y2="380" stroke-opacity="0.35"/>

              <!-- RIGHT: Small arched window -->
              <path class="fp-window" d="M282,375 L282,328 Q291,316 300,328 L300,375 Z"/>
              <line class="fp-window" x1="291" y1="330" x2="291" y2="375"/>
              <line class="fp-window" x1="282" y1="348" x2="300" y2="348"/>

              <!-- Interior: café counter + espresso machine -->
              <rect class="fp-counter" x="93"  y="366" width="30" height="8"/>
              <rect class="fp-detail"  x="99"  y="356" width="16" height="10"/>
              <rect class="fp-detail"  x="103" y="352" width="6"  height="5"/>
              <line class="fp-detail"  x1="104" y1="352" x2="105" y2="350"/>
              <rect class="fp-seat"    x="93"  y="374" width="10" height="12"/>
              <rect class="fp-seat"    x="107" y="374" width="10" height="12"/>

              <!-- Interior: cocktail bar counter + bottle shelving -->
              <rect class="fp-counter" x="254" y="363" width="38" height="8"/>
              <line class="fp-detail"  x1="257" y1="353" x2="287" y2="353"/>
              <line class="fp-detail"  x1="257" y1="344" x2="287" y2="344"/>
              <line class="fp-detail"  x1="261" y1="353" x2="261" y2="344" stroke-opacity="0.65"/>
              <line class="fp-detail"  x1="267" y1="353" x2="267" y2="343" stroke-opacity="0.65"/>
              <line class="fp-detail"  x1="273" y1="353" x2="273" y2="344" stroke-opacity="0.65"/>
              <line class="fp-detail"  x1="279" y1="353" x2="279" y2="344" stroke-opacity="0.65"/>
              <line class="fp-detail"  x1="285" y1="353" x2="285" y2="343" stroke-opacity="0.65"/>

              <!-- Staircase shaft + Ground floor treads -->
              <line class="fp-detail" x1="294" y1="275" x2="294" y2="420"/>
              <line class="fp-stair"  x1="294" y1="288" x2="318" y2="288"/>
              <line class="fp-stair"  x1="294" y1="300" x2="318" y2="300"/>
              <line class="fp-stair"  x1="294" y1="312" x2="318" y2="312"/>
              <line class="fp-stair"  x1="294" y1="324" x2="318" y2="324"/>
              <line class="fp-stair"  x1="294" y1="336" x2="318" y2="336"/>
              <line class="fp-stair"  x1="294" y1="348" x2="318" y2="348"/>
              <line class="fp-stair"  x1="294" y1="360" x2="318" y2="360"/>
              <line class="fp-stair"  x1="294" y1="372" x2="318" y2="372"/>
              <line class="fp-stair"  x1="294" y1="384" x2="318" y2="384"/>
              <line class="fp-stair"  x1="294" y1="396" x2="318" y2="396"/>
              <line class="fp-stair"  x1="294" y1="408" x2="318" y2="408"/>

              <!-- Right-side floor label — bracket + tick + multi-line text -->
              <line class="fp-label-bracket" x1="322" y1="278" x2="322" y2="417"/>
              <line class="fp-label-tick"    x1="322" y1="347" x2="330" y2="347"/>
              <text class="fp-floor-label fp-floor-label--right" text-anchor="start">
                <tspan x="334" y="343">Ground</tspan>
                <tspan x="334" dy="14">Caf&#233; &amp; Bar</tspan>
              </text>
            </g>

            <!-- ═══ DIMENSION &amp; LEVEL ANNOTATIONS ═══ -->
            <line class="fp-dim-line" x1="28" y1="45"  x2="28" y2="420"/>
            <line class="fp-dim-line" x1="18" y1="45"  x2="54" y2="45"/>
            <line class="fp-dim-line" x1="18" y1="127" x2="54" y2="127"/>
            <line class="fp-dim-line" x1="18" y1="275" x2="54" y2="275"/>
            <line class="fp-dim-line" x1="18" y1="420" x2="54" y2="420"/>

            <text class="fp-level-label" x="16" y="88"  text-anchor="end">Level 2</text>
            <text class="fp-level-label" x="16" y="206" text-anchor="end">Level 1</text>
            <text class="fp-level-label" x="16" y="352" text-anchor="end">Ground</text>

            <text class="fp-level-label" x="32" y="86"  text-anchor="start">45&#x2013;127</text>
            <text class="fp-level-label" x="32" y="204" text-anchor="start">127&#x2013;275</text>
            <text class="fp-level-label" x="32" y="350" text-anchor="start">275&#x2013;420</text>

            <!-- Ground line extension to outdoor area -->
            <line class="fp-foundation" x1="318" y1="424" x2="578" y2="424"/>

            <!-- OUTDOOR AREA — garden zone to the right of the building -->
            <g class="fp-floor fp-outdoor" data-floor="terrace" data-url="<?php echo esc_attr( home_url( '/cafe' ) ); ?>"
               tabindex="0" role="button" aria-label="Outdoor Area — tap to explore">
              <rect class="fp-zone-hatch"  x="450" y="290" width="128" height="130" opacity="0.25"/>
              <line class="fp-detail-dash" x1="450" y1="290" x2="578" y2="290"/>
              <line class="fp-detail-dash" x1="450" y1="290" x2="450" y2="420"/>
              <line class="fp-detail-dash" x1="578" y1="290" x2="578" y2="420"/>
              <rect class="fp-outdoor-element" x="508" y="374" width="6" height="46" fill="rgba(0,0,0,0.72)"/>
              <ellipse class="fp-outdoor-element" cx="504" cy="352" rx="15" ry="21" fill="rgba(0,0,0,0.06)"/>
              <ellipse class="fp-outdoor-element" cx="516" cy="347" rx="11" ry="16" fill="rgba(0,0,0,0.06)"/>
              <ellipse class="fp-outdoor-element" cx="510" cy="338" rx="19" ry="13" fill="rgba(0,0,0,0.08)"/>
              <rect class="fp-outdoor-element" x="461" y="404" width="20" height="6"/>
              <rect class="fp-outdoor-element" x="461" y="396" width="4" height="9"/>
              <line class="fp-outdoor-element" x1="462" y1="410" x2="462" y2="417"/>
              <line class="fp-outdoor-element" x1="479" y1="410" x2="479" y2="417"/>
              <rect class="fp-outdoor-element" x="486" y="406" width="13" height="4" fill="rgba(0,0,0,0.35)"/>
              <line class="fp-outdoor-element" x1="490" y1="410" x2="490" y2="417"/>
              <line class="fp-outdoor-element" x1="496" y1="410" x2="496" y2="417"/>
              <rect class="fp-outdoor-element" x="538" y="404" width="20" height="6"/>
              <rect class="fp-outdoor-element" x="554" y="396" width="4" height="9"/>
              <line class="fp-outdoor-element" x1="540" y1="410" x2="540" y2="417"/>
              <line class="fp-outdoor-element" x1="557" y1="410" x2="557" y2="417"/>
              <rect class="fp-zone-hit"    x="450" y="290" width="128" height="130"/>
              <rect class="fp-zone-border" x="450" y="290" width="128" height="130"/>
              <text class="fp-level-label" text-anchor="middle" x="514" y="281">Outdoor Area</text>
              <line class="fp-dim-line" x1="514" y1="284" x2="514" y2="290" opacity="0.35"/>
            </g>

          </svg>
          <!-- Desktop hover popup — positioned relative to svg-wrap so it sits beside the drawing -->
          <div class="venue-floorplan__popup" role="tooltip" aria-hidden="true">
            <p class="venue-floorplan__popup-label"></p>
            <div class="venue-floorplan__popup-stats"></div>
            <p class="venue-floorplan__popup-use"></p>
          </div>

        </div><!-- /.venue-floorplan__svg-wrap -->

        <!-- Mobile mini-detail: shown at <1000px on floor tap (populated by JS) -->
        <div class="venue-floorplan__mini-detail" aria-live="polite">
          <p class="venue-floorplan__mini-label"></p>
          <div class="venue-floorplan__mini-row">
            <div class="venue-floorplan__mini-stats"></div>
            <a class="venue-floorplan__mini-link" href="#">Explore floor &rarr;</a>
          </div>
        </div>

      </div><!-- /.venue-floorplan__building -->

      <!-- Right: Floor detail panel -->
      <div class="venue-floorplan__detail">

        <div class="venue-floorplan__detail-header">
          <p class="venue-floorplan__detail-label">Level 1 &mdash; The Gallery</p>
          <h3 class="venue-floorplan__detail-title">Column-free gallery floor.</h3>
          <p class="venue-floorplan__detail-desc">No columns, neutral walls, adjustable track lighting. Full-width terrace connection. Purpose-built to hold exhibitions without competing with them &mdash; works equally for events that need real architecture. Holds 60+ standing or 30 seated.</p>
        </div>

        <!-- SWAP POINT: when real floor plan drawings are provided,
             update the plan src in FLOOR_DATA inside venue-floorplan.js.
             JS will toggle --placeholder and show the img automatically. -->
        <div class="tempo-frame tempo-frame--placeholder venue-floorplan__plan" aria-label="Floor plan for Level 1 Gallery">
          <div class="tempo-frame__mat">
            <div class="tempo-frame__artwork">
              <img class="tempo-frame__img" src="" alt="" style="display:none">
              <span class="tempo-frame__label">Level 1 &mdash; Floor Plan Coming Soon</span>
            </div>
          </div>
        </div>

        <div class="venue-floorplan__specs">
          <!-- Populated by venue-floorplan.js on init -->
        </div>

      </div><!-- /.venue-floorplan__detail -->

    </div><!-- /.venue-floorplan__split -->

  </section>

  <!-- ── 7. The Neighbourhood ───────────────────────────── -->
  <section class="page-inner__section" aria-labelledby="neighbourhood-heading">
    <div class="page-inner__container">
      <div class="page-inner__split">

        <div class="tempo-frame page-venue__neighbourhood-img" data-interactive aria-label="Pasteur Street, District 3, Ho Chi Minh City">
          <div class="tempo-frame__mat">
            <div class="tempo-frame__artwork">
              <img class="tempo-frame__img" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/venue/venue-neighbourhood.jpg" alt="Pasteur Street District 3 Ho Chi Minh City" loading="lazy">
              <span class="tempo-frame__caption">Pasteur Street &middot; District 3</span>
            </div>
          </div>
        </div>

        <div>
          <p class="page-inner__section-head">District 3</p>
          <h2 class="page-inner__section-title" id="neighbourhood-heading">Pasteur Street.<br>District 3, Ho Chi Minh City.</h2>

          <div class="page-inner__section-body">
            <p>Pasteur Street runs through the heart of District 3 under a canopy of old-growth trees. The streetscape is predominantly colonial &mdash; low-rise facades, independent businesses, residences above shops. 218c sits on a stretch that includes some of the neighbourhood&rsquo;s best independent caf&eacute;s and restaurants.</p>
            <p>T&#7841;o &ETH;&agrave;n park is a four-minute walk. B&#7871;n Th&agrave;nh market is 10 minutes on foot. Grab and ride-share drop-off directly on Pasteur Street.</p>
          </div>

          <div class="page-inner__info-grid page-venue__neighbourhood-grid">
            <div>
              <p class="page-inner__info-label">Getting Here</p>
              <p class="page-inner__info-value">Grab &amp; ride-share on Pasteur. 10 min walk from B&#7871;n Th&agrave;nh.</p>
            </div>
            <div>
              <p class="page-inner__info-label">Parking</p>
              <p class="page-inner__info-value">Street parking on Pasteur &amp; surrounding blocks. Lots on Nguy&#7877;n Th&#7883; Minh Khai.</p>
            </div>
            <div>
              <p class="page-inner__info-label">Neighbourhood</p>
              <p class="page-inner__info-value">T&#7841;o &ETH;&agrave;n park, Pasteur dining strip, colonial streetscape.</p>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- ── 8. Contact Strip ────────────────────────────────── -->
  <div class="page-venue__contact-strip">
    <div class="page-inner__container">
      <div class="page-inner__info-grid">
        <div>
          <p class="page-inner__info-label">Address</p>
          <p class="page-inner__info-value">218c Pasteur, Xu&acirc;n Ho&agrave;<br>Qu&#7853;n 3, Ho Chi Minh City</p>
        </div>
        <div>
          <p class="page-inner__info-label">Caf&eacute; Hours</p>
          <p class="page-inner__info-value">08:00 &ndash; 17:00 daily</p>
        </div>
        <div>
          <p class="page-inner__info-label">Bar Hours</p>
          <p class="page-inner__info-value">18:00 &ndash; 01:00 daily</p>
        </div>
        <div>
          <p class="page-inner__info-label">Event Enquiries</p>
          <p class="page-inner__info-value"><a href="mailto:events@tempohouse.com.vn">events@tempohouse.com.vn</a></p>
        </div>
        <div>
          <p class="page-inner__info-label">General</p>
          <p class="page-inner__info-value"><a href="mailto:hello@tempohouse.com.vn">hello@tempohouse.com.vn</a></p>
        </div>
      </div>
    </div>
  </div>

  <!-- ── 9. Footer CTA ──────────────────────────────────── -->
  <section class="page-inner__section--dark page-venue__footer-cta" aria-label="Enquire about your event">
    <div class="page-inner__container">
      <h2 class="page-venue__footer-cta-title">Come when you have<br>something to say.</h2>
      <div class="page-inner__cta-row page-venue__footer-cta-row">
        <a href="<?php echo esc_url( home_url( '/event-enquiry' ) ); ?>" class="page-inner__cta-primary">Enquire About Your Event</a>
        <a href="<?php echo esc_url( home_url( '/reservations' ) ); ?>" class="page-inner__cta-secondary">Book a table &rarr;</a>
      </div>
    </div>
  </section>

</main>

<?php get_footer(); ?>
