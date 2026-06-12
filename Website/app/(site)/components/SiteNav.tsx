"use client";

import { useState, useEffect, useCallback } from "react";
import Link from "next/link";
import Image from "next/image";
import { usePathname } from "next/navigation";
import styles from "./SiteNav.module.css";

type Mode = "day" | "night";

const NAV_LINKS = [
  { label: "Home",         href: "/" },
  { label: "Venue",        href: "/venue" },
  { label: "Café",         href: "/cafe" },
  { label: "Bar",          href: "/bar" },
  { label: "Gallery",      href: "/gallery" },
  { label: "What's On",   href: "/whats-on" },
  { label: "Events",       href: "/events" },
  { label: "Reservations", href: "/reservations" },
  { label: "Contact",      href: "/contact" },
];

function detectMode(): Mode {
  const stored = typeof localStorage !== "undefined" ? localStorage.getItem("tempo-mode") : null;
  if (stored === "day" || stored === "night") return stored;
  const hcmcHour = new Date(
    new Date().toLocaleString("en", { timeZone: "Asia/Ho_Chi_Minh" })
  ).getHours();
  return hcmcHour >= 7 && hcmcHour < 18 ? "day" : "night";
}

export default function SiteNav() {
  const [open, setOpen] = useState(false);
  const [scrolled, setScrolled] = useState(false);
  const [mode, setMode] = useState<Mode>("day");
  const pathname = usePathname();

  useEffect(() => {
    const m = detectMode();
    setMode(m);
    document.documentElement.setAttribute("data-tempo", m);
  }, []);

  useEffect(() => { setOpen(false); }, [pathname]);

  useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 32);
    window.addEventListener("scroll", onScroll, { passive: true });
    return () => window.removeEventListener("scroll", onScroll);
  }, []);

  useEffect(() => {
    document.body.style.overflow = open ? "hidden" : "";
    return () => { document.body.style.overflow = ""; };
  }, [open]);

  useEffect(() => {
    if (!open) return;
    const onKey = (e: KeyboardEvent) => { if (e.key === "Escape") setOpen(false); };
    window.addEventListener("keydown", onKey);
    return () => window.removeEventListener("keydown", onKey);
  }, [open]);

  const toggleMode = useCallback(() => {
    const next: Mode = mode === "day" ? "night" : "day";
    setMode(next);
    document.documentElement.setAttribute("data-tempo", next);
    localStorage.setItem("tempo-mode", next);
  }, [mode]);

  const isActive = (href: string) =>
    href === "/" ? pathname === "/" : pathname.startsWith(href);

  return (
    <>
      {/* ── Masthead ─────────────────────────────── */}
      <header className={`${styles.masthead} ${scrolled ? styles.scrolled : ""}`}>
        <div className={styles.grid}>

          <button
            className={styles.menuTrigger}
            onClick={() => setOpen(v => !v)}
            aria-expanded={open}
            aria-controls="site-drawer"
            aria-label="Open navigation"
          >
            MENU
          </button>

          <Link href="/" className={styles.brandFrame} aria-label="TEMPO House — home">
            <Image
              src="/content/brand-assets/tempo_house_logo_burnt_transparent.png"
              alt="TEMPO House"
              width={512}
              height={512}
              priority
              className={styles.logoImg}
            />
          </Link>

          <Link href="/reservations" className={styles.reserveCta}>
            · RESERVE ·
          </Link>
        </div>
      </header>

      {/* ── Overlay ──────────────────────────────── */}
      {open && (
        <div
          className={styles.overlay}
          onClick={() => setOpen(false)}
          aria-hidden="true"
        />
      )}

      {/* ── Drawer ───────────────────────────────── */}
      <nav
        id="site-drawer"
        className={`${styles.drawer} ${open ? styles.drawerOpen : ""}`}
        aria-label="Primary navigation"
        aria-hidden={!open}
        inert={!open || undefined}
      >
        {/* Drawer header — MENU label + close X */}
        <div className={styles.drawerHeader}>
          <span className={styles.drawerLabel}>Menu</span>
          <button
            className={styles.closeBtn}
            onClick={() => setOpen(false)}
            tabIndex={open ? 0 : -1}
            aria-label="Close navigation"
          >
            ✕
          </button>
        </div>

        {/* Nav links */}
        <ul className={styles.drawerNav} role="list">
          {NAV_LINKS.map((link, i) => (
            <li key={link.href} style={{ "--i": i } as React.CSSProperties}>
              <Link
                href={link.href}
                className={`${styles.drawerLink} ${isActive(link.href) ? styles.drawerLinkActive : ""}`}
                tabIndex={open ? 0 : -1}
              >
                {link.label}
              </Link>
            </li>
          ))}
        </ul>

        {/* Bottom area */}
        <div className={styles.drawerFoot}>
          <Link
            href="/reservations"
            className={styles.drawerCta}
            tabIndex={open ? 0 : -1}
          >
            Reserve a Table
          </Link>

          <div className={styles.drawerMeta}>
            <button
              className={styles.modeToggle}
              onClick={toggleMode}
              tabIndex={open ? 0 : -1}
            >
              {mode === "day" ? "☽ Night mode" : "☀ Day mode"}
            </button>
            <span className={styles.langToggle}>EN · VI</span>
          </div>
        </div>
      </nav>
    </>
  );
}
