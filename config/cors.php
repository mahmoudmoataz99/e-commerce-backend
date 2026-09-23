<?php

return [

    // this makes sure our api can be called from the react native app
    // (and from a browser during testing) without getting blocked
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // using * for now to keep it simple while building/testing.
    // if you go to production, its better to list your real app domains here instead
    'allowed_origins' => ['http://localhost:8081'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
