# Floor Plan Editor — UX Analysis & Improvement Roadmap
**Date:** 2026-06-16  
**Scope:** Live view + Edit Floorplan builder  
**Reference:** OpenTable floor plan builder, SevenRooms, TableUp  

---

## Executive Summary

The floor plan editor is functionally complete and visually polished. The primary gaps are around **interaction depth** (what happens after the user does something), **contextual information density** (right panel is underutilised), and **builder workflow friction** (missing affordances that reduce confidence when editing).

Priority tiers:
- **P1 — Quick wins** (1–2 hrs each, high impact)
- **P2 — Core workflow** (half-day each, significant UX lift)
- **P3 — Power features** (larger effort, differentiating)

---

## Section 1: Live View UX Issues

### 1.1 Right panel default state is wasted space _(P1)_
**Current:** "Select a table to see its details." placeholder text only.  
**Problem:** Every time you load the page, the panel is blank. This is dead real estate on every page load.  
**Fix:** Show floor-level summary by default when no table is selected:
- Total tables on this floor / total seats
- Tables currently booked / occupied / free / blocked (with colour dots)
- Next seating time coming up
- Quick "+ New Reservation" shortcut

### 1.2 Table click shows status but no reservation context _(P1)_
**Current:** Clicking a booked table shows its status chip ("Booked") in the right panel. Nothing else.  
**Problem:** Front-of-house staff need to know WHO is booked and when. The status chip alone requires them to cross-reference a separate reservations list.  
**Fix:** When clicking a booked/occupied table, show in the right panel:
- Guest name
- Party size
- Reservation time
- Notes/tags
- Quick link: "View reservation →"

### 1.3 Date/time filter lacks a "now" anchor _(P1)_
**Current:** "Today" chip and "All day" chip. Changing the time changes live status but there's no visual indicator of what "now" is vs a future/past time.  
**Problem:** Staff don't know if they're looking at current or future status.  
**Fix:**
- Show "Now" label when time matches current time (within 15 min)
- Add a visual "current time" marker (e.g. "⬤ Live" indicator)
- When date = today + time = current hour → show "Live" badge in the header

### 1.4 Floor popup requires hover — no pinned view _(P2)_
**Current:** Floor stats (Tables, Seat capacity, Booked today) appear on hover only.  
**Problem:** Hover-only interactions are invisible to users who don't explore. Mobile unfriendly. Hard to glance at all 3 floors simultaneously.  
**Fix:** Make the floor nav row itself a persistent summary strip:
- Each tab shows a small "booked/total" counter inline: `G Ground Floor  3/5`
- The popup on hover gives the full breakdown (keep as-is)
- Consider a "floors overview" collapsed panel above the canvas (optional)

### 1.5 Canvas shows no feedback when all tables are free _(P2)_
**Current:** All-green canvas looks fine but gives no actionable insight.  
**Problem:** At a glance, staff can't distinguish "quiet night, 0 reservations" from "data not loading."  
**Fix:** Add a subtle status summary strip below the canvas toolbar (or as canvas overlay text) showing counts: e.g. `Free: 5   Booked: 0   Occupied: 0`

---

## Section 2: Edit Floorplan Builder UX Issues

### 2.1 No zoom-to-fit on builder mode entry _(P1)_
**Current:** Canvas starts at whatever zoom/pan state it was in.  
**Problem:** If tables are scattered or off-screen, user opens builder to an empty or confusing view.  
**Fix:** On entering builder mode, call `zoomFit()` automatically. Already implemented — just wire it to the `enterBuilderMode()` function.

```javascript
function enterBuilderMode() {
  // ... existing code ...
  setTimeout(zoomFit, 100); // after render
}
```

### 2.2 Placement mode has no ghost preview _(P1)_
**Current:** User clicks a palette item → crosshair cursor appears → click on canvas to place. No visual indication of where the table will land.  
**Problem:** Blind placement is anxiety-inducing. Users don't know if the table will be in the right position until it appears.  
**Fix:** When in placing mode, draw a semi-transparent ghost shape that follows the cursor on the canvas. Use Konva `mousemove` on the stage layer to update a "ghost" shape position.

### 2.3 "Remove table" has no confirmation _(P1)_
**Current:** Clicking "Remove table" in the props panel immediately deletes the table.  
**Problem:** Accidental deletions lose layout work. There's no undo on delete currently.  
**Fix option A:** Add an inline confirm state — button text changes to "Confirm remove?" with a cancel link for 3 seconds before executing.  
**Fix option B:** Wire into undo stack (delete should be undoable).  
Both should be implemented.

### 2.4 Publish has no success/failure feedback _(P1)_
**Current:** "Publish updates" button saves to API. On success, button becomes disabled. No toast/notification.  
**Problem:** Users don't know if the save worked. They might click multiple times or feel uncertain.  
**Fix:** After successful save:
- Show a brief toast: "Floorplan saved ✓" (2 seconds)
- Button briefly shows "Saved ✓" text before returning to "Publish updates"
- On error: show inline error message with retry

### 2.5 No table rotation control _(P2)_
**Current:** Tables are placed at 0° and cannot be rotated via the UI. The `rotation_deg` field exists in the DB and Konva honours it, but there's no way to set it.  
**Problem:** Real restaurants need angled tables for space optimisation (diagonal placement at corners, etc.).  
**Fix:** In the Props panel, add a Rotation row:
- Number input (0–359°) or a rotation dial
- Additionally: on canvas, a small rotate handle on selected table (small arc icon at the corner of the selection box)

### 2.6 Empty floor state gives no direction _(P2)_
**Current:** Switching to Level 1 or Level 2 shows a blank canvas with just the grid.  
**Problem:** New users don't know what to do — there are no instructions or visual cues.  
**Fix:** When `tables` count for the floor is 0, show a centred empty state:
- Icon (table + plus symbol)
- Headline: "No tables on this floor yet"
- Subtext: "Click an element in the panel to the left to place it on the map."
- Optional: a faint arrow pointing toward the palette

### 2.7 No snap-to-grid _(P2)_
**Current:** Tables can be placed at any pixel position. Very hard to create clean, aligned layouts.  
**Problem:** Professional floor plans look clean and grid-aligned. Without snap, layouts quickly become messy.  
**Fix:** On drag end (`dragend` event on Konva group), snap position to nearest grid interval (e.g. 24px matching the background grid):
```javascript
group.on('dragend', function() {
  var snap = 24;
  group.x(Math.round(group.x() / snap) * snap);
  group.y(Math.round(group.y() / snap) * snap);
  layer.batchDraw();
});
```

### 2.8 Table label editing requires opening props panel _(P2)_
**Current:** To rename a table, user must: click table → props panel opens → edit label field.  
**Problem:** Labelling multiple tables sequentially is slow — 3 clicks per table.  
**Fix:** Double-click on the table label text on the canvas to enter inline edit mode (HTML input overlay over the Konva canvas, positioned at the table location).

### 2.9 No side chairs on rectangular tables _(P2)_
**Current:** Rect tables only have chairs on top and bottom rows. A 6-top rect (T3) has 3 top, 3 bottom but none on the sides.  
**Problem:** Rect tables in real restaurants often have end chairs too (especially for 4/6-tops).  
**Fix option:** For rect tables ≥4 capacity, add 1 end chair on left and right sides, reducing top/bottom count accordingly. E.g. a 6-top: 2 top, 2 bottom, 1 left, 1 right.

### 2.10 No keyboard shortcut reference _(P3)_
**Current:** Ctrl+Z (undo) and Ctrl+Y (redo) work but are invisible in the UI.  
**Problem:** Power users expect keyboard shortcuts but won't find them.  
**Fix:** Add a `?` icon or "Keyboard shortcuts" link in the canvas toolbar area that shows a small overlay panel:
| Action | Shortcut |
|---|---|
| Undo | Ctrl+Z |
| Redo | Ctrl+Y / Ctrl+Shift+Z |
| Delete selected | Delete / Backspace |
| Escape | Cancel placing / deselect |
| Fit to screen | Ctrl+0 |
| Zoom in/out | +/- or scroll wheel |

### 2.11 Duplicate table (copy/paste) missing _(P3)_
**Current:** No way to duplicate a table.  
**Problem:** Setting up a floor with 10 identical 4-top tables requires placing each one individually.  
**Fix:** Ctrl+D or right-click context menu → "Duplicate". Creates a copy offset by (20, 20)px and selects it immediately.

### 2.12 No multi-select or group move _(P3)_
**Current:** Only one table can be selected at a time.  
**Problem:** Rearranging a section of the floor requires moving each table individually.  
**Fix:** Shift-click or drag-select (rubber-band selection) to select multiple tables. Move all selected as a group.

---

## Section 3: General / Navigation Issues

### 3.1 "Edit Floorplan" button could be more discoverable _(P1)_  
**Current:** Small outline button in the far top-right of the header.  
**Problem:** In a busy WP admin header, this can be overlooked, especially for first-time users.  
**Fix options:**
- Add a pencil icon to the left of the "Edit Floorplan" text: `✏ Edit Floorplan`
- On hover, the button gets a slightly more prominent shadow treatment
- First-time: show a tooltip/tooltip arrow pointing to it

### 3.2 Builder mode title is inconsistent _(P1)_
**Current:** Live mode shows "Restaurant map". Builder mode shows "Floor Plans — Edit".  
**Problem:** Inconsistent naming. "Floor Plans — Edit" feels like a WordPress admin page title, not a mode indicator.  
**Fix:** Standardise. Options:
- Live: "Restaurant map" / Builder: "Restaurant map — Editing"
- Or: Mode pill badge next to the title: `Restaurant map [EDITING]`

### 3.3 Calendar view tab is a dead end _(P2)_
**Current:** "Calendar" tab exists in the subbar but clicking it does nothing (placeholder).  
**Problem:** Presenting a UI element that doesn't work damages trust and creates confusion.  
**Fix short-term:** Either remove the tab entirely until Calendar is built, or show a "Coming soon" tooltip on hover and visually disable the tab (dimmed, no hover state).

### 3.4 Dining Areas section has no implementation _(P2)_
**Current:** "DINING AREAS" section in the right panel with a "+ New" button. Clicking shows nothing.  
**Problem:** Same dead-end problem. "+ New" implies something will happen.  
**Fix short-term:** Either remove or show a placeholder state explaining what dining areas are and that this feature is coming.

---

## Recommended Build Order

### Sprint 1 — Quick Wins (P1s)
1. Zoom-to-fit on builder mode entry (30 min)
2. Remove table confirmation (45 min)
3. Publish success toast (30 min)
4. Right panel default state with floor stats (2 hrs)
5. Pencil icon on "Edit Floorplan" button (15 min)
6. Disable Calendar tab with "Coming soon" (15 min)
7. Builder mode title consistency (15 min)

### Sprint 2 — Core Workflow (P2s)
1. Placement ghost preview on canvas (3 hrs)
2. Snap-to-grid on drag (1 hr)
3. Empty floor state (1 hr)
4. Table rotation control in props panel (2 hrs)
5. Booked table context in right panel (guest name, time) (3 hrs)
6. Side chairs on rectangular tables (1 hr)

### Sprint 3 — Power Features (P3s)
1. Duplicate table (Ctrl+D) (2 hrs)
2. Keyboard shortcut overlay (2 hrs)
3. Multi-select + group move (4 hrs)
4. Inline label editing on double-click (3 hrs)
5. Calendar view implementation (separate project)
6. Dining areas implementation (separate project)

---

## Comparative Notes — OpenTable Builder

From the OpenTable floor plan reference video, key UX patterns worth borrowing:
- **Ghost preview on placement** — OpenTable shows a translucent table following the cursor before placing
- **Auto-naming** — Tables auto-increment their label (T1, T2...) on placement — we have this ✓
- **Section colouring** — Dining areas are tinted regions on the canvas (e.g. Patio = pale yellow zone)
- **Occupancy numbers** — Live view shows current party at the table (e.g. "4/6") not just colour status
- **Rotation via drag handle** — Small rotate icon at top of selected table bounding box
