<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | Ini menentukan guard dan konfigurasi password reset default aplikasi.
    | Karena sistemmu hanya memiliki login admin, maka default diarahkan
    | ke guard 'web' dan provider 'admins'.
    |
    */

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'admins',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Setiap guard mewakili cara login yang berbeda (misalnya admin, user).
    | Guard 'web' di bawah menggunakan driver 'session' dan provider 'admins'.
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'admins',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | Provider menentukan bagaimana data pengguna diambil dari database.
    | Di sini kita gunakan model App\Models\Admin untuk tabel 'admins'.
    |
    */

    'providers' => [
        'admins' => [
            'driver' => 'eloquent',
            'model' => App\Models\Admin::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | Konfigurasi reset password untuk admin.
    | 'table' menyimpan token reset, 'expire' waktu token berlaku (menit).
    |
    */

    'passwords' => [
        'admins' => [
            'provider' => 'admins',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Waktu (dalam detik) sebelum password confirmation kadaluarsa.
    | Default: 3 jam (10800 detik).
    |
    */

    'password_timeout' => 10800,

];
