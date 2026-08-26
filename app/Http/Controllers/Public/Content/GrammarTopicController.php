<?php

namespace App\Http\Controllers\Public\Content;

use App\Http\Controllers\Controller;
use App\Models\Content\GrammarTopic;
use App\Models\System\Level;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GrammarTopicController extends Controller
{
    /**
     * Display a listing of grammar topics: a category picker by default,
     * or the topics within one category when ?category= is present.
     */
    public function index(Request $request): View
    {
        // Order by the level's CEFR code (A1, A2, B1... sorts correctly as
        // a plain string) so topics form one continuous study path.
        $topics = GrammarTopic::with('level')
            ->orderBy(Level::select('code')->whereColumn('levels.id', 'grammar_topics.level_id'))
            ->orderBy('title')
            ->get();

        $categoryCounts = $topics->groupBy('category')->filter(fn ($group, $key) => $key !== '')->map->count()->sortKeys();

        $selectedCategory = $request->query('category');

        if ($selectedCategory && $selectedCategory !== 'all') {
            $topics = $topics->filter(fn ($topic) => $topic->category === $selectedCategory)->values();
        }

        return view('public.grammartopics.index', compact('topics', 'categoryCounts', 'selectedCategory'));
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
