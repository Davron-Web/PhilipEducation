(function () {
    'use strict';

    /* ---- Dark mode toggle (persisted) ---------------------------------- */
    var root = document.documentElement;
    var toggleBtn = document.getElementById('theme-toggle');
    var saved = localStorage.getItem('theme');

    // The <html> tag already ships with data-bs-theme="dark" (neon is the
    // default look now, set server-side to avoid a light-theme flash on
    // load) — only an explicit saved preference for light should switch it.
    if (saved === 'light') {
        root.setAttribute('data-bs-theme', 'light');
    }

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            var isDark = root.getAttribute('data-bs-theme') === 'dark';
            if (isDark) {
                root.setAttribute('data-bs-theme', 'light');
                localStorage.setItem('theme', 'light');
            } else {
                root.setAttribute('data-bs-theme', 'dark');
                localStorage.setItem('theme', 'dark');
            }
        });
    }

    /* ---- Generic level/category filter tabs -----------------------------
       Any button with [data-filter-group] + [data-filter-value] toggles
       visibility of sibling cards carrying a matching [data-filter-target]
       attribute on data-<group>. Used on Lessons/Grammar/Words indexes. */
    document.querySelectorAll('[data-filter-buttons]').forEach(function (group) {
        var targetSelector = group.getAttribute('data-filter-buttons');
        var items = document.querySelectorAll(targetSelector);

        group.querySelectorAll('[data-filter-value]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var value = btn.getAttribute('data-filter-value');

                group.querySelectorAll('[data-filter-value]').forEach(function (b) {
                    b.classList.remove('active');
                });
                btn.classList.add('active');

                items.forEach(function (item) {
                    var itemValue = item.getAttribute('data-filter-item');
                    var show = value === 'all' || itemValue === value;
                    item.classList.toggle('d-none', !show);
                });
            });
        });
    });

    /* ---- Exercise fill-in-the-blank self-check --------------------------- */
    document.querySelectorAll('[data-exercise-check]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            form.querySelectorAll('[data-answer]').forEach(function (input) {
                var correct = (input.getAttribute('data-answer') || '').trim().toLowerCase();
                var given = (input.value || '').trim().toLowerCase();
                input.classList.remove('is-valid', 'is-invalid');
                input.classList.add(given.length && given === correct ? 'is-valid' : 'is-invalid');
            });
        });
    });

    /* ---- Test self-check quiz --------------------------------------------- */
    document.querySelectorAll('[data-test-quiz]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var questions = form.querySelectorAll('[data-question]');
            var correctCount = 0;

            questions.forEach(function (q) {
                var selected = q.querySelector('input[type=radio]:checked');
                var options = q.querySelectorAll('.form-check');
                options.forEach(function (opt) { opt.classList.remove('text-success', 'text-danger'); });

                if (selected) {
                    var isCorrect = selected.getAttribute('data-correct') === '1';
                    selected.closest('.form-check').classList.add(isCorrect ? 'text-success' : 'text-danger');
                    if (isCorrect) correctCount++;
                }
            });

            var resultBox = form.querySelector('[data-quiz-result]');
            if (resultBox) {
                var total = questions.length;
                var percent = total ? Math.round((correctCount / total) * 100) : 0;
                resultBox.classList.remove('d-none', 'alert-success', 'alert-warning');
                resultBox.classList.add(percent >= 70 ? 'alert-success' : 'alert-warning');
                resultBox.textContent = 'Result: ' + correctCount + ' / ' + total + ' correct (' + percent + '%)';
                resultBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    });
})();
