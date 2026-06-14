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
    <p class="page-inner__lead">Reservations are recommended for evenings, weekends, and groups of four or more. Walk-ins are welcome when we have space. Fill in the form and we&rsquo;ll confirm within one business day. For same-day bookings, call us directly.</p>
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
            <label class="res-form__label" for="res-email">Email <abbr title="required">*</abbr></label>
            <input class="res-form__input" type="email" id="res-email" name="email" autocomplete="email" required>
          </div>
        </div>

        <div class="res-form__row res-form__row--split">
          <div class="res-form__group">
            <label class="res-form__label" for="res-phone">Phone <span class="res-form__optional">(optional)</span></label>
            <input class="res-form__input" type="tel" id="res-phone" name="phone" autocomplete="tel">
          </div>
          <div class="res-form__group">
            <label class="res-form__label" for="res-date">When are you coming? <abbr title="required">*</abbr></label>
            <input class="res-form__input" type="date" id="res-date" name="date" required>
          </div>
        </div>

        <div class="res-form__row res-form__row--split">
          <div class="res-form__group">
            <label class="res-form__label" for="res-time">Preferred time <abbr title="required">*</abbr></label>
            <div class="res-form__select-wrap">
              <select class="res-form__select" id="res-time" name="time_preference" required>
                <option value="" disabled selected>Pick a time slot</option>
                <option value="morning">Morning &mdash; 07:00&ndash;10:00</option>
                <option value="late-morning">Late morning &mdash; 10:00&ndash;12:00</option>
                <option value="lunch">Lunch &mdash; 12:00&ndash;14:00</option>
                <option value="afternoon">Afternoon &mdash; 14:00&ndash;17:00</option>
                <option value="evening">Early evening &mdash; 18:00&ndash;20:00</option>
                <option value="late-evening">Late evening &mdash; 20:00&ndash;01:00</option>
              </select>
            </div>
          </div>
          <div class="res-form__group">
            <label class="res-form__label" for="res-party">How many people? <abbr title="required">*</abbr></label>
            <div class="res-form__select-wrap">
              <select class="res-form__select" id="res-party" name="party_size" required>
                <option value="" disabled selected>Select party size</option>
                <option value="1">Just me</option>
                <option value="2">2 people</option>
                <option value="3-4">3&ndash;4 people</option>
                <option value="5-6">5&ndash;6 people</option>
                <option value="7-10">7&ndash;10 people</option>
                <option value="10+">10+ people</option>
              </select>
            </div>
          </div>
        </div>

        <div class="res-form__row">
          <div class="res-form__group">
            <label class="res-form__label" for="res-space">Where would you like to sit? <span class="res-form__optional">(optional)</span></label>
            <div class="res-form__select-wrap">
              <select class="res-form__select" id="res-space" name="space_preference">
                <option value="">No preference</option>
                <option value="cafe-indoors">Café &mdash; indoors</option>
                <option value="outdoor">Outdoor seating</option>
                <option value="bar">Bar &mdash; evenings only</option>
              </select>
            </div>
          </div>
        </div>

        <div class="res-form__row">
          <div class="res-form__group">
            <label class="res-form__label" for="res-notes">Anything we should know? <span class="res-form__optional">(optional)</span></label>
            <textarea class="res-form__textarea" id="res-notes" name="notes" rows="4" placeholder="Dietary requirements, special occasions, accessibility needs&hellip;"></textarea>
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
            Caf&eacute; 07:00&ndash;17:00<br>
            Bar 18:00&ndash;01:00<br>
            Open daily
          </p>
        </div>

        <div>
          <p class="page-inner__info-label">Address</p>
          <p class="page-inner__info-value">
            218c Pasteur<br>
            Xu&acirc;n Ho&agrave;, Qu&#7853;n 3<br>
            Ho Chi Minh City
          </p>
        </div>

        <div>
          <p class="page-inner__info-label">Walk-ins</p>
          <p class="page-inner__info-value">
            Welcome any time we have space. No reservation needed for 1&ndash;2 guests during café hours or before 19:00 on weeknights.
          </p>
        </div>

      </div>
    </div>
  </section>

  <!-- ── Private events prompt ────────────────────────── -->
  <section class="page-inner__section page-reservations__private-prompt">
    <div class="page-inner__container">
      <p class="page-reservations__private-eyebrow">Private Hire &amp; Events</p>
      <h2 class="page-reservations__private-title">Booking for a private event?</h2>
      <p class="page-reservations__private-body">Full venue hire, group events, and bookings of 10 or more use a different form. Takes two minutes.</p>
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
          okBody.textContent = 'Thanks, ' + data.name.split(' ')[0] + ' — we'll confirm your reservation within one business day.';
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
