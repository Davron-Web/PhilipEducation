<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AchievementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'icon' => $this->icon,
            'points' => (int) $this->points,
            'is_earned' => $this->when(isset($this->is_earned), fn () => (bool) $this->is_earned),
            'earned_at' => $this->whenPivotLoaded('user_achievements', fn () => $this->pivot->earned_at?->toIso8601String()),
        ];
    }
}
