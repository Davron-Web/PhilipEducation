@extends('layouts.admin')

@section('title', 'Сертификаты')

@section('content')
    <x-admin.page-header title="Сертификаты">
        <x-slot:actions>
            <a href="{{ route('admin.certificate.certificates.create') }}" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Добавить сертификат
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="card">
        <form method="GET" class="filters">
            <input type="text" name="search" class="input" placeholder="Поиск по номеру сертификата..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-ghost">Фильтр</button>
            <a href="{{ route('admin.certificate.certificates.index') }}" class="btn btn-ghost">Сброс</a>
        </form>
    </div>

    <div class="card table-card">
        <div class="table-scroll">
            <table class="tbl">
                <thead>
                <tr>
                    <th style="width:70px">ID</th>
                    <th>Пользователь</th>
                    <th>Уровень</th>
                    <th>Номер сертификата</th>
                    <th>Дата выдачи</th>
                    <th>Файл</th>
                    <th style="width:220px;text-align:right">Действия</th>
                </tr>
                </thead>
                <tbody>
                @forelse($certificates as $certificate)
                    <tr>
                        <td class="id-cell">#{{ $certificate->id }}</td>
                        <td class="title-cell">{{ $certificate->user->name ?? '—' }}</td>
                        <td><span class="badge badge-primary">{{ $certificate->level->name ?? '—' }}</span></td>
                        <td class="text-cell">{{ $certificate->certificate_number }}</td>
                        <td>{{ $certificate->issued_at?->format('d.m.Y') }}</td>
                        <td>
                            @if($certificate->file_path)
                                <a class="chip-link" href="{{ asset('storage/' . $certificate->file_path) }}" target="_blank">Скачать</a>
                            @else
                                <span class="id-cell">Нет файла</span>
                            @endif
                        </td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('admin.certificate.certificates.show', $certificate) }}" class="btn btn-sm btn-ghost">Просмотр</a>
                                <a href="{{ route('admin.certificate.certificates.edit', $certificate) }}" class="btn btn-sm btn-warning">Изменить</a>
                                <form action="{{ route('admin.certificate.certificates.destroy', $certificate) }}" method="POST" onsubmit="return confirm('Удалить сертификат?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Удалить</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row"><td colspan="7">Сертификаты не найдены</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(method_exists($certificates, 'hasPages') && $certificates->hasPages())
        <div class="pagination">{{ $certificates->withQueryString()->links() }}</div>
    @endif
@endsection
