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
            'description' => $input['description'],
            'katamars' => $input['katamars'],
            'read_parts' => $input['read_parts'],
            'bible' => $input['bible'],
            'quiz' => $input['quiz'],
        ]);
        foreach ($input['videos'] as $video) {
            DailyReadVideo::create([
                'daily_read_id' => $new->id,
                'video' => $video,
            ]);
        }

        return $new;
    }
}
