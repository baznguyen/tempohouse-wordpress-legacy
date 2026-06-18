# TEMPO House Elevation Drawing — Implementation Notes

## Location
Inline SVG embedded in:
`WordPress/themes/tempohouse/page-templates/page-venue.php` — lines 260–581

Styles live in:
`WordPress/themes/tempohouse/assets/css/components/venue-floorplan.css`

## Rendering Engine
Hand-authored SVG (no library). Interactive — each floor zone is a `<g>` with `data-floor` attribute, `tabindex="0"`, `role="button"`. JavaScript in `assets/js/venue-floorplan.js` handles hover/click to reveal floor details in the adjacent panel.

## viewBox
`0 0 468 465` — square-ish, portrait.

## Card / Container Style
The SVG sits inside `.venue-floorplan__svg-wrap`:
- `background: var(--tempo-cream)` — warm cream paper feel
- `border: 1px solid var(--color-border-strong)`
- `box-shadow: 0 2px 20px rgba(26,24,22,0.07), 0 1px 4px rgba(26,24,22,0.04), inset 0 0 0 1px rgba(26,24,22,0.04)`
- `padding: var(--space-8) var(--space-6) var(--space-6)`

Caption is CSS-generated via `::after`:
```css
content: '218c Pasteur  ·  District 3  ·  HCMC  ·  Elevation';
font-family: var(--font-display); /* Space Grotesk */
font-size: 0.58rem;
font-weight: 500;
letter-spacing: 0.16em;
text-transform: uppercase;
color: var(--color-text-muted);
border-top: 1px solid var(--color-border);
```

## SVG Class System (CSS-driven)

All visual properties come from CSS class selectors. The SVG itself uses no inline styles.

| Class | Stroke | Stroke-width | Fill | Notes |
|---|---|---|---|---|
| `.fp-walls rect/path` | `rgba(26,24,22,0.72)` | 1.8 | none | Outer structure |
| `.fp-foundation` | `rgba(26,24,22,0.72)` | 2.5 | none | Ground slab |
| `.fp-detail` | `rgba(26,24,22,0.22)` | 1 | none | Cornices, rails, treads |
| `.fp-detail-dash` | `rgba(26,24,22,0.15)` | 0.8 | none | `stroke-dasharray: 3 8` — pergola slats |
| `.fp-floor-line` | `rgba(26,24,22,0.42)` | 1.5 | none | Floor level dividers |
| `.fp-track-light` | none | — | `rgba(180,130,50,0.65)` | Amber track lighting dots (circles r=4.5) |
| `.fp-window` | `rgba(26,24,22,0.38)` | 1.2 | `rgba(26,24,22,0.05)` | Arched window paths |
| `.fp-door` | `rgba(26,24,22,0.38)` | 1.2 | `rgba(26,24,22,0.05)` | Door panels |
| `.fp-arch` | `rgba(26,24,22,0.38)` | 1.2 | none | Arch outlines |
| `.fp-counter` | `rgba(26,24,22,0.28)` | 1.2 | `rgba(26,24,22,0.06)` | Café/bar counters |
| `.fp-seat` | `rgba(26,24,22,0.20)` | 1 | none | Lounge seating |
| `.fp-stair` | `rgba(26,24,22,0.22)` | 0.8 | none | Stair treads |
| `.fp-dim-line` | `rgba(26,24,22,0.20)` | 0.8 | none | Dimension lines |
| `.fp-level-label` | — | — | `rgba(26,24,22,0.40)` | `font-size: 8px`, Space Grotesk, 0.12em letter-spacing |
| `.fp-floor-label--right` | — | — | `rgba(26,24,22,0.42)` | `font-size: 9px`, right-side bracket labels |

## Interactive Overlays (per floor zone)
Each `<g class="fp-floor">` contains:
- `fp-floor-bg` — transparent rect; fills `rgba(123,59,59,0.10)` on active (terracotta tint)
- `fp-zone-hatch` — diagonal hatch pattern (`rgba(123,59,59,0.16)`, 6×6, 45°, `stroke-width: 0.65`) — `opacity: 0` default, fades in on hover/active
- `fp-zone-glow` — top-to-bottom amber gradient (`rgba(221,170,98,0.28)→0`) — same fade
- `fp-zone-hit` — transparent click target
- `fp-zone-border` — terracotta border `rgba(123,59,59,0.55)` on hover/active

## Colour Tokens Used
- **Ink**: `rgba(26,24,22,…)` — the `--color-ink` / `#1A1816` family, alpha-varied by element weight
- **Terracotta accent**: `rgba(123,59,59,…)` — the `--color-accent` / `#7c3b3b` family, for hover states
- **Amber track light**: `rgba(180,130,50,0.65)` → `rgba(221,170,98,1.0)` when active (the `--tempo-amber` token)
- **Background**: `var(--tempo-cream)` — approximately `#FAF6EF`

## Building Structure in SVG
- Parapet/roof crown: stacked `<rect>` elements (widths tapering inward, y=2–47)
- Left/right outer walls: tall `<rect>` x=62,310 width=8, y=45–420
- Floor zones: y ranges — Level 2: 45–127, Level 1: 127–275, Ground: 275–420
- Staircase shaft: right side, x=294–318, extends full height; treads are `<line>` elements every 12px
- Right-side labels: bracket line + tick mark + `<text>` with two `<tspan>` lines

## Notes for Future Sketches

1. **Consistent weight hierarchy**: Outer walls at 1.8px → floor lines at 1.5px → details at 1px → stairs/dims at 0.8px. Never heavier than outer walls.
2. **No fill on structural elements** — they read as outline drawings, ink on cream. Only counters and windows have subtle ink fills.
3. **Hatch overlay is interactive** — if producing a static SVG (e.g. the floor plan), bake the hatch in directly rather than toggling via CSS.
4. **Amber dots** for track lighting are the single warmest accent in the drawing — use sparingly, max 5–6 per floor zone.
5. **Terracotta only on hover/active** — resting state is ink-on-cream. For static SVGs, use ink labels not terracotta.
6. **Caption via CSS** — the elevation relies on a `::after` pseudo-element for the caption. For a standalone SVG `<img>`, bake the caption as an SVG `<text>` instead.
7. **Font**: `var(--font-display)` = Space Grotesk. Fall back to `'Inter', sans-serif`.
8. **Label style**: `font-size: 8–9px`, `font-weight: 500`, `letter-spacing: 0.10–0.18em`, `text-transform: uppercase`.
