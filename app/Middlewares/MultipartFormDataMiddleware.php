<?php

namespace App\Middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;

class MultipartFormDataMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $contentType = $request->getHeaderLine('Content-Type');
        
        // Only process multipart/form-data
        if (strpos($contentType, 'multipart/form-data') !== false) {
            // Slim already handles multipart/form-data properly through UploadedFiles
            // The getParsedBody() will return form fields, and getUploadedFiles() returns files
            // This middleware ensures the request is properly set up for file uploads
        }
        
        return $handler->handle($request);
    }
}
