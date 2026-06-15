document.addEventListener('DOMContentLoaded', function () {

  // ── Time-of-day hero theming ──────────────────────
  var hero = document.querySelector('.hero');
  if (hero) {
    var hour = new Date().getHours();
    var act = (hour >= 5 && hour < 12) ? 'morning'
            : (hour >= 12 && hour < 18) ? 'afternoon'
            : 'evening';
    hero.setAttribute('data-tempo-act', act);

    // ── Per-letter scroll parallax ────────────────────
    // Letters: T  E  M  P  O  H  O  U  S  E
    var letterSpeeds = [0.55, 0.22, 0.72, 0.32, 0.48, 0.60, 0.38, 0.75, 0.28, 0.50];
    var bleedChars   = document.querySelectorAll('.hero__bleed-char');
    var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (!reducedMotion && bleedChars.length) {
      var rafId      = 0;
      var heroHeight = hero.offsetHeight || window.innerHeight;

      function maxOffset() {
        var w = window.innerWidth;
        return w >= 1024 ? 130 : w >= 768 ? 80 : 48;
      }

      function updateLetters() {
        var progress = Math.min(1, window.pageYOffset / heroHeight);
        var max      = maxOffset();
        bleedChars.forEach(function (el, i) {
          var y = -(progress * (letterSpeeds[i] || 0.4) * max);
          el.style.transform = 'translateY(' + y.toFixed(2) + 'px)';
        });
        rafId = 0;
      }

      window.addEventListener('scroll', function () {
        if (rafId) return;
        rafId = requestAnimationFrame(updateLetters);
      }, { passive: true });

      window.addEventListener('resize', function () {
        heroHeight = hero.offsetHeight || window.innerHeight;
        if (!rafId) rafId = requestAnimationFrame(updateLetters);
      });
    }
  }

  // ── Drawer nav ──────────────────────────────────
  var trigger  = document.getElementById('site-nav-trigger');
  var closeBtn = document.getElementById('site-nav-close');
  var drawer   = document.getElementById('site-drawer');
  var overlay  = document.getElementById('site-nav-overlay');

  function openDrawer() {
    drawer.classList.add('is-open');
    overlay.classList.add('is-open');
    trigger.setAttribute('aria-expanded', 'true');
    drawer.setAttribute('aria-hidden', 'false');
    overlay.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
  }

  function closeDrawer() {
    drawer.classList.remove('is-open');
    overlay.classList.remove('is-open');
    trigger.setAttribute('aria-expanded', 'false');
    drawer.setAttribute('aria-hidden', 'true');
    overlay.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }

  if (trigger) trigger.addEventListener('click', openDrawer);
  if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
  if (overlay)  overlay.addEventListener('click', closeDrawer);

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && drawer && drawer.classList.contains('is-open')) {
      closeDrawer();
    }
  });

});
