/**
 * Multi-step room booking wizard (Stay → Guest → Submit).
 */
(function () {
    'use strict';

    var maxStep = 3;
    var step = 1;

    function wizardRoot() {
        return document.getElementById('ma-booking-wizard');
    }

    function getCart() {
        var root = wizardRoot();
        if (!root) return [];
        if (!root._maBwCart) root._maBwCart = [];
        return root._maBwCart;
    }

    function getRooms() {
        var root = wizardRoot();
        if (!root) return [];
        try {
            return JSON.parse(root.getAttribute('data-rooms') || '[]');
        } catch (e) {
            return [];
        }
    }

    function el(id) {
        return document.getElementById(id);
    }

    function roomById(id) {
        var n = parseInt(id, 10);
        if (!n) return null;
        return getRooms().find(function (r) { return Number(r.id) === n; }) || null;
    }

    function todayStr() {
        return new Date().toISOString().slice(0, 10);
    }

    function defaultCheckout(fromCheckin) {
        var d = new Date(fromCheckin + 'T12:00:00');
        d.setDate(d.getDate() + 2);
        return d.toISOString().slice(0, 10);
    }

    function nightsBetween(a, b) {
        if (!a || !b) return 0;
        var t0 = new Date(a + 'T12:00:00').getTime();
        var t1 = new Date(b + 'T12:00:00').getTime();
        var n = Math.round((t1 - t0) / 86400000);
        return n > 0 ? n : 0;
    }

    function money(n) {
        return '$' + (Number(n) || 0).toFixed(2);
    }

    function escapeHtml(s) {
        var d = document.createElement('div');
        d.textContent = s || '';
        return d.innerHTML;
    }

    function persistSlug(slug) {
        if (slug && window.maBookingRoom) window.maBookingRoom.set(slug);
    }

    function resolveInitialSlug() {
        var root = wizardRoot();
        var selectedRoomSlug = root ? (root.getAttribute('data-selected-room-slug') || '') : '';
        try {
            var p = new URLSearchParams(window.location.search);
            return p.get('room') || selectedRoomSlug || (window.maBookingRoom ? window.maBookingRoom.get() : '');
        } catch (e) {
            return selectedRoomSlug;
        }
    }

    function selectedRoomIdFromRoot() {
        var root = wizardRoot();
        return root ? parseInt(root.getAttribute('data-selected-room-id') || '0', 10) : 0;
    }

    function syncLineItemsInput() {
        var lineItemsInput = el('ma-bw-line-items');
        var checkin = el('bw-checkin');
        var checkout = el('bw-checkout');
        if (!lineItemsInput || !checkin || !checkout) return;

        var ci = checkin.value;
        var co = checkout.value;
        var nights = nightsBetween(ci, co) || 1;
        var cart = getCart();
        lineItemsInput.value = JSON.stringify(cart.map(function (item) {
            return {
                room_id: item.room_id,
                room_name: item.room_name,
                check_in: ci,
                check_out: co,
                nights: nights
            };
        }));
    }

    function rebuildCartFromHiddenInput() {
        var lineItemsInput = el('ma-bw-line-items');
        if (!lineItemsInput || !lineItemsInput.value || lineItemsInput.value === '[]') return false;

        try {
            var parsed = JSON.parse(lineItemsInput.value);
            if (!Array.isArray(parsed) || !parsed.length) return false;

            var cart = getCart();
            cart.length = 0;
            parsed.forEach(function (line) {
                var rid = parseInt(line.room_id, 10);
                var r = roomById(rid);
                cart.push({
                    room_id: rid,
                    room_name: line.room_name || (r ? r.name : 'Room'),
                    slug: r ? r.slug : '',
                    price: r ? r.price : 0
                });
            });
            return cart.length > 0;
        } catch (e) {
            return false;
        }
    }

    function ensureCartPopulated() {
        var cart = getCart();
        if (cart.length > 0) return true;

        if (rebuildCartFromHiddenInput()) return true;

        seedInitialRoom();
        syncLineItemsInput();
        return getCart().length > 0;
    }

    function cartTotal() {
        var checkin = el('bw-checkin');
        var checkout = el('bw-checkout');
        if (!checkin || !checkout) return 0;
        var nights = nightsBetween(checkin.value, checkout.value) || 1;
        return getCart().reduce(function (sum, item) {
            return sum + (item.price || 0) * nights;
        }, 0);
    }

    function renderCart() {
        var lineList = el('bw-line-list');
        var checkin = el('bw-checkin');
        var checkout = el('bw-checkout');
        if (!lineList || !checkin || !checkout) return;

        var cart = getCart();
        lineList.innerHTML = '';
        if (cart.length === 0) {
            lineList.innerHTML = '<p class="text-muted small mb-0">No rooms added yet. Use “Add room” or continue from a room page to add one automatically.</p>';
            return;
        }

        cart.forEach(function (item, idx) {
            var row = document.createElement('div');
            row.className = 'ma-bw__line-item';
            row.innerHTML =
                '<div class="ma-bw__line-info">' +
                '<strong>' + escapeHtml(item.room_name) + '</strong>' +
                '<span class="small text-muted d-block">' + escapeHtml(checkin.value) + ' → ' + escapeHtml(checkout.value) + '</span>' +
                '</div>' +
                '<button type="button" class="ma-bw__line-remove btn btn-sm btn-link text-danger" data-idx="' + idx + '">Remove</button>';
            lineList.appendChild(row);
        });

        lineList.querySelectorAll('.ma-bw__line-remove').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var i = parseInt(btn.getAttribute('data-idx'), 10);
                var cartRef = getCart();
                cartRef.splice(i, 1);
                renderCart();
                renderSummary();
                syncLineItemsInput();
            });
        });
    }

    function formatDateShort(iso) {
        if (!iso) return '';
        try {
            return new Date(iso + 'T12:00:00').toLocaleDateString(undefined, { weekday: 'short', month: 'short', day: 'numeric' });
        } catch (e) {
            return iso;
        }
    }

    function renderSummary() {
        var checkin = el('bw-checkin');
        var checkout = el('bw-checkout');
        var nightsBadge = el('bw-nights-badge');
        var summaryBody = el('bw-summary-body');
        var summaryTotal = el('bw-summary-total');
        var continueBtn = el('bw-continue');
        var summaryContinue = el('bw-summary-continue');
        if (!checkin || !checkout || !summaryBody || !summaryTotal) return;

        var cart = getCart();
        var ci = checkin.value;
        var co = checkout.value;
        var nights = nightsBetween(ci, co);
        var adults = el('bw-adults');
        var children = el('bw-children');
        var roomCount = el('bw-room-count');

        if (nightsBadge) {
            if (nights > 0) {
                nightsBadge.hidden = false;
                nightsBadge.textContent = nights + ' night' + (nights === 1 ? '' : 's');
            } else {
                nightsBadge.hidden = true;
            }
        }

        var html = '';
        if (ci && co) {
            html += '<p class="ma-bw__summary-dates">' + formatDateShort(ci) + ' → ' + formatDateShort(co);
            if (roomCount) html += ' · ' + roomCount.value + ' room(s)';
            html += '</p>';
        }
        if (cart.length) {
            cart.forEach(function (item) {
                html += '<div class="ma-bw__summary-line">' +
                    '<strong>' + escapeHtml(item.room_name) + '</strong>' +
                    '<span class="small d-block text-muted">' + escapeHtml(ci) + ' → ' + escapeHtml(co) + '</span>' +
                    '<span class="small">' + (item.price > 0 ? money(item.price * (nights || 1)) : 'Rate on request') + '</span>' +
                    '</div>';
            });
            if (nights > 0 && adults) {
                html += '<p class="small text-muted mb-0">' + nights + ' night(s) · ' + adults.value + ' adult(s), ' + (children ? children.value : 0) + ' child(ren)</p>';
            }
        } else {
            html = '<p class="text-muted small mb-0">Add dates and at least one room.</p>';
        }

        summaryBody.innerHTML = html;
        summaryTotal.textContent = money(cartTotal());

        var canContinue = ci && co && nights > 0 && (step !== 1 || cart.length > 0);
        if (summaryContinue) summaryContinue.disabled = !canContinue;
        if (continueBtn && step === 1) continueBtn.disabled = !canContinue;
    }

    function directPayEnabled() {
        var root = wizardRoot();
        return root && root.getAttribute('data-direct-pay') === '1';
    }

    function clearHotelChannelSelection() {
        var channelInput = el('bw-confirmation-channel');
        if (channelInput) channelInput.value = '';
    }

    function renderReview() {
        var reviewBody = el('bw-review-body');
        var checkin = el('bw-checkin');
        var checkout = el('bw-checkout');
        if (!reviewBody || !checkin || !checkout) return;

        var name = el('bw-name');
        var phone = el('bw-phone');
        var email = el('bw-email');
        var country = el('bw-country');
        var nights = nightsBetween(checkin.value, checkout.value);
        var html = '<dl class="ma-bw__review-dl">';
        html += '<dt>Stay</dt><dd>' + escapeHtml(checkin.value) + ' → ' + escapeHtml(checkout.value) + ' (' + nights + ' nights)</dd>';
        getCart().forEach(function (item) {
            html += '<dt>Room</dt><dd>' + escapeHtml(item.room_name) + '</dd>';
        });
        if (name && name.value) {
            html += '<dt>Guest</dt><dd>' + escapeHtml(name.value) + '<br>' + escapeHtml(phone ? phone.value : '') + '<br>' + escapeHtml(email ? email.value : '') + '<br>' + escapeHtml(country ? country.value : '') + '</dd>';
        }
        html += '<dt>Estimated total</dt><dd>' + money(cartTotal()) + '</dd>';
        html += '</dl>';
        reviewBody.innerHTML = html;
    }

    function selectPaymentPath(timing, clearHotel) {
        var panelDirect = el('bw-panel-direct');
        var panelHotel = el('bw-panel-hotel');
        var timingInput = el('bw-payment-timing');
        var channelInput = el('bw-confirmation-channel');
        var root = wizardRoot();
        if (!timingInput || !channelInput || !root) return;

        if (timing !== 'pay_direct' && timing !== 'pay_at_hotel') {
            timingInput.value = '';
            channelInput.value = '';
            root.querySelectorAll('[data-bw-path]').forEach(function (btn) {
                btn.classList.add('style-three');
            });
            if (panelDirect) panelDirect.hidden = true;
            if (panelHotel) panelHotel.hidden = true;
            clearInlineError();
            return;
        }

        timingInput.value = timing;
        root.querySelectorAll('[data-bw-path]').forEach(function (btn) {
            var active = btn.getAttribute('data-bw-path') === timing;
            btn.classList.toggle('style-three', !active);
        });

        if (timing === 'pay_direct') {
            if (panelDirect) panelDirect.hidden = false;
            if (panelHotel) panelHotel.hidden = true;
            channelInput.value = directPayEnabled() ? 'card' : '';
        } else {
            if (panelDirect) panelDirect.hidden = true;
            if (panelHotel) panelHotel.hidden = false;
            if (clearHotel) {
                clearHotelChannelSelection();
            }
        }
        clearInlineError();
    }

    function setStep(n) {
        var root = wizardRoot();
        if (!root) return;

        step = n;
        root.querySelectorAll('[data-step-indicator]').forEach(function (node) {
            var s = parseInt(node.getAttribute('data-step-indicator'), 10);
            node.classList.toggle('ma-bw__step--active', s === step);
            node.classList.toggle('ma-bw__step--done', s < step);
        });
        root.querySelectorAll('[data-bw-step]').forEach(function (panel) {
            var s = parseInt(panel.getAttribute('data-bw-step'), 10);
            var show = s === step;
            panel.hidden = !show;
            panel.classList.toggle('ma-bw__panel--active', show);
        });

        var backBtn = el('bw-back');
        var continueBtn = el('bw-continue');
        var summaryContinue = el('bw-summary-continue');
        if (backBtn) backBtn.hidden = step <= 1;
        if (continueBtn) {
            continueBtn.classList.toggle('d-none', step >= maxStep);
            if (step > 1) continueBtn.disabled = false;
        }
        if (summaryContinue) {
            summaryContinue.classList.toggle('d-none', step >= maxStep);
        }

        if (step === maxStep) {
            renderReview();
            var timing = el('bw-payment-timing');
            var path = timing && timing.value ? timing.value : '';
            selectPaymentPath(path, false);
        }
        renderSummary();
    }

    function closeRoomModal() {
        var modalEl = el('bw-room-modal');
        if (modalEl && window.bootstrap && window.bootstrap.Modal) {
            var inst = window.bootstrap.Modal.getInstance(modalEl);
            if (inst) inst.hide();
        }
    }

    function showInlineError(message) {
        var box = el('bw-inline-error');
        if (!box) {
            alert(message);
            return;
        }
        box.textContent = message;
        box.hidden = false;
        box.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function clearInlineError() {
        var box = el('bw-inline-error');
        if (box) {
            box.hidden = true;
            box.textContent = '';
        }
    }

    function validateStep(s) {
        var checkin = el('bw-checkin');
        var checkout = el('bw-checkout');
        var form = el('ma-bw-form');
        var root = wizardRoot();
        if (!checkin || !checkout || !form || !root) return false;

        clearInlineError();

        if (s === 1) {
            if (!checkin.value || !checkout.value) {
                showInlineError('Please choose check-in and check-out dates.');
                return false;
            }
            if (nightsBetween(checkin.value, checkout.value) < 1) {
                showInlineError('Check-out must be after check-in.');
                return false;
            }
            if (!ensureCartPopulated()) {
                showInlineError('Please add at least one room to your stay.');
                return false;
            }
            syncLineItemsInput();
            return true;
        }
        if (s === 2) {
            return form.reportValidity();
        }
        if (s === 3) {
            return true;
        }
        return true;
    }

    function validateSubmit() {
        var root = wizardRoot();
        var timingInput = el('bw-payment-timing');
        var channelInput = el('bw-confirmation-channel');
        if (!root || !timingInput || !channelInput) return false;

        clearInlineError();
        ensureCartPopulated();
        syncLineItemsInput();

        if (!timingInput.value) {
            showInlineError('Please choose Book and pay now or Book and pay at the hotel.');
            return false;
        }

        if (timingInput.value === 'pay_direct') {
            if (!directPayEnabled()) {
                showInlineError('Card payment is not available yet. Please choose Book and pay at the hotel.');
                return false;
            }
            channelInput.value = 'card';
            var cardName = el('bw-card-name');
            var cardNum = el('bw-card-number');
            var cardExp = el('bw-card-exp');
            var cardCvc = el('bw-card-cvc');
            if (!cardName || !cardName.value.trim()) {
                showInlineError('Please enter the name on your card.');
                return false;
            }
            if (!cardNum || cardNum.value.replace(/\s/g, '').length < 13) {
                showInlineError('Please enter a valid card number.');
                return false;
            }
            if (!cardExp || !cardExp.value.trim()) {
                showInlineError('Please enter the card expiry date.');
                return false;
            }
            if (!cardCvc || cardCvc.value.length < 3) {
                showInlineError('Please enter the card CVC.');
                return false;
            }
            return true;
        }

        if (!channelInput.value || (channelInput.value !== 'whatsapp' && channelInput.value !== 'email')) {
            showInlineError('Please submit through WhatsApp or email.');
            return false;
        }
        return true;
    }

    function goNext() {
        var root = wizardRoot();
        if (!root) return;

        closeRoomModal();

        if (root.getAttribute('data-channels-ready') !== '1') {
            showInlineError('Online booking is not available right now. Please contact the hotel.');
            return;
        }
        if (!validateStep(step)) return;
        if (step < maxStep) setStep(step + 1);
    }

    function goBack() {
        closeRoomModal();
        clearInlineError();
        if (step > 1) setStep(step - 1);
    }

    function addRoomFromModal() {
        var modalRoom = el('bw-modal-room');
        if (!modalRoom) return;

        var opt = modalRoom.selectedOptions[0];
        if (!opt || !opt.value) {
            showInlineError('Choose a room type in the list, then tap Add to stay.');
            return;
        }

        var id = parseInt(opt.value, 10);
        var r = roomById(id);
        if (!r) return;

        var cart = getCart();
        if (cart.some(function (c) { return c.room_id === id; })) {
            showInlineError('This room is already in your stay.');
            return;
        }

        cart.push({
            room_id: id,
            room_name: r.name,
            slug: r.slug,
            price: r.price
        });
        persistSlug(r.slug);
        clearInlineError();
        renderCart();
        renderSummary();
        syncLineItemsInput();

        closeRoomModal();
    }

    function findRoomBySlug(slug) {
        if (!slug) return null;
        var list = getRooms();
        var exact = list.find(function (r) { return r.slug === slug; });
        if (exact) return exact;
        var norm = slug.toLowerCase().replace(/[^a-z0-9]+/g, '-');
        return list.find(function (r) {
            var rs = (r.slug || '').toLowerCase();
            return rs === norm || rs.indexOf(norm) !== -1 || norm.indexOf(rs) !== -1
                || norm.replace(/-/g, '').indexOf(rs.replace(/-/g, '')) !== -1;
        }) || null;
    }

    function cleanupStuckModals() {
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
        document.querySelectorAll('.modal-backdrop').forEach(function (el) { el.remove(); });
        document.querySelectorAll('.modal.fade').forEach(function (modal) {
            modal.classList.remove('show');
            modal.setAttribute('aria-hidden', 'true');
            modal.style.removeProperty('display');
        });
    }

    function seedInitialRoom() {
        var cart = getCart();
        var slug = resolveInitialSlug();
        var room = null;
        var selectedId = selectedRoomIdFromRoot();
        if (selectedId) room = roomById(selectedId);
        if (!room && slug) room = findRoomBySlug(slug);
        if (room && !cart.some(function (c) { return c.room_id === room.id; })) {
            cart.push({
                room_id: room.id,
                room_name: room.name,
                slug: room.slug,
                price: room.price
            });
            persistSlug(room.slug);
        }
    }

    function initDates() {
        var checkin = el('bw-checkin');
        var checkout = el('bw-checkout');
        if (!checkin || !checkout) return;

        var t = todayStr();
        if (!checkin.value) checkin.value = t;
        if (!checkout.value) checkout.value = defaultCheckout(checkin.value);
        checkin.min = t;
        checkout.min = checkin.value;
    }

    function bindPathPickers() {
        document.querySelectorAll('[data-bw-path]').forEach(function (btn) {
            if (btn.dataset.maBwPathBound) return;
            btn.dataset.maBwPathBound = '1';
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                selectPaymentPath(btn.getAttribute('data-bw-path'), true);
            });
        });
        document.querySelectorAll('[data-bw-hotel-submit]').forEach(function (btn) {
            if (btn.dataset.maBwHotelSubmitBound) return;
            btn.dataset.maBwHotelSubmitBound = '1';
            btn.addEventListener('click', function () {
                var channelInput = el('bw-confirmation-channel');
                var timingInput = el('bw-payment-timing');
                if (timingInput) timingInput.value = 'pay_at_hotel';
                if (channelInput) channelInput.value = btn.getAttribute('data-bw-hotel-submit') || '';
            });
        });
    }

    function bindActions() {
        var continueBtn = el('bw-continue');
        var backBtn = el('bw-back');
        var summaryContinue = el('bw-summary-continue');
        var modalAdd = el('bw-modal-add');

        if (continueBtn && !continueBtn.dataset.maBwBound) {
            continueBtn.dataset.maBwBound = '1';
            continueBtn.addEventListener('click', function (e) {
                e.preventDefault();
                goNext();
            });
        }
        if (summaryContinue && !summaryContinue.dataset.maBwBound) {
            summaryContinue.dataset.maBwBound = '1';
            summaryContinue.addEventListener('click', function (e) {
                e.preventDefault();
                goNext();
            });
        }
        if (backBtn && !backBtn.dataset.maBwBound) {
            backBtn.dataset.maBwBound = '1';
            backBtn.addEventListener('click', function (e) {
                e.preventDefault();
                goBack();
            });
        }
        if (modalAdd && !modalAdd.dataset.maBwBound) {
            modalAdd.dataset.maBwBound = '1';
            modalAdd.addEventListener('click', function (e) {
                e.preventDefault();
                addRoomFromModal();
            });
        }
    }

    function init() {
        var root = wizardRoot();
        if (!root) return;

        cleanupStuckModals();
        initDates();

        if (getCart().length === 0) {
            rebuildCartFromHiddenInput();
            if (getCart().length === 0) seedInitialRoom();
        }

        renderCart();
        renderSummary();
        syncLineItemsInput();
        bindPathPickers();
        bindActions();

        var timingInput = el('bw-payment-timing');
        selectPaymentPath(timingInput && timingInput.value ? timingInput.value : '', false);

        setStep(step > 1 ? step : 1);
    }

    function onWizardSubmit(e) {
        var form = el('ma-bw-form');
        if (!form || e.target !== form) return;
        if (step !== maxStep) {
            e.preventDefault();
            return;
        }
        if (!validateSubmit()) {
            e.preventDefault();
        }
    }

    function onDateChange() {
        var checkin = el('bw-checkin');
        var checkout = el('bw-checkout');
        if (checkin && checkout) {
            checkout.min = checkin.value;
            if (checkout.value && checkout.value <= checkin.value) {
                checkout.value = defaultCheckout(checkin.value);
            }
        }
        renderSummary();
        syncLineItemsInput();
    }

    document.addEventListener('submit', onWizardSubmit, true);
    document.addEventListener('change', function (e) {
        if (!wizardRoot() || !wizardRoot().contains(e.target)) return;
        if (e.target.id === 'bw-checkin' || e.target.id === 'bw-checkout') onDateChange();
        if (e.target.id === 'bw-adults' || e.target.id === 'bw-children' || e.target.id === 'bw-room-count') renderSummary();
    });

    document.addEventListener('ma:spa-content', init);
    init();
})();
