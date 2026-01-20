<?php

use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Services\KeluhanService;
use App\Support\JsonResponder;
use App\Middlewares\JwtMiddleware;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->group('/keluhan', function (RouteCollectorProxy $keluhan) use ($container) {
        // List all keluhan
        $keluhan->get('', function (Request $req, Response $res) use ($container) {
            try {
                $svc = $container->get(KeluhanService::class);
                return $svc->listKeluhan($res);
            } catch (\Throwable $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        });

        // Get single keluhan
        $keluhan->get('/{id:[0-9a-fA-F-]{36}}', function (Request $req, Response $res, array $args) use ($container) {
            try {
                $svc = $container->get(KeluhanService::class);
                return $svc->getKeluhan($res, $args['id']);
            } catch (\Throwable $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        });

        // Create keluhan
        $keluhan->post('', function (Request $req, Response $res) use ($container) {
            try {
                $svc = $container->get(KeluhanService::class);
                $data = $req->getParsedBody() ?? [];
                return $svc->createKeluhan($res, $data);
            } catch (\InvalidArgumentException $e) {
                return JsonResponder::error($res, $e->getMessage(), 422);
            } catch (\Throwable $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        })->add(new JwtMiddleware());

        // Update keluhan (PUT)
        $keluhan->put('/{id:[0-9a-fA-F-]{36}}', function (Request $req, Response $res, array $args) use ($container) {
            try {
                $svc = $container->get(KeluhanService::class);
                $data = $req->getParsedBody() ?? [];
                return $svc->updateKeluhan($res, $args['id'], $data);
            } catch (\InvalidArgumentException $e) {
                return JsonResponder::error($res, $e->getMessage(), 422);
            } catch (\Throwable $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        })->add(new JwtMiddleware());

        // Update keluhan (POST - fallback for browsers/tools that don't support PUT)
        $keluhan->post('/{id:[0-9a-fA-F-]{36}}', function (Request $req, Response $res, array $args) use ($container) {
            try {
                $svc = $container->get(KeluhanService::class);
                $data = $req->getParsedBody() ?? [];
                return $svc->updateKeluhan($res, $args['id'], $data);
            } catch (\InvalidArgumentException $e) {
                return JsonResponder::error($res, $e->getMessage(), 422);
            } catch (\Throwable $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        })->add(new JwtMiddleware());

        // Delete keluhan
        $keluhan->delete('/{id:[0-9a-fA-F-]{36}}', function (Request $req, Response $res, array $args) use ($container) {
            try {
                $svc = $container->get(KeluhanService::class);
                return $svc->deleteKeluhan($res, $args['id']);
            } catch (\Throwable $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        })->add(new JwtMiddleware());
    });
};
