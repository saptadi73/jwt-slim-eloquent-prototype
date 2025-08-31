<?php
namespace App\Support;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;

class RequestHelper
{
    public static function getJsonBody(ServerRequestInterface $request)
    {
        $contentType = $request->getHeaderLine('Content-Type');
        if (strpos($contentType, 'application/json') !== false) {
            $body = (string)$request->getBody();
            $data = json_decode($body, true);
            return is_array($data) ? $data : [];
        }
        $data = $request->getParsedBody();
        return is_array($data) ? $data : [];
    }

     /**
     * Pilih satu file dari daftar key kandidat, mis. ['file','photo'].
     */
    public static function pickUploadedFile(ServerRequestInterface $request, array $keys = ['file','photo']): ? UploadedFileInterface
    {
        $files = $request->getUploadedFiles();
        foreach ($keys as $k) {
            if (isset($files[$k])) {
                return $files[$k];
            }
        }
        return null;
    }
    
    public static function parseMultipart(ServerRequestInterface $request): array
    {
        $data = $request->getParsedBody();
        $files = $request->getUploadedFiles();
        return [
            'data' => is_array($data) ? $data : [],
            'files' => is_array($files) ? $files : []
        ];
    }
}
