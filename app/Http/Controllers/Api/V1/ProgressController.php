<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ProgressResource;
use App\Models\Content\Lesson;
use App\Models\Gamification\Achievement;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    public function __invoke(Request $request): ProgressResource
    {
        $user = $request->user();
        $title = $user->currentTitle();

        return new ProgressResource([
            'xp' => (int) $user->points,
            'title' => $title?->only(['code', 'name', 'icon', 'min_xp']),
            'lessons_completed' => $user->progress()->where('is_completed', true)->count(),
            'lessons_total' => Lesson::where('is_published', true)->count(),
            'words_learned' => $user->words()->wherePivot('learned', true)->count(),
            'tests_passed' => $user->results()->where('passed', true)->count(),
            'achievements_earned' => $user->achievements()->count(),
            'achievements_total' => Achievement::count(),
            'hours_studied' => round($user->progress()->sum('time_spent') / 3600, 1),
        ]);
    }
}
