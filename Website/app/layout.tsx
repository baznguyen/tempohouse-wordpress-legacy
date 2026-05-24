import type { Metadata, Viewport } from "next";
import Script from "next/script";
import "./globals.css";

const KLAVIYO_ID = process.env.NEXT_PUBLIC_KLAVIYO_COMPANY_ID ?? "VCR2Ei";
const SITE_URL   = "https://tempohouse.com.vn";

const jsonLd = {
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": ["CafeOrCoffeeShop", "BarOrPub", "ArtGallery"],
      "@id": `${SITE_URL}/#business`,
      "name": "TEMPO House",
      "alternateName": ["Tempo House Saigon", "TEMPO House HCMC", "Tempo House Ho Chi Minh City"],
      "description": "Specialty café by day, intimate cocktail bar by night, with a curated art gallery and events space. Coming soon to Ho Chi Minh City (Saigon), Vietnam.",
      "url": SITE_URL,
      "address": {
        "@type": "PostalAddress",
        "addressLocality": "Ho Chi Minh City",
        "addressRegion": "Hồ Chí Minh",
        "addressCountry": "VN",
      },
      "geo": {
        "@type": "GeoCoordinates",
        "latitude": 10.7769,
        "longitude": 106.7009,
      },
      "areaServed": {
        "@type": "City",
        "name": "Ho Chi Minh City",
      },
      "sameAs": [
        "https://www.instagram.com/tempohouse.sgn",
        "https://www.facebook.com/tempohouse.sgn",
        "https://www.tiktok.com/@tempohouse.sgn",
      ],
      "servesCuisine": ["Specialty Coffee", "Cocktails", "Bar"],
      "currenciesAccepted": "VND",
      "priceRange": "$$",
    },
    {
      "@type": "WebSite",
      "@id": `${SITE_URL}/#website`,
      "url": SITE_URL,
      "name": "TEMPO House",
      "description": "Coffee in the morning. Connection at night.",
      "inLanguage": ["en", "vi"],
    },
  ],
};

export const metadata: Metadata = {
  metadataBase: new URL(SITE_URL),
  title: {
    default: "TEMPO House | Specialty Café & Cocktail Bar — Ho Chi Minh City",
    template: "%s | TEMPO House",
  },
  description:
    "TEMPO House — specialty café by day, intimate cocktail bar by night. Curated art gallery and events space. Coming soon to Ho Chi Minh City (Saigon), Vietnam.",
  keywords: [
    "TEMPO House",
    "specialty coffee Ho Chi Minh City",
    "specialty coffee Saigon",
    "café Ho Chi Minh City",
    "café Saigon",
    "cocktail bar Ho Chi Minh City",
    "cocktail bar Saigon",
    "art gallery HCMC",
    "events venue Ho Chi Minh City",
    "creative space Saigon",
    "coffee shop Ho Chi Minh City",
    "Melbourne style café Vietnam",
    "specialty café Vietnam",
    "coffee bar HCMC",
    "venue Saigon",
    "quán cà phê đặc sản Sài Gòn",
    "bar cocktail TP.HCM",
    "không gian sáng tạo Sài Gòn",
  ],
  authors:   [{ name: "TEMPO House", url: SITE_URL }],
  creator:   "TEMPO House",
  publisher: "TEMPO House",
  robots: {
    index: true,
    follow: true,
    googleBot: {
      index: true,
      follow: true,
      "max-image-preview": "large",
      "max-snippet": -1,
    },
  },
  alternates: {
    canonical: "/",
    languages: { en: "/", vi: "/" },
  },
  openGraph: {
    type: "website",
    locale: "en_US",
    alternateLocale: ["vi_VN"],
    url: SITE_URL,
    siteName: "TEMPO House",
    title: "TEMPO House | Specialty Café & Cocktail Bar — Ho Chi Minh City",
    description:
      "Coffee in the morning. Connection at night. Specialty café, cocktail bar, art gallery & events. Coming soon to Ho Chi Minh City.",
    images: [
      {
        url: "/content/brand-assets/og-image.jpg",
        width: 1200,
        height: 630,
        alt: "TEMPO House signature floor — Specialty Café & Cocktail Bar, Ho Chi Minh City",
      },
    ],
  },
  twitter: {
    card: "summary_large_image",
    title: "TEMPO House | Coming Soon — Ho Chi Minh City",
    description: "Coffee in the morning. Connection at night. Coming soon to Saigon.",
    images: ["/content/brand-assets/og-image.jpg"],
  },
  icons: {
    icon: [
      { url: "/favicon.ico" },
      { url: "/favicon.svg", type: "image/svg+xml" },
    ],
    apple: "/apple-touch-icon.png",
    other: [{ rel: "manifest", url: "/site.webmanifest" }],
  },
  other: {
    "geo.region":    "VN-SG",
    "geo.placename": "Ho Chi Minh City",
    "geo.position":  "10.7769;106.7009",
    "ICBM":          "10.7769, 106.7009",
  },
};

export const viewport: Viewport = {
  width: "device-width",
  initialScale: 1,
  themeColor: "#F2EDE6",
};

export default function RootLayout({
  children,
}: Readonly<{ children: React.ReactNode }>) {
  return (
    <html lang="en" data-tempo="day">
      <head>
        <script
          type="application/ld+json"
          dangerouslySetInnerHTML={{ __html: JSON.stringify(jsonLd) }}
        />
      </head>
      <body>
        {children}
        {/* Klaviyo — site tracking & onsite JS */}
        <Script
          src={`https://static.klaviyo.com/onsite/js/${KLAVIYO_ID}/klaviyo.js?company_id=${KLAVIYO_ID}`}
          strategy="afterInteractive"
        />
        <Script id="klaviyo-init" strategy="afterInteractive">{`
          !function(){if(!window.klaviyo){window._klOnsite=window._klOnsite||[];try{window.klaviyo=new Proxy({},{get:function(n,i){return"push"===i?function(){var n;(n=window._klOnsite).push.apply(n,arguments)}:function(){for(var n=arguments.length,o=new Array(n),w=0;w<n;w++)o[w]=arguments[w];var t="function"==typeof o[o.length-1]?o.pop():void 0,e=new Promise((function(n){window._klOnsite.push([i].concat(o,[function(i){t&&t(i),n(i)}]))}));return e}}})}catch(n){window.klaviyo=window.klaviyo||[],window.klaviyo.push=function(){var n;(n=window._klOnsite).push.apply(n,arguments)}}}}();
        `}</Script>
      </body>
    </html>
  );
}
