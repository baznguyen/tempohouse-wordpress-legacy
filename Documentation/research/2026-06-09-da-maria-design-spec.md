# TEMPO House — UI/UX Design Specification
**Date:** 09 June 2026
**Prepared by:** Muse (Creative Director) — Raging Monk AI
**Reference:** Da Maria restaurant website (editorial broadsheet design language)
**Status:** APPROVED — Single source of truth for Phase 1 build

> This document is the authoritative specification for every component, layout, and interaction in the TEMPO House website rebuild. Every ambiguity here is a day of lost build time. Read it before writing a single line of CSS.

---

## Contents

1. [Design Philosophy](#1-design-philosophy)
2. [Color System](#2-color-system)
3. [Typography System](#3-typography-system)
4. [Background & Texture](#4-background--texture)
5. [Navigation Component](#5-navigation-component)
6. [Hero Section](#6-hero-section)
7. [Reserve Modal](#7-reserve-modal)
8. [Reservation Page](#8-reservation-page)
9. [Menu Section](#9-menu-section)
10. [What's On / Events Cards](#10-whats-on--events-cards)
11. [Footer](#11-footer)
12. [Mobile Patterns](#12-mobile-patterns)
13. [Interaction & Animation Spec](#13-interaction--animation-spec)
14. [Component Inventory](#14-component-inventory)
15. [Illustration Language](#15-illustration-language)

---

## 1. Design Philosophy

### The Editorial Broadsheet Principle

Da Maria's visual identity is built on a single powerful idea: the Italian newspaper broadsheet. Massive Didone headlines bleed off the viewport. Hairline rules run the full width of the page. Type is not contained — it breathes past its own boundaries. The whole page feels like it was composed at a press, not in a browser.

TEMPO translates this principle into something warmer, more Vietnamese, and dualistic. Where Da Maria is confident crimson and Italian white, TEMPO is cream-and-terracotta by day, ink-and-amber by night. The editorial broadsheet is still there — but it's printed on aged Vietnamese rice paper, not Roman newsprint.

### How TEMPO Differs from Da Maria

| Dimension | Da Maria | TEMPO House |
|---|---|---|
| Cultural register | Italian tratoria | Saigon creative community |
| Background texture | Damask floral wallpaper (ornate) | Linen grain / rice paper (warmer, quieter) |
| Headline type | Heavy Didone (Bodoni weight) | Cormorant Garamond (same thick-thin, more elegant) |
| Accent color | Deep crimson #B5302A | Terracotta #C76E4B (day) / Amber #DDAA62 (night) |
| Mode | Single | Day/Night duality — the site is a different venue at different hours |
| Illustration subjects | Vintage telephone, cloche | Bougainvillea, coffee ritual, gallery motif |
| Tone | Roman celebration | Unhurried, exacting, dual-natured |

### The Day/Night Duality — TEMPO's Innovation

Da Maria doesn't have this. It is TEMPO's primary design innovation: the website is literally a different venue at different hours. The `[data-tempo="day"]` and `[data-tempo="night"]` attribute on `<html>` drives everything — backgrounds, accent colors, shadows, the dominant photographic mood, even the illustration style. The transition is not a hard swap; it is a deepening of the same tonal palette.

**Auto-trigger logic:** On page load, read `new Date()` in HCMC timezone (UTC+7). If local hour is between 07:00 and 17:59, set `data-tempo="day"`. If 18:00–06:59, set `data-tempo="night"`. Store override in `localStorage` under key `tempo-mode`. User override via the toggle in the nav persists for 24 hours, then auto-resets.

### Design Grammar Rules (non-negotiable)

1. Headlines use `--font-accent` (Cormorant Garamond) for all editorial-weight display moments. `--font-display` (Bricolage Grotesque) is used for section labels, UI headings, functional copy. Never reverse this.
2. All thin rule lines use `1px solid var(--color-border-accent)` at reduced opacity — never `var(--color-border)` for decorative lines.
3. Oval/capsule borders use `border-radius: var(--radius-full)` with `1px solid` stroke only. Never filled.
4. Category dividers use the diamond pattern: `◆ SECTION NAME ◆` — Unicode `◆` (U+25C6), tracked to `0.28em`, accent color.
5. Every page has a maximum content width of `var(--content-width)` (1200px) centered, with `var(--gutter)` padding. The viewport-bleed headline technique overrides this for specific elements only — it requires `overflow: visible` with `width: 100vw` and negative margin compensation.

---

## 2. Color System

All colors are defined in `/Website/app/brand-tokens.css`. Never hardcode a hex in a component. Always consume semantic tokens.

### Raw Palette

```css
--tempo-cream:           #F7F3EE   /* Day: backgrounds, menu foundation */
--tempo-terracotta:      #C76E4B   /* Day: accent text, highlights, signage */
--tempo-charcoal:        #2C2C2C   /* Day: typography, contrast */
--tempo-amber:           #DDAA62   /* Night: warmth, lighting, wine */
--tempo-sage:            #8A9277   /* Both: nature, grounding, gallery note */
--tempo-sand:            #E7D8C9   /* Both: soft blocks, UI panels */
--tempo-ink:             #1A1816   /* Night: near-black richest typography */
```

### Semantic Token Activation

All component CSS must reference semantic tokens, not raw palette values. The semantic tokens switch automatically based on `[data-tempo]` attribute.

#### Day Mode `[data-tempo="day"]`

| Semantic Token | Raw Value | Design Intent |
|---|---|---|
| `--color-bg` | `--tempo-cream` (#F7F3EE) | Page background — rice paper |
| `--color-bg-alt` | `--tempo-cream-dark` (#EDE8E1) | Alternate section bg |
| `--color-surface` | `--tempo-white` (#FFFFFF) | Elevated card/panel surface |
| `--color-surface-raised` | `--tempo-sand` (#E7D8C9) | Higher elevation panels |
| `--color-border` | `rgba(44,44,44, 0.08)` | Default structural borders |
| `--color-border-strong` | `rgba(44,44,44, 0.16)` | Stronger borders, focus states |
| `--color-border-accent` | `--tempo-terracotta` | Decorative hairline rules, dividers |
| `--color-text-primary` | `--tempo-charcoal` (#2C2C2C) | Main body text |
| `--color-text-secondary` | `--tempo-charcoal-soft` (#4A4A44) | Supporting text |
| `--color-text-muted` | `--tempo-charcoal-muted` (#7A7870) | Captions, metadata |
| `--color-text-inverse` | `--tempo-cream` | Text on dark surfaces |
| `--color-accent` | `--tempo-terracotta` (#C76E4B) | Primary accent — all interactive, category labels |
| `--color-accent-hover` | `--tempo-terracotta-dim` (#9E5539) | Hover state for accent elements |
| `--color-accent-dim` | `--tempo-terracotta-pale` (#EDD4C7) | Dimmed accent for backgrounds |
| `--color-accent-glow` | `rgba(199,110,75, 0.10)` | Glow effects, text selection |
| `--color-signature` | `--tempo-amber` (#DDAA62) | Brand signature bar / decorative warm line |
| `--color-nav-bg` | `--tempo-cream` | Navigation background |
| `--color-nav-text` | `--tempo-charcoal` | Navigation text |
| `--color-nav-active` | `--tempo-terracotta` | Active nav link |
| `--color-card-bg` | `--tempo-white` | Card background |
| `--color-input-bg` | `--tempo-white` | Form input background |
| `--color-input-border` | `rgba(44,44,44, 0.16)` | Input stroke |
| `--color-input-focus` | `--tempo-terracotta` | Input focus ring |

#### Night Mode `[data-tempo="night"]`

| Semantic Token | Raw Value | Design Intent |
|---|---|---|
| `--color-bg` | `--tempo-ink` (#1A1816) | Deep near-black — the bar at 10pm |
| `--color-bg-alt` | `#211F1C` | Slightly lighter dark for alternating sections |
| `--color-surface` | `#2A2722` | Elevated surface on dark bg |
| `--color-surface-raised` | `#332F29` | Higher elevation panels in dark mode |
| `--color-border` | `rgba(247,243,238, 0.08)` | Subtle light borders on dark |
| `--color-border-strong` | `rgba(247,243,238, 0.16)` | Stronger light borders |
| `--color-border-accent` | `--tempo-amber` (#DDAA62) | Decorative hairlines in amber |
| `--color-text-primary` | `--tempo-cream` (#F7F3EE) | Main text in night — cream on ink |
| `--color-text-secondary` | `--tempo-sand` (#E7D8C9) | Supporting text |
| `--color-text-muted` | `--tempo-charcoal-muted` (#7A7870) | Captions — same muted in both modes |
| `--color-accent` | `--tempo-amber` (#DDAA62) | Night accent — amber glow |
| `--color-accent-hover` | `--tempo-amber-dim` (#B8893E) | Amber hover state |
| `--color-nav-active` | `--tempo-amber` | Active nav in night |
| `--color-signature` | `--tempo-terracotta` | Signature bar shifts to terracotta at night |

### Shadow System

Day mode shadows use warm charcoal base. Night mode shadows use pure black base with stronger opacity.

```css
/* Day */
--shadow-sm:  0 1px  3px rgba(44,44,44,0.08), 0 1px 2px rgba(44,44,44,0.04);
--shadow-md:  0 4px 12px rgba(44,44,44,0.10), 0 2px 4px rgba(44,44,44,0.06);
--shadow-lg:  0 12px 40px rgba(44,44,44,0.12), 0 4px 8px rgba(44,44,44,0.08);
--shadow-xl:  0 24px 60px rgba(44,44,44,0.16);

/* Night */
--shadow-sm:  0 1px  3px rgba(0,0,0,0.24), 0 1px 2px rgba(0,0,0,0.16);
--shadow-md:  0 4px 12px rgba(0,0,0,0.32), 0 2px 4px rgba(0,0,0,0.20);
--shadow-lg:  0 12px 40px rgba(0,0,0,0.40), 0 4px 8px rgba(0,0,0,0.24);
--shadow-xl:  0 24px 60px rgba(0,0,0,0.56);
```

---

## 3. Typography System

### Font Families

```css
--font-display: 'Bricolage Grotesque', 'Inter', 'Helvetica Neue', Arial, sans-serif;
--font-body:    'Space Grotesk', 'Inter', system-ui, sans-serif;
--font-accent:  'Cormorant Garamond', Georgia, serif;
```

**Loading:** All three are loaded via Google Fonts in `globals.css`. The `@import url(...)` in globals.css already includes: Bricolage Grotesque (weights 300/400/500, opsz 12–96), Cormorant Garamond (weights 300/400, italic), Space Grotesk (weights 300/400/500/600). Also loaded: Bodoni Moda for potential fallback editorial use.

### Font Role Assignment

| Role | Font | Weight | Style | Use Case |
|---|---|---|---|---|
| Viewport-bleed headline | `--font-accent` | 400–500 | Normal or Italic | Hero headlines that bleed off edge |
| Editorial display | `--font-accent` | 400 | Normal | Section H1/H2 in editorial sections |
| Accent italic | `--font-accent` | 300–400 | Italic | Taglines, pullquotes, category labels, form field labels |
| Section heading | `--font-display` | 300–400 | Normal | Functional H2/H3 in card grids, UI headings |
| Navigation | `--font-display` | 400 | Normal | All nav text, labels, CTAs |
| Body copy | `--font-body` | 300–400 | Normal | Paragraphs, descriptions, metadata |
| Small caps / labels | `--font-body` | 400 | Normal + `text-transform: uppercase; letter-spacing: 0.2em` | All label/eyebrow text |
| Form field labels | `--font-accent` | 300 | Italic | Field labels above inputs — rendered in accent color |
| Prices | `--font-body` | 400 | Normal | Menu pricing |

### Type Scale

All scale tokens are fluid (CSS `clamp()`), defined in `globals.css`:

```css
--text-xs:   clamp(0.7rem,   1vw,   0.75rem);    /* 11–12px */
--text-sm:   clamp(0.8rem,   1.2vw, 0.875rem);   /* 13–14px */
--text-base: clamp(0.9rem,   1.5vw, 1rem);       /* 14–16px */
--text-md:   clamp(1rem,     2vw,   1.125rem);   /* 16–18px */
--text-lg:   clamp(1.125rem, 2.5vw, 1.25rem);   /* 18–20px */
--text-xl:   clamp(1.25rem,  3vw,   1.5rem);     /* 20–24px */
--text-2xl:  clamp(1.5rem,   4vw,   2rem);       /* 24–32px */
--text-3xl:  clamp(2rem,     5vw,   3rem);       /* 32–48px */
--text-4xl:  clamp(2.5rem,   6vw,   4rem);       /* 40–64px */
--text-5xl:  clamp(3rem,     8vw,   6rem);       /* 48–96px */
--text-hero: clamp(4rem,     12vw,  10rem);      /* 64–160px — viewport bleed */
```

### The "Newspaper Bleed" Headline Technique

This is the defining hero technique borrowed from Da Maria. The headline type is sized so large it extends past the viewport edges — only partial letterforms are visible at the sides. This creates editorial drama without a single image.

**Implementation:**

```css
.headline-bleed {
  font-family: var(--font-accent);
  font-size: var(--text-hero);           /* clamp(4rem, 12vw, 10rem) */
  font-weight: 400;
  line-height: var(--leading-tight);     /* 1.1 */
  letter-spacing: var(--tracking-tight); /* -0.03em */
  color: var(--color-text-primary);

  /* The bleed technique */
  width: 100vw;
  position: relative;
  left: 50%;
  transform: translateX(-50%);
  text-align: center;
  white-space: nowrap;
  overflow: visible;                     /* Critical — browser must NOT clip */
  padding-inline: 0;
}
```

**Important:** The parent container must NOT have `overflow: hidden` set. Set `overflow: clip` (not `hidden`) on the body/wrapper to prevent horizontal scroll while still allowing the visual bleed. Or use `overflow-x: hidden` on `<body>` — already present in globals.css via `overflow-x: hidden`.

**When to use the bleed technique:**
- Hero section: venue name "TEMPO HOUSE" — bleed the full wordmark
- Section transitions: major section names (e.g. "GALLERY", "THE BAR") as interstitial headlines
- Do NOT use on body copy, menu items, or utility text

### Typography Hierarchy in Practice

```
TEMPO HOUSE (hero, bleed)
font: --font-accent, weight 400, size --text-hero, color --color-text-primary

Specialty Café & Bar (hero sub-label)
font: --font-accent, weight 300, italic, size --text-xl, color --color-accent

◆ CAFÉ ◆  (section category divider)
font: --font-accent, weight 300, italic, size --text-sm, color --color-accent
letter-spacing: 0.28em, text-transform: uppercase

Section heading (editorial page headings)
font: --font-accent, weight 400, size --text-4xl or --text-5xl, color --color-text-primary

UI section label / eyebrow
font: --font-display, weight 400, size --text-xs, text-transform: uppercase, letter-spacing: 0.2em
color: --color-text-muted

Body paragraph
font: --font-body, weight 300, size --text-base, line-height: --leading-loose (1.85)
color: --color-text-secondary

Form field label
font: --font-accent, weight 300, italic, size --text-xs
text-transform: uppercase, letter-spacing: 0.18em, color: --color-accent

Price
font: --font-body, weight 400, size --text-sm, color: --color-text-primary
```

---

## 4. Background & Texture

### Linen Grain Texture

TEMPO's background texture adapts Da Maria's ornate damask wallpaper into something quieter and warmer — a fine linen grain, like aged Vietnamese rice paper or raw linen cloth. It is almost imperceptible at first glance but adds analogue depth that distinguishes the page from a flat digital surface.

**Implementation options (in order of preference):**

**Option A — CSS noise (no image, most performant):**
Apply a subtle SVG filter with fractal noise to the `<body>` pseudo-element:

```css
body::before {
  content: '';
  position: fixed;
  inset: 0;
  z-index: -1;
  pointer-events: none;
  background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)'/%3E%3C/svg%3E");
  background-size: 200px 200px;
  background-repeat: repeat;
  opacity: 0.025;  /* Day: very subtle — barely perceptible */
  mix-blend-mode: multiply;
}

[data-tempo="night"] body::before {
  opacity: 0.04;         /* Night: slightly more visible on dark bg */
  mix-blend-mode: screen;
}
```

**Option B — PNG texture tile (fallback if CSS noise is insufficient):**
A 512×512px grayscale linen scan at 100% opacity, stored at `/public/content/textures/linen-grain.png`. Applied as a `background-image` repeat on `body::before`. Opacity `0.04` day, `0.06` night.

**Which to choose:** Test Option A first — it is zero bytes and scales perfectly. If the grain looks too uniform or geometric on screen, switch to Option B with a real linen photograph.

**Critical:** The texture must not visually interrupt text or photography. If you can see it without squinting, it is too strong. Calibrate on a calibrated monitor at 100% zoom.

### Panel Elevation

Content panels (cards, drawers, modals) sit "above" the textured background via box shadow, not opacity layering. Use the shadow system:

- **Level 0 (background):** no shadow, background is `--color-bg`
- **Level 1 (cards, sections):** `background: var(--color-card-bg)`, `box-shadow: var(--shadow-sm)`
- **Level 2 (drawers, popovers):** `background: var(--color-surface)`, `box-shadow: var(--shadow-lg)`
- **Level 3 (modals):** `background: var(--color-surface)`, `box-shadow: var(--shadow-xl)`

In day mode, panels use pure white `--color-surface` (#FFFFFF) against the cream background — the contrast is subtle but visible. In night mode, panels use `#2A2722` against `#1A1816` ink background — the contrast is equally subtle.

### Decorative Hairline Rules

Thin horizontal rules are a core visual element throughout the site. These run full-width across the page (not constrained to the content container) and are used to:
1. Connect the three nav elements (the masthead rule)
2. Separate sections where a full bleed divider is not appropriate
3. Separate form sections within cards

```css
.hairline {
  width: 100%;
  height: 1px;
  background: var(--color-border-accent);
  opacity: 0.35;  /* Day */
  border: none;
}

[data-tempo="night"] .hairline {
  opacity: 0.25;  /* Amber at lower opacity on dark */
}
```

For the nav masthead hairline specifically, the rule spans the full viewport width:

```css
.nav-hairline {
  position: absolute;
  top: 50%;
  left: 0;
  width: 100vw;
  height: 1px;
  background: var(--color-border-accent);
  opacity: 0.3;
  pointer-events: none;
}
```

---

## 5. Navigation Component

**File:** `/Website/app/components/SiteNav.tsx` + `/Website/app/components/SiteNav.module.css`

### Desktop Navigation (≥ 1024px)

The nav is inspired directly by Da Maria's newspaper masthead layout: three elements floating on a shared hairline rule, spanning the full viewport width.

#### Structure

```
[═══════════════════════════════════════════════════════════] ← full-width hairline rule
    [MENU ●]                [TEMPO HOUSE]            [• RESERVE •]
              ┌─────────────────────────────────┐
              │         TEMPO HOUSE             │
              │  Specialty Café & Bar           │
              └─────────────────────────────────┘
[═══════════════════════════════════════════════════════════] ← full-width hairline rule (bottom of logo box)
```

The hairlines are the structural armature. The three nav elements anchor to them.

#### Component Anatomy

**LEFT: Menu Pill**
- Text: "MENU" in `--font-display`, weight 400, `--text-xs`, uppercase, tracking `0.2em`
- Border: `1px solid var(--color-border-accent)` rounded to `--radius-full` (pill shape)
- Padding: `0.5rem 1rem`
- Count badge: small circle `18px × 18px`, `background: var(--color-accent)`, white text, `--font-body` 10px. Positioned to the right of the text, overlapping the pill border slightly. Shows number of items in menu (for future cart/order feature — start at "0" hidden, show when > 0). For Phase 1: badge is hidden.
- Hover: `background: var(--color-accent-glow)`, no border change
- Active (menu open): `background: var(--color-accent)`, text color `var(--color-text-inverse)`

**CENTER: Logo Box**
- Container: thin-bordered rectangle — `1px solid var(--color-border-accent)`, opacity 0.5
- Padding: `0.75rem 2rem`
- Inside top: "TEMPO HOUSE" in `--font-accent`, weight 400, `--text-lg`, uppercase, letter-spacing `0.14em`, color `var(--color-text-primary)`
- Inside bottom: "Specialty Café & Bar" in `--font-accent`, weight 300, italic, `--text-xs`, color `var(--color-text-secondary)`, letter-spacing `0.06em`
- The hairline rules extend from the left and right edges of this box to the viewport edges
- Logo box does NOT have a hover state — it is a landmark, not a button. It is a link to `/` on click.

**RIGHT: Reserve Oval**
- Text: "• RESERVE •" in `--font-display`, weight 400, `--text-xs`, uppercase, tracking `0.18em`
- The `•` are actual bullet characters (U+2022) with `0.3em` space on each side
- Border: `1px solid var(--color-border-accent)`, border-radius `--radius-full` (oval capsule)
- Padding: `0.5rem 1.25rem`
- Hover: `background: var(--color-accent)`, text color `--color-text-inverse`, border-color `var(--color-accent)`
- Links to `/reservations`
- Transition: `background var(--duration-fast) var(--ease-out-expo), color var(--duration-fast) var(--ease-out-expo)`

#### Day/Night Toggle (inline with right side)
Positioned to the right of the Reserve oval, separated by `1.5rem`:
- Icon: sun (day) / moon (night) — SVG inline, `16px × 16px`, `stroke: var(--color-text-muted)`
- No label text
- On click: toggle `data-tempo` attribute on `<html>`, store in `localStorage`
- Hover: icon stroke shifts to `var(--color-accent)`

#### Language Toggle (inline)
- "EN | VI" in `--font-body`, `--text-xs`, tracking `0.12em`, `color: var(--color-text-muted)`
- Active language: `color: var(--color-accent)`, font-weight 500
- Positioned between Reserve oval and Day/Night toggle

#### Nav Sizing and Spacing

```css
.siteNav {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  z-index: var(--z-nav);               /* 300 */
  background: var(--color-nav-bg);
  padding-block: 1rem;
  padding-inline: var(--gutter);
}

.navInner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: relative;
  max-width: var(--max-width);         /* 1440px */
  margin-inline: auto;
}

.navHairline {
  position: absolute;
  top: 50%;
  left: calc(-1 * var(--gutter));     /* Bleed to gutter edge */
  width: calc(100% + 2 * var(--gutter));
  height: 1px;
  background: var(--color-border-accent);
  opacity: 0.3;
  pointer-events: none;
}
```

The three elements (menu pill, logo box, reserve oval) must sit ON the hairline — vertically centered to it. This gives the masthead newspaper effect.

#### Scroll Behavior

- At scroll position 0: nav is fully transparent, hairlines visible
- On scroll down 40px: `backdrop-filter: blur(12px)`, `background: color-mix(in srgb, var(--color-nav-bg) 85%, transparent)`
- This requires the `backdrop-filter` to be on a separate pseudo-element to avoid blurring the nav content itself

```css
.siteNav::before {
  content: '';
  position: absolute;
  inset: 0;
  backdrop-filter: blur(12px);
  background: color-mix(in srgb, var(--color-nav-bg) 0%, transparent);
  transition: background var(--duration-slow) var(--ease-out-expo);
  z-index: -1;
}

.siteNav.scrolled::before {
  background: color-mix(in srgb, var(--color-nav-bg) 85%, transparent);
}
```

### Mobile Navigation (< 1024px)

**Structure:**
```
[MENU ●]        TEMPO HOUSE        [RESERVE]
```

- LEFT: Menu pill (same as desktop, slightly smaller padding: `0.4rem 0.8rem`)
- CENTER: "TEMPO HOUSE" only — no subline, no border box. `--font-accent`, weight 400, `--text-md`, tracking `0.12em`. Centered in the remaining space.
- RIGHT: "RESERVE" — plain text in `--font-display`, `--text-xs`, uppercase, tracking `0.16em`, `color: var(--color-accent)`. No oval border (too crowded). Links to `/reservations`.
- Day/Night toggle and language toggle move into the menu drawer on mobile.
- No hairline rule on mobile nav (would look out of place at small width).

### Menu Drawer

**Slide direction:** From the LEFT edge — matches Da Maria. The content shifts right slightly (not covered).

**Animation spec:**
```css
.menuDrawer {
  position: fixed;
  top: 0;
  left: 0;
  height: 100vh;
  width: min(380px, 85vw);
  background: var(--color-surface);
  z-index: calc(var(--z-nav) + 50);   /* 350 — above nav */
  transform: translateX(-100%);
  transition: transform var(--duration-slow) var(--ease-out-expo);
  box-shadow: var(--shadow-xl);
  overflow-y: auto;
}

.menuDrawer.open {
  transform: translateX(0);
}
```

**Drawer overlay (dim the rest of the page):**
```css
.menuOverlay {
  position: fixed;
  inset: 0;
  background: rgba(26, 24, 22, 0.55);   /* --tempo-ink at 55% */
  z-index: calc(var(--z-nav) + 40);      /* 340 — below drawer, above content */
  opacity: 0;
  pointer-events: none;
  transition: opacity var(--duration-slow) var(--ease-out-expo);
  backdrop-filter: blur(2px);
}

.menuOverlay.visible {
  opacity: 1;
  pointer-events: all;
}
```

**Drawer interior layout:**

```
[MENU            ×]          ← top row, hairline below
                             ← 2rem padding-top
HOME                         ← active = --color-accent
VENUE
CAFÉ & BAR
GALLERY
WHAT'S ON
EVENTS ▾                     ← expand icon, reveals sub-items
RESERVATIONS
                             ← 2rem gap
──────────────────           ← hairline
EN  |  VI                    ← language toggle
[ ☀ Day / ☾ Night ]         ← mode toggle (mobile only)
```

**Top row:**
- "MENU" label: `--font-display`, weight 400, `--text-xs`, uppercase, tracking `0.2em`, `color: var(--color-text-muted)`
- Close button (×): 28px × 28px. Background `var(--color-accent)`. Color: white. No border-radius (square, sharp corners — matches Da Maria's red square close button). Positioned flush to the top-right of the drawer. The `×` is `--font-body` weight 300, `font-size: 1rem`.

**Navigation links:**
```css
.drawerLink {
  display: block;
  font-family: var(--font-accent);
  font-weight: 400;
  font-size: var(--text-3xl);          /* Large type — editorial style */
  line-height: 1.15;
  color: var(--color-text-primary);
  padding-block: 0.4rem;
  letter-spacing: -0.02em;
  transition: color var(--duration-fast) var(--ease-out-expo);
  border: none;
  background: none;
}

.drawerLink:hover,
.drawerLink[data-active="true"] {
  color: var(--color-accent);
}
```

**Sub-items under EVENTS (collapsed by default):**
- Indent: `padding-left: 1.5rem`
- Font size: `--text-xl` (smaller than main links)
- Font: `--font-accent`, italic, weight 300
- Color: `--color-text-secondary` at rest, `--color-accent` on hover

**Drawer padding:** `padding: 1.5rem` on all sides.

---

## 6. Hero Section

**File:** `/Website/app/components/ParallaxHero.tsx` + `/Website/app/components/ParallaxHero.module.css`

### Core Concept

The hero is a direct adaptation of Da Maria's newspaper bleed + photograph overlap technique, with the addition of TEMPO's parallax depth layers (from the Debonaïr research brief). The venue name sits so large it crops at the viewport edges. Behind it (or layered through it) is the primary photography.

### Parallax Layer Architecture

Five named layers, from back to front:

| Layer | Element | Parallax Factor | z-index |
|---|---|---|---|
| `layer-bg` | Full-bleed venue photograph (or video loop) | 0.0 (fixed) | 0 |
| `layer-mid` | Atmospheric overlay — linen grain or duotone | 0.2 | 1 |
| `layer-text` | THE VENUE NAME headline (bleed) | 0.4 | 2 |
| `layer-fore` | Foreground illustration motif (bougainvillea sprig) | 0.7 | 3 |
| `layer-ui` | CTA button + subline | 0.0 (fixed to viewport) | 4 |

**Parallax implementation:** Use `transform: translateY()` driven by scroll position. On each scroll event: `layerOffset = scrollY * parallaxFactor`. Apply via JavaScript (no CSS `perspective` trickery — it creates rendering artifacts with `position: fixed` children). Use `will-change: transform` only on actively animating layers.

### Full Hero Spec

**Hero container:**
```css
.hero {
  position: relative;
  width: 100%;
  height: 100svh;             /* Full viewport, safe area aware */
  min-height: 600px;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
}
```

**Background photograph / video:**
```css
.heroBg {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center 40%;
}
```

**Photograph overlay:**
- Day mode: a warm cream-to-transparent gradient at the bottom `linear-gradient(to top, var(--color-bg) 0%, transparent 40%)` — bleeds the photo into the page content below.
- Night mode: same gradient with `--color-bg` (ink) — creates a seamless dark-to-photo transition.
- Additionally: overall darken overlay at `rgba(0,0,0,0.25)` day / `rgba(0,0,0,0.45)` night to ensure text legibility on any photo.

**Hero headline:**
```css
.heroHeadline {
  font-family: var(--font-accent);
  font-size: var(--text-hero);         /* clamp(4rem, 12vw, 10rem) */
  font-weight: 400;
  line-height: var(--leading-tight);   /* 1.1 */
  letter-spacing: var(--tracking-tight); /* -0.03em */
  color: var(--color-surface);         /* White — readable on photo */

  /* Bleed technique */
  width: 100vw;
  position: relative;
  left: 50%;
  transform: translateX(-50%);
  text-align: center;
  white-space: nowrap;
  overflow: visible;
  text-shadow: 0 2px 40px rgba(0,0,0,0.25); /* Subtle legibility shadow */
}
```

Day mode: headline color can shift to `var(--color-text-primary)` if a lighter hero photo is used, with no overlay. Night mode: always cream/white on dark photo.

**Hero subline:**
```css
.heroSubline {
  font-family: var(--font-accent);
  font-style: italic;
  font-weight: 300;
  font-size: var(--text-xl);
  color: var(--color-surface);
  opacity: 0.85;
  letter-spacing: 0.06em;
  text-align: center;
  margin-top: var(--space-4);
}
```

Text: "Specialty Café · Cocktail Bar · Art Gallery · Events" — using `·` (interpunct U+00B7) as separator.

**Hero CTA:**
```css
.heroCta {
  display: inline-flex;
  align-items: center;
  gap: 0.6rem;
  margin-top: var(--space-8);
  font-family: var(--font-display);
  font-weight: 400;
  font-size: var(--text-xs);
  letter-spacing: 0.22em;
  text-transform: uppercase;
  color: var(--color-surface);
  border: 1px solid rgba(247,243,238,0.6);  /* Cream at 60% */
  border-radius: var(--radius-full);
  padding: 0.7rem 1.75rem;
  transition: background var(--duration-fast) var(--ease-out-expo),
              border-color var(--duration-fast) var(--ease-out-expo);
}

.heroCta:hover {
  background: rgba(247,243,238,0.15);
  border-color: rgba(247,243,238,0.9);
}
```

Text: "• RESERVE •" — links to `/reservations`. This is the primary conversion CTA and must be in the first viewport.

### Day/Night Hero Variants

**Day hero:**
- Photograph: morning interior — available light through windows, espresso detail, warm but not golden
- Overall darken overlay: `rgba(0,0,0,0.20)` — minimal
- Headline color: `--color-surface` (white) or `var(--color-text-primary)` (charcoal) depending on photo brightness
- Bottom gradient: `linear-gradient(to top, var(--tempo-cream) 0%, transparent 45%)`

**Night hero:**
- Photograph: evening bar — amber lamp spill, intimate seating, glassware
- Overall darken overlay: `rgba(0,0,0,0.40)` — deeper
- Headline color: `var(--tempo-cream)` — always light on dark
- Bottom gradient: `linear-gradient(to top, var(--tempo-ink) 0%, transparent 45%)`
- The amber in the photograph echoes `--color-accent` (amber) — coherent ambient connection

### Hero Entrance Animation

Elements animate in on page load, staggered:
1. Background photo: `opacity: 0 → 1`, duration `--duration-slower` (1000ms), delay 0ms
2. Hero headline: `opacity: 0, transform: translateY(2rem) → opacity: 1, translateY(0)`, duration 900ms, delay 300ms, `--ease-out-expo`
3. Subline: same pattern, delay 500ms
4. CTA button: same pattern, delay 700ms
5. Foreground illustration: `opacity: 0 → 0.7`, duration 1200ms, delay 900ms

Use `animation-fill-mode: both` on all — no flash of unstyled content.

---

## 7. Reserve Modal

**File:** `/Website/app/components/ReserveModal.tsx` + `/Website/app/components/ReserveModal.module.css`

**Trigger:** Clicking "• RESERVE •" in the nav on mobile, or the hero CTA on small screens where the full reservation page is not appropriate. On desktop, the nav Reserve oval links directly to the `/reservations` page — the modal is a mobile convenience.

### Modal Animation

**Enter:** Scale from 0.94 to 1.0 with fade-in. Not a top-down slide.
```css
@keyframes modalEnter {
  from { opacity: 0; transform: scale(0.94) translateY(1rem); }
  to   { opacity: 1; transform: scale(1) translateY(0); }
}
```
Duration: `var(--duration-slow)` (600ms), `var(--ease-out-expo)`.

**Exit:** Reverse — scale to 0.94, fade out. Duration: `var(--duration-base)` (300ms), `var(--ease-in-expo)`.

**Backdrop:**
```css
.modalBackdrop {
  position: fixed;
  inset: 0;
  background: rgba(26,24,22,0.65);
  backdrop-filter: blur(4px);
  z-index: var(--z-modal);         /* 200 */
  animation: fadeIn var(--duration-base) var(--ease-out-expo);
}
```

### Modal Panel

```css
.modalPanel {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  z-index: calc(var(--z-modal) + 1); /* 201 */
  width: min(480px, 90vw);
  max-height: 85vh;
  overflow-y: auto;
  background: var(--color-surface);
  padding: 2rem 2.5rem;
  border-top: 2px solid var(--color-accent); /* Accent top border — Da Maria trademark */
  box-shadow: var(--shadow-xl);
}
```

### Modal Interior Layout

```
[Bougainvillea sprig illustration — centered, 80px width, opacity 0.7]
BOOK NOW                             ← --font-accent, --text-3xl, --color-accent
──────────────────────────────────   ← hairline
  Call · info@tempohouse.com.vn      ← italic, --color-accent, centered

  [FORM GRID — 2 columns]
  NAME           E-MAIL
  PHONE          GUESTS
  DATE & TIME    (full width below)

  [ RESERVE ]                        ← outlined button, --color-accent, full width
```

**"BOOK NOW" headline:**
```css
font-family: var(--font-accent);
font-weight: 400;
font-size: var(--text-3xl);
color: var(--color-accent);
text-align: center;
letter-spacing: -0.01em;
margin-bottom: var(--space-4);
```

**Contact line above form:**
```css
font-family: var(--font-accent);
font-style: italic;
font-weight: 300;
font-size: var(--text-sm);
color: var(--color-accent);
text-align: center;
letter-spacing: 0.06em;
margin-bottom: var(--space-6);
```

**Form field labels:**
```css
.fieldLabel {
  font-family: var(--font-accent);
  font-style: italic;
  font-weight: 300;
  font-size: var(--text-xs);
  text-transform: uppercase;
  letter-spacing: 0.18em;
  color: var(--color-accent);
  display: block;
  margin-bottom: var(--space-1);
}
```

**Form inputs:**
```css
.fieldInput {
  width: 100%;
  background: var(--color-input-bg);
  border: 1px solid var(--color-input-border);
  border-radius: var(--radius-sm);     /* 2px — barely rounded, rectilinear */
  padding: 0.65rem 0.75rem;
  font-family: var(--font-body);
  font-weight: 300;
  font-size: var(--text-sm);
  color: var(--color-text-primary);
  transition: border-color var(--duration-fast) ease;
}

.fieldInput:focus {
  border-color: var(--color-input-focus);
  outline: none;
}

.fieldInput::placeholder {
  color: var(--color-input-placeholder);
  font-style: italic;
}
```

**Reserve button (outlined, no fill):**
```css
.reserveBtn {
  display: block;
  width: 100%;
  padding: 0.85rem 1.5rem;
  font-family: var(--font-display);
  font-weight: 400;
  font-size: var(--text-xs);
  letter-spacing: 0.24em;
  text-transform: uppercase;
  color: var(--color-accent);
  background: transparent;
  border: 1px solid var(--color-accent);
  border-radius: 0;                    /* Sharp corners — no radius */
  cursor: pointer;
  margin-top: var(--space-6);
  transition: background var(--duration-fast) var(--ease-out-expo),
              color var(--duration-fast) var(--ease-out-expo);
}

.reserveBtn:hover {
  background: var(--color-accent);
  color: var(--color-text-inverse);
}
```

**Close button (top-right of modal):**
- Square 28px × 28px. No border-radius. `background: var(--color-accent)`. Color: white. `×` character.
- Position: `position: absolute; top: 0; right: 0;` — flush to the corner of the modal panel.
- This is an intentional Da Maria pattern — the red square close button. TEMPO adapts it with terracotta/amber depending on mode.

**The TEMPO illustration:**
A fine-line bougainvillea sprig (see Section 15 for full illustration direction). SVG, 80px wide, centered above the headline. Opacity 0.7. In night mode, the illustration SVG strokes use `currentColor` so they automatically shift to amber.

---

## 8. Reservation Page

**Route:** `/reservations`
**File:** `/Website/app/(pages)/reservations/page.tsx` + `page.module.css`

### Split Layout

Da Maria's reservation page is a 50/50 split: left = food photography, right = form. TEMPO adapts this as a 55/45 split (slightly more emphasis on the photograph — the space does the selling).

```css
.reservationsLayout {
  display: grid;
  grid-template-columns: 55fr 45fr;
  min-height: 100vh;
  padding-top: 80px;   /* Nav height clearance */
}

@media (max-width: 768px) {
  .reservationsLayout {
    grid-template-columns: 1fr;
  }
}
```

**Left half (photograph):**
- Full-height photograph, `object-fit: cover`, `object-position: center`
- Day: morning interior shot — light, people in conversation
- Night: evening bar shot — amber, intimate
- No overlay on this side — let the photograph breathe

**Right half (form):**
```css
.formSide {
  background: var(--color-surface);
  padding: clamp(3rem, 8vw, 6rem) clamp(2rem, 5vw, 4rem);
  display: flex;
  flex-direction: column;
  justify-content: center;
}
```

**"BOOK A TABLE" headline:**
```css
font-family: var(--font-accent);
font-weight: 400;
font-size: var(--text-4xl);
color: var(--color-accent);
letter-spacing: -0.02em;
margin-bottom: var(--space-8);
```

Below the headline, a one-line descriptor in `--font-accent`, italic, `--text-sm`:
> *"Reservations recommended for evenings. Walk-ins welcomed during the day."*

**Form structure:** Same field styles as the modal (italic accent labels, minimal inputs, outlined submit button). No two-column grid here — all fields stack single column for this full-page treatment, giving each field room to breathe.

**"VIEW MENU" button:** Below the form. Same outlined style as Reserve button but with `color: var(--color-text-secondary)` and `border-color: var(--color-border-strong)`. Links to `/menus` (Phase 2) or jumps to menu section on Home.

**Note below button:** Walk-in note and hours in `--font-body`, `--text-xs`, `color: var(--color-text-muted)`, `text-align: center`.

### Eat App / Resy Embed

When the Eat App or Resy integration is live, it replaces the custom form on this page. The styled form is a Phase 1 placeholder. The embed should be wrapped in the same `formSide` container with the same typography overrides applied via CSS targeting the embed's root element.

---

## 9. Menu Section

**File:** `/Website/app/components/MenuSection.tsx` + `MenuSection.module.css`

This section is used on the Home page (condensed preview) and the `/cafe` and `/bar` pages (full menus), as well as the future `/menus` page.

### Category Divider

TEMPO's adaptation of Da Maria's `♦ ANTIPASTI ♦` diamond divider:

```css
.categoryDivider {
  display: flex;
  align-items: center;
  gap: var(--space-3);
  margin-block: var(--space-8);
}

.categoryDivider::before,
.categoryDivider::after {
  content: '';
  flex: 1;
  height: 1px;
  background: var(--color-border-accent);
  opacity: 0.4;
}

.categoryName {
  font-family: var(--font-accent);
  font-style: italic;
  font-weight: 300;
  font-size: var(--text-xs);
  text-transform: uppercase;
  letter-spacing: 0.28em;
  color: var(--color-accent);
  white-space: nowrap;
  display: flex;
  align-items: center;
  gap: 0.5em;
}
```

HTML structure:
```html
<div class="categoryDivider">
  <span class="categoryName">◆ CAFÉ ◆</span>
</div>
```

The `◆` (Unicode U+25C6, filled diamond) is used as a separator. It sits inside the `.categoryName` span so it inherits the accent color. Do not put the diamonds in the pseudo-element hairlines.

**Day mode categories:** use terracotta (`--color-accent` = terracotta in day)
**Night mode categories:** use amber (`--color-accent` = amber in night)

### Menu Item Layout

```css
.menuItem {
  display: grid;
  grid-template-columns: 1fr auto;
  grid-template-rows: auto auto;
  gap: 0 var(--space-6);
  padding-block: var(--space-4);
  border-bottom: 1px solid var(--color-border);
}

.menuItemName {
  font-family: var(--font-accent);
  font-weight: 400;
  font-size: var(--text-md);
  color: var(--color-text-primary);
  grid-column: 1;
  grid-row: 1;
  letter-spacing: -0.01em;
}

.menuItemDescription {
  font-family: var(--font-body);
  font-weight: 300;
  font-size: var(--text-sm);
  color: var(--color-text-muted);
  line-height: var(--leading-normal);
  grid-column: 1;
  grid-row: 2;
  margin-top: var(--space-1);
}

.menuItemPrice {
  font-family: var(--font-body);
  font-weight: 400;
  font-size: var(--text-sm);
  color: var(--color-text-primary);
  grid-column: 2;
  grid-row: 1 / span 2;
  align-self: start;
  white-space: nowrap;
}
```

### Café Menu (Day) vs Bar Menu (Night)

**Café menu section:**
- Background: `--color-bg` (cream) — no background change, sits naturally on the page
- Category label color: `--tempo-terracotta` via `--color-accent`
- Item name color: `--tempo-charcoal` via `--color-text-primary`

**Bar menu section:**
- If shown within a night-mode context: ink background, amber category labels
- If shown within the same page as the café menu (e.g. on the Home page at transition): add `data-tempo="night"` scoped to the bar menu container using `.tempo-night` utility class from brand-tokens.css

```html
<!-- Cafe menu — uses page-level day mode -->
<section class="menuSection">...</section>

<!-- Bar menu — localised night mode -->
<section class="menuSection tempo-night">...</section>
```

The `.tempo-night` utility class in brand-tokens.css sets:
```css
.tempo-night {
  --color-accent: var(--tempo-amber);
  background: var(--tempo-ink);
  color: var(--tempo-cream);
}
```

### Three-Card Mobile Layout (from Da Maria)

On the Home page, the menu preview uses a three-card stack adapted from Da Maria's mobile card layout:

```
┌──────────────┐  ┌──────────────┐  ┌──────────────┐
│   THE CAFÉ   │  │  BOOK A      │  │   FIND US    │
│              │  │  TABLE       │  │              │
│  Menu items  │  │  [photo]     │  │  Phone       │
│  preview     │  │  Brief desc  │  │  Hours       │
│  ◆ COFFEE ◆  │  │  [Reserve →] │  │  Address     │
└──────────────┘  └──────────────┘  └──────────────┘
```

On desktop: three equal columns. On mobile: single column stack. Each card:
- Background: `var(--color-card-bg)`
- Border: `1px solid var(--color-card-border)`
- Padding: `var(--space-8)`
- Box shadow: `var(--shadow-sm)`

---

## 10. What's On / Events Cards

**File:** `/Website/app/components/EventCard.tsx` + `EventCard.module.css`
**Route:** `/whats-on`

### Card Anatomy

```
┌────────────────────────────────────────────────┐
│                                                │
│                [PHOTO — 16:9]                  │
│                                                │
├────────────────────────────────────────────────┤
│  ◆ DJ & LIVE MUSIC                             │  ← event type tag
│                                                │
│  The Amber Hour                                │  ← event name (--font-accent)
│  Friday sessions, from 8pm                    │  ← short desc (--font-body)
│                                                │
│  Fri 14 Jun · 8:00pm                          │  ← date/time (--font-body, muted)
│                                         →      │  ← right-arrow CTA
└────────────────────────────────────────────────┘
```

### Card CSS

```css
.eventCard {
  background: var(--color-card-bg);
  border: 1px solid var(--color-card-border);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  cursor: pointer;
  transition: box-shadow var(--duration-base) var(--ease-out-expo),
              transform var(--duration-base) var(--ease-out-expo);
}

.eventCard:hover {
  box-shadow: var(--shadow-lg);
  transform: translateY(-3px);
}

.eventCard__image {
  width: 100%;
  aspect-ratio: 16 / 9;
  object-fit: cover;
  object-position: center;
  transition: transform var(--duration-slow) var(--ease-out-expo);
}

.eventCard:hover .eventCard__image {
  transform: scale(1.04);
}

.eventCard__body {
  padding: var(--space-6);
  flex: 1;
  display: flex;
  flex-direction: column;
}

.eventCard__type {
  font-family: var(--font-accent);
  font-style: italic;
  font-weight: 300;
  font-size: var(--text-xs);
  text-transform: uppercase;
  letter-spacing: 0.22em;
  color: var(--color-accent);
  margin-bottom: var(--space-2);
}

.eventCard__type::before {
  content: '◆ ';
}

.eventCard__name {
  font-family: var(--font-accent);
  font-weight: 400;
  font-size: var(--text-2xl);
  color: var(--color-text-primary);
  letter-spacing: -0.02em;
  line-height: var(--leading-snug);
  margin-bottom: var(--space-2);
}

.eventCard__desc {
  font-family: var(--font-body);
  font-weight: 300;
  font-size: var(--text-sm);
  color: var(--color-text-secondary);
  line-height: var(--leading-normal);
  flex: 1;
}

.eventCard__meta {
  font-family: var(--font-body);
  font-weight: 400;
  font-size: var(--text-xs);
  color: var(--color-text-muted);
  text-transform: uppercase;
  letter-spacing: 0.12em;
  margin-top: var(--space-4);
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.eventCard__arrow {
  color: var(--color-accent);
  font-size: var(--text-lg);
  line-height: 1;
  transition: transform var(--duration-fast) var(--ease-out-expo);
}

.eventCard:hover .eventCard__arrow {
  transform: translateX(4px);
}
```

### Grid Layout

```css
.eventsGrid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: var(--space-6);
  padding: var(--space-section) var(--gutter);
  max-width: var(--content-width);
  margin-inline: auto;
}

@media (max-width: 640px) {
  .eventsGrid {
    grid-template-columns: 1fr;
    gap: var(--space-4);
  }
}
```

**Featured event:** First card in the grid spans `grid-column: 1 / -1` and uses a horizontal layout (image left, content right) at desktop. Same card HTML, different CSS applied via a `data-featured="true"` attribute.

### Filter Bar

Above the grid, a filter row:
```
All  ·  Café & Bar  ·  Gallery  ·  Workshops  ·  DJ & Live Music  ·  Activations
```

```css
.filterBar {
  display: flex;
  align-items: center;
  gap: var(--space-2) var(--space-6);
  flex-wrap: wrap;
  padding: var(--space-8) var(--gutter) var(--space-4);
  max-width: var(--content-width);
  margin-inline: auto;
  border-bottom: 1px solid var(--color-border);
}

.filterBtn {
  font-family: var(--font-body);
  font-weight: 400;
  font-size: var(--text-xs);
  text-transform: uppercase;
  letter-spacing: 0.16em;
  color: var(--color-text-muted);
  background: none;
  border: none;
  padding: 0.25rem 0;
  border-bottom: 1px solid transparent;
  cursor: pointer;
  transition: color var(--duration-fast) ease,
              border-color var(--duration-fast) ease;
}

.filterBtn:hover,
.filterBtn[data-active="true"] {
  color: var(--color-accent);
  border-color: var(--color-accent);
}
```

---

## 11. Footer

**File:** `/Website/app/components/SiteFooter.tsx` + `SiteFooter.module.css`

### Signature Floor Rule

The footer is separated from the page content by the TEMPO signature floor pattern — the existing brand asset used in the coming-soon page (`/public/content/brand-assets/tempo_house_signature_floor.png`), repeated as a horizontal band:

```css
.footer {
  position: relative;
  padding-top: var(--space-16);      /* Extra space for the floor rule */
}

.footer::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 5px;
  background-image: url('/content/brand-assets/tempo_house_signature_floor.png');
  background-size: 52px auto;
  background-repeat: repeat-x;
  opacity: 0.28;                     /* Day mode */
}

[data-tempo="night"] .footer::before {
  opacity: 0.18;                     /* More subtle on dark bg */
}
```

### Four-Column Layout

```css
.footerGrid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: var(--space-12) var(--space-8);
  padding: var(--space-16) var(--gutter) var(--space-10);
  max-width: var(--content-width);
  margin-inline: auto;
}

@media (max-width: 900px) {
  .footerGrid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 540px) {
  .footerGrid { grid-template-columns: 1fr; }
}
```

**Column 1 — Navigation:**
- Column heading: "Navigate" in footer heading style (see below)
- Links: The Venue, Café & Bar, Gallery, What's On, Events, Reservations, Creator Floor, Jobs

**Column 2 — Visit:**
- Column heading: "Visit"
- Address (TBC) — when confirmed, one line
- Hours: listed as Mon–Thurs, Fri–Sat, Sunday
- "Get Directions →" — links to Google Maps. The arrow is the accent color.

**Column 3 — Connect:**
- Column heading: "Connect"
- Email: info@tempohouse.com.vn
- Instagram, Facebook, TikTok — icon + label (no icon-only — accessibility)
- Klaviyo signup — one-line: "Stay in the loop" + email input inline

**Column 4 — Legal:**
- Column heading: "TEMPO House"
- Logo (small, wordmark version) — `32px` height
- Brief tagline: "Coffee in the morning. Connection at night."
- Links: Privacy Policy, © 2026 TEMPO House

**Footer heading style:**
```css
.footerColHead {
  font-family: var(--font-display);
  font-weight: 400;
  font-size: var(--text-xs);
  text-transform: uppercase;
  letter-spacing: 0.22em;
  color: var(--color-text-muted);
  margin-bottom: var(--space-4);
}
```

**Footer link style:**
```css
.footerLink {
  display: block;
  font-family: var(--font-body);
  font-weight: 300;
  font-size: var(--text-sm);
  color: var(--color-text-secondary);
  padding-block: 0.2rem;
  transition: color var(--duration-fast) ease;
}

.footerLink:hover {
  color: var(--color-accent);
}
```

### Footer Bottom Bar

Below the four-column grid, a full-width hairline and a bottom bar:
```css
.footerBottom {
  border-top: 1px solid var(--color-border);
  padding: var(--space-4) var(--gutter);
  max-width: var(--content-width);
  margin-inline: auto;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: var(--space-3);
}
```

Left: "© 2026 TEMPO House" — `--font-body`, `--text-xs`, tracking `0.14em`, uppercase, `--color-text-muted`
Right: Social icons (Instagram, Facebook, TikTok) as SVG only (no labels at this level), `18px`, `color: --color-text-muted`, hover: `--color-accent`

---

## 12. Mobile Patterns

### Breakpoints

```css
/* All defined as max-width — mobile-first overrides */
@media (max-width: 1023px) { /* Below desktop nav */ }
@media (max-width: 767px)  { /* Below tablet */ }
@media (max-width: 540px)  { /* Below large mobile */ }
@media (max-width: 390px)  { /* Small phones (iPhone SE) */ }
```

### Touch Targets

Every interactive element must have a minimum tap target of **44px × 44px** per Apple HIG and WCAG 2.5.5. Where the visual element is smaller (e.g. nav close button at 28px), use a pseudo-element to extend the hit area:
```css
.closeBtn::after {
  content: '';
  position: absolute;
  inset: -8px;    /* Extends the hit target by 8px on each side */
}
```

### Mobile Card Stack (Three-Card Pattern)

The three-card Home page preview (menu, book, contact) becomes a vertical stack on mobile. No carousel — stacks cleanly as block elements. Each card has `border-radius: var(--radius-md)` on mobile (vs sharp corners on desktop) for ease of scroll interaction.

### Mobile Menu Overlay

On mobile, when the hamburger menu is open:
- The gallery illustration (Section 15) appears as the overlay background, `opacity: 0.07`, fixed behind the menu links
- This is TEMPO's adaptation of The Ivy Asia's branded menu overlay concept
- The artwork changes with the current gallery exhibition (CMS-managed, Phase 2 — Phase 1 uses the current brand motif)

```css
.menuDrawer__artwork {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
  opacity: 0.07;
  pointer-events: none;
  z-index: 0;
}
```

All drawer content (`z-index: 1`) sits above this.

### Mobile Spacing Adjustments

- Section padding: `--space-section` computes to `clamp(4rem, 10vw, 10rem)` — reduces naturally to ~4rem on mobile, which is appropriate
- Hero: `height: 100svh` — use `svh` (small viewport height) to avoid iOS address bar height issues
- Form inputs: `font-size: 16px` minimum to prevent iOS zoom-on-focus. Since `--text-sm` can resolve to ~14px, override form inputs specifically: `font-size: max(16px, var(--text-sm))`

### Bottom Navigation Bar (Future / Phase 2)

Consider a bottom sticky bar on mobile for the primary actions:
```
[Cafe]  [Bar]  [Gallery]  [What's On]  [Reserve]
```
This is not required for Phase 1 but should be designed for at a structural level. Leave `padding-bottom: env(safe-area-inset-bottom)` on the mobile body to accommodate it later.

---

## 13. Interaction & Animation Spec

### Named Easing Curves

All defined in `globals.css`:

```css
--ease-out-expo: cubic-bezier(0.16, 1, 0.3, 1);    /* Fast start, settles slowly */
--ease-in-expo:  cubic-bezier(0.7, 0, 0.84, 0);    /* Slow start, fast exit */
--ease-in-out:   cubic-bezier(0.65, 0, 0.35, 1);   /* Smooth in and out */
--ease-spring:   cubic-bezier(0.34, 1.56, 0.64, 1); /* Slight overshoot — use sparingly */
```

**When to use each:**
- `--ease-out-expo`: Element entering the screen (drawers opening, modals appearing, scroll reveals, cards fading in)
- `--ease-in-expo`: Element leaving the screen (drawers closing, modals dismissing)
- `--ease-in-out`: Property transitions that don't involve enter/exit (hover state changes, day/night palette transitions)
- `--ease-spring`: Micro-interactions only (button press feedback, badge bounce) — never for large elements

### Durations

```css
--duration-fast:   150ms;   /* Hover states, color transitions */
--duration-base:   300ms;   /* Button transitions, small state changes */
--duration-slow:   600ms;   /* Drawer open/close, modal enter/exit */
--duration-slower: 1000ms;  /* Hero entrance, page load animations */
```

### Scroll Reveal

All non-hero content elements animate in as the user scrolls them into view. Use `IntersectionObserver` — never scroll event listeners.

**Threshold:** `0.15` — element must be 15% visible before animation triggers.
**Root margin:** `0px 0px -60px 0px` — triggers slightly before the element's bottom reaches the viewport bottom.

**Default scroll reveal animation:**
```css
.scrollReveal {
  opacity: 0;
  transform: translateY(1.5rem);
  transition: opacity var(--duration-slow) var(--ease-out-expo),
              transform var(--duration-slow) var(--ease-out-expo);
}

.scrollReveal.revealed {
  opacity: 1;
  transform: translateY(0);
}
```

**Stagger for grid items:** Each grid item gets a `transition-delay` equal to `index * 80ms` (cap at 320ms). Apply via inline `style` prop in the React component.

### Drawer Open/Close

```
Open:
  Overlay: opacity 0 → 1, duration 400ms, --ease-out-expo, no delay
  Drawer:  translateX(-100%) → 0, duration 600ms, --ease-out-expo, delay 0ms

Close:
  Drawer:  translateX(0) → -100%, duration 300ms, --ease-in-expo, no delay
  Overlay: opacity 1 → 0, duration 300ms, --ease-in-expo, delay 100ms
```

When drawer opens: `document.body.style.overflow = 'hidden'` to prevent scroll bleed.
When drawer closes: restore `overflow`.

### Modal Enter/Exit

```
Enter:
  Backdrop: opacity 0 → 1, 300ms, --ease-out-expo
  Panel:    opacity 0, scale(0.94), translateY(1rem) → opacity 1, scale(1), translateY(0)
            duration 600ms, --ease-out-expo, delay 50ms

Exit:
  Panel:    opacity 1, scale(1) → opacity 0, scale(0.96), duration 200ms, --ease-in-expo
  Backdrop: opacity 1 → 0, 250ms, --ease-in-expo, delay 50ms
```

Dismiss on: backdrop click, Escape key, close button click. `aria-modal="true"` on panel, `role="dialog"`, focus trap active while open.

### Day/Night Transition

The palette transition is CSS custom property animation — the smoothest method available without a re-paint flood.

```css
html {
  transition:
    background-color var(--duration-slower) var(--ease-in-out),
    color var(--duration-slower) var(--ease-in-out);
}

/* All semantic tokens that produce visual color should transition */
body, .siteNav, .menuDrawer, .heroSection {
  transition: background-color var(--duration-slower) var(--ease-in-out),
              color var(--duration-slower) var(--ease-in-out),
              border-color var(--duration-slower) var(--ease-in-out);
}
```

**Note:** CSS custom properties cannot be directly transitioned — the transition applies to the computed color values. The `[data-tempo]` attribute swap triggers the custom property values to change, and the transition rules on the consuming elements smooth the interpolation.

The transition duration is `--duration-slower` (1000ms) — a full second. This is intentional. The palette "deepens" rather than snaps. The user should feel the room shifting, not a switch being flipped.

### Hero Parallax Scroll

```javascript
// ParallaxHero.tsx
const handleScroll = useCallback(() => {
  const scrollY = window.scrollY;
  // Layer rates
  setLayerMid(scrollY * 0.2);
  setLayerText(scrollY * 0.4);
  setLayerFore(scrollY * 0.7);
}, []);

useEffect(() => {
  window.addEventListener('scroll', handleScroll, { passive: true });
  return () => window.removeEventListener('scroll', handleScroll);
}, [handleScroll]);
```

Apply transforms:
```css
.layerMid  { transform: translateY(calc(var(--parallax-mid) * 1px)); }
.layerText { transform: translateY(calc(var(--parallax-text) * 1px)) translateX(-50%); }
.layerFore { transform: translateY(calc(var(--parallax-fore) * 1px)); }
```

Pass values via inline CSS variables on the element. Use `requestAnimationFrame` throttle — don't update on every scroll event.

**Performance:** Add `will-change: transform` to the three parallax layers. Remove `will-change` when the hero is scrolled out of view (IntersectionObserver watching the hero itself) to free GPU memory.

### Event Card Hover (Second Image Reveal)

From the sitemap animation brief: hovering an event card reveals a second image (interior setup).

```css
.eventCard__imageWrap {
  position: relative;
  overflow: hidden;
  aspect-ratio: 16 / 9;
}

.eventCard__imageAlt {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  opacity: 0;
  transition: opacity var(--duration-slow) var(--ease-out-expo);
}

.eventCard:hover .eventCard__imageAlt {
  opacity: 1;
}

.eventCard:hover .eventCard__image {
  opacity: 0;
  transition: opacity var(--duration-slow) var(--ease-in-expo);
}
```

### Reduced Motion

All animations must respect `prefers-reduced-motion`. The global reset in `globals.css` handles this:
```css
@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
}
```

For the parallax specifically, disable the scroll listener entirely:
```javascript
const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
if (!prefersReducedMotion) {
  window.addEventListener('scroll', handleScroll, { passive: true });
}
```

---

## 14. Component Inventory

Every component to be built for Phase 1. File paths follow the Next.js App Router convention.

| Component | File Path | Used On | Priority |
|---|---|---|---|
| `SiteNav` | `app/components/SiteNav.tsx` | All pages | P0 |
| `SiteNav` styles | `app/components/SiteNav.module.css` | — | P0 |
| `MenuDrawer` | `app/components/MenuDrawer.tsx` | SiteNav (mounted inside) | P0 |
| `ReserveModal` | `app/components/ReserveModal.tsx` | Mobile hero, mobile nav Reserve | P0 |
| `DayNightToggle` | `app/components/DayNightToggle.tsx` | SiteNav (desktop), MenuDrawer (mobile) | P0 |
| `ParallaxHero` | `app/components/ParallaxHero.tsx` | Home `/` | P0 |
| `SiteFooter` | `app/components/SiteFooter.tsx` | All pages | P0 |
| `EmailForm` | `app/components/EmailForm.tsx` | Home, Footer | P0 (EXISTS — refactor into brand system) |
| `MenuSection` | `app/components/MenuSection.tsx` | Home, `/cafe`, `/bar`, `/menus` | P0 |
| `CategoryDivider` | `app/components/CategoryDivider.tsx` | MenuSection | P0 |
| `MenuItem` | `app/components/MenuItem.tsx` | MenuSection | P0 |
| `EventCard` | `app/components/EventCard.tsx` | Home (teaser), `/whats-on` | P0 |
| `EventsGrid` | `app/components/EventsGrid.tsx` | `/whats-on` | P0 |
| `FilterBar` | `app/components/FilterBar.tsx` | `/whats-on` | P1 |
| `SplitHero` | `app/components/SplitHero.tsx` | `/reservations`, `/venue`, event pages | P0 |
| `IllustrationMotif` | `app/components/IllustrationMotif.tsx` | Hero, modal, section breaks | P1 |
| `HairlineRule` | `app/components/HairlineRule.tsx` | Nav, section separators, forms | P0 |
| `BleedHeadline` | `app/components/BleedHeadline.tsx` | Hero, section interstitials | P0 |
| `ScrollReveal` | `app/components/ScrollReveal.tsx` | Wraps any content block | P0 |
| `TextureBackground` | Applied via `body::before` in `globals.css` | All pages | P0 |
| **Pages** | | | |
| Home | `app/page.tsx` | — | P0 (EXISTS — full rebuild) |
| Reservations | `app/(pages)/reservations/page.tsx` | — | P0 |
| What's On | `app/(pages)/whats-on/page.tsx` | — | P0 |
| What's On detail | `app/(pages)/whats-on/[slug]/page.tsx` | — | P0 |
| Venue | `app/(pages)/venue/page.tsx` | — | P1 |
| Café | `app/(pages)/specialty-cafe-saigon/page.tsx` | — | P1 |
| Bar | `app/(pages)/cocktail-bar-saigon/page.tsx` | — | P1 |
| Gallery | `app/(pages)/gallery/page.tsx` | — | P1 |
| Events Hub | `app/(pages)/event-venue-hcmc/page.tsx` | — | P0 |
| Event Enquiry | `app/(pages)/events/enquiry/page.tsx` | — | P0 |
| Corporate Events | `app/(pages)/corporate-events/page.tsx` | — | P0 |
| Private Dining | `app/(pages)/private-dining-ho-chi-minh-city/page.tsx` | — | P0 |
| Weddings | `app/(pages)/wedding-venue-ho-chi-minh-city/page.tsx` | — | P1 |
| Product Launches | `app/(pages)/product-launches-venue-saigon/page.tsx` | — | P1 |
| Art Events | `app/(pages)/art-gallery-hire-saigon/page.tsx` | — | P1 |
| Workshops | `app/(pages)/workshop-space-saigon/page.tsx` | — | P1 |
| Birthdays | `app/(pages)/birthday-venue-saigon/page.tsx` | — | P1 |
| Contact | `app/(pages)/contact/page.tsx` | — | P1 |
| Privacy Policy | `app/privacy-policy/page.tsx` | — | EXISTS |
| Not Found | `app/not-found.tsx` | — | EXISTS |

### Component API Notes

**`BleedHeadline`:**
```tsx
<BleedHeadline as="h1" size="hero" italic={false}>
  TEMPO HOUSE
</BleedHeadline>
```
Props: `as` (h1–h6), `size` (hero | 5xl | 4xl | 3xl), `italic`, `color` (defaults to `--color-text-primary`). Applies the bleed CSS class internally.

**`ScrollReveal`:**
```tsx
<ScrollReveal delay={160}>
  <EventCard {...props} />
</ScrollReveal>
```
Props: `delay` (ms, default 0), `once` (default true — don't re-animate on scroll up).

**`CategoryDivider`:**
```tsx
<CategoryDivider label="COFFEE" />
// Renders: ─────── ◆ COFFEE ◆ ───────
```

**`DayNightToggle`:**
```tsx
<DayNightToggle />
```
No props. Reads/writes `document.documentElement.dataset.tempo` and `localStorage`. Exports a `useDayNight()` hook for reading current mode elsewhere.

**`IllustrationMotif`:**
```tsx
<IllustrationMotif variant="bougainvillea" size={80} opacity={0.7} />
```
Props: `variant` (bougainvillea | coffee-ring | crane), `size` (px), `opacity`.

---

## 15. Illustration Language

### The Role of Illustration

Da Maria uses vintage fine-line pen illustrations (rotary telephone, cloche, etc.) as section decorators — not decoration for its own sake, but as a register signal: we are a serious establishment that has been around long enough to use a telephone illustration without irony.

TEMPO's illustration language must do the same work while being specifically rooted in:
1. Saigon / Vietnamese flora and urban context
2. The coffee and hospitality ritual
3. The gallery / arts layer of the brand

### Direction: Fine-Line Botanical + Ritual Objects

**Style specification:**
- Fine-line illustration — single-weight or near-single-weight pen strokes, no fills
- Ink line illustration style: think Redouté botanical prints, but stripped of color. Or: the line drawings in a good mid-century Vietnamese recipe book.
- NOT decorative-maximalist. NOT Art Nouveau. NOT tropical-tourist.
- The drawings should look like something a precise hand drew with a 0.3mm fineliner, then scanned at 1200dpi. Documentary restraint.
- SVG format. Strokes use `currentColor` so they respond to the day/night theme (charcoal day, amber or cream night).
- Minimum detail: enough to read clearly at 60px. No more than needed.

### Subject Catalogue (Phase 1 minimum)

**Primary motif — Bougainvillea sprig:**
- A single spray of bougainvillea, 3–5 bracts, one open flower visible, trailing vine
- Used in: Reserve modal (above headline), section interstitials, menu drawer accent
- Cultural note: bougainvillea is ubiquitous in HCMC's laneways and colonial architecture. It is specifically Saigon flora, not generic "Asian tropical."
- Size variants: 80px (modal), 48px (section break), 120px (hero foreground layer)

**Secondary motif — Coffee ritual object:**
- A Vietnamese phin filter, viewed from above, steam lines rising in three thin curves
- OR: an espresso cup, single-line profile, saucer below
- Used in: café section header, mobile menu overlay (ghost image), menu page
- This is the "telephone illustration" equivalent — signals hospitality seriousness without stating it

**Tertiary motif — Gallery frame:**
- A simple thin-line rectangular frame, slightly baroque proportions (portrait orientation, slightly more decorative corner treatment than a plain rectangle)
- Used in: gallery section, exhibition cards, events page
- The frame implies curation without using the word "curated"

### Sourcing / Creation

**Phase 1 path (fastest):** Commission a Saigon-based illustrator for 3 SVG illustrations (bougainvillea, phin filter, gallery frame). Brief: "Fine-line botanical, single weight, Redouté reference, no fills, SVG, currentColor strokes." Budget: VND 3–5M for all three.

**Alternative:** Use Noun Project or Freepik and find existing fine-line botanicals. Heavy customisation required to ensure the bougainvillea reads as specifically Vietnamese (not generic pink flower). Check licensing carefully — SVG must be usable in commercial context.

**File locations:** `/public/content/illustrations/bougainvillea.svg`, `/public/content/illustrations/phin-filter.svg`, `/public/content/illustrations/gallery-frame.svg`

### Illustration Do's and Don'ts

**Do:**
- Use illustrations at low opacity (0.6–0.8) as section accents, not focal points
- Size them to complement the headline they accompany — never larger than the text they frame
- Use `currentColor` for all SVG strokes — the illustration lives in the brand system, not outside it
- Rotate or flip variants for visual variety (the bougainvillea can trail left or right)
- Let the illustration breathe — white space around it is part of its power

**Do not:**
- Use more than one illustration per section
- Animate the illustration with anything except a very slow `opacity: 0 → 0.7` fade on scroll reveal
- Use fills — line art only
- Use the illustration as a background element (it reads as wallpaper, which kills the editorial quality)
- Use emoji or icon libraries as substitutes — they have the wrong register entirely

---

## Appendix A: CSS Token Quick Reference

For the developer who needs to look up a token without cross-referencing the full brand-tokens.css:

```css
/* Fonts */
--font-display  → Bricolage Grotesque (headlines, UI, nav labels)
--font-body     → Space Grotesk (body, captions, form text)
--font-accent   → Cormorant Garamond (editorial headlines, italic accents, form labels)

/* Type scale (fluid) */
--text-hero  → clamp(4rem, 12vw, 10rem)   /* Bleed headlines */
--text-5xl   → clamp(3rem, 8vw, 6rem)     /* Page-level H1 */
--text-4xl   → clamp(2.5rem, 6vw, 4rem)  /* Section headlines */
--text-3xl   → clamp(2rem, 5vw, 3rem)    /* Card headlines, drawer links */
--text-2xl   → clamp(1.5rem, 4vw, 2rem)  /* Event card names */
--text-xl    → clamp(1.25rem, 3vw, 1.5rem)
--text-lg    → clamp(1.125rem, 2.5vw, 1.25rem)
--text-md    → clamp(1rem, 2vw, 1.125rem)
--text-base  → clamp(0.9rem, 1.5vw, 1rem)
--text-sm    → clamp(0.8rem, 1.2vw, 0.875rem)
--text-xs    → clamp(0.7rem, 1vw, 0.75rem)

/* Spacing */
--space-1 → 0.25rem   --space-4 → 1rem    --space-12 → 3rem
--space-2 → 0.5rem    --space-6 → 1.5rem  --space-16 → 4rem
--space-3 → 0.75rem   --space-8 → 2rem    --space-section → clamp(4rem, 10vw, 10rem)

/* Border radius */
--radius-sm   → 2px      /* Form inputs */
--radius-md   → 6px      /* Cards on mobile */
--radius-lg   → 12px     /* Large panels */
--radius-full → 9999px   /* Pills, ovals */

/* Z-index */
--z-base    → 0    --z-overlay → 100   --z-nav   → 300
--z-raised  → 10   --z-modal   → 200   --z-toast → 400

/* Motion */
--ease-out-expo → cubic-bezier(0.16, 1, 0.3, 1)   /* Default enter */
--ease-in-expo  → cubic-bezier(0.7, 0, 0.84, 0)   /* Default exit */
--ease-in-out   → cubic-bezier(0.65, 0, 0.35, 1)  /* Transitions */
--ease-spring   → cubic-bezier(0.34, 1.56, 0.64, 1) /* Micro-interactions only */

--duration-fast   → 150ms   --duration-slow   → 600ms
--duration-base   → 300ms   --duration-slower → 1000ms
```

---

## Appendix B: Day/Night Auto-Detection Snippet

Place this as an inline `<script>` in the `<head>` (before body render) to prevent flash of wrong mode:

```html
<script>
  (function() {
    var stored = localStorage.getItem('tempo-mode');
    if (stored === 'day' || stored === 'night') {
      document.documentElement.setAttribute('data-tempo', stored);
    } else {
      // Auto-detect by HCMC time (UTC+7)
      var hcmcHour = new Date(Date.now() + 7 * 3600000)
                       .getUTCHours();
      var mode = (hcmcHour >= 7 && hcmcHour < 18) ? 'day' : 'night';
      document.documentElement.setAttribute('data-tempo', mode);
    }
  })();
</script>
```

Place this as the first script inside `<head>` in `app/layout.tsx`. It must run synchronously (not `strategy="afterInteractive"`) to prevent any flash.

---

*Document prepared by Muse, Creative Director, Raging Monk AI. Version 1.0 — 09 June 2026.*
*Questions: contact bailey@ragingmonk.co*
