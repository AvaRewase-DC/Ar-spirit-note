<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShowDailyReadRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'date' => 'date|exists:daily_reads,day',
        ];
    }
}
