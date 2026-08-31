<?php

namespace App\Http\Controllers\Public\Ielts;

use App\Http\Controllers\Controller;
use App\Models\Ielts\IeltsPassage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class IeltsReadingController extends Controller
{
    public function index(): View
    {
        $passages = IeltsPassage::where('skill', 'reading')->withCount('questions')->orderBy('id')->get();

        $bestScores = Auth::user()->ieltsPassageAttempts()
            ->selectRaw('ielts_passage_id, MAX(score) as best_score, MAX(total) as total')
            ->groupBy('ielts_passage_id')
            ->get()
            ->keyBy('ielts_passage_id');

        return view('public.ielts.reading.index', compact('passages', 'bestScores'));
    }

    public function show(IeltsPassage $passage): View
    {
        abort_unless($passage->skill === 'reading', 404);

        $lastAttempt = $passage->attemptsFor(Auth::user())->first();

        return view('public.ielts.reading.show', compact('passage', 'lastAttempt'));
    }

    public function submit(Request $request, IeltsPassage $passage): RedirectResponse
    {
        abort_unless($passage->skill === 'reading', 404);

        $questions = $passage->questions;

        $data = $request->validate([
            'answers' => 'required|array|size:'.$questions->count(),
            'answers.*' => 'required|integer|min:0',
        ]);

        $score = 0;
        foreach ($questions as $index => $question) {
            if ((int) ($data['answers'][$index] ?? -1) === $question->correct_index) {
                $score++;
            }
        }

        $passage->attempts()->create([
            'user_id' => Auth::id(),
            'answers' => $data['answers'],
            'score' => $score,
            'total' => $questions->count(),
        ]);

        return redirect()->route('ielts.reading.show', $passage)
            ->with('success', "Результат: {$score} из {$questions->count()}.");
    }
}
