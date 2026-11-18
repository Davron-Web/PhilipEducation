<?php

namespace App\Http\Controllers\Public\Gamification;

use App\Http\Controllers\Controller;
use App\Services\LeaderboardService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LeaderboardController extends Controller
{
    public function index(LeaderboardService $leaderboard): View
    {
        $user = Auth::user();

        $boards = [];
        $me = [];

        foreach (LeaderboardService::PERIODS as $period) {
            $board = $leaderboard->board($period);
            $boards[$period] = $board;

            $inBoard = $board->first(fn ($row) => $row->user->id === $user->id);

            // Если человек уже виден в топ-50 — второй раз его место не
            // считаем, просто подсвечиваем существующую строку в шаблоне.
            $me[$period] = $inBoard ? null : [
                'user' => $leaderboard->toRowUser($user),
                'rank' => $leaderboard->rankFor($user, $period),
                'points' => $leaderboard->pointsFor($user, $period),
                'change' => $leaderboard->changeFor($user->id, $period),
            ];
        }

        return view('public.leaderboard.index', [
            'boards' => $boards,
            'me' => $me,
            'currentUserId' => $user->id,
        ]);
    }
}
