<?php defined( 'ABSPATH' ) || exit;
require_once THR_PLUGIN_DIR . 'templates/emails/base.php';

$is_vi      = ( $r->diner_lang ?? '' ) === 'vi';
$first_name = explode( ' ', $r->diner_name )[0];

// $when is pre-translated by class-email.php send_reminder()
$h1  = $is_vi ? "Hẹn gặp bạn {$when}" : "See you {$when}";
$intro = $is_vi
    ? "Xin chào {$first_name}, đây là nhắc nhở về đặt bàn sắp tới của bạn tại TEMPO House."
    : "Hi {$first_name}, this is a friendly reminder about your upcoming reservation at TEMPO House.";
$lbl_date     = $is_vi ? 'Ngày'     : 'Date';
$lbl_time     = $is_vi ? 'Giờ'      : 'Time';
$lbl_guests   = $is_vi ? 'Số khách' : 'Guests';
$lbl_location = $is_vi ? 'Địa chỉ' : 'Location';
$btn_directions = $is_vi ? 'Xem bản đồ' : 'Get Directions';
$btn_cancel     = $is_vi ? 'Hủy'        : 'Cancel';

$content = <<<HTML
<h2>{$h1}</h2>
<p>{$intro}</p>

<div class="ref-badge">{$r->reference_code}</div>

<div style="margin:24px 0;">
  <div class="detail-row">
    <span class="detail-label">{$lbl_date}</span>
    <span class="detail-value">{$date_local}</span>
  </div>
  <div class="detail-row">
    <span class="detail-label">{$lbl_time}</span>
    <span class="detail-value">{$time_local}</span>
  </div>
  <div class="detail-row">
    <span class="detail-label">{$lbl_guests}</span>
    <span class="detail-value">{$r->party_size}</span>
  </div>
  <div class="detail-row">
    <span class="detail-label">{$lbl_location}</span>
    <span class="detail-value">{$venue_address}</span>
  </div>
</div>

<div style="margin-top:8px;">
  <a href="https://maps.google.com/?q=TEMPO+House+Ho+Chi+Minh+City" class="cta-btn">{$btn_directions}</a>
</div>
<div style="margin-top:12px;">
  <a href="{$cancel_url}" class="cta-btn secondary">{$btn_cancel}</a>
</div>

<p class="policy">{$policy}</p>
HTML;

echo thr_email_wrap( $content, $accent, $logo_url ?? null, $venue_name );
