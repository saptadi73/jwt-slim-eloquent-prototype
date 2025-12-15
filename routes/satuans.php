<?php

use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Services\SatuanService;
use App\Support\JsonResponder;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->group('/satuans', function (RouteCollectorProxy $satuan) use ($container) {
        $satuan->get('', function (Request $req, Response $res) use ($container) {
            try {
                $svc = $container->get(SatuanService::class);
                return $svc->listSatuans($res);
            } catch (\Throwable $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        });

        $satuan->get('/{id}', function (Request $req, Response $res, array $args) use ($container) {
            try {
                $svc = $container->get(SatuanService::class);
                return $svc->getSatuan($res, $args['id']);
            } catch (\Throwable $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        });

        $satuan->post('', function (Request $req, Response $res) use ($container) {
            try {
                $svc = $container->get(SatuanService::class);
                $data = $req->getParsedBody() ?? [];
                return $svc->createSatuan($res, $data);
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

        $satuan->put('/{id}', function (Request $req, Response $res, array $args) use ($container) {
            try {
                $svc = $container->get(SatuanService::class);
                $data = $req->getParsedBody() ?? [];
                return $svc->updateSatuan($res, $args['id'], $data);
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

        $satuan->post('/{id}', function (Request $req, Response $res, array $args) use ($container) {
            try {
                $svc = $container->get(SatuanService::class);
                $data = $req->getParsedBody() ?? [];
                return $svc->updateSatuan($res, $args['id'], $data);
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

        $satuan->delete('/{id}', function (Request $req, Response $res, array $args) use ($container) {
            try {
                $svc = $container->get(SatuanService::class);
                return $svc->deleteSatuan($res, $args['id']);
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
