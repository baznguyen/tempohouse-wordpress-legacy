# Booking Widget — Component Map

CSS file: `WordPress/plugins/tempohouse-reservations/assets/css/booking-widget.css`

All colours inherit from `tokens.css` via CSS custom properties scoped to `.thr-widget`.
Night/afternoon theme overrides are included — the widget adapts automatically.

---

## Colour mapping: UI Base → TEMPO House

| UI Base (purple) | TEMPO token | Value (day) |
|---|---|---|
| Primary action `#5B4FCF` | `--color-accent` (terracotta) | `#7C3B3B` |
| Background lavender `#E8E4F8` | `--color-bg` (cream) | `#F7F3EE` |
| Card white | `--thr-surface` | `#FFFFFF` |
| Sunken surface | `--thr-surface-sunken` | `#F0EBE3` |
| Scarce-slot amber | `--tempo-amber` | `#DDAA62` |
| Unavailable grey | `--color-text-muted` | `rgba(26,24,22,0.45)` |
| Primary text | `--color-text-primary` | `#1A1816` |
| Secondary text | `--color-text-secondary` | `rgba(26,24,22,0.72)` |

Night mode replaces all: terracotta → gold `#C49A3A`, surfaces → near-black, text → warm cream.

---

## Typography

| Use | Font | Token |
|---|---|---|
| Labels, buttons, chips, metadata | Bricolage Grotesque | `--font-display` |
| Headings (confirm screen, section titles) | Cormorant Garamond | `--font-accent` |
| Form inputs, body text | Space Grotesk | `--font-body` |

---

## Component HTML Snippets

### Date Chip
```html
<div class="thr-date-rail">
  <button class="thr-date-chip thr-date-chip--past" type="button" disabled>
    <span class="thr-date-chip__day">Fri</span>
    <span class="thr-date-chip__num">13</span>
    <span class="thr-date-chip__mon">Jun</span>
  </button>
  <button class="thr-date-chip thr-date-chip--available" type="button">
    <span class="thr-date-chip__day">Mon</span>
    <span class="thr-date-chip__num">16</span>
    <span class="thr-date-chip__mon">Jun</span>
    <span class="thr-date-chip__dot"></span>
  </button>
  <button class="thr-date-chip thr-date-chip--selected" type="button" aria-pressed="true">
    <span class="thr-date-chip__day">Tue</span>
    <span class="thr-date-chip__num">17</span>
    <span class="thr-date-chip__mon">Jun</span>
    <span class="thr-date-chip__dot"></span>
  </button>
  <button class="thr-date-chip thr-date-chip--full" type="button">
    <span class="thr-date-chip__day">Wed</span>
    <span class="thr-date-chip__num">18</span>
    <span class="thr-date-chip__mon">Jun</span>
  </button>
  <button class="thr-date-rail__more" type="button" aria-label="View full calendar">›</button>
</div>
```

**States:** `--available` (green dot), `--selected` (terracotta fill), `--full` (dimmed), `--past` / `--closed` (opacity 0.3, pointer-events none)

---

### Period Toggle
```html
<div class="thr-period-toggle" role="group" aria-label="Dining period">
  <button class="thr-period-toggle__opt" type="button" data-period="lunch">Lunch</button>
  <button class="thr-period-toggle__opt thr-period-toggle__opt--active" type="button" data-period="dinner">Dinner</button>
</div>
```

Toggle by adding/removing `--active` class. Drives which time slots are loaded.

---

### Time Pills
```html
<div class="thr-time-grid">
  <button class="thr-time-pill" type="button">11:30 AM</button>
  <button class="thr-time-pill thr-time-pill--scarce" type="button">12:00 PM</button>
  <button class="thr-time-pill thr-time-pill--selected" type="button" aria-pressed="true">12:30 PM</button>
  <button class="thr-time-pill thr-time-pill--unavailable" type="button" disabled>1:00 PM</button>
</div>
```

**States:** default · `--scarce` (amber text + dot, ≤2 tables) · `--selected` (terracotta fill) · `--unavailable` (strikethrough, not clickable)

---

### Guest Stepper
```html
<div class="thr-stepper" role="group" aria-label="Number of guests">
  <button class="thr-stepper__btn" type="button" aria-label="Decrease guests" id="guests-dec">−</button>
  <span class="thr-stepper__val" id="guests-val" aria-live="polite">2</span>
  <button class="thr-stepper__btn" type="button" aria-label="Increase guests" id="guests-inc">+</button>
</div>
```

Min/max enforced via JS. When value ≥ `private_room_min_party` (from config), animate private room checkbox in.

---

### Private Room Reveal
```html
<!-- Hidden by default, toggled by JS when guest count hits threshold -->
<div class="thr-private-room thr-private-room--hidden" id="private-room-wrap">
  <label class="thr-checkbox-row">
    <input class="thr-checkbox" type="checkbox" name="private_room" id="private-room">
    <span class="thr-checkbox-label">
      Request Private Room
      <span class="thr-checkbox-label__sub">Available for parties of 12–15 guests</span>
    </span>
  </label>
</div>
```

Toggle class `--hidden` ↔ `--visible` on `#private-room-wrap`.

---

### Form Field
```html
<div class="thr-field">
  <label class="thr-field__label" for="name">
    Full Name <span class="thr-field__required">*</span>
  </label>
  <input class="thr-field__input" type="text" id="name" name="diner_name"
         autocomplete="name" placeholder="Your name">
  <span class="thr-field__error" hidden>Please enter your full name.</span>
</div>
```

Add `.thr-field--error` to the wrapper to activate error styling. Remove `hidden` from error span.

---

### Phone Field
```html
<div class="thr-field">
  <label class="thr-field__label" for="phone">Phone Number</label>
  <div class="thr-phone-wrap">
    <button class="thr-phone-prefix" type="button" aria-label="Country code: Vietnam +84">
      <span class="thr-phone-prefix__flag">🇻🇳</span>
      <span class="thr-phone-prefix__code">+84</span>
    </button>
    <input class="thr-field__input thr-phone-input" type="tel" id="phone"
           name="diner_phone" autocomplete="tel-national"
           placeholder="_ _ _ — _ _ _ — _ _ _">
  </div>
  <label class="thr-zalo-opt">
    <input type="checkbox" name="use_zalo"> 
    <span class="thr-zalo-opt__label">Contact me via Zalo</span>
  </label>
</div>
```

---

### Select / Dropdown
```html
<div class="thr-field">
  <label class="thr-field__label" for="occasion">Special Occasion</label>
  <div class="thr-select-wrap">
    <select class="thr-field__select" id="occasion" name="occasion">
      <option value="">None</option>
      <option value="birthday">Birthday</option>
      <option value="anniversary">Anniversary</option>
      <option value="corporate">Corporate</option>
    </select>
  </div>
</div>
```

Arrow caret injected via CSS `::after` on `.thr-select-wrap`. No JS needed.

---

### Buttons
```html
<!-- Primary -->
<button class="thr-btn thr-btn--primary" type="submit">Reserve Now</button>

<!-- Secondary (outline) -->
<button class="thr-btn thr-btn--secondary" type="button">Back</button>

<!-- Ghost (low emphasis) -->
<button class="thr-btn thr-btn--ghost" type="button">Modify booking</button>

<!-- Small variant -->
<button class="thr-btn thr-btn--primary thr-btn--sm" type="button">Apply</button>

<!-- Split row -->
<div class="thr-btn-row">
  <button class="thr-btn thr-btn--ghost" type="button">Modify</button>
  <button class="thr-btn thr-btn--primary" type="button">Done</button>
</div>

<!-- Back text button -->
<button class="thr-btn-back" type="button">
  <span class="thr-btn-back__arrow">←</span> Back
</button>
```

---

### Confirmation Card
```html
<div class="thr-confirm">
  <!-- Full-bleed hero image (venue photo, period-matched) -->
  <div class="thr-confirm__hero">
    <img src="dinner-hero.jpg" alt="TEMPO House — Dinner">
  </div>

  <div class="thr-confirm__body">
    <div class="thr-countdown">In 4 days</div>

    <h2 class="thr-confirm__heading">Your table is reserved</h2>
    <p class="thr-confirm__sub">See you Friday evening</p>

    <div class="thr-confirm__grid">
      <div class="thr-confirm__item">
        <span class="thr-confirm__item-label">Date</span>
        <span class="thr-confirm__item-value">20 Jun 2026</span>
      </div>
      <div class="thr-confirm__item">
        <span class="thr-confirm__item-label">Time</span>
        <span class="thr-confirm__item-value">7:30 PM</span>
      </div>
      <div class="thr-confirm__item">
        <span class="thr-confirm__item-label">Guests</span>
        <span class="thr-confirm__item-value">4 people</span>
      </div>
      <div class="thr-confirm__item">
        <span class="thr-confirm__item-label">Reference</span>
        <span class="thr-confirm__item-value">
          <span class="thr-confirm__ref">TH-4821</span>
          <button class="thr-copy-btn" type="button" aria-label="Copy reference number">
            <!-- 16×16 copy SVG icon -->
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
              <rect x="5" y="5" width="8" height="9" rx="1.5" stroke="currentColor" stroke-width="1.4"/>
              <path d="M3 11V3.5A1.5 1.5 0 0 1 4.5 2H11" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
            </svg>
          </button>
        </span>
      </div>
    </div>

    <!-- Footer: email confirmation note -->
    <p class="thr-field__hint" style="margin-bottom:20px">
      Confirmation sent to your email.
    </p>

    <!-- Action row -->
    <div class="thr-confirm__actions">
      <div class="thr-confirm__icon-row">
        <!-- Add to calendar -->
        <button class="thr-icon-btn" type="button" aria-label="Add to calendar">
          <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
            <rect x="2" y="4" width="14" height="12" rx="2" stroke="currentColor" stroke-width="1.4"/>
            <path d="M2 7h14M6 2v4M12 2v4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
          </svg>
        </button>
        <!-- Call -->
        <button class="thr-icon-btn" type="button" aria-label="Call venue">
          <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
            <path d="M3 3h3l1.5 4L6 8.5c1 2 3 4 5 5l1.5-1.5L16 13.5V17c-8 0-14-6-13-14z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
          </svg>
        </button>
        <!-- Directions -->
        <button class="thr-icon-btn" type="button" aria-label="Get directions">
          <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
            <path d="M9 2C6.24 2 4 4.24 4 7c0 3.75 5 9 5 9s5-5.25 5-9c0-2.76-2.24-5-5-5z" stroke="currentColor" stroke-width="1.4"/>
            <circle cx="9" cy="7" r="1.5" stroke="currentColor" stroke-width="1.4"/>
          </svg>
        </button>
      </div>

      <div class="thr-btn-row" style="width:auto;gap:8px">
        <button class="thr-btn thr-btn--ghost thr-btn--sm" type="button">Modify</button>
        <button class="thr-btn thr-btn--primary thr-btn--sm" type="button">Done</button>
      </div>
    </div>
  </div>
</div>
```

---

### Alternatives Strip
```html
<div class="thr-alternatives">
  <span class="thr-alternatives__label">Other available times</span>
  <div class="thr-alternatives__list">
    <button class="thr-alt-pill" type="button">
      <span class="thr-alt-pill__date">Fri 20</span>
      <span class="thr-alt-pill__time">6:00 PM</span>
    </button>
    <button class="thr-alt-pill" type="button">
      <span class="thr-alt-pill__date">Sat 21</span>
      <span class="thr-alt-pill__time">7:30 PM</span>
    </button>
  </div>
</div>
```

---

## Progress Indicator

```html
<div class="thr-progress">
  <div class="thr-progress__step thr-progress__step--done"></div>
  <div class="thr-progress__step thr-progress__step--active"></div>
  <div class="thr-progress__step"></div>
  <span class="thr-progress__label">Step 2 of 3</span>
</div>
```

---

## Section spacing

Wrap each logical group in `.thr-fields` for consistent vertical gap between form rows.
Use `<hr class="thr-section-divider">` between distinct sections.

---

## Animation classes

| Class | Effect |
|---|---|
| `.thr-animate-up` | Fade in from below (single element) |
| `.thr-animate-pop` | Scale-in pop (confirmation card reveal) |
| `.thr-stagger` | Wrapper: children fade in sequentially (up to 5, 60ms apart) |
| `.thr-skeleton` | Shimmer placeholder while loading slots |

---

## Next screens to design

1. **Screen 1** — Entry (Modify/Cancel + Book, with inline lookup expand)
2. **Screen 2** — Step 1/3 Booking: Date rail → Period toggle → Time grid → Stepper + private room
3. **Screen 3** — Step 2/3 Guest details: Name, Email, Phone (+ Zalo) + Optional section
4. **Screen 4** — Review (summary before submitting)
5. **Screen 5** — Confirmation card
