<?php
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Services\DashboardService;
use App\Support\JsonResponder;
use App\Middlewares\JwtMiddleware;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

return function (App $app) {
    $app->group('/dashboard', function (RouteCollectorProxy $dashboard) {
        
        // Get complete dashboard data (all metrics and charts)
        $dashboard->get('', function (Request $request, Response $response) {
            $months = $request->getQueryParams()['months'] ?? 6;
            $data = DashboardService::getCompleteDashboard((int) $months);
            return JsonResponder::success($response, $data, 'Dashboard data retrieved');
        });

        // Get dashboard summary only (metrics without charts)
        $dashboard->get('/summary', function (Request $request, Response $response) {
            $data = DashboardService::getDashboardSummary();
            return JsonResponder::success($response, $data, 'Dashboard summary retrieved');
        });

        // Get workorder metrics
        $dashboard->get('/workorder-metrics', function (Request $request, Response $response) {
            $data = DashboardService::getWorkorderMetrics();
            return JsonResponder::success($response, $data, 'Workorder metrics retrieved');
        });

        // Get attendance metrics
        $dashboard->get('/attendance-metrics', function (Request $request, Response $response) {
            $data = DashboardService::getAttendanceMetrics();
            return JsonResponder::success($response, $data, 'Attendance metrics retrieved');
        });

        // Get workorder status for pie chart
        $dashboard->get('/workorder-status', function (Request $request, Response $response) {
            $data = DashboardService::getWorkorderStatus();
            return JsonResponder::success($response, $data, 'Workorder status retrieved');
        });

        // Get sales data
        $dashboard->get('/sales', function (Request $request, Response $response) {
            $months = $request->getQueryParams()['months'] ?? 6;
            $data = DashboardService::getSalesData((int) $months);
            return JsonResponder::success($response, $data, 'Sales data retrieved');
        });

        // Get purchases data
        $dashboard->get('/purchases', function (Request $request, Response $response) {
            $months = $request->getQueryParams()['months'] ?? 6;
            $data = DashboardService::getPurchasesData((int) $months);
            return JsonResponder::success($response, $data, 'Purchases data retrieved');
        });

        // Get expenses data
        $dashboard->get('/expenses', function (Request $request, Response $response) {
            $months = $request->getQueryParams()['months'] ?? 6;
            $data = DashboardService::getExpensesData((int) $months);
            return JsonResponder::success($response, $data, 'Expenses data retrieved');
        });

        // Get comparison data (purchases, expenses, sales)
        $dashboard->get('/comparison', function (Request $request, Response $response) {
            $months = $request->getQueryParams()['months'] ?? 6;
            $data = DashboardService::getPurchaseExpensesSalesComparison((int) $months);
            return JsonResponder::success($response, $data, 'Comparison data retrieved');
        });

    })->add(new JwtMiddleware());
};
