<?php defined( 'ABSPATH' ) || exit;
require_once THR_PLUGIN_DIR . 'templates/emails/base.php';

$date_display = date( 'l, F j, Y', strtotime( $date_local ) );
$rows_html    = '';

foreach ( $rows as $r ) {
    $time    = esc_html( substr( $r->dt_local, 11, 5 ) );
    $name    = esc_html( $r->diner_name ) . ( $r->is_vip ? ' <span style="color:#B8860B;font-size:11px;letter-spacing:0.1em;text-transform:uppercase;">VIP</span>' : '' );
    $party   = (int) $r->party_size;
    $occ     = esc_html( ucfirst( $r->occasion ) );
    $notes   = $r->notes_diner ? esc_html( $r->notes_diner ) : '';
    $tags    = $r->tag_names ? '<span style="color:rgba(221,170,98,0.7);font-size:11px;">' . esc_html( $r->tag_names ) . '</span>' : '';
    $status  = match( $r->status ) {
        'confirmed' => '<span style="color:#DDAA62;">Confirmed</span>',
        'seated'    => '<span style="color:#e74c3c;">Seated</span>',
        default     => esc_html( ucfirst( $r->status ) ),
    };
    $zalo = isset( $r->diner_zalo ) && $r->diner_zalo ? esc_html( $r->diner_zalo ) : '';

    $rows_html .= <<<ROW
  <tr>
    <td style="padding:10px 8px;border-bottom:1px solid rgba(247,243,238,0.08);color:#F7F3EE;font-weight:600;white-space:nowrap;">{$time}</td>
    <td style="padding:10px 8px;border-bottom:1px solid rgba(247,243,238,0.08);">{$name}{$tags}</td>
    <td style="padding:10px 8px;border-bottom:1px solid rgba(247,243,238,0.08);color:#F7F3EE;text-align:center;">{$party}</td>
    <td style="padding:10px 8px;border-bottom:1px solid rgba(247,243,238,0.08);color:rgba(247,243,238,0.6);">{$occ}</td>
    <td style="padding:10px 8px;border-bottom:1px solid rgba(247,243,238,0.08);">{$status}</td>
    <td style="padding:10px 8px;border-bottom:1px solid rgba(247,243,238,0.08);color:rgba(247,243,238,0.55);font-size:13px;">{$notes}</td>
    <td style="padding:10px 8px;border-bottom:1px solid rgba(247,243,238,0.08);color:rgba(247,243,238,0.55);font-size:13px;">{$zalo}</td>
  </tr>
ROW;
}

$count_rows = count( $rows );

$table_html = $rows_html
    ? <<<TABLE
<table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin-top:24px;">
  <thead>
    <tr style="background:rgba(221,170,98,0.08);">
      <th style="padding:8px;text-align:left;color:rgba(247,243,238,0.4);font-size:11px;letter-spacing:0.08em;text-transform:uppercase;border-bottom:1px solid rgba(247,243,238,0.12);">Time</th>
      <th style="padding:8px;text-align:left;color:rgba(247,243,238,0.4);font-size:11px;letter-spacing:0.08em;text-transform:uppercase;border-bottom:1px solid rgba(247,243,238,0.12);">Guest</th>
      <th style="padding:8px;text-align:center;color:rgba(247,243,238,0.4);font-size:11px;letter-spacing:0.08em;text-transform:uppercase;border-bottom:1px solid rgba(247,243,238,0.12);">Pax</th>
      <th style="padding:8px;text-align:left;color:rgba(247,243,238,0.4);font-size:11px;letter-spacing:0.08em;text-transform:uppercase;border-bottom:1px solid rgba(247,243,238,0.12);">Occasion</th>
      <th style="padding:8px;text-align:left;color:rgba(247,243,238,0.4);font-size:11px;letter-spacing:0.08em;text-transform:uppercase;border-bottom:1px solid rgba(247,243,238,0.12);">Status</th>
      <th style="padding:8px;text-align:left;color:rgba(247,243,238,0.4);font-size:11px;letter-spacing:0.08em;text-transform:uppercase;border-bottom:1px solid rgba(247,243,238,0.12);">Notes</th>
      <th style="padding:8px;text-align:left;color:rgba(247,243,238,0.4);font-size:11px;letter-spacing:0.08em;text-transform:uppercase;border-bottom:1px solid rgba(247,243,238,0.12);">Zalo</th>
    </tr>
  </thead>
  <tbody>
    {$rows_html}
  </tbody>
</table>
TABLE
    : '<p style="color:rgba(247,243,238,0.4);">No reservations for this date.</p>';

$content = <<<HTML
<h2>Shift Report — {$date_display}</h2>

<div style="display:flex;gap:24px;margin:24px 0;">
  <div style="background:rgba(221,170,98,0.08);border:1px solid rgba(221,170,98,0.15);border-radius:4px;padding:16px 24px;text-align:center;">
    <div style="font-size:28px;font-weight:700;color:#DDAA62;">{$covers}</div>
    <div style="font-size:11px;letter-spacing:0.1em;text-transform:uppercase;color:rgba(247,243,238,0.4);margin-top:4px;">Covers</div>
  </div>
  <div style="background:rgba(221,170,98,0.08);border:1px solid rgba(221,170,98,0.15);border-radius:4px;padding:16px 24px;text-align:center;">
    <div style="font-size:28px;font-weight:700;color:#F7F3EE;">{$count_rows}</div>
    <div style="font-size:11px;letter-spacing:0.1em;text-transform:uppercase;color:rgba(247,243,238,0.4);margin-top:4px;">Reservations</div>
  </div>
  <div style="background:rgba(184,134,11,0.08);border:1px solid rgba(184,134,11,0.2);border-radius:4px;padding:16px 24px;text-align:center;">
    <div style="font-size:28px;font-weight:700;color:#B8860B;">{$vip_count}</div>
    <div style="font-size:11px;letter-spacing:0.1em;text-transform:uppercase;color:rgba(247,243,238,0.4);margin-top:4px;">VIP</div>
  </div>
</div>

{$table_html}
HTML;

echo thr_email_wrap( $content, $accent, $logo_url ?? null, $venue_name );
