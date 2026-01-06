<?php

use App\Services\DepartmentService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function ($app) {
    // Debug route
    $app->get('/api/departments/debug', function (Request $request, Response $response) {
        $data = ['message' => 'Departments route is loaded!'];
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    });

    try {
        $departmentService = new DepartmentService();
    } catch (\Throwable $e) {
        error_log('Error instantiating DepartmentService: ' . $e->getMessage());
        return;
    }

    // Get all departments with employee count (summary)
    $app->get('/api/departments/summary/count', function (Request $request, Response $response) use ($departmentService) {
        return $departmentService->getDepartmentsWithCount($response);
    });

    // Get all departments
    $app->get('/api/departments', function (Request $request, Response $response) use ($departmentService) {
        $params = $request->getQueryParams();
        return $departmentService->index($response, $params);
    });

    // Get department by ID
    $app->get('/api/departments/{id}', function (Request $request, Response $response, array $args) use ($departmentService) {
        return $departmentService->show($response, $args['id']);
    });

    // Create new department
    $app->post('/api/departments', function (Request $request, Response $response) use ($departmentService) {
        $data = $request->getParsedBody();
        return $departmentService->store($response, $data);
    });

    // Update department
    $app->put('/api/departments/{id}', function (Request $request, Response $response, array $args) use ($departmentService) {
        $data = $request->getParsedBody();
        return $departmentService->update($response, $args['id'], $data);
    });

    // Delete department
    $app->delete('/api/departments/{id}', function (Request $request, Response $response, array $args) use ($departmentService) {
        return $departmentService->destroy($response, $args['id']);
    });
};
