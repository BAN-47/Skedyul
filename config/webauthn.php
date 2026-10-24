<?php

$origin = rtrim(
    (string) env('WEBAUTHN_ORIGIN', env('APP_URL', 'http://localhost')),
    '/'
);

return [
    'rp_id' => env(
        'WEBAUTHN_RP_ID',
        parse_url($origin, PHP_URL_HOST) ?: 'localhost'
    ),
    'rp_name' => env('WEBAUTHN_RP_NAME', env('APP_NAME', 'SKEDYUL')),
    'origin' => $origin,
];