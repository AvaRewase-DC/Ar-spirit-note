<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QrCodeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'data' => 'required|string',
        ];
    }
}
