@extends('layouts.admin')

@section('title', 'Уровни')

@section('content')
    <x-admin.page-header title="Уровни" />

    <div class="card">
        <form method="POST" action="{{ route('admin.system.levels.store') }}" class="filters">
            @csrf
            <input type="text" name="name" class="input" placeholder="Название уровня (Beginner, Intermediate...)" value="{{ old('name') }}" required>
            <input type="text" name="code" class="input" style="max-width:200px" placeholder="Код (A1, A2, B1...)" value="{{ old('code') }}" required>
            <button type="submit" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Добавить уровень
            </button>
        </form>
    </div>

    <div class="item-grid">
        @forelse($levels as $level)
            <div class="card item-card">
                <div class="item-top">
                    <div class="item-icon yellow">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
                    </div>
                    <span class="badge badge-primary">{{ $level->code }}</span>
                </div>
                <h3>{{ $level->name }}</h3>
                <div class="item-actions">
                    <form action="{{ route('admin.system.levels.destroy', $level) }}" method="POST" onsubmit="return confirm('Удалить уровень?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                    </form>
                </div>
            </div>
        @empty
            <x-admin.empty-state message="Уровней пока нет — добавьте первый через форму выше." />
        @endforelse
    </div>
@endsection
