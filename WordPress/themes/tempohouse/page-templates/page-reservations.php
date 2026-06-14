<?php
/**
 * Template Name: Reservations
 * Description:  Table reservation request form.
 */
get_header();
?>

<main class="page-reservations" id="main" role="main">

  <!-- ── Banner ──────────────────────────────────────── -->
  <header class="page-inner__banner">
    <p class="page-inner__eyebrow">Reservations</p>
    <h1 class="page-inner__title">Book a table.</h1>
    <p class="page-inner__lead">Reservations recommended for evenings, weekends, and groups of four or more. Walk-ins are always welcome when we have availability.</p>
  </header>

  <!-- ── Form ────────────────────────────────────────── -->
  <section class="page-inner__section page-reservations__form-section">
    <div class="page-inner__container container--narrow">

      <form class="res-form" id="res-form" novalidate aria-label="Table reservation request form">

        <div class="res-form__row res-form__row--split">
          <div class="res-form__group">
            <label class="res-form__label" for="res-name">Your name <abbr title="required">*</abbr></label>
            <input class="res-form__input" type="text" id="res-name" name="name" autocomplete="name" required>
          </div>
          <div class="res-form__group">
            <label class="res-form__label" for="res-email">Email address <abbr title="required">*</abbr></label>
            <input class="res-form__input" type="email" id="res-email" name="email" autocomplete="email" required>
          </div>
        </div>

        <div class="res-form__row res-form__row--split">
          <div class="res-form__group">
            <label class="res-form__label" for="res-phone">Phone <span class="res-form__optional">(optional)</span></label>
            <input class="res-form__input" type="tel" id="res-phone" name="phone" autocomplete="tel">
          </div>
          <div class="res-form__group">
            <label class="res-form__label" for="res-date">Date <abbr title="required">*</abbr></label>
            <input class="res-form__input" type="date" id="res-date" name="date" required>
          </div>
        </div>

        <div class="res-form__row res-form__row--split">
          <div class="res-form__group">
            <label class="res-form__label" for="res-time">Time preference <abbr title="required">*</abbr></label>
            <div class="res-form__select-wrap">
              <select class="res-form__select" id="res-time" name="time_preference" required>
                <option value="" disabled selected>Select a time</option>
                <option value="morning">Morning 07:00–10:00</option>
                <option value="late-morning">Late morning 10:00–12:00</option>
                <option value="lunch">Lunch 12:00–14:00</option>
                <option value="afternoon">Afternoon 14:00–17:00</option>
                <option value="evening">Evening 18:00–20:00</option>
                <option value="late-evening">Late evening 20:00–01:00</option>
              </select>
            </div>
          </div>
          <div class="res-form__group">
            <label class="res-form__label" for="res-party">Party size <abbr title="required">*</abbr></label>
            <div class="res-form__select-wrap">
              <select class="res-form__select" id="res-party" name="party_size" required>
                <option value="" disabled selected>Select party size</option>
                <option value="1">Just me</option>
                <option value="2">2 people</option>
                <option value="3-4">3–4 people</option>
                <option value="5-6">5–6 people</option>
                <option value="7-10">7–10 people</option>
                <option value="10+">10+ people</option>
              </select>
            </div>
          </div>
        </div>

        <div class="res-form__row">
          <div class="res-form__group">
            <label class="res-form__label" for="res-space">Space preference <span class="res-form__optional">(optional)</span></label>
            <div class="res-form__select-wrap">
              <select class="res-form__select" id="res-space" name="space_preference">
                <option value="">No preference</option>
                <option value="cafe-indoors">Café (indoors)</option>
                <option value="outdoor">Outdoor seating</option>
                <option value="bar">Bar (evenings only)</option>
              </select>
            </div>
          </div>
        </div>

        <div class="res-form__row">
          <div class="res-form__group">
            <label class="res-form__label" for="res-notes">Additional notes <span class="res-form__optional">(optional)</span></label>
            <textarea class="res-form__textarea" id="res-notes" name="notes" rows="4" placeholder="Dietary requirements, special occasions, accessibility needs…"></textarea>
          </div>
        </div>

        <div class="res-form__footer">
          <button class="res-form__submit" type="submit" id="res-submit">Request Reservation</button>
          <p class="res-form__privacy">Your details are used only to confirm your reservation and are never shared.</p>
        </div>

        <p class="res-form__error" id="res-error" hidden></p>

        <div class="res-form__success" id="res-success" hidden>
          <p class="res-form__success-title">&#10022;&ensp;Your request is on its way.</p>
          <p class="res-form__success-body" id="res-success-body">Thanks&nbsp;&mdash; we&rsquo;ll confirm your reservation within one business day.</p>
        </div>

      </form>

    </div>
  </section>

  <!-- ── Info strip ───────────────────────────────────── -->
  <section class="page-inner__section page-inner__section--alt">
    <div class="page-inner__container">
      <div class="page-inner__info-grid">

        <div>
          <p class="page-inner__info-label">Hours</p>
          <p class="page-inner__info-value">
            Café 07:00–17:00<br>
            Bar 18:00–01:00<br>
            Open daily
          </p>
        </div>

        <div>
          <p class="page-inner__info-label">Address</p>
          <p class="page-inner__info-value">
            218c Pasteur<br>
            Xuân Hoà, Quận 3<br>
            Ho Chi Minh City
          </p>
        </div>

        <div>
          <p class="page-inner__info-label">Walk-ins</p>
          <p class="page-inner__info-value">
            Always welcome when we have availability. No reservation needed for 1–2 guests during quieter hours.
          </p>
        </div>

      </div>
    </div>
  </section>

  <!-- ── Private events prompt ────────────────────────── -->
  <section class="page-inner__section page-reservations__private-prompt">
    <div class="page-inner__container">
      <p class="page-reservations__private-eyebrow">Private Hire &amp; Events</p>
      <h2 class="page-reservations__private-title">Planning something bigger?</h2>
      <p class="page-reservations__private-body">For private hire, events, and group bookings of 10+, use our event enquiry form instead.</p>
      <a class="page-reservations__private-cta" href="/event-enquiry">Event Enquiry &rarr;</a>
    </div>
  </section>

</main>

<script>
(function () {
  var form    = document.getElementById('res-form');
  var submit  = document.getElementById('res-submit');
  var errEl   = document.getElementById('res-error');
  var okEl    = document.getElementById('res-success');
  var okBody  = document.getElementById('res-success-body');
  if (!form) return;

  var endpoint = '<?php echo esc_js( rest_url( 'tempohouse/v1/enquiry' ) ); ?>';

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    var data = {
      type:             'reservation',
      name:             form.querySelector('[name="name"]').value.trim(),
      email:            form.querySelector('[name="email"]').value.trim(),
      phone:            form.querySelector('[name="phone"]').value.trim(),
      date:             form.querySelector('[name="date"]').value,
      time_preference:  form.querySelector('[name="time_preference"]').value,
      party_size:       form.querySelector('[name="party_size"]').value,
      space_preference: form.querySelector('[name="space_preference"]').value,
      notes:            form.querySelector('[name="notes"]').value.trim(),
    };

    if (!data.name || !data.email || !data.date || !data.time_preference || !data.party_size) {
      errEl.textContent = 'Please fill in all required fields.';
      errEl.hidden = false;
      return;
    }

    errEl.hidden = true;
    submit.disabled = true;
    submit.textContent = '···';

    fetch(endpoint, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data),
    })
    .then(function (res) { return res.json(); })
    .then(function (json) {
      if (json.success) {
        form.style.display = 'none';
        if (okBody) {
          okBody.textContent = 'Thanks, ' + data.name.split(' ')[0] + ' — we’ll confirm your reservation within one business day.';
        }
        okEl.hidden = false;
      } else {
        throw new Error(json.message || 'An error occurred.');
      }
    })
    .catch(function (err) {
      errEl.textContent = err.message || 'Something went wrong — please try again or email hello@tempohouse.com.vn.';
      errEl.hidden = false;
      submit.disabled = false;
      submit.textContent = 'Request Reservation';
    });
  });
})();
</script>

<?php get_footer(); ?>
