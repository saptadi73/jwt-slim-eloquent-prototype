<?php
use Slim\App;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function (App $app) {
    // Test route - GET /api/test
    $app->get('/api/test', function (Request $request, Response $response) {
        $data = [
            'status' => 'success',
            'message' => 'Slim PHP 4 is running!',
            'timestamp' => date('Y-m-d H:i:s'),
            'php_version' => phpversion(),
            'app_env' => $_ENV['APP_ENV'] ?? 'unknown',
        ];
        
        $response->getBody()->write(json_encode($data, JSON_PRETTY_PRINT));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    });

    // Health check - GET /health
    $app->get('/health', function (Request $request, Response $response) {
        $data = [
            'status' => 'ok',
            'timestamp' => time(),
        ];
        
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    });

    // Root path
    $app->get('/', function (Request $request, Response $response) {
        $env = $_ENV['APP_ENV'] ?? 'unknown';
        $debug = $_ENV['APP_DEBUG'] ?? 'false';
        $phpVersion = phpversion();
        $dbDriver = $_ENV['DB_DRIVER'] ?? 'unknown';
        $dbHost = $_ENV['DB_HOST'] ?? 'unknown';
        
        $html = '<!DOCTYPE html>
<html>
<head>
    <title>Slim PHP 4 Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .container { max-width: 600px; }
        h1 { color: #333; }
        .success { color: green; }
        pre { background: #f4f4f4; padding: 10px; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="success">✓ Slim PHP 4 is Running!</h1>
        <p>Selamat! Aplikasi Slim PHP 4 Anda berhasil dijalankan.</p>
        
        <h2>Test Routes Available:</h2>
        <ul>
            <li><a href="/api/test">/api/test</a> - JSON Test Response</li>
            <li><a href="/health">/health</a> - Health Check</li>
        </ul>
        
        <h2>Info:</h2>
        <pre>
Environment: ' . $env . '
Debug Mode: ' . $debug . '
PHP Version: ' . $phpVersion . '
Database: ' . $dbDriver . ' (' . $dbHost . ')
        </pre>
    </div>
</body>
</html>';
        
        $response->getBody()->write($html);
        return $response
            ->withHeader('Content-Type', 'text/html; charset=utf-8')
            ->withStatus(200);
    });
};
