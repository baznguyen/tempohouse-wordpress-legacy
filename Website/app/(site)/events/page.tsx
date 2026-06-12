import type { Metadata } from "next";
import StubPage from "../components/StubPage";

export const metadata: Metadata = { title: "Private Events — TEMPO House" };

export default function EventsPage() {
  return <StubPage title="Events" subtitle="Private dining, corporate, and celebrations." eyebrow="Host with us" />;
}
