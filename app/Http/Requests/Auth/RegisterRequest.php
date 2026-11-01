<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\ApiRequest;

class RegisterRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:user,email',
            'password' => 'required|min:6',
            'repassword' => 'required|same:password',
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'email sudah ada',
            'repassword.same' => 'password ga sama',
            'password.min' => 'Invalid Input. Please check password length!',
        ];
    }
}
