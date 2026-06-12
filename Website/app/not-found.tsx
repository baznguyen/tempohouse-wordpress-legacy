import Link from "next/link";
import styles from "./not-found.module.css";

function MuralIllustration() {
  return (
    <svg
      className={styles.mural}
      viewBox="0 0 480 210"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
      aria-hidden="true"
    >
      {/* ── CORNER FRAME MARKS ───────────────────────────── */}
      <path d="M 14 36 L 14 14 L 36 14" stroke="currentColor" strokeWidth="0.9" opacity="0.38" />
      <path d="M 466 36 L 466 14 L 444 14" stroke="currentColor" strokeWidth="0.9" opacity="0.38" />
      <path d="M 14 174 L 14 196 L 36 196" stroke="currentColor" strokeWidth="0.9" opacity="0.38" />
      <path d="M 466 174 L 466 196 L 444 196" stroke="currentColor" strokeWidth="0.9" opacity="0.38" />

      {/* ── COFFEE CUP (left) ────────────────────────────── */}

      {/* Saucer outer */}
      <ellipse cx="95" cy="168" rx="52" ry="9" stroke="currentColor" strokeWidth="1.2" />
      {/* Saucer inner indent */}
      <ellipse cx="95" cy="167" rx="31" ry="5" stroke="currentColor" strokeWidth="0.75" />

      {/* Cup body — gently tapered, wider at top */}
      <path
        d="M 57 120 C 57 113 60 112 64 112 L 126 112 C 130 112 133 113 133 120 L 130 161 C 130 167 127 169 123 169 L 67 169 C 63 169 60 167 60 161 Z"
        stroke="currentColor" strokeWidth="1.5"
      />

      {/* Handle — elegant C-curve on right */}
      <path
        d="M 133 131 C 162 131 162 161 133 161"
        stroke="currentColor" strokeWidth="1.5"
      />

      {/* Cup opening ellipse */}
      <ellipse cx="95" cy="115" rx="33" ry="7" stroke="currentColor" strokeWidth="0.85" />

      {/* Latte-art hint — gentle arc on coffee surface */}
      <path
        d="M 85 114 Q 95 110 105 114"
        stroke="currentColor" strokeWidth="0.7"
        strokeLinecap="round"
      />

      {/* Steam wisps — three gentle S-curves */}
      <path d="M 74 106 C 69 96 78 86 73 76"  stroke="currentColor" strokeWidth="1.3" strokeLinecap="round" />
      <path d="M 95 103 C 90 93 99 83 94 73"  stroke="currentColor" strokeWidth="1.3" strokeLinecap="round" />
      <path d="M 116 106 C 111 96 120 86 115 76" stroke="currentColor" strokeWidth="1.3" strokeLinecap="round" />

      {/* Coffee beans scattered — left of saucer */}
      <ellipse cx="38" cy="165" rx="7.5" ry="4.5" transform="rotate(-28 38 165)" stroke="currentColor" strokeWidth="0.9" />
      <path d="M 35 162 Q 38 165 41 168" stroke="currentColor" strokeWidth="0.65" strokeLinecap="round" />

      {/* Coffee beans scattered — right of saucer */}
      <ellipse cx="156" cy="163" rx="7.5" ry="4.5" transform="rotate(22 156 163)" stroke="currentColor" strokeWidth="0.9" />
      <path d="M 153 160 Q 156 163 159 166" stroke="currentColor" strokeWidth="0.65" strokeLinecap="round" />

      {/* Small ambient plus/asterisk marks */}
      <path d="M 30 118 L 30 126 M 26 122 L 34 122" stroke="currentColor" strokeWidth="0.8" opacity="0.35" strokeLinecap="round" />
      <path d="M 166 100 L 166 106 M 163 103 L 169 103" stroke="currentColor" strokeWidth="0.75" opacity="0.3" strokeLinecap="round" />


      {/* ── BOTANICAL CENTER ─────────────────────────────── */}

      {/* Main stem */}
      <path d="M 240 188 C 240 168 238 148 240 76" stroke="currentColor" strokeWidth="1.1" strokeLinecap="round" />

      {/* Lower-left leaf */}
      <path
        d="M 240 155 C 213 143 202 123 209 107 C 222 118 238 137 240 155 Z"
        stroke="currentColor" strokeWidth="0.95"
        fill="currentColor" fillOpacity="0.10"
      />

      {/* Lower-right leaf */}
      <path
        d="M 240 155 C 267 143 278 123 271 107 C 258 118 242 137 240 155 Z"
        stroke="currentColor" strokeWidth="0.95"
        fill="currentColor" fillOpacity="0.10"
      />

      {/* Upper-left leaf */}
      <path
        d="M 240 120 C 217 110 209 93 215 79 C 228 88 240 105 240 120 Z"
        stroke="currentColor" strokeWidth="0.95"
        fill="currentColor" fillOpacity="0.10"
      />

      {/* Upper-right leaf */}
      <path
        d="M 240 120 C 263 110 271 93 265 79 C 252 88 240 105 240 120 Z"
        stroke="currentColor" strokeWidth="0.95"
        fill="currentColor" fillOpacity="0.10"
      />

      {/* Top bud — outer ring + filled centre */}
      <circle cx="240" cy="72" r="6.5" stroke="currentColor" strokeWidth="1" fill="currentColor" fillOpacity="0.14" />
      <circle cx="240" cy="72" r="3"   fill="currentColor" fillOpacity="0.52" />

      {/* Small ambient dots flanking the botanical */}
      <circle cx="196" cy="100" r="2.2"  fill="currentColor" fillOpacity="0.30" />
      <circle cx="204" cy="82"  r="1.3"  fill="currentColor" fillOpacity="0.22" />
      <circle cx="185" cy="124" r="1.3"  fill="currentColor" fillOpacity="0.20" />
      <circle cx="284" cy="100" r="2.2"  fill="currentColor" fillOpacity="0.30" />
      <circle cx="276" cy="80"  r="1.3"  fill="currentColor" fillOpacity="0.22" />
      <circle cx="293" cy="122" r="1.3"  fill="currentColor" fillOpacity="0.20" />
      <circle cx="218" cy="66"  r="1"    fill="currentColor" fillOpacity="0.20" />
      <circle cx="262" cy="66"  r="1"    fill="currentColor" fillOpacity="0.20" />

      {/* Small star/asterisk accents near botanical */}
      <path d="M 196 140 L 196 148 M 192 144 L 200 144 M 194 142 L 198 146 M 198 142 L 194 146"
        stroke="currentColor" strokeWidth="0.7" opacity="0.28" strokeLinecap="round" />
      <path d="M 284 138 L 284 146 M 280 142 L 288 142 M 282 140 L 286 144 M 286 140 L 282 144"
        stroke="currentColor" strokeWidth="0.7" opacity="0.28" strokeLinecap="round" />


      {/* ── COCKTAIL GLASS (right) ───────────────────────── */}

      {/* Foot / base */}
      <path d="M 352 178 L 418 178" stroke="currentColor" strokeWidth="2.1" strokeLinecap="round" />

      {/* Stem */}
      <path d="M 385 178 L 385 132" stroke="currentColor" strokeWidth="1.3" strokeLinecap="round" />

      {/* Martini bowl — V-shape */}
      <path d="M 337 88 L 385 132 L 433 88" stroke="currentColor" strokeWidth="1.5" strokeLinejoin="round" />

      {/* Rim arc */}
      <path d="M 337 88 Q 385 81 433 88" stroke="currentColor" strokeWidth="1.3" />

      {/* Liquid surface fill */}
      <path d="M 338 89 Q 385 82 432 89 L 433 95 Q 385 88 337 95 Z"
        fill="currentColor" fillOpacity="0.09"
      />

      {/* Citrus twist garnish on the rim */}
      <path
        d="M 417 85 C 424 77 434 71 438 63 C 442 55 437 49 431 51 C 425 53 424 62 430 65"
        stroke="currentColor" strokeWidth="1.2"
        strokeLinecap="round"
      />

      {/* Olive/sphere on a cocktail pick inside glass */}
      <line x1="360" y1="110" x2="378" y2="92" stroke="currentColor" strokeWidth="0.9" strokeLinecap="round" />
      <circle cx="359" cy="111" r="4.5" stroke="currentColor" strokeWidth="0.9" fill="currentColor" fillOpacity="0.15" />

      {/* Bubbles / carbonation dots */}
      <circle cx="368" cy="125" r="1.8" fill="currentColor" fillOpacity="0.24" />
      <circle cx="382" cy="133" r="2.3" fill="currentColor" fillOpacity="0.22" />
      <circle cx="396" cy="122" r="1.6" fill="currentColor" fillOpacity="0.20" />

      {/* Ice / geometric accent */}
      <path d="M 406 112 L 416 102 L 426 112 L 416 122 Z"
        stroke="currentColor" strokeWidth="0.85"
        fill="currentColor" fillOpacity="0.07"
      />

      {/* Ambient marks to the right of glass */}
      <path d="M 450 118 L 450 126 M 446 122 L 454 122" stroke="currentColor" strokeWidth="0.8" opacity="0.35" strokeLinecap="round" />
      <path d="M 316 108 L 316 114 M 313 111 L 319 111" stroke="currentColor" strokeWidth="0.75" opacity="0.3" strokeLinecap="round" />
    </svg>
  );
}

export default function NotFound() {
  return (
    <main className={styles.page}>
      <div className={styles.inner}>

        {/* Mural artwork */}
        <div className={styles.artwork}>
          <MuralIllustration />
        </div>

        {/* 404 label — Voice B */}
        <p className={styles.code}>404</p>

        {/* Divider */}
        <div className={styles.divider} role="presentation">
          <span className={styles.dividerLine} />
          <span className={styles.dividerDot} />
          <span className={styles.dividerLine} />
        </div>

        {/* Main heading — Voice A */}
        <blockquote className={styles.heading}>
          The room shifted.<br />
          This page didn&rsquo;t follow.
        </blockquote>

        {/* Body copy */}
        <p className={styles.body}>
          You&rsquo;ve wandered somewhere quiet — nothing&rsquo;s here, not yet.
          <br />
          Head back, or reach out if you&rsquo;re looking for something.
        </p>

        {/* Primary + secondary actions */}
        <div className={styles.actions}>
          <Link href="/" className={styles.btnHome}>
            Back to TEMPO
          </Link>
          <a href="mailto:info@tempohouse.com.vn" className={styles.btnEmail}>
            info@tempohouse.com.vn
          </a>
        </div>

        {/* Enquiry CTA */}
        <div className={styles.enquiry}>
          <p className={styles.enquiryLabel}>
            Hosting an event or seeking a creative space?
          </p>
          <a href="mailto:info@tempohouse.com.vn" className={styles.enquiryLink}>
            Get in Touch
          </a>
        </div>

      </div>

      <footer className={styles.footer}>
        <p className={styles.descriptor}>
          Specialty Café&ensp;·&ensp;Cocktail Bar&ensp;·&ensp;Art Gallery&ensp;·&ensp;Events
        </p>
        <p className={styles.copy}>© 2026 TEMPO House</p>
      </footer>
    </main>
  );
}
