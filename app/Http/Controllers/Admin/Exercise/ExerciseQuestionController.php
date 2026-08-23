<?php

namespace App\Http\Controllers\Admin\Exercise;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Exercise\StoreExerciseQuestionRequest;
use App\Http\Requests\Admin\Exercise\UpdateExerciseQuestionRequest;
use App\Models\Exercise\Exercise;
use App\Models\Exercise\ExerciseQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ExerciseQuestionController extends Controller
{
    /**
     * Список вопросов
     */
    public function index(): View
    {
        $questions = ExerciseQuestion::with('exercise')
            ->when(request('exercise_id'), function ($query, $exerciseId) {
                $query->where('exercise_id', $exerciseId);
            })
            ->when(request('search'), function ($query, $search) {
                $query->where('question', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(request('per_page', 20));

        $exercises = Exercise::orderBy('title')->get();

        return view('admin.exercise.exercisequestions.index', compact('questions', 'exercises'));
    }

    /**
     * Форма создания
     */
    public function create(): View
    {
        $exercises = Exercise::orderBy('title')->get();

        return view('admin.exercise.exercisequestions.create', compact('exercises'));
    }

    /**
     * Сохранение вопроса
     */
    public function store(StoreExerciseQuestionRequest $request): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request) {
                return ExerciseQuestion::create($request->validated());
            });

            return redirect()
                ->route('admin.exercise.exercisequestions.index')
                ->with('success', 'Вопрос успешно создан.');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ошибка: '.$e->getMessage());
        }
    }

    /**
     * Просмотр вопроса
     */
    public function show(ExerciseQuestion $exercisequestion): View
    {
        return view('admin.exercise.exercisequestions.show', [
            'exerciseQuestion' => $exercisequestion->load('exercise'),
        ]);
    }

    /**
     * Форма редактирования
     */
    public function edit(ExerciseQuestion $exercisequestion): View
    {
        $exercises = Exercise::orderBy('title')->get();

        return view('admin.exercise.exercisequestions.edit', ['exerciseQuestion' => $exercisequestion, 'exercises' => $exercises]);
    }

    /**
     * Обновление вопроса
     */
    public function update(UpdateExerciseQuestionRequest $request, ExerciseQuestion $exercisequestion): RedirectResponse
    {
        try {
            $exercisequestion->update($request->validated());

            return redirect()
                ->route('admin.exercise.exercisequestions.index')
                ->with('success', 'Вопрос успешно обновлён.');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ошибка: '.$e->getMessage());
        }
    }

    /**
     * Удаление вопроса
     */
    public function destroy(ExerciseQuestion $exercisequestion): RedirectResponse
    {
        try {
            $exercisequestion->delete();

            return redirect()
                ->route('admin.exercise.exercisequestions.index')
                ->with('success', 'Вопрос успешно удалён.');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ошибка: '.$e->getMessage());
        }
    }
}
