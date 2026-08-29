{{-- Отрисовка графика для IELTS Writing Task 1: bar / line / pie.
     Данные приходят из task.chart_data (JSON), без внешних библиотек. --}}
@props(['task'])

@php
    $data = $task->chart_data ?? [];
    $unit = $data['unit'] ?? '';
    $palette = ['#7C3AED', '#22D3EE', '#FFD700', '#5EEAD4', '#F472B6', '#94A3B8'];
@endphp

<div class="rounded-2xl border border-white/10 bg-armor/60 p-5">
    @if ($task->chart_type === 'bar')
        @php
            $categories = $data['categories'] ?? [];
            $series = $data['series'] ?? [];
            $max = collect($series)->flatMap(fn ($s) => $s['values'])->max() ?: 1;
        @endphp
        <div class="flex items-end justify-around gap-4 overflow-x-auto pb-4" style="min-height:200px">
            @foreach ($categories as $i => $cat)
                <div class="flex flex-col items-center gap-2">
                    <div class="flex items-end gap-1.5" style="height:170px">
                        @foreach ($series as $si => $s)
                            @php $h = max(2, round(($s['values'][$i] ?? 0) / $max * 100)); @endphp
                            <div
                                class="w-5 rounded-t transition-all"
                                style="height:{{ $h }}%; background:{{ $palette[$si % count($palette)] }}"
                                title="{{ $s['name'] }}: {{ $s['values'][$i] ?? 0 }}{{ $unit }}"
                            ></div>
                        @endforeach
                    </div>
                    <span class="text-xs font-semibold text-ink/50">{{ $cat }}</span>
                </div>
            @endforeach
        </div>
        <div class="mt-3 flex flex-wrap gap-4">
            @foreach ($series as $si => $s)
                <span class="flex items-center gap-1.5 text-xs font-semibold text-ink/70">
                    <span class="h-2.5 w-2.5 rounded-sm" style="background:{{ $palette[$si % count($palette)] }}"></span>
                    {{ $s['name'] }}
                </span>
            @endforeach
        </div>

    @elseif ($task->chart_type === 'line')
        @php
            $categories = $data['categories'] ?? [];
            $series = $data['series'] ?? [];
            $max = collect($series)->flatMap(fn ($s) => $s['values'])->max() ?: 1;
            $stepX = count($categories) > 1 ? 380 / (count($categories) - 1) : 0;
        @endphp
        <svg viewBox="0 0 400 220" class="w-full">
            @foreach ($series as $si => $s)
                @php
                    $points = collect($s['values'])->map(fn ($v, $i) => round($i * $stepX + 10, 1).','.round(195 - ($v / $max * 175), 1))->implode(' ');
                @endphp
                <polyline points="{{ $points }}" fill="none" stroke="{{ $palette[$si % count($palette)] }}" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round" />
                @foreach ($s['values'] as $i => $v)
                    <circle cx="{{ round($i * $stepX + 10, 1) }}" cy="{{ round(195 - ($v / $max * 175), 1) }}" r="3.2" fill="{{ $palette[$si % count($palette)] }}" />
                @endforeach
            @endforeach
            @foreach ($categories as $i => $cat)
                <text x="{{ round($i * $stepX + 10, 1) }}" y="212" font-size="10" fill="#9CA8B5" text-anchor="middle">{{ $cat }}</text>
            @endforeach
        </svg>
        <div class="mt-1 flex flex-wrap gap-4">
            @foreach ($series as $si => $s)
                <span class="flex items-center gap-1.5 text-xs font-semibold text-ink/70">
                    <span class="h-2.5 w-2.5 rounded-sm" style="background:{{ $palette[$si % count($palette)] }}"></span>
                    {{ $s['name'] }}
                </span>
            @endforeach
        </div>

    @elseif ($task->chart_type === 'pie')
        @php
            $segments = $data['segments'] ?? [];
            $total = collect($segments)->sum('value') ?: 1;
            $acc = 0;
            $stops = collect($segments)->map(function ($s, $i) use (&$acc, $total, $palette) {
                $start = round($acc / $total * 360, 2);
                $acc += $s['value'];
                $end = round($acc / $total * 360, 2);
                return $palette[$i % count($palette)]." {$start}deg {$end}deg";
            })->implode(', ');
        @endphp
        <div class="flex flex-wrap items-center gap-8">
            <div class="h-44 w-44 shrink-0 rounded-full" style="background: conic-gradient({{ $stops }});"></div>
            <div class="space-y-2">
                @foreach ($segments as $i => $s)
                    <div class="flex items-center gap-2 text-sm text-ink/80">
                        <span class="h-3 w-3 shrink-0 rounded-sm" style="background:{{ $palette[$i % count($palette)] }}"></span>
                        {{ $s['label'] }} — <strong class="text-ink">{{ $s['value'] }}{{ $unit }}</strong>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
