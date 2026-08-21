<?php

namespace App\Http\Controllers\Admin\Test;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Test\StoreTestAnswerRequest;
use App\Http\Requests\Admin\Test\UpdateTestAnswerRequest;
use App\Models\Test\TestAnswer;
use App\Models\Test\TestQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TestAnswerController extends Controller
{
    public function index(): View
    {
        $answers = TestAnswer::with('question')
            ->when(request('question_id'), function ($query, $questionId) {
                $query->where('question_id', $questionId);
            })
            ->when(request('is_correct') !== null, function ($query) {
                $query->where('is_correct', request('is_correct'));
            })
            ->orderBy('question_id')
            ->orderBy('id')
            ->paginate(request('per_page', 20))
            ->withQueryString();

        $questions = TestQuestion::orderBy('id')->get();

        return view('admin.test.testanswers.index', compact('answers', 'questions'));
    }

    public function create(): View
    {
        $questions = TestQuestion::orderBy('id')->get();

        return view('admin.test.testanswers.create', compact('questions'));
    }

    public function store(StoreTestAnswerRequest $request): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request) {
                $data = $request->validated();

                if (!empty($data['is_correct'])) {
                    TestAnswer::where('question_id', $data['question_id'])
                        ->update(['is_correct' => false]);
                }

                return TestAnswer::create($data);
            });

            return redirect()
                ->route('admin.test.testanswers.index')
                ->with('success', 'Test answer created successfully');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    public function show(TestAnswer $testanswer): View
    {
        return view('admin.test.testanswers.show', [
            'testAnswer' => $testanswer->load('question')
        ]);
    }

    public function edit(TestAnswer $testanswer): View
    {
        $questions = TestQuestion::orderBy('id')->get();

        return view('admin.test.testanswers.edit', ['testAnswer' => $testanswer, 'questions' => $questions]);
    }

    public function update(UpdateTestAnswerRequest $request, TestAnswer $testanswer): RedirectResponse
    {
        try {
            $data = $request->validated();

            if (!empty($data['is_correct']) && !$testanswer->is_correct) {
                TestAnswer::where('question_id', $testanswer->question_id)
                    ->where('id', '!=', $testanswer->id)
                    ->update(['is_correct' => false]);
            }

            $testanswer->update($data);

            return redirect()
                ->route('admin.test.testanswers.index')
                ->with('success', 'Test answer updated successfully');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(TestAnswer $testanswer): RedirectResponse
    {
        try {
            $testanswer->delete();

            return redirect()
                ->route('admin.test.testanswers.index')
                ->with('success', 'Test answer deleted successfully');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }
}
