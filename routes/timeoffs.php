<?php

use App\Services\TimeOffService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function ($app) {
    $timeOffService = new TimeOffService();

    // Get all time offs
    $app->get('/api/timeoffs', function (Request $request, Response $response) use ($timeOffService) {
        $params = $request->getQueryParams();
        return $timeOffService->index($response, $params);
    });

    // Get time off by ID
    $app->get('/api/timeoffs/{id}', function (Request $request, Response $response, array $args) use ($timeOffService) {
        return $timeOffService->show($response, $args['id']);
    });

    // Create new time off request
    $app->post('/api/timeoffs', function (Request $request, Response $response) use ($timeOffService) {
        $data = $request->getParsedBody();
        return $timeOffService->store($response, $data);
    });

    // Update time off
    $app->put('/api/timeoffs/{id}', function (Request $request, Response $response, array $args) use ($timeOffService) {
        $data = $request->getParsedBody();
        return $timeOffService->update($response, $args['id'], $data);
    });

    // Approve time off
    $app->post('/api/timeoffs/{id}/approve', function (Request $request, Response $response, array $args) use ($timeOffService) {
        $data = $request->getParsedBody();
        return $timeOffService->approve($response, $args['id'], $data);
    });

    // Reject time off
    $app->post('/api/timeoffs/{id}/reject', function (Request $request, Response $response, array $args) use ($timeOffService) {
        $data = $request->getParsedBody();
        return $timeOffService->reject($response, $args['id'], $data);
    });

    // Cancel time off
    $app->post('/api/timeoffs/{id}/cancel', function (Request $request, Response $response, array $args) use ($timeOffService) {
        return $timeOffService->cancel($response, $args['id']);
    });

    // Delete time off
    $app->delete('/api/timeoffs/{id}', function (Request $request, Response $response, array $args) use ($timeOffService) {
        return $timeOffService->destroy($response, $args['id']);
    });

    // Get time offs by pegawai
    $app->get('/api/pegawai/{pegawaiId}/timeoffs', function (Request $request, Response $response, array $args) use ($timeOffService) {
        $params = $request->getQueryParams();
        $params['pegawai_id'] = $args['pegawaiId'];
        return $timeOffService->index($response, $params);
    });
};
