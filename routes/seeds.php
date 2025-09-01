<?php
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Services\CheckListService;
use App\Support\JsonResponder;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

return function (App $app) {
    // Kumpulan endpoint seed/isi tabel checklist
    $app->group('/seeds', function (RouteCollectorProxy $check) {
        $check->get('/checklist1', function (Request $request, Response $response) {
            $payload = CheckListService::isiTableTeknisiServiceAC();
            return JsonResponder::success($response, $payload, 'Checklist retrieved');
        });
        $check->get('/checklist2', function (Request $request, Response $response) {
            $payload = CheckListService::isiTableTeknisiServiceAC2();
            return JsonResponder::success($response, $payload, 'Checklist retrieved');
        });
        $check->get('/tipe', function (Request $request, Response $response) {
            try {
                $payload = CheckListService::isiTableTipe();
                return JsonResponder::success($response, $payload, 'Checklist retrieved');
            } catch (\Throwable $e) {
                return JsonResponder::error($response, $e->getMessage(), 500);
            }
        });
        $check->get('/jenisworkorder', function (Request $request, Response $response) {
            $payload = CheckListService::isiTableJenisWorkorder();
            return JsonResponder::success($response, $payload, 'Checklist retrieved');
        });
        $check->get('/brand', function (Request $request, Response $response) {
            try {
                $payload = CheckListService::isiBrand();
                return JsonResponder::success($response, $payload, 'Brand retrieved');
            } catch (\Throwable $e) {
                return JsonResponder::error($response, $e->getMessage(), 500);
            }
        });
    });
};
