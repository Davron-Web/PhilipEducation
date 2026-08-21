@extends('layouts.admin')

@section('title', ($grammarTopic ?? $grammar ?? $topic ?? null)?->title)

@section('content')
    @php
        $topic = $grammarTopic ?? $grammar ?? $topic ?? null;
        if (!$topic) abort(404);
        $desc = $topic->theory_content ?? $topic->theory ?? null;
    @endphp

    <x-admin.page-header :title="$topic->title" :backRoute="route('admin.content.grammartopics.index')">
        <x-slot:actions>
            <a href="{{ route('admin.content.grammartopics.edit', $topic->id) }}" class="btn btn-warning">Изменить</a>
            <form action="{{ route('admin.content.grammartopics.destroy', $topic->id) }}" method="POST" onsubmit="return confirm('Удалить правило?')" style="margin:0">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Удалить</button>
            </form>
        </x-slot:actions>
    </x-admin.page-header>

    <section class="stats-grid">
        <x-admin.stat-card color="yellow" label="Уровень" :value="$topic->level?->name ?? '—'"
            icon='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>' />
        <x-admin.stat-card color="blue" label="Порядок" :value="'№ ' . ($topic->order_number ?? '—')"
            icon='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="18" x2="20" y2="18"/></svg>' />
        <x-admin.stat-card color="yellow" label="Правило" :value="'#' . $topic->id"
            icon='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 4V15l13.5-13.5z"/></svg>' />
    </section>

    <div class="show-grid">
        <div class="card">
            <h3>Теория правила</h3>
            <div class="prose-text">{{ $desc ?? 'Теория пока не добавлена.' }}</div>
        </div>
        <div class="card">
            <h3>Информация</h3>
            <div class="info-row"><span class="info-label">ID</span><span class="info-value">#{{ $topic->id }}</span></div>
            <div class="info-row"><span class="info-label">Уровень</span><span class="info-value">{{ $topic->level?->name ?? '—' }}</span></div>
            <div class="info-row"><span class="info-label">Создано</span><span class="info-value">{{ $topic->created_at?->format('d.m.Y H:i') }}</span></div>
            <div class="info-row"><span class="info-label">Обновлено</span><span class="info-value">{{ $topic->updated_at?->format('d.m.Y H:i') }}</span></div>
        </div>
    </div>
@endsection
