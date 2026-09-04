<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'time_limit' => $this->time_limit,
            'passing_score' => (int) $this->passing_score,
            'questions_count' => $this->whenCounted('questions'),
            'best_score' => $this->when(isset($this->best_score), fn () => (int) $this->best_score),
        ];
    }
}
