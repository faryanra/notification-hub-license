<?php
// ============================================
// NOTIFICATION HUB LICENSE SERVER
// ============================================

// 🔐 Allow CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// 📁 Load config and helpers (مسیر اصلاح شده برای Vercel)
$config = require __DIR__ . '/../providers/config.php';
require __DIR__ . '/../providers/helpers.php';

// 🕒 Start timing for logging
$start_time = microtime(true);

// 📥 Accept both GET and POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $license_key = trim($_POST['license_key'] ?? '');
    $domain      = normalize_domain($_POST['domain'] ?? '');
    $product     = $_POST['product'] ?? '';
    $site_id     = $_POST['site_id'] ?? null;
    $version     = $_POST['version'] ?? '';
} else {
    $license_key = trim($_GET['license_key'] ?? '');
    $domain      = normalize_domain($_GET['domain'] ?? '');
    $product     = $_GET['product'] ?? '';
    $site_id     = $_GET['site_id'] ?? null;
    $version     = $_GET['version'] ?? '';
}

// 🔐 Validation
if ($product !== $config['product']) {
    respond([
        'status'  => 'inactive',
        'message' => 'Invalid product: ' . $product,
        'ttl'     => $config['default_ttl']
    ], 200);
}

if ($license_key === '' || $domain === '') {
    respond([
        'status'  => 'inactive',
        'message' => 'Missing license_key or domain',
        'ttl'     => $config['default_ttl']
    ], 200);
}

// 🏷️ Detect license source based on prefix
$source = 'direct';
if (str_starts_with($license_key, 'LS-')) {
    $source = 'lemon';
} elseif (str_starts_with($license_key, 'FM-')) {
    $source = 'freemius';
} elseif (str_starts_with($license_key, 'EV-')) {
    $source = 'envato';
}

// 📁 Load provider (مسیر اصلاح شده برای Vercel)
$provider_file = __DIR__ . "/../providers/{$source}.php";

if (!file_exists($provider_file)) {
    respond([
        'status'  => 'inactive',
        'message' => 'License provider not supported: ' . $source,
        'ttl'     => $config['default_ttl']
    ], 200);
}

require $provider_file;

// 🔍 Verify license with selected provider
$result = verify_license(
    $license_key,
    $domain,
    $site_id,
    $config
);

// 🔒 Ensure status is from allowed set
$allowed_statuses = ['active', 'inactive', 'revoked', 'grace', 'banned'];
if (!in_array($result['status'], $allowed_statuses)) {
    $result['status'] = 'inactive';
}

// 📦 Add missing fields if not provided by provider
$result['source'] = $source;
$result['ttl'] = $result['ttl'] ?? $config['default_ttl'];
$result['grace_days'] = $result['grace_days'] ?? $config['grace_days'];
$result['features'] = $result['features'] ?? [];

// 🪵 Log the attempt
$execution_time = round((microtime(true) - $start_time) * 1000, 2); // ms
log_attempt($license_key, $domain, $source, $result, $execution_time);

// 📤 Send response
respond($result);