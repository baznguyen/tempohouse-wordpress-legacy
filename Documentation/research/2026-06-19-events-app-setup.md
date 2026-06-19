# Events App — Setup & Tech Stack
**Date:** 2026-06-19  
**App name:** TEMPO Events  
**URL:** `events.tempohouse.com.vn`  
**Local dev:** `localhost:7777`  
**Hosting:** SiteGround (shared/cloud) + Phusion Passenger  
**Project root:** `Events/` (alongside `Website/` and `WordPress/`)

---

## 1. What This App Is

A standalone Next.js admin application, completely separate from:
- The public website (`Website/` — static export, FTP deploy)
- The WordPress reservations plugin (`WordPress/` — PHP, Docker dev)

It handles everything event-layout related: designing layouts, managing events, sharing with clients, PDF downloads. It calls the WP REST API read-only for reservation data when needed. WordPress does not know this app exists.

---

## 2. Tech Stack — Final Decisions

| Layer | Choice | Reason |
|---|---|---|
| Framework | Next.js 16.2.6 + App Router | Matches existing website version — shared knowledge |
| Language | TypeScript 5.x | Project standard |
| React | React 19 | Matches website |
| Canvas | `react-konva` + `konva` | React bindings for Konva.js — reuses existing canvas knowledge |
| Global state | `zustand` + `immer` | Undo/redo history, cross-component canvas state |
| Auth | Supabase Auth (`@supabase/ssr`) | Same vendor as data — one dashboard, email/password invite flow |
| Database | Supabase (PostgreSQL) | Hosted, free tier sufficient, Realtime built-in for Phase 2 |
| PDF | `jspdf` | Client-side designed PDF (A3 landscape, not browser print) |
| UI primitives | `@radix-ui/*` | Accessible dropdowns, tooltips, dialogs — unstyled, matches TEMPO CSS approach |
| Styles | CSS Modules | Project standard — no Tailwind |
| Hosting | SiteGround + Phusion Passenger | Existing hosting, Node.js Selector in cPanel |
| Local dev port | `7777` | Specified |
| WP integration | Server-side fetch to `wp-json/thr/v1/*` | Read-only; layouts live in Supabase |

---

## 3. Project Structure

```
tempohouse.com.vn/
├── Brand Assets/
├── Documentation/
├── Website/               ← public Next.js site (unchanged)
├── WordPress/             ← reservations WP plugin (unchanged)
└── Events/                ← NEW: events admin Next.js app
    ├── app/
    │   ├── (auth)/
    │   │   └── login/
    │   │       ├── page.tsx
    │   │       └── login.module.css
    │   ├── (admin)/
    │   │   ├── layout.tsx              ← admin shell (sidebar nav, auth guard)
    │   │   ├── page.tsx                → redirect to /layouts
    │   │   ├── layouts/
    │   │   │   ├── page.tsx            ← layout list
    │   │   │   └── [id]/
    │   │   │       └── page.tsx        ← canvas editor
    │   │   └── settings/
    │   │       └── page.tsx            ← (Phase 2: app settings)
    │   ├── share/
    │   │   └── [token]/
    │   │       └── page.tsx            ← public client view (no auth)
    │   ├── layout.tsx                  ← root layout (fonts, global CSS)
    │   └── globals.css
    ├── components/
    │   ├── canvas/
    │   │   ├── EventCanvas.tsx         ← react-konva stage, all layers
    │   │   ├── Toolbar.tsx             ← top toolbox (furniture types)
    │   │   ├── PropertiesPanel.tsx     ← floating props card
    │   │   ├── ZonePanel.tsx           ← zone color picker
    │   │   ├── NotationPin.tsx
    │   │   └── furniture/
    │   │       ├── RectTable.tsx
    │   │       ├── RoundTable.tsx
    │   │       ├── SquareTable.tsx
    │   │       ├── Lounge.tsx
    │   │       ├── CoffeeTable.tsx
    │   │       └── ...
    │   └── ui/
    │       ├── ColorPicker.tsx
    │       ├── PaxStepper.tsx
    │       └── ...
    ├── lib/
    │   ├── supabase/
    │   │   ├── client.ts               ← browser Supabase client
    │   │   ├── server.ts               ← server Supabase client (SSR)
    │   │   └── middleware.ts           ← session refresh helper
    │   ├── canvas/
    │   │   ├── furniture-config.ts     ← all furniture types, mm dimensions, max pax
    │   │   ├── presets.ts              ← 6 layout presets as JSON configs
    │   │   ├── join-tables.ts          ← table joining logic
    │   │   └── scale.ts                ← mm ↔ canvas unit conversions
    │   ├── pdf/
    │   │   └── generate.ts             ← jsPDF layout document composer
    │   └── wp-api.ts                   ← server-side fetch wrapper for WP REST
    ├── store/
    │   └── canvas-store.ts             ← Zustand store (items, zones, notations, undo stack)
    ├── types/
    │   └── layout.ts                   ← shared types (LayoutItem, Zone, Notation, etc.)
    ├── middleware.ts                    ← Next.js middleware (auth guard for /admin/*)
    ├── next.config.ts
    ├── package.json
    ├── tsconfig.json
    └── .env.local                      ← local env vars (gitignored)
```

---

## 4. Supabase Setup

### 4.1 Database Tables

Create in Supabase SQL editor. All tables use RLS (Row Level Security) — authenticated users only.

```sql
-- Event layouts
create table event_layouts (
  id            uuid primary key default gen_random_uuid(),
  name          text not null,
  event_type    text,                    -- 'cocktail' | 'seated_dinner' | 'theatre' | 'gallery' | 'custom'
  event_date    date,
  event_time    time,
  notes         text,
  share_token   text unique,
  share_enabled boolean default false,
  capacity      int default 0,           -- cached total pax (updated on save)
  room_area_sqm numeric(6,2),           -- for capacity estimator
  created_by    uuid references auth.users(id),
  created_at    timestamptz default now(),
  updated_at    timestamptz default now()
);

-- Layout items (furniture placed on canvas)
create table event_layout_items (
  id            uuid primary key default gen_random_uuid(),
  layout_id     uuid references event_layouts(id) on delete cascade,
  item_type     text not null,           -- furniture type key
  item_variant  text,                    -- e.g. '800x1200', '700', '1500'
  x             numeric(8,2) not null,
  y             numeric(8,2) not null,
  rotation      numeric(6,2) default 0,
  pax           smallint default 0,
  label         text,
  zone_id       uuid,                    -- FK to event_zones (set after zone created)
  join_group    uuid,                    -- shared UUID for joined table sets
  created_at    timestamptz default now()
);

-- Event zones (coloured regions)
create table event_zones (
  id            uuid primary key default gen_random_uuid(),
  layout_id     uuid references event_layouts(id) on delete cascade,
  name          text not null,
  hex_color     char(7) not null default '#C76E4B',
  x             numeric(8,2) not null,
  y             numeric(8,2) not null,
  width         numeric(8,2) not null,
  height        numeric(8,2) not null
);

-- Notations / annotations
create table event_notations (
  id            uuid primary key default gen_random_uuid(),
  layout_id     uuid references event_layouts(id) on delete cascade,
  ref           text not null,           -- auto-assigned: 'A', 'B', 'C'...
  x             numeric(8,2) not null,
  y             numeric(8,2) not null,
  title         text,
  body          text,
  staff_only    boolean default false
);

-- RLS policies
alter table event_layouts enable row level security;
alter table event_layout_items enable row level security;
alter table event_zones enable row level security;
alter table event_notations enable row level security;

-- Authenticated users can do everything
create policy "auth full access" on event_layouts for all to authenticated using (true);
create policy "auth full access" on event_layout_items for all to authenticated using (true);
create policy "auth full access" on event_zones for all to authenticated using (true);
create policy "auth full access" on event_notations for all to authenticated using (true);

-- Public: read layout by share_token (for client view page)
create policy "public share read" on event_layouts
  for select to anon
  using (share_enabled = true);

create policy "public share items" on event_layout_items
  for select to anon
  using (
    exists (
      select 1 from event_layouts el
      where el.id = layout_id and el.share_enabled = true
    )
  );

-- Repeat public select policies for event_zones and event_notations (excluding staff_only)
```

### 4.2 Auth Setup

In Supabase Dashboard → Authentication → Settings:
- Enable Email provider
- Disable "Confirm email" for staff invites (optional — simplifies onboarding)
- Set Site URL: `https://events.tempohouse.com.vn`
- Add redirect URL: `https://events.tempohouse.com.vn/auth/callback`

Add staff:
- Dashboard → Authentication → Users → "Invite user" → enter staff email
- Staff receive invite email, set their own password
- No self-signup — only invited users can access the app

### 4.3 Environment Variables

```bash
# Events/.env.local
NEXT_PUBLIC_SUPABASE_URL=https://[project-ref].supabase.co
NEXT_PUBLIC_SUPABASE_ANON_KEY=eyJ...
SUPABASE_SERVICE_ROLE_KEY=eyJ...        # server-only, never exposed to browser

# WP integration (for reading reservation data)
WP_API_URL=https://tempohouse.com.vn/wp-json/thr/v1
WP_API_KEY=                             # WP application password for read-only calls

# App
NEXT_PUBLIC_APP_URL=https://events.tempohouse.com.vn
```

---

## 5. Authentication — Next.js Middleware

```typescript
// Events/middleware.ts
import { createServerClient } from '@supabase/ssr'
import { NextResponse, type NextRequest } from 'next/server'

export async function middleware(request: NextRequest) {
  let response = NextResponse.next({ request })

  const supabase = createServerClient(
    process.env.NEXT_PUBLIC_SUPABASE_URL!,
    process.env.NEXT_PUBLIC_SUPABASE_ANON_KEY!,
    { cookies: { /* @supabase/ssr cookie helpers */ } }
  )

  const { data: { user } } = await supabase.auth.getUser()

  // Protect all /admin/* routes
  if (request.nextUrl.pathname.startsWith('/') && 
      !request.nextUrl.pathname.startsWith('/login') &&
      !request.nextUrl.pathname.startsWith('/share') &&
      !user) {
    return NextResponse.redirect(new URL('/login', request.url))
  }

  return response
}

export const config = {
  matcher: ['/((?!_next/static|_next/image|favicon.ico).*)'],
}
```

Public routes (no auth required):
- `/login` — staff sign-in page
- `/share/[token]` — client-facing read-only layout view
- `/auth/callback` — Supabase OAuth callback

Protected routes (requires Supabase session):
- Everything else (`/layouts`, `/layouts/[id]`, `/settings`)

---

## 6. Local Dev Configuration

```json
// Events/package.json (scripts section)
{
  "scripts": {
    "dev": "next dev -p 7777",
    "build": "next build",
    "start": "node server.js",
    "lint": "next lint"
  }
}
```

Local dev runs at `http://localhost:7777`. The Supabase calls go direct to `https://[project].supabase.co` — no local proxy needed (Supabase has a local CLI if offline dev is ever needed).

WP API calls in dev: set `WP_API_URL=http://localhost:8888/wp-json/thr/v1` to point at the Docker WP instance.

---

## 7. SiteGround Deployment

### 7.1 Which SiteGround Plan?

**Shared hosting (GrowBig / GoGeek):**
- Uses cPanel Node.js Selector (Phusion Passenger under the hood)
- SSH access available
- Memory: ~1.5–2GB per process (enough for Next.js)
- CPU: shared — builds should be done locally and uploaded, not on server

**Cloud hosting / VPS:**
- Full root access
- Can use PM2 for process management
- More control, but same approach

The setup below covers **shared hosting with cPanel Node.js**. The VPS path is simpler (just use PM2).

### 7.2 SiteGround: One-Time Setup

**Step 1 — Add subdomain:**
- cPanel → Domains → Subdomains
- Subdomain: `events`
- Domain: `tempohouse.com.vn`
- Document root: `/home/[user]/events.tempohouse.com.vn` (SiteGround auto-fills)

**Step 2 — Create Node.js app:**
- cPanel → Software → Node.js
- → "Create Application"
  - Node.js version: **20.x** (LTS — stable for Next.js 16)
  - Application mode: Production
  - Application root: `/home/[user]/events.tempohouse.com.vn`
  - Application URL: `events.tempohouse.com.vn`
  - Application startup file: `server.js`
- → Create

SiteGround's Passenger will:
- Watch for a `server.js` at the application root
- Manage the process (start, restart, crash recovery)
- Proxy HTTP from `events.tempohouse.com.vn` → the Node.js process port

**Step 3 — Set environment variables:**
- In the Node.js app panel, there's an "Environment Variables" section
- Add all vars from `.env.local` (production values)
- `NEXT_PUBLIC_APP_URL=https://events.tempohouse.com.vn`
- Supabase keys, WP API URL, etc.

**Step 4 — SSL:**
- cPanel → SSL/TLS → Let's Encrypt
- Issue certificate for `events.tempohouse.com.vn`
- Auto-renews — no action needed after initial setup

### 7.3 Custom Server File

Next.js needs a `server.js` for Passenger (Passenger calls this file directly):

```javascript
// Events/server.js
const { createServer } = require('http')
const { parse } = require('url')
const next = require('next')

const app = next({ dev: false })
const handle = app.getRequestHandler()

app.prepare().then(() => {
  createServer((req, res) => {
    const parsedUrl = parse(req.url, true)
    handle(req, res, parsedUrl)
  }).listen(process.env.PORT || 3000, (err) => {
    if (err) throw err
  })
})
```

Passenger assigns `process.env.PORT` automatically. Do not hardcode port 3000 — let Passenger set it.

### 7.4 Deploy Workflow

**First deploy (SSH):**
```bash
# SSH into SiteGround
ssh [user]@tempohouse.com.vn -p 18765

# Navigate to app root (created by SiteGround in step 2)
cd ~/events.tempohouse.com.vn

# Clone the repo (or push via git)
git clone https://github.com/[org]/tempohouse-events.git .

# Install deps (production only)
npm ci --omit=dev

# Build
npm run build

# Restart via cPanel → Node.js → Restart App
# OR: touch tmp/restart.txt (Passenger restart signal)
touch tmp/restart.txt
```

**Subsequent deploys (SSH):**
```bash
ssh [user]@tempohouse.com.vn -p 18765
cd ~/events.tempohouse.com.vn
git pull origin main
npm ci --omit=dev
npm run build
touch tmp/restart.txt
```

**Or via SiteGround's Git integration:**
- cPanel → Git Version Control → manage deployments
- Push to repo → trigger build hook

### 7.5 next.config.ts for SiteGround

```typescript
// Events/next.config.ts
import type { NextConfig } from 'next'

const nextConfig: NextConfig = {
  // NO output: "export" — this is a server app
  // Passenger handles the Node.js process
  
  // If SiteGround serves assets from a CDN or subfolder:
  // assetPrefix: 'https://events.tempohouse.com.vn',
  
  experimental: {
    // Nothing special needed for SiteGround
  },
}

export default nextConfig
```

Key difference from the public website: **no `output: "export"`**. This is a full server-side Next.js app.

---

## 8. WP Integration

The events app calls the WP REST API server-side (from Server Actions or Route Handlers) to read reservation data. The WP site does not know about the events app.

```typescript
// Events/lib/wp-api.ts
const WP_BASE = process.env.WP_API_URL  // 'https://tempohouse.com.vn/wp-json/thr/v1'
const WP_KEY  = process.env.WP_API_KEY   // WP Application Password (base64 encoded)

export async function getReservations(params: Record<string, string> = {}) {
  const url = new URL(`${WP_BASE}/reservations`)
  Object.entries(params).forEach(([k, v]) => url.searchParams.set(k, v))
  
  const res = await fetch(url.toString(), {
    headers: {
      'Authorization': `Basic ${WP_KEY}`,
    },
    next: { revalidate: 60 },  // cache for 60s
  })
  
  if (!res.ok) throw new Error(`WP API error: ${res.status}`)
  return res.json()
}
```

**WP setup needed:** Create a WP Application Password for the events app:
- WP Admin → Users → [admin user] → Application Passwords
- Name: "Events App (read-only)"
- Copy the generated password → base64 encode `username:password` → put in `WP_API_KEY`

**CORS on WP side:** Add to the WP plugin or `functions.php`:
```php
add_filter('rest_pre_serve_request', function($value) {
  $origin = get_http_origin();
  if (in_array($origin, ['https://events.tempohouse.com.vn', 'http://localhost:7777'])) {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Access-Control-Allow-Methods: GET, OPTIONS');
    header('Access-Control-Allow-Headers: Authorization, Content-Type');
  }
  return $value;
});
```

---

## 9. Key Libraries — package.json

```json
{
  "name": "tempo-events",
  "version": "0.1.0",
  "private": true,
  "scripts": {
    "dev": "next dev -p 7777",
    "build": "next build",
    "start": "node server.js",
    "lint": "next lint",
    "type-check": "tsc --noEmit"
  },
  "dependencies": {
    "next": "16.2.6",
    "react": "19.2.4",
    "react-dom": "19.2.4",
    "konva": "^9.3.14",
    "react-konva": "^18.2.10",
    "zustand": "^4.5.2",
    "immer": "^10.1.1",
    "jspdf": "^2.5.1",
    "@supabase/supabase-js": "^2.45.0",
    "@supabase/ssr": "^0.5.1",
    "@radix-ui/react-dropdown-menu": "^2.1.1",
    "@radix-ui/react-dialog": "^1.1.1",
    "@radix-ui/react-tooltip": "^1.1.2",
    "@radix-ui/react-popover": "^1.1.1",
    "@radix-ui/react-tabs": "^1.1.0"
  },
  "devDependencies": {
    "typescript": "^5.4.5",
    "@types/node": "^20",
    "@types/react": "^19",
    "@types/react-dom": "^19",
    "eslint": "^9",
    "eslint-config-next": "16.2.6"
  }
}
```

**Bundle size estimate (gzip):** ~210KB total for the admin canvas app. Loaded only for authenticated staff — not on the public site.

---

## 10. Route Summary

| Route | Auth | Purpose |
|---|---|---|
| `/login` | Public | Staff sign-in via Supabase Auth |
| `/` | Protected | Redirects to `/layouts` |
| `/layouts` | Protected | List of all event layouts |
| `/layouts/[id]` | Protected | Canvas editor for a specific layout |
| `/share/[token]` | Public | Client read-only layout view |
| `/auth/callback` | Public | Supabase OAuth redirect handler |

---

## 11. Limitations & Notes

**react-konva + React 19:** `react-konva` is pinned to React 18 in its peer deps. With React 19, it should still work (React 19 is backwards-compatible with 18 APIs). If type errors appear, add `"overrides": { "react": "19.2.4" }` to package.json. Test the canvas render early.

**SiteGround memory:** `npm run build` on a shared server can hit memory limits. Build locally (or in CI) and upload the `.next/` folder + `node_modules/` via SFTP, or use `npm run build` over SSH during low-traffic periods.

**SiteGround Node.js Selector vs. PM2:** Passenger (used by SiteGround's Selector) is not PM2. You do not need `pm2` commands. Restart the app by touching `tmp/restart.txt` or using the cPanel "Restart" button.

**`react-konva` + Next.js App Router:** The canvas must be a Client Component (`'use client'`). Konva runs entirely in the browser. The page file (`app/(admin)/layouts/[id]/page.tsx`) can be a Server Component that fetches layout data from Supabase, then passes it as props to `<EventCanvas />` which is marked `'use client'`.

---

## 12. Open Questions Before Build

1. **SiteGround plan type** — Shared (GrowBig/GoGeek) or Cloud/VPS? Shared: use cPanel Node.js Selector as above. VPS: swap Passenger for PM2 + Nginx reverse proxy.
2. **Git hosting** — GitHub or GitLab? Needed for the deploy workflow (SSH + git pull).
3. **WP Application Password** — needs to be created in WP Admin for the server-side integration.
4. **react-konva compatibility with React 19** — verify on first run; may need a version override.
5. **Supabase region** — choose Singapore (ap-southeast-1) for lowest latency from HCMC.

---

*Related: `Documentation/design/event-layout-designer-spec.md` · `Documentation/research/2026-06-19-event-layout-designer-research.md` · `Documentation/research/2026-06-19-event-layout-designer-tech-stack.md`*
