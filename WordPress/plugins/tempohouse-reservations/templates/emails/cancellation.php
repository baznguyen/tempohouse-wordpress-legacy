<?php defined( 'ABSPATH' ) || exit;
require_once THR_PLUGIN_DIR . 'templates/emails/base.php';

$is_vi      = ( $r->diner_lang ?? '' ) === 'vi';
$first_name = explode( ' ', $r->diner_name )[0];
$book_url   = home_url( '/reservations/' );

$h1          = $is_vi ? 'Đặt bàn đã bị hủy'                                                             : 'Reservation cancelled';
$intro       = $is_vi
    ? "Xin chào {$first_name}, đặt bàn của bạn tại TEMPO House đã được hủy."
    : "Hi {$first_name}, your reservation at TEMPO House has been cancelled.";
$lbl_was     = $is_vi ? 'Đã đặt cho'  : 'Was booked for';
$lbl_guests  = $is_vi ? 'Số khách'    : 'Guests';
$outro       = $is_vi ? 'Chúng tôi hy vọng sẽ gặp lại bạn sớm.' : 'We hope to see you again soon.';
$btn_book    = $is_vi ? 'Đặt bàn lại' : 'Book Again';

$content = <<<HTML
<h2>{$h1}</h2>
<p>{$intro}</p>

<div class="ref-badge" style="opacity:0.5;">{$r->reference_code}</div>

<div style="margin:24px 0;">
  <div class="detail-row">
    <span class="detail-label">{$lbl_was}</span>
    <span class="detail-value">{$date_local} at {$time_local}</span>
  </div>
  <div class="detail-row">
    <span class="detail-label">{$lbl_guests}</span>
    <span class="detail-value">{$r->party_size}</span>
  </div>
</div>

<p>{$outro}</p>

<div style="margin-top:8px;">
  <a href="{$book_url}" class="cta-btn">{$btn_book}</a>
</div>
HTML;

echo thr_email_wrap( $content, $accent, $logo_url ?? null, $venue_name );
