<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\ApiRequest;

class LoginRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email|min:7',
            'password' => 'required|min:8',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Invalid Input',
            'email.email' => 'Invalid Input',
            'email.min' => 'Invalid Input. Please check your email length!',
            'password.required' => 'Invalid Input',
            'password.min' => 'Invalid Input. Please check your password length!',
        ];
    }
}
