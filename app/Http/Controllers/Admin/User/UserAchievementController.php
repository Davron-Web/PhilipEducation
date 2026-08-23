<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\StoreUserAchievementRequest;
use App\Models\Gamification\Achievement;
use App\Models\User;
use App\Models\User\UserAchievement;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserAchievementController extends Controller
{
    public function index(): View
    {
        $userAchievements = UserAchievement::with(['user', 'achievement'])
            ->when(request('user_id'), function ($query, $userId) {
                $query->where('user_id', $userId);
            })
            ->when(request('achievement_id'), function ($query, $achievementId) {
                $query->where('achievement_id', $achievementId);
            })
            ->latest()
            ->paginate(request('per_page', 20))
            ->withQueryString();

        $users = User::orderBy('name')->get();
        $achievements = Achievement::orderBy('title')->get();

        return view('admin.user.userachievements.index', compact('userAchievements', 'users', 'achievements'));
    }

    public function create(): View
    {
        $users = User::orderBy('name')->get();
        $achievements = Achievement::orderBy('title')->get();

        return view('admin.user.userachievements.create', compact('users', 'achievements'));
    }

    public function store(StoreUserAchievementRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $exists = UserAchievement::where($data)->exists();

        if ($exists) {
            return redirect()
                ->back()
                ->with('error', 'User already has this achievement');
        }

        UserAchievement::create($data);

        return redirect()
            ->route('admin.user.userachievements.index')
            ->with('success', 'Achievement granted successfully');
    }

    public function show(UserAchievement $userachievement): View
    {
        return view('admin.user.userachievements.show', [
            'userAchievement' => $userachievement->load(['user', 'achievement']),
        ]);
    }

    public function destroy(UserAchievement $userachievement): RedirectResponse
    {
        $userachievement->delete();

        return redirect()
            ->route('admin.user.userachievements.index')
            ->with('success', 'Achievement removed from user');
    }
}
