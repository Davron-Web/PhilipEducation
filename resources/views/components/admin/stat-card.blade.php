@props(['icon' => '', 'color' => 'blue', 'label' => '', 'value' => 0])

<div class="card stat">
    <div class="stat-icon {{ $color }}">
        {!! $icon !!}
    </div>
    <div>
        <div class="stat-label">{{ $label }}</div>
        <div class="stat-value">{{ $value }}</div>
    </div>
</div>
