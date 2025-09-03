<?php

use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Services\CustomerService;
use App\Support\JsonResponder;
use App\Support\RequestHelper;
use App\Middlewares\JwtMiddleware;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->group('/customers', function (RouteCollectorProxy $cust) use ($container) {

        // Create customer (multipart/json)
        $cust->post('/new', function (Request $request, Response $response) use ($container) {
            /** @var CustomerService $svc */
            $svc  = $container->get(CustomerService::class);
            $data = RequestHelper::getJsonBody($request) ?? ($request->getParsedBody() ?? []);
            $file = RequestHelper::pickUploadedFile($request, ['file', 'photo']);

            try {
                // return JsonResponder::error($response, $data, 501);
                return $svc->createCustomerAndAsset($request, $response, $data, $file);
            } catch (\InvalidArgumentException $e) {
                return JsonResponder::error($response, $e->getMessage(), 422);
            } catch (\Throwable $e) {
                // return JsonResponder::error($response, 'Internal server error', 500);
                return JsonResponder::error($response, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'data'    => $data,
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                    // hati-hati: trace bisa panjang
                    // 'trace' => $e->getTrace()
                ], 500);
            }
        });

        $cust->get('/assets/{id:[0-9a-fA-F-]{36}}', function (Request $request, Response $response, array $args) use ($container) {
            /** @var CustomerService $svc */

            try {
                $svc = $container->get(CustomerService::class);
                return $svc->getCustomerWithAsset($response, $args['id']);
            } catch (\Throwable $th) {
                //throw $th;
                return JsonResponder::error($response, 'Failed to retrieve Customer with Asset: ' . $th->getMessage(), 500);
            }
        });

        $cust->get('/all', function (Request $request, Response $response) use ($container) {

            try {
                $svc = $container->get(CustomerService::class);
                return $svc->getAllCustomers($response);
            } catch (\Throwable $th) {
                //throw $th;
                return JsonResponder::error($response, 'Failed to retrieve customers: ' . $th->getMessage(), 500);
            }
        });
        $cust->get('/brand', function (Request $request, Response $response) use ($container) {

            try {
                $svc = $container->get(CustomerService::class);
                return $svc->listBrand($response);
            } catch (\Throwable $th) {
                //throw $th;
                return JsonResponder::error($response, 'Failed to retrieve brands: ' . $th->getMessage(), 500);
            }
        });
        $cust->get('/brand/delete/{id:[0-9a-fA-F-]{36}}', function (Request $request, Response $response, array $args) use ($container) {
            try {
                $svc = $container->get(CustomerService::class);
                return $svc->deleteBrand($response, $args['id']);
            } catch (\Throwable $th) {
                //throw $th;
                return JsonResponder::error($response, 'Failed to delete brand: ' . $th->getMessage(), 500);
            }
        });

        $cust->get('/tipe/delete/{id:[0-9a-fA-F-]{36}}', function (Request $request, Response $response, array $args) use ($container) {
            try {
                $svc = $container->get(CustomerService::class);
                return $svc->deleteTipe($response, $args['id']);
            } catch (\Throwable $th) {
                //throw $th;
                return JsonResponder::error($response, 'Failed to delete type: ' . $th->getMessage(), 500);
            }
        });

        $cust->get('/tipe', function (Request $request, Response $response) use ($container) {

            try {
                $svc = $container->get(CustomerService::class);
                return $svc->listTipe($response);
            } catch (\Throwable $th) {
                //throw $th;
                return JsonResponder::error($response, 'Failed to retrieve types: ' . $th->getMessage(), 500);
            }
        });

        $cust->post('/tipe/update/{id:[0-9a-fA-F-]{36}}', function (Request $request, Response $response, array $args) use ($container) {
            $data = RequestHelper::getJsonBody($request) ?? ($request->getParsedBody() ?? []);
            try {
                $svc = $container->get(CustomerService::class);
                return $svc->updateTipe($response, $args['id'], $data);
            } catch (\Throwable $th) {
                //throw $th;
                return JsonResponder::error($response, 'Failed to update type: ' . $th->getMessage(), 500);
            }
        });

        $cust->post('/brand/update/{id:[0-9a-fA-F-]{36}}', function (Request $request, Response $response, array $args) use ($container) {
            $data = RequestHelper::getJsonBody($request) ?? ($request->getParsedBody() ?? []);
            try {
                $svc = $container->get(CustomerService::class);
                return $svc->updateBrand($response, $args['id'], $data);
            } catch (\Throwable $th) {
                //throw $th;
                return JsonResponder::error($response, 'Failed to update brand: ' . $th->getMessage(), 500);
            }
        });

        $cust->post('/assets/new', function (Request $request, Response $response) use ($container) {
            /** @var CustomerService $svc */
            $svc  = $container->get(CustomerService::class);
            $data = RequestHelper::getJsonBody($request) ?? ($request->getParsedBody() ?? []);
            $file = RequestHelper::pickUploadedFile($request, ['file', 'photo']);

            try {
                return $svc->createCustomerAsset($request, $response, $data, $file);

            } catch (\InvalidArgumentException $e) {
                return JsonResponder::error($response, $e->getMessage(), 422);
            } catch (\Throwable $e) {
                return JsonResponder::error($response, [
                    'message' => $e->getMessage(),
                    'type'    => get_class($e),
                    'data'    => $data,
                    'file'    => $e->getFile() . ':' . $e->getLine(),
                ], 500);
            }
        });

        // Tambah endpoints lain: index/show/update/delete kalau diperlukan
        // $cust->get('',  [CustomerController::class, 'index']);
        // $cust->get('/{id}', [CustomerController::class, 'show']);
        // dst...
    })->add(new JwtMiddleware());
};
