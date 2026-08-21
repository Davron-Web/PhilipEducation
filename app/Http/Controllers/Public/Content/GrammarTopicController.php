<?php

namespace App\Http\Controllers\Public\Content;

use App\Http\Controllers\Controller;
use App\Models\Content\GrammarTopic;
use Illuminate\View\View;

class GrammarTopicController extends Controller
{
    /**
     * Display a listing of grammar topics.
     */
    public function index(): View
    {
        $topics = GrammarTopic::with('level')->orderBy('title')->get();

        return view('public.grammartopics.index', compact('topics'));
    }

    /**
     * Display the specified grammar topic.
     */
    public function show($id): View
    {
        $topic = GrammarTopic::with('level')->findOrFail($id);

        return view('public.grammartopics.show', compact('topic'));
    }
}
