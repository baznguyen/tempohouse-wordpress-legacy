<?php defined( 'ABSPATH' ) || exit;
require_once THR_PLUGIN_DIR . 'templates/emails/base.php';

$is_vi      = ( $entry->diner_lang ?? '' ) === 'vi';
$first_name = explode( ' ', $entry->diner_name )[0];
$date_fmt   = date( 'l, F j, Y', strtotime( $entry->requested_date ) );
$party_size = (int) $entry->party_size;
$party_label = $party_size . ( $is_vi ? ' khách' : ( $party_size > 1 ? ' guests' : ' guest' ) );

$h1          = $is_vi ? 'Tin vui — có thể có bàn trống'                                   : 'Good news — a table may be available';
$intro       = $is_vi
    ? "Xin chào {$first_name}, chúng tôi có thể sắp xếp bàn cho bạn vào ngày <strong>{$date_fmt}</strong>."
    : "Hi {$first_name}, we have some availability that might work for you on <strong>{$date_fmt}</strong>.";
$urgency     = $is_vi
    ? 'Vui lòng <strong>liên hệ với chúng tôi sớm nhất có thể</strong> để xác nhận đặt bàn — chỗ có hạn và chúng tôi phục vụ theo thứ tự danh sách chờ.'
    : 'Please call or message us <strong>as soon as possible</strong> to confirm your booking — availability is limited and we work on a first-come, first-served basis for waitlist guests.';
$lbl_date    = $is_vi ? 'Ngày của bạn' : 'Your date';
$lbl_party   = $is_vi ? 'Số khách'     : 'Party size';
$quote_note  = $is_vi
    ? 'Vui lòng cung cấp mã danh sách chờ khi liên hệ.'
    : 'Quote your waitlist reference code when you get in touch.';
$btn_confirm = $is_vi ? 'Xác nhận bàn của tôi' : 'Confirm My Table';
$disclaimer  = $is_vi
    ? 'Sự có mặt của bàn không được đảm bảo cho đến khi được xác nhận với đội ngũ chúng tôi. Nếu bạn không còn cần bàn, không cần thực hiện thêm bất kỳ hành động nào.'
    : 'Availability is not guaranteed until confirmed with our team. If you no longer require a table, no action is needed.';

$content = <<<HTML
<h2>{$h1}</h2>
<p style="margin-bottom:24px;">{$intro}</p>

<p>{$urgency}</p>

<div class="ref-badge">{$entry->reference_code}</div>

<div style="margin:24px 0;">
  <div class="detail-row">
    <span class="detail-label">{$lbl_date}</span>
    <span class="detail-value">{$date_fmt}</span>
  </div>
  <div class="detail-row">
    <span class="detail-label">{$lbl_party}</span>
    <span class="detail-value">{$party_label}</span>
  </div>
</div>

<p>{$quote_note}</p>

<div style="margin-top:24px;">
  <a href="{$booking_url}" class="cta-btn">{$btn_confirm}</a>
</div>

<p style="margin-top:24px;color:rgba(247,243,238,0.45);font-size:13px;">
  {$disclaimer}
</p>
HTML;

echo thr_email_wrap( $content, $accent, $logo_url ?? null, $venue_name );
