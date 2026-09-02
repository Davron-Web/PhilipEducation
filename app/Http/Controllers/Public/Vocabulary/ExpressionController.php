<?php

namespace App\Http\Controllers\Public\Vocabulary;

use App\Http\Controllers\Controller;
use App\Models\User\UserExpression;
use App\Models\Vocabulary\Expression;
use App\Services\AchievementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ExpressionController extends Controller
{
    /**
     * Display a listing of expressions (idioms, phrasal verbs, proverbs,
     * collocations). Mirrors WordController::index(): no ?category query
     * shows the category picker grid, a category (or "all") shows the
     * filtered list/flashcards, further narrowed by optional type/level/
     * search query params.
     */
    public function index(Request $request): View
    {
        $expressions = Expression::with(['translations', 'level'])->orderBy('text')->orderBy('id')->get();

        $learnedExpressionIds = Auth::check()
            ? Auth::user()->expressions()->wherePivot('learned', true)->pluck('expressions.id')->all()
            : [];

        $categoryCounts = $expressions->groupBy('category')->filter(fn ($group, $key) => $key !== '')->map->count()->sortKeys();

        $selectedCategory = $request->query('category');
        $selectedType = $request->query('type');
        $selectedLevel = $request->query('level');
        $search = trim((string) $request->query('search'));

        if ($selectedCategory && $selectedCategory !== 'all') {
            $expressions = $expressions->filter(fn ($expression) => $expression->category === $selectedCategory)->values();
        }

        if ($selectedType) {
            $expressions = $expressions->filter(fn ($expression) => $expression->type === $selectedType)->values();
        }

        if ($selectedLevel) {
            $expressions = $expressions->filter(fn ($expression) => $expression->level?->code === $selectedLevel)->values();
        }

        if ($search !== '') {
            $expressions = $expressions->filter(fn ($expression) => str_contains(
                mb_strtolower($expression->text),
                mb_strtolower($search)
            ))->values();
        }

        $types = [
            'idiom' => 'Идиома',
            'phrasal_verb' => 'Фразовый глагол',
            'proverb' => 'Пословица',
            'collocation' => 'Коллокация',
        ];

        return view('public.expressions.index', compact(
            'expressions',
            'learnedExpressionIds',
            'categoryCounts',
            'selectedCategory',
            'selectedType',
            'selectedLevel',
            'search',
            'types'
        ));
    }

    /**
     * Display the specified expression.
     */
    public function show($id): View
    {
        $expression = Expression::with(['translations', 'level'])->findOrFail($id);

        $isLearned = Auth::check()
            ? Auth::user()->expressions()->wherePivot('learned', true)->where('expressions.id', $id)->exists()
            : false;

        return view('public.expressions.show', compact('expression', 'isLearned'));
    }

    /**
     * Отметить выражение как выученное/на повторении для текущего
     * пользователя (карточки в режиме флеш-карт) — зеркало
     * WordController::markProgress().
     */
    public function markProgress(Request $request, Expression $expression, AchievementService $achievements)
    {
        $request->validate(['learned' => 'required|boolean']);

        Auth::user()->expressions()->syncWithoutDetaching([
            $expression->id => [
                'learned' => $request->boolean('learned'),
                'last_reviewed_at' => now(),
            ],
        ]);

        if ($request->boolean('learned')) {
            $achievements->checkAndAward(Auth::user(), 'expressions_learned');
        }

        if ($request->wantsJson()) {
            return response()->json(['ok' => true]);
        }

        return back();
    }

    /**
     * Записать результат ответа в мини-квизе «Проверь себя» (см.
     * expressions/index.blade.php, режим quiz). В отличие от markProgress
     * (который просто помечает «выучено»), здесь считаем правильные и
     * неправильные ответы — поля correct_answers/wrong_answers уже были
     * в user_expressions с самого начала, но раньше их никто не заполнял.
     */
    public function recordPractice(Request $request, Expression $expression)
    {
        $request->validate(['correct' => 'required|boolean']);

        $pivot = UserExpression::firstOrNew([
            'user_id' => Auth::id(),
            'expression_id' => $expression->id,
        ]);

        if ($request->boolean('correct')) {
            $pivot->correct_answers = ($pivot->correct_answers ?? 0) + 1;
        } else {
            $pivot->wrong_answers = ($pivot->wrong_answers ?? 0) + 1;
        }
        $pivot->learned = $pivot->learned ?? false;
        $pivot->last_reviewed_at = now();
        $pivot->save();

        return response()->json(['ok' => true]);
    }

    /**
     * Добавить своё выражение (без привязки к уроку) и сразу сохранить
     * его перевод — зеркало WordController::store().
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'text' => 'required|string|max:255',
            'type' => 'required|in:idiom,phrasal_verb,proverb,collocation',
            'translation' => 'required|string|max:255',
            'meaning' => 'nullable|string|max:500',
            'example' => 'nullable|string|max:500',
        ]);

        $expression = Expression::create([
            'text' => $data['text'],
            'type' => $data['type'],
            'meaning' => $data['meaning'] ?? null,
            'example' => $data['example'] ?? null,
        ]);

        $expression->translations()->create([
            'language' => 'ru',
            'translation' => $data['translation'],
        ]);

        Auth::user()->expressions()->syncWithoutDetaching([$expression->id => ['learned' => false]]);

        return redirect()->route('expressions.index')->with('success', "Выражение «{$expression->text}» добавлено!");
    }
}
