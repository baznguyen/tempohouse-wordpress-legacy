"use client";

import { useEffect, useRef } from "react";
import type { CSSProperties } from "react";
import Link from "next/link";
import styles from "./ParallaxHero.module.css";

const TEMPO = ["T", "E", "M", "P", "O"];
const HOUSE = ["H", "O", "U", "S", "E"];

export default function ParallaxHero() {
  const bleedRef = useRef<HTMLDivElement>(null);
  const heroRef = useRef<HTMLElement>(null);

  useEffect(() => {
    const bleed = bleedRef.current;
    if (!bleed) return;

    let rafId: number;
    const tick = () => {
      bleed.style.setProperty("--scroll-y", window.scrollY + "px");
      rafId = 0;
    };
    const onScroll = () => {
      if (!rafId) rafId = requestAnimationFrame(tick);
    };
    window.addEventListener("scroll", onScroll, { passive: true });

    // Time-based background / colour switching
    const h = new Date().getHours();
    const act = h >= 17 || h < 6 ? "evening" : h >= 11 ? "afternoon" : "morning";
    heroRef.current?.setAttribute("data-tempo-act", act);

    return () => {
      window.removeEventListener("scroll", onScroll);
      if (rafId) cancelAnimationFrame(rafId);
    };
  }, []);

  return (
    <section ref={heroRef} className={styles.hero} aria-label="Hero">
      <div className={styles.noise} aria-hidden="true" />

      <div className={styles.heroInner}>
        <p className={styles.eyebrow}>Now Open · Ho Chi Minh City</p>

        <div ref={bleedRef} className={styles.bleedWrap} aria-hidden="true">
          <span className={styles.bleedLine}>
            {TEMPO.map((char, i) => (
              <span
                key={i}
                className={styles.bleedChar}
                style={{ "--i": i } as CSSProperties}
              >
                {char}
              </span>
            ))}
          </span>
          <span className={styles.bleedLine}>
            {HOUSE.map((char, i) => (
              <span
                key={i}
                className={styles.bleedChar}
                style={{ "--i": i + 5 } as CSSProperties}
              >
                {char}
              </span>
            ))}
          </span>
        </div>

        <p className={styles.heroTagline}>
          Coffee in the morning.{" "}
          <em>Connection at night.</em>
        </p>
        <p className={styles.heroDescriptor}>
          Specialty Café &nbsp;·&nbsp; Cocktail Bar &nbsp;·&nbsp; Art Gallery &nbsp;·&nbsp; Events
        </p>
        <div className={styles.heroCtas}>
          <Link href="/reservations" className={styles.ctaPrimary}>Reserve a Table</Link>
          <Link href="/programme" className={styles.ctaSecondary}>What&apos;s On →</Link>
        </div>
      </div>

      <div className={styles.scrollHint} aria-hidden="true">
        <span />
      </div>
    </section>
  );
}
