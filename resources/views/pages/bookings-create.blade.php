@extends('layouts.app')

@section('title', 'Нова резервация — '.config('app.name'))

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Нова резервация</h1>
            <p class="text-body-secondary mb-0 small">Изберете бизнес, локация и услуга. След това ползвайте AI препоръки за час или въведете дата и час ръчно.</p>
            <p class="small text-body-secondary mb-0 mt-1">
                <a href="{{ route('home') }}">Начало</a> ·
                <a href="{{ route('venues.index') }}">Локации</a> ·
                <a href="{{ route('bookings.index') }}">Списък резервации</a>
            </p>
        </div>
        <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Към списъка
        </a>
    </div>

    <div class="row g-4 justify-content-center">
        <div class="col-lg-10 col-xl-9">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('bookings.store') }}" id="booking-form">
                        @csrf
                        <div class="mb-3">
                            <label for="business_id" class="form-label">Бизнес <span class="text-danger">*</span></label>
                            <select id="business_id" class="form-select" required aria-describedby="business-help">
                                <option value="">— изберете бизнес —</option>
                                @foreach ($businesses as $business)
                                    <option value="{{ $business->id }}" @selected($prefillBusinessId === (string) $business->id)>{{ $business->name }}</option>
                                @endforeach
                                @if ($orphanVenues->isNotEmpty())
                                    <option value="__orphan__" @selected($prefillBusinessId === '__orphan__')>Локации без бизнес</option>
                                @endif
                            </select>
                            <p id="business-help" class="form-text small mb-0">Филтрира наличните локации за избрания бизнес.</p>
                            <p class="form-text small mb-0 mt-1">
                                <a href="{{ $bookingFlowUrls['newBusinessUrl'] }}" class="link-secondary"><i class="bi bi-plus-lg"></i> Нов бизнес</a>
                            </p>
                        </div>

                        <div class="mb-3">
                            <label for="venue_id" class="form-label">Локация <span class="text-danger">*</span></label>
                            <select name="venue_id" id="venue_id" class="form-select @error('venue_id') is-invalid @enderror" required disabled>
                                <option value="">— първо изберете бизнес —</option>
                            </select>
                            @error('venue_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <p id="booking-new-venue-wrap" class="form-text small mb-0 mt-1">
                                <a id="booking-new-venue-link" href="#" class="link-secondary disabled" tabindex="-1" aria-disabled="true"><i class="bi bi-plus-lg"></i> Нова локация за избрания бизнес</a>
                            </p>
                        </div>

                        <div class="mb-3">
                            <label for="service_id" class="form-label">Услуга <span class="text-danger">*</span></label>
                            <select name="service_id" id="service_id" class="form-select @error('service_id') is-invalid @enderror" required disabled>
                                <option value="">— първо изберете локация —</option>
                            </select>
                            @error('service_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <p id="booking-new-service-wrap" class="form-text small mb-0 mt-1">
                                <a id="booking-new-service-link" href="#" class="link-secondary disabled" tabindex="-1" aria-disabled="true" target="_blank" rel="noopener"><i class="bi bi-plus-lg"></i> Нова услуга за избраната локация</a>
                            </p>
                        </div>

                        <div class="mb-4 p-3 rounded border bg-body-tertiary">
                            <button type="button" id="ai-open-btn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#aiRecommendationsModal" disabled>
                                <i class="bi bi-stars me-1"></i> Отвори AI препоръки за час
                            </button>
                            <p class="form-text small mb-0 mt-2">Налично след избор на локация и услуга. Можете да пропуснете и да въведете дата и час по-долу.</p>
                        </div>

                        <div class="mb-3">
                            <label for="customer_id" class="form-label">Клиент</label>
                            <select name="customer_id" id="customer_id" class="form-select @error('customer_id') is-invalid @enderror">
                                <option value="">— без клиент —</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>{{ $customer->name }}</option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Клиенти се добавят през API <code>POST /api/v1/customers</code> или може да добавим отделна страница по желание.</div>
                        </div>

                        <div class="mb-3">
                            <label for="starts_at" class="form-label">Начален дата и час <span class="text-danger">*</span></label>
                            <input
                                type="datetime-local"
                                name="starts_at"
                                id="starts_at"
                                value="{{ old('starts_at') }}"
                                class="form-control @error('starts_at') is-invalid @enderror"
                                required
                            >
                            @error('starts_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Статус</label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                                <option value="confirmed" @selected(old('status', 'confirmed') === 'confirmed')>Потвърдена</option>
                                <option value="pending" @selected(old('status') === 'pending')>Чакаща</option>
                                <option value="cancelled" @selected(old('status') === 'cancelled')>Отменена</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="notes" class="form-label">Бележки</label>
                            <textarea name="notes" id="notes" rows="2" class="form-control @error('notes') is-invalid @enderror" maxlength="2000">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Запази резервацията
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="aiRecommendationsModal" tabindex="-1" aria-labelledby="aiRecommendationsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="aiRecommendationsModalLabel">AI препоръки</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Затвори"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-body-secondary mb-3 mb-lg-4">
                        Препоръките използват избраните във формата <strong>локация</strong> и <strong>услуга</strong>. Нужен е <code>OPENAI_API_KEY</code> в <code>.env</code>.
                    </p>
                    <ul class="nav nav-tabs mb-3" id="aiRecTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="ai-slots-tab" data-bs-toggle="tab" data-bs-target="#ai-slots-pane" type="button" role="tab" aria-controls="ai-slots-pane" aria-selected="true">Слотове</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="ai-load-tab" data-bs-toggle="tab" data-bs-target="#ai-load-pane" type="button" role="tab" aria-controls="ai-load-pane" aria-selected="false">Натовареност</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="aiRecTabContent">
                        <div class="tab-pane fade show active" id="ai-slots-pane" role="tabpanel" aria-labelledby="ai-slots-tab" tabindex="0">
                            <div id="ai-slots-status" class="small text-body-secondary mb-2"></div>
                            <div id="ai-slots-render"></div>
                        </div>
                        <div class="tab-pane fade" id="ai-load-pane" role="tabpanel" aria-labelledby="ai-load-tab" tabindex="0">
                            <div id="ai-load-status" class="small text-body-secondary mb-2"></div>
                            <div id="ai-load-render"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Затвори</button>
                </div>
            </div>
        </div>
    </div>

    <script type="application/json" id="businesses-tree-data">@json($businessesTreePayload)</script>
    <script type="application/json" id="booking-flow-urls">@json($bookingFlowUrls)</script>
    @push('scripts')
        <script>
            (function () {
                var raw = document.getElementById('businesses-tree-data');
                var tree = raw ? JSON.parse(raw.textContent) : [];
                var flowRaw = document.getElementById('booking-flow-urls');
                var flowUrls = {};
                if (flowRaw) {
                    try {
                        flowUrls = JSON.parse(flowRaw.textContent);
                    } catch (e) {
                        flowUrls = {};
                    }
                }

                var map = {};
                var urlMap = {};
                tree.forEach(function (biz) {
                    (biz.venues || []).forEach(function (row) {
                        map[row.id] = row.services;
                        urlMap[row.id] = row.add_service_url || '';
                    });
                });

                function appendReturnBooking(url) {
                    if (!url || url === '#') {
                        return url;
                    }
                    return url + (url.indexOf('?') >= 0 ? '&' : '?') + 'return=booking';
                }

                function syncNewVenueLink(businessKey) {
                    var link = document.getElementById('booking-new-venue-link');
                    if (!link) {
                        return;
                    }
                    if (!flowUrls.locationTemplate) {
                        link.classList.add('disabled');
                        link.href = '#';
                        link.setAttribute('aria-disabled', 'true');
                        link.setAttribute('tabindex', '-1');
                        return;
                    }
                    if (!businessKey || businessKey === '__orphan__') {
                        link.classList.add('disabled');
                        link.href = '#';
                        link.setAttribute('aria-disabled', 'true');
                        link.setAttribute('tabindex', '-1');
                        return;
                    }
                    link.href = appendReturnBooking(flowUrls.locationTemplate.replace('__BID__', String(businessKey)));
                    link.classList.remove('disabled');
                    link.removeAttribute('aria-disabled');
                    link.removeAttribute('tabindex');
                }

                function syncNewServiceLink(venueId) {
                    var link = document.getElementById('booking-new-service-link');
                    if (!link) {
                        return;
                    }
                    var vKey = venueId ? String(venueId) : '';
                    if (!vKey) {
                        link.classList.add('disabled');
                        link.href = '#';
                        link.setAttribute('aria-disabled', 'true');
                        link.setAttribute('tabindex', '-1');
                        return;
                    }
                    var base = urlMap[vKey] || (flowUrls.serviceTemplate ? flowUrls.serviceTemplate.replace('__VID__', vKey) : '');
                    if (!base) {
                        link.classList.add('disabled');
                        link.href = '#';
                        link.setAttribute('aria-disabled', 'true');
                        link.setAttribute('tabindex', '-1');
                        return;
                    }
                    link.href = appendReturnBooking(base);
                    link.classList.remove('disabled');
                    link.removeAttribute('aria-disabled');
                    link.removeAttribute('tabindex');
                }

                var businessEl = document.getElementById('business_id');
                var venueEl = document.getElementById('venue_id');
                var serviceEl = document.getElementById('service_id');
                var aiOpenBtn = document.getElementById('ai-open-btn');

                function syncAiButton() {
                    if (!aiOpenBtn) return;
                    var ok = venueEl.value && serviceEl.value;
                    aiOpenBtn.disabled = !ok;
                }

                function fillServices(venueId) {
                    serviceEl.innerHTML = '';
                    syncNewServiceLink('');
                    if (!venueId || !map[venueId] || !map[venueId].length) {
                        serviceEl.disabled = true;
                        var o = document.createElement('option');
                        o.value = '';
                        o.textContent = venueId ? 'Няма услуги за тази локация' : '— първо изберете локация —';
                        serviceEl.appendChild(o);
                        syncNewServiceLink(venueId);
                        syncAiButton();
                        return;
                    }
                    serviceEl.disabled = false;
                    var ph = document.createElement('option');
                    ph.value = '';
                    ph.textContent = '— изберете услуга —';
                    serviceEl.appendChild(ph);
                    map[venueId].forEach(function (s) {
                        var opt = document.createElement('option');
                        opt.value = s.id;
                        opt.textContent = s.label;
                        serviceEl.appendChild(opt);
                    });
                    syncNewServiceLink(venueId);
                    syncAiButton();
                }

                function fillVenues(businessKey) {
                    venueEl.innerHTML = '';
                    venueEl.value = '';
                    fillServices('');
                    syncNewVenueLink(businessKey);
                    if (!businessKey) {
                        venueEl.disabled = true;
                        var ox = document.createElement('option');
                        ox.value = '';
                        ox.textContent = '— първо изберете бизнес —';
                        venueEl.appendChild(ox);
                        syncAiButton();
                        return;
                    }
                    var biz = tree.find(function (b) {
                        return String(b.id) === String(businessKey);
                    });
                    if (!biz || !(biz.venues && biz.venues.length)) {
                        venueEl.disabled = true;
                        var oz = document.createElement('option');
                        oz.value = '';
                        oz.textContent = 'Няма локации за този бизнес';
                        venueEl.appendChild(oz);
                        syncAiButton();
                        return;
                    }
                    venueEl.disabled = false;
                    var pv = document.createElement('option');
                    pv.value = '';
                    pv.textContent = '— изберете локация —';
                    venueEl.appendChild(pv);
                    biz.venues.forEach(function (v) {
                        var vo = document.createElement('option');
                        vo.value = String(v.id);
                        vo.textContent = v.name || ('Локация #' + v.id);
                        venueEl.appendChild(vo);
                    });
                    syncAiButton();
                }

                businessEl.addEventListener('change', function () {
                    fillVenues(this.value);
                });

                venueEl.addEventListener('change', function () {
                    fillServices(this.value);
                });

                serviceEl.addEventListener('change', syncAiButton);

                var prefillBiz = @json($prefillBusinessId);
                var oldVenue = @json(old('venue_id'));
                var oldService = @json(old('service_id'));
                if (prefillBiz !== null && prefillBiz !== undefined && String(prefillBiz) !== '') {
                    businessEl.value = String(prefillBiz);
                    fillVenues(String(prefillBiz));
                    if (oldVenue) {
                        venueEl.value = String(oldVenue);
                        fillServices(String(oldVenue));
                        if (oldService) {
                            serviceEl.value = String(oldService);
                        }
                    }
                }
                syncAiButton();
            })();
        </script>
    @endpush

    <script type="application/json" id="ai-ajax-config">@json($aiAjaxConfig)</script>
    @push('scripts')
        <script>
            (function () {
                var cfgEl = document.getElementById('ai-ajax-config');
                var cfg = cfgEl ? JSON.parse(cfgEl.textContent) : {};
                var modalEl = document.getElementById('aiRecommendationsModal');
                if (!modalEl || !cfg.slotsUrl) return;

                var slotsStatus = document.getElementById('ai-slots-status');
                var slotsRender = document.getElementById('ai-slots-render');
                var loadStatus = document.getElementById('ai-load-status');
                var loadRender = document.getElementById('ai-load-render');
                var loadFetched = false;

                function csrfHeaders() {
                    var token = document.querySelector('meta[name="csrf-token"]');
                    return {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token ? token.getAttribute('content') : '',
                        'X-Requested-With': 'XMLHttpRequest'
                    };
                }

                function isoToDatetimeLocal(iso) {
                    var d = new Date(iso);
                    if (isNaN(d.getTime())) return '';
                    var pad = function (n) { return String(n).padStart(2, '0'); };
                    return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate()) + 'T' + pad(d.getHours()) + ':' + pad(d.getMinutes());
                }

                function formatIsoForDisplay(iso, timeZone) {
                    if (!iso) {
                        return '—';
                    }
                    var d = new Date(iso);
                    if (isNaN(d.getTime())) {
                        return String(iso);
                    }
                    try {
                        var parts = new Intl.DateTimeFormat('bg-BG', {
                            timeZone: timeZone || undefined,
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit',
                            hourCycle: 'h23'
                        }).formatToParts(d);
                        var map = {};
                        parts.forEach(function (p) { map[p.type] = p.value; });
                        return (map.day || '') + '.' + (map.month || '') + '.' + (map.year || '') + ' ' + (map.hour || '') + ':' + (map.minute || '');
                    } catch (e) {
                        var pad = function (n) { return String(n).padStart(2, '0'); };
                        return pad(d.getDate()) + '.' + pad(d.getMonth() + 1) + '.' + d.getFullYear() + ' ' + pad(d.getHours()) + ':' + pad(d.getMinutes());
                    }
                }

                function resolveAiVenueTimezone() {
                    var formV = document.getElementById('venue_id');
                    var vid = formV && formV.value ? String(formV.value) : '';
                    var row = cfg.venues && cfg.venues.find(function (v) { return String(v.id) === String(vid); });
                    return row && row.timezone ? row.timezone : undefined;
                }

                function resolveVenueId() {
                    var formV = document.getElementById('venue_id');
                    return formV && formV.value ? String(formV.value) : '';
                }

                function getFormPayload() {
                    var venueId = resolveVenueId();
                    var serviceId = document.getElementById('service_id').value;
                    var startsAt = document.getElementById('starts_at').value;
                    var preferred = startsAt && startsAt.length >= 10 ? startsAt.slice(0, 10) : null;
                    return { venueId: venueId, serviceId: serviceId, preferredDate: preferred };
                }

                function setLoading(el, text) {
                    el.innerHTML = '<div class="d-flex align-items-center gap-2 text-body-secondary"><div class="spinner-border spinner-border-sm" role="status"></div><span>' + text + '</span></div>';
                }

                function fetchSlots() {
                    var p = getFormPayload();
                    if (!p.venueId || !p.serviceId) {
                        slotsStatus.textContent = '';
                        slotsRender.innerHTML = '<div class="alert alert-warning mb-0">Изберете <strong>бизнес</strong>, <strong>локация</strong> и <strong>услуга</strong> във формата — препоръките са за конкретна услуга.</div>';
                        return;
                    }
                    slotsStatus.textContent = '';
                    setLoading(slotsRender, 'Зареждане на препоръки…');
                    var body = { venue_id: parseInt(p.venueId, 10) };
                    if (p.serviceId) body.service_id = parseInt(p.serviceId, 10);
                    if (p.preferredDate) body.preferred_date = p.preferredDate;

                    fetch(cfg.slotsUrl, { method: 'POST', headers: csrfHeaders(), body: JSON.stringify(body) })
                        .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, status: r.status, json: j }; }); })
                        .then(function (res) {
                            if (!res.ok) {
                                var em = res.json.message;
                                if (!em && res.json.errors) {
                                    em = Object.values(res.json.errors).flat().join(' ');
                                }
                                slotsRender.innerHTML = '<div class="alert alert-danger mb-0">' + escapeHtml(em || ('Грешка ' + res.status)) + '</div>';
                                return;
                            }
                            var d = res.json.data || {};
                            slotsStatus.textContent = 'Резултат от модела — проверете часовете преди запис.';
                            slotsRender.innerHTML = renderSlots(d);
                            bindApplyButtons();
                        })
                        .catch(function () {
                            slotsRender.innerHTML = '<div class="alert alert-danger mb-0">Мрежова грешка.</div>';
                        });
                }

                function renderSlots(data) {
                    var tz = resolveAiVenueTimezone();
                    var parts = [];
                    var rec = data.recommended_slots || [];
                    var alt = data.alternatives || [];
                    var ass = data.assumptions || [];
                    if (ass.length) {
                        parts.push('<p class="small text-body-secondary"><strong>Допускания:</strong> ' + ass.map(function (a) { return escapeHtml(String(a)); }).join(' · ') + '</p>');
                    }
                    if (!rec.length && !alt.length) {
                        parts.push('<p class="text-body-secondary mb-0">Няма върнати слотове.</p>');
                        var formV = document.getElementById('venue_id');
                        var vid = formV ? formV.value : '';
                        var venueRow = cfg.venues && cfg.venues.find(function (v) { return String(v.id) === String(vid); });
                        if (venueRow && !venueRow.has_business_hours && venueRow.edit_business_hours_url) {
                            parts.push(
                                '<p class="small mb-0 mt-2">' +
                                '<a class="link-primary" href="' + escapeHtml(venueRow.edit_business_hours_url) + '" target="_blank" rel="noopener">' +
                                'Задайте работни часове за тази локация</a> — така препоръките ще са в рамките на реалното ви отваряне.' +
                                '</p>'
                            );
                        }
                        return parts.join('');
                    }
                    if (rec.length) {
                        parts.push('<h3 class="h6">Препоръчани</h3><ul class="list-group list-group-flush border rounded mb-3">');
                        rec.forEach(function (s) {
                            parts.push(renderSlotRow(s, tz));
                        });
                        parts.push('</ul>');
                    }
                    if (alt.length) {
                        parts.push('<h3 class="h6">Алтернативи</h3><ul class="list-group list-group-flush border rounded">');
                        alt.forEach(function (s) {
                            parts.push(renderSlotRow(s, tz));
                        });
                        parts.push('</ul>');
                    }
                    return parts.join('');
                }

                function escapeHtml(s) {
                    var div = document.createElement('div');
                    div.textContent = s;
                    return div.innerHTML;
                }

                function renderSlotRow(s, tz) {
                    var start = s.start_iso || '';
                    var end = s.end_iso || '';
                    var startDisp = formatIsoForDisplay(start, tz);
                    var endDisp = formatIsoForDisplay(end, tz);
                    var score = s.score_0_to_100 != null ? s.score_0_to_100 : '—';
                    var reason = s.reason ? escapeHtml(String(s.reason)) : '';
                    var localVal = isoToDatetimeLocal(start);
                    var btnAttr = localVal ? ' data-iso="' + escapeHtml(localVal) + '"' : '';
                    var btnDis = localVal ? '' : ' disabled';
                    return '<li class="list-group-item">' +
                        '<div class="d-flex flex-wrap justify-content-between gap-2">' +
                        '<div><div class="fw-semibold">' + escapeHtml(startDisp) + '</div>' +
                        '<div class="small text-body-secondary">до ' + escapeHtml(endDisp) + ' · оценка ' + escapeHtml(String(score)) + '</div>' +
                        (reason ? '<div class="small mt-1">' + reason + '</div>' : '') + '</div>' +
                        '<div><button type="button" class="btn btn-sm btn-outline-primary ai-apply-slot"' + btnAttr + btnDis + '>Приложи във формата</button></div>' +
                        '</div></li>';
                }

                function bindApplyButtons() {
                    document.querySelectorAll('.ai-apply-slot').forEach(function (btn) {
                        btn.addEventListener('click', function () {
                            var v = this.getAttribute('data-iso');
                            if (v) document.getElementById('starts_at').value = v;
                            var vid = resolveVenueId();
                            var formV = document.getElementById('venue_id');
                            if (vid && formV && String(formV.value) !== String(vid)) {
                                formV.value = vid;
                                formV.dispatchEvent(new Event('change', { bubbles: true }));
                            }
                            var m = bootstrap.Modal.getInstance(modalEl);
                            if (m) m.hide();
                        });
                    });
                }

                function fetchLoad() {
                    var p = getFormPayload();
                    if (!p.venueId) {
                        loadStatus.textContent = '';
                        loadRender.innerHTML = '<div class="alert alert-warning mb-0">Изберете <strong>бизнес</strong> и <strong>локация</strong> във формата.</div>';
                        return;
                    }
                    loadStatus.textContent = '';
                    setLoading(loadRender, 'Прогноза…');
                    fetch(cfg.loadUrl, {
                        method: 'POST',
                        headers: csrfHeaders(),
                        body: JSON.stringify({ venue_id: parseInt(p.venueId, 10) })
                    })
                        .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, status: r.status, json: j }; }); })
                        .then(function (res) {
                            if (!res.ok) {
                                var em = res.json.message;
                                if (!em && res.json.errors) {
                                    em = Object.values(res.json.errors).flat().join(' ');
                                }
                                loadRender.innerHTML = '<div class="alert alert-danger mb-0">' + escapeHtml(em || ('Грешка ' + res.status)) + '</div>';
                                return;
                            }
                            var d = res.json.data || {};
                            loadStatus.textContent = 'Ориентировъчна прогноза — не е гаранция.';
                            loadRender.innerHTML = renderLoad(d);
                        })
                        .catch(function () {
                            loadRender.innerHTML = '<div class="alert alert-danger mb-0">Мрежова грешка.</div>';
                        });
                }

                function renderLoad(data) {
                    var tz = resolveAiVenueTimezone();
                    var parts = [];
                    if (data.narrative) {
                        parts.push('<div class="alert alert-info small">' + escapeHtml(String(data.narrative)) + '</div>');
                    }
                    var hf = data.hourly_forecast || [];
                    if (hf.length) {
                        parts.push('<div class="table-responsive"><table class="table table-sm table-bordered"><thead><tr><th>Час</th><th>Натовареност</th><th>Бележка</th></tr></thead><tbody>');
                        hf.forEach(function (row) {
                            parts.push('<tr><td>' + escapeHtml(String(row.hour_local != null ? row.hour_local : '—')) + '</td><td>' + escapeHtml(String(row.expected_load_0_to_100 != null ? row.expected_load_0_to_100 : '—')) + '</td><td class="small">' + escapeHtml(String(row.note || '')) + '</td></tr>');
                        });
                        parts.push('</tbody></table></div>');
                    }
                    var peaks = data.peak_windows || [];
                    if (peaks.length) {
                        parts.push('<h3 class="h6 mt-3">Пикове</h3><ul class="small">');
                        peaks.forEach(function (w) {
                            var fromD = formatIsoForDisplay(w.from_iso || '', tz);
                            var toD = formatIsoForDisplay(w.to_iso || '', tz);
                            parts.push('<li>' + escapeHtml(fromD) + ' — ' + escapeHtml(toD) + ' (интензитет ' + escapeHtml(String(w.intensity_0_to_100 != null ? w.intensity_0_to_100 : '—')) + ')</li>');
                        });
                        parts.push('</ul>');
                    }
                    var quiet = data.quiet_windows || [];
                    if (quiet.length) {
                        parts.push('<h3 class="h6 mt-3">По-спокойни периоди</h3><ul class="small">');
                        quiet.forEach(function (w) {
                            var fromD = formatIsoForDisplay(w.from_iso || '', tz);
                            var toD = formatIsoForDisplay(w.to_iso || '', tz);
                            parts.push('<li>' + escapeHtml(fromD) + ' — ' + escapeHtml(toD) + ' (интензитет ' + escapeHtml(String(w.intensity_0_to_100 != null ? w.intensity_0_to_100 : '—')) + ')</li>');
                        });
                        parts.push('</ul>');
                    }
                    if (!parts.length) parts.push('<p class="text-body-secondary mb-0">Няма данни за показване.</p>');
                    return parts.join('');
                }

                modalEl.addEventListener('shown.bs.modal', function () {
                    loadFetched = false;
                    loadRender.innerHTML = '';
                    loadStatus.textContent = '';
                    var slotsTabBtn = document.getElementById('ai-slots-tab');
                    if (slotsTabBtn && typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                        bootstrap.Tab.getOrCreateInstance(slotsTabBtn).show();
                    }
                    fetchSlots();
                });

                var tabList = document.getElementById('aiRecTab');
                if (tabList) {
                    tabList.addEventListener('shown.bs.tab', function (e) {
                        if (e.target.id !== 'ai-load-tab') return;
                        if (loadFetched) return;
                        loadFetched = true;
                        fetchLoad();
                    });
                }
            })();
        </script>
    @endpush
@endsection
