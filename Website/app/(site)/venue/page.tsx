import type { Metadata } from "next";
import Link from "next/link";
import styles from "./page.module.css";

const SITE_URL = "https://tempohouse.com.vn";

export const metadata: Metadata = {
  title: "The Venue — 218c Pasteur Street, District 3, Ho Chi Minh City",
  description:
    "Specialty café, craft cocktail bar, and rotating art gallery at 218c Pasteur Street, District 3, Saigon. 500m from Reunification Palace. Open daily. Available for private hire.",
  alternates: { canonical: "/venue" },
  openGraph: {
    title: "The Venue — 218c Pasteur Street, District 3 | TEMPO House",
    description:
      "Specialty café by day. Cocktail bar by night. Rotating gallery always open. 218c Pasteur Street, District 3, Ho Chi Minh City — 500m from Reunification Palace.",
    url: `${SITE_URL}/venue`,
  },
};

const venueJsonLd = {
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": ["EventVenue", "CafeOrCoffeeShop", "BarOrPub", "ArtGallery"],
      "@id": `${SITE_URL}/venue#venue`,
      "name": "TEMPO House",
      "description":
        "A French-colonial shophouse on Pasteur Street, District 3, Ho Chi Minh City. Specialty café, craft cocktail bar, rotating art gallery, and private event space.",
      "url": `${SITE_URL}/venue`,
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "218c Pasteur Street, Xuân Hòa",
        "addressLocality": "Ho Chi Minh City",
        "addressRegion": "Hồ Chí Minh",
        "postalCode": "72400",
        "addressCountry": "VN",
      },
      "geo": {
        "@type": "GeoCoordinates",
        "latitude": 10.7769,
        "longitude": 106.7009,
      },
      "hasMap": "https://maps.google.com/?q=218c+Pasteur+Street+District+3+Ho+Chi+Minh+City",
      "openingHoursSpecification": [
        {
          "@type": "OpeningHoursSpecification",
          "dayOfWeek": ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"],
          "opens": "07:00",
          "closes": "17:00",
          "description": "Specialty café",
        },
        {
          "@type": "OpeningHoursSpecification",
          "dayOfWeek": ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"],
          "opens": "18:00",
          "closes": "01:00",
          "description": "Craft cocktail bar",
        },
      ],
      "maximumAttendeeCapacity": 120,
      "amenityFeature": [
        { "@type": "LocationFeatureSpecification", "name": "Art Gallery", "value": true },
        { "@type": "LocationFeatureSpecification", "name": "Private Hire", "value": true },
        { "@type": "LocationFeatureSpecification", "name": "Specialty Coffee", "value": true },
        { "@type": "LocationFeatureSpecification", "name": "Craft Cocktails", "value": true },
      ],
      "tourBookingPage": `${SITE_URL}/events/enquiry`,
    },
  ],
};

export default function VenuePage() {
  return (
    <>
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(venueJsonLd) }}
      />
      <div className={styles.page}>

        {/* ── Page header ──────────────────────────────────── */}
        <header className={styles.pageHeader}>
          <div className="container">
            <p className={styles.eyebrow}>The Venue</p>
            <h1 className={styles.pageTitle}>
              218c Pasteur Street.<br />
              <em>District 3.</em>
            </h1>
            <p className={styles.pageIntro}>
              A French-colonial shophouse on a quiet stretch of Pasteur Street — five hundred
              metres from Reunification Palace, far enough from District 1 to breathe. Specialty
              café from seven in the morning. Intimate cocktail bar from six in the evening.
              Rotating gallery, always open, no admission.
            </p>
          </div>
        </header>

        {/* ── At-a-glance strip ─────────────────────────── */}
        <div className={styles.statsStrip}>
          <div className="container">
            <div className={styles.statsGrid}>
              <div className={styles.stat}>
                <p className={styles.statTime}>07:00 – 17:00</p>
                <p className={styles.statLabel}>Specialty café · open daily</p>
              </div>
              <div className={styles.statDivider} aria-hidden="true" />
              <div className={styles.stat}>
                <p className={styles.statTime}>18:00 – 01:00</p>
                <p className={styles.statLabel}>Craft cocktail bar · open daily</p>
              </div>
              <div className={styles.statDivider} aria-hidden="true" />
              <div className={styles.stat}>
                <p className={styles.statTime}>4× a year</p>
                <p className={styles.statLabel}>Rotating exhibitions · always on view</p>
              </div>
            </div>
          </div>
        </div>

        {/* ── The space ─────────────────────────────────── */}
        <section className={styles.spaceSection} aria-labelledby="space-heading">
          <div className="container">
            <div className={styles.spaceGrid}>
              <div className={styles.spaceLeft}>
                <p className={styles.sectionEyebrow}>The Space</p>
                <h2 id="space-heading" className={styles.sectionTitle}>
                  A shophouse on<br />
                  <em>Pasteur Street.</em>
                </h2>
              </div>
              <div className={styles.spaceRight}>
                <p className={styles.bodyText}>
                  TEMPO House is a French-colonial shophouse in the part of District 3 that most
                  of Saigon doesn&apos;t think to visit. The street is quieter here. The building
                  was designed to let in light from both ends — the same light that moves across
                  the space from the first coffee of the morning to the last cocktail at night.
                </p>
                <p className={styles.bodyText}>
                  One address. Three uses. The pace doesn&apos;t change between them.
                </p>
                <address className={styles.addressBlock}>
                  218c Pasteur Street<br />
                  Xuân Hòa, District 3<br />
                  Ho Chi Minh City, Vietnam
                </address>
              </div>
            </div>
          </div>
        </section>

        {/* ── Floor plan ───────────────────────────────── */}
        <section className={styles.planSection} aria-labelledby="plan-heading">
          <div className="container">
            <div className={styles.planHeader}>
              <p className={styles.sectionEyebrow}>The Building</p>
              <h2 id="plan-heading" className={styles.sectionTitle}>
                Ground floor.<br />
                <em>To scale.</em>
              </h2>
            </div>
            <div className={styles.planWrapper}>
              <img
                src="/floor-plans/ground-floor.svg"
                alt="TEMPO House ground floor plan — bar, bathroom, back of house, storage and stairwell, east garden and south terrace to Pasteur Street. Scale 1:100."
                className={styles.planImage}
                width={1040}
                height={931}
              />
              <p className={styles.planCaption}>
                Ground floor · 1:100 · 218c Pasteur Street · Indicative, not to survey accuracy
              </p>
            </div>
          </div>
        </section>

        {/* ── Day / Night split ─────────────────────────── */}
        <section className={styles.rhythmSection} aria-label="Day and night at TEMPO House">
          <div className={styles.rhythmDay} data-tempo="day">
            <div className={styles.rhythmPane}>
              <p className={styles.rhythmEyebrow}>07:00 – 17:00</p>
              <h2 className={styles.rhythmTitle}>The café.</h2>
              <p className={styles.rhythmBody}>
                Specialty coffee in a space that knows how to hold a morning. Single-origin
                filter, slow bar, a rotation that follows the season. The kind of place where
                the second coffee is better than the first — because you stopped rushing.
              </p>
              <Link href="/cafe" className={styles.rhythmLink}>
                The café →
              </Link>
            </div>
          </div>
          <div className={styles.rhythmNight} data-tempo="night">
            <div className={styles.rhythmPane}>
              <p className={styles.rhythmEyebrow}>18:00 – 01:00</p>
              <h2 className={styles.rhythmTitle}>The bar.</h2>
              <p className={styles.rhythmBody}>
                The same address. Different company. Craft cocktails built around Vietnamese
                ingredients, seasonal spirits, and combinations that don&apos;t make sense until
                you taste them. The morning crowd never see this version of the room.
              </p>
              <Link href="/bar" className={styles.rhythmLink}>
                The bar →
              </Link>
            </div>
          </div>
        </section>

        {/* ── The gallery ──────────────────────────────── */}
        <section className={styles.gallerySection} aria-labelledby="gallery-heading">
          <div className="container">
            <div className={styles.galleryGrid}>
              <div className={styles.galleryText}>
                <p className={styles.sectionEyebrow}>The Gallery</p>
                <h2 id="gallery-heading" className={styles.sectionTitle}>
                  The art changes.<br />
                  <em>The room doesn&apos;t.</em>
                </h2>
                <p className={styles.bodyText}>
                  The gallery at TEMPO House rotates four times a year. We work primarily with
                  artists based in Vietnam — painting, photography, installation, whatever the
                  programme calls for. The work is always on view during café and bar hours.
                  No admission. No appointment.
                </p>
                <p className={styles.bodyText}>
                  You don&apos;t have to be here for the art. But you will notice it.
                </p>
                <Link href="/gallery" className={styles.galleryLink}>
                  View current exhibition →
                </Link>
              </div>
              <div className={styles.galleryVisual} aria-hidden="true">
                <div className={styles.galleryFrame}>
                  <div className={styles.galleryMat}>
                    <div className={styles.galleryArtwork} />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        {/* ── Private events ──────────────────────────── */}
        <section className={styles.eventsSection} aria-labelledby="events-heading">
          <div className="container">
            <div className={styles.eventsGrid}>
              <div className={styles.eventsText}>
                <p className={styles.sectionEyebrow}>Private Events &amp; Venue Hire</p>
                <h2 id="events-heading" className={styles.eventsTitle}>
                  The room,<br /><em>exclusively yours.</em>
                </h2>
                <p className={styles.bodyText}>
                  TEMPO House is available for exclusive hire. Intimate gatherings or full
                  buyouts — the space suits anything that doesn&apos;t belong in a hotel ballroom.
                </p>
                <p className={styles.bodyText}>
                  We don&apos;t offer packages. We offer the space and the team. Tell us what you need.
                </p>
                <Link href="/events/enquiry" className={styles.eventsBtn}>
                  Make an enquiry →
                </Link>
              </div>
              <ul className={styles.eventsList} aria-label="Event types">
                <li className={styles.eventsListItem}>Corporate dinners &amp; client events</li>
                <li className={styles.eventsListItem}>Product launches &amp; brand activations</li>
                <li className={styles.eventsListItem}>Editorial &amp; content shoots</li>
                <li className={styles.eventsListItem}>Private receptions &amp; celebrations</li>
                <li className={styles.eventsListItem}>Supper clubs &amp; cultural evenings</li>
                <li className={styles.eventsListItem}>Intimate performances &amp; screenings</li>
              </ul>
            </div>
          </div>
        </section>

        {/* ── Find us ──────────────────────────────────── */}
        <section className={styles.findSection} aria-labelledby="find-heading">
          <div className="container">
            <div className={styles.findGrid}>

              <div className={styles.findLocation}>
                <p className={styles.findEyebrow}>Find Us</p>
                <h2 id="find-heading" className={styles.findTitle}>218c Pasteur Street</h2>
                <address className={styles.findAddress}>
                  Xuân Hòa, District 3<br />
                  Ho Chi Minh City 72400<br />
                  Vietnam
                </address>
                <p className={styles.findNote}>
                  Walking distance from Reunification Palace (Dinh Thống Nhất).
                  Ten minutes from central District 1.
                </p>
                <a
                  href="https://maps.google.com/?q=218c+Pasteur+Street+District+3+Ho+Chi+Minh+City"
                  className={styles.findMapLink}
                  target="_blank"
                  rel="noopener noreferrer"
                >
                  Open in Google Maps →
                </a>
              </div>

              <div className={styles.findHours}>
                <p className={styles.findEyebrow}>Hours</p>
                <div className={styles.hoursList}>
                  <div className={styles.hoursRow}>
                    <span className={styles.hoursLabel}>Café</span>
                    <span className={styles.hoursValue}>Mon – Sun · 07:00 – 17:00</span>
                  </div>
                  <div className={styles.hoursRow}>
                    <span className={styles.hoursLabel}>Bar</span>
                    <span className={styles.hoursValue}>Mon – Sun · 18:00 – 01:00</span>
                  </div>
                  <div className={styles.hoursRow}>
                    <span className={styles.hoursLabel}>Gallery</span>
                    <span className={styles.hoursValue}>Open during café &amp; bar hours · No admission</span>
                  </div>
                </div>
                <a href="mailto:hello@tempohouse.com.vn" className={styles.findEmail}>
                  hello@tempohouse.com.vn
                </a>
              </div>

              <div className={styles.findActions}>
                <p className={styles.findEyebrow}>Plan Your Visit</p>
                <Link href="/reservations" className={styles.btnPrimary}>
                  Reserve a table
                </Link>
                <Link href="/events/enquiry" className={styles.btnSecondary}>
                  Private events enquiry
                </Link>
              </div>

            </div>
          </div>
        </section>

      </div>
    </>
  );
}
