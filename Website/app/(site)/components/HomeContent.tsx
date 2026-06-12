import Link from "next/link";
import ParallaxHero from "./ParallaxHero";
import MoodsParallax from "./MoodsParallax";
import EventCarousel from "./EventCarousel";
import styles from "./HomeContent.module.css";
import EmailForm from "../../components/EmailForm";

export default function HomeContent() {
  return (
    <>
      {/* ══════════════════════════════════════════
          PARALLAX HERO
      ══════════════════════════════════════════ */}
      <ParallaxHero />

      {/* ══════════════════════════════════════════
          THREE MOODS — scattered parallax frames
      ══════════════════════════════════════════ */}
      <MoodsParallax />

      {/* ══════════════════════════════════════════
          WHAT'S ON — infinite gallery carousel
      ══════════════════════════════════════════ */}
      <EventCarousel />

      {/* ══════════════════════════════════════════
          RESERVE CTA
      ══════════════════════════════════════════ */}
      <section className={styles.reserveSection} aria-label="Reservations">
        <div className="container container--narrow">
          <p className={styles.sectionEyebrow}>Dine with us</p>
          <h2 className={styles.reserveTitle}>Reserve a table</h2>
          <p className={styles.reserveBody}>
            Walk-ins always welcome. For groups of 6 or more, or for a guaranteed seat
            during the evening service, book ahead.
          </p>
          <div className={styles.reserveCtas}>
            <Link href="/reservations" className={styles.ctaPrimary}>
              Book a table
            </Link>
            <Link href="/events/enquiry" className={styles.ctaSecondary}>
              Host an event →
            </Link>
          </div>
        </div>
      </section>

      {/* ══════════════════════════════════════════
          EMAIL SIGNUP
      ══════════════════════════════════════════ */}
      <section className={styles.newsletter} id="newsletter" aria-label="Stay connected">
        <div className="container container--narrow">
          <p className={styles.sectionEyebrow}>Stay connected</p>
          <h2 className={styles.newsletterTitle}>The TEMPO letter.</h2>
          <p className={styles.newsletterBody}>
            Events, openings, menu changes, and the occasional recommendation.
            No noise — just the things worth knowing.
          </p>
          <div className={styles.formWrap}>
            <EmailForm />
          </div>
        </div>
      </section>
    </>
  );
}
