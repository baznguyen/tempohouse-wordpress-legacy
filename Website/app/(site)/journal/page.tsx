import type { Metadata } from "next";
import Link from "next/link";
import { ARTICLES, getFeaturedArticle } from "./data";
import styles from "./page.module.css";

export const metadata: Metadata = {
  title: "Journal — TEMPO House",
  description:
    "Stories, essays, and dispatches from 218c Pasteur — specialty coffee, cocktails, art, and the life of District 3.",
};

export default function JournalPage() {
  const featured = getFeaturedArticle();
  const rest = ARTICLES.filter((a) => !a.featured);

  return (
    <div className={styles.page}>
      <header className={styles.pageHeader}>
        <p className={styles.eyebrow}>The Journal</p>
        <h1 className={styles.pageTitle}>
          Stories from<br />
          <em>218c Pasteur</em>
        </h1>
      </header>

      {/* Featured article */}
      {featured && (
        <section className={styles.featured} aria-label="Featured article">
          <div className="container">
            <Link href={`/journal/${featured.slug}`} className={styles.featuredCard}>
              <div className={styles.featuredFrame}>
                <div className={styles.featuredMat}>
                  <div className={styles.featuredArtwork} aria-hidden="true">
                    <span className={styles.featuredNum}>01</span>
                  </div>
                </div>
              </div>
              <div className={styles.featuredInfo}>
                <p className={styles.articleCategory}>{featured.category}</p>
                <h2 className={styles.featuredTitle}>{featured.title}</h2>
                <p className={styles.articleExcerpt}>{featured.excerpt}</p>
                <p className={styles.articleMeta}>
                  {featured.readMinutes} min read &nbsp;·&nbsp;{" "}
                  {new Date(featured.publishedAt).toLocaleDateString("en-GB", {
                    day: "numeric",
                    month: "long",
                    year: "numeric",
                  })}
                </p>
                <span className={styles.articleCta}>Read →</span>
              </div>
            </Link>
          </div>
        </section>
      )}

      {/* Article grid */}
      <section className={styles.grid} aria-label="All articles">
        <div className="container">
          <div className={styles.articleGrid}>
            {rest.map((article) => (
              <Link
                key={article.slug}
                href={`/journal/${article.slug}`}
                className={styles.articleCard}
              >
                <div className={styles.articleFrame}>
                  <div className={styles.articleMat}>
                    <div className={styles.articleArtwork} data-category={article.category} aria-hidden="true">
                      <span className={styles.articleArtworkLabel}>{article.category}</span>
                    </div>
                  </div>
                </div>
                <div className={styles.articleInfo}>
                  <p className={styles.articleCategory}>{article.category}</p>
                  <h2 className={styles.articleTitle}>{article.title}</h2>
                  <p className={styles.articleExcerpt}>{article.excerpt}</p>
                  <p className={styles.articleMeta}>
                    {article.readMinutes} min read &nbsp;·&nbsp;{" "}
                    {new Date(article.publishedAt).toLocaleDateString("en-GB", {
                      day: "numeric",
                      month: "long",
                      year: "numeric",
                    })}
                  </p>
                </div>
              </Link>
            ))}
          </div>
        </div>
      </section>
    </div>
  );
}
