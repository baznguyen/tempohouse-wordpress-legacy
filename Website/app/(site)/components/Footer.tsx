import Link from "next/link";
import styles from "./Footer.module.css";

const NAV_DISCOVER = [
  { label: "Specialty Café",  href: "/cafe" },
  { label: "Cocktail Bar",    href: "/bar" },
  { label: "Gallery",         href: "/gallery" },
  { label: "Private Events",  href: "/events" },
  { label: "Contact",         href: "/contact" },
];

const NAV_PROGRAMME = [
  { label: "What's On",              href: "/programme" },
  { label: "Works on Paper Opening", href: "/programme/works-on-paper-opening-night" },
  { label: "TEMPO Sessions",         href: "/programme/tempo-sessions-july" },
  { label: "The Tasting Menu",       href: "/programme/tasting-menu-august" },
  { label: "Private Hire",           href: "/events/enquiry" },
];

const NAV_GALLERY = [
  { label: "The Gallery",       href: "/gallery" },
  { label: "Works on Paper",   href: "/gallery/works-on-paper" },
  { label: "Artist Programme",  href: "/gallery" },
];

const NAV_JOURNAL = [
  { label: "The Journal",            href: "/journal" },
  { label: "Coffee Guide HCMC",     href: "/journal/specialty-coffee-guide-ho-chi-minh-city" },
  { label: "Why District 3",        href: "/journal/why-district-3" },
  { label: "Building the Bar",      href: "/journal/building-the-bar" },
  { label: "The TEMPO Letter",      href: "/journal/tempo-letter-vol-1" },
];

export default function Footer() {
  return (
    <footer className={styles.footer}>

      {/* ── Statement band — colophon opening ── */}
      <div className={styles.statement}>
        <div className={styles.statementInner}>
          <p className={styles.statementText}>
            A place that treats the everyday
            <br />
            <em>with the same quiet reverence as art.</em>
          </p>
          <div className={styles.statementMeta}>
            <span className={styles.statementBrand}>TEMPO HOUSE</span>
            <span className={styles.statementDivider} aria-hidden="true">·</span>
            <span>EST. 2026</span>
            <span className={styles.statementDivider} aria-hidden="true">·</span>
            <span>HỒ CHÍ MINH</span>
          </div>
        </div>
      </div>

      {/* ── Navigation grid ── */}
      <div className={styles.navGrid}>

        {/* Col 1 — Discover */}
        <nav className={styles.col} aria-label="Discover">
          <p className={styles.colHead}>Discover</p>
          <ul className={styles.colLinks} role="list">
            {NAV_DISCOVER.map((l) => (
              <li key={l.href}>
                <Link href={l.href} className={styles.colLink}>{l.label}</Link>
              </li>
            ))}
          </ul>
        </nav>

        {/* Col 2 — Programme */}
        <nav className={styles.col} aria-label="Programme">
          <p className={styles.colHead}>Programme</p>
          <ul className={styles.colLinks} role="list">
            {NAV_PROGRAMME.map((l) => (
              <li key={l.href}>
                <Link href={l.href} className={styles.colLink}>{l.label}</Link>
              </li>
            ))}
          </ul>
        </nav>

        {/* Col 3 — Gallery + Journal */}
        <nav className={styles.col} aria-label="Gallery and Journal">
          <p className={styles.colHead}>Gallery</p>
          <ul className={styles.colLinks} role="list">
            {NAV_GALLERY.map((l) => (
              <li key={l.href}>
                <Link href={l.href} className={styles.colLink}>{l.label}</Link>
              </li>
            ))}
          </ul>

          <p className={`${styles.colHead} ${styles.colHeadSpaced}`}>Journal</p>
          <ul className={styles.colLinks} role="list">
            {NAV_JOURNAL.map((l) => (
              <li key={l.href}>
                <Link href={l.href} className={styles.colLink}>{l.label}</Link>
              </li>
            ))}
          </ul>
        </nav>

        {/* Col 4 — Visit */}
        <div className={styles.col}>
          <p className={styles.colHead}>Visit</p>

          <address className={styles.address}>
            <p>218c Pasteur</p>
            <p>Xuân Hòa, Quận 3</p>
            <p>Hồ Chí Minh City</p>
            <p>Vietnam</p>
          </address>

          <div className={styles.hours}>
            <div className={styles.hoursRow}>
              <span className={styles.hoursMode}>Café</span>
              <span className={styles.hoursTime}>07:00 – 17:00</span>
            </div>
            <div className={styles.hoursRow}>
              <span className={styles.hoursMode}>Bar</span>
              <span className={styles.hoursTime}>18:00 – 01:00</span>
            </div>
          </div>

          <div className={styles.connect}>
            <p className={styles.colHead}>Connect</p>
            <ul className={styles.colLinks} role="list">
              <li>
                <a
                  href="https://www.instagram.com/tempohouse.sgn"
                  target="_blank"
                  rel="noopener noreferrer"
                  className={styles.colLink}
                >
                  @tempohouse.sgn
                </a>
              </li>
              <li>
                <a href="mailto:hello@tempohouse.com.vn" className={styles.colLink}>
                  hello@tempohouse.com.vn
                </a>
              </li>
            </ul>
          </div>
        </div>

        {/* Col 5 — Newsletter + Reservations */}
        <div className={styles.col}>
          <p className={styles.colHead}>Stay in the loop</p>
          <p className={styles.newsletterSub}>
            Events, openings, and what's happening at TEMPO.
            The letter goes out when there's something worth saying.
          </p>
          <Link href="/#newsletter" className={styles.newsletterBtn}>
            Subscribe to the TEMPO Letter →
          </Link>

          <div className={styles.reserveBlock}>
            <Link href="/reservations" className={styles.reserveBtn}>
              Reserve a Table
            </Link>
          </div>
        </div>
      </div>

      {/* ── Colophon bottom strip ── */}
      <div className={styles.colophon}>
        <div className={styles.colophonInner}>
          <p className={styles.copy}>
            © {new Date().getFullYear()} TEMPO House. All rights reserved.
          </p>
          <p className={styles.colophonDescriptor}>
            Specialty Café &nbsp;·&nbsp; Cocktail Bar &nbsp;·&nbsp; Art Gallery &nbsp;·&nbsp; Events
          </p>
          <div className={styles.legalLinks}>
            <Link href="/privacy-policy" className={styles.legalLink}>Privacy Policy</Link>
          </div>
        </div>
      </div>

    </footer>
  );
}
