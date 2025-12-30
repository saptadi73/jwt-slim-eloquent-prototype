<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\TandaTanganService;
use App\Support\JsonResponder;

return function ($app) {
    $tandaTanganService = new TandaTanganService();

    // GET all signatures
    $app->get('/api/tanda-tangan', function (Request $request, Response $response) use ($tandaTanganService) {
        try {
            $page = (int) ($request->getQueryParams()['page'] ?? 1);
            $limit = (int) ($request->getQueryParams()['limit'] ?? 10);

            $tandaTangan = $tandaTanganService->getAll($page, $limit);

            return JsonResponder::success($response, $tandaTangan, 'Success', 200);
        } catch (\Exception $e) {
            return JsonResponder::error($response, 500, $e->getMessage());
        }
    });

    // GET signature by id
    $app->get('/api/tanda-tangan/{id}', function (Request $request, Response $response, $args) use ($tandaTanganService) {
        try {
            $tandaTangan = $tandaTanganService->getById($args['id']);
            return JsonResponder::success($response, $tandaTangan, 'Success', 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return JsonResponder::error($response, 404, 'Signature not found');
        } catch (\Exception $e) {
            return JsonResponder::error($response, 500, $e->getMessage());
        }
    });

    // GET signatures by employee id
    $app->get('/api/pegawai/{pegawaiId}/tanda-tangan', function (Request $request, Response $response, $args) use ($tandaTanganService) {
        try {
            $page = (int) ($request->getQueryParams()['page'] ?? 1);
            $limit = (int) ($request->getQueryParams()['limit'] ?? 10);

            $tandaTangan = $tandaTanganService->getByPegawaiId($args['pegawaiId'], $page, $limit);
            return JsonResponder::success($response, $tandaTangan, 'Success', 200);
        } catch (\Exception $e) {
            return JsonResponder::error($response, 500, $e->getMessage());
        }
    });

    // POST create signature
    $app->post('/api/tanda-tangan', function (Request $request, Response $response) use ($tandaTanganService) {
        try {
            $data = $request->getParsedBody();
            $uploadedFiles = $request->getUploadedFiles();

            if (empty($data['pegawai_id'])) {
                return JsonResponder::error($response, 400, 'Employee ID is required');
            }

            $file = $uploadedFiles['tanda_tangan'] ?? null;

            $tandaTangan = $tandaTanganService->store($data, $file);
            return JsonResponder::success($response, $tandaTangan, 'Signature created', 201);
        } catch (\Exception $e) {
            return JsonResponder::error($response, 500, $e->getMessage());
        }
    });

    // PUT update signature
    $app->put('/api/tanda-tangan/{id}', function (Request $request, Response $response, $args) use ($tandaTanganService) {
        try {
            $data = $request->getParsedBody();
            $uploadedFiles = $request->getUploadedFiles();

            $file = $uploadedFiles['tanda_tangan'] ?? null;

            $tandaTangan = $tandaTanganService->update($args['id'], $data, $file);
            return JsonResponder::success($response, $tandaTangan, 'Signature updated', 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return JsonResponder::error($response, 404, 'Signature not found');
        } catch (\Exception $e) {
            return JsonResponder::error($response, 500, $e->getMessage());
        }
    });

    // DELETE signature
    $app->delete('/api/tanda-tangan/{id}', function (Request $request, Response $response, $args) use ($tandaTanganService) {
        try {
            $tandaTanganService->delete($args['id']);
            return JsonResponder::success($response, [], 'Signature deleted', 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return JsonResponder::error($response, 404, 'Signature not found');
        } catch (\Exception $e) {
            return JsonResponder::error($response, 500, $e->getMessage());
        }
    });
};
