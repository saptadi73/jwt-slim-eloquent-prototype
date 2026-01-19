<?php
namespace App\Middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response as SlimResponse;

/**
 * CorsMiddleware - Handle CORS headers untuk all responses
 * 
 * Configure untuk allow cross-origin requests dari any origin
 * dengan all necessary headers dan methods
 */
final class CorsMiddleware implements MiddlewareInterface
{
    /**
     * Allowed origins (set '*' untuk allow semua)
     * Bisa di-override via environment variable
     */
    private string $allowedOrigin;

    /**
     * Allowed methods
     */
    private array $allowedMethods = [
        'GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS', 'HEAD'
    ];

    /**
     * Allowed headers
     */
    private array $allowedHeaders = [
        'Authorization',
        'Content-Type',
        'Accept',
        'Origin',
        'X-Requested-With',
        'X-CSRF-Token',
        'User-Agent',
        'Accept-Language',
        'Content-Length',
        'If-Modified-Since',
    ];

    /**
     * Max age untuk preflight cache (dalam seconds)
     */
    private int $maxAge = 86400;

    public function __construct()
    {
        // Allow semua origin secara default
        // Bisa dikontrol via ENV: CORS_ALLOWED_ORIGIN
        $this->allowedOrigin = $_ENV['CORS_ALLOWED_ORIGIN'] ?? '*';
    }

    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        $method = strtoupper($request->getMethod());

        // Handle OPTIONS preflight request
        if ($method === 'OPTIONS') {
            $response = new SlimResponse(204);
            return $this->applyCors($response, $request);
        }

        // Handle regular request
        $response = $handler->handle($request);
        return $this->applyCors($response, $request);
    }

    /**
     * Apply CORS headers ke response
     */
    private function applyCors(Response $response, Request $request): Response
    {
        // Determine origin
        $origin = $this->getAllowedOrigin($request);

        // Apply headers
        $response = $response
            ->withHeader('Access-Control-Allow-Origin', $origin)
            ->withHeader(
                'Access-Control-Allow-Methods',
                implode(', ', $this->allowedMethods)
            )
            ->withHeader(
                'Access-Control-Allow-Headers',
                implode(', ', $this->allowedHeaders)
            )
            ->withHeader('Access-Control-Max-Age', (string)$this->maxAge)
            ->withHeader('Access-Control-Allow-Credentials', 'true')
            ->withHeader('Vary', 'Origin');

        // Add exposed headers untuk allow client read custom headers
        if ($response->hasHeader('X-Total-Count')) {
            $response = $response->withHeader(
                'Access-Control-Expose-Headers',
                'X-Total-Count, X-Page, X-Per-Page'
            );
        }

        return $response;
    }

    /**
     * Determine allowed origin
     * 
     * Return '*' jika config allow all
     * Otherwise return requesting origin jika dalam whitelist
     */
    private function getAllowedOrigin(Request $request): string
    {
        // If configured to allow all origins
        if ($this->allowedOrigin === '*') {
            return '*';
        }

        // Get requesting origin
        $requestOrigin = $request->getHeaderLine('Origin');

        // If no origin header, return config origin
        if (!$requestOrigin) {
            return $this->allowedOrigin;
        }

        // Check if requesting origin dalam whitelist
        $whitelist = array_map('trim', explode(',', $this->allowedOrigin));
        if (in_array($requestOrigin, $whitelist, true)) {
            return $requestOrigin;
        }

        // Default to first allowed origin
        return $whitelist[0] ?? '*';
    }
}
