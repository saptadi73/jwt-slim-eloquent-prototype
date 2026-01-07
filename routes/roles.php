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

    // Role Management Endpoints
    $app->group('/roles', function (RouteCollectorProxy $roleGroup) use ($container) {
        
        // Get all available roles
        $roleGroup->get('', function (Request $request, Response $response) {
            $roles = UserService::getAllRoles();
            return JsonResponder::success($response, $roles, 'Roles retrieved');
        });
        
        // Get all users with their roles
        $roleGroup->get('/users', function (Request $request, Response $response) {
            $users = UserService::getAllWithRoles();
            return JsonResponder::success($response, $users, 'Users retrieved with roles');
        });

        // Assign multiple roles to a user (replace existing)
        $roleGroup->post('/users/{userId}/roles', function (Request $request, Response $response, array $args) {
            $userId = $args['userId'];
            $data = RequestHelper::getJsonBody($request) ?? ($request->getParsedBody() ?? []);
            $roleIds = $data['role_ids'] ?? [];

            if (empty($roleIds)) {
                return JsonResponder::error($response, 'role_ids is required', 400);
            }

            $result = UserService::assignRoles($userId, $roleIds);
            if ($result['success']) {
                return JsonResponder::success($response, $result['user'], $result['message']);
            }
            return JsonResponder::error($response, $result['message'], 404);
        });

        // Add single role to user (without removing existing)
        $roleGroup->post('/users/{userId}/roles/{roleId}', function (Request $request, Response $response, array $args) {
            $userId = $args['userId'];
            $roleId = $args['roleId'];

            $result = UserService::addRole($userId, $roleId);
            if ($result['success']) {
                return JsonResponder::success($response, $result['user'], $result['message']);
            }
            return JsonResponder::error($response, $result['message'], 404);
        });

        // Remove single role from user
        $roleGroup->delete('/users/{userId}/roles/{roleId}', function (Request $request, Response $response, array $args) {
            $userId = $args['userId'];
            $roleId = $args['roleId'];

            $result = UserService::removeRole($userId, $roleId);
            if ($result['success']) {
                return JsonResponder::success($response, $result['user'], $result['message']);
            }
            return JsonResponder::error($response, $result['message'], 404);
        });

    })->add(new JwtMiddleware());
};
