<?php

use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Services\ExpenseService;
use App\Support\JsonResponder;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->group('/expenses', function (RouteCollectorProxy $expenses) use ($container) {
        // List all expenses
        $expenses->get('', function (Request $req, Response $res) use ($container) {
            try {
                $svc = $container->get(ExpenseService::class);
                return $svc->listExpenses($res);
            } catch (\Throwable $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        });

        // Get single expense
        $expenses->get('/{id}', function (Request $req, Response $res, array $args) use ($container) {
            try {
                $svc = $container->get(ExpenseService::class);
                return $svc->getExpense($res, $args['id']);
            } catch (\Throwable $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        });

        // Create new expense
        $expenses->post('', function (Request $req, Response $res) use ($container) {
            try {
                $svc = $container->get(ExpenseService::class);
                $data = $req->getParsedBody() ?? [];
                $files = $req->getUploadedFiles();
                $fileBukti = $files['bukti'] ?? null;
                return $svc->createExpense($res, $data, $fileBukti);
            } catch (\Throwable $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        });

        // Update expense
        $expenses->put('/{id}', function (Request $req, Response $res, array $args) use ($container) {
            try {
                $svc = $container->get(ExpenseService::class);
                $data = $req->getParsedBody() ?? [];
                $files = $req->getUploadedFiles();
                $fileBukti = $files['bukti'] ?? null;
                return $svc->updateExpense($res, $args['id'], $data, $fileBukti);
            } catch (\Throwable $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        });

        // Delete expense
        $expenses->delete('/{id}', function (Request $req, Response $res, array $args) use ($container) {
            try {
                $svc = $container->get(ExpenseService::class);
                return $svc->deleteExpense($res, $args['id']);
            } catch (\Throwable $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        });
    });

    // Biaya Workorder routes
    $app->group('/biaya-workorder', function (RouteCollectorProxy $biaya) use ($container) {
        // List all biaya workorder
        $biaya->get('', function (Request $req, Response $res) use ($container) {
            try {
                $svc = $container->get(ExpenseService::class);
                return $svc->listBiayaWorkorder($res);
            } catch (\Throwable $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        });

        // Get single biaya workorder
        $biaya->get('/{id}', function (Request $req, Response $res, array $args) use ($container) {
            try {
                $svc = $container->get(ExpenseService::class);
                return $svc->getBiayaWorkorder($res, $args['id']);
            } catch (\Throwable $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        });

        // Create new biaya workorder
        $biaya->post('', function (Request $req, Response $res) use ($container) {
            try {
                $svc = $container->get(ExpenseService::class);
                $data = $req->getParsedBody() ?? [];
                $files = $req->getUploadedFiles();
                $fileBukti = $files['bukti'] ?? null;
                return $svc->createBiayaWorkorder($res, $data, $fileBukti);
            } catch (\Throwable $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        });

        // Update biaya workorder
        $biaya->put('/{id}', function (Request $req, Response $res, array $args) use ($container) {
            try {
                $svc = $container->get(ExpenseService::class);
                $data = $req->getParsedBody() ?? [];
                $files = $req->getUploadedFiles();
                $fileBukti = $files['bukti'] ?? null;
                return $svc->updateBiayaWorkorder($res, $args['id'], $data, $fileBukti);
            } catch (\Throwable $e) {
                return JsonResponder::error($res, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        });

        // Delete biaya workorder
        $biaya->delete('/{id}', function (Request $req, Response $res, array $args) use ($container) {
            try {
                $svc = $container->get(ExpenseService::class);
                return $svc->deleteBiayaWorkorder($res, $args['id']);
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
