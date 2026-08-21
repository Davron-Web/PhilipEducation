<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\StoreUserWordRequest;
use App\Http\Requests\Admin\User\UpdateUserWordRequest;
use App\Models\User;
use App\Models\User\UserWord;
use App\Models\Vocabulary\Word;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserWordController extends Controller
{
    public function index(): View
    {
        $userWords = UserWord::with(['user', 'word'])
            ->when(request('user_id'), function ($query, $userId) {
                $query->where('user_id', $userId);
            })
            ->when(request('word_id'), function ($query, $wordId) {
                $query->where('word_id', $wordId);
            })
            ->when(request('learned') !== null, function ($query) {
                $query->where('learned', request('learned'));
            })
            ->latest('last_reviewed_at')
            ->paginate(20)
            ->withQueryString();

        $users = User::orderBy('name')->pluck('name', 'id');
        $words = Word::orderBy('word')->pluck('word', 'id');

        return view('admin.user.userwords.index', compact('userWords', 'users', 'words'));
    }

    public function create(): View
    {
        $users = User::orderBy('name')->get();
        $words = Word::orderBy('word')->get();

        return view('admin.user.userwords.create', compact('users', 'words'));
    }

    public function store(StoreUserWordRequest $request): RedirectResponse
    {
        UserWord::create($request->validated());

        return redirect()
            ->route('admin.user.userwords.index')
            ->with('success', 'User word created successfully.');
    }

    public function show(UserWord $userword): View
    {
        return view('admin.user.userwords.show', [
            'userWord' => $userword->load(['user', 'word'])
        ]);
    }

    public function edit(UserWord $userword): View
    {
        $users = User::orderBy('name')->get();
        $words = Word::orderBy('word')->get();

        return view('admin.user.userwords.edit', ['userWord' => $userword, 'users' => $users, 'words' => $words]);
    }

    public function update(UpdateUserWordRequest $request, UserWord $userword): RedirectResponse
    {
        $userword->update($request->validated());

        return redirect()
            ->route('admin.user.userwords.index')
            ->with('success', 'User word updated successfully.');
    }

    public function destroy(UserWord $userword): RedirectResponse
    {
        $userword->delete();

        return redirect()
            ->route('admin.user.userwords.index')
            ->with('success', 'User word deleted successfully.');
    }
}
