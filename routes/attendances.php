<?php

use App\Services\AttendanceService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function ($app) {
    $attendanceService = new AttendanceService();

    // Get all attendances
    $app->get('/api/attendances', function (Request $request, Response $response) use ($attendanceService) {
        $params = $request->getQueryParams();
        return $attendanceService->index($response, $params);
    });

    // Get attendance by ID
    $app->get('/api/attendances/{id}', function (Request $request, Response $response, array $args) use ($attendanceService) {
        return $attendanceService->show($response, $args['id']);
    });

    // Get attendance summary
    $app->get('/api/attendances/summary/employee', function (Request $request, Response $response) use ($attendanceService) {
        $params = $request->getQueryParams();
        return $attendanceService->summary($response, $params);
    });

    // Check in
    $app->post('/api/attendances/checkin', function (Request $request, Response $response) use ($attendanceService) {
        $data = $request->getParsedBody();
        $uploadedFiles = $request->getUploadedFiles();
        
        if (isset($uploadedFiles['photo'])) {
            $data['photo_file'] = $uploadedFiles['photo'];
        }
        
        return $attendanceService->checkIn($response, $data);
    });

    // Check out
    $app->post('/api/attendances/{id}/checkout', function (Request $request, Response $response, array $args) use ($attendanceService) {
        $data = $request->getParsedBody();
        $uploadedFiles = $request->getUploadedFiles();
        
        if (isset($uploadedFiles['photo'])) {
            $data['photo_file'] = $uploadedFiles['photo'];
        }
        
        return $attendanceService->checkOut($response, $args['id'], $data);
    });

    // Create manual attendance
    $app->post('/api/attendances', function (Request $request, Response $response) use ($attendanceService) {
        $data = $request->getParsedBody();
        return $attendanceService->store($response, $data);
    });

    // Update attendance
    $app->put('/api/attendances/{id}', function (Request $request, Response $response, array $args) use ($attendanceService) {
        $data = $request->getParsedBody();
        return $attendanceService->update($response, $args['id'], $data);
    });

    // Delete attendance
    $app->delete('/api/attendances/{id}', function (Request $request, Response $response, array $args) use ($attendanceService) {
        return $attendanceService->destroy($response, $args['id']);
    });
};
