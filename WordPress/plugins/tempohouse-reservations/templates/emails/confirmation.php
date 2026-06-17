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

// VietQR deposit section
$deposit_html = '';
if ( (float) $r->deposit_amount > 0 && ! $r->deposit_paid ) {
    $bank_id      = THR_Settings::get( 'vietqr_bank_id', 'VCB' );
    $account_no   = THR_Settings::get( 'vietqr_account_no', '' );
    $account_name = THR_Settings::get( 'vietqr_account_name', 'TEMPO House' );
    $amount_raw   = (int) $r->deposit_amount;
    $amount_fmt   = number_format( $amount_raw ) . ' đ';
    $add_info     = urlencode( 'Deposit ' . $r->reference_code );
    $enc_name     = urlencode( $account_name );

    $qr_url   = "https://img.vietqr.io/image/{$bank_id}-{$account_no}-qr_only.png?amount={$amount_raw}&addInfo={$add_info}&accountName={$enc_name}";
    $dep_hd   = $is_vi ? 'Yêu cầu đặt cọc' : 'Deposit Required';
    $dep_note = $is_vi
        ? "Vui lòng chuyển khoản <strong>{$amount_fmt}</strong> để xác nhận chỗ của bạn. Quét mã QR bên dưới hoặc chuyển khoản thủ công."
        : "Please transfer <strong>{$amount_fmt}</strong> to confirm your seat. Scan the QR code below or transfer manually.";
    $dep_confirm = $is_vi
        ? 'Đặt cọc xác nhận chỗ của bạn. Chúng tôi sẽ liên hệ sau khi nhận được.'
        : 'Your deposit confirms your reservation. We will follow up once received.';

    if ( $account_no ) {
        $deposit_html = <<<DEP
<div style="margin:32px 0;padding:20px 24px;border:1px solid rgba(221,170,98,0.3);border-radius:4px;background:rgba(221,170,98,0.04);">
  <h3 style="margin:0 0 8px;font-size:16px;color:#DDAA62;">{$dep_hd}</h3>
  <p style="margin:0 0 16px;font-size:14px;">{$dep_note}</p>
  <div style="text-align:center;margin-bottom:16px;">
    <img src="{$qr_url}" alt="VietQR" width="200" height="200" style="border-radius:4px;background:#fff;padding:8px;">
  </div>
  <div style="font-size:13px;line-height:1.8;">
    <div><strong>Bank:</strong> {$bank_id}</div>
    <div><strong>Account:</strong> {$account_no}</div>
    <div><strong>Name:</strong> {$account_name}</div>
    <div><strong>Amount:</strong> {$amount_fmt}</div>
    <div><strong>Reference:</strong> {$r->reference_code}</div>
  </div>
  <p style="margin:12px 0 0;font-size:12px;color:rgba(247,243,238,0.5);">{$dep_confirm}</p>
</div>
DEP;
    }
}

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

{$deposit_html}

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
