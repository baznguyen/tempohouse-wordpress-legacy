"use client";

import { useEffect, useRef, useState, useCallback } from "react";
import type { CSSProperties } from "react";
import Link from "next/link";
import styles from "./MoodsParallax.module.css";

const FRAMES = [
  {
    key:   "cafe",
    speed:  -0.07,
    num:   "01",
    mode:  "Day",
    time:  "07:00 – 17:00",
    title: "Specialty Café",
    cta:   "Explore the Café",
    href:  "/cafe",
  },
  {
    key:   "bar",
    speed:  0.05,
    num:   "02",
    mode:  "Night",
    time:  "18:00 – 01:00",
    title: "Cocktail Bar",
    cta:   "Explore the Bar",
    href:  "/bar",
  },
  {
    key:   "gallery",
    speed:  -0.04,
    num:   "03",
    mode:  "Event",
    time:  "By programme",
    title: "Gallery & Events",
    cta:   "See the Space",
    href:  "/gallery",
  },
];

const SCROLL_PADDING = 20; // matches scroll-padding-inline-start in CSS

export default function MoodsParallax() {
  const sectionRef    = useRef<HTMLElement>(null);
  const framesWrapRef = useRef<HTMLDivElement>(null);
  const [activeIndex, setActiveIndex] = useState(0);

  // ── Parallax (desktop only) ──────────────────────
  useEffect(() => {
    const section = sectionRef.current;
    if (!section) return;

    let rafId: number;

    const tick = () => {
      const rect   = section.getBoundingClientRect();
      const offset = window.innerHeight / 2 - (rect.top + rect.height / 2);
      section.style.setProperty("--parallax", `${offset}px`);
      rafId = 0;
    };

    const onScroll = () => {
      if (!rafId) rafId = requestAnimationFrame(tick);
    };

    window.addEventListener("scroll", onScroll, { passive: true });
    tick();

    return () => {
      window.removeEventListener("scroll", onScroll);
      if (rafId) cancelAnimationFrame(rafId);
    };
  }, []);

  // ── Carousel navigation (mobile) ─────────────────

  // Scroll framesWrap so that frame[index] snaps into view.
  const scrollToFrame = useCallback((index: number) => {
    const wrap = framesWrapRef.current;
    if (!wrap) return;
    const cards = wrap.querySelectorAll<HTMLElement>("article");
    const card  = cards[index];
    if (!card) return;
    // snap point = offsetLeft minus the scroll-padding
    wrap.scrollTo({ left: card.offsetLeft - SCROLL_PADDING, behavior: "smooth" });
  }, []);

  // Track which frame is snapped by finding the card whose
  // snap point is closest to the current scrollLeft.
  const handleWrapScroll = useCallback(() => {
    const wrap = framesWrapRef.current;
    if (!wrap) return;
    const cards    = Array.from(wrap.querySelectorAll<HTMLElement>("article"));
    const scrollLeft = wrap.scrollLeft;
    let nearest = 0;
    let minDist = Infinity;
    cards.forEach((card, i) => {
      const dist = Math.abs((card.offsetLeft - SCROLL_PADDING) - scrollLeft);
      if (dist < minDist) { minDist = dist; nearest = i; }
    });
    setActiveIndex(nearest);
  }, []);

  useEffect(() => {
    const wrap = framesWrapRef.current;
    if (!wrap) return;
    wrap.addEventListener("scroll", handleWrapScroll, { passive: true });
    return () => wrap.removeEventListener("scroll", handleWrapScroll);
  }, [handleWrapScroll]);

  return (
    <section ref={sectionRef} className={styles.moods} aria-label="The space">
      <p className={styles.eyebrow}>The Space</p>

      <div className={styles.bleedText} aria-hidden="true">
        <span className={styles.bleedLine}>CURATING</span>
        <span className={styles.bleedLine}>EXPERIENCES</span>
      </div>

      <div ref={framesWrapRef} className={styles.framesWrap}>
        {FRAMES.map((frame) => (
          <article
            key={frame.key}
            className={styles.frame}
            data-frame={frame.key}
            style={{ "--speed": frame.speed } as CSSProperties}
          >
            <Link
              href={frame.href}
              className={styles.frameLink}
              aria-label={`${frame.title} — ${frame.cta}`}
            />

            <div className={styles.frameArt}>
              <div className={styles.mat}>
                <div className={styles.artwork}>
                  <span className={styles.num}>{frame.num}</span>

                  <div className={styles.titleBar}>
                    <p className={styles.labelMode}>
                      {frame.mode}
                      <span className={styles.labelSep}> · </span>
                      {frame.time}
                    </p>
                    <h3 className={styles.labelTitle}>{frame.title}</h3>
                    <span className={styles.labelCta}>{frame.cta} →</span>
                  </div>
                </div>
              </div>
            </div>
          </article>
        ))}
      </div>

      {/* Carousel navigation — hidden on desktop via CSS */}
      <nav className={styles.carouselNav} aria-label="Carousel navigation">
        <button
          className={styles.navBtn}
          onClick={() => scrollToFrame(Math.max(0, activeIndex - 1))}
          aria-label="Previous frame"
          disabled={activeIndex === 0}
        >
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
            <path d="M10 12L6 8l4-4" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
          </svg>
        </button>

        <div className={styles.dots}>
          {FRAMES.map((frame, i) => (
            <button
              key={frame.key}
              className={`${styles.dot}${i === activeIndex ? ` ${styles.dotActive}` : ""}`}
              onClick={() => scrollToFrame(i)}
              aria-label={`View ${frame.title}`}
            />
          ))}
        </div>

        <button
          className={styles.navBtn}
          onClick={() => scrollToFrame(Math.min(FRAMES.length - 1, activeIndex + 1))}
          aria-label="Next frame"
          disabled={activeIndex === FRAMES.length - 1}
        >
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
            <path d="M6 12l4-4-4-4" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
          </svg>
        </button>
      </nav>
    </section>
  );
}
