<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    // API routes are served from the domain root (no "api/" prefix), so the
    // old ['api/*'] value meant Laravel never applied CORS at all.
    'paths' => ['*'],

    'allowed_methods' => ['*'],

    // Comma-separated list, e.g. "https://arabic-go.com,https://www.arabic-go.com".
    // The admin panel (CodeIgniter) talks to the database directly, so it needs no CORS entry.
    'allowed_origins' => array_values(array_filter(array_map('trim', explode(',', (string) env(
        'CORS_ALLOWED_ORIGINS',
        'https://arabic-go.com,https://www.arabic-go.com'
    ))))),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
