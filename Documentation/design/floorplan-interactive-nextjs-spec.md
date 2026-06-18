# Interactive Floor Plan — Customer-Facing Next.js System
**Date:** 2026-06-18  
**Status:** Planned — not yet built  
**Context:** This is DISTINCT from the WordPress admin floor plan editor (Konva.js in `tempohouse-reservations` plugin). That system is for staff. This is for customers/prospects visiting the venue page.

---

## Purpose

A customer-facing interactive floor plan embedded on the TEMPO House website. Primary use case: event enquiry — prospects visiting `/events/enquiry` or `/venue` can explore the space and configure a layout that suits their event type before submitting an enquiry.

**Selling goal:** Remove "will the space work for us?" as a friction point. Let prospects self-serve the layout exploration, which primes them to enquire with confidence.

---

## System Architecture

### Two-layer model

```
Layer 1 — Structural SVG (static, fixed)
  Walls, columns, bar counter, bathroom, back of house, garden, terrace
  Source: /floor-plans/ground-floor.svg
  Never changes. Users cannot interact with it.

Layer 2 — Furniture Overlay (interactive, React)
  Tables, chairs, zones dropped onto the fixed floor plan
  State: React useState / useReducer
  Draggable via @dnd-kit
```

The static SVG provides the architectural truth. The React overlay handles all interactivity. This separation means:
- Floor plan accuracy is maintained (no drift from user interaction)
- Interactive layer can be reset without touching structural layer
- SVG can be updated independently (e.g. after renovation) without touching interaction code

---

## Tech Stack

| Concern | Choice | Reason |
|---|---|---|
| Framework | Next.js (App Router) | Already the site stack |
| Language | TypeScript | Project standard |
| Rendering | React + SVG | Native, no canvas overhead, accessible |
| Drag & Drop | @dnd-kit/core + @dnd-kit/sortable | Lightweight, accessible, touch-native |
| State | React useState / useReducer | Local component state (no server sync needed) |
| Responsive | CSS container queries + SVG `viewBox` scaling | Floor plan must scale cleanly at all breakpoints |
| Styles | CSS Modules | Project standard — no Tailwind |

---

## Routes

| Route | Purpose |
|---|---|
| `/venue` | Static SVG only (Phase 1 — current). May upgrade to show interactive floor plan preview. |
| `/events/enquiry` | Primary home for the interactive configurator. Prospects select a layout, then submit. |
| Potentially `/events/floor-plan` | Standalone view if configurator becomes complex enough to need its own page. |

Decision: start by embedding within `/events/enquiry` page flow. Extract to its own route if scope warrants it.

---

## Event Configuration Layouts

Pre-built layout presets that the prospect can switch between. Each preset defines a set of furniture items with default positions.

| Preset | Capacity | Layout character |
|---|---|---|
| Cocktail reception | 60–80 | Standing + high tables, bar open, no formal seating |
| Seated dinner | 30–40 | Round 4/6-tops, formal rows or clusters |
| Gallery opening | 50–70 | Walls cleared, seating peripheral, artwork focus |
| Corporate breakfast | 20–30 | Boardroom-style or cabaret rows, lectern |
| Intimate supper club | 16–24 | Single long table or 2–3 intimate clusters |
| Private screening | 30–40 | Theatre rows facing a screen end |

Each preset is a JSON config: array of `{ type, x, y, seats, rotation }` objects positioned in SVG coordinate space.

---

## Interaction Model

### Desktop
- **Drag**: Grab furniture items from a sidebar palette → drop onto the floor plan canvas
- **Reposition**: Drag placed items to rearrange
- **Select**: Click an item to select it; shows a props chip (type, seats)
- **Remove**: Selected item shows a × / delete control
- **Preset picker**: Toggle between layout presets via a pill-tab row above the canvas
- **Reset**: "Start over" button returns to the selected preset's default positions
- **Capacity counter**: Live total seat count updates as items are added/removed

### Mobile / Touch
- **Tap to place**: Tap palette item → tap location on canvas to place (no drag — fat-finger drag on SVG is unreliable at phone size)
- **Tap to select/move**: Tap a placed item to select; arrow buttons (or tap-destination) to nudge
- **Preset picker**: Full-width tabs, scrollable horizontally if needed
- All controls large enough for touch (min 44px hit targets)

### Accessibility
- All interactive elements are keyboard-navigable
- Presets are radio-group buttons (keyboard switchable)
- Palette items are button elements
- Canvas fallback: screen-reader description of the selected layout ("Cocktail reception — standing room for 70, bar open on east side")

---

## Component Structure

```
/Website/app/(site)/events/enquiry/
  page.tsx                    ← enquiry form page
  FloorConfigurator/
    index.tsx                 ← orchestrator: preset state, layout state
    Canvas.tsx                ← SVG container; renders structural layer + furniture overlay
    StructuralFloorPlan.tsx   ← inline SVG (from ground-floor.svg, stripped to structural paths)
    FurnitureLayer.tsx        ← @dnd-kit droppable; renders placed furniture items
    FurniturePalette.tsx      ← sidebar palette of draggable items
    PresetPicker.tsx          ← pill tabs for switching layout presets
    CapacityBadge.tsx         ← live seat count
    furniture-items.ts        ← type definitions + SVG shapes for each furniture type
    presets.ts                ← layout preset configs (JSON arrays of placed items)
    floor-configurator.module.css
```

---

## Structural SVG Integration

The interactive canvas uses the same `/floor-plans/ground-floor.svg` geometry but renders structural paths inline in the React component (not via `<img>`). This allows:
- SVG coordinate space shared between structural layer and furniture overlay
- CSS pointer-events control (structure is `pointer-events: none`)
- Single `viewBox` governs both layers

When the static SVG is updated (e.g. after renovations), `StructuralFloorPlan.tsx` must be updated in sync.

---

## Data Flow (No Backend Required)

The configurator is purely client-side state — no API calls needed for the drag-and-drop experience itself.

```
User interaction
  → React state update (placed items array)
  → Canvas re-renders furniture overlay
  → CapacityBadge reads total seats from state
  → On "Submit enquiry" → selected layout + capacity
    serialised to form hidden field → standard enquiry form POST
```

The enquiry submission carries:
- Selected preset name
- Capacity configured
- Optional: serialised layout JSON (so TEMPO team can see exactly what the prospect envisioned)

---

## Responsive Behaviour

| Breakpoint | Canvas | Palette | Preset picker |
|---|---|---|---|
| ≥ 1024px | Full size, side-by-side with palette | Left sidebar | Above canvas |
| 768–1023px | Full width, palette collapses below | Horizontal scroll row below canvas | Above canvas |
| < 768px | Full width; tap-to-place interaction | Bottom sheet or horizontal scroll | Top tabs, scrollable |

SVG `viewBox` stays constant. The canvas element scales via CSS `width: 100%; height: auto` — SVG scales proportionally. Furniture items positioned in SVG coordinate space scale automatically with it.

---

## Build Phases

### Phase A — Core configurator (MVP)
- [ ] `StructuralFloorPlan.tsx` — inline structural SVG
- [ ] `FurniturePalette.tsx` — palette with 4 basic item types (round 4, round 6, rect 4, high table)
- [ ] `FurnitureLayer.tsx` — @dnd-kit droppable canvas, drag-to-place, drag-to-move
- [ ] `CapacityBadge.tsx` — live seat counter
- [ ] Basic CSS layout (desktop first)
- [ ] Embed in `/events/enquiry` page

### Phase B — Presets + Polish
- [ ] `PresetPicker.tsx` — 6 layout presets with default positions
- [ ] Reset button
- [ ] Mobile touch interaction (tap-to-place)
- [ ] Responsive CSS

### Phase C — Enquiry Integration
- [ ] Serialise layout to hidden form field
- [ ] Enquiry form shows selected preset + capacity in the staff email
- [ ] Screen-reader accessible layout description

---

## Related Files

- `Website/public/floor-plans/ground-floor.svg` — Phase 1 static SVG (source of structural geometry)
- `WordPress/plugins/tempohouse-reservations/assets/js/floor-plan-builder.js` — Separate system (admin/staff, Konva.js) — NOT related to this feature
- `Documentation/design/floor-plan-ux-analysis.md` — UX analysis of the WordPress admin editor (different system)
- `Documentation/design/floor-plan-editor-build-log.md` — Build log for WordPress editor (different system)
