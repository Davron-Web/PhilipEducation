@extends('layouts.admin')

@section('title', 'Редактировать сертификат')

@section('content')
    <x-admin.page-header :title="'Редактировать сертификат: ' . $certificate->certificate_number" :backRoute="route('admin.certificate.certificates.index')" />

    <form action="{{ route('admin.certificate.certificates.update', $certificate) }}" method="POST" enctype="multipart/form-data" class="card">
        @csrf
        @method('PUT')
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Пользователь *</label>
                <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                    <option value="">Выберите пользователя</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id', $certificate->user_id) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
                @error('user_id')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Уровень *</label>
                <select name="level_id" class="form-select @error('level_id') is-invalid @enderror" required>
                    <option value="">Выберите уровень</option>
                    @foreach($levels as $level)
                        <option value="{{ $level->id }}" {{ old('level_id', $certificate->level_id) == $level->id ? 'selected' : '' }}>{{ $level->name }}</option>
                    @endforeach
                </select>
                @error('level_id')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Номер сертификата *</label>
                <input type="text" name="certificate_number" class="form-input @error('certificate_number') is-invalid @enderror" value="{{ old('certificate_number', $certificate->certificate_number) }}" required>
                @error('certificate_number')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Дата выдачи *</label>
                <input type="date" name="issued_at" class="form-input @error('issued_at') is-invalid @enderror" value="{{ old('issued_at', $certificate->issued_at?->format('Y-m-d')) }}" required>
                @error('issued_at')<div class="error-text">{{ $message }}</div>@enderror
            </div>
            <div class="form-group full">
                <label class="form-label">Текущий файл</label>
                @if($certificate->file_path)
                    <div><a href="{{ asset('storage/' . $certificate->file_path) }}" target="_blank" class="chip-link">Просмотреть текущий файл</a></div>
                @else
                    <div style="color:var(--muted)">Файл не загружен</div>
                @endif
            </div>
            <div class="form-group full">
                <label class="form-label">Новый файл (заменит текущий)</label>
                <input type="file" name="file" class="form-input @error('file') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                @error('file')<div class="error-text">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Сохранить изменения</button>
            <a href="{{ route('admin.certificate.certificates.index') }}" class="btn btn-ghost">Отмена</a>
        </div>
    </form>
@endsection
