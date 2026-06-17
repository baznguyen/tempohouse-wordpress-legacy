/* ═══════════════════════════════════════════════════════════════
   Spotlight Scroll Lock
   Locks window scroll while a spotlight dark-room overlay is open
   (moods frames + events cards). Mouse-only — touch unaffected.

   Uses overflow:hidden on <html> rather than position:fixed so that
   scroll position is never touched — avoids the smooth-scroll jump
   that position:fixed + scrollTo() causes on sites with scroll-behavior:smooth.
   ═══════════════════════════════════════════════════════════════ */
(function () {
  'use strict';

  var locked = false;

  function lockScroll() {
    if (locked) return;
    locked = true;
    // Compensate for scrollbar disappearing to prevent layout shift
    var sbw = window.innerWidth - document.documentElement.clientWidth;
    if (sbw > 0) document.documentElement.style.paddingRight = sbw + 'px';
    document.documentElement.style.overflow = 'hidden';
  }

  function unlockScroll() {
    if (!locked) return;
    locked = false;
    document.documentElement.style.overflow = '';
    document.documentElement.style.paddingRight = '';
  }

  function attach(selector) {
    var el = document.querySelector(selector);
    if (!el) return;
    el.addEventListener('mouseenter', lockScroll);
    el.addEventListener('mouseleave', unlockScroll);
  }

  // Card-aware delegate: lock only while the pointer is over an actual card,
  // not the surrounding viewport padding (120px top / 72px bottom).
  function attachCards(viewportSelector) {
    var vp = document.querySelector(viewportSelector);
    if (!vp) return;
    vp.addEventListener('mouseover', function (e) {
      if (e.target.closest('.event-card')) lockScroll();
    });
    vp.addEventListener('mouseout', function (e) {
      var to = e.relatedTarget;
      if (!to || !to.closest('.event-card')) unlockScroll();
    });
  }

  // Delegate: lock scroll while pointer is inside any data-interactive tempo-frame.
  // Moving between two frames stays locked — only unlocks when leaving to a non-frame target.
  function attachInteractiveFrames() {
    document.addEventListener('mouseover', function (e) {
      if (e.target.closest('.tempo-frame[data-interactive]')) lockScroll();
    });
    document.addEventListener('mouseout', function (e) {
      var to = e.relatedTarget;
      if (to && to.closest('.tempo-frame[data-interactive]')) return;
      if (e.target.closest('.tempo-frame[data-interactive]')) unlockScroll();
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    attach('.moods__frames-wrap');
    attachCards('.events__viewport');
    attachInteractiveFrames();
  });
})();
