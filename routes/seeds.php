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

        // contoh seed lain (brand/tipe/jenisworkorder bila memang seed):
        // $check->get('/brand', ...);
        // $check->get('/tipe', ...);
        // $check->get('/jenisworkorder', ...);
    });
};
