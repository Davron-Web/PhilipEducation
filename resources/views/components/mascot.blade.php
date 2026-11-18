{{-- Знак-маскот сайта: красная панда, PNG-силуэт с реальной alpha-
     прозрачностью (public/images/panda.png, лицензия Flaticon — см.
     атрибуцию в подвале сайта). Один компонент на весь сайт: логотип,
     Phil, страницы входа/ошибок — все берут ровно этот файл, поэтому
     смена маскота — правка в одном месте.

     Перекраска — через CSS mask-image, не через SVG-трассировку и не
     через две версии PNG под тему: у исходника настоящий альфа-канал
     (проверено через GD — фон полностью прозрачен, силуэт полностью
     непрозрачен), а mask-image с background-color: currentColor красит
     его в любой цвет без потери качества и без второго файла. Родитель
     задаёт цвет через обычный text-* класс (text-white на тёмной шапке,
     text-navy/text-brand на светлых карточках) — ровно как раньше
     работал fill="currentColor" у прежней инлайн-совы.

     mask-size: contain, а не cover — силуэт шире, чем выше (уши почти
     впритык к краям кадра), cover обрезал бы их в квадратной области. --}}
@props(['size' => 24])

<span
    aria-hidden="true"
    style="
        display: inline-block;
        width: {{ $size }}px;
        height: {{ $size }}px;
        background-color: currentColor;
        -webkit-mask-image: url('{{ asset('images/panda.png') }}');
        mask-image: url('{{ asset('images/panda.png') }}');
        -webkit-mask-repeat: no-repeat;
        mask-repeat: no-repeat;
        -webkit-mask-position: center;
        mask-position: center;
        -webkit-mask-size: contain;
        mask-size: contain;
    "
    {{ $attributes }}
></span>
