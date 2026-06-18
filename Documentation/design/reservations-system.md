# TEMPO House Reservations System
**Plugin:** `tempohouse-reservations` v1.4.0  
**Last updated:** 2026-06-18  
**Status:** Active development — floor plan builder in progress

---

## Overview

Custom WordPress plugin providing a full front-of-house reservations system for TEMPO House. Built specifically for the venue — not a third-party plugin. Handles public bookings, floor plan management, live table status, event enquiries, waitlist, and email automations.

The floor plan builder is the most complex subsystem and is the current focus of development.

---

## System Architecture

```
WordPress REST API  (tempohouse/v1/*)
        │
        ├── class-api-reservations.php    — CRUD + status transitions
        ├── class-api-floors.php          — Floor plans + background images
        ├── class-api-furniture.php       — Base furniture (tables, zones, labels)
        ├── class-api-layouts.php         — Named layouts, slots, periods
        ├── class-api-availability.php    — Date/time availability query
        ├── class-api-blocks.php          — Manual availability blocks
        ├── class-api-tags.php            — Tagging system
        ├── class-api-waitlist.php        — Waitlist management
        ├── class-api-reports.php         — Shift + dashboard reports
        ├── class-api-settings.php        — Venue settings
        └── class-api-events.php          — Event enquiry form

Frontend
        ├── floor-plan-builder.js         — Konva.js floor plan editor + live view
        ├── thr-modal.js                  — Shared modal utility (alert/confirm/prompt/3-button)
        ├── booking.js                    — Public booking form
        └── admin.js                      — General admin list/actions

Email Templates
        ├── confirmation.php, pending.php, cancellation.php
        ├── reminder.php, feedback.php
        ├── waitlist-joined.php, waitlist-notify.php
        └── event-enquiry-reply.php, shift-report.php
```

---

## Database Tables

| Table | Purpose |
|---|---|
| `thr_reservations` | Core booking records — status, datetime, party, diner contact, notes, deposit |
| `thr_floor_plans` | One row per physical floor — name, dimensions, background image URL + transform |
| `thr_furniture` | Base table of tables/zones/labels on a floor — position, capacity, group_id, meta |
| `thr_layouts` | Named layout variants per floor (e.g. "Lunch config", "Private event") |
| `thr_layout_slots` | Override positions/capacities for furniture within a named layout |
| `thr_layout_periods` | Day-of-week + time ranges that activate a layout automatically |
| `thr_tags` | Tags for reservations (VIP, Birthday, Corporate, etc.) — seeded at install |
| `thr_reservation_tags` | Pivot: reservation ↔ tag many-to-many |
| `thr_availability_blocks` | Manual blocks (furniture or floor-level) with datetime range + reason |
| `thr_event_enquiries` | Venue event enquiry submissions |
| `thr_waitlist` | Waitlist entries with status (waiting / notified / converted / expired) |

### Key schema fields: `thr_furniture`
```
id, floor_plan_id, type, label, pos_x, pos_y, width, height,
rotation_deg, capacity_min, capacity_max, shape, is_combinable,
is_available, element_key, group_id, meta (JSON), created_at, updated_at
```

### Key schema fields: `thr_layout_slots`
```
id, layout_id, furniture_id, type, label, pos_x, pos_y, width, height,
rotation_deg, capacity_min, capacity_max, element_key, group_id,
is_visible, meta (JSON)
```

### `meta` JSON column (furniture & slots)
Used for everything that doesn't warrant a dedicated column:
- `members: []` — array of furniture IDs belonging to this zone
- `color` — zone border/fill color override
- `members_period` — period label shown in zone (e.g. "Lunch")
- `joinable_override` — per-slot joinable override in layout mode
- `fontSize`, `color`, `bgColor` — text label styling

---

## REST API Surface

Base namespace: `tempohouse/v1/`

### Floor Plans
| Method | Route | Purpose |
|---|---|---|
| GET/POST | `floor-plans` | List all / create new floor |
| GET/PATCH/DELETE | `floor-plans/{id}` | Single floor CRUD |
| POST | `floor-plans/{id}/background` | Upload/update floor background image |

### Furniture (Base Layout)
| Method | Route | Purpose |
|---|---|---|
| GET/POST | `floor-plans/{floor_id}/furniture` | List / create furniture on floor |
| GET/PATCH/DELETE | `furniture/{id}` | Single furniture CRUD |
| GET | `furniture/types` | Available furniture type definitions |

### Named Layouts
| Method | Route | Purpose |
|---|---|---|
| GET/POST | `layouts` | List / create layouts |
| GET/PATCH/DELETE | `layouts/{id}` | Single layout CRUD |
| POST | `layouts/{id}/duplicate` | Duplicate layout + all its slots |
| POST | `layouts/{id}/snapshot` | Copy current base furniture into layout slots |
| POST | `layouts/{id}/activate` | Set as active layout on its floor |
| POST | `floor-plans/{id}/deactivate-layout` | Return floor to base furniture |
| GET/POST | `layouts/{layout_id}/periods` | List / create activation periods |
| GET/PATCH/DELETE | `periods/{id}` | Single period CRUD |
| GET/POST | `layouts/{layout_id}/slots` | List / create slots |
| GET/PATCH/DELETE | `slots/{id}` | Single slot CRUD |
| POST | `layouts/{layout_id}/slots/bulk` | Replace all slots in one request (publish path) |

### Reservations
| Method | Route | Purpose |
|---|---|---|
| GET/POST | `reservations` | List / create (admin) |
| GET/PATCH/DELETE | `reservations/{id}` | Single reservation CRUD |
| PATCH | `reservations/{id}/status` | Status transition only |
| POST | `public/booking` | Public booking form submission |
| POST | `public/cancel` | Guest cancellation via token |

### Availability, Blocks, Waitlist
| Method | Route | Purpose |
|---|---|---|
| GET | `availability` | Available slots for a date/time/party_size |
| GET/POST | `blocks` | List / create availability blocks |
| GET/PATCH/DELETE | `blocks/{id}` | Single block CRUD |
| GET/POST | `waitlist` | Admin list / add to waitlist |
| POST | `public/waitlist` | Public waitlist join |
| POST | `waitlist/{id}/notify` | Manually trigger waitlist notification email |
| POST | `waitlist/{id}/convert` | Convert waitlist entry to reservation |

### Reports, Settings, Events
| Method | Route | Purpose |
|---|---|---|
| GET | `reports/shift` | Shift summary report |
| GET | `reports/dashboard` | Dashboard metrics |
| GET/PATCH | `settings` | Venue-level settings |
| GET | `public/config` | Public-safe config for booking form |
| POST | `public/event-enquiry` | Event enquiry form submission |
| GET | `event-enquiries` | Admin list of enquiries |
| PATCH | `event-enquiries/{id}/status` | Update enquiry status |

---

## Floor Plan Builder

### Tech Stack
- **Renderer:** Konva.js v9.3.14 (CDN) — HTML5 Canvas via Stage/Layer/Group/Shape
- **State:** Plain JS object `S` — no framework
- **Pattern:** IIFE, `(function() { ... })()` — no module bundler

### Layers (Konva)
| Layer | Variable | Contents |
|---|---|---|
| Background | `bgLayer` | Background image + dot-grid |
| Zones | `zoneLayer` | Zone rectangles, zone labels |
| Tables | `tableLayer` | All furniture groups (tables, labels, etc.) |

### State Object `S`
```js
{
  mode,           // 'live' | 'builder'
  floors,         // array from API
  floorId,        // active floor ID
  tables,         // id → furniture/slot object (in-memory source of truth)
  activeLayout,   // null = base mode, object = layout mode
  layouts,        // array of layouts for current floor
  selectedIds,    // Set — multi-select
  selected,       // string ID of primary selection
  liveStatus,     // furniture_id → status string
  floorStats,     // floorId → { tables, seats, booked }
  date, time,     // live view filter
  placing,        // type key being placed ('round_4', etc.)
  snapEnabled,    // 24px grid snap toggle
  dirty,          // true = unpublished changes exist
  undoStack,      // array of snapshots
  redoStack,
}
```

### Two Modes
**Live mode** (`S.mode === 'live'`)
- Tables rendered with colour-coded status: Free (green) / Reserved (blue) / Occupied (orange) / Blocked (grey)
- Date + time chips filter which reservations are active
- Polling every 30s via `fetchLiveStatus()`
- Click table → shows reservation details in float panel

**Builder mode** (`S.mode === 'builder'`)
- Full drag/drop via Konva draggable groups
- Rubber-band multi-select
- Float panel per element (props, zone members, label style)
- Undo/Redo stack
- Snap-to-grid (24px)
- No writes to DB until "Publish updates"

### Deferred Save Pattern (as of 2026-06-18)
**All** intermediate changes (drag, zone membership, join/separate, rename, joinable toggle) update `S.tables` in-memory only. `markDirty()` enables the Publish button. `saveLayout()` is the **only** DB write path:
- **Layout mode:** `POST layouts/{id}/slots/bulk` — replaces entire slot set atomically
- **Base mode:** `PATCH furniture/{id}` per item (positions, labels, group_id, is_combinable, meta)

### Overlap & Zone Positioning
- `aabbOverlap(a, b, pad)` — axis-aligned bounding box test, pad=16px minimum gap
- `isOverlappingAny(x, y, w, h, excludeId)` — checks against all non-zone, non-label furniture
- `findFreePositionNear(cx, cy, w, h, excludeId, preferBBox)` — grid-aligned spiral search; prefers candidates inside zone bbox; max radius 10 grid steps
- `_intentX / _intentY` — captures drop coordinates BEFORE nudge so zone membership uses the user's intended drop point, not the auto-repositioned position

### Zone System
- Zones are furniture items with `type === 'zone'`, rendered on `zoneLayer`
- Members stored in `meta.members` — array of furniture IDs
- Zone membership evaluated on dragend via bbox check using `getZoneBBoxRaw()`
- Drag into zone → added to members, auto-repositioned if overlapping
- Drag out → modal prompt to confirm removal from zone
- Zone rename via double-click inline edit
- Zone color, label, period label editable in float panel

### Layout System
- Each floor can have multiple named layouts (e.g. "Lunch", "Private Hire")
- `S.activeLayout !== null` = currently editing a layout (slots mode)
- Layouts panel: list, create, duplicate, activate, set periods
- Active layout banner shows when viewing a non-base layout in live mode
- All zone membership and position writes route to `slots/` endpoints in layout mode

### Dirty-Nav Guard (as of 2026-06-18)
Three interception layers when `S.dirty && S.mode === 'builder'`:
1. **Floor tab click** — wrapped in `guardDirtyNav()`
2. **WordPress admin links** — delegated capture listener on `document`, intercepts all `<a>` outside `#fp-app`
3. **Browser navigation** — `beforeunload` shows native browser dialog

Modal choices: **Publish & continue** (save then proceed) / **Abandon changes** (clear dirty, proceed) / **Stay** (cancel).

---

## Key Functions Reference

| Function | Purpose |
|---|---|
| `initKonva()` | Creates Stage, Layers, Transformer; sets up rubber-band |
| `loadFloors()` | Fetches all floor plans, renders tabs, selects first |
| `selectFloor(id)` | Switches active floor — clears layout, loads furniture |
| `loadFurniture(floorId)` | GET furniture → populates `S.tables`, renders |
| `renderAllTables()` | Destroys + redraws all table nodes |
| `addTableNode(item)` | Draws one furniture item as a Konva Group |
| `renderZoneLayer()` | Redraws all zone rectangles and labels |
| `enterBuilderMode()` | Switches to builder, stops live poll, enables drag |
| `exitBuilderMode()` | Guard check → `_doExitBuilder()` |
| `guardDirtyNav(proceed)` | Shows 3-button modal when dirty; calls proceed() or saves |
| `saveLayout()` | Bulk-saves all `S.tables` to API; returns Promise |
| `markDirty()` | Sets `S.dirty = true`, enables Publish button |
| `pushUndo()` | Deep-clones `S.tables` onto undo stack |
| `undo() / redo()` | Restores snapshot, redraws all tables |
| `placeTable(type, x, y)` | Creates furniture via POST, adds to canvas |
| `duplicateSelected()` | Clones selected item(s) offset by +24px |
| `joinSelected()` | Sets `group_id` to lowest ID in selection (in-memory) |
| `separateSelected()` | Nulls `group_id` for whole group (in-memory) |
| `findFreePositionNear()` | Grid-aligned spiral search for non-overlapping position |
| `initLayoutSystem(floor)` | Loads layouts panel for current floor |
| `loadLayout(layoutId)` | GET slots → replaces `S.tables`, re-renders |
| `loadBaseLayout()` | Clears `S.activeLayout`, reloads base furniture |
| `fetchLiveStatus()` | GET availability → updates `S.liveStatus`, redraws |
| `apiFetch(method, endpoint, body)` | REST fetch wrapper with nonce auth |

---

## Email Automations

Handled by `class-email.php` + cron (`class-cron.php`):

| Trigger | Template |
|---|---|
| Booking confirmed | `confirmation.php` |
| Booking pending review | `pending.php` |
| Booking cancelled | `cancellation.php` |
| 24h before reservation | `reminder.php` |
| After seated (feedback) | `feedback.php` |
| Waitlist joined | `waitlist-joined.php` |
| Waitlist spot available | `waitlist-notify.php` |
| Event enquiry submitted | `event-enquiry-reply.php` |
| End of shift | `shift-report.php` |

---

## Dev Environment

**Docker container:** `wp-env-tempohouse-wp-9fe0e8e2-wordpress-1` on port 8888

The Google Drive path is NOT volume-mounted for the plugin — only the theme is. After editing plugin JS/CSS, sync manually:

```bash
# Sync floor plan builder JS
docker cp "/Users/baileywang/Library/CloudStorage/GoogleDrive-bailey@ragingmonk.co/My Drive/Websites/tempohouse.com.vn/WordPress/plugins/tempohouse-reservations/assets/js/floor-plan-builder.js" \
  wp-env-tempohouse-wp-9fe0e8e2-wordpress-1:/var/www/html/wp-content/plugins/tempohouse-reservations/assets/js/floor-plan-builder.js

# Sync thr-modal.js
docker cp "/Users/baileywang/Library/CloudStorage/GoogleDrive-bailey@ragingmonk.co/My Drive/Websites/tempohouse.com.vn/WordPress/plugins/tempohouse-reservations/assets/js/thr-modal.js" \
  wp-env-tempohouse-wp-9fe0e8e2-wordpress-1:/var/www/html/wp-content/plugins/tempohouse-reservations/assets/js/thr-modal.js

# Sync PHP includes
docker cp "/Users/baileywang/Library/CloudStorage/GoogleDrive-bailey@ragingmonk.co/My Drive/Websites/tempohouse.com.vn/WordPress/plugins/tempohouse-reservations/includes/class-api-layouts.php" \
  wp-env-tempohouse-wp-9fe0e8e2-wordpress-1:/var/www/html/wp-content/plugins/tempohouse-reservations/includes/class-api-layouts.php
```

---

## Build History

| Version | Date | Summary |
|---|---|---|
| v5.1 (rebuild) | 2026-06-16 | Full floor plan editor rebuild — Konva.js, live view, builder mode, palette, props panel |
| v5.2–5.5 | 2026-06-16 | Zone system, zone membership drag, zone colors, zone float panel |
| v5.6–5.8 | 2026-06-16/17 | Layout system — named layouts, slots, periods, activate/deactivate, layout banner |
| v5.9–5.11 | 2026-06-17 | Multi-select rubber-band, group join/separate, undo/redo |
| v5.12–5.14 | 2026-06-17 | Background image upload, pan/crop/scale editor |
| v5.15–5.18 | 2026-06-17/18 | Text label element, duplicate, snap-to-grid, rotation transformer |
| v3.21.x | 2026-06-18 | Floor plan: zone drag-into overlap auto-reposition, element 16px margin gap, deferred save |
| v3.21.4 | 2026-06-18 | Dirty-nav guard: floor tabs, WordPress admin links, beforeunload |

---

## Known Bugs (Floor Plan)

| ID | Severity | Symptom | Status |
|---|---|---|---|
| FP-001 | High | Drag into zone with overlap showed "no space" error and element wasn't added to zone | Fixed 2026-06-18 — `findFreePositionNear` + `_intentX/_intentY` + correct PATCH endpoint |
| FP-002 | Medium | Zone membership PATCH used wrong endpoint (`furniture/`) in layout mode | Fixed 2026-06-18 — `S.activeLayout ? 'slots/' : 'furniture/'` guard |
| FP-003 | Low | Elements too close together — only ~4px visual gap | Fixed 2026-06-18 — `aabbOverlap` pad bumped 4→16px |

---

## Feature Gaps & Roadmap

### Floor Plan Builder
| Priority | Feature | Notes |
|---|---|---|
| High | Table assignment on booking creation | Select a table/zone from the floor plan when creating/editing a reservation |
| High | Live view click → quick reservation create | Click an empty table in live view to open new booking pre-filled with table |
| Medium | Rotation widget on canvas | Currently rotation only via transformer; no numeric input |
| Medium | Side chairs on rect tables | Currently only top/bottom rows rendered |
| Medium | Mobile / touch layout | Right panel collapses at 900px; no touch-specific drag UX |
| Medium | Keyboard shortcuts overlay | Only Ctrl+Z / Ctrl+S / Del implemented; no discoverability |
| Low | WebSocket live status | Currently 30s REST poll; could upgrade for true real-time |
| Low | Minimap / overview | Useful for large venues with many elements |
| Low | Print/export floor plan | PDF or PNG export of current floor layout |

### Reservations Module
| Priority | Feature | Notes |
|---|---|---|
| High | Reservation list / dashboard | Admin view of all reservations — filters, status change, search |
| High | Seating chart on reservation | Show table assignment visually when viewing a booking |
| Medium | Booking widget — table selection | Let guests optionally request a table area from the public booking form |
| Medium | Calendar view | Day/week view of reservations timeline (currently placeholder) |
| Medium | FOH Run-sheet | Tonight's reservations in timeline order with table assignments and special notes |
| Medium | Dining areas | Named areas (Bar Area, Garden, etc.) with capacity display |
| Low | Deposit flow | Stripe/VNPay integration for deposit collection on booking |
| Low | Automated layout activation | `thr_layout_periods` rows already exist — cron job to auto-activate by day+time |
| Low | Customer profiles | Diner history, visit count, preferences |
| Low | SMS/Zalo reminders | diner_zalo field exists in schema — just needs integration |

### Infrastructure
| Priority | Gap | Notes |
|---|---|---|
| High | Plugin deploy to production | Currently only in Docker dev — no live site deployment yet |
| High | Theme volume-mount the plugin | Only theme is mounted; plugin requires `docker cp` after every change |
| Medium | CSS for thr-modal extra button | `thr-modal-btn-ghost` used for Abandon button; confirm looks OK visually |
| Low | E2E tests | No automated tests for the floor plan builder interactions |

---

## Files Changed This Session (2026-06-18)

| File | Changes |
|---|---|
| `assets/js/floor-plan-builder.js` | Deferred save (removed 10 intermediate PATCHes), `findFreePositionNear`, `_intentX/_intentY`, `guardDirtyNav`, `beforeunload`, admin link interceptor, `saveLayout()` returns Promise, `saveLayout` base-mode adds `group_id`+`is_combinable` |
| `assets/js/thr-modal.js` | Added third `extra` button — `opts.extra`, `opts.extraDanger`; resolves `'extra'` |
