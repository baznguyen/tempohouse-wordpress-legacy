# TEMPO House — Navigation & Information Architecture
**Date:** 9 June 2026
**Prepared by:** Raging Monk AI — Muse (Creative Director)
**Status:** Phase 1 Build Spec — Source of Truth
**Scope:** Desktop nav, drawer, mobile nav, footer, CTA strategy, active states, link label glossary

> This document is the single source of truth for navigation copy and structure in Phase 1. The sitemap (`2026-05-24-tempo-house-sitemap.md`) governs URL strategy and page scope; this document governs what goes in the nav, how it behaves, and exactly what every label says. When in doubt, this file wins.

---

## 1. Navigation Philosophy

### The Da Maria Pattern in the TEMPO Context

The Da Maria three-element masthead — pill trigger left, framed brand name centre, oval CTA right — works for TEMPO because it communicates the same thing the venue communicates: a point of view held with quiet confidence, not decoration. The structure is formal without being stiff. The framed centre name says *we know what we are*. The pill and oval flanking it say *we also know why you're here*.

For TEMPO, this geometry does additional work. The hairline rules extending left and right from the brand name frame — rules that terminate in the MENU pill and RESERVE oval — become a single horizontal composition. A bar. A staff line. A tempo marking. The name isn't floating; it's anchored to both ends of the navigation, and those ends have clear purpose. This is architecture, not layout.

The reference site uses a static tertiary colour for both the pill and oval. TEMPO's version tokens both elements against `--color-text-primary` and `--color-border-strong` in day mode, shifting to `--color-text-primary` (cream) and `--color-border-strong` (cream/8%) in night mode — so the nav deepens with the room rather than remaining a fixed design element floating over a changing palette.

### Day/Night Toggle Integration Decision

The toggle does **not** live in the masthead. Reasons:

1. The three-element masthead is a tight composition — adding a fourth element in the header row breaks the geometry and introduces visual competition with RESERVE.
2. The toggle is a *preference*, not a *destination*. It belongs in the drawer where preferences and secondary navigation live.
3. On mobile, header real estate is already at a premium. A fourth element would require the RESERVE CTA to disappear, which we can't afford.

The toggle lives in the **menu drawer**, positioned above the language toggle, below the main navigation links. It is a first-class UI element in the drawer — not buried in a footer strip.

The auto-mode (HCMC local time — day before 18:00, night from 18:00) means most visitors will never need to touch it. Those who do will find it in the drawer.

---

## 2. Desktop Navigation — Exact Spec

### The Masthead

The masthead is a single horizontal bar, `64px` tall, `position: sticky`, `top: 0`, `z-index: var(--z-nav)`.

**Background:** `var(--color-nav-bg)` — Cream in day (`#F7F3EE`), Ink in night (`#1A1816`).
**Transition:** `background-color 600ms var(--ease-in-out)` — tracks the global day/night switch.
**Bottom border:** `1px solid var(--color-border)` — hairline, always present.

Three children, laid out as a CSS Grid: `grid-template-columns: 1fr auto 1fr`, with horizontal padding of `var(--gutter)` each side.

---

#### Left Element — MENU Pill

**Component name:** `NavMenuTrigger`
**Element:** `<button>` — accessible, keyboard-operable, triggers drawer open/close.

**Label text (exact):** `MENU`
**No badge.** Badge counters are removed entirely. Reason: a badge count implying "items in cart" or "notifications" is an e-commerce/app register. TEMPO is not a utility — it is a place. A badge on a restaurant nav pill signals the wrong product category. The pill is clean.

**Shape:** Pill/capsule — `border-radius: var(--radius-full)`, `padding: 10px 22px`.
**Border:** `1px solid var(--color-border-strong)`.
**Background:** transparent.
**Text:** `var(--font-display)`, `var(--text-xs)`, `letter-spacing: var(--tracking-widest)`, `font-weight: 500`, `color: var(--color-nav-text)`.

**Day state:**
- Border: `rgba(44, 44, 44, 0.16)` (`--color-border-strong`)
- Text: `var(--tempo-charcoal)` (`#2C2C2C`)

**Night state:**
- Border: `rgba(247, 243, 238, 0.16)` (`--color-border-strong`)
- Text: `var(--tempo-cream)` (`#F7F3EE`)

**Hover:** border transitions to `var(--color-border-accent)` (Terracotta day / Amber night) over `200ms var(--ease-out-expo)`. No background fill.
**When drawer is open:** the label switches to `CLOSE` (same pill, same style). This removes the need for a separate close button in the header row and keeps the masthead composition intact.

---

#### Centre Element — Brand Name Frame

**Component name:** `NavBrandFrame`
**Element:** `<a href="/">` — links to homepage.

**Text content (exact):** `TEMPO HOUSE`
**Typography:** `var(--font-display)`, `var(--text-sm)`, `letter-spacing: var(--tracking-widest)`, `font-weight: 500`, `color: var(--color-nav-text)`.
**Case:** All caps — set in CSS via `text-transform: uppercase`, not in the string.

**Framing:**
- The brand name sits inside a box: `border: 1px solid var(--color-border-strong)`, `padding: 10px 28px`.
- No `border-radius` — a sharp rectangle, not a pill. This is the intentional contrast: pill left, rectangle centre, oval right.
- The hairline rules extending left and right from this box are achieved by making the box `display: flex; align-items: center` and extending the top/bottom border pseudo-elements to connect visually to the pill and oval. In practice: the single `1px` bottom border on the masthead container provides the connecting line — the pill and oval borders sit at the same vertical midpoint.

**Day state:** border `rgba(44, 44, 44, 0.16)`, text `var(--tempo-charcoal)`.
**Night state:** border `rgba(247, 243, 238, 0.16)`, text `var(--tempo-cream)`.
**Hover:** No visual change. Cursor is `default` — the brand name is a link but not a CTA, so no hover affordance beyond the URL change.

---

#### Right Element — RESERVE Oval

**Component name:** `NavReserveCTA`
**Element:** `<a href="/reservations">` — links to the Eat App reservation page.

**Label text (exact):** `• RESERVE •`
The centred dots are `·` (U+00B7 MIDDLE DOT), not bullet points or asterisks. Exact string: `· RESERVE ·` with a single space either side of the word.

**Shape:** Oval — `border-radius: var(--radius-full)`, `padding: 10px 28px`.
**Typography:** `var(--font-display)`, `var(--text-xs)`, `letter-spacing: var(--tracking-widest)`, `font-weight: 500`.

**Day state:**
- Background: `var(--tempo-terracotta)` (`#C76E4B`)
- Text: `var(--tempo-cream)` (`#F7F3EE`)
- Border: none.

**Night state:**
- Background: `var(--tempo-amber)` (`#DDAA62`)
- Text: `var(--tempo-ink)` (`#1A1816`)
- Border: none.

**Hover (day):** Background transitions to `var(--tempo-terracotta-dim)` (`#9E5539`) over `200ms var(--ease-out-expo)`.
**Hover (night):** Background transitions to `var(--tempo-amber-dim)` (`#B8893E`) over `200ms var(--ease-out-expo)`.

---

### Scroll Behaviour

**The nav does not compact or disappear on scroll.** Reason: the masthead composition is the primary brand expression on every page — collapsing or hiding it on scroll would interrupt the ambient design presence TEMPO needs to maintain. Sticky at 64px height, always.

**One scroll-triggered change:** on scroll past `100px`, the masthead bottom border opacity increases from `var(--color-border)` (8% alpha) to `var(--color-border-strong)` (16% alpha). A subtle strengthening of the frame — the nav earns its permanence as you move through content. Transition: `border-color 300ms var(--ease-in-out)`.

---

## 3. Menu Drawer — Exact Spec

### Animation

**Direction:** Slides in from the **left**. The MENU pill is on the left; the drawer originates from that trigger. This is spatially logical and matches the Da Maria pattern.

**Open animation:**
- Drawer `transform: translateX(-100%)` → `translateX(0)`.
- Duration: `480ms`.
- Easing: `var(--ease-out-expo)` (`cubic-bezier(0.16, 1, 0.3, 1)`).

**Close animation:**
- Drawer `translateX(0)` → `translateX(-100%)`.
- Duration: `320ms`.
- Easing: `var(--ease-in-expo)` (`cubic-bezier(0.7, 0, 0.84, 0)`).

The open is slower and more deliberate (a door opening). The close is faster and decisive (a door falling shut). This asymmetry is intentional — it matches the TEMPO personality: unhurried in welcome, clean in departure.

**Overlay:**
- A full-viewport overlay `<div>` sits between the drawer and the page content.
- `background: var(--tempo-ink)`, `opacity: 0.56`.
- Fades in/out: `opacity 0` → `0.56`, `300ms var(--ease-in-out)`.
- Clicking the overlay closes the drawer (same as clicking CLOSE in the masthead).

**Drawer width:** `min(480px, 100vw)` — wide enough to read comfortably, never full-viewport on large screens.

**Drawer background:**
- Day mode: `var(--tempo-ink)` — `#1A1816`.
- Night mode: `var(--tempo-ink)` — same.
- **Decision:** The drawer is always ink. Reason: the drawer is an immersive moment, a step inside. The pale cream day palette is correct for the open site, but the drawer needs to feel like stepping through a threshold — a preview of the night mode regardless of time. This also ensures the large nav type always reads as cream-on-dark, which is the stronger typographic composition for large display text.

---

### Drawer Layout

The drawer is a flex column: `display: flex; flex-direction: column; height: 100vh`.

Top section (grows to fill available space): navigation links.
Bottom section (fixed height): toggles + footer strip.

**Top padding:** `var(--space-16)` from top edge — breathing room below the masthead.
**Horizontal padding:** `var(--space-12)` left, `var(--space-8)` right.

---

### Navigation Link List — Exact Order

Each link: `display: block`, `padding: var(--space-3) 0`, minimum touch target height `56px` on desktop (larger than 44px minimum — these are large editorial nav items).

**Typography:** `var(--font-display)`, `font-weight: 300`, `letter-spacing: var(--tracking-wide)`, `text-transform: uppercase`, `color: var(--tempo-cream)`.

**Font size progression:** The links scale down the list to communicate hierarchy:
- First link (The Venue): `var(--text-3xl)` — `clamp(2rem, 5vw, 3rem)`
- Standard links: `var(--text-2xl)` — `clamp(1.5rem, 4vw, 2rem)`

All links are the same size in the final spec. The `var(--text-2xl)` scale gives sufficient presence without the drawer becoming a headline poster. The editorial weight comes from the large tracking and ink background, not from oversized type.

**Exact link order:**

```
The Venue        →  /venue
The Café         →  /specialty-cafe-saigon
The Bar          →  /cocktail-bar-saigon
Gallery          →  /gallery
What's On        →  /whats-on
Events           →  /events                 [with expand indicator — see below]
Contact          →  /contact
```

**Events sub-menu treatment:**

Events does **not** use an accordion. Reason: accordion patterns require interaction, interrupt the reading flow, and introduce a disclosure mechanic that makes the nav feel like a form. Instead:

- "Events" is a link to `/events` (the hub).
- Below it, indented `var(--space-8)` left with a `1px solid rgba(247, 243, 238, 0.16)` left border, a set of sub-links in a smaller scale (`var(--text-base)`, `letter-spacing: var(--tracking-wide)`) appear **always visible** — not collapsed.

**Sub-links (always shown, indented):**

```
  Corporate Events    →  /corporate-events
  Private Dining      →  /private-dining-ho-chi-minh-city
  Weddings            →  /wedding-venue-ho-chi-minh-city
  Product Launches    →  /product-launches-venue-saigon
  Gallery Hire        →  /art-gallery-hire-saigon
  Workshops           →  /workshop-space-saigon
  Birthdays           →  /birthday-venue-saigon
  ─────────────────────────
  Enquire Now →       →  /events/enquiry
```

The "Enquire Now →" sub-link sits below a thin divider and uses `color: var(--tempo-amber)` to distinguish it as a CTA within the link list. The arrow `→` is a literal character, not an SVG icon.

**Reservations link:** Reservations (`/reservations`) does **not** appear in the drawer navigation. It is already the primary CTA in the masthead (RESERVE oval). Duplicating it in the drawer creates noise. Visitors who want to reserve will use the masthead CTA — they do not need to open the drawer to find it.

---

### Active State

The drawer detects the current pathname via `usePathname()` (Next.js App Router).

**Active link style:**
- `color: var(--color-accent)` — Terracotta day / Amber night. Since the drawer is always ink-background, the active colour is `var(--tempo-amber)` regardless of global mode.
- A `2px` left border in `var(--tempo-amber)` appears on the active link row (negative-margin trick or pseudo-element — developer preference).
- Sub-links under Events activate individually when on a sub-page.

---

### Language Toggle

**Position:** In the bottom section of the drawer, below the day/night toggle.
**Phase 1 decision:** The language toggle is **present but single-state** in Phase 1. It renders as `EN / VI` with `EN` appearing active (`var(--tempo-amber)`) and `VI` dimmed (`rgba(247, 243, 238, 0.32)`). Clicking `VI` shows a coming-soon tooltip: *"Vietnamese coming soon — Tiếng Việt sắp ra mắt"* (this copy is bilingual so Vietnamese readers understand the message). This is preferable to hiding the toggle entirely because it signals intent and sets expectation.

**Typography:** `var(--font-display)`, `var(--text-xs)`, `letter-spacing: var(--tracking-widest)`, `text-transform: uppercase`.

**When Phase 2 VI content is ready:** toggle activates without code change — remove the tooltip, wire `html[lang]` and the CMS language field.

---

### Day/Night Toggle

**Position:** In the bottom section of the drawer, above the language toggle.
**Label (day mode):** `DAY ·` with a filled circle indicator. Clicking switches to night.
**Label (night mode):** `· NIGHT` with the indicator on the right.

Exact implementation: a `<button>` with two spans — `DAY` and `NIGHT` — and a sliding indicator dot between them. The active word is `color: var(--tempo-amber)`, inactive is `rgba(247, 243, 238, 0.32)`. The indicator slides between them: `transform: translateX()` transition `300ms var(--ease-in-out)`.

**Stores preference to `localStorage` with key `tempo-mode`.** On page load, the root layout reads this key and applies `data-tempo="day"` or `data-tempo="night"` to `<html>` before first paint (set in a blocking script tag in `<head>`, not in a React effect, to prevent flash). If no stored preference, auto-detects HCMC local time: hours 6–17 = day, 18–5 = night.

---

### Close Button

There is **no separate close button inside the drawer**. The MENU pill in the masthead serves as the close trigger (label switches to `CLOSE` when drawer is open). The overlay click also closes. This decision eliminates a design decision (where does the X go?) and keeps the drawer interior clean for navigation links and toggles.

---

### Drawer Footer Strip

**Minimal.** Three lines:

```
info@tempohouse.com.vn

Instagram  ·  Facebook  ·  TikTok

© 2026 TEMPO House
```

**Typography:** `var(--font-body)`, `var(--text-xs)`, `color: rgba(247, 243, 238, 0.48)` — muted, present if needed, not competing with the navigation.

The email is a `mailto:` link. Social icons are **text labels only** (no SVG icons in the drawer) — matching the typographic register of the rest of the drawer. If icons are added in Phase 2, they should be clean SVG monochrome at 16px, not brand-coloured logos.

No address in the drawer footer strip. The address lives in the site footer. The drawer is a navigation tool, not an information dump.

---

### Mobile Menu as Micro-Gallery (Phase 2 feature)

As noted in the sitemap design spec and The Ivy Asia analysis, the mobile menu drawer overlay background should show the current gallery exhibition artwork from the CMS. This is a Phase 2 feature requiring the WordPress CMS connection. In Phase 1, the drawer background is flat `var(--tempo-ink)`. The CSS architecture should accommodate a `--drawer-bg-image` CSS variable so Phase 2 can inject the exhibition image without a structural change.

---

## 4. Mobile Navigation — Exact Spec

### Header

**Height:** `56px` (reduced from desktop `64px`).
**Position:** `sticky`, `top: 0`, `z-index: var(--z-nav)`.
**Layout:** Two elements only — `display: flex; justify-content: space-between; align-items: center; padding: 0 var(--space-6)`.

**Left:** MENU pill — same component as desktop, identical styling, identical label/CLOSE toggle behaviour.
**Centre:** Brand name — same `NavBrandFrame` component, same styling, but the framed box is visually simplified: no extended hairline rules (there is no RESERVE oval to connect to on mobile). The border-box remains. Logo link: `/`.
**Right element:** Absent. No RESERVE oval in the mobile header — the space is too constrained to give the oval adequate breathing room alongside the MENU pill and brand name.

**Decision on RESERVE placement — mobile:**

The RESERVE CTA on mobile appears in two locations:
1. **Inside the drawer** — as a full-width button at the top of the navigation section, above the page links. It precedes all nav links deliberately: reservation is the highest-value action and should be the first affordance in the mobile drawer.
2. **Sticky bottom CTA bar** — on all pages except `/reservations` and `/events/enquiry` themselves, a sticky `56px` bar appears at the bottom of the viewport on mobile. Full-width, background `var(--tempo-terracotta)` day / `var(--tempo-amber)` night, text `RESERVE — tempohouse.com.vn` centered. This eliminates the need to scroll to find the CTA on content-heavy pages.

---

### Mobile Drawer — RESERVE CTA (top of drawer)

**Component:** `NavReserveCTAFull`
**Placement:** Below the top padding, above the first nav link.
**Style:** Full-width block, `padding: var(--space-4) var(--space-6)`, `border: 1px solid var(--tempo-amber)`, `border-radius: var(--radius-sm)`, `margin-bottom: var(--space-8)`.
**Text:** `· RESERVE ·` — same label as desktop oval, same typography (`var(--font-display)`, `var(--text-xs)`, `letter-spacing: var(--tracking-widest)`), centered.
**Colour:** Text `var(--tempo-amber)`, background transparent. (This is an outlined variant — the filled oval is a desktop masthead affordance; the drawer version is outlined to feel at home on the dark background without overpowering the navigation links.)

---

### Mobile Drawer — Differences from Desktop

The drawer is the **same component** as desktop. No separate mobile drawer. Differences handled via CSS media queries:

- Link font size reduces from `var(--text-2xl)` to `var(--text-xl)` (`clamp(1.25rem, 3vw, 1.5rem)`) on viewports below `640px`.
- Touch targets: each `<a>` in the drawer has `min-height: 52px` and `display: flex; align-items: center` — exceeds the 44px minimum.
- Sub-link touch targets: `min-height: 44px`.
- Bottom section (toggles + footer strip) remains identical.

---

### Sticky Bottom CTA Bar — Mobile

**Component:** `MobileReserveCTA`
**Behaviour:** Visible on mobile (`max-width: 768px`), hidden on desktop.
**Position:** `position: fixed; bottom: 0; left: 0; right: 0; z-index: var(--z-overlay)`.
**Height:** `56px`.

**Shows on:** All pages.
**Hides on:** `/reservations`, `/events/enquiry`. Use `usePathname()` to conditionally render.

**Day style:**
- Background: `var(--tempo-terracotta)` (`#C76E4B`)
- Text: `var(--tempo-cream)` (`#F7F3EE`)

**Night style:**
- Background: `var(--tempo-amber)` (`#DDAA62`)
- Text: `var(--tempo-ink)` (`#1A1816`)

**Text (exact):** `RESERVE A TABLE` — simpler than the decorative dot-flanked version. On mobile context, clarity wins over decoration.
**Font:** `var(--font-display)`, `var(--text-xs)`, `letter-spacing: var(--tracking-widest)`, `font-weight: 500`, `text-transform: uppercase`, centered.
**Link target:** `/reservations`.

**Safe area:** Add `padding-bottom: env(safe-area-inset-bottom)` to handle iPhone notch/home bar. Adjust height accordingly to maintain the 56px tap area above the safe area.

**Body padding:** When this bar is visible, add `padding-bottom: 56px` to `<body>` (or the page scroll container) to prevent page content hiding beneath it.

---

### Sticky Header on Mobile Scroll

The header is `sticky` — it does not disappear on scroll. Same reasoning as desktop: the masthead is the persistent brand anchor on every page. The sticky bottom CTA bar provides conversion access; the sticky top header provides orientation and navigation access.

---

## 5. Footer — Exact Spec

### Top Border — Signature Floor Pattern

The very first element in the footer is a full-width horizontal strip: the TEMPO signature floor pattern image (`/public/content/brand-assets/tempo_house_signature_floor.png`) used as a decorative horizontal rule.

**Implementation:**
```css
.footer-signature-rule {
  width: 100%;
  height: 32px;   /* adjust to aspect ratio of the image */
  object-fit: cover;
  object-position: center;
  opacity: 0.48;  /* day mode */
  display: block;
}

[data-tempo="night"] .footer-signature-rule {
  opacity: 0.24;  /* more restrained on dark background */
}
```

This image sits between the page content and the footer column grid. It is not a `<hr>` — it is an `<img>` element for its semantic value as decorative art.

---

### Footer Palette

The footer uses `--color-bg-alt` as background — `var(--tempo-cream-dark)` (`#EDE8E1`) in day mode, `#211F1C` in night mode. This distinguishes it from the main page background without requiring a hard contrast shift. It is one step deeper/darker than the primary background in both modes.

Text: `var(--color-text-primary)` for headings, `var(--color-text-secondary)` for body links and info text.

---

### Column Grid

**Desktop:** `grid-template-columns: 1fr 1fr 1fr 1fr` (four equal columns). Max-width `var(--content-width)` (`1200px`), centred, `padding: var(--space-16) var(--gutter)`.

**Mobile:** `grid-template-columns: 1fr 1fr` (two columns), columns wrap:
- Row 1: Explore (col 1) + Visit (col 2)
- Row 2: Connect (col 1) + Legal (col 2)

**Column heading style:** `var(--font-display)`, `var(--text-xs)`, `letter-spacing: var(--tracking-widest)`, `text-transform: uppercase`, `color: var(--color-text-primary)`, `margin-bottom: var(--space-6)`.

**Link style:** `var(--font-body)`, `var(--text-sm)`, `color: var(--color-text-secondary)`, `line-height: var(--leading-loose)`. Hover: `color: var(--color-accent)`, transition `150ms var(--ease-out-expo)`.

---

### Col 1 — Explore

**Heading (exact):** `EXPLORE`

**Links (exact order and labels):**
```
The Venue          →  /venue
The Café           →  /specialty-cafe-saigon
The Bar            →  /cocktail-bar-saigon
Gallery            →  /gallery
What's On          →  /whats-on
Events & Functions →  /events
Reservations       →  /reservations
Contact            →  /contact
```

---

### Col 2 — Visit

**Heading (exact):** `VISIT`

**Content:**

```
[Address line 1]         ← TBC — placeholder: "District 1, Ho Chi Minh City"
[Address line 2]         ← TBC — specific street address when confirmed
Ho Chi Minh City, Việt Nam

→ Google Maps            ← link to Google Maps pin when confirmed
```

**Hours (exact format):**
```
Café         Mon – Fri   07:30 – 17:00
             Sat – Sun   08:00 – 17:00

Bar          Daily       17:00 – late

Gallery      Tue – Sun   10:00 – 20:00
             (closed Mon)
```

Hours are set in `<time>` elements where possible for schema compliance. Hours TBC — use these as placeholders and update via CMS when confirmed.

The small "Café" / "Bar" / "Gallery" labels before each set of hours use `var(--font-display)`, `var(--text-xs)`, `letter-spacing: var(--tracking-widest)`, `color: var(--color-accent)` — terracotta in day, amber in night.

---

### Col 3 — Connect

**Heading (exact):** `CONNECT`

**Links (exact order):**
```
Instagram                →  [Instagram URL]
Facebook                 →  [Facebook URL]
TikTok                   →  [TikTok URL]

info@tempohouse.com.vn   →  mailto:info@tempohouse.com.vn
```

**Email signup teaser:**
Below the social + email links, a one-line CTA for the Klaviyo list:

```
Stay in the loop on events, exhibitions, and what's next.

[Email address field]   [→]
```

The `[→]` submit is a right-arrow character button. Minimal inline form — not a modal, not a full Klaviyo embed. On submit, calls the Klaviyo subscribe API endpoint. Confirmation: the input replaces with `— thank you.` in `var(--font-accent)` italic.

**Form copy (exact):**
- Placeholder: `your@email.com`
- Submit button text: `→` (arrow character, no label text)
- Success: `— thank you.` (em dash, space, italic)

---

### Col 4 — Legal

**Heading (exact):** `LEGAL`

**Links:**
```
Privacy Policy    →  /privacy-policy
```

**Copyright line:**

```
© 2026 TEMPO House.
All rights reserved.
```

`font-size: var(--text-xs)`, `color: var(--color-text-muted)`, `line-height: var(--leading-loose)`.

**Below the grid** (full-width, centred, below the four-column section):

```
TEMPO House is a venue by TEMPO Life.
```

`var(--font-body)`, `var(--text-xs)`, `color: var(--color-text-muted)`, centred, `padding-bottom: var(--space-8)`.

---

## 6. CTA Strategy

### Primary CTA: RESERVE

**Target:** `/reservations`
**Appears in:**
1. Desktop masthead — RESERVE oval (always visible, every page)
2. Mobile drawer — RESERVE outlined block (top of drawer navigation)
3. Mobile sticky bottom bar — `RESERVE A TABLE` (every page except `/reservations` and `/events/enquiry`)
4. End-of-section CTA block on: Home (`/`), The Café (`/specialty-cafe-saigon`), The Bar (`/cocktail-bar-saigon`), The Venue (`/venue`)

**End-of-section CTA block style:** full-width section, `background: var(--color-bg-alt)`, centred, with two lines:
```
Line 1 (display):    A TABLE WORTH KEEPING.
Line 2 (CTA):        [· RESERVE ·]
```
The CTA is an `<a>` styled as the terracotta/amber oval from the masthead, at a larger scale (`padding: 14px 36px`).

---

### Secondary CTA: EVENTS ENQUIRY

**Target:** `/events/enquiry`
**Label (exact):** `ENQUIRE NOW →`
**Appears in:**
1. Events hub (`/events`) — sticky floating button on scroll past the hero
2. Every events sub-page — sticky button on scroll, and at the bottom of the page above the footer
3. Gallery page (`/gallery`) — section CTA: `HOST YOUR OPENING HERE →`
4. What's On (`/whats-on`) — passive placement, not sticky: a section at the bottom linking to events enquiry
5. The Venue (`/venue`) — below the spaces section: `HOST SOMETHING HERE →`

**Sticky events enquiry button (scroll-triggered):**
- Appears after scrolling `300px` on events pages and sub-pages.
- `position: fixed; bottom: var(--space-8); right: var(--gutter); z-index: var(--z-overlay)`.
- On mobile: this is suppressed in favour of the sticky bottom bar, which takes the bottom real estate.
- Style: filled terracotta (day) / amber (night) pill, `padding: 12px 24px`, shadow `var(--shadow-md)`.
- Text: `ENQUIRE NOW →`
- Fades in: `opacity 0 → 1`, `300ms var(--ease-out-expo)`.

**On `/events/enquiry` page:** Neither sticky CTA appears. The page is the conversion destination.
**On `/reservations` page:** Neither sticky CTA appears.

---

### Tertiary CTA: EXPLORE THE GALLERY

Appears only on Home page (`/`) in the gallery section, and on the What's On page.
**Label (exact):** `TEMPO GALLERY →`
**Style:** Text link with arrow, no fill. `color: var(--color-accent)`, `var(--font-display)`, `var(--text-xs)`, `letter-spacing: var(--tracking-widest)`.

---

## 7. Active States & Wayfinding

### Page-Level Active State

The `<SiteNav>` component receives the current pathname via `usePathname()`. The active page link in the drawer is styled with `color: var(--tempo-amber)` and the `2px` amber left border indicator.

Since the drawer is always ink-background, amber is always the active colour — regardless of whether the global mode is day or night.

Sub-page active state: when on `/corporate-events`, both the "Events" parent link and the "Corporate Events" sub-link in the drawer receive the amber active treatment.

---

### Breadcrumbs — Events Sub-Pages

Events sub-pages (`/corporate-events`, `/private-dining-ho-chi-minh-city`, `/weddings`, etc.) display a minimal breadcrumb below the masthead and above the page hero:

```
Events & Functions  /  Corporate Events
```

**Style:** `var(--font-body)`, `var(--text-xs)`, `color: var(--color-text-muted)`, `letter-spacing: var(--tracking-wide)`. The separator is ` / ` (space, slash, space), not a chevron icon — typographic, not iconic.

"Events & Functions" links to `/events`. Current page label is plain text, no link.

**Schema:** Implement as `<nav aria-label="Breadcrumb">` with `BreadcrumbList` JSON-LD for Google rich results on event venue search pages.

---

### Back-to-Top

A back-to-top trigger appears after scrolling `600px` on any page longer than `1400px` viewport height. Position: `fixed; bottom: var(--space-8); right: var(--gutter)`.

On desktop, it coexists with the events enquiry sticky button by stacking below it (`bottom: calc(var(--space-8) + 56px)` when both are present).

On mobile, back-to-top is absent — the sticky bottom bar and natural momentum scrolling make it unnecessary.

**Style:** A small circle, `40px` × `40px`, `border: 1px solid var(--color-border-strong)`, background `var(--color-bg)`, containing an upward arrow `↑` in `var(--font-display)`. Not labelled. `aria-label="Back to top"` on the element.

---

## 8. Link Label Glossary

This is the source of truth. If a label here conflicts with a label in any component, this document governs. Update this table when any label changes.

| Page | URL | Drawer Label | Footer (Explore col) Label | Breadcrumb Label | Notes |
|---|---|---|---|---|---|
| Home | `/` | *(not in drawer — brand name links home)* | *(not in footer col — implied by brand)* | — | — |
| The Venue | `/venue` | `THE VENUE` | `The Venue` | `The Venue` | Story + spaces page |
| The Café | `/specialty-cafe-saigon` | `THE CAFÉ` | `The Café` | `The Café` | Note: É with accent — `É` |
| The Bar | `/cocktail-bar-saigon` | `THE BAR` | `The Bar` | `The Bar` | — |
| Gallery | `/gallery` | `GALLERY` | `Gallery` | `Gallery` | — |
| What's On | `/whats-on` | `WHAT'S ON` | `What's On` | `What's On` | Apostrophe: `'` (curly) |
| Events Hub | `/events` | `EVENTS` | `Events & Functions` | `Events & Functions` | Drawer is short; footer is descriptive |
| Corporate Events | `/corporate-events` | `Corporate Events` *(sub-link)* | *(not in footer col)* | `Corporate Events` | — |
| Private Dining | `/private-dining-ho-chi-minh-city` | `Private Dining` *(sub-link)* | *(not in footer col)* | `Private Dining` | — |
| Weddings | `/wedding-venue-ho-chi-minh-city` | `Weddings` *(sub-link)* | *(not in footer col)* | `Weddings` | — |
| Product Launches | `/product-launches-venue-saigon` | `Product Launches` *(sub-link)* | *(not in footer col)* | `Product Launches` | — |
| Gallery Hire | `/art-gallery-hire-saigon` | `Gallery Hire` *(sub-link)* | *(not in footer col)* | `Gallery Hire` | — |
| Workshops | `/workshop-space-saigon` | `Workshops` *(sub-link)* | *(not in footer col)* | `Workshops` | — |
| Birthdays | `/birthday-venue-saigon` | `Birthdays` *(sub-link)* | *(not in footer col)* | `Birthdays` | — |
| Events Enquiry | `/events/enquiry` | `Enquire Now →` *(sub-link, amber)* | *(not in footer col)* | `Enquiry` | Arrow: `→` character |
| Reservations | `/reservations` | *(not in drawer link list — appears as outlined CTA block at top of drawer)* | `Reservations` | — | — |
| Contact | `/contact` | `CONTACT` | `Contact` | `Contact` | — |
| Privacy Policy | `/privacy-policy` | *(not in drawer)* | `Privacy Policy` *(Legal col)* | — | — |

**Case rule for drawer labels:** ALL CAPS for top-level links. Title Case for sub-links.
**Case rule for footer labels:** Title Case throughout.
**Typographic conventions:**
- Café: always with accent (`É`) — `The Café`, `THE CAFÉ`. Never `The Cafe`.
- What's On: curly apostrophe (`'`) — not a straight apostrophe (`'`).
- Enquire Now →: the arrow is the `→` character (U+2192), not `>` or `>>`.

---

## Appendix — Component Inventory

Components required for the Phase 1 navigation build:

| Component | File path (suggested) | Notes |
|---|---|---|
| `SiteNav` | `app/components/SiteNav.tsx` | Masthead wrapper — three-column grid |
| `NavMenuTrigger` | *(within SiteNav)* | MENU/CLOSE pill button |
| `NavBrandFrame` | *(within SiteNav)* | Brand name link in bordered box |
| `NavReserveCTA` | *(within SiteNav)* | RESERVE oval — desktop only |
| `NavDrawer` | `app/components/NavDrawer.tsx` | Full drawer + overlay |
| `NavDrawerLinks` | *(within NavDrawer)* | Link list with sub-links |
| `NavDrawerToggles` | *(within NavDrawer)* | Day/Night + Language toggles |
| `NavDrawerFooter` | *(within NavDrawer)* | Email + socials + copyright strip |
| `MobileReserveCTA` | `app/components/MobileReserveCTA.tsx` | Sticky bottom bar — mobile only |
| `SiteFooter` | `app/components/SiteFooter.tsx` | Four-column footer + signature rule |
| `FooterSignatureRule` | *(within SiteFooter)* | `<img>` of floor pattern with opacity |
| `FooterNewsletterForm` | *(within SiteFooter)* | Inline Klaviyo subscribe form |
| `BreadcrumbNav` | `app/components/BreadcrumbNav.tsx` | Events sub-page breadcrumbs |
| `BackToTop` | `app/components/BackToTop.tsx` | Scroll-triggered back-to-top button |
| `EventsEnquiryCTA` | `app/components/EventsEnquiryCTA.tsx` | Floating enquiry CTA — scroll-triggered |

**State management:** No global state library required. Day/night mode stored in `localStorage`, read in a blocking `<script>` in `<head>`. Drawer open/close managed with `useState` in `SiteNav`. Current pathname from `usePathname()` (Next.js App Router built-in).

---

*Prepared by Muse — Raging Monk AI Creative Director. Phase 1 build reference. Questions: bailey@ragingmonk.co.*
