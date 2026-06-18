"use client";

import { useEffect, useRef, useState } from "react";
import styles from "./PinGate.module.css";

const PIN_CORRECT   = "1688";
const SESSION_KEY   = "tempo-preview-unlocked";
const PIN_LEN       = 4;

export default function PinGate({ children }: { children: React.ReactNode }) {
  const [unlocked, setUnlocked]   = useState(false);
  const [exiting,  setExiting]    = useState(false);
  const [digits,   setDigits]     = useState<string[]>(Array(PIN_LEN).fill(""));
  const [error,    setError]      = useState(false);
  const inputRefs = useRef<(HTMLInputElement | null)[]>([]);

  // Check session on mount — avoids flash if already unlocked
  useEffect(() => {
    if (typeof window !== "undefined" && sessionStorage.getItem(SESSION_KEY) === "1") {
      setUnlocked(true);
    } else {
      // Focus first box after mount
      requestAnimationFrame(() => inputRefs.current[0]?.focus());
    }
  }, []);

  function handleChange(idx: number, val: string) {
    const char = val.replace(/\D/g, "").slice(-1);
    setError(false);
    const next = [...digits];
    next[idx] = char;
    setDigits(next);
    if (char && idx < PIN_LEN - 1) {
      inputRefs.current[idx + 1]?.focus();
    }
    // Auto-submit when all 4 filled
    if (char && idx === PIN_LEN - 1) {
      const full = [...next.slice(0, idx), char].join("");
      if (full.length === PIN_LEN) attemptUnlock(full);
    }
  }

  function handleKeyDown(idx: number, e: React.KeyboardEvent<HTMLInputElement>) {
    if (e.key === "Backspace") {
      setError(false);
      if (digits[idx]) {
        const next = [...digits];
        next[idx] = "";
        setDigits(next);
      } else if (idx > 0) {
        inputRefs.current[idx - 1]?.focus();
      }
    }
    if (e.key === "Enter") {
      const full = digits.join("");
      if (full.length === PIN_LEN) attemptUnlock(full);
    }
  }

  function handlePaste(e: React.ClipboardEvent) {
    const text = e.clipboardData.getData("text").replace(/\D/g, "").slice(0, PIN_LEN);
    if (!text) return;
    e.preventDefault();
    const next = Array(PIN_LEN).fill("");
    text.split("").forEach((c, i) => { next[i] = c; });
    setDigits(next);
    setError(false);
    if (text.length === PIN_LEN) {
      attemptUnlock(text);
    } else {
      inputRefs.current[text.length]?.focus();
    }
  }

  function attemptUnlock(code: string) {
    if (code === PIN_CORRECT) {
      sessionStorage.setItem(SESSION_KEY, "1");
      setExiting(true);
      setTimeout(() => setUnlocked(true), 600);
    } else {
      setError(true);
      setDigits(Array(PIN_LEN).fill(""));
      setTimeout(() => inputRefs.current[0]?.focus(), 50);
    }
  }

  if (unlocked) return <>{children}</>;

  return (
    <>
      {/* Render children underneath so they're ready when gate lifts */}
      <div aria-hidden="true" style={{ visibility: "hidden", position: "absolute", inset: 0, pointerEvents: "none" }}>
        {children}
      </div>

      <div className={`${styles.overlay} ${exiting ? styles.exit : ""}`} role="dialog" aria-modal="true" aria-label="Preview access">
        <div className={styles.inner}>

          <p className={styles.announce}>Preview Access</p>

          <img
            src="/content/brand-assets/tempo_house_logo_burnt_transparent.png"
            alt="TEMPO House"
            className={styles.logo}
          />

          <div className={styles.divider} role="presentation">
            <span className={styles.dividerLine} />
            <span className={styles.dividerDot} />
            <span className={styles.dividerLine} />
          </div>

          <p className={styles.label}>Enter your access code</p>

          <div className={`${styles.pinRow} ${error ? styles.shake : ""}`} onAnimationEnd={() => setError(false)}>
            {digits.map((d, i) => (
              <input
                key={i}
                ref={el => { inputRefs.current[i] = el; }}
                type="password"
                inputMode="numeric"
                maxLength={1}
                value={d}
                onChange={e => handleChange(i, e.target.value)}
                onKeyDown={e => handleKeyDown(i, e)}
                onPaste={handlePaste}
                aria-label={`PIN digit ${i + 1}`}
                className={`${styles.pinBox} ${error ? styles.pinBoxError : ""} ${d ? styles.pinBoxFilled : ""}`}
              />
            ))}
          </div>

          {error && (
            <p className={styles.error}>Incorrect code — please try again</p>
          )}

        </div>

        <footer className={styles.footer}>
          <p className={styles.footerText}>© 2026 TEMPO House</p>
        </footer>
      </div>
    </>
  );
}
