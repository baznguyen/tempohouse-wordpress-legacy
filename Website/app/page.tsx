import styles from "./page.module.css";
import EmailForm from "./components/EmailForm";

function IconInstagram() {
  return (
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.4" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
      <rect x="2" y="2" width="20" height="20" rx="5" />
      <circle cx="12" cy="12" r="4" />
      <circle cx="17.5" cy="6.5" r="0.8" fill="currentColor" stroke="none" />
    </svg>
  );
}

function IconFacebook() {
  return (
    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
      <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" />
    </svg>
  );
}

function IconTikTok() {
  return (
    <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
      <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1V9.01a6.33 6.33 0 0 0-.79-.05 6.34 6.34 0 0 0-6.34 6.34 6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.33-6.34V8.69a8.25 8.25 0 0 0 4.83 1.55V6.79a4.85 4.85 0 0 1-1.06-.1z" />
    </svg>
  );
}

function IconMapPin() {
  return (
    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
      <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
      <circle cx="12" cy="10" r="3" />
    </svg>
  );
}

export default function HomePage() {
  return (
    <main className={styles.page}>

      <div className={styles.inner}>

        {/* Opening label */}
        <p className={styles.announce}>Opening Soon · Ho Chi Minh City</p>

        {/* Hero — tile circle with white logo centred inside */}
        <div className={styles.heroCircle} aria-label="TEMPO House logo">
          <img
            src="/content/brand-assets/tempo_house_logo_white_transparent.png"
            alt="TEMPO House"
            className={styles.logoOnCircle}
          />
        </div>

        {/* Divider */}
        <div className={styles.divider} role="presentation">
          <span className={styles.dividerLine} />
          <span className={styles.dividerDot} />
          <span className={styles.dividerLine} />
        </div>

        {/* Tagline */}
        <blockquote className={styles.tagline}>
          <p>Coffee in the morning.</p>
          <p>Connection at night.</p>
          <p className={styles.taglineSub}>Come together for the experience.</p>
        </blockquote>

        {/* Venue descriptor */}
        <p className={styles.descriptor}>
          Specialty Café&ensp;·&ensp;Cocktail Bar&ensp;·&ensp;Art Gallery&ensp;·&ensp;Events
          <br />
          Ho Chi Minh City, Vietnam
        </p>

        {/* Email signup */}
        <p className={styles.formLabel}>Be the first to know</p>
        <div className={styles.formWrap}>
          <EmailForm />
        </div>

      </div>

      <footer className={styles.footer}>

        <a
          href="https://maps.app.goo.gl/WJbiqzMPtCJJAF5V6"
          target="_blank"
          rel="noopener noreferrer"
          className={styles.location}
          aria-label="View on Google Maps"
        >
          <IconMapPin />
          Ho Chi Minh City
        </a>

        <nav className={styles.socials} aria-label="Social media">
          <a href="https://www.instagram.com/tempohouse.sgn" target="_blank" rel="noopener noreferrer" className={styles.socialIcon} aria-label="Instagram">
            <IconInstagram />
          </a>
          <a href="https://www.facebook.com/tempohouse.sgn" target="_blank" rel="noopener noreferrer" className={styles.socialIcon} aria-label="Facebook">
            <IconFacebook />
          </a>
          <a href="https://www.tiktok.com/@tempohouse.sgn" target="_blank" rel="noopener noreferrer" className={styles.socialIcon} aria-label="TikTok">
            <IconTikTok />
          </a>
        </nav>

        <p className={styles.copy}>© 2026 TEMPO House</p>

      </footer>

    </main>
  );
}
