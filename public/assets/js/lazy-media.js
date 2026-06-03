/**
 * Defer heavy background images so page content paints first (home slider, etc.).
 */
(function () {
    'use strict';

    function loadDeferredBackgrounds() {
        document.querySelectorAll('[data-defer-bg]').forEach(function (el) {
            var url = el.getAttribute('data-defer-bg');
            if (!url || el.style.backgroundImage) {
                return;
            }
            el.style.backgroundImage = "url('" + url.replace(/'/g, "\\'") + "')";
        });
    }

    function preloadAdjacentSlides() {
        if (!window.jQuery) {
            return;
        }
        var $slider = window.jQuery('.slider-two-active.slick-initialized');
        if (!$slider.length) {
            return;
        }
        $slider.on('beforeChange', function (event, slick, current, next) {
            var slide = slick.$slides.get(next);
            if (!slide) {
                return;
            }
            var item = slide.querySelector('[data-defer-bg]');
            if (item) {
                var url = item.getAttribute('data-defer-bg');
                if (url && !item.style.backgroundImage) {
                    item.style.backgroundImage = "url('" + url + "')";
                }
            }
        });
    }

    function scheduleDeferredLoads() {
        var run = function () {
            loadDeferredBackgrounds();
            preloadAdjacentSlides();
        };
        if ('requestIdleCallback' in window) {
            requestIdleCallback(run, { timeout: 2000 });
        } else {
            setTimeout(run, 120);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', scheduleDeferredLoads);
    } else {
        scheduleDeferredLoads();
    }

    document.addEventListener('ma:spa-content', scheduleDeferredLoads);
})();
