<?php

use Illuminate\Support\Str;

return [
    'driver' => env('SESSION_DRIVER', 'file'),
    'lifetime' => env('SESSION_LIFETIME', 120),
    'expire_on_close' => false,
    'encrypt' => false,
    'files' => storage_path('framework/sessions'),
    'connection' => env('SESSION_CONNECTION'),
    'table' => 'sessions',
    'store' => env('SESSION_STORE'),
    'lottery' => [2, 100],
    'cookie' => env('SESSION_COOKIE', 'laravel_session'),
    'path' => '/',
    'domain' => env('SESSION_DOMAIN', null), // Để trống nếu không sử dụng subdomain
    'secure' => env('SESSION_SECURE_COOKIE', null),  // Đặt là false nếu không dùng HTTPS
    'http_only' => true,
    'same_site' => 'lax', // Để cookie hoạt động trong các yêu cầu cross-origin
    'partitioned' => false,

];
