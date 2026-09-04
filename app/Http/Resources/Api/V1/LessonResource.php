<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'level' => $this->whenLoaded('level', fn () => [
                'code' => $this->level?->code,
                'name' => $this->level?->name,
            ]),
            'order_number' => $this->order_number,
            // Теория — большой HTML, в списке она не нужна: отдаём только
            // в карточке урока, где ресурс создаётся с полным флагом.
            'theory' => $this->when($request->routeIs('api.v1.lessons.show'), fn () => $this->theory),
            'is_completed' => $this->when(
                isset($this->user_is_completed),
                fn () => (bool) $this->user_is_completed
            ),
        ];
    }
}
