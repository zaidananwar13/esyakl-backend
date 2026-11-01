<?php

namespace App\Http\Requests\Discussion;

use App\Http\Requests\ApiRequest;

class CreateDiscussionRequest extends ApiRequest
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
            'class' => 'required|integer|exists:class,id_class',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
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
            'class.required' => 'Class ID is required',
            'class.exists' => 'Class not found',
            'title.required' => 'Title is required',
            'content.required' => 'Content is required',
        ];
    }
}
