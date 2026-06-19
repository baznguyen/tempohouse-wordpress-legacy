<?php
// Base email layout — included by other templates via thr_email_wrap()
// Not called directly.
defined( 'ABSPATH' ) || exit;

function thr_email_wrap( string $content, string $accent = '#DDAA62', ?string $logo_url = null, string $venue_name = 'TEMPO House' ): string {
    $logo_block = $logo_url
        ? "<img src=\"$logo_url\" alt=\"$venue_name\" width=\"120\" style=\"display:block;margin:0 auto 24px;\">"
        : "<span style=\"font-family:'Georgia',serif;font-size:22px;letter-spacing:0.12em;color:#F7F3EE;text-transform:uppercase;\">$venue_name</span>";

    // Optional WhatsApp link in footer
    $wa_number  = THR_Settings::get( 'venue_whatsapp', '' );
    $wa_block   = $wa_number
        ? "<p style=\"margin-top:6px;\"><a href=\"https://wa.me/{$wa_number}\" style=\"color:rgba(247,243,238,0.35);font-size:11px;\">WhatsApp us</a></p>"
        : '';

    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="color-scheme" content="dark">
<title>$venue_name</title>
<style>
  body{margin:0;padding:0;background:#0F0E0C;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;}
  a{color:$accent;text-decoration:none;}
  a:hover{text-decoration:underline;}
  .wrap{max-width:580px;margin:0 auto;background:#0F0E0C;}
  .header{padding:40px 32px 24px;text-align:center;border-bottom:1px solid rgba(247,243,238,0.10);}
  .body{padding:40px 32px;}
  .detail-row{display:flex;padding:10px 0;border-bottom:1px solid rgba(247,243,238,0.07);}
  .detail-label{width:140px;color:rgba(247,243,238,0.45);font-size:12px;letter-spacing:0.08em;text-transform:uppercase;padding-top:2px;flex-shrink:0;}
  .detail-value{color:#F7F3EE;font-size:15px;font-weight:500;}
  .ref-badge{display:inline-block;background:rgba(221,170,98,0.12);border:1px solid rgba(221,170,98,0.3);border-radius:4px;padding:6px 16px;font-size:18px;font-weight:700;letter-spacing:0.12em;color:$accent;margin:16px 0;}
  .cta-btn{display:inline-block;padding:14px 32px;border:1px solid $accent;color:$accent;font-size:13px;letter-spacing:0.12em;text-transform:uppercase;border-radius:2px;margin-top:24px;text-decoration:none;}
  .cta-btn:hover{background:rgba(221,170,98,0.10);}
  .cta-btn.secondary{border-color:rgba(247,243,238,0.2);color:rgba(247,243,238,0.6);}
  .policy{font-size:12px;color:rgba(247,243,238,0.35);line-height:1.6;margin-top:24px;padding-top:16px;border-top:1px solid rgba(247,243,238,0.06);}
  .footer{padding:24px 32px;border-top:1px solid rgba(247,243,238,0.08);text-align:center;}
  .footer p{margin:0;font-size:12px;color:rgba(247,243,238,0.3);line-height:1.7;}
  h2{margin:0 0 8px;font-family:'Georgia',serif;font-size:24px;font-weight:400;color:#F7F3EE;letter-spacing:0.02em;}
  p{margin:0 0 12px;color:rgba(247,243,238,0.75);font-size:15px;line-height:1.65;}
  .vip-badge{display:inline-block;background:rgba(184,134,11,0.15);border:1px solid rgba(184,134,11,0.3);color:#B8860B;font-size:11px;letter-spacing:0.1em;text-transform:uppercase;padding:2px 8px;border-radius:2px;margin-left:8px;vertical-align:middle;}
  @media only screen and (max-width:600px){
    .body,.header,.footer{padding:28px 20px!important;}
    .detail-row{flex-direction:column;}
    .detail-label{width:auto;margin-bottom:2px;}
  }
</style>
</head>
<body>
<div class="wrap">
  <div class="header">$logo_block</div>
  <div class="body">
    $content
  </div>
  <div class="footer">
    <p>TEMPO House &middot; Ho Chi Minh City, Vietnam</p>
    <p style="margin-top:8px;">
      <a href="https://tempohouse.com.vn" style="color:rgba(247,243,238,0.35);font-size:11px;letter-spacing:0.06em;">tempohouse.com.vn</a>
    </p>
    $wa_block
  </div>
</div>
</body>
</html>
HTML;
}
