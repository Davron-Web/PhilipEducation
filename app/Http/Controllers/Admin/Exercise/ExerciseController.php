<?php

namespace App\Http\Controllers\Admin\Exercise;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Exercise\StoreExerciseRequest;
use App\Http\Requests\Admin\Exercise\UpdateExerciseRequest;
use App\Models\Content\Lesson;
use App\Models\Exercise\Exercise;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ExerciseController extends Controller
{
    /**
     * Список упражнений
     */
    public function index(): View
    {
        $exercises = Exercise::with('lesson')
            ->when(request('search'), function ($query, $search) {
                $query->where('title', 'like', "%{$search}%");
            })
            ->when(request('lesson_id'), function ($query, $lessonId) {
                $query->where('lesson_id', $lessonId);
            })
            ->when(request('type'), function ($query, $type) {
                $query->where('type', $type);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $lessons = Lesson::orderBy('order_number')->get();
        $types = Exercise::TYPES;

        return view('admin.exercise.exercises.index', compact('exercises', 'lessons', 'types'));
    }

    /**
     * Форма создания
     */
    public function create(): View
    {
        $lessons = Lesson::orderBy('order_number')->get();
        $types = Exercise::TYPES;

        return view('admin.exercise.exercises.create', compact('lessons', 'types'));
    }

    /**
     * Сохранение упражнения
     */
    public function store(StoreExerciseRequest $request): RedirectResponse
    {
        Exercise::create($request->validated());

        return redirect()
            ->route('admin.exercise.exercises.index')
            ->with('success', 'Упражнение успешно создано.');
    }

    /**
     * Просмотр упражнения
     */
    public function show(Exercise $exercise): View
    {
        return view('admin.exercise.exercises.show', [
            'exercise' => $exercise->load('lesson', 'questions'),
        ]);
    }

    /**
     * Форма редактирования
     */
    public function edit(Exercise $exercise): View
    {
        $lessons = Lesson::orderBy('order_number')->get();
        $types = Exercise::TYPES;

        return view('admin.exercise.exercises.edit', compact('exercise', 'lessons', 'types'));
    }

    /**
     * Обновление упражнения
     */
    public function update(UpdateExerciseRequest $request, Exercise $exercise): RedirectResponse
    {
        $exercise->update($request->validated());

        return redirect()
            ->route('admin.exercise.exercises.index')
            ->with('success', 'Упражнение успешно обновлено.');
    }

    /**
     * Удаление упражнения
     */
    public function destroy(Exercise $exercise): RedirectResponse
    {
        if ($exercise->userAnswers()->exists()) {
            return redirect()
                ->route('admin.exercise.exercises.index')
                ->with('error', 'Нельзя удалить: у упражнения есть ответы пользователей.');
        }

        $exercise->delete();

        return redirect()
            ->route('admin.exercise.exercises.index')
            ->with('success', 'Упражнение успешно удалено.');
    }
}
