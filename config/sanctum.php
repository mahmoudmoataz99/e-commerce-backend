<?php

use Laravel\Sanctum\Sanctum;

return [

    // these are domains that are allowed to use cookie based auth with sanctum
    // (mostly for web apps). For our react native app we use bearer tokens
    // instead so this doesnt matter too much, but keeping it here just in case
    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
        '%s%s',
        'localhost,localhost:3000,localhost:8081,127.0.0.1,127.0.0.1:8000,::1',
        Sanctum::currentApplicationUrlWithPort(),
    ))),

    'guard' => ['web'],

    // how many minutes before a token expires. null means tokens never expire
    // (fine for a school/demo project, you might want to change this later)
    'expiration' => null,

    'token_prefix' => env('SANCTUM_TOKEN_PREFIX', ''),

    'middleware' => [
        'authenticate_session' => Laravel\Sanctum\Http\Middleware\AuthenticateSession::class,
        'encrypt_cookies' => Illuminate\Cookie\Middleware\EncryptCookies::class,
        'validate_csrf_token' => Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
    ],

];
