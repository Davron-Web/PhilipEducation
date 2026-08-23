(function () {
    'use strict';

    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    var isNeonDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';

    /* ================= Snow, injected into every .hero-banner ===========
       Only in the light theme's hero — the neon dark theme replaces it
       with the ambient particle background below (see initNeonParticles). */
    function initSnow() {
        if (reduceMotion || isNeonDark) return;

        document.querySelectorAll('.hero-banner').forEach(function (hero) {
            var canvas = document.createElement('canvas');
            canvas.className = 'ph-snow';
            hero.appendChild(canvas);

            var w = hero.clientWidth, h = hero.clientHeight;
            if (!w || !h) return;
            var ctx = canvas.getContext('2d');
            var dpr = Math.min(window.devicePixelRatio || 1, 2);
            canvas.width = w * dpr;
            canvas.height = h * dpr;
            ctx.scale(dpr, dpr);

            var count = Math.max(16, Math.round((w * h) / 11000));
            var flakes = [];
            for (var i = 0; i < count; i++) {
                flakes.push({
                    x: Math.random() * w,
                    y: Math.random() * h,
                    r: 1 + Math.random() * 2,
                    speedY: .2 + Math.random() * .5,
                    speedX: (Math.random() - .5) * .25,
                    drift: Math.random() * Math.PI * 2,
                    driftSpeed: .006 + Math.random() * .01,
                    opacity: .3 + Math.random() * .45
                });
            }

            function step() {
                ctx.clearRect(0, 0, w, h);
                ctx.fillStyle = '#fff';
                flakes.forEach(function (f) {
                    f.drift += f.driftSpeed;
                    f.y += f.speedY;
                    f.x += f.speedX + Math.sin(f.drift) * .3;
                    if (f.y > h + 4) { f.y = -4; f.x = Math.random() * w; }
                    if (f.x > w + 4) f.x = -4;
                    if (f.x < -4) f.x = w + 4;
                    ctx.globalAlpha = f.opacity;
                    ctx.beginPath();
                    ctx.arc(f.x, f.y, f.r, 0, Math.PI * 2);
                    ctx.fill();
                });
                ctx.globalAlpha = 1;
                requestAnimationFrame(step);
            }
            requestAnimationFrame(step);
        });
    }

    /* ================= Reveal-on-scroll for cards & sections ============ */
    function initReveal() {
        var targets = [];
        document.querySelectorAll('.card-hover').forEach(function (el, i) {
            el.setAttribute('data-ph-reveal', '');
            el.style.setProperty('--ph-reveal-index', Math.min(i, 10));
            targets.push(el);
        });

        if (reduceMotion || !('IntersectionObserver' in window)) {
            targets.forEach(function (el) { el.classList.add('is-visible'); });
            return;
        }

        var observer = new IntersectionObserver(function (entries, obs) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    obs.unobserve(entry.target);
                }
            });
        }, { threshold: .1, rootMargin: '0px 0px -40px 0px' });

        targets.forEach(function (el) { observer.observe(el); });
    }

    /* ================= Neon dark theme: constellation particle field ===== */
    function initNeonParticles() {
        var canvas = document.getElementById('ph-neon-particles');
        if (!canvas || reduceMotion || !isNeonDark) return;

        var ctx = canvas.getContext('2d');
        var dpr = Math.min(window.devicePixelRatio || 1, 2);
        var w, h, points;

        function resize() {
            w = window.innerWidth; h = window.innerHeight;
            canvas.width = w * dpr; canvas.height = h * dpr;
            canvas.style.width = w + 'px'; canvas.style.height = h + 'px';
            ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
            var count = Math.min(70, Math.round((w * h) / 24000));
            points = [];
            for (var i = 0; i < count; i++) {
                points.push({
                    x: Math.random() * w, y: Math.random() * h,
                    vx: (Math.random() - .5) * .25, vy: (Math.random() - .5) * .25,
                    r: 1 + Math.random() * 1.4
                });
            }
        }
        window.addEventListener('resize', resize);
        resize();

        function tick() {
            ctx.clearRect(0, 0, w, h);
            points.forEach(function (p) {
                p.x += p.vx; p.y += p.vy;
                if (p.x < 0 || p.x > w) p.vx *= -1;
                if (p.y < 0 || p.y > h) p.vy *= -1;
            });
            for (var i = 0; i < points.length; i++) {
                for (var j = i + 1; j < points.length; j++) {
                    var dx = points[i].x - points[j].x, dy = points[i].y - points[j].y;
                    var dist = Math.sqrt(dx * dx + dy * dy);
                    if (dist < 130) {
                        ctx.strokeStyle = 'rgba(125,211,252,' + (0.12 * (1 - dist / 130)) + ')';
                        ctx.lineWidth = 1;
                        ctx.beginPath();
                        ctx.moveTo(points[i].x, points[i].y);
                        ctx.lineTo(points[j].x, points[j].y);
                        ctx.stroke();
                    }
                }
            }
            points.forEach(function (p) {
                ctx.beginPath();
                ctx.fillStyle = 'rgba(186,230,253,.5)';
                ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
                ctx.fill();
            });
            requestAnimationFrame(tick);
        }
        requestAnimationFrame(tick);
    }

    document.addEventListener('DOMContentLoaded', function () {
        initSnow();
        initReveal();
        initNeonParticles();
    });
})();
