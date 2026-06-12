import type { Metadata } from "next";
import StubPage from "../components/StubPage";

export const metadata: Metadata = { title: "Venue — TEMPO House" };

export default function VenuePage() {
  return <StubPage title="Venue" subtitle="The space, the story, the atmosphere." eyebrow="The Space" />;
}
