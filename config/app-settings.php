<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Settings
    |--------------------------------------------------------------------------
    */
    'api' => [
        'title_prefix' => env('API_TITLE_PREFIX', 'E - Syakl'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Frontend URL Configuration
    |--------------------------------------------------------------------------
    | Used for OAuth redirects and email links.
    */
    'frontend_url' => env('FRONTEND_URL', 'https://arabic-go.com'),

    /*
    |--------------------------------------------------------------------------
    | Password Settings
    |--------------------------------------------------------------------------
    */
    'password' => [
        'min_length' => 8,
        'require_uppercase' => true,
        'require_lowercase' => true,
        'require_number' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Reset Settings
    |--------------------------------------------------------------------------
    */
    'password_reset' => [
        'expiry_minutes' => env('PASSWORD_RESET_EXPIRY', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Verification Settings
    |--------------------------------------------------------------------------
    */
    'verification' => [
        'token_expiry_minutes' => env('VERIFICATION_TOKEN_EXPIRY', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | Class/Course Settings
    |--------------------------------------------------------------------------
    */
    'class' => [
        'default_expiry_days' => env('CLASS_DEFAULT_EXPIRY_DAYS', 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | Quiz/Grading Settings
    |--------------------------------------------------------------------------
    */
    'grading' => [
        'pass_score' => env('QUIZ_PASS_SCORE', 78),
    ],

    /*
    |--------------------------------------------------------------------------
    | Media & Assets Settings
    |--------------------------------------------------------------------------
    */
    'media' => [
        'base_url' => env('MEDIA_BASE_URL', 'https://admin.arabic-go.com/'),
        'upload_path' => 'uploads/media/',
    ],

    'storage' => [
        'base_url' => env('STORAGE_BASE_URL', 'https://api.arabic-go.com/storage/'),
    ],

    'certificate' => [
        'validity_years' => 2,
        'upload_path' => 'uploads/certificate/',
    ],

    'frontend' => [
        'base_url' => env('FRONTEND_URL', 'https://arabic-go.com/'),
        'local_url' => env('FRONTEND_LOCAL_URL', 'http://localhost:3000/'),
    ],

    /*
    |--------------------------------------------------------------------------
    | File Upload Settings
    |--------------------------------------------------------------------------
    */
    'uploads' => [
        'profile_image' => [
            'max_size' => 2 * 1024 * 1024, // 2MB
            'allowed_mimes' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
            'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        ],
        'project_file' => [
            'max_size' => 12 * 1024 * 1024, // 12MB
            'allowed_mimes' => [
                'application/zip',
                'application/x-rar-compressed',
                'application/pdf',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'text/plain',
            ],
            'allowed_extensions' => ['zip', 'rar', 'pdf', 'docx', 'txt'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    */
    'security' => [
        // Rate limiting
        'rate_limits' => [
            'login' => ['attempts' => 5, 'decay_minutes' => 1],
            'registration' => ['attempts' => 3, 'decay_minutes' => 1],
            'password_reset' => ['attempts' => 3, 'decay_minutes' => 5],
            'api_default' => ['attempts' => 60, 'decay_minutes' => 1],
        ],
    ],
];
