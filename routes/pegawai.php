<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\PegawaiService;
use App\Support\JsonResponder;
use App\Support\FormDataParser;

return function ($app) {
    $pegawaiService = new PegawaiService();

    // GET all employees
    $app->get('/api/pegawai', function (Request $request, Response $response) use ($pegawaiService) {
        try {
            $params = $request->getQueryParams();
            $page = (int) ($params['page'] ?? 1);
            $limit = (int) ($params['limit'] ?? 10);
            
            // Extract filters
            $filters = [
                'department_id' => $params['department_id'] ?? null,
                'group_id' => $params['group_id'] ?? null,
                'position_id' => $params['position_id'] ?? null,
                'search' => $params['search'] ?? null,
                'is_active' => $params['is_active'] ?? null,
            ];

            $pegawai = $pegawaiService->getAll($page, $limit, $filters);

            return JsonResponder::success($response, $pegawai, 'Success', 200);
        } catch (\Exception $e) {
            return JsonResponder::error($response, $e->getMessage(), 500);
        }
    });

    // GET employee by id
    $app->get('/api/pegawai/{id}', function (Request $request, Response $response, $args) use ($pegawaiService) {
        try {
            $pegawai = $pegawaiService->getById($args['id']);
            return JsonResponder::success($response, $pegawai, 'Success', 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return JsonResponder::error($response, 'Employee not found', 404);
        } catch (\Exception $e) {
            return JsonResponder::error($response, $e->getMessage(), 500);
        }
    });

    // POST create employee with photo and signature upload
    $app->post('/api/pegawai', function (Request $request, Response $response) use ($pegawaiService) {
        try {
            $parsed = FormDataParser::parse($request);
            $data = $parsed['data'];
            $uploadedFiles = $parsed['files'];
            
            // Debug: log the incoming data
            error_log('POST /api/pegawai - Content-Type: ' . $request->getHeaderLine('Content-Type'));
            error_log('POST /api/pegawai - All received fields: ' . json_encode(array_keys($data)));
            error_log('POST /api/pegawai - Full data: ' . json_encode($data));
            error_log('POST /api/pegawai - position_id: ' . ($data['position_id'] ?? 'NOT SENT'));
            error_log('POST /api/pegawai - hire_date: ' . ($data['hire_date'] ?? 'NOT SENT'));
            error_log('POST /api/pegawai - Uploaded files: ' . json_encode(array_keys($uploadedFiles)));

            // Validate required field
            if (empty($data['nama'] ?? null)) {
                error_log('POST /api/pegawai - Validation failed: nama is empty');
                return JsonResponder::error($response, 'Name (nama) is required', 400);
            }

            $fotoFile = $uploadedFiles['url_foto'] ?? null;
            $tandaTanganFile = $uploadedFiles['tanda_tangan'] ?? null;

            error_log('POST /api/pegawai - Storing employee: ' . $data['nama']);
            
            $pegawai = $pegawaiService->store($data, $fotoFile, $tandaTanganFile);
            return JsonResponder::success($response, $pegawai->load(['departemen', 'group', 'tandaTangan']), 'Employee created', 201);
        } catch (\Exception $e) {
            error_log('POST /api/pegawai - Error: ' . $e->getMessage());
            error_log('POST /api/pegawai - File: ' . $e->getFile() . ':' . $e->getLine());
            return JsonResponder::error($response, $e->getMessage(), 500);
        }
    });

    // PUT update employee
    $app->put('/api/pegawai/{id}', function (Request $request, Response $response, $args) use ($pegawaiService) {
        try {
            $parsed = FormDataParser::parse($request);
            $data = $parsed['data'];
            $uploadedFiles = $parsed['files'];

            $fotoFile = $uploadedFiles['url_foto'] ?? null;
            $tandaTanganFile = $uploadedFiles['tanda_tangan'] ?? null;

            $pegawai = $pegawaiService->update($args['id'], $data, $fotoFile, $tandaTanganFile);
            return JsonResponder::success($response, $pegawai->load(['departemen', 'group', 'tandaTangan']), 'Employee updated', 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return JsonResponder::error($response, 'Employee not found', 404);
        } catch (\Exception $e) {
            return JsonResponder::error($response, $e->getMessage(), 500);
        }
    });

    // POST update employee (alternative endpoint for form submissions with files)
    $app->post('/api/pegawai/{id}', function (Request $request, Response $response, $args) use ($pegawaiService) {
        try {
            $parsed = FormDataParser::parse($request);
            $data = $parsed['data'];
            $uploadedFiles = $parsed['files'];

            $fotoFile = $uploadedFiles['url_foto'] ?? null;
            $tandaTanganFile = $uploadedFiles['tanda_tangan'] ?? null;

            $pegawai = $pegawaiService->update($args['id'], $data, $fotoFile, $tandaTanganFile);
            return JsonResponder::success($response, $pegawai->load(['departemen', 'group', 'tandaTangan']), 'Employee updated', 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return JsonResponder::error($response, 'Employee not found', 404);
        } catch (\Exception $e) {
            return JsonResponder::error($response, $e->getMessage(), 500);
        }
    });

    // DELETE employee
    $app->delete('/api/pegawai/{id}', function (Request $request, Response $response, $args) use ($pegawaiService) {
        try {
            $pegawaiService->delete($args['id']);
            return JsonResponder::success($response, [], 'Employee deleted', 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return JsonResponder::error($response, 'Employee not found', 404);
        } catch (\Exception $e) {
            return JsonResponder::error($response, $e->getMessage(), 500);
        }
    });
};
