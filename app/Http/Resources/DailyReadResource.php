<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DailyReadResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'coptic_date' => $this->getCopticDate(),
            'read_parts' => $this->read_parts,
            'day' => $this->day,
            'description' => $this->description,
            'katamars' => $this->katamars,
            'bible' => $this->bible,
            'quiz' => $this->quiz,
            'videos' => $this->whenLoaded('videos', fn () => $this->videos->map->video),
            'saint_fests' => $this->whenLoaded('saintFests', fn () => $this->saintFests->map->title),
        ];
    }
}
