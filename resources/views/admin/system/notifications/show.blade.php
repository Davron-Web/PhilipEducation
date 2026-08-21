@extends('layouts.admin')

@section('title', 'Уведомление: ' . $notification->title)

@section('content')
    <x-admin.page-header :title="$notification->title" :backRoute="route('admin.system.notifications.index')">
        <x-slot:badge>
            @if($notification->is_read)
                <span class="badge badge-success">Прочитано</span>
            @else
                <span class="badge badge-warning">Не прочитано</span>
            @endif
        </x-slot:badge>
    </x-admin.page-header>

    <div class="card">
        <h3>Информация</h3>
        <div class="info-row"><span class="info-label">ID</span><span class="info-value">#{{ $notification->id }}</span></div>
        <div class="info-row">
            <span class="info-label">Получатель</span>
            <span class="info-value">
                @if($notification->user)
                    <a class="chip-link" href="{{ route('admin.user.users.show', $notification->user) }}">{{ $notification->user->name ?? $notification->user->email }}</a>
                @else
                    —
                @endif
            </span>
        </div>
        <div class="info-row"><span class="info-label">Название</span><span class="info-value" style="font-weight:700">{{ $notification->title }}</span></div>
        <div class="info-row"><span class="info-label">Прочитано</span><span class="info-value">{{ $notification->read_at?->format('d.m.Y H:i') ?? 'Ещё не прочитано' }}</span></div>
        <div class="info-row"><span class="info-label">Отправлено</span><span class="info-value">{{ $notification->created_at?->format('d.m.Y H:i') }}</span></div>
    </div>

    <div class="card richtext-card">
        <div class="richtext-head"><h3>Сообщение</h3></div>
        <div class="richtext-body">{!! nl2br(e($notification->message)) !!}</div>
    </div>

    <div class="form-actions">
        @if(!$notification->is_read)
            <form action="{{ route('admin.system.notifications.mark-read', $notification) }}" method="POST">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-accent">Отметить как прочитанное</button>
            </form>
        @endif
        <form action="{{ route('admin.system.notifications.destroy', $notification) }}" method="POST" onsubmit="return confirm('Удалить уведомление?');">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger">Удалить уведомление</button>
        </form>
    </div>
@endsection
