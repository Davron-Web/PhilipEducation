<?php

namespace App\Http\Controllers\Admin\Vocabulary;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Vocabulary\StoreWordRequest;
use App\Http\Requests\Admin\Vocabulary\UpdateWordRequest;
use App\Models\Content\Lesson;
use App\Models\Vocabulary\Word;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WordController extends Controller
{
    public function index(): View
    {
        $words = Word::with(['lesson', 'translations'])
            ->when(request('search'), function ($query, $search) {
                $query->where('word', 'like', "%{$search}%");
            })
            ->when(request('lesson_id'), function ($query, $lessonId) {
                $query->where('lesson_id', $lessonId);
            })
            ->when(request('difficulty'), function ($query, $difficulty) {
                match ($difficulty) {
                    'easy' => $query->whereBetween('difficulty', [1, 2]),
                    'medium' => $query->where('difficulty', 3),
                    'hard' => $query->whereBetween('difficulty', [4, 5]),
                    default => $query->where('difficulty', $difficulty),
                };
            })
            ->orderBy('word')
            ->paginate(20)
            ->withQueryString();

        $lessons = Lesson::orderBy('order_number')->pluck('title', 'id');

        // Words are legitimately taught in more than one lesson (same word,
        // different example sentence per grammar topic). Surface that count
        // so it reads as intentional reuse rather than a data-quality bug.
        $duplicateCounts = Word::selectRaw('LOWER(word) as word_key, COUNT(*) as cnt')
            ->groupBy('word_key')
            ->having('cnt', '>', 1)
            ->pluck('cnt', 'word_key');

        return view('admin.vocabulary.words.index', compact('words', 'lessons', 'duplicateCounts'));
    }

    public function create(): View
    {
        $lessons = Lesson::orderBy('order_number')->get();

        return view('admin.vocabulary.words.create', compact('lessons'));
    }

    public function store(StoreWordRequest $request): RedirectResponse
    {
        Word::create($request->validated());

        return redirect()
            ->route('admin.vocabulary.words.index')
            ->with('success', 'Word created successfully.');
    }

    public function show(Word $word): View
    {
        return view('admin.vocabulary.words.show', [
            'word' => $word->load(['lesson', 'translations', 'userWords'])
        ]);
    }

    public function edit(Word $word): View
    {
        $lessons = Lesson::orderBy('order_number')->get();

        return view('admin.vocabulary.words.edit', compact('word', 'lessons'));
    }

    public function update(UpdateWordRequest $request, Word $word): RedirectResponse
    {
        $word->update($request->validated());

        return redirect()
            ->route('admin.vocabulary.words.index')
            ->with('success', 'Word updated successfully.');
    }

    public function destroy(Word $word): RedirectResponse
    {
        if ($word->userWords()->exists()) {
            return redirect()
                ->route('admin.vocabulary.words.index')
                ->with('error', 'Cannot delete: users have this word in their vocabulary.');
        }

        $word->delete();

        return redirect()
            ->route('admin.vocabulary.words.index')
            ->with('success', 'Word deleted successfully.');
    }
}
