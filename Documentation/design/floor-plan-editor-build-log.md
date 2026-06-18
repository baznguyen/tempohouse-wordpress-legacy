# Floor Plan Editor — Build Log
**Version:** v5.1 (complete rebuild)  
**Date:** 2026-06-16  
**Status:** Live — deployed to Docker dev environment

---

## Overview

Full from-scratch rebuild of the floor plan editor module inside `tempohouse-reservations` WordPress plugin. Replaced a placeholder "Editor coming soon" page with a production-quality, OpenTable-inspired floor plan builder and live view.

---

## Stack

| Layer | Tech |
|---|---|
| Rendering | Konva.js v9.3.14 (CDN) |
| Backend | WordPress REST API (`thr/v1`) |
| Storage | `wp_thr_floor_plans` + `wp_thr_furniture` DB tables |
| Admin UI | PHP (`THR_Admin` class) + vanilla JS IIFE |
| Styles | Single scoped CSS file (`floor-plan-builder.css`) under `#fp-app` |

---

## Files Changed

### `WordPress/plugins/tempohouse-reservations/assets/js/floor-plan-builder.js`
Complete rewrite. ~1200 lines, IIFE pattern.

**Key state object:**
```js
const S = {
  mode,          // 'live' | 'builder'
  floors,        // array of floor plan objects
  floorId,       // currently active floor
  tables,        // id → furniture item map
  liveStatus,    // furniture_id → status map (from reservations API)
  floorStats,    // floorId → { tables, seats, booked }
  selected,      // currently selected table id (builder)
  dirty,         // unsaved changes flag
  date, time,    // live mode date/time filter
  placing,       // type key being placed on canvas
  undoStack, redoStack
}
```

**Key functions:** `initKonva`, `loadFloors`, `selectFloor`, `renderFloorTabs`, `loadFloorStats`, `loadFurniture`, `renderAllTables`, `addTableNode`, `drawRoundTable`, `drawRectTable`, `drawBoothTable`, `addChair` (helper), `liveStyle`, `selectTable`, `deselect`, `buildPalette`, `startPlacing`, `placeTable`, `showPropsPanel`, `saveLayout`, `startLiveUpdates`, `fetchLiveStatus`, `enterBuilderMode`, `exitBuilderMode`

### `WordPress/plugins/tempohouse-reservations/assets/css/floor-plan-builder.css`
Complete rewrite. v5.1 — full design pass.

**Token system:**
```css
--bg, --panel, --border, --border-2
--text-1, --text-2, --text-3
--accent, --accent-h, --accent-lt, --accent-bd
--blue, --blue-lt, --red, --red-lt
--hdr (58px), --fnav (52px), --sub (46px), --leg (40px)
--pal (200px), --rp (268px)
```

### `WordPress/plugins/tempohouse-reservations/includes/class-admin.php`
Rebuilt the `render_floor_plans_page()` method HTML structure.

**Layout zones:**
- `.fp-header` — title + mode-specific action buttons
- `.fp-floor-nav` — 3-floor selector tabs with hover popups
- `.fp-subbar` — date/time chips + segmented view control (live mode only)
- `.fp-body` — flex row: palette | canvas | right panel
- `.fp-legend` — status dot legend (live mode only)

---

## Features Implemented

### Live View
- Colour-coded table status: Free (green) / Booked (blue) / Occupied (orange) / Blocked (gray)
- Date + time chips — click to filter reservation status at a specific moment
- Polling every 30s for live status updates
- Click a table to see its live status in the right panel
- Legend bar at bottom

### Floor Navigator
- 3 floors (Ground Floor, Level 1, Level 2) rendered as pill tabs
- Active floor: green accent background + green icon badge
- Hover popup per floor: Tables count, Seat capacity, Booked today
- Popup stats loaded async from REST API + reservation count

### Builder Mode (Edit Floorplan)
Accessed via "Edit Floorplan" button.

**Palette** — Left sidebar, 2-column grid, 3 sections:
- TABLES: Round 2/4/6/8, Rect 2/4/6/8
- SEATS: Booth 2/4/6, Stool, HiTop 2/4
- ZONES: Bar, VIP, Outdoor, Stage

**Canvas:**
- Konva.js stage with dot-grid background
- Drag to move tables
- Click to select → opens props panel
- Floating toolbar: Zoom in/out, Fit all, Delete selected, Zoom %

**Props Panel** — Right panel transforms when table selected:
- Label (editable text input)
- Type (readonly)
- Capacity (min/max seats — number inputs)
- Position (readonly, shows x,y)
- "Remove table" danger button

**Undo/Redo** — Full undo stack with Ctrl+Z / Ctrl+Y

**Publish** — "Publish updates" saves all layout changes to REST API via `PUT /thr/v1/floor-plans/{id}/furniture`

---

## CSS Architecture Decisions

### Specificity Strategy
All styles scoped under `#fp-app`. Key rule: button-related component classes that need visible borders/backgrounds are prefixed `#fp-app .fp-btn` etc. to get specificity (1,1,0) — beating the `#fp-app button` reset at (1,0,1).

**Classes prefixed with `#fp-app`:** `.fp-btn`, `.fp-btn-primary/outline/ghost/danger`, `.fp-chip`, `.fp-add-floor-btn`, `.fp-right-action-btn`, `.fp-props-back`, `.fp-tool`, `.fp-tool--delete`, `.fp-view-tab`, `.fp-view-tab--active`

### Reset Strategy
```css
/* Wildcard: box-sizing + font only. NO margin/padding zero. */
#fp-app * { box-sizing: border-box; font-family: ...; }

/* Targeted block-element margin reset only */
#fp-app h1, #fp-app p, #fp-app ul ... { margin: 0; padding: 0; }

/* Button reset: no border (each class handles its own) */
#fp-app button { cursor: pointer; background: none; line-height: 1; ... }
```

**Why:** Original wildcard `margin: 0; padding: 0` on `#fp-app *` (specificity 1,0,0) overrode all component padding rules. Removing it was the key fix.

---

## Chair Rendering

### Round Tables
`addChair()` helper places `Konva.Rect` with:
- `cornerRadius: [cW/2, cW/2, 3, 3]` — fully rounded seat back (outer), slight seat edge
- `offsetX: cW/2, offsetY: cH/2` — rotates around chair center
- Positioned at `r + gap + cH/2` from table center, angled by `a * 180/π + 90`

### Rect Tables
- Top chairs: `addChair(..., 0°)` — rounded back faces up (outward)
- Bottom chairs: `addChair(..., 180°)` — rounded back faces down (outward)

### SVG Palette Icons
`svgChair()` helper renders pill-shaped `<rect rx="cW/2">` chairs, rotated via SVG `transform="rotate(...)"`. Matches Konva visual language.

---

## Known Limitations / Future Work

- Calendar view tab is a placeholder (not implemented)
- Dining Areas section has no data yet (+ New button shows area creation intent)
- No side chairs on rect tables (only top/bottom rows)
- No rotation control for tables on canvas
- Mobile layout collapses right panel below 900px (no touch-optimised interaction)
- No multi-select / group move
- Live status polling is REST-based; could upgrade to WebSocket for true real-time
