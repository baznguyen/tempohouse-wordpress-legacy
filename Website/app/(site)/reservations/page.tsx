import type { Metadata } from "next";
import StubPage from "../components/StubPage";

export const metadata: Metadata = { title: "Reservations — TEMPO House" };

export default function ReservationsPage() {
  return <StubPage title="Reserve" subtitle="Book a table for café or evening dining." eyebrow="Reservations" />;
}
