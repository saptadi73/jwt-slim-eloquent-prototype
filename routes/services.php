<?php
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Services\ServiceService;
use App\Support\RequestHelper;
use App\Middlewares\JwtMiddleware;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->group('/services', function (RouteCollectorProxy $grp) use ($container) {
        // Create
        $grp->post('', function (Request $request, Response $response) use ($container) {
            $data = RequestHelper::getJsonBody($request) ?? ($request->getParsedBody() ?? []);
            $svc = $container->get(ServiceService::class);
            return $svc->create($response, (array) $data);
        })->add(new JwtMiddleware());

        // List
        $grp->get('', function (Request $request, Response $response) use ($container) {
            $svc = $container->get(ServiceService::class);
            return $svc->list($response);
        });

        // Get by ID
        $grp->get('/{id}', function (Request $request, Response $response, array $args) use ($container) {
            $id = $args['id'];
            $svc = $container->get(ServiceService::class);
            return $svc->get($response, $id);
        });

        // Update
        $grp->post('/update/{id}', function (Request $request, Response $response, array $args) use ($container) {
            $id = $args['id'];
            $data = RequestHelper::getJsonBody($request) ?? ($request->getParsedBody() ?? []);
            $svc = $container->get(ServiceService::class);
            return $svc->update($response, $id, (array) $data);
        })->add(new JwtMiddleware());

        // Delete
        $grp->post('/delete/{id}', function (Request $request, Response $response, array $args) use ($container) {
            $id = $args['id'];
            $svc = $container->get(ServiceService::class);
            return $svc->delete($response, $id);
        })->add(new JwtMiddleware());
    });
};
