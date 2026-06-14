<?php defined( 'ABSPATH' ) || exit;
require_once THR_PLUGIN_DIR . 'templates/emails/base.php';

$is_vi      = ( $r->diner_lang ?? '' ) === 'vi';
$first_name = explode( ' ', $r->diner_name )[0];

$h1       = $is_vi ? 'Buổi tối của bạn thế nào?' : 'How was your evening?';
$intro    = $is_vi
    ? "Xin chào {$first_name}, chúng tôi hy vọng bạn đã có một buổi tối tuyệt vời tại TEMPO House. Chúng tôi rất muốn nghe ý kiến của bạn."
    : "Hi {$first_name}, we hope you had a wonderful time at TEMPO House last night. We'd love to hear what you thought.";
$body     = $is_vi
    ? 'Phản hồi của bạn giúp chúng tôi cải thiện và có ý nghĩa rất lớn với đội ngũ của chúng tôi.'
    : 'Your feedback helps us improve and means a lot to our team.';
$btn_fb   = $is_vi ? 'Chia sẻ phản hồi'    : 'Share Your Feedback';
$btn_goog = $is_vi ? 'Đánh giá trên Google' : 'Leave a Google Review';
$lbl_ref  = $is_vi ? 'Mã đặt bàn'           : 'Reference';

$feedback_block = $feedback_url
    ? "<div style=\"margin-top:8px;\"><a href=\"{$feedback_url}\" class=\"cta-btn\">{$btn_fb}</a></div>"
    : '';

$review_block = $google_rev_url
    ? "<div style=\"margin-top:12px;\"><a href=\"{$google_rev_url}\" class=\"cta-btn secondary\">{$btn_goog}</a></div>"
    : '';

$content = <<<HTML
<h2>{$h1}</h2>
<p>{$intro}</p>

<p>{$body}</p>

{$feedback_block}
{$review_block}

<p style="margin-top:28px;font-size:13px;color:rgba(247,243,238,0.4);">
  {$lbl_ref}: {$r->reference_code}
</p>
HTML;

echo thr_email_wrap( $content, $accent, $logo_url ?? null, $venue_name );
