<?php

namespace App\Http\Controllers\Admin\Gamification;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Gamification\StoreAchievementRequest;
use App\Http\Requests\Admin\Gamification\UpdateAchievementRequest;
use App\Models\Gamification\Achievement;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AchievementController extends Controller
{
    public function index(): View
    {
        $achievements = Achievement::latest()
            ->when(request('search'), function ($query, $search) {
                $query->where('title', 'like', "%{$search}%");
            })
            ->paginate(20)
            ->withQueryString();

        return view('admin.gamification.achievements.index', compact('achievements'));
    }

    public function create(): View
    {
        return view('admin.gamification.achievements.create');
    }

    public function store(StoreAchievementRequest $request): RedirectResponse
    {
        Achievement::create($request->validated());

        return redirect()
            ->route('admin.gamification.achievements.index')
            ->with('success', 'Достижение создано');
    }

    public function show(Achievement $achievement): View
    {
        return view('admin.gamification.achievements.show', [
            'achievement' => $achievement->load('users')
        ]);
    }

    public function edit(Achievement $achievement): View
    {
        return view('admin.gamification.achievements.edit', compact('achievement'));
    }

    public function update(UpdateAchievementRequest $request, Achievement $achievement): RedirectResponse
    {
        $achievement->update($request->validated());

        return redirect()
            ->route('admin.gamification.achievements.index')
            ->with('success', 'Достижение обновлено');
    }

    public function destroy(Achievement $achievement): RedirectResponse
    {
        if ($achievement->users()->exists()) {
            return redirect()
                ->route('admin.gamification.achievements.index')
                ->with('error', 'Нельзя удалить: достижение уже получено пользователями');
        }

        $achievement->delete();

        return redirect()
            ->route('admin.gamification.achievements.index')
            ->with('success', 'Достижение удалено');
    }
}
