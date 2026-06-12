import type { Metadata } from "next";
import StubPage from "../components/StubPage";

export const metadata: Metadata = { title: "Cocktail Bar — TEMPO House" };

export default function BarPage() {
  return <StubPage title="Bar" subtitle="Vietnamese botanicals. Familiar faces. One more round." eyebrow="18:00 – 01:00" />;
}
