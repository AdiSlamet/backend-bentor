<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    */
    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    */
    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
        'api' => [
            'driver' => 'sanctum', // Atau 'sanctum' jika pakai Sanctum
            'provider' => 'admins',
        ],
        'admin' => [
            'driver' => 'sanctum',
            'provider' => 'admins',
        ],
        'driver' => [
            'driver' => 'sanctum',
            'provider' => 'drivers',
        ],
        // 'api' => [
        //     'driver' => 'sanctum',
        //     'provider' => 'drivers',
        // ],
        'user' => [
            'driver' => 'sanctum',
            'provider' => 'users',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    */
    'providers' => [
        'admins' => [
            'driver' => 'eloquent',
            'model' => App\Models\Admin\Admin::class,
        ],
        'drivers' => [
            'driver' => 'eloquent',
            'model' => App\Models\Driver\Driver::class,
        ],
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    */
    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    */
    'password_timeout' => 10800,
];