<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ApiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            'title' => config('app-settings.api.title_prefix', 'E - Syakl') . ' | Validation Error',
            'code' => 422,
            'message' => 'Invalid Input',
            'data' => $validator->errors()
        ], 422));
    }
}
