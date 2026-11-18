<?php

namespace App\Http\Controllers\Public\Placement;

use App\Http\Controllers\Controller;
use App\Models\Placement\PlacementAttempt;
use App\Models\Placement\PlacementQuestion;
use App\Services\PlacementGradingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Тест на определение уровня: 50 вопросов A1-C2 за один проход,
 * без таймера на каждый вопрос (в отличие от IELTS-заданий — здесь
 * нужна точность, а не скорость).
 */
class PlacementController extends Controller
{
    /**
     * Что уровень означает на практике — не карточка курса ("что пройдём"),
     * а короткая оценка "что вы уже умеете", специально для страницы
     * результата. Тексты авторские, ориентир — стандартные формулировки
     * CEFR (Common European Framework of Reference), не привязаны к
     * levels.description в БД: то поле уже занято описанием программы
     * курса и используется в других местах сайта.
     */
    private const PRACTICAL_DESCRIPTIONS = [
        'A1' => 'Вы понимаете и используете простые фразы и выражения, можете представиться и задать собеседнику базовые вопросы о нём — при условии, что говорят медленно и чётко.',
        'A2' => 'Вы справляетесь в привычных бытовых ситуациях: покупки, дорога, кафе — и можете коротко рассказать о себе, семье и работе простыми предложениями.',
        'B1' => 'Вы уверенно общаетесь на знакомые темы, включая путешествия, учёбу и работу, понимаете основную суть фильмов и статей и можете объяснить своё мнение.',
        'B2' => 'Вы свободно ведёте беседу с носителями языка, обсуждаете абстрактные и профессиональные темы и понимаете сложные тексты почти без словаря.',
        'C1' => 'Вы гибко и точно используете язык в учёбе и на работе, понимаете объёмные сложные тексты и улавливаете скрытые смыслы и стилистические нюансы.',
        'C2' => 'Вы владеете языком почти как носитель: понимаете любую устную и письменную речь и точно передаёте тончайшие оттенки смысла даже в сложных ситуациях.',
    ];

    /** Вступительная страница — объясняет тест, показывает прошлый результат. */
    public function intro(): View
    {
        $user = Auth::user();

        $lastAttempt = PlacementAttempt::where('user_id', $user->id)
            ->where('status', 'completed')
            ->with('resultLevel')
            ->latest('completed_at')
            ->first();

        $questionsCount = PlacementQuestion::count();

        return view('public.placement.intro', compact('lastAttempt', 'questionsCount'));
    }

    /** Создаёт попытку и ведёт к вопросам. */
    public function start(): RedirectResponse
    {
        $attempt = PlacementAttempt::create([
            'user_id' => Auth::id(),
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        return redirect()->route('tests.placement.show', $attempt->id);
    }

    /** Страница со всеми 50 вопросами одной формой. */
    public function show(PlacementAttempt $attempt): View|RedirectResponse
    {
        $this->authorize('view', $attempt);

        if ($attempt->isCompleted()) {
            return redirect()->route('tests.placement.result', $attempt->id);
        }

        $questions = PlacementQuestion::with('options')->orderBy('order_number')->get();

        return view('public.placement.show', compact('attempt', 'questions'));
    }

    /** Оценивает ответы и ведёт на разбор результата. */
    public function submit(Request $request, PlacementAttempt $attempt, PlacementGradingService $grading): RedirectResponse
    {
        $this->authorize('view', $attempt);

        if ($attempt->isCompleted()) {
            return redirect()->route('tests.placement.result', $attempt->id);
        }

        $data = $request->validate(['answers' => 'present|array']);

        $grading->grade(Auth::user(), $attempt, $data['answers']);

        return redirect()->route('tests.placement.result', $attempt->id);
    }

    /** Итог: определённый уровень и разбор по каждому уровню. */
    public function result(PlacementAttempt $attempt): View
    {
        $this->authorize('view', $attempt);

        $attempt->load(['resultLevel', 'answers.question.level']);

        $breakdown = $attempt->answers
            ->groupBy(fn ($answer) => $answer->question->level->code)
            ->map(function ($answers, $code) {
                $correct = $answers->where('is_correct', true)->count();
                $total = $answers->count();

                return [
                    'code' => $code,
                    'correct' => $correct,
                    'total' => $total,
                    'passed' => $total > 0 && $correct / $total >= PlacementGradingService::PASS_THRESHOLD,
                ];
            })
            ->sortBy(fn ($row, $code) => array_search($code, ['A1', 'A2', 'B1', 'B2', 'C1', 'C2']))
            ->values();

        $practicalDescription = self::PRACTICAL_DESCRIPTIONS[$attempt->resultLevel->code] ?? null;

        return view('public.placement.result', compact('attempt', 'breakdown', 'practicalDescription'));
    }
}
