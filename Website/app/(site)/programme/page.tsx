import type { Metadata } from "next";
import Link from "next/link";
import { EVENTS } from "./data";
import styles from "./page.module.css";

export const metadata: Metadata = {
  title: "What's On — TEMPO House",
  description:
    "Live music, gallery openings, tasting menus, and private dining at TEMPO House, 218c Pasteur, District 3, Ho Chi Minh City.",
};

const TYPE_COLORS: Record<string, string> = {
  Music:     "music",
  Exhibition:"exhibition",
  Dining:    "dining",
  Special:   "special",
};

export default function ProgrammePage() {
  const upcoming = EVENTS.filter(
    (e) => new Date(e.startDate) >= new Date("2026-01-01")
  );

  return (
    <div className={styles.page}>
      <header className={styles.pageHeader}>
        <div className="container">
          <p className={styles.eyebrow}>Programming</p>
          <h1 className={styles.pageTitle}>
            What&apos;s On
          </h1>
          <p className={styles.pageIntro}>
            Live music. Exhibition openings. Tasting menus. Events worth clearing your calendar.
          </p>
        </div>
      </header>

      <section className={styles.eventSection}>
        <div className="container">
          {upcoming.length === 0 ? (
            <p className={styles.empty}>
              The programme is taking shape. Subscribe to the TEMPO Letter to be first to know.
            </p>
          ) : (
            <div className={styles.eventGrid}>
              {upcoming.map((event) => (
                <article
                  key={event.slug}
                  className={styles.eventCard}
                  data-type={TYPE_COLORS[event.type] ?? "special"}
                >
                  {/* Frame */}
                  <Link href={`/programme/${event.slug}`} className={styles.frameLink}>
                    <div className={styles.eventFrame}>
                      <div className={styles.eventMat}>
                        <div className={styles.eventArtwork}>
                          <p className={styles.eventTypeLabel}>{event.type}</p>
                        </div>
                      </div>
                    </div>
                  </Link>

                  {/* Label */}
                  <div className={styles.eventInfo}>
                    <div>
                      <p className={styles.eventDate}>{event.date}</p>
                      <p className={styles.eventTime}>{event.time}</p>
                    </div>
                    <Link href={`/programme/${event.slug}`} className={styles.eventTitleLink}>
                      <h2 className={styles.eventTitle}>{event.title}</h2>
                    </Link>
                    <p className={styles.eventDescription}>{event.description}</p>
                    <div className={styles.eventFooter}>
                      <span className={styles.eventPrice}>{event.price}</span>
                      <Link href={`/programme/${event.slug}`} className={styles.eventCta}>
                        {event.reservationRequired ? "Reserve →" : "Details →"}
                      </Link>
                    </div>
                  </div>
                </article>
              ))}
            </div>
          )}
        </div>
      </section>

      <section className={styles.hireCta}>
        <div className="container container--narrow">
          <p className={styles.hireEyebrow}>Private Hire</p>
          <h2 className={styles.hireTitle}>Host your own event</h2>
          <p className={styles.hireBody}>
            The space is available for private dinners, brand events, gallery activations, and
            celebrations. Capacity up to 80.
          </p>
          <Link href="/events/enquiry" className={styles.hireBtn}>
            Enquire →
          </Link>
        </div>
      </section>
    </div>
  );
}
