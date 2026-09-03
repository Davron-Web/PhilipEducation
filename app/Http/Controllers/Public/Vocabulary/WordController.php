<?php

namespace App\Http\Controllers\Public\Vocabulary;

use App\Http\Controllers\Controller;
use App\Models\User\UserWord;
use App\Models\Vocabulary\Word;
use App\Services\AchievementService;
use App\Services\SpacedRepetitionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class WordController extends Controller
{
    /**
     * Display a listing of words. The same word is often taught in several
     * lessons with different example sentences, so the dictionary view
     * shows one entry per unique word (earliest occurrence) instead of
     * every per-lesson duplicate.
     */
    public function index(Request $request): View
    {
        $allWords = Word::with(['translations', 'lesson'])->orderBy('word')->orderBy('id')->get();

        $words = $allWords->unique(fn ($word) => Str::lower($word->word))->values();

        $learnedWordIds = Auth::check()
            ? Auth::user()->words()->wherePivot('learned', true)->pluck('words.id')->all()
            : [];

        // A word counts as learned if the user learned it via *any* of its
        // per-lesson duplicates, not just the one shown here.
        $learnedWords = $allWords
            ->filter(fn ($word) => in_array($word->id, $learnedWordIds, true))
            ->map(fn ($word) => Str::lower($word->word))
            ->all();

        $learnedWordIds = $words
            ->filter(fn ($word) => in_array(Str::lower($word->word), $learnedWords, true))
            ->pluck('id')
            ->all();

        // Category counts are computed from the full set *before* filtering,
        // so the category picker always shows how many words each topic has.
        $categoryCounts = $words->groupBy('category')->filter(fn ($group, $key) => $key !== '')->map->count()->sortKeys();

        $selectedCategory = $request->query('category');

        if ($selectedCategory && $selectedCategory !== 'all') {
            $words = $words->filter(fn ($word) => $word->category === $selectedCategory)->values();
        }

        return view('public.words.index', compact('words', 'learnedWordIds', 'categoryCounts', 'selectedCategory'));
    }

    /**
     * Display the specified word.
     */
    public function show($id): View
    {
        $word = Word::with(['translations', 'lesson'])->findOrFail($id);

        $isLearned = Auth::check()
            ? Auth::user()->words()->wherePivot('learned', true)->where('words.id', $id)->exists()
            : false;

        return view('public.words.show', compact('word', 'isLearned'));
    }

    /**
     * Отметить слово как выученное/на повторении для текущего пользователя
     * (карточки в режиме флеш-карт).
     */
    public function markProgress(Request $request, Word $word, AchievementService $achievements, SpacedRepetitionService $srs)
    {
        $request->validate(['learned' => 'required|boolean']);

        Auth::user()->words()->syncWithoutDetaching([
            $word->id => [
                'learned' => $request->boolean('learned'),
                'last_reviewed_at' => now(),
            ],
        ]);

        if ($request->boolean('learned')) {
            // Отмеченное «знаю» слово встаёт в очередь повторения, иначе
            // оно исчезало бы из поля зрения навсегда.
            $progress = UserWord::firstOrNew([
                'user_id' => Auth::id(),
                'word_id' => $word->id,
            ]);
            $srs->scheduleIfNew($progress);

            $achievements->checkAndAward(Auth::user(), 'words_learned');
        }

        if ($request->wantsJson()) {
            return response()->json(['ok' => true]);
        }

        return back();
    }

    /**
     * Добавить своё слово в словарь (без привязки к уроку) и сразу
     * сохранить его перевод.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'word' => 'required|string|max:255',
            'translation' => 'required|string|max:255',
            'example' => 'nullable|string|max:500',
        ]);

        $word = Word::create([
            'word' => $data['word'],
            'example' => $data['example'] ?? null,
        ]);

        $word->translations()->create([
            'language' => 'ru',
            'translation' => $data['translation'],
        ]);

        Auth::user()->words()->syncWithoutDetaching([$word->id => ['learned' => false]]);

        return redirect()->route('words.index')->with('success', "Слово «{$word->word}» добавлено в словарь!");
    }

    /**
     * Сессия повторения: слова, у которых подошёл срок.
     */
    public function review(SpacedRepetitionService $srs): View
    {
        $queue = $srs->dueQueue(Auth::user());
        $dueCount = $srs->dueCount(Auth::user());

        $cards = $queue->map(fn (UserWord $progress) => [
            'word_id' => $progress->word_id,
            'word' => $progress->word->word,
            'transcription' => $progress->word->transcription,
            'translation' => $progress->word->translations->pluck('translation')->join(', ') ?: '—',
            'example' => $progress->word->example,
        ])->values();

        return view('public.words.review', compact('cards', 'dueCount'));
    }

    /**
     * Ответ в сессии повторения — пересчитывает срок следующего показа.
     */
    public function reviewAnswer(Request $request, Word $word, SpacedRepetitionService $srs, AchievementService $achievements)
    {
        $request->validate(['remembered' => 'required|boolean']);

        $progress = UserWord::firstOrNew([
            'user_id' => Auth::id(),
            'word_id' => $word->id,
        ]);

        $progress = $srs->review($progress, $request->boolean('remembered'));

        if ($progress->learned) {
            $achievements->checkAndAward(Auth::user(), 'words_learned');
        }

        return response()->json([
            'ok' => true,
            'interval_days' => $progress->interval_days,
            'next_review_at' => $progress->next_review_at?->toIso8601String(),
            'due_left' => $srs->dueCount(Auth::user()),
        ]);
    }
}
