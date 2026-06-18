# TEMPO House — Design Language

**Version:** 3.32  
**Last updated:** 2026-06-17  
**Stack:** WordPress · Vanilla CSS · Vanilla JS · no build step

---

## 1. Brand Voice (copy principles)

Write like a magazine editor, not a hospitality copywriter. Magazine editors **state**; hospitality copywriters enthuse.

- Short sentences. Declarative. No qualifiers.
- Use specifics as proof (capacities, addresses, times — not adjectives).
- Avoid: curated, immersive, vibrant, experience (as noun), amazing, awesome, nestled, eclectic.
- Place-first, then quality signal, then quiet invitation.
- Temporal awareness: reference the day/night/morning/evening rhythm of the building.

**H1 pattern across inner pages:**
```
[What it is / where it is] + [what earns it the guest]
```
Examples: "Specialty café, District 3. The kind that earns a regular." / "When the café closes, the bar opens."

---

## 2. Typography

| Role        | Font                  | CSS variable       |
|-------------|----------------------|--------------------|
| Display     | Bricolage Grotesque  | `--font-display`   |
| Accent/H    | Cormorant Garamond   | `--font-accent`    |
| Body        | Space Grotesk        | `--font-body`      |

**Scale:** `--text-xs` (0.75rem) → `--text-4xl` (2.25rem). Large display sizes use `clamp()`.

Eyebrows/labels: `--font-display`, 0.65–0.75rem, `letter-spacing: 0.15–0.18em`, uppercase.  
Section titles: `--font-accent`, `clamp(2rem, 5vw, 4.5rem)`, weight 300, `letter-spacing: -0.025em`.  
Body: `--font-body`, weight 300, `line-height: 1.7`.

---

## 3. Colour Palette

```css
--tempo-cream:      #F7F3EE   /* page background (day mode) */
--tempo-cream-dark: #EDE7DE   /* alt section background */
--tempo-ink:        #1A1816   /* ← artwork interiors only, NOT section backgrounds */
--tempo-terracotta: #7c3b3b   /* primary accent */
--tempo-amber:      #DDAA62   /* warm gold — downlight glow, numbers */
--tempo-sage:       #6B7B5E   /* secondary accent */
--tempo-sand:       #C8B89A   /* mid-warm tone */
```

**Semantic tokens** (use these in component code):

| Token                    | Value                     |
|--------------------------|---------------------------|
| `--color-bg`             | `--tempo-cream`           |
| `--color-bg-alt`         | `#F0EBE3`                 |
| `--color-text-primary`   | `--tempo-ink`             |
| `--color-text-secondary` | `rgba(26,24,22,0.72)`     |
| `--color-text-muted`     | `rgba(26,24,22,0.45)`     |
| `--color-accent`         | `--tempo-terracotta`      |
| `--color-border`         | `rgba(26,24,22,0.10)`     |
| `--color-border-strong`  | `rgba(26,24,22,0.22)`     |
| `--color-section-dark`   | `#2D2924` — warm espresso dark for full-width dark sections |
| `--tempo-frame-border`   | `var(--tempo-sand)` — warm sand = natural wood frame on cream (NOT dark walnut) |

---

## 4. Spacing

`--space-{n}` scale: 1=0.25rem, 2=0.5rem, 3=0.75rem, 4=1rem, 5=1.25rem, 6=1.5rem, 8=2rem, 10=2.5rem, 12=3rem, 16=4rem, 20=5rem, 24=6rem.

`--space-section: clamp(4rem, 8vw, 8rem)` — standard vertical section padding.  
`--gutter: clamp(1rem, 5vw, 3rem)` — horizontal page gutter.  
`--content-width: 1280px` — max container width.

---

## 5. Section Patterns

Three core section backgrounds, defined in `inner-page.css`:

| Class                        | Background                              | Use when                        |
|------------------------------|-----------------------------------------|---------------------------------|
| `page-inner__section`        | `--color-bg` (cream)                    | Default                         |
| `page-inner__section--alt`   | `rgba(247,243,238,0.5)` + borders       | Alternating content sections    |
| `page-inner__section--dark`  | `--color-section-dark` + noise texture  | CTA blocks, footers, emphasis   |

Dark sections include a noise grain texture via `::before` and require `position:relative; isolation:isolate`. Child elements get `z-index:1` via `> *`.

> **RULE — NEVER use `--tempo-ink` (#1A1816) as a page section background.** The near-pure-black creates too stark a contrast jump against the cream theme and is visually harsh. Use `--color-section-dark` (#2D2924) instead — it reads as unmistakably dark but with warm espresso undertones that sit harmoniously against cream. Reserve `--tempo-ink` for small dark elements: the interior of `.tempo-frame__artwork`, the `body::before` spotlight overlay, the lightbox backdrop.

---

## 6. Tempo Frame — Art Frame Standard ★

> **This is the site-wide standard for all image frames in art/venue/gallery contexts.**

**File:** `assets/css/components/tempo-frame.css`  
**JS:** `assets/js/tempo-frame.js` (lightbox)  
**Spotlight:** `assets/css/components/spotlight.css` (page dim on hover)

### Design
The tempo-frame simulates a **gallery picture frame with track lighting**. It uses a three-layer anatomy matching the homepage moods frame:
1. **Outer frame border** — warm sand ring (`--tempo-frame-border: var(--tempo-sand)`), 14px solid, reads as natural wood against cream.
2. **Cream mat** — `.tempo-frame__mat`, 18px cream padding inside the border, the museum-style mat mount.
3. **Dark artwork interior** — `.tempo-frame__artwork`, ink-dark background with an always-on amber downlight from above (`::before`) and a hover-activated warm flood from centre (`::after`).

On hover (for `<a>` elements), the page dims via the spotlight system and the frame lifts (`translateY(-3px)`). The flood glow inside the artwork activates.

### HTML Patterns

```html
<!-- Clickthrough to a sub-page (placeholder, no image yet) -->
<a class="tempo-frame" href="/gallery">
  <div class="tempo-frame__mat">
    <div class="tempo-frame__artwork">
      <span class="tempo-frame__label">Gallery Level 1 — Image Coming Soon</span>
      <span class="tempo-frame__caption">Gallery · Level 1</span>
    </div>
  </div>
</a>

<!-- Clickthrough with real image -->
<a class="tempo-frame" href="/gallery">
  <div class="tempo-frame__mat">
    <div class="tempo-frame__artwork">
      <img class="tempo-frame__img" src="image.jpg" alt="Gallery Level 1">
      <span class="tempo-frame__caption">Gallery · Level 1</span>
    </div>
  </div>
</a>

<!-- Lightbox (opens image fullscreen) -->
<a class="tempo-frame" href="image-large.jpg" data-lightbox
   data-lightbox-caption="Gallery Level 1 — TEMPO House">
  <div class="tempo-frame__mat">
    <div class="tempo-frame__artwork">
      <img class="tempo-frame__img" src="image.jpg" alt="…">
      <span class="tempo-frame__caption">Gallery · Level 1</span>
    </div>
  </div>
</a>

<!-- Non-interactive placeholder (div, no href) -->
<div class="tempo-frame">
  <div class="tempo-frame__mat">
    <div class="tempo-frame__artwork">
      <span class="tempo-frame__label">Neighbourhood — Image Coming Soon</span>
      <span class="tempo-frame__caption">Pasteur Street · District 3</span>
    </div>
  </div>
</div>
```

### Modifier Classes

| Class                      | Effect                                              |
|----------------------------|-----------------------------------------------------|
| `tempo-frame--placeholder` | Switches artwork canvas to `--tempo-frame-canvas` (warm ivory day / warm espresso night). Labels use dark/cream text automatically. **No borders, dashes, or inner lines of any kind.** The artwork is a plain warm canvas. |
| `tempo-frame--portrait`    | Sets `.tempo-frame__artwork { aspect-ratio: 2/3 }`  |
| `tempo-frame--square`      | Sets `.tempo-frame__artwork { aspect-ratio: 1/1 }`  |
| `tempo-frame--wide`        | Sets `.tempo-frame__artwork { aspect-ratio: 16/9 }` |
| `tempo-frame--cinematic`   | Sets `.tempo-frame__artwork { aspect-ratio: 21/9 }` |

> **RULE — NO INNER BORDERS ON PLACEHOLDER FRAMES.** Never add a dashed, dotted, solid, or inset border inside `.tempo-frame__artwork` on placeholder frames. The placeholder state is a warm canvas waiting for photography — it should look like an empty gallery wall, not a wireframe or developer marker. The only visual difference from a frame with a real image is the canvas colour and the label.

Default aspect ratio is `4/3` (on `.tempo-frame__artwork`).

To override aspect ratio from a page-level selector, target `.your-context .tempo-frame__artwork { aspect-ratio: … }`.

### Swapping Placeholders for Real Images

When real photography is available:
1. Remove `tempo-frame--placeholder` class (if present).
2. Add `<img class="tempo-frame__img" src="…" alt="…">` inside `.tempo-frame__artwork`.
3. For lightbox: change `<div>` wrapper to `<a>`, add `data-lightbox`, set `href` to full-res URL.
4. Optionally keep `<span class="tempo-frame__caption">…</span>` inside `.tempo-frame__artwork`.

### When NOT to use tempo-frame

Use `page-inner__img-placeholder` (the light cream placeholder) for:
- Editorial content images on light backgrounds (café menu, bar illustrations).
- Simple decorative images that don't warrant gallery treatment.

Use `tempo-frame` for:
- All venue/gallery photography.
- Hero images in dark-context sections.
- Showcasing architecture, space quality, atmosphere.

---

## 7. Tempo Carousel — Card Grid Standard ★

> **Standard for all card grids on tablet and mobile.**

**File:** `assets/css/components/tempo-carousel.css`  
**JS:** `assets/js/tempo-carousel.js`

### Behaviour
- **Desktop (>900px):** 3-column grid — no carousel, no nav.
- **Tablet (600–900px):** 2-column grid — no carousel, no nav.
- **Mobile (<600px):** Horizontal scroll with `scroll-snap-type: x mandatory`. Each card 82% wide. Pagination dots + prev/next buttons shown.

### HTML Pattern

```html
<div class="tempo-carousel" data-carousel>
  <div class="page-inner__card-grid tempo-carousel__track">
    <article class="page-inner__card">…</article>
    <article class="page-inner__card">…</article>
    <!-- … -->
  </div>

  <nav class="tempo-carousel__nav" aria-label="[Section] navigation">
    <button class="tempo-carousel__btn tempo-carousel__prev" aria-label="Previous">
      <svg viewBox="0 0 14 14" fill="none">
        <path d="M9 11L5 7l4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </button>
    <div class="tempo-carousel__dots"></div><!-- Dots injected by JS -->
    <button class="tempo-carousel__btn tempo-carousel__next" aria-label="Next">
      <svg viewBox="0 0 14 14" fill="none">
        <path d="M5 11l4-4-4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </button>
  </nav>
</div>
```

The JS reads `.tempo-carousel__track > *` as carousel items and auto-generates dots to match.

---

## 8. Architectural Line Drawing — Art Style Standard ★

> **This is the canonical art style for all floor plans, elevations, and any technical line drawing in the TEMPO brand — on the website, in event packs, and in print collateral.**

### Origin & Rationale

The style derives from the architectural drawings produced for the TEMPO House fitout — AutoCAD-style black ink on white, with red dimension annotations. It reads as **precise, intentional, and understated**: a building that takes itself seriously without performing it. On-brand because TEMPO is a designed space, not a styled one.

### Visual Grammar

| Element | Specification |
|---------|---------------|
| **Lines** | `#1A1816` (tempo-ink), varying weight by hierarchy |
| **Wall sections** | 45° diagonal hatching on cream field (`#EDE7DE`), wall thickness 300 mm |
| **Dimension lines** | `#7c3b3b` (tempo-terracotta), 0.8 px; tick marks (not arrows) at ends |
| **Dimension text** | 5 pt, Space Grotesk, terracotta, `letter-spacing: 0` |
| **Space labels** | 4–7 pt, Space Grotesk, `--tempo-ink`, `letter-spacing: 0.8–1.2` |
| **Title / heading** | Cormorant Garamond, `letter-spacing: 2.5`, weight 600 |
| **Tree symbols** | Circle + 4 radial cross-lines (× pattern) + trunk dot; `rgba(107,123,94,0.12)` fill (sage) |
| **Background** | `#FFFFFF` (white) for export/PDF; `#F7F3EE` (cream) for web embed |
| **Road surface** | `#E2DDD5` flat fill; centreline dashed grey |
| **Outdoor paving** | 12 × 12 grid pattern, `#F7F3EE` + `#D8D0C4` lines |

### SVG Line-Weight Hierarchy

```
Exterior cut walls:       stroke-width 2.0    (primary element)
Interior partition walls: stroke-width 1.5 (or solid fill)
Dimension lines:          stroke-width 0.8
Extension lines:          stroke-width 0.55
Window glazing bars:      stroke-width 0.55
Tree canopy outlines:     stroke-width 0.7
Space label text:         4–7 pt
Dimension text:           5 pt
```

### Files

```
Website/public/floor-plans/ground-floor.svg     ← Ground floor (main venue + terrace)
Website/public/floor-plans/site-plan.svg        ← Site overview (future)
Documentation/assets/floor-plans/               ← High-res export versions for print / event packs
```

### SVG viewBox Convention

All floor plan SVGs use `viewBox="0 0 520 450"` (or proportional variant). Scale is **1:100** — 1 SVG unit = 100 mm. The viewBox coordinate units are documented in SVG comments. All dimension annotations are in **millimetres**, written as e.g. `13 500` (space-separated thousands).

### Embedding in the Website

```tsx
{/* Next.js — venue page, static SVG */}
<img
  src="/floor-plans/ground-floor.svg"
  alt="TEMPO House ground floor plan …"
  className={styles.planImage}
  width={1040}
  height={900}
/>
```

For WordPress pages, use the `venue-floorplan` interactive component (see §8 below) which wires the SVG elevation to the interactive panel.

### Do / Don't

**Do:** Keep line weights light. The plan should feel like a sketch, not engineering.  
**Do:** Use Space Grotesk for all labels — consistent with site type system.  
**Do:** Show plants with the botanical symbol (circle + cross + trunk dot).  
**Don't:** Use solid black fill for floor areas — only walls and bar counters are solid.  
**Don't:** Add colour fills to rooms — rooms are white; the drawing breathes.  
**Don't:** Use a grid background on the SVG itself — let the page background provide context.

---

## 9. Venue Floor Plan Explorer (WordPress) ★

> **Interactive building elevation + floor plan panel. Venue page only.**

**CSS:** `assets/css/components/venue-floorplan.css`  
**JS:** `assets/js/venue-floorplan.js`  
**Template section:** `page-templates/page-venue.php` — `section.venue-floorplan`

### Architecture
Full-width architectural drawing-board section (cream `--color-bg-alt` + subtle graph-paper grid overlay). Asymmetric 5fr/7fr split:
- **Left:** SVG building cross-section / elevation. Three `<g class="fp-floor" data-floor="…">` zones — `terrace`, `level1`, `ground`. Each contains `<rect class="fp-zone-hit">` (transparent hit area) and `<rect class="fp-zone-border">` (terracotta border on hover/active).
- **Right:** Detail panel with label, title, description, floor plan image (`.venue-floorplan__plan`), and spec grid.

**Desktop:** Hover `fp-floor` zones → popup with capacity stats. Click → update right panel.  
**Mobile (≤900px):** Horizontal tab list (`.venue-floorplan__tab`) replaces hover. Tabs update the right panel. Popup hidden.

### Swapping in Real Drawings

**Building elevation (left):**
1. Replace the `<svg class="venue-floorplan__svg">` with the real architectural drawing.
2. Maintain the `<g class="fp-floor" data-floor="[terrace|level1|ground]">` wrapper groups.
3. Inside each `<g>`, keep `<rect class="fp-zone-hit">` (covers the entire floor zone area) and `<rect class="fp-zone-border">` (same dimensions — shows terracotta stroke on hover).
4. SVG can be inline or an `<img>` — but `<img>` cannot receive CSS hover states, so **keep it inline**.

**Floor plan images (right panel):**
1. Update the `plan` path in `FLOOR_DATA` inside `venue-floorplan.js`:
   ```js
   level1: {
     plan: '/wp-content/uploads/tempo-house-level1-floorplan.jpg',
     …
   }
   ```
2. The JS will insert the `<img>` and remove `tempo-frame--placeholder` automatically.

### Floor Data Schema (in venue-floorplan.js)
```js
{
  label:  'Level 1 — The Gallery',
  title:  'Column-free gallery floor.',
  desc:   'Body copy for the right panel.',
  stats:  [
    { num: '80',     unit: 'standing'   },
    { num: '50',     unit: 'seated'     },
    { num: '200m²',  unit: 'floor area' }
  ],
  use:   'Short comma-separated use cases for the popup',
  plan:  '/path/to/floorplan.jpg'  // empty string = placeholder
}
```

---

## 10. Spotlight — Dark Overlay System

**File:** `assets/css/components/spotlight.css`

The `body::before` overlay dims the page when specific interactive elements are hovered. Currently triggered by:
- `.moods__frame` — moods section frames
- `.event-card` — What's On event cards
- `a.tempo-frame` — any art frame link

The hovered element is lifted to `z-index: 260` (above the overlay at `z-index: 250`) and receives a `filter: drop-shadow()` amber glow.

To add a new element to the spotlight system:
```css
body:has(.your-element:hover)::before {
  background: rgba(10, 8, 6, 0.48);
}
body:has(.your-element:hover) .your-element:hover {
  z-index: 260;
  position: relative;
  filter:
    brightness(1.06)
    drop-shadow(0 0 16px rgba(221, 170, 98, 0.36))
    drop-shadow(0 0 44px rgba(221, 170, 98, 0.18));
}
```

Note: the parent elements of `.your-element` must not create a stacking context (no `transform`, `filter`, `will-change: transform` on ancestors) or the `z-index: 260` cannot escape.

---

## 11. File Structure

```
WordPress/themes/tempohouse/
  assets/
    css/
      tokens.css                   ← design tokens (single source of truth)
      base.css                     ← reset + global styles
      components/
        nav.css
        hero.css
        moods.css
        events.css
        spotlight.css              ← dark overlay system
        tempo-frame.css   ★        ← art frame component
        tempo-carousel.css ★       ← card grid carousel
        venue-floorplan.css ★      ← venue floor plan explorer
      pages/
        inner-page.css             ← shared inner page structure (BEM: page-inner__)
        venue.css                  ← /venue page specifics (BEM: page-venue__)
        [page].css                 ← per-template overrides
    js/
      drag-scroll.js
      hero.js
      moods.js
      events.js
      time-switcher.js
      spotlight.js
      tempo-frame.js    ★          ← lightbox
      tempo-carousel.js ★          ← carousel logic
      venue-floorplan.js ★         ← floor plan interactions
  page-templates/
    page-venue.php
    page-[name].php
  functions.php                    ← enqueue registration
```

**Next.js app (website):**

```
Website/public/
  floor-plans/
    ground-floor.svg               ← Ground floor plan SVG (1:100, 29 KB)
    site-plan.svg                  ← [future] Full site overview
  fonts/
  content/
Documentation/design/
  design-language.md               ← This file
Documentation/assets/floor-plans/  ← [future] High-res exports for print / event packs
```

---

## 12. BEM Namespaces

| Namespace          | Scope                           |
|--------------------|---------------------------------|
| `page-inner__`     | All inner page structural elements (shared) |
| `page-venue__`     | /venue page specifics           |
| `page-cafe__`      | /cafe page specifics            |
| `page-bar__`       | /bar page specifics             |
| `tempo-frame__`    | Art frame component             |
| `tempo-carousel__` | Carousel component              |
| `venue-floorplan__`| Floor plan explorer             |
| `fp-`              | SVG element classes (floor plan SVG only) |

---

## 13. Dev Environment

- **Git repo:** `/Users/baileywang/Library/CloudStorage/GoogleDrive-bailey@ragingmonk.co/My Drive/Websites/tempohouse.com.vn/`
- **Dev mount (wp-env):** `/Users/baileywang/Desktop/AppDev/TempoHouse-WP/themes/tempohouse/`
- **Docker container:** `wp-env-tempohouse-wp-9fe0e8e2`
- **Dev URL:** `http://localhost:8888`

After any file edit: sync to dev mount, then clear OPcache:
```bash
cp "GoogleDrive/…/file.php" "~/Desktop/AppDev/TempoHouse-WP/…/file.php"
docker exec wp-env-tempohouse-wp-9fe0e8e2-wordpress-1 php -r "opcache_reset();"
```

**NEVER edit the live production site directly. Dev only.**
