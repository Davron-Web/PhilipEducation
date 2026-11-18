<?php

namespace App\Http\Controllers\Public\Vocabulary;

use App\Http\Controllers\Controller;
use App\Models\Vocabulary\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CollectionController extends Controller
{
    /**
     * Страница коллекции: слова с транскрипцией/аудио/переводом + запуск
     * флеш-карточек и мини-теста по этой коллекции (переиспользует те же
     * Alpine-фабрики flashcardDeck()/wordQuiz(), что и общий словарь —
     * см. public/assets/js/flashcard-deck.js).
     */
    public function show(Collection $collection): View
    {
        $words = $collection->words()->with('translations')->get();

        $learnedWordIds = Auth::check()
            ? Auth::user()->words()->wherePivot('learned', true)->pluck('words.id')->all()
            : [];

        return view('public.words.collection', compact('collection', 'words', 'learnedWordIds'));
    }
}
