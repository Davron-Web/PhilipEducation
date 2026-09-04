<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'plan' => $this->whenLoaded('plan', fn () => [
                'code' => $this->plan?->code,
                'name' => $this->plan?->name,
                'duration_days' => $this->plan?->duration_days,
            ]),
            'is_gift' => $this->source === 'gift',
            'starts_at' => $this->starts_at?->toIso8601String(),
            // null — бессрочный доступ, выданный администратором.
            'ends_at' => $this->ends_at?->toIso8601String(),
            'days_left' => $this->ends_at ? max(0, (int) now()->diffInDays($this->ends_at, false)) : null,
        ];
    }
}
