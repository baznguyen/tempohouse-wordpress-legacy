/* TEMPO House — Public Booking Widget */
(function () {
  'use strict';

  const cfg = thrBooking.config;
  const api = thrBooking.apiUrl;

  let selected = { date: null, time: null, partySize: 2, occasion: 'dinner' };
  let currentLang = 'en';

  // ── Bilingual strings ─────────────────────────────────────────────────────
  const STRINGS = {
    en: {
      // Step 1
      lblDate:           'Date',
      lblGuests:         'Guests',
      lblOccasion:       'Occasion',
      lblAvailableTimes: 'Available times',
      hintSelectDate:    'Select a date to see available times.',
      txtLoading:        'Loading available times…',
      txtNoAvailability: 'No availability for this date.',
      txtUnableToLoad:   'Unable to load times. Please try again.',
      btnContinue:       'Continue →',
      txtGuest:          'guest',
      txtGuests:         'guests',
      // Step 2
      btnBack:                 '← Back',
      ttlYourDetails:          'Your details',
      lblFullName:             'Full name',
      phFullName:              'Your name',
      lblEmail:                'Email',
      phEmail:                 'your@email.com',
      lblPhone:                'Phone / Zalo',
      phPhone:                 '+84 xxx xxx xxx',
      lblSpecialRequests:      'Special requests',
      phSpecialRequests:       'Allergies, high chair, birthday cake…',
      lblLanguage:             'Language preference',
      errNameEmail:            'Please fill in your name and email address.',
      errEmail:                'Please enter a valid email address.',
      btnConfirmReservation:   'Confirm reservation',
      btnConfirming:           'Confirming…',
      errBookingFailed:        'Booking failed. Please try again.',
      errGeneric:              'Something went wrong. Please try again.',
      // Step 3
      ttlConfirmed:   "You're confirmed",
      msgConfirmed:   'Your reservation has been received. Check your email for full details.',
      lblRefCode:     'Reference code',
      noteRefCode:    'Keep your reference code — you may need it to amend or cancel.',
      btnDirections:  'Get directions',
      btnAddGcal:     '+ Add to Google Calendar',
      btnDownloadIcs: 'Download .ics',
      // Waitlist
      ttlWaitlist:     'Join the waitlist',
      msgWaitlist:     "We'll notify you if a table opens up for your preferred date.",
      lblWlName:       'Your name',
      phWlName:        'Full name',
      lblWlEmail:      'Email',
      lblWlPhone:      'Phone',
      lblWlTime:       'Preferred time (optional)',
      phWlTime:        'e.g. 19:30',
      btnJoinWaitlist: 'Join waitlist',
      btnJoining:      'Joining…',
      ttlWlSuccess:    "You're on the waitlist",
      lblWlRef:        'Reference:',
      msgWlSuccess:    "We'll email you if a table becomes available.",
      errWlName:       'Name is required.',
      errWlEmail:      'A valid email is required.',
      errWlFailed:     'Failed to join waitlist.',
    },
    vi: {
      // Step 1
      lblDate:           'Ngày',
      lblGuests:         'Số khách',
      lblOccasion:       'Dịp',
      lblAvailableTimes: 'Khung giờ trống',
      hintSelectDate:    'Chọn ngày để xem khung giờ.',
      txtLoading:        'Đang tải giờ trống…',
      txtNoAvailability: 'Không có chỗ trống ngày này.',
      txtUnableToLoad:   'Không tải được giờ. Vui lòng thử lại.',
      btnContinue:       'Tiếp theo →',
      txtGuest:          'khách',
      txtGuests:         'khách',
      // Step 2
      btnBack:                 '← Quay lại',
      ttlYourDetails:          'Thông tin của bạn',
      lblFullName:             'Họ và tên',
      phFullName:              'Họ và tên',
      lblEmail:                'Email',
      phEmail:                 'email@cua.ban',
      lblPhone:                'Điện thoại / Zalo',
      phPhone:                 '+84 xxx xxx xxx',
      lblSpecialRequests:      'Yêu cầu đặc biệt',
      phSpecialRequests:       'Dị ứng, ghế trẻ em, bánh sinh nhật…',
      lblLanguage:             'Ngôn ngữ ưu tiên',
      errNameEmail:            'Vui lòng điền tên và email.',
      errEmail:                'Vui lòng nhập email hợp lệ.',
      btnConfirmReservation:   'Xác nhận đặt bàn',
      btnConfirming:           'Đang xác nhận…',
      errBookingFailed:        'Đặt bàn thất bại. Vui lòng thử lại.',
      errGeneric:              'Có lỗi xảy ra. Vui lòng thử lại.',
      // Step 3
      ttlConfirmed:   'Đã xác nhận',
      msgConfirmed:   'Đặt bàn của bạn đã được tiếp nhận. Kiểm tra email để xem chi tiết.',
      lblRefCode:     'Mã đặt bàn',
      noteRefCode:    'Lưu mã đặt bàn của bạn — có thể cần dùng để thay đổi hoặc hủy.',
      btnDirections:  'Xem bản đồ',
      btnAddGcal:     '+ Thêm vào Google Calendar',
      btnDownloadIcs: 'Tải file .ics',
      // Waitlist
      ttlWaitlist:     'Đăng ký danh sách chờ',
      msgWaitlist:     'Chúng tôi sẽ thông báo khi có bàn trống theo ngày bạn chọn.',
      lblWlName:       'Họ và tên',
      phWlName:        'Họ và tên',
      lblWlEmail:      'Email',
      lblWlPhone:      'Điện thoại',
      lblWlTime:       'Giờ ưu tiên (tùy chọn)',
      phWlTime:        'vd. 19:30',
      btnJoinWaitlist: 'Đăng ký danh sách chờ',
      btnJoining:      'Đang đăng ký…',
      ttlWlSuccess:    'Bạn đã vào danh sách chờ',
      lblWlRef:        'Mã:',
      msgWlSuccess:    'Chúng tôi sẽ gửi email khi có bàn trống.',
      errWlName:       'Vui lòng nhập tên.',
      errWlEmail:      'Vui lòng nhập email hợp lệ.',
      errWlFailed:     'Không thể đăng ký danh sách chờ.',
    }
  };

  function L(key) {
    var s = STRINGS[currentLang] || STRINGS.en;
    return s[key] !== undefined ? s[key] : (STRINGS.en[key] || key);
  }

  // ── Init ──────────────────────────────────────────────────────────────────
  document.addEventListener('DOMContentLoaded', function () {
    if (!document.getElementById('thr-booking-wrap')) return;
    initPartySizeSelect();
    initOccasionSelect();
    initDateInput();
    bindEvents();
    applyLocale('en');
    populateSummary();
    if (cfg.cancelPolicy) {
      const el = document.getElementById('thr-policy-text');
      if (el) el.textContent = cfg.cancelPolicy;
    }
  });

  // ── Locale switcher ───────────────────────────────────────────────────────
  function applyLocale(lang) {
    currentLang = lang;
    // Text content nodes
    document.querySelectorAll('[data-i18n]').forEach(function(el) {
      var key = el.getAttribute('data-i18n');
      var val = L(key);
      if (val) el.textContent = val;
    });
    // Placeholder attributes
    document.querySelectorAll('[data-ph-i18n]').forEach(function(el) {
      var key = el.getAttribute('data-ph-i18n');
      var val = L(key);
      if (val) el.placeholder = val;
    });
    // Rebuild party size options to update guest/guests label
    rebuildPartySizeSelect();
    // Update summary bar guest text if it's showing
    populateSummary();
  }

  // ── Populate selects from config ──────────────────────────────────────────
  function initPartySizeSelect() {
    rebuildPartySizeSelect();
  }

  function rebuildPartySizeSelect() {
    const sel = document.getElementById('thr-party-size');
    if (!sel) return;
    const current = parseInt(sel.value, 10) || selected.partySize || 2;
    sel.innerHTML = '';
    for (let i = cfg.partySizeMin; i <= cfg.partySizeMax; i++) {
      const opt = document.createElement('option');
      opt.value = i;
      opt.textContent = i + ' ' + (i === 1 ? L('txtGuest') : L('txtGuests'));
      if (i === current) opt.selected = true;
      sel.appendChild(opt);
    }
  }

  function initOccasionSelect() {
    const sel = document.getElementById('thr-occasion');
    if (!sel) return;
    Object.entries(cfg.occasionTypes || {}).forEach(([slug, label]) => {
      const opt = document.createElement('option');
      opt.value = slug;
      opt.textContent = label;
      sel.appendChild(opt);
    });
  }

  function initDateInput() {
    const input = document.getElementById('thr-date');
    if (!input) return;
    const now     = new Date();
    const minDate = new Date(now.getTime() + cfg.advanceMin * 60000);
    const maxDate = new Date(now.getTime() + cfg.advanceMax * 24 * 3600000);
    input.min = formatDate(minDate);
    input.max = formatDate(maxDate);
    input.value = '';
  }

  // ── Event bindings ────────────────────────────────────────────────────────
  function bindEvents() {
    // Language radio
    document.querySelectorAll('input[name="thr-lang"]').forEach(function(radio) {
      radio.addEventListener('change', function() {
        applyLocale(this.value);
      });
    });

    // Date change → fetch slots
    const dateInput = document.getElementById('thr-date');
    if (dateInput) dateInput.addEventListener('change', function () {
      selected.date = this.value;
      selected.time = null;
      document.getElementById('thr-next-1').disabled = true;
      if (selected.date) fetchSlots(selected.date, selected.partySize);
    });

    // Party size change → re-fetch slots
    const sizeSelect = document.getElementById('thr-party-size');
    if (sizeSelect) sizeSelect.addEventListener('change', function () {
      selected.partySize = parseInt(this.value, 10);
      selected.time = null;
      document.getElementById('thr-next-1').disabled = true;
      if (selected.date) fetchSlots(selected.date, selected.partySize);
    });

    // Occasion
    const occSelect = document.getElementById('thr-occasion');
    if (occSelect) occSelect.addEventListener('change', function () {
      selected.occasion = this.value;
    });

    // Step 1 → Step 2
    const next1 = document.getElementById('thr-next-1');
    if (next1) next1.addEventListener('click', function () {
      if (!selected.date || !selected.time) return;
      populateSummary();
      goToStep(2);
    });

    // Back buttons
    document.querySelectorAll('.thr-back-btn').forEach(btn => {
      btn.addEventListener('click', function () {
        goToStep(parseInt(this.dataset.target, 10));
      });
    });

    // Submit
    const submit = document.getElementById('thr-submit');
    if (submit) submit.addEventListener('click', submitBooking);
  }

  // ── Slot fetching ─────────────────────────────────────────────────────────
  function fetchSlots(date, partySize) {
    const container = document.getElementById('thr-time-slots');
    const waitlistSection = document.getElementById('thr-waitlist-section');
    container.innerHTML = '<p class="thr-slots-loading">' + L('txtLoading') + '</p>';
    if (waitlistSection) waitlistSection.style.display = 'none';

    fetch(`${api}availability?date=${date}&party_size=${partySize}`)
      .then(r => r.json())
      .then(data => {
        container.innerHTML = '';
        const availableSlots = (data.slots || []).filter(s => s.available);
        if (availableSlots.length === 0) {
          container.innerHTML = '<p class="thr-hint">' + L('txtNoAvailability') + '</p>';
          showWaitlistSection(date);
          return;
        }
        availableSlots.forEach(slot => {
          const btn = document.createElement('button');
          btn.type = 'button';
          btn.className = 'thr-time-slot';
          btn.textContent = formatTime(slot.time);
          btn.dataset.time = slot.time;
          btn.addEventListener('click', function () {
            document.querySelectorAll('.thr-time-slot').forEach(b => b.classList.remove('thr-time-slot--selected'));
            this.classList.add('thr-time-slot--selected');
            selected.time = slot.time;
            document.getElementById('thr-next-1').disabled = false;
            document.getElementById('thr-next-1').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
          });
          container.appendChild(btn);
        });
      })
      .catch(() => {
        container.innerHTML = '<p class="thr-hint">' + L('txtUnableToLoad') + '</p>';
      });
  }

  // ── Waitlist section ──────────────────────────────────────────────────────
  function showWaitlistSection(date) {
    let section = document.getElementById('thr-waitlist-section');
    if (!section) {
      section = buildWaitlistSection();
      const slotsArea = document.getElementById('thr-time-slots');
      if (slotsArea && slotsArea.parentNode) slotsArea.parentNode.insertBefore(section, slotsArea.nextSibling);
    } else {
      // Update text in existing section for current locale
      section.querySelectorAll('[data-i18n]').forEach(function(el) {
        var key = el.getAttribute('data-i18n');
        var val = L(key);
        if (val) el.textContent = val;
      });
      section.querySelectorAll('[data-ph-i18n]').forEach(function(el) {
        var key = el.getAttribute('data-ph-i18n');
        var val = L(key);
        if (val) el.placeholder = val;
      });
    }
    const wlDate = section.querySelector('[name="wl_date"]');
    if (wlDate) wlDate.value = date;
    section.style.display = '';
  }

  function buildWaitlistSection() {
    const section = document.createElement('div');
    section.id = 'thr-waitlist-section';
    section.style.cssText = 'margin-top:24px;padding:20px;border:1px solid rgba(221,170,98,0.2);border-radius:4px;background:rgba(221,170,98,0.04);';
    section.innerHTML =
      '<h3 style="margin:0 0 8px;font-size:16px;color:#F7F3EE;" data-i18n="ttlWaitlist">' + L('ttlWaitlist') + '</h3>' +
      '<p style="margin:0 0 16px;font-size:14px;color:rgba(247,243,238,0.65);" data-i18n="msgWaitlist">' + L('msgWaitlist') + '</p>' +
      '<div id="thr-wl-form">' +
        '<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">' +
          '<label style="display:block;">' +
            '<span class="thr-field-label" data-i18n="lblWlName">' + L('lblWlName') + '</span> *' +
            '<input type="text" name="wl_name" class="thr-input" placeholder="' + L('phWlName') + '" data-ph-i18n="phWlName" required>' +
          '</label>' +
          '<label style="display:block;">' +
            '<span class="thr-field-label" data-i18n="lblWlEmail">' + L('lblWlEmail') + '</span> *' +
            '<input type="email" name="wl_email" class="thr-input" placeholder="your@email.com" data-ph-i18n="phEmail" required>' +
          '</label>' +
        '</div>' +
        '<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">' +
          '<label style="display:block;">' +
            '<span class="thr-field-label" data-i18n="lblWlPhone">' + L('lblWlPhone') + '</span>' +
            '<input type="tel" name="wl_phone" class="thr-input" placeholder="+84…" data-ph-i18n="phPhone">' +
          '</label>' +
          '<label style="display:block;">' +
            '<span class="thr-field-label" data-i18n="lblWlTime">' + L('lblWlTime') + '</span>' +
            '<input type="text" name="wl_time" class="thr-input" placeholder="' + L('phWlTime') + '" data-ph-i18n="phWlTime">' +
          '</label>' +
        '</div>' +
        '<input type="hidden" name="wl_date">' +
        '<p id="thr-wl-error" style="color:#e74c3c;display:none;font-size:13px;margin:0 0 10px;"></p>' +
        '<button type="button" id="thr-wl-submit" class="thr-btn thr-btn--secondary" style="width:100%;" data-i18n="btnJoinWaitlist">' + L('btnJoinWaitlist') + '</button>' +
      '</div>' +
      '<div id="thr-wl-success" style="display:none;text-align:center;padding:16px 0;">' +
        '<p style="color:#DDAA62;font-size:16px;margin:0 0 4px;" data-i18n="ttlWlSuccess">' + L('ttlWlSuccess') + '</p>' +
        '<p style="color:rgba(247,243,238,0.6);font-size:13px;margin:0;"><span data-i18n="lblWlRef">' + L('lblWlRef') + '</span> <strong id="thr-wl-ref" style="color:#F7F3EE;"></strong></p>' +
        '<p style="color:rgba(247,243,238,0.5);font-size:12px;margin-top:8px;" data-i18n="msgWlSuccess">' + L('msgWlSuccess') + '</p>' +
      '</div>';

    section.querySelector('#thr-wl-submit').addEventListener('click', function () {
      submitWaitlist(section);
    });

    return section;
  }

  function submitWaitlist(section) {
    const btn     = section.querySelector('#thr-wl-submit');
    const errEl   = section.querySelector('#thr-wl-error');
    const name    = section.querySelector('[name="wl_name"]').value.trim();
    const email   = section.querySelector('[name="wl_email"]').value.trim();
    const phone   = section.querySelector('[name="wl_phone"]').value.trim();
    const time    = section.querySelector('[name="wl_time"]').value.trim();
    const date    = section.querySelector('[name="wl_date"]').value;

    errEl.style.display = 'none';
    if (!name)                { errEl.textContent = L('errWlName');  errEl.style.display = 'block'; return; }
    if (!validateEmail(email)){ errEl.textContent = L('errWlEmail'); errEl.style.display = 'block'; return; }

    btn.disabled = true;
    btn.textContent = L('btnJoining');

    fetch(`${api}public/waitlist`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        diner_name:     name,
        diner_email:    email,
        diner_phone:    phone || null,
        diner_lang:     currentLang,
        requested_date: date,
        requested_time: time || null,
        party_size:     selected.partySize,
        occasion:       selected.occasion,
      }),
    })
    .then(async r => {
      const data = await r.json();
      if (!r.ok) throw new Error(data.message || L('errWlFailed'));
      return data;
    })
    .then(data => {
      section.querySelector('#thr-wl-ref').textContent = data.reference_code;
      section.querySelector('#thr-wl-form').style.display = 'none';
      section.querySelector('#thr-wl-success').style.display = 'block';
    })
    .catch(err => {
      errEl.textContent = err.message;
      errEl.style.display = 'block';
      btn.disabled = false;
      btn.textContent = L('btnJoinWaitlist');
    });
  }

  // ── Submission ────────────────────────────────────────────────────────────
  function submitBooking() {
    const btn = document.getElementById('thr-submit');
    const errEl = document.getElementById('thr-error');
    errEl.style.display = 'none';

    const name  = document.getElementById('thr-name').value.trim();
    const email = document.getElementById('thr-email').value.trim();
    const phone = document.getElementById('thr-phone').value.trim();
    const notes = document.getElementById('thr-notes').value.trim();

    if (!name || !email) {
      showError(L('errNameEmail'));
      return;
    }
    if (!validateEmail(email)) {
      showError(L('errEmail'));
      return;
    }

    btn.disabled = true;
    btn.classList.add('thr-btn--loading');
    btn.textContent = L('btnConfirming');

    const localDtStr = `${selected.date}T${selected.time}:00+07:00`;
    const utcDt      = new Date(localDtStr).toISOString().slice(0, 19).replace('T', ' ');

    fetch(`${api}public/booking`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        diner_name:     name,
        diner_email:    email,
        diner_phone:    phone,
        diner_lang:     currentLang,
        reservation_dt: utcDt,
        party_size:     selected.partySize,
        occasion:       selected.occasion,
        notes_diner:    notes,
      }),
    })
    .then(async r => {
      const data = await r.json();
      if (!r.ok) throw new Error(data.message || L('errBookingFailed'));
      return data;
    })
    .then(data => {
      document.getElementById('thr-ref-code').textContent = data.reference_code;

      const detailEl = document.getElementById('thr-confirm-details');
      if (detailEl) {
        const guestText = selected.partySize + ' ' + (selected.partySize === 1 ? L('txtGuest') : L('txtGuests'));
        detailEl.innerHTML =
          '<div>' + formatDateDisplay(selected.date) + ' at ' + formatTime(selected.time) + '</div>' +
          '<div>' + guestText + '</div>' +
          '<div>' + (cfg.occasionTypes?.[selected.occasion] || selected.occasion) + '</div>';
      }
      // Build calendar links
      buildCalendarLinks(selected.date, selected.time, data.reference_code);
      goToStep(3);
    })
    .catch(err => {
      showError(err.message || L('errGeneric'));
      btn.disabled = false;
      btn.classList.remove('thr-btn--loading');
      btn.textContent = L('btnConfirmReservation');
    });
  }

  // ── Helpers ───────────────────────────────────────────────────────────────
  function goToStep(n) {
    document.querySelectorAll('.thr-step').forEach(el => el.classList.add('thr-step--hidden'));
    const target = document.getElementById('thr-step-' + n);
    if (target) {
      target.classList.remove('thr-step--hidden');
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  }

  function populateSummary() {
    const dateEl = document.getElementById('thr-summary-date');
    const timeEl = document.getElementById('thr-summary-time');
    const sizeEl = document.getElementById('thr-summary-size');
    const occEl  = document.getElementById('thr-summary-occasion');
    if (dateEl) dateEl.textContent = selected.date ? formatDateDisplay(selected.date) : '';
    if (timeEl) timeEl.textContent = selected.time ? formatTime(selected.time) : '';
    if (sizeEl && selected.partySize) {
      sizeEl.textContent = selected.partySize + ' ' + (selected.partySize === 1 ? L('txtGuest') : L('txtGuests'));
    }
    if (occEl)  occEl.textContent  = selected.occasion ? (cfg.occasionTypes?.[selected.occasion] || selected.occasion) : '';
  }

  function showError(msg) {
    const el = document.getElementById('thr-error');
    el.textContent = msg;
    el.style.display = 'block';
    el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  }

  function formatDate(d) {
    return d.toISOString().slice(0, 10);
  }

  function formatDateDisplay(str) {
    const d = new Date(str + 'T12:00:00');
    return d.toLocaleDateString('en-GB', { weekday: 'short', day: 'numeric', month: 'long', year: 'numeric' });
  }

  function formatTime(t) {
    const [h, m] = t.split(':').map(Number);
    const ampm = h >= 12 ? 'pm' : 'am';
    const hour  = h % 12 || 12;
    return `${hour}:${String(m).padStart(2, '0')}${ampm}`;
  }

  function validateEmail(e) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(e);
  }


  // ── Calendar links ────────────────────────────────────────────────────────
  function buildCalendarLinks(date, time, refCode) {
    var calSection = document.getElementById('thr-calendar-links');
    var gcalLink   = document.getElementById('thr-gcal-link');
    var icsLink    = document.getElementById('thr-ics-link');
    if (!calSection || !gcalLink || !icsLink) return;

    // Build local datetime strings (GMT+7, no TZ suffix → "floating" for calendar)
    var startLocal = date.replace(/-/g, '') + 'T' + time.replace(':', '') + '00';
    // End = 2h after start
    var endH       = parseInt(time.split(':')[0], 10) + 2;
    var endTime    = String(endH % 24).padStart(2, '0') + time.split(':')[1];
    var endLocal   = date.replace(/-/g, '') + 'T' + endTime + '00';
    if (endH >= 24) {
      // crosses midnight — keep same for simplicity
      endLocal = date.replace(/-/g, '') + 'T235959';
    }

    var title    = encodeURIComponent('Dinner at TEMPO House');
    var location = encodeURIComponent('TEMPO House, Ho Chi Minh City');
    var details  = encodeURIComponent('Reference: ' + refCode + '\nTemple House — Ho Chi Minh City');
    var gcalUrl  = 'https://calendar.google.com/calendar/render?action=TEMPLATE'
                 + '&text=' + title
                 + '&dates=' + startLocal + '/' + endLocal
                 + '&location=' + location
                 + '&details=' + details;
    gcalLink.href = gcalUrl;

    // Build .ics content
    var now  = new Date().toISOString().replace(/[-:]/g, '').split('.')[0] + 'Z';
    var ics  = [
      'BEGIN:VCALENDAR',
      'VERSION:2.0',
      'PRODID:-//TEMPO House//Reservation//EN',
      'BEGIN:VEVENT',
      'UID:' + refCode + '@tempohouse.com.vn',
      'DTSTAMP:' + now,
      'DTSTART;TZID=Asia/Ho_Chi_Minh:' + startLocal,
      'DTEND;TZID=Asia/Ho_Chi_Minh:' + endLocal,
      'SUMMARY:Dinner at TEMPO House',
      'LOCATION:TEMPO House\, Ho Chi Minh City',
      'DESCRIPTION:Reference: ' + refCode,
      'END:VEVENT',
      'END:VCALENDAR',
    ].join('\r\n');
    var blob = new Blob([ics], {type: 'text/calendar'});
    icsLink.href = URL.createObjectURL(blob);

    // Update text for current locale
    gcalLink.textContent  = L('btnAddGcal');
    icsLink.textContent   = L('btnDownloadIcs');
    calSection.style.display = '';
  }

})();