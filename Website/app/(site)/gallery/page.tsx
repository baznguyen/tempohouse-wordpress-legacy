import type { Metadata } from "next";
import Link from "next/link";
import { EXHIBITIONS, getCurrentExhibition } from "./data";
import styles from "./page.module.css";

export const metadata: Metadata = {
  title: "Gallery — TEMPO House",
  description:
    "Rotating exhibitions from local and international artists. The gallery at TEMPO House, 218c Pasteur, District 3, Ho Chi Minh City — open during café and bar hours.",
};

export default function GalleryPage() {
  const current = getCurrentExhibition();
  const past = EXHIBITIONS.filter((e) => e.status === "past");

  return (
    <div className={styles.page}>
      <header className={styles.pageHeader}>
        <div className="container">
          <p className={styles.eyebrow}>The Gallery</p>
          <h1 className={styles.pageTitle}>
            Rotating exhibitions.<br />
            <em>Open daily.</em>
          </h1>
          <p className={styles.pageIntro}>
            The gallery walls at TEMPO House change four times a year. The work is always on view
            during café and bar hours — no appointment, no admission.
          </p>
        </div>
      </header>

      {/* Current / upcoming show */}
      {current && (
        <section className={styles.currentSection} aria-label="Current exhibition">
          <div className="container">
            <p className={styles.sectionLabel}>
              {current.status === "current" ? "On now" : "Opening soon"}
            </p>
            <Link href={`/gallery/${current.slug}`} className={styles.currentCard}>
              <div className={styles.currentFrame}>
                <div className={styles.currentMat}>
                  <div className={styles.currentArtwork} aria-hidden="true">
                    <span className={styles.currentNum}>01</span>
                  </div>
                </div>
              </div>
              <div className={styles.currentInfo}>
                <div>
                  <p className={styles.exhibitionMedium}>{current.medium}</p>
                  <h2 className={styles.currentTitle}>{current.title}</h2>
                  <p className={styles.exhibitionSubtitle}>{current.subtitle}</p>
                </div>
                <p className={styles.exhibitionArtists}>
                  {current.artists.join(" · ")}
                </p>
                <p className={styles.exhibitionDates}>
                  {current.openDate} — {current.closeDate}
                </p>
                <p className={styles.currentCuratorial}>
                  {current.curatorialNote.split("\n\n")[0]}
                </p>
                <span className={styles.viewBtn}>View exhibition →</span>
              </div>
            </Link>
          </div>
        </section>
      )}

      {/* Programme statement */}
      <section className={styles.statement}>
        <div className="container container--narrow">
          <p className={styles.statementEyebrow}>The Programme</p>
          <p className={styles.statementBody}>
            TEMPO House curates four exhibitions per year. We work primarily with artists
            based in Vietnam alongside invited international artists whose practice relates
            to the conversations happening in the city. The gallery is not a commercial
            gallery in the traditional sense — selected works are available for acquisition,
            but the programme is driven by curatorial interest, not market logic.
          </p>
          <p className={styles.statementBody}>
            If you are an artist interested in exhibiting, or a collector enquiring about
            available works, write to us at{" "}
            <a href="mailto:gallery@tempohouse.com.vn" className={styles.emailLink}>
              gallery@tempohouse.com.vn
            </a>
            .
          </p>
        </div>
      </section>

      {/* Past shows */}
      {past.length > 0 && (
        <section className={styles.archiveSection}>
          <div className="container">
            <p className={styles.sectionLabel}>Archive</p>
            <div className={styles.archiveGrid}>
              {past.map((ex) => (
                <Link
                  key={ex.slug}
                  href={`/gallery/${ex.slug}`}
                  className={styles.archiveCard}
                >
                  <div className={styles.archiveFrame}>
                    <div className={styles.archiveMat}>
                      <div className={styles.archiveArtwork} aria-hidden="true" />
                    </div>
                  </div>
                  <div className={styles.archiveInfo}>
                    <p className={styles.archiveTitle}>{ex.title}</p>
                    <p className={styles.archiveMeta}>
                      {ex.artists.slice(0, 3).join(", ")}
                      {ex.artists.length > 3 ? ` + ${ex.artists.length - 3} more` : ""}
                    </p>
                    <p className={styles.archiveDates}>
                      {ex.openDate} — {ex.closeDate}
                    </p>
                  </div>
                </Link>
              ))}
            </div>
          </div>
        </section>
      )}
    </div>
  );
}
