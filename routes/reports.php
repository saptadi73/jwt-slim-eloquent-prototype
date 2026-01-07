<?php

use App\Services\ReportService;
use App\Services\InventoryReportService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function ($app) {
    $reportService = new ReportService();
    $inventoryReport = new InventoryReportService();

    // Balance Sheet Report
    $app->get('/api/reports/balance-sheet', function (Request $request, Response $response) use ($reportService) {
        $params = $request->getQueryParams();
        return $reportService->getBalanceSheet($response, $params);
    });

    // Profit & Loss Report
    $app->get('/api/reports/profit-loss', function (Request $request, Response $response) use ($reportService) {
        $params = $request->getQueryParams();
        return $reportService->getProfitAndLoss($response, $params);
    });

    // Cash Book Report
    $app->get('/api/reports/cash-book', function (Request $request, Response $response) use ($reportService) {
        $params = $request->getQueryParams();
        return $reportService->getCashBook($response, $params);
    });

    // Aged Ledger Report (Receivable/Payable)
    $app->get('/api/reports/aged-ledger', function (Request $request, Response $response) use ($reportService) {
        $params = $request->getQueryParams();
        return $reportService->getAgedLedger($response, $params);
    });

    // General Ledger Report
    $app->get('/api/reports/general-ledger', function (Request $request, Response $response) use ($reportService) {
        $params = $request->getQueryParams();
        return $reportService->getGeneralLedger($response, $params);
    });

    // Trial Balance Report
    $app->get('/api/reports/trial-balance', function (Request $request, Response $response) use ($reportService) {
        $params = $request->getQueryParams();
        return $reportService->getTrialBalance($response, $params);
    });

    // Product Movement Report (Laporan Pergerakan Barang)
    $app->get('/api/reports/product-movements', function (Request $request, Response $response) use ($inventoryReport) {
        $params = $request->getQueryParams();
        return $inventoryReport->getProductMovements($response, $params);
    });

    // Product Movement Summary (requires start_date & end_date)
    $app->get('/api/reports/product-movements/summary', function (Request $request, Response $response) use ($inventoryReport) {
        $params = $request->getQueryParams();
        return $inventoryReport->getProductMovementsSummary($response, $params);
    });
};
