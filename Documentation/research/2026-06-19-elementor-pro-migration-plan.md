# Elementor Pro Migration Plan — TEMPO House
**Date:** 2026-06-19  
**Status:** Research complete, ready to implement

---

## Decision Summary

Elementor Pro is a viable migration path. The existing CSS design system and all custom JS modules are compatible with Elementor without modification. Interactive components (carousels, floor plan, hero animation) work via Elementor's Custom HTML widget, which renders CSS visually in the editor canvas (JS effects only activate on the live frontend — same trade-off as Gutenberg's HTML block, but better since CSS layout is visible).

**What changes:** Visual page layout, header, and footer are rebuilt in Elementor's canvas. PHP templates become inert.  
**What stays identical:** All `assets/css/`, all `assets/js/`, `inc/` PHP, ACF fields, CPTs, REST endpoints, the design system.

---

## Component Coverage

| Component | Approach | Notes |
|---|---|---|
| Sticky header | Elementor Theme Builder (native) | Zero JS required |
| Full-screen drawer nav | Custom HTML widget in header template | Existing nav.js loads via functions.php |
| Time-of-day popup | Custom HTML widget in header template | time-switcher.js unchanged |
| FAQ flip cards | Elementor Flip Box widget (native) | 3D click-to-flip = built-in; no custom HTML needed |
| Event cards (ACF Loop Grid) | Loop Grid widget + ACF dynamic tags | Native; taxonomy filter for active/upcoming |
| Mixed post type loop (CPT + tagged posts) | Loop Grid + `elementor/query` PHP hook | ~10 lines in functions.php |
| Tempo-frame hover + `--dl-*` CSS vars | Custom HTML widget | CSS vars work; `overflow-y:clip` via Custom CSS tab |
| Hero character animation | Custom HTML widget | `--i` per-span markup unchanged |
| Horizontal carousels (bar, café, gallery walk) | Custom HTML widget | Existing JS + markup unchanged; Swiper drag ≠ CSS scroll-snap |
| SVG interactive floor plan | Custom HTML widget | Elementor Hotspot widget is raster-only; custom HTML required |
| Gallery masonry grid | Elementor Gallery widget (native) | column-count masonry = native |
| Contact / reservation forms | Elementor Form widget + Custom Action class | POST to `/wp-json/tempohouse/v1/enquiry` |
| Time-switcher day/night mode | No change — enqueue via functions.php | No Elementor conflict with `data-tempo-time` |
| ACF event fields (date, time, category) | Dynamic Tags on single event template | Native; real values visible in editor canvas |

---

## Architecture Decisions

### CSS Loading Strategy
Do NOT paste the design system into Elementor's Global CSS panel. Keep all CSS files enqueued via `functions.php` but switch the action hook:

```php
// Use elementor/frontend/after_enqueue_styles to load after Elementor's widget CSS
// This ensures source-order cascade wins for identical specificity selectors
add_action( 'elementor/frontend/after_enqueue_styles', function() {
    $ver = '3.75.1';
    $uri = get_stylesheet_directory_uri();
    wp_enqueue_style( 'tempo-tokens', $uri . '/assets/css/tokens.css', [], $ver );
    wp_enqueue_style( 'tempo-base',   $uri . '/assets/css/base.css',   ['tempo-tokens'], $ver );
    wp_enqueue_style( 'tempo-nav',    $uri . '/assets/css/components/nav.css', ['tempo-base'], $ver );
    // ... all other stylesheets
});
```

**Why:** Elementor's widget CSS loads at step 4 in the cascade. Theme CSS at step 3 (default) loses source-order to Elementor. Shifting to `elementor/frontend/after_enqueue_styles` pushes theme CSS to load after widget CSS, winning the cascade without needing `!important`.

### JS Loading Strategy
Keep all JS enqueued via `wp_enqueue_scripts` in `functions.php`. Add Elementor editor hook for components that initialise on `DOMContentLoaded`:

```js
// Wrap each JS initializer:
function initTempoCarousel() { /* existing code */ }

if ( window.elementorFrontend ) {
    window.elementorFrontend.hooks.addAction('frontend/element_ready/global', initTempoCarousel);
} else {
    document.addEventListener('DOMContentLoaded', initTempoCarousel);
}
```

This ensures carousels and JS interactions reinitialise if Elementor re-renders a widget in the editor.

### Base Theme
Switch to **Hello Elementor child theme**. Move all `functions.php` PHP logic into the child theme's `functions.php`. ACF field definitions, CPT registration, REST endpoints, SEO meta — all stay in `inc/` files, required from the child theme.

**Child theme structure:**
```
tempohouse-child/
├── functions.php          ← requires all inc/ files; CSS/JS enqueues
├── style.css              ← child theme declaration
└── inc/ → symlink or copy from parent theme inc/
```

### Custom Form Action (Reservation + Contact forms)
Register a named Elementor Form Action that routes submissions to the existing REST endpoint:

```php
class THR_Elementor_Form_Action extends \ElementorPro\Modules\Forms\Classes\Action_Base {
    public function get_name()  { return 'thr_enquiry'; }
    public function get_label() { return 'THR Enquiry API'; }

    public function run( $record, $ajax_handler ) {
        $fields = $record->get('fields');
        $type   = $record->get_form_settings('form_name') === 'Reservation' ? 'reservation' : 'contact';

        wp_remote_post( rest_url('tempohouse/v1/enquiry'), [
            'body'    => wp_json_encode([
                'type'    => $type,
                'name'    => $fields['name']['value']    ?? '',
                'email'   => $fields['email']['value']   ?? '',
                'phone'   => $fields['phone']['value']   ?? '',
                'message' => $fields['message']['value'] ?? '',
                'date'    => $fields['date']['value']    ?? '',
                'guests'  => $fields['guests']['value']  ?? '',
            ]),
            'headers' => ['Content-Type' => 'application/json'],
        ]);
    }

    public function register_settings_section( $widget ) {}
}

add_action( 'elementor_pro/forms/actions/register', function( $registrar ) {
    $registrar->register( new THR_Elementor_Form_Action() );
});
```

### Mixed Post Type Event Query
For the What's On page — Loop Grid mixing CPT `event` + standard posts tagged `event`:

```php
add_action( 'elementor/query/tempo_events_all', function( $query ) {
    $query->set( 'post_type', ['event', 'post'] );
    $query->set( 'tax_query', [[
        'taxonomy' => 'post_tag',
        'field'    => 'slug',
        'terms'    => 'event',
        'operator' => 'IN',
    ]]);
    $query->set( 'orderby', 'meta_value' );
    $query->set( 'meta_key', 'event_date' );
    $query->set( 'order', 'ASC' );
});
```

Set `tempo_events_all` as the Query ID in the Loop Grid widget.

---

## Migration Phases

### Phase 0 — Setup (1 day)
1. Purchase Elementor Pro Essential ($59/yr) — single production site; localhost `.local` domain is license-exempt
2. Install Elementor + Elementor Pro on localhost Docker environment
3. Create Hello Elementor child theme; migrate all `functions.php` / `inc/` logic
4. Enable Elementor experiments:
   - Settings → Experiments → **Flexbox Container** → Active
   - Settings → Experiments → **Optimized DOM Output** → Active
   - Settings → Experiments → **Improved Asset Loading** → Active
5. Run Tools → Regenerate CSS & Data
6. Update CSS enqueue hook to `elementor/frontend/after_enqueue_styles`
7. Add Elementor frontend init hook to each JS module
8. Register Custom Form Action class

### Phase 1 — Header + Footer (2 days)
**Header template** (Theme Builder → Header):
- Sticky container → Custom HTML widget (full nav drawer markup: brand, links, hamburger, drawer overlay, time-popup)
- JS loads automatically via `wp_enqueue_scripts` — no Elementor involvement needed
- Set sticky: Advanced → Motion Effects → Sticky: Top

**Footer template** (Theme Builder → Footer):
- Brand statement → Heading widget (editable)
- "TEMPO HOUSE · EST. 2026" line → Text widget
- 3-column nav → Nav Menu widgets (3 columns, each pointing to a registered footer menu)
- Address + hours → Text widgets linked to ACF Options fields via dynamic tags
- Instagram + email → Button/Text widgets (editable)
- Newsletter CTA → Custom HTML widget (Klaviyo fetch form)

**Verify:** Time-switcher popup, drawer open/close, sticky behaviour on scroll.

### Phase 2 — Simple Pages (3–4 days)

**FAQ page** (simplest — flip cards are native):
- Banner → Heading + Text widgets
- 18 FAQ cards across 4 categories → Flip Box widget (Box Click trigger), one per card, arranged in grid
- Enquiry CTA → Heading + Button widgets

**Contact page:**
- Banner → Heading widget
- Map → HTML widget (Google Maps iframe)
- Contact form → Elementor Form widget with THR Custom Action
- Getting Here (3-col) → 3-column container → Text widgets

**Gallery page:**
- Banner → Heading + Text widgets
- Catalogue strip (4 stat items) → 4-column container → Heading + Text widgets each
- Gallery wall (3 frames) → Custom HTML widget (existing 3-frame markup + moods.js)
- Programme statement → Quote widget + Button widgets
- For Artists section → Heading + Text + List + Button widgets
- Gallery walk carousel → Custom HTML widget (existing markup + page-gallery.js)
- Manifesto strip → Custom HTML (design-only element)
- Space hire section → Heading + Text + List + Button widgets
- Info strip (4-col) → 4-column container → Heading + Text widgets

### Phase 3 — Content Pages (4–5 days)

**Bar page:**
- Banner → Heading + Text widgets
- Provenance strip → Custom HTML (CSS grid-dependent)
- Cocktail Programme text + image → Text widgets + Custom HTML (tempo-frame)
- Signatures carousel → Custom HTML (page-bar.js)
- Manifesto strip → Custom HTML
- Happy Hour → Custom HTML (JS targets `.page-bar__hh-time`)
- Wine list + Night Programme + Atmosphere → Text + List widgets
- Info strip → 3-col container → Text widgets
- Footer CTA → Heading + Button widgets

**Café page:**
- Banner, provenance, brew methods → Heading/Text widgets + Custom HTML
- Coffee section → Text + List + Button widgets; image = Custom HTML (tempo-frame)
- Matcha carousel → Custom HTML (page-cafe.js)
- Kitchen + Space sections → Text + List widgets
- Rhythm timeline → Custom HTML (CSS grid)
- Info strip → Text widgets

**Venue page:**
- Banner → Heading + Button widgets
- Capacity strip (3-col) → 3-column container → Heading + Text
- Building image → Custom HTML (tempo-frame)
- Floor plan SVG → Custom HTML (full SVG + venue-floorplan.js initialiser)
- Neighbourhood + Contact strip → Text widgets

### Phase 4 — Events Pages (3–4 days)

**What's On page:**
- Loop Grid widget, Query ID: `tempo_events_all` (mixed CPT + tagged posts)
- Loop item template: event-card markup with ACF dynamic tags (image, title, date, category, time)
- Taxonomy Filter widget for active/upcoming filtering
- Subscribe strip → Custom HTML (Klaviyo)

**Events/Private Events page:**
- Banner + Buttons → Heading + Button widgets
- Elevation diagram → Custom HTML (SVG + events-spaces.js)
- Full Venue callout → Heading + Button widgets
- What We Host wall → Custom HTML (events-spaces.js Part 2)
- Catering → Text + List + Custom HTML (tempo-frame)
- FAQ excerpt (3 Q&A, non-flip) → Text widgets
- Enquiry CTA → Heading + Button widgets

**Single event template** (Theme Builder → Single → CPT event):
- Poster hero → Featured Image widget (full-width, `event-poster` size)
- Category → Dynamic Tag (ACF `event_category`)
- Title → Post Title widget
- Date / Time / Admission meta bar → Text widgets with ACF dynamic tags
- Body → Post Content widget (uses locked block template from cpt-events.php)
- Ticket + Reserve CTAs → Button widgets (Ticket = conditional via Dynamic Visibility)
- Related events strip → Loop Grid (3 posts, same CPT, exclude current)

### Phase 5 — Homepage (1–2 days)
The most JS-heavy page — all sections are Custom HTML widgets except the footer CTA:

- Hero → Custom HTML (existing hero.js letter animation markup)
- Moods carousel → Custom HTML (existing moods.php query + moods.js)
- Events carousel → Custom HTML (existing events.php query + events.js)
- Reserve CTA section → Heading + Button widgets (native — fully editable)
- Newsletter → Custom HTML (Klaviyo)

### Phase 6 — Performance + QA (2 days)
- Enable Elementor's Asset Loading experiments if not already active
- Audit with Chrome DevTools: target 90+ desktop, 75–85 mobile Lighthouse
- Add LCP preload hint for hero background (Elementor doesn't do this automatically):
  ```php
  add_action( 'wp_head', function() {
      if ( is_front_page() ) {
          echo '<link rel="preload" as="image" href="' . get_template_directory_uri() . '/assets/images/hero-bg.jpg" fetchpriority="high">';
      }
  }, 1 );
  ```
- Verify: all JS interactions on live frontend (time-switcher, all carousels, floor plan, flip cards, forms)
- Verify: all ACF dynamic fields render correctly on single event template
- Verify: Loop Grid shows both CPT events and tagged posts on What's On page
- Verify: Form submissions reach `tempohouse/v1/enquiry` endpoint

---

## Elementor Pro Licensing

| Environment | Domain pattern | License consumed? |
|---|---|---|
| Localhost Docker | `tempohouse.local` or `.test` | **No** — exempt |
| Production | `tempohouse.com.vn` | **Yes** |
| Staging (SiteGround) | `staging.tempohouse.com.vn` | **Yes** |

**Recommendation:** Essential plan ($59/yr, 1 site) covers production. Develop on `.local` (free). If a staging site is needed on a live subdomain, upgrade to Advanced ($99/yr, 3 sites).

---

## What the Marketing Team Gains

| After migration | Editor experience |
|---|---|
| All headings, body copy, buttons | Click to edit inline, see live |
| Images (non-JS sections) | Click image → replace from Media Library |
| FAQ flip cards | Each card editable front + back in panel |
| Event cards (title, date, category, image) | Edit in WordPress Events CPT editor (ACF sidebar) |
| Contact/Reservation forms | Edit fields in Elementor panel |
| Carousels, hero animation, floor plan | Visible as rendered HTML, not editable (developer task) |

---

## Estimated Timeline

| Phase | Work | Days |
|---|---|---|
| 0. Setup | Install, child theme, hooks, custom action | 1 |
| 1. Header + Footer | Theme Builder templates | 2 |
| 2. Simple pages | FAQ, Contact, Gallery | 3–4 |
| 3. Content pages | Bar, Café, Venue | 4–5 |
| 4. Events pages | What's On, Events, Single Event | 3–4 |
| 5. Homepage | Hero + carousels | 1–2 |
| 6. Performance + QA | Lighthouse, full UX pass | 2 |
| **Total** | | **~16–20 days** |

---

## Known Limitations (Accept Before Starting)

1. **JS effects in editor canvas** — Carousels, hover animations, and floor plan will appear as static HTML in the Elementor editor. CSS layout/styles are visible; JS interactions are not. This is identical to the Gutenberg limitation and unavoidable without rebuilding each component as an Elementor widget (a much larger investment).

2. **Full-screen drawer nav** — Elementor's Nav Menu widget does not produce a true full-screen overlay. The drawer is delivered via Custom HTML widget — non-editable by marketing, developer-only change.

3. **Page performance overhead** — Elementor adds ~200–300KB payload vs the hand-coded theme. With Improved Asset Loading enabled, real-world difference is ~100–150KB per page. Mobile Lighthouse 75–85 is realistic (was 90+ with pure PHP).

4. **AJAX filtering on mixed post type Loop Grid** — The Taxonomy Filter widget may be unstable when combined with a custom `elementor/query` hook (known GitHub bug). Test the What's On filter with real event data before launch.
