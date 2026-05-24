# TEMPO House — Project Documentation

## Folder Structure

```
tempohouse.com.vn/
├── Website/          Next.js 16 website (static export → FTP/SFTP deploy)
├── Documentation/    All docs, decisions, research, bug register
│   ├── bugs/         Bug register (bug-register.md)
│   ├── decisions/    Architecture decision records
│   └── research/     YYYY-MM-DD-topic-slug.md research files
├── Brand Assets/     Logos, colour palettes, fonts, brand guidelines
└── Tools/            Custom tools, MCPs, APIs built for TEMPO House
```

## Tech Stack

| Layer        | Choice                                   |
|--------------|------------------------------------------|
| Framework    | Next.js 16 (App Router)                  |
| Language     | TypeScript                               |
| Styling      | Custom CSS — no utility framework        |
| Fonts        | Cormorant Garamond (display) + DM Sans   |
| Animations   | GSAP + Framer Motion                     |
| Deployment   | Static export (`out/`) → FTP or SFTP     |

## Getting Started

```bash
cd Website
cp .env.example .env.local
# fill in SSH/FTP credentials
npm install
npm run dev
```

## Deploy

```bash
npm run deploy        # build + upload via FTP/SFTP
npm run deploy:dry    # dry run — shows what would upload
```

Set `DEPLOY_MODE=sftp` (key-based) or `DEPLOY_MODE=ftp` in `.env.local`.

## Brand

Brand assets (logos, palette, guidelines) live in `/Brand Assets/`.  
CSS variables in `Website/app/globals.css` should be updated once the final brand palette is confirmed.
