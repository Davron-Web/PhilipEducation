@props(['title' => '', 'backRoute' => null, 'backLabel' => '← Назад'])

<div class="page-header">
    <div>
        <div class="left">
            @if($backRoute)
                <a href="{{ $backRoute }}" class="btn btn-ghost">{{ $backLabel }}</a>
            @endif
            <h1>{{ $title }}</h1>
            {{ $badge ?? '' }}
        </div>
        @isset($sub)
            <div class="sub">{{ $sub }}</div>
        @endisset
    </div>

    @isset($actions)
        <div style="display:flex;gap:8px;flex-wrap:wrap">{{ $actions }}</div>
    @endisset
</div>
