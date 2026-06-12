import type { Metadata } from "next";
import StubPage from "../components/StubPage";

export const metadata: Metadata = { title: "Contact — TEMPO House" };

export default function ContactPage() {
  return <StubPage title="Contact" subtitle="Find us, write to us, or just say hello." eyebrow="Get in touch" />;
}
