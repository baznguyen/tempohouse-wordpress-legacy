<?php defined( 'ABSPATH' ) || exit;
require_once THR_PLUGIN_DIR . 'templates/emails/base.php';

// $enquiry   — object from thr_event_enquiries
// $internal  — bool: true = venue notification copy

$first_name  = explode( ' ', $enquiry->contact_name )[0];
$event_labels = [
    'corporate'        => 'Corporate dinner',
    'product_launch'   => 'Product launch',
    'brand_activation' => 'Brand activation',
    'birthday'         => 'Birthday celebration',
    'anniversary'      => 'Anniversary',
    'team_event'       => 'Team event',
    'other'            => 'Other',
];
$event_type_label = $event_labels[ $enquiry->event_type ] ?? ucfirst( str_replace( '_', ' ', $enquiry->event_type ) );
$preferred_date   = $enquiry->preferred_date ? date( 'l, F j, Y', strtotime( $enquiry->preferred_date ) ) : 'To be confirmed';
$catering         = $enquiry->catering_needed ? 'Yes' : 'No';
$budget           = $enquiry->budget_range ?: 'Not specified';
$company_line     = $enquiry->company_name ? "<div class=\"detail-row\"><span class=\"detail-label\">Company</span><span class=\"detail-value\">" . esc_html( $enquiry->company_name ) . "</span></div>" : '';
$notes_line       = $enquiry->notes ? "<div style=\"margin:16px 0;padding:12px 16px;background:rgba(221,170,98,0.04);border-left:2px solid rgba(221,170,98,0.3);font-size:13px;line-height:1.6;color:rgba(247,243,238,0.7);\">" . esc_html( $enquiry->notes ) . "</div>" : '';

if ( $internal ) {
    // Internal venue notification
    $h1    = 'New Event Enquiry';
    $intro = "A new private event enquiry has been submitted via the website. Reference: <strong>{$enquiry->reference_code}</strong>.";
    $cta   = '';
} else {
    // Auto-reply to enquirer
    $h1    = "Thank you, {$first_name}";
    $intro = "We've received your private event enquiry and our events team will be in touch within 24 hours to discuss your requirements and availability at TEMPO House.";
    $cta   = <<<CTA
<div style="margin:28px 0;">
  <p style="font-size:14px;color:rgba(247,243,238,0.6);">In the meantime, feel free to reach us directly at <a href="mailto:{$venue_email}" style="color:#DDAA62;">{$venue_email}</a> — please quote your reference code in your message.</p>
</div>
CTA;
}

$details_html = <<<DETAIL
<div style="margin:24px 0;">
  <div class="detail-row">
    <span class="detail-label">Reference</span>
    <span class="detail-value">{$enquiry->reference_code}</span>
  </div>
  <div class="detail-row">
    <span class="detail-label">Event type</span>
    <span class="detail-value">{$event_type_label}</span>
  </div>
  <div class="detail-row">
    <span class="detail-label">Preferred date</span>
    <span class="detail-value">{$preferred_date}</span>
  </div>
  <div class="detail-row">
    <span class="detail-label">Guest count</span>
    <span class="detail-value">{$enquiry->guest_count}</span>
  </div>
  <div class="detail-row">
    <span class="detail-label">Budget range</span>
    <span class="detail-value">{$budget}</span>
  </div>
  <div class="detail-row">
    <span class="detail-label">Catering needed</span>
    <span class="detail-value">{$catering}</span>
  </div>
  {$company_line}
</div>
{$notes_line}
DETAIL;

$capacity_note = $internal ? '' : <<<CAP
<div style="margin:24px 0;padding:16px 20px;border:1px solid rgba(221,170,98,0.15);border-radius:4px;background:rgba(221,170,98,0.04);">
  <p style="margin:0 0 8px;font-size:13px;font-weight:600;color:#DDAA62;">About TEMPO House</p>
  <p style="margin:0;font-size:13px;line-height:1.6;color:rgba(247,243,238,0.65);">
    TEMPO House is a premium event venue in Ho Chi Minh City, offering dedicated spaces for intimate gatherings of 20 to large-scale events of 200+ guests. Our team will prepare a tailored proposal based on your requirements.
  </p>
</div>
CAP;

$content = <<<HTML
<h2>{$h1}</h2>
<p style="margin-bottom:24px;">{$intro}</p>
<div class="ref-badge">{$enquiry->reference_code}</div>
{$details_html}
{$capacity_note}
{$cta}
<p style="font-size:12px;color:rgba(247,243,238,0.3);margin-top:24px;">We aim to respond to all enquiries within 24 hours on business days.</p>
HTML;

echo thr_email_wrap( $content, $accent, $logo_url ?? null, $venue_name );
