<section class="newsletter" id="newsletter" aria-label="Stay connected">
  <div class="container container--narrow">
    <p class="newsletter__eyebrow">First to know</p>
    <h2 class="newsletter__title">The TEMPO letter.</h2>
    <p class="newsletter__body">New events, private hire dates, and the occasional table that opens up. First access for those on the list.</p>
    <div class="newsletter__form-wrap">
      <form class="newsletter__form" id="newsletter-form" novalidate>
        <input
          type="email"
          id="newsletter-email"
          name="email"
          class="newsletter__input"
          placeholder="your@email.com"
          required
          autocomplete="email"
        >
        <button type="submit" class="newsletter__btn">Join the List</button>
        <p class="newsletter__error" id="newsletter-error" hidden>Something went wrong &mdash; please try again.</p>
        <p class="newsletter__success" id="newsletter-success" hidden>&#10022;&ensp;You&rsquo;re on the list.</p>
      </form>
    </div>
  </div>
</section>

<script>
(function () {
  var form    = document.getElementById('newsletter-form');
  var emailEl = document.getElementById('newsletter-email');
  var btn     = form ? form.querySelector('.newsletter__btn') : null;
  var errEl   = document.getElementById('newsletter-error');
  var okEl    = document.getElementById('newsletter-success');
  if (!form) return;

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    var email = emailEl.value.trim();
    if (!email) return;

    btn.disabled = true;
    btn.textContent = '···';
    errEl.hidden = true;
    okEl.hidden  = true;

    fetch('https://a.klaviyo.com/client/subscriptions/?company_id=VCR2Ei', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'revision': '2024-02-15' },
      body: JSON.stringify({
        data: {
          type: 'subscription',
          attributes: {
            custom_source: 'Website',
            profile: { data: { type: 'profile', attributes: { email: email } } }
          }
        }
      })
    })
    .then(function (res) {
      if (res.status === 202 || res.ok) {
        form.style.display = 'none';
        okEl.hidden = false;
      } else {
        throw new Error('fail');
      }
    })
    .catch(function () {
      btn.disabled = false;
      btn.textContent = 'Join the List';
      errEl.hidden = false;
    });
  });
})();
</script>
