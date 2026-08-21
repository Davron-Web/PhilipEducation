@extends('layouts.admin')

@section('title', 'Результат #' . $userResult->id)

@section('content')
    <x-admin.page-header :title="'Результат #' . $userResult->id" :backRoute="route('admin.user.userresults.index')">
        <x-slot:badge>
            @if($userResult->passed)
                <span class="badge badge-success">✓ Сдано</span>
            @else
                <span class="badge badge-danger">Не сдано</span>
            @endif
        </x-slot:badge>
        <x-slot:actions>
            <a href="{{ route('admin.user.userresults.edit', $userResult) }}" class="btn btn-warning">Изменить</a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="card">
        <h3>Информация</h3>
        <div class="info-row"><span class="info-label">ID</span><span class="info-value">#{{ $userResult->id }}</span></div>
        <div class="info-row">
            <span class="info-label">Пользователь</span>
            <span class="info-value">
                @if($userResult->user)
                    <a class="chip-link" href="{{ route('admin.user.users.show', $userResult->user) }}">{{ $userResult->user->name ?? $userResult->user->email }}</a>
                @else
                    —
                @endif
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">Тест</span>
            <span class="info-value">
                @if($userResult->test)
                    <a class="chip-link" href="{{ route('admin.test.tests.show', $userResult->test) }}">{{ $userResult->test->title }}</a>
                @else
                    —
                @endif
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">Балл</span>
            <span class="info-value">
                <div class="meter">
                    <div class="meter-track"><span class="meter-fill" style="width: {{ min($userResult->score, 100) }}%"></span></div>
                    <span>{{ $userResult->score }}%</span>
                </div>
            </span>
        </div>
        <div class="info-row"><span class="info-label">Дата попытки</span><span class="info-value">{{ $userResult->attempt_date?->format('d.m.Y H:i') }}</span></div>
        <div class="info-row"><span class="info-label">Создано</span><span class="info-value">{{ $userResult->created_at?->format('d.m.Y H:i') }}</span></div>
        <div class="info-row"><span class="info-label">Обновлено</span><span class="info-value">{{ $userResult->updated_at?->format('d.m.Y H:i') }}</span></div>

        <div class="form-actions" style="margin-top:16px">
            <form action="{{ route('admin.user.userresults.destroy', $userResult) }}" method="POST" onsubmit="return confirm('Удалить результат?');">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger">Удалить результат</button>
            </form>
        </div>
    </div>
@endsection
