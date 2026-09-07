<?php

namespace App\Http\Controllers\Public\Test;

use App\Http\Controllers\Controller;
use App\Models\Test\Test;
use App\Models\Test\TestAttempt;
use App\Models\Test\TestDraft;
use App\Services\TestGradingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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

        // Незаконченная попытка этого ученика — подставим его прежние ответы.
        $draft = TestDraft::where('user_id', Auth::id())
            ->where('test_id', $test->id)
            ->first();

        return view('public.tests.show', compact('test', 'draft'));
    }

    /**
     * Сохраняет незавершённый тест.
     *
     * Вызывается со страницы теста по мере ответов, поэтому отвечает пустым
     * 204: показывать тут нечего, а лишний JSON только гоняет данные.
     */
    public function saveDraft(Request $request, $id): Response
    {
        $test = Test::where('is_published', true)->findOrFail($id);

        $data = $request->validate([
            'answers' => 'present|array',
            'seconds_spent' => 'nullable|integer|min:0|max:86400',
        ]);

        TestDraft::updateOrCreate(
            ['user_id' => Auth::id(), 'test_id' => $test->id],
            [
                'answers' => $data['answers'],
                'seconds_spent' => $data['seconds_spent'] ?? 0,
            ]
        );

        return response()->noContent();
    }

    /** Начать тест заново, отбросив сохранённые ответы. */
    public function discardDraft($id): RedirectResponse
    {
        TestDraft::where('user_id', Auth::id())->where('test_id', $id)->delete();

        return redirect()->route('tests.show', $id);
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

        // Тест отправлен — черновику больше незачем существовать.
        TestDraft::where('user_id', Auth::id())->where('test_id', $test->id)->delete();

        return redirect()->route('tests.result', $attempt->id);
    }

    /**
     * Результат попытки: балл, вердикт и разбор ошибок.
     */
    public function result($attempt): View
    {
        $attempt = TestAttempt::with(['test.questions.answers', 'answers'])->findOrFail($attempt);

        // Чужую попытку показывать нельзя — в ней ответы другого ученика.
        $this->authorize('view', $attempt);

        return view('public.tests.result', compact('attempt'));
    }
}
