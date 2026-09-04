<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\AchievementResource;
use App\Models\Gamification\Achievement;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AchievementController extends Controller
{
    /** Все достижения; у полученных проставлено earned_at. */
    public function index(Request $request): AnonymousResourceCollection
    {
        $earned = $request->user()->achievements()->pluck('achievements.id')->flip();

        $achievements = Achievement::orderByDesc('points')->get()
            ->each(fn (Achievement $a) => $a->setAttribute('is_earned', $earned->has($a->id)));

        return AchievementResource::collection($achievements);
    }

    public function mine(Request $request): AnonymousResourceCollection
    {
        return AchievementResource::collection($request->user()->achievements()->get());
    }
}
