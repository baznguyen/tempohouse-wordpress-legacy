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

  document.addEventListener('DOMContentLoaded', function () {
    attach('.moods__frames-wrap');
    attach('.events__viewport');
  });
})();
