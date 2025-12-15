<?php

use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Services\KategoriService;
use App\Support\JsonResponder;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->group('/kategoris', function (RouteCollectorProxy $kategori) use ($container) {
        $kategori->get('', function (Request $req, Response $res) use ($container) {
            try {
                $svc = $container->get(KategoriService::class);
                return $svc->listKategoris($res);
            } catch (\Throwable $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        });

        $kategori->get('/{id}', function (Request $req, Response $res, array $args) use ($container) {
            try {
                $svc = $container->get(KategoriService::class);
                return $svc->getKategori($res, $args['id']);
            } catch (\Throwable $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        });

        $kategori->post('', function (Request $req, Response $res) use ($container) {
            try {
                $svc = $container->get(KategoriService::class);
                $data = $req->getParsedBody() ?? [];
                return $svc->createKategori($res, $data);
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

        $kategori->put('/{id}', function (Request $req, Response $res, array $args) use ($container) {
            try {
                $svc = $container->get(KategoriService::class);
                $data = $req->getParsedBody() ?? [];
                return $svc->updateKategori($res, $args['id'], $data);
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

        $kategori->post('/{id}', function (Request $req, Response $res, array $args) use ($container) {
            try {
                $svc = $container->get(KategoriService::class);
                $data = $req->getParsedBody() ?? [];
                return $svc->updateKategori($res, $args['id'], $data);
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

        $kategori->delete('/{id}', function (Request $req, Response $res, array $args) use ($container) {
            try {
                $svc = $container->get(KategoriService::class);
                return $svc->deleteKategori($res, $args['id']);
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
