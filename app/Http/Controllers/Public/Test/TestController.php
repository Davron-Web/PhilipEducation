<?php

namespace App\Http\Controllers\Public\Test;

use App\Http\Controllers\Controller;
use App\Models\Test\Test;
use Illuminate\View\View;

class TestController extends Controller
{
    /**
     * Display a listing of tests, grouped by level.
     */
    public function index(): View
    {
        $tests = Test::with('lesson.level')
            ->where('is_published', true)
            ->withCount('questions')
            ->get();

        $testsByLevel = $tests->groupBy(function (Test $test) {
            return optional(optional($test->lesson)->level)->code ?? 'General';
        });

        return view('public.tests.index', compact('testsByLevel'));
    }

    /**
     * Display the specified test with its questions and answers.
     */
    public function show($id): View
    {
        $test = Test::with(['lesson.level', 'questions.answers'])->findOrFail($id);

        return view('public.tests.show', compact('test'));
    }
}
