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
                sans: ['Manrope', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                display: ['Unbounded', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            },

            // Цвета — ссылки на CSS-переменные --pe-*-rgb (каналы R G B без
            // обёртки), которые переопределяются под html.dark. Формат
            // rgb(var(...) / <alpha-value>) — то, что реально даёт Tailwind'у
            // накладывать дробную прозрачность (bg-ink/50 и т.п.): на бывшем
            // var(--pe-ink), где --pe-ink уже был готовым цветом (#hex), это
            // не работало — такие классы просто не попадали в собранный CSS
            // (обнаружено и подтверждено на bg-ink/N, text-ink/N, bg-armor2/N,
            // border-brand/N, shadow-brand/N и т.д. по всему проекту, 612
            // вхождений в 71 файле). Сами --pe-X (без -rgb) остаются как были
            // — от них напрямую зависят дашборд, phil-widget, phil-mascot и
            // градиенты, которые используют var(--pe-X) как обычный цвет.
            colors: {
                ink: 'rgb(var(--pe-ink-rgb) / <alpha-value>)',
                armor: 'rgb(var(--pe-armor-rgb) / <alpha-value>)',
                armor2: 'rgb(var(--pe-armor2-rgb) / <alpha-value>)',
                surface2: 'rgb(var(--pe-surface2-rgb) / <alpha-value>)',
                line: 'rgb(var(--pe-line-rgb) / <alpha-value>)',
                brand: 'rgb(var(--pe-brand-rgb) / <alpha-value>)',
                sky: 'rgb(var(--pe-sky-rgb) / <alpha-value>)',
                skylight: 'rgb(var(--pe-skylight-rgb) / <alpha-value>)',
                sun: 'rgb(var(--pe-sun-rgb) / <alpha-value>)',
                navy: 'rgb(var(--pe-navy-rgb) / <alpha-value>)',
                navy2: 'rgb(var(--pe-navy2-rgb) / <alpha-value>)',
                gold: 'rgb(var(--pe-gold-rgb) / <alpha-value>)',
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
