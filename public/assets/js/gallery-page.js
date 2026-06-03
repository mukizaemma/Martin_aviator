/**
 * Gallery page: 3-column grid, category filter, lightbox modal.
 */
(function () {
    'use strict';

    var currentIndex = 0;
    var modalInstance = null;

    function pageRoot() {
        return document.querySelector('.ma-gallery-page');
    }

    function getVisibleCards() {
        var root = pageRoot();
        if (!root) return [];
        return Array.from(root.querySelectorAll('.ma-gallery-item:not(.ma-gallery-item--hidden) .ma-gallery-card'));
    }

    function applyFilter(selector) {
        var root = pageRoot();
        if (!root) return;

        root.querySelectorAll('.ma-gallery-item').forEach(function (item) {
            var show = selector === '*' || item.classList.contains(selector.replace(/^\./, ''));
            item.classList.toggle('ma-gallery-item--hidden', !show);
        });
    }

    function showLightboxAt(index) {
        var cards = getVisibleCards();
        if (!cards.length) return;

        if (index < 0) index = cards.length - 1;
        if (index >= cards.length) index = 0;
        currentIndex = index;

        var card = cards[currentIndex];
        var modalEl = document.getElementById('ma-gallery-lightbox');
        var img = document.getElementById('ma-gallery-lightbox-img');
        var caption = document.getElementById('ma-gallery-lightbox-label');
        var counter = document.getElementById('ma-gallery-counter');

        if (!modalEl || !img || !window.bootstrap || !window.bootstrap.Modal) return;

        var src = card.getAttribute('data-gallery-src') || '';
        var cap = card.getAttribute('data-gallery-caption') || '';
        img.src = src;
        img.alt = cap;
        if (caption) caption.textContent = cap;
        if (counter) counter.textContent = (currentIndex + 1) + ' / ' + cards.length;

        modalInstance = window.bootstrap.Modal.getOrCreateInstance(modalEl, {
            backdrop: true,
            keyboard: true,
        });
        modalInstance.show();
    }

    function stepLightbox(delta) {
        showLightboxAt(currentIndex + delta);
    }

    function onGalleryClick(e) {
        var root = pageRoot();
        if (!root) return;

        var tab = e.target.closest('.gallery-filter li');
        if (tab && root.contains(tab)) {
            root.querySelectorAll('.gallery-filter li').forEach(function (li) {
                li.classList.remove('current');
                li.setAttribute('aria-selected', 'false');
            });
            tab.classList.add('current');
            tab.setAttribute('aria-selected', 'true');
            applyFilter(tab.getAttribute('data-filter') || '*');
            return;
        }

        var card = e.target.closest('.ma-gallery-card');
        if (card && root.contains(card)) {
            var cards = getVisibleCards();
            var idx = cards.indexOf(card);
            showLightboxAt(idx >= 0 ? idx : 0);
        }
    }

    function bindLightboxNav() {
        var prev = document.getElementById('ma-gallery-prev');
        var next = document.getElementById('ma-gallery-next');
        var modalEl = document.getElementById('ma-gallery-lightbox');

        if (prev && !prev.dataset.maGalleryNavBound) {
            prev.dataset.maGalleryNavBound = '1';
            prev.addEventListener('click', function (e) {
                e.stopPropagation();
                stepLightbox(-1);
            });
        }
        if (next && !next.dataset.maGalleryNavBound) {
            next.dataset.maGalleryNavBound = '1';
            next.addEventListener('click', function (e) {
                e.stopPropagation();
                stepLightbox(1);
            });
        }
        if (modalEl && !modalEl.dataset.maGalleryKeyBound) {
            modalEl.dataset.maGalleryKeyBound = '1';
            modalEl.addEventListener('keydown', function (e) {
                if (!modalEl.classList.contains('show')) return;
                if (e.key === 'ArrowLeft') {
                    e.preventDefault();
                    stepLightbox(-1);
                }
                if (e.key === 'ArrowRight') {
                    e.preventDefault();
                    stepLightbox(1);
                }
            });
        }
    }

    function init() {
        var root = pageRoot();
        if (!root) return;

        modalInstance = null;

        if (!root.dataset.maGalleryInit) {
            root.dataset.maGalleryInit = '1';
            root.addEventListener('click', onGalleryClick);
        }

        bindLightboxNav();

        var current = root.querySelector('.gallery-filter li.current');
        applyFilter(current ? current.getAttribute('data-filter') || '*' : '*');
    }

    document.addEventListener('DOMContentLoaded', init);
    document.addEventListener('ma:spa-content', init);
})();
