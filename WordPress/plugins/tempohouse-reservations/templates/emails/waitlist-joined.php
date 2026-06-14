<?php defined( 'ABSPATH' ) || exit;
require_once THR_PLUGIN_DIR . 'templates/emails/base.php';

$is_vi      = ( $entry->diner_lang ?? '' ) === 'vi';
$first_name = explode( ' ', $entry->diner_name )[0];
$date_fmt   = date( 'l, F j, Y', strtotime( $entry->requested_date ) );
$party_size = (int) $entry->party_size;
$party_label = $party_size . ( $is_vi ? ' khách' : ( $party_size > 1 ? ' guests' : ' guest' ) );

if ( $entry->requested_time ) {
    $time_fmt = date( 'g:ia', strtotime( $entry->requested_date . ' ' . $entry->requested_time . ':00' ) );
} else {
    $time_fmt = $is_vi ? 'Bất kỳ giờ nào' : 'Any available time';
}

$h1          = $is_vi ? 'Bạn đã vào danh sách chờ' : "You're on the waitlist";
$intro       = $is_vi
    ? "Xin chào {$first_name}, chúng tôi đã thêm bạn vào danh sách chờ. Chúng tôi sẽ liên hệ ngay khi có bàn trống theo ngày bạn chọn."
    : "Hi {$first_name}, we've added you to our waitlist. We'll reach out as soon as a table becomes available for your preferred date.";
$lbl_date    = $is_vi ? 'Ngày yêu cầu'  : 'Requested date';
$lbl_time    = $is_vi ? 'Giờ ưu tiên'   : 'Preferred time';
$lbl_party   = $is_vi ? 'Số khách'       : 'Party size';
$note        = $is_vi
    ? 'Chúng tôi sẽ gửi email thông báo ngay khi có bàn trống. Lưu mã này để liên hệ với chúng tôi khi cần.'
    : "We'll send you a notification email the moment a table opens up. Save this reference code in case you need to follow up with us.";
$outro       = $is_vi
    ? 'Cảm ơn sự kiên nhẫn của bạn — chúng tôi mong được đón tiếp bạn.'
    : 'Thank you for your patience — we look forward to welcoming you.';

$content = <<<HTML
<h2>{$h1}</h2>
<p style="margin-bottom:24px;">{$intro}</p>

<div class="ref-badge">{$entry->reference_code}</div>

<div style="margin:24px 0;">
  <div class="detail-row">
    <span class="detail-label">{$lbl_date}</span>
    <span class="detail-value">{$date_fmt}</span>
  </div>
  <div class="detail-row">
    <span class="detail-label">{$lbl_time}</span>
    <span class="detail-value">{$time_fmt}</span>
  </div>
  <div class="detail-row">
    <span class="detail-label">{$lbl_party}</span>
    <span class="detail-value">{$party_label}</span>
  </div>
</div>

<p>{$note}</p>

<p style="margin-top:24px;">{$outro}</p>
HTML;

echo thr_email_wrap( $content, $accent, $logo_url ?? null, $venue_name );
