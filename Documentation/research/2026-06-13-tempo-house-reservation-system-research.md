# Tempo House — Table Reservation System Research
**Date:** 2026-06-13  
**Scope:** Open-source options, SaaS benchmarks, architecture recommendation, Vietnam-specific considerations  
**Method:** 111-agent deep research, 28 sources fetched, 25 claims adversarially verified (14 confirmed, 11 killed)

---

## Executive Summary

No existing open-source WordPress plugin fully meets Tempo House's requirements. Every major plugin either lacks a visual floor plan builder entirely, or gatekeeps basic table management behind expensive paid tiers. **The recommended approach is a custom build**: a Next.js front-end (Konva.js for the floor plan canvas) backed by a WordPress REST API, with TastyIgniter as an optional open-source backend reference, and Bistrochat handling Zalo/WhatsApp/Messenger booking channels.

**SevenRooms is the SaaS benchmark** — it's the closest to the full feature set requested, including pre-shift VIP reports, deposits, minimum spends, and experience-based booking flows. A custom build can match or exceed it specifically for Tempo House's venue context, at zero per-cover SaaS fees.

---

## 1. Open-Source Landscape — Scored Against Requirements

### Verified Findings

| Plugin / Project | Floor Plan Builder | Table Drag-Drop | Multi-Floor | Permissions | API | Verdict |
|---|---|---|---|---|---|---|
| **TastyIgniter** (MIT, Laravel) | ✗ | ✗ (extension unverified) | ✗ | ✓ | ✓ | Best base for custom build |
| **NateWr/restaurant-reservations** | ✗ | ✗ | ✗ (multi-venue only) | ✗ | Limited | Archived 2020, dead end |
| **Five Star Restaurant Reservations** | ✗ | ✗ | ✗ | Basic | Limited | Table dropdown = Ultimate tier only |
| **Amelia** (freemium) | ✗ | ✗ | ✗ | ✓ | ✓ | Service/appointment focus, not restaurant |
| **BirchPress** | ✗ | ✗ | ✗ | Basic | Limited | Abandoned, not suitable |

### Key Verified Claims

- **TastyIgniter** is MIT-licensed, Laravel-based, actively maintained (v4.2.4, June 2026). No native floor plan builder exists, but its extensible architecture is the most viable open-source base. *(confidence: high — GitHub confirmed)*

- **NateWr/restaurant-reservations** is fully archived (last commit 2020). Form-based only. Multi-location support is multi-venue (separate locations), not intra-venue multi-floor. *(confidence: high — GitHub confirmed)*

- **Five Star Restaurant Reservations** has no floor plan builder in any tier. Table selection is a form dropdown gated behind the most expensive Ultimate tier (confirmed v2.7.0, August 2025 — added only SMS sender ID, not layout tools). *(confidence: high — official product page confirmed)*

> **CAUTION (refuted):** TastyIgniter's Reservations extension having dining area/table management and capacity-per-slot settings was not verified — voted 1-2 against. Treat TastyIgniter as a database/auth/API framework only, not as a reservation feature provider.

---

## 2. Feature Gap Analysis

Every open-source option has the same gap: **no visual floor plan builder exists** in any free or freemium WordPress-compatible package. All features requiring spatial layout (drag-drop furniture, color-coded seat status, multi-floor toggle, area combining) require custom development.

### Gap Map

| Required Feature | Available in OSS | Notes |
|---|---|---|
| Upload CAD/PDF floor plan | ✗ | Must build — PDF.js for rendering, Konva.js for overlay |
| Drag-drop tables/furniture | ✗ | Must build — Konva.js recommended |
| Per-furniture capacity + orientation | ✗ | Must build |
| Color-coded time status (green/orange/red) | ✗ | Must build |
| Multi-floor toggle | ✗ | Must build |
| Combine tables/areas | ✗ | Must build |
| Mark unavailable/pre-booked | ✗ | Must build |
| Granular permissions matrix | Partial | TastyIgniter has roles; needs custom granularity |
| Subdomain (reservations.tempohouse.com.vn) | ✓ | Standard WP/Next.js subdomain |
| Email notifications (new/cancel/reminder) | Partial | Most plugins do basic email; templates need custom design |
| Post-visit feedback (Google Forms link) | ✗ | Build as email trigger with embedded Google Forms URL |
| Diner-facing public booking UI | Partial | Needs brand customization |
| Google Maps reservation integration | ✗ | Reserve with Google requires API partner setup |
| Pre-shift reports | ✗ | Must build |
| VIP/event/custom tags | ✗ | Must build |
| Venue/event space booking | ✗ | Must build separately from table reservations |
| Zalo / WhatsApp channels | ✗ | Bistrochat API integration |
| SMS notifications | Partial | Five Star Ultimate only; Twilio integration for custom |
| Deposits / prepayment | ✗ | Must build with VNPay/MoMo/ZaloPay |
| Waitlist management | ✗ | Must build |

---

## 3. SaaS Benchmark — SevenRooms

SevenRooms is the strongest benchmark for Tempo House's use case. Key **verified** capabilities:

| Feature | SevenRooms |
|---|---|
| Pre-shift reports with VIP identification | ✓ Confirmed |
| Deposit collection (partial or full) for large parties / peak times | ✓ Confirmed |
| Minimum spend policies for areas/holidays | ✓ Confirmed |
| Booking flow customization by experience/seating area/party size | ✓ Confirmed |
| PCI-compliant online payment | ✓ Confirmed |
| Auto-tagged guest profiles (VIP, regulars, big spenders) | ✓ Confirmed |
| Flat subscription pricing, no per-cover fees | ✓ Confirmed (~$499+/mo) |

> **CAUTION (refuted):** SevenRooms automated post-visit surveys (1-2), virtual waitlist with SMS Priority Alerts (0-3), and Instagram direct booking (0-3) were NOT verified — do not assume these features exist as described in marketing materials.

### Other SaaS Platforms

- **OpenTable**: Per-cover network fees not independently verified at specific dollar amounts. Guest-facing UI is OpenTable-branded, not venue-branded — a key reason to self-host.
- **Resy OS**: Customizable floor plans and dynamic pricing claims were refuted (0-3) — could not verify these as distinct advantages over competitors.
- **Tock**: Best for prepaid/ticketed experiences and fixed menus — relevant if Tempo House runs tasting menu nights or ticketed events.
- **Tablein / ResDiary / Quandoo**: Not independently verified in this research sweep; mid-tier options without the depth of SevenRooms.

---

## 4. Architecture Recommendation

### Recommended Stack

```
reservations.tempohouse.com.vn
├── Next.js 14+ App Router (front-end + API routes)
│   ├── Konva.js — floor plan canvas (confirmed HTML5 Canvas, polygonal shapes over background image, drag-drop, hover, layers)
│   ├── PDF.js — render uploaded CAD/PDF floor plan as canvas background
│   ├── GoJS — seating assignment drag-drop (COMMERCIAL LICENSE REQUIRED — see caveat below)
│   └── React Email — transactional email templates
├── WordPress REST API (headless, existing WP install)
│   ├── Custom CPTs: reservations, floor_plans, furniture_objects, shifts
│   └── Custom user roles: admin, manager, floor_staff, host
└── Bistrochat — Zalo / WhatsApp / Messenger / LINE booking channels
```

> **GoJS caveat:** GoJS is source-available but **not open-source for commercial use**. A commercial license is required for production deployment. Evaluate cost before committing. Alternative: **Fabric.js** (MIT) for the canvas layer — verify it supports the same cross-diagram drag-and-drop pattern before implementation.

> **Konva.js React/Vue/Angular compatibility was refuted (0-3).** The `react-konva` wrapper may work but must be independently verified against current docs before use in Next.js. The vanilla JS Konva Interactive Building Map sandbox is confirmed to work.

### Database Schema (WordPress custom tables or CPTs)

```sql
-- Core tables (implement as WP custom tables for performance)

wp_th_floor_plans
  id, name, floor_number, background_url (PDF/image), width_px, height_px, is_active, created_at

wp_th_furniture
  id, floor_plan_id, type (table|booth|bar_stool|lounge|high_top|banquette|outdoor_table|stage|dj_booth),
  label, x, y, rotation, capacity_min, capacity_max, orientation_deg, is_available, meta_json

wp_th_reservations
  id, reference_code, diner_name, diner_email, diner_phone, party_size,
  reservation_datetime (UTC), floor_plan_id, furniture_ids (JSON array),
  status (pending|confirmed|seated|completed|cancelled|no_show),
  type (dinner|bar|event|corporate|birthday|custom), tags (JSON array),
  is_vip, notes_internal, notes_diner, deposit_amount, deposit_paid,
  created_by (user_id), created_at, updated_at

wp_th_shifts
  id, shift_name (lunch|dinner|late_night|event), start_time, end_time,
  report_generated_at, report_url

wp_th_availability_blocks
  id, furniture_id, floor_plan_id, blocked_from, blocked_to,
  reason (maintenance|pre_booked|event|private_hire), created_by

wp_th_users (extends WP users)
  user_id, role (admin|manager|floor_staff|host),
  permissions_json (granular feature flags)
```

### Permissions Matrix Design

Modeled on SevenRooms/OpenTable but simplified for a 3-role venue:

| Permission | Admin | Manager | Floor Staff / Host |
|---|---|---|---|
| View floor plan (live status) | ✓ | ✓ | ✓ |
| Create/edit reservations | ✓ | ✓ | Create only |
| Cancel reservations | ✓ | ✓ | ✗ |
| Mark VIP | ✓ | ✓ | ✗ |
| Edit floor plan layout | ✓ | ✗ | ✗ |
| Add/remove furniture | ✓ | ✗ | ✗ |
| Block tables/areas/floors | ✓ | ✓ | ✗ |
| View pre-shift report | ✓ | ✓ | ✓ |
| Generate/schedule report | ✓ | ✓ | ✗ |
| Manage users/roles | ✓ | ✗ | ✗ |
| View deposits | ✓ | ✓ | ✗ |
| Process deposits | ✓ | ✓ | ✗ |
| Access admin settings | ✓ | ✗ | ✗ |

All permissions should be stored as a JSON feature flags array per user, allowing granular enable/disable on top of the base role matrix.

### Floor Plan Builder — Implementation Notes

1. **Upload**: Accept PDF or image (PNG/JPG). Use PDF.js to render the first page of a PDF as a canvas image at the correct aspect ratio.
2. **Canvas layer (Konva.js)**: Load the rendered floor plan as a background image. Draw furniture objects as Konva shapes on a separate layer. Each shape stores `x, y, rotation, capacity, type`.
3. **Furniture types to include**: round table (2/4/6/8-top), rectangular table (2/4/6/8-top), booth (2/4-top), bar stool (single), bar counter segment, lounge sofa, lounge armchair, high-top table (2/4), banquette, outdoor table, stage, DJ booth.
4. **Color-coded status**: Green = available, Orange = seated 0–45 min (configurable), Red = seated 45–90 min (configurable), Grey = unavailable/blocked. Poll reservation status every 30s on the backend view.
5. **Multi-floor**: Tabs or dropdown selector to switch between floor plans. Admin can toggle floors on/off for a given service period.

---

## 5. Email Notifications — Design & Timing

### Email Flow (industry standard + Tempo House extensions)

| Email | Trigger | Key Content |
|---|---|---|
| **Booking Confirmation** | Immediately on confirmation | Reference code, date/time, party size, location/address, Google Maps link, what to expect, cancellation policy |
| **48-Hour Reminder** | 48 hrs before reservation | Same details + parking/transport reminder + dress code if applicable |
| **Same-Day Reminder** | 4 hrs before | Abbreviated — "See you tonight at 7pm, [Name]" |
| **Cancellation** | On cancellation | Acknowledgment + rebooking CTA |
| **No-Show Follow-up** | 2 hrs after no-show | Gentle "we missed you" + rebooking link |
| **Post-Visit Feedback** | 2–4 hrs after reservation end | Google Forms link for feedback, optional Google Review CTA |

### Post-Visit Feedback — Google Forms Integration

1. Create a Google Form with: Overall experience (1-5), Food quality (1-5), Service (1-5), Ambiance (1-5), Would you return? (Y/N), Comments (text), Name (optional).
2. Pre-fill the form URL with the reservation reference code using Google Forms' `entry.XXXXXX` query parameter pattern — this links feedback to the specific reservation automatically.
3. Include the Google Review link as a secondary CTA below the form link (not the primary — avoid looking transactional).
4. Email subject line formula: *"How was your evening at Tempo House, [First Name]?"*

### Tempo House Email Brand Design Principles

- Dark background (#0D0D0D or #111111) matching site palette
- Single accent color (gold/amber from brand system)
- Minimal copy — venue photography hero image at top
- All CTAs as ghost buttons (border only, no fill) — consistent with brand aesthetic
- Footer: address, phone, social links, unsubscribe
- Use Resend or Postmark as transactional email service (both have WP + Next.js SDKs)

---

## 6. Vietnam-Specific Considerations

### Zalo Integration

- **Bistrochat** is the most viable integration path. Confirmed to support Zalo as 1 of 11 booking channels (alongside WhatsApp, LINE, WeChat, Instagram, Facebook, Telegram, Messenger, Website, Google Reserve, OpenRice). Explicitly positioned for Southeast Asian markets including Vietnam.
- **Zalo Notification Service (ZNS)** has an official API (`developers.zalo.me/docs/zalo-notification-service`) for transactional push notifications — reservation confirmations, reminders. Requires Zalo Official Account registration (OA).
- **Zalo Official Account**: Required for ZNS. Register at id.zalo.me. OA verification takes 3–5 business days.

> **CAUTION:** Bistrochat supporting 16 languages including Vietnamese was refuted (0-3). Verify language support directly with Bistrochat before committing.

### Payment Gateways for Deposits

Vietnam-relevant options for deposit/prepayment flows:
- **VNPay**: Dominant in Vietnam, supports QR, ATM, credit card. Has WooCommerce/WP plugin.
- **MoMo**: Vietnam's largest e-wallet. REST API available, widely used for restaurant/event deposits.
- **ZaloPay**: Integrated with Zalo ecosystem — logical choice if using ZNS for notifications. REST API available.
- **Stripe**: Works in Vietnam for foreign cards, but not widely used by local diners.

Recommended: Offer MoMo + VNPay as primary, ZaloPay as Zalo-channel-specific option.

### Timezone

All server-side datetimes store as UTC. All display, scheduling, and notification triggers must convert to `Asia/Ho_Chi_Minh` (UTC+7, no DST). Use `Intl.DateTimeFormat` with explicit timezone in Next.js; use `wp_timezone()` in WordPress custom code.

### Language

- Public-facing diner UI: Vietnamese + English toggle (minimum)
- Admin/manager back-end: English only is acceptable initially
- Email templates: Vietnamese + English versions based on diner locale preference (store on reservation record)

---

## 7. Missing Scope — Gaps Not in the Original Brief

The following features are standard in SevenRooms, Tock, and similar platforms and should be considered for Tempo House:

### High Priority (build in V1)

| Feature | Why It Matters |
|---|---|
| **Waitlist management** | Walk-ins are common at bar/supper club venues. Manage via QR check-in. |
| **Deposit / credit card hold** | Protects against no-shows for peak nights and event bookings. |
| **Cancellation policy enforcement** | Define cancellation window (e.g. 24hr) — charge card hold on late cancel. |
| **Minimum spend policy** | Per-area (e.g. VIP lounge requires minimum spend). Standard at supper clubs. |

### Medium Priority (V2)

| Feature | Why It Matters |
|---|---|
| **POS integration** | Sync reservation status to POS (e.g. MISA, Sapo POS — common in Vietnam) so staff see orders against table. |
| **Kitchen prep time buffers** | Set per-reservation-type buffer (e.g. event setup = 30min before) to prevent double-booking during setup. |
| **Guest CRM / profile** | Track visit history, preferences, allergies, spend per guest. Enables VIP auto-tagging. |
| **Corporate / group booking request flow** | Larger groups (10+) need a separate inquiry form rather than direct booking — prevents over-committing floor capacity. |
| **Event ticketing** | For Tempo House's live music / DJ nights — sell fixed-price tickets + include a table (Tock model). |

### Lower Priority (V3+)

| Feature | Why |
|---|---|
| **Google Reserve (Reserve with Google)** | Enables booking from Google Search/Maps directly. Requires API partner application to Google. |
| **Loyalty / repeat guest rewards** | Points system or "regular recognition" — SevenRooms auto-tags; custom build can do the same via visit count. |
| **Capacity override for fire code** | Hard cap per floor plan — reject reservations once capacity reached, regardless of available tables. |
| **Weather / force majeure cancellation** | Bulk-cancel + notification flow for outdoor areas during typhoon/heavy rain season. |
| **Dynamic pricing / peak-night surcharges** | Tock's model — premium for Friday/Saturday. Not standard in Vietnam yet, but worth the architecture hook. |
| **Staff scheduling integration** | Pre-shift report hooks into staffing (know covers per section → assign staff counts). |

---

## 8. Open Questions (Must Answer Before Architecture Lockdown)

1. **GoJS commercial license cost** — GoJS requires a commercial license for production. Evaluate cost vs Fabric.js (MIT) as an alternative for the drag-and-drop seating assignment canvas.

2. **Bistrochat white-label / API mode** — Does Bistrochat allow a white-label embedded widget at `reservations.tempohouse.com.vn`, or does it require a Bistrochat-hosted URL? Subdomain branding is a hard requirement.

3. **TastyIgniter Reservations extension (v4.x)** — The claim that TastyIgniter's extension ecosystem has table management and capacity-per-slot was refuted. Verify what the v4.x extension marketplace actually provides before using as a base.

4. **Vietnam payment law** — Confirm whether restaurant deposit collection requires a formal payment license (Nghị định 101/2012/NĐ-CP and amendments). MoMo/VNPay are licensed; self-processing may not be.

5. **Zalo OA verification timeline** — Factor 3–5 business day OA verification into launch planning if ZNS notifications are in V1 scope.

---

## 9. Recommended Build Phases

### Phase 1 — Core Reservation Engine (Weeks 1–6)
- WordPress custom tables + REST API endpoints
- Next.js booking form (diner-facing, mobile-first)
- Email notifications (confirmation, reminder, cancellation) via Resend
- Basic admin table list view (no visual floor plan yet)
- Admin + Manager + Staff roles with permission matrix
- Subdomain DNS + auth (NextAuth.js with WordPress user sync)

### Phase 2 — Visual Floor Plan Builder (Weeks 7–12)
- Konva.js floor plan canvas (vanilla JS, verify react-konva separately)
- PDF/image floor plan upload (PDF.js)
- Furniture library (all types listed above)
- Color-coded real-time table status
- Multi-floor tab switcher
- Table blocking / availability management

### Phase 3 — Venue + Event Booking (Weeks 13–18)
- Venue/event space booking module (separate from table reservations)
- Deposit + credit card hold (MoMo / VNPay integration)
- Pre-shift report generator (PDF export, scheduled email)
- Waitlist management
- Post-visit Google Forms feedback email

### Phase 4 — Channels + CRM (Weeks 19–24)
- Bistrochat integration (Zalo + WhatsApp + Messenger)
- Zalo ZNS transactional notifications
- Guest CRM (profile, visit history, VIP auto-tagging)
- Google Reserve API application + integration

---

## Sources

| URL | Confidence | Angle |
|---|---|---|
| github.com/tastyigniter/TastyIgniter | Primary ✓ | Open-source landscape |
| github.com/NateWr/restaurant-reservations | Primary ✓ | Open-source landscape |
| fivestarplugins.com/plugins/five-star-restaurant-reservations | Primary ✓ | Open-source landscape |
| sevenrooms.com/platform/table-management | Primary ✓ | SaaS benchmark |
| sevenrooms.com/platform/events-experiences | Primary ✓ | SaaS benchmark |
| konvajs.org/docs/sandbox/Interactive_Building_Map.html | Primary ✓ | Technical architecture |
| github.com/konvajs/konva | Primary ✓ | Technical architecture |
| gojs.net/latest/samples/seatingChart.html | Primary ✓ | Technical architecture |
| bistrochat.com | Primary ✓ | Vietnam / Zalo integration |
| developers.zalo.me/docs/zalo-notification-service | Primary (blocked) | Vietnam / Zalo |
| infobip.com/blog/zalo-business | Blog | Vietnam / Zalo |
| sevenrooms.com/blog/restaurant-reservation-confirmation-email-tips | Blog | Email design |
| tableo.com/restaurant-tips/restaurant-reservation-confirmation-email | Blog | Email design |

*Full source list: 28 fetched, quality ratings in raw output at `/tasks/w0thqakhl.output`*
