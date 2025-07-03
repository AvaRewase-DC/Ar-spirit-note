<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDailyReadRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'day' => ['required', 'date'],
            'description' => ['nullable', 'string'],
            'katamars' => ['nullable', 'url'],
            'read_parts' => ['required', 'string'],
            'bible' => ['nullable', 'string'],
            'quiz' => ['nullable', 'url'],
            'videos' => ['nullable', 'array'],
            'videos.*' => ['required', 'url'],
        ];
    }
}
