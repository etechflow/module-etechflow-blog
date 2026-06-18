/*
 * Etechflow_Blog — progressive enhancement. Pure vanilla JS, no dependencies,
 * so it runs unchanged on Hyvä, Luma and Adobe Commerce. Everything degrades
 * gracefully: the page is fully usable with JS disabled.
 */
(function () {
    'use strict';

    function ready(fn) {
        if (document.readyState !== 'loading') { fn(); }
        else { document.addEventListener('DOMContentLoaded', fn); }
    }

    /* Reading progress bar */
    function initProgress() {
        var bar = document.querySelector('[data-etf-progress] span');
        var article = document.querySelector('.etf-article');
        if (!bar || !article) { return; }
        function update() {
            var rect = article.getBoundingClientRect();
            var total = article.offsetHeight - window.innerHeight;
            var scrolled = Math.min(Math.max(-rect.top, 0), total > 0 ? total : 1);
            var pct = total > 0 ? (scrolled / total) * 100 : 0;
            bar.style.width = pct.toFixed(1) + '%';
        }
        window.addEventListener('scroll', update, { passive: true });
        window.addEventListener('resize', update, { passive: true });
        update();
    }

    /* Image lightbox for in-content images */
    function initLightbox() {
        var content = document.querySelector('.etf-content[data-etf-gallery="1"]');
        if (!content) { return; }
        content.addEventListener('click', function (e) {
            var img = e.target.closest('img');
            if (!img) { return; }
            e.preventDefault();
            var box = document.createElement('div');
            box.className = 'etf-lightbox';
            var big = document.createElement('img');
            big.src = img.getAttribute('src');
            big.alt = img.getAttribute('alt') || '';
            box.appendChild(big);
            box.addEventListener('click', function () { box.remove(); });
            document.body.appendChild(box);
        });
    }

    /* TOC scroll-spy */
    function initToc() {
        var toc = document.querySelector('[data-etf-toc]');
        if (!toc) { return; }
        var links = Array.prototype.slice.call(toc.querySelectorAll('a[href^="#"]'));
        var targets = links.map(function (a) {
            try { return document.getElementById(decodeURIComponent(a.getAttribute('href').slice(1))); }
            catch (err) { return null; }
        });
        function spy() {
            var idx = -1;
            for (var i = 0; i < targets.length; i++) {
                if (targets[i] && targets[i].getBoundingClientRect().top <= 120) { idx = i; }
            }
            links.forEach(function (a, i) { a.classList.toggle('etf-active', i === idx); });
        }
        window.addEventListener('scroll', spy, { passive: true });
        spy();
    }

    /* Pretty search URLs: /blog/search/{query}/ */
    function initSearch() {
        var forms = document.querySelectorAll('[data-etf-search]');
        Array.prototype.forEach.call(forms, function (form) {
            form.addEventListener('submit', function (e) {
                var input = form.querySelector('input[name="q"]');
                if (!input) { return; }
                var q = input.value.trim();
                if (!q) { return; }
                e.preventDefault();
                var base = form.getAttribute('action').replace(/\/+$/, '');
                window.location.href = base + '/' + encodeURIComponent(q) + '/';
            });
        });
    }

    ready(function () {
        initProgress();
        initLightbox();
        initToc();
        initSearch();
    });
})();
