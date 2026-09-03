<?php

namespace App\Http\Controllers\Public\Exercise;

use App\Http\Controllers\Controller;
use App\Models\Exercise\Exercise;
use App\Models\Exercise\UserExerciseAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ExerciseController extends Controller
{
    /**
     * Столько РАЗНЫХ вопросов по теме (уроку) должны быть неверными у
     * пользователя одновременно, чтобы предложить повторить урок.
     */
    private const MISTAKE_THRESHOLD = 3;

    /**
     * Display a listing of exercises.
     */
    public function index(): View
    {
        // Sorted by the parent lesson's level (A1, A2, B1... sorts correctly
        // as a plain string) so exercises form one continuous study path,
        // matching the lessons list.
        $exercises = Exercise::with('lesson.level')->withCount('questions')->orderBy('id')->get()
            ->sortBy(fn ($exercise) => optional(optional($exercise->lesson)->level)->code ?? 'zzz')
            ->values();

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

    /**
     * Проверить ответы пользователя на упражнение, сохранить их в
     * user_exercise_answers (правильность — точное совпадение с
     * correct_answer без учёта регистра/пробелов по краям) и, если
     * накопилось много неверных ответов по теме (уроку) этого
     * упражнения, предложить пройти сам урок ещё раз.
     */
    public function check(Request $request, Exercise $exercise)
    {
        $exercise->load(['questions', 'lesson']);

        $data = $request->validate([
            'answers' => 'present|array',
            'answers.*' => 'nullable|string|max:1000',
        ]);
        $answers = $data['answers'];

        $results = [];

        foreach ($exercise->questions as $question) {
            $given = trim((string) ($answers[$question->id] ?? ''));
            $isCorrect = $given !== '' && mb_strtolower($given) === mb_strtolower(trim($question->correct_answer));

            UserExerciseAnswer::updateOrCreate(
                ['user_id' => Auth::id(), 'question_id' => $question->id],
                [
                    'exercise_id' => $exercise->id,
                    'answer' => $given,
                    'is_correct' => $isCorrect,
                    'score' => $isCorrect ? 1 : 0,
                ]
            );

            $results[] = [
                'question_id' => $question->id,
                'correct' => $isCorrect,
                'correct_answer' => $question->correct_answer,
            ];
        }

        $suggestLesson = null;

        if ($exercise->lesson) {
            $mistakeCount = UserExerciseAnswer::where('user_id', Auth::id())
                ->where('is_correct', false)
                ->whereHas('exercise', fn ($q) => $q->where('lesson_id', $exercise->lesson_id))
                ->count();

            if ($mistakeCount >= self::MISTAKE_THRESHOLD) {
                $suggestLesson = [
                    'id' => $exercise->lesson->id,
                    'title' => $exercise->lesson->title,
                    'mistake_count' => $mistakeCount,
                ];
            }
        }

        return response()->json([
            'results' => $results,
            'suggest_lesson' => $suggestLesson,
        ]);
    }
}
