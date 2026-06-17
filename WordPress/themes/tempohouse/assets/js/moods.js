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
  if (!window.matchMedia('(max-width: 1100px)').matches) return;

  if (typeof window.tempoDragScroll === 'function') {
    window.tempoDragScroll(wrap);
  }

  var realItems = Array.from(wrap.children);
  var REAL = realItems.length;

  // Prepend + append clone sets for infinite wrap
  realItems.forEach(function (el) {
    var c = el.cloneNode(true);
    c.setAttribute('aria-hidden', 'true');
    c.classList.add('is-clone');
    wrap.insertBefore(c, wrap.firstChild);
  });
  realItems.forEach(function (el) {
    var c = el.cloneNode(true);
    c.setAttribute('aria-hidden', 'true');
    c.classList.add('is-clone');
    wrap.appendChild(c);
  });

  // DOM: [headClones(0…REAL-1) | realItems(REAL…2*REAL-1) | tailClones(2*REAL…3*REAL-1)]

  // Cache scroll-pad — CSS value, only changes on resize
  var scrollPad = parseFloat(getComputedStyle(wrap).scrollPaddingInlineStart) || 0;

  var prevBtn    = section.querySelector('.moods__nav-prev');
  var nextBtn    = section.querySelector('.moods__nav-next');
  var dots       = Array.from(section.querySelectorAll('.moods__dot'));
  var settleTimer = null;
  var dotsRafId   = 0;

  // Use wrap.children (live collection) directly — no array allocation per call
  function getCurrentIndex() {
    var snapLine = wrap.getBoundingClientRect().left + scrollPad;
    var children = wrap.children;
    var best = REAL, bestDist = Infinity;
    for (var i = 0; i < children.length; i++) {
      var d = Math.abs(children[i].getBoundingClientRect().left - snapLine);
      if (d < bestDist) { bestDist = d; best = i; }
    }
    return best;
  }

  function scrollToIdx(idx, smooth) {
    var card = wrap.children[idx];
    if (!card) return;
    var target = wrap.scrollLeft + card.getBoundingClientRect().left
               - wrap.getBoundingClientRect().left - scrollPad;
    if (smooth) {
      wrap.scrollTo({ left: target, behavior: 'smooth' });
    } else {
      wrap.scrollLeft = target;
    }
  }

  function normalize() {
    var idx = getCurrentIndex();
    if (idx < REAL) {
      scrollToIdx(idx + REAL, false);
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
    if (prevBtn) prevBtn.disabled = false;
    if (nextBtn) nextBtn.disabled = false;
  }

  // ── Scroll listeners ──────────────────────────────

  wrap.addEventListener('scroll', function () {
    // Throttle dot updates to one rAF per frame
    if (!dotsRafId) dotsRafId = requestAnimationFrame(function () {
      updateDots();
      dotsRafId = 0;
    });
    clearTimeout(settleTimer);
    settleTimer = setTimeout(normalize, 180);
  }, { passive: true });

  wrap.addEventListener('scrollend', function () {
    clearTimeout(settleTimer);
    normalize();
  }, { passive: true });

  // ── Resize: re-snap + recache scroll-pad ─────────

  var resizeTimer = null;
  window.addEventListener('resize', function () {
    scrollPad = parseFloat(getComputedStyle(wrap).scrollPaddingInlineStart) || 0;
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
    prevBtn.addEventListener('click', function () { scrollToIdx(getCurrentIndex() - 1, true); });
  }
  if (nextBtn) {
    nextBtn.disabled = false;
    nextBtn.addEventListener('click', function () { scrollToIdx(getCurrentIndex() + 1, true); });
  }

  dots.forEach(function (d, i) {
    d.addEventListener('click', function () { scrollToIdx(REAL + i, true); });
  });
});
