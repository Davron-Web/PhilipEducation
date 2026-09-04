<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\LessonResource;
use App\Models\Content\Lesson;
use App\Services\LevelProgressService;
use App\Services\XpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LessonController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();

        $lessons = Lesson::query()
            ->with('level')
            ->where('is_published', true)
            ->when($request->input('level'), fn ($q, $code) => $q->whereHas('level', fn ($l) => $l->where('code', $code)))
            // Отметку «пройден» подтягиваем подзапросом: иначе на каждый урок
            // в списке шёл бы отдельный запрос за прогрессом.
            ->addSelect(['user_is_completed' => \App\Models\User\UserProgress::selectRaw('is_completed')
                ->whereColumn('lesson_id', 'lessons.id')
                ->where('user_id', $user->id)
                ->limit(1)])
            ->orderBy('order_number')
            ->paginate(min(50, (int) $request->input('per_page', 20)));

        return LessonResource::collection($lessons);
    }

    public function show(Request $request, Lesson $lesson): LessonResource|JsonResponse
    {
        abort_unless($lesson->is_published, 404);

        // Платные уровни закрыты тем же правилом, что и на сайте.
        if (! $request->user()->canAccessLevel($lesson->level?->code)) {
            return response()->json(['message' => 'Требуется подписка'], 403);
        }

        return new LessonResource($lesson->load('level'));
    }

    public function complete(Request $request, Lesson $lesson, XpService $xp, LevelProgressService $levels): JsonResponse
    {
        abort_unless($lesson->is_published, 404);

        $user = $request->user();

        if (! $user->canAccessLevel($lesson->level?->code)) {
            return response()->json(['message' => 'Требуется подписка'], 403);
        }

        $data = $request->validate([
            'seconds_spent' => ['nullable', 'integer', 'min:0', 'max:14400'],
        ]);

        $user->progress()->updateOrCreate(
            ['lesson_id' => $lesson->id],
            [
                'progress_percent' => 100,
                'is_completed' => true,
                'completed_at' => now(),
                'time_spent' => $data['seconds_spent'] ?? 0,
            ]
        );

        $awarded = $xp->award($user, 'lesson', $lesson->id);
        $levels->checkAfterLesson($user, $lesson);

        return response()->json([
            'message' => 'Урок отмечен пройденным',
            'xp_awarded' => $awarded,
            'xp_total' => (int) $user->fresh()->points,
        ]);
    }
}
