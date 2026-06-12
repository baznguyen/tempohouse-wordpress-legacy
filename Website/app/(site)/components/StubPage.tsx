import Link from "next/link";
import styles from "./StubPage.module.css";

interface StubPageProps {
  title: string;
  subtitle?: string;
  eyebrow?: string;
}

export default function StubPage({ title, subtitle, eyebrow = "TEMPO House" }: StubPageProps) {
  return (
    <section className={styles.stub}>
      <div className={styles.inner}>
        <p className={styles.eyebrow}>{eyebrow}</p>
        <div className={styles.bleedWrap} aria-hidden="true">
          <span className={styles.bleedTitle}>{title}</span>
        </div>
        {subtitle && <p className={styles.subtitle}>{subtitle}</p>}
        <p className={styles.notice}>This page is under construction.</p>
        <Link href="/" className={styles.back}>← Back to home</Link>
      </div>
    </section>
  );
}
