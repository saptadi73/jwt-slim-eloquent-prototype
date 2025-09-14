<?php

use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Services\WorkOrderService;
use App\Services\OrganisasiService;
use App\Support\RequestHelper;
use App\Support\JsonResponder;
use App\Middlewares\JwtMiddleware;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->group('/wo', function (RouteCollectorProxy $wo) use ($container) {
        $wo->get('/ping', function (Request $req, Response $res) use ($container) {
            try {
                $svc = $container->get(\App\Services\WorkOrderService::class);
                return JsonResponder::success($res, 'ok', 200);
            } catch (\Throwable $e) {
                return JsonResponder::error($res, 'Container error: ' . $e->getMessage(), 500);
            }
        });
        // Create brand
        $wo->post('/pemeliharaan/create', function (Request $req, Response $res) use ($container) {
            $data = RequestHelper::getJsonBody($req);
            if (!$data) {
                return JsonResponder::error($res, 'Invalid JSON body', 400);
            }
            try {
                $svc = $container->get(WorkOrderService::class);
                return $svc->createWorkorderPemeliharaan($res, $data);
                
            } catch (\Exception $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'data'    => $workorder ?? null,
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        })->add(new JwtMiddleware());

        $wo->post('/penjualan/create', function (Request $req, Response $res) use ($container) {
            $data = RequestHelper::getJsonBody($req);
            if (!$data) {
                return JsonResponder::error($res, 'Invalid JSON body', 400);
            }
            try {
                $svc = $container->get(WorkOrderService::class);
                return $svc->createWorkOrderPenjualan($res, $data);
            } catch (\Exception $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'data'    => $workorder ?? null,
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        })->add(new JwtMiddleware());

        $wo->post('/penyewaan/create', function (Request $req, Response $res) use ($container) {
            $data = RequestHelper::getJsonBody($req);
            if (!$data) {
                return JsonResponder::error($res, 'Invalid JSON body', 400);
            }
            try {
                $svc = $container->get(WorkOrderService::class);
                return $svc->createWorkorderPenyewaan($res, $data);
            } catch (\Exception $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'data'    => $workorder ?? null,
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        })->add(new JwtMiddleware());

        $wo->get('/{id}', function (Request $req, Response $res, array $args) use ($container) {
            try {
                $svc = $container->get(WorkOrderService::class);
                return $svc->getWorkOrderById($res, $args);
            } catch (\Exception $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'data'    => $workorder ?? null,
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        });

        $wo->get('/pegawai/list', function (Request $req, Response $res) use ($container) {
            try {
                $svc = $container->get(WorkOrderService::class);
                return $svc->getPegawaiList($res);
            } catch (\Exception $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'data'    => $workorder ?? null,
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        });
    });
};
