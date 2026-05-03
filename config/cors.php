<?php

return [
    'paths' => ['api/*'],
    'allowed_methods' => ['GET'],
    'allowed_origins' => [
        'http://localhost:8080',
        env('FRONTEND_URL', ''),
    ],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
