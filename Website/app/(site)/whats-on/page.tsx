import type { Metadata } from "next";
import StubPage from "../components/StubPage";

export const metadata: Metadata = { title: "What's On — TEMPO House" };

export default function WhatsOnPage() {
  return <StubPage title="What's On" subtitle="Events, programming, and happenings at TEMPO." eyebrow="Programming" />;
}
