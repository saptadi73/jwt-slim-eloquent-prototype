<?php

use App\Services\PositionService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function ($app) {
    $positionService = new PositionService();

    // Get all positions
    $app->get('/api/positions', function (Request $request, Response $response) use ($positionService) {
        $params = $request->getQueryParams();
        return $positionService->index($response, $params);
    });

    // Get position by ID
    $app->get('/api/positions/{id}', function (Request $request, Response $response, array $args) use ($positionService) {
        return $positionService->show($response, $args['id']);
    });

    // Create new position
    $app->post('/api/positions', function (Request $request, Response $response) use ($positionService) {
        $data = $request->getParsedBody();
        return $positionService->store($response, $data);
    });

    // Update position
    $app->put('/api/positions/{id}', function (Request $request, Response $response, array $args) use ($positionService) {
        $data = $request->getParsedBody();
        return $positionService->update($response, $args['id'], $data);
    });

    // Delete position
    $app->delete('/api/positions/{id}', function (Request $request, Response $response, array $args) use ($positionService) {
        return $positionService->destroy($response, $args['id']);
    });
};
