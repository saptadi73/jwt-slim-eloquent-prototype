<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\PegawaiService;
use App\Support\JsonResponder;

return function ($app) {
    $pegawaiService = new PegawaiService();

    // GET all employees
    $app->get('/api/pegawai', function (Request $request, Response $response) use ($pegawaiService) {
        try {
            $page = (int) ($request->getQueryParams()['page'] ?? 1);
            $limit = (int) ($request->getQueryParams()['limit'] ?? 10);

            $pegawai = $pegawaiService->getAll($page, $limit);

            return JsonResponder::success($response, $pegawai, 'Success', 200);
        } catch (\Exception $e) {
            return JsonResponder::error($response, 500, $e->getMessage());
        }
    });

    // GET employee by id
    $app->get('/api/pegawai/{id}', function (Request $request, Response $response, $args) use ($pegawaiService) {
        try {
            $pegawai = $pegawaiService->getById($args['id']);
            return JsonResponder::success($response, $pegawai, 'Success', 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return JsonResponder::error($response, 404, 'Employee not found');
        } catch (\Exception $e) {
            return JsonResponder::error($response, 500, $e->getMessage());
        }
    });

    // POST create employee with photo and signature upload
    $app->post('/api/pegawai', function (Request $request, Response $response) use ($pegawaiService) {
        try {
            $data = $request->getParsedBody();
            $uploadedFiles = $request->getUploadedFiles();

            if (empty($data['nama'])) {
                return JsonResponder::error($response, 400, 'Name is required');
            }

            $fotoFile = $uploadedFiles['url_foto'] ?? null;
            $tandaTanganFile = $uploadedFiles['tanda_tangan'] ?? null;

            $pegawai = $pegawaiService->store($data, $fotoFile, $tandaTanganFile);
            return JsonResponder::success($response, $pegawai->load(['departemen', 'group', 'tandaTangan']), 'Employee created', 201);
        } catch (\Exception $e) {
            return JsonResponder::error($response, 500, $e->getMessage());
        }
    });

    // PUT update employee
    $app->put('/api/pegawai/{id}', function (Request $request, Response $response, $args) use ($pegawaiService) {
        try {
            $data = $request->getParsedBody();
            $uploadedFiles = $request->getUploadedFiles();

            $fotoFile = $uploadedFiles['url_foto'] ?? null;
            $tandaTanganFile = $uploadedFiles['tanda_tangan'] ?? null;

            $pegawai = $pegawaiService->update($args['id'], $data, $fotoFile, $tandaTanganFile);
            return JsonResponder::success($response, $pegawai->load(['departemen', 'group', 'tandaTangan']), 'Employee updated', 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return JsonResponder::error($response, 404, 'Employee not found');
        } catch (\Exception $e) {
            return JsonResponder::error($response, 500, $e->getMessage());
        }
    });

    // POST update employee (alternative endpoint for form submissions with files)
    $app->post('/api/pegawai/{id}', function (Request $request, Response $response, $args) use ($pegawaiService) {
        try {
            $data = $request->getParsedBody();
            $uploadedFiles = $request->getUploadedFiles();

            $fotoFile = $uploadedFiles['url_foto'] ?? null;
            $tandaTanganFile = $uploadedFiles['tanda_tangan'] ?? null;

            $pegawai = $pegawaiService->update($args['id'], $data, $fotoFile, $tandaTanganFile);
            return JsonResponder::success($response, $pegawai->load(['departemen', 'group', 'tandaTangan']), 'Employee updated', 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return JsonResponder::error($response, 404, 'Employee not found');
        } catch (\Exception $e) {
            return JsonResponder::error($response, 500, $e->getMessage());
        }
    });

    // DELETE employee
    $app->delete('/api/pegawai/{id}', function (Request $request, Response $response, $args) use ($pegawaiService) {
        try {
            $pegawaiService->delete($args['id']);
            return JsonResponder::success($response, [], 'Employee deleted', 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return JsonResponder::error($response, 404, 'Employee not found');
        } catch (\Exception $e) {
            return JsonResponder::error($response, 500, $e->getMessage());
        }
    });
};
