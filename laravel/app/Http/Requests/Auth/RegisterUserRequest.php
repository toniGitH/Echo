<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

final class RegisterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'email' => [
            'required',
            'string',
            'email:filter',
            'unique:users,email',
        ],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:50',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_\-+=\[\]{}|;:\'",.< >\/?¿]).+$/',
                'confirmed',
            ],
            // IMPORTANTE: El rol 'admin' NO se puede usar en el registro público.
            // Los administradores solo pueden ser designados por clientes mediante
            // un endpoint específico (aún no implementado en esta fase).
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['required', 'string', 'in:client,follower'],
        ];
    }

    public function messages(): array
    {
        return [
            // Name
            'name.required' => __('messages.user.EMPTY_NAME'),
            'name.min' => __('messages.user.INVALID_USER_NAME'),
            'name.max' => __('messages.user.INVALID_USER_NAME'),
            'email.unique' => __('messages.user.EMAIL_ALREADY_EXISTS'),

            // Email
            'email.required' => __('messages.user.EMPTY_EMAIL'),
            'email.email' => __('messages.user.INVALID_EMAIL_FORMAT'),
            'email.max' => __('messages.user.INVALID_EMAIL_FORMAT'),

            // Password
            'password.required' => __('messages.user.EMPTY_PASSWORD'),
            'password.min' => __('messages.user.INVALID_PASSWORD'),
            'password.max' => __('messages.user.INVALID_PASSWORD'),
            'password.regex' => __('messages.user.INVALID_PASSWORD'),
            'password.confirmed' => __('messages.user.PASSWORD_CONFIRMATION_MISMATCH'), // ⚠️ ESPECÍFICO

            // Roles
            'roles.required' => __('messages.user.ROLES_REQUIRED'),
            'roles.*.in' => __('messages.user.INVALID_ROLE_FOR_REGISTRATION'),
        ];
    }

}