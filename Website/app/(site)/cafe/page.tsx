import type { Metadata } from "next";
import StubPage from "../components/StubPage";

export const metadata: Metadata = { title: "Specialty Café — TEMPO House" };

export default function CafePage() {
  return <StubPage title="Café" subtitle="Single-origin coffee. Mornings worth staying for." eyebrow="07:00 – 17:00" />;
}
