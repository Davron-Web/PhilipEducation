<?php

namespace App\Http\Controllers\Admin\Test;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Test\StoreTestRequest;
use App\Http\Requests\Admin\Test\UpdateTestRequest;
use App\Models\Content\Lesson;
use App\Models\Test\Test;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TestController extends Controller
{
    public function index(): View
    {
        $tests = Test::with(['lesson'])
            ->when(request('lesson_id'), function ($query, $lessonId) {
                $query->where('lesson_id', $lessonId);
            })
            ->when(request('search'), function ($query, $search) {
                $query->where('title', 'like', "%{$search}%");
            })
            ->when(request('is_published') !== null, function ($query) {
                $query->where('is_published', request('is_published'));
            })
            ->orderBy('created_at', 'desc')
            ->paginate(request('per_page', 20))
            ->withQueryString();

        $lessons = Lesson::orderBy('title')->get();

        return view('admin.test.tests.index', compact('tests', 'lessons'));
    }

    public function create(): View
    {
        $lessons = Lesson::orderBy('title')->get();

        return view('admin.test.tests.create', compact('lessons'));
    }

    public function store(StoreTestRequest $request): RedirectResponse
    {
        try {
            $test = DB::transaction(function () use ($request) {
                return Test::create($request->validated());
            });

            return redirect()
                ->route('admin.test.tests.index')
                ->with('success', 'Test created successfully');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    public function show(Test $test): View
    {
        return view('admin.test.tests.show', [
            'test' => $test->load(['lesson', 'questions'])
        ]);
    }

    public function edit(Test $test): View
    {
        $lessons = Lesson::orderBy('title')->get();

        return view('admin.test.tests.edit', compact('test', 'lessons'));
    }

    public function update(UpdateTestRequest $request, Test $test): RedirectResponse
    {
        try {
            $test->update($request->validated());

            return redirect()
                ->route('admin.test.tests.index')
                ->with('success', 'Test updated successfully');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(Test $test): RedirectResponse
    {
        if ($test->attempts()->exists()) {
            return redirect()
                ->route('admin.test.tests.index')
                ->with('error', 'Cannot delete: users have attempted this test');
        }

        try {
            DB::transaction(function () use ($test) {
                $test->questions()->each(function ($question) {
                    $question->answers()->delete();
                    $question->delete();
                });
                $test->delete();
            });

            return redirect()
                ->route('admin.test.tests.index')
                ->with('success', 'Test deleted successfully');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }
}
