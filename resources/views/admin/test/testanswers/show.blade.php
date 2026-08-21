@extends('layouts.admin')

@section('title', 'Ответ теста #' . $testAnswer->id)

@section('content')
    <x-admin.page-header :title="'Ответ теста #' . $testAnswer->id" :backRoute="route('admin.test.testanswers.index')">
        <x-slot:badge>
            @if($testAnswer->is_correct)
                <span class="badge badge-success">✓ Верно</span>
            @else
                <span class="badge badge-secondary">Неверно</span>
            @endif
        </x-slot:badge>
        <x-slot:actions>
            <a href="{{ route('admin.test.testanswers.edit', $testAnswer) }}" class="btn btn-warning">Изменить</a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="card">
        <h3>Информация</h3>
        <div class="info-row"><span class="info-label">ID</span><span class="info-value">#{{ $testAnswer->id }}</span></div>
        <div class="info-row">
            <span class="info-label">Вопрос</span>
            <span class="info-value">
                @if($testAnswer->question)
                    <a class="chip-link" href="{{ route('admin.test.testquestions.show', $testAnswer->question) }}">{{ $testAnswer->question->question }}</a>
                @else
                    —
                @endif
            </span>
        </div>
        <div class="info-row"><span class="info-label">Ответ</span><span class="info-value" style="font-weight:700">{{ $testAnswer->answer }}</span></div>
        <div class="info-row"><span class="info-label">Создано</span><span class="info-value">{{ optional($testAnswer->created_at)->format('d.m.Y H:i') }}</span></div>
        <div class="info-row"><span class="info-label">Обновлено</span><span class="info-value">{{ optional($testAnswer->updated_at)->format('d.m.Y H:i') }}</span></div>

        <div class="form-actions" style="margin-top:16px">
            <form action="{{ route('admin.test.testanswers.destroy', $testAnswer) }}" method="POST" onsubmit="return confirm('Удалить ответ?');">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger">Удалить ответ</button>
            </form>
        </div>
    </div>
@endsection
