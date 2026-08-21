<?php

namespace App\Http\Controllers\Public\Exercise;

use App\Http\Controllers\Controller;
use App\Models\Exercise\Exercise;
use Illuminate\View\View;

class ExerciseController extends Controller
{
    /**
     * Display a listing of exercises.
     */
    public function index(): View
    {
        $exercises = Exercise::with('lesson')->withCount('questions')->orderBy('id')->get();

        return view('public.exercises.index', compact('exercises'));
    }

    /**
     * Display the specified exercise with its practice questions.
     */
    public function show($id): View
    {
        $exercise = Exercise::with(['lesson', 'questions'])->findOrFail($id);

        return view('public.exercises.show', compact('exercise'));
    }
}
