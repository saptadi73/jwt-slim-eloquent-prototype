<?php
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Services\UserService;
use App\Support\JsonResponder;
use App\Support\RequestHelper;
use App\Middlewares\JwtMiddleware;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->group('/users', function (RouteCollectorProxy $users) use ($container) {
        // Update user
        $users->put('/{id}', function (Request $request, Response $response, array $args) use ($container) {
            $id   = $args['id'];
            $data = RequestHelper::getJsonBody($request) ?? ($request->getParsedBody() ?? []);
            $svc = $container->get(UserService::class);
            $user = $svc->update($id, $data);
            if ($user) return JsonResponder::success($response, $user, 'User updated');
            return JsonResponder::error($response, 'User not found', 404);
        });

        // Delete user
        $users->delete('/{id}', function (Request $request, Response $response, array $args) {
            $id      = $args['id'];
            $deleted = UserService::delete($id);
            if ($deleted) return JsonResponder::success($response, [], 'User deleted');
            return JsonResponder::error($response, 'User not found', 404);
        });

        $users->post('/update/role', function (Request $request, Response $response) use ($container) {
            $data = RequestHelper::getJsonBody($request) ?? ($request->getParsedBody() ?? []);
            $svc = $container->get(UserService::class);
            $result = $svc->updateRole((array) $data);
            if ($result) return JsonResponder::success($response, $result, 'User role updated');
            return JsonResponder::error($response, 'User not found', 404);
        });
    })->add(new JwtMiddleware());
};
