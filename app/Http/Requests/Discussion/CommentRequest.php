<?php

namespace App\Http\Requests\Discussion;

use App\Http\Requests\ApiRequest;

class CommentRequest extends ApiRequest
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
            'commented' => 'required|string',
            'post_id' => 'required|integer',
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
            'commented.required' => 'Comment type is required',
            'post_id.required' => 'Post ID is required',
            'content.required' => 'Content is required',
        ];
    }
}
