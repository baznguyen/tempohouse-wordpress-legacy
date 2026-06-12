"use client";

import Link from "next/link";
import styles from "./EventCarousel.module.css";

export interface EventMedia {
  type: "image" | "video";
  src: string;
  poster?: string;
  alt?: string;
}

const EVENTS = [
  {
    key:      "sessions",
    interior: "dark" as const,
    category: "Live Music",
    title:    "TEMPO Sessions",
    month:    "Monthly",
    time:     "20:00 – 23:00",
    href:     "/#newsletter",
    media:    null as EventMedia | null,
  },
  {
    key:      "exhibition",
    interior: "sand" as const,
    category: "Exhibition",
    title:    "Gallery Opening",
    month:    "Rotating",
    time:     "By programme",
    href:     "/#newsletter",
    media:    null as EventMedia | null,
  },
  {
    key:      "dining",
    interior: "cream" as const,
    category: "Private Dining",
    title:    "Tasting Menu",
    month:    "Weekly",
    time:     "19:00 – 22:00",
    href:     "/events/enquiry",
    media:    null as EventMedia | null,
  },
];

// Duplicate once — seamless infinite loop via translateX(-50%)
const TRACK_ITEMS = [...EVENTS, ...EVENTS];

export default function EventCarousel() {
  const handleCardEnter = (e: React.MouseEvent<HTMLElement>) => {
    const video = e.currentTarget.querySelector("video");
    if (video) video.play().catch(() => {});
  };

  const handleCardLeave = (e: React.MouseEvent<HTMLElement>) => {
    const video = e.currentTarget.querySelector("video");
    if (video) {
      video.pause();
      video.currentTime = 0;
    }
  };

  return (
    <section className={styles.section} aria-label="What's on">
      <div className="container">
        <header className={styles.header}>
          <p className={styles.eyebrow}>Programming</p>
          <h2 className={styles.title}>What&apos;s On</h2>
        </header>
      </div>

      {/* Infinite gallery wall ── frames scroll left */}
      <div className={styles.viewport}>
        <div className={styles.track}>
          {TRACK_ITEMS.map((event, i) => (
            <article
              key={`${event.key}-${i}`}
              className={styles.card}
              data-interior={event.interior}
              onMouseEnter={handleCardEnter}
              onMouseLeave={handleCardLeave}
            >
              {/* Full-card click target */}
              <Link
                href={event.href}
                className={styles.cardLink}
                aria-label={`${event.title} — ${event.time}`}
              />

              <div className={styles.frameArt}>
                <div className={styles.mat}>
                  <div className={styles.artwork}>

                    {/* Image or video thumbnail */}
                    {event.media && (
                      <div className={styles.mediaLayer}>
                        {event.media.type === "video" ? (
                          <video
                            className={styles.media}
                            src={event.media.src}
                            poster={event.media.poster}
                            muted
                            loop
                            playsInline
                            preload="none"
                          />
                        ) : (
                          <img
                            className={styles.media}
                            src={event.media.src}
                            alt={event.media.alt ?? event.title}
                          />
                        )}
                      </div>
                    )}

                    {/* Ghost category text when no media */}
                    {!event.media && (
                      <span className={styles.categoryGhost}>{event.category}</span>
                    )}

                    {/* Event title — always visible at bottom */}
                    <div className={styles.titleBar}>
                      <p className={styles.eventTitle}>{event.title}</p>
                    </div>

                    {/* Date / time — slides in on hover at top */}
                    <div className={styles.dateReveal}>
                      <span className={styles.dateMonth}>{event.month}</span>
                      <span className={styles.dateTime}>{event.time}</span>
                    </div>

                  </div>
                </div>
              </div>
            </article>
          ))}
        </div>
      </div>

      <div className="container">
        <div className={styles.footer}>
          <p className={styles.footerNote}>
            The programme is taking shape. Subscribe to be first to know.
          </p>
          <Link href="/programme" className={styles.footerCta}>
            See all events →
          </Link>
        </div>
      </div>
    </section>
  );
}
