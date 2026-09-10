/** @type {import('tailwindcss').Config} */
export default {
    // Тема переключается классом .dark на <html>, см. layouts/app.blade.php.
    darkMode: 'class',

    content: [
        './resources/views/**/*.blade.php',
        // Админка построена на шаблоне PlainAdmin со своим CSS и Tailwind
        // не подключает — сканировать её значило бы тащить в сборку классы,
        // которые никогда не применятся.
        '!./resources/views/admin/**',
        // reader.js навешивает hidden/flex на #practiceBar.
        './public/assets/js/*.js',
    ],

    // Классы, которые ставит JS. В разметке они сейчас есть, но правка blade
    // не должна молча выкинуть их из сборки и сломать скрипт.
    safelist: ['hidden', 'flex'],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                display: ['"Playfair Display"', 'ui-serif', 'Georgia', 'serif'],
            },

            // Цвета — ссылки на CSS-переменные --pe-*, которые переопределяются
            // под html.dark. Одна палитра обслуживает обе темы, и разметку
            // (bg-armor2, text-ink, text-gold) для тёмной темы править не нужно.
            colors: {
                ink: 'var(--pe-ink)',
                armor: 'var(--pe-armor)',
                armor2: 'var(--pe-armor2)',
                surface2: 'var(--pe-surface2)',
                line: 'var(--pe-line)',
                brand: 'var(--pe-brand)',
                sky: 'var(--pe-sky)',
                skylight: 'var(--pe-skylight)',
                sun: 'var(--pe-sun)',
                navy: 'var(--pe-navy)',
                navy2: 'var(--pe-navy2)',
                gold: 'var(--pe-gold)',
            },

            // Шкала скруглений сужена глобально: весь существующий
            // rounded-xl/2xl/3xl в разметке разом становится «острым»,
            // в духе премиальных бутик-сайтов, без правки каждого файла.
            borderRadius: {
                none: '0',
                sm: '2px',
                DEFAULT: '3px',
                md: '4px',
                lg: '5px',
                xl: '6px',
                '2xl': '8px',
                '3xl': '10px',
                '4xl': '12px',
                full: '9999px',
            },

            boxShadow: {
                soft: '0 10px 30px -10px rgba(26, 26, 46, .18)',
                softLg: '0 20px 50px -15px rgba(26, 26, 46, .28)',
            },
        },
    },

    // Пусто намеренно. Play CDN был подключён без ?plugins=forms, то есть
    // ресета форм на сайте нет. Подключение @tailwindcss/forms изменило бы
    // вид всех полей ввода — это отдельная задача, а не часть переезда с CDN.
    plugins: [],
};
