"use client";

import { useState } from "react";

const COMPANY_ID = process.env.NEXT_PUBLIC_KLAVIYO_COMPANY_ID ?? "VCR2Ei";
const LIST_ID    = process.env.NEXT_PUBLIC_KLAVIYO_LIST_ID    ?? "";

export default function EmailForm({ dark = false }: { dark?: boolean }) {
  const [email,  setEmail]  = useState("");
  const [status, setStatus] = useState<"idle" | "loading" | "success" | "error">("idle");

  async function handleSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setStatus("loading");
    try {
      const res = await fetch(
        `https://a.klaviyo.com/client/subscriptions/?company_id=${COMPANY_ID}`,
        {
          method: "POST",
          headers: { "Content-Type": "application/json", revision: "2024-02-15" },
          body: JSON.stringify({
            data: {
              type: "subscription",
              attributes: {
                custom_source: "Website",
                profile: { data: { type: "profile", attributes: { email } } },
              },
              ...(LIST_ID && {
                relationships: { list: { data: { type: "list", id: LIST_ID } } },
              }),
            },
          }),
        }
      );
      const ok = res.status === 202 || res.ok;
      setStatus(ok ? "success" : "error");
      if (ok) setEmail("");
    } catch {
      setStatus("error");
    }
  }

  const c = dark
    ? { success: "#DDAA62", borderIdle: "rgba(247,243,238,0.22)", borderFocus: "rgba(247,243,238,0.7)", inputText: "#F7F3EE", inputPlaceholder: "rgba(247,243,238,0.35)", btnBg: "#DDAA62", btnBgHover: "#B8893E", btnText: "#1A1816" }
    : { success: "#7B3A35", borderIdle: "rgba(123,58,53,0.18)",   borderFocus: "rgba(123,58,53,0.6)",   inputText: "#2C1A18", inputPlaceholder: undefined,                  btnBg: "#7B3A35", btnBgHover: "#5C2B27", btnText: "#F2EDE6" };

  /* ── Success ── */
  if (status === "success") {
    return (
      <p style={{
        fontFamily: "var(--font-accent)",
        fontStyle: "italic",
        fontWeight: 300,
        fontSize: "clamp(0.95rem, 2.5vw, 1.2rem)",
        color: c.success,
        letterSpacing: "0.02em",
      }}>
        ✦&ensp;You&apos;re on the list.
      </p>
    );
  }

  /* ── Form ── */
  const borderIdle  = `1px solid ${c.borderIdle}`;
  const borderFocus = `1px solid ${c.borderFocus}`;

  return (
    <form
      onSubmit={handleSubmit}
      style={{ display: "flex", flexDirection: "column", gap: "0.65rem", width: "100%" }}
    >
      <input
        type="email"
        name="email"
        value={email}
        onChange={(e) => setEmail(e.target.value)}
        placeholder="your@email.com"
        required
        disabled={status === "loading"}
        style={{
          width: "100%",
          background: "transparent",
          border: "none",
          borderBottom: borderIdle,
          padding: "0.75rem 0",
          fontFamily: "var(--font-body)",
          fontWeight: 300,
          fontSize: "clamp(0.85rem, 2.5vw, 0.95rem)",
          letterSpacing: "0.02em",
          color: c.inputText,
          ...(c.inputPlaceholder && { ["--placeholder-color" as string]: c.inputPlaceholder }),
          outline: "none",
          transition: "border-color 200ms ease",
          textAlign: "center",
        }}
        onFocus={(e) => (e.currentTarget.style.borderBottom = borderFocus)}
        onBlur={(e)  => (e.currentTarget.style.borderBottom = borderIdle)}
      />

      <button
        type="submit"
        disabled={status === "loading"}
        style={{
          width: "100%",
          background: c.btnBg,
          color: c.btnText,
          border: "none",
          padding: "0.85rem 1.5rem",
          fontFamily: "var(--font-body)",
          fontWeight: 400,
          fontSize: "0.6rem",
          letterSpacing: "0.28em",
          textTransform: "uppercase",
          cursor: status === "loading" ? "wait" : "pointer",
          opacity: status === "loading" ? 0.6 : 1,
          transition: "background 200ms ease, opacity 200ms ease",
        }}
        onMouseEnter={(e) => { if (status !== "loading") e.currentTarget.style.background = c.btnBgHover; }}
        onMouseLeave={(e) => { e.currentTarget.style.background = c.btnBg; }}
      >
        {status === "loading" ? "···" : "Join the List"}
      </button>

      {status === "error" && (
        <p style={{
          fontSize: "0.62rem",
          letterSpacing: "0.15em",
          color: "#B84040",
          textAlign: "center",
          textTransform: "uppercase",
        }}>
          Something went wrong — please try again.
        </p>
      )}
    </form>
  );
}
