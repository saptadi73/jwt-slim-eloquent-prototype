<?php

namespace App\Middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;

class MethodOverrideMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $method = $request->getMethod();
        
        // Jika POST, cek apakah ada _method override
        if ($method === 'POST') {
            $body = $request->getParsedBody();
            if (is_array($body) && isset($body['_method'])) {
                $request = $request->withMethod(strtoupper($body['_method']));
            }
        }
        
        return $handler->handle($request);
    }
}
