@extends('layouts.admin')

@section('title', $title->name)

@section('content')
    <x-admin.page-header :title="$title->name" :backRoute="route('admin.gamification.titles.index')">
        <x-slot:actions>
            <a href="{{ route('admin.gamification.titles.edit', $title) }}" class="btn btn-warning">Изменить</a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="card">
        <div style="display:flex;align-items:center;gap:16px;margin-bottom:16px">
            <span style="font-size:40px">{{ $title->icon ?: '🏅' }}</span>
            <div>
                <div style="font-weight:700;font-size:18px">{{ $title->name }}</div>
                <div style="opacity:.7">{{ $title->description ?: 'Без описания' }}</div>
            </div>
        </div>
        <p>Порог: <strong>{{ number_format($title->min_xp, 0, ',', ' ') }} XP</strong></p>
        <p>Получили учеников: <strong>{{ $title->users_count }}</strong></p>
        <p>Статус: <span class="badge badge-{{ $title->is_active ? 'success' : 'warning' }}">{{ $title->is_active ? 'Активен' : 'Скрыт' }}</span></p>
    </div>

    <div class="card table-card">
        <div class="table-scroll">
            <table class="tbl">
                <thead><tr><th>Ученик</th><th>Email</th><th style="width:180px">Получен</th></tr></thead>
                <tbody>
                @forelse($holders as $holder)
                    <tr>
                        <td class="title-cell">{{ $holder->name }}</td>
                        <td class="text-cell">{{ $holder->email }}</td>
                        <td>{{ optional($holder->pivot->earned_at)->format('d.m.Y') ?: '—' }}</td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="3">Титул пока никто не получил</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($holders->hasPages())
        <div class="pagination">{{ $holders->links() }}</div>
    @endif
@endsection