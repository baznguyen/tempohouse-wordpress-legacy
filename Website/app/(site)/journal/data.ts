export type ArticleCategory = "Coffee" | "Cocktails" | "Art" | "Saigon" | "The Craft";

export interface Article {
  slug: string;
  category: ArticleCategory;
  title: string;
  excerpt: string;
  body: string;
  readMinutes: number;
  publishedAt: string;
  featured?: boolean;
}

export const ARTICLES: Article[] = [
  {
    slug: "specialty-coffee-guide-ho-chi-minh-city",
    category: "Coffee",
    title: "The Guide to Specialty Coffee in Ho Chi Minh City",
    excerpt:
      "Third-wave coffee arrived in Saigon quietly — no manifesto, no fanfare. A handful of roasters, a few spare rooms, and a city that had been drinking its own excellent coffee for decades without needing to call it anything.",
    readMinutes: 6,
    publishedAt: "2026-06-01",
    featured: true,
    body: `<p>Third-wave coffee arrived in Saigon quietly — no manifesto, no fanfare. A handful of roasters, a few spare rooms, and a city that had been drinking its own excellent coffee for decades without needing to call it anything. The difference now is precision: controlled extraction, single-origin traceability, and the kind of attention to water temperature that the rest of the world is only catching up to.</p>

<p>Ho Chi Minh City has always had a serious coffee culture. The cà phê trứng, the slow phin drip, the condensed milk ritual — these weren't trends, they were infrastructure. What specialty coffee added was a new vocabulary for something the city already understood instinctively: that a good cup is worth your full attention.</p>

<h2>What Makes a Coffee Specialty</h2>

<p>The Specialty Coffee Association defines specialty coffee as beans scoring 80 points or above on a 100-point scale — graded on origin character, processing cleanliness, and flavour complexity. In practice, what you notice is clarity. There is no muddy middle, no burnt-rubber aftertaste, no need for sugar to make the cup approachable. A well-pulled shot of a natural-processed Ethiopian can taste of blueberry jam and dark chocolate without a drop of either.</p>

<p>The roasters defining the scene in HCMC are sourcing from Da Lat highlands, from Buon Ma Thuot, from single farms in the Central Highlands that have been growing Robusta for three generations and are only now experimenting with Arabica varietals suited to the altitude. Local terroir, handled with the same care as a Kenyan AA or a Guatemalan Huehuetenango.</p>

<h2>The District 3 Scene</h2>

<p>District 3 has become the natural home for the city's specialty coffee culture — not by design, but by the logic of neighbourhoods. The street grid around Pasteur, Võ Văn Tần, and Nguyễn Thị Minh Khai attracts a creative professional density that supports the kind of unhurried morning ritual that good coffee requires. These are people who work from cafés, who know their baristas by name, who will wait four minutes for a manual pour without checking their phone.</p>

<p>At TEMPO House, the morning programme runs from 07:00 to 17:00. The coffee list rotates seasonally around two or three origins — typically one washed, one natural, one experimental. The pours are filter and espresso, the equipment is calibrated weekly, and the team tastes together every morning before the first customer arrives. Not because this is required. Because it matters.</p>

<h2>What to Order</h2>

<p>If you are new to specialty coffee in Saigon, start with a filter — either a V60 or a Chemex depending on what the barista recommends for the day's lot. This is the clearest expression of what an origin actually tastes like, without the intensity of espresso compression. Ask what they're excited about. A good barista will tell you without being asked.</p>

<p>If you prefer espresso, a flat white is the clearest test of a café's calibration. Milk should complement, not mask. If it tastes predominantly of milk, the espresso is under-extracted or the ratio is off. At TEMPO, the flat white is pulled to a 1:2.5 ratio with full-cream milk at 62°C. It should taste of the origin, with cream as context rather than subject.</p>

<p>The kitchen runs until 15:00 — seasonal pastries, a small savoury menu, nothing elaborate. The food is designed to accompany coffee, not compete with it.</p>

<blockquote>The best café is the one you return to on a Tuesday morning when nothing particular is happening. That is the only reliable test.</blockquote>

<p>TEMPO House is at 218c Pasteur, District 3. Open daily 07:00 — 17:00. Walk-ins welcome.</p>`,
  },
  {
    slug: "why-district-3",
    category: "Saigon",
    title: "Why District 3",
    excerpt:
      "District 3 doesn't announce itself. There is no landmark moment where you cross from somewhere else into it — the streets simply become quieter, the trees older, the buildings more varied in their ambitions.",
    readMinutes: 4,
    publishedAt: "2026-06-03",
    body: `<p>District 3 doesn't announce itself. There is no landmark moment where you cross from somewhere else into it — the streets simply become quieter, the trees older, the buildings more varied in their ambitions. A French colonial villa next to a brutalist apartment block next to a narrow shophouse that has been a pharmacy, a noodle shop, and a design studio in successive decades. The neighbourhood holds its history lightly, without needing to museum it.</p>

<p>For a city that moves as fast as Ho Chi Minh City, District 3 is unusually still. Not sleepy — there is too much coffee being consumed for that — but deliberate. The people who choose to work here, to live here, or to open here tend to be people who have made a considered decision about the kind of environment they want to inhabit. That self-selection creates something that is difficult to manufacture and impossible to replicate once lost: a genuine neighbourhood character.</p>

<h2>The Creative Corridor</h2>

<p>The stretch of Pasteur between Điện Biên Phủ and Lý Tự Trọng has quietly become the address of choice for architects, brand studios, independent publishers, and the kind of café that takes its sourcing seriously. Not because rents are low — they are not particularly — but because the streets are walkable, the buildings have character, and the surrounding network of creative professionals creates a kind of productive osmosis. You bump into people here. Ideas move between tables.</p>

<p>Võ Văn Tần runs parallel and carries a different energy: bookshops, a few excellent bánh mì counters that have been there for decades, and a stretch of evening activity that starts as early as five and runs until the neighbourhood decides it has had enough, which is usually later than it planned.</p>

<h2>Why This Address</h2>

<p>218c Pasteur was chosen because the space could hold two lives without either compromising the other. The proportions allowed for a morning room — high ceilings, east light, the particular silence of a space that has not yet been fully inhabited for the day — and an evening room in the same bones, dressed differently. This is not a conversion. It is a building that always had two moods; we simply made them explicit.</p>

<p>The gallery walls came from the same logic. District 3 has galleries, but they are mostly gallery-galleries — white-box spaces visited for openings and left quiet in between. We wanted the work to live alongside the coffee and the cocktails, available on a Tuesday afternoon to whoever happened to be there. Art as part of the texture of the day, not a special occasion requiring a specific intention.</p>

<blockquote>Every city has a neighbourhood that holds the people who are building its next ten years. In Ho Chi Minh City, that neighbourhood is District 3. We wanted to be part of what they were building.</blockquote>

<p>TEMPO House is at 218c Pasteur. Open daily.</p>`,
  },
  {
    slug: "building-the-bar",
    category: "Cocktails",
    title: "Building the Bar",
    excerpt:
      "The bar at TEMPO didn't start with a menu. It started with a conversation about what Vietnamese botanicals actually taste like when you stop using them as decoration.",
    readMinutes: 5,
    publishedAt: "2026-06-05",
    body: `<p>The bar at TEMPO didn't start with a menu. It started with a conversation about what Vietnamese botanicals actually taste like when you stop using them as decoration.</p>

<p>Saigon has excellent drinking. It has always had excellent drinking — the bia hơi culture, the late-night street food beers, the rooftop bars that have multiplied along with the city's international profile. What it has had less of is a serious cocktail bar that treats Vietnamese ingredients with the same rigour that Tokyo or London applies to their native botanicals. Not fusion — fluency. The difference matters.</p>

<h2>The Ingredient Map</h2>

<p>The research phase took four months. The team worked through a list of Vietnamese plants, herbs, and fermented products that have flavour profiles interesting enough to anchor a drink rather than merely accent one. Lá lốt, the wild betel leaf used in grilled beef dishes, turned out to carry a complex peppery-herbal note that behaves well in stirred drinks with aged spirit. Calamansi from the Mekong delta — not lime, not orange, its own thing — became the acid source in several of the lighter builds. Mít (jackfruit) fermented and concentrated becomes almost caramelised, with a tropical depth that works unexpectedly well against scotch.</p>

<p>The spirits selection is international — we are not trying to make a Vietnamese spirits bar, which would be an artificial constraint — but the vocabulary of the cocktail list draws from local produce and technique. The ice programme uses filtered water, cut to specification. The garnish is either functional or absent.</p>

<h2>The List</h2>

<p>The TEMPO bar menu opens with ten signature cocktails and a short list of considered classics. The signatures rotate twice a year — wet and dry season — because the produce changes and the palate of the room changes with the weather. The classics are the classics because they work; the team doesn't believe in reinventing the Negroni unless the reinvention is genuinely better, which it rarely is.</p>

<p>The spirits list is curated around the question of what is actually interesting rather than what is well-known. There are whisky expressions here that most Saigon bars don't stock because they require research to source and confidence to sell. The team can talk about all of them without consulting a cheat sheet, which is the only qualification that matters.</p>

<h2>18:00</h2>

<p>The bar opens at 18:00. By that hour the café crowd has mostly moved on, the light in the space has shifted from white to amber, and the music has changed — same playlist, different register. The transformation is not theatrical. It happens the way dusk happens: gradually, then completely.</p>

<blockquote>We are not trying to be the best bar in Ho Chi Minh City. We are trying to be the bar that the people who care about these things come back to. That is a smaller ambition with a harder brief.</blockquote>

<p>The bar is open nightly 18:00 — 01:00. Reservations recommended Thursday through Saturday.</p>`,
  },
  {
    slug: "first-light-to-last-call",
    category: "The Craft",
    title: "First Light to Last Call",
    excerpt:
      "07:12. The grinder has been running for four minutes. The espresso machine is holding temperature. Outside, Pasteur is still in the blue hour — the light that belongs to no particular time of day.",
    readMinutes: 5,
    publishedAt: "2026-06-07",
    body: `<p>07:12. The grinder has been running for four minutes. The espresso machine is holding temperature. Outside, Pasteur is still in the blue hour — the light that belongs to no particular time of day, when the street is awake but not yet committed to the morning.</p>

<p>This is when TEMPO is most itself: before the first customer arrives, when the space is still holding the quiet of the night before. The team is tasting the day's filter — a washed natural from Da Lat, bright and clean with a finish that goes long. Someone makes a note about the grind. Someone else disagrees about the temperature. The conversation is unhurried.</p>

<h2>The Morning</h2>

<p>By 08:00, the first regulars have arrived. They do not need to be asked what they want — the barista has started their usual before they sit down. This is not a trick. It is what happens when a space is used intentionally over time: the rituals become shared. The morning regulars are architects, writers, a few people who work in the building industry, one retired professor who comes every day and reads for two hours without looking at his phone. He tips well and says nothing about the coffee, which is the highest compliment.</p>

<p>The kitchen runs from 08:00 — croissants, a small savoury pastry that changes weekly, and a seasonal fruit element that depends on what was at the market. Nothing complicated. Everything made here.</p>

<p>By 10:00, the tables have a different population: laptops, notebooks, the quiet industriousness of people who have decided the office is not where they do their best thinking. TEMPO does not limit working hours. The wifi is reliable. The power points are accessible. The understanding is mutual: the space is yours as long as you are present in it.</p>

<h2>The Turn</h2>

<p>15:00 is when the shift happens. The kitchen closes. The afternoon light comes in lower and warmer through the west-facing windows. The café crowd begins to thin — not all at once, but gradually, as the day finds its logic.</p>

<p>Between 16:00 and 18:00, the space does something unusual: it is prepared for its other self. The furniture doesn't move. The walls don't change. But the lighting is adjusted — lower, warmer, more directional. The music shifts in tempo and register. The bar team arrives and begins their own opening ritual, the mirror image of the morning: tasting, calibrating, tasting again.</p>

<h2>18:00</h2>

<p>At six, the bar opens. The light by now is amber — the pendant lamps over the bar doing the work that the afternoon sun was doing an hour ago. The first evening guests arrive for early drinks: the after-work crowd, the pre-dinner crowd, the people who have no particular schedule and are exactly where they should be.</p>

<p>The conversation at the bar is different from the conversation at the coffee counter. Slower in some ways, more alive in others. People stay longer. They order a second drink. The bartender makes a recommendation that was not asked for and turns out to be correct. Someone at a corner table is looking at the work on the wall, really looking at it, in the way that you only do when you are not in a hurry.</p>

<blockquote>The space holds both lives without either diminishing the other. That is the thing we are most proud of, and the thing that was hardest to get right.</blockquote>

<p>By 23:00, the bar is at its fullest. By 01:00, it is quiet again. Someone sweeps. Someone wipes down the machine. Tomorrow at 07:00, it begins again.</p>`,
  },
  {
    slug: "works-on-paper-inaugural-exhibition",
    category: "Art",
    title: "Works on Paper: The Inaugural Exhibition",
    excerpt:
      "The first show at TEMPO House is not a statement about what we believe art should be. It is a more honest thing than that: a gathering of works that have been living in our heads for the months we spent building this space.",
    readMinutes: 4,
    publishedAt: "2026-06-09",
    body: `<p>The first show at TEMPO House is not a statement about what we believe art should be. It is a more honest thing than that: a gathering of works that have been living in our heads for the months we spent building this space. Drawings, prints, and works on paper from five artists working in Ho Chi Minh City and Hanoi. All of them people whose practice we have been watching closely, and whose work we wanted to bring into the same room to see what the conversation between them looked like.</p>

<p>Paper was the right starting point for a first exhibition. Not because it is modest — good work on paper is not modest — but because it is direct. There is no stretcher, no frame weight, no glass catching the light at the wrong angle. The relationship between the mark and the surface is immediate. You see the decisions the artist made, and the ones they didn't make, more clearly than almost any other medium.</p>

<h2>The Works</h2>

<p>The five artists in the show work in different registers: abstraction, observational drawing, printed matter, collage. What connects them is an attention to process that is visible in the finished work. These are not images that were arrived at easily or efficiently. You can see the time in them.</p>

<p>The selection was made over six months of studio visits and conversations. We were not looking for work that matched the space — that felt like the wrong brief. We were looking for work that would make the space worth coming back to after the opening, when the room was quiet and the work had settled into its positions on the wall.</p>

<h2>The Gallery Programme</h2>

<p>TEMPO's gallery walls are not a decoration programme. They are a curatorial commitment. Shows will run for six to eight weeks, changing four times a year. The work will always be available to view during café and bar hours — no separate admission, no appointment required. Some works will be available for acquisition; enquiries can be directed to the team.</p>

<p>Future shows are in development with artists whose practices intersect with the conversations we are most interested in: craft and process, the relationship between the handmade and the digital, the visual culture of Ho Chi Minh City understood from the inside rather than the outside.</p>

<blockquote>We wanted the first show to feel like a beginning rather than a statement. Six months from now, the programme will have its own logic — one we cannot fully anticipate yet. That uncertainty is part of what makes it interesting.</blockquote>

<p><em>Works on Paper</em> opens with a reception on Friday 27 June, 18:00 — 21:00. The exhibition runs through 9 August. TEMPO House, 218c Pasteur, District 3. Open during café and bar hours.</p>`,
  },
  {
    slug: "tempo-letter-vol-1",
    category: "The Craft",
    title: "The TEMPO Letter, Vol. 1",
    excerpt:
      "This is the first edition of the TEMPO Letter — a periodic note from 218c Pasteur about what is happening here, what we are thinking about, and what is worth your time in the city.",
    readMinutes: 3,
    publishedAt: "2026-06-10",
    body: `<p>This is the first edition of the TEMPO Letter — a periodic note from 218c Pasteur about what is happening here, what we are thinking about, and what is worth your time in the city. Not a newsletter in the sense of announcements and promotions. More like a letter from a place you visit regularly: news, yes, but also atmosphere. The thing that happened at the bar on Wednesday. The artist we have been talking to. The coffee that surprised us this week.</p>

<p>We will send this when there is something worth saying. Not on a fixed schedule, not because the calendar says it is time. If that means one letter a month, that is what it means. If it means two, it means two. The frequency will find itself.</p>

<h2>What's happening at TEMPO</h2>

<p>The café opens this month. The bar follows the same evening. We have been preparing for this for longer than we expected — not because the work was difficult, but because we kept finding things we wanted to get right before we opened the doors. The coffee programme has been dialled in through three months of testing. The bar team has been working on the cocktail list since January. The gallery walls are ready for their first show.</p>

<p><em>Works on Paper</em> — five artists, drawings and prints — opens 27 June with a reception that evening. The work will be on the walls from that night through August, visible to anyone who comes in for coffee or a drink without any separate occasion required.</p>

<h2>The programme ahead</h2>

<p>July brings the first TEMPO Sessions — a monthly live music evening that we are keeping deliberately small. Twenty seats at the bar, a musician or small ensemble, and the kind of listening environment that most live music venues in the city don't have the quiet to achieve. Details in the next letter.</p>

<p>August: a tasting menu collaboration between our kitchen and bar team — a single evening, twelve guests, a menu that moves through the seasons of the Da Lat highlands. Reservations by enquiry.</p>

<h2>A note on what this is</h2>

<p>TEMPO House is trying to be something specific: a place where the morning and the evening are equally worth your time, where the art on the walls is not incidental, where the people behind the counter know their craft well enough that the craft disappears into the experience. This is not a modest ambition, and we are not always going to get it right. But it is the brief we have set ourselves, and the letter is part of how we hold ourselves to it.</p>

<blockquote>Thank you for being here before we opened. We will try to deserve the early confidence.</blockquote>

<p>See you at the counter.</p>

<p><em>— The team at TEMPO House</em></p>`,
  },
];

export function getArticle(slug: string): Article | undefined {
  return ARTICLES.find((a) => a.slug === slug);
}

export function getFeaturedArticle(): Article | undefined {
  return ARTICLES.find((a) => a.featured) ?? ARTICLES[0];
}
