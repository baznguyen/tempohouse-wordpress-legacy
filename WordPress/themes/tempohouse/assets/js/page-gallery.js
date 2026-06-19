/**
 * Gallery page — walk section.
 * Desktop (>1000px): all frames visible as a static wall (CSS only, no JS needed).
 * Mobile (<1000px):  viewport scroll-snap carousel, mirrors events.js pattern.
 * Wall section uses .moods class — moods.js handles parallax + mobile carousel.
 */
(function () {
  'use strict';

  // ── Gallery Walk — mobile scroll-snap carousel ──────

  function initWalkCarousel(viewport) {
    var track = viewport.querySelector('.page-gallery__walk-track');
    if (!track) return;

    // If loaded at desktop width, wait for the mobile breakpoint before init.
    // This ensures click handlers are always in place when the nav becomes visible.
    var mq = window.matchMedia('(max-width: 1000px)');
    if (!mq.matches) {
      var onMobile = function (e) {
        if (e.matches) { mq.removeEventListener('change', onMobile); doInit(); }
      };
      mq.addEventListener('change', onMobile);
      return;
    }
    doInit();

    function doInit() {
    if (typeof window.tempoDragScroll === 'function') {
      window.tempoDragScroll(viewport);
    }

    var realItems = Array.from(track.children);
    var REAL = realItems.length;

    // Prepend in reverse order so the last item's clone sits immediately to
    // the left of the first real item — seamless backward infinite wrap.
    realItems.slice().reverse().forEach(function (el) {
      var c = el.cloneNode(true);
      c.setAttribute('aria-hidden', 'true');
      c.classList.add('is-clone');
      track.insertBefore(c, track.firstChild);
    });
    realItems.forEach(function (el) {
      var c = el.cloneNode(true);
      c.setAttribute('aria-hidden', 'true');
      c.classList.add('is-clone');
      track.appendChild(c);
    });

    var scrollPad   = parseFloat(getComputedStyle(viewport).scrollPaddingInlineStart) || 48;
    var section     = viewport.closest('.page-gallery__walk');
    var prevBtn     = section && section.querySelector('.page-gallery__walk-nav-prev');
    var nextBtn     = section && section.querySelector('.page-gallery__walk-nav-next');
    var dots        = section ? Array.from(section.querySelectorAll('.page-gallery__walk-dot')) : [];
    var settleTimer = null;
    var dotsRafId   = 0;
    var autoTimer   = null;

    function startAuto() {
      clearInterval(autoTimer);
      autoTimer = setInterval(function () {
        scrollToIdx(getCurrentIndex() + 1, true);
      }, 4000);
    }

    function stopAuto() {
      clearInterval(autoTimer);
      autoTimer = null;
    }

    function getCurrentIndex() {
      var snapLine = viewport.getBoundingClientRect().left + scrollPad;
      var children = track.children;
      var best = REAL, bestDist = Infinity;
      for (var i = 0; i < children.length; i++) {
        var d = Math.abs(children[i].getBoundingClientRect().left - snapLine);
        if (d < bestDist) { bestDist = d; best = i; }
      }
      return best;
    }

    function scrollToIdx(idx, smooth) {
      var card = track.children[idx];
      if (!card) return;
      var target = Math.round(
        viewport.scrollLeft + card.getBoundingClientRect().left
        - viewport.getBoundingClientRect().left - scrollPad
      );
      if (smooth) {
        // scrollTo with behavior:'smooth' works reliably when no CSS scroll-snap fights it.
        viewport.scrollTo({ left: target, behavior: 'smooth' });
      } else {
        // Instant jump — used for normalize teleports and init positioning.
        viewport.scrollLeft = target;
      }
    }

    function normalize() {
      var idx = getCurrentIndex();
      if (idx < REAL) { idx = idx + REAL; }
      else if (idx >= REAL * 2) { idx = idx - REAL; }
      // Always snap to exact frame position — handles both clone-boundary teleports and
      // swipe-stop offsets (no CSS scroll-snap means we must align precisely in JS).
      var card = track.children[idx];
      var target = Math.round(
        viewport.scrollLeft + card.getBoundingClientRect().left
        - viewport.getBoundingClientRect().left - scrollPad
      );
      if (Math.abs(viewport.scrollLeft - target) > 1) {
        viewport.scrollLeft = target;
      }
      updateDots();
    }

    function updateDots() {
      var idx    = getCurrentIndex();
      var dotIdx = ((idx - REAL) % REAL + REAL) % REAL;
      dots.forEach(function (d, i) {
        d.classList.toggle('page-gallery__walk-dot--active', i === dotIdx);
      });
      if (prevBtn) prevBtn.disabled = false;
      if (nextBtn) nextBtn.disabled = false;
    }

    viewport.addEventListener('scroll', function () {
      if (!dotsRafId) dotsRafId = requestAnimationFrame(function () {
        updateDots();
        dotsRafId = 0;
      });
      stopAuto();
      clearTimeout(settleTimer);
      settleTimer = setTimeout(function () {
        normalize();
        startAuto();
      }, 180);
    }, { passive: true });

    viewport.addEventListener('scrollend', function () {
      clearTimeout(settleTimer);
      normalize();
      startAuto();
    }, { passive: true });

    var resizeTimer = null;
    window.addEventListener('resize', function () {
      scrollPad = parseFloat(getComputedStyle(viewport).scrollPaddingInlineStart) || 48;
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(function () { scrollToIdx(getCurrentIndex(), false); }, 150);
    }, { passive: true });

    // Pause auto-advance while user is dragging/touching
    viewport.addEventListener('touchstart', stopAuto, { passive: true });

    scrollToIdx(REAL, false);
    updateDots();

    if (prevBtn) {
      prevBtn.disabled = false;
      prevBtn.addEventListener('click', function () {
        stopAuto();
        scrollToIdx(getCurrentIndex() - 1, true);
      });
    }
    if (nextBtn) {
      nextBtn.disabled = false;
      nextBtn.addEventListener('click', function () {
        stopAuto();
        scrollToIdx(getCurrentIndex() + 1, true);
      });
    }
    dots.forEach(function (d, i) {
      d.addEventListener('click', function () {
        stopAuto();
        scrollToIdx(REAL + i, true);
      });
    });

    startAuto();
    } // end doInit
  }

  // ── Init ─────────────────────────────────────────────

  function init() {
    document.querySelectorAll('[data-gallery-walk]').forEach(initWalkCarousel);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();
