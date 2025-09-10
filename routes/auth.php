<?php
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Services\AuthService;
use App\Support\JsonResponder;
use App\Support\RequestHelper;
use App\Middlewares\JwtMiddleware;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

return function (App $app) {

    $app->get('/ping', function (Request $request, Response $response) {
        $response->getBody()->write(json_encode(['message' => 'pong']));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->group('/auth', function (RouteCollectorProxy $auth) {
        // Register
        $auth->post('/register', function (Request $request, Response $response) {
            $data = RequestHelper::getJsonBody($request) ?? [];
            if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
                return JsonResponder::error($response, 'Invalid input', 400);
            }

            $role = \App\Models\Role::where('name', 'user')->first();
            if (!$role) {
                return JsonResponder::error($response, 'Role not found', 404);
            }

            $user = AuthService::register($data['name'], $data['email'], $data['password'], $role->id);
            return JsonResponder::success($response, $user, 'User registered');
        });

        // Login (tahan banting: JSON / form-urlencoded)
        $auth->post('/login', function (Request $request, Response $response) {
            $data = RequestHelper::getJsonBody($request) ?? [];
            if (!$data) {
                $parsed = $request->getParsedBody();
                if (is_array($parsed)) $data = $parsed;
            }
            $email = $data['email']    ?? null;
            $pass  = $data['password'] ?? null;
            
            if (!$email || !$pass) {
                return JsonResponder::error($response, 'Invalid input', 400);
            }

            try {
                $result = AuthService::login($email, $pass);
                if (!empty($result['success'])) {
                    return JsonResponder::success($response, ['token' => $result['token'],'role' => $result['user']['roles'][0]['name']], 'Login success');
                }
                return JsonResponder::error($response, $result['message'] ?? 'Unauthorized', 401);
            } catch (\Throwable $e) {
                return JsonResponder::error($response, 'Login error: '.$e->getMessage(), 500);
            }
        });

        // Logout (JWT)
        $auth->post('/logout', function (Request $request, Response $response) {
            $jwt = $request->getAttribute('jwt');
            if (!$jwt) {
                return JsonResponder::error($response, 'Invalid or missing token', 401);
            }
            return JsonResponder::success($response, [], 'Logout success');
        })->add(new JwtMiddleware());

        // Profile (JWT)
        $auth->get('/profile', function (Request $request, Response $response) {
            $jwt  = $request->getAttribute('jwt');
            $user = \App\Models\User::find($jwt['sub']);
            if (!$user) {
                return JsonResponder::error($response, 'User not found', 404);
            }
            return JsonResponder::success($response, [
                'user' => $user,
                'role' => $user->role,
            ], 'Profile data');
        })->add(new JwtMiddleware());
    });
};
