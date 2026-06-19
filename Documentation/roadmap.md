# TEMPO House — Product Roadmap
**Last updated:** 2026-06-19  
**Owner:** Bailey Nguyen  
**Format:** Phases in priority order. Each phase ships independently.

---

## Legend
- ✅ Shipped
- 🔨 In progress
- 📋 Planned (spec exists)
- 💡 Roadmap (concept only)

---

## Phase 0 — Foundation (Complete)
✅ WordPress site with TEMPO House theme  
✅ Reservations plugin with booking system (booking widget, confirmation emails, cancellation flow)  
✅ Floor plan editor v6 (Konva.js — table management for daily service)  
✅ Basic admin: floor plans, reservations, settings  
✅ Booking widget embeddable on site  

---

## Phase 1 — Event Layout Designer (Current priority)

**Goal:** Staff can design, save, and share event-specific floor plan layouts for private events. Client can view and approve via a share link. Staff can download a designed PDF.

**Spec:** `Documentation/design/event-layout-designer-spec.md`  
**Research:** `Documentation/research/2026-06-19-event-layout-designer-research.md`  
**Tech stack:** `Documentation/research/2026-06-19-event-layout-designer-tech-stack.md`

**Stack decisions (FINAL):**
- Standalone Next.js 16 app — NOT embedded in WordPress
- Project: `Events/` at repo root → hosted at `events.tempohouse.com.vn` on SiteGround
- Local dev: `localhost:7777` (`next dev -p 7777`)
- Canvas: react-konva (Konva.js React bindings)
- State: Zustand + immer
- Auth: Supabase Auth (email/password, staff invited from Supabase dashboard)
- Data: Supabase PostgreSQL (Singapore region)
- PDF: jsPDF client-side — designed A3 document
- WP integration: server-side fetch → `wp-json/thr/v1/*` read-only
- Deploy: SSH → git pull → npm build → Passenger restart via `touch tmp/restart.txt`

### 1A — Core Canvas + Toolbox
📋 New WP admin page "Event Layouts" under Reservations menu  
📋 Konva.js canvas with structural wall overlay + grid snap  
📋 Full furniture toolbox:
- Rectangle tables (4 size variants, mm-accurate)
- Round table 700mm (max 4 pax)
- Square table 700mm (max 4 pax)
- Bar stool chair
- Theatre chair (single, no pax)
- Flower centrepiece
- Media wall / backdrop stand (3 sizes)
- Projector (rotatable, projection cone indicator)
- Lounge (1800mm, 3 pax default)
- Coffee tables (square, round, rect variants)
- Side table

📋 Pax panel: per-table stepper with max enforcement, label edit  
📋 Hover tooltip: table label + pax count  
📋 Total capacity indicator in status bar  

### 1B — Table Joining
📋 Drag-overlap detection → snap-to-adjacent-edge on drop  
📋 Join group UUID assigned; dashed outline drawn around joined group  
📋 Chairs auto-removed from shared edge; restored on unjoin  
📋 Joined group pax = sum of members; shown on hover  

### 1C — Event Zones
📋 Zone draw mode: click-drag rectangle on canvas  
📋 Zone name + hex colour picker (TEMPO brand swatches + custom hex)  
📋 Zone renders below furniture; solid coloured border, transparent fill  

### 1D — Notation / Annotation System
📋 Notation pin placement: click to place, prompt for title + body  
📋 Reference character auto-assigned (A, B, C… or 1, 2…)  
📋 Hover card shows full note detail  
📋 Staff-only flag: pin hidden from client share view  
📋 PDF appendix: notation table at end of PDF document  

### 1E — Save / Load / Presets
📋 Layout metadata: name, event type, date, time, staff notes  
📋 Auto-save every 60s when dirty; save indicator in status bar  
📋 6 layout presets (blank, cocktail, seated dinner, theatre, gallery, boardroom)  
📋 Layout list sidebar: search, filter by event type, sort by date  

### 1F — PDF Export
📋 jsPDF + Konva toDataURL (pixelRatio: 3) for high-DPI output  
📋 Designed A3 landscape document (not browser print)  
📋 Page 1: event header, floor plan image, zone legend, capacity summary, scale label  
📋 Page 2 (if notations exist): notation appendix table + client sign-off block  
📋 Export options: include/exclude zone legend, notations, staff-only notations  

### 1G — Capacity Estimator (Gap analysis find)
📋 Live m²/person density in status bar  
📋 Thresholds: ≥2.0m² green, 1.5–2.0m² amber, <1.5m² red warning  
📋 Room area set at layout creation (overridable)  

### 1H — Constraint Markers (Gap analysis find)
📋 Structural overlay layer: fire exit markers, bar counter hatching  
📋 Non-interactive, non-editable — drawn from `structural_config.json`  

---

## Phase 2 — Client Sharing & Approval

**Goal:** Close the "email a PDF and hope for a reply" loop. Client gets a proper approval workflow.

📋 Share link system: token-based URL, enable/disable per layout  
📋 Client read-only view: canvas + notation hover, no edit capability  
📋 Client "Approve layout" button: generates timestamped approval record  
📋 Version snapshots: auto-save checkpoint on major change; manual checkpoint  
📋 Version history UI: list of snapshots, restore any version  
💡 Inline spatial comments: client clicks object → leaves comment anchored to it  
💡 Visual diff overlay: toggle to see changes between two versions (green = new, red = removed, arrow = moved)  

---

## Phase 3 — Reservations Floor Plan Editor — Improvements

**Goal:** Bring the existing daily reservations floor plan editor up to industry standard (based on competitive research `2026-06-16-floor-plan-designer-competitive-research.md`).

📋 Ghost placement preview (translucent table follows cursor during placement)  
📋 Inline label edit on double-click (floating HTML input over Konva canvas)  
📋 Occupancy overlay on table nodes (party size count rendered on table)  
📋 Booked table guest context in live right panel  
📋 "Running" status (amber — table seated past estimated end time)  
📋 Duplicate table Ctrl+D  
📋 Keyboard shortcuts overlay (? key or button)  
📋 Section/zone colouring on reservations floor plan  
📋 Combine tables in live mode  
📋 Floating action bar on table click (live mode)  

---

## Phase 4 — Event CRM & Guest Integration

**Goal:** Connect event layouts to guest lists and bookings. Enable catering export.

💡 Link event layout to a reservation or event booking record (FK)  
💡 Guest seating assignment panel: guest list sidebar, drag guest to seat  
💡 Dietary / allergy tags per guest (shown per table in staff view + PDF)  
💡 Meal count summary per table: "Table 3: 8 guests — 3 vege, 1 GF"  
💡 Notes-to-catering export: extract all notations + table meal counts as a catering brief PDF  
💡 Inhouse CRM: guest profiles, visit history, event preferences  

---

## Phase 5 — Next.js Customer-Facing Event Configurator

**Goal:** Prospects visiting /events/enquiry can explore a simplified version of the space and configure a layout before submitting an enquiry. Distinct from the admin Event Layout Designer.

**Spec (existing):** `Documentation/design/floorplan-interactive-nextjs-spec.md`

📋 Static structural SVG layer (ground-floor.svg)  
📋 @dnd-kit drag-to-place furniture layer  
📋 6 layout presets (same as admin presets)  
📋 Live capacity counter  
📋 Mobile touch: tap-to-place model  
📋 Serialise layout to hidden enquiry form field  

---

## Phase 6 — Advanced Features (Roadmap)

💡 Revision timeline with visual diff between versions  
💡 SVG export for infinite-scale PDF output  
💡 3D isometric preview of layout (lightweight, not full 3D tour)  
💡 Multiple venue areas: indoor + outdoor as separate layers/tabs on same canvas  
💡 Custom furniture upload: photo → auto-traced SVG asset in palette  
💡 Asset library by event type: 200+ venue-specific furniture items  
💡 Server-side PDF render (Puppeteer headless) for guaranteed print quality  
💡 Real-time co-editing (staff + planner work on same layout simultaneously)  

---

## North Star Vision

Every event at TEMPO House starts with a layout. Staff designs it in minutes using presets, shares it with the client in one click, client approves it with a timestamp, catering gets a brief extracted automatically, and the PDF that goes into the client's files looks like it came from a Michelin-starred event team. The floor plan IS the first impression of TEMPO's operational quality.

---

*Cross-reference: `Documentation/design/event-layout-designer-spec.md` · `Documentation/research/2026-06-19-event-layout-designer-research.md` · `Documentation/research/2026-06-16-floor-plan-designer-competitive-research.md`*
