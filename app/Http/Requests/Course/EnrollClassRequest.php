<?php

namespace App\Http\Requests\Course;

use App\Http\Requests\ApiRequest;

class EnrollClassRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'api_token' => 'required|string',
            'kelas' => 'required|integer|exists:class,id_class',
        ];
    }

    public function messages(): array
    {
        return [
            'api_token.required' => 'Unauthorized',
            'kelas.required' => 'Class ID is required',
            'kelas.exists' => 'Class not found',
        ];
    }
}
