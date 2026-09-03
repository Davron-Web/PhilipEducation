{{--
    Обложка книги. Если у книги задана картинка (cover_image из админки) —
    показываем её. Если нет, рисуем типографскую обложку: название антиквой,
    автор под золотой линейкой, тонкая рамка и приглушённый геометрический
    мотив. Палитра выбирается по id книги, поэтому обложка у каждой книги
    своя, но не меняется от захода к заходу.

    Это настоящий <svg>, а не блок с буквой: он масштабируется без потери
    чёткости и выглядит одинаково в светлой и тёмной теме.
--}}
@props([
    'book',
    'class' => 'h-40 w-full',
])

@php
    $palettes = [
        ['bg' => '#1A1A2E', 'bg2' => '#252540', 'accent' => '#C9A961'],
        ['bg' => '#2C1B47', 'bg2' => '#3A2560', 'accent' => '#D4AF37'],
        ['bg' => '#14332E', 'bg2' => '#1D473F', 'accent' => '#C9A961'],
        ['bg' => '#3B1F1F', 'bg2' => '#512A2A', 'accent' => '#D9A441'],
        ['bg' => '#17303F', 'bg2' => '#204357', 'accent' => '#C9A961'],
        ['bg' => '#2E2A22', 'bg2' => '#403A2F', 'accent' => '#D4AF37'],
    ];

    $palette = $palettes[($book->id ?? 0) % count($palettes)];

    // SVG не переносит текст сам — разбиваем заголовок на строки вручную.
    $title = trim($book->title);
    $maxChars = 18;
    $lines = [];
    $current = '';

    foreach (preg_split('/\s+/', $title) as $word) {
        $candidate = $current === '' ? $word : $current.' '.$word;
        if (mb_strlen($candidate) > $maxChars && $current !== '') {
            $lines[] = $current;
            $current = $word;
        } else {
            $current = $candidate;
        }
    }
    if ($current !== '') {
        $lines[] = $current;
    }

    // Больше трёх строк не показываем — обрезаем с многоточием.
    if (count($lines) > 3) {
        $lines = array_slice($lines, 0, 3);
        $lines[2] = rtrim($lines[2]).'…';
    }

    $fontSize = match (count($lines)) {
        1 => 30,
        2 => 26,
        default => 22,
    };
    $lineHeight = $fontSize + 8;
    // Блок заголовка центрируем по вертикали чуть выше середины —
    // ниже остаётся место под линейку и имя автора.
    $titleTop = 74 - (count($lines) - 1) * $lineHeight / 2;
    $ruleY = $titleTop + (count($lines) - 1) * $lineHeight + 26;
@endphp

@if ($book->cover_image)
    <img src="{{ $book->cover_image }}" alt="{{ $book->title }}" {{ $attributes->merge(['class' => $class.' object-cover']) }}>
@else
    <svg
        viewBox="0 0 400 180"
        preserveAspectRatio="xMidYMid slice"
        role="img"
        aria-label="Обложка книги «{{ $book->title }}»"
        {{ $attributes->merge(['class' => $class]) }}
    >
        <defs>
            <linearGradient id="bookcover-{{ $book->id }}" x1="0" y1="0" x2="1" y2="1">
                <stop offset="0%" stop-color="{{ $palette['bg2'] }}" />
                <stop offset="100%" stop-color="{{ $palette['bg'] }}" />
            </linearGradient>
        </defs>

        <rect width="400" height="180" fill="url(#bookcover-{{ $book->id }})" />

        {{-- Приглушённый мотив: концентрические дуги в углу --}}
        <g fill="none" stroke="{{ $palette['accent'] }}" stroke-opacity="0.13">
            <circle cx="352" cy="30" r="34" />
            <circle cx="352" cy="30" r="52" />
            <circle cx="352" cy="30" r="70" />
        </g>

        {{-- Тонкая рамка, как на переплёте --}}
        <rect x="14" y="14" width="372" height="152" fill="none" stroke="{{ $palette['accent'] }}" stroke-opacity="0.35" />

        <g text-anchor="middle" font-family="'Playfair Display', Georgia, serif">
            @foreach ($lines as $i => $line)
                <text
                    x="200"
                    y="{{ $titleTop + $i * $lineHeight }}"
                    fill="#FFFFFF"
                    font-size="{{ $fontSize }}"
                    font-weight="600"
                >{{ $line }}</text>
            @endforeach
        </g>

        <line x1="170" y1="{{ $ruleY }}" x2="230" y2="{{ $ruleY }}" stroke="{{ $palette['accent'] }}" stroke-width="2" />

        <text
            x="200"
            y="{{ $ruleY + 26 }}"
            text-anchor="middle"
            font-family="Inter, system-ui, sans-serif"
            font-size="12"
            letter-spacing="2.5"
            fill="{{ $palette['accent'] }}"
        >{{ mb_strtoupper($book->author) }}</text>
    </svg>
@endif
