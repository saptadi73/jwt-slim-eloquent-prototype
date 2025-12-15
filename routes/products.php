<?php

use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Services\ProductService;
use App\Support\JsonResponder;
use App\Support\RequestHelper;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->group('/products', function (RouteCollectorProxy $product) use ($container) {
        $product->get('', function (Request $req, Response $res) use ($container) {
            try {
                $svc = $container->get(ProductService::class);
                return $svc->listProducts($res);
            } catch (\Throwable $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        });

        $product->get('/{id}', function (Request $req, Response $res, array $args) use ($container) {
            try {
                $svc = $container->get(ProductService::class);
                return $svc->getProduct($res, $args['id']);
            } catch (\Throwable $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        });

        $product->post('', function (Request $req, Response $res) use ($container) {
            try {
                $svc = $container->get(ProductService::class);
                $data = $req->getParsedBody() ?? [];
                $file = RequestHelper::pickUploadedFile($req, ['gambar', 'file', 'photo']);
                return $svc->createProduct($res, $data, $file);
            } catch (\InvalidArgumentException $e) {
                return JsonResponder::error($res, $e->getMessage(), 422);
            } catch (\Throwable $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        });

        $product->put('/{id}', function (Request $req, Response $res, array $args) use ($container) {
            try {
                $svc = $container->get(ProductService::class);
                $data = $req->getParsedBody() ?? [];
                $file = RequestHelper::pickUploadedFile($req, ['gambar', 'file', 'photo']);
                return $svc->updateProduct($res, $args['id'], $data, $file);
            } catch (\InvalidArgumentException $e) {
                return JsonResponder::error($res, $e->getMessage(), 422);
            } catch (\Throwable $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        });

        $product->delete('/{id}', function (Request $req, Response $res, array $args) use ($container) {
            try {
                $svc = $container->get(ProductService::class);
                return $svc->deleteProduct($res, $args['id']);
            } catch (\Throwable $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        });

        // Handler untuk POST ke {id} - Update (multipart/form-data)
        $product->post('/{id}', function (Request $req, Response $res, array $args) use ($container) {
            try {
                $svc = $container->get(ProductService::class);
                $data = $req->getParsedBody() ?? [];
                $file = RequestHelper::pickUploadedFile($req, ['gambar', 'file', 'photo']);
                return $svc->updateProduct($res, $args['id'], $data, $file);
            } catch (\InvalidArgumentException $e) {
                return JsonResponder::error($res, $e->getMessage(), 422);
            } catch (\Throwable $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        });
    });
};
