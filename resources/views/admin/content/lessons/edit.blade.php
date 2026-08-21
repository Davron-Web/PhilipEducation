@extends('layouts.admin')

@section('title', 'Редактировать урок')

@section('content')
    @php
        $hasStatus = \Illuminate\Support\Facades\Schema::hasColumn('lessons', 'is_published');
        $levels = $levels ?? \App\Models\Level::orderBy('name')->get();
    @endphp

    <x-admin.page-header title="Редактировать урок" :backRoute="route('admin.content.lessons.show', $lesson)">
        <x-slot:actions>
            <form action="{{ route('admin.content.lessons.destroy', $lesson) }}" method="POST" onsubmit="return confirm('Удалить урок?')" style="margin:0">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Удалить урок</button>
            </form>
        </x-slot:actions>
    </x-admin.page-header>

    <form action="{{ route('admin.content.lessons.update', $lesson) }}" method="POST" class="card">
        @csrf
        @method('PUT')
        <h3>Данные урока</h3>
        <div class="form-grid">
            <div class="form-group full">
                <label class="form-label">Название урока *</label>
                <input type="text" name="title" class="form-input" value="{{ old('title', $lesson->title) }}" required>
            </div>

            <div class="form-group full">
                <label class="form-label">Краткое описание</label>
                <textarea name="description" class="form-textarea" rows="3" placeholder="Короткое описание урока для списка и карточки">{{ old('description', $lesson->description) }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Уровень</label>
                <select name="level_id" class="form-select">
                    <option value="">— не выбран —</option>
                    @foreach($levels as $level)
                        <option value="{{ $level->id }}" {{ old('level_id', $lesson->level_id) == $level->id ? 'selected' : '' }}>{{ $level->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Порядковый номер</label>
                <input type="number" name="order_number" class="form-input" value="{{ old('order_number', $lesson->order_number) }}" min="1">
            </div>

            <div class="form-group">
                <label class="form-label">Длительность (минут)</label>
                <input type="number" name="estimated_minutes" class="form-input" value="{{ old('estimated_minutes', $lesson->estimated_minutes) }}" min="1">
            </div>

            @if($hasStatus)
                <div class="form-group">
                    <label class="form-label">Статус</label>
                    <div class="form-check">
                        <label class="switch">
                            <input type="hidden" name="is_published" value="0">
                            <input type="checkbox" name="is_published" value="1" {{ old('is_published', $lesson->is_published) ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                        <span>Опубликовать урок</span>
                    </div>
                </div>
            @endif
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Сохранить изменения
            </button>
            <a href="{{ route('admin.content.lessons.show', $lesson) }}" class="btn btn-ghost">Отмена</a>
        </div>
    </form>

    <div class="card table-card">
        <div class="table-head">
            <h3>Содержание урока <small>{{ $lesson->contents->count() }}</small></h3>
            <a href="{{ route('admin.content.lessoncontents.create') }}?lesson_id={{ $lesson->id }}" class="btn btn-sm btn-primary">Добавить блок</a>
        </div>
        @forelse($lesson->contents->sortBy('order_number') as $block)
            <div class="richtext-card" style="margin-top:14px">
                <div class="richtext-head">
                    <h3>{{ $block->title ?: 'Блок #' . $block->order_number }} <span class="badge badge-primary">{{ $block->type }}</span></h3>
                    <div class="actions">
                        <a href="{{ route('admin.content.lessoncontents.edit', $block) }}" class="btn btn-sm btn-warning">Изменить</a>
                        <form action="{{ route('admin.content.lessoncontents.destroy', $block) }}" method="POST" onsubmit="return confirm('Удалить блок контента?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                        </form>
                    </div>
                </div>
                <div class="richtext-body">{!! $block->content !!}</div>
            </div>
        @empty
            <div class="empty-row" style="margin-top:14px">Содержание пока не добавлено. Нажмите «Добавить блок», чтобы создать его.</div>
        @endforelse
    </div>
@endsection
