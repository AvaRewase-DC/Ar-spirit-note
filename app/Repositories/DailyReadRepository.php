<?php

namespace App\Repositories;

use App\Models\DailyRead;
use App\Models\DailyReadVideo;
use Carbon\Carbon;

class DailyReadRepository
{
    public function __construct(protected DailyRead $model) {}

    public function index($year, $month)
    {
        $startOfMonth = Carbon::create($year, $month, 1)->startOfDay();
        $endOfMonth = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();

        $reads = $this->model->whereDate('day', '>=', $startOfMonth)
            ->whereDate('day', '<=', $endOfMonth)
            ->orderBy('day')
            ->get();

        return $reads;
    }

    public function show($date)
    {
        return $this->model->whereDate('day', $date ?? today())->first();
    }

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

    public function edit($id)
    {
        return $this->model->find($id);
    }

    public function update($input, $id)
    {
        $read = $this->model->find($id);
        $read->update([
            'day' => $input['day'],
            'description' => $input['description'] ?? null,
            'katamars' => $input['katamars'] ?? null,
            'read_parts' => $input['read_parts'],
            'bible' => $input['bible'] ?? null,
            'quiz' => $input['quiz'] ?? null,
        ]);

        $read->videos->each(function ($video) {
            $video->delete();
        });
        if (! empty($input['videos'])) {
            foreach ($input['videos'] as $video) {
                DailyReadVideo::create([
                    'daily_read_id' => $read->id,
                    'video' => $video,
                ]);
            }
        }

        return $read;
    }
}
