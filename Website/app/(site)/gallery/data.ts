export type ExhibitionStatus = "current" | "upcoming" | "past";

export interface Exhibition {
  slug: string;
  status: ExhibitionStatus;
  title: string;
  subtitle: string;
  artists: string[];
  openDate: string;    // display
  closeDate: string;   // display
  startDate: string;   // ISO for schema
  endDate: string;     // ISO for schema
  medium: string;
  description: string;
  curatorialNote: string;
  openingEvent?: string; // event slug
}

export const EXHIBITIONS: Exhibition[] = [
  {
    slug: "works-on-paper",
    status: "upcoming",
    title: "Works on Paper",
    subtitle: "Inaugural exhibition",
    artists: ["Nguyễn Thị Lan", "Trần Minh Đức", "Lê Hoàng Anh", "Pham Van Quang", "Mai Thu Hương"],
    openDate: "27 June 2026",
    closeDate: "9 August 2026",
    startDate: "2026-06-27",
    endDate: "2026-08-09",
    medium: "Drawing, printmaking, and collage",
    description:
      "Five artists working in drawing, printmaking, and collage. The inaugural exhibition of TEMPO House's gallery programme.",
    curatorialNote: `The first show at TEMPO House began with a question we kept returning to during the months of building the space: what is the most direct thing we can show?

Paper was the answer — not because it is simple (it is not) but because it is unmediated. The relationship between the artist's decision and the surface it lands on is visible in a way that painting or digital work can obscure. You see the process in works on paper. The false starts, the corrections, the moments where the artist committed to something uncertain and it resolved into something necessary.

The five artists in this exhibition work in different registers — abstraction, observational drawing, print, collage — but they share an investment in process that is visible in the finished work. These are not images that were arrived at efficiently. You can feel the time in them.

The show runs through August. It is available to view during all café and bar hours. Some works are available for acquisition — enquire at the counter or by email.`,
    openingEvent: "works-on-paper-opening-night",
  },
];

export function getExhibition(slug: string): Exhibition | undefined {
  return EXHIBITIONS.find((e) => e.slug === slug);
}

export function getCurrentExhibition(): Exhibition | undefined {
  return EXHIBITIONS.find((e) => e.status === "current") ??
         EXHIBITIONS.find((e) => e.status === "upcoming");
}
