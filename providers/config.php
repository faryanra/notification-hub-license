<?php

return [
    'product' => 'notification-hub',

    'grace_days' => 7,
    'default_ttl' => 86400, // 24h

    'providers' => [
        'direct',
        'lemon',
        'freemius',
        'envato'
    ],
];
