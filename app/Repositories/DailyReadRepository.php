<?php

namespace App\Repositories;

use App\Models\DailyRead;
use App\Models\DailyReadVideo;

class DailyReadRepository
{
    public function __construct(protected DailyRead $model) {}

    public function store($input)
    {
        $new = $this->model->create([
            'day' => $input['day'],
            'description' => $input['description'] ?? null,
            'katamars' => $input['katamars'] ?? null,
            'read_parts' => $input['read_parts'],
            'bible' => $input['bible'] ?? null,
            'quiz' => $input['quiz'] ?? null,
        ]);
        if (! empty($input['videos'])) {
            foreach ($input['videos'] as $video) {
                DailyReadVideo::create([
                    'daily_read_id' => $new->id,
                    'video' => $video,
                ]);
            }
        }

        return $new;
    }
}
