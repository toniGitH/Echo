<?php

return [

    'validation' => [
        'error' => 'Validation error. Please check the provided fields.',
        'required' => 'The :field field is required.',
    ],

    'user' => [
        'registered_success' => 'User registered successfully.',
        'EMAIL_ALREADY_EXISTS' => 'The email you entered already exists. You must choose another one',
        'EMPTY_USER_ID' => 'Internal error: User ID not generated.',
        'INVALID_USER_ID_FORMAT' => 'The user ID has an invalid format.',
        
        // Empty fields
        'EMPTY_EMAIL' => 'The email field is required.',
        'EMPTY_PASSWORD' => 'The password field is required.',
        'EMPTY_NAME' => 'The name field is required.',
        
        // Invalid formats
        'INVALID_EMAIL_FORMAT' => 'The email has an invalid format. It should be like email@email.com',
        'INVALID_USER_NAME' => 'The name must be between 3 and 100 characters.',
        'INVALID_PASSWORD' => 'The password must contain at least 8 characters, one uppercase, one lowercase, one number, and one special character.',
        'PASSWORD_CONFIRMATION_MISMATCH' => 'The password confirmation does not match.',
    ],

    'unexpected_error' => 'An unexpected error occurred. Please try again later.',
];
