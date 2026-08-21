<?php

namespace App\Http\Controllers\Admin\Gamification;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Gamification\StoreStudyStatisticRequest;
use App\Http\Requests\Admin\Gamification\UpdateStudyStatisticRequest;
use App\Models\Gamification\StudyStatistic;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StudyStatisticController extends Controller
{
    public function index(): View
    {
        $studyStatistics = StudyStatistic::with('user')
            ->when(request('user_id'), function ($query, $userId) {
                $query->where('user_id', $userId);
            })
            ->orderBy('study_time_minutes', 'desc')
            ->paginate(request('per_page', 20))
            ->withQueryString();

        $users = User::orderBy('name')->get();

        return view('admin.gamification.studystatistics.index', compact('studyStatistics', 'users'));
    }

    public function create(): View
    {
        $users = User::orderBy('name')->get();

        return view('admin.gamification.studystatistics.create', compact('users'));
    }

    public function store(StoreStudyStatisticRequest $request): RedirectResponse
    {
        StudyStatistic::create($request->validated());

        return redirect()
            ->route('admin.gamification.studystatistics.index')
            ->with('success', 'Statistic created successfully');
    }

    public function show(StudyStatistic $studyStatistic): View
    {
        return view('admin.gamification.studystatistics.show', [
            'studyStatistic' => $studyStatistic->load('user')
        ]);
    }

    public function edit(StudyStatistic $studyStatistic): View
    {
        $users = User::orderBy('name')->get();

        return view('admin.gamification.studystatistics.edit', compact('studyStatistic', 'users'));
    }

    public function update(UpdateStudyStatisticRequest $request, StudyStatistic $studyStatistic): RedirectResponse
    {
        $studyStatistic->update($request->validated());

        return redirect()
            ->route('admin.gamification.studystatistics.index')
            ->with('success', 'Statistic updated successfully');
    }

    public function destroy(StudyStatistic $studyStatistic): RedirectResponse
    {
        $studyStatistic->delete();

        return redirect()
            ->route('admin.gamification.studystatistics.index')
            ->with('success', 'Statistic deleted successfully');
    }
}
