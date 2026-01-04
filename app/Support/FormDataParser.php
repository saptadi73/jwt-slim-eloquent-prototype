<?php

namespace App\Support;

use Psr\Http\Message\ServerRequestInterface as Request;

class FormDataParser
{
    /**
     * Parse form data from request, handling multipart/form-data properly
     * Returns both parsed body and uploaded files
     */
    public static function parse(Request $request): array
    {
        $data = [];
        $files = [];
        
        // Get content type
        $contentType = $request->getHeaderLine('Content-Type');
        
        // Parse based on content type
        if (stripos($contentType, 'application/json') !== false) {
            // JSON data
            $body = $request->getBody()->getContents();
            $data = json_decode($body, true) ?? [];
        } else if (stripos($contentType, 'multipart/form-data') !== false) {
            // Multipart form data
            // Slim already parses this, get both body and files
            $data = $request->getParsedBody() ?? [];
            $files = $request->getUploadedFiles() ?? [];
        } else {
            // Form URL encoded
            $data = $request->getParsedBody() ?? [];
        }
        
        return [
            'data' => is_array($data) ? $data : [],
            'files' => is_array($files) ? $files : []
        ];
    }
}
