export type EventType = "Music" | "Exhibition" | "Dining" | "Special";

export interface TempoEvent {
  slug: string;
  type: EventType;
  title: string;
  subtitle: string;
  date: string;        // display string e.g. "Friday 27 June 2026"
  startDate: string;   // ISO 8601 for schema
  endDate: string;     // ISO 8601 for schema
  time: string;        // display string e.g. "18:00 – 21:00"
  capacity: number;
  price: string;       // display string e.g. "Free entry" or "350,000 VND"
  priceAmount?: number; // numeric for schema
  priceCurrency?: string;
  description: string;
  body: string;
  reservationRequired: boolean;
  featured?: boolean;
}

export const EVENTS: TempoEvent[] = [
  {
    slug: "works-on-paper-opening-night",
    type: "Exhibition",
    title: "Works on Paper — Opening Reception",
    subtitle: "The inaugural TEMPO House gallery exhibition",
    date: "Friday 27 June 2026",
    startDate: "2026-06-27T18:00:00+07:00",
    endDate: "2026-06-27T21:00:00+07:00",
    time: "18:00 – 21:00",
    capacity: 80,
    price: "Free entry",
    description:
      "Five artists. Drawings, prints, and works on paper. An evening to mark the beginning of TEMPO House's gallery programme and the opening of our inaugural exhibition.",
    body: `<p>The first show at TEMPO House opens on the evening of Friday 27 June. <em>Works on Paper</em> brings together five artists working across drawing, printmaking, and collage — all of them based in Ho Chi Minh City and Hanoi, all working in practices we have been following closely.</p>

<p>The opening reception runs from 18:00 to 21:00. The bar will be open. The works will be on the walls through August. This is an evening for the artists, for the people who care about what is being made in this city, and for anyone who wants to be in a room where something is beginning.</p>

<p>Entry is free. No RSVP required, though we ask that you arrive between 18:00 and 20:00 to allow the evening room to breathe at its own pace.</p>

<p>The exhibition continues through 9 August, visible during all café and bar hours.</p>`,
    reservationRequired: false,
    featured: true,
  },
  {
    slug: "tempo-sessions-july",
    type: "Music",
    title: "TEMPO Sessions — Vol. 1",
    subtitle: "Monthly live music at the bar",
    date: "Saturday 19 July 2026",
    startDate: "2026-07-19T20:00:00+07:00",
    endDate: "2026-07-19T23:00:00+07:00",
    time: "20:00 – 23:00",
    capacity: 20,
    price: "Free entry",
    description:
      "Twenty seats. A musician. The kind of listening room that most venues in Saigon don't have the quiet to achieve. Monthly, at the bar.",
    body: `<p>TEMPO Sessions is a monthly live music programme at the bar. Twenty seats. A musician or small ensemble. The kind of listening environment that most live music venues in Ho Chi Minh City don't have the quiet to achieve.</p>

<p>The first edition features an artist to be announced. The format is deliberate: no stage, no amplification above the acoustic level of the room, no set break. The music happens at the bar, among the drinks, as part of the evening rather than a performance event appended to it.</p>

<p>Entry is free. Seating is first-come — the room holds twenty and we will not add chairs. Doors open at 19:00. Music begins at 20:00.</p>

<p>The next edition will be announced in the TEMPO Letter.</p>`,
    reservationRequired: false,
  },
  {
    slug: "tasting-menu-august",
    type: "Dining",
    title: "The Tasting Menu",
    subtitle: "A seasonal dining collaboration — kitchen × bar",
    date: "Saturday 16 August 2026",
    startDate: "2026-08-16T19:00:00+07:00",
    endDate: "2026-08-16T23:00:00+07:00",
    time: "19:00 – 23:00",
    capacity: 12,
    price: "1,800,000 VND per person",
    priceAmount: 1800000,
    priceCurrency: "VND",
    description:
      "Twelve guests. A single evening. A menu built around the dry-season produce of the Da Lat highlands, with a paired drinks programme by the bar team.",
    body: `<p>Twelve guests. One evening. A menu that moves through the seasons of the Da Lat highlands — built by our kitchen team around what is arriving at the market, and paired with a drinks programme by the bar.</p>

<p>This is not a formal tasting menu in the restaurant sense. There is no uniform, no amuse-bouche nomenclature. The food will be direct and seasonal; the drinks will complement rather than perform. The evening is long and unhurried.</p>

<p>The menu will be sent to guests in the week before the event. Dietary requirements can be accommodated with advance notice at the time of booking.</p>

<p>Capacity is twelve. Reservations are required and payment is taken in advance. Enquiries and booking via the link below or at the bar in person.</p>

<p><strong>1,800,000 VND per person</strong>, inclusive of the full drinks pairing.</p>`,
    reservationRequired: true,
  },
];

export function getEvent(slug: string): TempoEvent | undefined {
  return EVENTS.find((e) => e.slug === slug);
}
