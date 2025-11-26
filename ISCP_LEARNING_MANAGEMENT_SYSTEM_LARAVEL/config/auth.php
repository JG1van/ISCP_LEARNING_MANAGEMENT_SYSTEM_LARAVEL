<?php

return [

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'admins',
    ],

    // ==============================
    // GUARDS
    // ==============================
    'guards' => [

        // DEFAULT — untuk ADMIN
        'web' => [
            'driver' => 'session',
            'provider' => 'admins',
        ],

        // Guru & Siswa pakai login() manual tanpa guard
    ],

    // ==============================
    // PROVIDERS (3 MODEL)
    // ==============================
    'providers' => [

        // ADMIN TABLE
        'admins' => [
            'driver' => 'eloquent',
            'model' => App\Models\Admin::class,
        ],

        // GURU (Users)
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],

        // STUDENT
        'students' => [
            'driver' => 'eloquent',
            'model' => App\Models\Student::class,
        ],
    ],

    // ==============================
    // PASSWORD RESET
    // ==============================
    'passwords' => [

        'admins' => [
            'provider' => 'admins',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,
];
