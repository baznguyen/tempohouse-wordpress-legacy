<?php defined( 'ABSPATH' ) || exit;
require_once THR_PLUGIN_DIR . 'templates/emails/base.php';

$is_vi      = ( $r->diner_lang ?? '' ) === 'vi';
$first_name = explode( ' ', $r->diner_name )[0];
$occasion_label = THR_Settings::occasion_types()[ $r->occasion ] ?? ucfirst( $r->occasion );
$vip_badge  = $r->is_vip ? '<span class="vip-badge">VIP</span>' : '';

$h1        = $is_vi ? 'Đã xác nhận' : "You're confirmed";
$intro     = $is_vi
    ? "Xin chào {$first_name}, chúng tôi rất mong gặp bạn. Dưới đây là chi tiết đặt bàn của bạn."
    : "Hi {$first_name}, we can't wait to see you. Here are your booking details.";
$save_note = $is_vi
    ? 'Lưu mã này — bạn sẽ cần dùng nó khi muốn thay đổi hoặc hủy đặt bàn.'
    : "Save this code — you'll need it if you need to amend or cancel your booking.";

$lbl_date     = $is_vi ? 'Ngày'     : 'Date';
$lbl_time     = $is_vi ? 'Giờ'      : 'Time';
$lbl_guests   = $is_vi ? 'Số khách' : 'Guests';
$lbl_occasion = $is_vi ? 'Dịp'      : 'Occasion';
$lbl_location = $is_vi ? 'Địa chỉ' : 'Location';

$btn_directions = $is_vi ? 'Xem bản đồ'  : 'Get Directions';
$btn_cancel     = $is_vi ? 'Hủy đặt bàn' : 'Cancel Reservation';

$content = <<<HTML
<h2>{$h1}{$vip_badge}</h2>
<p style="margin-bottom:24px;">{$intro}</p>

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
    <span class="detail-label">{$lbl_occasion}</span>
    <span class="detail-value">{$occasion_label}</span>
  </div>
  <div class="detail-row">
    <span class="detail-label">{$lbl_location}</span>
    <span class="detail-value">{$venue_address}</span>
  </div>
</div>

<p>{$save_note}</p>

<div style="margin-top:8px;">
  <a href="https://maps.google.com/?q=TEMPO+House+Ho+Chi+Minh+City" class="cta-btn">{$btn_directions}</a>
</div>
<div style="margin-top:12px;">
  <a href="{$cancel_url}" class="cta-btn secondary">{$btn_cancel}</a>
</div>

<p class="policy">{$policy}</p>
HTML;

echo thr_email_wrap( $content, $accent, $logo_url ?? null, $venue_name );
