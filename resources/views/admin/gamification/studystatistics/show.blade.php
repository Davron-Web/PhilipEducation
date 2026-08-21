@extends('layouts.admin')

@section('title', 'Статистика #' . $studyStatistic->id)

@section('content')
    <x-admin.page-header :title="'Статистика обучения #' . $studyStatistic->id" :backRoute="route('admin.gamification.studystatistics.index')">
        <x-slot:actions>
            <a href="{{ route('admin.gamification.studystatistics.edit', $studyStatistic) }}" class="btn btn-warning">Изменить</a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="card">
        <h3>Информация</h3>
        <div class="info-row"><span class="info-label">ID</span><span class="info-value">#{{ $studyStatistic->id }}</span></div>
        <div class="info-row"><span class="info-label">Пользователь</span><span class="info-value">{{ $studyStatistic->user?->name ?? '—' }}</span></div>
        <div class="info-row"><span class="info-label">Уроков пройдено</span><span class="info-value">{{ $studyStatistic->total_lessons_completed }}</span></div>
        <div class="info-row"><span class="info-label">Тестов сдано</span><span class="info-value">{{ $studyStatistic->total_tests_passed }}</span></div>
        <div class="info-row"><span class="info-label">Слов изучено</span><span class="info-value">{{ $studyStatistic->total_words_learned }}</span></div>
        <div class="info-row"><span class="info-label">Время изучения (мин)</span><span class="info-value">{{ $studyStatistic->study_time_minutes }}</span></div>
        <div class="info-row"><span class="info-label">Создано</span><span class="info-value">{{ optional($studyStatistic->created_at)->format('d.m.Y H:i') }}</span></div>
        <div class="info-row"><span class="info-label">Обновлено</span><span class="info-value">{{ optional($studyStatistic->updated_at)->format('d.m.Y H:i') }}</span></div>
    </div>
@endsection
