"use client";

import { useEffect, useRef, useState, useCallback } from "react";
import Link from "next/link";
import styles from "./EventCarousel.module.css";
import { useDragScroll } from "./useDragScroll";

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

const CARD_GAP = 16; // matches mobile gap in CSS

export default function EventCarousel() {
  const viewportRef = useRef<HTMLDivElement>(null);
  const [activeIndex, setActiveIndex] = useState(0);

  // Attach mouse drag-to-scroll on the viewport (mobile scroll container)
  useDragScroll(viewportRef);

  // ── Mobile carousel navigation ───────────────────

  const getCardWidth = useCallback(() => {
    const viewport = viewportRef.current;
    const firstCard = viewport?.querySelector<HTMLElement>(`.${styles.card}`);
    return firstCard?.offsetWidth ?? 0;
  }, []);

  const scrollToCard = useCallback((index: number) => {
    const viewport = viewportRef.current;
    if (!viewport) return;
    const cw = getCardWidth();
    viewport.scrollTo({ left: index * (cw + CARD_GAP), behavior: "smooth" });
  }, [getCardWidth]);

  const handleViewportScroll = useCallback(() => {
    const viewport = viewportRef.current;
    if (!viewport) return;
    const cw = getCardWidth();
    if (!cw) return;
    const idx = Math.round(viewport.scrollLeft / (cw + CARD_GAP));
    setActiveIndex(Math.max(0, Math.min(idx, EVENTS.length - 1)));
  }, [getCardWidth]);

  useEffect(() => {
    const viewport = viewportRef.current;
    if (!viewport) return;
    viewport.addEventListener("scroll", handleViewportScroll, { passive: true });
    return () => viewport.removeEventListener("scroll", handleViewportScroll);
  }, [handleViewportScroll]);

  // ── Video hover handlers ─────────────────────────

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
      <div ref={viewportRef} className={styles.viewport}>
        <div className={styles.track}>
          {TRACK_ITEMS.map((event, i) => (
            <article
              key={`${event.key}-${i}`}
              className={styles.card}
              data-interior={event.interior}
              onMouseEnter={handleCardEnter}
              onMouseLeave={handleCardLeave}
            >
              <Link
                href={event.href}
                className={styles.cardLink}
                aria-label={`${event.title} — ${event.time}`}
              />

              <div className={styles.frameArt}>
                <div className={styles.mat}>
                  <div className={styles.artwork}>

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

                    {!event.media && (
                      <span className={styles.categoryGhost}>{event.category}</span>
                    )}

                    <div className={styles.titleBar}>
                      <p className={styles.eventTitle}>{event.title}</p>
                    </div>

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

      {/* Mobile carousel nav — hidden on desktop via CSS */}
      <nav className={styles.carouselNav} aria-label="Events navigation">
        <button
          className={styles.navBtn}
          onClick={() => scrollToCard(Math.max(0, activeIndex - 1))}
          aria-label="Previous event"
          disabled={activeIndex === 0}
        >
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
            <path d="M10 12L6 8l4-4" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
          </svg>
        </button>

        <div className={styles.dots}>
          {EVENTS.map((event, i) => (
            <button
              key={event.key}
              className={`${styles.dot}${i === activeIndex ? ` ${styles.dotActive}` : ""}`}
              onClick={() => scrollToCard(i)}
              aria-label={`View ${event.title}`}
            />
          ))}
        </div>

        <button
          className={styles.navBtn}
          onClick={() => scrollToCard(Math.min(EVENTS.length - 1, activeIndex + 1))}
          aria-label="Next event"
          disabled={activeIndex === EVENTS.length - 1}
        >
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
            <path d="M6 12l4-4-4-4" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
          </svg>
        </button>
      </nav>

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
