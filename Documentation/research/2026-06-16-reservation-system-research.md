# TEMPO House — Reservation System Research
**Date:** 2026-06-16  
**Method:** 8-agent parallel research — industry + competitor analysis, 5 stakeholder persona panels, Gru synthesis  
**Scope:** Table reservation systems, private event funnels, FOH ops tools, Vietnam-specific requirements

---

## Executive Summary

TEMPO House has a **partially-built table reservation system** (REST API, booking form, cron reminders, waitlist, floor plan builder) and **zero operational private events funnel**. Every CTA on the events pages links to `/event-enquiry` — a URL with no page template, no form handler, no database table, and no email workflow. This is the single highest-priority revenue gap: private events (20–150 guests) are the highest-margin use of the space and they currently route to a dead end.

The table reservation system is architecturally sound but has three critical defects that must be fixed before launch:
1. Wrong reminder cadence (4h instead of 2h)
2. Wrong shift-report time (22:00 instead of 15:00)
3. `deposit_amount`/`deposit_paid` fields exist in the schema but are never set, enforced, or surfaced

**Recommended build:** Two-track. Track A patches the existing reservation system's operational gaps (1–2 sprints). Track B builds the private events enquiry funnel from scratch as a parallel system (2–3 sprints).

---

## 1. Industry Landscape

### Must-Have Features (Vietnam/HCMC market)

| Feature | Rationale |
|---------|-----------|
| Direct website booking widget | 65% of diners book on the venue's own site |
| Mobile-first design | Vietnam mobile internet >65% of digital activity |
| **Zalo OA integration** | 75M Vietnamese users — primary business messaging channel; WhatsApp is secondary for expats |
| **MoMo / ZaloPay / VietQR deposit** | Vietnam's dominant payment rails; no Stripe/PayPal |
| Bilingual VI/EN | District 3 clientele mixes locals and expats |
| 48h + 2h reminders | Not 24h + 4h — HCMC guests book same-day at high rates |
| Separate private event enquiry flow | Quote-based workflow, NOT instant-book widget |
| Cancellation policy shown before payment | Surprise fees spike abandonment |
| Deposit enforcement | 92% of restaurants report no-show revenue loss; deposits cut this 50–80% |
| Operator mobile app / responsive dashboard | Floor managers cannot be desk-bound during service |

### Key Industry Patterns

- **Three distinct booking flows:** (1) table reservation — instant self-serve; (2) semi-private / private dining room — enquiry + quote; (3) full venue buyout — sales-led proposal with contract. Conflating these creates drop-off.
- **Operator time savings:** 8–10 hours/week when confirmations, reminders, and waitlist notifications are automated.
- **No-show rates:** 10–25% at premium restaurants without mitigation. Deposits cut this by 50–80%.
- **Guest data ownership:** Venues on OpenTable/Resy don't own the diner relationship data. First-party booking tools are the strategic choice.
- **Same-day booking is the majority pattern:** 66%+ of diners secure a table the day of. Systems must show real-time availability.

### Common Failure Modes

1. Multi-channel booking without a single source of truth → double-booking, data loss
2. No-show losses with no deposit policy (especially dangerous for a limited-cover cocktail bar)
3. Enquiry black hole for private events → auto-acknowledgement within hours is essential
4. Poorly timed reminders (7 days = ignored; 4h = too late for same-day HCMC bookers)
5. Deposit friction late in flow → abandonment
6. Language mismatch (English confirmation to Vietnamese guest → date format confusion → no-show risk)
7. Per-cover fees on third-party platforms eroding event margin

---

## 2. Competitive Landscape

### Platform Summary

| Platform | Best For | Vietnam Presence | Key Risk |
|----------|----------|-----------------|----------|
| OpenTable | Mass-market discovery | ~183 listings, not embedded | $1-1.50/cover compounds; no event workflow |
| Resy + Tock | Premium US independents | None | US-first; no SEA |
| SevenRooms | Enterprise hotels/nightlife | None | $499+/month; too complex for boutique |
| TheFork | European mid-range | None | Exited AU with 2 months notice — reliability risk |
| Chope (now Grab) | SEA casual dining | **Not in Vietnam** | Discount-first; harms premium brand |
| Eatigo (now FunNow) | Off-peak yield mgmt | **Not in Vietnam** | Discount model is incompatible with aspirational positioning |
| TableCheck | Asia-Pacific premium hotels | Emerging SEA | Enterprise pricing; Japan-centric DNA |
| Tagvenue | Event venue marketplace | Singapore only | Commission on confirmed events; no dining integration |

### Critical Market Gap

**No platform serves the hybrid café-by-day, bar-by-night, event-space-on-demand model in a single unified system.** Every platform forces a binary choice between restaurant reservations or event venue bookings.

**Zero viable reservation infrastructure exists in Vietnam for premium independent venues.** The market runs largely on manual WhatsApp/Facebook Messenger flows and phone calls — a completely uncontested digital booking opportunity.

**No platform natively handles Zalo messaging, MoMo/VietQR payment rails, or Vietnamese-language guest journeys.**

---

## 3. Stakeholder Panels

### Bailey — Venue Owner/Operator

**Top priorities:**
- Zero double-booking risk — single source of truth for all channels
- Deposit collection that actually fires — VietQR on confirmation automatically, not as a manual follow-up
- Zalo OA reminders at 24h and 2h (not email-only)
- FOH mobile view: tonight's run-sheet, colour-coded by status, one-tap transitions
- Private event enquiry capturing all qualifying info in one submission

**Critical existing bugs identified:**
- Reminder timing is 24h + 4h (`class-cron.php` lines 72–102) — wrong for HCMC. Should be 24h + 2h
- Shift report defaults to 22:00 (`class-settings.php` line 49) — during service. Should be 15:00
- `deposit_amount` and `deposit_paid` exist in DB but hardcoded to `0.00` and `false` on every insert (`class-api-reservations.php` lines 348–349)
- Waitlist sweep runs `twicedaily` — same-day cancellations at 19:00 may not notify until next day
- No private event enquiry flow as a distinct path — `/event-enquiry` CTA links to nothing
- Email-only reminders — diner_phone captures Zalo number but nothing uses it for outbound Zalo messaging
- No return-guest detection — every reservation treated as first-time

**Dealbreakers:** Account creation required to book / Commission-per-cover models / English-only flows / No mobile FOH view / Deposit as manual staff step / 4h reminder cadence

**Biggest revenue insight:** "The private event funnel is worth 10x fixing the reminder cadence. A 60-person corporate dinner is 20–40M VND and currently has no conversion path. Every such enquiry that gives up walks to SOMA or De La Sol."

---

### Thu — Senior Event Coordinator (Corporate/Social)

**Top priorities:**
- Instant pricing clarity — minimum spend or hire fee BEFORE submitting an enquiry
- Real capacity numbers per layout type (banquet, cabaret, theatre, cocktail)
- AV and technical specs upfront (HDMI, screen size, Wi-Fi capacity)
- 24-hour date hold available without calling
- Named contact, not a shared inbox

**Critical gaps identified:**
- `/event-enquiry` destination does not appear to exist as a built page — every CTA on the site hits a dead link
- Booking widget is table-reservation-only (party size capped at 20, no budget/AV fields, no event type selection)
- No `thr_event_enquiries` database table — a private event enquiry has nowhere to land
- Capacity figures are inconsistent across pages (Gallery L1 says 40 seated in one place, different elsewhere)
- No pricing surfaced anywhere — corporate clients need budget qualifier before investing time
- No downloadable floor plan — "available on request via email" forces an extra exchange before she can brief a designer

**Dealbreaker:** "A coordinator who clicks 'Enquire About Your Event' and lands on a 404 does not call the venue — she moves to the next tab."

---

### Minh — Local Regular Guest, 32, HCMC professional

**Top priorities:**
- Vibe clarity before arrival (café mode vs bar mode on a given night)
- Same-day booking without friction
- Staff recognition on return visits
- Minimum spend transparency upfront, never at the table
- Fast response (under 20 minutes daytime)

**Required channels:** Zalo OA as primary, Instagram DM as secondary, simple web form as fallback  
**Reminders:** Day-before + 2–3 hours before. No more.  
**Cancellation:** One reply or one tap — zero phone calls

**Key insight:** "HCMC no-shows are frequently a design failure, not a culture failure. When cancellation requires a phone call, guests choose silence over friction — the venue built the no-show rate into its own UX."

**Dealbreakers:** Account creation / Phone-only booking or cancellation / >6 form fields / No response within 20 minutes / Deposit on a standard bar table without clear bill-credit mechanism / Same-day booking blocked

---

### David — Corporate Event Booker, Senior Manager, multinational

**Top priorities:**
- Written quote I can forward for internal approval (hire fee, F&B minimum, deposit, cancellation terms — one document)
- Confirmed date hold while in approval
- **VAT-compliant invoice (hóa đơn đỏ) issued in company name** — non-negotiable for expense reimbursement
- Defined AV/setup inclusions
- Named contact who responds within one business day

**Dealbreakers:**
- No structured enquiry form (current `/event-enquiry` does not exist)
- No deposit/contract process visible anywhere in the system
- No VAT invoice capability — nothing in the plugin references tax codes, company billing, or hóa đơn issuance
- Price opacity — no indicative pricing anywhere on the site
- Party size ceiling at 20 in booking widget — corporate events for 40–80 have no submission path

**Preferred flow:** Structured web form → auto-acknowledgement with response time → PDF proposal within 24–48h → deposit invoice with VAT/company fields → bank transfer → signed contract → pre-event logistics email → post-event VAT invoice for final balance

---

### Linh — Front of House Manager

**Top priorities:**
- Shift-start overview: single screen — confirmed, pending, held vs. walkable, VIP flags
- Real-time floor status: which tables are seated / reserved-not-arrived / free
- Late arrival triage built in: prompt at T+15 and T+30 to hold or release
- Guest notes and history visible at check-in before they reach the desk
- Internal comms channel between bar, floor, and front desk tied to reservation

**Key insight:** "The biggest failure mode is the 15-minute gap between when a table turns and when the system reflects it. The reservation system that wins in a live venue is the one staff forget they are using because it fits the physical rhythm of service."

**UX requirements:**
- Dark mode / low-glare — a bright white admin screen at 21:00 in a dimly lit bar is unprofessional
- 48px minimum touch targets — staff are moving fast
- Floor map as PRIMARY screen during service (not buried under list view)
- Guest NAME as largest text on reservation card — not reference code, not time
- Status changes with immediate visual confirmation — no ambiguous spinner
- Search on partial name, phone, AND reference code

**Dealbreakers:** >2 taps to change table status / No mobile view / Locked guest notes field / No Late hold status / No overbooking guard / Generic confirmation emails with no arrival instructions

---

## 4. Synthesis & Recommendations

### Tier 1 — Critical (Ship before launch)

| # | Feature | Stakeholders | Complexity |
|---|---------|-------------|------------|
| 1 | Fix reminder cadence: 4h → 2h in `class-cron.php` + rename `reminder_4h_sent_at` column | Bailey, Minh | Low |
| 2 | Fix shift report default: 22:00 → 15:00 in `class-settings.php` | Bailey, Linh | Low |
| 3 | Deposit enforcement: VietQR URL generator, embed in confirmation email, `deposit_paid` badge in admin | Bailey, David | Medium |
| 4 | **Private event enquiry funnel**: new `thr_event_enquiries` table + REST endpoint + `page-event-enquiry.php` + 2 email templates + admin list with SLA counter | Bailey, Thu, David | High |
| 5 | FOH mobile run-sheet: new WP admin page — chronological list, name primary, dark-mode, tap-to-update, 48px targets, auto-refresh | Bailey, Linh | Medium |
| 6 | Waitlist immediate-fire on cancellation (change from `twicedaily` cron to `on_status_changed` hook) with 2h response window expiry | Bailey, Linh | Low-Medium |
| 7 | Separate Zalo phone field: `diner_zalo` distinct from `diner_phone_primary` — DB migration + form + API + email templates | Bailey, Minh | Low-Medium |

### Tier 2 — Operational Refinements (Post-launch)

- Return guest detection on `public_create()` — email/phone lookup, surface visit count in admin + run-sheet
- No-show auto-flag: cron checks `status=confirmed AND reservation_dt < NOW()-30min AND seated_at IS NULL`
- Walk-in quick-add: admin mobile modal, defaults to now, auto-status=seated, auto-tag=walk-in
- Bilingual form default to Vietnamese: detect `navigator.language`, pre-select VI
- Capacity guard: `venue_capacity` setting + slot-level occupancy check on availability endpoint
- Post-visit NPS routing: ≥4 → Google Review redirect; ≤3 → private capture in `thr_feedback`
- Publish indicative pricing + capacity table on events pages (content update, zero dev time)
- "Running late — hold table" status: pauses no-show auto-flag for 45 minutes
- Timestamped internal notes log (replace `notes_internal` text field with `thr_reservation_notes` table)

### Recommended Build Sequence

**Sprint 1 (1 week) — Critical fixes:**
1. Fix reminder cadence 4h → 2h
2. Fix shift report default 22:00 → 15:00
3. Fix booking form language default → Vietnamese for VI browsers
4. Add distinct `diner_zalo` field

**Sprint 2 (1–2 weeks) — Deposit enforcement:**
1. Add `deposit_rate_weekday` / `deposit_rate_weekend` settings
2. Calculate `deposit_amount` on `public_create()`
3. Build VietQR URL generator (pure PHP, no dependency)
4. Embed VietQR in confirmation email with amount, reference code, bank details
5. Add `deposit_paid` badge to admin list

**Sprint 3 (1–2 weeks) — FOH mobile run-sheet:**
1. New WP admin page under `thr_view_reservations` capability
2. Tonight's reservations chronologically — name as primary text, colour bands, deposit badge
3. Status buttons: Seated / Late / Complete / No-show (48px targets)
4. Walk-in quick-add modal
5. Dark-mode CSS
6. Auto-refresh via `/reservations?date=today` polling

**Sprint 4 (2–3 weeks) — Private event enquiry funnel:**
1. New DB table `thr_event_enquiries`
2. New REST endpoint `POST /public/event-enquiry`
3. New page template `page-event-enquiry.php` — single-page structured form
4. Guest auto-acknowledgement email (bilingual, includes floor plan PDF + AV spec links)
5. Internal notification email to `events@tempohouse.com.vn` (all fields, SLA deadline in subject)
6. Admin 'Event Enquiries' list with SLA countdown and status transitions
7. Verify all five `/event-enquiry` CTAs resolve correctly

**Sprint 5 (1 week) — Operational robustness:**
- No-show auto-flag + Late status (must ship together)
- Return guest detection
- Waitlist immediate-fire on cancellation
- Capacity guard

**Deferred (post-launch):**
- Zalo OA API integration (requires platform registration — initiate in parallel with Sprint 1)
- MoMo deeplink as deposit alternative
- Post-visit NPS routing
- Timestamped notes log
- Capacity analytics, AI demand prediction, Google Reserve integration

---

## 5. Key Risks

| Risk | Severity | Mitigation |
|------|----------|------------|
| Dead `/event-enquiry` link is live in production | **Critical** | Temporary redirect to `events@tempohouse.com.vn` as emergency patch while Sprint 4 builds |
| Deposit vs. same-day booking UX conflict | High | Frame as minimum spend reservation (not deposit); apply only to parties 4+ on Fri/Sat; show in Step 2 summary bar before submit |
| No-show auto-flag without Late status fires cancellation mid-service | High | Ship Late status and no-show flag in same sprint, Late status first |
| FOH adoption failure (system diverges from reality within 30 min) | High | Ship Sprint 3 early; test in real service before Sprint 4; iterate on friction before adding features |
| Zalo OA registration takes 2–4 weeks | Medium | Initiate platform registration now, in parallel with Sprint 1 |
| VAT invoice (hóa đơn đỏ) expectation from corporate clients | Medium | Plugin captures company name and tax code; hóa đơn issued by accounting system — communicate this clearly in admin |
| Capacity figures inconsistent across pages | Medium | Single canonical source (WordPress option or ACF field); resolve before events marketing spend |

---

## 6. Vietnam-Specific Requirements

- **Zalo OA** is the primary outbound messaging channel for Vietnamese guests (75M users). Email is correct for expats. `diner_zalo` field is the schema prerequisite — Zalo OA API integration follows platform approval.
- **VietQR** is the default deposit mechanism — no gateway contract, no per-transaction fee, works with every Vietnamese bank app. MoMo deeplink as secondary.
- **Same-day booking as first-class use case:** `booking_advance_min` defaults to 60 minutes (already set). Never raise this; it kills the local guest use case.
- **Vietnamese date format:** DD/MM/YYYY, not MM/DD/YYYY. Review `class-email.php` date output for `diner_lang=vi`.
- **No account creation — ever.** Guest checkout only. Any future CRM operates on phone/email matching server-side.
- **Business hours SLA:** 09:00–18:00 Mon–Sat. An enquiry submitted at 17:30 Friday has until 10:00 Monday. Naive 4-hour rolling window is wrong.
- **Hóa đơn đỏ (VAT red invoice):** Capture `company_name` and `tax_code` (MST) in the event enquiry form. Issuance is an accounting system function (MISA/FAST) — outside plugin scope, but data must be surfaced.

---

## 7. UX Principles

1. **Gallery restraint over feature density** — fewer elements than a standard restaurant tool, not more
2. **Name as primary identifier** — guest name is the largest, most prominent element in all FOH views
3. **State changes must be immediate and legible** — optimistic UI updates, no ambiguous spinners
4. **Deposit transparency before commitment** — VietQR amount visible in Step 2 summary bar, not a surprise in the confirmation email
5. **Vietnamese-first defaults, English-available** — this is a Vietnamese system with English as secondary, not a bilingual system
6. **Occasion context carries through every touchpoint** — birthday tag in confirmation, reminder, and FOH check-in view
7. **Service mode vs planning mode are different products** — floor plan builder = planning tool; FOH run-sheet = service tool. Never the same screen.
8. **Form fields earn their place** — every field must have a clear operational use. If FOH can't act on it, omit it.
