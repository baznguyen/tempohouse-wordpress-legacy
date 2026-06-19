/* global thrBooking */
(function () {
  'use strict';

  // ── State ────────────────────────────────────────────────────────────────────

  var S = {
    step:          'landing',   // landing | booking | contact | review | confirm
    guests:        2,
    date:          null,        // 'YYYY-MM-DD'
    period:        '',          // session slug; set by defaultSession() on config load
    time:          null,        // 'HH:MM'
    privateRoom:   false,
    name:          '',
    email:         '',
    phone:         '',
    useZalo:       false,
    area:          '',
    dietary:       '',
    occasion:      '',
    notes:         '',
    referral:      '',
    config:        null,        // from /public/config
    availDates:    {},          // {'YYYY-MM-DD': 'available'|'full'|'past'|'closed'}
    availDatesKey: null,        // 'YYYY-MM-sessionslug' — cache key; re-fetch when changed
    slots:         [],          // [{time, available, scarce}]
    bookingRef:    null,        // from confirmed booking
    loadedMonth:   null,        // 'YYYY-MM' currently shown in month rail
  };

  // ── DOM refs ─────────────────────────────────────────────────────────────────

  var $ = function (id) { return document.getElementById(id); };

  var elRoot       = $('thr-booking-root');
  var elHeader     = $('thr-header');
  var elHeaderBack = $('thr-header-back');
  var elHeaderTitle= $('thr-header-title');
  var elHeaderStep = $('thr-header-step');
  var elFooter     = $('thr-widget-footer');
  var elFooterCta  = $('footer-cta');

  // Step panels
  var panels = {
    landing:     $('step-landing'),
    booking:     $('step-booking'),
    preferences: $('step-preferences'),
    contact:     $('step-contact'),
    review:      $('step-review'),
    confirm:     $('step-confirm'),
  };

  // Booking step
  var elGuestsVal     = $('guests-val');
  var elGuestsDec     = $('guests-dec');
  var elGuestsInc     = $('guests-inc');
  var elPrivateWrap   = $('private-room-wrap');
  var elPrivateChk    = $('private-room-chk');
  var elDateRail      = $('date-rail');
  var elTimeGrid      = $('time-grid');

  // Contact step
  var elInputName     = $('input-name');
  var elInputEmail    = $('input-email');
  var elInputPhone    = $('input-phone');
  var elInputZalo     = $('input-zalo');
  var elInputArea     = $('input-area');
  var elInputDietary  = $('input-dietary');
  var elInputOccasion = $('input-occasion');
  var elInputNotes    = $('input-notes');
  var elInputReferral = $('input-referral');

  // Review step
  var elConfirmError  = $('confirm-error');
  var elConfirmBtn    = $('confirm-btn');

  // Confirm screen
  var elConfirmHero   = $('confirm-hero');
  var elConfirmDate   = $('confirm-date');
  var elConfirmTime   = $('confirm-time');
  var elConfirmGuests = $('confirm-guests');
  var elConfirmRef    = $('confirm-ref');

  // Landing
  var elBtnBook       = $('btn-book');
  var elBtnModify     = $('btn-modify');
  var elLookupWrap    = $('lookup-wrap');
  var elLookupEmail   = $('lookup-email');
  var elLookupBtn     = $('lookup-btn');
  var elLookupResult  = $('lookup-result');

  // ── Step navigation ──────────────────────────────────────────────────────────

  var STEP_ORDER = ['landing', 'booking', 'preferences', 'contact', 'review', 'confirm'];
  var STEP_TITLES = {
    landing:     'Reservation',
    booking:     'Book a Table',
    preferences: 'Your Preferences',
    contact:     'Your Details',
    review:      'Review Booking',
    confirm:     'Confirmed',
  };
  var STEP_NUMS = { booking: '1/4', preferences: '2/4', contact: '3/4', review: '4/4' };

  // Emoji map for occasion chips (display only — not stored)
  var OCCASION_EMOJI = {
    'birthday':    '🎂',
    'anniversary': '💍',
    'corporate':   '💼',
    'date-night':  '🍷',
    'celebration': '🥂',
    'farewell':    '✈️',
    'proposal':    '💐',
    'baby-shower': '🍼',
    'custom':      '✏️',
  };

  function goTo(step) {
    Object.keys(panels).forEach(function (key) {
      var el = panels[key];
      if (key === step) {
        el.classList.remove('thr-widget__step--hidden');
        el.classList.add('is-active');
      } else {
        el.classList.add('thr-widget__step--hidden');
        el.classList.remove('is-active');
      }
    });

    S.step = step;

    // Header
    elHeaderTitle.textContent = STEP_TITLES[step] || 'Reservation';
    var stepNum = STEP_NUMS[step];
    if (stepNum) {
      elHeaderStep.textContent = stepNum;
      elHeaderStep.hidden = false;
    } else {
      elHeaderStep.hidden = true;
    }
    elHeaderBack.hidden = (step === 'landing' || step === 'confirm');

    // Footer
    updateFooter();

    // Scroll top
    var content = $('thr-widget-content');
    if (content) content.scrollTop = 0;

    // Step-specific init
    if (step === 'booking')     initBookingStep();
    if (step === 'preferences') initPreferencesStep();
    if (step === 'review')      populateReview();
  }

  function goBack() {
    var idx = STEP_ORDER.indexOf(S.step);
    if (idx > 0) goTo(STEP_ORDER[idx - 1]);
  }

  function updateFooter() {
    var step = S.step;
    if (step === 'landing' || step === 'review' || step === 'confirm') {
      elFooter.classList.add('thr-widget-footer--hidden');
      return;
    }
    elFooter.classList.remove('thr-widget-footer--hidden');

    if (step === 'booking') {
      var ready = !!(S.date && S.time);
      elFooterCta.textContent = ready ? 'Set Preferences →' : 'Select a date & time';
      elFooterCta.disabled = !ready;
    } else if (step === 'preferences') {
      elFooterCta.textContent = 'Add Your Details →';
      elFooterCta.disabled = false;
    } else if (step === 'contact') {
      elFooterCta.textContent = 'Review Booking →';
      elFooterCta.disabled = false;
    }
  }

  // ── Config fetch ─────────────────────────────────────────────────────────────

  function fetchConfig() {
    return fetch(thrBooking.apiUrl + '/public/config')
      .then(function (r) { return r.json(); })
      .then(function (cfg) {
        S.config = cfg;
        // Update private room sub-label
        if (cfg.private_room_min_party && cfg.private_room_max_party) {
          var sub = elPrivateWrap && elPrivateWrap.querySelector('.thr-checkbox-label__sub');
          if (sub) {
            sub.textContent = 'For parties of ' + cfg.private_room_min_party + '–' + cfg.private_room_max_party + ' guests';
          }
        }
        // Render session chips + pick default session based on current time
        if (cfg.periods && Object.keys(cfg.periods).length) {
          if (!S.period) S.period = defaultSession(cfg.periods);
          renderSessions(cfg.periods);
        }
      })
      .catch(function () {/* fallback: use PHP-rendered defaults */});
  }

  // ── Stepper ──────────────────────────────────────────────────────────────────

  function updateStepper() {
    var min = S.config ? (S.config.party_size_min || 1) : 1;
    var max = S.config ? (S.config.party_size_max || 20) : 20;
    elGuestsVal.textContent = S.guests;
    elGuestsDec.disabled = (S.guests <= min);
    elGuestsInc.disabled = (S.guests >= max);

    var threshold = S.config ? (S.config.private_room_min_party || 12) : 12;
    var showPrivate = S.guests >= threshold;
    elPrivateWrap.classList.toggle('thr-private-room--hidden', !showPrivate);
    elPrivateWrap.classList.toggle('thr-private-room--visible', showPrivate);
    if (!showPrivate) {
      elPrivateChk.checked = false;
      S.privateRoom = false;
    }

    // If date already selected, reload slots (party size affects availability)
    if (S.date) loadSlots(S.date, S.period);
  }

  // ── Sessions (replaces period toggle) ────────────────────────────────────────

  function renderSessions(periods) {
    var rail = $('session-rail');
    if (!rail || !periods || typeof periods !== 'object') return;
    var keys = Object.keys(periods);
    if (keys.length === 0) return;

    rail.innerHTML = '';
    keys.forEach(function (slug) {
      var p = periods[slug];
      var btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'thr-session-chip' + (slug === S.period ? ' thr-session-chip--active' : '');
      btn.dataset.session = slug;
      btn.setAttribute('aria-pressed', slug === S.period ? 'true' : 'false');
      btn.innerHTML =
        '<span class="thr-session-chip__name">' + (p.label || capitalise(slug)) + '</span>' +
        '<span class="thr-session-chip__time">' + formatSessionRange(p.start, p.end) + '</span>';
      rail.appendChild(btn);
    });
  }

  function setSession(slug) {
    S.period = slug;
    S.time   = null;

    // Update active chip
    var rail = $('session-rail');
    if (rail) {
      rail.querySelectorAll('.thr-session-chip').forEach(function (chip) {
        var active = chip.dataset.session === slug;
        chip.classList.toggle('thr-session-chip--active', active);
        chip.setAttribute('aria-pressed', active ? 'true' : 'false');
      });
    }

    // Invalidate cache + re-fetch dates for new session
    if (S.loadedMonth) {
      S.availDatesKey = null;
      var parts = S.loadedMonth.split('-');
      fetchAvailDates(parseInt(parts[0], 10), parseInt(parts[1], 10));
    }
    if (S.date) loadSlots(S.date, slug);
    updateFooter();
  }

  // Pick the session that's currently active or next upcoming based on local time.
  function defaultSession(periods) {
    var keys = Object.keys(periods);
    if (!keys.length) return '';
    var now = new Date();
    var cur = now.getHours() * 60 + now.getMinutes();

    // Currently active session
    for (var i = 0; i < keys.length; i++) {
      var p = periods[keys[i]];
      if (cur >= timeToMin(p.start) && cur < timeToMin(p.end)) return keys[i];
    }
    // Next upcoming session today
    for (var i = 0; i < keys.length; i++) {
      if (cur < timeToMin(periods[keys[i]].start)) return keys[i];
    }
    // All sessions passed — default to the last one (Dinner)
    return keys[keys.length - 1];
  }

  function timeToMin(hhmm) {
    if (!hhmm) return 0;
    var p = hhmm.split(':');
    return parseInt(p[0], 10) * 60 + parseInt(p[1] || '0', 10);
  }

  function formatSessionRange(start, end) {
    if (!start || !end) return '';
    return formatTime(start) + '–' + formatTime(end);
  }


  // ── Preferences step ──────────────────────────────────────────────────────────

  function initPreferencesStep() {
    // Occasion chips are rendered once from config; re-render only if grid is empty
    var grid = $('occasion-grid');
    if (grid && !grid.querySelector('.thr-occasion-chip')) {
      var occasions = S.config ? (S.config.occasion_types || {}) : {};
      renderOccasionChips(occasions);
    }
    // Section chips are server-rendered (PHP) — just sync active state
    syncSectionChips();
  }

  function renderOccasionChips(occasions) {
    var grid = $('occasion-grid');
    if (!grid) return;
    grid.innerHTML = '';
    var keys = Object.keys(occasions);

    keys.forEach(function (slug) {
      var label  = occasions[slug];
      var emoji  = OCCASION_EMOJI[slug] || '✨';
      var isCustom = slug === 'custom';
      var btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'thr-occasion-chip' +
        (S.occasion === slug ? ' thr-occasion-chip--active' : '') +
        (isCustom ? ' thr-occasion-chip--custom' : '');
      btn.dataset.occasion = slug;
      btn.setAttribute('aria-pressed', S.occasion === slug ? 'true' : 'false');

      if (isCustom) {
        var inp = document.createElement('input');
        inp.type = 'text';
        inp.className = 'thr-custom-input';
        inp.id = 'input-custom-occasion';
        inp.placeholder = 'Describe your occasion…';
        inp.maxLength = 80;
        inp.value = S.occasion && S.occasion !== 'custom' && !OCCASION_EMOJI[S.occasion]
          ? S.occasion : '';
        inp.addEventListener('input', function () { S.occasion = inp.value || 'custom'; });
        inp.addEventListener('click', function (e) { e.stopPropagation(); });
        btn.innerHTML = '<span class="thr-occasion-chip__emoji">' + emoji + '</span>';
        btn.appendChild(inp);
        var lbl = document.createElement('span');
        lbl.className = 'thr-occasion-chip__label';
        lbl.textContent = label;
        btn.appendChild(lbl);
      } else {
        btn.innerHTML =
          '<span class="thr-occasion-chip__emoji">' + emoji + '</span>' +
          '<span class="thr-occasion-chip__label">' + label + '</span>';
      }
      grid.appendChild(btn);
    });
  }

  function setOccasion(slug) {
    S.occasion = slug;
    var grid = $('occasion-grid');
    if (!grid) return;
    grid.querySelectorAll('.thr-occasion-chip').forEach(function (chip) {
      var active = chip.dataset.occasion === slug;
      chip.classList.toggle('thr-occasion-chip--active', active);
      chip.setAttribute('aria-pressed', active ? 'true' : 'false');
      // For custom: deselect by clearing the input if we picked something else
      if (!active && chip.dataset.occasion === 'custom') {
        var inp = chip.querySelector('.thr-custom-input');
        if (inp) inp.value = '';
      }
    });
  }

  function syncSectionChips() {
    var rail = $('section-rail');
    if (!rail) return;
    // Default to 'any' if nothing selected
    if (!S.area) S.area = 'any';
    rail.querySelectorAll('.thr-pref-chip').forEach(function (chip) {
      var active = chip.dataset.section === S.area;
      chip.classList.toggle('thr-pref-chip--active', active);
      chip.setAttribute('aria-pressed', active ? 'true' : 'false');
    });
  }

  function setSection(slug) {
    S.area = slug;
    syncSectionChips();
  }

  // ── Month selector ────────────────────────────────────────────────────────────

  function renderMonthRail() {
    var rail = $('month-rail');
    if (!rail) return;

    var today = new Date();
    var advMax = S.config ? (S.config.booking_advance_max || 60) : 60;
    var maxDate = new Date(today.getFullYear(), today.getMonth(), today.getDate() + advMax);

    // Build month list: current month → month containing maxDate
    var months = [];
    var cur = new Date(today.getFullYear(), today.getMonth(), 1);
    while (cur <= maxDate) {
      months.push({
        yr:  cur.getFullYear(),
        mo:  cur.getMonth() + 1,
        key: cur.getFullYear() + '-' + pad2(cur.getMonth() + 1),
      });
      cur.setMonth(cur.getMonth() + 1);
    }

    // Default loadedMonth to current if not set or not in list
    if (!S.loadedMonth || !months.some(function (m) { return m.key === S.loadedMonth; })) {
      S.loadedMonth = months[0].key;
    }

    var MON = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    rail.innerHTML = '';
    months.forEach(function (m) {
      var btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'thr-month-chip' + (m.key === S.loadedMonth ? ' thr-month-chip--active' : '');
      btn.dataset.month = m.key;
      btn.setAttribute('aria-pressed', m.key === S.loadedMonth ? 'true' : 'false');
      btn.innerHTML =
        '<span class="thr-month-chip__mon">' + MON[m.mo - 1] + '</span>' +
        '<span class="thr-month-chip__yr">'  + m.yr           + '</span>';
      rail.appendChild(btn);
    });
  }

  function selectMonth(monthKey) {
    S.loadedMonth   = monthKey;
    S.date          = null;
    S.time          = null;
    S.availDatesKey = null;

    // Update chip active state
    var rail = $('month-rail');
    if (rail) {
      rail.querySelectorAll('.thr-month-chip').forEach(function (chip) {
        var active = chip.dataset.month === monthKey;
        chip.classList.toggle('thr-month-chip--active', active);
        chip.setAttribute('aria-pressed', active ? 'true' : 'false');
      });
    }

    var parts = monthKey.split('-');
    fetchAvailDates(parseInt(parts[0], 10), parseInt(parts[1], 10));
    updateFooter();
  }


  // ── Date rail ────────────────────────────────────────────────────────────────

  function initBookingStep() {
    renderMonthRail();

    // Period default set in fetchConfig(); fall back to last session if still blank
    if (!S.period && S.config && S.config.periods) {
      var keys = Object.keys(S.config.periods);
      if (keys.length) S.period = defaultSession(S.config.periods);
    }

    var today = new Date();
    if (!S.loadedMonth) {
      S.loadedMonth = today.getFullYear() + '-' + pad2(today.getMonth() + 1);
    }
    var parts = S.loadedMonth.split('-');
    var yr = parseInt(parts[0], 10);
    var mo = parseInt(parts[1], 10);
    var cacheKey = S.loadedMonth + '-' + S.period;

    if (S.availDatesKey !== cacheKey) {
      S.availDatesKey = cacheKey;
      fetchAvailDates(yr, mo);
    } else {
      renderDateRail(yr, mo, S.availDates);
    }
  }

  function fetchAvailDates(yr, mo) {
    elDateRail.innerHTML = '<div class="thr-skeleton" style="height:72px;width:100%;border-radius:26px;flex-shrink:0"></div>';
    var url = thrBooking.apiUrl + '/public/available-dates?year=' + yr + '&month=' + mo + '&party_size=' + S.guests + '&period=' + encodeURIComponent(S.period);
    fetch(url)
      .then(function (r) { return r.json(); })
      .then(function (data) {
        S.availDates = data.dates || {};
        renderDateRail(yr, mo, S.availDates);
      })
      .catch(function () {
        elDateRail.innerHTML = '<span class="thr-time-empty">Could not load dates. Please try again.</span>';
      });
  }

  function renderDateRail(yr, mo, dates) {
    elDateRail.innerHTML = '';
    var today = new Date();
    today.setHours(0, 0, 0, 0);
    var daysInMonth = new Date(yr, mo, 0).getDate();
    var chipCount = 0;

    var DAY = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
    var MON = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

    for (var d = 1; d <= daysInMonth; d++) {
      var dateStr = yr + '-' + pad2(mo) + '-' + pad2(d);
      var state   = dates[dateStr] || 'unavailable';
      var dt      = new Date(yr, mo - 1, d);

      // Skip past dates — no greyed-out waste of space
      if (dt < today || state === 'past') continue;

      chipCount++;
      var btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'thr-date-chip';
      btn.dataset.date = dateStr;
      btn.setAttribute('aria-label', formatDateFull(dateStr));

      if (state === 'closed' || state === 'unavailable') {
        btn.classList.add('thr-date-chip--closed');
        btn.disabled = true;
      } else if (state === 'full') {
        btn.classList.add('thr-date-chip--full');
      } else {
        btn.classList.add('thr-date-chip--available');
      }

      if (S.date === dateStr) {
        btn.classList.remove('thr-date-chip--available', 'thr-date-chip--full');
        btn.classList.add('thr-date-chip--selected');
        btn.setAttribute('aria-pressed', 'true');
      }

      btn.innerHTML =
        '<span class="thr-date-chip__day">' + DAY[dt.getDay()] + '</span>' +
        '<span class="thr-date-chip__num">' + d + '</span>' +
        '<span class="thr-date-chip__mon">' + MON[mo - 1] + '</span>' +
        (state === 'available' && S.date !== dateStr ? '<span class="thr-date-chip__dot"></span>' : '');

      elDateRail.appendChild(btn);
    }

    if (chipCount === 0) {
      elDateRail.innerHTML = '<span class="thr-time-empty">No available dates this month.</span>';
      return;
    }

    // Scroll selected chip into view, or first available
    var sel = elDateRail.querySelector('.thr-date-chip--selected');
    if (sel) {
      sel.scrollIntoView({ block: 'nearest', inline: 'center', behavior: 'smooth' });
    } else {
      var first = elDateRail.querySelector('.thr-date-chip--available');
      if (first) first.scrollIntoView({ block: 'nearest', inline: 'start', behavior: 'smooth' });
    }
  }

  function selectDate(dateStr) {
    S.date = dateStr;
    S.time = null;

    // Update chip selection
    elDateRail.querySelectorAll('.thr-date-chip').forEach(function (btn) {
      var isSelected = btn.dataset.date === dateStr;
      btn.classList.toggle('thr-date-chip--selected', isSelected);
      btn.classList.toggle('thr-date-chip--available', !isSelected && btn.classList.contains('thr-date-chip--available'));
      btn.setAttribute('aria-pressed', isSelected ? 'true' : 'false');
    });

    loadSlots(dateStr, S.period);
    updateFooter();
  }

  // ── Slot loading ─────────────────────────────────────────────────────────────

  function loadSlots(date, period) {
    elTimeGrid.innerHTML = '<div class="thr-skeleton" style="height:44px;border-radius:50px"></div>';
    var url = thrBooking.apiUrl + '/availability?date=' + date + '&party_size=' + S.guests + '&period=' + (period || S.period);
    fetch(url)
      .then(function (r) { return r.json(); })
      .then(function (data) { renderSlots(data.slots || []); })
      .catch(function () {
        elTimeGrid.innerHTML = '<span class="thr-time-empty">Could not load times. Please try again.</span>';
      });
  }

  function renderSlots(slots) {
    S.slots = slots;
    elTimeGrid.innerHTML = '';

    if (!slots.length) {
      elTimeGrid.innerHTML = '<span class="thr-time-empty">No available times for this date.</span>';
      return;
    }

    var unavailCount = 0;
    slots.forEach(function (slot) {
      if (!slot.available) unavailCount++;
    });
    var allUnavail = unavailCount === slots.length;

    slots.forEach(function (slot, i) {
      var btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'thr-time-pill';
      btn.dataset.time = slot.time;
      btn.setAttribute('aria-label', formatTime(slot.time));

      var displayTime = formatTime(slot.time);

      if (!slot.available) {
        btn.classList.add('thr-time-pill--unavailable');
        btn.disabled = true;
        btn.innerHTML = '<span>' + displayTime + '</span>';
      } else if (slot.scarce) {
        btn.classList.add('thr-time-pill--scarce');
        btn.innerHTML = '<span>' + displayTime + '</span><span class="thr-time-pill__dot"></span>';
      } else {
        btn.innerHTML = '<span>' + displayTime + '</span>';
      }

      if (S.time === slot.time && slot.available) {
        btn.classList.add('thr-time-pill--selected');
        btn.setAttribute('aria-pressed', 'true');
      }

      elTimeGrid.appendChild(btn);
    });

    // Show alternatives strip if all slots unavailable
    if (allUnavail && S.date) {
      fetchAlternatives(S.date, slots[0].time);
    }
  }

  function selectTime(time) {
    S.time = time;
    elTimeGrid.querySelectorAll('.thr-time-pill').forEach(function (btn) {
      var isSel = btn.dataset.time === time;
      btn.classList.toggle('thr-time-pill--selected', isSel);
      btn.setAttribute('aria-pressed', isSel ? 'true' : 'false');
    });
    updateFooter();
    if (elFooterCta) {
      elFooterCta.classList.remove('thr-btn--pulse');
      void elFooterCta.offsetWidth; // reflow to restart animation
      elFooterCta.classList.add('thr-btn--pulse');
      setTimeout(function () { elFooterCta.classList.remove('thr-btn--pulse'); }, 600);
    }
  }

  // ── Alternatives ─────────────────────────────────────────────────────────────

  function fetchAlternatives(date, time) {
    var url = thrBooking.apiUrl + '/public/alternatives?date=' + date + '&time=' + encodeURIComponent(time) + '&party_size=' + S.guests + '&period=' + S.period;
    fetch(url)
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (data.alternatives && data.alternatives.length) {
          renderAlternatives(data.alternatives);
        }
      });
  }

  function renderAlternatives(alts) {
    var existing = elTimeGrid.nextElementSibling;
    if (existing && existing.classList.contains('thr-alternatives')) existing.remove();

    var strip = document.createElement('div');
    strip.className = 'thr-alternatives';
    strip.innerHTML = '<span class="thr-alternatives__label">Other available times</span>';
    var list = document.createElement('div');
    list.className = 'thr-alternatives__list';

    alts.forEach(function (alt) {
      var pill = document.createElement('button');
      pill.type = 'button';
      pill.className = 'thr-alt-pill';
      pill.innerHTML =
        '<span class="thr-alt-pill__date">' + formatDateShort(alt.date) + '</span>' +
        '<span class="thr-alt-pill__time">' + formatTime(alt.time) + '</span>';
      pill.addEventListener('click', function () {
        S.date = alt.date;
        S.time = null;
        // Re-render rail for this date
        var parts = alt.date.split('-');
        var yr = parseInt(parts[0], 10);
        var mo = parseInt(parts[1], 10);
        var monthKey = yr + '-' + pad2(mo);
        if (S.loadedMonth !== monthKey) {
          S.loadedMonth = monthKey;
          fetchAvailDates(yr, mo);
        } else {
          renderDateRail(yr, mo, S.availDates);
          selectDate(alt.date);
        }
      });
      list.appendChild(pill);
    });

    strip.appendChild(list);
    elTimeGrid.insertAdjacentElement('afterend', strip);
  }

  // ── Contact form validation ───────────────────────────────────────────────────

  function validateContact() {
    var ok = true;

    function setError(inputId, errorId, condition, msg) {
      var input = $(inputId);
      var errEl = $(errorId);
      if (!input) return;
      var field = input.closest ? input.closest('.thr-field') : null;
      if (condition) {
        if (field) field.classList.add('thr-field--error');
        if (errEl) { errEl.textContent = msg; errEl.hidden = false; }
        ok = false;
      } else {
        if (field) field.classList.remove('thr-field--error');
        if (errEl) errEl.hidden = true;
      }
    }

    var name = elInputName ? elInputName.value.trim() : '';
    var email = elInputEmail ? elInputEmail.value.trim() : '';

    setError('input-name', 'error-name', name.length < 2, 'Please enter your full name.');
    setError('input-email', 'error-email', !isValidEmail(email), 'Please enter a valid email address.');

    S.name    = name;
    S.email   = email;
    S.phone   = elInputPhone ? elInputPhone.value.trim() : '';
    S.useZalo = elInputZalo ? elInputZalo.checked : false;
    S.area    = elInputArea ? elInputArea.value : '';
    S.dietary = elInputDietary ? elInputDietary.value : '';
    S.occasion= elInputOccasion ? elInputOccasion.value : '';
    S.notes   = elInputNotes ? elInputNotes.value.trim() : '';
    S.referral= elInputReferral ? elInputReferral.value : '';

    return ok;
  }

  // ── Review population ─────────────────────────────────────────────────────────

  function populateReview() {
    setText('review-date',   formatDateFull(S.date));
    setText('review-time',   formatTime(S.time));
    setText('review-guests', S.guests + (S.guests === 1 ? ' guest' : ' guests') + (S.privateRoom ? ' — Private Room' : ''));
    setText('review-name',   S.name);
    setText('review-email',  S.email);

    var phoneRow = $('review-phone-row');
    if (S.phone) {
      setText('review-phone', '+84 ' + S.phone);
      if (phoneRow) phoneRow.hidden = false;
    } else {
      if (phoneRow) phoneRow.hidden = true;
    }

    // Optional section
    var hasOpt = S.dietary || (S.area && S.area !== 'any') || S.occasion;
    var optSection = $('review-optional-section');
    if (optSection) optSection.hidden = !hasOpt;

    showReviewOptRow('review-dietary-row',  'review-dietary',  S.dietary);

    // Seating area — look up label from config
    var areaLabel = '';
    if (S.area && S.area !== 'any' && S.config && S.config.seating_sections) {
      areaLabel = S.config.seating_sections[S.area] || formatArea(S.area);
    }
    showReviewOptRow('review-area-row', 'review-area', areaLabel);

    // Occasion — look up label from config; for custom use the typed value
    var occasionLabel = '';
    if (S.occasion) {
      if (S.occasion === 'custom') {
        var customInp = $('input-custom-occasion');
        occasionLabel = customInp ? customInp.value : 'Custom occasion';
      } else if (S.config && S.config.occasion_types) {
        var emoji = OCCASION_EMOJI[S.occasion] || '';
        var label = S.config.occasion_types[S.occasion];
        occasionLabel = label ? (emoji ? emoji + ' ' + label : label) : capitalise(S.occasion);
      } else {
        occasionLabel = capitalise(S.occasion);
      }
    }
    showReviewOptRow('review-occasion-row', 'review-occasion', occasionLabel);

    var notesWrap = $('review-notes-wrap');
    if (notesWrap) notesWrap.hidden = !S.notes;
    if (S.notes) setText('review-notes', S.notes);
  }

  function showReviewOptRow(rowId, valId, value) {
    var row = $(rowId);
    if (!row) return;
    if (value) {
      setText(valId, value);
      row.hidden = false;
    } else {
      row.hidden = true;
    }
  }

  // ── Submit booking ────────────────────────────────────────────────────────────

  function submitBooking() {
    elConfirmBtn.disabled = true;
    elConfirmBtn.textContent = 'Confirming…';
    elConfirmError.hidden = true;

    var payload = {
      diner_name:     S.name,
      diner_email:    S.email,
      diner_phone:    S.phone ? '+84' + S.phone.replace(/\D/g,'') : '',
      reservation_dt: S.date + ' ' + S.time + ':00',
      party_size:     S.guests,
      private_room:   S.privateRoom ? 1 : 0,
      area_label:     S.area && S.area !== 'any' ? S.area : '',
      dietary_notes:  S.dietary,
      occasion:       (function () {
        if (!S.occasion) return '';
        if (S.occasion === 'custom') {
          var inp = document.getElementById('input-custom-occasion');
          return inp ? inp.value : '';
        }
        return S.occasion;
      }()),
      notes_diner:    S.notes,
      referral_source:S.referral,
      diner_lang:     (thrBooking.defaultLang || 'vi'),
    };

    fetch(thrBooking.apiUrl + '/public/booking', {
      method:  'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce':   thrBooking.nonce,
      },
      body: JSON.stringify(payload),
    })
      .then(function (r) { return r.json().then(function (d) { return { status: r.status, data: d }; }); })
      .then(function (res) {
        if (res.status >= 400) {
          var msg = (res.data && (res.data.message || (res.data.data && res.data.data.message))) || 'Booking failed. Please try again.';
          elConfirmError.textContent = msg;
          elConfirmError.hidden = false;
          elConfirmBtn.disabled = false;
          elConfirmBtn.textContent = 'Confirm';
          return;
        }
        S.bookingRef = res.data.booking_ref || res.data.reference || '';
        showConfirmation(res.data);
      })
      .catch(function () {
        elConfirmError.textContent = 'Network error. Please check your connection and try again.';
        elConfirmError.hidden = false;
        elConfirmBtn.disabled = false;
        elConfirmBtn.textContent = 'Confirm';
      });
  }

  // ── Confirmation screen ───────────────────────────────────────────────────────

  function showConfirmation(data) {
    // Hero image
    var heroImages = (thrBooking.heroImages || {});
    var heroUrl = heroImages[S.period] || heroImages['default'] || '';
    if (elConfirmHero && heroUrl) {
      elConfirmHero.style.backgroundImage = 'url(' + heroUrl + ')';
    }

    // Countdown
    var countdown = $('confirm-countdown');
    if (countdown && S.date) {
      var diff = daysBetween(new Date(), new Date(S.date));
      if (diff === 0) countdown.textContent = 'Today!';
      else if (diff === 1) countdown.textContent = 'Tomorrow';
      else countdown.textContent = 'In ' + diff + ' days';
    }

    // Sub-heading
    var sub = $('confirm-sub');
    if (sub) sub.textContent = 'See you ' + formatDateFull(S.date);

    // Grid values
    setText('confirm-date',   formatDateFull(S.date));
    setText('confirm-time',   formatTime(S.time));
    setText('confirm-guests', S.guests + (S.guests === 1 ? ' person' : ' people'));
    setText('confirm-ref',    data.booking_ref || data.reference || 'TH-' + Math.floor(1000 + Math.random() * 9000));

    goTo('confirm');
  }

  // ── Copy reference ────────────────────────────────────────────────────────────

  function bindCopyRef() {
    var btn = $('copy-ref-btn');
    if (!btn) return;
    btn.addEventListener('click', function () {
      var ref = elConfirmRef ? elConfirmRef.textContent : '';
      if (!ref) return;
      if (navigator.clipboard) {
        navigator.clipboard.writeText(ref).then(function () { flashCopied(btn); });
      } else {
        // Fallback
        var ta = document.createElement('textarea');
        ta.value = ref; document.body.appendChild(ta); ta.select();
        document.execCommand('copy'); document.body.removeChild(ta);
        flashCopied(btn);
      }
    });
  }

  function flashCopied(btn) {
    var orig = btn.innerHTML;
    btn.innerHTML = '✓';
    btn.style.color = '#7C3B3B';
    setTimeout(function () { btn.innerHTML = orig; btn.style.color = ''; }, 1500);
  }

  // ── Calendar add ──────────────────────────────────────────────────────────────

  function addToCalendar() {
    if (!S.date || !S.time) return;
    var start = S.date.replace(/-/g, '') + 'T' + S.time.replace(':', '') + '00';
    var cfg   = S.config || {};
    var dur   = cfg.default_duration || 120;
    var endDt = new Date(S.date + 'T' + S.time + ':00+07:00');
    endDt.setMinutes(endDt.getMinutes() + dur);
    var end   = endDt.toISOString().replace(/[-:]/g, '').replace(/\.\d{3}/, '');
    var venueName = elRoot ? (elRoot.dataset.venueName || 'TEMPO House') : 'TEMPO House';
    var gcUrl = 'https://calendar.google.com/calendar/render?action=TEMPLATE' +
      '&text=' + encodeURIComponent('Dinner at ' + venueName) +
      '&dates=' + start + '%2B0700/' + end +
      '&details=' + encodeURIComponent('Reservation ref: ' + (S.bookingRef || '')) +
      '&location=' + encodeURIComponent(thrBooking.venueAddress || '');
    window.open(gcUrl, '_blank', 'noopener');
  }

  // ── Lookup (modify/cancel) ────────────────────────────────────────────────────

  function toggleLookup() {
    var isOpen = elLookupWrap.classList.toggle('is-open');
    elBtnModify.textContent = isOpen ? 'Cancel' : 'Modify / Cancel';
    if (isOpen && elLookupEmail) elLookupEmail.focus();
    elLookupResult.innerHTML = '';
  }

  function runLookup() {
    var email = elLookupEmail ? elLookupEmail.value.trim() : '';
    if (!isValidEmail(email)) {
      elLookupResult.innerHTML = '<p class="thr-field__error" style="display:block">Please enter a valid email.</p>';
      return;
    }
    elLookupResult.innerHTML = '<span class="thr-skeleton" style="height:36px;display:block;border-radius:8px;margin-top:8px"></span>';
    fetch(thrBooking.apiUrl + '/public/reservation-lookup?email=' + encodeURIComponent(email))
      .then(function (r) { return r.json(); })
      .then(function (data) { renderLookupResult(data); })
      .catch(function () {
        elLookupResult.innerHTML = '<p class="thr-field__error" style="display:block">Could not look up reservations. Please try again.</p>';
      });
  }

  function renderLookupResult(data) {
    var reservations = data.reservations || [];
    if (!reservations.length) {
      elLookupResult.innerHTML = '<p class="thr-field__hint" style="margin-top:8px">No upcoming reservations found for this email.</p>';
      return;
    }
    var html = '<div style="margin-top:8px;display:flex;flex-direction:column;gap:8px">';
    reservations.forEach(function (r) {
      html += '<div class="thr-lookup-result-row" style="background:var(--thr-surface-sunken,#F0EBE3);border-radius:10px;padding:12px 14px;font-size:0.84rem">' +
        '<strong style="font-family:var(--font-display)">' + formatDateFull(r.date) + ' • ' + formatTime(r.time) + '</strong>' +
        '<span style="float:right;opacity:.65">' + r.party_size + ' guests</span>' +
        '<br><span style="opacity:.65">Ref: ' + r.booking_ref + '</span>' +
        '<br><a href="' + thrBooking.homeUrl + '?thr_modify=' + encodeURIComponent(r.booking_ref) + '" class="thr-btn thr-btn--secondary thr-btn--sm" style="margin-top:8px;display:inline-block;text-decoration:none">Modify / Cancel →</a>' +
        '</div>';
    });
    html += '</div>';
    elLookupResult.innerHTML = html;
  }

  // ── Utilities ─────────────────────────────────────────────────────────────────

  function pad2(n) { return n < 10 ? '0' + n : String(n); }

  function isValidEmail(e) { return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(e); }

  function capitalise(str) { return str.charAt(0).toUpperCase() + str.slice(1); }

  function formatArea(slug) {
    var map = { indoor: 'Indoor', outdoor: 'Outdoor / Terrace', bar: 'Bar seating', private: 'Private area' };
    return map[slug] || capitalise(slug);
  }

  function setText(id, val) {
    var el = $(id);
    if (el) el.textContent = val || '';
  }

  function formatTime(hhmm) {
    if (!hhmm) return '';
    var parts = hhmm.split(':');
    var h = parseInt(parts[0], 10);
    var m = parts[1] || '00';
    var ampm = h >= 12 ? 'PM' : 'AM';
    var h12 = h % 12 || 12;
    return h12 + ':' + m + ' ' + ampm;
  }

  function formatDateShort(dateStr) {
    if (!dateStr) return '';
    var d = new Date(dateStr + 'T12:00:00');
    var days = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
    return days[d.getDay()] + ' ' + d.getDate();
  }

  function formatDateFull(dateStr) {
    if (!dateStr) return '';
    var d = new Date(dateStr + 'T12:00:00');
    var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    return d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
  }

  function daysBetween(a, b) {
    var msPerDay = 86400000;
    var aDay = new Date(a.getFullYear(), a.getMonth(), a.getDate());
    var bDay = new Date(b.getFullYear(), b.getMonth(), b.getDate());
    return Math.round((bDay - aDay) / msPerDay);
  }

  // ── Event wiring ──────────────────────────────────────────────────────────────

  function bindEvents() {

    // Header back
    if (elHeaderBack) {
      elHeaderBack.addEventListener('click', goBack);
    }

    // Footer CTA
    if (elFooterCta) {
      elFooterCta.addEventListener('click', function () {
        if (S.step === 'booking') {
          goTo('preferences');
        } else if (S.step === 'preferences') {
          goTo('contact');
        } else if (S.step === 'contact') {
          if (validateContact()) goTo('review');
        }
      });
    }

    // Landing — Book new
    if (elBtnBook) {
      elBtnBook.addEventListener('click', function () { goTo('booking'); });
    }

    // Landing — Modify/Cancel toggle
    if (elBtnModify) {
      elBtnModify.addEventListener('click', toggleLookup);
    }

    // Lookup submit
    if (elLookupBtn) {
      elLookupBtn.addEventListener('click', runLookup);
    }
    if (elLookupEmail) {
      elLookupEmail.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') runLookup();
      });
    }

    // Stepper
    if (elGuestsDec) {
      elGuestsDec.addEventListener('click', function () {
        var min = S.config ? (S.config.party_size_min || 1) : 1;
        if (S.guests > min) { S.guests--; updateStepper(); }
      });
    }
    if (elGuestsInc) {
      elGuestsInc.addEventListener('click', function () {
        var max = S.config ? (S.config.party_size_max || 20) : 20;
        if (S.guests < max) { S.guests++; updateStepper(); }
      });
    }

    // Private room checkbox
    if (elPrivateChk) {
      elPrivateChk.addEventListener('change', function () {
        S.privateRoom = this.checked;
      });
    }

    // Month rail — delegated
    var monthRail = $('month-rail');
    if (monthRail) {
      monthRail.addEventListener('click', function (e) {
        var chip = e.target.closest('[data-month]');
        if (chip) selectMonth(chip.dataset.month);
      });
    }

    // Session rail — delegated
    var sessionRail = $('session-rail');
    if (sessionRail) {
      sessionRail.addEventListener('click', function (e) {
        var chip = e.target.closest('[data-session]');
        if (chip && !chip.disabled) setSession(chip.dataset.session);
      });
    }

    // Date chips — delegated
    if (elDateRail) {
      elDateRail.addEventListener('click', function (e) {
        var chip = e.target.closest('.thr-date-chip');
        if (chip && !chip.disabled && chip.dataset.date) selectDate(chip.dataset.date);
      });
    }

    // Time pills — delegated
    if (elTimeGrid) {
      elTimeGrid.addEventListener('click', function (e) {
        var pill = e.target.closest('.thr-time-pill');
        if (pill && !pill.disabled && pill.dataset.time) selectTime(pill.dataset.time);
      });
    }

    // Seating section chips — preferences step
    var sectionRail = $('section-rail');
    if (sectionRail) {
      sectionRail.addEventListener('click', function (e) {
        var chip = e.target.closest('.thr-pref-chip');
        if (chip) setSection(chip.dataset.section);
      });
    }

    // Occasion chips — preferences step
    var occasionGrid = $('occasion-grid');
    if (occasionGrid) {
      occasionGrid.addEventListener('click', function (e) {
        var chip = e.target.closest('.thr-occasion-chip');
        if (!chip) return;
        var slug = chip.dataset.occasion;
        if (slug === 'custom') {
          setOccasion('custom');
          var inp = chip.querySelector('.thr-custom-input');
          if (inp) setTimeout(function () { inp.focus(); }, 50);
        } else {
          setOccasion(slug);
        }
      });
    }

    // Preferences skip button
    var skipBtn = $('pref-skip-btn');
    if (skipBtn) {
      skipBtn.addEventListener('click', function () {
        S.area = 'any';
        S.occasion = '';
        goTo('contact');
      });
    }

    // Confirm button (review screen)
    if (elConfirmBtn) {
      elConfirmBtn.addEventListener('click', submitBooking);
    }

    // Confirm screen — copy ref
    bindCopyRef();

    // Confirm screen — actions
    var calBtn = $('confirm-cal-btn');
    if (calBtn) calBtn.addEventListener('click', addToCalendar);

    var callBtn = $('confirm-call-btn');
    if (callBtn) {
      callBtn.addEventListener('click', function () {
        var phone = thrBooking.venuePhone;
        if (phone) window.location.href = 'tel:' + phone;
      });
    }

    var pinBtn = $('confirm-pin-btn');
    if (pinBtn) {
      pinBtn.addEventListener('click', function () {
        var addr = thrBooking.venueAddress;
        if (addr) window.open('https://maps.google.com?q=' + encodeURIComponent(addr), '_blank', 'noopener');
      });
    }

    var modifyBtn = $('confirm-modify-btn');
    if (modifyBtn) {
      modifyBtn.addEventListener('click', function () { goTo('landing'); });
    }

    var homeBtn = $('confirm-home-btn');
    if (homeBtn) {
      homeBtn.addEventListener('click', function () {
        window.location.href = thrBooking.homeUrl || '/';
      });
    }

    // Intercept WP admin bar if present so it doesn't obscure header
    var adminBar = document.getElementById('wpadminbar');
    if (adminBar) adminBar.style.position = 'relative';
  }

  // ── Boot ──────────────────────────────────────────────────────────────────────

  function init() {
    fetchConfig().then(function () {
      updateStepper();
      bindEvents();
      goTo('landing');
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();
