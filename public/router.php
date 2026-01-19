<?php
// Custom router for PHP built-in server to ensure CORS headers on static files
// Usage: php -S localhost:8080 -t public public/router.php

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/');
$fullPath = __DIR__ . $uri;

// Handle CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS, HEAD');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, Accept, Origin, X-Requested-With');
    header('Access-Control-Max-Age: 86400');
    http_response_code(204);
    exit;
}

// Serve static files under /uploads or /images or /signatures with CORS headers
$staticPrefixes = ['/uploads/', '/images/', '/signatures/'];
foreach ($staticPrefixes as $prefix) {
    if (strpos($uri, $prefix) === 0 && is_file($fullPath)) {
        $mime = mime_content_type($fullPath) ?: 'application/octet-stream';
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, HEAD, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, Accept, Origin, X-Requested-With');
        header('Cache-Control', 'public, max-age=3600');
        header('Content-Type', $mime);
        header('Content-Length', (string) filesize($fullPath));
        readfile($fullPath);
        return true;
    }
}

// Let the built-in server handle other existing files (css/js/etc)
if (php_sapi_name() === 'cli-server' && is_file($fullPath)) {
    return false;
}

// Fallback to Slim front controller
require __DIR__ . '/index.php';
