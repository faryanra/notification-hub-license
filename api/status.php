<?php
header('Content-Type: application/json');

$status = [
    'status' => 'operational',
    'service' => 'Notification Hub License Server',
    'version' => '1.0.0',
    'timestamp' => time(),
    'endpoints' => [
        '/verify' => 'License verification (POST/GET)',
        '/status' => 'Service health check',
        '/' => 'Information page'
    ],
    'uptime' => '100%',
    'environment' => getenv('VERCEL') ? 'vercel' : 'self-hosted'
];

echo json_encode($status, JSON_PRETTY_PRINT);