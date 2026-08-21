<?php

namespace App\Http\Controllers\Public\Gamification;

use App\Http\Controllers\Controller;
use App\Models\Gamification\Achievement;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AchievementController extends Controller
{
    public function index(): View
    {
        $achievements = Achievement::orderByDesc('points')->get();

        $earned = Auth::user()->achievements()->get()->keyBy('id');

        return view('public.achievements.index', compact('achievements', 'earned'));
    }
}
