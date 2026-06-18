<?php defined( 'ABSPATH' ) || exit; ?>

<div class="thr-booking-wrap" id="thr-events-enquiry-wrap">

  <div id="thr-eq-form-wrap">
    <h2 class="thr-step-title">Private Events &amp; Venue Hire</h2>
    <p style="margin-bottom:24px;color:rgba(247,243,238,0.65);">Tell us about your event and we'll be in touch within 24 hours to discuss availability and packages.</p>

    <!-- Contact details -->
    <div class="thr-field-group">
      <label class="thr-label" for="eq-contact-name">Full name <span class="thr-required">*</span></label>
      <input type="text" id="eq-contact-name" class="thr-input" placeholder="Your name" autocomplete="name" required>
    </div>

    <div class="thr-field-row">
      <div class="thr-field-group">
        <label class="thr-label" for="eq-contact-email">Email <span class="thr-required">*</span></label>
        <input type="email" id="eq-contact-email" class="thr-input" placeholder="your@email.com" autocomplete="email" required>
      </div>
      <div class="thr-field-group">
        <label class="thr-label" for="eq-contact-phone">Phone</label>
        <input type="tel" id="eq-contact-phone" class="thr-input" placeholder="+84 xxx xxx xxx" autocomplete="tel">
      </div>
    </div>

    <div class="thr-field-row">
      <div class="thr-field-group">
        <label class="thr-label" for="eq-contact-zalo">Zalo</label>
        <input type="tel" id="eq-contact-zalo" class="thr-input" placeholder="+84 xxx xxx xxx">
      </div>
      <div class="thr-field-group">
        <label class="thr-label" for="eq-company-name">Company (optional)</label>
        <input type="text" id="eq-company-name" class="thr-input" placeholder="Company name" autocomplete="organization">
      </div>
    </div>

    <!-- Event details -->
    <div class="thr-field-row">
      <div class="thr-field-group">
        <label class="thr-label" for="eq-event-type">Event type <span class="thr-required">*</span></label>
        <select id="eq-event-type" class="thr-input thr-select">
          <option value="corporate">Corporate dinner</option>
          <option value="product_launch">Product launch</option>
          <option value="brand_activation">Brand activation</option>
          <option value="birthday">Birthday celebration</option>
          <option value="anniversary">Anniversary</option>
          <option value="team_event">Team event</option>
          <option value="other">Other</option>
        </select>
      </div>
      <div class="thr-field-group">
        <label class="thr-label" for="eq-preferred-date">Preferred date</label>
        <input type="date" id="eq-preferred-date" class="thr-input" autocomplete="off">
      </div>
    </div>

    <div class="thr-field-row">
      <div class="thr-field-group">
        <label class="thr-label" for="eq-guest-count">Expected guests <span class="thr-required">*</span></label>
        <input type="number" id="eq-guest-count" class="thr-input" value="50" min="1" max="500">
      </div>
      <div class="thr-field-group">
        <label class="thr-label" for="eq-budget-range">Estimated budget</label>
        <select id="eq-budget-range" class="thr-input thr-select">
          <option value="">— select if known —</option>
          <option value="under_10m">Under 10M VND</option>
          <option value="10_20m">10–20M VND</option>
          <option value="20_40m">20–40M VND</option>
          <option value="40m_plus">40M+ VND</option>
        </select>
      </div>
    </div>

    <div class="thr-field-group">
      <label class="thr-label">
        <input type="checkbox" id="eq-catering-needed" value="1">
        Catering / food &amp; beverage package needed
      </label>
    </div>

    <div class="thr-field-group">
      <label class="thr-label" for="eq-notes">Additional notes or requirements</label>
      <textarea id="eq-notes" class="thr-input thr-textarea" placeholder="Tell us more about your event, theme, AV requirements, or any special requests…" rows="4"></textarea>
    </div>

    <div class="thr-field-group">
      <label class="thr-label">Language preference</label>
      <div class="thr-lang-toggle">
        <label><input type="radio" name="eq-lang" value="en"> English</label>
        <label><input type="radio" name="eq-lang" value="vi" checked> Tiếng Việt</label>
      </div>
    </div>

    <div id="thr-eq-error" class="thr-error" style="display:none;"></div>

    <button type="button" id="thr-eq-submit" class="thr-btn">Send enquiry →</button>
  </div>

  <!-- Confirmation state -->
  <div id="thr-eq-success" style="display:none;text-align:center;padding:40px 0;">
    <div class="thr-confirm-icon">✓</div>
    <h2 class="thr-step-title">Enquiry received</h2>
    <p style="color:rgba(247,243,238,0.65);">Thank you for your interest in hosting your event at TEMPO House. Our events team will be in touch within 24 hours.</p>
    <div class="thr-ref-box" style="margin:24px auto;">
      <span class="thr-ref-label">Reference</span>
      <span class="thr-ref-code" id="thr-eq-ref-code"></span>
    </div>
    <p style="font-size:13px;color:rgba(247,243,238,0.4);">Please keep your reference code — quote it in any correspondence about this enquiry.</p>
  </div>

</div>

<script>
(function() {
  var api   = (typeof thrEvents !== 'undefined') ? thrEvents.apiUrl : '';
  var nonce = (typeof thrEvents !== 'undefined') ? thrEvents.nonce  : '';

  document.getElementById('thr-eq-submit').addEventListener('click', function() {
    var btn    = this;
    var errEl  = document.getElementById('thr-eq-error');
    errEl.style.display = 'none';

    var name   = document.getElementById('eq-contact-name').value.trim();
    var email  = document.getElementById('eq-contact-email').value.trim();
    var phone  = document.getElementById('eq-contact-phone').value.trim();
    var zalo   = document.getElementById('eq-contact-zalo').value.trim();
    var company= document.getElementById('eq-company-name').value.trim();
    var evType = document.getElementById('eq-event-type').value;
    var date   = document.getElementById('eq-preferred-date').value;
    var guests = parseInt(document.getElementById('eq-guest-count').value, 10);
    var budget = document.getElementById('eq-budget-range').value;
    var catering = document.getElementById('eq-catering-needed').checked ? 1 : 0;
    var notes  = document.getElementById('eq-notes').value.trim();
    var lang   = (document.querySelector('input[name="eq-lang"]:checked') || {}).value || 'en';

    if (!name) { errEl.textContent = 'Please enter your name.'; errEl.style.display = 'block'; return; }
    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { errEl.textContent = 'Please enter a valid email address.'; errEl.style.display = 'block'; return; }
    if (!guests || guests < 1) { errEl.textContent = 'Please enter the expected number of guests.'; errEl.style.display = 'block'; return; }

    btn.disabled = true;
    btn.textContent = 'Sending…';

    fetch(api + 'public/event-enquiry', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
      body: JSON.stringify({
        contact_name:     name,
        contact_email:    email,
        contact_phone:    phone || null,
        contact_zalo:     zalo || null,
        company_name:     company || null,
        event_type:       evType,
        preferred_date:   date || null,
        guest_count:      guests,
        budget_range:     budget || null,
        catering_needed:  catering,
        notes:            notes || null,
        lang:             lang,
      }),
    })
    .then(function(r) { return r.json().then(function(d) { return { ok: r.ok, data: d }; }); })
    .then(function(res) {
      if (!res.ok) throw new Error(res.data.message || 'Submission failed. Please try again.');
      document.getElementById('thr-eq-ref-code').textContent = res.data.reference_code;
      document.getElementById('thr-eq-form-wrap').style.display = 'none';
      document.getElementById('thr-eq-success').style.display = '';
    })
    .catch(function(err) {
      errEl.textContent = err.message;
      errEl.style.display = 'block';
      btn.disabled = false;
      btn.textContent = 'Send enquiry →';
    });
  });
})();
</script>
