<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\StoreUserResultRequest;
use App\Http\Requests\Admin\User\UpdateUserResultRequest;
use App\Models\Test\Test;
use App\Models\User;
use App\Models\User\UserResult;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserResultController extends Controller
{
    public function index(): View
    {
        $results = UserResult::with(['user', 'test'])
            ->when(request('user_id'), function ($query, $userId) {
                $query->where('user_id', $userId);
            })
            ->when(request('test_id'), function ($query, $testId) {
                $query->where('test_id', $testId);
            })
            ->when(request('status'), function ($query, $status) {
                $query->where('passed', $status === 'passed');
            })
            ->latest('attempt_date')
            ->paginate(20)
            ->withQueryString();

        $users = User::orderBy('name')->pluck('name', 'id');
        $tests = Test::orderBy('title')->pluck('title', 'id');

        return view('admin.user.userresults.index', compact('results', 'users', 'tests'));
    }

    public function create(): View
    {
        $users = User::orderBy('name')->get();
        $tests = Test::orderBy('title')->get();

        return view('admin.user.userresults.create', compact('users', 'tests'));
    }

    public function store(StoreUserResultRequest $request): RedirectResponse
    {
        UserResult::create($request->validated());

        return redirect()
            ->route('admin.user.userresults.index')
            ->with('success', 'users result created successfully.');
    }

    public function show(UserResult $userresult): View
    {
        return view('admin.user.userresults.show', [
            'userResult' => $userresult->load(['user', 'test'])
        ]);
    }

    public function edit(UserResult $userresult): View
    {
        $users = User::orderBy('name')->get();
        $tests = Test::orderBy('title')->get();

        return view('admin.user.userresults.edit', ['userResult' => $userresult, 'users' => $users, 'tests' => $tests]);
    }

    public function update(UpdateUserResultRequest $request, UserResult $userresult): RedirectResponse
    {
        $userresult->update($request->validated());

        return redirect()
            ->route('admin.user.userresults.index')
            ->with('success', 'users result updated successfully.');
    }

    public function destroy(UserResult $userresult): RedirectResponse
    {
        $userresult->delete();

        return redirect()
            ->route('admin.user.userresults.index')
            ->with('success', 'users result deleted successfully.');
    }
}
