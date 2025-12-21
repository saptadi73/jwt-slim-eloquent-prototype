<?php

namespace App\Services;

use App\Models\JournalEntry;
use App\Models\JournalLine;
use App\Models\ChartOfAccount;
use App\Models\SaleOrder;
use App\Models\PurchaseOrder;
use App\Models\Expense;
use App\Models\ProductMoveHistory;
use App\Support\JsonResponder;
use Illuminate\Database\Capsule\Manager as DB;
use Psr\Http\Message\ResponseInterface as Response;

class AccountingService
{
    /**
     * Create a miscellaneous journal entry
     * Used for manual adjustments and other non-standard entries
     */
    public function createMiscellaneousJournal(Response $response, array $data): Response
    {
        try {
            // Validate that debits equal credits
            $totalDebit = 0;
            $totalCredit = 0;
            foreach ($data['lines'] as $line) {
                $totalDebit += $line['debit'] ?? 0;
                $totalCredit += $line['credit'] ?? 0;
            }

            if (abs($totalDebit - $totalCredit) > 0.01) {
                return JsonResponder::error($response, 'Debit and credit must be balanced', 400);
            }

            $entry = DB::connection()->transaction(function () use ($data) {
                // Create journal entry
                $entry = JournalEntry::create([
                    'entry_date' => $data['entry_date'],
                    'reference_number' => $data['reference_number'] ?? 'MISC-' . time(),
                    'description' => $data['description'] ?? 'Miscellaneous Journal Entry',
                    'status' => $data['status'] ?? 'posted',
                    'created_by' => $data['created_by'] ?? null,
                ]);

                // Create journal lines
                foreach ($data['lines'] as $line) {
                    JournalLine::create([
                        'journal_entry_id' => $entry->id,
                        'chart_of_account_id' => $line['chart_of_account_id'],
                        'description' => $line['description'] ?? '',
                        'debit' => $line['debit'] ?? 0,
                        'credit' => $line['credit'] ?? 0,
                    ]);
                }

                return $entry->load('journalLines.chartOfAccount');
            });

            return JsonResponder::success($response, $entry, 'Miscellaneous journal entry created successfully', 201);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to create journal entry: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Create sales perpetual journal
     * Records the sale of goods with inventory tracking
     */
    public function createSalesPerpetualJournal(Response $response, array $data): Response
    {
        try {
            $entry = DB::connection()->transaction(function () use ($data) {
                $saleOrder = SaleOrder::with('customer', 'productOrderLines.product')->find($data['sale_order_id']);
                
                if (!$saleOrder) {
                    throw new \Exception('Sale order not found');
                }

                // Get CoA IDs
                $accountsReceivable = ChartOfAccount::where('code', '1120')->first(); // A/R
                $salesRevenue = ChartOfAccount::where('code', '4100')->first(); // Sales Revenue
                $cogs = ChartOfAccount::where('code', '5100')->first(); // Cost of Goods Sold
                $inventory = ChartOfAccount::where('code', '1130')->first(); // Inventory

                if (!$accountsReceivable || !$salesRevenue || !$cogs || !$inventory) {
                    throw new \Exception('Required chart of accounts not found');
                }

                // Create journal entry
                $entry = JournalEntry::create([
                    'entry_date' => $saleOrder->order_date,
                    'reference_number' => 'SALE-' . $saleOrder->order_number,
                    'description' => 'Sales Perpetual Journal - ' . $saleOrder->customer->name,
                    'status' => 'posted',
                    'created_by' => $data['created_by'] ?? null,
                ]);

                // Debit Accounts Receivable / Credit Sales Revenue
                JournalLine::create([
                    'journal_entry_id' => $entry->id,
                    'chart_of_account_id' => $accountsReceivable->id,
                    'description' => 'Accounts Receivable - ' . $saleOrder->customer->name,
                    'debit' => $saleOrder->total_amount,
                    'credit' => 0,
                    'customer_id' => $saleOrder->customer_id,
                ]);

                JournalLine::create([
                    'journal_entry_id' => $entry->id,
                    'chart_of_account_id' => $salesRevenue->id,
                    'description' => 'Sales Revenue - ' . $saleOrder->order_number,
                    'debit' => 0,
                    'credit' => $saleOrder->total_amount,
                ]);

                // Calculate COGS
                $totalCost = 0;
                foreach ($saleOrder->productOrderLines as $line) {
                    $totalCost += ($line->product->cost ?? 0) * $line->quantity;
                }

                // Debit COGS / Credit Inventory
                if ($totalCost > 0) {
                    JournalLine::create([
                        'journal_entry_id' => $entry->id,
                        'chart_of_account_id' => $cogs->id,
                        'description' => 'Cost of Goods Sold - ' . $saleOrder->order_number,
                        'debit' => $totalCost,
                        'credit' => 0,
                    ]);

                    JournalLine::create([
                        'journal_entry_id' => $entry->id,
                        'chart_of_account_id' => $inventory->id,
                        'description' => 'Inventory Reduction - ' . $saleOrder->order_number,
                        'debit' => 0,
                        'credit' => $totalCost,
                    ]);
                }

                return $entry->load('journalLines.chartOfAccount');
            });

            return JsonResponder::success($response, $entry, 'Sales perpetual journal created successfully', 201);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to create sales journal: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Create sales payment journal
     * Records payment received from customer
     */
    public function createSalesPaymentJournal(Response $response, array $data): Response
    {
        try {
            $entry = DB::connection()->transaction(function () use ($data) {
                // Get CoA IDs
                $cash = ChartOfAccount::where('code', '1110')->first(); // Cash/Bank
                $accountsReceivable = ChartOfAccount::where('code', '1120')->first(); // A/R

                if (!$cash || !$accountsReceivable) {
                    throw new \Exception('Required chart of accounts not found');
                }

                // Create journal entry
                $entry = JournalEntry::create([
                    'entry_date' => $data['payment_date'],
                    'reference_number' => $data['reference_number'] ?? 'PAY-SALE-' . time(),
                    'description' => $data['description'] ?? 'Payment received from customer',
                    'status' => 'posted',
                    'created_by' => $data['created_by'] ?? null,
                ]);

                // Debit Cash / Credit Accounts Receivable
                JournalLine::create([
                    'journal_entry_id' => $entry->id,
                    'chart_of_account_id' => $cash->id,
                    'description' => 'Cash received - ' . ($data['description'] ?? ''),
                    'debit' => $data['amount'],
                    'credit' => 0,
                ]);

                JournalLine::create([
                    'journal_entry_id' => $entry->id,
                    'chart_of_account_id' => $accountsReceivable->id,
                    'description' => 'A/R reduction - ' . ($data['description'] ?? ''),
                    'debit' => 0,
                    'credit' => $data['amount'],
                    'customer_id' => $data['customer_id'] ?? null,
                ]);

                return $entry->load('journalLines.chartOfAccount');
            });

            return JsonResponder::success($response, $entry, 'Sales payment journal created successfully', 201);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to create payment journal: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Create purchase journal
     * Records purchase of goods/services
     */
    public function createPurchaseJournal(Response $response, array $data): Response
    {
        try {
            $entry = DB::connection()->transaction(function () use ($data) {
                $purchaseOrder = PurchaseOrder::with('vendor', 'purchaseOrderLines.product')->find($data['purchase_order_id']);
                
                if (!$purchaseOrder) {
                    throw new \Exception('Purchase order not found');
                }

                // Get CoA IDs
                $inventory = ChartOfAccount::where('code', '1130')->first(); // Inventory
                $accountsPayable = ChartOfAccount::where('code', '2110')->first(); // A/P

                if (!$inventory || !$accountsPayable) {
                    throw new \Exception('Required chart of accounts not found');
                }

                // Create journal entry
                $entry = JournalEntry::create([
                    'entry_date' => $purchaseOrder->order_date,
                    'reference_number' => 'PUR-' . $purchaseOrder->order_number,
                    'description' => 'Purchase Journal - ' . $purchaseOrder->vendor->name,
                    'status' => 'posted',
                    'created_by' => $data['created_by'] ?? null,
                ]);

                // Debit Inventory / Credit Accounts Payable
                JournalLine::create([
                    'journal_entry_id' => $entry->id,
                    'chart_of_account_id' => $inventory->id,
                    'description' => 'Inventory Purchase - ' . $purchaseOrder->order_number,
                    'debit' => $purchaseOrder->total_amount,
                    'credit' => 0,
                ]);

                JournalLine::create([
                    'journal_entry_id' => $entry->id,
                    'chart_of_account_id' => $accountsPayable->id,
                    'description' => 'Accounts Payable - ' . $purchaseOrder->vendor->name,
                    'debit' => 0,
                    'credit' => $purchaseOrder->total_amount,
                    'vendor_id' => $purchaseOrder->vendor_id,
                ]);

                return $entry->load('journalLines.chartOfAccount');
            });

            return JsonResponder::success($response, $entry, 'Purchase journal created successfully', 201);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to create purchase journal: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Create purchase payment journal
     * Records payment to vendor
     */
    public function createPurchasePaymentJournal(Response $response, array $data): Response
    {
        try {
            $entry = DB::connection()->transaction(function () use ($data) {
                // Get CoA IDs
                $accountsPayable = ChartOfAccount::where('code', '2110')->first(); // A/P
                $cash = ChartOfAccount::where('code', '1110')->first(); // Cash/Bank

                if (!$cash || !$accountsPayable) {
                    throw new \Exception('Required chart of accounts not found');
                }

                // Create journal entry
                $entry = JournalEntry::create([
                    'entry_date' => $data['payment_date'],
                    'reference_number' => $data['reference_number'] ?? 'PAY-PUR-' . time(),
                    'description' => $data['description'] ?? 'Payment to vendor',
                    'status' => 'posted',
                    'created_by' => $data['created_by'] ?? null,
                ]);

                // Debit Accounts Payable / Credit Cash
                JournalLine::create([
                    'journal_entry_id' => $entry->id,
                    'chart_of_account_id' => $accountsPayable->id,
                    'description' => 'A/P reduction - ' . ($data['description'] ?? ''),
                    'debit' => $data['amount'],
                    'credit' => 0,
                    'vendor_id' => $data['vendor_id'] ?? null,
                ]);

                JournalLine::create([
                    'journal_entry_id' => $entry->id,
                    'chart_of_account_id' => $cash->id,
                    'description' => 'Cash payment - ' . ($data['description'] ?? ''),
                    'debit' => 0,
                    'credit' => $data['amount'],
                ]);

                return $entry->load('journalLines.chartOfAccount');
            });

            return JsonResponder::success($response, $entry, 'Purchase payment journal created successfully', 201);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to create payment journal: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Create expense journal
     * Records expenses incurred
     */
    public function createExpenseJournal(Response $response, array $data): Response
    {
        try {
            $entry = DB::connection()->transaction(function () use ($data) {
                $expense = Expense::find($data['expense_id']);
                
                if (!$expense) {
                    throw new \Exception('Expense not found');
                }

                // Get CoA IDs
                $expenseAccount = ChartOfAccount::find($data['expense_account_id']);
                $accountsPayable = ChartOfAccount::where('code', '2110')->first(); // A/P

                if (!$expenseAccount || !$accountsPayable) {
                    throw new \Exception('Required chart of accounts not found');
                }

                // Create journal entry
                $entry = JournalEntry::create([
                    'entry_date' => $expense->expense_date,
                    'reference_number' => 'EXP-' . $expense->id,
                    'description' => 'Expense Journal - ' . $expense->description,
                    'status' => 'posted',
                    'created_by' => $data['created_by'] ?? null,
                ]);

                // Debit Expense / Credit Accounts Payable (if not paid) or Cash (if paid)
                JournalLine::create([
                    'journal_entry_id' => $entry->id,
                    'chart_of_account_id' => $expenseAccount->id,
                    'description' => 'Expense - ' . $expense->description,
                    'debit' => $expense->amount,
                    'credit' => 0,
                ]);

                JournalLine::create([
                    'journal_entry_id' => $entry->id,
                    'chart_of_account_id' => $accountsPayable->id,
                    'description' => 'Accounts Payable - ' . $expense->description,
                    'debit' => 0,
                    'credit' => $expense->amount,
                    'vendor_id' => $expense->vendor_id ?? null,
                ]);

                return $entry->load('journalLines.chartOfAccount');
            });

            return JsonResponder::success($response, $entry, 'Expense journal created successfully', 201);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to create expense journal: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Create expense payment journal
     * Records payment of expenses
     */
    public function createExpensePaymentJournal(Response $response, array $data): Response
    {
        try {
            $entry = DB::connection()->transaction(function () use ($data) {
                // Get CoA IDs
                $accountsPayable = ChartOfAccount::where('code', '2110')->first(); // A/P
                $cash = ChartOfAccount::where('code', '1110')->first(); // Cash/Bank

                if (!$cash || !$accountsPayable) {
                    throw new \Exception('Required chart of accounts not found');
                }

                // Create journal entry
                $entry = JournalEntry::create([
                    'entry_date' => $data['payment_date'],
                    'reference_number' => $data['reference_number'] ?? 'PAY-EXP-' . time(),
                    'description' => $data['description'] ?? 'Expense payment',
                    'status' => 'posted',
                    'created_by' => $data['created_by'] ?? null,
                ]);

                // Debit Accounts Payable / Credit Cash
                JournalLine::create([
                    'journal_entry_id' => $entry->id,
                    'chart_of_account_id' => $accountsPayable->id,
                    'description' => 'Expense payment - ' . ($data['description'] ?? ''),
                    'debit' => $data['amount'],
                    'credit' => 0,
                ]);

                JournalLine::create([
                    'journal_entry_id' => $entry->id,
                    'chart_of_account_id' => $cash->id,
                    'description' => 'Cash payment - ' . ($data['description'] ?? ''),
                    'debit' => 0,
                    'credit' => $data['amount'],
                ]);

                return $entry->load('journalLines.chartOfAccount');
            });

            return JsonResponder::success($response, $entry, 'Expense payment journal created successfully', 201);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to create payment journal: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Create journal for internal goods expenditure
     * Records goods used internally (not for sale)
     */
    public function createInternalGoodsExpenditureJournal(Response $response, array $data): Response
    {
        try {
            $entry = DB::connection()->transaction(function () use ($data) {
                // Get CoA IDs
                $internalExpense = ChartOfAccount::where('code', '5200')->first(); // Internal Expense
                $inventory = ChartOfAccount::where('code', '1130')->first(); // Inventory

                if (!$internalExpense || !$inventory) {
                    throw new \Exception('Required chart of accounts not found');
                }

                // Create journal entry
                $entry = JournalEntry::create([
                    'entry_date' => $data['usage_date'],
                    'reference_number' => $data['reference_number'] ?? 'INT-USE-' . time(),
                    'description' => $data['description'] ?? 'Internal goods expenditure',
                    'status' => 'posted',
                    'created_by' => $data['created_by'] ?? null,
                ]);

                // Debit Internal Expense / Credit Inventory
                JournalLine::create([
                    'journal_entry_id' => $entry->id,
                    'chart_of_account_id' => $internalExpense->id,
                    'description' => 'Internal use - ' . ($data['description'] ?? ''),
                    'debit' => $data['amount'],
                    'credit' => 0,
                ]);

                JournalLine::create([
                    'journal_entry_id' => $entry->id,
                    'chart_of_account_id' => $inventory->id,
                    'description' => 'Inventory reduction - ' . ($data['description'] ?? ''),
                    'debit' => 0,
                    'credit' => $data['amount'],
                ]);

                return $entry->load('journalLines.chartOfAccount');
            });

            return JsonResponder::success($response, $entry, 'Internal goods expenditure journal created successfully', 201);
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to create internal expenditure journal: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Get all journal entries with filtering
     */
    public function getAllJournals(Response $response, array $filters = []): Response
    {
        try {
            $query = JournalEntry::with(['journalLines.chartOfAccount', 'createdBy']);

            // Apply filters
            if (isset($filters['start_date'])) {
                $query->where('entry_date', '>=', $filters['start_date']);
            }
            if (isset($filters['end_date'])) {
                $query->where('entry_date', '<=', $filters['end_date']);
            }
            if (isset($filters['status'])) {
                $query->where('status', $filters['status']);
            }
            if (isset($filters['reference_number'])) {
                $query->where('reference_number', 'like', '%' . $filters['reference_number'] . '%');
            }

            $entries = $query->orderBy('entry_date', 'desc')->get();

            return JsonResponder::success($response, $entries, 'Journal entries retrieved successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to retrieve journal entries: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Get journal entry by ID
     */
    public function getJournalById(Response $response, string $id): Response
    {
        try {
            $entry = JournalEntry::with(['journalLines.chartOfAccount', 'journalLines.customer', 'journalLines.vendor', 'createdBy'])
                ->find($id);
            
            if (!$entry) {
                return JsonResponder::error($response, 'Journal entry not found', 404);
            }

            return JsonResponder::success($response, $entry, 'Journal entry retrieved successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to retrieve journal entry: ' . $th->getMessage(), 500);
        }
    }

    /**
     * Delete journal entry
     */
    public function deleteJournal(Response $response, string $id): Response
    {
        try {
            DB::connection()->transaction(function () use ($id, $response) {
                $entry = JournalEntry::find($id);
                if (!$entry) {
                    throw new \Exception('Journal entry not found');
                }

                // Delete related lines first
                JournalLine::where('journal_entry_id', $id)->delete();
                
                // Delete the entry
                $entry->delete();
            });

            return JsonResponder::success($response, null, 'Journal entry deleted successfully');
        } catch (\Throwable $th) {
            return JsonResponder::error($response, 'Failed to delete journal entry: ' . $th->getMessage(), 500);
        }
    }
}
