<?php

function verify_license(
    string $license_key,
    string $domain,
    ?string $site_id,
    array $config
): array {
    
    // 🗃️ MVP: static licenses with domain binding
    $licenses = [
        'NH-DEV-1234' => [
            'status'   => 'active',
            'features' => ['telegram', 'automation', 'webhook'],
            'domains'  => ['test.com', 'localhost'],
        ],
        'NH-PRO-5678' => [
            'status'   => 'active',
            'features' => ['telegram', 'automation', 'webhook', 'slack'],
            'domains'  => ['example.com', 'staging.example.com'],
        ],
        'NH-REVOKED-0001' => [
            'status'   => 'revoked',
            'features' => [],
            'domains'  => [],
        ],
        'NH-EXPIRED-0002' => [
            'status'   => 'inactive',
            'features' => ['telegram'],
            'domains'  => ['expired.com'],
            'grace_days' => 3,
        ],
    ];
    
    // 🔍 License not found
    if (!isset($licenses[$license_key])) {
        return [
            'status'  => 'inactive',
            'message' => 'License not found',
            'ttl'     => 3600,
        ];
    }
    
    $license = $licenses[$license_key];
    
    // 🌐 Domain validation
    $domain_allowed = false;
    if (!empty($license['domains'])) {
        foreach ($license['domains'] as $allowed_domain) {
            if ($domain === $allowed_domain || str_ends_with($domain, '.' . $allowed_domain)) {
                $domain_allowed = true;
                break;
            }
        }
    }
    
    // If domains are specified but none match, reject
    if (!empty($license['domains']) && !$domain_allowed) {
        return [
            'status'   => 'inactive',
            'message'  => 'Domain not authorized for this license',
            'features' => [],
            'ttl'      => 86400,
        ];
    }
    
    return [
        'status'     => $license['status'],
        'features'   => $license['features'],
        'grace_days' => $license['grace_days'] ?? $config['grace_days'],
        'ttl'        => $config['default_ttl'],
        'message'    => $license['status'] === 'active' 
            ? 'License active for domain: ' . $domain 
            : ucfirst($license['status']) . ' license',
    ];
}