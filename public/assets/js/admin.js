(function () {
    'use strict';

    var root = document.documentElement;
    var toggle = document.getElementById('themeToggle');
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('overlay');
    var burger = document.getElementById('burger');

    if (localStorage.getItem('theme') === 'dark') {
        root.dataset.theme = 'dark';
        if (toggle) toggle.checked = true;
    }

    if (toggle) {
        toggle.addEventListener('change', function () {
            root.dataset.theme = toggle.checked ? 'dark' : 'light';
            localStorage.setItem('theme', root.dataset.theme);
        });
    }

    if (burger && sidebar && overlay) {
        burger.addEventListener('click', function () {
            sidebar.classList.add('open');
            overlay.classList.add('show');
        });
        overlay.addEventListener('click', function () {
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
        });
    }
})();
