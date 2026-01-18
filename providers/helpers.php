<?php

/**
 * Standardized JSON response for license API
 */
function respond(array $data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    
    // 🔐 Security headers
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    
    echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Normalize domain for consistent comparison
 */
function normalize_domain(string $domain): string {
    $domain = strtolower(trim($domain));
    $domain = preg_replace('#^https?://#', '', $domain);
    $domain = preg_replace('#^www\.#', '', $domain);
    return rtrim($domain, '/');
}

/**
 * Log license verification attempts (compatible with Vercel)
 */
function log_attempt(string $license_key, string $domain, string $source, array $result, float $execution_time = 0): void {
    try {
        $log_dir = __DIR__ . '/../logs';
        
        // در Vercel شاید نتوانیم فایل بنویسیم، پس به جای آن console log کنیم
        if (getenv('VERCEL') === '1') {
            // در Vercel از error_log استفاده می‌کنیم
            error_log(json_encode([
                'time' => date('Y-m-d H:i:s'),
                'license' => substr($license_key, 0, 8) . '...',
                'domain' => $domain,
                'source' => $source,
                'status' => $result['status'],
                'execution_time' => $execution_time . 'ms',
                'ip' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]));
            return;
        }
        
        // در هاست معمولی به فایل لاگ می‌نویسیم
        if (!file_exists($log_dir)) {
            @mkdir($log_dir, 0755, true);
        }
        
        $log_entry = sprintf(
            "[%s] %s | %s | %s | %s | %s | %sms\n",
            date('Y-m-d H:i:s'),
            $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
            substr($license_key, 0, 8) . '...',
            $domain,
            $source,
            $result['status'],
            $execution_time
        );
        
        @file_put_contents($log_dir . '/verification.log', $log_entry, FILE_APPEND);
        
        // Keep log file size under control (1MB max)
        $log_file = $log_dir . '/verification.log';
        if (file_exists($log_file) && filesize($log_file) > 1048576) { // 1MB
            $lines = file($log_file, FILE_IGNORE_NEW_LINES);
            $keep_lines = array_slice($lines, -1000); // Keep last 1000 lines
            file_put_contents($log_file, implode("\n", $keep_lines) . "\n");
        }
    } catch (Exception $e) {
        // Silent fail on logging errors
    }
}