<?php

use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Services\WorkOrderService;
use App\Services\OrganisasiService;
use App\Support\RequestHelper;
use App\Support\JsonResponder;
use App\Middlewares\JwtMiddleware;
use App\Models\WorkOrderAcService;
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
                    'data'    => $data ?? null,
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
                    'data'    => $data ?? null,
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
                    'data'    => $data ?? null,
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
                    'data'    => null,
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        });
        
        $wo->get('/wo/list', function (Request $req, Response $res) use ($container) {
            try {
                $svc = $container->get(WorkOrderService::class);
                return $svc->listWorkOrders($res);
            } catch (\Exception $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'data'    => null,
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
                    'data'    => null,
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        });

        $wo->get('/service/{id:[0-9a-fA-F-]{36}}', function (Request $req, Response $res, array $args) use ($container) {
            try {
                $svc = $container->get(WorkOrderService::class);
                return $svc->getWorkOrderServiceById($res, $args['id']);
            } catch (\Exception $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'data'    => null,
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        });

        $wo->get('/penyewaan/{id:[0-9a-fA-F-]{36}}', function (Request $req, Response $res, array $args) use ($container) {
            try {
                $svc = $container->get(WorkOrderService::class);
                return $svc->getWorkOrderPenyewaanById($res, $args['id']);
            } catch (\Exception $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'data'    => null,
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        });

        $wo->get('/penjualan/{id:[0-9a-fA-F-]{36}}', function (Request $req, Response $res, array $args) use ($container) {
            try {
                $svc = $container->get(WorkOrderService::class);
                return $svc->getWorkOrderPenjualanById($res, $args['id']);
            } catch (\Exception $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'data'    => null,
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        });

        $wo->post('/service/update/{id:[0-9a-fA-F-]{36}}', function (Request $req, Response $res, array $args) use ($container) {
            $data = RequestHelper::getJsonBody($req);
            if (!$data) {
                return JsonResponder::error($res, 'Invalid JSON body', 400);
            }
            try {
                $svc = $container->get(WorkOrderService::class);
                return $svc->updateWorkOrderService($res,$data, $args['id'] );
            } catch (\Exception $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'data'    => $data ?? null,
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        })->add(new JwtMiddleware());

        $wo->post('/penyewaan/update/{id:[0-9a-fA-F-]{36}}', function (Request $req, Response $res, array $args) use ($container) {
            $data = RequestHelper::getJsonBody($req);
            if (!$data) {
                return JsonResponder::error($res, 'Invalid JSON body', 400);
            }
            try {
                $svc = $container->get(WorkOrderService::class);
                return $svc->updateWorkOrderPenyewaan($res, $data, $args['id']);
            } catch (\Exception $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'data'    => $data ?? null,
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        })->add(new JwtMiddleware());

        $wo->post('/penjualan/update/{id:[0-9a-fA-F-]{36}}', function (Request $req, Response $res, array $args) use ($container) {
            $data = RequestHelper::getJsonBody($req);
            if (!$data) {
                return JsonResponder::error($res, 'Invalid JSON body', 400);
            }
            try {
                $svc = $container->get(WorkOrderService::class);
                return $svc->updateWorkOrderPenjualan($res, $data, $args['id']);
            } catch (\Exception $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'data'    => $data ?? null,
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        })->add(new JwtMiddleware());

    $wo->post('/service/close/{id}', function (Request $req, Response $res, array $args) use ($container) {
            $file = RequestHelper::pickUploadedFile($req, ['file', 'photo']);
            try {
                $svc = $container->get(WorkOrderService::class);
                return $svc->updateSignatureWorkorderService($res, $file, $args['id']);
            } catch (\Exception $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'data'    => null,
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        });

        $wo->post('/penyewaan/close/{id}', function (Request $req, Response $res, array $args) use ($container) {
            try {
                $file = RequestHelper::pickUploadedFile($req, ['file', 'photo']);
                $svc = $container->get(WorkOrderService::class);
                return $svc->updateSignatureWorkorderPenyewaan($res, $file, $args['id']);
            } catch (\Exception $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'data'    => null,
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        });

        $wo->post('/penjualan/close/{id}', function (Request $req, Response $res, array $args) use ($container) {
            try {
                $file = RequestHelper::pickUploadedFile($req, ['file', 'photo']);
                $svc = $container->get(WorkOrderService::class);
                return $svc->updateSignatureWorkorderPenjualan($res, $file, $args['id']);
            } catch (\Exception $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'data'    => null,
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        });

        $wo->get('/service/customercode/{id}', function (Request $req, Response $res, array $args) use ($container) {
            try {
                $svc = $container->get(WorkOrderService::class);
                return $svc->getWorkoderServiceByCustomerCode($res, $args['id']);
            } catch (\Exception $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'data'    => null,
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        });

        $wo->get('/penyewaan/customercode/{id}', function (Request $req, Response $res, array $args) use ($container) {
            try {
                $svc = $container->get(WorkOrderService::class);
                return $svc->getWorkoderPenyewaanByCustomerCode($res, $args['id']);
            } catch (\Exception $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'data'    => null,
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        });

        $wo->get('/penjualan/customercode/{id}', function (Request $req, Response $res, array $args) use ($container) {
            try {
                $svc = $container->get(WorkOrderService::class);
                return $svc->getWorkoderPenjualanByCustomerCode($res, $args['id']);
            } catch (\Exception $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'data'    => null,
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        });

        $wo->post('/service/sign/{id:[0-9a-fA-F-]{36}}', function (Request $req, Response $res, array $args) use ($container) {
            try {
                $svc = $container->get(WorkOrderService::class);
                return $svc->setLinkSignatureWorkorderService($res, $args['id']);
            } catch (\Exception $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'data'    => $data ?? null,
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        })->add(new JwtMiddleware());

        $wo->post('/penjualan/sign/{id:[0-9a-fA-F-]{36}}', function (Request $req, Response $res, array $args) use ($container) {
            try {
                $svc = $container->get(WorkOrderService::class);
                return $svc->setLinkSignatureWorkorderPenjualan($res, $args['id']);
            } catch (\Exception $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'data'    => $data ?? null,
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        })->add(new JwtMiddleware());

        $wo->post('/penyewaan/sign/{id:[0-9a-fA-F-]{36}}', function (Request $req, Response $res, array $args) use ($container) {
            try {
                $svc = $container->get(WorkOrderService::class);
                return $svc->setLinkSignatureWorkorderPenyewaan($res, $args['id']);
            } catch (\Exception $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'data'    => $data ?? null,
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        })->add(new JwtMiddleware());

    });
};
