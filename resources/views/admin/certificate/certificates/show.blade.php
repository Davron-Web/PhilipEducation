@extends('layouts.admin')

@section('title', 'Сертификат: ' . $certificate->certificate_number)

@section('content')
    <x-admin.page-header :title="'Сертификат: ' . $certificate->certificate_number" :backRoute="route('admin.certificate.certificates.index')">
        <x-slot:actions>
            <a href="{{ route('admin.certificate.certificates.edit', $certificate) }}" class="btn btn-warning">Изменить</a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="card">
        <h3>Информация</h3>
        <div class="info-row"><span class="info-label">ID</span><span class="info-value">#{{ $certificate->id }}</span></div>
        <div class="info-row"><span class="info-label">Номер сертификата</span><span class="info-value" style="font-weight:700">{{ $certificate->certificate_number }}</span></div>
        <div class="info-row">
            <span class="info-label">Пользователь</span>
            <span class="info-value">
                @if($certificate->user)
                    <a class="chip-link" href="{{ route('admin.user.users.show', $certificate->user) }}">{{ $certificate->user->name ?? $certificate->user->email }}</a>
                @else
                    —
                @endif
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">Уровень</span>
            <span class="info-value">
                @if($certificate->level)
                    <span class="badge badge-primary">{{ $certificate->level->name }}</span>
                @else
                    —
                @endif
            </span>
        </div>
        <div class="info-row"><span class="info-label">Дата выдачи</span><span class="info-value">{{ $certificate->issued_at?->format('d.m.Y') }}</span></div>
        <div class="info-row"><span class="info-label">Создано</span><span class="info-value">{{ optional($certificate->created_at)->format('d.m.Y H:i') }}</span></div>
        <div class="info-row"><span class="info-label">Обновлено</span><span class="info-value">{{ optional($certificate->updated_at)->format('d.m.Y H:i') }}</span></div>
    </div>

    <div class="card icon-card">
        @if($certificate->file_path)
            <div class="icon-card-wrap">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4M12 3l8 4.5v9L12 21l-8-4.5v-9L12 3Z"/></svg>
            </div>
            <a href="{{ asset('storage/' . $certificate->file_path) }}" target="_blank" class="btn btn-primary">Скачать сертификат</a>
        @else
            <div class="icon-card-wrap" style="opacity:.5">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M6 18L18 6"/></svg>
            </div>
            <span style="color:var(--muted)">Файл не загружен</span>
        @endif
    </div>

    <div class="form-actions">
        <form action="{{ route('admin.certificate.certificates.destroy', $certificate) }}" method="POST" onsubmit="return confirm('Удалить сертификат?');">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger">Удалить сертификат</button>
        </form>
    </div>
@endsection
