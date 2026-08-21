<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Считаем реальные данные из базы (если связей нет, вернется 0)
        // Замените названия методов (completedLessons, learnedWords) на ваши реальные отношения (relationships) в модели User

        $lessonsCompleted = $user->completedLessons()->count(); // или $user->lessons_completed
        $wordsLearned = $user->learnedWords()->count();         // или $user->words_learned
        $testsPassed = $user->passedTests()->count();           // или $user->tests_passed

        // Общее количество выполненных заданий (можно хранить как отдельное поле в таблице users)
        $totalTasks = $user->total_tasks_completed ?? 0;

        // Вычисляем проценты для прогресс-баров (например, цель = 50 уроков)
        $lessonsProgress = min(100, ($lessonsCompleted / 50) * 100);
        $wordsProgress = min(100, ($wordsLearned / 500) * 100);
        $testsProgress = min(100, ($testsPassed / 20) * 100);

        $stats = [
            'lessons_completed' => $lessonsCompleted,
            'lessons_progress' => round($lessonsProgress),

            'words_learned' => $wordsLearned,
            'words_progress' => round($wordsProgress),

            'tests_passed' => $testsPassed,
            'tests_progress' => round($testsProgress),

            'total_tasks_completed' => $totalTasks,
        ];

        return view('user.dashboard', compact('stats')); // Укажите правильный путь к вашему view
    }
}
