<?php

function verify_license($license_key, $domain, $site_id, $config): array {
    
    $valid_domains = [
        'LS-123456' => ['customer-site.com', 'dev.customer-site.com'],
        'LS-789012' => ['another-site.com'],
    ];
    
    $license_id = substr($license_key, 0, 9);
    
    // 🔍 Check if domain is allowed
    if (isset($valid_domains[$license_id])) {
        $allowed = false;
        foreach ($valid_domains[$license_id] as $allowed_domain) {
            if ($domain === $allowed_domain || str_ends_with($domain, '.' . $allowed_domain)) {
                $allowed = true;
                break;
            }
        }
        
        if (!$allowed) {
            return [
                'status'   => 'inactive',
                'message'  => 'Domain not authorized in Lemon Squeezy',
                'features' => [],
                'ttl'      => $config['default_ttl'],
            ];
        }
    }
    
    return [
        'status'     => 'active',
        'features'   => ['telegram', 'automation'],
        'grace_days' => $config['grace_days'],
        'ttl'        => $config['default_ttl'],
        'message'    => 'Validated via Lemon Squeezy (simulated)',
    ];
}