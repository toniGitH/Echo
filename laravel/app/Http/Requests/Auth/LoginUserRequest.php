<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

final class LoginUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email:filter'],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:50',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_\-+=\[\]{}|;:\'",.<>\/?¿]).+$/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            // Email
            'email.required' => __('messages.user.EMPTY_EMAIL'),
            'email.email' => __('messages.user.INVALID_EMAIL_FORMAT'),

            // Password
            'password.required' => __('messages.user.EMPTY_PASSWORD'),
            'password.min' => __('messages.user.INVALID_PASSWORD'),
            'password.max' => __('messages.user.INVALID_PASSWORD'),
            'password.regex' => __('messages.user.INVALID_PASSWORD'),
        ];
    }
}
