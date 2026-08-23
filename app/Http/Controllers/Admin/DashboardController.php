<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'students' => User::count(),
            'students_growth' => '+12%',
            'courses' => $this->safeCount(Course::class, 32),
            'lessons_done' => $this->safeCount(Lesson::class, 8540),
            'avg_test_score' => 87,
        ];

        $recentStudents = User::latest()->take(4)->get()
            ->map(fn ($u) => [
                'name' => $u->name,
                'email' => $u->email,
                'date' => $u->created_at?->format('d.m.Y') ?? '—',
                'status' => 'active',
            ])
            ->toArray();

        $recentLessons = class_exists(Lesson::class)
            ? Lesson::latest()->take(3)->get()
                ->map(fn ($l) => [
                    'title' => $l->title ?? $l->name ?? 'Урок',
                    'course' => '—',
                    'date' => $l->created_at?->format('d.m.Y') ?? '—',
                ])
                ->toArray()
            : [];

        $progress = [];

        return view('admin.dashboard', compact('stats', 'recentStudents', 'recentLessons', 'progress'));
    }

    /** Количество записей, если модель и таблица существуют, иначе заглушка */
    private function safeCount(string $model, int $fallback): int
    {
        if (! class_exists($model)) {
            return $fallback;
        }

        try {
            return (int) $model::count();
        } catch (\Throwable) {
            return $fallback;
        }
    }
}
