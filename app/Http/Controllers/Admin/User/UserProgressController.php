<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\StoreUserProgressRequest;
use App\Http\Requests\Admin\User\UpdateUserProgressRequest;
use App\Models\Content\Lesson;
use App\Models\User;
use App\Models\User\UserProgress;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserProgressController extends Controller
{
    public function index(): View
    {
        $progressRecords = UserProgress::with(['user', 'lesson'])
            ->when(request('user_id'), function ($query, $userId) {
                $query->where('user_id', $userId);
            })
            ->when(request('lesson_id'), function ($query, $lessonId) {
                $query->where('lesson_id', $lessonId);
            })
            ->when(request('status'), function ($query, $status) {
                $query->where('is_completed', $status === 'completed');
            })
            ->latest('updated_at')
            ->paginate(20)
            ->withQueryString();

        $users = User::orderBy('name')->pluck('name', 'id');
        $lessons = Lesson::orderBy('order_number')->pluck('title', 'id');

        return view('admin.user.userprogresses.index', compact('progressRecords', 'users', 'lessons'));
    }

    public function create(): View
    {
        $users = User::orderBy('name')->get();
        $lessons = Lesson::orderBy('order_number')->get();

        return view('admin.user.userprogresses.create', compact('users', 'lessons'));
    }

    public function store(StoreUserProgressRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (!empty($data['is_completed']) && empty($data['completed_at'])) {
            $data['completed_at'] = now();
        }

        UserProgress::create($data);

        return redirect()
            ->route('admin.user.userprogresses.index')
            ->with('success', 'users progress created successfully.');
    }

    public function show(UserProgress $userprogress): View
    {
        return view('admin.user.userprogresses.show', [
            'userProgress' => $userprogress->load(['user', 'lesson'])
        ]);
    }

    public function edit(UserProgress $userprogress): View
    {
        $users = User::orderBy('name')->get();
        $lessons = Lesson::orderBy('order_number')->get();

        return view('admin.user.userprogresses.edit', ['userProgress' => $userprogress, 'users' => $users, 'lessons' => $lessons]);
    }

    public function update(UpdateUserProgressRequest $request, UserProgress $userprogress): RedirectResponse
    {
        $data = $request->validated();

        if (!empty($data['is_completed']) && !$userprogress->is_completed) {
            $data['completed_at'] = now();
        }

        $userprogress->update($data);

        return redirect()
            ->route('admin.user.userprogresses.index')
            ->with('success', 'users progress updated successfully.');
    }

    public function destroy(UserProgress $userprogress): RedirectResponse
    {
        $userprogress->delete();

        return redirect()
            ->route('admin.user.userprogresses.index')
            ->with('success', 'users progress deleted successfully.');
    }
}
