<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\TestResource;
use App\Models\Test\Test;
use App\Services\TestGradingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TestController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $tests = Test::withCount('questions')
            ->addSelect(['best_score' => \App\Models\User\UserResult::selectRaw('max(score)')
                ->whereColumn('test_id', 'tests.id')
                ->where('user_id', $request->user()->id)])
            ->orderBy('id')
            ->paginate(min(50, (int) $request->input('per_page', 20)));

        return TestResource::collection($tests);
    }

    /**
     * Вопросы теста без правильных ответов.
     *
     * Варианты отдаём без флага is_correct: иначе мобильный клиент получал
     * бы ключ к тесту вместе с самим тестом.
     */
    public function show(Test $test): JsonResponse
    {
        $test->load('questions.answers');

        return response()->json([
            'data' => [
                'id' => $test->id,
                'title' => $test->title,
                'passing_score' => (int) $test->passing_score,
                'questions' => $test->questions->map(fn ($question) => [
                    'id' => $question->id,
                    'question' => $question->question,
                    'type' => $question->type,
                    'options' => $question->type === 'text'
                        ? []
                        : $question->answers->map(fn ($answer) => [
                            'id' => $answer->id,
                            'answer' => $answer->answer,
                        ])->values(),
                ])->values(),
            ],
        ]);
    }

    public function submit(Request $request, Test $test, TestGradingService $grading): JsonResponse
    {
        $data = $request->validate([
            'answers' => ['required', 'array'],
            'duration_seconds' => ['nullable', 'integer', 'min:0', 'max:14400'],
        ]);

        $attempt = $grading->grade(
            $request->user(),
            $test,
            $data['answers'],
            $data['duration_seconds'] ?? null,
        );

        return response()->json([
            'data' => [
                'attempt_id' => $attempt->id,
                'score' => (int) $attempt->score,
                'passed' => (bool) $attempt->passed,
                'xp_total' => (int) $request->user()->fresh()->points,
            ],
        ]);
    }
}
