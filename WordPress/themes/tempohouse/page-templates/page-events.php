<?php
/**
 * Template Name: Events
 * Description: Private events overview — interactive spaces explorer, event type gallery belt, catering, and FAQ.
 */
get_header();
?>

<main class="page-events" id="main" role="main">

  <!-- ── 1. Page Banner ──────────────────────────── -->
  <header class="page-inner__banner page-events__banner">
    <p class="page-inner__eyebrow">Private Events</p>
    <h1 class="page-inner__title">An event venue that doesn&rsquo;t look like one.<br>Private hire, District 3, Saigon.</h1>
    <p class="page-inner__lead">TEMPO House is a gallery, bar, and caf&eacute; available for exclusive hire &mdash; by the floor or the whole building. We host product launches, corporate dinners, art exhibitions, birthdays, weddings, and brand activations.</p>
    <div class="page-inner__cta-row">
      <a href="<?php echo esc_url( home_url( '/event-enquiry' ) ); ?>" class="page-inner__cta-primary">Start Your Enquiry</a>
      <a href="<?php echo esc_url( home_url( '/venue' ) ); ?>" class="page-inner__cta-secondary">See the Spaces &rarr;</a>
    </div>
  </header>

  <!-- ── 2. Available Spaces — Interactive Floor Plan ──────────────── -->
  <section class="page-inner__section venue-floorplan page-events__spaces-fp" id="events-spaces" aria-labelledby="spaces-title">

    <div class="venue-floorplan__header">
      <p class="page-inner__section-head">Available Spaces</p>
      <h2 class="page-inner__section-title" id="spaces-title">Hire one floor<br>or the whole building.</h2>
    </div>

    <!-- Mobile floor tabs (shown at ≤1000px) -->
    <div class="venue-floorplan__tabs" role="tablist" aria-label="Select space">
      <div class="venue-floorplan__tab-list">
        <button class="venue-floorplan__tab is-active" data-floor="level1" role="tab" aria-selected="true">The Gallery</button>
        <button class="venue-floorplan__tab" data-floor="ground" role="tab" aria-selected="false">Ground Floor</button>
        <button class="venue-floorplan__tab" data-floor="terrace" role="tab" aria-selected="false">Outdoor Area</button>
        <button class="venue-floorplan__tab" data-floor="level2" role="tab" aria-selected="false">Level 2 &mdash; Creators</button>
      </div>
    </div>

    <div class="venue-floorplan__split">

      <!-- Left: Building elevation SVG -->
      <div class="venue-floorplan__building" role="region" aria-label="Building cross-section — hover or click a floor to explore">

        <div class="venue-floorplan__svg-wrap">
          <svg class="venue-floorplan__svg" viewBox="0 0 590 465" fill="none" xmlns="http://www.w3.org/2000/svg"
               role="img" aria-label="TEMPO House building elevation — 218c Pasteur, District 3">

            <defs>
              <pattern id="efp-hatch" x="0" y="0" width="6" height="6" patternUnits="userSpaceOnUse" patternTransform="rotate(45 0 0)">
                <line x1="0" y1="0" x2="0" y2="6" stroke="rgba(123,59,59,0.16)" stroke-width="0.65"/>
              </pattern>
              <linearGradient id="efp-glow-l1" x1="0" y1="0" x2="0" y2="1" gradientUnits="objectBoundingBox">
                <stop offset="0%"   stop-color="rgba(221,170,98,0.28)"/>
                <stop offset="50%"  stop-color="rgba(221,170,98,0.08)"/>
                <stop offset="100%" stop-color="rgba(221,170,98,0)"/>
              </linearGradient>
              <linearGradient id="efp-glow-l2" x1="0" y1="0" x2="0" y2="1" gradientUnits="objectBoundingBox">
                <stop offset="0%"   stop-color="rgba(221,170,98,0.28)"/>
                <stop offset="50%"  stop-color="rgba(221,170,98,0.08)"/>
                <stop offset="100%" stop-color="rgba(221,170,98,0)"/>
              </linearGradient>
              <linearGradient id="efp-glow-gnd" x1="0" y1="0" x2="0" y2="1" gradientUnits="objectBoundingBox">
                <stop offset="0%"   stop-color="rgba(221,170,98,0.28)"/>
                <stop offset="50%"  stop-color="rgba(221,170,98,0.08)"/>
                <stop offset="100%" stop-color="rgba(221,170,98,0)"/>
              </linearGradient>
            </defs>

            <!-- PARAPET / ROOF CROWN -->
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

            <!-- Building outer walls -->
            <g class="fp-walls">
              <rect x="62"  y="45" width="8"  height="375"/>
              <rect x="310" y="45" width="8"  height="375"/>
            </g>
            <rect class="fp-foundation" x="50" y="420" width="280" height="7"/>

            <!-- FLOOR ZONE: LEVEL 2 — CREATORS (y=45–127) -->
            <g class="fp-floor" data-floor="level2" data-url="<?php echo esc_attr( home_url( '/creators' ) ); ?>"
               tabindex="0" role="button" aria-label="Level 2 Creators — tap to explore">
              <rect class="fp-floor-bg"    x="70" y="45"  width="248" height="82"/>
              <rect class="fp-zone-hatch"  x="62" y="45"  width="256" height="82"/>
              <rect class="fp-zone-glow"   x="62" y="45"  width="256" height="82" fill="url(#efp-glow-l2)"/>
              <rect class="fp-zone-hit"    x="62" y="45"  width="256" height="82"/>
              <rect class="fp-zone-border" x="62" y="45"  width="256" height="82"/>
              <line class="fp-detail"      x1="70"  y1="51" x2="310" y2="51"/>
              <line class="fp-detail"      x1="70"  y1="58" x2="310" y2="58"/>
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
              <rect class="fp-detail" x="70" y="58" width="6" height="66"/>
              <path class="fp-detail" d="M82,122 C82,110 82,96 84,82"/>
              <path class="fp-detail" d="M84,88 C76,80 66,74 67,70 C71,74 80,82 84,88"/>
              <path class="fp-detail" d="M84,86 C92,76 100,70 98,66 C94,70 86,79 84,86"/>
              <path class="fp-detail" d="M83,97 C72,95 63,91 62,86 C67,87 76,93 83,97"/>
              <path class="fp-detail" d="M84,95 C95,92 103,89 104,84 C99,86 91,91 84,95"/>
              <path class="fp-detail" d="M83,106 C74,108 65,109 63,105 C67,104 76,105 83,106"/>
              <ellipse class="fp-detail" cx="77"  cy="118" rx="9"  ry="5"/>
              <ellipse class="fp-detail" cx="91"  cy="120" rx="7"  ry="4"/>
              <path class="fp-detail" d="M290,122 C290,110 290,96 288,82"/>
              <path class="fp-detail" d="M288,88 C296,80 306,74 305,70 C301,74 292,82 288,88"/>
              <path class="fp-detail" d="M288,86 C280,76 272,70 274,66 C278,70 286,79 288,86"/>
              <path class="fp-detail" d="M289,97 C300,95 309,91 310,86 C305,87 296,93 289,97"/>
              <path class="fp-detail" d="M288,106 C298,108 307,109 309,105 C305,104 296,105 288,106"/>
              <ellipse class="fp-detail" cx="295" cy="118" rx="9"  ry="5"/>
              <ellipse class="fp-detail" cx="280" cy="120" rx="7"  ry="4"/>
              <rect class="fp-seat" x="148" y="110" width="32" height="8"/>
              <path class="fp-seat" d="M148,111 L144,99"/>
              <line class="fp-seat" x1="150" y1="118" x2="148" y2="122"/>
              <line class="fp-seat" x1="178" y1="118" x2="178" y2="122"/>
              <rect class="fp-seat" x="194" y="110" width="32" height="8"/>
              <path class="fp-seat" d="M194,111 L190,99"/>
              <line class="fp-seat" x1="196" y1="118" x2="194" y2="122"/>
              <line class="fp-seat" x1="224" y1="118" x2="224" y2="122"/>
              <rect class="fp-seat" x="182" y="107" width="10" height="6"/>
              <line class="fp-detail" x1="294" y1="45"  x2="294" y2="127"/>
              <line class="fp-stair"  x1="294" y1="62"  x2="318" y2="62"/>
              <line class="fp-stair"  x1="294" y1="74"  x2="318" y2="74"/>
              <line class="fp-stair"  x1="294" y1="86"  x2="318" y2="86"/>
              <line class="fp-stair"  x1="294" y1="98"  x2="318" y2="98"/>
              <line class="fp-stair"  x1="294" y1="110" x2="318" y2="110"/>
              <line class="fp-stair"  x1="294" y1="122" x2="318" y2="122"/>
              <line class="fp-label-bracket" x1="322" y1="48"  x2="322" y2="124"/>
              <line class="fp-label-tick"    x1="322" y1="86"  x2="330" y2="86"/>
              <text class="fp-floor-label fp-floor-label--right" text-anchor="start">
                <tspan x="334" y="82">Level 2</tspan>
                <tspan x="334" dy="13">Creators</tspan>
              </text>
              <line class="fp-floor-line" x1="62" y1="127" x2="318" y2="127"/>
            </g>

            <!-- FLOOR ZONE: LEVEL 1 — GALLERY (y=127–275) -->
            <g class="fp-floor is-active" data-floor="level1" data-url="<?php echo esc_attr( home_url( '/art-gallery' ) ); ?>"
               tabindex="0" role="button" aria-label="Level 1 Gallery — tap to explore">
              <rect class="fp-floor-bg"    x="70" y="127" width="248" height="148"/>
              <rect class="fp-zone-hatch"  x="62" y="127" width="256" height="148"/>
              <rect class="fp-zone-glow"   x="62" y="127" width="256" height="148" fill="url(#efp-glow-l1)"/>
              <rect class="fp-zone-hit"    x="62" y="127" width="256" height="148"/>
              <rect class="fp-zone-border" x="62" y="127" width="256" height="148"/>
              <line class="fp-detail" x1="70"  y1="133" x2="292" y2="133"/>
              <line class="fp-detail" x1="70"  y1="138" x2="292" y2="138"/>
              <line class="fp-detail" x1="82" y1="152" x2="284" y2="152"/>
              <circle class="fp-track-light" cx="108" cy="152" r="4.5"/>
              <circle class="fp-track-light" cx="144" cy="152" r="4.5"/>
              <circle class="fp-track-light" cx="180" cy="152" r="4.5"/>
              <circle class="fp-track-light" cx="216" cy="152" r="4.5"/>
              <circle class="fp-track-light" cx="252" cy="152" r="4.5"/>
              <path class="fp-window" d="M70,244 L70,166 Q88,144 106,166 L106,244 Z"/>
              <line class="fp-window" x1="70"  y1="180" x2="106" y2="180"/>
              <line class="fp-window" x1="70"  y1="198" x2="106" y2="198"/>
              <line class="fp-window" x1="70"  y1="216" x2="106" y2="216"/>
              <line class="fp-window" x1="70"  y1="232" x2="106" y2="232"/>
              <line class="fp-window" x1="88"  y1="166" x2="88"  y2="244"/>
              <path class="fp-window" d="M280,244 L280,174 Q292,156 304,174 L304,244 Z"/>
              <line class="fp-window" x1="280" y1="192" x2="304" y2="192"/>
              <line class="fp-window" x1="280" y1="212" x2="304" y2="212"/>
              <line class="fp-window" x1="280" y1="230" x2="304" y2="230"/>
              <line class="fp-window" x1="292" y1="175" x2="292" y2="244"/>
              <line class="fp-detail" x1="106" y1="144" x2="280" y2="144" stroke-opacity="0.18"/>
              <line class="fp-detail" x1="70"  y1="268" x2="290" y2="268"/>
              <line class="fp-detail" x1="70"  y1="272" x2="290" y2="272"/>
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
              <line class="fp-label-bracket" x1="322" y1="130" x2="322" y2="272"/>
              <line class="fp-label-tick"    x1="322" y1="201" x2="330" y2="201"/>
              <text class="fp-floor-label fp-floor-label--right" text-anchor="start">
                <tspan x="334" y="197">Level 1</tspan>
                <tspan x="334" dy="14">The Gallery</tspan>
              </text>
              <line class="fp-floor-line" x1="62" y1="275" x2="318" y2="275"/>
            </g>

            <!-- FLOOR ZONE: GROUND FLOOR (y=275–420) -->
            <g class="fp-floor" data-floor="ground" data-url="<?php echo esc_attr( home_url( '/cafe' ) ); ?>"
               tabindex="0" role="button" aria-label="Ground Floor Café and Bar — tap to explore">
              <rect class="fp-floor-bg"    x="70" y="275" width="248" height="145"/>
              <rect class="fp-zone-hatch"  x="62" y="275" width="256" height="145"/>
              <rect class="fp-zone-glow"   x="62" y="275" width="256" height="145" fill="url(#efp-glow-gnd)"/>
              <rect class="fp-zone-hit"    x="62" y="275" width="256" height="145"/>
              <rect class="fp-zone-border" x="62" y="275" width="256" height="145"/>
              <line class="fp-detail" x1="70"  y1="281" x2="290" y2="281"/>
              <line class="fp-detail" x1="70"  y1="286" x2="290" y2="286"/>
              <path class="fp-window" d="M70,382 L70,318 Q80,305 90,318 L90,382 Z"/>
              <line class="fp-window" x1="80"  y1="320" x2="80"  y2="382"/>
              <line class="fp-window" x1="70"  y1="336" x2="90"  y2="336"/>
              <line class="fp-window" x1="70"  y1="354" x2="90"  y2="354"/>
              <line class="fp-window" x1="70"  y1="372" x2="90"  y2="372"/>
              <g class="fp-walls">
                <path d="M120,420 L120,344 Q190,278 260,344 L260,420"/>
              </g>
              <path class="fp-arch"   d="M132,420 L132,349 Q190,284 248,349 L248,420"/>
              <path class="fp-detail" d="M140,420 L140,353 Q190,289 240,353 L240,420" stroke-opacity="0.40"/>
              <path class="fp-detail" d="M184,280 L190,273 L196,280"/>
              <line class="fp-detail"  x1="190" y1="273" x2="190" y2="283" stroke-opacity="0.55"/>
              <line class="fp-detail" x1="128" y1="348" x2="128" y2="420" stroke-opacity="0.45"/>
              <line class="fp-detail" x1="252" y1="348" x2="252" y2="420" stroke-opacity="0.45"/>
              <rect class="fp-door"   x="162" y="358" width="56" height="62"/>
              <path class="fp-arch"   d="M162,360 Q190,341 218,360"/>
              <line class="fp-door"   x1="190" y1="362" x2="190" y2="420"/>
              <line class="fp-detail" x1="166" y1="380" x2="188" y2="380" stroke-opacity="0.35"/>
              <line class="fp-detail" x1="192" y1="380" x2="214" y2="380" stroke-opacity="0.35"/>
              <path class="fp-window" d="M282,375 L282,328 Q291,316 300,328 L300,375 Z"/>
              <line class="fp-window" x1="291" y1="330" x2="291" y2="375"/>
              <line class="fp-window" x1="282" y1="348" x2="300" y2="348"/>
              <rect class="fp-counter" x="93"  y="366" width="30" height="8"/>
              <rect class="fp-detail"  x="99"  y="356" width="16" height="10"/>
              <rect class="fp-detail"  x="103" y="352" width="6"  height="5"/>
              <line class="fp-detail"  x1="104" y1="352" x2="105" y2="350"/>
              <rect class="fp-seat"    x="93"  y="374" width="10" height="12"/>
              <rect class="fp-seat"    x="107" y="374" width="10" height="12"/>
              <rect class="fp-counter" x="254" y="363" width="38" height="8"/>
              <line class="fp-detail"  x1="257" y1="353" x2="287" y2="353"/>
              <line class="fp-detail"  x1="257" y1="344" x2="287" y2="344"/>
              <line class="fp-detail"  x1="261" y1="353" x2="261" y2="344" stroke-opacity="0.65"/>
              <line class="fp-detail"  x1="267" y1="353" x2="267" y2="343" stroke-opacity="0.65"/>
              <line class="fp-detail"  x1="273" y1="353" x2="273" y2="344" stroke-opacity="0.65"/>
              <line class="fp-detail"  x1="279" y1="353" x2="279" y2="344" stroke-opacity="0.65"/>
              <line class="fp-detail"  x1="285" y1="353" x2="285" y2="343" stroke-opacity="0.65"/>
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
              <line class="fp-label-bracket" x1="322" y1="278" x2="322" y2="417"/>
              <line class="fp-label-tick"    x1="322" y1="347" x2="330" y2="347"/>
              <text class="fp-floor-label fp-floor-label--right" text-anchor="start">
                <tspan x="334" y="343">Ground</tspan>
                <tspan x="334" dy="14">Caf&#233; &amp; Bar</tspan>
              </text>
            </g>

            <!-- Ground line extension to outdoor area -->
            <line class="fp-foundation" x1="318" y1="424" x2="578" y2="424"/>

            <!-- OUTDOOR AREA — garden to the right of the building -->
            <g class="fp-floor fp-outdoor" data-floor="terrace" data-url="<?php echo esc_attr( home_url( '/cafe' ) ); ?>"
               tabindex="0" role="button" aria-label="Outdoor Area — tap to explore">
              <rect class="fp-zone-hatch"  x="450" y="290" width="128" height="130" opacity="0.25"/>
              <!-- Dashed zone outline -->
              <line class="fp-detail-dash" x1="450" y1="290" x2="578" y2="290"/>
              <line class="fp-detail-dash" x1="450" y1="290" x2="450" y2="420"/>
              <line class="fp-detail-dash" x1="578" y1="290" x2="578" y2="420"/>
              <!-- Tree trunk -->
              <rect class="fp-outdoor-element" x="508" y="374" width="6" height="46" fill="rgba(0,0,0,0.72)"/>
              <!-- Tree crown (layered ellipses) -->
              <ellipse class="fp-outdoor-element" cx="504" cy="352" rx="15" ry="21" fill="rgba(0,0,0,0.06)"/>
              <ellipse class="fp-outdoor-element" cx="516" cy="347" rx="11" ry="16" fill="rgba(0,0,0,0.06)"/>
              <ellipse class="fp-outdoor-element" cx="510" cy="338" rx="19" ry="13" fill="rgba(0,0,0,0.08)"/>
              <!-- Lounge chair left -->
              <rect class="fp-outdoor-element" x="461" y="404" width="20" height="6"/>
              <rect class="fp-outdoor-element" x="461" y="396" width="4" height="9"/>
              <line class="fp-outdoor-element" x1="462" y1="410" x2="462" y2="417"/>
              <line class="fp-outdoor-element" x1="479" y1="410" x2="479" y2="417"/>
              <!-- Side table -->
              <rect class="fp-outdoor-element" x="486" y="406" width="13" height="4" fill="rgba(0,0,0,0.35)"/>
              <line class="fp-outdoor-element" x1="490" y1="410" x2="490" y2="417"/>
              <line class="fp-outdoor-element" x1="496" y1="410" x2="496" y2="417"/>
              <!-- Lounge chair right -->
              <rect class="fp-outdoor-element" x="538" y="404" width="20" height="6"/>
              <rect class="fp-outdoor-element" x="554" y="396" width="4" height="9"/>
              <line class="fp-outdoor-element" x1="540" y1="410" x2="540" y2="417"/>
              <line class="fp-outdoor-element" x1="557" y1="410" x2="557" y2="417"/>
              <!-- Hit zone and border -->
              <rect class="fp-zone-hit"    x="450" y="290" width="128" height="130"/>
              <rect class="fp-zone-border" x="450" y="290" width="128" height="130"/>
              <!-- Label floating above zone -->
              <text class="fp-level-label" text-anchor="middle" x="514" y="281">Outdoor Area</text>
              <line class="fp-dim-line" x1="514" y1="284" x2="514" y2="290" opacity="0.35"/>
            </g>

            <!-- Dimension annotations -->
            <line class="fp-dim-line" x1="28" y1="45"  x2="28" y2="420"/>
            <line class="fp-dim-line" x1="18" y1="45"  x2="54" y2="45"/>
            <line class="fp-dim-line" x1="18" y1="127" x2="54" y2="127"/>
            <line class="fp-dim-line" x1="18" y1="275" x2="54" y2="275"/>
            <line class="fp-dim-line" x1="18" y1="420" x2="54" y2="420"/>
            <text class="fp-level-label" x="16" y="88"  text-anchor="end">Level 2</text>
            <text class="fp-level-label" x="16" y="206" text-anchor="end">Level 1</text>
            <text class="fp-level-label" x="16" y="352" text-anchor="end">Ground</text>

          </svg>

          <!-- Desktop hover popup -->
          <div class="venue-floorplan__popup" role="tooltip" aria-hidden="true">
            <p class="venue-floorplan__popup-label"></p>
            <div class="venue-floorplan__popup-stats"></div>
            <p class="venue-floorplan__popup-use"></p>
          </div>

        </div><!-- /.venue-floorplan__svg-wrap -->

        <!-- Mobile mini-detail strip -->
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
          <h3 class="venue-floorplan__detail-title">60 standing &middot; 30 seated.</h3>
          <p class="venue-floorplan__detail-desc">Column-free floor with neutral gallery walls and adjustable track lighting on a dimmer. No fixed furniture &mdash; the space gets completely out of the way of your event. Built for art exhibitions, which makes it equally suited to product launches, brand activations, and seated dinners that need real architecture behind them. 200m&sup2; of uninterrupted floor in the heart of District 3.</p>
        </div>

        <!-- Capacity stats row — populated by JS on floor change -->
        <div class="venue-floorplan__specs"></div>

        <!-- Best for — populated by JS -->
        <div class="venue-floorplan__use-cases">
          <span class="venue-floorplan__use-label">Best for</span>
          <span class="venue-floorplan__use-text">Product Launches &middot; Exhibitions &middot; Brand Activations &middot; Seated Dinners</span>
        </div>

        <!-- Feature highlights — populated by JS -->
        <ul class="venue-floorplan__features">
          <li class="venue-floorplan__feature-item">Column-free open plan &mdash; no obstructions</li>
          <li class="venue-floorplan__feature-item">Adjustable track lighting with dimmers</li>
          <li class="venue-floorplan__feature-item">Gallery-grade hanging rail system</li>
          <li class="venue-floorplan__feature-item">In-house sound system + wireless mic</li>
          <li class="venue-floorplan__feature-item">Neutral gallery walls &mdash; works with any aesthetic</li>
        </ul>

        <!-- CTA link — href + text populated by JS -->
        <a class="venue-floorplan__detail-cta page-inner__cta-secondary" href="<?php echo esc_url( home_url( '/art-gallery' ) ); ?>">
          Explore Level 1 &mdash; The Gallery &rarr;
        </a>

      </div><!-- /.venue-floorplan__detail -->

    </div><!-- /.venue-floorplan__split -->

    <!-- Full Venue callout -->
    <div class="page-inner__container page-events__full-venue">
      <div class="page-events__full-venue-inner">
        <div>
          <p class="page-inner__info-label">Full Venue Hire</p>
          <p class="page-inner__info-value">Both floors plus the outdoor terrace under exclusive hire. Gallery, bar &amp; terrace &mdash; entirely yours. Up to 150+ guests standing.</p>
        </div>
        <a href="<?php echo esc_url( home_url( '/event-enquiry' ) ); ?>" class="page-inner__cta-secondary">Enquire about full venue &rarr;</a>
      </div>
    </div>

  </section><!-- /.page-events__spaces-fp -->

  <!-- ── 3. What We Host — Gallery Wall ───────────────────── -->
  <section class="page-events__wall" aria-labelledby="event-types-title">

    <p class="page-events__wall-eyebrow">What We Host</p>

    <div class="page-events__wall-layout">

      <!-- Frame grid: 5 cols on desktop, horizontal carousel on mobile -->
      <div class="page-events__wall-frames">

        <!-- Col 1, Row 1: large portrait -->
        <a href="<?php echo esc_url( home_url( '/events/product-launch' ) ); ?>" class="page-events__wall-frame" data-event="product-launch">
          <div class="page-events__wall-art"><div class="page-events__wall-mat"><div class="page-events__wall-artwork">
            <span class="page-events__wall-num" aria-hidden="true">01</span>
            <div class="page-events__wall-bar">
              <h3 class="page-events__wall-name">Product Launches</h3>
              <span class="page-events__wall-cta">Enquire &rarr;</span>
            </div>
          </div></div></div>
        </a>

        <!-- Col 2, Row 1: narrow portrait -->
        <a href="<?php echo esc_url( home_url( '/events/brand-activation' ) ); ?>" class="page-events__wall-frame" data-event="brand-activation">
          <div class="page-events__wall-art"><div class="page-events__wall-mat"><div class="page-events__wall-artwork">
            <span class="page-events__wall-num" aria-hidden="true">08</span>
            <div class="page-events__wall-bar">
              <h3 class="page-events__wall-name">Brand Activations</h3>
              <span class="page-events__wall-cta">Enquire &rarr;</span>
            </div>
          </div></div></div>
        </a>

        <!-- Col 4, Row 1: narrow portrait -->
        <a href="<?php echo esc_url( home_url( '/events/intimate-gatherings' ) ); ?>" class="page-events__wall-frame" data-event="intimate">
          <div class="page-events__wall-art"><div class="page-events__wall-mat"><div class="page-events__wall-artwork">
            <span class="page-events__wall-num" aria-hidden="true">04</span>
            <div class="page-events__wall-bar">
              <h3 class="page-events__wall-name">Intimate Gatherings</h3>
              <span class="page-events__wall-cta">Enquire &rarr;</span>
            </div>
          </div></div></div>
        </a>

        <!-- Col 5, Row 1: large portrait -->
        <a href="<?php echo esc_url( home_url( '/events/art-exhibitions' ) ); ?>" class="page-events__wall-frame" data-event="art-exhibition">
          <div class="page-events__wall-art"><div class="page-events__wall-mat"><div class="page-events__wall-artwork">
            <span class="page-events__wall-num" aria-hidden="true">07</span>
            <div class="page-events__wall-bar">
              <h3 class="page-events__wall-name">Art Exhibitions</h3>
              <span class="page-events__wall-cta">Enquire &rarr;</span>
            </div>
          </div></div></div>
        </a>

        <!-- Col 1, Row 2: landscape -->
        <a href="<?php echo esc_url( home_url( '/events/corporate-events' ) ); ?>" class="page-events__wall-frame" data-event="corporate">
          <div class="page-events__wall-art"><div class="page-events__wall-mat"><div class="page-events__wall-artwork">
            <span class="page-events__wall-num" aria-hidden="true">02</span>
            <div class="page-events__wall-bar">
              <h3 class="page-events__wall-name">Corporate Events</h3>
              <span class="page-events__wall-cta">Enquire &rarr;</span>
            </div>
          </div></div></div>
        </a>

        <!-- Col 2, Row 2: square -->
        <a href="<?php echo esc_url( home_url( '/events/birthday-celebration' ) ); ?>" class="page-events__wall-frame" data-event="birthday">
          <div class="page-events__wall-art"><div class="page-events__wall-mat"><div class="page-events__wall-artwork">
            <span class="page-events__wall-num" aria-hidden="true">03</span>
            <div class="page-events__wall-bar">
              <h3 class="page-events__wall-name">Birthday Celebrations</h3>
              <span class="page-events__wall-cta">Enquire &rarr;</span>
            </div>
          </div></div></div>
        </a>

        <!-- Col 4, Row 2: landscape -->
        <a href="<?php echo esc_url( home_url( '/events/intimate-weddings' ) ); ?>" class="page-events__wall-frame" data-event="weddings">
          <div class="page-events__wall-art"><div class="page-events__wall-mat"><div class="page-events__wall-artwork">
            <span class="page-events__wall-num" aria-hidden="true">05</span>
            <div class="page-events__wall-bar">
              <h3 class="page-events__wall-name">Weddings</h3>
              <span class="page-events__wall-cta">Enquire &rarr;</span>
            </div>
          </div></div></div>
        </a>

        <!-- Col 5, Row 2: medium portrait -->
        <a href="<?php echo esc_url( home_url( '/events/engagement-party' ) ); ?>" class="page-events__wall-frame" data-event="engagement">
          <div class="page-events__wall-art"><div class="page-events__wall-mat"><div class="page-events__wall-artwork">
            <span class="page-events__wall-num" aria-hidden="true">06</span>
            <div class="page-events__wall-bar">
              <h3 class="page-events__wall-name">Engagement Parties</h3>
              <span class="page-events__wall-cta">Enquire &rarr;</span>
            </div>
          </div></div></div>
        </a>

      </div><!-- /.page-events__wall-frames -->

      <!-- Mobile pagination nav (hidden on desktop via CSS) -->
      <nav class="page-events__wall-nav" aria-label="Event types">
        <button class="page-events__wall-prev" aria-label="Previous">&#8249;</button>
        <div class="page-events__wall-dots" role="list"></div>
        <button class="page-events__wall-next" aria-label="Next">&#8250;</button>
      </nav>

      <!-- Hero copy: absolute center on desktop, below nav on mobile -->
      <div class="page-events__wall-copy">
        <h2 class="page-events__wall-title" id="event-types-title">Eight types<br>of event.</h2>
        <p class="page-events__wall-sub">One venue hire in District 3, Saigon.</p>
        <a href="<?php echo esc_url( home_url( '/event-enquiry' ) ); ?>" class="page-inner__cta-primary">Start Your Enquiry</a>
      </div>

    </div><!-- /.page-events__wall-layout -->

  </section>

  <!-- ── 4. Catering & Partners ─────────────────── -->
  <section class="page-inner__section" aria-labelledby="catering-title">
    <div class="page-inner__container">
      <div class="page-inner__split">

        <div class="page-events__catering-text">
          <p class="page-inner__section-head">Catering &amp; Partners</p>
          <h2 class="page-inner__section-title" id="catering-title">Food, bar, florals,<br>A/V &mdash; sourced for you.</h2>
          <div class="page-inner__section-body">
            <p>We work with a curated network of HCMC caterers and event vendors. They arrive with their own team, their own equipment, and a clear brief. You can use our partners or bring your own &mdash; either works. Bar service runs from our in-house cocktail menu and can be packaged by consumption or by package rate.</p>
          </div>
          <ul class="page-inner__feature-list">
            <li class="page-inner__feature-item">Catering partners &mdash; cana&eacute;p&eacute;s to full sit-down menus</li>
            <li class="page-inner__feature-item">Bar packages drawn from our cocktail program</li>
            <li class="page-inner__feature-item">Floral and event styling coordination</li>
            <li class="page-inner__feature-item">A/V and sound system setup</li>
            <li class="page-inner__feature-item">Photography referrals from our network</li>
          </ul>
        </div>

        <a class="tempo-frame tempo-frame--placeholder page-events__catering-img" href="<?php echo esc_url( home_url( '/event-enquiry' ) ); ?>" aria-label="Catering and partners at TEMPO House — click to enquire">
          <div class="tempo-frame__mat">
            <div class="tempo-frame__artwork">
              <span class="tempo-frame__label">Catering &amp; Partners &mdash; Image Coming Soon</span>
            </div>
          </div>
        </a>

      </div>
    </div>
  </section>

  <!-- ── 5. FAQ (replaces old Process section) ──────────────────────── -->
  <section class="page-inner__section page-inner__section--alt" aria-labelledby="faq-title">
    <div class="page-inner__container">

      <div class="page-inner__section-head">
        <p>Questions</p>
      </div>
      <h2 class="page-inner__section-title" id="faq-title">Common questions,<br>answered.</h2>

      <ul class="page-events__faq-list" role="list">

        <li class="page-events__faq-item">
          <span class="page-events__faq-badge" aria-hidden="true">01</span>
          <h3 class="page-events__faq-q">What does it cost to hire TEMPO House?</h3>
          <p class="page-events__faq-a">Hire rates depend on the space, day, and duration. We don&rsquo;t publish fixed pricing &mdash; every event is different. Fill in the enquiry form and we&rsquo;ll respond with a detailed proposal within 24 hours.</p>
        </li>

        <li class="page-events__faq-item">
          <span class="page-events__faq-badge" aria-hidden="true">02</span>
          <h3 class="page-events__faq-q">How many guests can the venue hold?</h3>
          <p class="page-events__faq-a">Up to 80 standing on Level 1, 60 seated on the Ground Floor. Full venue hire accommodates 150+ standing. We also host events as intimate as 20 guests in the Creators space.</p>
        </li>

        <li class="page-events__faq-item">
          <span class="page-events__faq-badge" aria-hidden="true">03</span>
          <h3 class="page-events__faq-q">What&rsquo;s included in the venue hire?</h3>
          <p class="page-events__faq-a">Access to the space, in-house lighting and sound, bar service from our cocktail program, and a dedicated coordinator on-site from setup through to close. Tables and basic furnishings included.</p>
        </li>

      </ul>

      <div class="page-events__faq-footer">
        <p class="page-events__faq-footer-note">More questions? We&rsquo;ve answered everything on the FAQ page.</p>
        <a href="<?php echo esc_url( home_url( '/faq' ) ); ?>" class="page-inner__cta-secondary">Full FAQ &rarr;</a>
      </div>

    </div>
  </section>

  <!-- ── 6. Enquiry CTA ──────────────────────────── -->
  <section class="page-inner__section" aria-labelledby="enquiry-cta-title">
    <div class="page-inner__container">
      <div class="page-events__enquiry-cta">
        <h2 class="page-inner__section-title" id="enquiry-cta-title">Ready to talk about your event?</h2>
        <p class="page-inner__section-body">Fill in the enquiry form with your date, guest count, and type of event. We read every enquiry before responding &mdash; no automated replies, no sales calls.</p>
        <div class="page-inner__cta-row">
          <a href="<?php echo esc_url( home_url( '/event-enquiry' ) ); ?>" class="page-inner__cta-primary">Send an Enquiry</a>
          <a href="mailto:events@tempohouse.com.vn" class="page-inner__cta-secondary">events@tempohouse.com.vn</a>
        </div>
      </div>
    </div>
  </section>

</main>

<?php get_footer(); ?>
