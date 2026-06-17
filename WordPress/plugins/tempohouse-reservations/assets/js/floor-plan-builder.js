/* ══════════════════════════════════════════════════════════════════════
   TEMPO House — Floor Plan Editor v6.0
   Konva.js canvas + horizontal toolbar + floating properties card
   ══════════════════════════════════════════════════════════════════════ */
(function () {
  'use strict';

  const cfg   = window.thrFP || {};
  const API   = cfg.apiUrl  || '';
  const NONCE = cfg.nonce   || '';
  const TYPES = cfg.types   || {};
  const TODAY = cfg.today   || new Date().toISOString().slice(0, 10);

  /* ── Status palette ─────────────────────────────────────────────── */
  const STATUS = {
    free:     { fill: '#22C55E', stroke: '#16A34A', chair: '#86EFAC', text: '#FFFFFF', label: 'Free' },
    booked:   { fill: '#3B82F6', stroke: '#2563EB', chair: '#93C5FD', text: '#FFFFFF', label: 'Booked' },
    occupied: { fill: '#F97316', stroke: '#EA580C', chair: '#FED7AA', text: '#FFFFFF', label: 'Occupied' },
    blocked:  { fill: '#9CA3AF', stroke: '#6B7280', chair: '#D1D5DB', text: '#FFFFFF', label: 'Blocked' },
  };
  const TABLE_FILL   = '#7C8290';
  const TABLE_STROKE = '#5E636E';
  const CHAIR_FILL   = '#B5BAC4';

  /* ── Toolbar definition (replaces PALETTE) ──────────────────────── */
  const TOOLBAR = [
    { key: 'table-round',  lbl: 'Round',     w: 80,  h: 80,  cap: 4 },
    { key: 'table-square', lbl: 'Square',    w: 72,  h: 72,  cap: 4 },
    { key: 'table-rect',   lbl: 'Rect',      w: 110, h: 60,  cap: 6 },
    { key: 'lounge',       lbl: 'Lounge',    w: 130, h: 64,  cap: 4 },
    { key: 'bar-seat',     lbl: 'Bar Seat',  w: 36,  h: 36,  cap: 1 },
    { key: 'bar-table',    lbl: 'Bar Table', w: 52,  h: 90,  cap: 4 },
    null, // separator
    { key: 'text_label',   lbl: 'Label',     w: 80,  h: 30,  cap: 0 },
    { key: '__zone',       lbl: 'Zone',      w: 0,   h: 0,   cap: 0, action: 'zone' },
  ];

  /* ── App state ──────────────────────────────────────────────────── */
  const S = {
    mode:        'live',
    floors:      [],
    floorId:     null,
    tables:      {},      // id → item
    liveStatus:  {},      // id → 'free'|'booked'|'occupied'|'blocked'
    floorStats:  {},      // floorId → { tables, seats, booked }
    selected:    null,
    selectedIds: new Set(), // for multi-select
    pendingZoneId:      null, // zone currently being edited (shift-click membership)
    pendingZoneMembers: null, // Set<string> of tableIds (pending, not yet saved)
    rubber:      null,    // rubber band state
    dirty:       false,
    date:        TODAY,
    time:        '',
    placing:     null,
    liveTimer:   null,
    undoStack:   [],
    redoStack:   [],
    bgConfig:    { url: null, scale: 1, scaleY: 0, opacity: 0.5, offsetX: 0, offsetY: 0, crop: null },
    bgEditMode:   false,
    bgKeepRatio:  true,
    bgTransformer: null,
    bgCropMode:   false,
    bgCropRect:   null,
    bgCropTr:     null,
    bgCropOverlays: null,
    bgCropBorder: null,
    bgCropOrigX:  0,
    bgCropOrigY:  0,
    snapEnabled: true,
    spaceDown:    false,
    panning:      null,    // { active, startX, startY, stageX, stageY, moved }
    activeLayout: null,    // { id, name, is_default } or null = base
    layouts:      [],      // all layouts for current floor plan
    layoutPanel:  false,
  };

  /* ── Konva refs ─────────────────────────────────────────────────── */
  let stage, bgLayer, zoneLayer, tableLayer, transformer;

  /* ══════════════════════════════════════════════════════════════════
     BOOT
  ══════════════════════════════════════════════════════════════════ */
  document.addEventListener('DOMContentLoaded', function () {
    if (!document.getElementById('fp-app')) return;
    initKonva();
    bindHeader();
    bindFloorNav();
    bindSubbar();
    buildToolbar();
    bindCanvasTools();
    bindFloatPanel();
    bindToolbar();
    bindKeyboard();
    bindBgControls();
    setDateLabel(S.date);
    setTimeLabel(S.time);
    loadFloors();
  });

  /* ══════════════════════════════════════════════════════════════════
     KONVA INIT
  ══════════════════════════════════════════════════════════════════ */
  function initKonva() {
    var wrap = document.getElementById('fp-canvas-wrap');
    var W = wrap ? wrap.clientWidth  : 800;
    var H = wrap ? wrap.clientHeight : 500;

    stage = new Konva.Stage({ container: 'fp-konva', width: W, height: H });

    // Background layer (non-interactive)
    bgLayer = new Konva.Layer({ listening: false });
    stage.add(bgLayer);

    // Zone layer (between bg and tables so zones render behind tables)
    zoneLayer = new Konva.Layer();
    stage.add(zoneLayer);

    tableLayer = new Konva.Layer();

    transformer = new Konva.Transformer({
      rotateEnabled: true,
      resizeEnabled: false,
      borderStroke: '#3B82F6',
      borderStrokeWidth: 1.5,
      anchorSize: 7,
      anchorCornerRadius: 3,
      anchorFill: '#FFFFFF',
      anchorStroke: '#3B82F6',
      padding: 4,
    });
    tableLayer.add(transformer);
    stage.add(tableLayer);

    var ro = new ResizeObserver(function () {
      if (!wrap) return;
      stage.width(wrap.clientWidth);
      stage.height(wrap.clientHeight);
      tableLayer.batchDraw();
    });
    ro.observe(wrap);

    // Mouse events for rubber band + placing + pan
    stage.on('mousedown touchstart', function (e) {
      if (S.placing) return;
      var pos = stage.getPointerPosition();
      if (!pos) return;

      // Space+drag: pan regardless of target
      if (S.spaceDown && !e.evt.shiftKey) {
        e.evt.preventDefault();
        S.panning = { active: true, startX: pos.x, startY: pos.y, stageX: stage.x(), stageY: stage.y(), moved: false };
        stage.container().style.cursor = 'grabbing';
        return;
      }

      if (e.target !== stage) return;

      if (S.mode === 'builder' && e.evt.shiftKey) {
        // Shift+drag on background = rubber band select
        S.rubber = { x: pos.x, y: pos.y, active: false };
      } else {
        // Any other click-drag on background = pan
        S.panning = { active: true, startX: pos.x, startY: pos.y, stageX: stage.x(), stageY: stage.y(), moved: false };
        stage.container().style.cursor = 'grabbing';
      }
    });

    stage.on('mousemove touchmove', function () {
      var pos = stage.getPointerPosition();
      if (!pos) return;

      if (S.panning && S.panning.active) {
        var dx = pos.x - S.panning.startX;
        var dy = pos.y - S.panning.startY;
        if (Math.abs(dx) > 2 || Math.abs(dy) > 2) S.panning.moved = true;
        stage.x(S.panning.stageX + dx);
        stage.y(S.panning.stageY + dy);
        stage.batchDraw();
        return;
      }

      if (!S.rubber) return;
      var x = Math.min(S.rubber.x, pos.x);
      var y = Math.min(S.rubber.y, pos.y);
      var w = Math.abs(pos.x - S.rubber.x);
      var h = Math.abs(pos.y - S.rubber.y);
      if (w > 4 || h > 4) {
        S.rubber.active = true;
        showRubberBand(x, y, w, h);
      }
    });

    stage.on('mouseup touchend', function (e) {
      if (S.panning) {
        S.panning.active = false;
        stage.container().style.cursor = S.spaceDown ? 'grab' : '';
      }
      if (S.rubber && S.rubber.active) {
        selectItemsInRect();
        hideRubberBand();
      }
      S.rubber = null;
    });

    stage.on('click tap', function (e) {
      if (e.target !== stage) return;
      if (S.placing) { placeTable(stage.getPointerPosition()); return; }
      if (S.panning && S.panning.moved) { S.panning.moved = false; return; }
      if (!S.rubber || !S.rubber.active) deselect();
    });

    stage.on('wheel', function (e) {
      e.evt.preventDefault();
      zoomBy(e.evt.deltaY < 0 ? 1.1 : 1 / 1.1);
    });

    // Space+drag to pan
    document.addEventListener('keydown', function (e) {
      var tag = document.activeElement ? document.activeElement.tagName : '';
      if (e.code === 'Space' && !S.spaceDown && tag !== 'INPUT' && tag !== 'TEXTAREA' && tag !== 'SELECT') {
        e.preventDefault();
        S.spaceDown = true;
        stage.container().style.cursor = 'grab';
      }
    });
    document.addEventListener('keyup', function (e) {
      if (e.code === 'Space') {
        S.spaceDown = false;
        if (S.panning && !S.panning.moved) S.panning = null;
        stage.container().style.cursor = '';
      }
    });

    // Middle-mouse-button drag to pan
    stage.container().addEventListener('mousedown', function (e) {
      if (e.button !== 1) return;
      e.preventDefault();
      S.panning = { active: true, startX: e.clientX, startY: e.clientY, stageX: stage.x(), stageY: stage.y(), moved: false, clientCoords: true };
      stage.container().style.cursor = 'grabbing';
    });
    document.addEventListener('mousemove', function (e) {
      if (!S.panning || !S.panning.active || !S.panning.clientCoords) return;
      var dx = e.clientX - S.panning.startX;
      var dy = e.clientY - S.panning.startY;
      if (Math.abs(dx) > 2 || Math.abs(dy) > 2) S.panning.moved = true;
      stage.x(S.panning.stageX + dx);
      stage.y(S.panning.stageY + dy);
      stage.batchDraw();
    });
    document.addEventListener('mouseup', function (e) {
      if (e.button !== 1) return;
      if (S.panning && S.panning.clientCoords) {
        S.panning = null;
        stage.container().style.cursor = S.spaceDown ? 'grab' : '';
      }
    });
  }

  /* ══════════════════════════════════════════════════════════════════
     RUBBER BAND SELECTION
  ══════════════════════════════════════════════════════════════════ */
  function showRubberBand(x, y, w, h) {
    var wrap = document.getElementById('fp-canvas-wrap');
    if (!wrap) return;
    var rb = document.getElementById('fp-rubber-band');
    if (!rb) {
      rb = document.createElement('div');
      rb.id = 'fp-rubber-band';
      rb.style.cssText = 'position:absolute;pointer-events:none;border:1.5px dashed #3B82F6;background:rgba(59,130,246,0.08);border-radius:2px;z-index:10;';
      wrap.appendChild(rb);
    }
    rb.hidden = false;
    rb.style.left   = x + 'px';
    rb.style.top    = y + 'px';
    rb.style.width  = w + 'px';
    rb.style.height = h + 'px';
  }

  function updateRubberBand(x, y, w, h) {
    var rb = document.getElementById('fp-rubber-band');
    if (!rb) return;
    rb.style.left   = x + 'px';
    rb.style.top    = y + 'px';
    rb.style.width  = w + 'px';
    rb.style.height = h + 'px';
  }

  function hideRubberBand() {
    var rb = document.getElementById('fp-rubber-band');
    if (rb) rb.hidden = true;
  }

  function selectItemsInRect() {
    if (!S.rubber) return;
    var pos = stage.getPointerPosition();
    var rx1 = Math.min(S.rubber.x, pos.x);
    var ry1 = Math.min(S.rubber.y, pos.y);
    var rx2 = Math.max(S.rubber.x, pos.x);
    var ry2 = Math.max(S.rubber.y, pos.y);

    // Convert canvas px to stage coords
    var scale = stage.scaleX();
    var stPos = stage.position();
    var sx1 = (rx1 - stPos.x) / scale;
    var sy1 = (ry1 - stPos.y) / scale;
    var sx2 = (rx2 - stPos.x) / scale;
    var sy2 = (ry2 - stPos.y) / scale;

    var found = [];
    Object.values(S.tables).forEach(function (item) {
      if (item.type === 'zone') return;
      var cx = toNum(item.pos_x, 0);
      var cy = toNum(item.pos_y, 0);
      if (cx >= sx1 && cx <= sx2 && cy >= sy1 && cy <= sy2) {
        found.push(String(item.id));
      }
    });

    if (found.length === 0) {
      deselect();
    } else if (found.length === 1) {
      selectTable(found[0]);
    } else {
      S.selected = null;
      S.selectedIds = new Set(found);
      var nodes = found.map(function (id) { return stage.findOne('#tbl-' + id); }).filter(Boolean);
      if (S.mode === 'builder') { transformer.nodes(nodes); tableLayer.batchDraw(); }
      updateZoneButton();
      showMultiSelectFloat(found.length);
    }
  }

  /* ══════════════════════════════════════════════════════════════════
     OVERLAP DETECTION
  ══════════════════════════════════════════════════════════════════ */
  function tableAABB(item) {
    var w = toNum(item.width, 80);
    var h = toNum(item.height, 80);
    return { x: toNum(item.pos_x, 0) - w / 2, y: toNum(item.pos_y, 0) - h / 2, w: w, h: h };
  }

  function aabbOverlap(a, b, pad) {
    pad = pad || 0;
    return !(
      a.x + a.w + pad <= b.x ||
      b.x + b.w + pad <= a.x ||
      a.y + a.h + pad <= b.y ||
      b.y + b.h + pad <= a.y
    );
  }

  function isOverlappingAny(x, y, w, h, excludeId) {
    var test = { x: x - w / 2, y: y - h / 2, w: w, h: h };
    return Object.values(S.tables).some(function (other) {
      if (excludeId !== undefined && String(other.id) === String(excludeId)) return false;
      if (other.type === 'text_label' || other.type === 'zone') return false;
      return aabbOverlap(test, tableAABB(other), 4);
    });
  }

  /* ══════════════════════════════════════════════════════════════════
     MODAL — delegate to shared thr-modal.js
  ══════════════════════════════════════════════════════════════════ */
  var fpModal = window.thrModal || function (opts) {
    if (opts.type === 'confirm') return Promise.resolve(confirm(opts.title || ''));
    if (opts.type === 'alert')   { alert(opts.body || opts.title || ''); return Promise.resolve(true); }
    var r = prompt(opts.title || ''); return Promise.resolve(r);
  };

  /* ══════════════════════════════════════════════════════════════════
     TOAST NOTIFICATIONS
  ══════════════════════════════════════════════════════════════════ */
  function showToast(msg, type) {
    var toast = document.getElementById('fp-toast');
    if (!toast) return;
    toast.textContent = msg;
    toast.className = 'fp-toast fp-toast--' + (type || 'info') + ' fp-toast--visible';
    clearTimeout(toast._t);
    toast._t = setTimeout(function () {
      toast.classList.remove('fp-toast--visible');
    }, 2500);
  }

  /* ══════════════════════════════════════════════════════════════════
     FLOOR LOADING
  ══════════════════════════════════════════════════════════════════ */
  function loadFloors() {
    apiFetch('GET', 'floor-plans').then(function (data) {
      S.floors = Array.isArray(data) ? data : (data.data || []);
      renderFloorTabs();
      if (S.floors.length) {
        selectFloor(S.floors[0].id);
        S.floors.forEach(function (f) { loadFloorStats(f.id); });
      } else {
        showEmptyState(true);
      }
    }).catch(function () {
      S.floors = [];
      renderFloorTabs();
      showEmptyState(true);
    });
  }

  function selectFloor(id) {
    S.floorId = id;
    S.activeLayout = null;
    S.layouts = [];
    document.querySelectorAll('.fp-floor-tab').forEach(function (el) {
      el.classList.toggle('fp-floor-tab--active', String(el.dataset.floorId) === String(id));
    });
    var floor = S.floors.find(function (f) { return String(f.id) === String(id); });
    loadBackground(floor || null);
    loadFurniture(id);
    deselect();
    initLayoutSystem(floor);
  }

  /* ── Render floor tabs ──────────────────────────────────────────── */
  function renderFloorTabs() {
    var container = document.getElementById('fp-floor-tabs');
    if (!container) return;
    container.innerHTML = '';

    S.floors.forEach(function (floor, i) {
      var iconLabel = i === 0 ? 'G' : String(i);
      var tab = document.createElement('div');
      tab.className = 'fp-floor-tab';
      tab.dataset.floorId = floor.id;
      tab.innerHTML =
        '<div class="fp-floor-tab-inner">' +
          '<span class="fp-floor-icon">' + escHtml(iconLabel) + '</span>' +
          '<span class="fp-floor-tab-name">' + escHtml(floor.name) + '</span>' +
        '</div>' +
        '<div class="fp-floor-popup">' +
          '<div class="fp-fpopup-title">' + escHtml(floor.name) + '</div>' +
          '<div class="fp-fpopup-stats">' +
            '<div class="fp-fpopup-row"><span class="fp-fpopup-label">Tables</span><span class="fp-fpopup-num" data-stat="tables">—</span></div>' +
            '<div class="fp-fpopup-row"><span class="fp-fpopup-label">Seat capacity</span><span class="fp-fpopup-num" data-stat="seats">—</span></div>' +
          '</div>' +
          '<div class="fp-fpopup-divider"></div>' +
          '<div class="fp-fpopup-booked">' +
            '<span class="fp-fpopup-label">Booked today</span>' +
            '<span class="fp-fpopup-num" data-stat="booked">—</span>' +
          '</div>' +
        '</div>';

      tab.querySelector('.fp-floor-tab-inner').addEventListener('click', function () {
        selectFloor(floor.id);
      });
      container.appendChild(tab);
    });
  }

  /* ── Load popup stats ───────────────────────────────────────────── */
  function loadFloorStats(floorId) {
    apiFetch('GET', 'floor-plans/' + floorId + '/furniture').then(function (data) {
      var items = Array.isArray(data) ? data : (data.data || []);
      var tables = items.filter(function (i) { return !isZone(i.type) && i.type !== 'text_label'; }).length;
      var seats  = items.reduce(function (s, i) { return s + (parseInt(i.capacity_max) || 0); }, 0);
      if (!S.floorStats[floorId]) S.floorStats[floorId] = {};
      S.floorStats[floorId].tables = tables;
      S.floorStats[floorId].seats  = seats;
      updateFloorPopup(floorId);
    });

    apiFetch('GET', 'reservations?date=' + S.date + '&per_page=100').then(function (data) {
      var items = Array.isArray(data) ? data : (data.data || []);
      var booked = items.filter(function (r) {
        return r.floor_plan_id === floorId || r.floor_plan_id === null;
      }).length;
      if (!S.floorStats[floorId]) S.floorStats[floorId] = {};
      S.floorStats[floorId].booked = booked;
      updateFloorPopup(floorId);
    }).catch(function () {});
  }

  function updateFloorPopup(floorId) {
    var stats = S.floorStats[floorId];
    if (!stats) return;
    var tab = document.querySelector('.fp-floor-tab[data-floor-id="' + floorId + '"]');
    if (!tab) return;
    var popup = tab.querySelector('.fp-floor-popup');
    if (!popup) return;
    ['tables', 'seats', 'booked'].forEach(function (k) {
      var el = popup.querySelector('[data-stat="' + k + '"]');
      if (el && stats[k] !== undefined) el.textContent = String(stats[k]);
    });
  }

  /* ══════════════════════════════════════════════════════════════════
     BACKGROUND IMAGE LAYER
  ══════════════════════════════════════════════════════════════════ */
  function loadBackground(floor, autoFit) {
    if (S.bgEditMode) exitBgEditMode(false);
    if (S.bgCropMode) exitBgCropMode(false);
    bgLayer.destroyChildren();
    bgLayer.batchDraw();
    S.bgKonvaImg = null;

    if (!floor || !floor.background_url) {
      S.bgConfig = { url: null, scale: 1, scaleY: 0, opacity: 0.5, offsetX: 0, offsetY: 0, crop: null };
      updateBgControls();
      return;
    }

    S.bgConfig = {
      url:     floor.background_url,
      scale:   toNum(floor.bg_scale,    1),
      scaleY:  toNum(floor.bg_scale_y,  0),
      opacity: toNum(floor.bg_opacity,  0.5),
      offsetX: toNum(floor.bg_offset_x, 0),
      offsetY: toNum(floor.bg_offset_y, 0),
      crop:    (floor.bg_crop && floor.bg_crop.w > 0) ? floor.bg_crop : null,
    };

    var img = new window.Image();
    img.crossOrigin = 'anonymous';
    img.onload = function () {
      var sx = S.bgConfig.scale;
      var sy = S.bgConfig.scaleY || sx;

      // Auto-fit on first upload or default state — skip if a crop is already stored
      if (!S.bgConfig.crop && (autoFit || (S.bgConfig.scale === 1 && !S.bgConfig.scaleY && !S.bgConfig.offsetX && !S.bgConfig.offsetY))) {
        var stageW = stage.width();
        var stageH = stage.height();
        var fit    = Math.min(stageW / img.naturalWidth, stageH / img.naturalHeight, 1);
        sx = fit;
        sy = fit;
        S.bgConfig.scale   = sx;
        S.bgConfig.scaleY  = 0;
        S.bgConfig.offsetX = Math.round((stageW - img.naturalWidth  * sx) / 2);
        S.bgConfig.offsetY = Math.round((stageH - img.naturalHeight * sy) / 2);
        if (S.floorId) {
          apiFetch('PATCH', 'floor-plans/' + S.floorId, {
            bg_scale: S.bgConfig.scale, bg_scale_y: 0,
            bg_offset_x: S.bgConfig.offsetX, bg_offset_y: S.bgConfig.offsetY,
          }).catch(function () {});
        }
      }

      var konvaImg = new Konva.Image({
        image:     img,
        x:         S.bgConfig.offsetX,
        y:         S.bgConfig.offsetY,
        scaleX:    sx,
        scaleY:    sy,
        opacity:   S.bgConfig.opacity,
        listening: false,
        draggable: false,
        name:      'bg-image',
      });

      // Apply stored crop if present
      var c = S.bgConfig.crop;
      if (c && c.w > 0) {
        konvaImg.setAttrs({
          width:  c.w,
          height: c.h,
          crop:   { x: c.x, y: c.y, width: c.w, height: c.h },
        });
      }

      S.bgKonvaImg = konvaImg;
      bgLayer.add(konvaImg);
      bgLayer.batchDraw();
      updateBgControls();
    };
    img.onerror = function () { showToast('Background image failed to load', 'warn'); };
    img.src = floor.background_url;

    updateBgControls();
  }

  function updateBgControls() {
    var hasUrl    = !!S.bgConfig.url;
    var sliders   = document.getElementById('fp-bg-sliders');
    var noThumb   = document.getElementById('fp-bg-no-thumb');
    var thumb     = document.getElementById('fp-bg-thumb');
    var opSlider  = document.getElementById('fp-bg-opacity');
    var scSlider  = document.getElementById('fp-bg-scale');
    var dot       = document.getElementById('fp-bg-nav-dot');
    var navBtn    = document.getElementById('fp-bg-nav-btn');
    var moveBtn   = document.getElementById('fp-bg-move-btn');
    var cropBtn   = document.getElementById('fp-bg-crop-btn');

    if (sliders)  sliders.hidden  = !hasUrl;
    if (noThumb)  noThumb.hidden  = hasUrl;
    if (thumb) {
      thumb.hidden = !hasUrl;
      if (hasUrl) thumb.src = S.bgConfig.url;
    }
    if (opSlider) opSlider.value = String(S.bgConfig.opacity);
    if (scSlider) scSlider.value = String(S.bgConfig.scale);
    if (dot)      dot.hidden     = !hasUrl;
    if (navBtn)   navBtn.classList.toggle('fp-bg-nav-btn--has-bg', hasUrl);
    if (moveBtn)  moveBtn.hidden = !hasUrl;
    if (cropBtn)  cropBtn.hidden = !hasUrl;
  }

  function applyBgUpdate(patch) {
    Object.assign(S.bgConfig, patch);
    if (S.bgKonvaImg) {
      if (patch.opacity  !== undefined) S.bgKonvaImg.opacity(patch.opacity);
      if (patch.scale    !== undefined) {
        S.bgKonvaImg.scaleX(patch.scale);
        // When slider sets uniform scale, reset Y to match and clear scaleY
        S.bgConfig.scaleY = 0;
        S.bgKonvaImg.scaleY(patch.scale);
      }
      if (patch.offsetX  !== undefined) S.bgKonvaImg.x(patch.offsetX);
      if (patch.offsetY  !== undefined) S.bgKonvaImg.y(patch.offsetY);
      bgLayer.batchDraw();
    }
    clearTimeout(applyBgUpdate._t);
    applyBgUpdate._t = setTimeout(function () {
      if (!S.floorId || !S.bgConfig.url) return;
      apiFetch('PATCH', 'floor-plans/' + S.floorId, {
        bg_scale:    S.bgConfig.scale,
        bg_scale_y:  S.bgConfig.scaleY || 0,
        bg_opacity:  S.bgConfig.opacity,
        bg_offset_x: S.bgConfig.offsetX,
        bg_offset_y: S.bgConfig.offsetY,
      }).catch(function () {});
    }, 800);
  }

  function removeBg() {
    if (!S.floorId) return;
    if (S.bgEditMode) exitBgEditMode(false);
    if (S.bgCropMode) exitBgCropMode(false);
    apiFetch('PATCH', 'floor-plans/' + S.floorId, {
      bg_scale: 1, bg_scale_y: 0, bg_opacity: 0.5, bg_offset_x: 0, bg_offset_y: 0, bg_crop: null,
    }).catch(function () {});
    bgLayer.destroyChildren();
    bgLayer.batchDraw();
    S.bgConfig = { url: null, scale: 1, scaleY: 0, opacity: 0.5, offsetX: 0, offsetY: 0, crop: null };
    S.bgKonvaImg = null;
    var floor = S.floors.find(function (f) { return String(f.id) === String(S.floorId); });
    if (floor) floor.background_url = null;
    updateBgControls();
    showToast('Background removed', 'info');
  }

  function enterBgEditMode() {
    if (!S.bgKonvaImg || S.bgEditMode) return;
    S.bgEditMode = true;

    // Bring bgLayer above tables so transformer handles are visible
    bgLayer.moveToTop();
    bgLayer.listening(true);
    S.bgKonvaImg.listening(true);
    S.bgKonvaImg.draggable(true);

    // Clear any table selection — can't select tables while editing bg
    deselect();

    var bgTr = new Konva.Transformer({
      nodes:          [S.bgKonvaImg],
      keepRatio:      S.bgKeepRatio,
      rotateEnabled:  false,
      borderStroke:        '#3B82F6',
      borderStrokeWidth:   1.5,
      borderDash:          [4, 3],
      anchorStroke:        '#3B82F6',
      anchorFill:          '#ffffff',
      anchorSize:          10,
      anchorCornerRadius:  2,
      anchorStrokeWidth:   1.5,
    });
    S.bgTransformer = bgTr;
    bgLayer.add(bgTr);
    bgLayer.batchDraw();

    // Save on transform / drag end
    S.bgKonvaImg.on('transformend.bgedit', function () {
      S.bgConfig.scale   = S.bgKonvaImg.scaleX();
      S.bgConfig.scaleY  = S.bgKonvaImg.scaleY();
      S.bgConfig.offsetX = Math.round(S.bgKonvaImg.x());
      S.bgConfig.offsetY = Math.round(S.bgKonvaImg.y());
      saveBgTransform();
    });
    S.bgKonvaImg.on('dragend.bgedit', function () {
      S.bgConfig.offsetX = Math.round(S.bgKonvaImg.x());
      S.bgConfig.offsetY = Math.round(S.bgKonvaImg.y());
      saveBgTransform();
    });

    var bar = document.getElementById('fp-bg-edit-bar');
    if (bar) bar.hidden = false;

    // Close the bg popover so it doesn't overlap
    var popover = document.getElementById('fp-bg-popover');
    var navBtn  = document.getElementById('fp-bg-nav-btn');
    if (popover) { popover.hidden = true; }
    if (navBtn)  { navBtn.classList.remove('fp-bg-nav-btn--open'); }
  }

  function exitBgEditMode(save) {
    if (!S.bgEditMode) return;
    S.bgEditMode = false;

    if (S.bgTransformer) {
      S.bgTransformer.destroy();
      S.bgTransformer = null;
    }
    if (S.bgKonvaImg) {
      S.bgKonvaImg.off('.bgedit');
      S.bgKonvaImg.draggable(false);
      S.bgKonvaImg.listening(false);
      if (save !== false) {
        S.bgConfig.scale   = S.bgKonvaImg.scaleX();
        S.bgConfig.scaleY  = S.bgKonvaImg.scaleY();
        S.bgConfig.offsetX = Math.round(S.bgKonvaImg.x());
        S.bgConfig.offsetY = Math.round(S.bgKonvaImg.y());
        saveBgTransform();
      }
    }

    bgLayer.listening(false);
    bgLayer.moveToBottom(); // restore z-order: bg below zones below tables
    // zoneLayer sits in middle — move it back above bgLayer
    zoneLayer.moveToTop();
    tableLayer.moveToTop();
    bgLayer.batchDraw();

    var bar = document.getElementById('fp-bg-edit-bar');
    if (bar) bar.hidden = true;
  }

  function saveBgTransform() {
    if (!S.floorId || !S.bgConfig.url) return;
    clearTimeout(saveBgTransform._t);
    saveBgTransform._t = setTimeout(function () {
      apiFetch('PATCH', 'floor-plans/' + S.floorId, {
        bg_scale:    S.bgConfig.scale,
        bg_scale_y:  S.bgConfig.scaleY || 0,
        bg_offset_x: S.bgConfig.offsetX,
        bg_offset_y: S.bgConfig.offsetY,
      }).catch(function () {});
    }, 600);
  }

  /* ── Crop mode ──────────────────────────────────────────────────────── */

  function enterBgCropMode() {
    if (!S.bgKonvaImg) return;
    if (S.bgEditMode) exitBgEditMode(false);
    if (S.bgCropMode) return;
    S.bgCropMode = true;

    bgLayer.moveToTop();
    bgLayer.listening(true);
    deselect();

    var img   = S.bgKonvaImg;
    var imgEl = img.getAttr('image');
    var natW  = imgEl ? imgEl.naturalWidth  : 1;
    var natH  = imgEl ? imgEl.naturalHeight : 1;
    var sx    = img.scaleX();
    var sy    = img.scaleY();

    // If a crop is already applied, restore the full image temporarily so user can adjust
    var existingCrop = S.bgConfig.crop;
    var origImgX, origImgY;
    if (existingCrop && existingCrop.w > 0) {
      origImgX = img.x() - existingCrop.x * sx;
      origImgY = img.y() - existingCrop.y * sy;
      img.setAttrs({ x: origImgX, y: origImgY, width: natW, height: natH, crop: null });
    } else {
      origImgX = img.x();
      origImgY = img.y();
      // Clear any explicit width/height to ensure we're using natural dimensions
      img.setAttrs({ width: natW, height: natH, crop: null });
    }
    bgLayer.batchDraw();

    S.bgCropOrigX = origImgX;
    S.bgCropOrigY = origImgY;

    var imgCanvasW = natW * sx;
    var imgCanvasH = natH * sy;

    // Initial crop rect: matches existing crop (or full image)
    var initX = existingCrop ? (origImgX + existingCrop.x * sx) : origImgX;
    var initY = existingCrop ? (origImgY + existingCrop.y * sy) : origImgY;
    var initW = existingCrop ? (existingCrop.w * sx) : imgCanvasW;
    var initH = existingCrop ? (existingCrop.h * sy) : imgCanvasH;

    // Dark overlay (4 rects covering outside the crop area)
    var DARK = 'rgba(0,0,0,0.6)';
    var topOvl    = new Konva.Rect({ fill: DARK, listening: false, name: 'crop-ovl' });
    var bottomOvl = new Konva.Rect({ fill: DARK, listening: false, name: 'crop-ovl' });
    var leftOvl   = new Konva.Rect({ fill: DARK, listening: false, name: 'crop-ovl' });
    var rightOvl  = new Konva.Rect({ fill: DARK, listening: false, name: 'crop-ovl' });
    bgLayer.add(topOvl); bgLayer.add(bottomOvl);
    bgLayer.add(leftOvl); bgLayer.add(rightOvl);
    S.bgCropOverlays = { top: topOvl, bottom: bottomOvl, left: leftOvl, right: rightOvl,
                         ix: origImgX, iy: origImgY, iw: imgCanvasW, ih: imgCanvasH };

    // Visual crop border
    var cropBorder = new Konva.Rect({
      fill: 'transparent',
      stroke: 'rgba(255,255,255,0.9)',
      strokeWidth: 1.5,
      dash: [6, 3],
      listening: false,
      name: 'crop-border',
    });
    S.bgCropBorder = cropBorder;
    bgLayer.add(cropBorder);

    // Transparent drag target (the crop rect itself)
    var cropHelper = new Konva.Rect({
      x: initX, y: initY,
      width: initW, height: initH,
      fill: 'transparent',
      draggable: true,
      name: 'crop-helper',
    });
    S.bgCropRect = cropHelper;
    bgLayer.add(cropHelper);

    // Transformer for resize handles
    var cropTr = new Konva.Transformer({
      nodes: [cropHelper],
      keepRatio: false,
      rotateEnabled: false,
      borderStroke: 'rgba(255,255,255,0.7)',
      borderStrokeWidth: 1,
      anchorStroke: '#ffffff',
      anchorFill: 'rgba(255,255,255,0.9)',
      anchorSize: 9,
      anchorCornerRadius: 2,
      anchorStrokeWidth: 1.5,
    });
    S.bgCropTr = cropTr;
    bgLayer.add(cropTr);

    // Constrain drag to image bounds
    cropHelper.dragBoundFunc(function (pos) {
      var cw = this.width()  * (this.scaleX() || 1);
      var ch = this.height() * (this.scaleY() || 1);
      var o  = S.bgCropOverlays;
      return {
        x: Math.max(o.ix, Math.min(o.ix + o.iw - cw, pos.x)),
        y: Math.max(o.iy, Math.min(o.iy + o.ih - ch, pos.y)),
      };
    });

    // Constrain resize to image bounds, minimum 20px
    cropTr.boundBoxFunc(function (oldBox, newBox) {
      var o = S.bgCropOverlays;
      var x = Math.max(o.ix, newBox.x);
      var y = Math.max(o.iy, newBox.y);
      var w = Math.min(o.ix + o.iw - x, Math.max(20, newBox.width));
      var h = Math.min(o.iy + o.ih - y, Math.max(20, newBox.height));
      return { x: x, y: y, width: w, height: h };
    });

    // Bake transformer scale into width/height on every tick (keeps scaleX/Y = 1)
    cropHelper.on('transform', function () {
      this.setAttrs({
        width:  Math.max(20, this.width()  * (this.scaleX() || 1)),
        height: Math.max(20, this.height() * (this.scaleY() || 1)),
        scaleX: 1, scaleY: 1,
      });
      updateCropOverlay();
    });
    cropHelper.on('dragmove', function () { updateCropOverlay(); });

    updateCropOverlay();

    var bar = document.getElementById('fp-bg-crop-bar');
    if (bar) bar.hidden = false;

    var popover = document.getElementById('fp-bg-popover');
    var navBtn  = document.getElementById('fp-bg-nav-btn');
    if (popover) popover.hidden = true;
    if (navBtn)  navBtn.classList.remove('fp-bg-nav-btn--open');
  }

  function updateCropOverlay() {
    if (!S.bgCropRect || !S.bgCropOverlays) return;
    var cr = S.bgCropRect;
    var cx = cr.x(), cy = cr.y();
    var cw = cr.width()  * (cr.scaleX() || 1);
    var ch = cr.height() * (cr.scaleY() || 1);
    var o  = S.bgCropOverlays;

    o.top.setAttrs({    x: o.ix, y: o.iy,    width: o.iw,               height: Math.max(0, cy - o.iy)           });
    o.bottom.setAttrs({ x: o.ix, y: cy + ch, width: o.iw,               height: Math.max(0, o.iy + o.ih - cy - ch) });
    o.left.setAttrs({   x: o.ix, y: cy,      width: Math.max(0, cx - o.ix), height: ch                             });
    o.right.setAttrs({  x: cx + cw, y: cy,   width: Math.max(0, o.ix + o.iw - cx - cw), height: ch                });

    if (S.bgCropBorder) S.bgCropBorder.setAttrs({ x: cx, y: cy, width: cw, height: ch });

    bgLayer.batchDraw();
  }

  function applyBgCrop() {
    if (!S.bgCropRect || !S.bgKonvaImg) return;
    var cr  = S.bgCropRect;
    var img = S.bgKonvaImg;

    var cx = cr.x(), cy = cr.y();
    var cw = cr.width()  * (cr.scaleX() || 1);
    var ch = cr.height() * (cr.scaleY() || 1);

    var sx    = img.scaleX();
    var sy    = img.scaleY();
    var imgEl = img.getAttr('image');
    var natW  = imgEl ? imgEl.naturalWidth  : 1;
    var natH  = imgEl ? imgEl.naturalHeight : 1;

    // Convert canvas-space crop rect to source-image pixel coordinates
    var origX   = S.bgCropOrigX;
    var origY   = S.bgCropOrigY;
    var imgCropX = (cx - origX) / sx;
    var imgCropY = (cy - origY) / sy;
    var imgCropW = cw / sx;
    var imgCropH = ch / sy;

    // Clamp to natural image bounds
    imgCropX = Math.max(0, Math.min(natW - 1, imgCropX));
    imgCropY = Math.max(0, Math.min(natH - 1, imgCropY));
    imgCropW = Math.min(natW - imgCropX, Math.max(1, imgCropW));
    imgCropH = Math.min(natH - imgCropY, Math.max(1, imgCropH));

    // Apply to the Konva image (x/y = canvas position of crop top-left)
    img.setAttrs({
      x:      cx, y:      cy,
      width:  imgCropW, height: imgCropH,
      crop:   { x: imgCropX, y: imgCropY, width: imgCropW, height: imgCropH },
    });

    S.bgConfig.offsetX = cx;
    S.bgConfig.offsetY = cy;
    S.bgConfig.crop    = { x: imgCropX, y: imgCropY, w: imgCropW, h: imgCropH };

    if (S.floorId) {
      apiFetch('PATCH', 'floor-plans/' + S.floorId, {
        bg_offset_x: cx, bg_offset_y: cy,
        bg_crop: JSON.stringify(S.bgConfig.crop),
      }).catch(function () {});
    }

    exitBgCropMode(true);
    bgLayer.batchDraw();
  }

  function exitBgCropMode(cropAlreadyApplied) {
    if (!S.bgCropMode) return;
    S.bgCropMode = false;

    // Destroy all crop UI nodes
    if (S.bgCropTr)     { S.bgCropTr.destroy();     S.bgCropTr = null; }
    if (S.bgCropRect)   { S.bgCropRect.destroy();   S.bgCropRect = null; }
    if (S.bgCropBorder) { S.bgCropBorder.destroy(); S.bgCropBorder = null; }
    if (S.bgCropOverlays) {
      var ovls = S.bgCropOverlays;
      ovls.top.destroy(); ovls.bottom.destroy();
      ovls.left.destroy(); ovls.right.destroy();
      S.bgCropOverlays = null;
    }

    // If cancelling (not applying): restore image to its pre-crop-mode state
    if (!cropAlreadyApplied && S.bgKonvaImg) {
      var img = S.bgKonvaImg;
      var c   = S.bgConfig.crop;
      if (c && c.w > 0) {
        // Re-apply the existing crop using stored original image origin
        img.setAttrs({
          x:      S.bgCropOrigX + c.x * img.scaleX(),
          y:      S.bgCropOrigY + c.y * img.scaleY(),
          width:  c.w, height: c.h,
          crop:   { x: c.x, y: c.y, width: c.w, height: c.h },
        });
      } else {
        // No prior crop — restore to full image at original position
        var imgEl = img.getAttr('image');
        img.setAttrs({
          x:      S.bgCropOrigX, y: S.bgCropOrigY,
          width:  imgEl ? imgEl.naturalWidth  : 0,
          height: imgEl ? imgEl.naturalHeight : 0,
          crop:   null,
        });
      }
    }

    bgLayer.listening(false);
    bgLayer.moveToBottom();
    zoneLayer.moveToTop();
    tableLayer.moveToTop();
    bgLayer.batchDraw();

    var bar = document.getElementById('fp-bg-crop-bar');
    if (bar) bar.hidden = true;
  }

  function bindBgControls() {
    var fileInput = document.getElementById('fp-bg-file');
    var uploadBtn = document.getElementById('fp-bg-upload');
    var opSlider  = document.getElementById('fp-bg-opacity');
    var scSlider  = document.getElementById('fp-bg-scale');
    var removeBtn = document.getElementById('fp-bg-remove');
    var navBtn    = document.getElementById('fp-bg-nav-btn');
    var popover   = document.getElementById('fp-bg-popover');

    if (navBtn && popover) {
      navBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        var isOpen = !popover.hidden;
        popover.hidden = isOpen;
        navBtn.classList.toggle('fp-bg-nav-btn--open', !isOpen);
      });
      document.addEventListener('click', function (e) {
        if (!popover.hidden && !popover.contains(e.target) && e.target !== navBtn) {
          popover.hidden = true;
          navBtn.classList.remove('fp-bg-nav-btn--open');
        }
      });
    }

    if (uploadBtn && fileInput) {
      var uploadLbl = uploadBtn.querySelector('span');
      uploadBtn.addEventListener('click', function () { fileInput.click(); });
      fileInput.addEventListener('change', function () {
        var file = fileInput.files[0];
        if (!file || !S.floorId) return;
        var allowed = ['image/jpeg', 'image/png', 'image/webp'];
        if (!allowed.includes(file.type)) {
          showToast('Please upload a JPEG, PNG, or WebP image', 'warn');
          fileInput.value = '';
          return;
        }
        if (file.size > 20 * 1024 * 1024) {
          showToast('File must be under 20 MB', 'warn');
          fileInput.value = '';
          return;
        }
        if (uploadLbl) uploadLbl.textContent = 'Uploading…';
        uploadBtn.disabled = true;
        var fd = new FormData();
        fd.append('background', file);
        fetch(API + 'floor-plans/' + S.floorId + '/background', {
          method: 'POST',
          headers: { 'X-WP-Nonce': NONCE },
          body: fd,
        }).then(function (r) {
          if (!r.ok) return r.json().then(function (e) { throw e; });
          return r.json();
        }).then(function (floor) {
          var idx = S.floors.findIndex(function (f) { return String(f.id) === String(S.floorId); });
          if (idx >= 0) S.floors[idx] = floor;
          loadBackground(floor, true); // autoFit=true on fresh upload
          showToast('Background uploaded', 'info');
        }).catch(function (e) {
          showToast((e && e.message) ? e.message : 'Upload failed', 'warn');
        }).finally(function () {
          if (uploadLbl) uploadLbl.textContent = 'Upload image';
          uploadBtn.disabled = false;
          fileInput.value = '';
        });
      });
    }

    if (opSlider) {
      opSlider.addEventListener('input', function () {
        applyBgUpdate({ opacity: parseFloat(this.value) });
      });
    }

    if (scSlider) {
      scSlider.addEventListener('input', function () {
        applyBgUpdate({ scale: parseFloat(this.value) });
      });
    }

    if (removeBtn) {
      removeBtn.addEventListener('click', function () {
        removeBg();
        if (popover) { popover.hidden = true; }
        if (navBtn) navBtn.classList.remove('fp-bg-nav-btn--open');
      });
    }

    var ratioBtn = document.getElementById('fp-bg-ratio-btn');
    if (ratioBtn) {
      ratioBtn.addEventListener('click', function () {
        S.bgKeepRatio = !S.bgKeepRatio;
        ratioBtn.dataset.locked = String(S.bgKeepRatio);
        var lbl = ratioBtn.querySelector('.fp-bg-ratio-label');
        if (lbl) lbl.textContent = S.bgKeepRatio ? 'Lock ratio' : 'Free resize';
        // Update transformer live if currently in edit mode
        if (S.bgTransformer) {
          S.bgTransformer.keepRatio(S.bgKeepRatio);
          bgLayer.batchDraw();
        }
      });
    }

    var moveBtn  = document.getElementById('fp-bg-move-btn');
    var doneBtn  = document.getElementById('fp-bg-edit-done');

    if (moveBtn) {
      moveBtn.addEventListener('click', function () {
        enterBgEditMode();
      });
    }

    if (doneBtn) {
      doneBtn.addEventListener('click', function () {
        exitBgEditMode(true);
      });
    }

    var cropBtn   = document.getElementById('fp-bg-crop-btn');
    var cropApply = document.getElementById('fp-bg-crop-apply');
    var cropReset = document.getElementById('fp-bg-crop-reset');

    if (cropBtn) {
      cropBtn.addEventListener('click', function () {
        enterBgCropMode();
      });
    }

    if (cropApply) {
      cropApply.addEventListener('click', function () {
        applyBgCrop();
      });
    }

    if (cropReset) {
      cropReset.addEventListener('click', function () {
        if (!S.bgKonvaImg || !S.bgCropMode) return;
        var img   = S.bgKonvaImg;
        var imgEl = img.getAttr('image');
        // Restore full image at original position
        img.setAttrs({
          x:      S.bgCropOrigX, y: S.bgCropOrigY,
          width:  imgEl ? imgEl.naturalWidth  : 0,
          height: imgEl ? imgEl.naturalHeight : 0,
          crop:   null,
        });
        S.bgConfig.offsetX = S.bgCropOrigX;
        S.bgConfig.offsetY = S.bgCropOrigY;
        S.bgConfig.crop    = null;
        if (S.floorId) {
          apiFetch('PATCH', 'floor-plans/' + S.floorId, {
            bg_offset_x: S.bgCropOrigX, bg_offset_y: S.bgCropOrigY,
            bg_crop: null,
          }).catch(function () {});
        }
        exitBgCropMode(true);
        bgLayer.batchDraw();
      });
    }

    // Esc exits either bg edit mode or crop mode
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        if (S.bgCropMode)  exitBgCropMode(false);
        else if (S.bgEditMode) exitBgEditMode(true);
      }
    });
  }

  /* ══════════════════════════════════════════════════════════════════
     FURNITURE LOADING + RENDERING
  ══════════════════════════════════════════════════════════════════ */
  function loadFurniture(floorId) {
    S.tables = {};
    clearCanvas();
    showEmptyState(false);

    apiFetch('GET', 'floor-plans/' + floorId + '/furniture').then(function (data) {
      var items = Array.isArray(data) ? data : (data.data || []);
      items.forEach(function (item) { S.tables[item.id] = item; });
      renderAllTables();
      renderZoneLayer();
      updateStatusBar();
      if (S.mode === 'live') startLiveUpdates();
      if (!items.length) showEmptyState(true);
    }).catch(function () {
      showEmptyState(true);
    });
  }

  function renderAllTables() {
    clearCanvas();
    Object.values(S.tables).forEach(function (item) { addTableNode(item); });
    tableLayer.batchDraw();
  }

  function clearCanvas() {
    tableLayer.getChildren(function (n) { return n !== transformer; }).forEach(function (n) { n.destroy(); });
    tableLayer.batchDraw();
  }

  /* ── Build and add a table / label to the layer ─────────────────── */
  function addTableNode(item, autoSelect) {
    // Zones are rendered in zoneLayer, not tableLayer
    if (item.type === 'zone') return null;

    var group = new Konva.Group({
      id:        'tbl-' + item.id,
      x:         toNum(item.pos_x, 200),
      y:         toNum(item.pos_y, 200),
      rotation:  toNum(item.rotation_deg, 0),
      draggable: S.mode === 'builder',
      name:      'table-group',
    });

    // Group membership ring — drawn BEFORE the table shape so it renders behind
    if (item.group_id) {
      var gW = toNum(item.width, 80);
      var gH = toNum(item.height, 80);
      if (isRound(item)) {
        var gR = Math.max(24, Math.min(52, gW / 2)) + 9;
        group.add(new Konva.Circle({
          radius: gR, fill: 'transparent',
          stroke: '#0D9488', strokeWidth: 2, dash: [5, 3],
          listening: false, name: 'group-ring',
        }));
      } else {
        group.add(new Konva.Rect({
          x: -gW / 2 - 9, y: -gH / 2 - 9, width: gW + 18, height: gH + 18,
          fill: 'transparent',
          stroke: '#0D9488', strokeWidth: 2, dash: [5, 3],
          cornerRadius: 10, listening: false, name: 'group-ring',
        }));
      }
    }

    if (item.type === 'text_label')   { drawTextLabel(group, item); }
    else if (isBarSeat(item))         { drawBarSeat(group, item); }
    else if (isRound(item))           { drawRoundTable(group, item); }
    else if (isLounge(item))          { drawLounge(group, item); }
    else if (isBooth(item.type))      { drawBoothTable(group, item); }
    else                              { drawRectTable(group, item); }

    group.on('click tap', function (e) {
      e.cancelBubble = true;
      if (S.placing) return;
      if (S.pendingZoneId && e.evt && e.evt.shiftKey && S.mode === 'builder') {
        toggleZoneMembership(String(item.id));
      } else if (e.evt && e.evt.shiftKey && S.mode === 'builder') {
        toggleMultiSelect(String(item.id));
      } else {
        selectTable(item.id);
      }
    });

    group.on('dblclick dbltap', function (e) {
      e.cancelBubble = true;
      if (S.mode !== 'builder') return;
      startInlineLabelEdit(item.id, group);
    });

    group.on('dragstart', function () {
      pushUndo(item.id);
      var d = S.tables[item.id];
      if (d) { d._preDragX = d.pos_x; d._preDragY = d.pos_y; }
    });

    group.on('dragmove', function () {
      if (S.mode !== 'builder') return;
      var d = S.tables[item.id];
      if (!d || d.type === 'zone' || d.type === 'text_label') return;
      var cx = this.x();
      var cy = this.y();
      Object.values(S.tables).forEach(function (z) {
        if (z.type !== 'zone') return;
        var zGroup = zoneLayer.findOne('#zone-' + z.id);
        if (!zGroup) return;
        var zRect = zGroup.findOne('Rect');
        if (!zRect) return;
        var c = (z.meta && z.meta.color) ? z.meta.color : '#EAF5EE';
        var bbox = getZoneBBoxRaw(z, d.id);
        var inside = bbox && (cx > bbox.x1 && cx < bbox.x2 && cy > bbox.y1 && cy < bbox.y2);
        zRect.fill(hexToRgba(c, inside ? 0.22 : 0.12));
        zRect.stroke(inside ? 'rgba(60,140,80,0.65)' : 'rgba(100,110,100,0.38)');
        zRect.strokeWidth(inside ? 2 : 1.5);
        zRect.dash(inside ? [] : [6, 4]);
      });
      zoneLayer.batchDraw();
    });

    group.on('dragend', function () {
      var d = S.tables[item.id];
      if (!d) return;
      var nx = snapToGrid(this.x());
      var ny = snapToGrid(this.y());

      if (d.type !== 'text_label' && d.type !== 'zone' && isOverlappingAny(nx, ny, toNum(d.width, 80), toNum(d.height, 80), item.id)) {
        this.position({ x: d.pos_x, y: d.pos_y });
        if (transformer.nodes().indexOf(this) !== -1) {
          transformer.nodes([this]);
        }
        tableLayer.draw();
        showToast('Tables cannot overlap', 'warn');
        S.undoStack.pop();
        syncUndoBtns();
        resetZoneHighlights();
        return;
      }

      d.pos_x = nx;
      d.pos_y = ny;
      this.position({ x: d.pos_x, y: d.pos_y });
      markDirty();
      tableLayer.batchDraw();
      resetZoneHighlights();

      // Zone membership: auto-add when dropped inside a zone, confirm-remove when dragged out
      if (S.mode === 'builder' && d.type !== 'zone' && d.type !== 'text_label') {
        var sid       = String(item.id);
        var finalX    = d.pos_x;
        var finalY    = d.pos_y;
        var didAdd    = false;
        var hasPendingConfirm = false;

        Object.values(S.tables).forEach(function (z) {
          if (z.type !== 'zone') return;
          if (!z.meta) z.meta = { color: '#EAF5EE', members: [] };
          var members  = (z.meta.members || []).map(String);
          var wasIn    = members.indexOf(sid) !== -1;
          // bbox excludes the dragged table if it was a member, so the natural boundary is used
          var checkBbox = wasIn ? getZoneBBoxRaw(z, sid) : getZoneBBoxRaw(z);
          var isInNow   = checkBbox && (finalX > checkBbox.x1 && finalX < checkBbox.x2 && finalY > checkBbox.y1 && finalY < checkBbox.y2);

          if (!wasIn && isInNow) {
            z.meta.members = members.concat([sid]);
            apiFetch('PATCH', 'furniture/' + z.id, { meta: z.meta });
            showToast('Added to "' + (z.label || 'Zone') + '"', 'ok');
            didAdd = true;
            if (S.pendingZoneId === String(z.id) && S.pendingZoneMembers) {
              S.pendingZoneMembers.add(sid);
              showZoneFloatProps(String(z.id), z);
            }
          } else if (wasIn && !isInNow) {
            hasPendingConfirm = true;
            (function (zRef, membersSnap, preDragX, preDragY) {
              fpModal({
                type:   'confirm',
                title:  'Remove from zone?',
                body:   '<p>"' + escHtml(d.label || 'Table') + '" was dragged outside <strong>' + escHtml(zRef.label || 'Zone') + '</strong>. Remove it from this zone?</p>',
                ok:     'Remove from zone',
                cancel: 'Keep in zone',
              }).then(function (confirmed) {
                if (confirmed) {
                  zRef.meta.members = membersSnap.filter(function (m) { return m !== sid; });
                  apiFetch('PATCH', 'furniture/' + zRef.id, { meta: zRef.meta });
                  if (S.pendingZoneId === String(zRef.id) && S.pendingZoneMembers) {
                    S.pendingZoneMembers.delete(sid);
                    showZoneFloatProps(String(zRef.id), zRef);
                  }
                } else {
                  // Revert position back inside the zone
                  if (preDragX !== undefined) {
                    d.pos_x = preDragX;
                    d.pos_y = preDragY;
                    var tNode = stage.findOne('#tbl-' + item.id);
                    if (tNode) tNode.position({ x: d.pos_x, y: d.pos_y });
                    tableLayer.batchDraw();
                  }
                }
                renderZoneLayer();
              });
            })(z, members, d._preDragX, d._preDragY);
          }
        });

        // Render immediately if an add happened or no confirm is pending;
        // the modal handler also calls renderZoneLayer after confirm resolves.
        if (!hasPendingConfirm || didAdd) renderZoneLayer();
      } else {
        renderZoneLayer();
      }
    });

    group.on('transformend', function () {
      var d = S.tables[item.id];
      if (!d) return;
      d.pos_x        = Math.round(this.x());
      d.pos_y        = Math.round(this.y());
      d.rotation_deg = Math.round(this.rotation());
      markDirty();
      renderZoneLayer();
    });

    tableLayer.add(group);
    tableLayer.moveToTop(transformer);

    if (autoSelect) selectTable(item.id);
    return group;
  }

  /* ── Inline label editing ────────────────────────────────────────── */
  function startInlineLabelEdit(id, group) {
    var item = S.tables[id];
    if (!item) return;

    var absPos   = group.getAbsolutePosition();
    var stageBox = stage.container().getBoundingClientRect();
    var scale    = stage.scaleX();

    var input = document.createElement('input');
    input.value = item.label || '';
    input.style.cssText = [
      'position:fixed',
      'top:'    + (stageBox.top  + absPos.y * scale - 12) + 'px',
      'left:'   + (stageBox.left + absPos.x * scale - 30) + 'px',
      'width:80px',
      'font-size:12px',
      'font-family:system-ui,sans-serif',
      'font-weight:700',
      'text-align:center',
      'padding:2px 6px',
      'border:2px solid #3B82F6',
      'border-radius:4px',
      'background:#fff',
      'color:#0F1523',
      'z-index:99999',
      'outline:none',
    ].join(';');
    document.body.appendChild(input);
    input.focus();
    input.select();

    function commit() {
      var val = input.value.trim() || item.label;
      input.remove();
      if (val === item.label) return;
      item.label = val;
      var node = stage.findOne('#tbl-' + id);
      if (node) { node.destroy(); }
      addTableNode(item, false);
      if (S.selected === id) selectTable(id);
      tableLayer.batchDraw();
      markDirty();
      var title = document.getElementById('fp-fp-title');
      if (title) title.textContent = val;
    }
    input.addEventListener('blur',    commit);
    input.addEventListener('keydown', function (e) {
      if (e.key === 'Enter')  { e.preventDefault(); commit(); }
      if (e.key === 'Escape') { input.removeEventListener('blur', commit); input.remove(); }
    });
  }

  /* ── Round table ────────────────────────────────────────────────── */
  function addChair(group, cx, cy, cW, cH, angleDeg, fill) {
    var rBack = cW / 2;
    group.add(new Konva.Rect({
      x: cx, y: cy,
      width: cW, height: cH,
      offsetX: cW / 2, offsetY: cH / 2,
      cornerRadius: [rBack, rBack, 3, 3],
      fill: fill,
      rotation: angleDeg,
      listening: false,
    }));
  }

  function drawRoundTable(group, item) {
    var st    = liveStyle(item);
    var r     = Math.max(24, Math.min(52, toNum(item.width, 70) / 2));
    var cap   = Math.max(1, parseInt(item.capacity_max) || 4);
    var cW    = Math.max(10, r * 0.34);
    var cH    = Math.max(13, r * 0.48);
    var gap   = 4;
    var cDist = r + gap + cH / 2;

    for (var i = 0; i < cap; i++) {
      var a    = (i / cap) * Math.PI * 2 - Math.PI / 2;
      var cx   = Math.cos(a) * cDist;
      var cy   = Math.sin(a) * cDist;
      var deg  = a * 180 / Math.PI + 90;
      addChair(group, cx, cy, cW, cH, deg, st.chair);
    }

    group.add(new Konva.Circle({
      radius: r,
      fill: st.fill, stroke: st.stroke, strokeWidth: 1.5,
      shadowColor: 'rgba(0,0,0,0.15)', shadowBlur: 6, shadowOffsetY: 2,
    }));

    group.add(new Konva.Text({
      text: item.label || '?',
      fontSize: Math.max(11, r * 0.42),
      fontFamily: 'system-ui,-apple-system,sans-serif',
      fontStyle: 'bold',
      fill: '#FFFFFF',
      align: 'center',
      verticalAlign: 'middle',
      width: r * 2, height: r * 2,
      offsetX: r, offsetY: r,
      listening: false,
    }));
  }

  /* ── Rect table ─────────────────────────────────────────────────── */
  function drawRectTable(group, item) {
    var st   = liveStyle(item);
    var W    = toNum(item.width,  90);
    var H    = toNum(item.height, 56);
    var cap  = Math.max(1, parseInt(item.capacity_max) || 4);
    var top  = Math.ceil(cap / 2);
    var bot  = Math.floor(cap / 2);
    var cW   = 12, cH = 14, gap = 4;

    for (var i = 0; i < top; i++) {
      var sp = W / (top + 1);
      var cx = -W / 2 + sp * (i + 1);
      var cy = -H / 2 - gap - cH / 2;
      addChair(group, cx, cy, cW, cH, 0, st.chair);
    }
    for (var j = 0; j < bot; j++) {
      var sp2 = W / (bot + 1);
      var cx2 = -W / 2 + sp2 * (j + 1);
      var cy2 = H / 2 + gap + cH / 2;
      addChair(group, cx2, cy2, cW, cH, 180, st.chair);
    }

    group.add(new Konva.Rect({
      x: -W / 2, y: -H / 2,
      width: W, height: H,
      cornerRadius: 6,
      fill: st.fill, stroke: st.stroke, strokeWidth: 1.5,
      shadowColor: 'rgba(0,0,0,0.12)', shadowBlur: 5, shadowOffsetY: 2,
    }));

    group.add(new Konva.Text({
      text: item.label || '?',
      fontSize: 13,
      fontFamily: 'system-ui,-apple-system,sans-serif',
      fontStyle: 'bold',
      fill: '#FFFFFF',
      align: 'center',
      verticalAlign: 'middle',
      width: W, height: H,
      offsetX: W / 2, offsetY: H / 2,
      listening: false,
    }));
  }

  /* ── Booth ──────────────────────────────────────────────────────── */
  function drawBoothTable(group, item) {
    var st  = liveStyle(item);
    var W   = toNum(item.width, 100);
    var H   = toNum(item.height, 60);
    var bH  = 14;

    group.add(new Konva.Rect({ x: -W / 2, y: -H / 2 - bH, width: W, height: bH, cornerRadius: [6, 6, 0, 0], fill: st.chair, listening: false }));
    group.add(new Konva.Rect({ x: -W / 2, y: H / 2, width: W, height: bH, cornerRadius: [0, 0, 6, 6], fill: st.chair, listening: false }));

    group.add(new Konva.Rect({
      x: -W / 2, y: -H / 2,
      width: W, height: H,
      cornerRadius: 4,
      fill: st.fill, stroke: st.stroke, strokeWidth: 1.5,
      listening: false,
    }));

    group.add(new Konva.Text({
      text: item.label || '?',
      fontSize: 13,
      fontFamily: 'system-ui,-apple-system,sans-serif',
      fontStyle: 'bold',
      fill: '#FFFFFF',
      align: 'center',
      verticalAlign: 'middle',
      width: W, height: H,
      offsetX: W / 2, offsetY: H / 2,
      listening: false,
    }));
  }

  /* ── Lounge / sofa ──────────────────────────────────────────────── */
  function drawLounge(group, item) {
    var st = liveStyle(item);
    var W  = toNum(item.width, 130);
    var H  = toNum(item.height, 64);

    // Backrest at top
    group.add(new Konva.Rect({
      x: -W / 2, y: -H / 2 - 14,
      width: W, height: 14,
      cornerRadius: [6, 6, 0, 0],
      fill: st.chair,
      listening: false,
    }));

    // Main body
    group.add(new Konva.Rect({
      x: -W / 2, y: -H / 2,
      width: W, height: H,
      cornerRadius: 6,
      fill: st.fill, stroke: st.stroke, strokeWidth: 1.5,
      shadowColor: 'rgba(0,0,0,0.12)', shadowBlur: 5, shadowOffsetY: 2,
      listening: false,
    }));

    group.add(new Konva.Text({
      text: item.label || '?',
      fontSize: 13,
      fontFamily: 'system-ui,-apple-system,sans-serif',
      fontStyle: 'bold',
      fill: '#FFFFFF',
      align: 'center',
      verticalAlign: 'middle',
      width: W, height: H,
      offsetX: W / 2, offsetY: H / 2,
      listening: false,
    }));
  }

  /* ── Bar seat / stool ───────────────────────────────────────────── */
  function drawBarSeat(group, item) {
    var st = liveStyle(item);
    var r  = 15;

    // Stem below circle
    group.add(new Konva.Rect({
      x: -2, y: r,
      width: 4, height: 10,
      cornerRadius: 2,
      fill: st.chair,
      listening: false,
    }));

    // Seat circle
    group.add(new Konva.Circle({
      radius: r,
      fill: st.fill, stroke: st.stroke, strokeWidth: 1.5,
      shadowColor: 'rgba(0,0,0,0.15)', shadowBlur: 4, shadowOffsetY: 1,
    }));

    group.add(new Konva.Text({
      text: item.label || '?',
      fontSize: 10,
      fontFamily: 'system-ui,-apple-system,sans-serif',
      fontStyle: 'bold',
      fill: '#FFFFFF',
      align: 'center',
      verticalAlign: 'middle',
      width: r * 2, height: r * 2,
      offsetX: r, offsetY: r,
      listening: false,
    }));
  }

  /* ── Text label ─────────────────────────────────────────────────── */
  function drawTextLabel(group, item) {
    var meta     = item.meta || {};
    var fontSize = toNum(meta.fontSize, 15);
    var color    = meta.color || '#1a1a1a';
    var text     = item.label || 'Label';
    var bgColor  = meta.bgColor || '';

    if (bgColor) {
      group.add(new Konva.Rect({
        x: -4, y: -fontSize / 2 - 4,
        width: text.length * fontSize * 0.62 + 8,
        height: fontSize + 8,
        cornerRadius: 4,
        fill: bgColor,
        opacity: 0.85,
        listening: false,
      }));
    }

    group.add(new Konva.Text({
      text: text,
      fontSize: fontSize,
      fontFamily: 'system-ui,-apple-system,sans-serif',
      fontStyle: meta.bold ? 'bold' : 'normal',
      fill: color,
      listening: false,
      shadowColor: 'rgba(0,0,0,0.3)',
      shadowBlur: 3,
      shadowOffsetY: 1,
    }));
  }

  /* ── Resolve live vs builder style ─────────────────────────────── */
  function liveStyle(item) {
    if (S.mode !== 'live') return { fill: TABLE_FILL, stroke: TABLE_STROKE, chair: CHAIR_FILL };
    if (item.is_available === '0' || item.is_available === 0) return STATUS.blocked;
    var key = S.liveStatus[item.id] || 'free';
    return STATUS[key] || STATUS.free;
  }

  /* ══════════════════════════════════════════════════════════════════
     ZONE LAYER
  ══════════════════════════════════════════════════════════════════ */
  function renderZoneLayer() {
    zoneLayer.destroyChildren();

    Object.values(S.tables).forEach(function (item) {
      if (item.type !== 'zone') return;
      var meta    = item.meta || {};
      var members = meta.members || [];
      var color   = meta.color || '#EAF5EE';

      if (!members.length) return;

      var bbox = getZoneBBoxRaw(item);
      if (!bbox) return;

      var zx = bbox.x1;
      var zy = bbox.y1;
      var zw = bbox.x2 - bbox.x1;
      var zh = bbox.y2 - bbox.y1;

      var rot = typeof (meta.rotation) === 'number' ? meta.rotation : (parseFloat(meta.rotation) || 0);
      var cx  = zx + zw / 2;
      var cy  = zy + zh / 2;

      var isSelected = String(item.id) === String(S.selected);
      var rect = new Konva.Rect({
        x: -zw / 2, y: -zh / 2,
        width: zw, height: zh,
        cornerRadius: 10,
        fill:        hexToRgba(color, isSelected ? 0.18 : 0.14),
        stroke:      isSelected ? '#2E7D52' : 'rgba(60,80,60,0.45)',
        strokeWidth: isSelected ? 2 : 1.5,
        dash:        isSelected ? [] : [5, 4],
        name: 'zone-rect',
        hitStrokeWidth: 12,
      });

      var label = new Konva.Text({
        x: -zw / 2 + 9, y: -zh / 2 + 6,
        text: item.label || 'Zone',
        fontSize: 10,
        fontFamily: 'system-ui,-apple-system,sans-serif',
        fontStyle: '600',
        fill: 'rgba(50,75,50,0.70)',
        listening: false,
      });

      // Wrap rect + label in a sub-group so rotation pivots around the zone center
      var visGroup = new Konva.Group({ x: cx, y: cy, rotation: rot });
      visGroup.add(rect);
      visGroup.add(label);

      var zGroup = new Konva.Group({
        id: 'zone-' + item.id,
        name: 'zone-group',
        draggable: S.mode === 'builder',
      });
      zGroup.add(visGroup);

      zGroup.on('click tap', function (e) {
        e.cancelBubble = true;
        if (S.mode === 'builder') selectZone(item.id);
      });

      // Double-click to rename zone
      zGroup.on('dblclick dbltap', function (e) {
        e.cancelBubble = true;
        if (S.mode !== 'builder') return;
        fpModal({
          type:  'prompt',
          title: 'Rename Zone',
          body:  '<input class="thr-modal-input" type="text" value="' + escAttr(item.label || '') + '" maxlength="40">',
          ok:    'Rename',
        }).then(function (name) {
          if (!name || name === item.label) return;
          item.label = name;
          apiFetch('PATCH', 'furniture/' + item.id, { label: name }).then(function () {
            renderZoneLayer();
            var titleEl = document.getElementById('fp-fp-title');
            if (titleEl && S.selected === item.id) titleEl.textContent = name;
            showToast('Zone renamed to "' + name + '"', 'info');
            markDirty();
          });
        });
      });

      // Drag zone: move all member tables together
      zGroup.on('dragstart', function () {
        if (S.mode !== 'builder') return;
        members.forEach(function (mid) {
          var t = S.tables[mid];
          if (t) { t._zdx = t.pos_x; t._zdy = t.pos_y; }
        });
      });

      zGroup.on('dragmove', function () {
        if (S.mode !== 'builder') return;
        var dx = this.x();
        var dy = this.y();
        members.forEach(function (mid) {
          var tNode = stage.findOne('#tbl-' + mid);
          var t = S.tables[mid];
          if (tNode && t && t._zdx !== undefined) {
            tNode.position({ x: t._zdx + dx, y: t._zdy + dy });
          }
        });
        tableLayer.batchDraw();
      });

      zGroup.on('dragend', function () {
        if (S.mode !== 'builder') return;
        var dx = this.x();
        var dy = this.y();
        this.position({ x: 0, y: 0 }); // zone has no stored pos; reset after dragging
        members.forEach(function (mid) {
          var t = S.tables[mid];
          var tNode = stage.findOne('#tbl-' + mid);
          if (!t || !tNode || t._zdx === undefined) return;
          t.pos_x = snapToGrid(t._zdx + dx);
          t.pos_y = snapToGrid(t._zdy + dy);
          tNode.position({ x: t.pos_x, y: t.pos_y });
          delete t._zdx;
          delete t._zdy;
          apiFetch('PATCH', 'furniture/' + mid, { pos_x: t.pos_x, pos_y: t.pos_y });
        });
        tableLayer.batchDraw();
        renderZoneLayer();
        markDirty();
      });

      zoneLayer.add(zGroup);
    });

    zoneLayer.batchDraw();
  }

  function resetZoneHighlights() {
    Object.values(S.tables).forEach(function (z) {
      if (z.type !== 'zone') return;
      var zGroup = zoneLayer.findOne('#zone-' + z.id);
      if (!zGroup) return;
      var zRect = zGroup.findOne('Rect');
      if (!zRect) return;
      zRect.fill(hexToRgba((z.meta && z.meta.color) ? z.meta.color : '#EAF5EE', 0.14));
      zRect.stroke('rgba(60,80,60,0.55)');
      zRect.strokeWidth(1.5);
      zRect.dash([5, 4]);
    });
    zoneLayer.batchDraw();
  }

  function createZone() {
    if (S.selectedIds.size < 2 || S.mode !== 'builder') return;
    fpModal({
      type:  'prompt',
      title: 'Create Zone',
      body:  '<p>Name this zone to group the selected tables into a labelled area.</p>' +
             '<input class="thr-modal-input" type="text" placeholder=\'e.g. VIP, Outdoor, Bar Area\' maxlength="40">',
      ok:    'Create Zone',
    }).then(function (name) {
      if (!name) return;
      var payload = {
        type:         'zone',
        label:        name,
        pos_x:        0,
        pos_y:        0,
        width:        0,
        height:       0,
        rotation_deg: 0,
        capacity_min: 0,
        capacity_max: 0,
        meta:         { color: '#EAF5EE', members: Array.from(S.selectedIds) },
      };
      apiFetch('POST', 'floor-plans/' + S.floorId + '/furniture', payload).then(function (item) {
        S.tables[item.id] = item;
        renderZoneLayer();
        deselect();
        showToast('Zone "' + item.label + '" created', 'info');
      }).catch(function (err) {
        console.warn('Create zone failed', err);
      });
    });
  }

  function deleteZone(id) {
    var endpoint = S.activeLayout ? 'slots/' + id : 'furniture/' + id;
    apiFetch('DELETE', endpoint).then(function () {
      delete S.tables[id];
      renderZoneLayer();
      deselect();
    }).catch(function (err) { console.warn('Delete zone failed', err); });
  }

  /* ── Group / Join helpers ────────────────────────────────────────── */
  function canItemJoin(item) {
    var typeDef = TYPES[item.type] || {};
    if (!typeDef.joinable) return false;
    // Check per-item override: in layout mode stored in meta.joinable_override, in base mode in is_combinable
    var meta = item.meta || {};
    if (typeof meta.joinable_override === 'boolean') return meta.joinable_override;
    if (item.is_combinable === 0 || item.is_combinable === '0' || item.is_combinable === false) return false;
    return true;
  }

  function getGroupLabel(groupId) {
    var members = Object.values(S.tables).filter(function (t) { return t.group_id === groupId; });
    if (!members.length) return 'Group';
    members.sort(function (a, b) { return a.id - b.id; });
    return members.map(function (m) { return m.label || ('T' + m.id); }).join(' + ');
  }

  function joinSelected() {
    if (S.selectedIds.size < 2) return;
    var ids = Array.from(S.selectedIds).map(Number).sort(function (a, b) { return a - b; });
    var groupId = ids[0]; // use lowest ID as group identifier

    // PATCH all selected to share group_id = groupId
    var promises = ids.map(function (id) {
      return apiFetch('PATCH', 'furniture/' + id, { group_id: groupId }).then(function () {
        if (S.tables[id]) S.tables[id].group_id = groupId;
      });
    });
    Promise.all(promises).then(function () {
      ids.forEach(function (id) {
        var node = stage.findOne('#tbl-' + id);
        if (node) { node.destroy(); }
        if (S.tables[id]) addTableNode(S.tables[id]);
      });
      tableLayer.batchDraw();
      var totalCap = ids.reduce(function (s, id) { return s + (parseInt((S.tables[id] || {}).capacity_max) || 0); }, 0);
      showToast('Joined ' + ids.length + ' tables \xB7 ' + totalCap + ' seats combined', 'ok');
      markDirty();
      showMultiSelectFloat(S.selectedIds.size); // refresh panel
    }).catch(function (err) { console.warn('joinSelected failed', err); });
  }

  function separateSelected() {
    // Separate all tables in the same group as any of the selected items
    var groupId = null;
    S.selectedIds.forEach(function (sid) {
      var it = S.tables[sid];
      if (it && it.group_id) groupId = it.group_id;
    });
    var toSep = Object.values(S.tables).filter(function (t) { return t.group_id === groupId; }).map(function (t) { return t.id; });
    if (!toSep.length) return;

    var promises = toSep.map(function (id) {
      return apiFetch('PATCH', 'furniture/' + id, { group_id: null }).then(function () {
        if (S.tables[id]) S.tables[id].group_id = null;
      });
    });
    Promise.all(promises).then(function () {
      toSep.forEach(function (id) {
        var node = stage.findOne('#tbl-' + id);
        if (node) { node.destroy(); }
        if (S.tables[id]) addTableNode(S.tables[id]);
      });
      tableLayer.batchDraw();
      showToast('Tables separated', 'info');
      markDirty();
      if (S.selectedIds.size > 1) showMultiSelectFloat(S.selectedIds.size);
      else if (S.selected) showFloatPanel(S.selected);
    }).catch(function (err) { console.warn('separateSelected failed', err); });
  }

  function leaveGroup(id) {
    var item = S.tables[id];
    if (!item || !item.group_id) return;
    apiFetch('PATCH', 'furniture/' + id, { group_id: null }).then(function () {
      item.group_id = null;
      var node = stage.findOne('#tbl-' + id);
      if (node) { node.destroy(); }
      addTableNode(item);
      tableLayer.batchDraw();
      showToast('Left group', 'info');
      markDirty();
      if (S.selected === String(id)) showFloatPanel(id);
    }).catch(function (err) { console.warn('leaveGroup failed', err); });
  }

  function selectZone(id) {
    S.selected = id;
    S.selectedIds = new Set();
    var zone = S.tables[id];
    S.pendingZoneId      = id;
    S.pendingZoneMembers = new Set(((zone && zone.meta && zone.meta.members) || []).map(String));
    showFloatPanel(id);
    transformer.nodes([]);
    tableLayer.batchDraw();
    renderZoneLayer();
    updateZoneMemberVisuals();
    updateZoneButton();
  }

  /* ══════════════════════════════════════════════════════════════════
     SELECTION
  ══════════════════════════════════════════════════════════════════ */
  function selectTable(id) {
    S.selected = id;
    S.selectedIds = new Set();
    var node = stage.findOne('#tbl-' + id);
    if (node && S.mode === 'builder') {
      transformer.nodes([node]);
      tableLayer.batchDraw();
    }
    showFloatPanel(id);
    var delBtn = document.getElementById('fp-delete-sel');
    if (delBtn) delBtn.disabled = false;
    // Position float panel after DOM update
    if (node) {
      setTimeout(function () { positionFloatPanel(node); }, 10);
    }
    updateZoneButton();
  }

  function deselect() {
    var wasZone = S.selected && S.tables[S.selected] && S.tables[S.selected].type === 'zone';
    S.selected           = null;
    S.selectedIds        = new Set();
    S.pendingZoneId      = null;
    S.pendingZoneMembers = null;
    transformer.nodes([]);
    tableLayer.batchDraw();
    if (wasZone) renderZoneLayer();
    updateZoneMemberVisuals();
    hideFloatPanel();
    var delBtn = document.getElementById('fp-delete-sel');
    if (delBtn) delBtn.disabled = true;
    updateZoneButton();
  }

  /* ── Multi-select ────────────────────────────────────────────────── */
  function toggleMultiSelect(id) {
    S.selected = null;
    if (S.selectedIds.has(id)) {
      S.selectedIds.delete(id);
    } else {
      S.selectedIds.add(id);
    }

    if (S.selectedIds.size === 0) {
      deselect();
      return;
    }

    var nodes = Array.from(S.selectedIds).map(function (sid) {
      return stage.findOne('#tbl-' + sid);
    }).filter(Boolean);

    if (S.mode === 'builder') { transformer.nodes(nodes); tableLayer.batchDraw(); }
    updateZoneButton();

    if (S.selectedIds.size === 1) {
      selectTable(Array.from(S.selectedIds)[0]);
    } else {
      showMultiSelectFloat(S.selectedIds.size);
    }
  }

  function showMultiSelectFloat(count) {
    var panel = document.getElementById('fp-float-panel');
    if (!panel) return;
    panel.hidden = false;
    var title = document.getElementById('fp-fp-title');
    if (title) title.textContent = count + ' items selected';
    var body = document.getElementById('fp-fp-body');
    // Determine if selected items are all joinable
    var selArr = Array.from(S.selectedIds);
    var allJoinable = selArr.every(function (sid) {
      var it = S.tables[sid];
      return it && canItemJoin(it);
    });
    // Check if all selected are in the same group (and that group is non-null)
    var groupIds = selArr.map(function (sid) { return (S.tables[sid] || {}).group_id; }).filter(Boolean);
    var allSameGroup = groupIds.length === selArr.length && groupIds.every(function (g) { return g === groupIds[0]; });
    var totalCap = selArr.reduce(function (s, sid) { return s + (parseInt((S.tables[sid] || {}).capacity_max) || 0); }, 0);

    if (body) body.innerHTML =
      '<p style="font-size:12px;color:#6B7280;margin:6px 0 8px;">Shift+click to add/remove \xB7 Ctrl+D to duplicate</p>' +
      (totalCap > 0 ? '<p style="font-size:12px;color:#374151;margin:0 0 8px;">Combined capacity: <strong>' + totalCap + '</strong> seats</p>' : '') +
      (allJoinable && !allSameGroup ?
        '<button class="fp-btn fp-btn-sm fp-btn-outline" id="fp-join-sel" type="button" style="width:100%;margin-bottom:4px;">Join as combined seating</button>' : '') +
      (allSameGroup ?
        '<button class="fp-btn fp-btn-sm fp-btn-outline" id="fp-separate-sel" type="button" style="width:100%;margin-bottom:4px;">Separate all from group</button>' : '');

    setTimeout(function () {
      var joinBtn = document.getElementById('fp-join-sel');
      if (joinBtn) joinBtn.addEventListener('click', function () { joinSelected(); });
      var sepBtn = document.getElementById('fp-separate-sel');
      if (sepBtn) sepBtn.addEventListener('click', function () { separateSelected(); });
    }, 0);

    var delBtn = document.getElementById('fp-fp-delete');
    if (delBtn) delBtn.disabled = true;
    var dupBtn = document.getElementById('fp-tb-dup');
    if (dupBtn) dupBtn.disabled = false;
    // Position panel near the collective bounding box of selected items
    setTimeout(function () {
      var nodes = Array.from(S.selectedIds).map(function (sid) {
        return stage.findOne('#tbl-' + sid);
      }).filter(Boolean);
      if (!nodes.length) return;
      var x1 = Infinity, y1 = Infinity, x2 = -Infinity, y2 = -Infinity;
      nodes.forEach(function (n) {
        var r = n.getClientRect();
        if (r.x < x1) x1 = r.x;
        if (r.y < y1) y1 = r.y;
        if (r.x + r.width  > x2) x2 = r.x + r.width;
        if (r.y + r.height > y2) y2 = r.y + r.height;
      });
      positionFloatPanelByRect({ x: x1, y: y1, width: x2 - x1, height: y2 - y1 });
    }, 10);
  }

  function updateZoneButton() {
    var zoneBtn = document.getElementById('fp-tb-zone-btn');
    if (!zoneBtn) return;
    var zoneSelected = !!(S.selected && S.tables[S.selected] && S.tables[S.selected].type === 'zone');
    zoneBtn.disabled = S.selectedIds.size < 2 || zoneSelected;
  }

  /* ══════════════════════════════════════════════════════════════════
     TOOLBAR
  ══════════════════════════════════════════════════════════════════ */
  function buildToolbar() {
    var bar = document.getElementById('fp-toolbar');
    if (!bar) return;
    bar.innerHTML = '';

    TOOLBAR.forEach(function (def) {
      if (def === null) {
        var sep = document.createElement('div');
        sep.className = 'fp-tb-sep';
        bar.appendChild(sep);
        return;
      }

      var btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'fp-tb-item';
      btn.dataset.typeKey = def.key;
      if (def.key === '__zone') btn.id = 'fp-tb-zone-btn';
      btn.innerHTML =
        '<span class="fp-tb-icon">' + toolbarIcon(def.key) + '</span>' +
        '<span class="fp-tb-lbl">' + escHtml(def.lbl) + '</span>';

      if (def.action === 'zone') {
        btn.disabled = true;
        btn.addEventListener('click', function () { createZone(); });
      } else {
        btn.addEventListener('click', function () { startPlacing(def.key, btn); });
      }

      bar.appendChild(btn);
    });

    // Right side controls
    var right = document.createElement('div');
    right.className = 'fp-tb-right';
    right.innerHTML =
      '<button type="button" id="fp-tb-snap" class="fp-tb-ctrl fp-tb-ctrl--active" title="Snap to grid">' +
        '<svg width="16" height="16" viewBox="0 0 16 16" fill="none"><rect x="1" y="1" width="5" height="5" rx="1" stroke="currentColor" stroke-width="1.3"/><rect x="10" y="1" width="5" height="5" rx="1" stroke="currentColor" stroke-width="1.3"/><rect x="1" y="10" width="5" height="5" rx="1" stroke="currentColor" stroke-width="1.3"/><rect x="10" y="10" width="5" height="5" rx="1" stroke="currentColor" stroke-width="1.3"/></svg>' +
        '<span>Snap</span>' +
      '</button>' +
      '<button type="button" id="fp-tb-dup" class="fp-tb-ctrl" title="Duplicate (Ctrl+D)" disabled>' +
        '<svg width="16" height="16" viewBox="0 0 16 16" fill="none"><rect x="1" y="5" width="10" height="10" rx="1.5" stroke="currentColor" stroke-width="1.3"/><path d="M5 5V3a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v7a1 1 0 0 0-1 1h-2" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>' +
        '<span>Dupe</span>' +
      '</button>' +
      '<span id="fp-tb-status" class="fp-tb-status"></span>';
    bar.appendChild(right);

    updateStatusBar();
  }

  function toolbarIcon(key) {
    switch (key) {
      case 'table-round':
        return '<svg width="28" height="28" viewBox="0 0 28 28"><circle cx="14" cy="14" r="6" fill="currentColor" opacity=".85"/><circle cx="14" cy="4" r="2.5" fill="currentColor" opacity=".4"/><circle cx="14" cy="24" r="2.5" fill="currentColor" opacity=".4"/><circle cx="4" cy="14" r="2.5" fill="currentColor" opacity=".4"/><circle cx="24" cy="14" r="2.5" fill="currentColor" opacity=".4"/></svg>';
      case 'table-square':
        return '<svg width="28" height="28" viewBox="0 0 28 28"><rect x="8" y="8" width="12" height="12" rx="1.5" fill="currentColor" opacity=".85"/><rect x="4" y="3.5" width="6" height="4" rx="1.5" fill="currentColor" opacity=".4"/><rect x="18" y="3.5" width="6" height="4" rx="1.5" fill="currentColor" opacity=".4"/><rect x="4" y="20.5" width="6" height="4" rx="1.5" fill="currentColor" opacity=".4"/><rect x="18" y="20.5" width="6" height="4" rx="1.5" fill="currentColor" opacity=".4"/></svg>';
      case 'table-rect':
        return '<svg width="28" height="28" viewBox="0 0 28 28"><rect x="4" y="9" width="20" height="10" rx="1.5" fill="currentColor" opacity=".85"/><rect x="5" y="4" width="6" height="4" rx="1.5" fill="currentColor" opacity=".4"/><rect x="12" y="4" width="4" height="4" rx="1.5" fill="currentColor" opacity=".4"/><rect x="17" y="4" width="6" height="4" rx="1.5" fill="currentColor" opacity=".4"/><rect x="5" y="20" width="6" height="4" rx="1.5" fill="currentColor" opacity=".4"/><rect x="12" y="20" width="4" height="4" rx="1.5" fill="currentColor" opacity=".4"/><rect x="17" y="20" width="6" height="4" rx="1.5" fill="currentColor" opacity=".4"/></svg>';
      case 'lounge':
        return '<svg width="28" height="28" viewBox="0 0 28 28"><rect x="3" y="11" width="22" height="10" rx="3" fill="currentColor" opacity=".85"/><rect x="2" y="8" width="24" height="6" rx="3" fill="currentColor" opacity=".45"/></svg>';
      case 'bar-seat':
        return '<svg width="28" height="28" viewBox="0 0 28 28"><circle cx="14" cy="17" r="6" fill="currentColor" opacity=".85"/><rect x="13" y="4" width="2" height="9" rx="1" fill="currentColor" opacity=".5"/></svg>';
      case 'bar-table':
        return '<svg width="28" height="28" viewBox="0 0 28 28"><rect x="9" y="3" width="10" height="22" rx="2" fill="currentColor" opacity=".85"/><rect x="3" y="5" width="4" height="4" rx="1.5" fill="currentColor" opacity=".4"/><rect x="21" y="5" width="4" height="4" rx="1.5" fill="currentColor" opacity=".4"/><rect x="3" y="12" width="4" height="4" rx="1.5" fill="currentColor" opacity=".4"/><rect x="21" y="12" width="4" height="4" rx="1.5" fill="currentColor" opacity=".4"/></svg>';
      case 'text_label':
        return '<svg width="28" height="28" viewBox="0 0 28 28"><text x="4" y="20" font-size="16" font-weight="700" font-family="system-ui" fill="currentColor" opacity=".85">Aa</text></svg>';
      case '__zone':
        return '<svg width="28" height="28" viewBox="0 0 28 28"><rect x="3" y="3" width="22" height="22" rx="3" stroke="currentColor" stroke-width="1.8" stroke-dasharray="4 2.5" fill="none" opacity=".7"/><path d="M9 10h10M9 14h7M9 18h5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" opacity=".5"/></svg>';
      default:
        return '<svg width="28" height="28" viewBox="0 0 28 28"><rect x="6" y="6" width="16" height="16" rx="2" fill="currentColor" opacity=".7"/></svg>';
    }
  }

  /* ══════════════════════════════════════════════════════════════════
     PLACING
  ══════════════════════════════════════════════════════════════════ */
  function startPlacing(typeKey, el) {
    if (S.mode !== 'builder') return;
    S.placing = typeKey;
    document.querySelectorAll('.fp-tb-item').forEach(function (d) {
      d.classList.toggle('fp-tb-item--active', d === el);
    });
    var hint = document.getElementById('fp-place-hint');
    if (hint) hint.hidden = false;
    var cnv = stage.container();
    if (cnv) cnv.style.cursor = 'crosshair';
  }

  function cancelPlacing() {
    S.placing = null;
    document.querySelectorAll('.fp-tb-item').forEach(function (d) {
      d.classList.remove('fp-tb-item--active');
    });
    var hint = document.getElementById('fp-place-hint');
    if (hint) hint.hidden = true;
    var cnv = stage.container();
    if (cnv) cnv.style.cursor = '';
  }

  function placeTable(pos) {
    if (!S.placing || !S.floorId) return;
    var typeKey = S.placing;
    var tbDef   = TOOLBAR.find(function (d) { return d && d.key === typeKey; });
    var W = tbDef ? tbDef.w : 90;
    var H = tbDef ? tbDef.h : 56;
    var cap = tbDef ? tbDef.cap : 4;

    var scale = stage.scaleX();
    var stPos  = stage.position();
    var sx = (pos.x - stPos.x) / scale;
    var sy = (pos.y - stPos.y) / scale;
    var px = snapToGrid(sx);
    var py = snapToGrid(sy);

    if (typeKey !== 'text_label' && isOverlappingAny(px, py, W, H)) {
      showToast('Cannot place here — overlaps another table', 'warn');
      return;
    }

    cancelPlacing();

    var defaultLabel = typeKey === 'text_label'
      ? 'Label'
      : 'T' + (Object.values(S.tables).filter(function (t) { return t.type !== 'text_label' && t.type !== 'zone'; }).length + 1);

    var typeDef = TYPES[typeKey] || {};
    var payload = {
      type:         typeKey,
      label:        defaultLabel,
      pos_x:        px,
      pos_y:        py,
      width:        W,
      height:       H,
      rotation_deg: 0,
      capacity_min: typeDef.capacity ? typeDef.capacity[0] : 0,
      capacity_max: cap,
      shape:        typeDef.shape || (isRoundKey(typeKey) ? 'circle' : 'rect'),
    };

    apiFetch('POST', 'floor-plans/' + S.floorId + '/furniture', payload).then(function (item) {
      S.tables[item.id] = item;
      addTableNode(item, false);
      tableLayer.batchDraw();
      updateStatusBar();
      markDirty();
      showEmptyState(false);
      if (S.floorStats[S.floorId]) {
        S.floorStats[S.floorId].tables = Object.values(S.tables).filter(function (t) { return !isZone(t.type) && t.type !== 'text_label'; }).length;
        S.floorStats[S.floorId].seats  = Object.values(S.tables).reduce(function (s, t) { return s + (parseInt(t.capacity_max) || 0); }, 0);
        updateFloorPopup(S.floorId);
      }
      // Open float panel immediately after placing
      selectTable(item.id);
      showFloatPanel(item.id);

      if (typeKey === 'text_label') {
        var node = stage.findOne('#tbl-' + item.id);
        if (node) setTimeout(function () { startInlineLabelEdit(item.id, node); }, 100);
      }
    }).catch(function (err) {
      console.warn('Place failed', err);
    });
  }

  /* ══════════════════════════════════════════════════════════════════
     FLOAT PANEL
  ══════════════════════════════════════════════════════════════════ */
  function showFloatPanel(id) {
    var item = S.tables[id];
    if (!item) return;
    var panel = document.getElementById('fp-float-panel');
    if (!panel) return;
    panel.hidden = false;

    var title = document.getElementById('fp-fp-title');
    if (title) title.textContent = item.label || (item.type === 'text_label' ? 'Label' : item.type === 'zone' ? 'Zone' : 'Table');

    if (item.type === 'zone') {
      showZoneFloatProps(id, item);
    } else if (item.type === 'text_label') {
      showLabelFloatProps(id, item);
    } else {
      showTableFloatProps(id, item);
    }

    // Enable delete button
    var delBtn = document.getElementById('fp-fp-delete');
    if (delBtn) delBtn.disabled = false;

    // Enable dup button (non-zone)
    var dupBtn = document.getElementById('fp-tb-dup');
    if (dupBtn) dupBtn.disabled = false;

    // Position on desktop
    if (item.type !== 'zone') {
      var node = stage.findOne('#tbl-' + id);
      if (node) positionFloatPanel(node);
    } else {
      // For zones, try to position near the zone group
      var zNode = zoneLayer.findOne('#zone-' + id);
      if (zNode) {
        var zRect = zNode.getClientRect();
        positionFloatPanelByRect(zRect);
      }
    }
  }

  function hideFloatPanel() {
    var panel = document.getElementById('fp-float-panel');
    if (panel) panel.hidden = true;
    var delBtn = document.getElementById('fp-fp-delete');
    if (delBtn) delBtn.disabled = true;
    var dupBtn = document.getElementById('fp-tb-dup');
    if (dupBtn) dupBtn.disabled = true;
  }

  function positionFloatPanel(konvaNode) {
    if (!konvaNode) return;
    var rect = konvaNode.getClientRect();
    positionFloatPanelByRect(rect);
  }

  function positionFloatPanelByRect(rect) {
    if (window.innerWidth <= 768) return; // mobile: bottom sheet handled by CSS

    var panel = document.getElementById('fp-float-panel');
    var wrap  = document.getElementById('fp-canvas-wrap');
    if (!panel || !wrap) return;

    // rect coords are canvas-relative (Konva getClientRect is relative to the stage canvas)
    var wrapW  = wrap.offsetWidth;
    var wrapH  = wrap.offsetHeight;
    var panelW = panel.offsetWidth  || 240;
    var panelH = panel.offsetHeight || 340;
    var GAP    = 24;
    var MARGIN = 8;

    var ix = rect.x;
    var iy = rect.y;
    var iw = rect.width;
    var ih = rect.height;

    function clamp(v, lo, hi) { return Math.max(lo, Math.min(v, hi)); }

    // Try placements: right → left → below → above → clamped fallback
    var placements = [
      function () { // right, vertically centred on item
        var l = ix + iw + GAP;
        if (l + panelW > wrapW - MARGIN) return null;
        return { left: l, top: clamp(iy + ih / 2 - panelH / 2, MARGIN, wrapH - panelH - MARGIN) };
      },
      function () { // left, vertically centred on item
        var l = ix - panelW - GAP;
        if (l < MARGIN) return null;
        return { left: l, top: clamp(iy + ih / 2 - panelH / 2, MARGIN, wrapH - panelH - MARGIN) };
      },
      function () { // below, horizontally centred on item
        var t = iy + ih + GAP;
        if (t + panelH > wrapH - MARGIN) return null;
        return { left: clamp(ix + iw / 2 - panelW / 2, MARGIN, wrapW - panelW - MARGIN), top: t };
      },
      function () { // above, horizontally centred on item
        var t = iy - panelH - GAP;
        if (t < MARGIN) return null;
        return { left: clamp(ix + iw / 2 - panelW / 2, MARGIN, wrapW - panelW - MARGIN), top: t };
      },
      function () { // fallback: clamp-right
        return {
          left: clamp(ix + iw + GAP, MARGIN, wrapW - panelW - MARGIN),
          top:  clamp(iy + ih / 2 - panelH / 2, MARGIN, wrapH - panelH - MARGIN),
        };
      },
    ];

    var chosen = null;
    for (var i = 0; i < placements.length; i++) { chosen = placements[i](); if (chosen) break; }

    panel.style.position = 'absolute';
    panel.style.left     = chosen.left + 'px';
    panel.style.top      = chosen.top  + 'px';
  }

  function showTableFloatProps(id, item) {
    var body = document.getElementById('fp-fp-body');
    if (!body) return;
    var statusKey = S.liveStatus[id] || (item.is_available === '0' || item.is_available === 0 ? 'blocked' : 'free');
    var readonly  = S.mode === 'live' ? 'readonly' : '';
    var typeDef   = TYPES[item.type] || {};
    var isBarSeatItem = isBarSeat(item);

    body.innerHTML =
      '<div class="fp-prop-row">' +
        '<label class="fp-prop-label">Label</label>' +
        '<input class="fp-prop-input" id="fp-fp-label" value="' + escAttr(item.label || '') + '" ' + readonly + '>' +
      '</div>' +
      (!isBarSeatItem ?
        '<div class="fp-prop-row">' +
          '<label class="fp-prop-label">Min seats</label>' +
          '<input class="fp-prop-input fp-prop-input--sm" id="fp-fp-cap-min" type="number" min="0" max="20" value="' + (parseInt(item.capacity_min) || 0) + '" ' + readonly + '>' +
        '</div>' +
        '<div class="fp-prop-row">' +
          '<label class="fp-prop-label">Max seats</label>' +
          '<input class="fp-prop-input fp-prop-input--sm" id="fp-fp-cap" type="number" min="1" max="40" value="' + (parseInt(item.capacity_max) || 4) + '" ' + readonly + '>' +
        '</div>' : '') +
      (S.mode === 'builder' ?
        '<div class="fp-prop-row">' +
          '<label class="fp-prop-label">Ref. ID</label>' +
          '<input class="fp-prop-input fp-prop-input--sm" id="fp-fp-key" maxlength="20" value="' + escAttr(item.element_key || '') + '" placeholder="e.g. T1">' +
        '</div>' : '') +
      (S.mode === 'live' ?
        '<div class="fp-prop-row">' +
          '<label class="fp-prop-label">Status</label>' +
          '<span class="fp-status-chip fp-status-chip--' + statusKey + '">' + (STATUS[statusKey] ? STATUS[statusKey].label : statusKey) + '</span>' +
        '</div>' : '') +
      '<div class="fp-prop-row">' +
        '<label class="fp-prop-label" style="font-size:11px;color:#9CA3AF;">' + escHtml(typeDef.label || item.type) + '</label>' +
      '</div>' +
      (S.mode === 'builder' && typeDef.joinable ?
        '<div class="fp-prop-row">' +
          '<label class="fp-prop-label">Joinable</label>' +
          '<label style="display:flex;align-items:center;gap:5px;font-size:12px;color:var(--text-2);">' +
            '<input type="checkbox" id="fp-fp-joinable"' + (canItemJoin(item) ? ' checked' : '') + '> allow joining' +
          '</label>' +
        '</div>' : '') +
      (S.mode === 'builder' && item.group_id ?
        '<div class="fp-prop-row fp-prop-row--group" id="fp-group-row">' +
          '<span class="fp-group-badge">Joined \xB7 ' + escHtml(getGroupLabel(item.group_id)) + '</span>' +
          '<button class="fp-btn fp-btn-xs fp-btn-ghost fp-group-leave-btn" id="fp-group-leave" type="button">Leave group</button>' +
        '</div>' : '');

    if (S.mode === 'builder') {
      var capEl = document.getElementById('fp-fp-cap');
      if (capEl && !isBarSeatItem) {
        capEl.addEventListener('input', function () { applyPropChange(id); });
      }
      var labelEl = document.getElementById('fp-fp-label');
      if (labelEl) {
        labelEl.addEventListener('change', function () { applyPropChange(id); });
      }
      var capMinEl = document.getElementById('fp-fp-cap-min');
      if (capMinEl && !isBarSeatItem) {
        capMinEl.addEventListener('input', function () { applyPropChange(id); });
      }
      var keyEl = document.getElementById('fp-fp-key');
      if (keyEl) {
        keyEl.addEventListener('change', function () { applyPropChange(id); });
      }
      var leaveBtn = document.getElementById('fp-group-leave');
      if (leaveBtn) {
        leaveBtn.addEventListener('click', function () { leaveGroup(id); });
      }
      var joinableEl = document.getElementById('fp-fp-joinable');
      if (joinableEl) {
        joinableEl.addEventListener('change', function () {
          var enabled = joinableEl.checked;
          if (S.activeLayout) {
            // Layout mode: store override in slot meta
            var m = Object.assign({}, item.meta || {});
            m.joinable_override = enabled;
            item.meta = m;
            apiFetch('PATCH', 'slots/' + id, { meta: m }).catch(function (e) { console.warn('joinable patch failed', e); });
          } else {
            // Base mode: patch is_combinable on furniture
            item.is_combinable = enabled ? 1 : 0;
            apiFetch('PATCH', 'furniture/' + id, { is_combinable: item.is_combinable }).catch(function (e) { console.warn('joinable patch failed', e); });
          }
          markDirty();
        });
      }
    }
  }

  function showLabelFloatProps(id, item) {
    var body = document.getElementById('fp-fp-body');
    if (!body) return;
    var meta     = item.meta || {};
    var readonly = S.mode === 'live' ? 'readonly' : '';

    body.innerHTML =
      '<div class="fp-prop-row">' +
        '<label class="fp-prop-label">Text</label>' +
        '<input class="fp-prop-input" id="fp-fp-label" value="' + escAttr(item.label || '') + '" ' + readonly + '>' +
      '</div>' +
      '<div class="fp-prop-row">' +
        '<label class="fp-prop-label">Font size</label>' +
        '<input class="fp-prop-input" id="fp-fp-font-size" type="number" min="8" max="72" value="' + toNum(meta.fontSize, 15) + '" ' + readonly + '>' +
      '</div>' +
      '<div class="fp-prop-row">' +
        '<label class="fp-prop-label">Colour</label>' +
        '<input class="fp-prop-input fp-prop-color" id="fp-fp-color" type="color" value="' + escAttr(meta.color || '#1a1a1a') + '" ' + (S.mode === 'live' ? 'disabled' : '') + '>' +
      '</div>' +
      '<div class="fp-prop-row">' +
        '<label class="fp-prop-label">Background</label>' +
        '<input class="fp-prop-input fp-prop-color" id="fp-fp-bg-color" type="color" value="' + escAttr(meta.bgColor || '#ffffff') + '" ' + (S.mode === 'live' ? 'disabled' : '') + '>' +
        '<label style="margin-top:4px;display:flex;align-items:center;gap:6px;font-size:12px;">' +
          '<input type="checkbox" id="fp-fp-bg-none" ' + (!meta.bgColor ? 'checked' : '') + ' ' + readonly + '> None' +
        '</label>' +
      '</div>';

    if (S.mode === 'builder') {
      ['fp-fp-label', 'fp-fp-font-size', 'fp-fp-color', 'fp-fp-bg-color', 'fp-fp-bg-none'].forEach(function (inputId) {
        var el = document.getElementById(inputId);
        if (el) el.addEventListener('change', function () { applyLabelPropChange(id); });
      });
    }
  }

  function showZoneFloatProps(id, item) {
    var body = document.getElementById('fp-fp-body');
    if (!body) return;
    var meta    = item.meta || {};
    var pending = (S.pendingZoneId === id && S.pendingZoneMembers) ? S.pendingZoneMembers : new Set((meta.members || []).map(String));

    // Member chips — click to remove (toggle off)
    var memberHtml = pending.size === 0
      ? '<span class="fp-zone-empty">No tables in this zone yet</span>'
      : Array.from(pending).map(function (mid) {
          var t   = S.tables[mid];
          var lbl = t ? escHtml(t.label || mid) : escHtml(mid);
          return '<button type="button" class="fp-zone-chip fp-zone-chip--member" data-toggle="' + escAttr(mid) + '" title="Shift-click on canvas, or click here to remove">' +
            lbl +
            '<svg width="7" height="7" viewBox="0 0 7 7" fill="none" aria-hidden="true"><path d="M1 1l5 5M6 1L1 6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>' +
            '</button>';
        }).join('');

    // Available chips — click to add (toggle on)
    var availHtml = Object.keys(S.tables).reduce(function (acc, tid) {
      var t = S.tables[tid];
      if (!t || t.type === 'zone' || t.type === 'text_label') return acc;
      if (pending.has(String(tid))) return acc;
      return acc + '<button type="button" class="fp-zone-chip fp-zone-chip--add" data-toggle="' + escAttr(String(tid)) + '" title="Shift-click on canvas, or click here to add">' +
        escHtml(t.label || 'Table') +
        '<svg width="7" height="7" viewBox="0 0 7 7" fill="none" aria-hidden="true"><path d="M3.5 1v5M1 3.5h5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>' +
        '</button>';
    }, '');

    body.innerHTML =
      '<div class="fp-prop-row">' +
        '<label class="fp-prop-label">Name</label>' +
        '<input class="fp-prop-input" id="fp-fp-label" value="' + escAttr(item.label || '') + '">' +
      '</div>' +
      '<div class="fp-prop-row">' +
        '<label class="fp-prop-label">Colour</label>' +
        '<input class="fp-prop-input fp-prop-color" id="fp-fp-zone-color" type="color" value="' + escAttr(meta.color || '#EAF5EE') + '">' +
      '</div>' +
      '<div class="fp-prop-row">' +
        '<label class="fp-prop-label">Rotation</label>' +
        '<div class="fp-zone-rot-ctrl">' +
          '<input type="number" class="fp-prop-input fp-zone-rot-input" id="fp-fp-zone-rot" min="0" max="359" value="' + escAttr(String(meta.rotation || 0)) + '">°' +
          '<div class="fp-zone-rot-presets">' +
            '<button type="button" data-rot="0">0°</button>' +
            '<button type="button" data-rot="60">60°</button>' +
            '<button type="button" data-rot="90">90°</button>' +
            '<button type="button" data-rot="180">180°</button>' +
          '</div>' +
        '</div>' +
      '</div>' +
      '<div class="fp-prop-row fp-zone-section">' +
        '<span class="fp-prop-label">Members</span>' +
        '<div class="fp-zone-chips" id="fp-zone-chips">' + memberHtml +
          (availHtml ? availHtml : '') +
        '</div>' +
      '</div>' +
      '<p class="fp-zone-hint">Shift-click a table to add or remove it from this zone.</p>' +
      '<div class="fp-zone-actions">' +
        '<button type="button" id="fp-fp-save-zone" class="fp-btn fp-btn-primary" style="flex:1;">Update Zone</button>' +
        '<button type="button" id="fp-fp-ungroup" class="fp-btn fp-btn-secondary">Ungroup</button>' +
      '</div>';

    document.getElementById('fp-zone-chips').addEventListener('click', function (e) {
      var btn = e.target.closest('[data-toggle]');
      if (btn) toggleZoneMembership(btn.dataset.toggle);
    });

    var rotEl = document.getElementById('fp-fp-zone-rot');
    if (rotEl) {
      rotEl.addEventListener('input', function () {
        var val = ((parseInt(this.value, 10) || 0) % 360 + 360) % 360;
        applyZoneRotationPreview(id, val);
      });
    }
    body.querySelectorAll('.fp-zone-rot-presets [data-rot]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var val = parseInt(btn.dataset.rot, 10);
        if (rotEl) rotEl.value = val;
        applyZoneRotationPreview(id, val);
      });
    });

    document.getElementById('fp-fp-save-zone').addEventListener('click', function () {
      saveZone(id);
    });

    var ungroupBtn = document.getElementById('fp-fp-ungroup');
    if (ungroupBtn) {
      ungroupBtn.addEventListener('click', function () {
        fpModal({
          type:   'confirm',
          title:  'Remove zone "' + (item.label || 'Zone') + '"?',
          body:   '<p>The zone grouping will be removed. Tables will remain on the floor plan.</p>',
          ok:     'Remove Zone',
          cancel: 'Keep',
          danger: true,
        }).then(function (ok) {
          if (ok) deleteZone(id);
        });
      });
    }
  }

  function toggleZoneMembership(tableId) {
    if (!S.pendingZoneId || !S.pendingZoneMembers) return;
    var tid = String(tableId);
    if (S.pendingZoneMembers.has(tid)) {
      S.pendingZoneMembers.delete(tid);
    } else {
      S.pendingZoneMembers.add(tid);
    }
    updateZoneMemberVisuals();
    var zone = S.tables[S.pendingZoneId];
    if (zone) showZoneFloatProps(S.pendingZoneId, zone);
  }

  function updateZoneMemberVisuals() {
    var hasZone = !!(S.pendingZoneId && S.pendingZoneMembers);
    Object.keys(S.tables).forEach(function (tid) {
      var t = S.tables[tid];
      if (!t || t.type === 'zone' || t.type === 'text_label') return;
      var g = stage.findOne('#tbl-' + tid);
      if (!g) return;
      g.opacity(hasZone && !S.pendingZoneMembers.has(String(tid)) ? 0.38 : 1);
    });
    tableLayer.batchDraw();
  }

  function saveZone(zoneId) {
    var zone = S.tables[zoneId];
    if (!zone) return;
    var labelEl = document.getElementById('fp-fp-label');
    var colorEl = document.getElementById('fp-fp-zone-color');
    var rotEl   = document.getElementById('fp-fp-zone-rot');
    if (labelEl) zone.label = labelEl.value.trim() || zone.label;
    if (!zone.meta) zone.meta = {};
    if (colorEl) zone.meta.color = colorEl.value;
    if (rotEl)  zone.meta.rotation = ((parseInt(rotEl.value, 10) || 0) % 360 + 360) % 360;
    zone.meta.members = Array.from(S.pendingZoneMembers || []);
    var saveBtn = document.getElementById('fp-fp-save-zone');
    if (saveBtn) { saveBtn.disabled = true; saveBtn.textContent = 'Saving…'; }
    apiFetch('PATCH', 'furniture/' + zoneId, { label: zone.label, meta: zone.meta }).then(function () {
      renderZoneLayer();
      var titleEl = document.getElementById('fp-fp-title');
      if (titleEl) titleEl.textContent = zone.label;
      showToast('Zone updated', 'ok');
      if (saveBtn) { saveBtn.disabled = false; saveBtn.textContent = 'Update Zone'; }
      markDirty();
    }).catch(function (err) {
      console.warn('saveZone failed', err);
      if (saveBtn) { saveBtn.disabled = false; saveBtn.textContent = 'Update Zone'; }
    });
  }

  function applyZoneRotationPreview(zoneId, rotation) {
    var zGroup = zoneLayer.findOne('#zone-' + zoneId);
    if (!zGroup) return;
    var visG = zGroup.getChildren(function (n) { return n.getClassName() === 'Group'; })[0];
    if (visG) { visG.rotation(rotation); zoneLayer.batchDraw(); }
  }

  /* ── Prop change handlers ────────────────────────────────────────── */
  function applyPropChange(id) {
    var item = S.tables[id];
    if (!item) return;
    var labelEl  = document.getElementById('fp-fp-label');
    var capEl    = document.getElementById('fp-fp-cap');
    var capMinEl = document.getElementById('fp-fp-cap-min');
    var keyEl    = document.getElementById('fp-fp-key');
    if (labelEl)  item.label        = labelEl.value.trim();
    if (capEl)    item.capacity_max = parseInt(capEl.value) || 1;
    if (capMinEl) item.capacity_min = parseInt(capMinEl.value) || 0;
    if (keyEl)    item.element_key  = keyEl.value.trim();

    var node = stage.findOne('#tbl-' + id);
    if (node) { node.destroy(); }
    addTableNode(item);
    if (S.selected === id) {
      var newNode = stage.findOne('#tbl-' + id);
      if (newNode && S.mode === 'builder') transformer.nodes([newNode]);
    }
    tableLayer.batchDraw();
    markDirty();
    updateStatusBar();

    var title = document.getElementById('fp-fp-title');
    if (title) title.textContent = item.label || 'Table';
  }

  function applyLabelPropChange(id) {
    var item = S.tables[id];
    if (!item) return;

    var labelEl   = document.getElementById('fp-fp-label');
    var sizeEl    = document.getElementById('fp-fp-font-size');
    var colorEl   = document.getElementById('fp-fp-color');
    var bgColorEl = document.getElementById('fp-fp-bg-color');
    var bgNoneEl  = document.getElementById('fp-fp-bg-none');

    if (labelEl)  item.label = labelEl.value.trim() || 'Label';
    var meta = item.meta || {};
    if (sizeEl)    meta.fontSize = parseInt(sizeEl.value) || 15;
    if (colorEl)   meta.color    = colorEl.value;
    if (bgNoneEl && bgNoneEl.checked) {
      meta.bgColor = '';
    } else if (bgColorEl) {
      meta.bgColor = bgColorEl.value;
    }
    item.meta = meta;

    var node = stage.findOne('#tbl-' + id);
    if (node) { node.destroy(); }
    addTableNode(item);
    if (S.selected === id) {
      var newNode = stage.findOne('#tbl-' + id);
      if (newNode && S.mode === 'builder') transformer.nodes([newNode]);
    }
    tableLayer.batchDraw();
    markDirty();

    var title = document.getElementById('fp-fp-title');
    if (title) title.textContent = item.label;
  }

  /* ══════════════════════════════════════════════════════════════════
     DUPLICATE
  ══════════════════════════════════════════════════════════════════ */
  // Returns the next unused label for a given prefix, e.g. "T" → "T5" if T1-T4 exist
  function nextLabel(prefix) {
    var max = 0;
    Object.values(S.tables).forEach(function (t) {
      var lbl = String(t.label || '');
      if (lbl.indexOf(prefix) === 0) {
        var n = parseInt(lbl.slice(prefix.length));
        if (!isNaN(n) && n > max) max = n;
      }
    });
    return prefix + (max + 1);
  }

  // Extract the non-numeric prefix from a label, e.g. "T3" → "T", "Table 12" → "Table "
  function labelPrefix(lbl) {
    var m = String(lbl || '').match(/^(.*?)(\d+)\s*$/);
    return m ? m[1] : String(lbl || '');
  }

  function duplicateZone(zone) {
    var memberIds = ((zone.meta && zone.meta.members) || []).map(String);
    if (!memberIds.length) {
      showToast('Zone has no members to duplicate', 'info');
      return;
    }
    var OFFSET = 40; // px offset for all copies
    var endpoint = S.activeLayout
      ? 'layouts/' + S.activeLayout.id + '/slots'
      : 'floor-plans/' + S.floorId + '/furniture';

    // Phase 1: duplicate each member table in parallel
    // We need sequential label allocation so numbers don't collide, so chain them
    var newMemberIds = [];

    function dupNextMember(idx) {
      if (idx >= memberIds.length) return Promise.resolve();
      var src = S.tables[memberIds[idx]];
      if (!src) return dupNextMember(idx + 1);

      var prefix = labelPrefix(src.label);
      var newLabel = nextLabel(prefix || 'T');

      var payload = {
        type:         src.type,
        label:        newLabel,
        pos_x:        toNum(src.pos_x, 0) + OFFSET,
        pos_y:        toNum(src.pos_y, 0) + OFFSET,
        width:        src.width,
        height:       src.height,
        rotation_deg: src.rotation_deg || 0,
        capacity_min: src.capacity_min || 0,
        capacity_max: src.capacity_max || 4,
        shape:        src.shape || 'rect',
      };
      if (src.meta) payload.meta = JSON.parse(JSON.stringify(src.meta));
      if (S.activeLayout) payload.furniture_id = src.furniture_id || null;

      return apiFetch('POST', endpoint, payload).then(function (newItem) {
        S.tables[newItem.id] = newItem;
        addTableNode(newItem, false);
        newMemberIds.push(String(newItem.id));
        return dupNextMember(idx + 1);
      });
    }

    dupNextMember(0).then(function () {
      // Phase 2: create the new zone referencing the new member IDs
      var newMeta = JSON.parse(JSON.stringify(zone.meta || {}));
      newMeta.members = newMemberIds;
      var zPayload = {
        type: 'zone', label: (zone.label || 'Zone') + ' (copy)',
        pos_x: 0, pos_y: 0, width: 0, height: 0,
        rotation_deg: 0, capacity_min: 0, capacity_max: 0,
        meta: newMeta,
      };
      if (S.activeLayout) zPayload.furniture_id = zone.furniture_id || null;
      return apiFetch('POST', endpoint, zPayload);
    }).then(function (newZone) {
      S.tables[newZone.id] = newZone;
      tableLayer.batchDraw();
      renderZoneLayer();
      updateStatusBar();
      markDirty();
      showToast('Zone "' + newZone.label + '" duplicated with ' + newMemberIds.length + ' tables', 'info');
      selectZone(newZone.id);
    }).catch(function (err) { console.warn('Duplicate zone failed', err); });
  }

  function duplicateSelected() {
    if (!S.selected || !S.floorId) return;
    var item = S.tables[S.selected];
    if (!item) return;

    if (item.type === 'zone') {
      duplicateZone(item);
      return;
    }

    var payload = {
      type:         item.type,
      label:        (item.label || 'T') + ' (copy)',
      pos_x:        toNum(item.pos_x, 0) + 24,
      pos_y:        toNum(item.pos_y, 0) + 24,
      width:        item.width,
      height:       item.height,
      rotation_deg: item.rotation_deg || 0,
      capacity_min: item.capacity_min || 0,
      capacity_max: item.capacity_max || 4,
      shape:        item.shape || 'rect',
    };
    if (item.meta) payload.meta = JSON.parse(JSON.stringify(item.meta));

    if (S.activeLayout) {
      // Layout mode: create a new slot
      payload.furniture_id = item.furniture_id || null;
      apiFetch('POST', 'layouts/' + S.activeLayout.id + '/slots', payload).then(function (newItem) {
        S.tables[newItem.id] = newItem;
        addTableNode(newItem, false);
        tableLayer.batchDraw();
        updateStatusBar();
        markDirty();
        selectTable(newItem.id);
      }).catch(function (err) { console.warn('Duplicate failed', err); });
    } else {
      apiFetch('POST', 'floor-plans/' + S.floorId + '/furniture', payload).then(function (newItem) {
        S.tables[newItem.id] = newItem;
        addTableNode(newItem, false);
        tableLayer.batchDraw();
        updateStatusBar();
        markDirty();
        selectTable(newItem.id);
      }).catch(function (err) { console.warn('Duplicate failed', err); });
    }
  }

  /* ══════════════════════════════════════════════════════════════════
     DELETE / UNDO / REDO
  ══════════════════════════════════════════════════════════════════ */
  function deleteSelectedWithConfirm(sourceBtn) {
    var btn = sourceBtn || document.getElementById('fp-fp-delete');
    if (!btn || !S.selected) { deleteSelected(); return; }
    if (btn.dataset.confirm === '1') {
      clearTimeout(parseInt(btn.dataset.timer, 10));
      delete btn.dataset.confirm;
      delete btn.dataset.timer;
      var item = S.tables[S.selected];
      btn.textContent = item && item.type === 'text_label' ? 'Remove label' : item && item.type === 'zone' ? 'Remove zone' : 'Remove table';
      btn.classList.remove('fp-btn-danger--confirming');
      deleteSelected();
      return;
    }
    btn.textContent = 'Confirm remove?';
    btn.dataset.confirm = '1';
    btn.classList.add('fp-btn-danger--confirming');
    var t = setTimeout(function () {
      var it = S.tables[S.selected];
      btn.textContent = it && it.type === 'text_label' ? 'Remove label' : it && it.type === 'zone' ? 'Remove zone' : 'Remove table';
      delete btn.dataset.confirm;
      delete btn.dataset.timer;
      btn.classList.remove('fp-btn-danger--confirming');
    }, 3000);
    btn.dataset.timer = String(t);
  }

  function deleteSelected() {
    var id = S.selected;
    if (!id) return;

    var item = S.tables[id];
    if (item && item.type === 'zone') {
      deleteZone(id);
      return;
    }

    var endpoint = S.activeLayout ? 'slots/' + id : 'furniture/' + id;
    apiFetch('DELETE', endpoint).then(function () {
      var node = stage.findOne('#tbl-' + id);
      if (node) node.destroy();
      delete S.tables[id];
      deselect();
      tableLayer.batchDraw();
      updateStatusBar();
      markDirty();
      if (!Object.keys(S.tables).length) showEmptyState(true);
    }).catch(function (err) { console.warn('Delete failed', err); });
  }

  function pushUndo(id) {
    var item = S.tables[id];
    if (!item) return;
    S.undoStack.push({ id: id, pos_x: item.pos_x, pos_y: item.pos_y, rotation_deg: item.rotation_deg });
    if (S.undoStack.length > 60) S.undoStack.shift();
    S.redoStack = [];
    syncUndoBtns();
  }

  function undo() {
    var snap = S.undoStack.pop();
    if (!snap) return;
    var item = S.tables[snap.id];
    if (item) {
      S.redoStack.push({ id: snap.id, pos_x: item.pos_x, pos_y: item.pos_y, rotation_deg: item.rotation_deg });
      item.pos_x = snap.pos_x; item.pos_y = snap.pos_y; item.rotation_deg = snap.rotation_deg;
      var node = stage.findOne('#tbl-' + snap.id);
      if (node) { node.position({ x: item.pos_x, y: item.pos_y }); node.rotation(item.rotation_deg); }
      tableLayer.batchDraw();
      renderZoneLayer();
      markDirty();
    }
    syncUndoBtns();
  }

  function redo() {
    var snap = S.redoStack.pop();
    if (!snap) return;
    var item = S.tables[snap.id];
    if (item) {
      S.undoStack.push({ id: snap.id, pos_x: item.pos_x, pos_y: item.pos_y, rotation_deg: item.rotation_deg });
      item.pos_x = snap.pos_x; item.pos_y = snap.pos_y; item.rotation_deg = snap.rotation_deg;
      var node = stage.findOne('#tbl-' + snap.id);
      if (node) { node.position({ x: item.pos_x, y: item.pos_y }); node.rotation(item.rotation_deg); }
      tableLayer.batchDraw();
      renderZoneLayer();
      markDirty();
    }
    syncUndoBtns();
  }

  function syncUndoBtns() {
    var u = document.getElementById('fp-btn-undo');
    var r = document.getElementById('fp-btn-redo');
    if (u) u.disabled = S.undoStack.length === 0;
    if (r) r.disabled = S.redoStack.length === 0;
  }

  /* ══════════════════════════════════════════════════════════════════
     PUBLISH / SAVE
  ══════════════════════════════════════════════════════════════════ */
  function markDirty() {
    S.dirty = true;
    var btn = document.getElementById('fp-btn-publish');
    if (btn) btn.disabled = false;
  }

  function saveLayout() {
    if (!S.floorId) return;
    var btn = document.getElementById('fp-btn-publish');
    if (btn) btn.textContent = 'Saving…';

    var promise;
    if (S.activeLayout) {
      // Layout mode: bulk-save to slots endpoint
      var items = Object.values(S.tables).map(function (item) {
        return {
          furniture_id: item.furniture_id || null,
          type:         item.type,
          label:        item.label,
          pos_x:        item.pos_x,
          pos_y:        item.pos_y,
          width:        item.width,
          height:       item.height,
          rotation_deg: item.rotation_deg || 0,
          capacity_min: item.capacity_min,
          capacity_max: item.capacity_max,
          element_key:  item.element_key || null,
          group_id:     item.group_id || null,
          is_visible:   item.is_visible !== undefined ? item.is_visible : 1,
          meta:         item.meta || null,
        };
      });
      promise = apiFetch('POST', 'layouts/' + S.activeLayout.id + '/slots/bulk', { items: items });
    } else {
      // Base mode: PATCH each furniture item individually (existing behaviour)
      var promises = Object.values(S.tables).map(function (item) {
        var patch = {
          label:        item.label,
          pos_x:        item.pos_x,
          pos_y:        item.pos_y,
          rotation_deg: item.rotation_deg || 0,
          capacity_min: item.capacity_min,
          capacity_max: item.capacity_max,
          element_key:  item.element_key,
        };
        if (item.meta) patch.meta = item.meta;
        return apiFetch('PATCH', 'furniture/' + item.id, patch);
      });
      promise = Promise.all(promises);
    }

    promise.then(function () {
      S.dirty = false;
      if (btn) {
        btn.textContent = 'Saved ✓';
        btn.disabled = true;
        setTimeout(function () { btn.textContent = 'Publish updates'; }, 2500);
      }
    }).catch(function () {
      if (btn) { btn.textContent = 'Error — retry'; btn.disabled = false; }
    });
  }

  /* ══════════════════════════════════════════════════════════════════
     LIVE STATUS
  ══════════════════════════════════════════════════════════════════ */
  function startLiveUpdates() {
    fetchLiveStatus();
    clearInterval(S.liveTimer);
    S.liveTimer = setInterval(fetchLiveStatus, 30000);
  }

  function stopLiveUpdates() {
    clearInterval(S.liveTimer);
    S.liveTimer = null;
  }

  function fetchLiveStatus() {
    if (!S.floorId || S.mode !== 'live') return;
    var url = 'reservations?date=' + S.date + '&per_page=100';
    apiFetch('GET', url).then(function (data) {
      var items = Array.isArray(data) ? data : (data.data || []);
      S.liveStatus = {};
      items.forEach(function (res) {
        if (!res.furniture_ids || !res.furniture_ids.length) return;
        var statusKey = 'booked';
        if (res.status === 'seated')   statusKey = 'occupied';
        if (res.status === 'canceled' || res.status === 'no_show') return;
        res.furniture_ids.forEach(function (fid) {
          S.liveStatus[fid] = statusKey;
        });
      });
      renderAllTables();
    }).catch(function () {});
  }

  /* ══════════════════════════════════════════════════════════════════
     MODE SWITCHING
  ══════════════════════════════════════════════════════════════════ */
  function enterBuilderMode() {
    S.mode = 'builder';
    document.getElementById('fp-app').dataset.mode = 'builder';
    stopLiveUpdates();
    tableLayer.getChildren(function (n) { return n.name() === 'table-group'; })
      .forEach(function (g) { g.draggable(true); });
    renderAllTables();
    renderZoneLayer();
    tableLayer.batchDraw();
    setTimeout(zoomFit, 150);
  }

  function exitBuilderMode() {
    if (!S.dirty) { _doExitBuilder(); return; }
    fpModal({
      type:   'confirm',
      title:  'Exit without publishing?',
      body:   '<p>You have unsaved layout changes. These will be lost if you exit now.</p>',
      ok:     'Exit',
      cancel: 'Stay',
      danger: true,
    }).then(function (ok) {
      if (ok) _doExitBuilder();
    });
  }

  function _doExitBuilder() {
    S.mode = 'live';
    document.getElementById('fp-app').dataset.mode = 'live';
    deselect();
    cancelPlacing();
    S.dirty = false;
    tableLayer.getChildren(function (n) { return n.name() === 'table-group'; })
      .forEach(function (g) { g.draggable(false); });
    transformer.nodes([]);
    renderAllTables();
    renderZoneLayer();
    tableLayer.batchDraw();
    startLiveUpdates();
    var btn = document.getElementById('fp-btn-publish');
    if (btn) btn.disabled = true;
  }

  /* ══════════════════════════════════════════════════════════════════
     ADD FLOOR
  ══════════════════════════════════════════════════════════════════ */
  function addFloor() {
    fpModal({
      type:  'prompt',
      title: 'Add Floor',
      body:  '<input class="thr-modal-input" type="text" placeholder=\'e.g. Level 1, Rooftop, Garden\' maxlength="40">',
      ok:    'Add Floor',
    }).then(function (name) {
      if (!name) return;
      apiFetch('POST', 'floor-plans', {
        name:         name,
        floor_number: S.floors.length + 1,
        is_active:    1,
        width_px:     1200,
        height_px:    800,
      }).then(function (floor) {
        S.floors.push(floor);
        renderFloorTabs();
        selectFloor(floor.id);
        loadFloorStats(floor.id);
      }).catch(function (err) {
        console.warn('Add floor failed', err);
        fpModal({ type: 'alert', title: 'Error', body: '<p>Could not add floor. You may not have permission.</p>', ok: 'OK' });
      });
    });
  }

  /* ══════════════════════════════════════════════════════════════════
     ZOOM
  ══════════════════════════════════════════════════════════════════ */
  function zoomBy(factor) {
    var oldScale = stage.scaleX();
    var newScale = Math.max(0.2, Math.min(4, oldScale * factor));
    var cx = stage.width() / 2, cy = stage.height() / 2;
    var pos = stage.position();
    stage.scale({ x: newScale, y: newScale });
    stage.position({
      x: cx - (cx - pos.x) * (newScale / oldScale),
      y: cy - (cy - pos.y) * (newScale / oldScale),
    });
    stage.batchDraw();
    var badge = document.getElementById('fp-zoom-pct');
    if (badge) badge.textContent = Math.round(newScale * 100) + '%';
  }

  function zoomFit() {
    stage.scale({ x: 1, y: 1 });
    stage.position({ x: 0, y: 0 });
    stage.batchDraw();
    var badge = document.getElementById('fp-zoom-pct');
    if (badge) badge.textContent = '100%';
  }

  /* ══════════════════════════════════════════════════════════════════
     EVENT BINDINGS
  ══════════════════════════════════════════════════════════════════ */
  function bindHeader() {
    bindEl('fp-btn-edit',          'click', enterBuilderMode);
    bindEl('fp-btn-exit-builder',  'click', exitBuilderMode);
    bindEl('fp-btn-publish',       'click', saveLayout);
    bindEl('fp-btn-undo',          'click', undo);
    bindEl('fp-btn-redo',          'click', redo);

    var newResBtn = document.getElementById('fp-btn-new-res');
    if (newResBtn && cfg.newResUrl) newResBtn.href = cfg.newResUrl;
  }

  function bindFloorNav() {
    bindEl('fp-add-floor', 'click', addFloor);
  }

  function bindSubbar() {
    var chipDate  = document.getElementById('fp-chip-date');
    var chipTime  = document.getElementById('fp-chip-time');
    var datePanel = document.getElementById('fp-date-panel');
    var timePanel = document.getElementById('fp-time-panel');

    if (chipDate) chipDate.addEventListener('click', function (e) {
      e.stopPropagation();
      if (datePanel) { datePanel.hidden = !datePanel.hidden; if (timePanel) timePanel.hidden = true; }
    });

    if (chipTime) chipTime.addEventListener('click', function (e) {
      e.stopPropagation();
      if (timePanel) { timePanel.hidden = !timePanel.hidden; if (datePanel) datePanel.hidden = true; }
    });

    var dateInput = document.getElementById('fp-date-input');
    if (dateInput) {
      dateInput.value = S.date;
      dateInput.addEventListener('change', function () {
        S.date = this.value || TODAY;
        setDateLabel(S.date);
        if (datePanel) datePanel.hidden = true;
        S.floors.forEach(function (f) { loadFloorStats(f.id); });
        if (S.mode === 'live') fetchLiveStatus();
      });
    }

    document.querySelectorAll('.fp-time-opt').forEach(function (opt) {
      opt.addEventListener('click', function () {
        S.time = this.dataset.time || '';
        setTimeLabel(S.time);
        document.querySelectorAll('.fp-time-opt').forEach(function (o) {
          o.classList.toggle('fp-time-opt--active', o === opt);
        });
        if (timePanel) timePanel.hidden = true;
        if (S.mode === 'live') fetchLiveStatus();
      });
    });

    document.querySelectorAll('.fp-view-tab').forEach(function (btn) {
      btn.addEventListener('click', function () {
        document.querySelectorAll('.fp-view-tab').forEach(function (b) { b.classList.remove('fp-view-tab--active'); });
        this.classList.add('fp-view-tab--active');
      });
    });

    document.addEventListener('click', function () {
      if (datePanel) datePanel.hidden = true;
      if (timePanel) timePanel.hidden = true;
    });
  }

  function bindCanvasTools() {
    bindEl('fp-zoom-in',    'click', function () { zoomBy(1.2); });
    bindEl('fp-zoom-out',   'click', function () { zoomBy(1 / 1.2); });
    bindEl('fp-zoom-fit',   'click', zoomFit);
    bindEl('fp-delete-sel', 'click', function () { deleteSelectedWithConfirm(null); });
  }

  function bindFloatPanel() {
    bindEl('fp-fp-close',  'click', function () { hideFloatPanel(); deselect(); });
    bindEl('fp-fp-delete', 'click', function (e) { deleteSelectedWithConfirm(e.currentTarget); });
    bindEl('fp-fp-back',   'click', deselect);
    makePanelDraggable();
  }

  function makePanelDraggable() {
    var panel = document.getElementById('fp-float-panel');
    if (!panel) return;
    var hd = panel.querySelector('.fp-fp-hd');
    if (!hd) return;

    var _drag = null;

    hd.addEventListener('mousedown', function (e) {
      if (e.target.closest && e.target.closest('.fp-fp-close')) return;
      if (panel.hidden) return;
      e.preventDefault();
      _drag = {
        startX:   e.clientX,
        startY:   e.clientY,
        origLeft: panel.offsetLeft,
        origTop:  panel.offsetTop,
      };
      document.body.style.userSelect = 'none';
      hd.style.cursor = 'grabbing';
    });

    document.addEventListener('mousemove', function (e) {
      if (!_drag) return;
      var wrap = document.getElementById('fp-canvas-wrap');
      var wrapW = wrap ? wrap.offsetWidth  : 9999;
      var wrapH = wrap ? wrap.offsetHeight : 9999;
      var newLeft = Math.max(0, Math.min(_drag.origLeft + (e.clientX - _drag.startX), wrapW - panel.offsetWidth));
      var newTop  = Math.max(0, Math.min(_drag.origTop  + (e.clientY - _drag.startY), wrapH - panel.offsetHeight));
      panel.style.left = newLeft + 'px';
      panel.style.top  = newTop  + 'px';
    });

    document.addEventListener('mouseup', function () {
      if (!_drag) return;
      _drag = null;
      document.body.style.userSelect = '';
      hd.style.cursor = '';
    });
  }

  function bindToolbar() {
    bindEl('fp-tb-snap', 'click', function (e) {
      S.snapEnabled = !S.snapEnabled;
      e.currentTarget.classList.toggle('fp-tb-ctrl--active', S.snapEnabled);
    });
    bindEl('fp-tb-dup', 'click', duplicateSelected);
  }

  function bindKeyboard() {
    document.addEventListener('keydown', function (e) {
      var tag = document.activeElement ? document.activeElement.tagName : '';
      var inField = tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT';

      if (e.key === 'Escape') {
        if (S.placing) { cancelPlacing(); return; }
        if (S.selected || S.selectedIds.size) { deselect(); return; }
      }
      if (!inField && (e.key === 'Delete' || e.key === 'Backspace') && S.selected && S.mode === 'builder') {
        deleteSelected(); return;
      }
      if ((e.ctrlKey || e.metaKey) && e.key === 'z' && !e.shiftKey) { e.preventDefault(); undo(); return; }
      if ((e.ctrlKey || e.metaKey) && (e.key === 'y' || (e.key === 'z' && e.shiftKey))) { e.preventDefault(); redo(); return; }
      if ((e.ctrlKey || e.metaKey) && e.key === 's') { e.preventDefault(); if (S.mode === 'builder' && S.dirty) saveLayout(); return; }
      if ((e.ctrlKey || e.metaKey) && e.key === 'd') { e.preventDefault(); if (S.mode === 'builder') duplicateSelected(); return; }
    });
  }

  /* ══════════════════════════════════════════════════════════════════
     UI HELPERS
  ══════════════════════════════════════════════════════════════════ */
  function updateStatusBar() {
    var items   = Object.values(S.tables).filter(function (t) { return t.type !== 'text_label' && t.type !== 'zone'; });
    var tCount  = items.length;
    var sCount  = items.reduce(function (s, t) { return s + (parseInt(t.capacity_max) || 0); }, 0);

    var statusEl = document.getElementById('fp-tb-status');
    if (statusEl) {
      statusEl.textContent = tCount
        ? tCount + ' table' + (tCount !== 1 ? 's' : '') + ' · ' + sCount + ' seat' + (sCount !== 1 ? 's' : '')
        : 'No tables';
    }

    // Backwards compat
    var cntEl = document.getElementById('fp-table-count');
    if (cntEl) cntEl.textContent = String(tCount);
  }

  // Kept for backwards compat (called in some places)
  function updateTableCount() { updateStatusBar(); }

  function showEmptyState(show) {
    var wrap = document.getElementById('fp-canvas-wrap');
    if (!wrap) return;
    var existing = wrap.querySelector('.fp-canvas-empty');
    if (show && !existing) {
      var div = document.createElement('div');
      div.className = 'fp-canvas-empty';
      div.innerHTML =
        '<div class="fp-canvas-empty-icon">' +
          '<svg width="24" height="24" fill="none" viewBox="0 0 24 24"><rect x="2" y="4" width="20" height="16" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="M6 12h12M12 8v8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>' +
        '</div>' +
        '<span class="fp-canvas-empty-text">No tables yet — click <strong>Edit Floorplan</strong> to start adding</span>';
      wrap.appendChild(div);
    } else if (!show && existing) {
      existing.remove();
    }
  }

  function setDateLabel(date) {
    var el = document.getElementById('fp-date-lbl');
    if (!el) return;
    if (!date || date === TODAY) { el.textContent = 'Today'; return; }
    try {
      var d = new Date(date + 'T00:00:00');
      el.textContent = d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    } catch (e) { el.textContent = date; }
  }

  function setTimeLabel(time) {
    var el = document.getElementById('fp-time-lbl');
    if (!el) return;
    if (!time) { el.textContent = 'All day'; return; }
    var parts = time.split(':');
    var h = parseInt(parts[0], 10), m = parts[1] || '00';
    el.textContent = (h % 12 || 12) + ':' + m + (h >= 12 ? ' PM' : ' AM');
  }

  function bindEl(id, ev, fn) {
    var el = document.getElementById(id);
    if (el) el.addEventListener(ev, fn);
  }

  /* ══════════════════════════════════════════════════════════════════
     TYPE HELPERS
  ══════════════════════════════════════════════════════════════════ */
  function isRound(item) {
    var t = item.type || '';
    if (t === 'table-round' || t === 'bar-seat') return true;
    var typeDef = TYPES[t] || {};
    return (typeDef.shape || item.shape || 'rect') === 'circle';
  }

  // Checks by type key string (for placeTable shape resolution)
  function isRoundKey(typeKey) {
    return typeKey === 'table-round' || typeKey === 'bar-seat' || typeKey === 'table-square' ||
      (TYPES[typeKey] && (TYPES[typeKey].shape === 'circle'));
  }

  function isLounge(item) {
    return item.type === 'lounge';
  }

  function isBarSeat(item) {
    var t = item.type || '';
    return t === 'bar-seat' || t === 'bar-stool';
  }

  function isBooth(typeKey) {
    return typeof typeKey === 'string' && typeKey.indexOf('booth') === 0;
  }

  function isZone(typeKey) {
    return typeKey === 'zone' ||
      typeKey === 'stage' || typeKey === 'dj-booth' || typeKey === 'area-vip' || typeKey === 'bar-counter';
  }

  function snapToGrid(v) { return S.snapEnabled ? Math.round(v / 24) * 24 : Math.round(v); }

  // Kept for backwards compat
  function snapTo24(v) { return Math.round(v / 24) * 24; }

  function toNum(v, def) { return isNaN(parseFloat(v)) ? def : parseFloat(v); }

  function hexToRgba(hex, alpha) {
    var h = (hex || '#EAF5EE').replace('#', '');
    if (h.length === 3) h = h[0]+h[0]+h[1]+h[1]+h[2]+h[2];
    var r = parseInt(h.substring(0, 2), 16);
    var g = parseInt(h.substring(2, 4), 16);
    var b = parseInt(h.substring(4, 6), 16);
    return 'rgba(' + r + ',' + g + ',' + b + ',' + alpha + ')';
  }

  // Derive a darker border from the zone fill so it's always visible on canvas
  function zoneBorderColor(hex, alpha) {
    var h = (hex || '#EAF5EE').replace('#', '');
    if (h.length === 3) h = h[0]+h[0]+h[1]+h[1]+h[2]+h[2];
    var r = Math.round(parseInt(h.substring(0, 2), 16) * 0.52);
    var g = Math.round(parseInt(h.substring(2, 4), 16) * 0.52);
    var b = Math.round(parseInt(h.substring(4, 6), 16) * 0.52);
    return 'rgba(' + r + ',' + g + ',' + b + ',' + (alpha || 0.85) + ')';
  }

  function getZoneBBoxRaw(zItem, excludeId) {
    var members = (zItem.meta && zItem.meta.members) ? zItem.meta.members : [];
    var minX = Infinity, minY = Infinity, maxX = -Infinity, maxY = -Infinity;
    members.forEach(function (mid) {
      if (excludeId !== undefined && String(mid) === String(excludeId)) return;
      var m = S.tables[mid];
      if (!m) return;
      var mW = toNum(m.width, 80) / 2;
      var mH = toNum(m.height, 80) / 2;
      minX = Math.min(minX, toNum(m.pos_x, 0) - mW);
      minY = Math.min(minY, toNum(m.pos_y, 0) - mH);
      maxX = Math.max(maxX, toNum(m.pos_x, 0) + mW);
      maxY = Math.max(maxY, toNum(m.pos_y, 0) + mH);
    });
    if (!isFinite(minX)) return null;
    var pad = 20;
    return { x1: minX - pad, y1: minY - pad, x2: maxX + pad, y2: maxY + pad };
  }

  /* ══════════════════════════════════════════════════════════════════
     LAYOUT SYSTEM
  ══════════════════════════════════════════════════════════════════ */

  function initLayoutSystem(floor) {
    // Reset badge
    var nameEl = document.getElementById('fp-layout-name');
    if (nameEl) nameEl.textContent = 'Base layout';
    var btn = document.getElementById('fp-layout-btn');
    if (btn) btn.classList.remove('fp-layout-active');

    // Hide panel and banner
    var panel = document.getElementById('fp-layout-panel');
    if (panel) panel.hidden = true;
    S.layoutPanel = false;
    hideLayoutBanner();

    if (!S.floorId) return;

    // Wire up toggle button (only once — use flag)
    if (!initLayoutSystem._wired) {
      initLayoutSystem._wired = true;

      var layoutBtn = document.getElementById('fp-layout-btn');
      if (layoutBtn) {
        layoutBtn.addEventListener('click', function (e) {
          e.stopPropagation();
          toggleLayoutPanel();
        });
      }

      var closeBtn = document.getElementById('fp-layout-panel-close');
      if (closeBtn) {
        closeBtn.addEventListener('click', function () {
          closeLayoutPanel();
        });
      }

      var newBtn = document.getElementById('fp-layout-new-btn');
      if (newBtn) {
        newBtn.addEventListener('click', function () {
          var name = window.prompt('Layout name:', 'Event layout');
          if (!name || !name.trim()) return;
          apiFetch('POST', 'layouts', {
            floor_plan_id: S.floorId,
            name: name.trim(),
            copy_from_base: true,
          }).then(function (layout) {
            S.layouts.push(layout);
            renderLayoutPanel();
            showToast('Layout "' + layout.name + '" created', 'success');
          }).catch(function () {
            showToast('Failed to create layout', 'error');
          });
        });
      }

      var backBase = document.getElementById('fp-layout-back-base');
      if (backBase) {
        backBase.addEventListener('click', function () {
          loadBaseLayout();
        });
      }

      // Backdrop click closes panel
      document.addEventListener('click', function (e) {
        if (!S.layoutPanel) return;
        var panel = document.getElementById('fp-layout-panel');
        var btn   = document.getElementById('fp-layout-btn');
        if (panel && !panel.contains(e.target) && btn && !btn.contains(e.target)) {
          closeLayoutPanel();
        }
      });
    }

    // Load layouts for this floor plan
    apiFetch('GET', 'layouts?floor_plan_id=' + S.floorId).then(function (data) {
      S.layouts = Array.isArray(data) ? data : [];
      renderLayoutPanel();

      // If floor plan has an active_layout_id, load it
      if (floor && floor.active_layout_id) {
        var active = S.layouts.find(function (l) { return l.id === floor.active_layout_id; });
        if (active) {
          loadLayout(active.id);
        }
      }
    }).catch(function () {
      S.layouts = [];
    });
  }

  function toggleLayoutPanel() {
    if (S.layoutPanel) {
      closeLayoutPanel();
    } else {
      openLayoutPanel();
    }
  }

  function openLayoutPanel() {
    var panel = document.getElementById('fp-layout-panel');
    if (panel) panel.hidden = false;
    S.layoutPanel = true;
  }

  function closeLayoutPanel() {
    var panel = document.getElementById('fp-layout-panel');
    if (panel) panel.hidden = true;
    S.layoutPanel = false;
  }

  function renderLayoutPanel() {
    var list = document.getElementById('fp-layout-list');
    if (!list) return;
    list.innerHTML = '';

    // Base layout item
    var baseEl = document.createElement('div');
    baseEl.className = 'fp-layout-base-item' + (S.activeLayout === null ? ' active' : '');
    baseEl.innerHTML =
      '<div class="fp-layout-base-name">Base layout</div>' +
      '<div class="fp-layout-base-meta">The default furniture arrangement</div>';
    baseEl.addEventListener('click', function () {
      loadBaseLayout();
      closeLayoutPanel();
    });
    list.appendChild(baseEl);

    // Layout items
    S.layouts.forEach(function (layout) {
      var isActive = S.activeLayout && S.activeLayout.id === layout.id;
      var item = document.createElement('div');
      item.className = 'fp-layout-item' + (isActive ? ' active' : '');

      var nameHtml = '<span class="fp-layout-item-name">' + escHtml(layout.name);
      if (layout.is_default) {
        nameHtml += '<span class="fp-layout-default-badge">Default</span>';
      }
      nameHtml += '</span>';

      item.innerHTML =
        nameHtml +
        '<div class="fp-layout-item-meta">Created ' + escHtml(layout.created_at ? layout.created_at.slice(0, 10) : '') + '</div>' +
        '<div class="fp-layout-item-actions">' +
          '<button class="fp-btn fp-btn-outline fp-btn-xs" data-action="load" type="button">Load</button>' +
          '<button class="fp-btn fp-btn-outline fp-btn-xs" data-action="periods" type="button">⏱ Periods</button>' +
          '<button class="fp-btn fp-btn-outline fp-btn-xs" data-action="duplicate" type="button">Duplicate</button>' +
          '<button class="fp-btn fp-btn-outline fp-btn-xs" data-action="snapshot" type="button">↺ Refresh</button>' +
          '<button class="fp-btn fp-btn-ghost fp-btn-xs" data-action="delete" type="button" style="color:var(--red);">Delete</button>' +
        '</div>' +
        '<div class="fp-layout-periods-panel" data-periods-for="' + layout.id + '" hidden></div>';

      item.querySelector('[data-action="load"]').addEventListener('click', function (e) {
        e.stopPropagation();
        loadLayout(layout.id);
        closeLayoutPanel();
      });
      item.querySelector('[data-action="duplicate"]').addEventListener('click', function (e) {
        e.stopPropagation();
        apiFetch('POST', 'layouts/' + layout.id + '/duplicate').then(function (newLayout) {
          S.layouts.push(newLayout);
          renderLayoutPanel();
          showToast('Layout duplicated as "' + newLayout.name + '"', 'info');
        }).catch(function () { showToast('Duplicate failed', 'error'); });
      });
      item.querySelector('[data-action="periods"]').addEventListener('click', function (e) {
        e.stopPropagation();
        var panel = item.querySelector('.fp-layout-periods-panel');
        if (!panel) return;
        var isOpen = !panel.hidden;
        if (isOpen) { panel.hidden = true; return; }
        panel.hidden = false;
        panel.innerHTML = '<div style="padding:6px 0;font-size:11px;color:#9CA3AF;">Loading…</div>';
        apiFetch('GET', 'layouts/' + layout.id + '/periods').then(function (periods) {
          renderPeriodsPanel(panel, layout.id, periods);
        }).catch(function () {
          panel.innerHTML = '<div style="font-size:11px;color:var(--red);">Failed to load periods</div>';
        });
      });
      item.querySelector('[data-action="snapshot"]').addEventListener('click', function (e) {
        e.stopPropagation();
        if (!window.confirm('Replace all slots in "' + layout.name + '" with the current base furniture?')) return;
        apiFetch('POST', 'layouts/' + layout.id + '/snapshot').then(function (res) {
          showToast('Snapshot updated — ' + res.slots_created + ' slots copied', 'success');
          // Reload if this is the active layout
          if (S.activeLayout && S.activeLayout.id === layout.id) {
            loadLayout(layout.id);
          }
        }).catch(function () {
          showToast('Snapshot failed', 'error');
        });
      });
      item.querySelector('[data-action="delete"]').addEventListener('click', function (e) {
        e.stopPropagation();
        if (!window.confirm('Delete layout "' + layout.name + '"? This cannot be undone.')) return;
        apiFetch('DELETE', 'layouts/' + layout.id).then(function () {
          S.layouts = S.layouts.filter(function (l) { return l.id !== layout.id; });
          if (S.activeLayout && S.activeLayout.id === layout.id) {
            loadBaseLayout();
          }
          renderLayoutPanel();
          showToast('Layout deleted', 'success');
        }).catch(function () {
          showToast('Delete failed', 'error');
        });
      });

      item.addEventListener('click', function () {
        loadLayout(layout.id);
        closeLayoutPanel();
      });

      list.appendChild(item);
    });
  }

  var DAYS = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];

  function renderPeriodsPanel(panel, layoutId, periods) {
    var html = '<div class="fp-periods-wrap">';
    if (!periods.length) {
      html += '<p class="fp-periods-empty">No periods yet. Add one to enable time-based auto-switching.</p>';
    }
    periods.forEach(function (p) {
      var dayBits = parseInt(p.days_of_week) || 127;
      var dayStr = DAYS.filter(function (d, i) { return dayBits & (1 << i); }).join(', ') || 'None';
      html += '<div class="fp-period-row" data-pid="' + p.id + '">' +
        '<div class="fp-period-info">' +
          '<span class="fp-period-name">' + escHtml(p.name) + '</span>' +
          '<span class="fp-period-times">' + escHtml(p.start_time.slice(0,5)) + ' – ' + escHtml(p.end_time.slice(0,5)) + '</span>' +
          '<span class="fp-period-days">' + escHtml(dayStr) + '</span>' +
        '</div>' +
        '<button class="fp-btn fp-btn-ghost fp-btn-xs fp-period-del" type="button" style="color:var(--red);" data-pid="' + p.id + '">✕</button>' +
      '</div>';
    });
    html += '<button class="fp-btn fp-btn-outline fp-btn-xs fp-period-add" type="button" style="margin-top:6px;width:100%;">+ Add period</button></div>';
    panel.innerHTML = html;

    panel.querySelectorAll('.fp-period-del').forEach(function (btn) {
      btn.addEventListener('click', function (e) {
        e.stopPropagation();
        var pid = btn.dataset.pid;
        if (!window.confirm('Remove this period?')) return;
        apiFetch('DELETE', 'periods/' + pid).then(function () {
          periods = periods.filter(function (p) { return String(p.id) !== pid; });
          renderPeriodsPanel(panel, layoutId, periods);
        }).catch(function () { showToast('Delete failed', 'error'); });
      });
    });

    panel.querySelector('.fp-period-add').addEventListener('click', function (e) {
      e.stopPropagation();
      showAddPeriodForm(panel, layoutId, periods);
    });
  }

  function showAddPeriodForm(panel, layoutId, periods) {
    var formHtml =
      '<div class="fp-period-form">' +
        '<input class="fp-prop-input fp-prop-input--sm" id="fp-pf-name" placeholder="e.g. Dinner" style="margin-bottom:4px;">' +
        '<div style="display:flex;gap:6px;margin-bottom:4px;">' +
          '<input class="fp-prop-input fp-prop-input--sm" id="fp-pf-start" type="time" value="18:00" style="flex:1;">' +
          '<span style="align-self:center;color:#9CA3AF;">–</span>' +
          '<input class="fp-prop-input fp-prop-input--sm" id="fp-pf-end" type="time" value="23:00" style="flex:1;">' +
        '</div>' +
        '<div class="fp-period-days-row">' +
          DAYS.map(function (d, i) {
            return '<label class="fp-day-chip"><input type="checkbox" value="' + (1 << i) + '" checked> ' + d + '</label>';
          }).join('') +
        '</div>' +
        '<div style="display:flex;gap:6px;margin-top:6px;">' +
          '<button class="fp-btn fp-btn-primary fp-btn-xs fp-pf-save" type="button" style="flex:1;">Save</button>' +
          '<button class="fp-btn fp-btn-ghost fp-btn-xs fp-pf-cancel" type="button">Cancel</button>' +
        '</div>' +
      '</div>';

    var existing = panel.querySelector('.fp-period-form');
    if (existing) { existing.remove(); return; }
    panel.insertAdjacentHTML('beforeend', formHtml);

    var form = panel.querySelector('.fp-period-form');
    form.querySelector('.fp-pf-cancel').addEventListener('click', function (e) { e.stopPropagation(); form.remove(); });
    form.querySelector('.fp-pf-save').addEventListener('click', function (e) {
      e.stopPropagation();
      var name  = form.querySelector('#fp-pf-name').value.trim();
      var start = form.querySelector('#fp-pf-start').value;
      var end   = form.querySelector('#fp-pf-end').value;
      if (!name) { form.querySelector('#fp-pf-name').focus(); return; }
      var bits = 0;
      form.querySelectorAll('.fp-period-days-row input[type=checkbox]').forEach(function (cb) {
        if (cb.checked) bits |= parseInt(cb.value);
      });
      apiFetch('POST', 'layouts/' + layoutId + '/periods', {
        name: name, start_time: start + ':00', end_time: end + ':00', days_of_week: bits,
      }).then(function (p) {
        periods.push(p);
        renderPeriodsPanel(panel, layoutId, periods);
      }).catch(function () { showToast('Failed to add period', 'error'); });
    });
  }

  function loadLayout(layoutId) {
    apiFetch('GET', 'layouts/' + layoutId + '/slots').then(function (data) {
      var slots = Array.isArray(data) ? data : [];
      S.tables = {};
      slots.forEach(function (slot) {
        // Map slot fields to the same structure addTableNode expects
        S.tables[slot.id] = {
          id:           slot.id,
          furniture_id: slot.furniture_id,
          type:         slot.type,
          label:        slot.label,
          pos_x:        slot.pos_x,
          pos_y:        slot.pos_y,
          width:        slot.width,
          height:       slot.height,
          rotation_deg: slot.rotation_deg,
          capacity_min: slot.capacity_min,
          capacity_max: slot.capacity_max,
          element_key:  slot.element_key,
          group_id:     slot.group_id,
          is_visible:   slot.is_visible,
          meta:         slot.meta,
        };
      });

      S.activeLayout = S.layouts.find(function (l) { return l.id === layoutId; }) || { id: layoutId, name: 'Layout #' + layoutId };

      renderAllTables();
      renderZoneLayer();
      updateStatusBar();

      // Update header badge
      var nameEl = document.getElementById('fp-layout-name');
      if (nameEl) nameEl.textContent = S.activeLayout.name;
      var layoutBtn = document.getElementById('fp-layout-btn');
      if (layoutBtn) layoutBtn.classList.add('fp-layout-active');

      // Show banner
      showLayoutBanner(S.activeLayout.name);

      // Re-render panel to update active state
      renderLayoutPanel();
    }).catch(function () {
      showToast('Failed to load layout slots', 'error');
    });
  }

  function loadBaseLayout() {
    apiFetch('GET', 'floor-plans/' + S.floorId + '/furniture').then(function (data) {
      var items = Array.isArray(data) ? data : (data.data || []);
      S.tables = {};
      items.forEach(function (item) { S.tables[item.id] = item; });
      S.activeLayout = null;

      renderAllTables();
      renderZoneLayer();
      updateStatusBar();

      // Update header badge
      var nameEl = document.getElementById('fp-layout-name');
      if (nameEl) nameEl.textContent = 'Base layout';
      var layoutBtn = document.getElementById('fp-layout-btn');
      if (layoutBtn) layoutBtn.classList.remove('fp-layout-active');

      hideLayoutBanner();
      renderLayoutPanel();
    }).catch(function () {
      showToast('Failed to reload base layout', 'error');
    });
  }

  function showLayoutBanner(name) {
    var banner = document.getElementById('fp-layout-banner');
    var nameEl = document.getElementById('fp-layout-banner-name');
    if (banner) banner.hidden = false;
    if (nameEl) nameEl.textContent = name;
  }

  function hideLayoutBanner() {
    var banner = document.getElementById('fp-layout-banner');
    if (banner) banner.hidden = true;
  }

  /* ══════════════════════════════════════════════════════════════════
     API
  ══════════════════════════════════════════════════════════════════ */
  function apiFetch(method, path, body) {
    var opts = {
      method: method,
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': NONCE },
    };
    if (body !== undefined) opts.body = JSON.stringify(body);
    return fetch(API + path, opts).then(function (r) {
      if (!r.ok) return r.json().then(function (e) { throw e; }, function () { throw new Error(r.status); });
      if (r.status === 204) return null;
      return r.json();
    });
  }

  /* ══════════════════════════════════════════════════════════════════
     SECURITY
  ══════════════════════════════════════════════════════════════════ */
  function escHtml(str) {
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  function escAttr(str) { return escHtml(str); }
})();
