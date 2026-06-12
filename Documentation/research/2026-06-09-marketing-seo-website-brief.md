# TEMPO House — Marketing, SEO & Website Creative Brief
**Date:** 2026-06-09  
**Prepared by:** Muse (Brand & UX Creative Director, Raging Monk AI) + Claude Code  
**Status:** Pre-launch strategic brief

---

## PART 1 — MARKET CONTEXT & DEMOGRAPHICS

### The Landscape: HCMC District 3, 2025

District 3 is Saigon's most layered neighbourhood — colonial architecture adjacent to design studios, family phở shops next to concept stores. It attracts the city's creative professional class at a density higher than any other district. The area around Pasteur, Võ Văn Tần, and Nguyễn Thị Minh Khai forms an emerging cultural corridor where TEMPO House is positioned to become the anchor venue.

**Market opportunity:**
- Third-wave coffee culture in Vietnam grew ~35% YoY 2022–2025. HCMC now has the highest specialty café density in SEA outside Singapore.
- The craft cocktail market is nascent but accelerating — driven by expat demand and an emerging Vietnamese middle-class appetite for considered drinking experiences.
- There is no venue in D3 that successfully integrates café, bar, gallery, and events under a single coherent cultural identity. This gap is TEMPO's moat.

---

### Primary Audience: The Culturally Anchored Creative

**Profile:**
- Age: 25–42
- Mix: ~50% expat (French, American, Australian, Korean, Japanese), ~50% culturally-engaged urban Vietnamese
- Occupations: architects, art directors, designers, writers, founders, curators, strategists, brand managers, chefs
- Income: upper-middle to high (VND 50M+/month or foreign-income equivalents)
- Frequency: 2–4x/week for café; 1–2x/week for bar; 4–8x/year for events
- Channels: Instagram primary, Google Maps for discovery, personal recommendation decisive

**What drives their decision to visit:**
1. Instagram-worthy but not Instagram-*only* — they're tired of venues built for the grid
2. Consistent quality in both coffee and cocktails (they will notice the difference)
3. Art and programming that gives them a reason to return
4. A room that holds its atmosphere — light, music, crowd energy
5. Staff who know their product without performing knowledge

**What kills the visit before it happens:**
- Generic menu (anything that looks like "craft" as marketing copy)
- Poor Google Maps presence (no hours, no photos, low reviews)
- A social feed that looks like promotion, not curation
- No events calendar to plan around

---

### Secondary Audience: The Cultural Event Ecosystem

- Art collectors and gallery visitors (occasional, high value)
- Corporate and brand event producers (low frequency, very high spend)
- Private dining and celebration hosts (evening/weekend)
- Press, cultural writers, influencers in the Saigon creative scene
- Visiting international creatives (architects, designers, gallerists on residency/travel)

**Key trigger:** Trust that TEMPO can hold a room worthy of their professional name.

---

## PART 2 — SEO & DIGITAL DISCOVERY STRATEGY

### Channel Priority Stack

| Channel | Role | Priority |
|---|---|---|
| **Google Search** | High-intent discovery ("cocktail bar near me", "café D3 HCMC") | 🔴 Critical |
| **Google Maps / GMB** | Local pack — the first thing someone sees | 🔴 Critical |
| **Instagram** | Brand building, event amplification, visual curation | 🔴 Critical |
| **TikTok** | Organic reach, youth demographic crossover, event hype | 🟡 High |
| **Zalo** | Vietnamese local community, event invitations, reservation follow-up | 🟡 High |
| **Newsletter** | Owned channel — highest conversion, event pre-sales | 🟡 High |
| **Press / Editorial** | Long-form credibility, SEO backlinks, cultural authority | 🟢 Medium |
| **Facebook** | Legacy audience, event RSVPs, Vietnamese local 30+ | 🟢 Medium |

---

### SEO Keyword Architecture

#### Tier 1 — Primary Cluster (Highest intent, direct revenue)

| Keyword | Search Intent | Target Page |
|---|---|---|
| specialty coffee District 3 HCMC | Discovery → visit | `/cafe` |
| cocktail bar Pasteur Street Saigon | Discovery → visit | `/bar` |
| cocktail bar District 3 Ho Chi Minh | Discovery → visit | `/bar` |
| best café Ho Chi Minh City 2025 | Discovery → visit | `/` + `/cafe` |
| art gallery café Ho Chi Minh City | Discovery → visit | `/gallery` |
| private event venue District 3 HCMC | High-value intent → enquiry | `/events` |
| event space Saigon hire | High-value intent → enquiry | `/events` |
| restaurant reservation District 3 | Direct booking intent | `/reservations` |

#### Tier 2 — Long-tail Content Cluster (SEO via editorial content)

| Keyword | Target Content |
|---|---|
| specialty coffee HCMC guide | Journal: "The TEMPO Guide to Specialty Coffee in Saigon" |
| craft cocktail bar guide Ho Chi Minh | Journal: "Eight Drinks Worth Your Evening in Saigon" |
| art gallery openings Ho Chi Minh City | Programme page + press releases |
| gallery opening HCMC events | Programme / What's On |
| Vietnamese contemporary art gallery Saigon | Gallery programme page |
| private dining experience Ho Chi Minh City | Events / enquiry page |
| best coffee shop for working Saigon | Journal content / café page |
| expat café Ho Chi Minh City | Café page + review-bait content |
| things to do District 3 Saigon | Homepage + journal |
| couple date night HCMC cocktail bar | Bar page + event listings |

#### Tier 3 — Brand + Navigational

| Keyword | Priority |
|---|---|
| TEMPO House HCMC | Branded search — must own position 1 |
| tempohouse.com.vn | Direct navigational |
| @tempohouse.sgn | Social to web cross-referral |
| TEMPO House reservations | Direct booking intent |

---

### Technical SEO: Next.js Static Export Checklist

- [ ] **`sitemap.xml`** — generate at `/public/sitemap.xml` covering all routes; submit to Google Search Console
- [ ] **`robots.txt`** — allow all at `/public/robots.txt`
- [ ] **Meta titles** — unique per page, 50–60 chars, keyword-leading (e.g. `Specialty Café · TEMPO House, District 3 Ho Chi Minh City`)
- [ ] **Meta descriptions** — unique, 150–160 chars, inviting + keyword-rich
- [ ] **Open Graph + Twitter Card** — every page needs `og:title`, `og:description`, `og:image` (1200×630px), `og:type`
- [ ] **JSON-LD Schema** — `Restaurant` schema on `/cafe` and `/bar`; `EventVenue` on `/events`; `Event` schema on each programme listing; `LocalBusiness` on homepage
- [ ] **Canonical tags** — prevent duplicate content from trailing slash / non-trailing slash variants
- [ ] **`hreflang`** — when Vietnamese language version launches, declare `en-vn` and `vi-vn` alternates
- [ ] **Core Web Vitals** — Image optimisation via `next/image`; font display swap; CSS critical path
- [ ] **Google My Business** — dual category listing: "Coffee Shop" + "Cocktail Bar" + "Event Venue"; 10+ photos at launch; hours updated; booking link to `/reservations`
- [ ] **Structured data for events** — each What's On listing must have `@type: Event` with `startDate`, `endDate`, `location`, `organizer`, `image`

---

## PART 3 — MUSE BRAND AUDIT FINDINGS

### Overall: AMBER — Strong foundations, execution gaps pre-launch

| Dimension | RAG | Priority |
|---|---|---|
| Brand Consistency | 🟡 Amber | Build brand system doc before any external materials |
| Visual Health | 🟢 Green (conditional) | Define usage hierarchy — 3 fonts + 6 colours need rules |
| Voice & Tone | 🟡 Amber | Day-mode and night-mode voice variants needed |
| Competitive Position | 🟢 Green | Genuinely unoccupied territory — dual-mode + gallery |
| Audience Resonance | 🟡 Amber | Hierarchy needed: primary → secondary → occasion |
| Gaps & Regressions | 🔴 Red | No brand system doc, no photo brief, no art programme brief |

### Key Muse Findings

**Positioning Statement (Muse):**
> *TEMPO House is District 3's living room for the culturally awake — a single address where specialty craft, considered curation, and creative community move seamlessly from first light to last call.*

**Tagline Recommendation (Muse):**
The current tagline — *"Coffee in the morning. Connection at night."* — works at a functional level. Muse recommends exploring:
> *"Find your tempo."*
Operates across both modes. Activates the brand name. Owns the concept of pace and self-regulation that resonates with the primary creative professional audience.

**Five Brand Personality Pillars:**
1. **Considered** — deliberate, edited, earns every choice
2. **Atmospheric** — mood is authored, not ambient by accident
3. **Quietly Confident** — no velvet rope, no need for one
4. **Culturally Fluent** — Vietnamese and international at equal height
5. **Dual-Natured** — two acts of the same story, not two brands

**Critical pre-launch gaps (from Muse audit):**
- 🔴 No brand system document (application rules, photo direction)
- 🔴 Photography creative direction absent — can undermine everything
- 🟡 Art programme position undefined (curating? hosting? selling?)
- 🟡 Instagram content strategy undefined — silence pre-launch is a missed window
- 🟡 Amber-on-cream contrast failure — WCAG audit needed
- 🟡 Day/night transition is the core differentiator — not yet a *designed moment*

---

## PART 4 — WEBSITE CREATIVE DIRECTION: EVENT/EXPERIENCE/ART-DRIVEN ARCHITECTURE

### Design Philosophy (Muse)

The website must feel like entering a gallery space online — not browsing a menu or reading a brochure. Every page is a curated moment. Navigation is unhurried. White space is generous. The editorial voice leads; the product follows.

**Three governing design principles:**
1. **The site has a mood** — not neutral, not clean-for-clean's-sake. The day/night palette toggle is not a feature — it is the brand's core concept made interactive.
2. **Content is the design** — art, events, and editorial copy are the visual material. No decorative illustration; no stock photography.
3. **Every page has a purpose** — the hierarchy flows: discover → feel → book. Nothing exists for SEO alone or for completeness.

---

### Recommended Website Architecture

```
/ (Home)
  ├── /venue              — The space, photography-led
  ├── /cafe               — Daytime: coffee, kitchen, ritual
  ├── /bar                — Evening: cocktails, spirits, atmosphere
  ├── /gallery            — Art programme: current + archive
  │     └── /gallery/[exhibition-slug]   — Individual exhibition pages
  ├── /programme          — Full What's On / events calendar
  │     └── /programme/[event-slug]      — Individual event pages
  ├── /journal            — Editorial: stories, interviews, essays
  │     └── /journal/[article-slug]      — Individual articles
  ├── /events             — Private hire + event enquiry
  │     └── /events/enquiry
  ├── /reservations       — Table booking
  ├── /contact
  └── /privacy-policy
```

---

### Page-by-Page Creative Brief

---

#### `/` — Homepage: The Arrival

**Purpose:** Orient, seduce, convert.

**Above the fold:**
- Full-viewport hero. Not a static image — the brand itself is the visual. The bleed type `TEMPO / HOUSE` continues. Below it: the positioning statement. Then a single action: `Reserve →` or `What's On →`
- The day/night toggle is visible and inviting — not hidden in settings. This is part of the onboarding moment.

**Below the fold — content blocks:**
1. **Three Moods** — the art-frame card grid (built). Each frame is a door into `/cafe`, `/bar`, `/gallery`
2. **Current Exhibition** — a single artwork, full-bleed or large, with artist name and one sentence. Links to `/gallery/[current-show]`
3. **What's On** — the event grid (built). Three upcoming events in frame format.
4. **The TEMPO Letter** — newsletter capture. Not "subscribe to our newsletter" — it's *"The TEMPO Letter. Events, openings, and what's worth knowing. First in the room."*
5. **Reserve** — clean CTA section. One line of copy. One button.

**SEO:** Target "specialty café cocktail bar District 3 Ho Chi Minh City"

---

#### `/venue` — The Space

**Purpose:** Justify the decision before someone visits. Build desire.

**Structure:**
- Hero: Full-bleed photography of the space — morning light version and evening version (toggle-reactive if possible)
- Copy: One paragraph on the address and the concept. No bullet points.
- Three panels: Café floor / Bar floor / Gallery wall — each with a single sentence and a CTA
- Hours + address + embedded map

**Photography direction (Muse):**
Film-emulating. People at periphery — shoulders, hands, not faces. Light as subject. Never empty-room shots alone.

**SEO:** Target "venue District 3 HCMC", "event space Pasteur Street Saigon"

---

#### `/cafe` — The Morning

**Purpose:** Convert specialty coffee enthusiasts. Build the morning ritual identity.

**Structure:**
- Hero: Morning — light through louvers, hands around ceramic, steam
- Copy: The ritual. First-person, unhurried. "07:00. Something from Yirgacheffe. The city is still finding itself."
- Coffee programme: Origins, method, the approach — no buzzwords
- Kitchen: Seasonal, considered. A few items, all intentional.
- Hours and "Find your table →" CTA

**New content needed:** Single-origin coffee sourcing story; kitchen philosophy

**SEO:** Target "specialty coffee District 3", "best café Ho Chi Minh City", "filter coffee Saigon"

---

#### `/bar` — The Evening

**Purpose:** Convert cocktail audience. Build the night ritual identity.

**Structure:**
- Hero: Evening — amber light, ice against glass, bottles backlit
- Copy: The transition. "By six, the light has shifted and so has everything else."
- Cocktail programme: Signature list with brief (not verbose) descriptions. Seasonal variations.
- Spirits: The curation principle — what they stock and why
- "Reserve a seat →" CTA — evening reservations separate from lunch

**New content needed:** Cocktail philosophy; signature drink descriptions; spirits curation narrative

**SEO:** Target "cocktail bar D3 HCMC", "craft cocktail Saigon", "bar Pasteur Street Ho Chi Minh"

---

#### `/gallery` — The Programme

**Purpose:** Cultural authority. Make the art programme feel real and curated, not decorative.

**Structure:**
- Current exhibition: Hero-sized, artist name prominent, exhibition dates, curatorial note (100–150 words)
- Past exhibitions: Archive grid — each with a thumbnail, artist, dates
- Artist statement: One pull-quote per show
- Acquisition / enquire link for collectors
- "Open during café and bar hours" — clear access information

**New content needed:** Curatorial position statement; first exhibition brief; artist relationship model

**SEO:** Target "art gallery Ho Chi Minh City", "Vietnamese contemporary art gallery", "gallery opening HCMC"

---

#### `/gallery/[exhibition-slug]` — Exhibition Detail (NEW)

**Purpose:** Deep content per show. SEO long-tail. Press resource.

**Structure:**
- Full exhibition photography (gallery documentation standard)
- Artist bio + statement
- Curatorial note from TEMPO
- Works list (if applicable)
- Related events (opening night, artist talk)
- Share / press enquiry CTA

---

#### `/programme` — What's On (Expanded from stub)

**Purpose:** Drive event attendance. Build habitual return visits.

**Structure:**
- Filter: All / Music / Exhibition / Private Dining / Special
- Event grid: The art-frame card format (built) with real event data
- Calendar view option (toggle)
- "Host your own event →" CTA to `/events/enquiry`

**SEO:** Target "events Ho Chi Minh City", "gallery opening HCMC", "live music Saigon", "things to do D3"

---

#### `/programme/[event-slug]` — Event Detail (NEW)

**Purpose:** Conversion. Get the RSVP or reservation.

**Structure:**
- Event title + date + time
- Full hero image
- Description (atmosphere-led, not promotional)
- Practical details: Capacity, entry, ticket price or reservation requirement
- **Structured data: JSON-LD `Event` schema** — critical for Google Events rich results
- Reserve / RSVP CTA
- Related events

---

#### `/journal` — The Editorial (NEW — Priority)

**Purpose:** SEO long-tail. Brand voice expression. Cultural authority. Newsletter content pipeline.

**Why this page must exist:**
The fastest route to organic search rankings for a new venue is original editorial content. "The best specialty coffee in Saigon" — written with genuine authority — will rank within 90 days if structured correctly. It also feeds the newsletter and builds the brand as a publisher, not just a venue.

**Content pillars:**
1. **Coffee** — Origins, methods, seasonal menus, the baristas behind them
2. **Cocktails** — The bar programme, spirits, seasonal recipes, the team
3. **Art** — Exhibition previews, artist interviews, collector perspectives
4. **Saigon** — The neighbourhood, the city, what's worth knowing, what's worth protecting
5. **The Craft** — Behind-the-scenes: sourcing, training, the invisible work

**Launch content plan (minimum 6 articles at launch):**
1. "The Guide to Specialty Coffee in Ho Chi Minh City" — high search volume
2. "Why District 3" — brand story, neighbourhood love letter
3. "First Exhibition: [Artist Name]" — gallery programme launch
4. "The Night Programme: Building TEMPO's Cocktail List" — bar credibility
5. "A Week at TEMPO" — day-in-the-life, both modes, builds mental model
6. "The TEMPO Letter, Vol. 1" — first newsletter repurposed as web content

**SEO:** Multiple long-tail targets per article. This section will drive the majority of organic search traffic within 6 months.

---

#### `/events` — Private Hire

**Purpose:** High-value conversion. Corporate and private enquiries.

**Structure:**
- Hero: The space dressed for an event — intimate dinner lighting
- The proposition: What TEMPO offers for private events
- Capacity and configuration: Dinner / standing reception / gallery activation
- The team: Who handles events (not a generic "our events team")
- Enquiry form: Concise — date, occasion type, guest count, name, email
- Past events: A tasteful gallery of previous hire occasions (with client permission)

**SEO:** Target "private event venue District 3", "event space hire HCMC", "private dining Saigon"

---

## PART 5 — CONTENT & INSTAGRAM STRATEGY

### The TEMPO Instagram (@tempohouse.sgn)

**Pre-launch content plan (6–8 weeks before opening):**

| Week | Theme | Content |
|---|---|---|
| W-8 | Space reveal | Architectural photography — the bones of the space. No text. Pure mood. |
| W-7 | The morning | Coffee equipment, light through windows, slow morning energy |
| W-6 | The night | Bar build in low light. Bottles. Ice. Darkness. |
| W-5 | The art | First artist announcement. One work. One sentence. |
| W-4 | The team | Behind the counter. The bar team at work. Not portraits — motion. |
| W-3 | The programme | First event announced. Event format card in brand design. |
| W-2 | Countdown | "Open in 14 days." Day/night countdown graphics. |
| W-1 | Final week | Reservation link live. Newsletter link. "We're ready." |

**Content principles (Muse):**
- Never: flat lays, promo graphics, stock photography, countdown sticker templates
- Always: film grain aesthetic, light as subject, people at periphery not centre, minimal copy
- Caption voice: one or two lines maximum. Atmosphere, not announcement.

---

## PART 6 — LAUNCH PRIORITIES (ORDERED BY IMPACT)

### Immediate (pre-launch)
1. **Google My Business** — create and verify listing; dual category (coffee shop + cocktail bar); add hours, photos, booking link
2. **Holding page upgrade** — add email capture with *"Be first in the room"* value exchange; add Instagram link
3. **Brand photography shoot** — brief attached; executed before any content goes live
4. **Instagram pre-launch content** — 8-week content calendar above

### At launch
1. **Sitemap + robots.txt** — submit to Google Search Console day one
2. **JSON-LD schema** — Restaurant + LocalBusiness on homepage; Event on all programme pages
3. **Meta tags audit** — every page unique title + description + OG image
4. **Journal** — minimum 6 articles live at launch
5. **Gallery** — current exhibition page live with full artist content

### Post-launch (0–90 days)
1. **Press outreach** — Saigoneer, Oi Vietnam, Vietnam Heritage, Time Out Saigon, Condé Nast Traveller SEA
2. **TikTok activation** — "A day at TEMPO" content format; behind-the-bar content
3. **Event programme activation** — first three events in the calendar drive return visits and content
4. **Newsletter Vol. 1** — sent within 2 weeks of opening to all pre-launch subscribers
5. **Google review strategy** — staff briefed to prompt happy regulars; respond to every review

---

## APPENDIX — VOICE EXAMPLES (MUSE)

### What TEMPO writes:

> *"07:00. Something from Yirgacheffe. The city is still finding itself."*

> *"By six, the light has shifted and so has everything else."*

> *"Thursday. Nine works. One evening. Reserve."*

> *"The space holds eighty. It has held weddings, launches, breakups, and one very memorable Thursday."*

> *"The art is not decoration. The events are not promotions. The community is not an audience."*

### What TEMPO never writes:

> *"We are excited to announce our new specialty coffee programme featuring single-origin beans sourced from award-winning farms..."*

> *"Join us for a special evening of cocktails and live art installations!"*

> *"The perfect venue for your next event — contact us today for availability!"*

---

*Filed: `Documentation/research/2026-06-09-marketing-seo-website-brief.md`*  
*Next: Execute website expansion — journal, programme, gallery exhibition detail pages.*
