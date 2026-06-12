import type { Metadata } from "next";
import StubPage from "../../components/StubPage";

export const metadata: Metadata = { title: "Event Enquiry — TEMPO House" };

export default function EventEnquiryPage() {
  return <StubPage title="Enquire" subtitle="Tell us about your event and we'll be in touch." eyebrow="Private Events" />;
}
