/* TEMPO House Reservations — Admin JS */
(function ($) {
  'use strict';

  // Status transitions via AJAX
  $(document).on('click', '.thr-status-btn', function () {
    const $btn = $(this);
    const id     = $btn.data('id');
    const status = $btn.data('status');
    const labels = { confirmed: 'Confirm', seated: 'Seat Now', completed: 'Complete', cancelled: 'Cancel', no_show: 'No-show' };

    if (status === 'cancelled' && !confirm('Cancel this reservation?')) return;
    if (status === 'no_show'   && !confirm('Mark as no-show?')) return;

    $btn.prop('disabled', true).text('Updating…');

    $.post(thrAdmin.ajaxUrl, {
      action: 'thr_update_status',
      nonce:  thrAdmin.nonce,
      id:     id,
      status: status,
    }).done(function (res) {
      if (res.success) {
        // Reload so status badges refresh
        window.location.reload();
      } else {
        alert(res.data || 'Update failed.');
        $btn.prop('disabled', false).text(labels[status] || 'Update');
      }
    }).fail(function () {
      alert('Network error. Please try again.');
      $btn.prop('disabled', false).text(labels[status] || 'Update');
    });
  });

  // VIP toggle
  $(document).on('change', '.thr-vip-toggle', function () {
    const $cb = $(this);
    const id   = $cb.data('id');
    const isVip = $cb.is(':checked') ? 1 : 0;

    fetch(thrAdmin.apiUrl + 'reservations/' + id, {
      method: 'PATCH',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': thrAdmin.nonce },
      body: JSON.stringify({ is_vip: isVip }),
    })
    .then(r => r.json())
    .then(data => {
      if (data.is_vip !== undefined) {
        // Show brief confirmation
        const notice = $('<span style="color:#2d6a4f;margin-left:8px;font-size:0.82rem;">Saved</span>');
        $cb.closest('label').append(notice);
        setTimeout(() => notice.fadeOut(300, () => notice.remove()), 2000);
      }
    });
  });

  // Resend confirmation email
  $(document).on('click', '.thr-resend-btn', function () {
    const $btn = $(this);
    const id   = $btn.data('id');

    if (!confirm('Resend confirmation email to guest?')) return;
    $btn.prop('disabled', true).text('Sending…');

    fetch(thrAdmin.apiUrl + 'reservations/' + id + '/status', {
      method: 'PATCH',
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': thrAdmin.nonce },
      body: JSON.stringify({ status: 'confirmed', _resend_email: true }),
    })
    .then(() => {
      $btn.text('Sent ✓');
      setTimeout(() => { $btn.prop('disabled', false).text('Resend confirmation'); }, 3000);
    });
  });

})(jQuery);
