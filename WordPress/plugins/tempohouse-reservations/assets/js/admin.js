/* TEMPO House Reservations — Admin JS */
(function ($) {
  'use strict';

  var modal = window.thrModal || function (opts) {
    // Fallback if thr-modal.js not loaded
    if (opts.type === 'confirm') return Promise.resolve(confirm(opts.title || opts.body || ''));
    if (opts.type === 'alert') { alert(opts.body || opts.title || ''); return Promise.resolve(true); }
    return Promise.resolve(null);
  };

  // Status transitions via AJAX
  $(document).on('click', '.thr-status-btn', function () {
    var $btn   = $(this);
    var id     = $btn.data('id');
    var status = $btn.data('status');
    var labels = { confirmed: 'Confirm', seated: 'Seat Now', completed: 'Complete', cancelled: 'Cancel', no_show: 'No-show' };

    var needsConfirm = status === 'cancelled' || status === 'no_show';
    var confirmTitle = status === 'cancelled' ? 'Cancel this reservation?' : 'Mark as no-show?';
    var confirmOk    = status === 'cancelled' ? 'Cancel Reservation' : 'Mark No-show';

    if (needsConfirm) {
      modal({
        type:   'confirm',
        title:  confirmTitle,
        ok:     confirmOk,
        cancel: 'Go back',
        danger: true,
      }).then(function (ok) {
        if (ok) doStatusUpdate($btn, id, status, labels);
      });
    } else {
      doStatusUpdate($btn, id, status, labels);
    }
  });

  function doStatusUpdate($btn, id, status, labels) {
    $btn.prop('disabled', true).text('Updating…');

    $.post(thrAdmin.ajaxUrl, {
      action: 'thr_update_status',
      nonce:  thrAdmin.nonce,
      id:     id,
      status: status,
    }).done(function (res) {
      if (res.success) {
        window.location.reload();
      } else {
        modal({ type: 'alert', title: 'Update failed', body: '<p>' + (res.data || 'Something went wrong. Please try again.') + '</p>', ok: 'OK' });
        $btn.prop('disabled', false).text(labels[status] || 'Update');
      }
    }).fail(function () {
      modal({ type: 'alert', title: 'Network error', body: '<p>Could not reach the server. Please check your connection and try again.</p>', ok: 'OK' });
      $btn.prop('disabled', false).text(labels[status] || 'Update');
    });
  }

  // VIP toggle
  $(document).on('change', '.thr-vip-toggle', function () {
    var $cb   = $(this);
    var id    = $cb.data('id');
    var isVip = $cb.is(':checked') ? 1 : 0;

    fetch(thrAdmin.apiUrl + 'reservations/' + id, {
      method: 'PATCH',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': thrAdmin.nonce },
      body: JSON.stringify({ is_vip: isVip }),
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
      if (data.is_vip !== undefined) {
        var notice = $('<span style="color:#2d6a4f;margin-left:8px;font-size:0.82rem;">Saved</span>');
        $cb.closest('label').append(notice);
        setTimeout(function () { notice.fadeOut(300, function () { notice.remove(); }); }, 2000);
      }
    });
  });

  // Resend confirmation email
  $(document).on('click', '.thr-resend-btn', function () {
    var $btn = $(this);
    var id   = $btn.data('id');

    modal({
      type:   'confirm',
      title:  'Resend confirmation email?',
      body:   '<p>A new confirmation email will be sent to the guest.</p>',
      ok:     'Resend',
      cancel: 'Cancel',
    }).then(function (ok) {
      if (!ok) return;
      $btn.prop('disabled', true).text('Sending…');

      fetch(thrAdmin.apiUrl + 'reservations/' + id + '/status', {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': thrAdmin.nonce },
        body: JSON.stringify({ status: 'confirmed', _resend_email: true }),
      })
      .then(function () {
        $btn.text('Sent ✓');
        setTimeout(function () { $btn.prop('disabled', false).text('Resend confirmation'); }, 3000);
      });
    });
  });

})(jQuery);
