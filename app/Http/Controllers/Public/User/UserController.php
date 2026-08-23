<?php

namespace App\Http\Controllers\Public\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\User\UpdateUserProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function profile(): View
    {
        $user = Auth::user()->load([
            'role',
            'level',
            'progress.lesson',
            'results.test',
            'words.translations',
            'achievements',
            'certificates',
        ]);

        return view('public.profiles.show', compact('user'));
    }

    public function edit(): View
    {
        $user = Auth::user();

        return view('public.profiles.edit', compact('user'));
    }

    public function update(UpdateUserProfileRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $data = $request->validated();

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()
            ->route('profiles.show')
            ->with('success', 'Profile updated successfully');
    }
}
