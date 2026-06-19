<?php
/**
 * Booking widget — standalone page template.
 * Accessed via page slug: reserve
 */
defined( 'ABSPATH' ) || exit;

$venue_name       = THR_Settings::get( 'venue_name', 'TEMPO House' );
$api_base         = rtrim( rest_url( THR_REST_NS ), '/' );
$home_url         = home_url( '/' );
$venue_phone      = THR_Settings::get( 'venue_phone', '' );
$venue_address    = THR_Settings::get( 'venue_address', '' );
$private_room_min = (int) THR_Settings::get( 'private_room_min_party', 12 );
$private_room_max = (int) THR_Settings::get( 'private_room_max_party', 15 );
$occasion_types   = THR_Settings::occasion_types();
$seating_sections = THR_Settings::seating_sections();
$referral_sources = THR_Settings::referral_sources();
$hero_landing     = THR_Settings::get( 'hero_landing_url', '' );
$hero_lunch       = THR_Settings::get( 'hero_lunch_url', '' );
$hero_dinner      = THR_Settings::get( 'hero_dinner_url', '' );
?>
<!DOCTYPE html>
<html lang="vi" <?php echo get_language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
  <meta name="theme-color" content="#F7F3EE">
  <title>Reserve a table — <?php echo esc_html( $venue_name ); ?></title>
  <?php wp_head(); ?>
</head>
<body class="thr-booking-page">

<div id="thr-booking-root" class="thr-booking-root">

  <!-- ── Fixed header ────────────────────────────────────────────────────── -->
  <header class="thr-widget-header" id="thr-header">
    <button class="thr-header__back" id="thr-header-back" type="button" aria-label="Go back" hidden>
      <svg width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
        <path d="M13 4l-6 6 6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </button>
    <h1 class="thr-header__title" id="thr-header-title">Reservation</h1>
    <span class="thr-header__step" id="thr-header-step" aria-hidden="true" hidden></span>
  </header>

  <!-- ── Scrollable content ──────────────────────────────────────────────── -->
  <main class="thr-widget-content" id="thr-widget-content">

    <!-- ═══ SCREEN: Landing ═══════════════════════════════════════════════ -->
    <div class="thr-widget-step is-active" id="step-landing">

      <div class="thr-landing-hero" id="landing-hero"
           role="img" aria-label="<?php echo esc_attr( $venue_name ); ?> dining room"
           style="<?php echo $hero_landing ? 'background-image:url(' . esc_attr( $hero_landing ) . ')' : ''; ?>">
      </div>

      <div class="thr-landing-cards">

        <!-- Modify / Cancel card -->
        <div class="thr-landing-card" id="card-modify">
          <h2 class="thr-landing-card__title">Modify/Cancel Reservation</h2>
          <p class="thr-landing-card__body">Need to make changes? Modify or cancel your reservation easily, subject to availability.</p>

          <!-- Inline lookup — hidden until "Modify/Cancel" is tapped -->
          <div class="thr-lookup" id="lookup-wrap" aria-live="polite">
            <div class="thr-lookup__row" style="margin-top:0">
              <div class="thr-field thr-lookup__input" style="margin-bottom:0">
                <input class="thr-field__input" type="email" id="lookup-email"
                       placeholder="Your email address" autocomplete="email"
                       aria-label="Email address for reservation lookup">
              </div>
              <button class="thr-btn thr-btn--secondary thr-btn--sm" id="lookup-btn"
                      type="button" aria-label="Look up reservation" style="flex-shrink:0;width:auto;padding:0 18px">
                →
              </button>
            </div>
            <div id="lookup-result"></div>
          </div>

          <button class="thr-btn thr-btn--secondary" id="btn-modify" type="button">
            Modify / Cancel
          </button>
        </div>

        <!-- Book card -->
        <div class="thr-landing-card" id="card-book">
          <h2 class="thr-landing-card__title">Book A New Reservation</h2>
          <p class="thr-landing-card__body">Secure your table by choosing your preferred date, time, and number of guests.</p>
          <button class="thr-btn thr-btn--primary" id="btn-book" type="button">
            Get your seat now
          </button>
        </div>

      </div>
    </div><!-- /step-landing -->


    <!-- ═══ SCREEN 1/3: Booking params ═══════════════════════════════════ -->
    <div class="thr-widget-step thr-widget__step--hidden" id="step-booking">
      <div class="thr-widget__inner">

        <!-- Number of guests -->
        <div class="thr-section-head thr-section-head--step">
          <span class="thr-section-head__label">Number of guests</span>
        </div>
        <div class="thr-stepper" role="group" aria-label="Number of guests">
          <button class="thr-stepper__btn" type="button" id="guests-dec" aria-label="Fewer guests">−</button>
          <span class="thr-stepper__val" id="guests-val" aria-live="polite" aria-atomic="true">2</span>
          <button class="thr-stepper__btn" type="button" id="guests-inc" aria-label="More guests">+</button>
        </div>

        <!-- Private room — hidden until threshold is reached -->
        <div class="thr-private-room thr-private-room--hidden" id="private-room-wrap">
          <label class="thr-checkbox-row" for="private-room-chk">
            <input class="thr-checkbox" type="checkbox" id="private-room-chk" name="private_room">
            <span class="thr-checkbox-label">
              Book Private Room if Available
              <span class="thr-checkbox-label__sub">
                For parties of <?php echo esc_html( $private_room_min ); ?>–<?php echo esc_html( $private_room_max ); ?> guests
              </span>
            </span>
          </label>
        </div>

        <hr class="thr-section-divider">

        <!-- Date rail -->
        <div class="thr-section-head thr-section-head--step">
          <span class="thr-section-head__label">Select Date</span>
        </div>
        <!-- Month selector — JS renders chips based on booking_advance_max -->
        <div id="month-rail" class="thr-month-rail" role="listbox" aria-label="Select month"></div>
        <div class="thr-date-rail" id="date-rail" role="listbox" aria-label="Select a date">
          <!-- JS renders date chips here -->
          <div class="thr-skeleton" style="height:72px;width:100%;border-radius:26px;flex-shrink:0"></div>
        </div>

        <hr class="thr-section-divider">

        <!-- Session selector — JS renders chips from plugin settings -->
        <div class="thr-section-head thr-section-head--step">
          <span class="thr-section-head__label">Dining Session</span>
        </div>
        <div id="session-rail" class="thr-session-rail" role="listbox" aria-label="Dining session">
          <!-- Skeleton placeholder until JS loads config -->
          <div class="thr-skeleton" style="height:52px;width:110px;border-radius:9999px;flex-shrink:0"></div>
          <div class="thr-skeleton" style="height:52px;width:80px;border-radius:9999px;flex-shrink:0"></div>
          <div class="thr-skeleton" style="height:52px;width:110px;border-radius:9999px;flex-shrink:0"></div>
          <div class="thr-skeleton" style="height:52px;width:80px;border-radius:9999px;flex-shrink:0"></div>
        </div>

        <!-- Time slots -->
        <div class="thr-time-grid" id="time-grid" style="margin-top:16px" role="listbox" aria-label="Select a time">
          <span class="thr-time-empty">Select a date to see available times.</span>
        </div>

        <!-- Legend -->
        <div class="thr-time-legend" id="time-legend">
          <span class="thr-time-legend__item">
            <span class="thr-time-legend__dot thr-time-legend__dot--selected"></span> Selected
          </span>
          <span class="thr-time-legend__item">
            <span class="thr-time-legend__dot thr-time-legend__dot--unavailable"></span> Unavailable
          </span>
          <span class="thr-time-legend__item">
            <span class="thr-time-legend__dot thr-time-legend__dot--popular"></span> Popular
          </span>
        </div>

      </div>
    </div><!-- /step-booking -->


    <!-- ═══ SCREEN 2/4: Preferences ══════════════════════════════════════ -->
    <div class="thr-widget-step thr-widget__step--hidden" id="step-preferences">
      <div class="thr-widget__inner">

        <!-- Seating area preference — chips from plugin settings -->
        <div class="thr-section-head thr-section-head--step">
          <span class="thr-section-head__label">Where would you like to sit?</span>
        </div>
        <div id="section-rail" class="thr-pref-rail" role="listbox" aria-label="Seating area">
          <?php foreach ( $seating_sections as $slug => $label ) : ?>
          <button class="thr-pref-chip<?php echo $slug === 'any' ? ' thr-pref-chip--active' : ''; ?>"
                  type="button" data-section="<?php echo esc_attr( $slug ); ?>"
                  aria-pressed="<?php echo $slug === 'any' ? 'true' : 'false'; ?>">
            <?php echo esc_html( $label ); ?>
          </button>
          <?php endforeach; ?>
        </div>
        <p class="thr-field__hint" style="margin-top:4px">A preference, not a guarantee — we'll do our best.</p>

        <hr class="thr-section-divider">

        <!-- Special occasion chips -->
        <div class="thr-section-head thr-section-head--step">
          <span class="thr-section-head__label">Any special occasion?</span>
        </div>
        <div id="occasion-grid" class="thr-occasion-grid" role="listbox" aria-label="Special occasion">
          <!-- JS renders from config so emoji map is in one place -->
          <div class="thr-skeleton" style="height:52px;border-radius:12px"></div>
          <div class="thr-skeleton" style="height:52px;border-radius:12px"></div>
          <div class="thr-skeleton" style="height:52px;border-radius:12px"></div>
          <div class="thr-skeleton" style="height:52px;border-radius:12px"></div>
        </div>

        <button class="thr-pref-skip" id="pref-skip-btn" type="button">Skip — no preferences</button>

      </div>
    </div><!-- /step-preferences -->


    <!-- ═══ SCREEN 3/4: Contact details ══════════════════════════════════ -->
    <div class="thr-widget-step thr-widget__step--hidden" id="step-contact">
      <div class="thr-widget__inner">

        <div class="thr-section-head thr-section-head--step">
          <span class="thr-section-head__label">Contact Information</span>
        </div>
        <div class="thr-fields">

          <div class="thr-field" id="field-name">
            <label class="thr-field__label" for="input-name">
              Full Name <span class="thr-field__required">*</span>
            </label>
            <input class="thr-field__input" type="text" id="input-name" name="diner_name"
                   autocomplete="name" placeholder="Your full name">
            <span class="thr-field__error" id="error-name" hidden>Please enter your full name.</span>
          </div>

          <div class="thr-field" id="field-email">
            <label class="thr-field__label" for="input-email">
              Email <span class="thr-field__required">*</span>
            </label>
            <input class="thr-field__input" type="email" id="input-email" name="diner_email"
                   autocomplete="email" placeholder="your@email.com">
            <span class="thr-field__error" id="error-email" hidden>Please enter a valid email address.</span>
          </div>

          <div class="thr-field">
            <label class="thr-field__label" for="input-phone">Phone Number</label>
            <div class="thr-phone-wrap">
              <button class="thr-phone-prefix" type="button" aria-label="Country: Vietnam +84" id="phone-prefix-btn">
                <span class="thr-phone-prefix__flag">🇻🇳</span>
                <span class="thr-phone-prefix__code">+84</span>
              </button>
              <input class="thr-field__input thr-phone-input" type="tel" id="input-phone"
                     name="diner_phone" autocomplete="tel-national"
                     placeholder="_ _ _ — _ _ _ — _ _ _">
            </div>
            <!-- Zalo opt-in removed until ZNS API integration is live -->
          </div>

        </div>

        <hr class="thr-section-divider">

        <div class="thr-section-head thr-section-head--step">
          <span class="thr-section-head__label">A little more info</span>
        </div>
        <div class="thr-fields">

          <div class="thr-field">
            <label class="thr-field__label" for="input-dietary">Dietary Requirements</label>
            <div class="thr-select-wrap">
              <select class="thr-field__select" id="input-dietary" name="dietary_notes">
                <option value="">None</option>
                <option value="Vegetarian">Vegetarian</option>
                <option value="Vegan">Vegan</option>
                <option value="Gluten-free">Gluten-free</option>
                <option value="Halal">Halal</option>
                <option value="Nut allergy">Nut allergy</option>
                <option value="Other (see notes)">Other — add in notes below</option>
              </select>
            </div>
          </div>

          <div class="thr-field">
            <label class="thr-field__label" for="input-notes">Special Requests</label>
            <textarea class="thr-field__input thr-field__textarea" id="input-notes" name="notes_diner"
                      placeholder="Cake message, high chair, accessibility needs, or anything else…" rows="3"></textarea>
          </div>

          <div class="thr-field">
            <label class="thr-field__label" for="input-referral">How did you hear about us?</label>
            <div class="thr-select-wrap">
              <select class="thr-field__select" id="input-referral" name="referral_source">
                <option value="">Prefer not to say</option>
                <?php foreach ( $referral_sources as $slug => $label ) : ?>
                <option value="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $label ); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

        </div>

      </div>
    </div><!-- /step-contact -->


    <!-- ═══ SCREEN 4/4: Review ════════════════════════════════════════════ -->
    <div class="thr-widget-step thr-widget__step--hidden" id="step-review">
      <div class="thr-widget__inner">

        <div class="thr-section-head thr-section-head--step">
          <span class="thr-section-head__label">Your Reservation</span>
        </div>
        <div class="thr-review-rows">
          <div class="thr-review-row">
            <span class="thr-review-row__icon" aria-hidden="true">
              <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><rect x="2" y="4" width="14" height="12" rx="1.5" stroke="currentColor" stroke-width="1.4"/><path d="M2 7h14M6 2v3M12 2v3" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
            </span>
            <span class="thr-review-row__label">Date</span>
            <span class="thr-review-row__value" id="review-date"></span>
          </div>
          <div class="thr-review-row">
            <span class="thr-review-row__icon" aria-hidden="true">
              <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><circle cx="9" cy="9" r="7" stroke="currentColor" stroke-width="1.4"/><path d="M9 5v4l2.5 1.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            <span class="thr-review-row__label">Time</span>
            <span class="thr-review-row__value" id="review-time"></span>
          </div>
          <div class="thr-review-row">
            <span class="thr-review-row__icon" aria-hidden="true">
              <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><circle cx="9" cy="6" r="3" stroke="currentColor" stroke-width="1.4"/><path d="M3 16c0-3.314 2.686-6 6-6s6 2.686 6 6" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
            </span>
            <span class="thr-review-row__label">Guests</span>
            <span class="thr-review-row__value" id="review-guests"></span>
          </div>
        </div>

        <hr class="thr-section-divider">

        <div class="thr-section-head thr-section-head--title">
          <span class="thr-section-head__label">Contact Details</span>
        </div>
        <div class="thr-review-rows">
          <div class="thr-review-row">
            <span class="thr-review-row__label">Name</span>
            <span class="thr-review-row__value" id="review-name"></span>
          </div>
          <div class="thr-review-row">
            <span class="thr-review-row__label">Email</span>
            <span class="thr-review-row__value" id="review-email" style="font-size:0.82rem"></span>
          </div>
          <div class="thr-review-row" id="review-phone-row">
            <span class="thr-review-row__label">Phone</span>
            <span class="thr-review-row__value" id="review-phone"></span>
          </div>
        </div>

        <div id="review-optional-section" hidden>
          <hr class="thr-section-divider">
          <div class="thr-section-head thr-section-head--step">
            <span class="thr-section-head__label">Preferences</span>
          </div>
          <div class="thr-review-rows">
            <div class="thr-review-row" id="review-occasion-row" hidden>
              <span class="thr-review-row__icon" aria-hidden="true">🎉</span>
              <span class="thr-review-row__label">Occasion</span>
              <span class="thr-review-row__value" id="review-occasion"></span>
            </div>
            <div class="thr-review-row" id="review-area-row" hidden>
              <span class="thr-review-row__icon" aria-hidden="true">🪑</span>
              <span class="thr-review-row__label">Seating</span>
              <span class="thr-review-row__value" id="review-area"></span>
            </div>
            <div class="thr-review-row" id="review-dietary-row" hidden>
              <span class="thr-review-row__icon" aria-hidden="true">🌿</span>
              <span class="thr-review-row__label">Dietary</span>
              <span class="thr-review-row__value" id="review-dietary"></span>
            </div>
          </div>
        </div>

        <div id="review-notes-wrap" hidden>
          <hr class="thr-section-divider">
          <p class="thr-field__hint" style="margin-bottom:6px;font-weight:600;color:var(--thr-text-secondary)">Special Note</p>
          <p class="thr-review-note" id="review-notes"></p>
        </div>

        <hr class="thr-section-divider">

        <div class="thr-error" id="confirm-error" hidden></div>

        <button class="thr-btn thr-btn--primary" id="confirm-btn" type="button" style="margin-top:4px">
          Confirm
        </button>

      </div>
    </div><!-- /step-review -->


    <!-- ═══ SCREEN: Confirmation ══════════════════════════════════════════ -->
    <div class="thr-widget-step thr-widget__step--hidden" id="step-confirm">

      <div class="thr-confirm-hero" id="confirm-hero" role="img" aria-label="<?php echo esc_attr( $venue_name ); ?>"></div>

      <div class="thr-confirm-body thr-widget__inner">

        <div class="thr-countdown" id="confirm-countdown" aria-live="polite">Soon</div>

        <h2 class="thr-confirm__heading">Your table is reserved</h2>
        <p class="thr-confirm__sub" id="confirm-sub">See you soon</p>

        <div class="thr-confirm__grid">
          <div class="thr-confirm__item">
            <span class="thr-confirm__item-label">Date</span>
            <span class="thr-confirm__item-value" id="confirm-date">—</span>
          </div>
          <div class="thr-confirm__item">
            <span class="thr-confirm__item-label">Time</span>
            <span class="thr-confirm__item-value" id="confirm-time">—</span>
          </div>
          <div class="thr-confirm__item">
            <span class="thr-confirm__item-label">Guests</span>
            <span class="thr-confirm__item-value" id="confirm-guests">—</span>
          </div>
          <div class="thr-confirm__item">
            <span class="thr-confirm__item-label">Reference</span>
            <span class="thr-confirm__item-value">
              <span class="thr-confirm__ref" id="confirm-ref">—</span>
              <button class="thr-copy-btn" id="copy-ref-btn" type="button" aria-label="Copy reference number">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                  <rect x="5" y="5" width="8" height="9" rx="1.5" stroke="currentColor" stroke-width="1.4"/>
                  <path d="M3 11V3.5A1.5 1.5 0 0 1 4.5 2H11" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                </svg>
              </button>
            </span>
          </div>
        </div>

        <p class="thr-field__hint" style="margin-bottom:0;padding-bottom:16px;border-bottom:1px solid var(--thr-border)">
          Confirmation sent to your email.
        </p>

        <div class="thr-confirm-venue">
          <span class="thr-confirm-venue__name"><?php echo esc_html( $venue_name ); ?></span>
          <div class="thr-confirm-venue__icons">
            <button class="thr-icon-btn" id="confirm-cal-btn" type="button" aria-label="Add to calendar">
              <svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
                <rect x="2" y="4" width="14" height="12" rx="2" stroke="currentColor" stroke-width="1.4"/>
                <path d="M2 7h14M6 2v4M12 2v4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
              </svg>
            </button>
            <button class="thr-icon-btn" id="confirm-call-btn" type="button" aria-label="Call venue">
              <svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
                <path d="M3 3h3l1.5 4L6 8.5c1 2 3 4 5 5l1.5-1.5L16 13.5V17c-8 0-14-6-13-14z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
              </svg>
            </button>
            <button class="thr-icon-btn" id="confirm-pin-btn" type="button" aria-label="Get directions">
              <svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
                <path d="M9 2C6.24 2 4 4.24 4 7c0 3.75 5 9 5 9s5-5.25 5-9c0-2.76-2.24-5-5-5z" stroke="currentColor" stroke-width="1.4"/>
                <circle cx="9" cy="7" r="1.5" stroke="currentColor" stroke-width="1.4"/>
              </svg>
            </button>
          </div>
        </div>

        <div class="thr-btn-row">
          <button class="thr-btn thr-btn--ghost" id="confirm-modify-btn" type="button">Modify/Cancel</button>
          <button class="thr-btn thr-btn--primary" id="confirm-home-btn" type="button">Back to Home</button>
        </div>

      </div>
    </div><!-- /step-confirm -->

  </main><!-- /thr-widget-content -->

  <!-- ── Sticky footer CTA ────────────────────────────────────────────────── -->
  <footer class="thr-widget-footer thr-widget-footer--hidden" id="thr-widget-footer">
    <button class="thr-btn thr-btn--primary" id="footer-cta" type="button" disabled>
      Add Contact Details
    </button>
  </footer>

</div><!-- /thr-booking-root -->

<!-- Localised data for booking-widget.js -->
<script>
window.thrBooking = {
  apiUrl:        '<?php echo esc_js( $api_base ); ?>',
  nonce:         '<?php echo esc_js( wp_create_nonce( 'wp_rest' ) ); ?>',
  venuePhone:    '<?php echo esc_js( $venue_phone ?? '' ); ?>',
  venueAddress:  '<?php echo esc_js( $venue_address ?? '' ); ?>',
  homeUrl:       '<?php echo esc_js( $home_url ?? home_url('/') ); ?>',
  defaultLang:   '<?php echo esc_js( THR_Settings::get( 'booking_default_lang', 'vi' ) ); ?>',
  heroImages: {
    lunch:   '<?php echo esc_js( $hero_lunch   ?? '' ); ?>',
    dinner:  '<?php echo esc_js( $hero_dinner  ?? '' ); ?>',
    default: '<?php echo esc_js( $hero_landing ?? '' ); ?>',
  },
};
</script>

<?php wp_footer(); ?>
</body>
</html>
