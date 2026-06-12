# Beat Animation System — TEMPO House Hero

**Status:** Parked — implemented, tested, removed. Ready to re-apply.  
**Files affected:** `Website/app/(site)/components/ParallaxHero.tsx`, `ParallaxHero.module.css`

---

## Concept

Each letter in TEMPO / HOUSE becomes an independent frequency bar in an audio equaliser. Letters snap to peak height on their own rhythm then decay with gravity — exactly like a live spectrum analyser. Because every letter has a different cycle length, the pattern never repeats predictably (polyrhythm).

---

## Why it was removed

The scale effect felt mismatched with the gallery/art-museum aesthetic of the site. It reads as nightclub UI. May suit a future "evening mode" toggle or a dedicated event/bar landing page.

---

## Technical approach

### Key CSS technique

- **Arrive** uses `transform: translateY()` + `filter: blur()`  
- **Beat** uses the CSS `scale` individual property (separate from `transform`)  
- Because `scale` and `transform` are independent CSS properties, they **compose without conflict** — both run simultaneously with no override issue.

### `@property` registration

`--peak-scale` must be registered as a typed `<number>` for CSS to interpolate it inside `@keyframes`. Without registration it is an untyped string and keyframe interpolation silently breaks.

```css
@property --peak-scale {
  syntax: "<number>";
  inherits: false;
  initial-value: 1;
}
```

Browser support: Chrome 85+, Firefox 128+, Safari 16.4+.

### Beat keyframe shape

```css
@keyframes beatPulse {
  /* steps(1, start) = instant snap to peak at t=0 */
  0% {
    scale: 1 1;
    animation-timing-function: steps(1, start);
  }
  /* Peak reached. ease-out = gravity decay */
  12% {
    scale: 1 var(--peak-scale, 1.30);
    animation-timing-function: cubic-bezier(0.2, 0, 0.5, 1);
  }
  /* Back at baseline. Rest here (~52% of cycle) */
  48%, 100% {
    scale: 1 1;
  }
}
```

`transform-origin: center 100%` on `.bleedChar` anchors the scale at the baseline so letters grow upward like EQ bars.

`.bleedWrap` needs `overflow: visible` (not `overflow: hidden`) to allow letters to pulse above the box.

### Per-letter data

Each letter carries three CSS custom properties set inline in JSX:

| Property | Role |
|---|---|
| `--peak-scale` | Max `scaleY` at beat peak |
| `--beat-dur` | Animation cycle length |
| `--beat-delay` | Initial phase offset (prevents sync at startup) |

```tsx
const BEAT_LETTERS = [
  // TEMPO
  { char: "T", i: 0, dur: "0.72s", peak: 1.42, delay: "0ms"   },
  { char: "E", i: 1, dur: "0.96s", peak: 1.24, delay: "240ms" },
  { char: "M", i: 2, dur: "0.64s", peak: 1.56, delay: "80ms"  },
  { char: "P", i: 3, dur: "0.88s", peak: 1.32, delay: "320ms" },
  { char: "O", i: 4, dur: "1.10s", peak: 1.18, delay: "160ms" },
  // HOUSE
  { char: "H", i: 5, dur: "0.76s", peak: 1.48, delay: "280ms" },
  { char: "O", i: 6, dur: "1.04s", peak: 1.28, delay: "120ms" },
  { char: "U", i: 7, dur: "0.60s", peak: 1.44, delay: "360ms" },
  { char: "S", i: 8, dur: "0.84s", peak: 1.22, delay: "40ms"  },
  { char: "E", i: 9, dur: "1.12s", peak: 1.36, delay: "200ms" },
];
```

Effective BPM range: 54 (slowest, 1.12s) to 100 (fastest, 0.60s).

### Animation declaration on `.bleedChar`

```css
.bleedChar {
  display: inline-block;
  transform-origin: center 100%;
  animation:
    tempoArrive 0.9s cubic-bezier(0.16, 1, 0.3, 1)
      calc(var(--i, 0) * 60ms) both,
    beatPulse var(--beat-dur, 0.8s) linear
      calc(1400ms + var(--beat-delay, 0ms)) infinite;
}
```

The `1400ms` base delay on `beatPulse` ensures all letters finish their arrive animation before the beat starts.

### JSX render pattern

```tsx
<span className={styles.bleedLine}>
  {BEAT_LETTERS.slice(0, 5).map((l) => (
    <span
      key={l.i}
      className={styles.bleedChar}
      style={{
        "--i": l.i,
        "--beat-dur": l.dur,
        "--peak-scale": l.peak,
        "--beat-delay": l.delay,
      } as CSSProperties}
    >
      {l.char}
    </span>
  ))}
</span>
```

### Reduced motion fallback

```css
@media (prefers-reduced-motion: reduce) {
  .bleedChar {
    animation: tempoArriveFast 0.3s ease-out calc(var(--i, 0) * 30ms) both !important;
    scale: 1 1 !important;
  }
}
@keyframes tempoArriveFast {
  from { opacity: 0; }
  to   { opacity: 1; }
}
```

---

## Re-implementation checklist

1. Add `@property --peak-scale` block to the CSS file (before any usage)
2. Replace `const TEMPO/HOUSE` arrays in `ParallaxHero.tsx` with `BEAT_LETTERS`
3. Update JSX render to pass `--beat-dur`, `--peak-scale`, `--beat-delay` per letter
4. Add `beatPulse` keyframe to CSS
5. Update `.bleedChar` animation declaration
6. Set `.bleedWrap { overflow: visible }`
7. Set `.bleedChar { transform-origin: center 100% }`
8. Add reduced-motion override

---

## Possible refinements before shipping

- **Tune the peak values** — current range 1.18–1.56 is quite aggressive at 36vw. Try 1.08–1.28 for a subtler read.
- **Evening-only mode** — activate beat only when `[data-tempo-act="evening"]` is set, keep letters static during day.
- **Sync to actual audio** — if the site ever plays ambient music, the `--peak-scale` values could be driven by a Web Audio `AnalyserNode` via JS for a live spectrum effect.
- **Canvas fallback** — for browsers that don't support `@property`, a `<canvas>` drawn behind the text is a solid fallback that also enables true audio sync.
