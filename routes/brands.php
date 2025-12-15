<?php

use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Services\BrandService;
use App\Support\JsonResponder;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->group('/brands', function (RouteCollectorProxy $brand) use ($container) {
        $brand->get('', function (Request $req, Response $res) use ($container) {
            try {
                $svc = $container->get(BrandService::class);
                return $svc->listBrands($res);
            } catch (\Throwable $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        });

        $brand->get('/{id}', function (Request $req, Response $res, array $args) use ($container) {
            try {
                $svc = $container->get(BrandService::class);
                return $svc->getBrand($res, $args['id']);
            } catch (\Throwable $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        });

        $brand->post('', function (Request $req, Response $res) use ($container) {
            try {
                $svc = $container->get(BrandService::class);
                $data = $req->getParsedBody() ?? [];
                return $svc->createBrand($res, $data);
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

        $brand->put('/{id}', function (Request $req, Response $res, array $args) use ($container) {
            try {
                $svc = $container->get(BrandService::class);
                $data = $req->getParsedBody() ?? [];
                return $svc->updateBrand($res, $args['id'], $data);
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

        $brand->post('/{id}', function (Request $req, Response $res, array $args) use ($container) {
            try {
                $svc = $container->get(BrandService::class);
                $data = $req->getParsedBody() ?? [];
                return $svc->updateBrand($res, $args['id'], $data);
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

        $brand->delete('/{id}', function (Request $req, Response $res, array $args) use ($container) {
            try {
                $svc = $container->get(BrandService::class);
                return $svc->deleteBrand($res, $args['id']);
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
