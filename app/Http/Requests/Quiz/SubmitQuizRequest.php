<?php

namespace App\Http\Requests\Quiz;

use App\Http\Requests\ApiRequest;

class SubmitQuizRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'api_token' => 'required|string',
            'id_syllabus' => 'required|integer|exists:syllabus,id_syllabus',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'api_token.required' => 'Unauthorized',
            'id_syllabus.required' => 'Syllabus ID is required',
            'id_syllabus.exists' => 'Syllabus not found',
        ];
    }
}
