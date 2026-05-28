/**
 * Remember the guest's chosen room across pages until they change it on the booking form.
 */
(function () {
    var STORAGE_KEY = 'ma_booking_room_slug';

    function getSlug() {
        try {
            return sessionStorage.getItem(STORAGE_KEY) || '';
        } catch (e) {
            return '';
        }
    }

    function setSlug(slug) {
        if (!slug) return;
        try {
            sessionStorage.setItem(STORAGE_KEY, slug);
        } catch (e) {}
    }

    function slugFromUrl(href) {
        try {
            var u = new URL(href, window.location.origin);
            return u.searchParams.get('room') || '';
        } catch (e) {
            return '';
        }
    }

    function appendRoomToBookingUrl(href, slug) {
        if (!slug || href.indexOf('book-room') === -1) return href;
        try {
            var u = new URL(href, window.location.origin);
            if (!u.searchParams.get('room')) {
                u.searchParams.set('room', slug);
            }
            return u.pathname + u.search;
        } catch (e) {
            return href;
        }
    }

    function augmentBookingChannelLinks() {
        var slug = getSlug();
        if (!slug) return;
        document.querySelectorAll('.ma-book-channels a[href*="book-room"]').forEach(function (a) {
            a.href = appendRoomToBookingUrl(a.href, slug);
        });
    }

    document.addEventListener('click', function (e) {
        var link = e.target.closest('[data-ma-room-slug]');
        if (link) {
            setSlug(link.getAttribute('data-ma-room-slug'));
            return;
        }
        var bookLink = e.target.closest('.ma-book-channels a[href*="book-room"]');
        if (bookLink) {
            var fromHref = slugFromUrl(bookLink.href);
            if (fromHref) setSlug(fromHref);
        }
    }, true);

    document.addEventListener('ma:spa-content', augmentBookingChannelLinks);
    augmentBookingChannelLinks();

    window.maBookingRoom = {
        get: getSlug,
        set: setSlug,
        appendToUrl: appendRoomToBookingUrl
    };
})();
