(function () {
  window.tempoDragScroll = function (el) {
    if (!el) return;

    var isDown = false;
    var startX = 0;
    var startScrollLeft = 0;
    var moved = 0;

    function onMouseDown(e) {
      isDown = true;
      moved = 0;
      startX = e.pageX;
      startScrollLeft = el.scrollLeft;
      el.style.cursor = 'grabbing';
      el.style.userSelect = 'none';
    }

    function onMouseMove(e) {
      if (!isDown) return;
      var dx = e.pageX - startX;
      moved = Math.abs(dx);
      el.scrollLeft = startScrollLeft - dx;
    }

    function stop() {
      if (!isDown) return;
      isDown = false;
      el.style.cursor = '';
      el.style.userSelect = '';
    }

    // Capture phase — fires before Link click handlers; blocks navigation after drag
    function onClickCapture(e) {
      if (moved > 5) {
        e.preventDefault();
        e.stopPropagation();
        moved = 0;
      }
    }

    el.addEventListener('mousedown', onMouseDown);
    el.addEventListener('mousemove', onMouseMove);
    el.addEventListener('mouseleave', stop);
    window.addEventListener('mouseup', stop);
    el.addEventListener('click', onClickCapture, true);
  };
})();
