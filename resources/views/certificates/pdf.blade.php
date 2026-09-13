{{--
    Канонический макет сертификата — единственный источник правды для его
    внешнего вида. Сейчас используется только превью на /achievements
    (через <x-certificate-preview>, см. resources/views/components/
    certificate-preview.blade.php), но рассчитан на переиспользование,
    когда появится настоящая выдача PDF: рендерер (Dompdf/Browsershot и
    т.п.) сможет взять этот же файл через
    View::make('certificates.pdf', [...])->render() — без второго макета.

    Поэтому холст зафиксирован в размерах A4 landscape при 96dpi
    (1122×794 css px), а не подогнан под текущую карточку — масштабирование
    под карточку/модалку делает обёртка certificate-preview снаружи.

    Бумага сертификата намеренно не следует теме сайта (светлая/тёмная) —
    настоящий диплом на бумаге не перекрашивается вместе с интерфейсом
    вокруг него, поэтому все цвета здесь — литералы, а не --pe-* токены.

    Параметры: levelCode, levelName, recipientName, certificateNumber,
    issuedAtLabel (уже отформатированная строка даты или заглушка).
--}}
@once
    <style>
        .cert-doc {
            position: relative;
            width: 1122px;
            height: 794px;
            background: linear-gradient(135deg, #fbf7ec, #f3ecd8);
            font-family: 'Manrope', ui-sans-serif, system-ui, sans-serif;
            color: #241f14;
        }
        .cert-doc__corner {
            position: absolute;
            width: 46px;
            height: 46px;
            border-color: #b8922f;
        }
        .cert-doc__corner--tl { top: 18px; left: 18px; border-top: 3px solid; border-left: 3px solid; }
        .cert-doc__corner--tr { top: 18px; right: 18px; border-top: 3px solid; border-right: 3px solid; }
        .cert-doc__corner--bl { bottom: 18px; left: 18px; border-bottom: 3px solid; border-left: 3px solid; }
        .cert-doc__corner--br { bottom: 18px; right: 18px; border-bottom: 3px solid; border-right: 3px solid; }
        .cert-doc__frame {
            position: absolute;
            inset: 34px;
            border: 2px solid #b8922f;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 0 90px;
        }
        .cert-doc__frame::before {
            content: '';
            position: absolute;
            inset: 10px;
            border: 1px solid rgba(184, 146, 47, .45);
            pointer-events: none;
        }
        .cert-doc__seal {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 64px;
            height: 64px;
            border-radius: 9999px;
            margin-bottom: 18px;
            background: linear-gradient(135deg, #1e3a8a, #2fa8e0);
            color: #fff;
            box-shadow: 0 10px 24px -8px rgba(30, 58, 138, .55);
        }
        .cert-doc__eyebrow {
            font-size: 15px;
            letter-spacing: .28em;
            text-transform: uppercase;
            font-weight: 700;
            color: #8a6d1f;
            margin: 0 0 14px;
        }
        .cert-doc__level {
            font-family: 'Unbounded', ui-sans-serif, system-ui, sans-serif;
            font-size: 64px;
            line-height: 1;
            font-weight: 800;
            color: #1e3a8a;
            margin: 0;
        }
        .cert-doc__levelname {
            font-size: 20px;
            color: #7c6a3f;
            margin: 8px 0 26px;
        }
        .cert-doc__lead {
            font-size: 16px;
            font-style: italic;
            color: #8a7c5c;
            margin: 0 0 10px;
        }
        .cert-doc__name {
            display: inline-block;
            min-width: 420px;
            font-family: 'Unbounded', ui-sans-serif, system-ui, sans-serif;
            font-size: 40px;
            font-weight: 700;
            color: #241f14;
            margin: 0 0 10px;
            padding-bottom: 14px;
            border-bottom: 2px solid #b8922f;
        }
        .cert-doc__desc {
            max-width: 640px;
            font-size: 16px;
            line-height: 1.6;
            color: #5b5240;
            margin: 22px 0 0;
        }
        .cert-doc__footer {
            position: absolute;
            left: 90px;
            right: 90px;
            bottom: 56px;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
        }
        .cert-doc__foot-col {
            display: flex;
            flex-direction: column;
            align-items: center;
            min-width: 160px;
        }
        .cert-doc__foot-value,
        .cert-doc__signature {
            border-top: 1px solid #c9b877;
            padding-top: 8px;
            margin-top: 26px;
            font-size: 18px;
            font-weight: 700;
            color: #241f14;
        }
        .cert-doc__signature {
            font-family: 'Unbounded', ui-sans-serif, system-ui, sans-serif;
            color: #1e3a8a;
        }
        .cert-doc__foot-label {
            margin-top: 6px;
            font-size: 11px;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: #a3966b;
        }
    </style>
@endonce

<div class="cert-doc">
    <span class="cert-doc__corner cert-doc__corner--tl"></span>
    <span class="cert-doc__corner cert-doc__corner--tr"></span>
    <span class="cert-doc__corner cert-doc__corner--bl"></span>
    <span class="cert-doc__corner cert-doc__corner--br"></span>

    <div class="cert-doc__frame">
        <span class="cert-doc__seal">
            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M8 21h8M12 17v4M7 4h10v4a5 5 0 0 1-10 0V4Z" /><path d="M7 6H4a1 1 0 0 0-1 1c0 2.5 1.8 4.5 4.2 4.9M17 6h3a1 1 0 0 1 1 1c0 2.5-1.8 4.5-4.2 4.9" /></svg>
        </span>

        <p class="cert-doc__eyebrow">Certificate of Completion</p>
        <h1 class="cert-doc__level">Level {{ $levelCode }}</h1>
        <p class="cert-doc__levelname">{{ $levelName }}</p>

        <p class="cert-doc__lead">This certifies that</p>
        <p class="cert-doc__name">{{ $recipientName }}</p>
        <p class="cert-doc__desc">has successfully completed all requirements of the {{ $levelName }} English course on Philip Education.</p>

        <div class="cert-doc__footer">
            <div class="cert-doc__foot-col">
                <span class="cert-doc__foot-value">{{ $issuedAtLabel }}</span>
                <span class="cert-doc__foot-label">Date issued</span>
            </div>
            <div class="cert-doc__foot-col">
                <span class="cert-doc__signature">Philip Education Team</span>
                <span class="cert-doc__foot-label">Authorized signature</span>
            </div>
            <div class="cert-doc__foot-col">
                <span class="cert-doc__foot-value">{{ $certificateNumber }}</span>
                <span class="cert-doc__foot-label">Certificate No.</span>
            </div>
        </div>
    </div>
</div>
