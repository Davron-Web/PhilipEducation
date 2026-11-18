{{--
    Единый логотип сайта: иконка (шапка выпускника) + текст "Philip Education".
    Используется в шапке, футере, на страницах входа/регистрации, в админке и
    на страницах ошибок — чтобы значок менялся в одном месте, а не в пяти.

    Props:
      size      — размер иконки в пикселях (по умолчанию 36, как в шапке).
      text      — показывать ли текст "Philip Education" рядом с иконкой
                  (по умолчанию true). false — только иконка (для админки,
                  auth-страниц, страниц ошибок — там своя обвязка текста).
      textClass — классы текстового блока, если нужно переопределить
                  дефолтные (например, добавить "hidden sm:block" в шапке,
                  где текст скрыт на мобильных, а иконка — нет).
--}}
@props([
    'size' => 36,
    'text' => true,
    'textClass' => 'font-display text-lg font-semibold text-white',
])

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2']) }}>
    <svg
        width="{{ $size }}"
        height="{{ $size }}"
        viewBox="0 0 36 36"
        class="shrink-0"
        @if ($text) aria-hidden="true" @else role="img" aria-label="Philip Education" @endif
    >
        <rect width="36" height="36" rx="6" fill="#29A3DF"/>
        <path d="M9 16 V24 Q18 29 27 24 V16 Z" fill="#FFFFFF"/>
        <polygon points="18,7 34,14 18,21 2,14" fill="#FFFFFF" stroke="#29A3DF" stroke-width="1"/>
        <path d="M31 15 V24" fill="none" stroke="#FFFFFF" stroke-width="1.5" stroke-linecap="round"/>
        <circle cx="31" cy="25" r="1.8" fill="#FFFFFF"/>
    </svg>

    @if ($text)
        <span class="{{ $textClass }}">
            Philip <span class="text-gold">Education</span>
        </span>
    @endif
</span>
