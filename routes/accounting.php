<?php

use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Services\AccountingService;
use App\Services\ReportService;
use App\Support\JsonResponder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Middlewares\JwtMiddleware;

return function (App $app) {
    $container = $app->getContainer();

    $app->group('/accounting', function (RouteCollectorProxy $accounting) use ($container) {

        // ===========================
        // Journal Entry Routes
        // ===========================

        // Get all journal entries with filters
        $accounting->get('/journals', function (Request $request, Response $response) use ($container) {
            $params = $request->getQueryParams();
            $svc = $container->get(AccountingService::class);
            return $svc->getAllJournals($response, $params);
        });

        // Get journal entry by ID
        $accounting->get('/journals/{id}', function (Request $request, Response $response, array $args) use ($container) {
            $id = $args['id'];
            $svc = $container->get(AccountingService::class);
            return $svc->getJournalById($response, $id);
        });

        // Delete journal entry
        $accounting->delete('/journals/{id}', function (Request $request, Response $response, array $args) use ($container) {
            $id = $args['id'];
            $svc = $container->get(AccountingService::class);
            return $svc->deleteJournal($response, $id);
        })->add(new JwtMiddleware());

        // Create Miscellaneous Journal Entry
        $accounting->post('/journals/miscellaneous', function (Request $request, Response $response) use ($container) {
            $data = json_decode($request->getBody()->getContents(), true) ?? [];
            $svc = $container->get(AccountingService::class);
            return $svc->createMiscellaneousJournal($response, (array) $data);
        })->add(new JwtMiddleware());

        // Create Sales Perpetual Journal
        $accounting->post('/journals/sales-perpetual', function (Request $request, Response $response) use ($container) {
            $data = json_decode($request->getBody()->getContents(), true) ?? [];
            
            if (!isset($data['sale_order_id'])) {
                return JsonResponder::error($response, 'sale_order_id is required', 400);
            }

            $svc = $container->get(AccountingService::class);
            return $svc->createSalesPerpetualJournal($response, (array) $data);
        })->add(new JwtMiddleware());

        // Create Sales Payment Journal
        $accounting->post('/journals/sales-payment', function (Request $request, Response $response) use ($container) {
            $data = json_decode($request->getBody()->getContents(), true) ?? [];
            
            if (!isset($data['payment_date']) || !isset($data['amount'])) {
                return JsonResponder::error($response, 'payment_date and amount are required', 400);
            }

            $svc = $container->get(AccountingService::class);
            return $svc->createSalesPaymentJournal($response, (array) $data);
        })->add(new JwtMiddleware());

        // Create Purchase Journal
        $accounting->post('/journals/purchase', function (Request $request, Response $response) use ($container) {
            $data = json_decode($request->getBody()->getContents(), true) ?? [];
            
            if (!isset($data['purchase_order_id'])) {
                return JsonResponder::error($response, 'purchase_order_id is required', 400);
            }

            $svc = $container->get(AccountingService::class);
            return $svc->createPurchaseJournal($response, (array) $data);
        })->add(new JwtMiddleware());

        // Create Purchase Payment Journal
        $accounting->post('/journals/purchase-payment', function (Request $request, Response $response) use ($container) {
            $data = json_decode($request->getBody()->getContents(), true) ?? [];
            
            if (!isset($data['payment_date']) || !isset($data['amount'])) {
                return JsonResponder::error($response, 'payment_date and amount are required', 400);
            }

            $svc = $container->get(AccountingService::class);
            return $svc->createPurchasePaymentJournal($response, (array) $data);
        })->add(new JwtMiddleware());

        // Create Expense Journal
        $accounting->post('/journals/expense', function (Request $request, Response $response) use ($container) {
            $data = json_decode($request->getBody()->getContents(), true) ?? [];
            
            if (!isset($data['expense_id']) || !isset($data['expense_account_id'])) {
                return JsonResponder::error($response, 'expense_id and expense_account_id are required', 400);
            }

            $svc = $container->get(AccountingService::class);
            return $svc->createExpenseJournal($response, (array) $data);
        })->add(new JwtMiddleware());

        // Create Expense Payment Journal
        $accounting->post('/journals/expense-payment', function (Request $request, Response $response) use ($container) {
            $data = json_decode($request->getBody()->getContents(), true) ?? [];
            
            if (!isset($data['payment_date']) || !isset($data['amount'])) {
                return JsonResponder::error($response, 'payment_date and amount are required', 400);
            }

            $svc = $container->get(AccountingService::class);
            return $svc->createExpensePaymentJournal($response, (array) $data);
        })->add(new JwtMiddleware());

        // Create Internal Goods Expenditure Journal
        $accounting->post('/journals/internal-expenditure', function (Request $request, Response $response) use ($container) {
            $data = json_decode($request->getBody()->getContents(), true) ?? [];
            
            if (!isset($data['usage_date']) || !isset($data['amount'])) {
                return JsonResponder::error($response, 'usage_date and amount are required', 400);
            }

            $svc = $container->get(AccountingService::class);
            return $svc->createInternalGoodsExpenditureJournal($response, (array) $data);
        })->add(new JwtMiddleware());

        // ===========================
        // Financial Reports Routes
        // ===========================

        // Balance Sheet Report
        $accounting->get('/reports/balance-sheet', function (Request $request, Response $response) use ($container) {
            $params = $request->getQueryParams();
            $svc = $container->get(ReportService::class);
            return $svc->getBalanceSheet($response, $params);
        });

        // Profit & Loss Report
        $accounting->get('/reports/profit-loss', function (Request $request, Response $response) use ($container) {
            $params = $request->getQueryParams();
            $svc = $container->get(ReportService::class);
            return $svc->getProfitAndLoss($response, $params);
        });

        // Cash Book Report
        $accounting->get('/reports/cash-book', function (Request $request, Response $response) use ($container) {
            $params = $request->getQueryParams();
            $svc = $container->get(ReportService::class);
            return $svc->getCashBook($response, $params);
        });

        // Aged Ledger Report (Receivable/Payable Aging)
        $accounting->get('/reports/aged-ledger', function (Request $request, Response $response) use ($container) {
            $params = $request->getQueryParams();
            
            if (!isset($params['type'])) {
                return JsonResponder::error($response, 'type parameter is required (receivable or payable)', 400);
            }

            $svc = $container->get(ReportService::class);
            return $svc->getAgedLedger($response, $params);
        });

        // Trial Balance Report
        $accounting->get('/reports/trial-balance', function (Request $request, Response $response) use ($container) {
            $params = $request->getQueryParams();
            $svc = $container->get(ReportService::class);
            return $svc->getTrialBalance($response, $params);
        });

        // General Ledger Report
        $accounting->get('/reports/general-ledger', function (Request $request, Response $response) use ($container) {
            $params = $request->getQueryParams();
            
            if (!isset($params['account_id'])) {
                return JsonResponder::error($response, 'account_id parameter is required', 400);
            }

            $svc = $container->get(ReportService::class);
            return $svc->getGeneralLedger($response, $params);
        });
    });
};
