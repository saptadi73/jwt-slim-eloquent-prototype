<?php
// src/routes/web.php
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Services\AuthService;
use App\Services\UserService;
use App\Services\CustomerService;
use App\Services\CheckListService;
use App\Support\JsonResponder;
use App\Support\RequestHelper;
use App\Middlewares\JwtMiddleware;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

return function (App $app) {
    $container = $app->getContainer(); // pastikan pakai createFromContainer di bootstrap

    // Home
    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write("Hello Slim 4 + Eloquent ORM!");
        return $response;
    });

    /**
     * AUTH GROUP
     * POST /auth/register
     * POST /auth/login
     * POST /auth/logout (JWT)
     * GET  /auth/profile (JWT)
     */
    $app->group('/auth', function (RouteCollectorProxy $auth) {
        // Register
        $auth->post('/register', function (Request $request, Response $response) {
            $data = RequestHelper::getJsonBody($request);
            if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
                return JsonResponder::error($response, 'Invalid input', 400);
            }

            $role = \App\Models\Role::where('name', 'User')->first();
            if (!$role) {
                return JsonResponder::error($response, 'Role not found', 404);
            }

            $user = AuthService::register($data['name'], $data['email'], $data['password'], $role->id);
            return JsonResponder::success($response, $user, 'User registered');
        });

        // Login
        $auth->post('/login', function (Request $request, Response $response) {
            $data = RequestHelper::getJsonBody($request);
            if (empty($data['email']) || empty($data['password'])) {
                return JsonResponder::error($response, 'Invalid input', 400);
            }
            $result = AuthService::login($data['email'], $data['password']);
            if ($result['success']) {
                return JsonResponder::success($response, ['token' => $result['token']], 'Login success');
            }
            return JsonResponder::error($response, $result['message'], 401);
        });

        // Logout (JWT protected)
        $auth->post('/logout', function (Request $request, Response $response) {
            $jwt = $request->getAttribute('jwt');
            if (!$jwt) {
                return JsonResponder::error($response, 'Invalid or missing token', 401);
            }
            return JsonResponder::success($response, [], 'Logout success');
        })->add(new JwtMiddleware());

        // Profile (JWT protected)
        $auth->get('/profile', function (Request $request, Response $response) {
            $jwt  = $request->getAttribute('jwt');
            $user = \App\Models\User::find($jwt['sub']);
            if (!$user) {
                return JsonResponder::error($response, 'User not found', 404);
            }
            $role = $user->role;
            return JsonResponder::success($response, ['user' => $user, 'role' => $role], 'Profile data');
        })->add(new JwtMiddleware());
    });

    /**
     * USERS GROUP
     * PUT  /users/{id}
     * DELETE /users/{id}
     */
    $app->group('/users', function (RouteCollectorProxy $users) {
        // Update user
        $users->put('/{id}', function (Request $request, Response $response, array $args) {
            $id   = $args['id'];
            $data = RequestHelper::getJsonBody($request);
            $user = UserService::update($id, $data);
            if ($user) {
                return JsonResponder::success($response, $user, 'User updated');
            }
            return JsonResponder::error($response, 'User not found', 404);
        });

        // Delete user
        $users->delete('/{id}', function (Request $request, Response $response, array $args) {
            $id      = $args['id'];
            $deleted = UserService::delete($id);
            if ($deleted) {
                return JsonResponder::success($response, [], 'User deleted');
            }
            return JsonResponder::error($response, 'User not found', 404);
        });
    })->add(new JwtMiddleware()); // Proteksi seluruh grup /users

    /**
     * CHECKLISTS GROUP (contoh utilitas)
     * GET /checklists/seed1
     * GET /checklists/seed2
     */
    $app->group('/checklists', function (RouteCollectorProxy $check) {
        $check->get('/seed1', function (Request $request, Response $response) {
            $payload = CheckListService::isiTableTeknisiServiceAC();
            return JsonResponder::success($response, $payload, 'Checklist retrieved');
        });

        $check->get('/seed2', function (Request $request, Response $response) {
            $payload = CheckListService::isiTableTeknisiServiceAC2();
            return JsonResponder::success($response, $payload, 'Checklist retrieved');
        });
    });

    /**
     * CUSTOMERS GROUP
     * POST /customers
     * (tambahkan rute lain: GET /customers, GET /customers/{id}, dll)
     */
    $app->group('/customers', function (RouteCollectorProxy $cust) use ($container) {

        // Create (multipart/json)
        $cust->post('/new', function (Request $request, Response $response) use ($container) {
            /** @var CustomerService $svc */
            $svc  = $container->get(CustomerService::class); // pastikan service ini ada di container
            $data = RequestHelper::getJsonBody($request);
            $file = RequestHelper::pickUploadedFile($request, ['file', 'photo']);

            try {
                return $svc->createCustomerAndAsset($request, $response, $data, $file);
            } catch (\InvalidArgumentException $e) {
                return JsonResponder::error($response, $e->getMessage(), 422);
            } catch (\Throwable $e) {
                return JsonResponder::error($response, 'Internal server error', 500);
            }
        });

        // Contoh lain (opsional):
        // $cust->get('',  [CustomerController::class, 'index']);
        // $cust->get('/{id}', [CustomerController::class, 'show']);
    })->add(new JwtMiddleware()); // misal semua endpoint customer butuh JWT
};
