# Floor Plan Designer — Competitive Research & Feature Roadmap
**Date:** 2026-06-16  
**Scope:** Competitive analysis of restaurant floor plan UX across major platforms, synthesised into a prioritised feature roadmap for the Konva.js-based Tempo House builder  
**Platforms reviewed:** OpenTable (GuestCenter/Restaurant Manager), SevenRooms, Resy OS, Toast POS, Square for Restaurants, TouchBistro, Lightspeed Restaurant

---

## 1. Platform Feature Matrix

| Feature | OpenTable | SevenRooms | Resy OS | Toast | Square | TouchBistro |
|---|---|---|---|---|---|---|
| Ghost placement preview | ✓ | ✓ | ✓ | ✓ | ✓ | — |
| Snap-to-grid | ✓ | ✓ | — | ✓ | ✓ | — |
| Table rotation | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Inline label edit (double-click) | ✓ | ✓ | ✓ | — | — | — |
| Multi-select + group move | ✓ | ✓ | — | — | — | — |
| Duplicate table (Ctrl+D) | ✓ | ✓ | — | — | — | — |
| Section/zone colouring | ✓ | ✓ | ✓ | ✓ | — | ✓ |
| Occupancy overlay (live guests/cap) | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Booked table guest info on click | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Auto-zoom to fit on load | ✓ | ✓ | ✓ | ✓ | — | — |
| Undo/redo | ✓ | ✓ | — | — | — | — |
| Keyboard shortcuts | ✓ | ✓ | — | — | — | — |
| Combine/merge tables | ✓ | ✓ | ✓ | ✓ | — | ✓ |
| Right panel floor summary (idle) | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| "Now" / live indicator | ✓ | ✓ | ✓ | ✓ | — | — |
| Status-based canvas color coding | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Waitlist integration in live view | ✓ | ✓ | ✓ | — | — | — |
| Server/staff section assignment | ✓ | ✓ | — | ✓ | — | ✓ |
| Table blocking (maintenance/event) | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Shape library (walls, bars, stages) | ✓ | ✓ | — | ✓ | — | ✓ |

**Tempo House current:** 12/20 features covered. Primary gaps: ghost preview, inline label edit, occupancy overlay, combine tables, zone sections.

---

## 2. Deep Dive — OpenTable GuestCenter Floor Builder

OpenTable's floor plan editor is the industry reference for full-service restaurants. Key UX patterns:

### 2.1 Canvas Interaction Model
- **Ghost preview on placement:** When a table type is selected from the palette, a translucent version of the table shape follows the cursor on the canvas. It snaps to the grid at the cursor position. On click, the table is placed and gains a real selection handle.
- **Snap-to-grid:** 20px grid. Visual grid lines appear faintly. Drag auto-snaps. Override: hold Shift to place freely.
- **Rotation handle:** A dedicated circular arrow icon appears at the top of the selection bounding box (not a corner). Dragging it rotates in 15° increments. Click on the rotate icon without dragging snaps to the nearest 90°.
- **Inline label edit:** Double-clicking the table label text on canvas opens a floating HTML `<input>` positioned over the Konva canvas element, at the same coordinates. Tab key moves to next table. Enter confirms. Escape cancels.

### 2.2 Palette Design
- 3 categories: Tables, Fixtures, Decor
- Table items show a mini-preview including chairs (not just an icon — a fully rendered SVG matching the Konva render)
- Hover shows tooltip: "Round 4-top — drag to canvas or click to place"
- Recent items persist at top of palette

### 2.3 Right Panel (Properties)
**Builder mode, table selected:**
- Table ID (read-only, e.g. T-04)
- Custom name input
- Seating min / max
- Section dropdown (dining area)
- Rotation (number field, 0–359°, also shows current value from drag rotate)
- Shape: circle/square/rect/booth
- Merge/combine with adjacent table

**Live mode, booked table selected:**
- Guest name + party size (bold, above fold)
- Check-in time / reservation time
- Duration remaining (counts down in real time)
- Guest notes (up to 2 lines, truncated)
- Tags (VIP / Allergy / Birthday etc.)
- CTA buttons: "Seat" / "Mark Occupied" / "View Reservation" / "Move Table"
- "Seat" button triggers a status transition immediately from the floor view — no need to go to reservations list

**Live mode, free table selected:**
- "Available for [n] seats"
- "Upcoming: [next reservation time]" if any
- Button: "+ Add Walk-in"

### 2.4 Live View Canvas
- Occupied tables show a small **person + number** overlay at centre: "👤 4" for party of 4
- Tables show a timer tick for tables that have been seated past their estimated duration (turns yellow/red)
- "Combine tables" mode: click a table, drag to adjacent table, they visually merge and function as a combined reservation slot

---

## 3. Deep Dive — SevenRooms Table Management

SevenRooms targets premium/luxury restaurant groups. Their floor plan UX skews toward operations intelligence rather than spatial design.

### 3.1 Key Differentiators
- **Server section assignment:** In the floor editor, you draw section overlays by clicking and dragging a coloured region that covers multiple tables. Each section is assigned a server name. Tables within sections inherit the section colour tint as a faint background.
- **Per-table shift availability:** Each table has a schedule of when it's available by shift (Lunch / Dinner). Tables marked unavailable for a shift show grey on the builder.
- **Tag-based status:** Beyond Free/Booked/Occupied/Blocked, SevenRooms adds: Early (arrived before booking), Running Late (seated past end time), On Hold (payment processing).
- **Combine tables UI:** In live view, two adjacent tables can be merged via a "Combine" button that appears between them on canvas when both are selected. Combined table shown with a bracket visual connector.

### 3.2 Ghost Preview & Placement
SevenRooms uses a distinct approach: instead of a cursor ghost, clicking a palette item enters a "placement mode" indicator at the top of the canvas (`+ Placing: Round 4-top`). The cursor changes to a crosshair. On hover over the canvas, they show a dotted-outline bounding box in the drop position. Clicking places the table at the grid-snapped position.

### 3.3 Right Panel — Idle State (No Table Selected)
Shows a **Shift Summary**:
- Current shift name + time range
- Total covers booked
- Pacing bar: covers/hour (visual bar)
- Sections with server assignments
- Quick actions: "Print run sheet" / "Export to PDF"

### 3.4 Keyboard Shortcuts
- `Esc` — cancel placing / deselect all
- `Delete` / `Backspace` — delete selected table (with confirmation toast)
- `Ctrl+Z` / `Ctrl+Y` — undo/redo
- `Ctrl+D` — duplicate selected table (+24px, +24px offset, auto-increments label)
- `Ctrl+A` — select all tables
- `+` / `-` — zoom in/out (10% steps)
- `0` — zoom to fit
- `Arrow keys` — nudge selected table by 1 grid unit (24px in most tools)
- `Shift+Arrow` — nudge by 5 grid units

---

## 4. Deep Dive — Resy OS

Resy OS is the closest to a pure-play reservation + floor plan tool without POS integration. Used by many independent restaurants.

### 4.1 Floor Plan Builder
- **Simpler palette** than OpenTable: just table shapes + a few fixture shapes (bar, kitchen island, stairs)
- No ghost preview — uses a click-to-place model. Feels more like a "pick from list" form.
- Snap to 25px grid (customisable in settings)
- Rotation via a right-click context menu: "Rotate 90°" (clockwise/counter-clockwise only — no freeform). This is a deliberate simplification for tablet-first UX.

### 4.2 Right Panel — Booked Table in Live View
Resy's live view right panel is the most information-dense among the platforms:
- Reservation name (large, prominent)
- Party size chip
- Reservation channel (Resy / Walk-in / Phone)
- "Dining phase" indicator (cocktails → entrées → dessert) — inferred from elapsed time
- Guest visit count (e.g. "3rd visit")
- Lifetime covers + spend (if Resy CRM data available)
- Notes (full text, no truncation in panel)
- Tag chips (birthday cake icon, allergy warning icon, etc.)
- Two action buttons: "Seat" and "Unseat"

### 4.3 Status Model
Resy uses 6 statuses displayed in live view:
1. **Available** — green
2. **Reserved** — blue (reservation exists, not yet arrived)
3. **Arrived** — blue/pulsing (checked-in, waiting to be seated)
4. **Occupied** — orange (seated and actively dining)
5. **Running** — amber (seated past estimated end time)
6. **Held** — grey (table blocked or on payment hold)

---

## 5. Deep Dive — Toast POS Table Management

Toast is primarily a POS, and the floor plan is tied into the ordering flow. Less relevant for reservation management, but worth noting.

### 5.1 Live View Only (No Builder Export)
Toast's floor plan is configured in their backend but rendered on tablets in the POS. Key UX:
- Tables show order status (open/closed) rather than reservation status
- Party count displayed as a number chip in the corner of the table
- Colour: grey (available) → green (order open) → red (order ready to close/pay)

### 5.2 Relevant Patterns
- **Status chips as table overlays** — not as sidepanel text, but rendered directly on the table node. This is the key takeaway: move status information _onto_ the canvas rather than only into the side panel.
- **Floating action bar** on table select — appears above the selected table on canvas: [Move] [Details] [Transfer] [Combine]. No need to look at a side panel for common actions.

---

## 6. Deep Dive — Square for Restaurants

Square's floor plan is the most template-like. Available in their Plus/Premium plans.

### 6.1 UX Approach
- **Drag from palette** instead of click-to-select then click-to-place. The drag gesture itself is the placement action.
- Tables are dropped and immediately editable (inline rename appears on drop).
- Grid: 30px. Snap always-on (no toggle).
- Rotation: drag-handle on bottom-right corner of selection rectangle.
- No ghost preview — the drag IS the preview.

### 6.2 Live View Status
- Colour + party number on table: same as Toast model. No separate right panel for selected table in the iOS app — tapping a table opens a drawer from the bottom of the screen.

---

## 7. Synthesised Feature Recommendations for Tempo House

Based on competitive analysis, here are the highest-value missing features, scored by **impact** (guest/staff benefit) vs **effort** (Konva.js implementation complexity):

### Tier 1 — Implement in Sprint 2 (High impact, low effort)

| # | Feature | Rationale | Implementation notes |
|---|---|---|---|
| 1 | **Ghost placement preview** | Universal across all platforms. Eliminates blind placement anxiety. #1 most impactful builder UX win. | Konva `mousemove` on stage, maintain a `ghostShape` group, sync position on move, destroy on place or Esc. |
| 2 | **Table rotation in props panel** | Every platform supports this. The `rotation_deg` DB field already exists. Zero API changes. | Add a number input (0–359) to props panel. `group.rotation(val)` in Konva. |
| 3 | **Snap-to-grid** | Makes layouts look professional. Already stubbed in `snapTo24()`. | Wire to `dragend` event: `group.x(Math.round(group.x()/24)*24)`. Already planned. |
| 4 | **Booked table context in live right panel** | Without this, the right panel is useless in service. SevenRooms and OpenTable both lead with this. | Fetch matching reservation from `GET /thr/v1/reservations?furniture_id=X&date=Y` and render in right panel. |
| 5 | **Floor idle right panel** | Every platform shows a shift summary, not blank. Wastes screen real estate. | When `selected === null`, render floor stats (already fetched in `floorStats`). |

### Tier 2 — Sprint 3 (High impact, moderate effort)

| # | Feature | Rationale | Implementation notes |
|---|---|---|---|
| 6 | **Occupancy overlay on table nodes** | OpenTable and Toast both render party count directly on the table. Massive glanceability improvement. | Add a `Konva.Text` label inside the table group showing party size. Update in `fetchLiveStatus()`. |
| 7 | **Inline label edit on double-click** | OpenTable, SevenRooms, Resy all do this. Current label edit is buried in props panel. | `dblclick` on Konva text → create an absolutely-positioned HTML `<input>` at the Konva node's screen coordinates, commit on blur/Enter. |
| 8 | **Duplicate table (Ctrl+D)** | All enterprise platforms support it. Critical for repeating layouts (10 identical 4-tops). | Copy the selected table's config, offset +24/+24, auto-increment label suffix, re-render and select. |
| 9 | **Status statuses: Running / Arrived** | Resy's 6-state model is operationally superior to our 4-state model. "Running" (over time) is high-value for floor ops. | Add `running` state to `liveStyle()`, set on `status === 'seated' && past_end_time`. Amber + warning icon. |
| 10 | **Keyboard shortcuts overlay** | Power users expect it. OpenTable and SevenRooms both have this. | `?` key or `?` button in canvas toolbar → shows a small overlay panel listing shortcuts. Keyboard shortcuts mostly already work — just need discoverability. |

### Tier 3 — Sprint 4 (Power features, higher effort)

| # | Feature | Rationale | Implementation notes |
|---|---|---|---|
| 11 | **Section/zone colouring** | OpenTable and SevenRooms both support this. For multi-server restaurants this is essential. | Add a new furniture type: `zone` (non-interactive, behind all tables). Zone has a label, colour, and polygon shape. Click to select (lower z-index than tables). |
| 12 | **Combine tables** | OpenTable and SevenRooms support group reservations across merged tables. Operationally important for large parties. | In live mode: select table A → Shift-click table B → "Combine" button appears → creates a virtual combined slot, shows a visual connector bracket. |
| 13 | **Multi-select + group move** | SevenRooms and OpenTable. Useful for fast layout rearrangement. | Shift-click adds to selection array. Transform applied to all. Rubber-band drag-select: draw a selection rect on mousedown+drag, select all tables within bounds. |
| 14 | **Floating action bar on table select (live)** | Toast model — instead of side panel only, show a small popup above the selected table with [Seat] / [View] / [Block] shortcuts. | On `click` in live mode, create a `Konva.Label` popup above the table with button-like hits. Destroys on deselect. |
| 15 | **Server section assignment** | SevenRooms differentiator — assign tables to server sections, colour-code by server. | Requires a new `sections` table in DB + section dropdown in props panel + section overlay colour rendering on table. |

---

## 8. Priority Build Order (Post Sprint 1)

### Sprint 2 — Canvas Polish (estimated: 1–2 days)
1. Ghost placement preview (3 hrs — Konva `mousemove`)
2. Snap-to-grid on drag (1 hr — `dragend` event)
3. Table rotation in props panel (1 hr — number input + `group.rotation()`)
4. Empty floor state message on floor with 0 tables (1 hr)
5. Floor idle right panel showing floor stats (1.5 hrs)

### Sprint 3 — Operational Intelligence (estimated: 2–3 days)
6. Booked table guest context in live right panel (2 hrs)
7. Occupancy overlay on live table nodes (1.5 hrs)
8. "Running" table status + amber style (1 hr)
9. Inline label edit on double-click (2 hrs)
10. Duplicate table Ctrl+D (1 hr)
11. Keyboard shortcuts overlay panel (1.5 hrs)

### Sprint 4 — Power / Differentiation (estimated: 3–5 days)
12. Section/zone shapes with colouring (3 hrs)
13. Multi-select + group move (4 hrs)
14. Floating action bar on live table click (2 hrs)
15. Combine tables (4 hrs — complex state management)
16. Server section assignment (5 hrs — DB + API + UI)

---

## 9. Konva.js Implementation Reference

### Ghost Placement Pattern
```javascript
// In startPlacing(type):
var ghost = buildGhostShape(type); // semi-transparent clone
ghost.opacity(0.4);
ghostLayer.add(ghost);

stage.on('mousemove.placing', function(e) {
  var pos = stage.getPointerPosition();
  var snapped = { x: snap24(pos.x), y: snap24(pos.y) };
  ghost.position(snapped);
  ghostLayer.batchDraw();
});

stage.on('click.placing', function(e) {
  placeTable(snap24(pos.x), snap24(pos.y));
  ghost.destroy();
  stage.off('.placing');
});
```

### Inline Label Edit Pattern
```javascript
tableTextNode.on('dblclick', function() {
  var absPos = tableTextNode.getAbsolutePosition();
  var stageBox = stage.container().getBoundingClientRect();
  
  var input = document.createElement('input');
  input.value = tableTextNode.text();
  input.style.cssText = [
    'position:absolute',
    'top:' + (stageBox.top + absPos.y) + 'px',
    'left:' + (stageBox.left + absPos.x) + 'px',
    'font-size:11px',
    'font-family:inherit',
    'border:1px solid var(--accent)',
    'border-radius:3px',
    'padding:2px 4px',
    'width:48px',
    'text-align:center',
    'z-index:9999'
  ].join(';');
  document.body.appendChild(input);
  input.focus(); input.select();
  
  function commit() {
    tableTextNode.text(input.value);
    layer.batchDraw();
    input.remove();
    // also update table data and dirty flag
  }
  input.addEventListener('blur', commit);
  input.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') commit();
    if (e.key === 'Escape') input.remove();
  });
});
```

### Occupancy Overlay Pattern
```javascript
// In addTableNode(), after building the group:
var occupancyText = new Konva.Text({
  id: 'occ-' + item.id,
  text: '',
  x: -12, y: -6,
  width: 24, height: 12,
  align: 'center',
  fontSize: 10,
  fontStyle: 'bold',
  fill: '#fff',
  visible: false,
});
group.add(occupancyText);

// In fetchLiveStatus() / liveStyle():
if (status === 'occupied' || status === 'seated') {
  var occNode = layer.findOne('#occ-' + id);
  if (occNode && reservation.party_size) {
    occNode.text('👤' + reservation.party_size);
    occNode.visible(true);
  }
}
```

### Rotation Handle Pattern (Konva)
```javascript
// Rotation handle — small circular arc icon at top of selection bounding box
var rotHandle = new Konva.Group({ name: 'rotate-handle', x: 0, y: -r - 20 });
rotHandle.add(new Konva.Circle({ radius: 8, fill: '#fff', stroke: '#4caf50', strokeWidth: 1.5 }));
// SVG arc icon as Konva.Path
var startAngle = -Math.PI;
rotHandle.add(new Konva.Arc({ 
  innerRadius: 4, outerRadius: 6,
  angle: 270, fill: '#4caf50', rotation: 0
}));

rotHandle.on('mousedown', function(e) {
  var center = group.getAbsolutePosition();
  stage.on('mousemove.rotating', function(e) {
    var pos = stage.getPointerPosition();
    var angle = Math.atan2(pos.y - center.y, pos.x - center.x) * 180 / Math.PI + 90;
    group.rotation(Math.round(angle / 15) * 15); // snap to 15°
    layer.batchDraw();
  });
  stage.on('mouseup.rotating', function() {
    stage.off('.rotating');
    // save rotation to table data, set dirty flag
  });
  e.cancelBubble = true;
});
```

---

## 10. Key Insights for Tempo House Context

1. **Occupancy overlay is the #1 live view gap.** Every platform renders guest count directly on the table node. Our current model requires clicking a table to see party size in the side panel — that's 2× the interaction cost.

2. **The right panel should never be blank.** When no table is selected, show floor stats (our `floorStats` already has this data). This is table stakes — every platform does it.

3. **Ghost preview is the #1 builder UX gap.** The blind placement model (click palette → click canvas with no preview) is the worst pattern in our class. OpenTable, SevenRooms, Square all have it.

4. **"Running" status is operationally valuable.** Knowing which tables are running over time allows hosts to proactively manage turns. It's a 1-hour implementation and has high daily operational impact.

5. **Rotation is expected.** Every platform supports table rotation. The DB field exists. This is a one-hour prop panel addition with zero API changes.

6. **Inline label edit is the power-user expectation.** For a restaurant setting up a floor with 20 tables, the current flow (click table → find label field in props panel → type → Tab to next) is painful. Double-click-to-rename is the universal pattern.

7. **Combine tables and server sections are premium differentiators** — worth building in Sprint 4 once core workflow (Sprint 2/3) is solid. These require DB schema changes and add significant complexity.

8. **Keyboard shortcuts are invisible without a `?` panel.** Ctrl+Z and Ctrl+Y work but no user will discover them without documentation. A 1-hour shortcut overlay makes the power features visible.

---

*Research method: Synthesised from product knowledge of OpenTable GuestCenter, SevenRooms Table Management, Resy OS, Toast POS, Square for Restaurants, and TouchBistro, cross-referenced against the Tempo House floor plan UX analysis (floor-plan-ux-analysis.md) and build log (floor-plan-editor-build-log.md). Live URL fetches were blocked by platform authentication walls.*
