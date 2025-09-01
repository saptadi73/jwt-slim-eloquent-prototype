<?php
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Services\WorkOrderService;
use App\Support\RequestHelper;
use App\Support\JsonResponder;
use App\Middlewares\JwtMiddleware;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->group('/wo', function (RouteCollectorProxy $wo) use ($container) {
        // Create brand
        $wo->post('/new', function (Request $request, Response $response) use ($container) {
            /** @var WorkOrderService $svc */
            $svc  = $container->get(WorkOrderService::class);
            $data = RequestHelper::getJsonBody($request) ?? ($request->getParsedBody() ?? []);

            try {
                return $svc->createWorkOrder($request, $response, $data);
            } catch (\InvalidArgumentException $e) {
                return JsonResponder::error($response, $e->getMessage(), 422);
            } catch (\Throwable $e) {
                return JsonResponder::error($response, 'Internal server error', 500);
            }
        });

        $wo->get('/{id}', function (Request $request, Response $response, array $args) use ($container) {
            /** @var WorkOrderService $svc */

            try {
                $svc = $container->get(WorkOrderService::class);
                return $svc->getWorkOrderById($response, $args['id']);
            } catch (\Throwable $th) {
                //throw $th;
                return JsonResponder::error($response, 'Failed to retrieve Work Order: ' . $th->getMessage(), 500);
            }
        });

        $wo->get('/all', function (Request $request, Response $response) use ($container) {

            try {
                $svc = $container->get(WorkOrderService::class);
                return $svc->listWorkOrders($response);
            } catch (\Throwable $th) {
                //throw $th;
                return JsonResponder::error($response, 'Failed to retrieve Work Orders: ' . $th->getMessage(), 500);
            }
        });

        $wo->get('/jenisworkorder', function (Request $request, Response $response) use ($container) {

            try {
                $svc = $container->get(WorkOrderService::class);
                return $svc->listJenisWorkOrders($response);
            } catch (\Throwable $th) {
                //throw $th;
                return JsonResponder::error($response, 'Failed to retrieve Jenis Work Orders: ' . $th->getMessage(), 500);
            }
        });

        $wo->get('/checklistwo/{id}', function (Request $request, Response $response, array $args) use ($container) {

            try {
                $svc = $container->get(WorkOrderService::class);
                return $svc->getChecklistForWorkOrder($response, $args['id']);
            } catch (\Throwable $th) {
                //throw $th;
                return JsonResponder::error($response, 'Failed to retrieve Checklist for Work Order: ' . $th->getMessage(), 500);
            }
        });

        $wo->post('/jenistitle/', function (Request $request, Response $response) use ($container) {
            $data = RequestHelper::getJsonBody($request) ?? ($request->getParsedBody() ?? []);
            try {
                $svc = $container->get(WorkOrderService::class);
                return $svc->getChecklistTemplateByJenisTitle($response, $data);
            } catch (\Throwable $th) {
                //throw $th;
                return JsonResponder::error($response, 'Failed to retrieve Checklist Template: ' . $th->getMessage(), 500);
            }
        });

        $wo->post('/wo/checklist/input', function (Request $request, Response $response) use ($container) {
            $data = RequestHelper::getJsonBody($request) ?? ($request->getParsedBody() ?? []);
            try {
                $svc = $container->get(WorkOrderService::class);
                return $svc->inputChecklist($response, $data);
            } catch (\Throwable $th) {
                //throw $th;
                return JsonResponder::error($response, 'Failed to input Checklist for Work Order: ' . $th->getMessage(), 500);
            }
        });

        $wo->get('/delete/{id}', function (Request $request, Response $response, array $args) use ($container) {
            try {
                $svc = $container->get(WorkOrderService::class);
                return $svc->deleteWorkOrder($request, $response, $args['id']);
            } catch (\Throwable $th) {
                //throw $th;
                return JsonResponder::error($response, 'Failed to delete Work Order: ' . $th->getMessage(), 500);
            }
        });

        $wo->post('/update/{id}', function (Request $request, Response $response, array $args) use ($container) {
            $data = RequestHelper::getJsonBody($request) ?? ($request->getParsedBody() ?? []);
            try {
                $svc = $container->get(WorkOrderService::class);
                return $svc->updateWorkOrder($request, $response, $args['id'], $data);
            } catch (\Throwable $th) {
                //throw $th;
                return JsonResponder::error($response, 'Failed to update Work Order: ' . $th->getMessage(), 500);
            }
        });

        // Tambahkan endpoint lain terkait WorkOrder di sini…
        // $wo->get('/jenisworkorder', ...)
    })->add(new JwtMiddleware());
};
