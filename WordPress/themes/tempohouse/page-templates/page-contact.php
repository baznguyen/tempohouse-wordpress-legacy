<?php
/**
 * Template Name: Contact
 * Description:  Contact page — map in frame, address, form, directions, reservations.
 */
get_header();
?>

<main class="page-contact" id="main" role="main">

  <!-- ── Banner ─────────────────────────────────── -->
  <header class="page-inner__banner">
    <div class="page-inner__container">
      <p class="page-inner__eyebrow">Contact</p>
      <h1 class="page-inner__title">218c Pasteur, District&nbsp;3,<br>Ho Chi Minh City.</h1>
    </div>
  </header>

  <!-- ── Map + Info — full-bleed two-column ──────── -->
  <section class="page-contact__map-section">

    <div class="page-contact__map-col">
      <div class="page-contact__map-frame">
        <iframe
          class="page-contact__map-iframe"
          src="https://www.google.com/maps?q=218c+Pasteur+Qu%E1%BA%ADn+3+Ho+Chi+Minh+City&output=embed"
          width="100%"
          height="100%"
          style="border:0;"
          allowfullscreen=""
          loading="eager"
          referrerpolicy="no-referrer-when-downgrade"
          title="TEMPO House location"
        ></iframe>
      </div>
    </div>

    <div class="page-contact__info-col">
      <div class="page-contact__info-inner">

        <div class="page-contact__info-block">
          <p class="page-contact__info-label">Address</p>
          <p class="page-contact__info-value">218c Pasteur, Xu&acirc;n Ho&agrave;,<br>Qu&#7853;n 3, Ho Chi Minh City</p>
          <p class="page-contact__info-note">10 minutes by Grab from B&#7871;n Th&agrave;nh. Motorbike parking directly outside.</p>
        </div>

        <div class="page-contact__info-block">
          <p class="page-contact__info-label">Hours</p>
          <div class="page-contact__hours-row">
            <span>Caf&eacute;</span>
            <span>08:00 &ndash; 17:00 daily</span>
          </div>
          <div class="page-contact__hours-row">
            <span>Bar</span>
            <span>18:00 &ndash; 01:00 daily</span>
          </div>
          <div class="page-contact__hours-row">
            <span>Gallery</span>
            <span>Open every day, free entry</span>
          </div>
        </div>

        <div class="page-contact__info-block">
          <p class="page-contact__info-label">Reach us</p>
          <div class="page-contact__dir-row">
            <span>General</span>
            <a href="mailto:hello@tempohouse.com.vn">hello@tempohouse.com.vn</a>
          </div>
          <div class="page-contact__dir-row">
            <span>Events</span>
            <a href="mailto:events@tempohouse.com.vn">events@tempohouse.com.vn</a>
          </div>
          <div class="page-contact__dir-row">
            <span>Press</span>
            <a href="mailto:marketing@tempohouse.com.vn">marketing@tempohouse.com.vn</a>
          </div>
          <div class="page-contact__dir-row">
            <span>Instagram</span>
            <a href="https://instagram.com/tempohouse.sgn" target="_blank" rel="noopener noreferrer">@tempohouse.sgn</a>
          </div>
        </div>

      </div>
    </div>

  </section>

  <!-- ── Form — gallery visitor-book aesthetic ───── -->
  <section class="page-contact__form-section">
    <div class="page-inner__container">
      <div class="page-contact__form-layout">

        <div class="page-contact__form-intro">
          <p class="page-contact__form-eyebrow">Leave a note</p>
          <h2 class="page-contact__form-heading">We&rsquo;d love to<br>hear from you.</h2>
          <p class="page-contact__form-subtext">Whether you&rsquo;re planning a private event, reaching out about the gallery, or simply want to say hello &mdash; our team reads every message.</p>
        </div>

        <form class="contact-form" id="contact-form" novalidate aria-label="Contact form">

          <div class="contact-form__row">
            <div class="contact-form__group">
              <label class="contact-form__label" for="cf-name">Name <abbr title="required">*</abbr></label>
              <input class="contact-form__input" type="text" id="cf-name" name="name" autocomplete="name" required>
            </div>
            <div class="contact-form__group">
              <label class="contact-form__label" for="cf-email">Email <abbr title="required">*</abbr></label>
              <input class="contact-form__input" type="email" id="cf-email" name="email" autocomplete="email" required>
            </div>
          </div>

          <div class="contact-form__group">
            <label class="contact-form__label" for="cf-subject">Subject</label>
            <div class="contact-form__select-wrap">
              <select class="contact-form__select" id="cf-subject" name="subject">
                <option value="" disabled selected>Select a topic</option>
                <option value="general">General enquiry</option>
                <option value="press">Press &amp; media</option>
                <option value="partnership">Partnership</option>
                <option value="gallery">Gallery programme</option>
                <option value="other">Other</option>
              </select>
            </div>
          </div>

          <div class="contact-form__group">
            <label class="contact-form__label" for="cf-message">Message <abbr title="required">*</abbr></label>
            <textarea class="contact-form__textarea" id="cf-message" name="message" rows="6" required></textarea>
          </div>

          <div class="contact-form__footer">
            <button class="contact-form__submit" type="submit" id="cf-submit">Send message</button>
            <p class="contact-form__privacy">Your message goes directly to our team.</p>
          </div>

          <p class="contact-form__error" id="cf-error" hidden></p>
          <div class="contact-form__success" id="cf-success" hidden>
            <p class="contact-form__success-title">&#10022;&ensp;Message received.</p>
            <p class="contact-form__success-body">Got it &mdash; we&rsquo;ll be back to you shortly.</p>
          </div>

        </form><!-- /.contact-form -->

      </div>
    </div>
  </section>

  <!-- ── Getting Here ───────────────────────────── -->
  <section class="page-inner__section page-inner__section--alt">
    <div class="page-inner__container">

      <p class="page-inner__section-head">Getting Here</p>
      <h2 class="page-inner__section-title">218c Pasteur, District 3, Ho Chi Minh City.</h2>

      <div class="page-inner__info-grid">

        <div>
          <p class="page-inner__info-label">By Grab / Ride-share</p>
          <p class="page-inner__info-value">Search &ldquo;218c Pasteur, Qu&#7841;n 3&rdquo; and drop off directly outside. Around 10 minutes from B&#7871;n Th&agrave;nh market.</p>
        </div>

        <div>
          <p class="page-inner__info-label">On Foot</p>
          <p class="page-inner__info-value">A 12-minute walk from the eastern edge of T&acirc;o &ETH;&agrave;n Park. Head south on Pasteur &mdash; tree-lined the whole way.</p>
        </div>

        <div>
          <p class="page-inner__info-label">Parking</p>
          <p class="page-inner__info-value">Motorbike parking directly outside the entrance. Cars: street parking on Pasteur and the surrounding side streets.</p>
        </div>

      </div>

    </div>
  </section>


</main><!-- /.page-contact -->

<script>
(function () {
  var form   = document.getElementById('contact-form');
  var submit = document.getElementById('cf-submit');
  var errEl  = document.getElementById('cf-error');
  var okEl   = document.getElementById('cf-success');
  if (!form) return;

  var endpoint = '<?php echo esc_js( rest_url( 'tempohouse/v1/enquiry' ) ); ?>';

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    var data = {
      type:    'contact',
      name:    form.querySelector('[name="name"]').value.trim(),
      email:   form.querySelector('[name="email"]').value.trim(),
      subject: form.querySelector('[name="subject"]').value,
      message: form.querySelector('[name="message"]').value.trim(),
    };

    if (!data.name || !data.email || !data.message) {
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
        okEl.hidden = false;
      } else {
        throw new Error(json.message || 'An error occurred.');
      }
    })
    .catch(function (err) {
      errEl.textContent = err.message || 'Something went wrong — please try again or email us directly.';
      errEl.hidden = false;
      submit.disabled = false;
      submit.textContent = 'Send message';
    });
  });
})();
</script>

<?php get_footer(); ?>
