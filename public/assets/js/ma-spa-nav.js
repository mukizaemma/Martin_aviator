/**
 * In-app navigation for #spa-content (partial HTML from X-SPA-Partial).
 * Header/footer/booking links use full page loads for reliability.
 */
(function () {
    'use strict';

    var contentSelector = '[data-spa-container]';
    var content = document.querySelector(contentSelector);

    if (!content || !window.fetch || !window.history || !window.history.pushState) {
        return;
    }

    var spaFetchHeaders = {
        'X-Requested-With': 'XMLHttpRequest',
        'X-SPA-Partial': '1',
        'Accept': 'text/html',
    };

    var prefetchCache = new Map();
    var inFlightController = null;
    var canPrefetch = window.matchMedia('(hover: hover) and (pointer: fine)').matches;

    var SPA_SKIP_PREFIXES = [
        '/book-room',
        '/pay/',
        '/guest/',
        '/MyCart',
        '/reserve/',
        '/dashboard',
        '/login',
        '/register',
    ];

    function pathSkipped(pathname) {
        return SPA_SKIP_PREFIXES.some(function (prefix) {
            return pathname === prefix || pathname.indexOf(prefix) === 0;
        });
    }

    function shouldHandleLink(link) {
        if (!link || !link.href) {
            return false;
        }
        if (link.target && link.target !== '_self') {
            return false;
        }
        if (link.hasAttribute('download')) {
            return false;
        }
        if (link.hasAttribute('data-no-spa')) {
            return false;
        }
        if (link.closest('[data-no-spa]')) {
            return false;
        }
        if ((link.getAttribute('rel') || '').indexOf('external') !== -1) {
            return false;
        }

        var proto = (link.protocol || '').toLowerCase();
        if (proto === 'tel:' || proto === 'mailto:' || proto === 'javascript:') {
            return false;
        }

        if (link.closest('.main-header, .main-footer, .hidden-bar, .form-back-drop')) {
            return false;
        }

        var url;
        try {
            url = new URL(link.href, window.location.origin);
        } catch (e) {
            return false;
        }

        if (url.origin !== window.location.origin) {
            return false;
        }
        if (url.hash && url.pathname === window.location.pathname) {
            return false;
        }
        if (pathSkipped(url.pathname)) {
            return false;
        }

        return true;
    }

    function extractDocumentParts(htmlText) {
        var doc = new DOMParser().parseFromString(htmlText, 'text/html');
        var nextContent = doc.querySelector(contentSelector);
        if (!nextContent) {
            return null;
        }
        return {
            title: doc.title || document.title,
            bodyClass: doc.body ? doc.body.className : document.body.className,
            contentHtml: nextContent.innerHTML,
        };
    }

    function showLoadingState(isLoading) {
        document.body.classList.toggle('spa-loading', isLoading);
    }

    function reinitSpaContent() {
        window.dispatchEvent(new Event('ma:spa-content'));
    }

    function applySpaTitle(spaTitleHeader) {
        if (!spaTitleHeader) {
            return;
        }
        var siteNameEl = document.querySelector('meta[name="spa-site-name"]');
        var siteName = siteNameEl ? siteNameEl.getAttribute('content') : '';
        document.title = siteName ? spaTitleHeader + ' | ' + siteName : spaTitleHeader;
    }

    function closeMobileNav() {
        var collapse = document.querySelector('.main-header .navbar-collapse');
        if (collapse) {
            collapse.classList.remove('show');
        }
        var toggle = document.querySelector('.main-header .navbar-toggle');
        if (toggle) {
            toggle.setAttribute('aria-expanded', 'false');
        }
    }

    function finalizeNavigation(parsed, requestUrl, pushState) {
        if (window.jQuery && typeof window.maDestroySlickIn === 'function') {
            window.maDestroySlickIn(content);
        }

        content.innerHTML = parsed.contentHtml;

        if (parsed.title) {
            document.title = parsed.title;
        }

        if (parsed.bodyClass !== undefined) {
            var hadSpaLoading = document.body.classList.contains('spa-loading');
            document.body.className = parsed.bodyClass;
            if (hadSpaLoading) {
                document.body.classList.add('spa-loading');
            }
        }

        window.scrollTo(0, 0);

        if (pushState) {
            window.history.pushState({ spa: true, url: requestUrl }, '', requestUrl);
        }

        reinitSpaContent();

        if (typeof window.initParallaxBackgrounds === 'function') {
            window.initParallaxBackgrounds();
        }
    }

    function loadPage(url, pushState) {
        if (inFlightController) {
            inFlightController.abort();
        }

        inFlightController = new AbortController();
        var requestUrl = url.toString();
        showLoadingState(true);

        var fetchPromise = prefetchCache.get(requestUrl) || fetch(requestUrl, {
            signal: inFlightController.signal,
            headers: spaFetchHeaders,
            credentials: 'same-origin',
        }).then(function (response) {
            if (!response.ok) {
                throw new Error('Navigation failed');
            }
            var spaTitle = response.headers.get('X-SPA-Title');
            return response.text().then(function (htmlText) {
                return { spaTitle: spaTitle, htmlText: htmlText };
            });
        });

        return fetchPromise
            .then(function (payload) {
                if (payload.spaTitle) {
                    applySpaTitle(payload.spaTitle);
                    finalizeNavigation(
                        { contentHtml: payload.htmlText, title: null, bodyClass: undefined },
                        requestUrl,
                        pushState
                    );
                    return;
                }

                var parsed = extractDocumentParts(payload.htmlText);
                if (!parsed) {
                    window.location.href = requestUrl;
                    return;
                }

                finalizeNavigation(parsed, requestUrl, pushState);
            })
            .catch(function (error) {
                if (error.name === 'AbortError') {
                    return;
                }
                window.location.href = requestUrl;
            })
            .finally(function () {
                showLoadingState(false);
                inFlightController = null;
            });
    }

    function prefetchHref(href) {
        if (prefetchCache.has(href)) {
            return;
        }

        prefetchCache.set(
            href,
            fetch(href, {
                headers: spaFetchHeaders,
                credentials: 'same-origin',
            })
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error('Prefetch failed');
                    }
                    return response.text().then(function (htmlText) {
                        return { spaTitle: response.headers.get('X-SPA-Title'), htmlText: htmlText };
                    });
                })
                .catch(function () {
                    prefetchCache.delete(href);
                })
        );
    }

    if (!history.state || !history.state.spa) {
        history.replaceState({ spa: true, url: window.location.href }, '', window.location.href);
    }

    if (canPrefetch) {
        document.addEventListener(
            'mouseenter',
            function (event) {
                var link = event.target.closest('a[href]');
                if (!shouldHandleLink(link)) {
                    return;
                }
                prefetchHref(new URL(link.href, window.location.origin).toString());
            },
            true
        );
    }

    document.addEventListener('click', function (event) {
        if (event.defaultPrevented || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
            return;
        }

        var link = event.target.closest('a[href]');
        if (!shouldHandleLink(link)) {
            return;
        }

        event.preventDefault();
        closeMobileNav();

        var url = new URL(link.href, window.location.origin);
        loadPage(url, true);
    });

    window.addEventListener('popstate', function (event) {
        if (!event.state || !event.state.spa) {
            window.location.reload();
            return;
        }
        loadPage(new URL(window.location.href), false);
    });
})();
