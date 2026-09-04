<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Профиль текущего пользователя.
 *
 * Ресурс перечисляет поля явно, а не отдаёт модель целиком: так новая
 * колонка в users не утечёт в API сама собой — именно этим кончился
 * прежний JSON-профиль, отдававший всю модель вместе с email.
 */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'avatar_url' => $this->avatarUrl(),
            'level' => $this->whenLoaded('level', fn () => [
                'id' => $this->level?->id,
                'code' => $this->level?->code,
                'name' => $this->level?->name,
            ]),
            'role' => $this->whenLoaded('role', fn () => $this->role?->name),
            'xp' => (int) $this->points,
            'title' => $this->when(
                $this->relationLoaded('titles'),
                fn () => $this->currentTitle()?->only(['code', 'name', 'icon', 'min_xp'])
            ),
            'email_verified' => $this->hasVerifiedEmail(),
            'has_active_subscription' => $this->hasActiveSubscription(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
