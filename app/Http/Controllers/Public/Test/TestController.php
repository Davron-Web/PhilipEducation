<?php

namespace App\Http\Controllers\Public\Test;

use App\Http\Controllers\Controller;
use App\Models\Test\Test;
use App\Models\Test\TestAttempt;
use App\Services\TestGradingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TestController extends Controller
{
    /**
     * Display a listing of tests, grouped by level.
     */
    public function index(): View
    {
        $tests = Test::with('lesson.level')
            ->where('is_published', true)
            ->withCount('questions')
            ->get();

        $testsByLevel = $tests->groupBy(function (Test $test) {
            return optional(optional($test->lesson)->level)->code ?? 'General';
        });

        return view('public.tests.index', compact('testsByLevel'));
    }

    /**
     * Display the specified test with its questions and answers.
     */
    public function show($id): View
    {
        $test = Test::with(['lesson.level', 'questions.answers'])->findOrFail($id);

        return view('public.tests.show', compact('test'));
    }

    /**
     * Проверяет ответы, сохраняет попытку и ведёт на разбор результата.
     */
    public function submit(Request $request, $id, TestGradingService $grading)
    {
        $test = Test::with('questions.answers')->where('is_published', true)->findOrFail($id);

        $data = $request->validate([
            'answers' => 'present|array',
            'duration_seconds' => 'nullable|integer|min:0|max:86400',
        ]);

        $attempt = $grading->grade(
            Auth::user(),
            $test,
            $data['answers'],
            $data['duration_seconds'] ?? null,
        );

        return redirect()->route('tests.result', $attempt->id);
    }

    /**
     * Результат попытки: балл, вердикт и разбор ошибок.
     */
    public function result($attempt): View
    {
        $attempt = TestAttempt::with(['test.questions.answers', 'answers'])->findOrFail($attempt);

        // Чужую попытку показывать нельзя — в ней ответы другого ученика.
        abort_unless($attempt->user_id === Auth::id(), 403);

        return view('public.tests.result', compact('attempt'));
    }
}
