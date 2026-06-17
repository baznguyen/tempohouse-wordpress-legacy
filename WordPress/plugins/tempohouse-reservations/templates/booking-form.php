<?php defined( 'ABSPATH' ) || exit; ?>

<div class="thr-booking-wrap" id="thr-booking-wrap">

  <!-- Step 1: Date, Party size, Occasion -->
  <div class="thr-step" id="thr-step-1" data-step="1">
    <h2 class="thr-step-title"><?= esc_html( $atts['title'] ?? 'Reserve a table' ); ?></h2>

    <div class="thr-field-group">
      <label class="thr-label" for="thr-date" data-i18n="lblDate">Date</label>
      <input type="date" id="thr-date" class="thr-input" required
             min="" max="" autocomplete="off">
    </div>

    <div class="thr-field-row">
      <div class="thr-field-group">
        <label class="thr-label" for="thr-party-size" data-i18n="lblGuests">Guests</label>
        <select id="thr-party-size" class="thr-input thr-select">
          <!-- Populated by JS from config.partySizeMin/Max -->
        </select>
      </div>

      <div class="thr-field-group">
        <label class="thr-label" for="thr-occasion" data-i18n="lblOccasion">Occasion</label>
        <select id="thr-occasion" class="thr-input thr-select">
          <!-- Populated by JS from config.occasionTypes -->
        </select>
      </div>
    </div>

    <div class="thr-field-group">
      <label class="thr-label" data-i18n="lblAvailableTimes">Available times</label>
      <div id="thr-time-slots" class="thr-time-slots">
        <p class="thr-hint" data-i18n="hintSelectDate">Select a date to see available times.</p>
      </div>
    </div>

    <button type="button" id="thr-next-1" class="thr-btn" disabled data-i18n="btnContinue">Continue →</button>
  </div>

  <!-- Step 2: Guest details -->
  <div class="thr-step thr-step--hidden" id="thr-step-2" data-step="2">
    <button type="button" class="thr-back-btn" data-target="1" data-i18n="btnBack">← Back</button>
    <h2 class="thr-step-title" data-i18n="ttlYourDetails">Your details</h2>

    <div class="thr-field-group">
      <label class="thr-label" for="thr-name">
        <span data-i18n="lblFullName">Full name</span> <span class="thr-required">*</span>
      </label>
      <input type="text" id="thr-name" class="thr-input" placeholder="Your name"
             data-ph-i18n="phFullName" autocomplete="name" required>
    </div>

    <div class="thr-field-group">
      <label class="thr-label" for="thr-email">
        <span data-i18n="lblEmail">Email</span> <span class="thr-required">*</span>
      </label>
      <input type="email" id="thr-email" class="thr-input" placeholder="your@email.com"
             data-ph-i18n="phEmail" autocomplete="email" required>
    </div>

    <div class="thr-field-row">
      <div class="thr-field-group">
        <label class="thr-label" for="thr-phone" data-i18n="lblPhone">Phone</label>
        <input type="tel" id="thr-phone" class="thr-input" placeholder="+84 xxx xxx xxx"
               data-ph-i18n="phPhone" autocomplete="tel">
      </div>
      <div class="thr-field-group">
        <label class="thr-label" for="thr-zalo" data-i18n="lblZalo">Zalo (optional)</label>
        <input type="tel" id="thr-zalo" class="thr-input" placeholder="+84 xxx xxx xxx"
               data-ph-i18n="phZalo" autocomplete="tel">
      </div>
    </div>

    <div class="thr-field-group">
      <label class="thr-label" for="thr-notes" data-i18n="lblSpecialRequests">Special requests</label>
      <textarea id="thr-notes" class="thr-input thr-textarea" placeholder="Allergies, high chair, birthday cake…"
                data-ph-i18n="phSpecialRequests" rows="3"></textarea>
    </div>

    <div class="thr-field-group">
      <label class="thr-label" data-i18n="lblLanguage">Language preference</label>
      <div class="thr-lang-toggle">
        <label><input type="radio" name="thr-lang" value="en"> English</label>
        <label><input type="radio" name="thr-lang" value="vi"> Tiếng Việt</label>
      </div>
    </div>

    <div class="thr-summary-bar">
      <span id="thr-summary-date"></span>
      <span class="thr-divider">·</span>
      <span id="thr-summary-time"></span>
      <span class="thr-divider">·</span>
      <span id="thr-summary-size"></span>
      <span class="thr-divider">·</span>
      <span id="thr-summary-occasion"></span>
    </div>

    <div id="thr-error" class="thr-error" style="display:none;"></div>

    <button type="button" id="thr-submit" class="thr-btn" data-i18n="btnConfirmReservation">Confirm reservation</button>

    <p class="thr-policy" id="thr-policy-text"></p>
  </div>

  <!-- Step 3: Confirmation -->
  <div class="thr-step thr-step--hidden" id="thr-step-3" data-step="3">
    <div class="thr-confirm-icon">✓</div>
    <h2 class="thr-step-title" data-i18n="ttlConfirmed">You're confirmed</h2>
    <p class="thr-confirm-msg" data-i18n="msgConfirmed">Your reservation has been received. Check your email for full details.</p>

    <div class="thr-ref-box">
      <span class="thr-ref-label" data-i18n="lblRefCode">Reference code</span>
      <span class="thr-ref-code" id="thr-ref-code"></span>
    </div>

    <div class="thr-confirm-details" id="thr-confirm-details"></div>

    <p class="thr-confirm-note" data-i18n="noteRefCode">Keep your reference code — you may need it to amend or cancel.</p>

    <div id="thr-calendar-links" style="display:none;margin-top:0;">
      <a id="thr-gcal-link" href="#" target="_blank" class="thr-btn thr-btn--outline" data-i18n="btnAddGcal">+ Add to Google Calendar</a>
      <a id="thr-ics-link" href="#" download="tempo-house-reservation.ics" class="thr-btn thr-btn--outline" data-i18n="btnDownloadIcs">Download .ics</a>
    </div>
    <a href="https://maps.google.com/?q=TEMPO+House+Ho+Chi+Minh+City" target="_blank"
       class="thr-btn thr-btn--outline" data-i18n="btnDirections">Get directions</a>
  </div>

</div>
