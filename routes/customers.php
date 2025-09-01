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
        $cust->post('', function (Request $request, Response $response) use ($container) {
            /** @var CustomerService $svc */
            $svc  = $container->get(CustomerService::class);
            $data = RequestHelper::getJsonBody($request) ?? ($request->getParsedBody() ?? []);
            $file = RequestHelper::pickUploadedFile($request, ['file', 'photo']);

            try {
                return $svc->createCustomerAndAsset($request, $response, $data, $file);
            } catch (\InvalidArgumentException $e) {
                return JsonResponder::error($response, $e->getMessage(), 422);
            } catch (\Throwable $e) {
                return JsonResponder::error($response, 'Internal server error', 500);
            }
        });

        // Tambah endpoints lain: index/show/update/delete kalau diperlukan
        // $cust->get('',  [CustomerController::class, 'index']);
        // $cust->get('/{id}', [CustomerController::class, 'show']);
        // dst...
    })->add(new JwtMiddleware());
};
