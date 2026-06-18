/**
 * Events Spaces Floor Plan Explorer
 *
 * Scoped to .page-events__spaces-fp (not .venue-floorplan) — no conflict with venue page.
 * Includes outdoor terrace zone + richer detail panel (no art frame).
 */
(function () {
  'use strict';

  var FLOOR_DATA = {
    level1: {
      label:    'Level 1 — The Gallery',
      title:    '60 standing · 30 seated.',
      desc:     'Column-free floor with neutral gallery walls and adjustable track lighting on a dimmer. No fixed furniture — the space gets completely out of the way of your event. Built for art exhibitions, which makes it equally suited to product launches, brand activations, and seated dinners that need real architecture behind them. 200m² of uninterrupted floor in the heart of District 3.',
      stats:    [
        { num: '60+',   unit: 'standing'   },
        { num: '30',    unit: 'seated'     },
        { num: '200m²', unit: 'floor area' }
      ],
      use:      'Product Launches · Exhibitions · Brand Activations · Seated Dinners',
      features: [
        'Column-free open plan — no obstructions',
        'Adjustable track lighting with dimmers',
        'Gallery-grade hanging rail system',
        'In-house sound system + wireless mic',
        'Neutral gallery walls — works with any aesthetic'
      ],
      url: '/art-gallery'
    },
    ground: {
      label:    'Ground Floor — Café & Bar',
      title:    '90 standing · 40 seated.',
      desc:     'The café and full cocktail bar floor with natural light from both ends and direct access to the outdoor area. Flexible seating layout, full bar service, and the lived-in architectural character of a converted District 3 shophouse. Works for cocktail receptions, seated dinners, and daytime buyouts with indoor–outdoor flow.',
      stats:    [
        { num: '90+',   unit: 'standing'   },
        { num: '40',    unit: 'seated'     },
        { num: '150m²', unit: 'floor area' }
      ],
      use:      'Cocktail Receptions · Seated Dinners · Daytime Buyouts · Terrace Events',
      features: [
        'Full cocktail bar included in hire',
        'Direct outdoor area access — indoor/outdoor flow',
        'Natural light from both street façades',
        'Flexible seating and layout configurations',
        'Street-level arrival — easy guest access'
      ],
      url: '/cafe'
    },
    level2: {
      label:    'Level 2 — Creators Studio',
      title:    'Content studio. Not for hire.',
      desc:     'Level 2 houses the in-house TEMPO content studio — used for brand shoots, podcast recordings, and creative production by TEMPO House and partner creators. This floor is not available for external event hire.',
      stats:    [
        { num: '—', unit: 'for hire'   },
        { num: '—', unit: 'bookings'   },
        { num: '—', unit: 'capacity'   }
      ],
      use:      'Content Studio · Brand Shoots · Podcast Recording · Partner Creators',
      features: [
        'In-house content creation studio',
        'Brand shoots and photographic production',
        'Podcast recording and video sessions',
        'Partner creator programme available',
        'Not available for external event hire'
      ],
      url: '/creators'
    },
    terrace: {
      label:    'Outdoor Area',
      title:    '80+ standing · 40 seated.',
      desc:     'The outdoor area sits beside the building at street level on Pasteur Street, District 3. Lounge seating for up to 40 guests, or open standing receptions for 80+. Available for pre-dinner cocktails, arrival drinks, and street-front brand activations. Included with Ground Floor hire or bookable independently.',
      stats:    [
        { num: '80+', unit: 'standing' },
        { num: '40',  unit: 'seated'   },
        { num: '—',   unit: 'open air' }
      ],
      use:      'Arrival Drinks · Cocktail Receptions · Street-Front Activations · Outdoor Overflow',
      features: [
        'Lounge seating for up to 40 guests',
        'Pasteur St frontage — District 3, HCMC',
        'Ideal for arrival drinks and event overflow',
        'Included with Ground Floor hire',
        'Bookable independently for standing events'
      ],
      url: '/cafe'
    }
  };

  var DEFAULT_FLOOR = 'level1';

  function init() {
    var section = document.querySelector('.page-events__spaces-fp');
    if (!section) return;

    var svgZones    = Array.from(section.querySelectorAll('.fp-floor'));
    var popup       = section.querySelector('.venue-floorplan__popup');
    var tabs        = Array.from(section.querySelectorAll('.venue-floorplan__tab'));

    var detailLabel  = section.querySelector('.venue-floorplan__detail-label');
    var detailTitle  = section.querySelector('.venue-floorplan__detail-title');
    var detailDesc   = section.querySelector('.venue-floorplan__detail-desc');
    var specsEl      = section.querySelector('.venue-floorplan__specs');
    var useCasesEl   = section.querySelector('.venue-floorplan__use-cases');
    var featuresEl   = section.querySelector('.venue-floorplan__features');
    var detailCtaEl  = section.querySelector('.venue-floorplan__detail-cta');
    var detailHeader = section.querySelector('.venue-floorplan__detail-header');

    var miniDetail = section.querySelector('.venue-floorplan__mini-detail');
    var miniLabel  = miniDetail && miniDetail.querySelector('.venue-floorplan__mini-label');
    var miniStats  = miniDetail && miniDetail.querySelector('.venue-floorplan__mini-stats');
    var miniLink   = miniDetail && miniDetail.querySelector('.venue-floorplan__mini-link');

    var activeFloor = DEFAULT_FLOOR;

    // SVG Y-coordinate centres for each floor zone (viewBox height 465)
    var ZONE_Y_SVG = { level2: 86, level1: 201, ground: 348, terrace: 355 };

    // ── Popup ──────────────────────────────────────

    function showPopup(floor) {
      if (!popup || !floor) return;
      var data = FLOOR_DATA[floor];
      if (!data) return;

      var labelEl = popup.querySelector('.venue-floorplan__popup-label');
      var statsEl = popup.querySelector('.venue-floorplan__popup-stats');
      var useEl   = popup.querySelector('.venue-floorplan__popup-use');

      if (labelEl) labelEl.textContent = data.label;
      if (useEl)   useEl.textContent   = data.use;
      if (statsEl) {
        statsEl.innerHTML = data.stats.map(function (s) {
          return '<span class="venue-floorplan__popup-stat">'
            + '<strong>' + s.num + '</strong>'
            + '<span>' + s.unit + '</span>'
            + '</span>';
        }).join('');
      }

      var svgEl     = section.querySelector('.venue-floorplan__svg');
      var svgWrapEl = section.querySelector('.venue-floorplan__svg-wrap');
      if (svgEl && svgWrapEl && ZONE_Y_SVG[floor] !== undefined) {
        var svgRect  = svgEl.getBoundingClientRect();
        var wrapRect = svgWrapEl.getBoundingClientRect();
        var fraction = ZONE_Y_SVG[floor] / 465;
        var zoneYPx  = svgRect.height * fraction;
        var centerPx = (svgRect.top - wrapRect.top) + zoneYPx;
        popup.style.top    = centerPx + 'px';
        popup.style.bottom = '';
        // Terrace zone is on the right of the SVG — flip popup to left side
        if (floor === 'terrace') {
          popup.style.right = 'auto';
          popup.style.left  = 'var(--space-5)';
        } else {
          popup.style.right = '';
          popup.style.left  = '';
        }
      }

      popup.classList.add('is-visible');
      popup.setAttribute('aria-hidden', 'false');
    }

    function hidePopup() {
      if (!popup) return;
      popup.classList.remove('is-visible');
      popup.setAttribute('aria-hidden', 'true');
    }

    // ── Mobile mini-detail ─────────────────────────

    function showMiniDetail(floor, url) {
      if (!miniDetail) return;
      var data = FLOOR_DATA[floor];
      if (!data) return;
      if (miniLabel) miniLabel.textContent = data.label;
      if (miniStats) {
        miniStats.innerHTML = data.stats.slice(0, 2).map(function (s) {
          return '<span class="venue-floorplan__mini-stat">'
            + '<strong>' + s.num + '</strong>'
            + '<span>' + s.unit + '</span>'
            + '</span>';
        }).join('');
      }
      if (miniLink) miniLink.href = url || data.url || '#';
      miniDetail.classList.add('is-visible');
    }

    // ── Detail panel ───────────────────────────────

    function transitionOut(els, done) {
      els.forEach(function (el) { if (el) el.classList.add('is-transitioning'); });
      setTimeout(done, 180);
    }

    function transitionIn(els) {
      els.forEach(function (el) { if (el) el.classList.remove('is-transitioning'); });
    }

    function updateDetail(floor) {
      var data = FLOOR_DATA[floor];
      if (!data) return;

      var transEls = [detailHeader, specsEl, useCasesEl, featuresEl, detailCtaEl].filter(Boolean);

      transitionOut(transEls, function () {
        if (detailLabel) detailLabel.textContent = data.label;
        if (detailTitle) detailTitle.textContent = data.title;
        if (detailDesc)  detailDesc.textContent  = data.desc;

        if (specsEl) {
          specsEl.innerHTML = data.stats.map(function (s) {
            return '<div class="venue-floorplan__spec-cell">'
              + '<p class="venue-floorplan__spec-num">' + s.num + '</p>'
              + '<p class="venue-floorplan__spec-unit">' + s.unit + '</p>'
              + '</div>';
          }).join('');
        }

        if (useCasesEl) {
          useCasesEl.innerHTML =
            '<span class="venue-floorplan__use-label">Best for</span>'
            + '<span class="venue-floorplan__use-text">' + data.use + '</span>';
        }

        if (featuresEl) {
          featuresEl.innerHTML = data.features.map(function (f) {
            return '<li class="venue-floorplan__feature-item">' + f + '</li>';
          }).join('');
        }

        if (detailCtaEl) {
          detailCtaEl.href        = data.url || '#';
          detailCtaEl.textContent = 'Explore ' + data.label + ' →';
        }

        requestAnimationFrame(function () {
          requestAnimationFrame(function () { transitionIn(transEls); });
        });
      });
    }

    // ── Activate floor ─────────────────────────────

    function setFloor(floor) {
      if (floor === activeFloor) return;
      activeFloor = floor;
      svgZones.forEach(function (z) {
        z.classList.toggle('is-active', z.dataset.floor === floor);
      });
      tabs.forEach(function (t) {
        t.classList.toggle('is-active', t.dataset.floor === floor);
        t.setAttribute('aria-selected', t.dataset.floor === floor ? 'true' : 'false');
      });
      updateDetail(floor);
    }

    // ── SVG zone events ─────────────────────────────

    var isMobile = window.matchMedia('(max-width: 1000px)');

    svgZones.forEach(function (zone) {
      var floor = zone.dataset.floor;
      var url   = zone.dataset.url;

      zone.addEventListener('mouseenter', function () {
        if (isMobile.matches) {
          showMiniDetail(floor, url);
          svgZones.forEach(function (z) { z.classList.toggle('is-active', z.dataset.floor === floor); });
          return;
        }
        showPopup(floor);
      });

      zone.addEventListener('mouseleave', function () { hidePopup(); });

      zone.addEventListener('click', function () {
        if (isMobile.matches) {
          showMiniDetail(floor, url);
          svgZones.forEach(function (z) { z.classList.toggle('is-active', z.dataset.floor === floor); });
          return;
        }
        setFloor(floor);
        hidePopup();
      });

      zone.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          if (isMobile.matches) {
            showMiniDetail(floor, url);
            svgZones.forEach(function (z) { z.classList.toggle('is-active', z.dataset.floor === floor); });
            return;
          }
          setFloor(floor);
        }
      });
    });

    // ── Mobile tab events ───────────────────────────

    tabs.forEach(function (tab) {
      tab.addEventListener('click', function () { setFloor(tab.dataset.floor); });
    });

    // ── Init default state ─────────────────────────

    svgZones.forEach(function (z) {
      z.classList.toggle('is-active', z.dataset.floor === DEFAULT_FLOOR);
    });
    tabs.forEach(function (t) {
      t.classList.toggle('is-active', t.dataset.floor === DEFAULT_FLOOR);
      t.setAttribute('aria-selected', t.dataset.floor === DEFAULT_FLOOR ? 'true' : 'false');
    });
    updateDetail(DEFAULT_FLOOR);

    if (popup) {
      popup.setAttribute('aria-hidden', 'true');
      popup.setAttribute('aria-live', 'polite');
    }

    var defaultZone = svgZones.filter(function (z) { return z.dataset.floor === DEFAULT_FLOOR; })[0];
    showMiniDetail(DEFAULT_FLOOR, defaultZone ? defaultZone.dataset.url : '');
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () { init(); initWallCarousel(); });
  } else {
    init();
    initWallCarousel();
  }
})();

// ── Wall gallery carousel (mobile auto-loop) ────────────────────────────────
(function () {
  'use strict';

  function initWallCarousel() {
    var track   = document.querySelector('.page-events__wall-frames');
    var dotsEl  = document.querySelector('.page-events__wall-dots');
    var prevBtn = document.querySelector('.page-events__wall-prev');
    var nextBtn = document.querySelector('.page-events__wall-next');
    if (!track) return;

    var items = Array.from(track.querySelectorAll('.page-events__wall-frame'));
    if (items.length === 0) return;

    var dots      = [];
    var autoTimer = null;
    var isMobileQ = window.matchMedia('(max-width: 999px)');

    if (dotsEl) {
      items.forEach(function (_, i) {
        var dot = document.createElement('button');
        dot.className = 'page-events__wall-dot';
        if (i === 0) dot.classList.add('page-events__wall-dot--active');
        dot.setAttribute('aria-label', 'Go to slide ' + (i + 1));
        dot.addEventListener('click', function () { stopAuto(); goTo(i); startAuto(); });
        dotsEl.appendChild(dot);
        dots.push(dot);
      });
    }

    function getPad() {
      return parseFloat(getComputedStyle(track).paddingInlineStart) || 0;
    }

    function getIndex() {
      var snap = track.getBoundingClientRect().left + getPad();
      var best = 0, bestDist = Infinity;
      items.forEach(function (el, i) {
        var d = Math.abs(el.getBoundingClientRect().left - snap);
        if (d < bestDist) { bestDist = d; best = i; }
      });
      return best;
    }

    function goTo(i) {
      var idx = ((i % items.length) + items.length) % items.length;
      var el  = items[idx];
      var left = el.getBoundingClientRect().left - track.getBoundingClientRect().left + track.scrollLeft - getPad();
      track.scrollTo({ left: left, behavior: 'smooth' });
    }

    function updateDots(i) {
      dots.forEach(function (d, idx) {
        d.classList.toggle('page-events__wall-dot--active', idx === i);
      });
    }

    track.addEventListener('scroll', function () { updateDots(getIndex()); }, { passive: true });

    function startAuto() {
      if (!isMobileQ.matches) return;
      stopAuto();
      autoTimer = setInterval(function () { goTo(getIndex() + 1); }, 3800);
    }

    function stopAuto() { clearInterval(autoTimer); autoTimer = null; }

    if (prevBtn) prevBtn.addEventListener('click', function () { stopAuto(); goTo(getIndex() - 1); startAuto(); });
    if (nextBtn) nextBtn.addEventListener('click', function () { stopAuto(); goTo(getIndex() + 1); startAuto(); });

    track.addEventListener('touchstart', stopAuto, { passive: true });
    track.addEventListener('touchend', function () { setTimeout(startAuto, 1200); }, { passive: true });
    track.addEventListener('mouseenter', stopAuto, { passive: true });
    track.addEventListener('mouseleave', startAuto, { passive: true });

    isMobileQ.addEventListener('change', function (e) {
      if (e.matches) { startAuto(); } else { stopAuto(); }
    });

    startAuto();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initWallCarousel);
  } else {
    initWallCarousel();
  }
})();
