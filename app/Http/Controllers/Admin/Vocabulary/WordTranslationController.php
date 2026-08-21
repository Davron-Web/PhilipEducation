<?php

namespace App\Http\Controllers\Admin\Vocabulary;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Vocabulary\StoreWordTranslationRequest;
use App\Http\Requests\Admin\Vocabulary\UpdateWordTranslationRequest;
use App\Models\Vocabulary\Word;
use App\Models\Vocabulary\WordTranslation;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WordTranslationController extends Controller
{
    public function index(): View
    {
        $translations = WordTranslation::with('word')
            ->when(request('search'), function ($query, $search) {
                $query->where('translation', 'like', "%{$search}%");
            })
            ->when(request('word_id'), function ($query, $wordId) {
                $query->where('word_id', $wordId);
            })
            ->when(request('language'), function ($query, $language) {
                $query->where('language', $language);
            })
            ->orderBy('word_id')
            ->paginate(20)
            ->withQueryString();

        $words = Word::orderBy('word')->pluck('word', 'id');
        $languages = WordTranslation::distinct()->orderBy('language')->pluck('language');

        return view('admin.vocabulary.wordtranslations.index', compact('translations', 'words', 'languages'));
    }

    public function create(): View
    {
        $words = Word::orderBy('word')->get();

        return view('admin.vocabulary.wordtranslations.create', compact('words'));
    }

    public function store(StoreWordTranslationRequest $request): RedirectResponse
    {
        WordTranslation::create($request->validated());

        return redirect()
            ->route('admin.vocabulary.wordtranslations.index')
            ->with('success', 'Translation created successfully.');
    }

    public function show(WordTranslation $wordTranslation): View
    {
        return view('admin.vocabulary.wordtranslations.show', [
            'wordTranslation' => $wordTranslation->load('word')
        ]);
    }

    public function edit(WordTranslation $wordTranslation): View
    {
        $words = Word::orderBy('word')->get();

        return view('admin.vocabulary.wordtranslations.edit', compact('wordTranslation', 'words'));
    }

    public function update(UpdateWordTranslationRequest $request, WordTranslation $wordTranslation): RedirectResponse
    {
        $wordTranslation->update($request->validated());

        return redirect()
            ->route('admin.vocabulary.wordtranslations.index')
            ->with('success', 'Translation updated successfully.');
    }

    public function destroy(WordTranslation $wordTranslation): RedirectResponse
    {
        $wordTranslation->delete();

        return redirect()
            ->route('admin.vocabulary.wordtranslations.index')
            ->with('success', 'Translation deleted successfully.');
    }
}
