@extends('layouts.app')

@section('title', $topic->title)
@section('page_title', 'Grammar')
@section('page_description', optional($topic->level)->name ?? 'Grammar topic')

@section('content')
    <div class="mb-3">
        <a href="{{ route('grammartopics.index') }}" class="link-secondary text-decoration-none small">
            <i class="bi bi-arrow-left me-1"></i>Back to grammar topics
        </a>
    </div>

    <div class="card shadow-sm rounded-4 border-0">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-2 mb-2">
                <x-level-badge :level="$topic->level" />
            </div>
            <h1 class="h4 fw-bold mb-3">{{ $topic->title }}</h1>
            <div class="text-body" style="white-space: pre-line;">{{ $topic->theory }}</div>
        </div>
    </div>
@endsection
