import type { Metadata } from "next";
import Link from "next/link";
import { notFound } from "next/navigation";
import { EVENTS, getEvent } from "../data";
import styles from "./page.module.css";

interface Props {
  params: Promise<{ slug: string }>;
}

export async function generateStaticParams() {
  return EVENTS.map((e) => ({ slug: e.slug }));
}

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { slug } = await params;
  const event = getEvent(slug);
  if (!event) return {};
  return {
    title: `${event.title} — TEMPO House`,
    description: event.description,
  };
}

export default async function EventPage({ params }: Props) {
  const { slug } = await params;
  const event = getEvent(slug);
  if (!event) notFound();

  const jsonLd = {
    "@context": "https://schema.org",
    "@type": "Event",
    name: event.title,
    description: event.description,
    startDate: event.startDate,
    endDate: event.endDate,
    eventStatus: "https://schema.org/EventScheduled",
    eventAttendanceMode: "https://schema.org/OfflineEventAttendanceMode",
    location: {
      "@type": "Place",
      name: "TEMPO House",
      address: {
        "@type": "PostalAddress",
        streetAddress: "218c Pasteur",
        addressLocality: "District 3",
        addressRegion: "Ho Chi Minh City",
        postalCode: "72400",
        addressCountry: "VN",
      },
    },
    organizer: {
      "@type": "Organization",
      name: "TEMPO House",
      url: "https://tempohouse.com.vn",
    },
    ...(event.priceAmount !== undefined && {
      offers: {
        "@type": "Offer",
        price: event.priceAmount,
        priceCurrency: event.priceCurrency ?? "VND",
        availability: "https://schema.org/InStock",
        url: `https://tempohouse.com.vn/programme/${event.slug}`,
      },
    }),
  };

  return (
    <>
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(jsonLd) }}
      />

      <div className={styles.page}>
        {/* Header */}
        <header className={styles.header}>
          <div className="container container--narrow">
            <Link href="/programme" className={styles.backLink}>
              ← What&apos;s On
            </Link>
            <p className={styles.eventType}>{event.type}</p>
            <h1 className={styles.title}>{event.title}</h1>
            {event.subtitle && (
              <p className={styles.subtitle}>{event.subtitle}</p>
            )}
          </div>
        </header>

        {/* Frame artwork */}
        <div className={styles.artworkWrap} aria-hidden="true">
          <div className="container">
            <div className={styles.artworkFrame} data-type={event.type.toLowerCase()}>
              <div className={styles.artworkMat}>
                <div className={styles.artworkField} />
              </div>
            </div>
          </div>
        </div>

        {/* Details + body */}
        <div className={styles.content}>
          <div className="container">
            <div className={styles.layout}>

              {/* Body copy */}
              <div
                className={styles.body}
                dangerouslySetInnerHTML={{ __html: event.body }}
              />

              {/* Sidebar */}
              <aside className={styles.sidebar}>
                <div className={styles.detailCard}>
                  <div className={styles.detailRow}>
                    <p className={styles.detailLabel}>Date</p>
                    <p className={styles.detailValue}>{event.date}</p>
                  </div>
                  <div className={styles.detailRow}>
                    <p className={styles.detailLabel}>Time</p>
                    <p className={styles.detailValue}>{event.time}</p>
                  </div>
                  <div className={styles.detailRow}>
                    <p className={styles.detailLabel}>Entry</p>
                    <p className={styles.detailValue}>{event.price}</p>
                  </div>
                  <div className={styles.detailRow}>
                    <p className={styles.detailLabel}>Capacity</p>
                    <p className={styles.detailValue}>{event.capacity} guests</p>
                  </div>
                  <div className={styles.detailRow}>
                    <p className={styles.detailLabel}>Location</p>
                    <p className={styles.detailValue}>
                      TEMPO House<br />
                      218c Pasteur, District 3<br />
                      Ho Chi Minh City
                    </p>
                  </div>

                  <Link
                    href={event.reservationRequired ? "/reservations" : "/#newsletter"}
                    className={styles.reserveBtn}
                  >
                    {event.reservationRequired ? "Reserve a seat" : "Subscribe for updates"}
                  </Link>
                </div>
              </aside>
            </div>
          </div>
        </div>
      </div>
    </>
  );
}
