/**
 * Tempo Carousel — converts card grids to mobile carousels.
 *
 * Usage: wrap a .page-inner__card-grid in <div data-carousel>
 * and add a .tempo-carousel__nav with dots container + prev/next buttons.
 * JS only activates below 600px (mirrors the CSS breakpoint).
 *
 * Dots are injected automatically based on item count.
 */
(function () {
  'use strict';

  var BREAKPOINT = 600;

  function initCarousel(carousel) {
    var track = carousel.querySelector('.tempo-carousel__track');
    if (!track) return;

    var items = Array.from(track.children).filter(function (el) {
      return !el.classList.contains('is-clone');
    });
    if (items.length === 0) return;

    var dotContainer = carousel.querySelector('.tempo-carousel__dots');
    var prevBtn = carousel.querySelector('.tempo-carousel__prev');
    var nextBtn = carousel.querySelector('.tempo-carousel__next');
    var dots = [];
    var dotsRaf = 0;
    var settleTimer = null;

    // ── Build dots ──────────────────────────────────
    if (dotContainer) {
      dotContainer.innerHTML = '';
      items.forEach(function (_, i) {
        var dot = document.createElement('button');
        dot.className = 'tempo-carousel__dot';
        if (i === 0) dot.classList.add('tempo-carousel__dot--active');
        dot.setAttribute('aria-label', 'Go to item ' + (i + 1));
        dot.addEventListener('click', function () { scrollToItem(i, true); });
        dotContainer.appendChild(dot);
        dots.push(dot);
      });
    }

    // ── Helpers ──────────────────────────────────────

    function getScrollPad() {
      return parseFloat(getComputedStyle(track).scrollPaddingInlineStart) || 0;
    }

    function getCurrentIndex() {
      var snapLine = track.getBoundingClientRect().left + getScrollPad();
      var best = 0, bestDist = Infinity;
      items.forEach(function (item, i) {
        var d = Math.abs(item.getBoundingClientRect().left - snapLine);
        if (d < bestDist) { bestDist = d; best = i; }
      });
      return best;
    }

    function scrollToItem(i, smooth) {
      var item = items[Math.max(0, Math.min(i, items.length - 1))];
      if (!item) return;
      var target = track.scrollLeft
        + item.getBoundingClientRect().left
        - track.getBoundingClientRect().left
        - getScrollPad();
      if (smooth) {
        track.scrollTo({ left: target, behavior: 'smooth' });
      } else {
        track.scrollLeft = target;
      }
    }

    function updateDots() {
      var idx = getCurrentIndex();
      dots.forEach(function (d, i) {
        d.classList.toggle('tempo-carousel__dot--active', i === idx);
      });
      if (prevBtn) prevBtn.disabled = idx <= 0;
      if (nextBtn) nextBtn.disabled = idx >= items.length - 1;
    }

    // ── Events ───────────────────────────────────────

    track.addEventListener('scroll', function () {
      if (!dotsRaf) dotsRaf = requestAnimationFrame(function () {
        updateDots();
        dotsRaf = 0;
      });
      clearTimeout(settleTimer);
      settleTimer = setTimeout(updateDots, 160);
    }, { passive: true });

    track.addEventListener('scrollend', function () {
      clearTimeout(settleTimer);
      updateDots();
    }, { passive: true });

    window.addEventListener('resize', function () {
      clearTimeout(settleTimer);
      settleTimer = setTimeout(function () {
        scrollToItem(getCurrentIndex(), false);
      }, 120);
    }, { passive: true });

    if (prevBtn) {
      prevBtn.addEventListener('click', function () {
        scrollToItem(getCurrentIndex() - 1, true);
      });
    }
    if (nextBtn) {
      nextBtn.addEventListener('click', function () {
        scrollToItem(getCurrentIndex() + 1, true);
      });
    }

    updateDots();
  }

  // ── Init — only activate at mobile breakpoint ────

  function maybeInit() {
    if (window.innerWidth >= BREAKPOINT) return;
    document.querySelectorAll('[data-carousel]:not([data-carousel-init])').forEach(function (el) {
      el.setAttribute('data-carousel-init', 'true');
      initCarousel(el);
    });
  }

  // Re-init if viewport crosses the breakpoint (e.g. rotate from landscape to portrait)
  var wasNarrow = window.innerWidth < BREAKPOINT;
  window.addEventListener('resize', function () {
    var isNarrow = window.innerWidth < BREAKPOINT;
    if (!wasNarrow && isNarrow) {
      document.querySelectorAll('[data-carousel]').forEach(function (el) {
        el.removeAttribute('data-carousel-init');
      });
      maybeInit();
    }
    wasNarrow = isNarrow;
  }, { passive: true });

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', maybeInit);
  } else {
    maybeInit();
  }
})();
