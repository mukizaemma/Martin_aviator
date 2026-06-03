/**
 * Compress large image uploads in the admin before submit (matches server 700 KB limit).
 * Shows original vs upload size under each file input.
 */
(function () {
    'use strict';

    var PRESETS = {
        slide: { width: 1920, height: 1080, cover: true },
        gallery: { width: 1400, height: 1050, cover: true },
        room: { width: 1400, height: 933, cover: true },
        facility: { width: 1400, height: 933, cover: true },
        service: { width: 1400, height: 933, cover: true },
        page: { width: 1920, height: 720, cover: true },
        dining: { width: 1200, height: 800, cover: true },
        partner: { width: 900, height: 450, cover: true },
        logo: { width: 480, height: 240, cover: false },
        general: { width: 1600, height: 1200, cover: true }
    };

    var maxBytes = parseInt(document.body.getAttribute('data-ma-max-image-bytes') || '716800', 10);

    function formatSize(bytes) {
        if (bytes < 1024) {
            return bytes + ' B';
        }
        if (bytes < 1024 * 1024) {
            return (bytes / 1024).toFixed(1) + ' KB';
        }
        return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    }

    function inferPreset(input) {
        if (input.getAttribute('data-ma-preset')) {
            return input.getAttribute('data-ma-preset');
        }
        var path = window.location.pathname;
        if (path.indexOf('page-headers') !== -1) {
            return 'page';
        }
        if (path.indexOf('gallery') !== -1 || path.indexOf('dining') !== -1) {
            return 'gallery';
        }
        if (path.indexOf('slides') !== -1) {
            return 'slide';
        }
        if (path.indexOf('rooms') !== -1) {
            return 'room';
        }
        if (path.indexOf('facilities') !== -1) {
            return 'facility';
        }
        if (path.indexOf('services') !== -1) {
            return 'service';
        }
        if (path.indexOf('partners') !== -1) {
            return 'partner';
        }
        return 'general';
    }

    function loadImage(file) {
        return new Promise(function (resolve, reject) {
            var url = URL.createObjectURL(file);
            var img = new Image();
            img.onload = function () {
                URL.revokeObjectURL(url);
                resolve(img);
            };
            img.onerror = function () {
                URL.revokeObjectURL(url);
                reject(new Error('Could not read image'));
            };
            img.src = url;
        });
    }

    function resizeToCanvas(img, preset) {
        var maxW = preset.width;
        var maxH = preset.height;
        var srcW = img.naturalWidth;
        var srcH = img.naturalHeight;
        var canvas = document.createElement('canvas');
        var ctx = canvas.getContext('2d');

        if (preset.cover) {
            var scale = Math.max(maxW / srcW, maxH / srcH);
            var tmpW = Math.max(1, Math.round(srcW * scale));
            var tmpH = Math.max(1, Math.round(srcH * scale));
            var tmp = document.createElement('canvas');
            tmp.width = tmpW;
            tmp.height = tmpH;
            tmp.getContext('2d').drawImage(img, 0, 0, tmpW, tmpH);
            canvas.width = maxW;
            canvas.height = maxH;
            var srcX = Math.max(0, Math.floor((tmpW - maxW) / 2));
            var srcY = Math.max(0, Math.floor((tmpH - maxH) / 2));
            ctx.drawImage(tmp, srcX, srcY, maxW, maxH, 0, 0, maxW, maxH);
        } else {
            scale = Math.min(maxW / srcW, maxH / srcH, 1);
            var dstW = Math.max(1, Math.round(srcW * scale));
            var dstH = Math.max(1, Math.round(srcH * scale));
            canvas.width = dstW;
            canvas.height = dstH;
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, dstW, dstH);
            ctx.drawImage(img, 0, 0, dstW, dstH);
        }

        return canvas;
    }

    function canvasToBlob(canvas, quality) {
        return new Promise(function (resolve) {
            canvas.toBlob(function (blob) {
                resolve(blob);
            }, 'image/jpeg', quality / 100);
        });
    }

    function scaleCanvas(canvas, factor) {
        var w = Math.max(320, Math.floor(canvas.width * factor));
        var h = Math.max(240, Math.floor(canvas.height * factor));
        var next = document.createElement('canvas');
        next.width = w;
        next.height = h;
        next.getContext('2d').drawImage(canvas, 0, 0, w, h);
        return next;
    }

    async function compressToLimit(canvas, limit) {
        var quality = 88;
        var current = canvas;
        var blob = null;

        for (var attempt = 0; attempt < 12; attempt++) {
            blob = await canvasToBlob(current, quality);
            if (blob && blob.size <= limit) {
                return blob;
            }
            if (quality > 58) {
                quality -= 6;
                continue;
            }
            current = scaleCanvas(current, 0.85);
            quality = 82;
        }

        blob = await canvasToBlob(current, 75);
        return blob;
    }

    function setInputFile(input, file) {
        var dt = new DataTransfer();
        dt.items.add(file);
        input.files = dt.files;
    }

    function statusEl(input) {
        var id = 'ma-upload-status-' + (input.name || 'file') + '-' + (input.id || Math.random().toString(36).slice(2));
        var existing = input._maStatusEl;
        if (existing) {
            return existing;
        }
        var el = document.createElement('div');
        el.className = 'ma-upload-size';
        el.id = id;
        el.setAttribute('aria-live', 'polite');
        input.insertAdjacentElement('afterend', el);
        input._maStatusEl = el;
        return el;
    }

    async function handleChange(event) {
        var input = event.target;
        var file = input.files && input.files[0];
        var status = statusEl(input);

        if (!file) {
            status.textContent = '';
            status.hidden = true;
            return;
        }

        if (!file.type || file.type.indexOf('image/') !== 0) {
            status.textContent = 'Selected: ' + formatSize(file.size);
            status.hidden = false;
            return;
        }

        status.hidden = false;
        status.className = 'ma-upload-size';
        status.textContent = 'Processing image…';

        var originalSize = file.size;
        var limitLabel = formatSize(maxBytes);

        try {
            if (originalSize <= maxBytes) {
                status.innerHTML =
                    '<span class="ma-upload-size-ok">Selected: <strong>' +
                    formatSize(originalSize) +
                    '</strong> — already under ' +
                    limitLabel +
                    ' (no compression needed).</span>';
                return;
            }

            var img = await loadImage(file);
            var presetKey = inferPreset(input);
            var preset = PRESETS[presetKey] || PRESETS.general;
            var canvas = resizeToCanvas(img, preset);
            var blob = await compressToLimit(canvas, maxBytes);

            if (!blob) {
                throw new Error('Compression failed');
            }

            var baseName = file.name.replace(/\.[^.]+$/, '') || 'image';
            var compressed = new File([blob], baseName + '.jpg', {
                type: 'image/jpeg',
                lastModified: Date.now()
            });

            setInputFile(input, compressed);

            var pct = originalSize > 0 ? Math.round((1 - compressed.size / originalSize) * 100) : 0;
            status.innerHTML =
                '<span class="ma-upload-size-ok">Original: <strong>' +
                formatSize(originalSize) +
                '</strong> → will upload: <strong>' +
                formatSize(compressed.size) +
                '</strong>' +
                (pct > 0 ? ' (' + pct + '% smaller)' : '') +
                '</span>';
        } catch (err) {
            status.innerHTML =
                '<span class="ma-upload-size-warn">Could not compress — uploading original (' +
                formatSize(originalSize) +
                ').</span>';
        }
    }

    function bindInputs(root) {
        if (!root) {
            return;
        }
        var inputs = root.querySelectorAll('input[type="file"]');
        inputs.forEach(function (input) {
            if (input.getAttribute('data-ma-compress') === 'off') {
                return;
            }
            if (input.accept && input.accept.indexOf('image') === -1) {
                return;
            }
            if (input._maCompressBound) {
                return;
            }
            input._maCompressBound = true;
            if (!input.accept) {
                input.setAttribute('accept', 'image/*');
            }
            input.addEventListener('change', handleChange);
        });
    }

    function init() {
        bindInputs(document.getElementById('layoutSidenav_content'));
        bindInputs(document.querySelector('.modal.show'));
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    document.addEventListener('shown.bs.modal', init);
})();
