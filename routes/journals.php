<?php

use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Services\JournalService;
use App\Support\JsonResponder;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function (App $app) {
    $container = $app->getContainer();

    $app->group('/journals', function (RouteCollectorProxy $journals) use ($container) {

        // Create Sales Journal
        $journals->post('/sales', function (Request $request, Response $response) use ($container) {
            $data = json_decode($request->getBody()->getContents(), true) ?? [];

            // Basic validation
            if (!isset($data['date']) || !isset($data['amount']) || !isset($data['receivable_account_id']) || !isset($data['revenue_account_id'])) {
                return JsonResponder::error($response, 'Date, amount, receivable_account_id, and revenue_account_id are required', 400);
            }

            /** @var JournalService $svc */
            $svc = $container->get(JournalService::class);
            return $svc->createSalesJournal($response, (array) $data);
        });

        // Create Sales Payment Journal
        $journals->post('/sales-payment', function (Request $request, Response $response) use ($container) {
            $data = json_decode($request->getBody()->getContents(), true) ?? [];

            // Basic validation
            if (!isset($data['date']) || !isset($data['amount']) || !isset($data['cash_account_id']) || !isset($data['receivable_account_id'])) {
                return JsonResponder::error($response, 'Date, amount, cash_account_id, and receivable_account_id are required', 400);
            }

            /** @var JournalService $svc */
            $svc = $container->get(JournalService::class);
            return $svc->createSalesPaymentJournal($response, (array) $data);
        });

        // Create Purchase Journal
        $journals->post('/purchase', function (Request $request, Response $response) use ($container) {
            $data = json_decode($request->getBody()->getContents(), true) ?? [];

            // Basic validation
            if (!isset($data['date']) || !isset($data['amount']) || !isset($data['inventory_account_id']) || !isset($data['payable_account_id'])) {
                return JsonResponder::error($response, 'Date, amount, inventory_account_id, and payable_account_id are required', 400);
            }

            /** @var JournalService $svc */
            $svc = $container->get(JournalService::class);
            return $svc->createPurchaseJournal($response, (array) $data);
        });

        // Create Purchase Payment Journal
        $journals->post('/purchase-payment', function (Request $request, Response $response) use ($container) {
            $data = json_decode($request->getBody()->getContents(), true) ?? [];

            // Basic validation
            if (!isset($data['date']) || !isset($data['amount']) || !isset($data['payable_account_id']) || !isset($data['cash_account_id'])) {
                return JsonResponder::error($response, 'Date, amount, payable_account_id, and cash_account_id are required', 400);
            }

            /** @var JournalService $svc */
            $svc = $container->get(JournalService::class);
            return $svc->createPurchasePaymentJournal($response, (array) $data);
        });

        // Create Expense Journal
        $journals->post('/expense', function (Request $request, Response $response) use ($container) {
            $data = json_decode($request->getBody()->getContents(), true) ?? [];

            // Basic validation
            if (!isset($data['date']) || !isset($data['amount']) || !isset($data['expense_account_id']) || !isset($data['credit_account_id'])) {
                return JsonResponder::error($response, 'Date, amount, expense_account_id, and credit_account_id are required', 400);
            }

            /** @var JournalService $svc */
            $svc = $container->get(JournalService::class);
            return $svc->createExpenseJournal($response, (array) $data);
        });

        // Create Expense Payment Journal
        $journals->post('/expense-payment', function (Request $request, Response $response) use ($container) {
            $data = json_decode($request->getBody()->getContents(), true) ?? [];

            // Basic validation
            if (!isset($data['date']) || !isset($data['amount']) || !isset($data['debit_account_id']) || !isset($data['cash_account_id'])) {
                return JsonResponder::error($response, 'Date, amount, debit_account_id, and cash_account_id are required', 400);
            }

            /** @var JournalService $svc */
            $svc = $container->get(JournalService::class);
            return $svc->createExpensePaymentJournal($response, (array) $data);
        });

        // Create Miscellaneous Journal
        $journals->post('/miscellaneous', function (Request $request, Response $response) use ($container) {
            $data = json_decode($request->getBody()->getContents(), true) ?? [];

            // Basic validation
            if (!isset($data['date']) || !isset($data['amount']) || !isset($data['debit_account_id']) || !isset($data['credit_account_id'])) {
                return JsonResponder::error($response, 'Date, amount, debit_account_id, and credit_account_id are required', 400);
            }

            /** @var JournalService $svc */
            $svc = $container->get(JournalService::class);
            return $svc->createMiscellaneousJournal($response, (array) $data);
        });
    });
};
