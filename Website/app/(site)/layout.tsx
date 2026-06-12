import type { ReactNode } from "react";
import SiteNav from "./components/SiteNav";
import Footer from "./components/Footer";

export default function SiteLayout({ children }: { children: ReactNode }) {
  return (
    <>
      <SiteNav />
      <main>{children}</main>
      <Footer />
    </>
  );
}
