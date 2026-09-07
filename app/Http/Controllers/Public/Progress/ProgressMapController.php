<?php

namespace App\Http\Controllers\Public\Progress;

use App\Http\Controllers\Controller;
use App\Services\ProgressMapService;
use App\Services\TopicStatsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProgressMapController extends Controller
{
    public function __invoke(Request $request, ProgressMapService $map, TopicStatsService $topics): View
    {
        $user = $request->user();

        return view('public.progress.map', [
            'levels' => $map->levels($user),
            'weakTopics' => $map->weakTopics($user),
            'strongTopics' => $map->strongTopics($user),
            'labeler' => $topics,
        ]);
    }
}
