# Event Layout Designer — Full Spec
**Date:** 2026-06-19  
**Status:** Spec — pending build  
**Plugin:** `tempohouse-reservations` (WordPress)  
**Stack:** Konva.js (canvas), vanilla JS IIFE, jsPDF (PDF export), PHP REST API  
**Related:** `floor-plan-builder.js` (reservations floor plan editor — distinct system, same plugin)

---

## Overview

The Event Layout Designer is a new admin module inside the `tempohouse-reservations` plugin for creating, managing, and sharing scale-accurate floor plan layouts for private events at TEMPO House. It is distinct from the reservations floor plan editor:

| | Reservations Floor Plan | Event Layout Designer |
|---|---|---|
| Purpose | Live table management during service | Design and communicate event-specific layouts |
| Who uses it | Floor staff during service | Events team + clients |
| Furniture | Fixed daily setup | Event-specific, temporary |
| Output | Live booking overlay | Shareable PDF + client-facing link |
| Saving | One canonical layout per floor | Many named layouts per event |

---

## 1. WordPress Integration

### Admin Menu
New sub-menu item under "Reservations" admin menu:
- **Label:** "Event Layouts"
- **Slug:** `thr-event-layouts`
- **Capability:** `manage_options`

### Admin Page Structure
```
┌─────────────────────────────────────────────────────────┐
│  [← Reservations]   EVENT LAYOUTS          [+ New Layout]│
├────────────────────┬────────────────────────────────────┤
│  EVENT LIST        │  CANVAS + TOOLBOX (right 75%)      │
│  (left sidebar)    │                                     │
│  Search…           │  ┌─ Toolbar ───────────────────┐   │
│  ─────────         │  │ [Rect▼][Round][Square][Stool│   │
│  ▸ Cocktail Night  │  │  Chair][Centrepiece][Wall▼] │   │
│    19 Jul · 18:00  │  │  [Projector][Lounge][Tables▼│   │
│  ▸ Wine Dinner     │  │  Side] ║ [Zone][Notation]   │   │
│    24 Jul · 19:30  │  │  ─────────────────────────  │   │
│  ▸ Art Exhibition  │  │  [Select] [Snap ⌗] [Undo]   │   │
│    01 Aug · 17:00  │  └─────────────────────────────┘   │
│  ─────────         │                                     │
│  + New Layout      │         [Canvas]                    │
│                    │                                     │
│  [Properties]      │                                     │
│  Name: …           │                                     │
│  Event type: …     │                                     │
│  Date: …           │                                     │
│  Time: …           │                                     │
│  Notes: …          │                                     │
│                    │                                     │
│  [Share Link]      │                                     │
│  [Download PDF]    │                                     │
│  [Delete]          │                                     │
└────────────────────┴────────────────────────────────────┘
```

---

## 2. Database Schema

### `wp_thr_event_layouts`
```sql
CREATE TABLE wp_thr_event_layouts (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name          VARCHAR(255) NOT NULL,
  event_type    VARCHAR(100),            -- 'cocktail' | 'seated_dinner' | 'theatre' | 'gallery' | 'custom'
  event_date    DATE,
  event_time    TIME,
  notes         TEXT,
  share_token   VARCHAR(64),             -- unique token for public share URL
  share_enabled TINYINT(1) DEFAULT 0,   -- 0 = private, 1 = link active
  created_at    DATETIME NOT NULL,
  updated_at    DATETIME NOT NULL,
  created_by    BIGINT UNSIGNED,         -- wp_users.ID
  PRIMARY KEY (id),
  UNIQUE KEY (share_token)
);
```

### `wp_thr_event_layout_items`
```sql
CREATE TABLE wp_thr_event_layout_items (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  layout_id     INT UNSIGNED NOT NULL,
  item_type     VARCHAR(50) NOT NULL,    -- see furniture type keys below
  item_variant  VARCHAR(50),             -- e.g. '800x1200' for rect, '1500' for backdrop
  x             FLOAT NOT NULL,
  y             FLOAT NOT NULL,
  rotation      FLOAT DEFAULT 0,
  pax           TINYINT DEFAULT 0,       -- 0 = not a seating item
  label         VARCHAR(100),            -- custom table label
  zone_id       INT UNSIGNED,            -- FK to wp_thr_event_zones
  join_group    VARCHAR(36),             -- UUID shared by joined tables
  notation_ref  VARCHAR(20),             -- e.g. 'A1' for notation pin
  PRIMARY KEY (id),
  KEY (layout_id)
);
```

### `wp_thr_event_zones`
```sql
CREATE TABLE wp_thr_event_zones (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  layout_id     INT UNSIGNED NOT NULL,
  name          VARCHAR(100) NOT NULL,
  hex_color     CHAR(7) NOT NULL DEFAULT '#C76E4B',
  x             FLOAT NOT NULL,
  y             FLOAT NOT NULL,
  width         FLOAT NOT NULL,
  height        FLOAT NOT NULL,
  PRIMARY KEY (id),
  KEY (layout_id)
);
```

### `wp_thr_event_notations`
```sql
CREATE TABLE wp_thr_event_notations (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  layout_id     INT UNSIGNED NOT NULL,
  ref           VARCHAR(20) NOT NULL,    -- auto-assigned e.g. 'A', 'B', 'C' or '1', '2'
  x             FLOAT NOT NULL,
  y             FLOAT NOT NULL,
  title         VARCHAR(100),            -- short label shown on canvas pin
  body          TEXT,                    -- full note shown on hover + PDF appendix
  staff_only    TINYINT(1) DEFAULT 0,   -- 1 = hidden from client share link
  PRIMARY KEY (id),
  KEY (layout_id)
);
```

---

## 3. REST API Endpoints

All under namespace `thr/v1`:

| Method | Path | Purpose |
|---|---|---|
| GET | `/event-layouts` | List all layouts |
| POST | `/event-layouts` | Create layout |
| GET | `/event-layouts/{id}` | Get single layout with items, zones, notations |
| PUT | `/event-layouts/{id}` | Update layout metadata |
| DELETE | `/event-layouts/{id}` | Delete layout |
| POST | `/event-layouts/{id}/save` | Full save: items + zones + notations (batch upsert) |
| POST | `/event-layouts/{id}/share` | Enable/disable share link; returns share URL |
| GET | `/event-layouts/shared/{token}` | Public endpoint (no auth) for client view |

---

## 4. Furniture Toolbox — Complete Specification

### Scale System
- Canvas coordinate unit: `1 unit = 10mm` at default zoom (1:50 scale visual)
- Default zoom renders 1 unit as 4px (so 1mm = 0.4px, 3m room = 120 canvas units)
- All dimensions below given in mm → stored in DB as canvas units (mm / 10)

### Toolbar Layout
Horizontal toolbar across top of canvas. Items grouped with separator dividers:

```
[Tables ▼] [Round] [Square] [Stool] [Chair] | [Centrepiece] [Backdrop ▼] [Projector] | [Lounge] [Coffee ▼] [Side] | [Zone] [Notation] || [Select] [Snap] [Undo] [Redo] [Fit] [Zoom+] [Zoom-]
```

### Furniture Types — Detailed Spec

#### Rectangle Tables
**Toolbar item:** "Rect ▼" (dropdown with size options)

| Variant key | Label | Width (mm) | Height (mm) | Default Pax | Max Pax |
|---|---|---|---|---|---|
| `rect-800x1200` | 800 × 1200 | 800 | 1200 | 6 | 8 |
| `rect-800x1500` | 800 × 1500 | 800 | 1500 | 6 | 8 |
| `rect-600x1200` | 600 × 1200 | 600 | 1200 | 4 | 6 |
| `rect-500x1200` | 500 × 1200 | 500 | 1200 | 4 | 6 |

Chairs drawn on long edges (no chairs on short ends by default). When joined, chairs auto-remove from the shared edge.

#### Round Table
**Toolbar item:** "Round"

| Variant key | Label | Radius (mm) | Default Pax | Max Pax |
|---|---|---|---|---|
| `round-700` | Ø700 Round | 700 | 4 | 4 |

Chairs drawn evenly spaced around circumference.

#### Square Table
**Toolbar item:** "Square"

| Variant key | Label | Size (mm) | Default Pax | Max Pax |
|---|---|---|---|---|
| `square-700` | 700 Square | 700 × 700 | 4 | 4 |

Chairs drawn 1 per side.

#### Bar Stool Chair
**Toolbar item:** "Stool"  
Single seat element, no pax assignment in the pax panel. Drawn as a circle (Ø400mm).

#### Theatre Chair (Single Chair)
**Toolbar item:** "Chair"  
Single seat, no table. For theatre-style rows. No pax panel. Drawn as small rectangle with back indicator.

#### Flower Centrepiece
**Toolbar item:** "Centrepiece"  
Decorative element. Drawn as a stylised circle with petal outline. No pax. Placed on a table surface (z-index above table but below chairs). Snaps to table centre when dragged near a table.

#### Media Wall / Backdrop Stand
**Toolbar item:** "Backdrop ▼" (dropdown)

| Variant key | Label | Width (mm) | Height (mm) |
|---|---|---|---|
| `backdrop-1200` | 1.2m Backdrop | 1200 | 2000 |
| `backdrop-1500` | 1.5m Backdrop | 1500 | 2000 |
| `backdrop-2000` | 2m Backdrop | 2000 | 2000 |

Drawn as a tall rectangle with diagonal cross hatch fill (indicates it's a structural/prop element).

#### Projector
**Toolbar item:** "Projector"  
Single equipment item. Drawn as a small filled rectangle with a trapezoid projection cone in front. No pax. Rotatable (indicates projection direction).

#### Lounge
**Toolbar item:** "Lounge"  
Drawn as a rounded rectangle (sofa profile). Width 1800mm, Depth 800mm. Pax: 3 default, max 4.

#### Coffee Tables
**Toolbar item:** "Coffee ▼" (dropdown)

| Variant key | Label | Size (mm) |
|---|---|---|
| `coffee-square` | Square Coffee | 600 × 600 |
| `coffee-round` | Round Coffee | Ø600 |
| `coffee-rect` | Rect Coffee | 1200 × 600 |

No pax. Drawn as solid filled rectangle/circle (smaller than dining tables, distinct fill style).

#### Side Table
**Toolbar item:** "Side"  
Single small table. Ø450mm circle. No pax. Used alongside lounges or against walls.

---

## 5. Pax System

### Which items have pax
All items with seating capability except: Bar Stool, Theatre Chair, Centrepiece, Backdrop, Projector, Coffee Tables, Side Table.

### Pax Panel (appears in floating properties card on item select)
```
┌─────────────────────────┐
│ ■ Rect 800×1200         │
│ Table label: [T-01   ]  │
│                         │
│ Seated: [─ 6 ─]  Max 8  │
│ ▓▓▓▓▓▓░░ (visual bar)   │
│                         │
│ [Rotate: 0°]            │
│ [Delete]                │
└─────────────────────────┘
```

- Stepper: − / + with number field
- Max pax enforced (cannot exceed `max_pax` for the variant)
- Default pax pre-filled on place
- Visual bar shows occupancy at a glance

### Hover Tooltip
Hovering any seating item (table group or lounge) shows a tooltip:
```
T-01 · 6 pax
```
For joined table groups:
```
T-01 + T-02 joined · 12 pax
```

### Total Capacity Indicator
Bottom status bar shows:
```
Total capacity: 48 pax  |  Tables: 8  |  Scale: 1:50
```

---

## 6. Table Joining

### Behaviour
1. User drags Table B on top of Table A.
2. System detects overlap during `dragmove`.
3. On `dragend`, if overlap ≥ 20% of either table's area:
   - Determine closest shared edge (north/south/east/west of A).
   - Snap Table B to butt exactly against that edge (no gap, no overlap).
   - Assign both items the same `join_group` UUID.
   - Chairs on the shared interior edge are auto-removed from both tables.
   - A dashed outline group indicator is drawn around the combined bounding box.

### Dashed Join Outline
- Konva `Rect` with `dash: [6, 4]` pattern
- Stroke: `rgba(120, 130, 150, 0.6)` (light grey-blue, visible but not dominant)
- `strokeWidth: 1.5`
- Fill: transparent
- `listening: false` (non-interactive — does not capture clicks)
- Re-drawn whenever the join group members move or are deleted

### Split / Unjoin
- User drags one joined table away from the other.
- On `dragend`, if the item's position has moved outside the original snap zone, the `join_group` UUID is cleared from that item.
- Chairs auto-restore on the formerly-shared edge.
- Dashed outline updates or disappears.

### Pax for Joined Tables
- Each table retains its own individual pax value.
- Hover over either table shows combined group pax: `T-01 + T-02 joined · 12 pax`
- In the PDF legend, joined groups list as "T-01 / T-02 (joined) · 12 pax"

### End-cap chairs
When two rectangle tables are joined end-to-end (north/south), chairs can be placed on the free short ends. When joined side-to-side (east/west), chairs can be placed on the free long edges plus the short ends of the now-longer combined form.

---

## 7. Event Zone System

### Purpose
A Zone is a named, coloured region that groups furniture items for an event area (e.g. "Cocktail Area," "Seated Dinner," "DJ / Dance Floor").

### Zone Creation
- Toolbox: [Zone] button enters zone-draw mode.
- User clicks and drags to draw a rectangle on the canvas.
- A name prompt appears inline: "Zone name:" + hex color picker.
- Zone is saved and drawn as a filled rectangle with:
  - `fill: hexColor + '15'` (very transparent fill, ~8% opacity)
  - `stroke: hexColor` at full opacity
  - `strokeWidth: 2`
  - `dash: none` (solid border — distinct from join group dashed outline)
  - Label in zone colour at top-left corner of the zone rect

### Zone Properties Panel
```
┌─────────────────────────┐
│ ◉ Zone: Cocktail Area   │
│ Name: [Cocktail Area  ] │
│ Color: [#C76E4B] ████   │
│ Hex: [#C76E4B         ] │
│                         │
│ [Delete Zone]           │
└─────────────────────────┘
```

Color picker: A preset swatch row of 8 named TEMPO brand colours + 8 additional event colours + a hex input field for custom entry.

**TEMPO preset colours:**
- Terracotta `#C76E4B`
- Amber `#DDAA62`
- Sage `#8A9277`
- Sand `#E7D8C9`
- Ink `#1A1816`
- Cream `#F7F3EE`

**Additional event colours:**
- Dusty Rose `#C9967E`, Slate Blue `#5B7A99`, Forest `#4A6741`, Mauve `#9B7B9E`, Gold `#B8962E`, Navy `#1E3A5F`, Coral `#E06B4F`, Charcoal `#3D3D3D`

### Zone Z-order
Zones render below all furniture items. Zone `zIndex` = 0. Furniture zIndex = 1+. Clicking on a zone only selects it if no furniture is at that position.

### Multiple Zones
No limit. Each zone has a unique hex colour. Two zones can overlap — overlapping border colours blend visually.

---

## 8. Notation / Annotation System

### Notation Pin
- Toolbox: [Notation] button enters notation-place mode.
- Click anywhere on canvas to place a notation pin.
- Pin appearance: small filled circle (16px) with the notation reference character inside (A, B, C… or 1, 2, 3…).
- A prompt appears: "Title:" + "Note:" (multiline textarea).
- Pin colour: `--accent` terracotta by default. Staff-only pins: grey.

### Hover Behaviour
Hovering over a notation pin shows a floating card:
```
┌─────────────────────────┐
│ [A]  Welcome Table      │
│ ─────────────────────── │
│ Position welcome gift & │
│ name cards here.        │
│               [Staff ✓] │
└─────────────────────────┘
```

### Staff-Only Toggle
Each notation has a "Staff only" toggle. Staff-only notations:
- Still visible on staff canvas view (greyed pin)
- Hidden from client share link view
- Still included in PDF appendix with "(Staff only)" tag visible only in staff PDF download

### Notation Appendix in PDF
At the end of the exported PDF, a table:

```
NOTATION APPENDIX
─────────────────────────────────────────────────────────────
Ref │ Title                  │ Note
────┼────────────────────────┼────────────────────────────────
 A  │ Welcome Table          │ Position welcome gift & name cards here.
 B  │ Photo Station          │ Backdrop + ring light behind cocktail bar.
 C  │ Bar Service (Staff)    │ (Staff only) Extra ice bins under bar — liaise with kitchen.
─────────────────────────────────────────────────────────────
```

---

## 9. Save / Load / Events

### Layout Metadata
Each layout has:
- **Name** (required): e.g. "Cocktail Night — July"
- **Event type** (optional dropdown): Cocktail Reception, Seated Dinner, Gallery Opening, Corporate Breakfast, Supper Club, Private Screening, Custom
- **Date** (optional): date picker
- **Time** (optional): time picker (start time of event)
- **Staff notes** (optional): plain text, not visible on client share

### Auto-save
- Dirty flag (`S.dirty`) triggers auto-save every 60 seconds if changes are present.
- Auto-save indicator in status bar: "Saving…" → "Saved 14:32"

### Layout List (left sidebar)
- Sorted by date (upcoming first, then past)
- Each entry shows: layout name, event type chip, date, time
- Filter: search by name, filter by event type
- "+ New Layout" button opens a modal: name + event type + date + time (required: name only)

### Presets (at creation)
When creating a new layout, offer optional starting presets:

| Preset | What it loads |
|---|---|
| Blank canvas | Empty canvas with structural walls only |
| Cocktail Reception | High tables at perimeter, lounges in centre, bar highlighted |
| Seated Dinner | Round 700mm tables in grid, chairs assigned |
| Theatre Style | Theatre chairs in rows facing media wall |
| Gallery / Standing | Minimal furniture, centrepieces, bar open |
| Boardroom / Breakfast | Joined rectangle tables in U-shape |

Each preset is a JSON config of `{ type, variant, x, y, rotation, pax }` objects. Loaded via `loadPreset(key)` function.

---

## 10. PDF Export

### Trigger
"Download PDF" button in left sidebar. Opens a brief modal:
```
┌──────────────────────────┐
│  Download Layout PDF     │
│  ─────────────────────── │
│  Include:                │
│  ☑ Zone legend           │
│  ☑ Notation appendix     │
│  ☑ Capacity summary      │
│  ☐ Staff-only notations  │
│                          │
│  Scale: 1:50 (auto)      │
│                          │
│  [Cancel] [Download PDF] │
└──────────────────────────┘
```

### PDF Structure (designed, not browser print)
Generated via jsPDF (loaded from CDN or bundled). Document is A3 landscape (420mm × 297mm):

**Page 1 — Floor Plan**
```
┌──────────────────────────────────────────────────────────────────┐
│  TEMPO House                                    218c Pasteur, D3 │
│  ─────────────────────────────────────────────────────────────── │
│  EVENT: Cocktail Night — July          Date: 19 Jul 2026 · 18:00 │
│  Type: Cocktail Reception              Capacity: 48 pax          │
│  ─────────────────────────────────────────────────────────────── │
│                                                                  │
│                    [FLOOR PLAN IMAGE]                            │
│                    (full Konva canvas at pixelRatio: 3)          │
│                                                                  │
│  ─────────────────────────────────────────────────────────────── │
│  Scale: 1:50          ████ Cocktail Area   ████ DJ Zone          │
│  Generated: 2026-06-19                 tempohouse.com.vn         │
└──────────────────────────────────────────────────────────────────┘
```

**Page 2 — Notation Appendix** (only if notations exist)
- Full notation appendix table (see §8)
- Space for client sign-off: "Layout approved by: _____________ Date: _______"

### PDF Implementation Notes
```javascript
// Konva canvas → high-DPI image
const dataUrl = stage.toDataURL({ pixelRatio: 3, mimeType: 'image/png' });

// jsPDF composition
const pdf = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a3' });
pdf.addImage(dataUrl, 'PNG', marginLeft, headerHeight, imgWidth, imgHeight);
// Then add header text, footer, legend chips via pdf.text() / pdf.setFillColor() etc.
pdf.save(`TEMPO-${layoutName}-${date}.pdf`);
```

---

## 11. Client Share Link

### URL Format
`https://[venue-domain]/wp-json/thr/v1/event-layouts/shared/{token}`

Or better: a dedicated WordPress page at `/event-layout/{token}` using a custom page template that renders a read-only version of the canvas.

### Read-Only Client View
- Same canvas, same zone colours, same notation pins (staff-only hidden)
- No toolbox, no properties panel
- Hover tooltips for notation pins
- "Download PDF" button (without staff-only notations)
- Header: "Layout: [Event Name] — [Date] — TEMPO House"
- No edit capability

### Share Controls (staff side)
```
Share Link
[Enable share link]  ☑

https://…/event-layout/abc123  [Copy]

Link is active — anyone with this link can view the layout.
[Disable link]
```

---

## 12. Canvas UX Conventions

### Follows Existing Floor Plan Editor Patterns
- Same Konva.js setup (stage, bgLayer, zoneLayer, furnitureLayer, overlayLayer)
- Same IIFE pattern, same state object `S`
- Same props panel (floating card, bottom-right of canvas)
- Same keyboard shortcuts:
  - `Esc` — deselect / cancel placing
  - `Delete` / `Backspace` — delete selected
  - `Ctrl+Z` / `Ctrl+Y` — undo/redo
  - `Ctrl+D` — duplicate
  - `Space+drag` — pan canvas
  - `+` / `-` — zoom
  - `0` — fit to screen

### Snap to Grid
- Grid: 10mm units (1 canvas unit at 1:50 = ~4px)
- Snap on `dragend` (same pattern as existing editor)
- Toggle: snap button in toolbar; `Shift` holds to disable snap temporarily

### Rotation
- Selection bounding box includes a rotation handle (circular arrow icon above centre)
- Drag rotates in 15° increments
- Rotation field in props panel (number input, 0–359°)

### Multi-Select
- `Shift+click` adds to selection
- Rubber-band: click and drag on empty canvas draws selection rect
- Multi-select moves all selected items together
- Multi-select delete: `Delete` removes all selected

### Undo / Redo
- Undo stack capped at 30 states
- States saved on: place, move (dragend), delete, rotate, pax change, zone draw, notation place

---

## 13. Capacity Estimator (from gap analysis — include in MVP)

Bottom status bar includes a capacity estimator tooltip:

```
Total: 48 pax  |  Area: ~85m²  |  Density: 1.77m²/person  ⚠ Tight
```

Density thresholds:
- ≥ 2.0 m²/person: Comfortable (green)
- 1.5–2.0 m²/person: Standard (amber)
- < 1.5 m²/person: Tight (red, warning icon)

Room area is set at layout creation (or derived from the structural SVG bounding box). Staff can override it in layout settings.

---

## 14. Constraint Markers (from gap analysis — include in MVP)

Structural overlay layer (non-interactive, drawn on `bgLayer`) shows:
- **Fire exit indicators**: small red arrow icons at known fire exit positions
- **Pillar markers**: grey filled circles at pillar positions (if any)
- **Bar counter**: hatched region (same as structural floor plan SVG)

These are sourced from a `structural_config.json` stored with the plugin, not editable per layout. Staff cannot accidentally drag a table onto a fire exit without seeing the visual conflict.

---

## 15. Build Plan

### Phase 1 — Core Event Layout Designer (MVP)
- [ ] New WP admin page `thr-event-layouts`
- [ ] DB tables: `wp_thr_event_layouts`, `wp_thr_event_layout_items`, `wp_thr_event_zones`, `wp_thr_event_notations`
- [ ] REST API: CRUD + `/save` (batch) + `/share`
- [ ] Canvas: Konva.js init, structural wall overlay, grid snap, pan/zoom
- [ ] Toolbox: all furniture types with correct mm dimensions
- [ ] Furniture rendering: drawRectTable, drawRoundTable, drawSquareTable, drawStool, drawChair, drawCentrepiece, drawBackdrop, drawProjector, drawLounge, drawCoffeeTable, drawSideTable
- [ ] Pax panel (floating props card): stepper, max enforcement, label edit
- [ ] Hover tooltip: pax display
- [ ] Table joining: drag-overlap detection, snap-to-edge, join group UUID, dashed outline, chair auto-adjust
- [ ] Zone system: draw mode, hex color picker, TEMPO swatches, z-order
- [ ] Notation system: pin placement, hover card, staff-only flag
- [ ] Save / auto-save / load
- [ ] Layout list (left sidebar): create, search, filter
- [ ] Presets: 6 layout presets as JSON configs
- [ ] Capacity estimator: status bar display
- [ ] Constraint markers: structural overlay
- [ ] PDF export: jsPDF + Konva toDataURL + header/footer/legend/appendix
- [ ] Undo/redo (30-state stack)
- [ ] Keyboard shortcuts

### Phase 2 — Sharing + Client Approval
- [ ] Share link system (token-based URL)
- [ ] Client read-only view (WP page template)
- [ ] "Approve layout" button (client-side, generates timestamped record)
- [ ] Version snapshots (auto on major change, manual checkpoint)
- [ ] Inline spatial comments (client leaves comment anchored to object)

### Phase 3 — CRM & Guest Integration
- [ ] Link layout to a reservation or inhouse event booking (FK to reservations system)
- [ ] Guest seating assignment panel (guest list → drag to table seat)
- [ ] Dietary tag per guest (shown per table in staff view)
- [ ] Notes-to-catering export (extract notations as brief document)
- [ ] Asset library presets by event type (expand furniture library)

### Phase 4 — Advanced / Roadmap
- [ ] Revision timeline with visual diff (green = new, red = removed, arrow = moved)
- [ ] SVG export for infinite-scale PDFs
- [ ] 3D preview sketch (lightweight isometric render of layout — not full 3D)
- [ ] Multiple venue areas (indoor + outdoor zones as separate canvas layers/tabs)
- [ ] Custom furniture upload (photo → auto-traced SVG asset)

---

*Related docs: `Documentation/research/2026-06-19-event-layout-designer-research.md`, `Documentation/research/2026-06-16-floor-plan-designer-competitive-research.md`, `Documentation/design/floor-plan-editor-build-log.md`*
