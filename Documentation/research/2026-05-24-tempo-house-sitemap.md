# TEMPO House — Website Sitemap & Feature Brief
**Date:** 24 May 2026  
**Prepared by:** Raging Monk AI — Claude (Architect)  
**Status:** Planning Document — Pre-Development  
**Input sources:** Muse Brand Analysis, Foundry Report, Tempo Overview flowchart, founder brief, Debonaïr Orlando analysis, The Ivy Asia analysis

---

## Architecture Decisions

| Decision | Choice | Rationale |
|---|---|---|
| CMS | WordPress Headless | Familiar editor UI, strong ecosystem for events/menus/jobs, ACF for structured data |
| Front-end | Next.js (App Router) | Existing codebase, SSR for dynamic CMS content, SEO |
| Deploy | SiteGround SFTP (current) → shift to Node server or Vercel | Static export won't work with dynamic CMS; need SSR or ISR |
| Reservations | Eat App / Resy (dining) + custom multi-step form (events) | Automation for tables, bespoke for high-value event enquiries |
| Language | EN/VI toggle (site-wide) | CMS manages both language fields per content item |
| Analytics | GA4 + Meta Pixel (live) | Already deployed |
| Email | Klaviyo | Already integrated |

> **Critical note on deploy:** Moving from `output: "export"` to server-side rendering (SSR or ISR) is required the moment WordPress content is pulled in. Recommend deploying Next.js app to Vercel or a Node-capable SiteGround plan rather than pure static SFTP. This is a prerequisite for Phase 1 full launch.

---

## Sitemap — Three Phases

### PHASE 1 — Core Launch (Build Now)
*Priority: get a real site live that replaces the holding page. Photography gap handled with hero video reel, atmospheric stills, and designed placeholder states.*

---

#### `/` — Home
**Purpose:** First impression, brand statement, activation showcase, day/night orientation.

**Key features:**
- **Rotating hero** — current laneway activation / seasonal theme. CMS-managed image or video. Changes per activation. This is the "alive" signal — the site looks different every time you visit.
- **Day/Night mode toggle** — first-class UI feature, not a setting. Toggles the full palette (cream → ink, terracotta → amber). Persists via localStorage. Ideally transitions based on HCMC local time automatically, overrideable by user.
- **Three-layer venue intro** — Cafe · Bar · Gallery as one continuous scroll narrative, not separate tabs. One building, three moods.
- **Current What's On teaser** — next 2–3 public events pulled from WP. Links to `/whats-on`.
- **Dining reservation CTA** — quick embed or link to Eat App / Resy.
- **Events enquiry CTA** — "Host something here" → `/events/enquiry`.
- **Email signup** — Klaviyo (existing), reframed as "Stay in the loop on events, exhibitions, and what's next."
- **Footer** — socials, Google Maps, Privacy Policy, language toggle.

**Animations:** Slow cinematic parallax on hero. Text fades timed to scroll. Day/night palette transition uses CSS custom property animation (not a hard swap). Referenced: The Ivy Asia's fluid scroll, Debonaïr's text choreography.

---

#### `/venue` — The Venue (Our Story + Spaces)
**Purpose:** Tell the founders' story and present the three physical floors as distinct but unified experiences. The brand's most important editorial page.

**Key features:**
- **Founders' story** — Aussie-Vietnamese, Melbourne meets Saigon, why TEMPO exists. Written per Muse brand voice (specific, warm, never "passionate"). Bilingual.
- **Embassy Education Group connection** — briefly noted as the institutional partner enabling the arts programme. Positions TEMPO's gallery credibility.
- **Floor-by-floor breakdown:**
  - **Ground Floor** — Cafe (day), Bar/Speakeasy (night). Hours, atmosphere, day-to-night transition described editorially.
  - **Level 1 / Level 2 — TEMPO Gallery** — art gallery, creative events space. Named sub-brand. Exhibition history, capacity, enquiry link.
  - **Level 3 — Creator Floor** — podcast studio hire, content creator studio hire. Separate hire page linked.
- **Laneway entry** — short section on the laneway as part of the TEMPO experience. "The activation before the activation."
- **Photography placeholder:** hero video loop of venue interior (shoot priority: morning light through windows, amber lamp detail shots, evening bar setup, Level 2 gallery walls).

---

#### `/whats-on` — What's On
**Purpose:** Public events calendar. Gallery-style presentation — "experience piece" UX where each event card feels like an artwork being presented, not a Eventbrite listing.

**Key features:**
- **Gallery grid layout** — full-bleed event cards, large imagery. Filter by: All / Cafe & Bar / Gallery & Exhibitions / Workshops / DJ & Live Music / Activations.
- **Event detail page** `/whats-on/[slug]` — full event description, date/time, tickets (if applicable), RSVP or reservation link. Artist/performer bio if relevant.
- **CMS-managed** — WP custom post type: Events. Fields: title, date, type, image, description (EN + VI), ticket link or reservation CTA, featured flag.
- **Upcoming vs. past toggle** — past events stay live as an archive. Builds credibility and brand story over time.
- **Recurring formats listed** — e.g. "DJ sets every Friday from 8pm" as a standing event, not requiring individual posts.

**UX reference:** Like an art gallery "programme" page — The Ivy Asia's events section, but darker and more editorial.

---

#### `/cafe` — The Cafe
**Purpose:** Own "specialty cafe HCMC / Saigon" search intent. Speak directly to the morning crowd — creative professionals, freelancers, local KOLs seeking a tucked-away work base. Full cream/terracotta day palette.

**SEO targets:** `specialty cafe Ho Chi Minh City` · `specialty coffee Saigon` · `Melbourne cafe HCMC` · `best cafe District 1 Saigon` · `cafe for working Saigon` · `quán cà phê đặc sản Sài Gòn`

**Key features:**
- **Hero** — full-bleed morning shot. Available light through windows. Espresso close-up. Day palette (cream, terracotta, sage).
- **Editorial intro** — Melbourne-trained, Saigon-made. Coffee sourcing philosophy. Written in TEMPO brand voice (specific, unhurried).
- **Menu (CMS-managed):**
  - Coffee & espresso programme
  - Alternative beverages (matcha, teas, fresh)
  - Morning food (breakfast, early tapas, pastries)
  - Afternoon bites
- **"The space by day"** — brief on the ground floor as a day workspace: natural light, low music, table availability, no time pressure.
- **Hours** — clearly stated, with note on walk-ins welcome during the day.
- **Reservation CTA** — Eat App/Resy for groups or guaranteed seating; walk-ins welcome.
- **Corporate breakfast / business meetings note** — links to `/corporate-events` for group bookings.
- **Neighbourhood note** — the laneway setting; nearby corporates and businesses.
- **Schema markup:** `CafeOrCoffeeShop`, `hasMenu`, `openingHoursSpecification`

---

#### `/bar` — The Bar
**Purpose:** Own "cocktail bar Saigon" and "speakeasy HCMC" search intent. Speak to the evening crowd — supper club, intimate nightcap, DJ nights, Melbourne bar culture. Full ink/amber night palette.

**SEO targets:** `cocktail bar Saigon` · `speakeasy Ho Chi Minh City` · `craft cocktails HCMC` · `intimate bar District 1 Saigon` · `bar with live music Saigon` · `supper club Saigon` · `bar cocktail TP.HCM`

**Key features:**
- **Hero** — full-bleed evening shot. Amber lamp spill, glassware, low tables, intimate seating. Night palette (ink, amber, charcoal, cream as negative space).
- **Editorial intro** — "The lights don't change dramatically. The room just shifts." The speakeasy-to-supper-club arc described in brand voice. All-day bar, mood deepens from late afternoon.
- **Menu (CMS-managed):**
  - Signature cocktails (hero section)
  - Spirits & wine list
  - Evening small plates / tapas (pan-Latin/Nikkei direction)
  - Non-alcoholic pairings
- **"The space by night"** — the transformation from cafe to bar. Music programming note (DJ sets, live acoustics schedule).
- **What's On integration** — next bar-specific events pulled from WP (DJ nights, acoustic sessions, tasting events).
- **Hours** — evening opening, last entry policy, note on late-night.
- **Reservation CTA** — recommended for evenings, Eat App/Resy.
- **Private hire note** — small bar buyouts for intimate groups → `/private-dining`.
- **Schema markup:** `BarOrPub`, `hasMenu`, `openingHoursSpecification`

---

#### `/gallery` — TEMPO Gallery (Level 2)
**Purpose:** Position TEMPO as a genuine cultural institution, not just a venue with white walls. This page earns the Embassy Education / PR Arts credibility.

**Key features:**
- **Current exhibition** — full-bleed hero with artist name, exhibition title, dates. Feels like a museum landing page.
- **Exhibition detail** `/gallery/[slug]` — artist bio, work descriptions, exhibition statement, imagery, opening night details.
- **Past exhibitions archive** — builds permanent record of TEMPO's cultural programme. Signals seriousness to artists and arts institutions.
- **Gallery hire CTA** — "Show your work here / Host your event here" → `/events/enquiry` with gallery pre-selected.
- **CMS-managed** — WP: Exhibitions post type. Fields: artist, title, medium, dates, images, statement (EN + VI), private event flag.
- **Embassy Education / PR Arts agency note** — subtle institutional credibility line. "TEMPO Gallery is curated in collaboration with [partner]."

---

#### `/events` — Events & Functions Hub
**Purpose:** Top-level events overview and navigation hub. Establishes TEMPO as a premium events venue. Links out to all SEO-targeted sub-pages. Sticky enquiry CTA throughout.

**SEO targets:** `event venue Saigon` · `event space Ho Chi Minh City` · `private venue HCMC` · `venue hire Saigon` · `không gian tổ chức sự kiện TP.HCM`

**Key features:**
- Hero: "Host something worth remembering" / "Tổ Chức Điều Gì Đó Đáng Nhớ"
- Visual grid of event types — each card links to its dedicated SEO page (see below)
- Spaces overview (Ground floor capacity / Level 2 Gallery capacity / full venue hire)
- Social proof section: past client logos, press mentions, testimonials
- Sticky "Enquire Now" CTA on scroll
- FAQ section (captures long-tail search, feeds featured snippets):
  - "How many people does TEMPO House hold?"
  - "Do you offer F&B packages for events?"
  - "Can I hire the art gallery separately?"
  - "Do you host weddings?"
- Schema: `EventVenue`, `AmenityFeature`

---

#### `/corporate-events` — Corporate Events *(SEO priority page)*
**Purpose:** Capture high-value B2B event leads. This is TEMPO's highest-margin acquisition channel.

**SEO targets:** `corporate event venue Saigon` · `corporate venue Ho Chi Minh City` · `meeting venue HCMC` · `corporate dinner venue Saigon` · `business event space District 1` · `conference venue Saigon` · `team dinner venue HCMC`

**Key features:**
- Hero: editorial shot of Level 2 configured for a corporate dinner
- Services: board dinners, company celebrations, brand activation space, corporate breakfast/lunch, annual functions, partner meetings
- Capacity breakdown by floor + configuration (boardroom / banquet / cocktail)
- F&B packages overview (beverage-only / canapes / full tapas menu)
- AV & production partners available
- "Why TEMPO?" — the non-corporate feel in a polished setting. Not a hotel ballroom.
- Embassy Education Group partnership as credibility signal
- Enquiry form or prominent link to `/events/enquiry`
- FAQ: "What's the minimum spend?", "Can I brand the space?", "Do you provide AV?"
- Schema: `EventVenue`, local business signals
- Internal links: `/floor-plans`, `/gallery`, `/events/enquiry`

---

#### `/private-dining` — Private Dining & Bar Hire *(SEO priority page)*
**Purpose:** Capture intimate private dining and exclusive bar hire leads. Speaks to the Mayfair supper club positioning.

**SEO targets:** `private dining Saigon` · `private dining Ho Chi Minh City` · `exclusive dining experience HCMC` · `private bar hire Saigon` · `intimate dinner venue Saigon` · `supper club HCMC` · `ăn tối riêng tư TP.HCM`

**Key features:**
- Hero: evening bar, amber light, intimate table setting — dark and editorial
- Editorial intro: "Some evenings are worth a room of your own." The speakeasy-as-private-venue concept.
- Ground floor bar buyout option (small groups, evening)
- Level 2 gallery configured as private dining room (medium groups)
- Full venue hire (combined floors)
- Curated menus: cocktail pairing dinner, tapas spread, bespoke menus for special occasions
- Guest experience: dedicated service, custom playlist, florals on request
- Occasions served: anniversary, business entertainment, birthday celebration, proposal
- Enquiry CTA + minimum spend note
- FAQ: "What is the minimum for a buyout?", "Can you create a bespoke menu?"
- Internal links: `/bar`, `/gallery`, `/events/enquiry`

---

#### `/weddings` — Weddings & Celebrations *(SEO priority page)*
**Purpose:** Capture wedding and major celebration leads. HCMC wedding venue search volume is significant.

**SEO targets:** `wedding venue Ho Chi Minh City` · `wedding venue Saigon` · `intimate wedding HCMC` · `wedding reception Saigon` · `engagement party venue HCMC` · `tiệc cưới TP.HCM` · `địa điểm đám cưới Sài Gòn`

**Key features:**
- Hero: styled intimate celebration imagery (when available) — florals, candlelight, gallery setting
- Positioning: intimate weddings and celebrations for guests who want craft over convention. Not a banquet hall.
- Spaces: Level 2 gallery (ceremony/reception for 40–80 guests), full venue (larger celebrations), ground floor bar (after-party)
- Styled packages: the laneway florals/activation as the arrival moment
- F&B: bespoke cocktail menu, champagne on arrival, canapes and tapas service
- Pre/post-event options: rehearsal dinner, morning-after brunch in the cafe
- Baby showers, engagement parties, bridal celebrations — listed as related occasions
- Enquiry CTA with date + guest count fields prominent
- Testimonials / social proof (when available)
- FAQ: "Do you have a minimum guest count?", "Can we bring our own cake?", "Do you have styling partners?"
- Internal links: `/floor-plans`, `/private-dining`, `/events/enquiry`

---

#### `/product-launches` — Brand Activations & Product Launches *(SEO priority page)*
**Purpose:** Capture brand marketing budgets. TEMPO's gallery + laneway + content floor makes it uniquely suited for brand storytelling events.

**SEO targets:** `product launch venue Saigon` · `brand activation space HCMC` · `launch event venue Ho Chi Minh City` · `brand event space Saigon` · `creative event space HCMC` · `influencer event venue Saigon`

**Key features:**
- Hero: gallery configured for a launch — installation aesthetic, moody lighting
- The TEMPO proposition for brands: gallery credibility + creator floor for content capture + bar hospitality + laneway activation entry moment. End-to-end brand experience in one address.
- Level 2 gallery as the launch canvas — white walls, flexible layout, genuine art-world aesthetic (not a generic event room)
- Creator Floor (Level 3) — content capture studio available same day: podcast interviews, social content shoots, behind-the-scenes
- Laneway activation — themed florals and installation as the arrival moment (high social media moment)
- Partnership packages: invitation to align with a TEMPO Gallery exhibition (cultural credibility for the brand)
- Past launch capabilities: 80 standing / 50 seated in gallery, 150+ with ground floor combined
- Press & PR agency note — links to `/events/enquiry` (Partnerships path)
- FAQ: "Can we shoot content in the venue?", "Do you work with styling and production partners?", "Can we theme the laneway?"
- Internal links: `/creator-floor`, `/gallery`, `/floor-plans`, `/events/enquiry`

---

#### `/art-events` — Art Events & Gallery Hire *(SEO priority page)*
**Purpose:** Attract artists, gallerists, arts institutions, and cultural organisations. Reinforces TEMPO's serious cultural positioning.

**SEO targets:** `art gallery hire Saigon` · `gallery space Ho Chi Minh City` · `art exhibition venue HCMC` · `art event space Saigon` · `gallery rental Ho Chi Minh City` · `not-for-profit art space HCMC`

**Key features:**
- Hero: gallery wall with artwork, opening night atmosphere
- TEMPO Gallery positioning — curated programme, Embassy Education Group / PR Arts collaboration, genuine exhibition practice
- What the space offers: hanging walls, track lighting, flexible layout, private view service
- Event types: solo exhibitions, group shows, artist talks, gallery openings, auction previews, art fairs (small), creative industry awards
- F&B during openings: wine service from bar, canapes, bespoke cocktail naming (artist tie-in)
- How to apply to show: brief on gallery submission / curation process
- How to hire for a one-off event: direct to enquiry
- Artist/curator credibility section: current and past exhibition artists (when available)
- Internal links: `/gallery`, `/whats-on`, `/events/enquiry`

---

#### `/workshops` — Workshops, Talks & Creative Events *(SEO priority page)*
**Purpose:** Capture the growing HCMC market for paid workshops, creative industry events, and learning experiences. Also serves the Embassy Education Group pipeline.

**SEO targets:** `workshop venue Saigon` · `event space for workshops HCMC` · `creative workshop Ho Chi Minh City` · `talk event venue Saigon` · `learning event space HCMC` · `workshop space District 1 Saigon`

**Key features:**
- Hero: workshop in progress — people around tables, intimate and focused
- TEMPO as workshop venue: Level 2 gallery in classroom/workshop configuration (theatre, U-shape, round tables), AV-equipped, ground floor F&B included
- Event types: brand workshops, creative industry panels, keynote talks, photo/film masterclasses, food/drink workshops, wellness and lifestyle (selective), Embassy Education programmes
- Creator Floor (Level 3) tie-in: workshop record-to-content flow
- Half-day / full-day hire options with F&B packages
- Host your own: pathway for external hosts to apply to run events at TEMPO
- Partnership with Embassy Education Group named — institutional credibility
- Enquiry CTA
- Internal links: `/creator-floor`, `/events/enquiry`, `/corporate-events`

---

#### `/birthdays` — Birthday & Private Celebrations *(SEO acquisition page)*
**Purpose:** Capture birthday and social celebration searches — high volume, high conversion intent.

**SEO targets:** `birthday venue Saigon` · `birthday party venue HCMC` · `birthday dinner Saigon` · `birthday cocktail bar HCMC` · `private celebration venue Saigon` · `tiệc sinh nhật TP.HCM`

**Key features:**
- Warmer, more personal tone than corporate pages — still TEMPO brand voice, but celebratory
- Ground floor bar for birthday cocktail gatherings (up to ~30 standing)
- Level 2 gallery for seated birthday dinners (up to 50)
- Birthday packages: welcome cocktail, bespoke cocktail naming, canapes, cake service (BYO or through TEMPO)
- Themed florals via laneway activation (seasonal theming can be requested)
- "No need to go loud to celebrate well." — the anti-club positioning for the HCMC birthday market
- Easy enquiry: date, number of guests, budget range
- Baby showers and intimate bridal events referenced as related
- Internal links: `/private-dining`, `/events/enquiry`

---

#### `/events/enquiry` — Master Event Enquiry Form
**Purpose:** The conversion endpoint for all event pages. Every event sub-page links here. Must be fast, frictionless, and beautiful.

**Form structure (multi-step):**
1. **Event type** — dropdown pre-filled if arriving from a specific event page
2. **Date + headcount + space preference** (Ground / Level 2 / Both / Creator Floor / Full venue)
3. **F&B requirements** (drinks only / canapes / full tapas / bespoke / unsure)
4. **Budget range** (optional — `<50M VND / 50–150M / 150M+ / Let's discuss`)
5. **Contact details** — name, company (optional), phone, email
6. **How did you hear about TEMPO?** — dropdown (Google / Instagram / Facebook / Word of mouth / Press / Other)
7. **Anything else** — open text, optional

**On submit:**
- Klaviyo lead capture (tagged by event type)
- WordPress enquiry log (CMS record)
- Email notification to events team at info@tempohouse.com.vn
- Confirmation message / email auto-reply via Klaviyo

**Design:** full-page experience, not embedded widget. Night palette (ink background). One question per step. Progress indicator. Should feel like a private members' application, not a Google Form.

---

#### `/reservations` — Reservations
**Purpose:** Frictionless booking for dining and bar seats.

**Key features:**
- **Eat App or Resy embed** — for table reservations (lunch, dinner, bar seats)
- **Walk-in note** — "We welcome walk-ins during the day. Evening reservations recommended."
- **Large group / event note** — groups of 8+ directed to events enquiry
- **Hours and policies** — clearly stated
- Bilingual

---

#### `/contact` — Contact
**Purpose:** General enquiries, press, partnerships.

**Sections:**
- General enquiries → info@tempohouse.com.vn
- Events enquiries → link to `/events/enquiry`
- Press & partnerships → dedicated email or form
- Google Maps embed
- Address (when confirmed)
- Social links
- Hours

---

#### `/privacy-policy` — Privacy Policy ✅ (LIVE)

---

### PHASE 2 — Depth & Conversion (Build within 60 days of Phase 1)

---

#### `/floor-plans` — Floor Plans & Spaces
**Purpose:** Show event clients exactly what they're working with. Essential for venue hire decision-making.

**Key features:**
- **Illustrated or architectural floor plans** — Ground Floor, Level 2 Gallery, Level 3 Creator Floor
- **Each floor:** dimensions, capacity (standing/seated/theatre), AV specs, natural light notes, adjacency to F&B service
- **"Configure your event"** interactive element — select floor(s) + layout style → see capacity
- Downloadable PDF version for event planners
- CTA: Enquire about this space → `/events/enquiry`

---

#### `/creator-floor` — Creator Floor (Level 3)
**Purpose:** Hire page for podcast studio and content creator studio. Separate revenue stream, different audience (creators, brands, media).

**Key features:**
- Studio specs — podcast setup, lighting, camera equipment, green screen (if applicable), acoustic treatment
- Half-day / full-day hire rates
- Past productions showcase (when available)
- Package options — studio only, studio + F&B from Level 2/Ground, studio + event space combo
- Booking enquiry form (similar to events form, simpler)
- Cross-promotion: "Record your podcast. Host the launch downstairs."

---

#### `/menus` — Full Menus
**Purpose:** Standalone menus page for direct access and SEO ("specialty coffee HCMC", "cocktail bar Saigon menu").

**Key features:**
- Day menu (morning/afternoon)
- Evening bar menu
- Upcoming food programme preview (tapas, pan-Latin/Nikkei direction)
- CMS-managed — update without dev involvement
- Bilingual menu descriptions (key items in both EN/VI)
- Downloadable PDF option

---

#### `/jobs` — Jobs Board
**Purpose:** Attract quality hospitality talent aligned with TEMPO's culture.

**Key features:**
- Active job listings (CMS-managed WP post type)
- Each listing: role title, type (full-time/part-time/casual), department (cafe / bar / events / gallery / creative), description, how to apply
- No listing = "No current openings — but we're always interested in exceptional people. Send your CV."
- Application: email or form
- Culture note — what it's like to work at TEMPO (brief, brand-voiced, no corporate HR language)

---

#### `/about` — About (expanded)
*May fold into `/venue` or exist as a dedicated page depending on content volume.*

**Key features:**
- Extended founders' story
- TEMPO Life entity brief (Embassy Education Group relationship, arts ministry connection, Vietnam arts industry context)
- Brand values (Unhurried / Exacting / Dual-natured / Rooted / Quietly generous — from Muse analysis)
- Community philosophy
- Press mentions (when available)

---

### PHASE 3 — Future Expansion (6–12 months)

---

#### `/catering` — TEMPO Catering Services
*When the catering entity is formalised as a separate service under TEMPO Life.*

- External catering for corporate, weddings, special events, sister companies
- In-house venue catering packages
- Central kitchen capability overview (when operational)
- Enquiry form

---

#### `/journal` — Journal / Editorial
*Brand voice content channel. Not a blog — an editorial programme.*

- Artist profiles and studio visits
- Behind the bar / behind the espresso machine
- "A morning at TEMPO" / "A night at TEMPO" editorial features
- Event recaps
- Food philosophy essays
- Photographer spotlight features

---

#### `/members` — TEMPO Members (if membership programme launches)
*Per Foundry recommendation: "Tempo Membership" for creative community.*

- What membership includes (early event access, reserved sections, curated experiences)
- Application / waitlist form
- Members-only event previews

---

## Navigation Structure

### Primary Navigation (Desktop)
```
TEMPO House    [Day/Night toggle]    [EN | VI]
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Venue    Cafe    Bar    Gallery    What's On    Events ▾    Reserve
                                               └ Corporate Events
                                               └ Private Dining
                                               └ Weddings
                                               └ Product Launches
                                               └ Art Events
                                               └ Workshops
                                               └ Birthdays
                                               └ Enquire →
```

### Primary Navigation (Mobile)
```
[Hamburger]    TEMPO House    [Reserve — CTA button]
```

Mobile menu full-screen overlay with same links.

### Footer
```
Col 1: Navigation               Col 2: Visit                Col 3: Connect
  The Venue                       Address (TBC)               Instagram
  Cafe & Bar                      Hours                       Facebook
  Gallery                         Google Maps →               TikTok
  What's On                       info@tempohouse.com.vn      Klaviyo signup
  Events & Functions            
  Reservations                  Col 4: Legal
  Creator Floor                   Privacy Policy
  Jobs                            © 2026 TEMPO House
```

---

## Page Priority Matrix

| Page | URL | Phase | Revenue Impact | SEO Value | Priority |
|---|---|---|---|---|---|
| Home (redesigned) | `/` | 1 | High | High | 🔴 P0 |
| Corporate Events | `/corporate-events` | 1 | Very High | Very High | 🔴 P0 |
| Private Dining | `/private-dining-ho-chi-minh-city` | 1 | Very High | Very High | 🔴 P0 |
| Events Hub | `/event-venue-hcmc` | 1 | High | Very High | 🔴 P0 |
| Event Enquiry Form | `/events/enquiry` | 1 | Very High | Low | 🔴 P0 |
| Reservations | `/reservations` | 1 | High | Medium | 🔴 P0 |
| What's On | `/whats-on` | 1 | Medium | High | 🔴 P0 |
| Cafe | `/specialty-cafe-saigon` | 1 | Medium | Very High | 🟠 P1 |
| Bar | `/cocktail-bar-saigon` | 1 | High | Very High | 🟠 P1 |
| Gallery | `/gallery` | 1 | High | High | 🟠 P1 |
| The Venue | `/venue` | 1 | Medium | Medium | 🟠 P1 |
| Weddings | `/wedding-venue-ho-chi-minh-city` | 1 | High | Very High | 🟠 P1 |
| Product Launches | `/product-launches-venue-saigon` | 1 | High | High | 🟠 P1 |
| Art Events | `/art-gallery-hire-saigon` | 1 | Medium | High | 🟠 P1 |
| Workshops | `/workshop-space-saigon` | 1 | Medium | High | 🟠 P1 |
| Birthdays | `/birthday-venue-saigon` | 1 | Medium | Very High | 🟠 P1 |
| Contact | `/contact` | 1 | Low | Low | 🟠 P1 |
| Floor Plans | `/floor-plans` | 2 | High | Medium | 🟡 P2 |
| Creator Floor | `/creator-floor` | 2 | Medium | Medium | 🟡 P2 |
| Menus (full) | `/menus` | 2 | Medium | High | 🟡 P2 |
| Jobs | `/jobs` | 2 | Low | Low | 🟡 P2 |
| Catering | `/catering` | 3 | High (future) | Medium | 🟢 P3 |
| Journal | `/journal` | 3 | Medium | Very High | 🟢 P3 |
| Members | `/members` | 3 | High (future) | Low | 🟢 P3 |

---

## WordPress CMS Content Types Required

| Post Type | Fields | Phase |
|---|---|---|
| `events` | Title, date, type, image(s), description EN+VI, ticket/RSVP link, featured flag | 1 |
| `exhibitions` | Artist, title, dates, images, statement EN+VI, private/public | 1 |
| `menus` | Category (day/night), items with description EN+VI, price, dietary flags | 2 |
| `jobs` | Title, department, type, description, apply link/email, active flag | 2 |
| `spaces` | Name, floor, capacity, dimensions, images, enquiry CTA | 2 |
| `catering` | Service type, description, gallery, enquiry link | 3 |
| `journal` | Title, author, category, body (Gutenberg), image, date | 3 |

---

## Design & Animation Direction

*Based on full scrape and analysis of Debonaïr Orlando (debonairorlando.com) and The Ivy Asia (theivyasia.com), conducted 24 May 2026.*

---

### The Core Insight from Both Sites

Both reference sites optimise for **throughput** — conversion, reservation volume, corporate events pipeline. TEMPO House needs to optimise for **desire** — making visitors want to exist in the space before they've booked anything. The gap between these two modes is TEMPO's design opportunity. The Ivy Asia's private dining page works because it sells the *feeling* first and administers the booking second. Debonaïr's private events page administers first and never sells at all. TEMPO must sell the idea of a private evening before showing a single form field.

---

### What to Borrow from Debonaïr Orlando

**1. Parallax depth-layer hero**
Debonaïr builds its hero from 8+ named PNG layers (arch, flanking lamps, branches, foreground bush, background scene) that move at different scroll speeds — creating a theatre-set depth effect no single photograph can replicate. For TEMPO: the laneway arch, a trailing vine or floral element, a warm lamp source, and the venue interior as background — each on its own layer, each parallaxing independently. The arch framing device is psychologically perfect: the visitor looks *through* an entrance into the scene. This is TEMPO's laneway activation moment, digitised.

**2. Persistent animated brand motif**
Debonaïr uses 8 butterfly PNGs per section (too many — decorative noise). The principle is correct; the execution is excessive. TEMPO needs exactly **one recurring motif** — a hand-drawn ink stroke, a paper crane, an abstracted coffee-ring or wine-glass silhouette from the gallery's current artist. It drifts subtly through sections. Changes monthly with the gallery exhibition. Never crowds a composition.

**3. Conversion-first hero placement**
"RESERVE NOW" appears in the very first viewport. Don't make visitors scroll to find the booking action — keep it visible, but secondary to the atmosphere (not structurally dominant as it is at The Ivy Asia).

**4. Entrance photography for private events**
Using the literal doorway/entrance of the venue as the private events hero is psychologically apt. It signals "you're crossing a threshold into something exclusive." For TEMPO: the laneway entrance, evening, with activation florals lit by warm amber.

---

### What to Borrow from The Ivy Asia

**1. The "near me" URL strategy**
The Ivy Asia's URLs — `/private-dining-near-me/`, `/asian-food-near-me/` — literally embed the highest-volume local search queries into the slug. This is deliberate technical SEO at scale. For TEMPO, apply this to the highest-value event pages:
- `/private-dining-ho-chi-minh-city/` (replaces `/private-dining`)
- `/cocktail-bar-saigon/` (replaces `/bar`)
- `/event-venue-hcmc/` (replaces `/events`)
- `/specialty-cafe-saigon/` (replaces `/cafe`)
- `/wedding-venue-ho-chi-minh-city/` (replaces `/weddings`)

**2. Named private dining spaces**
The Ivy Asia names its bookable spaces — "The Sakura Room", "The Edo Room" — giving each an identity, a mystique, and a booking anchor. TEMPO House spaces should be named:
- Ground floor bar buyout → **"The Ground"** or **"The Bar"**
- Level 2 gallery configured for dining → **"The Atelier"**
- Level 2 full gallery → **"The Gallery"**
- Full venue hire → **"The House"**
Named spaces command perceived premium and give event planners a specific thing to request.

**3. Event naming with cultural and seasonal identity**
The Ivy Asia names events "Sakura Season", "Year of The Fire Horse", "Royal Dragons Experience" — not "Valentine's Day Dinner" or "Spring Menu." For TEMPO in HCMC, the material is extraordinary:
- Tết, Mid-Autumn Festival, Monsoon Season
- Gallery exhibition opening nights named after the work or artist
- "The Amber Hour" for late-night DJ sessions
- "The Laneway Table" for outdoor activation dinners
- "First Light" for early-morning exclusive café events
Names that create mystique and cultural resonance rather than generic occasion labels.

**4. Texture as a breathing section**
Between dense content zones, The Ivy Asia uses a parchment/lava texture full-width as a visual rest — it transitions the mood without being a layout element. For TEMPO: aged rice paper, linen, or a zoomed-in detail of the bar surface or gallery wall used full-bleed as a palate cleanser between sections.

**5. Branded mobile menu illustration**
The Ivy Asia fills the mobile hamburger overlay with a branded dragon illustration — turning a utility moment into a brand experience. For TEMPO: the mobile menu overlay should feature the current gallery exhibition artwork (rotated monthly from the CMS). Every time the visitor opens the menu, they see a piece of TEMPO's art programme. The menu becomes a micro-gallery.

**6. Atmospheric testimonials over star ratings**
The Ivy Asia uses 2 curated atmospheric quotes: *"felt easy, elegant, and special from the moment we arrived"* — not a TripAdvisor score. For TEMPO, collect 3 quotes: one for café, one for bar/evening, one for events. Each should describe the *feeling*, not the food. Source from genuine guests, not press.

**7. Filter-driven private dining room finder**
The Ivy Asia's private dining page uses: Occasion → Session (Lunch/Dinner) → Guest Count → "Find a Room." For TEMPO's events enquiry, a filter flow could replace the static form on the `/events` hub: "What are you celebrating?" → "How many guests?" → "What time of day?" → recommended space + CTA to enquiry. Sells the experience before the administration.

**8. Regular events separated from dated events**
The Ivy Asia distinguishes "always available" formats (Set Menus, Live Entertainment Fridays) from specific date-bound events. On TEMPO's `/whats-on`: separate recurring formats (DJ sets every Friday, coffee tastings first Saturday of each month, open gallery Sundays) from one-off ticketed events.

---

### What to Avoid (from both sites)

| Avoid | Why |
|---|---|
| Eventbrite for events hosting | Zero SEO, broken brand journey at the moment of highest intent |
| Booking widget structurally embedded in hero viewport | Transactional noise in what should be an atmospheric first impression |
| Form-only private events page | No editorial sell, no emotional journey — pure administration |
| 8 animated motifs per section (Debonaïr excess) | Decorative noise. One motif, used with restraint, is more powerful |
| Geolocation interruption popup | TEMPO is one location. Never interrupt a visitor's first experience |
| "Premier Rewards" / loyalty programme language | Chain hospitality register. TEMPO frames guest relationships as connoisseurship |
| Generic event naming ("Autumn Dinner", "Valentine's Menu") | Flattens the brand. Name everything with cultural and tonal specificity |
| Gift Cards as a top-nav item | Merchandising register. Frame it editorially if it exists at all |
| "My Store" Shopify ghost text (Debonaïr's oversight) | Signals unpolished execution. Test every footer, every page |

---

### TEMPO Animation Principles

1. **Slow and deliberate** — nothing snaps. All transitions ease with exponential or spring curves (existing tokens in `brand-tokens.css`).
2. **Parallax depth layers on hero** — foreground / midground / background at different scroll speeds. The laneway arch as the framing device.
3. **One recurring brand motif** — a single illustrated element drifts through sections. Changes with gallery exhibition. Never crowds a composition.
4. **Reveal on scroll** — text fades up, images bloom in. Never pop or bounce.
5. **Texture breathing sections** — full-bleed rice paper / linen / bar surface detail between content zones. Palette deepens rather than switches.
6. **Day/Night transition** — CSS custom property animation across `data-tempo` attribute. Auto-triggers based on HCMC local time; user-overrideable. A deepening, not a hard swap.
7. **Event cards** — hover reveals second image (interior setup). Cursor changes to custom crosshair on gallery grid.
8. **Mobile menu as micro-gallery** — overlay background is current exhibition artwork from CMS. Changes monthly.
9. **No skeleton loaders** — opacity fade with subtle blur-out as content arrives from CMS. Never flash of unstyled content.
10. **Event enquiry form** — full-page experience, night palette, one question per step. Should feel like a private members' application, not a Google Form.

---

## Photography Brief (Pre-Shoot Priorities)

Since no photography is ready yet, the following shots are the absolute minimum to begin Phase 1 build:

**Priority 1 — Hero video reel (30–60 seconds, loopable)**
- Laneway entry, day and evening
- Bar setup — amber light, ice, glassware
- Morning espresso pull
- Level 2 gallery wall (even empty)
- Someone in conversation, mid-laugh, natural light

**Priority 2 — Static stills (minimum 20 shots)**
- 4 × exterior / laneway
- 4 × interior day (windows, natural light, coffee)
- 4 × interior evening (bar, amber, intimate)
- 4 × Level 2 gallery/event space
- 4 × food/drink detail (close, documentary, not styled flat-lay)

**Direction:** Available light always. No studio flash. Medium-close documentary. Reference: The Wolseley (London), Gimlet (Melbourne), Noma archive photography.

---

## Tech Stack Summary

```
Front-end:     Next.js (App Router, SSR/ISR — not static export)
Styling:       Custom CSS (existing tokens) — no Tailwind
CMS:           WordPress (headless) — ACF Pro for structured fields
API:           WordPress REST API or WPGraphQL
Reservations:  Eat App or Resy (dining) + custom multi-step form (events)
Email/CRM:     Klaviyo (existing)
Analytics:     GA4 + Meta Pixel (live)
Deploy:        Vercel (recommended) or SiteGround Node.js plan
Domain:        tempohouse.com.vn (live)
Hosting WP:    SiteGround or separate WP host (not public-facing)
```

---

## Next Steps (Ordered)

1. **Confirm deploy target** — Vercel vs SiteGround Node. This unblocks the SSR migration.
2. **Set up WordPress instance** — install ACF Pro, create custom post types for Events, Exhibitions.
3. **Book photoshoot** — minimum hero video + 20 stills. Site cannot launch at full quality without this.
4. **Build Phase 1 pages** — Home (redesigned), Events/Enquiry, What's On, Gallery, Cafe & Bar, Venue, Contact, Reservations.
5. **Integrate Eat App** — sign up, configure venue, embed widget.
6. **Wire Klaviyo to event enquiry form** — lead capture pipeline.
7. **Content entry** — populate WordPress with first events, first exhibition, menus.
8. **QA + mobile audit** — mobile-first, test on iOS Safari and Chrome Android.
9. **Deploy Phase 2** — Floor Plans, Creator Floor, Menus, Jobs.
