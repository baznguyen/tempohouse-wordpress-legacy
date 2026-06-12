import type { Metadata } from "next";
import Link from "next/link";
import { notFound } from "next/navigation";
import { ARTICLES, getArticle } from "../data";
import styles from "./page.module.css";

interface Props {
  params: Promise<{ slug: string }>;
}

export async function generateStaticParams() {
  return ARTICLES.map((a) => ({ slug: a.slug }));
}

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { slug } = await params;
  const article = getArticle(slug);
  if (!article) return {};
  return {
    title: `${article.title} — TEMPO House Journal`,
    description: article.excerpt,
  };
}

export default async function ArticlePage({ params }: Props) {
  const { slug } = await params;
  const article = getArticle(slug);
  if (!article) notFound();

  const publishedDate = new Date(article.publishedAt).toLocaleDateString("en-GB", {
    day: "numeric",
    month: "long",
    year: "numeric",
  });

  const relatedArticles = ARTICLES.filter(
    (a) => a.slug !== article.slug && a.category === article.category
  ).slice(0, 2);

  return (
    <article className={styles.page}>
      {/* Header */}
      <header className={styles.header}>
        <div className="container container--narrow">
          <Link href="/journal" className={styles.backLink}>
            ← Journal
          </Link>
          <p className={styles.category}>{article.category}</p>
          <h1 className={styles.title}>{article.title}</h1>
          <p className={styles.meta}>
            {article.readMinutes} min read &nbsp;·&nbsp; {publishedDate}
          </p>
        </div>
      </header>

      {/* Artwork panel */}
      <div className={styles.artworkWrap} aria-hidden="true">
        <div className="container">
          <div className={styles.artworkFrame}>
            <div className={styles.artworkMat}>
              <div className={styles.artworkField} data-category={article.category} />
            </div>
          </div>
        </div>
      </div>

      {/* Body */}
      <div className={styles.body}>
        <div className="container container--narrow">
          <div
            className={styles.prose}
            dangerouslySetInnerHTML={{ __html: article.body }}
          />
        </div>
      </div>

      {/* Related */}
      {relatedArticles.length > 0 && (
        <aside className={styles.related}>
          <div className="container">
            <p className={styles.relatedEyebrow}>More from the Journal</p>
            <div className={styles.relatedGrid}>
              {relatedArticles.map((a) => (
                <Link key={a.slug} href={`/journal/${a.slug}`} className={styles.relatedCard}>
                  <p className={styles.relatedCategory}>{a.category}</p>
                  <p className={styles.relatedTitle}>{a.title}</p>
                  <p className={styles.relatedMeta}>{a.readMinutes} min</p>
                </Link>
              ))}
            </div>
          </div>
        </aside>
      )}
    </article>
  );
}
