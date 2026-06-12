"use client";

import { useEffect, useRef, useState, useCallback } from "react";
import type { CSSProperties } from "react";
import Link from "next/link";
import styles from "./MoodsParallax.module.css";
import { useDragScroll } from "./useDragScroll";

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

const CARD_GAP = 16; // matches gap in mobile CSS

export default function MoodsParallax() {
  const sectionRef    = useRef<HTMLElement>(null);
  const framesWrapRef = useRef<HTMLDivElement>(null);
  const [activeIndex, setActiveIndex] = useState(0);

  // Attach mouse drag-to-scroll on the carousel container
  useDragScroll(framesWrapRef);

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

  // Uses card offsetWidth + fixed gap to compute scroll positions.
  // Avoids relying on offsetLeft, which is unreliable for statically
  // positioned elements inside a static-positioned scroll container.
  const getCardWidth = useCallback(() => {
    const wrap = framesWrapRef.current;
    const first = wrap?.querySelector<HTMLElement>("article");
    return first?.offsetWidth ?? 0;
  }, []);

  const scrollToFrame = useCallback((index: number) => {
    const wrap = framesWrapRef.current;
    if (!wrap) return;
    const cw = getCardWidth();
    wrap.scrollTo({ left: index * (cw + CARD_GAP), behavior: "smooth" });
  }, [getCardWidth]);

  const handleWrapScroll = useCallback(() => {
    const wrap = framesWrapRef.current;
    if (!wrap) return;
    const cw = getCardWidth();
    if (!cw) return;
    const idx = Math.round(wrap.scrollLeft / (cw + CARD_GAP));
    setActiveIndex(Math.max(0, Math.min(idx, FRAMES.length - 1)));
  }, [getCardWidth]);

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
      <nav className={styles.carouselNav} aria-label="Space carousel navigation">
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
