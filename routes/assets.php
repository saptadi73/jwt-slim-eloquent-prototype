<?php

use Slim\App;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

return function (App $app) {
    /**
     * Static Files & Assets Routes with CORS Headers
     * 
     * Routes untuk serve image, signature, dan file uploads dengan CORS headers
     */

    // ===========================================================================
    // IMAGES & UPLOADS - With CORS Headers
    // ===========================================================================
    
    /**
     * GET /uploads/:path
     * Serve any file dari uploads folder dengan CORS headers
     */
    $app->get('/uploads/{path:.+}', function (Request $request, Response $response, array $args) {
        $path = $args['path'] ?? '';
        
        // Security: prevent directory traversal
        if (strpos($path, '..') !== false || strpos($path, '\\') !== false) {
            return $response->withStatus(403);
        }

        $filePath = __DIR__ . '/../public/uploads/' . $path;

        // Check if file exists
        if (!is_file($filePath)) {
            return $response->withStatus(404);
        }

        // Check if readable
        if (!is_readable($filePath)) {
            return $response->withStatus(403);
        }

        // Determine MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filePath);
        finfo_close($finfo);

        if (!$mimeType) {
            $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            $mimeMap = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
                'pdf' => 'application/pdf',
                'txt' => 'text/plain',
            ];
            $mimeType = $mimeMap[$ext] ?? 'application/octet-stream';
        }

        // Read file
        $content = file_get_contents($filePath);
        $response->getBody()->write($content);

        // Send response with CORS headers
        return $response
            ->withHeader('Content-Type', $mimeType)
            ->withHeader('Content-Length', strlen($content))
            ->withHeader('Cache-Control', 'public, max-age=3600')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Methods', 'GET, HEAD, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    });

    /**
     * GET /images/:path
     * Alias untuk /uploads/:path
     */
    $app->get('/images/{path:.+}', function (Request $request, Response $response, array $args) {
        $path = $args['path'] ?? '';
        
        // Security: prevent directory traversal
        if (strpos($path, '..') !== false || strpos($path, '\\') !== false) {
            return $response->withStatus(403);
        }

        $filePath = __DIR__ . '/../public/uploads/' . $path;

        // Check if file exists
        if (!is_file($filePath)) {
            return $response->withStatus(404);
        }

        // Check if readable
        if (!is_readable($filePath)) {
            return $response->withStatus(403);
        }

        // Determine MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filePath);
        finfo_close($finfo);

        if (!$mimeType) {
            $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            $mimeMap = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
                'pdf' => 'application/pdf',
                'txt' => 'text/plain',
            ];
            $mimeType = $mimeMap[$ext] ?? 'application/octet-stream';
        }

        // Read file
        $content = file_get_contents($filePath);
        $response->getBody()->write($content);

        // Send response with CORS headers
        return $response
            ->withHeader('Content-Type', $mimeType)
            ->withHeader('Content-Length', strlen($content))
            ->withHeader('Cache-Control', 'public, max-age=3600')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Methods', 'GET, HEAD, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    });

    /**
     * OPTIONS /uploads/:path
     * Preflight untuk CORS
     */
    $app->options('/uploads/{path:.+}', function (Request $request, Response $response) {
        return $response
            ->withStatus(204)
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Methods', 'GET, HEAD, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
            ->withHeader('Access-Control-Max-Age', '86400');
    });

    /**
     * OPTIONS /images/:path
     * Preflight untuk CORS
     */
    $app->options('/images/{path:.+}', function (Request $request, Response $response) {
        return $response
            ->withStatus(204)
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Methods', 'GET, HEAD, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
            ->withHeader('Access-Control-Max-Age', '86400');
    });

    /**
     * GET /signatures/:path
     * Serve signature images dengan CORS headers
     */
    $app->get('/signatures/{path:.+}', function (Request $request, Response $response, array $args) {
        $path = $args['path'] ?? '';

        if (strpos($path, '..') !== false || strpos($path, '\\') !== false) {
            return $response->withStatus(403);
        }

        $filePath = __DIR__ . '/../public/uploads/signatures/' . $path;

        if (!is_file($filePath) || !is_readable($filePath)) {
            return $response->withStatus(404);
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filePath) ?: 'image/png';
        finfo_close($finfo);

        $content = file_get_contents($filePath);
        $response->getBody()->write($content);

        return $response
            ->withHeader('Content-Type', $mimeType)
            ->withHeader('Content-Length', strlen($content))
            ->withHeader('Cache-Control', 'public, max-age=3600')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Methods', 'GET, HEAD, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    });

    /**
     * OPTIONS /signatures/:path
     * Preflight untuk CORS
     */
    $app->options('/signatures/{path:.+}', function (Request $request, Response $response) {
        return $response
            ->withStatus(204)
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Methods', 'GET, HEAD, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
            ->withHeader('Access-Control-Max-Age', '86400');
    });
};
