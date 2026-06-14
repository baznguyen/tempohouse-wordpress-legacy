<?php defined( 'ABSPATH' ) || exit;
require_once THR_PLUGIN_DIR . 'templates/emails/base.php';

$is_vi      = ( $r->diner_lang ?? '' ) === 'vi';
$first_name = explode( ' ', $r->diner_name )[0];

$h1         = $is_vi ? 'Đã nhận yêu cầu đặt bàn' : 'Booking received';
$intro      = $is_vi
    ? "Xin chào {$first_name}, chúng tôi đã nhận yêu cầu đặt bàn của bạn và sẽ xác nhận sớm."
    : "Hi {$first_name}, we've received your booking request and will confirm it shortly.";
$lbl_req    = $is_vi ? 'Yêu cầu cho' : 'Requested for';
$lbl_guests = $is_vi ? 'Số khách'    : 'Guests';
$keep_note  = $is_vi
    ? 'Lưu mã đặt bàn của bạn. Chúng tôi sẽ gửi email xác nhận khi yêu cầu được chấp thuận.'
    : "Keep your reference code handy. We'll send a confirmation email once your reservation is approved.";
$btn_cancel = $is_vi ? 'Hủy yêu cầu' : 'Cancel Request';

$content = <<<HTML
<h2>{$h1}</h2>
<p>{$intro}</p>

<div class="ref-badge">{$r->reference_code}</div>

<div style="margin:24px 0;">
  <div class="detail-row">
    <span class="detail-label">{$lbl_req}</span>
    <span class="detail-value">{$date_local} at {$time_local}</span>
  </div>
  <div class="detail-row">
    <span class="detail-label">{$lbl_guests}</span>
    <span class="detail-value">{$r->party_size}</span>
  </div>
</div>

<p>{$keep_note}</p>

<div style="margin-top:8px;">
  <a href="{$cancel_url}" class="cta-btn secondary">{$btn_cancel}</a>
</div>

<p class="policy">{$policy}</p>
HTML;

echo thr_email_wrap( $content, $accent, $logo_url ?? null, $venue_name );
