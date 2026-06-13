document.addEventListener('DOMContentLoaded', function () {
  var section = document.querySelector('.moods');
  if (!section) return;

  var wrap = section.querySelector('.moods__frames-wrap');

  // ── Desktop parallax ─────────────────────────────
  var rafId   = 0;
  var windowH = window.innerHeight;

  window.addEventListener('resize', function () { windowH = window.innerHeight; });

  window.addEventListener('scroll', function () {
    if (rafId) return;
    rafId = requestAnimationFrame(function () {
      var rect   = section.getBoundingClientRect();
      var offset = windowH / 2 - (rect.top + rect.height / 2);
      section.style.setProperty('--parallax', offset + 'px');
      rafId = 0;
    });
  }, { passive: true });

  // ── Mobile infinite carousel ──────────────────────
  if (!wrap) return;

  // Only run on mobile (carousel mode)
  if (!window.matchMedia('(max-width: 1100px)').matches) return;

  if (typeof window.tempoDragScroll === 'function') {
    window.tempoDragScroll(wrap);
  }

  var realItems = Array.from(wrap.children);
  var REAL = realItems.length; // 3

  // Prepend a full clone set (so scrolling left wraps to the end)
  realItems.forEach(function (el) {
    var c = el.cloneNode(true);
    c.setAttribute('aria-hidden', 'true');
    c.classList.add('is-clone');
    wrap.insertBefore(c, wrap.firstChild);
  });

  // Append a full clone set (so scrolling right wraps to the start)
  realItems.forEach(function (el) {
    var c = el.cloneNode(true);
    c.setAttribute('aria-hidden', 'true');
    c.classList.add('is-clone');
    wrap.appendChild(c);
  });

  // DOM is now: [headClones(0…REAL-1) | realItems(REAL…2*REAL-1) | tailClones(2*REAL…3*REAL-1)]

  var prevBtn = section.querySelector('.moods__nav-prev');
  var nextBtn = section.querySelector('.moods__nav-next');
  var dots    = Array.from(section.querySelectorAll('.moods__dot'));
  var settleTimer = null;

  // ── Helpers ───────────────────────────────────────

  function getChildren() { return Array.from(wrap.children); }

  // Find the child whose left edge is closest to the container's left edge
  // (matches scroll-snap behaviour: snapped card sits at container left + scroll-padding)
  function getCurrentIndex() {
    var wrapLeft = wrap.getBoundingClientRect().left;
    var scrollPad = parseFloat(getComputedStyle(wrap).scrollPaddingInlineStart) || 0;
    var snapLine = wrapLeft + scrollPad;
    var children = getChildren();
    var best = REAL, bestDist = Infinity;
    children.forEach(function (el, i) {
      var d = Math.abs(el.getBoundingClientRect().left - snapLine);
      if (d < bestDist) { bestDist = d; best = i; }
    });
    return best;
  }

  function scrollToIdx(idx, smooth) {
    var card = getChildren()[idx];
    if (!card) return;
    var scrollPad = parseFloat(getComputedStyle(wrap).scrollPaddingInlineStart) || 0;
    var wrapRect  = wrap.getBoundingClientRect();
    var cardRect  = card.getBoundingClientRect();
    var target    = wrap.scrollLeft + cardRect.left - wrapRect.left - scrollPad;
    if (smooth) {
      wrap.scrollTo({ left: target, behavior: 'smooth' });
    } else {
      wrap.scrollLeft = target;
    }
  }

  function normalize() {
    var idx = getCurrentIndex();
    // Jumped into head clones → reset to equivalent real item
    if (idx < REAL) {
      scrollToIdx(idx + REAL, false);
    // Jumped into tail clones → reset to equivalent real item
    } else if (idx >= REAL * 2) {
      scrollToIdx(idx - REAL, false);
    }
    updateDots();
  }

  function updateDots() {
    var idx    = getCurrentIndex();
    var dotIdx = ((idx - REAL) % REAL + REAL) % REAL;
    dots.forEach(function (d, i) {
      d.classList.toggle('moods__dot--active', i === dotIdx);
    });
    // Never disable — infinite loop has no ends
    if (prevBtn) prevBtn.disabled = false;
    if (nextBtn) nextBtn.disabled = false;
  }

  // ── Scroll listener ───────────────────────────────

  wrap.addEventListener('scroll', function () {
    updateDots();
    clearTimeout(settleTimer);
    settleTimer = setTimeout(normalize, 180);
  }, { passive: true });

  // scrollend fires when scroll fully settles (Chrome 114+, FF 109+)
  wrap.addEventListener('scrollend', function () {
    clearTimeout(settleTimer);
    normalize();
  }, { passive: true });

  // ── Resize: re-snap when vw changes (frame widths are calc(100vw - X)) ──

  var resizeTimer = null;
  window.addEventListener('resize', function () {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function () {
      scrollToIdx(getCurrentIndex(), false);
    }, 150);
  }, { passive: true });

  // ── Init ──────────────────────────────────────────

  scrollToIdx(REAL, false);
  updateDots();

  // ── Buttons ───────────────────────────────────────

  if (prevBtn) {
    prevBtn.disabled = false;
    prevBtn.addEventListener('click', function () {
      scrollToIdx(getCurrentIndex() - 1, true);
    });
  }

  if (nextBtn) {
    nextBtn.disabled = false;
    nextBtn.addEventListener('click', function () {
      scrollToIdx(getCurrentIndex() + 1, true);
    });
  }

  dots.forEach(function (d, i) {
    d.addEventListener('click', function () {
      scrollToIdx(REAL + i, true);
    });
  });
});
