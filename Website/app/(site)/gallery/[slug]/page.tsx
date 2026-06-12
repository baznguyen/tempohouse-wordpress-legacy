import type { Metadata } from "next";
import Link from "next/link";
import { notFound } from "next/navigation";
import { EXHIBITIONS, getExhibition } from "../data";
import styles from "./page.module.css";

interface Props {
  params: Promise<{ slug: string }>;
}

export async function generateStaticParams() {
  return EXHIBITIONS.map((e) => ({ slug: e.slug }));
}

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { slug } = await params;
  const ex = getExhibition(slug);
  if (!ex) return {};
  return {
    title: `${ex.title} — Gallery — TEMPO House`,
    description: ex.description,
  };
}

export default async function ExhibitionPage({ params }: Props) {
  const { slug } = await params;
  const ex = getExhibition(slug);
  if (!ex) notFound();

  const jsonLd = {
    "@context": "https://schema.org",
    "@type": "ExhibitionEvent",
    name: ex.title,
    description: ex.description,
    startDate: ex.startDate,
    endDate: ex.endDate,
    location: {
      "@type": "Place",
      name: "TEMPO House Gallery",
      address: {
        "@type": "PostalAddress",
        streetAddress: "218c Pasteur",
        addressLocality: "District 3",
        addressRegion: "Ho Chi Minh City",
        postalCode: "72400",
        addressCountry: "VN",
      },
    },
    organizer: {
      "@type": "Organization",
      name: "TEMPO House",
      url: "https://tempohouse.com.vn",
    },
  };

  const curatorialParagraphs = ex.curatorialNote.split("\n\n");

  return (
    <>
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(jsonLd) }}
      />

      <div className={styles.page}>
        {/* Header */}
        <header className={styles.header}>
          <div className="container container--narrow">
            <Link href="/gallery" className={styles.backLink}>
              ← Gallery
            </Link>
            <p className={styles.statusTag}>
              {ex.status === "current" ? "On now" : ex.status === "upcoming" ? "Opening soon" : "Past exhibition"}
            </p>
            <h1 className={styles.title}>{ex.title}</h1>
            <p className={styles.subtitle}>{ex.subtitle}</p>
          </div>
        </header>

        {/* Artwork frame — full hero */}
        <div className={styles.heroFrame} aria-hidden="true">
          <div className="container">
            <div className={styles.outerFrame}>
              <div className={styles.matBoard}>
                <div className={styles.artworkField}>
                  <span className={styles.artworkNum}>01</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        {/* Exhibition details */}
        <div className={styles.content}>
          <div className="container">
            <div className={styles.layout}>

              {/* Curatorial note */}
              <div className={styles.main}>
                <p className={styles.sectionLabel}>Curatorial Note</p>
                {curatorialParagraphs.map((para, i) => (
                  <p key={i} className={styles.curatorial}>{para}</p>
                ))}
              </div>

              {/* Sidebar */}
              <aside className={styles.sidebar}>
                <div className={styles.infoCard}>
                  <div className={styles.infoRow}>
                    <p className={styles.infoLabel}>Exhibition</p>
                    <p className={styles.infoValue}>{ex.title}</p>
                  </div>
                  <div className={styles.infoRow}>
                    <p className={styles.infoLabel}>Medium</p>
                    <p className={styles.infoValue}>{ex.medium}</p>
                  </div>
                  <div className={styles.infoRow}>
                    <p className={styles.infoLabel}>Artists</p>
                    <div>
                      {ex.artists.map((artist) => (
                        <p key={artist} className={styles.infoValue}>{artist}</p>
                      ))}
                    </div>
                  </div>
                  <div className={styles.infoRow}>
                    <p className={styles.infoLabel}>Dates</p>
                    <p className={styles.infoValue}>
                      {ex.openDate} —<br />{ex.closeDate}
                    </p>
                  </div>
                  <div className={styles.infoRow}>
                    <p className={styles.infoLabel}>Access</p>
                    <p className={styles.infoValue}>
                      Open during café and bar hours.<br />No admission fee.
                    </p>
                  </div>

                  <a
                    href="mailto:gallery@tempohouse.com.vn"
                    className={styles.enquireBtn}
                  >
                    Acquisition enquiry →
                  </a>

                  {ex.openingEvent && (
                    <Link
                      href={`/programme/${ex.openingEvent}`}
                      className={styles.openingLink}
                    >
                      Opening reception →
                    </Link>
                  )}
                </div>
              </aside>
            </div>
          </div>
        </div>
      </div>
    </>
  );
}
