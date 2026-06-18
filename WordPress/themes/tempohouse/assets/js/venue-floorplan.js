/**
 * Venue Floor Plan Explorer
 *
 * Handles the interactive building elevation SVG and floor detail panel.
 * - Desktop: hover .fp-floor zones → popup; click → update detail panel
 * - Mobile: .venue-floorplan__tab buttons → update detail panel
 *
 * Floor data is read from the SVG zones' data-floor attributes.
 * When real drawings are provided, update the plan src in FLOOR_DATA.
 */
(function () {
  'use strict';

  // ── Floor data ──────────────────────────────────
  // Update plan paths when real drawings are provided.
  var FLOOR_DATA = {
    level1: {
      label:  'Level 1 — The Gallery',
      title:  'Column-free gallery floor.',
      desc:   'No columns, neutral walls, adjustable track lighting. Full-width terrace connection on the rear. Purpose-built to hold exhibitions without competing with them — works equally for events that need real architecture. Holds 60+ standing or 30 seated.',
      stats:  [
        { num: '60+',   unit: 'standing'   },
        { num: '30',    unit: 'seated'     },
        { num: '200m²', unit: 'floor area' }
      ],
      use:    'Launches · exhibitions · activations · seated dinners',
      plan:   '' // set to image path when available
    },
    ground: {
      label:  'Ground Floor — Café & Bar',
      title:  'Counter service, day and night.',
      desc:   'Café counter and cocktail bar in one open room. Natural light from both ends, flexible seating, direct outdoor area access. Bar and catering operate from here during full-venue hire. Holds 90+ standing or 40 seated.',
      stats:  [
        { num: '90+',   unit: 'standing'   },
        { num: '40',    unit: 'seated'     },
        { num: '150m²', unit: 'floor area' }
      ],
      use:    'Cocktail receptions · seated dinners · buyout events',
      plan:   ''
    },
    level2: {
      label:  'Level 2 — Creators Studio',
      title:  'Content studio. Not for hire.',
      desc:   'Level 2 houses the in-house TEMPO content studio — used for brand shoots, podcast recordings, and creative production by TEMPO House and partner creators. This floor is not available for external event hire.',
      stats:  [
        { num: '—', unit: 'for hire'  },
        { num: '—', unit: 'bookings'  },
        { num: '—', unit: 'capacity'  }
      ],
      use:    'Content studio · brand shoots · podcast · partner creators',
      plan:   ''
    },
    terrace: {
      label:  'Outdoor Area',
      title:  '80+ standing · 40 seated.',
      desc:   'The outdoor area sits beside the building at street level on Pasteur Street. Lounge seating for up to 40 guests, or open standing receptions for 80+. Included with Ground Floor hire or bookable independently.',
      stats:  [
        { num: '80+', unit: 'standing' },
        { num: '40',  unit: 'seated'   },
        { num: '—',   unit: 'open air' }
      ],
      use:    'Arrival Drinks · Cocktail Receptions · Street-Front Activations',
      plan:   ''
    }
  };

  var DEFAULT_FLOOR = 'level1';

  function init() {
    var section    = document.querySelector('.venue-floorplan');
    if (!section) return;

    var svgZones   = Array.from(section.querySelectorAll('.fp-floor'));
    var popup      = section.querySelector('.venue-floorplan__popup');
    var tabs       = Array.from(section.querySelectorAll('.venue-floorplan__tab'));

    var detailLabel = section.querySelector('.venue-floorplan__detail-label');
    var detailTitle = section.querySelector('.venue-floorplan__detail-title');
    var detailDesc  = section.querySelector('.venue-floorplan__detail-desc');
    var planEl      = section.querySelector('.venue-floorplan__plan');
    var planImg     = planEl && planEl.querySelector('.tempo-frame__img');
    var planLabel   = planEl && planEl.querySelector('.tempo-frame__label');
    var specsEl     = section.querySelector('.venue-floorplan__specs');
    var detailHeader = section.querySelector('.venue-floorplan__detail-header');

    // Mobile mini-detail
    var miniDetail = section.querySelector('.venue-floorplan__mini-detail');
    var miniLabel  = miniDetail && miniDetail.querySelector('.venue-floorplan__mini-label');
    var miniStats  = miniDetail && miniDetail.querySelector('.venue-floorplan__mini-stats');
    var miniLink   = miniDetail && miniDetail.querySelector('.venue-floorplan__mini-link');

    var activeFloor = DEFAULT_FLOOR;

    // ── Popup helpers ──────────────────────────────

    // SVG Y-coordinate centres for each floor zone (viewBox 0 0 590 465)
    var ZONE_Y_SVG = { level2: 86, level1: 201, ground: 348, terrace: 355 };

    function showPopup(floor, zoneEl) {
      if (!popup || !floor) return;
      var data = FLOOR_DATA[floor];
      if (!data) return;

      var label = popup.querySelector('.venue-floorplan__popup-label');
      var stats = popup.querySelector('.venue-floorplan__popup-stats');
      var use   = popup.querySelector('.venue-floorplan__popup-use');

      if (label) label.textContent = data.label;
      if (use)   use.textContent   = data.use;

      if (stats) {
        stats.innerHTML = data.stats.map(function (s) {
          return '<span class="venue-floorplan__popup-stat">'
            + '<strong>' + s.num + '</strong>'
            + '<span>' + s.unit + '</span>'
            + '</span>';
        }).join('');
      }

      // Position popup at the vertical centre of the hovered floor zone.
      // Popup is inside .venue-floorplan__svg-wrap, so top is relative to that.
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
      if (miniLink) {
        miniLink.href = url || '#';
      }
      miniDetail.classList.add('is-visible');
    }

    // ── Detail panel update ────────────────────────

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

      var transEls = [detailHeader, planEl, specsEl].filter(Boolean);

      transitionOut(transEls, function () {
        if (detailLabel) detailLabel.textContent = data.label;
        if (detailTitle) detailTitle.textContent = data.title;
        if (detailDesc)  detailDesc.textContent  = data.desc;

        // Floor plan image
        if (data.plan) {
          if (planImg) {
            planImg.src = data.plan;
            planImg.alt = data.label;
            planImg.style.display = '';
          }
          if (planLabel) planLabel.style.display = 'none';
          if (planEl) {
            planEl.classList.remove('tempo-frame--placeholder');
          }
        } else {
          if (planImg) planImg.style.display = 'none';
          if (planLabel) {
            planLabel.textContent = data.label + ' — Floor Plan Coming Soon';
            planLabel.style.display = '';
          }
          if (planEl) planEl.classList.add('tempo-frame--placeholder');
        }

        // Stats
        if (specsEl) {
          specsEl.innerHTML = data.stats.map(function (s) {
            return '<div>'
              + '<p class="venue-floorplan__spec-num">' + s.num + '</p>'
              + '<p class="venue-floorplan__spec-unit">' + s.unit + '</p>'
              + '</div>';
          }).join('');
        }

        requestAnimationFrame(function () {
          requestAnimationFrame(function () { transitionIn(transEls); });
        });
      });
    }

    // ── Activate floor (zone + tabs) ───────────────

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
          svgZones.forEach(function (z) {
            z.classList.toggle('is-active', z.dataset.floor === floor);
          });
          return;
        }
        showPopup(floor, zone);
      });

      zone.addEventListener('mouseleave', function () {
        hidePopup();
      });

      zone.addEventListener('click', function () {
        // Mobile: show mini detail on first tap; link in mini-detail navigates
        if (isMobile.matches) {
          showMiniDetail(floor, url);
          svgZones.forEach(function (z) {
            z.classList.toggle('is-active', z.dataset.floor === floor);
          });
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
            svgZones.forEach(function (z) {
              z.classList.toggle('is-active', z.dataset.floor === floor);
            });
            return;
          }
          setFloor(floor);
        }
      });
    });

    // ── Mobile tab events ───────────────────────────

    tabs.forEach(function (tab) {
      tab.addEventListener('click', function () {
        setFloor(tab.dataset.floor);
      });
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

    // Init mini-detail with default floor
    var defaultZone = svgZones.filter(function (z) { return z.dataset.floor === DEFAULT_FLOOR; })[0];
    showMiniDetail(DEFAULT_FLOOR, defaultZone ? defaultZone.dataset.url : '');

    // ── Mobile floor carousel dots ─────────────────
    initFloorCarousel(section);
  }

  function initFloorCarousel(section) {
    var grid     = section ? document.querySelector('.page-venue__floors-grid') : null;
    var dotsWrap = section ? document.querySelector('.page-venue__floors-dots') : null;
    if (!grid || !dotsWrap) return;

    var floors = Array.from(grid.querySelectorAll('.page-venue__floor'));
    if (!floors.length) return;

    var activeIdx   = 0;
    var autoTimer   = null;
    var resumeTimer = null;
    var INTERVAL    = 3800;
    var isMobileCarousel = window.matchMedia('(max-width: 1000px)');

    // Build dot buttons
    floors.forEach(function (floor, i) {
      var dot = document.createElement('button');
      dot.className = 'page-venue__floors-dot' + (i === 0 ? ' is-active' : '');
      dot.setAttribute('aria-label', 'Go to slide ' + (i + 1));
      dot.setAttribute('type', 'button');
      dot.addEventListener('click', function () {
        stopAuto();
        goTo(i);
        scheduleResume();
      });
      dotsWrap.appendChild(dot);
    });

    var dots = Array.from(dotsWrap.querySelectorAll('.page-venue__floors-dot'));

    function getSlideWidth() {
      return floors[0] ? floors[0].offsetWidth : grid.offsetWidth;
    }

    function goTo(idx, instant) {
      idx = Math.max(0, Math.min(idx, floors.length - 1));
      activeIdx = idx;
      grid.scrollTo({ left: floors[idx].offsetLeft, behavior: instant ? 'instant' : 'smooth' });
      dots.forEach(function (d, i) { d.classList.toggle('is-active', i === idx); });
    }

    function advance() {
      var next = activeIdx + 1;
      if (next >= floors.length) {
        // Snap back to start without animation to avoid reverse scroll
        goTo(0, true);
      } else {
        goTo(next);
      }
    }

    function startAuto() {
      clearInterval(autoTimer);
      if (isMobileCarousel.matches) {
        autoTimer = setInterval(advance, INTERVAL);
      }
    }

    function stopAuto() {
      clearInterval(autoTimer);
      autoTimer = null;
    }

    function scheduleResume() {
      clearTimeout(resumeTimer);
      resumeTimer = setTimeout(startAuto, 5000);
    }

    // Update active dot on manual scroll
    var ticking = false;
    grid.addEventListener('scroll', function () {
      if (ticking) return;
      ticking = true;
      requestAnimationFrame(function () {
        var scrollLeft = grid.scrollLeft;
        var slideWidth = getSlideWidth();
        var idx = Math.round(scrollLeft / slideWidth);
        idx = Math.max(0, Math.min(idx, dots.length - 1));
        activeIdx = idx;
        dots.forEach(function (d, i) { d.classList.toggle('is-active', i === idx); });
        ticking = false;
      });
    }, { passive: true });

    // Pause auto-loop on user touch; resume after idle
    grid.addEventListener('touchstart', function () {
      stopAuto();
      clearTimeout(resumeTimer);
    }, { passive: true });

    grid.addEventListener('touchend', scheduleResume, { passive: true });

    // Start / stop based on viewport
    isMobileCarousel.addEventListener('change', function (e) {
      if (e.matches) { startAuto(); } else { stopAuto(); }
    });

    startAuto();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
