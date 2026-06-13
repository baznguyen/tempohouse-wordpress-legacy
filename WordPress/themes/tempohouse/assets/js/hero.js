document.addEventListener('DOMContentLoaded', function () {

  // ── Time-of-day hero theming ──────────────────────
  var hero = document.querySelector('.hero');
  if (hero) {
    var hour = new Date().getHours();
    var act = (hour >= 5 && hour < 12) ? 'morning'
            : (hour >= 12 && hour < 18) ? 'afternoon'
            : 'evening';
    hero.setAttribute('data-tempo-act', act);

    // Scroll parallax
    var rafId = 0;
    window.addEventListener('scroll', function () {
      if (rafId) return;
      rafId = requestAnimationFrame(function () {
        hero.style.setProperty('--scroll-y', window.pageYOffset + 'px');
        rafId = 0;
      });
    }, { passive: true });
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
