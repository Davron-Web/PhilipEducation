<?php

namespace App\Http\Controllers\Public\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\User\UpdateUserProfileRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function me(): JsonResponse
    {
        $user = Auth::user()->load([
            'role',
            'level',
            'progress.lesson',
            'results.test',
            'words.translations',
            'achievements',
            'certificates',
            'comments',
        ]);

        return response()->json([
            'data' => $user,
        ]);
    }

    /**
     * Current user's own profile overview.
     */
    public function index(): View
    {
        $user = Auth::user()->load(['role', 'level']);
        $stats = $this->statsFor($user);

        return view('public.profiles.index', compact('user', 'stats'));
    }

    public function edit(): View
    {
        $user = Auth::user();

        return view('public.profiles.edit', compact('user'));
    }

    public function show(Request $request, User $user): View|JsonResponse
    {
        $user->load(['role', 'level']);
        $stats = $this->statsFor($user);

        if ($request->wantsJson()) {
            return response()->json([
                'data' => $user->load(['progress.lesson', 'results.test', 'words', 'achievements', 'certificates']),
            ]);
        }

        return view('public.profiles.show', compact('user', 'stats'));
    }

    public function update(UpdateUserProfileRequest $request): RedirectResponse|JsonResponse
    {
        $user = Auth::user();
        $user->update($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Profile updated successfully',
                'data' => $user->fresh(),
            ]);
        }

        return redirect()->route('profiles.edit')->with('success', 'Profile updated successfully.');
    }

    public function stats(): JsonResponse
    {
        $user = Auth::user();

        return response()->json([
            'data' => $this->statsFor($user),
        ]);
    }

    /**
     * @return array{level: mixed, points: int, words_learned: int, lessons_completed: int, tests_passed: int, achievements_count: int, certificates_count: int}
     */
    private function statsFor(User $user): array
    {
        return [
            'level' => $user->level,
            'points' => $user->points,
            'words_learned' => $user->words()->count(),
            'lessons_completed' => $user->progress()->where('is_completed', true)->count(),
            'tests_passed' => $user->results()->where('passed', true)->count(),
            'achievements_count' => $user->achievements()->count(),
            'certificates_count' => $user->certificates()->count(),
        ];
    }
}
