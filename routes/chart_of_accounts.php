<?php

use Slim\App;
use App\Services\ChartOfAccountService;
use Slim\Routing\RouteCollectorProxy;
use App\Support\JsonResponder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Middlewares\JwtMiddleware;


return function (App $app) {
    $container = $app->getContainer();

    $app->group('/chart-of-accounts', function (RouteCollectorProxy $chartOfAccounts) use ($container) {
        // List all chart of accounts
        $chartOfAccounts->get('', function (Request $request, Response $response) use ($container) {
            $svc = $container->get(ChartOfAccountService::class);
            return $svc->getAll($response);
        });

        // List expense type chart of accounts
        $chartOfAccounts->get('/expenses', function (Request $request, Response $response) use ($container) {
            $svc = $container->get(ChartOfAccountService::class);
            return $svc->getExpenses($response);
        });

        // Get chart of account by ID
        $chartOfAccounts->get('/{id}', function (Request $request, Response $response, array $args) use ($container) {
            $id = $args['id'];
            $svc = $container->get(ChartOfAccountService::class);
            return $svc->getById($response, $id);
        });

        // Create new chart of account
        $chartOfAccounts->post('', function (Request $request, Response $response) use ($container) {
            $data = json_decode($request->getBody()->getContents(), true) ?? [];
            $svc = $container->get(ChartOfAccountService::class);
            return $svc->create($response, (array) $data);
        })->add(new JwtMiddleware());

        // Update chart of account
        $chartOfAccounts->put('/{id}', function (Request $request, Response $response, array $args) use ($container) {
            $id = $args['id'];
            $data = json_decode($request->getBody()->getContents(), true) ?? [];
            $svc = $container->get(ChartOfAccountService::class);
            return $svc->update($response, $id, (array) $data);
        })->add(new JwtMiddleware());

        $chartOfAccounts->delete('/{id}', function (Request $request, Response $response, array $args) use ($container) {
            $id = $args['id'];
            $svc = $container->get(ChartOfAccountService::class);
            return $svc->delete($response, $id);
        })->add(new JwtMiddleware());
    });
};
