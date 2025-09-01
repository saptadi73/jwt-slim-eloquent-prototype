<?php
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Services\WorkOrderService;
use App\Support\RequestHelper;
use App\Middlewares\JwtMiddleware;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->group('/wo', function (RouteCollectorProxy $wo) use ($container) {
        // Create brand
        $wo->post('/brand', function (Request $request, Response $response) use ($container) {
            $data = RequestHelper::getJsonBody($request) ?? ($request->getParsedBody() ?? []);
            $svc  = $container->get(WorkOrderService::class);
            return $svc->createBrand($response, $request, $data);
        });

        // List tipe
        $wo->get('/tipe', function (Request $request, Response $response) use ($container) {
            $svc = $container->get(WorkOrderService::class);
            return $svc->getAllTipe($response);
        });

        // List brand
        $wo->get('/brand', function (Request $request, Response $response) use ($container) {
            $svc = $container->get(WorkOrderService::class);
            return $svc->getAllBrand($response);
        });

        // Tambahkan endpoint lain terkait WorkOrder di sini…
        // $wo->get('/jenisworkorder', ...)
    })->add(new JwtMiddleware());
};
