<?php
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Services\SaleOrderService;
use App\Services\PurchaseOrderService;
use App\Support\JsonResponder;
use App\Support\RequestHelper;
use App\Middlewares\JwtMiddleware;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->group('/orders', function (RouteCollectorProxy $orders) use ($container) {
        // Create Purchase Order
        $orders->post('/purchase', function (Request $request, Response $response) use ($container) {
            $data = RequestHelper::getJsonBody($request) ?? ($request->getParsedBody() ?? []);
            $svc = $container->get(PurchaseOrderService::class);
            return $svc->createPurchaseOrder($request, $response, (array) $data);
        });

        // Get Purchase Order by ID
        $orders->get('/purchase/{id}', function (Request $request, Response $response, array $args) use ($container) {
            $id = $args['id'];
            $svc = $container->get(PurchaseOrderService::class);
            return $svc->getPurchaseOrder($request, $response, $id);
        });

        // List all Purchase Orders
        $orders->get('/purchase', function (Request $request, Response $response) use ($container) {
            $svc = $container->get(PurchaseOrderService::class);
            return $svc->listPurchaseOrders($response);
        });

        $orders->post('/sale', function (Request $request, Response $response) use ($container) {
            $data = RequestHelper::getJsonBody($request) ?? ($request->getParsedBody() ?? []);
            $svc = $container->get(SaleOrderService::class);
            return $svc->createSaleOrder($response, (array) $data);
        })->add(new JwtMiddleware());

        $orders->get('/sale/{id}', function (Request $request, Response $response, array $args) use ($container) {
            $id = $args['id'];
            $svc = $container->get(SaleOrderService::class);
            return $svc->getSaleOrder($request, $response, $id);
        });

        $orders->get('/sale', function (Request $request, Response $response) use ($container) {
            $svc = $container->get(SaleOrderService::class);
            return $svc->listSaleOrders($response);
        });

        $orders->post('/update/sale/{id}', function (Request $request, Response $response, array $args) use ($container) {
            $id = $args['id'];
            $data = RequestHelper::getJsonBody($request) ?? ($request->getParsedBody() ?? []);
            $svc = $container->get(SaleOrderService::class);
            return $svc->updateSaleOrder($response, $id, (array) $data);
        })->add(new JwtMiddleware());

        $orders->post('/delete/sale/product-lines/{id}', function (Request $request, Response $response, array $args) use ($container) {
            $id = $args['id'];
            $data = RequestHelper::getJsonBody($request) ?? ($request->getParsedBody() ?? []);
            $svc = $container->get(SaleOrderService::class);
            return $svc->deleteProductLine($response, $id);
        })->add(new JwtMiddleware());

        $orders->post('/add/sale/service-lines/{id}', function (Request $request, Response $response, array $args) use ($container) {
            $id = $args['id'];
            $data = RequestHelper::getJsonBody($request) ?? ($request->getParsedBody() ?? []);
            $svc = $container->get(SaleOrderService::class);
            return $svc->addServiceLine($response, $id, (array) $data);
        })->add(new JwtMiddleware());

        $orders->post('/update/purchase/{id}', function (Request $request, Response $response, array $args) use ($container) {
            $id = $args['id'];
            $data = RequestHelper::getJsonBody($request) ?? ($request->getParsedBody() ?? []);
            $svc = $container->get(PurchaseOrderService::class);
            return $svc->updatePurchaseOrder($response, $id, (array) $data);
        })->add(new JwtMiddleware());

        $orders->post('/delete/purchase/{id}', function (Request $request, Response $response, array $args) use ($container) {
            $id = $args['id'];
            $svc = $container->get(PurchaseOrderService::class);
            return $svc->deletePurchaseOrder($response, $id);
        })->add(new JwtMiddleware());

        $orders->post('/delete/purchase/product-lines/{id}', function (Request $request, Response $response, array $args) use ($container) {
            $id = $args['id'];
            $svc = $container->get(PurchaseOrderService::class);
            return $svc->deletePurchaseOrderLine($response, $id);
        })->add(new JwtMiddleware());

        $orders->post('/add/purchase/product-lines/{id}', function (Request $request, Response $response, array $args) use ($container) {
            $id = $args['id'];
            $svc = $container->get(PurchaseOrderService::class);
            $data = RequestHelper::getJsonBody($request) ?? ($request->getParsedBody() ?? []);
            return $svc->addPurchaseOrderLine($response, $id, (array) $data);
        })->add(new JwtMiddleware());
    });
};