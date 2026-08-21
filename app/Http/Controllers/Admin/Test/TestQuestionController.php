<?php

namespace App\Http\Controllers\Admin\Test;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Test\StoreTestQuestionRequest;
use App\Http\Requests\Admin\Test\UpdateTestQuestionRequest;
use App\Models\Test\Test;
use App\Models\Test\TestQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TestQuestionController extends Controller
{
    public function index(): View
    {
        $questions = TestQuestion::with(['test', 'answers'])
            ->when(request('test_id'), function ($query, $testId) {
                $query->where('test_id', $testId);
            })
            ->when(request('search'), function ($query, $search) {
                $query->where('question', 'like', "%{$search}%");
            })
            ->when(request('type'), function ($query, $type) {
                $query->where('type', $type);
            })
            ->orderBy('test_id')
            ->orderBy('id')
            ->paginate(request('per_page', 20))
            ->withQueryString();

        $tests = Test::orderBy('title')->pluck('title', 'id');
        $types = TestQuestion::TYPES;

        return view('admin.test.testquestions.index', compact('questions', 'tests', 'types'));
    }

    public function create(): View
    {
        $tests = Test::orderBy('title')->get();
        $types = TestQuestion::TYPES;

        return view('admin.test.testquestions.create', compact('tests', 'types'));
    }

    public function store(StoreTestQuestionRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if (empty($data['points'])) {
            $data['points'] = 1;
        }

        TestQuestion::create($data);

        return redirect()
            ->route('admin.test.testquestions.index')
            ->with('success', 'Вопрос теста успешно создан.');
    }

    public function show(TestQuestion $testquestion): View
    {
        return view('admin.test.testquestions.show', [
            'testQuestion' => $testquestion->load(['test', 'answers']),
        ]);
    }

    public function edit(TestQuestion $testquestion): View
    {
        $tests = Test::orderBy('title')->get();
        $types = TestQuestion::TYPES;

        return view('admin.test.testquestions.edit', ['testQuestion' => $testquestion, 'tests' => $tests, 'types' => $types]);
    }

    public function update(UpdateTestQuestionRequest $request, TestQuestion $testquestion): RedirectResponse
    {
        $testquestion->update($request->validated());

        return redirect()
            ->route('admin.test.testquestions.index')
            ->with('success', 'Вопрос теста успешно обновлён.');
    }

    public function destroy(TestQuestion $testquestion): RedirectResponse
    {
        if ($testquestion->userAnswers()->exists()) {
            return redirect()
                ->route('admin.test.testquestions.index')
                ->with('error', 'Нельзя удалить: пользователи уже отвечали на этот вопрос.');
        }

        $testquestion->answers()->delete();
        $testquestion->delete();

        return redirect()
            ->route('admin.test.testquestions.index')
            ->with('success', 'Вопрос теста удалён.');
    }
}
