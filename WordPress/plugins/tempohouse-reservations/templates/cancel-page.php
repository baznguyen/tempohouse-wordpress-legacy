<?php
/**
 * Guest self-cancel page template.
 * Served when WP serves a page with slug 'cancel' (e.g. /reservations/cancel/).
 */
defined( 'ABSPATH' ) || exit;
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Cancel Reservation — TEMPO House</title>
<?php wp_head(); ?>
<style>body { background:#0F0E0C; margin:0; padding:0; }</style>
</head>
<body>
<div class="thr-booking-page">
  <header class="thr-bp-header">
    <a href="<?= home_url() ?>" class="thr-bp-logo">TEMPO House</a>
  </header>
  <main class="thr-bp-main">
    <div class="thr-booking-wrap" id="thr-cancel-wrap">
      <div id="thr-cancel-lookup">
        <h2 class="thr-step-title">Cancel reservation</h2>
        <p style="color:rgba(247,243,238,0.6);margin-bottom:28px;">Enter your reference code to look up your reservation.</p>
        <div class="thr-field-group">
          <label class="thr-label" for="thr-cancel-ref">Reference code</label>
          <input type="text" id="thr-cancel-ref" class="thr-input" placeholder="TH-XXXXXX" autocomplete="off" maxlength="10">
        </div>
        <div id="thr-cancel-lookup-error" class="thr-error" style="display:none;"></div>
        <button type="button" id="thr-cancel-lookup-btn" class="thr-btn">Look up reservation</button>
      </div>

      <div id="thr-cancel-confirm" style="display:none;">
        <h2 class="thr-step-title">Confirm cancellation</h2>
        <div id="thr-cancel-details" style="margin-bottom:24px;padding:20px;border:1px solid rgba(247,243,238,0.1);border-radius:3px;"></div>
        <div id="thr-cancel-confirm-error" class="thr-error" style="display:none;"></div>
        <button type="button" id="thr-cancel-confirm-btn" class="thr-btn" style="background:rgba(192,57,43,0.12);border-color:rgba(192,57,43,0.4);color:#e87066;">Yes, cancel this reservation</button>
        <button type="button" id="thr-cancel-back-btn" class="thr-btn thr-btn--outline" style="margin-top:10px;">← Go back</button>
      </div>

      <div id="thr-cancel-done" style="display:none;">
        <div class="thr-confirm-icon" style="background:rgba(192,57,43,0.1);border-color:rgba(192,57,43,0.3);color:#e87066;">✕</div>
        <h2 class="thr-step-title">Reservation cancelled</h2>
        <p class="thr-confirm-msg">Your reservation has been cancelled. We hope to welcome you another time.</p>
        <a href="<?= home_url( '/reservations/' ) ?>" class="thr-btn thr-btn--outline" style="margin-top:16px;">Book again</a>
      </div>
    </div>
  </main>
  <footer class="thr-bp-footer">
    <p>TEMPO House · Ho Chi Minh City · <a href="<?= home_url() ?>">tempohouse.com.vn</a></p>
  </footer>
</div>
<?php wp_head(); // enqueues thr-booking CSS ?>
<script>
(function() {
  var api     = '<?= esc_js( rest_url( THR_REST_NS . '/' ) ) ?>';
  var refParam = new URLSearchParams(window.location.search).get('ref') || '';
  var refField = document.getElementById('thr-cancel-ref');
  if (refParam) refField.value = refParam.toUpperCase();

  document.getElementById('thr-cancel-lookup-btn').addEventListener('click', doLookup);
  refField.addEventListener('keydown', function(e) { if (e.key === 'Enter') doLookup(); });
  document.getElementById('thr-cancel-back-btn').addEventListener('click', function() {
    show('thr-cancel-lookup'); hide('thr-cancel-confirm');
  });
  document.getElementById('thr-cancel-confirm-btn').addEventListener('click', doCancel);

  if (refParam) doLookup();

  function doLookup() {
    var ref = refField.value.trim().toUpperCase();
    var errEl = document.getElementById('thr-cancel-lookup-error');
    errEl.style.display = 'none';
    if (!ref) { errEl.textContent = 'Please enter your reference code.'; errEl.style.display = 'block'; return; }
    var btn = document.getElementById('thr-cancel-lookup-btn');
    btn.disabled = true; btn.textContent = 'Looking up…';

    fetch(api + 'public/cancel?ref=' + encodeURIComponent(ref))
      .then(function(r) { return r.json().then(function(d) { return {ok: r.ok, d: d}; }); })
      .then(function(res) {
        btn.disabled = false; btn.textContent = 'Look up reservation';
        if (!res.ok) { errEl.textContent = res.d.message || 'Reservation not found.'; errEl.style.display = 'block'; return; }
        var r = res.d;
        var details = '<table style="width:100%;border-collapse:collapse;">';
        details += row('Reference', r.reference_code);
        details += row('Name', r.diner_name);
        details += row('Date', r.date_local);
        details += row('Time', r.time_local);
        details += row('Guests', r.party_size);
        details += row('Status', r.status);
        details += '</table>';
        document.getElementById('thr-cancel-details').innerHTML = details;
        document.getElementById('thr-cancel-confirm-btn').dataset.ref = r.reference_code;
        show('thr-cancel-confirm'); hide('thr-cancel-lookup');
      })
      .catch(function() {
        btn.disabled = false; btn.textContent = 'Look up reservation';
        errEl.textContent = 'Unable to connect. Please try again.'; errEl.style.display = 'block';
      });
  }

  function doCancel() {
    var ref   = document.getElementById('thr-cancel-confirm-btn').dataset.ref;
    var errEl = document.getElementById('thr-cancel-confirm-error');
    var btn   = document.getElementById('thr-cancel-confirm-btn');
    errEl.style.display = 'none';
    btn.disabled = true; btn.textContent = 'Cancelling…';

    fetch(api + 'public/cancel', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ reference_code: ref })
    })
    .then(function(r) { return r.json().then(function(d) { return {ok: r.ok, d: d}; }); })
    .then(function(res) {
      if (!res.ok) {
        errEl.textContent = res.d.message || 'Cancellation failed.';
        errEl.style.display = 'block';
        btn.disabled = false; btn.textContent = 'Yes, cancel this reservation';
        return;
      }
      hide('thr-cancel-confirm'); show('thr-cancel-done');
    })
    .catch(function() {
      errEl.textContent = 'Connection error. Please try again.';
      errEl.style.display = 'block';
      btn.disabled = false; btn.textContent = 'Yes, cancel this reservation';
    });
  }

  function row(label, val) {
    return '<tr><td style="padding:7px 0;color:rgba(247,243,238,0.4);font-size:13px;width:100px;">' + label + '</td>'
         + '<td style="padding:7px 0;color:#F7F3EE;font-size:14px;">' + String(val || '—') + '</td></tr>';
  }

  function show(id) { document.getElementById(id).style.display = ''; }
  function hide(id) { document.getElementById(id).style.display = 'none'; }
})();
</script>
<?php wp_footer(); ?>
</body>
</html>
