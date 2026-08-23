(function () {
    'use strict';

    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (reduceMotion) return;

    function initSnow() {
        document.querySelectorAll('canvas.ph-snow[data-ph-snow]').forEach(function (canvas) {
            var w = canvas.clientWidth, h = canvas.clientHeight;
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

    document.addEventListener('DOMContentLoaded', initSnow);
})();
