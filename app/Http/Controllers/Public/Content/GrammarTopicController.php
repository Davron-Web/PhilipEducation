<?php

namespace App\Http\Controllers\Public\Content;

use App\Http\Controllers\Controller;
use App\Models\Content\GrammarTopic;
use App\Models\System\Level;
use Illuminate\View\View;

class GrammarTopicController extends Controller
{
    /**
     * Display a listing of grammar topics.
     */
    public function index(): View
    {
        // Order by the level's CEFR code (A1, A2, B1... sorts correctly as
        // a plain string) so topics form one continuous study path.
        $topics = GrammarTopic::with('level')
            ->orderBy(Level::select('code')->whereColumn('levels.id', 'grammar_topics.level_id'))
            ->orderBy('title')
            ->get();

        return view('public.grammartopics.index', compact('topics'));
    }

    /**
     * Display the specified grammar topic.
     */
    public function show($id): View
    {
        $topic = GrammarTopic::with(['level', 'lessons.exercises'])->findOrFail($id);

        return view('public.grammartopics.show', compact('topic'));
    }
}
