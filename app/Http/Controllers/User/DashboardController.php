<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Content\Lesson;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the user dashboard with progress stats.
     */
    public function index()
    {
        $user = Auth::user();

        $lessonsCompleted = $user->progress()->where('is_completed', true)->count();
        $wordsLearned = $user->words()->wherePivot('learned', true)->count();
        $testsPassed = $user->results()->where('passed', true)->count();
        $achievementsCount = $user->achievements()->count();

        $totalLessons = Lesson::where('is_published', true)->count();

        $stats = [
            'lessons_completed' => $lessonsCompleted,
            'lessons_progress' => $totalLessons ? round(min(100, ($lessonsCompleted / $totalLessons) * 100)) : 0,
            'words_learned' => $wordsLearned,
            'words_progress' => round(min(100, ($wordsLearned / 500) * 100)),
            'tests_passed' => $testsPassed,
            'tests_progress' => round(min(100, ($testsPassed / 20) * 100)),
            'achievements_count' => $achievementsCount,
        ];

        return view('user.dashboard', compact('stats'));
    }
}
