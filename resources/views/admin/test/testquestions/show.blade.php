@extends('layouts.admin')

@section('title', 'Вопрос теста #' . $testQuestion->id)

@section('content')
    <x-admin.page-header :title="'Вопрос теста #' . $testQuestion->id" :backRoute="route('admin.test.testquestions.index')">
        <x-slot:actions>
            <a href="{{ route('admin.test.testquestions.edit', $testQuestion) }}" class="btn btn-warning">Изменить</a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="card">
        <h3>Информация</h3>
        <div class="info-row"><span class="info-label">ID</span><span class="info-value">#{{ $testQuestion->id }}</span></div>
        <div class="info-row">
            <span class="info-label">Тест</span>
            <span class="info-value">
                @if($testQuestion->test)
                    <a class="chip-link" href="{{ route('admin.test.tests.show', $testQuestion->test) }}">{{ $testQuestion->test->title }}</a>
                @else
                    —
                @endif
            </span>
        </div>
        <div class="info-row"><span class="info-label">Тип</span><span class="info-value"><span class="badge badge-{{ $testQuestion->type_badge_color }}">{{ $testQuestion->type_label }}</span></span></div>
        <div class="info-row"><span class="info-label">Баллы</span><span class="info-value"><span class="badge badge-primary">{{ $testQuestion->points }}</span></span></div>
        <div class="info-row"><span class="info-label">Создано</span><span class="info-value">{{ optional($testQuestion->created_at)->format('d.m.Y H:i') }}</span></div>
        <div class="info-row"><span class="info-label">Обновлено</span><span class="info-value">{{ optional($testQuestion->updated_at)->format('d.m.Y H:i') }}</span></div>
    </div>

    <div class="card richtext-card">
        <div class="richtext-head"><h3>Вопрос</h3></div>
        <div class="richtext-body">{!! nl2br(e($testQuestion->question)) !!}</div>
    </div>

    @if($testQuestion->answers->isNotEmpty())
        <div class="card table-card">
            <div class="table-head"><h3>Варианты ответов <small>{{ $testQuestion->answers->count() }}</small></h3></div>
            <div class="table-scroll">
                <table class="tbl">
                    <thead><tr><th>Ответ</th><th style="width:140px">Правильный</th></tr></thead>
                    <tbody>
                    @foreach($testQuestion->answers as $answer)
                        <tr>
                            <td>{{ $answer->answer }}</td>
                            <td>
                                @if($answer->is_correct)
                                    <span class="badge badge-success">✓ Да</span>
                                @else
                                    <span class="badge badge-secondary">Нет</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <form action="{{ route('admin.test.testquestions.destroy', $testQuestion) }}" method="POST" onsubmit="return confirm('Удалить вопрос?');">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-danger">Удалить вопрос</button>
    </form>
@endsection
