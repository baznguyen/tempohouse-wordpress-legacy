<?php defined( 'ABSPATH' ) || exit;
require_once THR_PLUGIN_DIR . 'templates/emails/base.php';

$is_vi      = ( $r->diner_lang ?? '' ) === 'vi';
$first_name = explode( ' ', $r->diner_name )[0];
$book_url   = home_url( '/reserve/' );

$h1     = $is_vi ? 'Đặt bàn đã được cập nhật'                                                               : 'Booking updated';
$intro  = $is_vi
    ? "Xin chào {$first_name}, đặt bàn của bạn tại TEMPO House đã được cập nhật thành công."
    : "Hi {$first_name}, your reservation at TEMPO House has been updated.";
$lbl_date     = $is_vi ? 'Ngày'      : 'Date';
$lbl_time     = $is_vi ? 'Giờ'       : 'Time';
$lbl_guests   = $is_vi ? 'Số khách'  : 'Guests';
$lbl_ref      = $is_vi ? 'Mã đặt bàn': 'Reference';
$outro        = $is_vi ? 'Hẹn gặp bạn tại TEMPO House!' : 'See you at TEMPO House!';
$btn_lookup   = $is_vi ? 'Xem đặt bàn của tôi'         : 'View my reservation';

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
    <span class="detail-label">{$lbl_ref}</span>
    <span class="detail-value">{$r->reference_code}</span>
  </div>
</div>

<p>{$outro}</p>

<div style="margin-top:8px;">
  <a href="{$book_url}" class="cta-btn">{$btn_lookup}</a>
</div>
HTML;

echo thr_email_wrap( $content, $accent, $logo_url ?? null, $venue_name );
