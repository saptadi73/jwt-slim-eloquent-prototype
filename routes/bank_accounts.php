<?php

use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\BankAccountService;

return function (App $app) {
    $container = $app->getContainer();

    $app->group('/bank-accounts', function (RouteCollectorProxy $bankAccounts) use ($container) {
        $bankAccounts->get('', function (Request $request, Response $response) use ($container) {
            $params = $request->getQueryParams();

            $categories = $params['categories'] ?? null;
            if (is_string($categories)) {
                $categories = array_filter(array_map('trim', explode(',', $categories)));
            }

            $filters = [
                'categories' => $categories,
                'is_active' => $params['is_active'] ?? null,
                'search' => $params['search'] ?? null,
            ];

            $svc = $container->get(BankAccountService::class);
            return $svc->list($response, $filters);
        });
    });
};
