@extends('layouts.frontbase')

@section('content')

@include('frontend.includes.page-header', [
    'title' => 'Dining',
    'subtitle' => 'Order in a few taps — send your request by WhatsApp or email.',
    'imageUrl' => ! empty($setting->dining_hero_image)
        ? asset('storage/images/pages/'.$setting->dining_hero_image)
        : null,
])

<section class="dining-page py-100 rpy-70 bg-white rel z-1">
    <div class="dining-page__shell">
        <div class="dining-page__inner">
            @if (! empty($setting->dining_intro))
                <div class="dining-intro text-center mb-45 rmb-35 wow fadeInUp">
                    <div class="dining-intro__inner mx-auto">
                        {!! $setting->dining_intro !!}
                    </div>
                </div>
            @endif

            <p class="text-muted small mb-3 wow fadeInUp">Tap a price for local currency (RWF), or click the price to show or hide RWF next to the dollar amount.</p>

            <script type="application/json" id="dining-menu-data">@json($diningMenuColumns)</script>
            <div id="dining-menu-columns-app" class="dining-menu-columns-app wow fadeInUp">
                <p class="text-center text-muted py-5 mb-0 d-none" id="dining-menu-empty">Menu coming soon.</p>
                <div id="dining-menu-loaded" class="d-none">
                    <div class="dining-menu-columns-grid" id="dining-menu-columns-root" aria-live="polite"></div>
                </div>
            </div>
        </div>
    </div>
</section>

@php
    $waDigits = preg_replace('/\D+/', '', $setting->phone ?? '');
@endphp
<div id="dining-order-dock" class="dining-order-dock d-none" aria-live="polite">
    <div class="dining-order-dock__inner dining-order-dock__inner--wide py-3 px-3 px-md-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div>
                <strong class="dining-order-dock__label d-block mb-0">Your order</strong>
                <span id="dining-order-count" class="dining-order-dock__sub small">0 items</span>
            </div>
            <div class="d-flex flex-wrap gap-2 align-items-end">
                <div>
                    <label class="form-label small mb-1" for="dining-global-time">Needed around</label>
                    <input type="time" class="form-control form-control-sm" id="dining-global-time" style="max-width:9rem">
                </div>
                <div>
                    <label class="form-label small mb-1" for="dining-global-party">Party size</label>
                    <input type="number" class="form-control form-control-sm" id="dining-global-party" min="1" value="2" placeholder="Guests" style="max-width:7rem">
                </div>
            </div>
        </div>
        <div class="row g-3 align-items-start">
            <div class="col-lg-8">
                <div class="dining-order-summary-card rounded-3 border overflow-hidden bg-white text-dark shadow-sm">
                    <div class="dining-order-summary-card__head px-3 py-2 border-bottom">
                        <span class="dining-order-summary-card__title fw-semibold">Order summary</span>
                        <span class="text-muted small ms-2">Review before sending</span>
                    </div>
                    <div class="p-3">
                        <div class="table-responsive dining-order-summary-table-wrap">
                            <table class="table table-sm table-striped align-middle mb-0 dining-order-summary-table">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Item</th>
                                        <th scope="col" class="text-end text-nowrap" style="width:4rem;">Qty</th>
                                        <th scope="col" class="text-end text-nowrap" style="width:7rem;">Total</th>
                                    </tr>
                                </thead>
                                <tbody id="dining-order-table-body"></tbody>
                                <tfoot class="table-group-divider" id="dining-order-table-foot"></tfoot>
                            </table>
                        </div>
                        <label class="form-label small fw-semibold mt-3 mb-1" for="dining-order-additional">Additional requests</label>
                        <textarea class="form-control form-control-sm dining-order-summary-card__textarea" id="dining-order-additional" rows="2" placeholder="Allergies, room number, delivery preference, occasion…"></textarea>
                        <p class="text-muted small mb-0 mt-2" id="dining-order-channel-hint"></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <p class="dining-order-dock__sub small mb-3 mb-lg-2">Send this order to the hotel using the phone number (WhatsApp) and email shown below — the same contact details used across this website.</p>
                <div class="d-flex flex-column gap-2">
                    <button type="button" class="theme-btn btn-sm" id="dining-order-whatsapp"><i class="fab fa-whatsapp me-1"></i> Send via WhatsApp</button>
                    <button type="button" class="theme-btn style-three btn-sm" id="dining-order-email"><i class="far fa-envelope me-1"></i> Send via email</button>
                    <button type="button" class="btn btn-outline-light btn-sm" id="dining-order-clear">Clear order</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="dining-add-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content dining-modal">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" id="dining-modal-dish-name">Add to order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-2">
                <input type="hidden" id="dining-add-id">
                <div class="mb-3">
                    <label class="form-label">Quantity</label>
                    <input type="number" class="form-control" id="dining-add-qty" min="1" value="1">
                </div>
                <div class="mb-3">
                    <label class="form-label">Notes (optional)</label>
                    <textarea class="form-control" id="dining-add-notes" rows="2" placeholder="No onions, extra sauce…"></textarea>
                </div>
                <button type="button" class="theme-btn w-100 mt-2" id="dining-add-confirm">Add to tray</button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var PER = 10;
    var cfg = {
        wa: @json($waDigits),
        email: @json(trim($setting->email ?? '')),
        hotel: @json($setting->company ?? 'Martin Aviator Hotel'),
        displayPhone: @json($setting->phone ?? ''),
        displayEmail: @json(trim($setting->email ?? ''))
    };
    var cart = [];
    var dock = document.getElementById('dining-order-dock');
    var countEl = document.getElementById('dining-order-count');
    var modalEl = document.getElementById('dining-add-modal');
    var modal = null;
    if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        modal = new bootstrap.Modal(modalEl);
    }

    var elJson = document.getElementById('dining-menu-data');
    var columns = [];
    try {
        columns = elJson && elJson.textContent ? JSON.parse(elJson.textContent) : [];
    } catch (e) {
        columns = [];
    }
    var root = document.getElementById('dining-menu-columns-root');
    var emptyEl = document.getElementById('dining-menu-empty');
    var loadedEl = document.getElementById('dining-menu-loaded');
    var pageIndex = [];

    function buildLayout() {
        if (!root) return;
        root.innerHTML = '';
        pageIndex = columns.map(function () { return 0; });
        columns.forEach(function (col, i) {
            var wrap = document.createElement('div');
            wrap.className = 'home-dining-tcol dining-menu-tcol';

            var h4 = document.createElement('h4');
            h4.className = 'home-dining-tcol__title mb-2';
            h4.id = 'dining-col-title-' + i;

            var trOuter = document.createElement('div');
            trOuter.className = 'table-responsive home-dining-tcol__wrap border rounded-3 overflow-hidden bg-white';

            var table = document.createElement('table');
            table.className = 'table table-sm table-striped home-dining-mini-table dining-menu-page-table align-middle mb-0';

            var thead = document.createElement('thead');
            thead.className = 'table-light';
            var trh = document.createElement('tr');
            var th1 = document.createElement('th');
            th1.scope = 'col';
            th1.textContent = 'Item';
            var th2 = document.createElement('th');
            th2.scope = 'col';
            th2.className = 'text-end text-nowrap';
            th2.style.width = '6.5rem';
            th2.textContent = 'Price';
            var th3 = document.createElement('th');
            th3.scope = 'col';
            th3.className = 'text-end text-nowrap';
            th3.style.width = '5rem';
            var sr = document.createElement('span');
            sr.className = 'visually-hidden';
            sr.textContent = 'Add';
            th3.appendChild(sr);
            trh.appendChild(th1);
            trh.appendChild(th2);
            trh.appendChild(th3);
            thead.appendChild(trh);

            var tbody = document.createElement('tbody');
            tbody.id = 'dining-tbody-' + i;

            table.appendChild(thead);
            table.appendChild(tbody);
            trOuter.appendChild(table);

            var pager = document.createElement('div');
            pager.className = 'd-flex flex-wrap align-items-center justify-content-between gap-2 mt-2';
            pager.id = 'dining-pager-' + i;

            wrap.appendChild(h4);
            wrap.appendChild(trOuter);
            wrap.appendChild(pager);
            root.appendChild(wrap);
        });
    }

    function renderCol(idx) {
        var col = columns[idx];
        var tbody = document.getElementById('dining-tbody-' + idx);
        var pager = document.getElementById('dining-pager-' + idx);
        var titleEl = document.getElementById('dining-col-title-' + idx);
        if (!tbody || !pager || !titleEl || !col) return;

        titleEl.textContent = col.label || '';
        var items = col.items || [];
        var totalPages = Math.max(1, Math.ceil(items.length / PER));
        if (pageIndex[idx] >= totalPages) pageIndex[idx] = totalPages - 1;
        var start = pageIndex[idx] * PER;
        var slice = items.slice(start, start + PER);

        tbody.innerHTML = '';
        if (!slice.length) {
            var tr0 = document.createElement('tr');
            var td0 = document.createElement('td');
            td0.colSpan = 3;
            td0.className = 'text-muted text-center py-4 small';
            td0.textContent = 'No dishes in this section yet.';
            tr0.appendChild(td0);
            tbody.appendChild(tr0);
        } else {
            slice.forEach(function (it) {
                var tr = document.createElement('tr');
                var tdItem = document.createElement('td');
                var strong = document.createElement('div');
                strong.className = 'home-dining-mini-table__title fw-semibold';
                strong.textContent = it.title || '';
                tdItem.appendChild(strong);
                if (it.description) {
                    var desc = document.createElement('div');
                    desc.className = 'home-dining-mini-table__desc text-muted small mt-1';
                    desc.textContent = it.description;
                    if (it.descriptionTitle) desc.title = it.descriptionTitle;
                    tdItem.appendChild(desc);
                }
                var tdPrice = document.createElement('td');
                tdPrice.className = 'text-end align-top home-dining-mini-table__price';
                var ph = document.createElement('div');
                ph.innerHTML = it.priceHtml || '';
                tdPrice.appendChild(ph);

                var tdAct = document.createElement('td');
                tdAct.className = 'text-end align-top';
                var b = document.createElement('button');
                b.type = 'button';
                b.className = 'theme-btn style-three btn-sm dining-dish-add';
                b.setAttribute('data-id', String(it.id));
                b.setAttribute('data-title', it.title || '');
                b.setAttribute('data-price', it.priceUsd || '0');
                b.setAttribute('data-price-rwf', it.priceRwfAttr || '');
                b.innerHTML = '<i class="fas fa-plus me-1"></i> Add';
                tdAct.appendChild(b);

                tr.appendChild(tdItem);
                tr.appendChild(tdPrice);
                tr.appendChild(tdAct);
                tbody.appendChild(tr);
            });
        }

        pager.innerHTML = '';
        pager.className = 'd-flex flex-wrap align-items-center justify-content-between gap-2 mt-2';
        if (items.length <= PER) return;

        var info = document.createElement('span');
        info.className = 'text-muted small';
        info.textContent = 'Page ' + (pageIndex[idx] + 1) + ' of ' + totalPages;

        var prev = document.createElement('button');
        prev.type = 'button';
        prev.className = 'btn btn-sm btn-outline-secondary';
        prev.textContent = 'Prev';
        prev.disabled = pageIndex[idx] <= 0;
        prev.addEventListener('click', function () {
            if (pageIndex[idx] > 0) {
                pageIndex[idx]--;
                renderCol(idx);
            }
        });

        var next = document.createElement('button');
        next.type = 'button';
        next.className = 'btn btn-sm btn-outline-secondary';
        next.textContent = 'Next';
        next.disabled = pageIndex[idx] >= totalPages - 1;
        next.addEventListener('click', function () {
            if (pageIndex[idx] < totalPages - 1) {
                pageIndex[idx]++;
                renderCol(idx);
            }
        });

        pager.appendChild(prev);
        pager.appendChild(info);
        pager.appendChild(next);
    }

    var hasItems = columns.some(function (c) { return c.items && c.items.length; });
    if (!columns.length || !hasItems) {
        if (emptyEl) emptyEl.classList.remove('d-none');
    } else {
        if (loadedEl) loadedEl.classList.remove('d-none');
        buildLayout();
        columns.forEach(function (_, i) {
            renderCol(i);
        });
    }

    function save() {
        try { localStorage.setItem('dining_cart', JSON.stringify(cart)); } catch (e) {}
    }
    function load() {
        try {
            var raw = localStorage.getItem('dining_cart');
            if (raw) cart = JSON.parse(raw) || [];
        } catch (e) { cart = []; }
    }
    function moneyUsd(n) {
        var v = Math.round(n * 100) / 100;
        return '$' + v.toFixed(2);
    }

    function lineTotals(l) {
        var unit = parseFloat(String(l.priceUsd || '0').replace(',', '.')) || 0;
        var qty = parseInt(l.qty, 10) || 0;
        var lineUsd = unit * qty;
        var rwfEa = parseInt(String(l.priceRwf || '').replace(/\D/g, ''), 10) || 0;
        var lineRwf = rwfEa ? rwfEa * qty : 0;
        return { unit: unit, qty: qty, lineUsd: lineUsd, lineRwf: lineRwf };
    }

    function cartGrandTotals() {
        var sumUsd = 0;
        var sumRwf = 0;
        cart.forEach(function (l) {
            var t = lineTotals(l);
            sumUsd += t.lineUsd;
            sumRwf += t.lineRwf;
        });
        return { sumUsd: sumUsd, sumRwf: sumRwf };
    }

    function renderOrderSummary() {
        var tbody = document.getElementById('dining-order-table-body');
        var tfoot = document.getElementById('dining-order-table-foot');
        var hint = document.getElementById('dining-order-channel-hint');
        if (!tbody || !tfoot) return;

        tbody.innerHTML = '';
        if (!cart.length) {
            var trE = document.createElement('tr');
            var tdE = document.createElement('td');
            tdE.colSpan = 3;
            tdE.className = 'text-muted text-center py-3 small';
            tdE.textContent = 'No items in your order yet.';
            trE.appendChild(tdE);
            tbody.appendChild(trE);
            tfoot.innerHTML = '';
        } else {
            cart.forEach(function (l) {
                var t = lineTotals(l);
                var tr = document.createElement('tr');
                var tdItem = document.createElement('td');
                var title = document.createElement('div');
                title.className = 'fw-semibold';
                title.textContent = l.title || '';
                tdItem.appendChild(title);
                var unitLine = document.createElement('div');
                unitLine.className = 'text-muted small';
                unitLine.textContent = moneyUsd(t.unit) + ' each' + (l.priceRwf ? ' · ~' + String(l.priceRwf).replace(/\D/g, '') + ' RWF ea.' : '');
                tdItem.appendChild(unitLine);
                if (l.notes) {
                    var note = document.createElement('div');
                    note.className = 'text-muted small fst-italic mt-1';
                    note.textContent = 'Note: ' + l.notes;
                    tdItem.appendChild(note);
                }
                var tdQty = document.createElement('td');
                tdQty.className = 'text-end';
                tdQty.textContent = String(t.qty);
                var tdTot = document.createElement('td');
                tdTot.className = 'text-end fw-semibold text-nowrap';
                tdTot.textContent = moneyUsd(t.lineUsd);
                if (t.lineRwf > 0) {
                    var rwfSub = document.createElement('div');
                    rwfSub.className = 'text-muted small fw-normal';
                    rwfSub.textContent = '~' + t.lineRwf.toLocaleString('en-US') + ' RWF';
                    tdTot.appendChild(rwfSub);
                }
                tr.appendChild(tdItem);
                tr.appendChild(tdQty);
                tr.appendChild(tdTot);
                tbody.appendChild(tr);
            });

            var gt = cartGrandTotals();
            tfoot.innerHTML = '';
            var trF = document.createElement('tr');
            var tdL = document.createElement('th');
            tdL.scope = 'row';
            tdL.colSpan = 2;
            tdL.className = 'text-end border-0';
            tdL.textContent = 'Grand total';
            var tdG = document.createElement('td');
            tdG.className = 'text-end border-0 text-nowrap';
            var strong = document.createElement('strong');
            strong.className = 'text-body';
            strong.textContent = moneyUsd(gt.sumUsd);
            tdG.appendChild(strong);
            if (gt.sumRwf > 0) {
                var rwfTot = document.createElement('div');
                rwfTot.className = 'text-muted small fw-normal';
                rwfTot.textContent = '~' + gt.sumRwf.toLocaleString('en-US') + ' RWF';
                tdG.appendChild(rwfTot);
            }
            trF.appendChild(tdL);
            trF.appendChild(tdG);
            tfoot.appendChild(trF);
        }

        if (hint) {
            var parts = [];
            if (cfg.displayPhone) parts.push('WhatsApp: ' + cfg.displayPhone);
            if (cfg.displayEmail) parts.push('Email: ' + cfg.displayEmail);
            hint.textContent = parts.length ? ('Orders are sent to: ' + parts.join(' · ') + '.') : 'The hotel is still setting up contact details for online orders. Please call the front desk.';
        }
    }

    function refreshDock() {
        var n = cart.reduce(function (a, l) { return a + (l.qty || 0); }, 0);
        if (n > 0) {
            dock.classList.remove('d-none');
            countEl.textContent = n + ' item' + (n === 1 ? '' : 's');
        } else {
            dock.classList.add('d-none');
            countEl.textContent = '0 items';
        }
        renderOrderSummary();
        save();
    }

    function buildMessage() {
        var time = document.getElementById('dining-global-time') ? document.getElementById('dining-global-time').value : '';
        var party = document.getElementById('dining-global-party') ? document.getElementById('dining-global-party').value : '';
        var extraEl = document.getElementById('dining-order-additional');
        var extra = extraEl ? extraEl.value.trim() : '';
        var gt = cartGrandTotals();
        var sep = '----------------------------------------';
        var lines = [];
        lines.push('*' + cfg.hotel + ' — dining order*');
        lines.push('');
        lines.push('ORDER LINES');
        lines.push(sep);
        lines.push('Item | Qty | Unit (USD) | Line total (USD)');
        lines.push(sep);
        cart.forEach(function (l, i) {
            var t = lineTotals(l);
            var row = (i + 1) + '. ' + (l.title || '') + ' | ' + t.qty + ' | ' + moneyUsd(t.unit) + ' | ' + moneyUsd(t.lineUsd);
            lines.push(row);
            if (l.priceRwf) {
                lines.push('   (unit ~' + String(l.priceRwf).replace(/\D/g, '') + ' RWF ea. · line ~' + (t.lineRwf ? t.lineRwf.toLocaleString('en-US') : '—') + ' RWF)');
            }
            if (l.notes) lines.push('   Item note: ' + l.notes);
        });
        lines.push(sep);
        lines.push('GRAND TOTAL (USD): ' + moneyUsd(gt.sumUsd));
        if (gt.sumRwf > 0) lines.push('Approx. grand total (RWF): ' + gt.sumRwf.toLocaleString('en-US'));
        lines.push('');
        if (extra) {
            lines.push('ADDITIONAL REQUESTS');
            lines.push(sep);
            lines.push(extra);
            lines.push('');
        }
        lines.push('SERVICE DETAILS');
        lines.push(sep);
        lines.push('Needed around: ' + (time || '—'));
        lines.push('Party size: ' + (party || '—'));
        lines.push('');
        lines.push('— Sent from the hotel website dining page.');
        return lines.join('\n');
    }

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.dining-dish-add');
        if (!btn) return;
        document.getElementById('dining-add-id').value = btn.getAttribute('data-id');
        document.getElementById('dining-modal-dish-name').textContent = btn.getAttribute('data-title');
        document.getElementById('dining-add-qty').value = '1';
        document.getElementById('dining-add-notes').value = '';
        if (modal) modal.show();
    });

    document.getElementById('dining-add-confirm').addEventListener('click', function () {
        var id = document.getElementById('dining-add-id').value;
        var btn = document.querySelector('.dining-dish-add[data-id="' + id + '"]');
        if (!btn) return;
        var title = btn.getAttribute('data-title');
        var priceUsd = btn.getAttribute('data-price');
        var priceRwf = btn.getAttribute('data-price-rwf') || '';
        var qty = parseInt(document.getElementById('dining-add-qty').value, 10) || 1;
        var notes = document.getElementById('dining-add-notes').value.trim();
        cart.push({ id: id, title: title, priceUsd: priceUsd, priceRwf: priceRwf, qty: qty, notes: notes });
        if (modal) modal.hide();
        refreshDock();
    });
    document.getElementById('dining-order-clear').addEventListener('click', function () {
        cart = [];
        refreshDock();
    });
    document.getElementById('dining-order-whatsapp').addEventListener('click', function () {
        if (!cart.length) {
            alert('Your order is empty. Add dishes from the menu first.');
            return;
        }
        if (!cfg.wa || cfg.wa.length < 8) {
            alert('WhatsApp ordering is unavailable (no hotel phone on file). Please try email or call the hotel directly.');
            return;
        }
        var text = encodeURIComponent(buildMessage());
        window.open('https://wa.me/' + cfg.wa + '?text=' + text, '_blank');
    });
    document.getElementById('dining-order-email').addEventListener('click', function () {
        if (!cart.length) {
            alert('Your order is empty. Add dishes from the menu first.');
            return;
        }
        if (!cfg.email) {
            alert('Email ordering is unavailable (no hotel email on file). Please try WhatsApp or call the hotel directly.');
            return;
        }
        var sub = encodeURIComponent('Dining order — ' + cfg.hotel);
        var body = encodeURIComponent(buildMessage());
        window.location.href = 'mailto:' + cfg.email + '?subject=' + sub + '&body=' + body;
    });
    load();
    refreshDock();
})();
</script>
@endsection
