<?php

function verify_license($license_key, $domain, $site_id, $config): array {
    return [
        'status'     => 'active',
        'features'   => ['telegram', 'automation'],
        'grace_days' => $config['grace_days'],
        'ttl'        => $config['default_ttl'],
        'source'     => 'freemius',
        'message'    => 'Validated via Freemius (stub)',
    ];
}
