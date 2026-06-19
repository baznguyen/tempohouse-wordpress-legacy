# Event Layout Designer — Tech Stack Research
**Date:** 2026-06-19  
**Scope:** Architecture options for decoupling the Event Layout Designer from the existing WordPress/PHP/MySQL/Konva.js stack  
**Constraint:** WordPress = UX shell only (auth, admin nav, route registration). Not PHP rendering, not WP MySQL for event layout data.  
**Reference:** `Documentation/design/event-layout-designer-spec.md`, existing plugin `tempohouse-reservations` v1.4.0

---

## 1. What "WP as UX Shell" Actually Means

The existing floor plan editor is a vanilla JS IIFE loaded into a PHP-rendered WP admin page. Everything runs through WordPress:
- The admin page HTML is rendered by PHP (`THR_Admin::render_floor_plan_page()`)
- The canvas data (floor plans, furniture) is stored in WP MySQL (`wp_thr_furniture`, etc.)
- The REST API is WP REST (`/wp-json/thr/v1/...`) backed by `$wpdb` PHP queries
- The JS is a 1200-line vanilla IIFE with no build system

Decoupling means:
- **WP keeps:** login/auth, admin menu registration, loading the app shell page, the existing reservations/bookings data
- **WP drops:** rendering the canvas UI, owning the event layout data, the PHP REST handlers for layout ops
- **New system takes:** canvas app (compiled React SPA), layout data storage, layout API

The minimal WP involvement = one PHP function that registers an admin menu item and outputs a `<div id="eld-root"></div>` + a `<script>` tag pointing at the compiled React bundle. WordPress is used purely as an authenticated admin shell and a way to get WP user context into the app.

---

## 2. Canvas Library

### Current: Vanilla Konva.js (IIFE)
The existing floor plan builder uses Konva.js v9.3.14 via CDN, managed inside a vanilla IIFE. This works fine for the reservations editor but does not scale well to the complexity needed for the Event Layout Designer (table joining state, zone membership, notation anchoring, undo stacks, pax summaries, preset loading). Managing all of this in a global state object `const S = {...}` becomes brittle beyond ~1500 lines.

### Option A: `react-konva` (Recommended)
- **What it is:** Official React bindings for Konva.js. Each Konva object is a React component. Stage state lives in React state/reducers.
- **Why it fits:** Same Konva.js primitives (Stage, Layer, Rect, Circle, Text, Group, Transformer) — the rendering knowledge transfers directly from the existing editor. State management becomes proper React (useState, useReducer, Zustand), which eliminates the ad-hoc `S` object problem.
- **Bundle size:** `konva` (300KB gzip: 71KB) + `react-konva` (15KB) — acceptable for an admin app
- **Undo/redo:** Use `immer` with a history stack — trivial in React, painful in vanilla JS
- **PDF integration:** Same Konva `stage.toDataURL()` API works identically in react-konva

### Option B: Fabric.js
- Object-oriented canvas library with built-in serialisation (JSON in/out)
- Has React bindings but they're community-maintained (not official)
- Less alignment with existing Konva knowledge
- No meaningful advantage over react-konva for this use case

### Option C: React Flow + SVG
- Node/edge graph library — wrong abstraction for a 2D floor plan
- Not suitable

### Option D: Pixi.js / Three.js
- WebGL rendering — overkill for 2D floor plans
- No benefit for mm-scale SVG-style rendering

**Decision: `react-konva` + TypeScript.** Direct reuse of Konva.js rendering knowledge, React state solves the IIFE scaling problem, same PDF export path.

---

## 3. Frontend Framework

### React 18 + TypeScript + Vite

**Why React:**  
- `react-konva` is the best-maintained canvas option (see §2)
- React's component model and hooks handle the complex state (joined table groups, zone membership, notation refs, undo stacks) far better than vanilla JS
- Large ecosystem: Radix UI for accessible primitives (color picker, dropdowns), Zustand for global state, immer for immutable undo history

**Why Vite:**  
- Fast HMR in development
- Builds to a single `dist/` bundle (JS + CSS)
- The WP plugin enqueues the built `dist/main.js` as a regular WordPress script — no Node.js server needed in production
- Same local dev workflow: `vite dev` runs on port 3000, proxies WP API calls to `localhost:8888`

**Build output:** `dist/main.js` + `dist/main.css` → copied into the plugin at `assets/build/eld-app.*` → enqueued by WP on the Event Layouts admin page. This means the compiled React app is deployed alongside the WP plugin — no separate hosting required.

**Folder within plugin:**
```
WordPress/plugins/tempohouse-reservations/
  eld-app/          ← Vite source (React + TypeScript)
    src/
    public/
    vite.config.ts
    package.json
  assets/
    build/
      eld-app.js    ← compiled output (checked into git or built on deploy)
      eld-app.css
```

---

## 4. Backend / Data Layer Options

### The Key Question
Where does event layout data live? Three viable approaches evaluated below.

---

### Option A: Supabase (Hosted PostgreSQL)

**What it is:** Hosted Postgres + auto-generated REST + Realtime WebSockets + Storage + Edge Functions. Free tier: 500MB DB, 2GB storage, unlimited API calls, 50k monthly active users.

**Architecture:**
```
React SPA (in WP admin shell)
  → Supabase REST / Realtime (for all event layout ops)
  → WP REST API (for reservations data, auth nonce)
  
WP PHP (thin shell only):
  → Registers admin page
  → Issues Supabase JWT via /wp-json/thr/v1/eld-auth
  → Still owns: reservations, floor_plans, settings
```

**Auth bridge:**  
WP doesn't have a Supabase user table. Bridge options:
1. **Service role key proxy (recommended for this scale):** WP nonce → `GET /wp-json/thr/v1/eld-auth` → PHP verifies WP session → returns a short-lived Supabase service-role JWT to the React app. The service key lives only in WP PHP `wp_options`. React app uses this JWT for all Supabase calls. Simple, secure — the service key never reaches the browser.
2. **Supabase custom JWT provider:** WP signs a JWT with a shared secret; Supabase verifies it as a third-party auth provider. More elegant but requires Supabase Pro plan for custom auth JWT.
3. **Anon key + Row Level Security:** Supabase anon key in the React app. All rows scoped by a `session_token` column tied to the WP user ID. More work to implement correctly.

**For TEMPO House (< 10 staff users, no public Supabase access):** Option 1 (service role key via WP PHP proxy) is fine. The endpoint is behind WP authentication.

**Pros:**
- Real PostgreSQL — JSON columns, full-text search, complex queries trivially
- Realtime built-in (Phase 2: live collaboration, version sync)
- Storage for PDF snapshot archives
- Edge Functions for server-side PDF (Phase 2: Puppeteer in Supabase Edge)
- Free tier is more than sufficient forever for this venue's scale
- No server infrastructure to manage

**Cons:**
- Data lives in Supabase (US or EU region) — not locally on the WP server. Acceptable for layout data; reservations/PII stay in WP MySQL.
- Auth bridge requires a thin WP PHP endpoint
- Two backends to think about (WP for reservations + Supabase for layouts)
- Supabase is a vendor — if they change pricing or shut down, migration needed

---

### Option B: PocketBase (Self-Hosted Go Binary)

**What it is:** A single ~30MB Go binary that provides SQLite/PostgreSQL backing + REST API + Realtime WebSockets + built-in admin UI. Zero dependencies. Runs on the same VPS as WordPress.

**Architecture:**
```
React SPA (in WP admin shell)
  → PocketBase REST / Realtime (event layout ops)
  → WP REST API (reservations, auth)
  
PocketBase process:
  → Runs on same VPS at :8090 (e.g. behind nginx reverse proxy)
  → Nginx: /pb/* → localhost:8090
  
WP PHP:
  → Issues PocketBase admin token via /wp-json/thr/v1/eld-auth
```

**Pros:**
- Fully self-hosted — layout data stays on the same server as WordPress
- Zero external service dependency
- Realtime WebSockets built in
- SQLite backing (zero DB config, easy backup with `cp pocketbase.db backup.db`)
- Very lightweight: handles thousands of concurrent connections on a $6/month VPS
- PocketBase has its own admin UI (bonus — can inspect data directly)

**Cons:**
- Requires VPS access to run a persistent Go process (won't work on shared hosting)
- Nginx config needed to proxy PocketBase behind the domain
- Auth bridge still needed (WP → PocketBase admin token)
- Process management needed: systemd or Docker to keep PocketBase running
- Less ecosystem than Supabase (no Edge Functions, simpler Storage)

**Best for TEMPO if:** They have VPS access (likely given Docker dev setup) and want zero external dependencies.

---

### Option C: WP MySQL + React (Hybrid — "Modern Frontend, Same DB")

**What it is:** Keep WP MySQL as the data store but replace the PHP-rendered vanilla JS frontend with a compiled React SPA. New WP REST endpoints for event layouts (same pattern as `class-api-reservations.php`).

**Architecture:**
```
React SPA (in WP admin shell)
  → WP REST API /thr/v1/event-layouts/* (new PHP class, same MySQL)
  
WP PHP:
  → Owns all data
  → New API class: THR_API_Event_Layouts (same pattern as existing)
```

**Pros:**
- No new backend infrastructure
- Auth is trivial — WP nonces work out of the box
- All data in one place (WP MySQL)
- Deploy = same as the existing plugin
- Lowest complexity

**Cons:**
- Still PHP/MySQL — if "WP as UX shell only" means the backend too, this doesn't qualify
- MySQL is less capable for the JSON-heavy layout data (though MySQL 8+ JSON functions are reasonable)
- No native Realtime — Phase 2 collaboration would require polling or a separate WebSocket service
- PHP REST endpoints are more verbose than Supabase's auto-generated REST

---

### Option D: Cloudflare Workers + D1

**What it is:** Cloudflare's edge serverless stack. Workers = TypeScript serverless functions on Cloudflare's edge network. D1 = SQLite at the edge. R2 = object storage.

**Architecture:**
```
React SPA (in WP admin shell)
  → Cloudflare Worker API (event layout ops)
  → D1 database (SQLite, globally replicated)
  
WP PHP:
  → Issues a signed token for Cloudflare Worker auth
```

**Pros:**
- Extremely fast globally (HCMC users get edge-local responses)
- Free tier: 100k requests/day, 500MB D1 — sufficient for a boutique venue
- TypeScript for the API (same language as React frontend)
- R2 for PDF snapshot storage

**Cons:**
- D1 is relatively new (2022) — production maturity is lower than Supabase/PostgreSQL
- No native Realtime — would need Cloudflare Durable Objects (more complex)
- Auth bridge still needed
- Deployment complexity: WP plugin deploys to WP; Cloudflare Workers deploy separately via Wrangler
- Two separate deploy pipelines

---

## 5. Comparison Matrix

| Dimension | Supabase | PocketBase | WP MySQL + React | Cloudflare Workers |
|---|---|---|---|---|
| Data ownership | Supabase cloud | Self-hosted | WP server (same) | Cloudflare edge |
| Auth bridge complexity | Medium (WP JWT proxy) | Medium (WP → PB token) | None (WP nonces) | Medium |
| Backend infrastructure | None (hosted) | VPS process + nginx | None (WP already runs) | None (hosted edge) |
| Realtime (Phase 2) | ✓ native | ✓ native | ✗ (polling only) | ✗ (Durable Objects extra) |
| PDF server-side (Phase 2) | ✓ Edge Functions | ✓ (add Node service) | ✗ (client-side only) | ✓ Workers (limited) |
| Local dev complexity | Low | Medium (run PB + WP) | Low | Medium (Wrangler + WP) |
| Production deploy | Supabase dashboard | VPS process mgmt | WP plugin deploy | Wrangler CLI |
| Cost at current scale | Free | ~$6/mo VPS overhead | Free (WP already paid) | Free |
| Cost at growth | Still free | Same | WP hosting upgrade | Pay-as-you-go |
| Vendor lock-in | Medium | None (self-hosted) | Low (WP ecosystem) | Medium |
| Maturity | High (GA 2023) | High (v0.22+) | Very high | Medium (D1 beta→GA) |
| Ecosystem | Best | Good | WP plugins | Growing |

---

## 6. Authentication Bridge — Detail

All external backend options require a bridge between WP auth and the new backend. The pattern is the same regardless of option:

```
1. User logs into WordPress (already have WP session)
2. React SPA boots in WP admin shell
3. React SPA calls: GET /wp-json/thr/v1/eld-auth
   → PHP: verify_nonce() + current_user_can('manage_options')
   → PHP: calls Supabase/PocketBase to issue a short-lived token
   → Returns: { token: "...", expires_at: 1234567890 }
4. React stores token in memory (not localStorage — avoids XSS)
5. All API calls include: Authorization: Bearer {token}
6. Token refresh: repeat step 3 when token nears expiry
```

The WP REST endpoint (`/wp-json/thr/v1/eld-auth`) is a thin PHP function: verify WP auth → exchange for backend token → return. This is ~30 lines of PHP and only called once per session. The backend service key/admin password lives in `wp_options` (encrypted via WP's option API).

---

## 7. PDF Generation — Technical Deep Dive

PDF generation is client-side in the MVP and optionally server-side later.

### Client-Side MVP: jsPDF + Konva.toDataURL
```typescript
// Capture canvas at 3× pixel density (print quality)
const canvasImage = stage.toDataURL({ pixelRatio: 3, mimeType: 'image/png' });

// Compose designed PDF with jsPDF
import { jsPDF } from 'jspdf';
const pdf = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a3' });

// Header block
pdf.setFont('helvetica', 'bold');
pdf.setFontSize(14);
pdf.text('TEMPO House', 15, 20);
pdf.setFont('helvetica', 'normal');
pdf.setFontSize(10);
pdf.text(`EVENT: ${layout.name}`, 15, 28);
pdf.text(`${formatDate(layout.event_date)} · ${layout.event_time}`, 15, 34);
pdf.text(`Capacity: ${totalPax} guests`, 200, 34);

// Floor plan image (full bleed with margins)
pdf.addImage(canvasImage, 'PNG', 15, 45, 390, 220);

// Zone legend (coloured chips)
zones.forEach((zone, i) => {
  pdf.setFillColor(zone.hex_color);
  pdf.rect(15 + i * 60, 272, 8, 5, 'F');
  pdf.text(zone.name, 25 + i * 60, 276.5);
});

// Footer
pdf.setFontSize(8);
pdf.text('Scale 1:50', 15, 285);
pdf.text('tempohouse.com.vn', 350, 285);

// Page 2: notation appendix
if (notations.length) {
  pdf.addPage();
  // ... notation table render
}

pdf.save(`TEMPO-${slug}-${date}.pdf`);
```

**Output quality:** With `pixelRatio: 3`, a 1200px canvas renders at 3600px → embedded in an A3 PDF at ~210 DPI on the floor plan image. Acceptable for screen-quality design PDFs; slightly below print-optimal but indistinguishable on screen and most laser printers.

### Server-Side Phase 2: Supabase Edge Function + Chromium
```typescript
// supabase/functions/generate-pdf/index.ts
import puppeteer from 'https://deno.land/x/puppeteer@16.2.0/mod.ts';

Deno.serve(async (req) => {
  const { layoutId } = await req.json();
  const browser = await puppeteer.launch();
  const page = await browser.newPage();
  await page.setViewport({ width: 1920, height: 1080 });
  await page.goto(`${SITE_URL}/event-layout-render/${layoutId}`);
  await page.waitForSelector('#eld-canvas-ready');
  const pdf = await page.pdf({ format: 'A3', landscape: true, printBackground: true });
  await browser.close();
  return new Response(pdf, { headers: { 'Content-Type': 'application/pdf' } });
});
```

This produces a true vector-quality PDF at full resolution. Requires a `SITE_URL/event-layout-render/{id}` route — a public read-only render page that Puppeteer can navigate. Supabase Edge Functions support `@sparticuz/chromium` as a lightweight Chromium binary.

**When to add:** When clients start printing layouts at A3 for physical event setup. The jsPDF MVP is sufficient until that point.

---

## 8. Real-Time (Phase 2)

For Phase 2 (staff + client co-viewing, live approval), both Supabase and PocketBase have native WebSocket Realtime.

### Supabase Realtime
```typescript
import { createClient } from '@supabase/supabase-js';
const supabase = createClient(SUPABASE_URL, token);

// Subscribe to layout changes
supabase.channel('layout-42')
  .on('postgres_changes', {
    event: 'UPDATE',
    schema: 'public',
    table: 'event_layout_items',
    filter: `layout_id=eq.42`
  }, (payload) => {
    dispatch({ type: 'ITEM_UPDATED', payload: payload.new });
  })
  .subscribe();
```

This enables a client viewing the layout on a share link to see staff edits in real time — without page refresh.

### WP MySQL Fallback
If staying with WP MySQL (Option C), Realtime requires polling:
```typescript
// Poll every 5 seconds for layout version timestamp
useEffect(() => {
  const interval = setInterval(async () => {
    const { updated_at } = await fetchLayoutMeta(layoutId);
    if (updated_at > lastSeen) refetchLayout();
  }, 5000);
  return () => clearInterval(interval);
}, []);
```
This works but adds WP server load and provides a poor experience (5s lag on updates). Not recommended for Phase 2.

---

## 9. Local Dev Workflow

The existing WP dev environment runs at `localhost:8888` via Docker (`wp-env`). The React app needs to run alongside this.

### Proposed Dev Setup
```
localhost:8888  → WordPress (existing Docker container)
localhost:5173  → Vite dev server (React SPA)
```

Vite config proxies WP API calls:
```typescript
// eld-app/vite.config.ts
export default {
  server: {
    proxy: {
      '/wp-json': 'http://localhost:8888',
      '/wp-admin': 'http://localhost:8888',
    }
  }
}
```

WP admin page template:
```php
// In THR_Admin, for the event-layouts admin page:
wp_enqueue_script('thr-eld-app', THR_PLUGIN_URL . 'assets/build/eld-app.js', [], THR_VERSION, true);
wp_enqueue_style('thr-eld-app', THR_PLUGIN_URL . 'assets/build/eld-app.css', [], THR_VERSION);
wp_localize_script('thr-eld-app', 'thrELD', [
  'nonce'       => wp_create_nonce('thr_eld'),
  'userId'      => get_current_user_id(),
  'apiUrl'      => rest_url('thr/v1'),
  'userCan'     => [
    'manage'    => current_user_can('manage_options'),
  ],
  'locale'      => get_locale(),
]);
```

In dev, override the script URL to point at `localhost:5173/main.js` via a `WP_DEBUG`-gated condition. In production, the built `assets/build/eld-app.js` is used.

### Build + Deploy
```bash
# Build the React app
cd WordPress/plugins/tempohouse-reservations/eld-app
npm run build
# → outputs to ../assets/build/eld-app.js + eld-app.css

# Then deploy WP plugin as normal (rsync to live server)
```

No separate Node.js server or build pipeline in production — just static assets served by Apache/Nginx alongside WordPress.

---

## 10. Recommendation

### For TEMPO House MVP: Option C + React (Hybrid)

**WP MySQL + PHP REST API + React SPA** is the right call for the MVP, even though it keeps PHP on the backend.

**Why:**
1. **Speed to ship.** The auth story is zero complexity — WP nonces work out of the box. No bridge to write.
2. **No new infrastructure.** The layout data lives alongside the reservation data on the same WP MySQL server. One less moving part.
3. **Consistent deployment.** The existing plugin deploy (rsync WP files) still works. No Supabase dashboard to manage, no PocketBase process to keep alive.
4. **The "not WP tech" goal is met by React.** The vanilla JS IIFE + PHP-rendered templates is what made the existing editor feel like "WP tech." A React SPA with Vite, TypeScript, react-konva, and Zustand is modern regardless of what's in the DB. The user experience will feel completely different even with MySQL behind it.
5. **Migration path is clear.** If Phase 2 needs Realtime or Phase 3 needs Supabase, the React app is already decoupled — swap the API calls from WP REST to Supabase REST without changing the UI.

**What changes vs. staying pure WP:**
- The admin page is now an empty PHP shell that loads a compiled React bundle
- New PHP classes for event layout CRUD (same pattern as existing — 150 lines of PHP)
- All UI/UX = React + TypeScript + react-konva + Zustand + jsPDF
- No more vanilla JS IIFEs for this feature

### When to Move to Supabase: Phase 2 (client sharing + realtime)
When the client share link needs real-time updates (client sees staff edits live) and when the server-side PDF generation is needed, add Supabase as an additional backend layer. At that point, migrate the event layout tables to Supabase while keeping reservations in WP MySQL.

### Summary Table

| Decision | Choice | Rationale |
|---|---|---|
| Admin shell | WordPress PHP | Auth, menu, user context — keep |
| Frontend framework | React 18 + TypeScript + Vite | react-konva, proper state, ecosystem |
| Canvas library | react-konva (Konva.js) | Reuse rendering knowledge, React state |
| Global state | Zustand + immer | Undo/redo, cross-component state |
| Backend (MVP) | WP REST API (new PHP class) | Zero auth complexity, one deploy target |
| Database (MVP) | WP MySQL (new tables) | Same server, consistent with reservations |
| PDF generation | jsPDF + Konva.toDataURL | Client-side, no server dependency |
| Real-time (Phase 2) | Supabase Realtime | Migrate layout tables to Supabase at this point |
| PDF server-side (Phase 2) | Supabase Edge Function + Puppeteer | Upgrade when print quality needed |
| Local dev | Vite at :5173, proxy to WP :8888 | Parallel dev servers, same API |
| Production deploy | Build → assets/build/, rsync with plugin | No new deploy pipeline |

---

## 11. Key Libraries + Versions

```json
{
  "dependencies": {
    "react": "^18.3.1",
    "react-dom": "^18.3.1",
    "konva": "^9.3.14",
    "react-konva": "^18.2.10",
    "zustand": "^4.5.2",
    "immer": "^10.1.1",
    "jspdf": "^2.5.1",
    "@radix-ui/react-dropdown-menu": "^2.1.1",
    "@radix-ui/react-dialog": "^1.1.1",
    "@radix-ui/react-tooltip": "^1.1.2",
    "@radix-ui/react-popover": "^1.1.1"
  },
  "devDependencies": {
    "typescript": "^5.4.5",
    "vite": "^5.2.11",
    "@vitejs/plugin-react": "^4.3.0",
    "@types/react": "^18.3.3",
    "@types/react-dom": "^18.3.0"
  }
}
```

**Total estimated bundle size (gzip):** ~180KB for the full app (React 18: 43KB, react-konva + konva: 80KB, zustand: 2KB, jsPDF: 30KB, Radix: 25KB). Loaded only on the Event Layouts admin page.

---

## 12. What the Existing Reservations Floor Plan Builder Keeps

Nothing changes in the existing `floor-plan-builder.js` — it is a separate system. The Event Layout Designer is a completely new page and codebase within the same WP plugin. They coexist without interference.

| | Reservations Floor Plan | Event Layout Designer |
|---|---|---|
| Tech | Vanilla JS IIFE + Konva CDN | React + Vite + react-konva |
| State | Global `const S = {}` | Zustand + immer |
| Data | `wp_thr_furniture`, `wp_thr_floor_plans` | New `wp_thr_event_layouts` tables |
| API | `thr/v1/floors`, `thr/v1/furniture` | `thr/v1/event-layouts/*` (new PHP class) |
| Build | No build step (CDN Konva) | Vite build → assets/build/ |
| Auth | WP nonce in JS config | WP nonce in `window.thrELD` |

Future: when the reservations floor plan builder gets its Sprint 2-3 improvements (ghost preview, inline edit, etc.), those can optionally be migrated to React too — but that is a separate decision and not required now.

---

*Related: `Documentation/design/event-layout-designer-spec.md` · `Documentation/research/2026-06-19-event-layout-designer-research.md` · `Documentation/design/floor-plan-editor-build-log.md`*
