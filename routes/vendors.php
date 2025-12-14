<?php

use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Services\VendorService;
use App\Support\JsonResponder;
use App\Support\RequestHelper;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->group('/vendors', function (RouteCollectorProxy $vendor) use ($container) {
        $vendor->get('', function (Request $req, Response $res) use ($container) {
            try {
                $svc = $container->get(VendorService::class);
                return $svc->listVendors($res);
            } catch (\Throwable $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        });

        $vendor->get('/{id}', function (Request $req, Response $res, array $args) use ($container) {
            try {
                $svc = $container->get(VendorService::class);
                return $svc->getVendor($res, $args['id']);
            } catch (\Throwable $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        });

        $vendor->post('', function (Request $req, Response $res) use ($container) {
            // DEBUG: Lihat uploaded files di route level
            $uploadedFiles = $req->getUploadedFiles();
            $routeDebug = [
                'uploaded_files_keys' => array_keys($uploadedFiles),
                'uploaded_files_count' => count($uploadedFiles),
                'FILES_keys' => array_keys($_FILES ?? []),
                'FILES_count' => count($_FILES ?? []),
                'content_type' => $req->getHeaderLine('Content-Type'),
            ];
            
            // Cek setiap uploaded file
            foreach ($uploadedFiles as $key => $uploadedFile) {
                $routeDebug['file_' . $key] = [
                    'name' => $uploadedFile->getClientFilename(),
                    'size' => $uploadedFile->getSize(),
                    'error' => $uploadedFile->getError(),
                    'type' => $uploadedFile->getClientMediaType(),
                ];
            }
            
            /** @var VendorService $svc */
            $svc  = $container->get(VendorService::class);
            $data = $req->getParsedBody() ?? [];
            $file = RequestHelper::pickUploadedFile($req, ['gambar', 'file', 'photo']);
            
            $routeDebug['picked_file'] = $file ? [
                'class' => get_class($file),
                'name' => $file->getClientFilename(),
                'error' => $file->getError(),
            ] : null;
            
            try {
                $serviceResponse = $svc->createVendor($res, $data, $file);
                
                // Decode response untuk tambahkan route_debug
                $body = (string) $serviceResponse->getBody();
                $decoded = json_decode($body, true);
                if ($decoded) {
                    $decoded['route_debug'] = $routeDebug;
                    $res->getBody()->rewind();
                    $res->getBody()->write(json_encode($decoded));
                    return $res
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus($serviceResponse->getStatusCode());
                }
                return $serviceResponse;
            } catch (\InvalidArgumentException $e) {
                return JsonResponder::error($res, $e->getMessage(), 422);
            } catch (\Throwable $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'data'    => $data,
                    'route_debug' => $routeDebug,
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        });

        $vendor->put('/{id}', function (Request $req, Response $res, array $args) use ($container) {
            // DEBUG: Lihat uploaded files di route level
            $uploadedFiles = $req->getUploadedFiles();
            
            $routeDebug = [
                'uploaded_files_keys' => array_keys($uploadedFiles),
                'uploaded_files_count' => count($uploadedFiles),
                'FILES_keys' => array_keys($_FILES ?? []),
                'FILES_count' => count($_FILES ?? []),
                'content_type' => $req->getHeaderLine('Content-Type'),
            ];
            
            // Cek setiap uploaded file
            foreach ($uploadedFiles as $key => $uploadedFile) {
                $routeDebug['file_' . $key] = [
                    'name' => $uploadedFile->getClientFilename(),
                    'size' => $uploadedFile->getSize(),
                    'error' => $uploadedFile->getError(),
                    'type' => $uploadedFile->getClientMediaType(),
                ];
            }
            
            /** @var VendorService $svc */
            $svc  = $container->get(VendorService::class);
            $data = $req->getParsedBody() ?? [];
            $file = RequestHelper::pickUploadedFile($req, ['gambar', 'file', 'photo']);
            
            $routeDebug['picked_file'] = $file ? [
                'class' => get_class($file),
                'name' => $file->getClientFilename(),
                'error' => $file->getError(),
            ] : null;
            
            try {
                $serviceResponse = $svc->updateVendor($res, $args['id'], $data, $file);
                
                // Decode response untuk tambahkan route_debug
                $body = (string) $serviceResponse->getBody();
                $decoded = json_decode($body, true);
                if ($decoded) {
                    $decoded['route_debug'] = $routeDebug;
                    $res->getBody()->rewind();
                    $res->getBody()->write(json_encode($decoded));
                    return $res
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus($serviceResponse->getStatusCode());
                }
                return $serviceResponse;
            } catch (\InvalidArgumentException $e) {
                return JsonResponder::error($res, $e->getMessage(), 422);
            } catch (\Throwable $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'data'    => $data,
                    'route_debug' => $routeDebug,
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        });

        $vendor->delete('/{id}', function (Request $req, Response $res, array $args) use ($container) {
            try {
                $svc = $container->get(VendorService::class);
                return $svc->deleteVendor($res, $args['id']);
            } catch (\Throwable $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        });

        // Handler untuk POST ke {id} yang akan di-override ke PUT
        $vendor->post('/{id}', function (Request $req, Response $res, array $args) use ($container) {
            // DEBUG: Lihat uploaded files di route level
            $uploadedFiles = $req->getUploadedFiles();
            
            $routeDebug = [
                'uploaded_files_keys' => array_keys($uploadedFiles),
                'uploaded_files_count' => count($uploadedFiles),
                'FILES_keys' => array_keys($_FILES ?? []),
                'FILES_count' => count($_FILES ?? []),
                'content_type' => $req->getHeaderLine('Content-Type'),
                'method' => $req->getMethod(),
            ];
            
            // Cek setiap uploaded file
            foreach ($uploadedFiles as $key => $uploadedFile) {
                $routeDebug['file_' . $key] = [
                    'name' => $uploadedFile->getClientFilename(),
                    'size' => $uploadedFile->getSize(),
                    'error' => $uploadedFile->getError(),
                    'type' => $uploadedFile->getClientMediaType(),
                ];
            }
            
            /** @var VendorService $svc */
            $svc  = $container->get(VendorService::class);
            $data = $req->getParsedBody() ?? [];
            $file = RequestHelper::pickUploadedFile($req, ['gambar', 'file', 'photo']);
            
            $routeDebug['picked_file'] = $file ? [
                'class' => get_class($file),
                'name' => $file->getClientFilename(),
                'error' => $file->getError(),
            ] : null;
            
            try {
                $serviceResponse = $svc->updateVendor($res, $args['id'], $data, $file);
                
                // Decode response untuk tambahkan route_debug
                $body = (string) $serviceResponse->getBody();
                $decoded = json_decode($body, true);
                if ($decoded) {
                    $decoded['route_debug'] = $routeDebug;
                    $res->getBody()->rewind();
                    $res->getBody()->write(json_encode($decoded));
                    return $res
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus($serviceResponse->getStatusCode());
                }
                return $serviceResponse;
            } catch (\InvalidArgumentException $e) {
                return JsonResponder::error($res, $e->getMessage(), 422);
            } catch (\Throwable $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'data'    => $data,
                    'route_debug' => $routeDebug,
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        });
    });
};
