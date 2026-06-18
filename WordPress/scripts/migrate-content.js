#!/usr/bin/env node
/**
 * TEMPO House — Content Migration Script
 *
 * Migrates all Next.js hardcoded content (events, journal articles, exhibition)
 * into WordPress via the REST API. All posts are created as drafts — the
 * marketing team reviews and publishes.
 *
 * Requirements:
 *   - Node 18+ (uses native fetch)
 *   - WordPress installed and running locally
 *   - ACF plugin active (for meta fields to register correctly)
 *   - tempohouse theme active (registers Event CPT and rest-meta.php hooks)
 *   - Application Password created for your WP admin user
 *
 * Setup:
 *   1. Create a WP Application Password:
 *      WP Admin → Users → [your user] → scroll to "Application Passwords"
 *      → name it "Migration" → Generate → copy the password (with spaces)
 *   2. Set the three config values below (WP_URL, WP_USER, WP_APP_PASSWORD)
 *   3. Run: node WordPress/scripts/migrate-content.js
 *
 * Usage:
 *   node WordPress/scripts/migrate-content.js          # full migration
 *   node WordPress/scripts/migrate-content.js --dry    # print what would be created, no API calls
 *   node WordPress/scripts/migrate-content.js --events # events only
 *   node WordPress/scripts/migrate-content.js --posts  # journal posts only
 */

// ── CONFIG (edit these before running) ────────────────────────────────────────

const WP_URL       = process.env.WP_URL       || "http://localhost:8888";   // local WP base URL
const WP_USER      = process.env.WP_USER      || "admin";                   // WP username
const WP_APP_PASSWORD = process.env.WP_APP_PASSWORD || "";                  // Application Password (with spaces OK)

// ── FLAGS ────────────────────────────────────────────────────────────────────

const args   = process.argv.slice(2);
const DRY    = args.includes("--dry");
const ONLY_EVENTS = args.includes("--events");
const ONLY_POSTS  = args.includes("--posts");

// ── CONTENT DATA (from Next.js data.ts files) ────────────────────────────────

const EVENTS = [
  {
    slug:    "works-on-paper-opening-night",
    type:    "Exhibition",
    title:   "Works on Paper — Opening Reception",
    date:    "2026-06-27",       // YYYYMMDD for ACF date_picker
    endDate: "2026-06-27",
    time:    "18:00 – 21:00",
    price:   "Free entry",
    recurrence: "one-time",
    interior:   "dark",
    isActive:   true,
    description: "Five artists. Drawings, prints, and works on paper. An evening to mark the beginning of TEMPO House's gallery programme.",
    body: `<p>The first show at TEMPO House opens on the evening of Friday 27 June. <em>Works on Paper</em> brings together five artists working across drawing, printmaking, and collage — all of them based in Ho Chi Minh City and Hanoi, all working in practices we have been following closely.</p>

<p>The opening reception runs from 18:00 to 21:00. The bar will be open. The works will be on the walls through August. This is an evening for the artists, for the people who care about what is being made in this city, and for anyone who wants to be in a room where something is beginning.</p>

<p>Entry is free. No RSVP required, though we ask that you arrive between 18:00 and 20:00 to allow the evening room to breathe at its own pace.</p>

<p>The exhibition continues through 9 August, visible during all café and bar hours.</p>`,
  },
  {
    slug:    "tempo-sessions-july",
    type:    "Music",
    title:   "TEMPO Sessions — Vol. 1",
    date:    "2026-07-19",
    endDate: "2026-07-19",
    time:    "20:00 – 23:00",
    price:   "Free entry",
    recurrence: "monthly",
    interior:   "dark",
    isActive:   true,
    description: "Twenty seats. A musician. The kind of listening room that most venues in Saigon don't have the quiet to achieve.",
    body: `<p>TEMPO Sessions is a monthly live music programme at the bar. Twenty seats. A musician or small ensemble. The kind of listening environment that most live music venues in Ho Chi Minh City don't have the quiet to achieve.</p>

<p>The first edition features an artist to be announced. The format is deliberate: no stage, no amplification above the acoustic level of the room, no set break. The music happens at the bar, among the drinks, as part of the evening rather than a performance event appended to it.</p>

<p>Entry is free. Seating is first-come — the room holds twenty and we will not add chairs. Doors open at 19:00. Music begins at 20:00.</p>

<p>The next edition will be announced in the TEMPO Letter.</p>`,
  },
  {
    slug:    "tasting-menu-august",
    type:    "Dining",
    title:   "The Tasting Menu",
    date:    "2026-08-16",
    endDate: "2026-08-16",
    time:    "19:00 – 23:00",
    price:   "1,800,000 VND per person",
    recurrence: "monthly",
    interior:   "sand",
    isActive:   false,
    description: "Twelve guests. A single evening. A menu built around the dry-season produce of the Da Lat highlands, with a paired drinks programme.",
    body: `<p>Twelve guests. One evening. A menu that moves through the seasons of the Da Lat highlands — built by our kitchen team around what is arriving at the market, and paired with a drinks programme by the bar.</p>

<p>This is not a formal tasting menu in the restaurant sense. There is no uniform, no amuse-bouche nomenclature. The food will be direct and seasonal; the drinks will complement rather than perform. The evening is long and unhurried.</p>

<p>The menu will be sent to guests in the week before the event. Dietary requirements can be accommodated with advance notice at the time of booking.</p>

<p>Capacity is twelve. Reservations are required and payment is taken in advance. Enquiries and booking via the link below or at the bar in person.</p>

<p><strong>1,800,000 VND per person</strong>, inclusive of the full drinks pairing.</p>`,
  },
];

const ARTICLES = [
  {
    slug:        "specialty-coffee-guide-ho-chi-minh-city",
    category:    "Coffee",
    title:       "The Guide to Specialty Coffee in Ho Chi Minh City",
    excerpt:     "Third-wave coffee arrived in Saigon quietly — no manifesto, no fanfare. A handful of roasters, a few spare rooms, and a city that had been drinking its own excellent coffee for decades without needing to call it anything.",
    readMinutes: 6,
    publishedAt: "2026-06-01",
    featured:    true,
    body: `<p>Third-wave coffee arrived in Saigon quietly — no manifesto, no fanfare. A handful of roasters, a few spare rooms, and a city that had been drinking its own excellent coffee for decades without needing to call it anything. The difference now is precision: controlled extraction, single-origin traceability, and the kind of attention to water temperature that the rest of the world is only catching up to.</p>

<p>Ho Chi Minh City has always had a serious coffee culture. The cà phê trứng, the slow phin drip, the condensed milk ritual — these weren't trends, they were infrastructure. What specialty coffee added was a new vocabulary for something the city already understood instinctively: that a good cup is worth your full attention.</p>

<h2>What Makes a Coffee Specialty</h2>

<p>The Specialty Coffee Association defines specialty coffee as beans scoring 80 points or above on a 100-point scale — graded on origin character, processing cleanliness, and flavour complexity. In practice, what you notice is clarity. There is no muddy middle, no burnt-rubber aftertaste, no need for sugar to make the cup approachable.</p>

<p>The roasters defining the scene in HCMC are sourcing from Da Lat highlands, from Buon Ma Thuot, from single farms in the Central Highlands that have been growing Robusta for three generations and are only now experimenting with Arabica varietals suited to the altitude.</p>

<h2>The District 3 Scene</h2>

<p>District 3 has become the natural home for the city's specialty coffee culture — not by design, but by the logic of neighbourhoods. The street grid around Pasteur, Võ Văn Tần, and Nguyễn Thị Minh Khai attracts a creative professional density that supports the kind of unhurried morning ritual that good coffee requires.</p>

<p>At TEMPO House, the morning programme runs from 07:00 to 17:00. The coffee list rotates seasonally around two or three origins — typically one washed, one natural, one experimental. The pours are filter and espresso, the equipment is calibrated weekly, and the team tastes together every morning before the first customer arrives.</p>

<h2>What to Order</h2>

<p>If you are new to specialty coffee in Saigon, start with a filter — either a V60 or a Chemex depending on what the barista recommends for the day's lot. This is the clearest expression of what an origin actually tastes like.</p>

<p>If you prefer espresso, a flat white is the clearest test of a café's calibration. Milk should complement, not mask. At TEMPO, the flat white is pulled to a 1:2.5 ratio with full-cream milk at 62°C.</p>

<blockquote>The best café is the one you return to on a Tuesday morning when nothing particular is happening. That is the only reliable test.</blockquote>

<p>TEMPO House is at 218c Pasteur, District 3. Open daily 07:00 – 17:00. Walk-ins welcome.</p>`,
  },
  {
    slug:        "why-district-3",
    category:    "Saigon",
    title:       "Why District 3",
    excerpt:     "District 3 doesn't announce itself. There is no landmark moment where you cross from somewhere else into it — the streets simply become quieter, the trees older, the buildings more varied in their ambitions.",
    readMinutes: 4,
    publishedAt: "2026-06-03",
    body: `<p>District 3 doesn't announce itself. There is no landmark moment where you cross from somewhere else into it — the streets simply become quieter, the trees older, the buildings more varied in their ambitions. A French colonial villa next to a brutalist apartment block next to a narrow shophouse that has been a pharmacy, a noodle shop, and a design studio in successive decades.</p>

<h2>The Creative Corridor</h2>

<p>The stretch of Pasteur between Điện Biên Phủ and Lý Tự Trọng has quietly become the address of choice for architects, brand studios, independent publishers, and the kind of café that takes its sourcing seriously. Not because rents are low — they are not particularly — but because the streets are walkable, the buildings have character, and the surrounding network of creative professionals creates a kind of productive osmosis.</p>

<h2>Why This Address</h2>

<p>218c Pasteur was chosen because the space could hold two lives without either compromising the other. The proportions allowed for a morning room — high ceilings, east light, the particular silence of a space that has not yet been fully inhabited for the day — and an evening room in the same bones, dressed differently.</p>

<p>The gallery walls came from the same logic. District 3 has galleries, but they are mostly gallery-galleries — white-box spaces visited for openings and left quiet in between. We wanted the work to live alongside the coffee and the cocktails, available on a Tuesday afternoon to whoever happened to be there.</p>

<blockquote>Every city has a neighbourhood that holds the people who are building its next ten years. In Ho Chi Minh City, that neighbourhood is District 3.</blockquote>

<p>TEMPO House is at 218c Pasteur. Open daily.</p>`,
  },
  {
    slug:        "building-the-bar",
    category:    "Cocktails",
    title:       "Building the Bar",
    excerpt:     "The bar at TEMPO House was designed around a specific problem: how do you build a drinks programme that feels Vietnamese without performing Vietnameseness?",
    readMinutes: 5,
    publishedAt: "2026-06-05",
    body: `<p>The bar at TEMPO House was designed around a specific problem: how do you build a drinks programme that feels Vietnamese without performing Vietnameseness? The answer, arrived at after a few months of sourcing and testing, was to start with local ingredients and work backwards — not to foreground their origin, but to let them define the flavour logic of the drinks.</p>

<h2>The Ingredient Logic</h2>

<p>Vietnamese botanicals are extraordinary and largely unexported. Lá chanh — the kaffir lime leaf — has a citrus-herbal register that gin botanicals have been approximating for decades without quite getting there. Cà phê robusta, roasted darker than most specialty contexts would allow, produces an espresso intensity that works differently in a cocktail than any Latin American bean we tried. Da Lat strawberries, smaller and more acidic than commercial varieties, make syrups that hold their flavour through ice dilution in a way that commercial strawberry cordials don't.</p>

<h2>The Programme</h2>

<p>The bar menu is structured around three registers: short cocktails built for the beginning of an evening, long drinks for the middle, and digestive-leaning options for the end. The list rotates seasonally — not because seasonality is a marketing concept, but because local produce availability actually changes, and the drinks should reflect that honestly.</p>

<p>There is also a concise wine list focused on natural and low-intervention producers from France, Italy, and Georgia, and a spirits selection that skews Japanese whisky and aged rum alongside the standard call brands.</p>

<blockquote>A good bar is one that makes you want to stay longer than you planned. Everything else is decoration.</blockquote>

<p>The bar opens at 18:00 daily. Last orders at 00:30.</p>`,
  },
  {
    slug:        "first-light-to-last-call",
    category:    "The Craft",
    title:       "First Light to Last Call",
    excerpt:     "What it means to run a place that is a café in the morning and a bar at night — and why the handover between the two is the most important moment in the day.",
    readMinutes: 5,
    publishedAt: "2026-06-07",
    body: `<p>The hardest part of running a dual-programme venue is the handover. At 17:00, the café closes. The chairs that were arranged for morning light and laptop work need to become chairs that invite you to stay for another drink. The music shifts. The lighting drops. The staff who were calibrating espresso pour milk are now polishing glasses and setting out menus. It is, in a literal sense, the same room becoming a different room.</p>

<h2>The Morning Logic</h2>

<p>The café runs on a different energy to the bar. Morning customers are there for a purpose — work, a meeting, the ritual of coffee before the day properly starts. They want the room to be reliable: the same cup, the same seat if possible, the same music at the same volume. Surprise is not what you want at 08:00.</p>

<p>The barista's job in the morning is partly about the coffee and partly about creating the conditions for focus. Good café design is invisible — you don't notice it until something is wrong. The light should be right. The noise level should permit conversation at a normal register. The coffee should arrive at the right temperature and stay that way.</p>

<h2>The Evening Logic</h2>

<p>By 18:00, the same room needs to hold a different kind of attention. The evening customer is not there to work. They are there to be in a space with other people, to let the day decompress, to have a drink that makes the transition from work to not-work feel like an event rather than a commute. The bar team's job is to facilitate that — through pace, through recommendation, through the quality of the conversation at the counter.</p>

<blockquote>A room that works in both registers is a room that understands people, not just F&amp;B metrics.</blockquote>

<p>TEMPO House opens at 07:00 and closes when the last guest is ready to leave — typically around 01:00.</p>`,
  },
  {
    slug:        "works-on-paper-inaugural-exhibition",
    category:    "Art",
    title:       "Works on Paper: The Inaugural Exhibition",
    excerpt:     "The first exhibition at TEMPO House brings together five artists whose practices share a common material — paper — but little else. That is the point.",
    readMinutes: 4,
    publishedAt: "2026-06-09",
    body: `<p>The first exhibition at TEMPO House brings together five artists whose practices share a common material — paper — but little else. That is the point. The show is not a thematic survey of drawing or printmaking in Vietnam. It is a gathering of work that the gallery wanted to see in a room together, from artists whose practices we have been watching and whose approaches to the page feel, in different ways, urgent.</p>

<h2>The Artists</h2>

<p>Nguyễn Thị Lan works in graphite on large-format paper, producing portraits of domestic interiors — rooms in apartments, the undersides of furniture, the geometry of light through louvres. The scale is unusual; the detail is forensic. Trần Minh Đức makes prints from found objects — packaging, hardware, street detritus — that he inks and presses to create images that are simultaneously documentary and abstract.</p>

<p>Lê Hoàng Anh uses collage as a method of compression: images from different decades of Vietnamese magazine photography, cut and re-layered until the source material becomes illegible and something new emerges. Pham Van Quang draws in ink, fast and without revision, producing images that feel captured rather than constructed. Mai Thu Hương makes works on paper that are also objects — folded, stitched, sometimes wetted and dried — that exist on the border between drawing and sculpture.</p>

<h2>Why Paper</h2>

<p>Paper is not a neutral medium. In Vietnam, it carries associations — lottery tickets, religious offerings, bureaucratic forms, the particular texture of old newsprint. The artists in this show are all, in different ways, aware of those associations, and their work either draws on them or deliberately refuses them.</p>

<p>The exhibition runs from 27 June to 9 August 2026. Works are available for acquisition — enquiries to gallery@tempohouse.com.vn.</p>`,
  },
  {
    slug:        "tempo-letter-vol-1",
    category:    "The Craft",
    title:       "The TEMPO Letter, Vol. 1",
    excerpt:     "What we've been thinking about: the rhythm of a place, what makes a morning worth staying for, and a few things we noticed in the first weeks of being open.",
    readMinutes: 3,
    publishedAt: "2026-06-10",
    body: `<p>This is the first edition of the TEMPO Letter — a periodic note from the team at TEMPO House. No fixed schedule, no editorial quota. We'll write when we have something worth saying.</p>

<h2>On the Rhythm of a Place</h2>

<p>The thing that surprised us most in the first weeks was how quickly a place develops a rhythm. By the second week we had regulars — not just people who came back, but people who came back at the same time, to the same seat, for the same order. A place that has rhythm is a place that people have decided to make part of their own rhythm. That feels like a kind of success we weren't sure how to measure.</p>

<h2>What We've Been Listening To</h2>

<p>The morning playlist leans toward ambient and minimalist — Brian Eno, Nils Frahm, a lot of Japanese city pop from the eighties that works better at 08:00 than you'd expect. The evening shifts toward jazz and electronic — we've been exploring the intersection between Vietnamese cải lương and contemporary electronic music, which is a rabbit hole with no visible bottom.</p>

<h2>What's Coming</h2>

<p>The gallery opens on 27 June with <em>Works on Paper</em>. TEMPO Sessions — our monthly live music programme — launches in July. The tasting menu, a seasonal dining collaboration between the kitchen and bar teams, starts in August. We'll write about all of these as they happen.</p>

<p>If you'd like to receive the next edition directly, you can subscribe at the bottom of the homepage. We will not send anything we wouldn't want to receive ourselves.</p>

<p>— The TEMPO House team</p>`,
  },
];

// ── HELPERS ──────────────────────────────────────────────────────────────────

function authHeader() {
  const creds = Buffer.from(`${WP_USER}:${WP_APP_PASSWORD}`).toString("base64");
  return { Authorization: `Basic ${creds}` };
}

async function wpPost(endpoint, body) {
  if (DRY) {
    console.log(`  [DRY] POST ${WP_URL}/wp-json/wp/v2/${endpoint}`, JSON.stringify(body, null, 2));
    return { id: 0, link: "(dry run)" };
  }
  const res = await fetch(`${WP_URL}/wp-json/wp/v2/${endpoint}`, {
    method:  "POST",
    headers: { ...authHeader(), "Content-Type": "application/json" },
    body:    JSON.stringify(body),
  });
  if (!res.ok) {
    const err = await res.json().catch(() => ({}));
    throw new Error(`${res.status} ${res.statusText}: ${err.message || JSON.stringify(err)}`);
  }
  return res.json();
}

async function wpGet(endpoint) {
  const res = await fetch(`${WP_URL}/wp-json/wp/v2/${endpoint}`, {
    headers: authHeader(),
  });
  if (!res.ok) throw new Error(`GET ${endpoint} → ${res.status}`);
  return res.json();
}

async function getOrCreateTerm(taxonomy, name) {
  const slug = name.toLowerCase().replace(/\s+/g, "-").replace(/[^a-z0-9-]/g, "");
  const existing = await wpGet(`${taxonomy}?slug=${slug}&per_page=1`).catch(() => []);
  if (Array.isArray(existing) && existing.length > 0) return existing[0];
  if (DRY) { console.log(`  [DRY] Create ${taxonomy}: "${name}"`); return { id: 0 }; }
  return wpPost(taxonomy, { name, slug });
}

function formatDate(isoDate) {
  // Convert YYYY-MM-DD to YYYYMMDD for ACF date_picker
  return isoDate.replace(/-/g, "");
}

// ── MIGRATION FUNCTIONS ───────────────────────────────────────────────────────

async function migrateEvents() {
  console.log("\n📅  Migrating events as standard Posts tagged 'event'...\n");
  const eventTag = await getOrCreateTerm("tags", "event");
  console.log(`  ✓  Tag 'event' → id=${eventTag.id}`);

  for (const ev of EVENTS) {
    const categoryLabel = ev.type; // "Music", "Exhibition", "Dining"
    console.log(`  → Creating: "${ev.title}"`);
    try {
      const post = await wpPost("posts", {
        title:   { raw: ev.title },
        content: { raw: ev.body },
        excerpt: { raw: ev.description },
        status:  "draft",
        slug:    ev.slug,
        tags:    [ eventTag.id ],
        date:    `${ev.date}T${ev.time.split(" – ")[0]}:00`,
        meta: {
          event_category:  categoryLabel,
          event_date:      formatDate(ev.date),
          event_end_date:  formatDate(ev.endDate),
          event_time:      ev.time,
          event_recurrence: ev.recurrence,
          event_interior:  ev.interior,
          event_price:     ev.price,
        },
      });
      console.log(`    ✓  id=${post.id}  ${post.link || "(draft)"}`);
    } catch (err) {
      console.error(`    ✗  FAILED: ${err.message}`);
    }
  }
}

async function migrateArticles() {
  console.log("\n📰  Migrating journal articles as standard Posts...\n");

  // Map category names → WP category IDs
  const categoryNames = [...new Set(ARTICLES.map(a => a.category))];
  const categoryMap = {};
  for (const name of categoryNames) {
    const term = await getOrCreateTerm("categories", name);
    categoryMap[name] = term.id;
    console.log(`  ✓  Category '${name}' → id=${term.id}`);
  }

  // Get or create 'Journal' parent category
  const journalCat = await getOrCreateTerm("categories", "Journal");
  console.log(`  ✓  Category 'Journal' → id=${journalCat.id}`);

  for (const article of ARTICLES) {
    console.log(`  → Creating: "${article.title}"`);
    try {
      const post = await wpPost("posts", {
        title:      { raw: article.title },
        content:    { raw: article.body },
        excerpt:    { raw: article.excerpt },
        status:     "draft",
        slug:       article.slug,
        date:       `${article.publishedAt}T08:00:00`,
        categories: [ categoryMap[article.category], journalCat.id ],
        // read_minutes stored as custom meta — register via rest-meta.php if needed
        meta: {
          _read_minutes: String(article.readMinutes),
        },
      });
      console.log(`    ✓  id=${post.id}  ${post.link || "(draft)"}`);
    } catch (err) {
      console.error(`    ✗  FAILED: ${err.message}`);
    }
  }
}

async function migrateExhibition() {
  console.log("\n🖼   Migrating exhibition as a Post tagged 'exhibition'...\n");
  const exhibitionTag = await getOrCreateTerm("tags", "exhibition");
  console.log(`  ✓  Tag 'exhibition' → id=${exhibitionTag.id}`);

  const ex = {
    title: "Works on Paper — Inaugural Exhibition",
    slug:  "works-on-paper",
    date:  "2026-06-27",
    body:  `<p><em>Works on Paper</em> brings together five artists working across drawing, printmaking, and collage — all of them based in Ho Chi Minh City and Hanoi.</p>

<h2>Artists</h2>
<p>Nguyễn Thị Lan, Trần Minh Đức, Lê Hoàng Anh, Pham Van Quang, Mai Thu Hương</p>

<h2>Medium</h2>
<p>Drawing, printmaking, and collage</p>

<h2>Dates</h2>
<p>27 June – 9 August 2026. Open daily with café and bar hours.</p>

<h2>Curatorial Essay</h2>

<p>The show is not a thematic survey of drawing or printmaking in Vietnam. It is a gathering of work that the gallery wanted to see in a room together, from artists whose practices we have been watching and whose approaches to the page feel, in different ways, urgent.</p>

<p>Paper is not a neutral medium. In Vietnam, it carries associations — lottery tickets, religious offerings, bureaucratic forms, the particular texture of old newsprint. The artists in this show are all, in different ways, aware of those associations, and their work either draws on them or deliberately refuses them.</p>

<p>What connects these five practices is not a shared aesthetic position but a shared rigour — an insistence that the medium has something to say about the world it is made in, and that saying it clearly is a form of respect for both the work and the viewer.</p>

<p>The gallery at TEMPO House is open during all café and bar hours. Entry is free. Works are available for acquisition — enquiries to gallery@tempohouse.com.vn.</p>`,
  };

  console.log(`  → Creating: "${ex.title}"`);
  try {
    const post = await wpPost("posts", {
      title:   { raw: ex.title },
      content: { raw: ex.body },
      status:  "draft",
      slug:    ex.slug,
      date:    `${ex.date}T09:00:00`,
      tags:    [ exhibitionTag.id ],
    });
    console.log(`    ✓  id=${post.id}  ${post.link || "(draft)"}`);
  } catch (err) {
    console.error(`    ✗  FAILED: ${err.message}`);
  }
}

// ── MAIN ─────────────────────────────────────────────────────────────────────

async function main() {
  if (!WP_APP_PASSWORD && !DRY) {
    console.error("❌  WP_APP_PASSWORD is not set. Create an Application Password in WP Admin → Users → [your user].");
    console.error("    Then run: WP_APP_PASSWORD='xxxx xxxx xxxx xxxx xxxx xxxx' node WordPress/scripts/migrate-content.js");
    process.exit(1);
  }

  const mode = DRY ? " [DRY RUN — no API calls]" : "";
  console.log(`\n🚀  TEMPO House Content Migration${mode}`);
  console.log(`    Target: ${WP_URL}`);
  console.log(`    User:   ${WP_USER}\n`);

  if (!ONLY_POSTS) await migrateEvents();
  if (!ONLY_EVENTS) await migrateArticles();
  if (!ONLY_EVENTS && !ONLY_POSTS) await migrateExhibition();

  console.log("\n✅  Migration complete.");
  console.log("    All posts created as DRAFTS. Review and publish in WP Admin → Posts.\n");
}

main().catch(err => {
  console.error("❌  Migration failed:", err.message);
  process.exit(1);
});
